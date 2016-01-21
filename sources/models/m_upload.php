<?php

/**
 * Upload Model (no previews)
 * Saving by created_at - parsing
 * 
 * Special KEYS
 * article_id - object
 * nomer_id
 * tags
 * 
 * Revision: 1.3
 */


class m_upload extends class_model {

  var $tables = array(
    'uploads' => "
      `id` bigint(20) NOT NULL auto_increment,
      `filename` varchar(255) NOT NULL default '',
      `savename` varchar(255) NOT NULL default '',
      `title` varchar(255) NOT NULL default '',
      `text` text,
      `tags` varchar(255) default NULL,
      `page` varchar(250) default NULL,
      `object` bigint(20) NOT NULL default '0',
      `size` int(11) NOT NULL default '0',
      `mime` varchar(255) NOT NULL default '',
      `ext` varchar(20) NOT NULL default '',
      `views` int(11) NOT NULL default '0',
      `nomer_id` int(11) NOT NULL default '0',
      `author` varchar(255) default NULL,
      `active` tinyint(3) default '1',
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      PRIMARY KEY  (`id`),
      KEY `object` (`object`),
      KEY `nomer_id` (`nomer_id`),
      KEY `tags` (`tags`)    
    "
  );
    
  var $page = "";
  var $object = null; //object data  
  
  
  var $savedir = "uploads/";
  var $openurl = "/uploads/";
  
  var $ext_ar = array('gif', 'png', 'jpg', 'jpeg', 'zip', 'rar', 'doc', 'xls', 'pdf');
  var $error_codes = array(
  
    0=>"Файл загружен без ошибок.", 
    1=>"Првышен лимит upload_max_filesize в натсройках сервера php.ini.", 
    2=>"Превышен лимит MAX_FILE_SIZE указанный в форме.",
    3=>"Файл был загржен лишь частично.",
    4=>"Файл не был указан.",
    6=>"Временная папка для загрузки не доступна." 
          
  );
  
  var $fixed = array();
  var $fixed_keys = array();

function __construct() {
  global $sv;
  
  $this->t = $sv->t['uploads'];
  $this->per_page = 10;
  $this->savedir = PUBLIC_DIR."uploads/";
  $this->openurl = PUBLIC_URL."uploads/";
  $this->fixed_keys = array_keys($this->fixed);
 
  
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название',
  'type' => 'varchar',
  'size' => '255',
  'len' => '50',
  'default' => '',
  'show_in' => array(),
  'write_in' => array('edit')
  ));  

 
  $this->init_field(array(
  'name' => 'page',
  'title' => 'Раздел',
  'type' => 'varchar',
  'size' => '255',    
  'len' => '11',
  'default' => 'site',
  'show_in' => array(),
  'write_in' => array()  
  ));  

  
  
  $this->init_field(array(
  'name' => 'object',
  'title' => 'Родительский объект',
  'type' => 'int',
  'size' => '11', 
  'len' => '3',
  'default' => '',
  'show_in' => array(),
  'write_in' => array()
  ));  


  
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Описание',
  'type' => 'text',
  'size' => '',
  'len' => '50',
  'default' => '',
  'show_in' => array(),
  'write_in' => array('edit')
  ));    

  $this->init_field(array(
  'name' => 'author',
  'title' => 'Источник',
  'type' => 'varchar',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));

  
  $this->init_field(array(
  'name' => 'tags',
  'title' => 'Теги',
  'type' => 'varchar',
  'len' => '80',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));
  

    
  $this->init_field(array(
  'name' => 'filename',
  'title' => 'Имя файла',
  'type' => 'varchar',
  'size' => '255',
  'len' => '50',
  'default' => '',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));
  

  $this->init_field(array(
  'name' => 'savename',
  'title' => 'Файл',
  'type' => 'varchar',
  'size' => '255',
  'len' => '50',
  'input' => 'file',
  'default' => '',
  'show_in' => array('edit', 'remove'),
  'write_in' => array('create')
  ));
  
 

   
      
  $this->init_field(array(
  'name' => 'mime',
  'title' => 'MIME тип файла',
  'type' => 'varchar',
  'size' => '255',
  'len' => '50',
  'default' => '',
  'show_in' => array('remove'),
  'write_in' => array()
  ));
      
      
  $this->init_field(array(
  'name' => 'size',
  'title' => 'Размер файла в байтах',
  'type' => 'int',
  'size' => '11',
  'len' => '12',
  'default' => '0',
  'show_in' => array('remove'),
  'write_in' => array()
  ));
 
}

