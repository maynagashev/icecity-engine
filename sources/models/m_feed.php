<?php

/*
  рсс фиды
  
  ALTER TABLE `rss_feeds`  
  ADD `image_title` varchar(255) null after `webmaster`,
  ADD `image_url`  varchar(255) null after `image_title`,
  ADD `image_width`  varchar(255) null after `image_url`,
  ADD `image_height` varchar(255) null after `image_width`,
  ADD `image_description`  varchar(255) null after `image_height`
      
*/

class m_feed extends class_model {
  
  var $tables = array(
    'rss_feeds' => "
      `id` bigint( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
      
      `title` varchar(255) null,
      `link` varchar(255) null,
      `description` VARCHAR( 255 ) null,
      `language` varchar(255) null,
      `pubdate` varchar(255) null,
      `lastbuilddate` varchar(255) null,
      `docs` varchar(255) null,
      `generator` varchar(255) null,
      `managingeditor` varchar(255) null,
      `webmaster` varchar(255) null,
      
      `image_title` varchar(255) null,
      `image_url`  varchar(255) null,
      `image_width`  varchar(255) null,
      `image_height` varchar(255) null,
      `image_description`  varchar(255) null,
      
      `model` varchar(255) null,
      `channel` varchar(255) null,
      `size` int(11) not null default '20',
      `status_id` tinyint(1) not null default '1',
            
      `created_at` DATETIME NOT NULL ,
      `created_by` INT( 11 ) null,
      `updated_at` DATETIME NOT NULL ,
      `updated_by` INT( 11 ) null
    "
  );

var $models_ar = array(
  'blogpost' => 'blogpost',
  'bpost' => 'bpost',
);
  
function __construct() {
  global $sv, $std;  
  
  $this->t = $sv->t['rss_feeds'];
  $sv->init_class('rss');
  
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название фида',
  'type' => 'varchar',
  'len' => 50,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));  
    
  $this->init_field(array(
  'name' => 'link',
  'title' => 'link',
  'description' => 'http://liftoff.msfc.nasa.gov/',
  'type' => 'varchar',
  'len' => 50,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));  
    
  $this->init_field(array(
  'name' => 'description',
  'title' => 'description',
  'description' => 'Liftoff to Space Exploration.',
  'type' => 'varchar',
  'len' => 50,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));  
    
  $this->init_field(array(
  'name' => 'language',
  'title' => 'language',
  'description' => 'en-us',
  'type' => 'varchar',
  'len' => 50,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));  
  /*  
  $this->init_field(array(
  'name' => 'pubdate',
  'title' => 'pubDate',
  'description' => 'Tue, 10 Jun 2003 04:00:00 GMT',
  'type' => 'datetime',
  'len' => 50,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));  
    
  $this->init_field(array(
  'name' => 'lastbuilddate',
  'title' => 'lastBuildDate',
  'description' => 'Tue, 10 Jun 2003 09:41:01 GMT',
  'type' => 'datetime',
  'len' => 50,
  'show_in' => array('remove', 'default'),
  'write_in' => array('create', 'edit')
  ));  
   */
  $this->init_field(array(
  'name' => 'docs',
  'title' => 'docs',
  'description' => 'http://blogs.law.harvard.edu/tech/rss',
  'type' => 'varchar',
  'len' => 50,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));  
    
  $this->init_field(array(
  'name' => 'generator',
  'title' => 'generator',
  'description' => 'Icecity Engine',
  'type' => 'varchar',
  'len' => 50,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));  
    
  $this->init_field(array(
  'name' => 'managingeditor',
  'title' => 'managingEditor',
  'description' => 'editor@example.com',
  'type' => 'varchar',
  'len' => 50,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));  
    
  $this->init_field(array(
  'name' => 'webmaster',
  'title' => 'webMaster',
  'description' => 'webmaster@example.com',
  'type' => 'varchar',
  'len' => 50,
  'show_in' => array('remove'), 
  'write_in' => array('create', 'edit')
  ));  
  
  $this->init_field(array(
  'name' => 'image_title',
  'title' => 'image_title',
  'description' => 'My Image',
  'type' => 'varchar',
  'len' => 50,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));  

  $this->init_field(array(
  'name' => 'image_url',
  'title' => 'image_url',
  'description' => 'http://mydomain.com/blog/image.gif',
  'type' => 'varchar',
  'len' => 50,
  'show_in' => array('remove', 'default'),
  'write_in' => array('create', 'edit')
  ));     
      
  $this->init_field(array(
  'name' => 'image_width',
  'title' => 'image_width',
  'description' => '200',
  'type' => 'int',
  'len' => 10,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));     
    
  $this->init_field(array(
  'name' => 'image_height',
  'title' => 'image_height',
  'description' => '100',
  'type' => 'int',
  'len' => 10,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));  

  $this->init_field(array(
  'name' => 'image_description',
  'title' => 'image_description',
  'description' => 'Image title text',
  'type' => 'varchar',
  'len' => 50,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));    
  
