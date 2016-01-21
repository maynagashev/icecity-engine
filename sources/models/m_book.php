<?php

/*

Готевая книга

*/

class m_book extends class_model {
  
  var $tables = array(
    'book' => "
      `id` bigint(20) NOT NULL auto_increment,
      `date` datetime default NULL,
      `name` varchar(255) default NULL,
      `email` varchar(255) default NULL,
      `www` varchar(255) default NULL,
      `text` text not null default '',
      
      `refer` varchar(255) default NULL,
      `ip` varchar(255) default NULL,
      `agent` varchar(255) default null,
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      
      PRIMARY KEY  (`id`),
      KEY (`date`)
    "
  );
  
  var $per_page = 15;
  var $img_email_url = "/tools/email/";
  var $name_max_len = 30;

function __construct() {
  global $sv;  
  
  $this->t = $sv->t['book'];
  
  $this->init_field(array(
  'name' => 'date',
  'title' => 'Дата',
  'type' => 'datetime',   
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));    
  
  $this->init_field(array(
  'name' => 'name',
  'title' => 'Имя',
  'type' => 'varchar',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create', 'add')  
  ));  
  
  $this->init_field(array(
  'name' => 'email',
  'title' => 'Email',
  'type' => 'varchar',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create', 'add')  
  ));  
  
  $this->init_field(array(
  'name' => 'www',
  'title' => 'WWW',
  'type' => 'varchar',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create', 'add')  
  ));    
  
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Текст',
  'type' => 'text',
  'len' => '80',
  'show_in' => array(),
  'write_in' => array('edit', 'create', 'add')  
  ));    
  
   
  $this->init_field(array(
  'name' => 'ip',
  'title' => 'IP',
  'type' => 'varchar',  
  'show_in' => array('edit', 'remove', 'default'),
  'write_in' => array()
  ));  

  $this->init_field(array(
  'name' => 'agent',
  'title' => 'Agent',
  'type' => 'varchar',  
  'show_in' => array('edit', 'remove'),
  'write_in' => array()
  ));    
  
  $this->init_field(array(
  'name' => 'refer',
  'title' => 'Refer',
  'type' => 'varchar',  
  'show_in' => array('edit', 'remove'),
  'write_in' => array()
  ));    
    
}

function v_name($t) {
  global $sv, $std, $db;
  
  $t = strip_tags($t);
  $t = preg_replace("#[^A-Яа-я0-9a-z\-\_\.\ \(\)\[\]\:]#si", "", $t);
  $t = trim($t);
  
  
  $t = (strlen($t)>$this->name_max_len) ? substr($t, 0, $this->name_max_len) : $t;
  
  
  if ($t=='') {
    $this->v_err = 1;
    $this->v_errm[] = "Вы не указали свое имя.";
  }
  elseif (!preg_match("#[a-zа-я]#si", $t)) {
    $this->v_err = 1;
    $this->v_errm[] = "Имена состоящие только из цифр или спецсимволов не допустимы.";
  }
  
  return $t;
}
function v_email($t) {
  global $std;
  
  $t = trim($t);
  
  if ($t!='' && !$std->text->v_email($t)) {
    $t = '';
    //$this->v_err = 1;
    $this->v_errm[] = "Неверный формат почтового адреса (email), проигнорирован.";
  }
  return $t;
}
function v_www($t) {
  global $sv, $std;
  
  $t = strip_tags($t);
  $t = trim($t);
  
  return $t;
}
function v_text($t) {
  global $sv, $std;  
  
  $t = trim($t);  
  $t = strip_tags($t);
  
  $temp = preg_replace("#[^a-zа-я0-9]#si", "", $t);
  if ($temp=='') {
    $this->v_err = 1;
    $this->v_errm[] = "Текст сообщения не указан, либо не содержит обычного текста, напишите хоть что-нибудь.";
  }
   
  return $t;
}


