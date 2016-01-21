<?php
/*

// Ajax backend in post.php
switch($act) {
  case 'poll': 
    $sv->load_model('poll');    
    $sv->m['poll']->ajax_vote();    
  break;
}

// Block example 
$sv->load_model('poll');
$sv->parsed['poll'] = $sv->m['poll']->html_block();


// Public Block Template



	{if $sv->parsed.poll!==false}
	   <a name='active-poll'></a>
			<table width="100%" cellspacing="0" cellpadding="0" border="0">
			{assign var="p" value=$sv->parsed.poll}
			<tr>
			    <td class="header_green"><b>Вопрос недели</b></td>
			</tr>
			</table>
			<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
			    <td class="border_cell">
					<table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
					    <td class="question"><b>{$p.question}</b></td>
					</tr>
					<tr><td>
					<div id='active-poll'>
					{if $p.voted}
  					<div class='results'>
  					  {$p.results}
  					</div>					 
					{else}
  					  <div class='choices'>  		
  					  {if $p.list!==false}			  					 
      					{foreach from=$p.list item=d}
        					<div style='margin:5px 0;'><input type="radio" name='poll-cid' class='poll-cid' value="{$d.id}"><span>{$d.title}</span></div>
      					{/foreach}
      			  {else}
      			   <div><i>Варианты ответов пока что отсутствуют.</i></div>
      			  {/if}
              </div>  					
    					<div class='results' style='display:none;'>
    					</div>
    					
  					  <div id='msg' style='display:none;'></div>
  					  <div id='inputs'>
      					<input type="hidden" id="poll-id" value="{$p.id}">
      					<input type="submit" id="poll-submit" value="Голосовать">
  					  </div>
  					  
					{/if}
					<div id='wait' style="display:none;"><Table cellpadding="3" align="center"><tr><td><img src='/i/wait16.gif' width="16" height="16"></td><td style='padding-right: 10px;'>отправка&nbsp;данных</td></tr></table></div>
					</div>
					</td>
					</tr>
					<tr>
					    <td class="td_height" align="center"><a href="/polls/">Предыдущие опросы<img src="/i/arrow.gif" alt="" border="0"></a></td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="shadow"><span class="voice_block_arrow"><img src="/i/voice_block_arrow.gif" alt="" width="25" height="20" border="0" align="top"></span><img src="/i/shadow.gif" alt="" width="170" height="5" border="0"></td>
			</tr>
			</table>
  {/if}	
			
  
  





  
  
*/
class m_poll extends class_model {

  var $tables = array(
    'polls' => "
      `id` int(10) NOT NULL auto_increment,
      `question` text,
      `choices` longtext,
      `date` datetime default NULL,
      `image` varchar(255) default NULL,
      `image_exists` tinyint(3) NOT NULL default '0',
      `active` tinyint(3) NOT NULL default '0',
      `status_id` tinyint(3) NOT NULL default '0',
      `last_vote` int(11) default NULL,
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      PRIMARY KEY  (`id`) ",
    
    'voters' => "
      `id` bigint(20) NOT NULL auto_increment,
      `pid` int(11) default NULL,
      `cid` int(11) default NULL,
      `ip` varchar(255) default NULL,
      `agent` varchar(255) default NULL,
      `session` varchar(255) default NULL,
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      PRIMARY KEY  (`id`),
      KEY `pid` (`pid`)"
  );
    
  
  var $status_ar = array(
  0 => 'Черновик',
  1 => 'Опубликовано',
  2 => 'Скрыто'
  
  );
  
  var $img_dir = "i/polls/";
  var $img_url = "i/polls/";
  var $img_ext = array('gif', 'png', 'jpg');
  
  var $error_codes = array( 
    0=>"Файл загружен без ошибок.", 
    1=>"Првышен лимит upload_max_filesize в настройках сервера php.ini.", 
    2=>"Превышен лимит MAX_FILE_SIZE указанный в форме.",
    3=>"Файл был загржен лишь частично.",
    4=>"Файл не был указан.",
    6=>"Временная папка для загрузки не доступна."           
  );

  var $block_w = 130;
  var $per_page = 5;
  var $votes_per_ip = 50;
  
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['polls'];
  
    
  $this->init_field(array(
  'name' => 'date',
  'title' => 'Дата создания',
  'type' => 'datetime',
  'setcurrent' => 1,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));  

   
  $this->init_field(array(
  'name' => 'question',
  'title' => 'Вопрос',
  'type' => 'text',
  'size' => '60',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'unique' => 1
  ));  
  
