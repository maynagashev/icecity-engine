<?php

/*
Запросы SMS шлюза
*/

class m_sms extends class_model {
  
  var $tables = array(
    'sms' => "
      `id` bigint(20) NOT NULL auto_increment,
      `time` int(11) null,
      `date` datetime null,
      `text` text null,
      `subno` varchar(255) null,
      `code` varchar(255) null,
      
      `src_time` varchar(255) null,
      `src_get` text null,
      `src_post` text null,
      `ip` varchar(255) null,
      
      `status_id` tinyint(1) not null default '1',
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      
      PRIMARY KEY  (`id`),
      KEY (`date`),
      KEY (`code`)
    "
  );
  
  
  var $status_ar = array(
    0 => 'Скрыто',
    1 => 'Опубликовано',
    2 => 'Ошибка'
  );
  
     
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['sms'];
  
  
  $this->init_field(array(
  'name' => 'time',
  'title' => 'Время (unix)',
  'type' => 'varchar',
  'len' => 20,  
  'show_in' => array('remove'),  
  'write_in' => array('edit')
  ));      
  
  $this->init_field(array(
  'name' => 'created_at',
  'title' => 'Дата',
  'type' => 'datetime',    
  'show_in' => array('default', 'edit'),
  'write_in' => array()
  ));    
  
  $this->init_field(array(
  'name' => 'date',
  'title' => 'Время сообщения',
  'type' => 'datetime',    
  'show_in' => array(),
  'write_in' => array('edit')
  ));    


  $this->init_field(array(
  'name' => 'subno',
  'title' => 'Номер отправителя',
  'type' => 'varchar',  
  'len' => 20,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create')
  ));       
  
  
  $this->init_field(array(
  'name' => 'code',
  'title' => 'Кодовое слово',
  'type' => 'varchar',  
  'len' => 20,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create')
  ));         

  $this->init_field(array(
  'name' => 'text',
  'title' => 'Текст',
  'type' => 'text',  
  'len' => 70,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));     
    
  
  $this->init_field(array(
  'name' => 'src_time',
  'title' => 'Исходное время',
  'type' => 'varchar',  
  'len' => 30,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create')
  ));       

  
  $this->init_field(array(
  'name' => 'ip',
  'title' => 'IP',
  'type' => 'varchar',  
  'len' => 30,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));       

  
  $this->init_field(array(
  'name' => 'src_get',
  'title' => 'GET запрос',
  'type' => 'text',  
  'len' => 70,
  'show_in' => array(),
  'write_in' => array('edit')
  ));       
      

  $this->init_field(array(
  'name' => 'src_post',
  'title' => 'POST запрос',
  'type' => 'text',  
  'len' => 70,
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));         
  
  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Статус',
  'type' => 'tinyint',  
  'input' => 'select',
  'belongs_to' => array('list' => $this->status_ar),
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));         
    
  
}

function last_v($p) {
  global $sv, $std, $db;
  
  return $p;
}

//eoc
}
?>