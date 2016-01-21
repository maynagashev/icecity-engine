<?php

/**
 * 
  'valuta' => array('cat' => 'Магазин', 'title' => 'Валюта', 'value' => "руб."),  
  'webmoney_r' => array('cat' => 'Магазин', 'title' => 'Рублевый Webmoney кошелек', 'value' => 'R970464351240'),
  'accept_webmoney' => array('cat' => 'Магазин', 'title' => 'Принимать webmoney?', 'value' => 1)  
  
  CHANGES
  ---------------------------
  ALTER TABLE `config`  
  ADD `type` VARCHAR( 255 ) NULL AFTER `title`,
  ADD `len` INT( 11 ) NULL AFTER `type`;
 *
 */



class m_config extends class_model {
  
  var $tables = array(
    'config' => "
      `id` bigint(20) NOT NULL auto_increment,
      `name` varchar(255) NOT NULL,
      `title` varchar(255) default NULL,
      `type` varchar(255) default null,
      `len` int(11) not null default '0',
      `value` mediumtext NOT NULL,
      `cat` varchar(255) default NULL,
      `is_array` tinyint(1) NOT NULL default '0',
      `created_at` datetime default NULL,
      `created_by` int(11) default NULL,
      `updated_at` datetime default NULL,
      `updated_by` int(11) default NULL,
      PRIMARY KEY  (`id`)    
    "
  );

   
  var $loaded = false;
  var $cfg = array();
  var $name_list = array();
  
  
  var $vars = array(
  'site_title' => array('cat' => 'Основные настройки', 'title' => 'Название сайта', 'value' => 'Company Name', 'type' => 'varchar', 'len' => 50),
  'short_title' => array('cat' => 'Основные настройки', 'title' => 'Короткое название сайта', 'value' => 'Company', 'type' => 'varchar', 'len' => 50),
  'slogan' => array('cat' => 'Основные настройки', 'title' => 'Слоган сайта', 'value' => 'Company Slogan', 'type' => 'varchar', 'len' => 80),  
  'email' => array('cat' => 'Основные настройки', 'title' => 'Email', 'value' => 'webmaster@company.com', 'type' => 'varchar', 'len' => 50),  
  'phone' => array('cat' => 'Основные настройки', 'title' => 'Контактный телефон', 'value' => '+7 (код) номер', 'type' => 'varchar', 'len' => 50),  
 
  'notify_email' => array('cat' => 'Крон', 'title' => 'Email для различных уведомлений', 'value' => 'webmaster@company.com', 'type' => 'varchar', 'len' => 50),
  'last_notify' => array('cat' => 'Крон', 'title' => 'Время и дата последней рассылки уведомлений', 'value' => '', 'type' => 'varchar', 'len' => 50),  
  );
  var $keys;
  
  /**
   * Категории
   *  
   * @var array ('Основные настройки' => 'Основные настройки', ...)
   */
  var $cats;
  
  /**
   * Количество элементов по категориям
   *
   * @var array ('Основные настройки' => 4, ...)
   */
  var $cats_count;
  
  
  var $types_ar = array(
    '' => 'Обычное поле',
    'boolean' => 'Истина или ложь (селектор)',
    'checkbox' => 'Истина или ложь (галочка)',
    'int' => 'Целое число (int)',
    'integer' => 'Целое число (integer)',
    'bigint' => 'Большое целое число (bigint)',
    'float' => 'Число с плавающей запятой (float)',
    'double' => 'Число двойной точности (double)',
    'varchar' => 'Строка 255 симв.',
    'password' => 'Строка 255 симв. (пароль)',
    'file' => 'Файл',
    'text' => 'Текстовое поле',
    'select' => 'Обычный селектор (массив)',
    'multiselect' => 'Мультиселектор (массив)',
    'date' => 'Дата',
    'datetime' => 'Дата и время',
    'time' => 'Время в формате UNIX',
    'custom' => 'Свой обработчик (ci_value)' 
  );
 
  
  var $load_attaches = 0;
  var $load_markitup = 1;
  
  var $attaches_page = '';
  var $attaches_top_margin = 500;
  var $markitup_use_tinymce = 1;
  var $markitup_use_emoticons = 0;
  var $markitup_width = '100%';
  var $markitup_selector = 'textarea';
  var $markitup_type = 'html';
    
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['config'];
  $this->keys = array_keys($this->vars);
  
  $this->init_cats();
  