function v_title($val) {  
  $val = trim($val);

 
  
  return $val;
}


function v_shortcut($val) {  
  $val = trim($val);

  $val = preg_replace("#\s#msi", "_", $val);
  $val = preg_replace("#[^a-z0-9\_\-\.]#msi", "", $val);
  if ($val=='') {
    $this->v_errm[] = "Название для URL не указано";
    $this->v_err = true;
  }
 
  $val = strtolower($val);
  
    
  
  return $val;
}

/**
 * Uploading FILE
 *
 * @param unknown_type $val
 * @param unknown_type $name
 * @return unknown
 */
function v_savename($val, $name='savename') {  
  global $sv, $std, $db;
  

  if ($this->v_err) return false;
  
  $subdir = $this->parse_subdir();
  
  if (!$this->v_err) {    
    $file = $std->file->check_upload($name, $this->ext_ar, $this->savedir.$subdir);    
    $this->v_err = ($file['err']) ? true : $this->v_err;
    $this->v_errm = array_merge($this->v_errm, $file['errm']);
  }  
  
  
  if (!$this->v_err) {   
    if (move_uploaded_file($file['tmp_name'], $file['savepath']))	{	
      $this->add2roaster('filename', $file['name']);
      $this->add2roaster('mime', $file['type']);
      $this->add2roaster('size', $file['size']);

    $this->v_errm[] = "Файл успешно загружен.";  
    }
    else {
      $this->v_err = true;
      $this->v_errm[] = "Не удалось переместить файл из временной папки: {$file['tmp_name']} &rarr; {$file['savepath']}";     
      return false;
    }
  }
 
  return $file['savename'];
}


function vcb_savename($v, $d = array()) {
  $subdir = $this->parse_subdir($this->d['created_at']);
  $fn = $this->savedir.$subdir.$v;
  $e = (file_exists($fn)) ? "" : " - <span style='color:red;'>не найден</span>";
  $v = "<a href='{$this->openurl}{$subdir}{$v}' target=_blank>{$v}</a>{$e}";
  
  return $v;
}





// controllers ===================================

/**
 * Custom list related to PAGE and OBJECT
 *
 * @return unknown
 */
function c_default() {
  global $sv, $std, $db;
  
  $ret['fields'] = $this->get_active_fields('show');
  $sort = $this->get_sort($ret['fields']); 
  
  $db->q("SELECT 0 FROM {$this->t} WHERE object='{$this->object['id']}' ");  
  $ret['pl'] = $pl = $std->pl($db->nr(), 40, $sv->_get['page'], u($sv->act, $sv->code, $sv->id)."page=");
  
  //$j = $this->get_joins();
  
  $ar = array(); $i = $pl['k'];
  $q = "  SELECT {$this->t}.*
          FROM {$this->t}        
          WHERE object='{$this->object['id']}'
          ORDER BY `active` DESC, `title` ASC {$pl['ql']}";
  

  
  $db->q($q, __FILE__, __LINE__);
  while ($d = $db->f()) { $i++;
    $d['i'] = $i;   
    $ar[] = $d;
  }
  
  
  
  //addon parse values
  foreach($ar as $key=>$d) {
    $d = $this->parse($d);
    $this->d = $d;  
    foreach($ret['fields'] as $k) {     
      // callbacks
      $callback = "vcb_{$k}";
      if (method_exists($this, $callback)) {    
        $this->current_callback = $k;   
        $d[$k] = $this->$callback($d[$k]);          
      }  
    }   
    $ar[$key] = $d;
  }
  
  $ret['list'] = $ar;
  $ret['count'] = count($ar);
  
  
  return $ret;
}