  $this->init_field(array(
  'name' => 'model',
  'title' => 'Модель',
  'description' => '',
  'type' => 'varchar',
  'len' => 50,
  'input' => 'select',
  'belongs_to' => array('list' => $this->models_ar),
  'show_in' => array('remove', 'default'),
  'write_in' => array('create', 'edit')
  ));  
    
  $this->init_field(array(
  'name' => 'channel',
  'title' => 'Канал',
  'description' => 'all',
  'type' => 'varchar',
  'len' => 50,
  'show_in' => array('remove', 'default'),
  'write_in' => array('create', 'edit')
  ));  
    
  $this->init_field(array(
  'name' => 'size',
  'title' => 'Количество сообщений в ленте',
  'description' => '',
  'type' => 'int',
  'len' => 10,
  'default' => 20,
  'show_in' => array('remove', 'default'),
  'write_in' => array('create', 'edit')
  ));  
    
    
  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Лента включена?',
  'description' => '',
  'type' => 'boolean',
  'show_in' => array('remove', 'default'),
  'write_in' => array('create', 'edit')
  )); 
  
   
    
  
}

function parse($d) {
  global $sv, $std;
  return $d;
}

function last_v($p) {  
  global $sv;
 
  
  return $p;
}

// CONTROLLERS
function c_public_default() {
  global $sv;
  
  $id = (isset($sv->_get['id'])) ? intval($sv->_get['id']) : 0;
  if ($id <= 0) {
    $sv->view->show_err_page('badrequest');
  }
  $d = $this->get_item($id);
  if (!$d) { 
    $sv->view->show_err_page('notfound');
  }
  if ($d['status_id']<>1) {
    $sv->view->show_err_page('forbidden');
  }
  $feed = $d;
  
  // выбираем записи  
  $items = array();
  $model_name = $d['model'];
  $list_method = "rss_list_{$d['channel']}";
  $parse_method = "rss_parse";  
  
  $sv->load_model($model_name);
  if (!method_exists($sv->m[$model_name], $parse_method)) {
    die("Model method for parsing RSS not defined: <b>{$model_name}->{$parse_method}(d)</b>.");
  }
  if (!method_exists($sv->m[$model_name], $list_method)) {
    die("Model method for listing RSS items not defined: <b>{$model_name}->{$list_method}(d)</b>.");
  }
    
  $ar = $sv->m[$model_name]->$list_method($feed['size']);
  foreach($ar['list'] as $d) {
    // парсим для рсс
    $p = $sv->m[$model_name]->$parse_method($d);
    foreach($p as $k=>$v) { 
      // переводим тексты в юникод
      $p[$k] = mb_convert_encoding($v, "utf-8", 'cp1251'); 
    }
    $items[] = $p;
  }
  
  
  // выводим
  
  $sv->init_class('rss');
  $rss = &$sv->rss;    
  
  // формируем описание канала
  $channel_pubdate = $sv->post_time - TIME_SHIFT;
  $p = array(
    'title' => $feed['title'], 
    'link' => $feed['link'],     
    'description' => $feed['description'], 
    'language' => $feed['language'],
    //'pubDate' => $rss->rss_unix_to_rfc( strtotime($feed['pubdate'] ) ), 
    'lastBuildDate' => $rss->rss_unix_to_rfc( $channel_pubdate ),
    'docs' => $feed['docs'],
    'generator' => $feed['generator'],
    'managingEditor' => $feed['managingeditor'],
    'webMaster' => $feed['webmaster']  
  );
  
  // переводим заголовки в уникод
  foreach($p as $k => $v) { $p[$k] = mb_convert_encoding($v, "utf-8", 'cp1251'); }
  $channel_id = $rss->create_add_channel( $p );    
  
  // добавляем записи в канал
  foreach($items as $d) {
    $p = array( 
      'title'       => $d['title'],
      'link'        => $d['link'],
      'description' => $d['description'],
      'pubDate'	   => $rss->rss_unix_to_rfc( strtotime($d['pubdate']) ) ,
      'guid'        => $d['guid']);
      
    if (isset($d['copyright'])) {
      $p['copyright'] = $d['copyright'];
    }    
    $rss->create_add_item( $channel_id, $p);
  }  
  // втсавляем картинку
  if ($feed['image_url']!='') {
    $p = array( 'title'     => $feed['image_title'],
  					   'url'       => $feed['image_url'],
  					   'width'     => $feed['image_width'],
  					   'height'    => $feed['image_height'],
  					   'description' => $feed['image_description'],
  					   'link' => $feed['link'] );
  					   
    foreach($p as $k => $v) { $p[$k] = mb_convert_encoding($v, "utf-8", 'cp1251'); }
    $rss->create_add_image( $channel_id, $p);
  }
  
  $rss->rss_create_document();
  header("Content-Type: text/html; charset=utf-8");
  print $rss->rss_document;
  exit();
}

// STUFF
/**
 * генерация оболчки ленты
 * вызов установленного контроллера списка
 * обработка списка
 * генерация конечного фида
 *
 * @param unknown_type $feed
 * @return unknown
 */
function compile_feed($feed) {
  global $sv;
  
  $ret = '';
  return $ret;
}

//eoc
}

?>