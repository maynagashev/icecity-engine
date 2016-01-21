<?php

/**
 * Модель простого баннерного html блока, с записью массива активных блоков в файл
 */

class m_adv extends class_model {

var $tables = array(
  'adv' => "
    `id` bigint(20) NOT NULL auto_increment,
    `title` varchar(255) default NULL,
    `html` text NOT NULL default '',
    `status_id` tinyint(1) NOT NULL default '0',
    
    `views` bigint(20) not null default '0',
    `clicks` bigint(20) not null default '0',
    
    `created_at` datetime default NULL,
    `created_by` int(11) default NULL,
    `updated_at` datetime default NULL,
    `updated_by` int(11) default NULL,
      
    PRIMARY KEY  (`id`)
  "
);

var $adv; // <-- class_adv object

var $status_ar = array(
  0 => "Выключен",
  1 => "Включен"
);

var $click_url = "";
var $db_path = "";

function __construct() {
  global $sv;  
  
  $this->t = $sv->t['adv'];
    
  $sv->init_class("adv");  
  $this->db_path = &$this->adv->db_path;
  
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название',
  'type' => 'varchar',  
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'unique' => 1,
  'not_null' => 1
  ));    
   
  
  $this->init_field(array(
  'name' => 'info',
  'title' => 'Инфо',
  'virtual' => 'id',
  'show_in' => array('create', 'edit'),
  'write_in' => array()
  ));  
     
  $this->init_field(array(
  'name' => 'html',
  'title' => 'HTML код блока',
  'type' => 'text',  
  'len' => '80',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'unique' => 1
  ));    
  
  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Статус',
  'type' => 'tinyint',
  'input' => 'select',
  'show_in' => array('default','remove'),
  'write_in' => array('create', 'edit'), 
  'belongs_to' => array('list' => $this->status_ar)
  
  ));  
  
  $this->init_field(array(
  'name' => 'views',
  'title' => 'Показы',
  'type' => 'bigint',
  'len' => '20',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));  
  
  $this->init_field(array(
  'name' => 'clicks',
  'title' => 'Клики',
  'type' => 'bigint',
  'len' => '20',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));  


    
    
} 

function vcb_info($id) {
  
  $id = (!$id) ? "[будет доступен после создания записи]" : $id;
  $url = $this->click_url."?id={$id}";
  
  $ex =  (file_exists($this->db_path)) 
    ? "<span style='color:green;'>существует</span>" :
      "<span style='color:red;'>не существует</span>";
  
  $path = (file_exists($this->db_path)) ? $this->db_path : dirname($this->db_path);
    
  $wr =  (is_writeable($path)) 
    ? "<span style='color:green;'>доступен для записи</span>" :
      "<span style='color:red;'>не доступен для записи</span>";
  
  $ret = "
  <p>URL для учета переходов: <b><A href='{$url}'>{$url}</a></b></p>
  <p>Путь к файлу с базой активных кодов: <b>{$this->db_path}</b> <br>
  {$ex} &mdash; {$wr}</p>
  ";
  
  
  return $ret;
}

function after_update() {
  $this->db_sync();  
}
function after_create() {
  $this->db_sync();
}
function after_remove() {
  $this->db_sync();
}

function db_sync() {
  global $sv, $std, $db;
  
  $ar = $this->item_list("status_id='1'", "id ASC", 0, 1);
  
  if (count($ar['list'])<=0) {
    $this->v_errm[] = "Массив активных баннеров пуст.";
  }
  
  $ser = serialize($ar['list']);

  if (!is_writeable($this->db_path)) {
    $this->v_errm[] = "{$this->db_path} не доступен для записи";
  }
  
  if(file_put_contents($this->db_path, $ser)) {
    $this->v_errm[] = "{$this->db_path} успешно синхронизирован.";
  }
  else {
    $this->v_errm[] = "{$this->db_path} ошибка записи.";
  }
  
  echo "<pre>"; print_r($this->v_errm); echo "</pre>";
}
//eoc
}

?>