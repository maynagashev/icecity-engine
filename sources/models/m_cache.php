<?php

class m_cache extends class_model {
  
  var $tables = array(
    'cache' => "
      `id` bigint(20) NOT NULL auto_increment,
      `code` varchar(255) default NULL,
      `text` longtext,
      `time` int(11) NOT NULL default '0',
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      PRIMARY KEY  (`id`),
      KEY `code` (`code`)    
    "
  );
    
  var $t = "";
  var $d;

function __construct() {
  global $sv, $std, $db;
  
  $this->t = $sv->t['cache'];
  

  $this->init_field(array(
  'name' => 'code',
  'title' => 'Идентификатор',
  'type' => 'varchar',
  'size' => '255',
  'len' => '20',
  'default' => '',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'unique' => 1
  ));  

 
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Содержимое',
  'type' => 'text',
  'len' => '100',
  'show_in' => array(),
  'write_in' => array('create','edit')  
  ));  

 
  $this->init_field(array(
  'name' => 'time',
  'title' => 'Time',
  'type' => 'time',
  'len' => '20',
  'show_in' => array(),
  'write_in' => array('edit')  
  ));  
  

   
}

function v_code($val) {
  $val = trim($val);
  
  if ($val=='') {
    $this->v_err = true;
    $this->v_errm[] = "Идентификатор не указан";    
  }
  return $val;
}

function parse($d) {
  global $sv, $std;
  
  $d['f_time'] = date("Y-m-d H:i:s", $d['time']);
  return $d;
}
/**
 * Получение контента из кэша
 *
 * @param unknown_type $code
 * @param unknown_type $ser  данные сериализованы?
 * @param unknown_type $create  создавать несщустювущие строки?
 * @param unknown_type $create_value значение по умолчанию для новых строк
 * @return unknown
 */
function get_data($code, $ser=0, $create = 0, $create_value='') {
  global $db, $sv;
  
  $ecode = $db->esc($code); 
  $text = ($ser) ? serialize($create_value) : $create_value;
  
  $d = $this->get_item_wh("`code`='{$ecode}'", 1);
  if ($d!==false) {    
    if ($ser) {
      $ret = unserialize($d['text']);
      
      // if error while unserialization, set default value
      if ($ret === false) {
        $this->update_wh(array('text' => $text), "`code`='{$ecode}'");
        $ret = $create_value;
        $this->log("ошибка unserilize при чтении кэша \"{$code}\", устанавливаем дефолтное значение: {$create_value}");
      }
    }
    else {
      $ret = $d['text'];
    }    
  }
  else {    
    if ($create && $this->create_empty_rec($code, $text)) {      
      $ret = $create_value;
    }
    else {
      $ret = false;
    }
  }
  
  return $ret;
  
}
/**
 * Проверяет существование нужной записи по коду
 * если отсутствует то создает
 *
 * @param unknown_type $code
 * @return 1 или количество аффектных строк
 */
function sync($code, $create = 0, $create_value='') {
  global $sv, $std, $db;
  
  $ecode = $db->esc($code);
  
  $db->q("SELECT 0 FROM {$this->t} WHERE `code`='{$ecode}'", __FILE__, __LINE__);
  $s = $db->nr();
  if ($s>0) {    
    if ($s>1) {
      $d = $db->f();
      $db->q("DELETE * FROM {$this->t} WHERE `code`='{$ecode}' AND `id`<>'{$d['id']}'", __FILE__, __LINE__);
    }
    return $s;
  }
  else {
    return $this->create_empty_rec($code, $create_value);
  }
}

/**
 * READ - get_data synonim
 *
 * @param unknown_type $code
 * @param unknown_type $ser
 * @param unknown_type $create
 * @param unknown_type $create_value
 * @return unknown 
 */
function read($code, $ser=0, $create = 0, $create_value='') {
  return $this->get_data($code, $ser, $create, $create_value);
}

function write($code, $data, $ser=0) {
  global $sv, $db;
  
  $ecode = $db->esc($code);  
  if ($ser)  {
    $data = serialize($data);
  }
  
  $p = array(
  'text' => $data,
  'time' => $sv->post_time
  );
  
  return $this->update_wh($p, "`code`='{$ecode}'"); 
}

function write_with_check($code, $data, $ser=0) {
  global $sv, $db;
  
  $ecode = $db->esc($code);  
  if ($ser)  {
    $data = serialize($data);
  }

  $p = array(
  'text' => $data,
  'time' => $sv->post_time
  );
  
  // check
  $d = $this->get_item_wh("`code`='{$ecode}'");
  // если нету то втславяем 
  if (!$d) {
    $p['code'] = $code;
    $ret = $this->insert_row($p);
  }
  // если есть обновляем
  else {
    $ret = $this->update_wh($p, "`code`='{$ecode}'");   
  }
  
  return $ret;
}



/**
 * creates empty record
 *
 * @param unknown_type $code (unescaped)
 */
function create_empty_rec($code, $text='') {
  global $sv, $db;
  
  $p = array(
    'code' => $code,
    'time' => $sv->post_time,
    'text' => $text
  );
  
  return $this->insert_row($p);
}
  
  
}

?>