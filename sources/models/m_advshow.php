<?php

class m_advshow extends class_model {


  var $tables = array(
    'adv_show' => "
      `id` int(10) NOT NULL auto_increment,
      `stream_id` int(11) NOT NULL default '0',
      `block_id` int(11) NOT NULL default '0',
      `time` int(11) NOT NULL default '0',
      `text` text,
      `views` int(11) NOT NULL default '0',
      `position` varchar(255) default NULL,
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      PRIMARY KEY  (`id`),
      KEY `stream` (`stream_id`),
      KEY `time` (`time`)
    "
  );
    
  var $last_stream;
  var $last_block;
  
  
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['adv_show'];
 
    
  
  // virtual
  $this->init_field(array(
  'name' => 'f_time',
  'title' => 'Время последнего показа',  
  'show_in' => array('default', 'edit'),
  'write_in' => array(),
  'virtual' => 'time'
  ));    


  $this->init_field(array(
  'name' => 'time',
  'title' => 'unix_time',
  'type' => 'int',  
  'size' => '11',
  'len' => '20',
  'show_in' => array('default'),
  'write_in' => array()
  ));    
  
  

  
   
  $this->init_field(array(
  'name' => 'block_id',
  'title' => 'Блок',
  'type' => 'int',
  'input' => 'select',
  'show_in' => array(),
  'write_in' => array('create', 'edit'),
  'belongs_to' => array('table' => 'adv_blocks', 'field'=>'id', 'return' => 'title')
  ));  
  
  //virtual
  $this->init_field(array(
  'name' => 'block',
  'title' => 'Блок',
  'show_in' => array('default'),
  'write_in' => array(),
  'virtual' => "block_id"
  ));      
   
  $this->init_field(array(
  'name' => 'stream_id',
  'title' => 'Поток',
  'type' => 'int',
  'input' => 'select',
  'show_in' => array(),
  'write_in' => array('create', 'edit'),
  'belongs_to' => array('table' => 'adv_streams', 'field'=>'id', 'return' => 'title')
  ));  
  
  //virtual
  $this->init_field(array(
  'name' => 'stream',
  'title' => 'Поток',
  'show_in' => array('default'),
  'write_in' => array(),
  'virtual' => "stream_id"
  ));    
  

 
  $this->init_field(array(
  'name' => 'position',
  'title' => 'Группа баннеров',  
  'show_in' => array('edit', 'remove')  
  )); 
  

   
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Содержимое',
  'type' => 'text', 
  'show_in' => array('edit', 'remove')  
  )); 
  
  $this->init_field(array(
  'name' => 'views',
  'title' => 'Количество показов',
  'type' => 'int', 
  'show_in' => array('default', 'edit', 'remove')  
  )); 
  
  
      
}

function v_title($val) {
  
  $val = trim($val);
  
  if ($val=='') {
    $this->v_err = true;
    $this->v_errm = "Не указан адрес.";
  }
  
  return $val;  
}

function v_block_id($val) {
  global $sv, $db;
  
  $id = intval($val);
  $db->q("SELECT * FROM {$sv->t['adv_blocks']} WHERE id='{$id}'", __FILE__, __LINE__);
  if ($db->nr()<=0) {
    $this->v_err = true;
    $this->v_errm[] = "Рекламный блок не выбран.";
  }
  else {
    $this->last_block = $db->f();
  }
  
  return $id;
}


function v_stream_id($val) {
  global $sv, $db;
  
  $id = intval($val);
  $db->q("SELECT * FROM {$sv->t['adv_streams']} WHERE id='{$id}'", __FILE__, __LINE__);
  if ($db->nr()<=0) {
    $this->v_err = true;
    $this->v_errm[] = "Рекламный поток не выбран.";
  }
  else {
    $this->last_stream = $db->f();
  }
  return $id;
}

function last_v($p) {
  global $db, $sv;
  
  $stream  = intval($p['stream_id']);
  
  
  $block = intval($p['block_id']);
  $db->q("SELECT id FROM {$this->t} WHERE stream_id='{$stream}' AND block_id='{$block}' AND id<>'{$this->current_record}'", __FILE__, __LINE__ );
  if ($db->nr()>0) {
    $d = $db->f();
    $this->v_err = true;
    $this->v_errm[] = "Такая связка <a href='".u($sv->act, 'edit', $d['id'])."'>уже существует</a>.";
  }
  
}


function df_f_time($val) {
  global $std;
  
  if ($val<=0) {
    return "еще не выводился";
  }
  else {
    return $std->time->format($val, 0.5);
  }
}

function vcb_f_time($val) {
  
  return $this->df_f_time($val);
}
function df_code($val) {   
  return "[stream={$val}]";
}
function vcb_code($val) {  
  return "[stream={$this->d['id']}]";
}
function parse($d) {
  $d['code'] = "[stream={$d['id']}]";
  return $d;
}

function get_active_list() {
  global $db;
  
  $this->active_list_parsed = true;
  
  $ids = array();
  
  $db->q("SELECT * FROM {$this->t} WHERE active='1' ORDER BY `sort` ASC", __FILE__, __LINE__);
  while($d = $db->f()) {
    $d = $this->parse($d);
    $ar[$d['position']][] = $d;
    $ids[] = $d['id'];
  }
  
  $this->active_ids = $ids;
  $this->active_list = $ar;
  return $ar;  
}

function after_update($d, $p, $err) {
  
  $this->after_create($p, $err);
  
  
}
function after_create($p, $err) {
  global $db;  
  if (!$err) {
    $db->q("UPDATE {$this->t} SET text='".addslashes($this->last_block['text'])."',
     position='".addslashes($this->last_stream['position'])."' WHERE id='{$this->current_record}'", __FILE__, __LINE__);
    
    $this->update_stream_counters();
  }
}

function update_stream_counters() {
  global $sv, $std, $db;
  
  $ar = array();
  $db->q("SELECT stream_id, count(*) as size FROM {$this->t} GROUP BY stream_id", __FILE__, __LINE__);
  while($d = $db->f()) {
    $ar[$d['stream_id']] = $d['size'];
  }
  
  $db->q("UPDATE {$sv->t['adv_streams']} SET `count`='0'", __FILE__, __LINE__);
  foreach($ar as $sid => $size) {    
    $db->q("UPDATE {$sv->t['adv_streams']} SET `count`='{$size}' WHERE id='{$sid}'", __FILE__, __LINE__);
  }
}



//eoc
}  
  
?>