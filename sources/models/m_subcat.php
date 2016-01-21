<?php
/*
модель под категории, локальной категории товаров
*/

class m_subcat extends class_model {

  var $tables = array(
    'subcats' => "
      `id` bigint(20) NOT NULL auto_increment,
      `title` varchar(255) NOT NULL default '',
      `description` text null,
      `slug` varchar(255) NOT NULL default '',
      `status` int(1) not null default '1',
      `count` int(11) NOT NULL default '0',
      `cat_id` int(11) not null default '0',
      `cat_slug` varchar(255) null,
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      PRIMARY KEY  (`id`),
      KEY `slug` (`slug`)    
        "
  );

function __construct() {
  global $sv;  

  $this->t = $sv->t['subcats'];
  
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название категории',
  'type' => 'varchar',  
  'len' => 40,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'public_search' => 1
  ));    
  
  
  $this->init_field(array(
  'name' => 'cat_id',
  'title' => 'Раздел',
  'type' => 'varchar',  
  'input' => 'select',
  'belongs_to' => array('table' => 'cats', 'field' => 'id', 'return' => 'title', 'null' => 1),
  'show_in' => array( 'remove'),
  'write_in' => array('create', 'edit')
  ));    
    
  $this->init_field(array(
  'name' => 'cat',
  'title' => 'Раздел',
  'virtual' => 'cat_id',  
  'show_in' => array( 'default')
  ));    
    
  $this->init_field(array(
  'name' => 'cat_slug',
  'title' => 'Адрес раздела',
  'type' => 'varchar',
  'len' => '25',
  'show_in' => array('remove'),
  'write_in' => array()
  ));  

   
  $this->init_field(array(
  'name' => 'description',
  'title' => 'Описание категории',
  'type' => 'text',  
  'len' => 50,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit'),
  'public_search' => 1
  ));    
    

  $this->init_field(array(
  'name' => 'slug',
  'title' => 'Адрес категории (англ)',
  'type' => 'varchar',
  'len' => '25',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));  



    
  
 $this->init_field(array(
  'name' => 'count',
  'title' => 'Количество записей в категории',
  'type' => 'int',  
  'len' => 10,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));    
    
  $this->init_field(array(
  'name' => 'status',
  'title' => 'Включена?',
  'type' => 'boolean',  
  'default' => 1,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));  

  $sv->load_model('cat');
   
}


function parse($d) {

  $d['url'] = "/shop/{$d['cat_slug']}/{$d['slug']}/";

  return $d;
}

function last_v($p) {
  global $sv, $std;
  

  // slug
  $id = ($this->code=='edit') ? $this->current_record : 0; 
  if (!$std->text->is_valid_slug($p['slug'], $this->t, 'slug', $id)) {
    $p['slug'] = $std->text->gen_slug($p['title'], $this->t, 'slug');
  }
    
  if (isset($p['cat_id'])) {
    $sv->load_model('cat');
    $cat = $sv->m['cat']->get_item($p['cat_id']);
    if ($cat) {
      $p['cat_slug'] = $cat['slug'];
    }
    
  }
  return $p;
}

function v_slug($t) {
  $t = trim($t);
  $t = preg_replace("#\s#si", "-", $t);
  return $t;
}


function v_cat_id($id) {
  $id = intval($id);
  if ($id<=0) {
    $this->v_err = 1;
    $this->v_errm[] = "Не выбран раздел.";
  }
  return $id;
}

//eoc
}

?>