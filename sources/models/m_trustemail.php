<?php

/*
  доверенный почтовые адреса, с которых можно писать без модерации
*/

class m_trustemail extends class_model {
  
  var $tables = array(
    'trust_emails' => "
      `id` bigint( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
      `email` varchar(255) null,
      `ip` VARCHAR( 255 ) null,
            
      `created_at` DATETIME NOT NULL ,
      `created_by` INT( 11 ) NOT NULL ,
      `updated_at` DATETIME NOT NULL ,
      `updated_by` INT( 11 ) NOT NULL, 
      KEY (`email`)
    "
  );

  
function __construct() {
  global $sv, $std;  
  
  $this->t = $sv->t['trust_emails'];
  
  
  $this->init_field(array(
  'name' => 'email',
  'title' => 'Email',
  'type' => 'varchar',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));  
   
}

function parse($d) {
  global $sv, $std;
  return $d;
}

function last_v($p) {  
  global $sv;
  if ($this->code=='create') {
    $p['ip'] = $sv->ip;
  }
  
  return $p;
}

// ACTIONS 
function add_email($email, $ip) {
  global $sv, $std, $db;
  
  $ret = 'Добавляем новый адрес';
  
  $email = $db->esc($email);
  $d = $this->get_item_wh("`email`='{$email}'", 0, 0);
  if ($d) {
    $ret = 'Такой email уже есть';
  }
  else {
    $p = array('email' => $email, 'ip' => $ip);
    $af = $this->insert_row($p);
    if ($af) {
      $ret = "{$email} добавлен в траст";
    }
    else {
      $ret = "не удалось доабвить в траст {$email}";
    }
  }
  
  return $ret;
  
}

function get_trust($email) {
  global $sv, $db;
  
  $email = $db->esc($email);
  $d = $this->get_item_wh("`email`='{$email}'", 0, 0);
  $ret = ($d) ? 1 : 0;
  
  return $ret;
}

// eoc
}

?>