function last_v($p) {
  global $sv, $db;

  if ($sv->code=='create' || $sv->code=='add') {
    $p['date'] = $sv->date_time;
    $p['ip'] = $sv->ip;
    $p['agent'] = $sv->user_agent;
    $p['refer'] = $sv->refer;
  }
  
  // проверяем дубликаты за сутки
  if ($sv->code=='add') {
    
    $exp = $sv->post_time - 60*60*24; 
    $date = date("Y-m-d H:i:s", $exp);
    $db->q("
      SELECT 0 FROM {$this->t} 
      WHERE `name`='".$db->esc($p['name'])."' AND `text`='".$db->esc($p['text'])."'", __FILE__, __LINE__);
    if ($db->nr()>0) {
      $this->v_err = 1;
      $this->v_errm[] = "Вы уже добавляли точно такое же сообщение недавно.";
    }
    
  }
  
  if ($sv->code=='add') {
    $sv->load_model('antispam');
    if (!$sv->m['antispam']->v_code()) {
      $this->v_err = 1;
      $this->v_errm[] = "Неверно указан антиспам-код, попробуйте еще раз.";
    }
  }
  
  return $p;
}

// parsers
function parse($d) {
  global $std; 
  
  $d['f_date'] = $std->time->format($d['date'], 2, 1);
  
  $d['url'] = ($d['www']!='') ? $d['www'] : '';
  $d['url'] = (!preg_match("#^([a-z0-9]{2,10})\://#si", $d['url'])) ? "http://".$d['url'] : $d['url'];
  $d['f_url'] = preg_replace("#^(.{15}).*(.{10})$#si", "\\1...\\2", $d['www']);
  
  $d['f_text'] = $std->markitup->bbcode2html($d['text']);
  $d['img_email'] = ($d['email']!='') ? "<img src='{$this->img_email_url}?id={$d['id']}' border='0'>" : "";
  
  return $d;
}

//controllers 
/**
 * Публичный список сообщений с формой
 *
 * @return unknown
 */
function c_publiclist() {
  global $sv, $std, $db;
  
  $ret = $this->item_list_pls("", "`date` DESC", $this->per_page,  1, 0, $sv->view->root_url."?p=");
    
  $ret['form'] = $this->compile_form();

  return $ret;
}

function c_add() {
  global $sv, $std;

  $s = $this->init_submit_create(1, 0);

  if ($s['submited'] && $s['err']) {
    $s['errm'][] = "<A href='#form'>Исправить ошибку</a> &rarr;";
    
  }
  elseif($s['submited'] && !$s['err']) {
    $s['errm'][] = "Ваше сообщение успешно добавлено [".$std->time->format($sv->post_time, 0.5)."].";
  }
  
  $s['err_box'] = $std->err_box($s['err'], $s['errm']);
  
  // список сообщений   
  $ret = $this->item_list_pls("", "`date` DESC", $this->per_page,  1, 0, $sv->view->root_url."?p=");      
  $ret['form'] = $this->compile_form("", $s['v']);
    
  $ret['s'] = $s;
  return $ret;
}
/**
 * Готовая форма с капчей и markitup
 *
 * @param unknown_type $action_url
 * @param unknown_type $vals
 * @return unknown
 */
function compile_form( $action_url = "", $vals = array() ) {
  global $sv, $std, $db;
     
  $action_url = ($action_url=='') ? $sv->view->root_url."add/" : $action_url;
  
  // значения по умолчанию в полях
  $vars = array('name', 'email', 'www', 'text');
  foreach($vars as $k) {
    $vals[$k] = (isset($vals[$k])) ? $std->text->cut($vals[$k], 'replace', 'replace') : '';
  }
  
  
  // инициируем капчу для формы
  $sv->load_model('antispam');
  $c = $sv->m['antispam']->init_captcha();

  // markitup
  $markitup = $std->markitup->js("#msg_area");
  
$ret = "

<a name='form'></a>
<form action='{$action_url}' enctype='multipart/form-data' method='post'>
<table cellpadding=5 cellspacing=0 border=0>

    	    	
     <tr>
      <td colspan='2'>
        Ваше имя: <span style='color:red;'>*</span><br>
		    <input type='text' value='{$vals['name']}' name='new[name]' size='50'></td>
		 </tr>
		 <tr>
        <td>
		      E-mail:<br>
		      <input type='text' value='{$vals['email']}' name='new[email]' size='30'>
		    </td>
		    <td>
		      WWW:<br>
		      <input type='text' value='{$vals['www']}' name='new[www]' size='40'>
		    </td>
		 </tr> 
    
    <tr valign=bottom><td>Ваше сообщение: <span style='color:red;'>*</span></td>
        <td style='padding:0 0 0 8px;'>{$markitup}</td>
    </tr>
    
    
		<tr><td colspan=2 class='bbcode'>	
  	   <textarea cols='70' rows='10' id='msg_area' name='new[text]'>{$vals['text']}</textarea>
		</td></tr>			
		
 
    <tr>
			<td colspan=2 align=center style='padding:15px;'>
     <table cellpadding=0 cellspacing=0 style='margin:0px;' border=0>
      
      	<tr><td align=center rowspan=2 style='padding:5px 5px 0 0;'>{$c['img']}</td>
      	
        <td valign=bottom nowrap style='padding:0px;'><small>&#8592;&nbsp;введите код изображенный на картинке</td>
        </tr>
        <tr>
      	<td align=center valign=top style='padding:0px;'>
      	{$c['input1']}{$c['input2']}
     </td></tr></table>
      </td></tr>

	  <tr>
	    <td align=center colspan=2 style='padding:15px;'>
			<input type='submit' value='Добавить сообщение' style='font-size:110%;'></td>
		</tr>	
		
		</table>
		
		</form>		
";		
  
  return $ret;
}
//eoc
}
  

?>