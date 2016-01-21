<?php

/*
Модель animahen
*/


class m_animahen extends class_model {

  var $tables = array(
    'animahen' => "    
      `id` INT( 20 ) NOT NULL AUTO_INCREMENT ,
      
      `title` varchar(255) not null default '',  
      `title_ru` varchar(255) not null default '',  
      `title_etc` text null,  
      `page_title` varchar(255) not null default '',  
      `description` varchar(255) not null default '',  
      `keywords` varchar(255) not null default '',  
      
      
      `cat_id` varchar(255) not null default '', 
      `slug` varchar(255) not null default '',      
      `abc` varchar(255) not null default '',      
      
      
      `zhanr` varchar(255) not null default '',  
      `author` varchar(255) not null default '',  
      `perevod` text null,  
      
      `text` text null,  
      `status_id` tinyint(1) not null default '1',
     
      `views` int(20) not null default '0',
      `created_at` DATETIME NULL ,
      `created_by` INT( 11 ) NULL ,
      `updated_at` DATETIME NULL ,
      `updated_by` INT( 11 ) NULL ,
      
      PRIMARY KEY ( `id` ),
      KEY (`slug`), 
      KEY (`abc`)
    "
  );
  
  var $status_ar = array(
    0 => "Выключен", 
    1 => "Включен",    
  );
  var $cat_ar = array(
    'anime' => "Аниме",
    'manga' => "Манга",
    'hentai' => "Хентай"
  );
  
  var $abc_ar = array(   
   '0-9','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'
  );
  
  var $abc_page_id = 49;
  var $details_page_id = 84;
  
  var $load_attaches = 1;
  var $load_markitup = 1;  
  
  
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['animahen'];  
  
   
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название (англ.)',
  'type' => 'varchar',  
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create'),
  'unique' => 1
  ));    
  
  
  $this->init_field(array(
  'name' => 'title_ru',
  'title' => 'Название (рус.)',
  'type' => 'varchar',  
  'len' => '50',
  'show_in' => array( 'remove'),
  'write_in' => array('edit', 'create')
  ));      
  
  $this->init_field(array(
  'name' => 'title_etc',
  'title' => 'Другие названия',
  'type' => 'text',  
  'len' => '50',
  'show_in' => array('remove'),
  'write_in' => array('edit', 'create')
  ));      

 
  $this->init_field(array(
  'name' => 'page_title',
  'title' => 'Спец. заголовок для страницы',
  'type' => 'varchar',  
  'len' => '50',
  'show_in' => array( 'remove'),
  'write_in' => array('edit', 'create')
  ));      
     
  $this->init_field(array(
  'name' => 'description',
  'title' => 'DESCRIPTION страницы',
  'type' => 'varchar',  
  'len' => '50',
  'show_in' => array( 'remove'),
  'write_in' => array('edit', 'create')
  ));      
     
 $this->init_field(array(
  'name' => 'keywords',
  'title' => 'KEYWORDS страницы',
  'type' => 'varchar',  
  'len' => '50',
  'show_in' => array( 'remove'),
  'write_in' => array('edit', 'create')
  ));        
    
  $this->init_field(array(
  'name' => 'slug',
  'title' => 'Идентификатор (slug, англ. без пробелов)',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create'),
  'unique' => 1,
  'not_null' => 1
  ));    
  
  $this->init_field(array(
  'name' => 'abc',
  'title' => 'Начальная буква',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '10',
  'show_in' => array('remove', 'edit'),
  'write_in' => array(),
  'selector' => 1
  ));    

   
  $this->init_field(array(
  'name' => 'cat_id',
  'title' => 'Раздел',
  'type' => 'varchar',
  'input' => 'select',
  'show_in' => array('default'),
  'write_in' => array('create', 'edit'),
  'belongs_to' => array('list' => $this->cat_ar, 'not_null'=>1)
  ));     
  
  
  
  $this->init_field(array(
  'name' => 'zhanr',
  'title' => 'Жанр',
  'type' => 'varchar',  
  'len' => '50',
  'show_in' => array('remove'),
  'write_in' => array('edit', 'create')
  ));      
    
  
  $this->init_field(array(
  'name' => 'author',
  'title' => 'Автор',
  'type' => 'varchar',  
  'len' => '50',
  'show_in' => array('remove'),
  'write_in' => array('edit', 'create')
  ));      
  
  
  $this->init_field(array(
  'name' => 'perevod',
  'title' => 'Перевод',
  'type' => 'text',  
  'len' => '50',
  'show_in' => array('remove'),
  'write_in' => array('edit', 'create')
  ));      
    
  
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Описание',
  'type' => 'text',  
  'len' => '80',
  'show_in' => array('remove'),
  'write_in' => array('edit', 'create'), 
  'id' => 'full-text'
  ));    
   
    

  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Статус',
  'type' => 'int',
  'default' => 1,
  'input' => 'select',
  'show_in' => array(),
  'write_in' => array('edit'),
  'belongs_to' => array('list' => $this->status_ar, 'not_null'=>1)
  ));   
    
  
  $this->init_field(array(
  'name' => 'views',
  'title' => 'Просмотры',
  'type' => 'int',  
  'len' => '10',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create')
  ));    
     
   
    
}

