<?php

/*
пакеты каналов норком
*/

class m_tvpack extends class_model {

  var $tables = array(
    'tvpacks' => "
      `id` int(10) NOT NULL auto_increment,
      `title` varchar(255) null,
      `text` text null, 
      `items` text null,
      
      `votes` bigint(20) not null default '0',
      `rating` double(20,6) not null default '0',  
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      
      PRIMARY KEY  (`id`)    
    "
  );
    
  var $per_page = 20;
  
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['tvpacks'];
  
    
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название',
  'type' => 'varchar',  
  'len' =>  60,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'unique' => 1
  ));  

    
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Описание',
  'type' => 'text',  
  'len' => 80,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));  
  

  $this->init_field(array(
  'name' => 'items',
  'title' => 'Идентификаторы каналов',
  'type' => 'text',  
  'len' =>  60,
  'show_in' => array('remove'),
  'write_in' => array()
  ));  
  
  $this->init_field(array(
  'name' => 'items_view',
  'title' => 'Каналы в группе',
  'show_in' => array('default', 'edit'),
  'virtual' => 'items'
  ));      

   
}


// callbacks
function df_items_view($val) {
  global $db, $sv;
  
  $pack = $this->parse_items(array('items'=>$val));

  $tr = array();
  $r = $db->q("SELECT * FROM {$sv->t['tvlist']} WHERE id IN ({$pack['items_in']})", __FILE__, __LINE__);
  while($d = $db->f()) {
    $tr[] = "<div style='text-align:left;'><a href='".u('tvlist', 'edit', $d['id'])."'>{$d['title']}</a></div>";
  }
  
  $ret = "<div style='text-align:left:'>".implode("\n", $tr)."</div>";
  return $ret;
}

function vcb_items_view($val) {
  return $this->df_items_view($val);
}

//validations
function parse($d) {
  global $db, $std, $sv;
  
  $d = $this->parse_items($d);
  
  return $d;
}

function parse_items($d){
  

  $ar = explode(",", $d['items']);
  $items_ar = array();
  foreach($ar as $id) {
    $id = trim($id);
    if ($id!='')   {
      $id = intval($id);
      if ($id>0) {
        $items_ar[] = $id;
      }
    }
  }
  
  $d['items_ar'] = array_unique($items_ar);
  
  $in_ar = array();
  foreach ($d['items_ar'] as $id) {
    $in_ar[] = "'{$id}'";
  }
  if (count($in_ar)<=0) {
    $in_ar[] = "'0'";
  }
  $d['in_ar'] = $in_ar;
  $d['items_in'] = implode(", ", $in_ar);
  return $d;  
  
}



// eoc
}

?>