<?php

class m_url extends class_model {

  var $title = "Адреса страниц сайта";
  
  var $tables = array(
    'urls' => "
      `id` bigint(20) NOT NULL auto_increment,
      `url` varchar(255) BINARY NOT NULL default '',
      `page` bigint(20) NOT NULL default '0',
      `title` varchar(255) default NULL,
      
      `primary` tinyint(1) NOT NULL default '0',
      
      `module` varchar(255) default NULL,
      `object` int(11) not null default '0',
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      PRIMARY KEY  (`id`),
      KEY `url` (`url`),
      KEY `page` (`page`,`primary`)    
    "
  );

    
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['urls'];
  $this->per_page = 50;    
    
  

  $this->init_field(array(
  'name' => 'url',
  'title' => 'Адрес',
  'type' => 'varchar',  
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'unique' => 1
  ));    
  
  $this->init_field(array(
  'name' => 'page',
  'title' => 'Страница',
  'type' => 'bigint',
  'len' => '5',
  'default' => '',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  
  ));  
   $this->init_field(array(
  'name' => 'title',
  'title' => 'Название',
  'type' => 'varchar',
  'len' => '40',
  'default' => '',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  
  ));  
  
  $this->init_field(array(
  'name' => 'primary',
  'title' => 'Основной',
  'type' => 'tinyint',
  'input' => 'boolean',
  'default' => '1',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  
  ));  
        
  
  $this->init_field(array(
  'name' => 'module',
  'title' => 'Модуль',
  'type' => 'varchar',
  'len' => '30',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit'),
  'selector' => 1
  ));    
  
  $this->init_field(array(
  'name' => 'object',
  'title' => 'Объект',
  'type' => 'int',
  'len' => '10',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')  
  ));     
      
}

function parse($d) {
  
  $d['url'] = $this->compile_url($d['url']);
  
  return $d;
}
function v_url($val) {
  
  $val = preg_replace("#[^a-z0-9\_\-\/\.]#msi", "", $val);
  
  if ($val=='') {
    $this->v_err = true;
    $this->v_errm = "Не указан адрес.";
  }
  
  return $val;
}

function after_create() {
  global $sv, $db;
  
  t($db->log, 20);
  
}

/**
 * Обработка prefix_url при выводе
 *
 * @param unknown_type $url
 * @return unknown
 */
function compile_url($url) {
  global $sv, $std, $db;
  
  if (isset($sv->view->prefix_url)) {
    $url = preg_replace("#^".preg_quote($sv->view->prefix_url)."#si", "", $url);
    $url = ($url=='') ? "/" : $url;
  }
  
  return $url;
}

/**
 * *
 *  Создает или обновляет адрес url с параметрами $p
 * @param unknown_type $url
 * @param unknown_type $p
 * @return unknown
 */
function sync_url($url, $p=array()) {
  global $sv, $std, $db;
  
  if (!is_array($p) || count($p)<=0) return false;
  
  $p['url'] = $url;
  $eurl = $db->esc($url);
  $d = $this->get_item_wh("`url`='{$eurl}'", 0, 1);
  if (!$d) {
    $this->insert_row($p);
  }
  else {
    $this->update_wh($p, "`url`='{$eurl}'");
  }
  
  return true;
}

/**
 * *
 *  Создает или обновляет параметры урл по заданному условию
 * @param unknown_type $wh
 * @param unknown_type $p
 * @return unknown
 */
function sync_url_wh($wh, $p=array()) {
  global $sv, $std, $db;
  
  if (!is_array($p) || count($p)<=0) return false;
  
  $d = $this->get_item_wh($wh, 0, 1);
  if (!$d) {
    $this->insert_row($p);
  }
  else {
    $this->update_wh($p, $wh);
  }

  // проверка повторов один адрес должен вести только на одну страницу
  if (isset($p['url'])) {
    $ar = $this->item_list("`url`='".$db->esc($p['url'])."'", "", 0, 0);
    if ($ar['count']>1) {
      echo ("Внимание! Адрес <b>{$p['url']}</b> повторяется в таблице URLS, возможна ошибка в программе.");
    }
    
  }
  return true;
}


//eoc
}  
  
?>