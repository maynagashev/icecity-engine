<?php
/**
 * Публичный модуль управления подпиской rev.2
 * ! надо доработать и заюзать std->mail
 *
 */

class subscribe {
  
var $email = null;
var $site_title = "";
var $site_url = "";
var $antispam = 0;
var $headers = "";


function auto_run() {
  global $sv, $std, $db;
  
  $eol="\r\n";
  $mime_boundary=md5(time());
  $headers = "";
  
  $this->site_url = $sv->vars['site_url'];
  $this->site_title = $sv->vars['site_title'];
  
  $fromname = $this->site_title;
  $fromaddress = $sv->cfg['email'];
  
  // Common Headers
  $headers .= "From: ".$fromname." <".$fromaddress.">".$eol;
  $headers .= "Reply-To: ".$fromname." <".$fromaddress.">".$eol;
  $headers .= "Return-Path: ".$fromname." <".$fromaddress.">".$eol;    // these two to set reply address
  $headers .= "Message-ID: <".time()."-".$fromaddress.">".$eol;
  
  $this->headers = $headers;
  
  $sv->load_model('subscriber');
  
  if (!$this->email()) {    
    $sv->code = 'form';     
    $ret['invalid'] = ($this->email===false) ? 1 : 0;
  }
  else {
    $d = $sv->m['subscriber']->get_data($this->email);
    
    if ($d===false) {
      $sv->code = 'notexists';
      $ret['s'] = $this->submit(array('subscribe'));      
    }
    elseif ($d['accepted']) {
      $sv->code = 'accepted';
      $ret['s'] = $this->submit(array('remove', 'removing'));
      
    }
    elseif (!$d['accepted']) {
      $sv->code = 'notaccepted';
      $ret['s'] = $this->submit(array('activate', 'accepting'));      
    }
  }
  
  $ret['url'] = '/subscribe/';
  $ret['email'] = $this->email;
  
  return $ret;
}

function email() {
  global $sv, $std, $db;
  
  $email = (isset($sv->_post['email'])) ? $sv->_post['email'] : null;  
  $email = (isset($sv->_get['email'])) ? $sv->_get['email'] : $email;  
  
  
  if (!is_null($email)) {
    $this->email = ($std->text->v_email($email)) ? $email : false;
  }
  else {
    $this->email = null;
  }
  
  $ret = (is_null($this->email) || $this->email===false) ? 0 : 1;
 
  return $ret;
}

function submit($codes) {
  global $sv, $std, $db;
  
  $action = (isset($sv->_post['action'])) ? $sv->_post['action'] : null;
  $action = (isset($sv->_get['action'])) ? $sv->_get['action'] : $action;
  
  if (in_array($action, $codes)) {
    if (method_exists($this, "s_".$action)) {
      $e = "\$ret = \$this->s_{$action}();";      
      eval($e);    
      return $ret;
    }
    else {
      die("Method <b>s_{$action}</b> not exists.");
    }
  }
  else {
    return false;
  }
   
}


function s_subscribe() {
  global $sv, $db, $std;

  $err = false; 
  $errm = array();
  
  $email = addslashes($this->email);
  $p = $s = array();
  $p['email'] = $this->email;
  $p['accepted'] = 0;
  $p['status_id'] = 1;
  $p['created_at'] = $sv->date_time;
  $p['expires_at'] = date("Y-m-d H:i:s", $sv->post_time + 60*60*24*3);
  $p['ip'] = $sv->ip;
  $p['last_send'] = $sv->date_time;
  $p['accept_code'] = $sv->m['subscriber']->random();
  $p['remove_code'] = $sv->m['subscriber']->random();
  
  foreach($p as $k=>$v) {
    $s[] = "`{$k}`='".addslashes($v)."'";
  }
    
  $db->q("INSERT INTO {$sv->t['subscribers']} SET ".implode(", ", $s), __FILE__, __LINE__);
  $inserted = ($db->af()>0) ? 1 : 0;
  
  $sended = false;
  
  if ($inserted) {    
    $title = "Подтверждение подписки  {$this->site_title}";
    $msg = "
      Подписка на новости сайта {$this->site_title}:
      
      Адрес для подтверждения подписки: {$this->site_url}/subscribe/?email={$p['email']}&action=accepting&accept_code={$p['accept_code']}
      
      P.s. Если в не заказывали данную подписку, просто удалите это письмо, ваш адрес будет удален из нашей базы автоматически - в течении нескольких дней.
    ";
    $sended = mail($p['email'], $title, $msg, $this->headers);
  }
  
  if ($inserted && $sended) {
    $errm[] = "Письмо с кодом активации и дальнейшие инструкции - отправлены на адрес <b>{$this->email}</b>, проверьте почту.";
  }
  elseif ($inserted)  {
    $errm[] = "Адрес добавлен в базу данных подписчиков, но не активирован, так как не удалось отправить код активации на указанный адрес, 
    попробуйте активировать этот адрес позже.";
  }
  else {
    $err = 1;
    $errm[] = "Не удалось добавить адрес.";
  }
  
  
  
  
  $ret['err'] = $err;
  $ret['err_box'] = $std->err_box($err, $errm);
  
  return $ret;
}

function s_activate() {
  global $sv, $db, $std;

  $err = false; 
  $errm = array();
  
  $email = addslashes($this->email);
  $db->q("SELECT * FROM {$sv->t['subscribers']} WHERE `email`='{$email}'", __FILE__, __LINE__);
  if ($db->nr()<=0) {
    die("Адрес <b>{$email}</b> не найден в базе.");
  }
  
  $d = $db->f();
  
  if ($this->antispam) {
    $time = strtotime($d['last_send']);
    $exp = $sv->post_time - 60*15;
    if ($time>$exp) {
      $err = 1;
      $errm[] = "Вы отправляете запросы слишком часто, операция отменена, попробуйте повторить ее позже.";
    }
  }
  
  $sended = false;
  if (!$err) {
    $title = "Подтверждение подписки  {$this->site_title} (повторное письмо)";
    $msg = "
      Подписка на новости сайта {$this->site_title}:
      
      Адрес для подтверждения подписки: {$this->site_url}/subscribe/?email={$d['email']}&action=accepting&accept_code={$d['accept_code']}
      
      P.s. Если вы не заказывали данную подписку, просто удалите это письмо, ваш адрес будет удален из нашей базы автоматически - в течении нескольких дней.
    ";
    $sended =  mail($d['email'], $title, $msg, $this->headers);
    
    $db->q("UPDATE {$sv->t['subscribers']} SET `last_send`='{$sv->date_time}' WHERE id='{$d['id']}'", __FILE__, __LINE__);
    if ($sended) {
      $errm[] = "Повторное письмо с кодом активации успешно отправлено на адрес <b>{$d['email']}</b>.";
    }
    else {
      $err = 1;
      $errm[] = "Не удалось отправить письмо на адрес <b>{$d['email']}</b>, сообщите об ошибке администратору, либо повторите операцию позднее.";      
    }
  }
 
  
  $ret['err'] = $err;
  $ret['err_box'] = $std->err_box($err, $errm);
  
  return $ret;
}

function s_accepting() {
  global $sv, $std, $db;
  
  $err = false;
  $errm = array();
  
  $email = addslashes($this->email);
  $code = (isset($sv->_get['accept_code'])) ? $sv->_get['accept_code'] : null;
  if (is_null($code)) {
    $err = true;
    $errm[] = "Не указан код активации.";
  }
  else {
    $db->q("SELECT * FROM {$sv->t['subscribers']} WHERE email='{$email}'", __FILE__, __LINE__);
    if ($db->nr()<=0) {
      $err = true;
      $errm[] = "Email не найден в базе.";
    }
    else {
      $d = $db->f();
    }
  }
  
  if (!$err) {
   
    if ($d['accepted']==1) {
      $err = 1;
      $errm[] = "Подписка уже была подтверждена ранее.";
    }
    elseif ($d['accept_code']!==$code) {
      $err = 1; 
      $errm[] = "Код активации не верен.";
    }
    else {
      $db->q("UPDATE {$sv->t['subscribers']} SET accepted='1' WHERE id='{$d['id']}'", __FILE__, __LINE__);
      $errm[] = "Подписка на адрес <b>{$d['email']}</b> успешно подтверждена.";
    }
  }
  
  $ret['err'] = $err;
  $ret['err_box'] = $std->err_box($err, $errm);
  
  return $ret;
}

function s_remove() {
  global $sv, $db, $std;

  $err = false; 
  $errm = array();
  
  $email = addslashes($this->email);
  $db->q("SELECT * FROM {$sv->t['subscribers']} WHERE `email`='{$email}'", __FILE__, __LINE__);
  if ($db->nr()<=0) {
    die("Адрес <b>{$email}</b> не найден в базе.");
  }
  
  $d = $db->f();
  
  if ($this->antispam) {
    $time = strtotime($d['last_send']);
    $exp = $sv->post_time - 60*15;
    if ($time>$exp) {
      $err = 1;
      $errm[] = "Вы отправляете запросы слишком часто, операция отменена, попробуйте повторить ее позже.";
    }
  }
  
  $sended = false;
  if (!$err) {
    $title = "Отмена подписки на новости сайта  {$this->site_title}";
    $msg = "
      Удаление адреса {$d['email']} из базы данных подписчиков сайта {$this->site_title}:
      
      Адрес для удаления подписки: {$this->site_url}/subscribe/?email={$d['email']}&action=removing&remove_code={$d['remove_code']}
      
      P.s. Если вы не заказывали отмену подписки, просто удалите это письмо, новости сайта будут по прежнему приходить к вам на email.
    ";
    $sended =  mail($d['email'], $title, $msg, $this->headers);
    
    $db->q("UPDATE {$sv->t['subscribers']} SET `last_send`='{$sv->date_time}' WHERE id='{$d['id']}'", __FILE__, __LINE__);
    if ($sended) {
      $errm[] = "Письмо с кодом удаления подписки успешно отправлено на адрес <b>{$d['email']}</b>.";
    }
    else {
      $err = 1;
      $errm[] = "Не удалось отправить письмо на адрес <b>{$d['email']}</b>, сообщите об ошибке администратору, либо повторите операцию позднее.";      
    }
  }
 
  
  $ret['err'] = $err;
  $ret['err_box'] = $std->err_box($err, $errm);
  
  return $ret;
}

function s_removing() {
  global $sv, $std, $db;
  
  $err = false;
  $errm = array();
  
  $email = addslashes($this->email);
  $code = (isset($sv->_get['remove_code'])) ? $sv->_get['remove_code'] : null;
  if (is_null($code)) {
    $err = true;
    $errm[] = "Не указан код удаления.";
  }
  else {
    $db->q("SELECT * FROM {$sv->t['subscribers']} WHERE email='{$email}'", __FILE__, __LINE__);
    if ($db->nr()<=0) {
      $err = true;
      $errm[] = "Email не найден в базе.";
    }
    else {
      $d = $db->f();
    }
  }
  
  if (!$err) {
   
    if ($d['remove_code']!==$code) {
      $err = 1; 
      $errm[] = "Код удаления не верен.";
    }
    else {
      $db->q("DELETE FROM {$sv->t['subscribers']} WHERE id='{$d['id']}'", __FILE__, __LINE__);
      $errm[] = "Подписка на адрес <b>{$d['email']}</b> успешно отменена.";
    }
  }
  
  $ret['err'] = $err;
  $ret['err_box'] = $std->err_box($err, $errm);
  
  return $ret;
}


  
}

?>