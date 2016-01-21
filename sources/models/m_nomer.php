<?php

/*
CREATE TABLE `nomera` (
  `id` bigint(20) NOT NULL auto_increment,
  `date` date default NULL,
  `nomer` int(11) NOT NULL default '0',
  `nomer2` int(11) NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `active` tinyint(3) NOT NULL default '0',
  `created_at` datetime default NULL,
  `created_by` int(11) NOT NULL default '0',
  `updated_at` datetime default NULL,
  `updated_by` int(11) NOT NULL default '0',
  `expires_at` datetime default NULL,
  `status_id` tinyint(3) NOT NULL default '0',
  `pdf_file` varchar(255) default NULL,
  `pdf_exists` tinyint(3) NOT NULL default '0',
  `pdf_size` int(11) NOT NULL default '0',
  `photo` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `nomer2` (`nomer2`),
  KEY `status` (`status_id`),
  KEY `pdf` (`pdf_exists`)
)

*/

class m_nomer extends class_model {
  
  var $tables = array(
    'nomera' => "
      `id` bigint(20) NOT NULL auto_increment,
      `date` date default NULL,
      `nomer` int(11) NOT NULL default '0',
      `nomer2` int(11) NOT NULL default '0',
      `count` int(11) NOT NULL default '0',
      `active` tinyint(3) NOT NULL default '0',
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      `status_id` tinyint(3) NOT NULL default '0',
      `pdf_file` varchar(255) default NULL,
      `pdf_exists` tinyint(3) NOT NULL default '0',
      `pdf_size` int(11) NOT NULL default '0',
      `photo` varchar(255) NOT NULL,
      PRIMARY KEY  (`id`),
      KEY `nomer2` (`nomer2`),
      KEY `status` (`status_id`),
      KEY `pdf` (`pdf_exists`)
    "
  );
  
  var $status_ar = array(
  0 => 'Черновик',
  1 => 'Опубликован',
  2 => 'Скрыт'
  
  );
  
  var $pdf_dir = "pdf/";
  var $pdf_url = "pdf/";
  var $pdf_ext = array('pdf', 'doc', 'rar');
  
  
  var $photo_dir = "uploads/front/";
  var $photo_url = "uploads/front/";
  
  var $ext_ar = array(
    'jpg', 'jpeg', 'png', 'gif'
  );

  var $front_width = 175;
   
  var $error_codes = array( 
    0=>"Файл загружен без ошибок.", 
    1=>"Првышен лимит upload_max_filesize в натсройках сервера php.ini.", 
    2=>"Превышен лимит MAX_FILE_SIZE указанный в форме.",
    3=>"Файл был загржен лишь частично.",
    4=>"Файл не был указан.",
    6=>"Временная папка для загрузки не доступна."           
  );

  var $time = 0;
  var $date = null;
  var $d = null;

  
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['nomera'];
  $this->per_page =  20;    
  $this->pdf_dir = PUBLIC_DIR.$this->pdf_dir;
  $this->pdf_url = PUBLIC_URL.$this->pdf_url;
  
  $this->photo_dir = PUBLIC_DIR.$this->photo_dir;
  $this->photo_url = PUBLIC_URL.$this->photo_url;

  

  $this->init_field(array(
  'name' => 'date',
  'title' => 'Дата выпуска',
  'type' => 'date',  
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'unique' => 1
  ));    
  
  $this->init_field(array(
  'name' => 'nomer',
  'title' => 'Номер с начала года',
  'type' => 'int',
  'size' => '11',
  'len' => '5',
  'default' => '',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  
  ));  
  
  $this->init_field(array(
  'name' => 'nomer2',
  'title' => 'Номер по порядку за все время',
  'type' => 'int',
  'size' => '11',
  'len' => '6',
  'default' => '',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'unique' => 1
  ));  
  



  $this->init_field(array(
  'name' => 'photo',
  'title' => 'Обложка',
  'type' => 'file',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));  
  
  $this->init_field(array(
  'name' => 'photo_view',
  'title' => 'Препросмотр обложки',
  'show_in' => array('default', 'edit'),
  'virtual' => 'photo'
  ));      

   
  
