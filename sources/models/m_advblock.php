<?php


class m_advblock extends class_model {
  


  var $tables = array(
    'adv_blocks' => "
      `id` int(10) NOT NULL auto_increment,
      `title` varchar(255) default NULL,
      `link` int(11) default NULL,
      `text` text,
      `simple_mode` tinyint(1) default '0',
      `file` varchar(255) default NULL,
      `w` int(11) NOT NULL default '100',
      `h` int(11) NOT NULL default '100',
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      PRIMARY KEY  (`id`)
    "
  );
    
    
  var $dir = "uploads/b/";
  var $url = "uploads/b/";
  var $ext_ar = array('gif', 'png', 'jpg', 'swf');
  
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['adv_blocks'];
  
  $this->dir = PUBLIC_DIR.$this->dir;
  $this->url = PUBLIC_URL.$this->url;
  
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '70',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'unique' => 1
  ));    
  
  

  $this->init_field(array(
  'name' => 'text',
  'title' => 'Текст',
  'type' => 'text',   
  'len'  => '70',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit'), 
  ));    
  

  $this->init_field(array(
  'name' => 'file',
  'title' => 'Загрузить файл',
  'type' => 'varchar',
  'input' => 'file',  
  'show_in' => array('remove'),  
  'write_in' => array('create', 'edit')
  ));    
  
  

  $this->init_field(array(
  'name' => 'file_view',
  'title' => 'Препросмотр файла',
  'show_in' => array('default', 'edit'),
  'virtual' => 'file'
  ));    


  $this->init_field(array(
  'name' => 'w',
  'title' => 'Ширина',
  'type' => 'int',
  'size' => '11',
  'default' => '100',
  'show_in' => array('default', 'edit', 'remove'),
  'write_in' => array()
  ));      

  
  $this->init_field(array(
  'name' => 'h',
  'title' => 'Высота',
  'type' => 'int',
  'size' => '11',
  'default' => '100',
  'show_in' => array('default', 'edit', 'remove'),
  'write_in' => array()
  ));      

  
}


//validations ===============
function v_title($val) {
  $val = trim($val);
  
  if ($val=='') {
    $this->v_err = true;
    $this->v_errm[] = "Не указано название.";
  }
  
  return $val;
}

function v_file($val) {
 global $sv, $std, $db;

  $c_file = (isset($this->d['file'])) ? $this->d['file'] : "";

  if ($this->v_err) return false;
  
  $name = 'file';
  
  if (!$this->v_err) {    
    $file = $std->file->check_upload($name, $this->ext_ar, $this->dir, 0);    
    if ($file===false) {
      // не указан
      return $c_file;
    }
    $this->v_err = ($file['err']) ? true : $this->v_err;
    $this->v_errm = array_merge($this->v_errm, $file['errm']);
  }  
  
  
  if (!$this->v_err) {   
    // удаляем старый если был
    if ($c_file!='') {
      unlink($this->dir.$c_file);
    }
    
    if (move_uploaded_file($file['tmp_name'], $file['savepath']))	{	
    
      $img = getimagesize($file['savepath']);
      
      $this->add2roaster('w', $img[0]);      
      $this->add2roaster('h', $img[1]);      
      
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

//callbacks
function vcb_file_view($val) {
  global $db;
  
  
  $fn = $this->d['file'];
  $path = $this->dir.$fn;

  $checkbox = "<div style='padding: 5px 0 0 0;'><input type='checkbox' name='new[remove_file]'>&nbsp;удалить</div>";
  
  if ($fn=='') {
    $ret = "отсутствует";
    $exists = 0;
  }
  elseif (file_exists($path)) {
    $exists = 1;
    $url = $this->url.$fn;
    $ret = "<a href='{$url}' target=_blank>{$fn}</a>{$checkbox}";
  }
  else {
    $exists = 0;
    $ret = ($fn=='') ? "не загружен" : "{$fn} - файл не найден{$checkbox}";
  }
  
  
  return $ret;
}

function df_file_view($val) {
  
  $ret = ($val!='') ? "<a href='{$this->url}{$val}' target=_blank>{$val}</a>" : "<span style='color:red;'>нет файлов</span>";
  return $ret;
}


// pre post actions
function before_update() {
  global $sv, $db;
  
  if (isset($this->n['remove_file']) && $this->n['remove_file']=='on' && $this->d['file']!='') {
    $path = $this->dir.$this->d['file'];
    if (file_exists($path)) {
      unlink($path);
      $this->v_errm[] = "Удаляю {$path}";
    }
    else {
      $this->v_errm[] = "Прикрепленный файл удален.";
    }
    $db->q("UPDATE {$this->t} SET file='', w='100', h='100' WHERE id='{$this->current_record}'", __FILE__, __LINE__);
    $this->d['file'] = '';
    $this->d['w'] = 100;
    $this->d['h'] = 100;
  }
   
}

function before_edit() {
 
}
function after_update($d, $p, $err) {
  global $db, $sv;
  
  if (!$err) {
    $db->q("
    UPDATE {$sv->t['adv_show']} 
    SET text='".addslashes($p['text'])."'             
    WHERE block_id='{$d['id']}'", __FILE__, __LINE__);    
  }

}

function garbage_collector($d) {
  
  if ($d['file']!='') {
    unlink($this->dir.$d['file']);
  }
  
  return array('err' => false, 'errm'=>array());
}

function parse($d) {
  global $std;
  
  return $d;
}

//eoc
}  
  
?>