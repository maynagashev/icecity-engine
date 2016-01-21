<?php

class m_user extends class_model {

  var $title = "Дополнительная таблица с юзерами";
  
  var $tables = array(
    'users' => "
      `id` int(10) NOT NULL auto_increment,
      `login` varchar(128) NOT NULL default '',
      `password` varchar(128) NOT NULL default '',
      `group_id` varchar(128) NOT NULL default '',
      `name` varchar(255) NOT NULL default '',
      `email` varchar(255) NOT NULL default '',
      `phone` varchar(255) NOT NULL default '',
      `avatar` varchar(100) default NULL,
      `last_ip` varchar(255) default NULL,
      `last_visit` datetime default NULL,
      `last_time` int(11) NOT NULL default '0',
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      PRIMARY KEY  (`id`)    
    "
  );

   
  var $groups = array(
  '0' => 'Заблокированные',
  '3' => 'Администратор'
  
  );
  
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['users'];
  $this->per_page = 10;    
    
  $this->init_field(array(
  'name' => 'login',
  'title' => 'Логин',
  'type' => 'varchar',
  'len' => '20',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'unique' => 1
  ));    
  
  $this->init_field(array(
  'name' => 'password',
  'title' => 'Пароль',
  'type' => 'varchar',
  'size' => '255',
  'len' => '30',
  'input' => 'password',
  'default' => '',
  'show_in' => array(),
  'write_in' => array('create', 'edit')  
  ));  
  
  $this->init_field(array(
  'name' => 'email',
  'title' => 'Email',
  'type' => 'varchar',
  'len' => '30',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'unique' => 1
  ));    
  
  $this->init_field(array(
  'name' => 'name',
  'title' => 'Настоящее имя',
  'type' => 'varchar',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));    
    
  $this->init_field(array(
  'name' => 'last_ip',
  'title' => 'Последний IP',
  'type' => 'varchar',
  'show_in' => array('default', 'edit', 'remove')
  ));    
      
  $this->init_field(array(
  'name' => 'last_visit',
  'title' => 'Дата последнего посещения',
  'type' => 'datetime',
  'show_in' => array('default', 'edit', 'remove')
  ));    
        
      
  $this->init_field(array(
  'name' => 'group_id',
  'title' => 'Группа',
  'type' => 'int',
  'size' => '11',
  'input' => 'select',
  'default' => '3',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'belongs_to' => array('list' => $this->groups, 'not_null' =>1)
  ));   
    
  
}

function v_password($val) {
  global $sv;
  
  if ($val=='') {
    //not update
    $this->v_errm[] = "Пароль не изменен.";
    return null;
  }
  else {
   $val = $sv->prepare_pass($val);
   $this->v_errm[] = "Данные сохранены, установлен новый пароль.";
   
  }
  
  return $val;
}

//endofclss
}  
  
?>