// pre / post actions 
function before_create() {
  global $sv, $db;
  
  
  $this->add2roaster("object", $this->object['id']);
  
  /*
  $object_id = intval($this->object['id']);
  $db->q("SELECT nomer_id FROM {$sv->t['pages']} WHERE id='{$object_id}'", __FILE__, __LINE__);
  if ($db->nr()<=0) {
    $this->v_err = true;
    $this->v_errm[] = "Невозможно инициализировать страницу к которой должен быть прикреплен файл.";
  }
  else {
    $d = $db->f();
    $this->add2roaster('nomer_id', $d['nomer_id']);
  }
  */
  
}

function garbage_collector($d) {  
  $err = false;  $errm = array();  
  
  $r = $this->unlink_file($d['id']);
  $err = ($r['err']) ? 1 : $err;
  $errm = array_merge($errm, $r['errm']);   
 
  return array('err'=>$err, 'errm'=>$errm, 'last'=>true);
}

function after_update($d, $p, $err) {
  global $sv, $db;
  
  
  $db->q("SELECT 0 FROM {$this->t} WHERE object='{$this->d['object']}'", __FILE__, __LINE__);
  $c = $db->nr();
  
  if ($c==1) {
    //$p['leading'] = 1;
    //$db->q("UPDATE {$this->t} SET `leading`='1' WHERE id='{$this->d['id']}'", __FILE__, __LINE__);
  }
  
  if (!$err && $p['leading']==1) {
    //$db->q("UPDATE {$this->t} SET `leading`='0' WHERE `object`='{$this->d['object']}' AND id<>'{$this->d['id']}'", __FILE__, __LINE__);
    $db->q("UPDATE {$this->t} SET `active`='1' WHERE id='{$this->d['id']}'", __FILE__, __LINE__);
    //$db->q("UPDATE {$sv->t['pages']} SET `aim_id`='{$this->d['id']}' WHERE id='{$this->d['object']}'", __FILE__, __LINE__);
  }
}

// other =========================================

/**
 * Not used Here
 *
 * @return unknown
 */
function sub_scaffold() {
  global $sv, $std, $db;
  
  $this->code = $sub = (isset($sv->_get['sub']) && $sv->_get['sub']!='') 
                      ? preg_replace("#[^a-z0-9\_\-\.]#msi", "", $sv->_get['sub']) : "default";
                      
  $this->id = (isset($sv->_get['id']) && $sv->_get['id']!='') ? intval($sv->_get['id']) : 0;     
  $this->current_record = intval($this->id);    
  
  
  $sv->sub_url = u($sv->act, $sv->code, $sv->id);
   
  $c = $this->get_controllers($sub);  
  $this->call = $c['call'];
  $this->submit_call = $c['submit'];

  eval("\$ret = \$this->{$c['call']}();");
 
  $ret['m'] = &$this;
  
 
  return $ret;
}

function unlink_file($id) {
  global $sv, $db;
  
  $err = false;  $errm = array();  
  
  $id = intval($id);
  $db->q("SELECT * FROM {$this->t} WHERE id='{$id}'", __FILE__, __LINE__);
  if ($db->nr()<=0) {
    $err = 1;
    $errm[] = "Файл не найден в базе";
  }
  else {
    $d = $db->f();    
  }
  
  
  if (!$err) {
    $ar = $this->find_all_childs($d['savename'], $d['created_at']);
    $ar[] = $d['savename'];
    $subdir = $this->parse_subdir($d['created_at']);
    foreach($ar as $d) {
      $path = $this->savedir.$subdir.$d;
      @unlink($path);
      if (file_exists($path)) {
        $err = 1;
        $errm[] = "Не удалось удалить <b>{$path}</b>.";
      }
      else {
        $errm[] = "{$path} - удален;";
      }
    }    
  }
  if (!$err) {
    $db->q("DELETE FROM {$this->t} WHERE id='{$id}'", __FILE__, __LINE__);
  }
  
  return array('err'=>$err, 'errm'=>$errm);
}

function find_all_childs($fn, $created_at = false) {
global $sv;

  $subdir = $this->parse_subdir($created_at);
  
  
  $ar = array();
  $files = scandir($this->savedir.$subdir);
  foreach($files as $f) {
    if (preg_match("#^(wh|gs)([0-9]+)_{$fn}$#", $f, $m)) {     
      $add = ($m[1]=='gs') ? "gs" : "";
      $ar[$add.$m[2]] = $f;
    }
  }    
  
  return $ar;
}

