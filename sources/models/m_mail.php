<?php

class m_mail extends class_model {
  
  var $tables = array(
    'mail' => "
      `id` int(11) NOT NULL auto_increment,
      `title` varchar(255) NOT NULL,
      `text` text NOT NULL,
      `sender` int(11) NOT NULL default '0',
      `recipient` int(11) NOT NULL default '0',
      `time` int(11) NOT NULL default '0',
      `time_read` int(11) NOT NULL default '0',
      `sender_del` tinyint(3) NOT NULL default '0',
      `sender_deleted` tinyint(3) NOT NULL default '0',
      `recipient_del` tinyint(3) NOT NULL default '0',
      `recipient_deleted` tinyint(3) NOT NULL default '0',
      `sender_name` varchar(255) default NULL,
      `sender_contacts` varchar(255) default NULL,
      `ip` varchar(255) default NULL,
      `agent` varchar(255) default NULL,
      `created_at` datetime default NULL,
      `created_by` int(11) default NULL,
      `updated_at` datetime default NULL,
      `updated_by` int(11) default NULL,
      PRIMARY KEY  (`id`),
      KEY `ip` (`ip`),
      KEY `rec` (`recipient`),
      KEY `sender` (`sender`)
  
    "
  );
  
  
  var $per_page = 50;
  var $auth = 0;
  var $uid = 0;
  var $system_id = 1;

function __construct() {
  global $sv;  
  
  $this->t = $sv->t['mail'];
     
  
    

  $this->init_field(array(
  'name' => 'date',
  'title' => 'Дата',
  'virtual' => 'created_at',  
  'show_in' => array('default'),
  'write_in' => array()
  ));    
  
 
  

  $this->init_field(array(
  'name' => 'title',
  'title' => 'Заголовок',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));    
  
 

  $this->init_field(array(
  'name' => 'text',
  'title' => 'Текст',
  'type' => 'text',    
  'len' => '70',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));    
  
   

  $this->init_field(array(
  'name' => 'sender',
  'title' => 'Отправитель',
  'type' => 'int',   
  'len'  => '3',
  'input' => 'select',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'), 
  'belongs_to' => array('table'=>'account', 'field'=>'id', 'return'=>'login', 'not_null' => 0)
  ));    
  

  $this->init_field(array(
  'name' => 'recipient',
  'title' => 'Получатель',
  'type' => 'int',   
  'len'  => '3',
  'input' => 'select',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'), 
  'belongs_to' => array('table'=>'account', 'field'=>'id', 'return'=>'login', 'not_null' => 0)
  ));    
  

  $this->init_field(array(
  'name' => 'sender_name',
  'title' => 'Имя отправителя',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '50',
  'show_in' => array( 'remove'),
  'write_in' => array('edit')
  ));    
  

  $this->init_field(array(
  'name' => 'sender_contacts',
  'title' => 'Контакты отправителя',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '50',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));    

   

  $this->init_field(array(
  'name' => 'ip',
  'title' => 'IP отправителя',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));    
    
  

  $this->init_field(array(
  'name' => 'agent',
  'title' => 'Клиент отправителя',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '50',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));    
    
  $this->init_field(array(
  'name' => 'sender_del',
  'title' => 'sender_del',
  'type' => 'tinyint',  
  'input' => 'boolean',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));   
   
  $this->init_field(array(
  'name' => 'sender_deleted',
  'title' => 'sender_deleted',
  'type' => 'tinyint',  
  'input' => 'boolean',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));     
  
  
  $this->init_field(array(
  'name' => 'recipient_del',
  'title' => 'recipient_del',
  'type' => 'tinyint',  
  'input' => 'boolean',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));   
   
  $this->init_field(array(
  'name' => 'recipient_deleted',
  'title' => 'recipient_deleted',
  'type' => 'tinyint',  
  'input' => 'boolean',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));     
  
    
  
  
}


// validations
function v_title($val) {
  global $sv, $std;
  
  $val = $std->text->cut($val, 'cut', 'allow');
  $val = trim($val);
  
  if ($val=='') {
    $this->v_err = true;
    $this->v_errm[] = "Не указан заголовок.";
  }
  
  return $val;
}

function v_text($val) {
  global $sv, $std;
  
  $val = $std->text->cut($val, 'cut', 'allow');
  $val = trim($val);
  
  if ($val=='') {
    $this->v_err = true;
    $this->v_errm[] = "Не указан текст.";
  }
  
  return $val;
}

function v_sender_name($val) {
  global $sv, $std;
  
  $val = $std->text->cut($val, 'cut', 'allow');
  $val = trim($val);
  
  if ($val=='') {
    $this->v_err = true;
    $this->v_errm[] = "Вы не указали свое имя.";
  }
  
  return $val;
}

function v_sender_contacts($val) {
  global $sv, $std;
  
  $val = $std->text->cut($val, 'cut', 'allow');
  $val = trim($val);
  
  if ($val=='') {
    $this->v_err = true;
    $this->v_errm[] = "Вы не указали контактные данные.";
  }
  
  return $val;
}


function df_title($v) {
  
  return "<div style='text-align:left;'>{$v}</div>";
}


// actions
function send($to_id) {
  global $sv, $std, $db;
  
  $err = false;
  $errm = array();
  
  if ($sv->user['session']['account_id']>0) {
    $this->auth = 1;  
  }
  
  $to_id = intval($to_id);
  
  $db->q("SELECT * FROM {$sv->t['account']} WHERE id='{$to_id}'", __FILE__, __LINE__);
  if ($db->nr()<=0) {
    forbidden("recipient-not-found", "Получатель не найден.");
  }
  else {
    $recipient = $db->f();
  }
  
  
  
  $p = array();
  $v = array();
  $n = (isset($sv->_post['new'])) ? $sv->_post['new'] : array();
  
  
  
	if (!$err) {
	  $fs = ($this->auth) ? array('title', 'text') : array('title', 'text', 'sender_name', 'sender_contacts');

	  foreach ($fs as $f) {
	    if (!isset($n[$f])) {
	      die("<b>Error:</b> var <b>{$f}</b> not specified");
	    }
	    else {
	      $n[$f] = $std->text->cut($n[$f], 'allow', 'mstrip');
	    }
	    
	  	if (method_exists($this, "v_{$f}")) {	  	  
	  	  eval("\$p[\$f] = \$this->v_{$f}(\$n[\$f]);");
	  	  $v[$f] = $std->text->cut($p[$f], 'replace', 'replace');
	  	}
	  	else {
	  	  die("<b>warn:</b> method <b>v_{$f}</b> not exists");
	  	}
	  }
	  $err = ($this->v_err) ? true : $err;
	  $errm = array_merge($errm, $this->v_errm);
	}
	
	
	if (!$err) {
	  $p['recipient'] = $to_id;
	  $p['ip'] = $sv->ip;
	  $p['agent'] = $sv->user_agent;
	  $p['time'] = $sv->post_time;
	  $p['time_read'] = 0;
	  
	  if (!$this->auth) {
  	  $p['sender_del'] = 1;
  	  $p['sender_deleted'] = 1;
  	  $p['sender'] = 0;
	  }
	  else {
	    $p['sender'] = $sv->user['session']['account_id'];
	  }
	  
	}
	
	if (!$err) {
	  $exp = $sv->post_time - 60*60*15;
	  
	  
	  $ar = array('title', 'text', 'sender', 'ip');
	  $s = array();
	  foreach ($ar as $k) {
	  	$s[] = "`{$k}`='".addslashes($p[$k])."'";
	  }
	  $im = implode(" AND ", $s);
	  $q = "SELECT 0 FROM {$this->t} WHERE {$im} AND `time`>'{$exp}'";
	
	  $db->q($q, __FILE__, __LINE__);
	  if ($db->nr()>0) {
	    $err = true;
	    $errm[] = "Задействован флуд-контроль, вы недавно уже отправляли похожее сообщение.";
	  }
	  
	}
	
	if (!$err) {
	  $af = $this->insert_row($p);
	  if ($af<=0) {
	    $errm[] = "Возможно сообщение не отрпавлено.";
	  }
	  else {
	    $errm[] = "Ваше сообщение успешно отправлено.";    
	  }
	}
  
  
  return array('err'=>$err, 'errm' => $errm, 'v'=>$v);
}


function system_send($recipient_id, $title, $text, $tail = 0) {
  global $sv, $std, $db;
  
  $err = false;
  $errm = array();
    
  $recipient_id = intval($recipient_id);
  
  $db->q("SELECT * FROM {$sv->t['account']} WHERE id='{$recipient_id}'", __FILE__, __LINE__);
  if ($db->nr()<=0) {
    $err = 1;
    $errm[] = "Получатель не найден.";
  }
  else {
    $recipient = $db->f();
  }
  
  
  
  $p = $vals = $n = array(); 
  
	if (!$err) {
	  $fs = array('title', 'text');
	  $n['title'] = $title;
	  $n['text'] = $text;

	  foreach ($fs as $f) {
	  	if (method_exists($this, "v_{$f}")) {	  	  
	  	  eval("\$p[\$f] = \$this->v_{$f}(\$n[\$f]);");
	  	  $vals[$f] = $std->text->cut($p[$f], 'replace', 'replace');
	  	}
	  	else {
	  	  $err = 1;
	  	  $errm[] = "<b>warn:</b> method <b>v_{$f}</b> not exists";
	  	}
	  }
	  $err = ($this->v_err) ? true : $err;
	  $errm = array_merge($errm, $this->v_errm);
	}
	
	
	if (!$err) {
	  $p['recipient'] = $recipient_id;
	  $p['ip'] = $sv->ip;
	  $p['agent'] = $sv->user_agent;
	  $p['time'] = $sv->post_time;
	  $p['time_read'] = 0;
	  $p['sender'] = $this->system_id;

	  if ($tail)   {
	    $p['text'] = $p['text'].
	    "\n\n[ Данное уведомление рассылается автоматически, \n&nbsp; на него не нужно отвечать. ]";
	  }
	}
	


	if (!$err) {
	  $af = $this->insert_row($p);
	  if ($af<=0) {
	    $errm[] = "Возможно сообщение не отправлено.";
	  }
	  else {
	    $errm[] = "Ваше сообщение успешно отправлено.";    
	  }
	}  
  
  return array('err'=>$err, 'errm' => $errm, 'v'=>$vals);
}




//stuff
function init_user($uid = 0) {  
  $this->uid = abs(intval($uid));  
  return $this->uid;
}

/**
 * inbox, sent lists
 * ! must by inited user
 */
function mail_list($code='recipient') {  
  global $sv, $std, $db; $ret = array();
  
  $code = ($code!='recipient') ? 'sender' : 'recipient';  
  $code2 = ($code!='recipient') ? 'recipient' : 'sender';
  $uid = $this->uid;
  
  $wh = "m.{$code}='{$uid}' AND m.{$code}<>'0' AND (m.{$code}_del='0')"; // OR  (m.{$code}_del='1' AND m.time_read=0)
      
    
  $db->q("SELECT 0 FROM {$this->t} m WHERE {$wh}", __FILE__, __LINE__);
  $page = (isset($sv->_get['page'])) ? intval($sv->_get['page']) : 1;
  $ret['pl'] = $pl = $std->pl($db->nr(), 30, $page, u($sv->act, $sv->code, '')."?page=");
  t($pl);
  $db->q("SELECT m.*, a.login
          FROM {$sv->t['mail']} m 
          LEFT JOIN {$sv->t['account']} a ON (m.{$code2}=a.id)
          WHERE {$wh} ORDER BY m.id DESC {$pl['ql']}", __FILE__, __LINE__);
  $ar = array();
  $k = $pl['k'];
  while ($d = $db->f()) { $k++;
    $d['k'] = $k;
    $d['f_time'] = $std->gettime($d['time'], 3);
    $ar[] = $d;    
  }
  
  $ret['size'] = count($ar);
  $ret['list'] = $ar;



return $ret;  
}

/**
 * trash box
 * ! must by inited user
 */
function trash() {  
global $sv, $std, $db; $ret = array();

  $ret['uid'] = $uid = $this->uid;
 
  $wh = "(m.recipient='{$uid}' AND m.recipient<>'0' AND m.recipient_del='1' AND m.recipient_deleted='0')
        OR (m.sender='{$uid}' AND m.sender<>'0' AND m.sender_del='1' AND m.sender_deleted='0')";      
  
      
 
  
  $db->q("SELECT 0 FROM {$sv->t['mail']} m WHERE {$wh}", __FILE__, __LINE__);
  $page = (isset($sv->_get['page'])) ? intval($sv->_get['page']) : 1;
  $ret['pl'] = $pl = $std->pl($db->nr(), 30, $page, u($sv->act, $sv->code, '', "page="));
  
  $db->q("SELECT m.*, arecipient.login as recipient_login, asender.login as sender_login
          FROM {$sv->t['mail']} m 
          LEFT JOIN {$sv->t['account']} arecipient ON (m.recipient=arecipient.id)
          LEFT JOIN {$sv->t['account']} asender ON (m.sender=asender.id)
          WHERE {$wh} ORDER BY m.id DESC {$pl['ql']}", __FILE__, __LINE__);
  $ar = array();
  $k = $pl['k'];
  while ($d = $db->f()) { $k++;
    $d['k'] = $k;
    $d['f_time'] = $std->gettime($d['time'], 3);
    $ar[] = $d;    
  }
  
  $ret['size'] = count($ar);
  $ret['list'] = $ar;



return $ret;  
}


/**
 * View message
 *
 * @return unknown
 */
function view() {
global $sv, $std, $db; $ret = array();

  $ret['uid'] = $uid = $this->uid;
      
    
  $db->q("SELECT * FROM {$sv->t['mail']} WHERE id='{$sv->id}' AND (`sender`='{$uid}' OR `recipient`='{$uid}')", __FILE__, __LINE__);
  if ($db->nr()==0) die('Сообщение не найдено.');
  
  $d = $db->f();
  $d['f_time'] = $std->gettime($d['time'], 3)." (".$std->gettime($d['time'], 5).")";
  $d['read_time'] = ($d['time_read']>0) ? $std->gettime($d['time_read'], 3) : "<b>не прочитано получателем</b>";  
  
  $d['code'] = ($d['sender']==$uid) ? 'sender' : 'recipient';
  $code2 = ($d['code']=='sender') ? 'recipient' : 'sender';
  
  $user = intval($d[$code2]);
  
  if ( ($d['code']=='recipient' || ($d['code']=='sender' && $d['recipient']==$uid)) && $d['time_read']==0) {
    $db->q("UPDATE {$sv->t['mail']} SET time_read='{$sv->post_time}', recipient_del='0' WHERE id='{$d['id']}'", __FILE__,__LINE__);
    $d['read_time'] = $std->gettime($sv->post_time, 3);
  }
  
  //$d['text'] = $std->replace_bbcode($d['text']);
  $ret['d'] = $d;
  
  $db->q("SELECT a.* FROM {$sv->t['account']} a       
          WHERE a.id='{$user}'", __FILE__, __LINE__);
  if ($db->nr()==0) {
    $u['login'] = $d['ip'];
    $u['id'] = 0;
  }
  else {
    $u = $db->f();
  }
  
  $ret['answer_link'] = "/mailto/{$user}/?answer={$d['id']}";
  $ret['answer_link_quote'] = "/mailto/{$user}/?answer={$d['id']}&quote=1";
  $ret['u'] = $u;
 
  
return $ret;  
}


function unread_count($uid = 0) {
  global $sv, $db;
  
  $uid = intval($uid);
  if ($uid<=0 && isset($sv->user['session']['account_id'])) {
    $uid = intval($sv->user['session']['account_id']);
  }
  
  $db->q("SELECT 0 FROM {$this->t} WHERE recipient='{$uid}' AND time_read<='0' AND recipient_del='0'", __FILE__, __LINE__);
  $ret = $db->nr();
  
  return $ret;
}


//eoc
}

?>