  $this->init_field(array(
  'name' => 'count',
  'title' => 'Количество статей',
  'type' => 'int',
  'size' => '11',
  'len' => '5',
  'show_in' => array('default', 'edit', 'remove')
  
  ));
 
  $this->init_field(array(
  'name' => 'active',
  'title' => 'Активный',
  'type' => 'boolean',
  'size' => '3',
  'len' => '3',
  'default' => '0',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));    



  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Статус',
  'type' => 'tinyint',
  'input' => 'select',  
  'show_in' => array('remove'),
  'write_in' => array('edit'),
  'belongs_to' => array('list' => $this->status_ar, 'not_null' => 1)
  ));    
      
  //virtual
  $this->init_field(array(
  'name' => 'status',
  'title' => 'Статус',
  'show_in' => array('default'),
  'write_in' => array(),
  'virtual' => "status_id"
  ));     
    

  $this->init_field(array(
  'name' => 'pdf_file',
  'title' => 'Загрузить PDF версию',
  'type' => 'varchar',
  'input' => 'file',  
  'show_in' => array(),
  'write_in' => array('edit')
  ));    
  
  

  $this->init_field(array(
  'name' => 'pdf_file_view',
  'title' => 'PDF файл',
  'show_in' => array('default', 'edit', 'remove'),
  'virtual' => 'pdf_file'
  ));    

  $this->init_field(array(
  'name' => 'pdf_exists',
  'title' => 'PDF загружен',
  'type' => 'tinyint',
  'size' => '3',
  'show_in' => array()
  ));       
  

  $this->init_field(array(
  'name' => 'pdf_size',
  'title' => 'Размер PDF (байт)',
  'type' => 'int',
  'size' => '11',
  'show_in' => array('edit')
  ));      
  
}

