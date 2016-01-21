<?php
/*
стадартные логи
*/



class m_logrecord extends class_model {

  var $tables = array(
    'logs' => "
      `id` bigint(20) NOT NULL auto_increment,
      `type` varchar(255) NOT NULL default '',
      `user` int(11) NOT NULL default '0',  
      `ip` varchar(255) NOT NULL default '',
      `agent` varchar(255) NOT NULL default '',
      `request` varchar(255) NOT NULL default '',
      `refer` varchar(255) NOT NULL default '',  
      `title` varchar(255) NOT NULL default '',
      `text` text null,  
      `status` tinyint(3) NOT NULL default '0',
      `time` int(11) NOT NULL default '0',
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      PRIMARY KEY  (`id`),
      KEY (`time`)          
    "
  );

  var $per_page = 100;
   
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['logs'];

    
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Описание',
  'type' => 'varchar',
  'size' => '255',
  'len' => '50',
  'default' => '',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit') 
  ));  
  
  $this->init_field(array(
  'name' => 'text',
  'title' => 'text',
  'type' => 'text',
  'len' => '80',
  'default' => '',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit') 
  ));    
  
  $this->init_field(array(
  'name' => 'type',
  'title' => 'Тип (англ.)',
  'type' => 'varchar',
  'size' => '255',
  'len' => '10',
  'default' => '',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'selector' => 1
  ));  
  
  $this->init_field(array(
  'name' => 'user',
  'title' => 'Пользователь',
  'type' => 'int',
  'size' => '11',
  'len' => '5'
  ));
 
  $this->init_field(array(
  'name' => 'ip',
  'title' => 'IP-адрес',
  'type' => 'varchar',
  'size' => '255',
  'len' => '20',
  'default' => '',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));    

  $this->init_field(array(
  'name' => 'agent',
  'title' => 'Agent',
  'type' => 'varchar',
  'size' => '255',
  'len' => '20',
  'default' => '',
  'show_in' => array( 'remove'),
  'write_in' => array('create', 'edit')
  ));    
  
  $this->init_field(array(
  'name' => 'request',
  'title' => 'Request',
  'type' => 'varchar',
  'size' => '255',
  'len' => '20',
  'default' => '',
  'show_in' => array( 'remove'),
  'write_in' => array('create', 'edit')
  ));    
  
  
  $this->init_field(array(
  'name' => 'refer',
  'title' => 'Refer',
  'type' => 'varchar',
  'size' => '255',
  'len' => '20',
  'default' => '',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));    
  
  
  $this->init_field(array(
  'name' => 'time',
  'title' => 'Time',
  'type' => 'int',
  'len' => '12',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));    

  $this->init_field(array(
  'name' => 'f_time',
  'title' => 'Время',
  'virtual' => 'time',
  'show_in' => array('default', 'remove'),
  'write_in' => array()
  ));    

      
    
  $this->init_field(array(
  'name' => 'status',
  'title' => 'Результат',
  'type' => 'boolean',
  'size' => '3',
  'len' => '2'
  ));   
  
}


//parsers
function df_f_time($t) {
  global $std;
  
  return $std->time->format($t, 0.5);
}
function parse($d) {
  global $std;
  
  $d['f_time'] = $std->time->format($d['created_at'], 0.5, 1);
  
  return $d;
}

//stuff
function write_log($type, $str, $status = 0) {
  global $sv;
  
  $uid = (isset($sv->user['session']['account_id'])) ? intval($sv->user['session']['account_id']) : 0;
      
	  $p = array(
	  'user' => $uid,
	  'ip' => $sv->ip,
    'agent' => $sv->user_agent,
    'request' => $sv->request_url,
    'refer'	=> $sv->refer,
	  'type' => $type,
	  'title' => $str,
		'status' => $status,
		'time' => $sv->post_time
	  );
	  
   return $this->insert_row($p);
}


//eoc
}  
  
?>