  $this->init_field(array(
  'name' => 'choices',
  'title' => 'Варианты ответов',
  'type' => 'text',
  'input' => 'custom',
  'show_in' => array(),
  'write_in' => array('edit')
  )); 
   
 
  

   
 $this->init_field(array(
  'name' => 'image',
  'title' => 'Загрузить фото',
  'type' => 'varchar',
  'input' => 'file',  
  'show_in' => array(),
  'write_in' => array()
  ));    
  
  
  //virtual
  $this->init_field(array(
  'name' => 'image_view',
  'title' => 'Изображение',
  'show_in' => array('edit'),
  'virtual' => 'image'
  ));    

  $this->init_field(array(
  'name' => 'image_exists',
  'title' => 'Фото существует',
  'type' => 'tinyint',
  'size' => '3',
  'show_in' => array()
  ));       
   
  $this->init_field(array(
  'name' => 'active',
  'title' => 'Активное',
  'type' => 'boolean',
  'size' => '3',
  'len' => '3',
  'default' => '1',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));    

 

  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Статус',
  'type' => 'tinyint',
  'input' => 'select',  
  'show_in' => array('remove'),
  'write_in' => array('edit'),
  'belongs_to' => array('list' => $this->status_ar, 'not_null' => 1)
  ));    
      
  //virtual
  $this->init_field(array(
  'name' => 'status',
  'title' => 'Статус',
  'show_in' => array('default'),
  'write_in' => array(),
  'virtual' => "status_id"
  ));     
    
  
  
  
  
}


//validations
function v_choices($val) {

  if ($val=='') return $val;
  
  $i=0;
  foreach($val as $k=>$d) { $i++;
    $val[$i]['id'] = $i;
    $val[$i]['title'] = strip_tags($d['title']);
    $val[$i]['count'] = intval($d['count']);
  }
 
  
  $ret = serialize($val);
  return $ret;
}

function v_status_id($val) {
  
  $val = intval($val);
    
  if (($this->d['active']==1 || $this->p['active']==1) && $val==0) {
    $val = 1;
    $this->v_errm[] = "Голосование помечено как активное, текущий статус автоматически изменен на <b>опубликовано</b>.";
  }
      
  return $val;
}

function v_image($val) {
  
  return $val;
}

function last_v($p) {
  global $sv;
  
  if ($this->code=='create') {
    $p['date']  = $sv->date_time;
  }
  return $p;
}


//custom inputs 
function ci_choices($val) { 

  $ar = ($val=='') ? array() : unserialize($val);
  
  $divs = array(); $i = 0;
  foreach($ar as $k=>$v)  { $i++;
    $n = "new[choices][{$i}]";
    $divs[] = "
    <div id='ch{$i}' class='choice-row'>  
    <input type='text' size=40 name='{$n}[title]' value='{$v['title']}'>
    <input type='text' size=3 name='{$n}[count]' value='{$v['count']}' class='count'>
    <input type='button' value='удалить' class='choice-remove'>
    </div>";
  }
 
$js =<<<EOD
<style>
#choices-box { padding: 5px; }
.choice-row {padding:5px 0;}
.choice-row .count {text-align:center;}
</style>
<script language='JavaScript'>
var chi = '{$i}';

$(function() {
  
  $("#new-choice").css("padding", "10px 0");
  $("#add-choice").parent().css("margin-top", "5px");
  
  $("#add-choice").click( function() { 
    $("#new-choice").toggle();
    return false;
  });
  
  $("#new-choice-submit").click( function() { 
    chi++;
    $("#choices").append("<div id='ch"+chi+"' class='choice-row'>"   
      + "<input type='text' size=40 name='new[choices]["+chi+"][title]' value='"+$("#new-choice-text").val()+"'> "
      + "<input type='text' size=3 name='new[choices]["+chi+"][count]' value='0' class='count'> "
      + "<input type='button' value='удалить' class='choice-remove'> "
      + "</div>");
  
    $("#new-choice").hide();
    $("#new-choice-text").val('');
  });
      
 
  $('body').click(function(event) {
   if ($(event.target).is('.choice-remove')) {   
      if (confirm('Удалить строку?')) {
        $(event.target).parent().remove();
      }
   }
  });
 
});
 

</script>
  

EOD;
  
  $ret = "
  <div id='choices-box'>
    <div id='choices'>
      ".implode("\n", $divs)."
    </div>
    
    {$js}
    
    <div id='new-choice' style='display:none;'>
      <input type='text' size='30' id='new-choice-text'>
      <input type='button' id='new-choice-submit' value='Добавить строку'>  
    </div>
    <div><a href='#' id='add-choice'>Добавить вариант</a></div>
  </div>
  ";
  
  return $ret;
}


