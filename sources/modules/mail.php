<?php
class mail {		
  
var $t;
var $id;
var $auth = 0;
var $m;

function __construct() {
  global $sv, $db;
  
  $this->t = $sv->t['mail'];
}

function auto_run() 
{		    
  global $sv, $db, $std;
  
  $sv->vars['js'][] = 'mail.js';
  
  $c = array('status', 'inbox', 'sent', 'trash', 'to', 'new', 'view'); 
  $sv->code  = (in_array($sv->code, $c)) ? strtolower($sv->code) : 'default';   
  $sv->code = ($sv->code=='to') ? 'new' : $sv->code;
  
  $sv->id = isset($sv->view->purl[3]) ? intval($sv->view->purl[3]) : 0;
  
  if ($sv->user['session']['account_id']>0) {
    $this->auth = 1;
  }
  elseif ($sv->code!='new') {
    forbidden("not-auth", "Требуется авторизация");
  }
  
  $sv->load_model('mail');  
  $sv->m['mail']->init_user($sv->user['session']['account_id']);
  
  
  $s = $this->submit();
  $db->q("DELETE FROM {$this->t} WHERE sender_del='1' AND recipient_del='1'", __FILE__, __LINE__);
  $db->q("DELETE FROM {$this->t} WHERE sender_del='1' AND time_read='0'", __FILE__, __LINE__);
  
  
  
  	switch($sv->code)
  	{
  	  case 'status':
  	    //echo $this->status();
  	    exit();
  	  break;  	  
  	  case 'new': 
  	    $ar = $this->mailto($sv->id, 0);  	      	    
  	  break;
  	  
  	  case 'inbox':
  	    $ar = $sv->m['mail']->mail_list('recipient');
  	    //$ar = $this->item_list('to');
  	  break;
  	  case 'sent':
  	    $ar = $sv->m['mail']->mail_list('sender');
  	    //$ar = $this->item_list('from');
  	  
  	  break;
  	  case 'view':
  	    $ar = $sv->m['mail']->view();
  	  break;
  	  case 'trash':
  	    $ar = $sv->m['mail']->trash();
  	    //$ar = $this->trash();
  	  break;
  		default:
  			
  	}
  	
  //print_r($ar);
  	$ar['s'] = $s;
   
  
   return $ar;  
}


function mailto($to_id, $selfsend = 0) {
  global $sv, $std, $db; 
  
  $ret = array();
  $ret['answer'] = false;
  $ret['auth'] = $this->auth;
  
  
  $to_id = intval($to_id);  
  $uid = intval($sv->user['session']['account_id']);

  if ($to_id==$uid  && !$selfsend) {
    forbidden("cant-mail-oneself", "Нельзя отправлять самому себе.");
  }
    
  $db->q("SELECT * FROM {$sv->t['account']} WHERE id='{$to_id}'", __FILE__, __LINE__); 
  if ($db->nr()==0) {
    forbidden("recipient-not-found", "Получатель не найден.");
  }
  else {
    $ret['recipient'] = $db->f();
  }
  
  // quotes and answers opts
  if ($this->auth) {    
    $ret['quote'] = (isset($sv->_get['quote']) && $sv->_get['quote']==1) ? true : false;   
    
    if (isset($sv->_get['answer'])) {
      $aid = intval($sv->_get['answer']);
      $db->q("SELECT * FROM {$sv->t['mail']} WHERE `recipient`='{$uid}' AND id='{$aid}'", __FILE__,__LINE__);
      if ($db->nr()>0) {
        $a = $db->f();
        $a['text'] = trim($std->cut_bbcode($a['text']));
        $ret['answer'] = $a;
      }
    }
  }
  
  
  
    
return $ret;
}

function status() {
global $sv, $std, $db; $ret =array()  ;

  
  $uid = intval($sv->user['session']['account_id']);
  
  $db->q("SELECT 0 FROM {$this->t} WHERE `to`='{$uid}' AND (to_del='0' OR (to_del='1' AND time_read='0'))", __FILE__,__LINE__);
  $ret['inbox'] = $db->nr();
  
  $db->q("SELECT 0 FROM {$this->t} WHERE `to`='{$uid}' AND 
    ((to_del='0' AND time_read='0') OR 
     (to_del='1' AND time_read='0'))
    ", __FILE__,__LINE__);  
  $ret['inbox_new'] = $db->nr();
  
  $db->q("SELECT 0 FROM {$this->t} WHERE `from`='{$uid}'  AND from_del='0'", __FILE__,__LINE__);
  $ret['sent'] = $db->nr();
  
  $db->q("SELECT 0 FROM {$this->t} WHERE `from`='{$uid}'  AND from_del='0' AND time_read='0'", __FILE__,__LINE__);  
  $ret['sent_new'] = $db->nr();
  
  $db->q("SELECT 0 FROM {$this->t} 
    WHERE (`from`='{$uid}' AND from_del='1' AND from_deleted='0') 
    OR (`to`='{$uid}' AND `to_del`='1' AND to_deleted='0')", __FILE__,__LINE__);  
  $ret['trash'] = $db->nr();
  
 // $ret['inbox_new'] = 3;
 
  
  
  $mod = (isset($sv->mail_mod)) ? $sv->mail_mod : 'default';
  
  switch ($mod) {
    case 'photo':
      $c[1][1] = "red";
      $c[1][2] = "white";
      $c[2][1] = "white";
      $c[2][2] = "green";      
      $objects = "";
      $align = "center";
    break;
    default:
      $c[1][1] = "yellow";
      $c[1][2] = "black";
      $c[2][1] = "#6acc64";
      $c[2][2] = "white";
      $objects = "
      parent.document.getElementById('inbox').innerHTML=document.getElementById('inbox').value;
      parent.document.getElementById('sent').innerHTML=document.getElementById('sent').value;
      parent.document.getElementById('trash').innerHTML=document.getElementById('trash').value;
      ";
      $align = "right";
      
  }
  
  
 
  
  if ($ret['inbox_new']>0) {
    $new = ($ret['inbox_new']==1) ? "1 новое&nbsp;" : $ret['inbox_new']." новых&nbsp;";
    $blink = "
    document.body.style.backgroundColor='{$c[1][1]}';
    document.body.style.color='{$c[1][2]}';
    setInterval('blink()', 1000);";
  }
  else {
    $new = "нет новых";
    $blink = "";
  }
  $ret['inbox_new'] = ($ret['inbox_new']>0) ? "({$ret['inbox_new']})" : "";
  $ret['sent_new'] = ($ret['sent_new']>0) ? "({$ret['sent_new']})" : "";

  
   
  $html = "
  <html>
  <script language='JavaScript'>
    function refresh_parent() {
      {$objects}
      
      
    }
  </script>
  <meta http-equiv='Content-Type' content='text/html; charset=windows-1251'>
  <link rel='stylesheet' type='text/css' href='style.css'>
  <body color={$c[2][2]} leftmargin=0 topmargin=0  
    style='color: {$c[2][2]};background-color: {$c[2][1]};' onLoad='refresh_parent();'>
  <script language='JavaScript'>
   
   
    function blink() {
     if (document.body.style.backgroundColor=='{$c[2][1]}') {
        document.body.style.backgroundColor='{$c[1][1]}';
        document.body.style.color='{$c[1][2]}';
     }else {
        document.body.style.backgroundColor='{$c[2][1]}';
        document.body.style.color='{$c[2][2]}';
     };
    }
    {$blink}
  </script>
  
  <input type='hidden' id='inbox' value='{$ret['inbox']}{$ret['inbox_new']}'>
  <input type='hidden' id='sent' value='{$ret['sent']}{$ret['sent_new']}'>
  <input type='hidden' id='trash' value='{$ret['trash']}'>
  <div width=70 align={$align}>{$new}</div>
  </html>
  ";
  
  return $html;
  
}

function submit()
{
	GLOBAL $sv, $db, $std; $ret = array();

  $err = false;
  $errm = array();
  $v = array();
  $submited = false;
  	
  $n = isset($sv->_post['new']) ? $sv->_post['new'] : array();
  if (!isset($n['todo'])) {
    $err = true;      
    $todo = "default";
  }
  else {
    $submited = true;
    $todo = $n['todo'];
  }
  if (!$err) {
    $uid = intval($sv->user['session']['account_id']);
    
    if (!$this->auth && $todo!=='send') {
      forbidden("not-auth", "Требуется авторизация");
    }
  }
  
  if (!$err) {
    switch ($todo) {
    case 'send': 
      $r = $sv->m['mail']->send($sv->id);    
      $err = ($r['err']) ? true : $err;
      $errm = array_merge($errm, $r['errm']);
      $v = $r['v'];
    break;
    
    case 'delete':
      $s = (is_array($n['selected']) && count($n['selected'])>0) ? array_keys($n['selected']) : array();
      foreach ($s as $k=>$v) {
        $s[$k] = intval($v);
      }
      if (count($s)==0)  {
        $err = true;
        $errm[] = "Ничего не выбрано.";
      }
      
  
      if (!$err) {
        $in = implode(", ", $s);
        $sql = "UPDATE {$this->t} SET recipient_del='1' WHERE `recipient`='{$uid}' AND id IN ({$in})";
        $db->q($sql, __FILE__, __LINE__);
        $s = $db->af();
        
        $sql = "UPDATE {$this->t} SET sender_del='1' WHERE `sender`='{$uid}' AND id IN ({$in})";
        $db->q($sql, __FILE__, __LINE__);
        $s = $s + $db->af();
        switch ($s) {
          case 1: $t = 'сообщение'; break;
          case 2: case 3: case 4: $t = 'сообщения'; break;
          default: $t = 'сообщений';
        }
        
        $errm[] = "{$s} {$t} удалено.";
      }  
    break;
    
    
    case 'recover':
      $s = (is_array($n['selected']) && count($n['selected'])>0) ? array_keys($n['selected']) : array();
      foreach ($s as $k=>$v) {
        $s[$k] = intval($v);
      }
      if (count($s)==0)  {
        $err = true;
        $errm[] = "Ничего не выбрано.";
      }
      
      if (!$err) {
        $in = implode(", ", $s);
        $sql = "UPDATE {$this->t} SET recipient_del='0' WHERE `recipient`='{$uid}' AND id IN ({$in})";
        $db->q($sql, __FILE__, __LINE__);
        $s = $db->af();
        
        $sql = "UPDATE {$this->t} SET sender_del='0' WHERE `sender`='{$uid}' AND id IN ({$in})";
        $db->q($sql, __FILE__, __LINE__);
        $s = $s + $db->af();
        switch ($s) {
          case 1: $t = 'сообщение'; break;
          case 2: case 3: case 4: $t = 'сообщения'; break;
          default: $t = 'сообщений';
        }
        
        $errm[] = "{$s} {$t} восстановлено.";
      }  
    break;  
    
  
    case 'drop':
      $s = (is_array($n['selected']) && count($n['selected'])>0) ? array_keys($n['selected']) : array();
      foreach ($s as $k=>$v) {
        $s[$k] = intval($v);
      }
      if (count($s)==0)  {
        $err = true;
        $errm[] = "Ничего не выбрано.";
      }
      
      if (!$err) {
        $in = implode(", ", $s);
        $sql = "UPDATE {$this->t} SET recipient_deleted='1' WHERE `recipient`='{$uid}' AND id IN ({$in})";
        $db->q($sql, __FILE__, __LINE__);
        $s = $db->af();
        
        $sql = "UPDATE {$this->t} SET sender_deleted='1' WHERE `sender`='{$uid}' AND id IN ({$in})";
        $db->q($sql, __FILE__, __LINE__);
        $s = $s + $db->af();
        switch ($s) {
          case 1: $t = 'сообщение'; break;
          case 2: case 3: case 4: $t = 'сообщения'; break;
          default: $t = 'сообщений';
        }
        
        $errm[] = "{$s} {$t} удалено.";
      }  
    break;  
  
    
     
    default:
      
      return false;
   }
 }
 
 $ret['submited'] = $submited;
 $ret['v'] = $v;
 $ret['err']=  $err;
 $ret['err_box'] = $std->err_box($err, $errm);
 return $ret;		
}

function item_list($code='to') {  
global $sv, $std, $db; $ret = array();

  $uid = $sv->user['session']['account_id'];
  
  $code = ($code!='to') ? 'from' : 'to';  
  $code2 = ($code!='to') ? 'to' : 'from';
  
  $wh = "m.{$code}='{$uid}' AND m.{$code}<>'0' AND (m.{$code}_del='0' OR  (m.{$code}_del='1' AND m.time_read=0))";
      
  
      
 
  
  $db->q("SELECT 0 FROM {$sv->t['mail']} m WHERE {$wh}", __FILE__, __LINE__);
  $ret['pl'] = $pl = $std->pl($db->nr(), 30, $sv->_get['page'], u($sv->act, $sv->code, '', "page="));
  
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

// END OF CLASS
}


?>