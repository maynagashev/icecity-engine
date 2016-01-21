<?php


class m_person extends class_model {

var $title = "Статья о человеке для модуля \"С норильском связанные судьбы\" на норкоме";

var $public_url = "http://www.norcom.ru/cat/persons/";

var $tables = array(
  'persons' => "
  `id` bigint(20) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `slug` varchar(255) NOT NULL default '',
  `text` text,
  `date` datetime default NULL,
  `photo` varchar(255) not null default '',
  
  `status_id` tinyint(1) not null default '0',
  `views` int(11) NOT NULL default '0',
  `replycount` int(11) NOT NULL default '0',
    
  `ip` varchar(255) default NULL,
  `agent` varchar(255) default NULL,
  `refer` varchar(255) default NULL,
  
  `created_at` datetime default NULL,
  `created_by` int(11) NOT NULL default '0',
  `updated_at` datetime default NULL,
  `updated_by` int(11) NOT NULL default '0',
  `expires_at` datetime default NULL,
  
  PRIMARY KEY  (`id`),
  KEY (`status_id`),
  KEY (`date`)  
  
  "
);
var $status_ar = array(
  0 => "Черновик",
  1 => "Опубликована", 
  2 => "Отключена"
  
);
 
var $ext_ar = array();
var $uploads_dir;
var $uploads_url;
  
function __construct() {
  global $sv;  
    
  $this->t = $sv->t['persons'];
 
  $this->ext_ar = array(
    'jpg', 'jpeg', 'png', 'gif'
  );
  $this->uploads_dir = FILES_DIR."persons/";
  $this->uploads_url = FILES_URL."/persons/";
  $this->uploads_make_resize = 1;
  $this->uploads_h = 130;
  $this->uploads_w = 100;
  $this->table_width = "100%";

  $this->init_field(array(
  'name' => 'date',
  'title' => 'Дата публикации',
  'type' => 'datetime',
  'show_in' => array('remove', 'default'),  
  'write_in' => array('edit')
  ));      

  
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Заголовок',
  'type' => 'varchar',  
  'len' => '70',
  'not_null' => 1,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'not_null' => 1
  ));    

 $this->init_field(array(
  'name' => 'slug',
  'title' => 'Строковый идентификатор в адресе статьи',
  'type' => 'varchar',  
  'len' => '70',
  'not_null' => 1,
  'show_in' => array( 'remove', 'edit'),
  'write_in' => array() 
  ));    
    

   
 $this->init_field(array(
  'name' => 'photo',
  'title' => 'Основное фото',
  'type' => 'varchar',  
  'input' => 'file',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit') 
  ));    
    
 $this->init_field(array(
  'name' => 'photo_preview',
  'title' => 'Превью основного фото',
  'virtual' => 'photo',  
  'show_in' => array('edit'),
  'write_in' => array() 
  ));    
        
  $this->init_field(array(
  'name' => 'ann',
  'title' => 'Анонс (краткая выдержка из статьи)',
  'type' => 'text',   
  'len'  => '70',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));      
  
  
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Полный текст статьи',
  'type' => 'text',   
  'len'  => '100',
  'show_in' => array('remove'),
  'write_in' => array('edit'), 
  'id' => 'full-text'
  ));    
  
  $this->init_field(array(
  'name' => 'views',
  'title' => 'Просмотров',
  'type' => 'int',
  'len' => 5,
  'show_in' => array('default', 'remove'),
  'write_in' => array()
  ));    

  
  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Статус',
  'type' => 'int',
  'input' => 'select',  
  'belongs_to' => array('list' => $this->status_ar),
  'show_in' => array('remove'),  
  'write_in' => array('edit')
  ));    
  $this->init_field(array(
  'name' => 'status',
  'title' => 'Статус',
  'virtual' => 'status_id',
  'show_in' => array('default')
  ));    
  

  $this->init_field(array(
  'name' => 'ip',
  'title' => 'IP',
  'type' => 'varchar',
  'show_in' => array( 'remove'),
  'write_in' => array()
  ));         
  

  $this->init_field(array(
  'name' => 'agent',
  'title' => 'Agent',
  'type' => 'varchar',
  'show_in' => array('remove'),
  'write_in' => array()
  ));         
  

  $this->init_field(array(
  'name' => 'refer',
  'title' => 'Refer',
  'type' => 'varchar',
  'show_in' => array('remove'),
  'write_in' => array()
  ));
  



  
}

