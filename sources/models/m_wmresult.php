<?php

class m_wmresult extends class_model {

  var $title = "Результаты WM платежей";
  
  var $tables = array(
    'wmresults' => "
      `id` bigint(20) NOT NULL auto_increment,
      `date` date null,
      `text` text null,
      `ip` varchar(255) null,
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      
      PRIMARY KEY  (`id`),
      KEY (`date`)
    "
  );

    
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['wmresults'];
  $this->per_page = 50;    
    
  

  $this->init_field(array(
  'name' => 'date',
  'title' => 'Дата',
  'type' => 'datetime',  
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));    
  
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Содержимое',
  'type' => 'text',
  'len' => '70',
  'default' => '',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  
  ));  
  
   $this->init_field(array(
  'name' => 'ip',
  'title' => 'IP',
  'type' => 'varchar',
  'len' => '30',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  
  ));  

  
}


//eoc
}


?>