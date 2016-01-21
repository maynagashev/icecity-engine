<?php

class m_fcat extends class_model {

  var $name = 'Категория форумов';
  var $tables = array(
    'fcats' => "    
      `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
      
      `title` varchar(255) not null default '',  
      `description` text null,  
      `status_id` tinyint(1) not null default '1',
      
      `place`  int(11) not null default '0',
      
      `created_at` DATETIME NULL ,
      `created_by` INT( 11 ) NULL ,
      `updated_at` DATETIME NULL ,
      `updated_by` INT( 11 ) NULL ,
      `expired_at` DATETIME NULL,
      
      PRIMARY KEY ( `id` )
    "
  );
  
  var $status_ar = array(
    0 => "Выключена", 
    1 => "Включена",    
  );
  
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['fcats'];  
  
   
  $this->init_field(array(
  'name' => 'place',
  'title' => 'Порядок',
  'type' => 'int',
  'default' => 0,  
  'len' => 6,
  'show_in' => array('default'),
  'write_in' => array('create', 'edit'),
  ));    
    
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название категории',
  'type' => 'varchar',  
  'len' => 50,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create'),
  'unique' => 1
  ));    
  

  
  $this->init_field(array(
  'name' => 'description',
  'title' => 'Описание категории',
  'type' => 'text',  
  'len' => 70,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create')
  ));    
   

  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Статус',
  'type' => 'int',
  'default' => 1,
  'input' => 'select',
  'show_in' => array(),
  'write_in' => array('create', 'edit'),
  'belongs_to' => array('list' => $this->status_ar, 'not_null'=>1)
  ));   
    
   
  $this->init_field(array(
  'name' => 'status',
  'title' => 'Статус',
  'show_in' => array('default'),
  'virtual' => 'status_id'
  ));      
  
 
   
}

function parse($d) {
  global $sv;
  $sv->load_model('forum');
  $d['url'] = $sv->m['forum']->forum_url."/?cat=".$d['id'];
  return $d;
}

//eoc
}

?>