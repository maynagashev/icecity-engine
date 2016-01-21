<?php



class m_task extends class_model {
  var $title = "Список заданий/тикетов";
  var $tables = array(
    'tasks' => "
    
  `id` int(10) NOT NULL auto_increment,
  `date` date,
  `text` text NOT NULL default '',
  `status_id` tinyint(1) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  
  `created_at` datetime default NULL,
  `created_by` int(11) NOT NULL default '0',
  `updated_at` datetime default NULL,
  `updated_by` int(11) NOT NULL default '0',
  `expires_at` datetime default NULL,
  PRIMARY KEY  (`id`), 
  KEY (`date`)    
    
    "
  );
  var $address_list = array();	
  var $per_page = 30;
  var $status_ar = array(
    0 => 'В очереди',
    3 => 'Выполняется',
    1 => 'Выполнено',
    2 => 'Отложено'
  );

function __construct() {
  global $sv;
  
  $this->t = $sv->t['tasks'];
  
  
  $this->init_field(array(
  'name' => 'date',
  'title' => 'Дата',
  'type' => 'datetime',
  'setcurrent' => 1,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit'),
  ));
  
  $this->init_field(array(
  'name' => 'user_id',
  'title' => 'User',
  'type' => 'tinyint',
  'input' => 'select',
  'default' => $sv->_get['user_id'],
  'show_in' => array('remove'),
  'write_in' => array('create','edit'),
  'belongs_to' => array('table' => 'net_users', 'field'=>'id', 'return' => 'fio')  
  ));  
    
  $this->init_field(array(
  'name' => 'userlink',
  'title' => 'Userlink',
  'virtual' => 1,
  'show_in' => array('edit')
  ));  
    
  $this->init_field(array(
  'name' => 'status',
  'title' => 'Status',
  'virtual' => 'status_id',
  'show_in' => array('default','remove'),
  'write_in' => array(),
 
  ));    

   
  $this->init_field(array(
  'name' => 'user',
  'title' => 'User',
  'virtual' => 'user_id',
  'show_in' => array('default'),
  'write_in' => array(),
  ));    
  

   
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Text',
  'type' => 'text',
  'len' => '70',
  'show_in' => array('default','remove'),
  'write_in' => array('create','edit'),
  ));
  

  
  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Status',
  'type' => 'tinyint',
  'input' => 'select',  
  'show_in' => array('remove'),
  'write_in' => array('create','edit'),
  'belongs_to' => array('list' => $this->status_ar,'not_null' => 1),
  'filter' => 1,
  'filter_pattern' => "=="
  ));  
  
  
  $this->init_field(array(
  'name' => 'info',
  'title' => 'Последнее редактирование',
  'virtual' => 1,
  'show_in' => array('edit'),
  ));  
  
    

}

function vcb_userlink($id) {
  
  return "<a href='./?netusers_edit={$this->d['user_id']}'>Подробная информация о пользователе</a> &rarr;";
}

function vcb_info() {
  global $sv, $std, $db;

  $db->q("SELECT * FROM {$sv->t['account']} where id='{$this->d['updated_by']}'") ;
  $d = $db->f();
  $date = $std->time->format($this->d['updated_at'], 3, 1);
  $ret = "<b>{$d['login']}</b> [{$d['lastip']}] дата: <b>{$date}</b> ";
  return $ret;
}
function df_status($val) {
  switch($this->d['status_id']) {
    case 0:  $c = 'red'; break;
    case 1: $c = 'green'; break;
    case 2: $c = 'gray'; break;
    case 3: $c = 'blue'; break;
  }
  
  
  return "<span style='color:{$c};'>{$val}</span>";
}

function df_text($t) {
  $t = nl2br($t);
  return "<div style='text-align:left;'>{$t}</div>";
}
function df_user($t) {
  global $sv, $std, $db;
  
  $db->q("SELECT u.apart, a.street, a.home FROM {$sv->t['net_users']} u 
  LEFT JOIN {$sv->t['net_address']} a ON (u.address_id=a.id)
  WHERE u.id='{$this->d['user_id']}'");
  $d = $db->f();
  
  $add = ($d) ? "({$d['street']} {$d['home']}, кв. {$d['apart']})" : "";
  return "<div style='text-align:left;'><a href='./?netusers_edit={$this->d['user_id']}'>{$t}</a> {$add}</div>";
}


function last_v($p) {
  global $sv, $std, $db;
  
  $p['date'] = $sv->date_time;
  
  return $p;
}


function parse($d) {
  global $std;
    switch($d['status_id']) {
    case 0:  $c = 'red'; break;
    case 1: $c = 'green'; break;
    case 2: $c = 'gray'; break;
    case 3: $c = 'blue'; break;
  }
  
  $d['text'] = nl2br($d['text']);
  
  $d['f_status'] = "<span style='color:{$c}'>{$this->status_ar[$d['status_id']]}</span>";
  $d['f_date'] = $std->time->format($d['date'], 0.5, 1);
  return $d;
}

//eoc
}

?>