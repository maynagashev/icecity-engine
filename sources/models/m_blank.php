<?php

class m_blank extends class_model {

  var $tables = array(
    'blank' => "
      `id` bigint(20) NOT NULL auto_increment,
      
      `name` varchar(255) NOT NULL default '',
      `phone` varchar(255) NOT NULL default '',      
      `text` text null,
       
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      
      PRIMARY KEY  (`id`)    
        "
  );

function __construct() {
  global $sv;  
  
  $this->t = $sv->t['blank'];

  $this->init_field(array(
  'name' => 'title',
  'title' => 'Имя',
  'type' => 'varchar',  
  'len' => 40,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  ));    
  
  $this->init_field(array(
  'name' => 'phone',
  'title' => 'Телефон',
  'type' => 'varchar',
  'len' => '25',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));  
    
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Время звонка',
  'type' => 'text',
  'len' => 50,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));  
    
}

// PARSERS
function parse($d) {
  
  
  return $d;
}


// VALIDATORS ============================


// CALLBACKS ===============================



// PRE / POST ASCTIONS =====================

function before_update() {

} 

function after_change($p) {

}

 
// eoc
} 

?>