  $this->init_field(array(
  'name' => 'cat',
  'title' => 'Группа',
  'type' => 'varchar',
  'input' => 'select',
  'size' => '255',
  'len' => '30',
  'default' => '0',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'belongs_to' => array('list' => &$this->cats)  
  ));  

   
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название переменной (рус.)',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'unique' => 1
  ));    

   
  $this->init_field(array(
  'name' => 'name',
  'title' => 'Идентификатор (англ.)',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '30',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'unique' => 1
  ));    
  
   
  $this->init_field(array(
  'name' => 'type',
  'title' => 'Тип данных (input)',
  'type' => 'varchar',  
  'input' => 'select',
  'show_in' => array('default', 'remove', 'edit'),
  'write_in' => array('create'),
  'belongs_to' => array('list' => $this->types_ar),
  'selector' => 1
  ));    

   
  $this->init_field(array(
  'name' => 'size',
  'title' => 'Размер поля (символов)',
  'type' => 'int',  
  'show_in' => array('remove'),
  'write_in' => array('create'),
  'default' => 70
  ));    
    
   
  $this->init_field(array(
  'name' => 'value',
  'title' => 'Значение',
  'type' => 'text', 
  'size' => '255',
  'len' => '70',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  
  ));  
  

  /*
  $this->init_field(array(
  'name' => 'custom',
  'title' => 'Custom?',  
  'show_in' => array('default', 'edit'),
  'virtual' => 'name'
  ));  
    */

  
}

// VALIDATIONS
function v_name($val) {
  
  return $val;
}

function df_custom($val) {
 
  $r = (in_array($val, $this->keys)) ? "<span style='color:blue;'>bundled</span>" : "custom";
  
  return $r;
}

// PRE-POST ACTIONS
function before_edit() {
  
  // отправляем в wrow настройки инпута текущего параметра
  $this->fields['value']['input'] = ($this->d['type']!='') ? $this->d['type'] : $this->fields['value']['input'];
  $this->fields['value']['len'] = ($this->d['len']>0) ? $this->d['len'] : $this->fields['value']['len'];
}

// STUFF

/**
 * Генерим список категорий на основе списка записей из таблицы или дефолтного массива
 *
 * @param unknown_type $vars
 * @return unknown
 */
function init_cats($vars = false) {
  
  $vars = (is_array($vars)) ? $vars : $this->vars;
  
  $counts = array();  
  foreach($vars as $k=> $d) {    
    $counts[$d['cat']] = (isset($counts[$d['cat']])) ? $counts[$d['cat']]+1 : 1;
  }  
  $this->cats_count = $counts;
  
  $ret = array(); 
  foreach($counts as $cat => $count) {
    $ret[$cat] = $cat;
  }
  $this->cats = $ret;
  //$this->fields['cat']['belongs_to']['list'] = $ret;
  
  return $ret;  
}

function load_cfg($force = 0) {
  global $sv, $db;
  
  if ($this->loaded && !$force) return $this->cfg;
  
  $ar = array(); 
  $db->q("SELECT * FROM {$this->t}", __FILE__, __LINE__);
  while($d = $db->f()) {
    $ar[$d['name']] = $d;
  }
  $this->init_cats($ar);

  // проверяем наличие встроеных параметров
  $ar = $this->sync_vars($ar, $this->vars);
  
  // инициируем все переменные по списку
  $cfg = array();
  foreach($ar as $name => $d) {
    $cfg[$name] = (isset($d['value'])) ? $d['value'] : null;    
  }
  
  $this->cfg = $cfg;
  $this->name_list = $ar;
  $this->loaded = 1;
  
  return $cfg;
}

/**
 * Проверяем в текущем списке параметров, наличие указанных параметров, если нету то добавляем в базу
 * Возвращем объединенный массив.
 * В качестве текущего массива можно передавать $sv->cfg = array ($name => $val, ...), 
 * но лучше передвать $sv->m['config']->name_list (поименованный текущий список параметров из последней выборки)
 *
 * @param array $ar = array($name => $d/$v, ...)
 * @param array $check_ar = array($name2 => $d2, ...)
 * @return array($name => $d/$v, $name2 => $d2, ...)
 */

function sync_vars($ar, $check_ar) {
  
  $keys = array_keys($ar);    
  $to_insert = array();
 
  // проверяем новый массив на наличие элементов в текущей конфигурации
  foreach($check_ar as $name => $d) {
    // если в текущей нет нового то добавляем 
    if (!in_array($name, $keys)) {
      $to_insert[$name] = $d;
      $ar[$name] = $d;
    }
  }
  
  foreach($to_insert as $name => $d) {
    $p = array();
    $p['name'] = $name;
    $p['cat'] = (isset($d['cat'])) ? $d['cat'] : '';
    $p['value'] = (isset($d['value'])) ? $d['value'] : '';
    $p['title'] = (isset($d['title'])) ? $d['title'] : '';
    $p['type'] = (isset($d['type'])) ? $d['type'] : '';
    $p['len'] = (isset($d['len'])) ? $d['len'] : 0; 
    $this->insert_row($p); 
  }
  
  return $ar;
}

function set_val($name, $value) {
  global $sv, $std, $db;
  
  $q = "UPDATE {$this->t} SET `value`='".$db->esc($value)."' WHERE `name`='".$db->esc($name)."'";
  $db->q($q, __FILE__, __LINE__);
  
  return $db->af();
}


//eoc
}

?>