function c_view() {
  global $sv, $std, $db;
  
  $sv->load_model("article");
  $statuses = $sv->m['article']->status_ar;
  
  $this->get_current_record();
  $d = &$this->d;
  
  $s = $this->init_submit();  
  if ($s['submited'] && !$s['err']) {
    $d = $this->get_current_record();
  }

  // LIST
  $ar = array();
  $db->q("SELECT a.*, c.title as cat_title FROM {$sv->t['articles']} a 
          LEFT JOIN {$sv->t['maincats']} c ON (a.cat_id=c.id)
          WHERE a.nomer_id='{$d['id']}' 
          ORDER BY a.prioritet ASC, a.pop DESC, a.title ASC", __FILE__, __LINE__);
  
  $i = 0;
  while($f = $db->f()) { $i++;
    $f['prioritet'] = $i*10;
    $f['f_status'] = $statuses[$f['status_id']];
    
    $ar[] = $f;
  }
  
  $ret['articles'] = $ar;
  $ret['count'] = $c = count($ar);
  $ret['d'] = $d;
  
  $opts = array();
  for($i=1; $i<=$c; $i++) {
    $j = 10*$i;
    $opts[] = $j;
  }
  $ret['opts'] = $opts;
  
  //update count
  $db->q("UPDATE {$this->t} SET `count`='{$c}' WHERE id='{$this->current_record}'", __FILE__, __LINE__);
  
  // next / prev
  $ar = array('next' => "`nomer2`>'{$d['nomer2']}' ORDER BY `nomer2` ASC, `id` ASC", 
              'prev' => "`nomer2`<'{$d['nomer2']}' ORDER BY `nomer2` DESC, `id` DESC");
  foreach ($ar as $k=>$sql) {
    $q = "SELECT * FROM {$sv->t['nomera']} WHERE ".$sql." LIMIT 0,1";
    $db->q($q, __FILE__, __LINE__);
    if ($db->nr()>0) {      
      $f = $db->f();
      //$f = $this->m->parse($f);
      $ret[$k] = $f;
    }
    else {
      $ret[$k] = false;
    }    
  }
  

  return $ret;
}

function sc_view($err, $errm, $n, $d) {
  global $db, $sv;
  foreach ($n['items'] as $id => $ar) {
    $id = intval($id);
    $p = intval($ar['prioritet']);
    $q = "UPDATE {$sv->t['articles']} SET prioritet='{$p}' WHERE id='{$id}'";
  
    $db->q($q, __FILE__, __LINE__);    
  }
}

//validations ===============
function v_date($val) {
  
  if ($val=='0000-00-00') {
    $this->v_err = true;
    $this->v_errm[] = "Не указана дата выпуска.";
  }
  
  return $val;
}

function v_nomer($val) {
  global $sv, $db, $std;
  
  $val = abs(intval($val));
  if ($val==0) {
    $this->v_err = 1;
    $this->v_errm[] = "Номер с начала года не указан.";
  }
  
  //check in year
  if (isset($this->p['date']) && preg_match("#^([0-9]{4})\-[0-9]+\-[0-9]+$#msi", $this->p['date'], $m)) {
    $y = $m[1];
    $add = ($this->code=='edit') ? " AND id<>'{$this->current_record}'" : "";
    $q = "SELECT * FROM {$this->t} WHERE YEAR(`date`)='{$y}' AND `nomer`='{$val}' {$add}";
    $db->q($q, __FILE__, __LINE__);
    if ($db->nr()>0) {
      $this->v_err = true;
      $this->v_errm[] = "В указанном периоде уже судествует такой <b>номер выпуска с начала года</b>.";
    }    
  }
  
  return $val;
}

function v_nomer2($val) {
  $val = intval($val);
  if ($val<=0) {
    $this->v_err = true;
    $this->v_errm[] = "Не указан номер выпуска за все время (уникальный).";
  }
  return $val;
}
function v_pdf_file($val) {
 global $sv, $std, $db;
  
  $name = 'pdf_file';
  $d_ret = $this->d[$name];
  
  if ($this->v_err) return $d_ret;
  
  
  
  if (!isset($sv->_files[$name]) || !is_array($sv->_files[$name])) {   
    //$this->v_errm[] = "Файл не получен, оставляем {$this->d[$name]}";     
    return $d_ret;
  }
  
  $up = $sv->_files[$name];
  $err = intval($up['error']);
  
  if ($err!=0) {    
    if ($err!=4) {
      $this->v_errm[] = $this->error_codes[$err];
    }
    else {
      //$this->v_errm[] = "Файл не получен, оставляем {$this->d[$name]}";     
    }
    return $d_ret;
  }
  
 
  $filename = $up['name'];
    
  // ext check
  $ext = (preg_match("#^(.*)\.([a-z0-9]{2,10})$#msi", $filename, $m)) ? strtolower($m[2]) : "";
 
  $good = false;   
  foreach($this->pdf_ext as $e)  {
    if ($e===$ext) {
      $good = true;
      break;
    }
  }  
  if (!$good) {    
    $this->v_errm[] = "Неразрешенный для загрузки тип файла, 
    расширения \"<b>{$ext}</b>\" нет в списке: ".implode(", ", $this->pdf_ext);
    return $d_ret;
  }
  
  
  
  if ($ext=='php' || $ext=='phtml') {   
    $this->v_errm[] = "Загрузка php скриптов запрещена из соображений безопасности.";
    return $d_ret;    
  }
  
  
    
  $savename = $this->d['date']."-".$this->d['nomer2'].".".$ext;
  $save_path = $this->pdf_dir.$savename;
  
  $i = 0;
  while (file_exists($save_path)) { 
    $this->v_errm[] = "{$save_path} уже существует, удаляем чтобы загрузить новый.";
    unlink($save_path);
  }      
    

  if (move_uploaded_file($up['tmp_name'], $save_path))	{	
    $this->v_errm[] = "Файл <b>{$savename}</b> успешно загружен.";
    $this->add2roaster('pdf_size', $up['size']);  
    $this->add2roaster('pdf_exists', 1);  
  }
  else {    
    $this->v_errm[] = "Не удалось переместить файл из временной папки: {$up['tmp_name']} &rarr; {$save_path}";
    return $d_ret;
  }
 
  return $savename;
}

function v_active($val) {  
 
  return $val;
}

function v_status_id($val) {
  
  $val = intval($val);
    
  if (($this->d['active']==1 || $this->p['active']==1) && $val==0) {
    $val = 1;
    $this->v_errm[] = "Номер помечен как активный, текущий статус автоматически изменен на <b>опубликован</b>.";
  }
      
  return $val;
}

//upload photo
function v_photo($val) {
  global $sv, $std, $db;
  
  if ($this->v_err) return false;
  $err = 0;

  $name = "photo";

  $c_file = $this->bloguser[$name];
  $dir = $this->photo_dir;
  
  if (!$err) {    
    $file = $std->file->check_upload($name, $this->ext_ar, $dir, 0);   
   
    if ($file===false) {
      // не указан
      return $c_file;
    }
    $err = ($file['err']) ? true : $err;
    $this->v_errm = array_merge($this->v_errm, $file['errm']);
  }  
  
  
  if (!$err) {   
    // удаляем старый если был
    $r = $this->remove_files($c_file);
    $err = ($r['err']) ? 1 : $err;
    $this->v_errm = array_merge($this->v_errm, $r['errm']);
  }
  
  if (!$err) {  
    if (move_uploaded_file($file['tmp_name'], $file['savepath']))	{	
    
      $r = $this->resize_front($file['savename']);
      $this->v_errm = array_merge($this->v_errm, $r['errm']);
      /*
      $img = getimagesize($file['savepath']);      
      $this->add2roaster('w', $img[0]);      
      $this->add2roaster('h', $img[1]);      
      */
      
      $this->v_errm[] = "Файл успешно загружен.";  
    }
    else {
      //$this->v_err = true;
      $this->v_errm[] = "Не удалось переместить файл из временной папки: {$file['tmp_name']} &rarr; {$file['savepath']}";     
      return false;
    }
  }
  else {
    $errm[] = "Ошибка работы с файлами сообщите администратору.";
  }
 
  return $file['savename'];

}

function parse_photo_fn($file) {
  
  $d = array();
  if ($file=='') {
    $d['o'] = '';
    $d['s'] = '';
    $d['m'] = '';    
  }
  else {
    $d['o'] = $file;
    $d['s'] = "s_{$file}";
    $d['m'] = "m_{$file}";
  }
  
  return $d;
}

function remove_files($file) {
  
  $err = false;
  $errm = array();
  
  if ($file=='') {    
    return array('err' => $err, 'errm' => $errm);
  }
  
  $dir = $this->photo_dir;
  
  
  $original = $dir.$file;
  if (!file_exists($original)) {
    $errm[] = "Оригинал фото не найден: {$original}";
  }
  else {
    if (!unlink($original)) {
      $err = 1;
      $errm[] = "Не удалось удалить оригинал фото: {$original}";
    }
  }
  /*
  $s = $p_dir.$fn['s'];
  if (!file_exists($s)) {
    $errm[] = "Малый предпросмотр фото не найден: {$s}";
  }
  else {
    if (!unlink($s)) {
      $err = 1;
      $errm[] = "Не удалось удалить малый предпросмотр фото: {$s}";
    }
  }

  $m = $p_dir.$fn['m'];
  if (!file_exists($m)) {
    $errm[] = "Средний предпросмотр фото не найден: {$m}";
  }
  else {
    if (!unlink($m)) {
      $err = 1;
      $errm[] = "Не удалось удалить средний предпросмотр фото: {$m}";
    }
  }
  */
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  
  return $ret;
}

function create_files($file) {
  global $std, $db, $sv;
  
  $err = false;
  $errm = array();
  
  $src = $this->photo_dir.$file;
  if (!file_exists($src)) {
    $err = 1;
    $errm[] = "Исходный файл для ресайза не найден: {$src}";    
  }
  else {
    $fn = $this->parse_photo_fn($file);
  }
  
  
  if (!$err) {
    $std->resize->verbose = 0;
    // create S  
    $target = $this->photo_p_dir.$fn['s'];
    if (!$std->resize->auto_fixed($src, $target, 50, 50)) {
      $errm[] = "Не удалось создать малый предпросмотр: <b>{$target}</b>, сообщите администратору.";
      $errm = array_merge($errm, $std->resize->last_session);
    }
    
    // create M
    $target = $this->photo_p_dir.$fn['m'];
    if (!$std->resize->auto_by_width($src, $target, 200)) {
      $errm[] = "Не удалось создать средний предпросмотр: <b>{$target}</b>, сообщите администратору.";
      $errm = array_merge($errm, $std->resize->last_session);
    }
    
  }
  
  
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  return $ret;
}

function resize_front($file) {
  global $std, $db, $sv;
  
  $err = false;
  $errm = array();
  
  $src = $this->photo_dir.$file;
  if (!file_exists($src)) {
    $err = 1;
    $errm[] = "Исходный файл для ресайза не найден: {$src}";    
  }
  else {
    $fn = $this->parse_photo_fn($file);
  }
  
  
  if (!$err) {
    $std->resize->verbose = 0;
    /*
    // create S  
    $target = $this->photo_p_dir.$fn['s'];
    if (!$std->resize->auto_fixed($src, $target, 50, 50)) {
      $errm[] = "Не удалось создать малый предпросмотр: <b>{$target}</b>, сообщите администратору.";
      $errm = array_merge($errm, $std->resize->last_session);
    }
    */
    
    // create M
    
    if (!$std->resize->auto_by_width($src, $src, $this->front_width)) {
      $errm[] = "Не удалось подогнать размер файла: <b>{$target}</b>, сообщите администратору.";
      $errm = array_merge($errm, $std->resize->last_session);
    }
    
  }
  
  
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  return $ret;
}


//callbacks
function vcb_pdf_file_view($val) {
  global $db;
  
  
  $fn = $this->d['pdf_file'];
  $path = $this->pdf_dir.$fn;
  
  $checkbox = "<div style='padding: 5px 0 0 0;'><input type='checkbox' name='new[remove_pdf]'>&nbsp;удалить</div>";
  
  if ($fn=='') {
    $ret = "отсутствует";
    $exists = 0;
  }
  elseif (file_exists($path)) {
    $exists = 1;
    $url = $this->pdf_url.$fn;
    $ret = "<a href='{$url}' target=_blank>{$fn}</a>{$checkbox}";
  }
  else {
    $exists = 0;
    $ret = ($fn=='') ? "не загружен" : "{$fn} - файл не найден{$checkbox}";
  }
  
  if (($this->d['pdf_exists'] && !$exists) || (!$this->d['pdf_exists'] && $exists)) {
    $db->q("UPDATE {$this->t} SET pdf_exists='{$exists}' WHERE id='{$this->current_record}'", __FILE__, __LINE__);
  }
  
  return $ret;
}

function vcb_photo_view($val) {
  global $db;
  
  
  $fn = $this->d['photo'];
  $path = $this->photo_dir.$fn;

  $checkbox = "<div style='padding: 5px 0 0 0;'><input type='checkbox' name='new[remove_file]'>&nbsp;удалить</div>";
  
  if ($fn=='') {
    $ret = "отсутствует";
    $exists = 0;
  }
  elseif (file_exists($path)) {
    $exists = 1;
    $url = $this->photo_url.$fn;
    $ret = "<a href='{$url}' target=_blank>{$fn}</a>{$checkbox}";
  }
  else {
    $exists = 0;
    $ret = ($fn=='') ? "не загружен" : "{$fn} - файл не найден{$checkbox}";
  }
  
  
  return $ret;
}

function df_photo_view($val) {
  
  $ret = ($val!='') ? "<a href='{$this->photo_url}{$val}' target=_blank>{$val}</a>" : "<span style='color:red;'>нет файлов</span>";
  return $ret;
}




// pre post actions
function before_update() {
  global $sv, $db;
  
  if (isset($this->n['remove_pdf']) && $this->n['remove_pdf']=='on' && $this->d['pdf_file']!='') {
    $path = $this->pdf_dir.$this->d['pdf_file'];
    if (file_exists($path)) {
      unlink($path);
      $this->v_errm[] = "Удаляю {$path}";
    }
    else {
      $this->v_errm[] = "Поле PDF файл очищено.";
    }
    $db->q("UPDATE {$this->t} SET pdf_exists='0', pdf_file='', pdf_size='0' WHERE id='{$this->current_record}'", __FILE__, __LINE__);
    $this->d['pdf_file'] = '';
    $this->d['pdf_exists'] = 0;
  }
  
  
  $file = $this->d['photo'];  
  if (isset($this->n['remove_file']) && $this->n['remove_file']=='on' && $file!='') {
    $r = $this->remove_files($file);    
    $db->q("UPDATE {$this->t} SET photo='' WHERE id='{$this->current_record}'", __FILE__, __LINE__);
    $this->d['photo'] = '';
    
    $this->v_errm = array_merge($this->v_errm, $r['errm']);
  }
  
  
 
  
}
function before_edit() {
  if ($this->d['active']==1) {    
    $this->remove_from_roaster('active');
  }
}
function after_update($d, $p, $err) {
global $db, $sv;

  if (!$err && $p['active']==1) {  
    $db->q("UPDATE {$this->t} SET `active`='0' WHERE id<>'$this->current_record'", __FILE__, __LINE__);  
    
    //refreshing near_flag in articles
    
    $next_id = $this->near_id($this->current_record, 1);
    $prev_id = $this->near_id($this->current_record, 0);
    
    $db->q("UPDATE {$sv->t['articles']} SET near_flag='0' WHERE near_flag<>'0'", __FILE__, __LINE__);
    $db->q("UPDATE {$sv->t['articles']} SET near_flag='1' WHERE nomer_id='{$next_id}'", __FILE__, __LINE__);
    $db->q("UPDATE {$sv->t['articles']} SET near_flag='2' WHERE nomer_id='{$prev_id}'", __FILE__, __LINE__);
    
    
  }

}



function near_id($id, $dir = 1) {
  global $db;
  
  $id = intval($id);  
 
  $db->q("SELECT nomer2 FROM {$this->t} WHERE id='{$id}'", __FILE__, __LINE__);
  if ($db->nr()>0) {
    $d = $db->f();
    $nomer2 = intval($d['nomer2']);
  }
  else {
    return 0;
  }
  

  $sql = ($dir == 1) ?  "`nomer2`>'{$nomer2}' ORDER BY `nomer2` ASC, `id` ASC" :  "`nomer2`<'{$nomer2}' ORDER BY `nomer2` DESC, `id` DESC"; 
              
  $q = "SELECT id FROM {$this->t} WHERE {$sql} LIMIT 0,1";
  $db->q($q, __FILE__, __LINE__);
  if ($db->nr()>0) {      
    $f = $db->f();     
    $ret = $f['id'];
  }
  else {
    $ret = 0;
  }    
  return $ret;
}

function parse($d) {
  global $std;
  
  $d['pdf_url'] = $this->pdf_url;
  $d['pdf_size'] = (!isset($d['pdf_size'])) ? 0 : $d['pdf_size'];
  $d['pdf_act_size'] = $std->act_size($d['pdf_size']);
  $d['f_date'] = $std->time->format_date($d['date']);
  $d = $this->parse_photo($d);
  
  return $d;
}


function parse_photo($d) {
  global $std; 
 
  // photo

  
  if ($d['photo']!='')  {    
    
    $url = $this->photo_url;
    $dir = $this->photo_dir;    
    $d['img_url'] = $url.$d['photo'];
    $d['img_path'] = $dir.$d['photo'];     
    $d['img'] = "<img src='{$d['img_url']}' border='0' width='175'>";
    
  }
  else {
    $d['img_url'] = "";
    $d['img_path'] = "";
    $d['img'] = "";
  }

  return $d; 
}



 
function get_next($d) {
  global $sv, $std, $db;
  
  $ar = array('next' => "`date`>'{$d['date']}' ORDER BY `date` ASC", 
              'prev' => "`date`<'{$d['date']}' ORDER BY `date` DESC");
              
  foreach ($ar as $k=>$sql) {
    $q = "SELECT * FROM {$this->t} WHERE status_id='1' AND `count`>'0' AND ".$sql." LIMIT 0,1";
    $db->q($q, __FILE__, __LINE__);
    if ($db->nr()>0) {      
      $f = $db->f();
      $f = $this->parse($f);
      $ret[$k] = $f;
    }
    else {
      $ret[$k] = false;
    }    
  }  
  
  return $ret;
}


function get_prev($d) {
  global $sv, $std, $db;
  
  $sql = "`date`<'{$d['date']}' ORDER BY `date` DESC";
              
  $q = "SELECT id, nomer, nomer2, date FROM {$this->t} WHERE status_id='1' AND `count`>'0' AND ".$sql." LIMIT 0,1";

  $db->q($q, __FILE__, __LINE__);
  if ($db->nr()>0) {      
    $f = $db->f();
    $f = $this->parse($f);
    $ret = $f;
  }
  else {
    $ret = false;
  }    
  
  
  return $ret;
}

function last_list($lim) {
  global $sv, $std, $db;
  
  $lim = intval($lim);
  $ar = array();
  
  $q = "SELECT * FROM {$this->t} WHERE status_id='1' AND `count`>'0' ORDER BY date DESC LIMIT 0,{$lim}";
  $db->q($q, __FILE__, __LINE__);
  while($d = $db->f()) {
    $d = $this->parse($d);
    $ar[] = $d;
  }
  
  $ret['list'] = $ar;
  $ret['count'] = count($ar);
  return $ret;
}

function init_date($now=0) {
  global $sv, $std;
  
  $dt = (isset($sv->_get['date']) 
          && preg_match("#^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2})$#msi", $sv->_get['date'], $m)) 
        ? array('y' => $m[1], 'm' => $m[2], 'd' => $m[3], 'date'=>$m[0]) : null;
  
  if (!is_null($dt) && checkdate($dt['m'], $dt['d'], $dt['y'])) {
    $this->time = mktime(0,0,0, $dt['m'], $dt['d'], $dt['y']);    
    $this->date = date("Y-m-d", $this->time);
    return true;
  }
  else{
    if ($now) {
      $this->d = $sv->parsed['nomer'];
      $this->date = $this->d['date'];
      $this->time = $std->time->date2time($this->date);
    }
    else {
      return false;
    }
  }
}

function init_by_date($date = "", $cache=1) {
  global $sv, $std, $db;
  
  
  if (isset($this->d['date']) && $cache && $this->d['date']==$date) {
    return $this->d;
  }

  $date = addslashes($date);
  
  $db->q("SELECT *, UNIX_TIMESTAMP(`date`) as time 
          FROM {$this->t} WHERE `date`='{$date}'", __FILE__, __LINE__);
  if ($db->nr()<=0) {
    return false;
  }
  
  $nomer = $db->f();
  $nomer = $this->parse($nomer);
  return $nomer;  
}


function init_active_nomer() {
  global $sv, $std, $db;
  
  
  $db->q("SELECT *, UNIX_TIMESTAMP(`date`) as time FROM {$this->t} WHERE `active`='1'", __FILE__, __LINE__);
  if ($db->nr()>0) {
    $d = $db->f();  
    $d['f_date'] = $std->time->format($d['time'], 1);
    $d['dow'] = $std->time->dntorus(date("w", $d['time']), 1);
  }
  else {
    $d = false;
  }
  
  $sv->parsed['nomer'] = $d;
  $sv->vars['c_nomer'] = $d['id'];
  
}


function parse_format($fn) {
  
  $ext = (preg_match("#\.([^\.]+)$#msi", $fn, $m)) ? $m[1] : "xxx";  
  $ret['format'] = ($ext=="xxx") ? "*" : strtoupper($ext);
  $ret['ext'] = $ext;  
  $ret['icon_url'] = "/i/{$ext}.gif";
  $ret['icon'] = "<img src='{$ret['icon_url']}' width=16 height=16 alt='{$ext}' border='0'>";
  
  return $ret;
}
//eoc
}  
  
?>