function parse($d) {
  global $std;

  $d['abc'] = $this->str2abc($d['slug']);
  
  $d['raw_url'] = "/".$d['cat_id']."/".$d['abc']."/".$d['slug'];
  $d['url'] = $d['raw_url']."/";
  
  return $d;
}

function last_v($p) {
  
  $p['abc'] = $this->str2abc($p['slug']);
  $p['slug'] = trim(preg_replace("#[^a-z\-\_0-9\ ]#si", "", $p['slug']));
  $p['slug'] = preg_replace("# #si", "_", $p['slug']);
  $p['slug'] = strtolower($p['slug']);
  
  return $p;
}

function after_create($p, $err) {
  if (!$err) {    
    $p['id'] = $this->current_record;
    $this->sync_adr($p); 
  }
}

function after_update($d, $p, $err) {
  global $sv, $std, $db;
  
  if (!$err) {
    $p['id'] = $d['id'];
    $this->sync_adr($p);
  }
}

function c_details() {
  global $sv, $std, $db;
  $id = $sv->view->d['object'];
  $d = $this->get_item($id);

  $sv->vars['p_title'] = ($d['description']!='') ? $d['description'] : $d['title'];
  $sv->vars['p_title'] = ($d['page_title']!='') ? $d['page_title'] : $sv->vars['p_title'];
  
  $sv->view->page['description'] = $d['description'];
  $sv->view->page['keywords'] = $d['keywords'];
  
  $ret['d'] = $d;
  
  
  $db->q("update {$this->t} SET `views`=`views`+'1' WHERE id='{$d['id']}'", __FILE__, __LINE__);
  
  return $ret;
}


function c_syncabc() {
  $this->sync_abc_pages();
}

function str2abc($t) {

  if (preg_match("#^([a-z])#si", $t, $m)) {
    $ret = strtolower($m[1]);
  }
  else {
    $ret = '0-9';
  }
  
  return $ret;
}

/**
 * Обновляется список урлов для букв по трем разделам
 *
 */
function sync_abc_pages() {
  global $sv, $std, $db;
  
  $sv->load_model('url');
  
  $keys = array_keys($this->cat_ar);
  
  foreach($keys as $razdel) {
    foreach ($this->abc_ar as $letter) {
      $url = "/{$razdel}/{$letter}";
      $sv->m['url']->sync_url($url, array('title' => strtoupper($letter), 'page' => $this->abc_page_id, 'primary' => 0));
    }
    
    
  }
  
  
}

function list_by_url($url) {
  global $sv, $std, $db;  
  
  $razdel = $abc = '';
  if (preg_match("#/([^/]+)/([^/]+)/#si", $url, $m)) {
    $razdel = $db->esc($m[1]);
    $abc = $db->esc($m[2]);    
  }
  
  $ret = $this->item_list("`cat_id`='{$razdel}' AND `abc`='{$abc}'", "title ASC", 0, 1);
  $sv->vars['p_title'] = "".strtolower($this->cat_ar[$razdel])." на букву ".strtoupper($abc);
  
  t($ret['list']);
  return $ret;
}

function sync_adr($d) {
  global $sv, $std, $db;
  
  $sv->load_model('url');
  
  
  $d = $this->parse($d);
  $module = $db->esc($this->name);
  $id = intval($d['id']);
  
  $p = array( 'page' => $this->details_page_id, 
              'url' => $d['raw_url'], 
              'primary' => 0, 
              'module' => $module, 
              'object' => $id,
              'title' => $d['title']
              );
    t($p);          
  $sv->m['url']->sync_url_wh("`module`='{$module}' AND `object`='{$id}'", $p);
  
}
//eoc
}

?>