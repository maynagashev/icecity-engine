<?php


class m_serv extends class_model {

  var $tables = array(
    'services' => "
      `id` bigint(20) NOT NULL auto_increment,
      `title` varchar(255) NOT NULL default '',
      `description` text null,
      `text` text null,
      `image` varchar(255) null,
      
      `slug` varchar(255) NOT NULL default '',
      `status` int(1) not null default '1',
      `views` int(11) not null default '0',      
      `place` int(11) not null default '999',
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      PRIMARY KEY  (`id`),
      KEY `slug` (`slug`)    
        "
  );

var $c_cat = false;  
  
var $load_attaches = 1;
var $attaches_page = '';
var $attaches_top_margin = 500;
var $attaches_resizes = "140x115xf,400x266xw";

var $load_markitup = 1;
var $markitup_use_tinymce = 1;
var $markitup_use_emoticons = 0;
var $markitup_width = '100%';
var $markitup_selector = 'textarea';
var $markitup_type = 'html';


 // ЗАГРУЗКА ФАЙЛОВ 
  /**
   * Массив раширений для одиночно загружаемых файлов
   *
   * @var unknown_type
   */
  var $ext_ar = array('jpg', 'gif', 'png');  
  /**
   * Пути по умолчанию (необходимо назначить)
   *
   * @var unknown_type
   */
  var $uploads_dir = "uploads/";
  var $uploads_url = "uploads/";


function __construct() {
  global $sv;  

  $this->uploads_dir = PUBLIC_DIR.$this->uploads_dir;
  $this->uploads_url = PUBLIC_URL.$this->uploads_url;
   
  $this->t = $sv->t['services'];
  
  $this->init_field(array(
  'name' => 'place',
  'title' => 'Номер по порядку в списке',
  'type' => 'int',  
  'len' => 6,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));  
    
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название услуги',
  'type' => 'varchar',  
  'len' => 40,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'public_search' => 1
  ));    
  
 $this->init_field(array(
  'name' => 'description',
  'title' => 'Краткое описание',
  'type' => 'text',  
  'len' => 50,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit'),
  'public_search' => 1
  ));    
    /*
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Полное описание',
  'type' => 'text',  
  'len' => 60,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit'),
  'public_search' => 0
  ));   
    */
  $this->init_field(array(
  'name' => 'slug',
  'title' => 'Адрес категории (англ)',
  'type' => 'varchar',
  'len' => '25',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));  
  /*
 $this->init_field(array(
  'name' => 'views',
  'title' => 'Просмотры',
  'type' => 'int',  
  'len' => 10,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));    
    */
  $this->init_field(array(
  'name' => 'status',
  'title' => 'Включена?',
  'type' => 'boolean',  
  'default' => 1,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));  
  
  $this->init_field(array(
  'name' => 'image',
  'title' => 'Загрузка фото',
  'type' => 'varchar',
  'input' => 'file',
  'show_in' => array(),
  'write_in' => array('edit')
  ));          

  $this->init_field(array(
  'name' => 'image_view',
  'title' => 'Фото',
  'virtual' => 'image',
  'show_in' => array('default', 'edit'),
  'write_in' => array()
  ));  
      
}

//parsers
function parse($d) {
  $d['url'] = "/services/{$d['slug']}/";
  $d['url_image'] = ($d['image']!='') ? $this->uploads_url.$d['image'] : "/images/serv.jpg"; 
  return $d;
}
function parse_search($d) {
  global $std;
  
  $title = $d['title'];
  $url = $d['url'];
  $desc = $d['description'];  
    
  $p = array(
    'title' => $title, 
    'description' => $desc,
    'url' => $url
  );
  return $p;
}

// validators
function v_image() {  
  return $this->ev_file(0);
}
		
function last_v($p) {
  global $sv, $std, $db;
  
	 // slug
  $id = ($this->code=='edit') ? $this->current_record : 0; 
  $p['slug'] = (isset($p['slug'])) ? $p['slug'] : '';
  if (!$std->text->is_valid_slug($p['slug'], $this->t, 'slug', $id)) {
    $p['slug'] = $std->text->gen_slug($p['title'], $this->t, 'slug');
  }
 
  return $p;
 }
 
// callbacks
function vcb_image_view($val) {
  return $this->ev_file_view($this->current_callback, 'remove_file');
}

function df_image_view($val) {  
  $ret = ($val!='') ? "<img src='{$this->uploads_url}{$val}' border=0>" : "<span style='color:red;'>нет файлов</span>";
  return $ret;
}

// pre post
function before_update() {
  global $sv, $db;    
  $this->ev_init_file_remove('image', 'remove_file');   
} 
 
//eoc
}

		
?>