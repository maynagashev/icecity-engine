<?php

/*
Модель кино/концертных/выставочных залов и площадок
*/

class m_hall extends class_model {

  var $per_page = 20;
  
  var $tables = array(
    'halls' => "
      `id` int(20) NOT NULL auto_increment,
      `title` varchar(255) NOT NULL default '',
      `type`  varchar(255) NOT NULL default '',
      `text`  text default NULL,
      `temp`  tinyint(1) NOT NULL default '0',
      `status_id`  tinyint(1) NOT NULL default '1',
      `place` int(11) NOT NULL default '1000',
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
        
      PRIMARY KEY  (`id`),
      KEY (`type`)  
    "
  );

  var $type_ar = array(
    'kino'    => 'Кинотеатры',
    'theatre' => 'Театры',
    'concert' => 'Концертный зал',
    'club'    => 'Клуб',
    'square'  => 'Площадь',
    'etc'     => 'Другое'
  );
  
  var $status_ar = array(
    0 => 'Отключена',
    1 => 'Включена'
  );

function __construct() {
  global $sv, $db;  
  
  $this->t = $sv->t['halls'];
 
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название площадки',
  'type' => 'varchar',   
  'len' => 60,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'), 
  'search' => 1
  ));    
  
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Описание',
  'type' => 'text',
  'len' => '70',
  'show_in' => array('remove'),
  'write_in' => array('edit', 'create'),
  'search' => 1
  ));  
  
  $this->init_field(array(
  'name' => 'type',
  'title' => 'Тип',
  'type' => 'varchar',
  'len' => '50',
  'input' => 'select',
  'belongs_to' => array('list' => $this->type_ar),
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create')
   
  ));    

  $this->init_field(array(
  'name' => 'temp',
  'title' => 'Временная площадка?',
  'type' => 'boolean',
  'len' => '3',
  'show_in' => array('remove'),
  'write_in' => array('edit', 'create'),
  'selector' => 0
 
  ));        

  
  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Статус',
  'type' => 'tinyint',
  'len' => '3',
  'default' => 1,
  'input' => 'select',
  'belongs_to' => array('list' => $this->status_ar),
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create')
  ));    
  
  
  $this->init_field(array(
  'name' => 'place',
  'title' => 'Порядок для сортировки',
  'type' => 'int',
  'len' => '5',
  'default' => 10,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create')
  ));    
  
}

function v_title($t) {
  $t = strip_tags($t);
  
  return $t;
}
function v_type($t) {
  
  $t = strip_tags($t);
  $t = preg_replace("#[^a-z0-9\_\ \.]#si", "", $t);
  
  return $t;
}

function v_status_id($id) {
  $id = intval($id);
  return $id;
}

function v_temp($t) {
  return intval($t);
}

function v_place($i) {
  $i = intval($i);
  return $i;
}
//eoc
}

?>