function parse($d) {
  global $std;
  $d['f_date'] = $std->time->format($d['date'], 1, 1); 
  $d['url'] = $this->url."?id={$d['id']}";
  $d['edit_url'] = u($sv->act, 'edit', $d['id']);
  return $d;
}

// validations
function v_title($t) {
  
  $t = trim($t);
 
  return $t;
}

function v_photo($val) {  
  return $this->ev_file(0);
}

function vcb_photo_preview($fn) {
  global $std;
  
  $url = $this->uploads_url.$fn;
  $rurl = $this->uploads_url.$this->ev_file_resizename($fn);
  
  $ret = ($fn!='') ? "<a href='{$url}' target=_blank><img src='{$rurl}' border='0'></a>" : "-";
  
  $ret = $std->file->text_show_replace($ret);
  return $ret;
}

function v_ann($t) {  
  global $std;
  $t = $std->file->text_save_replace($t);  
  return $t;
}
function wcb_ann($t) {
  global $std;
  $t = $std->file->text_show_replace($t);  
  return $t;
}

function v_text($t) {  
  global $std;
  $t = $std->file->text_save_replace($t);  
  return $t;
}
function wcb_text($t) {
  global $std;
  $t = $std->file->text_show_replace($t);  
  return $t;
}

function df_title($t) {  
  return "<div align=left>{$t}</div>";
}

function last_v($p) {
  global $sv, $std, $db;
  
  if ($this->code=='create') {
    $p['date'] = $sv->date_time;  
    $p['ip'] = $sv->ip;
    $p['agent'] = $sv->user_agent;
    $p['refer'] = $sv->refer;
    $p['status_id'] = 0;
    
  }
  
  $p['slug'] = $this->compile_slug($p['title']);
  
  
  return $p;
}

// controllers
function c_edit() {
  global $sv, $std, $db;
  
  $d = $this->get_current_record();
  $s = $this->init_submit(); 
  
  if ($s['submited'] && !$s['err']) {
    if (isset($sv->_post['commit'])) {      
      header("Location: ".su($sv->act).$this->slave_url_addon);  exit();
    }
    $d = $this->get_current_record();     
  }
  
  $ret['s'] = $s;  

  $ret['form'] = $this->compile_edit_table($d);
  
  /*
  // attaches
  $sv->load_model('attach');  
  $sv->m['attach']->action_url = u($sv->act, "attaches", $d['id']);
  $ret['attach'] = $sv->m['attach']->init_object('news', $d['id'], $sv->user['session']['account_id']);
  $sv->parsed['admin_sidebar'] = "
    <div style='margin-top: 700px; border: 1px solid #dddddd;'>
      <div style='padding: 5px 10px;background-color:#efefef;'><b>Прикрепление файлов</b></div>
      {$ret['attach']['form']}
    </div>";
  */
  return $ret;
}
function c_attaches() {
  global $sv, $std, $db;
  
  $d = $this->get_current_record();
  if (!$d) die("Новость не найдена.");
  
  // attaches
  $sv->load_model('attach');  
  $sv->m['attach']->action_url = u($sv->act, "attaches", $d['id']);
  $ret['attach'] = $sv->m['attach']->init_object('news', $d['id'], $sv->user['session']['account_id']);
  
  return $ret;
}

function compile_slug($t) {
  global $std;
  
  $t = $std->text->translit($t);
  $t = str_replace(" ", "_", $t);
  $t = preg_replace("#[^a-z0-9\_\-]#si", "", $t);
  $t = strtolower($t);
  
  return $t;
}


function text_show_replace($t, $type='admin') {
  
  switch($type) {
    case 'user':
      //$t = preg_replace("#http\://img\.norcom\.ru#msi", "/img_norcom_ru", $t);
    break;
    default:
      $t = preg_replace("#http\://img\.norcom\.ru#msi", "img_norcom_ru", $t);
      
  }
  
  
  
  return $t;
}
function text_save_replace($t) {

  $t = preg_replace("#https\://redaktor\.wlan(\:447|\:444)#msi", "", $t);
  $t = preg_replace("#redaktor\.wlan(\:447|\:444)#msi", "", $t);
  $t = preg_replace("#=(\"|'|)[^=\(]*img_norcom_ru#msi", "=\\1http://img.norcom.ru", $t);
  $t = preg_replace("#/?img_norcom_ru#msi", "http://img.norcom.ru", $t);
 
  return $t;
}

//eoc
}

?>