function check_upload($name) {
  global $sv;
  
  if ($this->v_err) return false;
    
  if (!isset($sv->_files[$name]) || !is_array($sv->_files[$name])) {
    $this->v_err = true;
    $this->v_errm[] = "File input not found.";
    return false;
  }
  
  $up = $sv->_files[$name];
  $err = intval($up['error']);
  
  if ($err!=0) {
    $this->v_err = true;
    $this->v_errm[] = $this->error_codes[$err];
    return false;
  }  
  
  return true;
}

function parse_subdir($created_at = false) {
  global $sv, $std;
  
  if ($created_at===false) {
   
    $time = $sv->post_time;    
  }
  else {
    $time = strtotime($created_at);
  }
  
  $subdir = date("Y/m/d/", $time);
  
  
  if ($created_at===false) {
    $save_dir = $this->savedir.$subdir;
    $std->file->create_dirtree($save_dir);
  }
  
  
  return $subdir;
}

/**
 * Serialilize previews
 *
 * @param unknown_type $d
 * @return unknown
 */
function parse($d) {
  global $sv, $std, $db;
  
  $d['subdir'] = $this->parse_subdir($d['created_at']);
  
  $fn = $d['savename'];
 
  $path = $this->savedir.$d['subdir'].$fn;  
  
  
 
  return $d;
}

function light_parse($d) {
  global $sv, $std, $db;
  
  $d['subdir'] = $this->parse_subdir($d['created_at']);  
  
  
  //m_preview
  $keys = array_keys($d['childs']);
  $k = 150;
  foreach($keys as $i) {
    if ($i>150 && $i<500) {
      $k = $i;
     
    }
  }
  
  $d['m_preview'] = $d['childs'][$k];
  
 
  return $d;
}

function parse_in_article($d) {
  global $sv;
  
  $d['subdir'] = $this->parse_subdir($d['created_at']);  
  $d['pr'] = unserialize($d['previews']);  
  $d['p']['openurl'] = $d['subdir'].$d['savename'];
  
  
  
  return $d;
}

function create_grayscale($src_file, $target_file, $replace = false) {
  
  $err = false;
  $errm = array();
  $ns = false; 
  $t = array(); 
  
  if (file_exists($target_file) && !$replace) {
    $err = true;
    $errm[] = "Файл {$target_file} существует, пропускаем. ";
  }
  
 
  if (!$err) {
    $ext =(preg_match("#^(.*)\.([a-z0-9]{2,10})$#msi", $src_file, $m)) ?  strtolower($m[2]) :  "";  
    switch ($ext)
  	{
  		case 'jpg': case 'jpeg':
        if (imagetypes() & IMG_JPG) {$im_src = ImageCreateFromJPEG($src_file); } else {  $ns = true; } 
        $t['format'] = "JPG";		
      break;
  		case 'png': 
        if (imagetypes() & IMG_PNG) {$im_src = ImageCreateFromPNG($src_file); } else {$ns = true;} 
        $t['format'] = "PNG";
      break;
  		case 'gif': 
        if (imagetypes() & IMG_GIF) {$im_src = ImageCreateFromGIF($src_file); } else {$ns = true;}
        $t['format'] = "GIF"; 
      break;
  		default: 
  		  $err = 1;
  		  $errm[] = "Не поддерживаемый тип файла <b>{$ext}</b> в {$path}";
  	}		
  	
  	if ($im_src==false) {
  	  $err = 1;
  	  $errm[] = "Не удалось прочитать файл.";	  
  	}
  }
  
  
	if (!$err) {
	  if (!imagefilter($im_src, IMG_FILTER_GRAYSCALE)) {
	    $err = true;
	    $errm[] = "Не удалось применить черно-белый фильтр.";
	  }    
	}
	
	if (!$err) {
    if ($t["format"]=="JPG") { 
     imageJPEG($im_src, $target_file); 
    } 
    elseif ($t["format"]=="PNG") { 
     imagePNG($im_src, $target_file); 
    } 
    elseif ($t["format"]=="GIF") { 
     imageGIF($im_src, $target_file); 
    } 
	}
  $t['img'] = $img;
  $t['err'] = $err;
  $t['errm'] = $errm;
  
  return $t;
  
}


}

?>