//pre post actions
function before_create() {
  global $sv;
 
  
}

function before_update() {
  

  
}
function after_update($d, $p, $err) {
global $db, $sv;

  if (!$err && $p['active']==1) {  
    $db->q("UPDATE {$this->t} SET `active`='0' WHERE id<>'$this->current_record'", __FILE__, __LINE__);        
  }

}

function after_create($p, $err) {
  global $db;  

  if (!$err && $p['active']==1) {  
    $db->q("UPDATE {$this->t} SET `active`='0' WHERE id<>'$this->current_record'", __FILE__, __LINE__);        
  }
  
}



function parse($d) {
  global $std;
  
  
  $d['list'] = unserialize($d['choices']);
  $d['f_date'] = $std->time->f_date($d['date'], 0.6);
  
  return $d;
  
}

// PUBLIC CONTROLLERS
/**
 * Архив голосований
 * показывается список голосований с результатом, активное не выводится
 *
 * @return unknown
 */
function c_public_list() {
  global $sv, $std, $db;
  
  $db->q("  SELECT 0 FROM {$this->t} WHERE status_id='1' AND active<>'1'", __FILE__, __LINE__);
  $ret['pl'] = $pl = $std->pl($db->nr(), $this->per_page, $sv->_get['page'], $this->url);
  
  
  $ar = array();
  $db->q("  SELECT * FROM {$this->t} 
            WHERE status_id='1' AND active<>'1' ORDER BY `active` DESC, `date` DESC {$pl['ql']}", __FILE__, __LINE__);
  while($d = $db->f()) {
    $d = $sv->m['poll']->parse($d);
    $d['results'] = $this->format_results_polls($d, 320);
    $ar[] = $d;
  }
  
  $ret['list'] = $ar;
  $ret['count'] = count($ar);
  
  return $ret;
}

function ajax_vote() {
  global $sv, $db;
  $err = false;
  $errm = array();  
  
  $pid = (isset($sv->_r['pid'])) ? intval($sv->_r['pid']) : 0;
  $cid = (isset($sv->_r['cid'])) ? intval($sv->_r['cid']) : 0;
  
  $db->q("SELECT * FROM {$this->t} WHERE id='{$pid}'", __FILE__, __LINE__);
  if ($db->nr()>0) {
    $d = $this->parse($db->f());
  }
  else {
    $err = true;
    $errm[] = "Голосование не найдено.";
  }
  
  if(!$err && !$d['active']) {
    $err = true;
    $errm[] = "Это голосование уже закрыто.";
  }
  
  if (!$err) {
    
    if (!isset($d['list'][$cid])) {
      $err = true;
      $errm[] = "Вы ничего не выбрали.";
    }
    else {
      if ($this->is_voted($pid)) {
        $err = true;
        $errm[] = "Вы уже голосовали в этом опросе.";
      }
    }
  }
  
  if (!$err) {
    $d['list'][$cid]['count']++;     
    $ser = addslashes(serialize($d['list']));
    $db->q("UPDATE {$this->t} SET choices='{$ser}' WHERE id='{$d['id']}'", __FILE__, __LINE__);
    
    $p = $s = array();
    $p['pid'] = $pid;
    $p['cid'] = $cid;
    $p['ip'] = $sv->ip;
    $p['agent'] = $sv->user_agent;
    $p['session'] = $sv->m['session']->sid;
    $p['created_at'] = $sv->date_time;
    foreach($p as $k=>$v) {
      $s[] = "`{$k}`='".addslashes($v)."'";
    }
    $q = "INSERT INTO {$sv->t['voters']} SET ".implode(", ", $s);
    $db->q($q, __FILE__, __LINE__);
    
    $errm[] = "Ваш голос принят.";
  }
  $list = (!$err) ?  $this->parse_results($d['list'], $this->block_w, 1) : array();
    
  
  
  
  $msg = $this->encode(implode("<br>", $errm), 'utf8', 'cp1251');
  $list = $list;
  
  $ret = array('err' => $err, 'errm' => $msg, 'list' => $list);  

  echo json_encode($ret);
  
}

//view
function html_block() {
  global $sv, $std, $db;
  
  $db->q("SELECT * FROM {$this->t} WHERE active='1' AND status_id='1'", __FILE__, __LINE__);
  if ($db->nr()<=0) {
    return false;
  }
  
  $d = $db->f();
  $d = $this->parse($d);
  

  $d['voted'] = $this->is_voted($d['id']);
  if ($d['voted']) {
    $d['results'] = $this->format_results($d, $this->block_w);        
  }
  return $d;
}

function parse_results($ar, $w, $encode=0) {
  
 
  $sum = 0;
  $c = array(); $j = 1;
  foreach ($ar as $i=>$d) {  $j = $j-0.1;
    $c[$i] = $d['count']+$j;
    $sum += $d['count'];
  }
  
  if (count($c)<=0) {
    return array();
  }
  
 
  //print_r($c);
  arsort($c);
  //print_r($c);
  
  $ret = array();   $j = 0; $max = 0;
  foreach($c as $i => $count) { $j++;
    $d = $ar[$i];
    if ($j==1) {
      $max = $d['count'];
    }
    
    $d['title'] = ($encode) ? $this->encode($d['title']) : $d['title'];
    $d['p'] = ($max!=0) ? ($d['count']/$max) : 0; 
    $d['w'] = round($w*$d['p']);
    $d['pf'] = ($sum!=0) ? sprintf("%01.1f", ($d['count']/$sum)*100) : "0.0";
    $d['sum'] = $sum;
    $ret[$i] = $d;
    
    
  }
  
  
  return $ret;
}

function format_results($d, $w) {
  
  $ar = $this->parse_results($d['list'], $w);
  $divs = array();
  foreach($ar as $d) {
  
    $divs[] = "
    <div class='result-row'><table width=100% cellpadding=0 cellspacing=0>
    <tr><td>{$d['title']}</td><td align='right'>{$d['count']}</td></tr>
    <tr><td><div class='progress' style='width: {$d['w']}px; '></div></td><td align=right><small>{$d['pf']}%</small></td>
    </table></div>
    ";
  }

  $divs[] = "<p align='center'>Всего проголосовало: <b>{$d['sum']}</b> чел.</p>";
  
  return implode("\n", $divs);
}


function format_results_polls($d, $w) {
  
  $ar = $this->parse_results($d['list'], $w);
  $divs = array();
  foreach($ar as $d) {
  
    $divs[] = "
    <div class='result-row'><table width=100% cellpadding=0 cellspacing=0>
    <tr><td rowspan=2 width=1% bgcolor=#efefef>&nbsp;</td>
        <td class='res-td'>{$d['title']}</td><td align='right'>{$d['count']}</td></tr>
    <tr><td class='res-td'><div class='progress' style='width: {$d['w']}px; '></div></td><td align=right><small>{$d['pf']}%</small></td>
    </table></div>
    ";
  }

  $divs[] = "<div class='poll-itog'>Всего проголосовало: <b>{$d['sum']}</b> чел.</div>";
  
  return implode("\n", $divs);
}



function encode($str) {
  return mb_convert_encoding($str, "utf8", "cp1251");
}

// 
function is_voted($pid) {
  global $sv, $std, $db;
 // return false;
  $pid = intval($pid);
  $ip = addslashes($sv->ip);
  $sess = addslashes($sv->m['session']->sid);
  
  //check ip
  $db->q("SELECT session, agent FROM {$sv->t['voters']} WHERE pid='{$pid}' AND ip='{$ip}'", __FILE__, __LINE__);
  $s = $db->nr();
  if ($s<=0) {
    $ret = false;
  }
  elseif( $s > $this->votes_per_ip ) {
    $ret = true;
  }
  else {
    //ip exists check session AND limit
    $not_voted = true;
    while($d = $db->f()) {
      if ($d['session']==$sv->m['session']->sid) {
        $not_voted = false;
        break;
      }
    }
        
    if ($not_voted) {
      $ret = false;
    }
    else {
      $ret = true;
    }
  }
  
  //check session
  if (!$ret) {
    $db->q("SELECT 0 FROM {$sv->t['voters']} WHERE session='{$sess}' AND pid='{$pid}'", __FILE__, __LINE__);
    if ($db->nr()>0) {
      $ret = true;
    }
  }
  
  return $ret;
}

}  
  
?>