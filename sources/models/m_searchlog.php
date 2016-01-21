<?php

/*
лог поисковых запросов
*/
class m_searchlog extends class_model {
  var $tables = array(
    'search_logs' => "
      `id` bigint(20) NOT NULL auto_increment,
      `query` varchar(255) NOT NULL default '',
      `results` int(11) not null default '0',
      `date` datetime default NULL,
       
      `ip` varchar(255) default NULL,
      `agent` varchar(255) default NULL,
      `refer` varchar(255) default NULL,
      
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
  
  $this->t = $sv->t['search_logs'];
 
  $this->init_field(array(
  'name' => 'date',
  'title' => 'Дата публикации',
  'type' => 'datetime',
  'show_in' => array('remove', 'default'),  
  'write_in' => array('edit')
  ));      
  
  $this->init_field(array(
  'name' => 'query',
  'title' => 'Запрос',
  'type' => 'varchar',  
  'len' => '70',
  'not_null' => 1,
  'show_in' => array('default',),
  'write_in' => array('create', 'edit')
  ));    

  $this->init_field(array(
  'name' => 'results',
  'title' => 'Результатов',
  'type' => 'int',  
  'len' => '10',
  'show_in' => array('default',),
  'write_in' => array('create', 'edit')
  ));     
  $this->init_field(array(
  'name' => 'ip',
  'title' => 'IP',
  'type' => 'varchar',
  'show_in' => array('default', 'remove'),
  'write_in' => array(),
  'selector' => 1
  ));         
  
  $this->init_field(array(
  'name' => 'agent',
  'title' => 'Agent',
  'type' => 'varchar',
  'show_in' => array('edit', 'remove'),
  'write_in' => array(),
  'selector' => 0
  ));         
  
  $this->init_field(array(
  'name' => 'refer',
  'title' => 'Refer',
  'type' => 'varchar',
  'show_in' => array('edit', 'remove'),
  'write_in' => array()
  ));
  
}

function df_query($t) {
  global $std;
  $t = $std->text->cut($t, 'replace', 'replace');  
  return $t;
}

function vcb_agent($t) {
  global $std;
  $t = $std->text->cut($t, 'replace', 'replace');  
  return $t;  
}
function vcb_refer($t) {
  global $std;
  $t = $std->text->cut($t, 'replace', 'replace');  
  return $t;  
}

function last_v($p) {
  global $sv, $std, $db;
  
 
  if ($this->code=='create') {
    $p['date'] = $sv->date_time;  
    $p['ip'] = $sv->ip;
    $p['agent'] = $sv->user_agent;
    $p['refer'] = $sv->refer;
  }

   
  return $p;
}


//eoc
}
?>