<?php

/*

Модель подписчика необходимо доработать

*/
class m_subscriber extends class_model {

  var $tables = array(
    'subscribers' => "
      `id` int(10) NOT NULL auto_increment,
      `email` varchar(128) NOT NULL default '',
      `accepted` tinyint(3) NOT NULL default '0',
      `password` varchar(255) default NULL,
      `status_id` tinyint(3) NOT NULL default '1',
      `news` tinyint(3) NOT NULL default '1',
      `ip` varchar(255) default NULL,
      `accept_code` varchar(255) default NULL,
      `remove_code` varchar(255) default NULL,
      `last_send` datetime default NULL,
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      
      PRIMARY KEY  (`id`)    
    "
  );
    
  var $fromaddress = "";
  var $fromname = "";

  var $status_ar = array(
  0 => 'Приостановлен',
  1 => 'Активен'
  
  );
  
  var $debug = 0;
  var $debug_email = "pastor@bk.ru";
  var $ids = array();

function __construct() {
  global $sv;  
  
  $this->t = $sv->t['subscribers'];
  $this->per_page = 50;    
  $this->fromaddress = $sv->cfg['email'];
  
  $this->init_field(array(
  'name' => 'email',
  'title' => 'Email',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));    
  
  $this->init_field(array(
  'name' => 'accepted',
  'title' => 'Подтвержден',
  'type' => 'boolean',  
  'default' => '0',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')  
  ));  
    
  
  $this->init_field(array(
  'name' => 'accept_code',
  'title' => 'Код подтверждения',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '40',  
  'show_in' => array('remove'),
  'write_in' => array('edit')  
  ));  
    
  
  $this->init_field(array(
  'name' => 'remove_code',
  'title' => 'Код удаления',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '40', 
  'show_in' => array('remove'),
  'write_in' => array('edit')  
  ));  

     
  $this->init_field(array(
  'name' => 'password',
  'title' => 'Пароль',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '30',
  'show_in' => array(),
  'write_in' => array()  
  ));  
    
    

  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Статус',
  'type' => 'boolean',
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

  //virtual
  $this->init_field(array(
  'name' => 'date',
  'title' => 'Дата создания',
  'show_in' => array('default'),
  'write_in' => array(),
  'virtual' => "created_at"
  ));    
  
  //virtual
  $this->init_field(array(
  'name' => 'expiration',
  'title' => 'Дата удаления',
  'show_in' => array('default'),
  'write_in' => array(),
  'virtual' => "expires_at"
  ));       

  $this->init_field(array(
  'name' => 'last_send',
  'title' => 'Последняя отправка',
  'type' => 'datetime',  
  'setcurrent' => 1,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')  
  ));    
  
  $this->init_field(array(
  'name' => 'news',
  'title' => 'Получение новостей',
  'type' => 'boolean',  
  'default' => '1',
  'show_in' => array('remove'),
  'write_in' => array('edit')  
  ));  
    
    
    
  $this->init_field(array(
  'name' => 'ip',
  'title' => 'IP',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '30',
  'default' => '0',
  'show_in' => array('default', '', 'remove'),
  'write_in' => array()  
  ));  
      
    
      
}

function v_email($val) {
  global $std;   
  if (!$std->text->v_email($val)) {
    $this->v_err = true;
    $this->v_errm[] = "Неверный формат электронного адреса.";   
  }  
  return $val;
}

function v_accept_code($val) {
  $val = trim($val);
  if ($val=='') {
    $val = $this->random();
  }  
  return $val;
}

function v_remove_code($val) {
  $val = trim($val);
  if ($val=='') {
    $val = $this->random();
  }  
  return $val;
}


//func 
function daily_sender() {
  global $sv, $std, $db;
  
  $sv->load_model('article');
  $sv->load_model('movie');
  $sv->load_model('post');
  
  //$sv->post_time = strtotime("2008-05-05 13:18:15");
  $wh = array();
  $wh[] = "`sended`='0'";
  $wh[] = "`status_id`='1'";  
  $wh[] = "YEAR(`date`)='".date("Y", $sv->post_time)."'";
  $wh[] = "MONTH(`date`)='".date("m", $sv->post_time)."'";  
  $wh[] = "DAY(`date`)='".date("d", $sv->post_time)."'-1";
  

  $exp = $sv->post_time-60*60*24*7;
  $exp_date = date("Y-m-d H:i:s", $exp);
  $wh2 = array();  
  $wh2[] = "`sended`='0'";
  $wh2[] = "`status_id`='1'";  
  $wh2[] = "`date`>'{$exp_date}'";
  $where2 = implode(" AND ", $wh2);
    
  
  ec("Выбираем статьи...");
    $articles = $sv->m['article']->item_list(implode(" AND ", $wh), "`date` ASC", 0, 1); 
    if (count($articles['list'])<=0) {
      ec("Подготовленные для рассылки статьи отсутствуют, пропускаем.");
      return false;    
    }    
  
  ec("Выбираем видео...");
    $video = $sv->m['movie']->item_list($where2, "`date` DESC", 0, 1); 
   
  ec("Выбираем сообщения в блогах...");
    $post = $sv->m['post']->item_list($where2, "`date` DESC", 0, 1); 
   
    
      
  ec("Выбираем список адресов...");
  if ($this->debug) {
    ec("Отладочный режим, адрес для отладки: {$this->debug_email}");
    $users['list'] = array(0 => array('email' => $this->debug_email, 'remove_code' => '11111111'));
  }
  else {
    $users = $this->item_list("`status_id`='1' AND `accepted`='1'", "", __FILE__, __LINE__);
    if (count($users['list'])<=0) {
      ec("База подписчиков пуста.");
      return false;        
    }
  }
 
  ec("Подготавливаем текст...");
    $this->ids = array();
    $text = $this->get_text($articles['list'], $video['list'], $post['list']);
    
  ec("Обновляем статус материалов...");
  if ($this->debug) {
    ec("debug: пропускаем обновление статуса");
  }
  else {
    if (count($this->ids)>0) {
      $im = implode(", ", $this->ids);
      $db->q("UPDATE {$sv->t['articles']} SET `sended`='1' WHERE id IN ({$im})", __FILE__, __LINE__);
      $db->q("UPDATE {$sv->t['movies']} SET `sended`='1' WHERE {$where2}", __FILE__, __LINE__);
      $db->q("UPDATE {$sv->t['posts']} SET `sended`='1' WHERE {$where2}", __FILE__, __LINE__);
    }
  }
    
  ec("Начинаем рассылку по адресам...");
  $i = 0;
  foreach($users['list'] as $d) {  $i++;
    ec("{$i}. Sending email to {$d['email']}...");
    $res = $this->send2user($d, $text);
  }
    
    
  
  
}

//stuff

function get_text($ar, $movies, $posts) {
  global $sv, $std, $db;
 
  $tr = array();
  foreach($ar as $d) {
    $this->ids[] = intval($d['id']);
    $text = $sv->m['article']->parse_for_send($d);
    if ($text!='') {
      $tr[] = $text;
    }
  }  
  $articles = $tr;
  $f_time = $std->time->format($sv->post_time, 3);
  $y = date("Y", $sv->post_time);
  
  
// video
if (count($movies)>0)   {
  $tr = array();
  foreach($movies as $d) {
    $tr[] = $sv->m['movie']->parse_for_send($d);
  }
  $video = "-----------------
  
НОВОЕ ВИДЕО на сайте ЗВ:
  
".implode("\n\n", $tr)."

";
  
}
else {
  $video = "";
}


// video
if (count($posts)>0)   {
  $tr = array();
  foreach($posts as $d) {
    $tr[] = $sv->m['post']->parse_for_send($d);
  }
  $blogs = "-----------------

БЛОГИ на сайте ЗВ:
  
".implode("\n\n", $tr)."

";
  
}
else {
  $blogs = "";
}


  //pr($posts);
  
$text = "Новые материалы с сайта «Заполярный вестник» <http://norilsk-zv.ru> 
{$f_time}

---

".implode("\n\n---\n\n", $articles)."

{$video}
{$blogs}
-----------------

Посетите версию сайта «Заполярного вестника» для КПК и смарт-фонов
<http://wap.norilsk-zv.ru>

----

Об этом E-mail
Вы получили этот e-mail, потому что подписаны на рассылку материалов газеты «Заполярный вестник»

Чтобы отказаться от рассылки материалов пройдите по ссылке:
<#unsubscribe_url#>  

«Заполярный вестник», 663300, Норильск, Комсомольская 33а, (3919)465900, e-mail: zv@nrd.ru
Copyright {$y} «Заполярный вестник»

";

ec($text);

  return $text;
}

function send2user($d, $text) {
  global $sv, $std, $db;
  
  $url = "http://norilsk-zv.ru/subscribe/?email={$d['email']}&action=removing&remove_code={$d['remove_code']}";
  $text = str_replace("#unsubscribe_url#", $url, $text);
  
  $date = $std->time->format($sv->post_time, 0.5);
  $title = "Рассылка \"Заполярного вестника\" от {$date}";
  
  $std->mail->from_name = $this->fromname;
  $std->mail->from_address = $this->fromaddress;
  
  $sent = $std->mail->send($d['email'], $title, $text);
  return $sent;
}



/**
 * Statndart mail
 *
 */
function mail($to, $body, $subject, $fromaddress, $fromname) {
    
  $eol="\r\n";
  $mime_boundary=md5(time());
  $headers = "";

  // Common Headers
  $headers .= "From: ".$fromname." <".$fromaddress.">".$eol;
  $headers .= "Reply-To: ".$fromname." <".$fromaddress.">".$eol;
  $headers .= "Return-Path: ".$fromname." <".$fromaddress.">".$eol;    // these two to set reply address
  $headers .= "Message-ID: <".time()."-".$fromaddress.">".$eol;
  //$headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters

  
  $msg = $body;
  
  // SEND THE EMAIL
  //ini_set("SMTP", "aspmx.l.google.com");
  
  ini_set("sendmail_from",$fromaddress);  // the INI lines are to force the From Address to be used !
  $mail_sent = mail($to, $subject, $msg, $headers);
    
  ini_restore("sendmail_from");
  
  return $mail_sent;
}


function random()   {  
  $ret = md5(uniqid());
  return $ret;
}
  

function get_data($email) {
  global $sv, $db;
  
  $email  = addslashes($email);
  $db->q("SELECT * FROM {$this->t} WHERE email='{$email}'", __FILE__, __LINE__);
  if ($db->nr()<=0)  {
    return false;
  }
  else {
    $d = $db->f();
    
    return $d;
  }
 
  
  
}

function compile_form($action_url = "") {
  global $sv, $std, $db;
  
  $action_url = ($action_url=='') ? "/subscribe/" : $action_url;
  
$ret = <<<EOD
  
	<form action="{$action_url}" method="POST" enctype="multipart/form-data">

  	<table cellspacing="0" cellpadding="3" border="0">
  	<tr>
  	    <td colspan="2"><b>Введите ваш e-mail:</b></td>
  	</tr>
  	<tr>
  	    <td><input type="text" name='email' size='20'></td>
  		  <td><input type="submit" value="&raquo;"></td>
  	</tr>
  	<tr>
  	    <td colspan="2"><a href="{$action_url}">Управление подпиской</a></td>
  	</tr>
  	</table>
			
	</form>
			
EOD;
			  
  
  return $ret;
}

}


?>