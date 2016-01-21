<?php

class m_gratter extends class_model {

  var $tables = array(
    'gratters' => "
      `id` bigint(20) NOT NULL auto_increment,
      `title` varchar(255) NOT NULL default '',
      `text` text,
      `author` varchar(255) NOT NULL,
      `date` datetime default NULL,
      `photo` varchar(255) default NULL,
      `size` int(11) NOT NULL default '0',
      `mime` varchar(255) default NULL,
      `w` int(11) NOT NULL default '0',
      `h` int(11) NOT NULL default '0',
      `ip` varchar(255) default NULL,
      `agent` varchar(255) default NULL,
      `refer` varchar(255) default NULL,
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      `views` int(11) NOT NULL default '0',
      `replycount` int(11) NOT NULL default '0',
      `ftopic` int(11) NOT NULL default '0',
      PRIMARY KEY  (`id`)    
    "
  );
    
  var $url = "http://norilsk-zv.ru/gratters/";
  var $email = "zv@nrd.ru";
  
  var $photo_dir = "uploads/gratters/";
  var $photo_url = "uploads/gratters/";
  var $photo_p_dir = "uploads/gratters/p/";
  var $photo_p_url = "uploads/gratters/p/";
  
  var $ext_ar = array(
    'jpg', 'jpeg', 'png', 'gif'
  );
  var $per_page = 20;
   
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['gratters'];

  $this->photo_dir = PUBLIC_DIR.$this->photo_dir;
  $this->photo_url = PUBLIC_URL.$this->photo_url;

  $this->photo_p_dir = PUBLIC_DIR.$this->photo_p_dir;
  $this->photo_p_url = PUBLIC_URL.$this->photo_p_url;
  
    $this->init_field(array(
  'name' => 'date',
  'title' => 'Дата публикации',
  'type' => 'datetime',
  'setcurrent' => 1,
  'show_in' => array('remove', 'default'),  
  'write_in' => array('edit')
  ));      

  
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Кого поздравляем?',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '70',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));    

  $this->init_field(array(
  'name' => 'text',
  'title' => 'Текст поздравления',
  'type' => 'text',   
  'len'  => '70',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit'), 
  ));    

  
  $this->init_field(array(
  'name' => 'author',
  'title' => 'Подпись',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '70',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));    

  $this->init_field(array(
  'name' => 'photo',
  'title' => 'Фотография',
  'type' => 'varchar',
  'input' => 'file',  
  'show_in' => array('remove'),  
  'write_in' => array('create', 'edit')
  ));    
  
  
  $this->init_field(array(
  'name' => 'photo_view',
  'title' => 'Предпросмотр файла',
  'show_in' => array('default', 'edit'),
  'virtual' => 'photo'
  ));    

  $this->init_field(array(
  'name' => 'w',
  'title' => 'Ширина',
  'type' => 'int',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));      
  
  $this->init_field(array(
  'name' => 'h',
  'title' => 'Высота',
  'type' => 'int',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));      
  
  $this->init_field(array(
  'name' => 'size',
  'title' => 'Размер',
  'type' => 'int',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));       

  $this->init_field(array(
  'name' => 'mime',
  'title' => 'MIME',
  'type' => 'varchar',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));       
  

  $this->init_field(array(
  'name' => 'ip',
  'title' => 'IP',
  'type' => 'varchar',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));         
  

  $this->init_field(array(
  'name' => 'agent',
  'title' => 'Agent',
  'type' => 'varchar',
  'show_in' => array( 'remove'),
  'write_in' => array('edit')
  ));         
  

  $this->init_field(array(
  'name' => 'refer',
  'title' => 'Refer',
  'type' => 'varchar',
  'show_in' => array( 'remove'),
  'write_in' => array('edit')
  ));         
  
  
}

/// validations
function v_title($t) {
  global $std;
  
  $t = $std->text->cut($t, 'cut', 'allow');
  $t = trim($t);
  
  if ($t=='') {
    $this->v_err = 1;
    $this->v_errm[] = "Не указано название поздравления, кого поздравляем?";
  }
  
  if (strlen($t)>100) {
    $this->v_err = 1;
    $this->v_errm[] = "Поле \"кого поздравляем\" длиннее чем 100 символов, постарайтесь описать покороче.";
  }
  
  return $t;
}

function v_author($t) {
  global $std;
  
  $t = $std->text->cut($t, 'cut', 'allow');
  $t = trim($t);
  
  if ($t=='') {
    $this->v_err = 1;
    $this->v_errm[] = "Вы не представились, не указана подпись.";
  }
  
  if (strlen($t)>100) {
    $this->v_err = 1;
    $this->v_errm[] = "Поле \"подпись\" длиннее чем 100 символов, постарайтесь описать покороче.";
  }
  
  return $t;
}


function v_text($t) {
  global $std;
  
  $t = $std->text->cut($t, 'cut', 'allow');
  $t = trim($t);
  
  if ($t=='') {
    $this->v_err = 1;
    $this->v_errm[] = "Не указан текст поздравления.";
  }
  
  if (strlen($t)>1000) {
    $this->v_err = 1;
    $this->v_errm[] = "Текст поздравления длиннее чем 1000 символов, постарайтесь описать покороче.";
  }
    
  
  return $t;
}

function last_v($p) {
  global $sv, $std, $db;
  
  if ($this->code=='create' || $this->code=='publicadd') {
    $p['date'] = $sv->date_time;  
    $p['ip'] = $sv->ip;
    $p['agent'] = $sv->user_agent;
    $p['refer'] = $sv->refer;
  }
  
  return $p;
}

//upload photo
function v_photo($val="") {
  global $sv, $std, $db;
  
  if ($this->v_err) return false;
  $err = 0;

  $name = "photo";

  $c_file = (isset($this->d[$name])) ? $this->d[$name] : "";
  $dir = $this->photo_dir;
  
  if (!$err) {    
    $file = $std->file->check_upload($name, $this->ext_ar, $dir, 0);      
    if ($file===false) {      
      // не указан
      //$err = 1;
      $this->v_errm[] = "Не указан файл с фотографией.";
      return $c_file;
    }
    else {
      $err = ($file['err']) ? true : $err;
      $this->v_errm = array_merge($this->v_errm, $file['errm']);
    }
  }  
  
  
  if (!$err) {   
    // удаляем старый если был
    $r = $this->remove_files($c_file);
    $err = ($r['err']) ? 1 : $err;
    $this->v_errm = array_merge($this->v_errm, $r['errm']);
  }
  
  if (!$err) {  
    if (move_uploaded_file($file['tmp_name'], $file['savepath']))	{	
    
      $r = $this->create_files($file['savename']);
      $this->v_errm = array_merge($this->v_errm, $r['errm']);
      
      $img = getimagesize($file['savepath']);      
      $size = filesize($file['savepath']);
      
      $this->add2roaster('w', $img[0]);      
      $this->add2roaster('h', $img[1]);           
      $this->add2roaster('size', $size);     
      $this->add2roaster('mime', $file['mime']);    
      
      
      $this->v_errm[] = "Файл успешно загружен.";  
    }
    else {
      //$this->v_err = true;
      $this->v_errm[] = "Не удалось переместить файл из временной папки: {$file['tmp_name']} &rarr; {$file['savepath']}";     
      $err = 1;
    }
  }
  else {
    $errm[] = "Ошибка работы с файлами сообщите администратору.";
  }
  $this->v_err = $err;
 
  return $file['savename'];

}

// stuff for upload
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
  
  $fn = $this->parse_photo_fn($file);
  
  $dir = $this->photo_dir;
  $p_dir = $this->photo_p_dir;
  
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
    if (!$std->resize->auto_fixed($src, $target, 150, 150)) {
      $errm[] = "Не удалось создать малый предпросмотр: <b>{$target}</b>, сообщите администратору.";
      $errm = array_merge($errm, $std->resize->last_session);
    }
    
    // create M
    $target = $this->photo_p_dir.$fn['m'];
    if (!$std->resize->auto_by_width($src, $target, 500)) {
      $errm[] = "Не удалось создать средний предпросмотр: <b>{$target}</b>, сообщите администратору.";
      $errm = array_merge($errm, $std->resize->last_session);
    }
    
  }
  
  
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  return $ret;
}

// pre / post
function before_update() {
  global $sv, $db;
  
  $file = $this->d['photo'];
  
  if (isset($this->n['remove_file']) && $this->n['remove_file']=='on' && $file!='') {
    $r = $this->remove_files($file);    
    $db->q("UPDATE {$this->t} SET photo='' WHERE id='{$this->current_record}'", __FILE__, __LINE__);
    $this->d['photo'] = '';
    
    $this->v_errm = array_merge($this->v_errm, $r['errm']);
  }
   
}



// callbacks
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

// parsers
function parse($d) {
  global $sv, $std, $db;
  
  
  $d = $this->parse_photo($d);
  $d['f_date'] = $std->time->format($d['date'], 0.5, 1);
  $d['f_date2'] = $std->time->format($d['date'], 0.6, 1);
  $t = strtoupper($d['title']);
  $t = str_replace("ч", "Ч", $t);
  $t = str_replace("я", "Я", $t);
  
  $d['alt'] = "
  <p>".$t."</p>
  <p>".nl2br($d['text'])."</p>
  ";
  $d['alt'] = str_replace("'", "\"", $d['alt']);
  
  
  $d['url'] = $this->url."view/?id={$d['id']}";
  $d['html'] = "{$d['img']['m']}<br><br><b>{$d['title']}</b><br>".nl2br($d['text'])
              ."<div align='right'><i>{$d['author']}, {$d['f_date']}</i></div>";
  
  return $d;
}
function parse_photo($d) {
  global $std; 
 
  // photo
  
  $fn = $this->parse_photo_fn($d['photo']); // return array o s m
  
  $url = $this->photo_url;
  $p_url = $this->photo_p_url;
  
  $dir = $this->photo_dir;
  $p_dir = $this->photo_p_dir;
  
  if ($d['photo']!='')  {    
    
    $d['img_url'] = array(
      'o' => $url.$fn['o'],
      's' => $p_url.$fn['s'],
      'm' => $p_url.$fn['m']
    );
    $d['img_path'] = array(
      'o' => $dir.$fn['o'],
      's' => $p_dir.$fn['s'],
      'm' => $p_dir.$fn['m']
    );
    
    $size = array();
    foreach($d['img_path'] as $k => $path) {
      $s = getimagesize($path);
      $size[$k]['w'] = $s[0];
      $size[$k]['h'] = $s[1];
    }
    
    if ($size['o']['w']<$size['m']['w']) {
      $d['img_url']['m'] = $d['img_url']['o'];
    }
    
  }
  else {
    $d['img_url'] = $fn;
    $d['img_path'] = $fn;
  }
  foreach($d['img_url'] as $k=>$v) {
    $d['img'][$k] = ($v!='') ? "<img src='{$v}' border='0'>" : "";
  }
  
  //icon
  $d['icon'] = ($d['img_url']['s']!='') ? $d['img_url']['s'] : "/i/blog_default.gif";
  $d['img_icon'] = "<img src='{$d['icon']}' alt='' width='150' height='150' border='0'>";

  return $d; 
}




// puclic controllers
function c_publiclist() {
  global $sv, $std, $db;

  //$this->per_page = 2;
  $p = (isset($sv->_get['p'])) ? intval($sv->_get['p']) : 1;
  $ret = $this->item_list_pl("", "`date` DESC", $this->per_page, $p, 1, $this->url."?p=");
  
  return $ret;
}

function c_publicadd() {
  global $sv, $std, $db;
  
  $s = $this->init_submit();

  $ret['s'] = $s;

  return $ret;
}
function sc_publicadd($err, $errm, $n) {
  global $sv, $std, $db;
  
  $vals = array();
  $p = array();

  $keys = array('title', 'text', 'author');
  foreach($keys as $k) {
    $name = "v_{$k}";
    if (!method_exists($this, $name)) {
      die("no validation for <b>{$k}</b>");
    }
    $p[$k] = $this->$name($n[$k]);
    $vals[$k] = $std->text->cut($p[$k], 'replace', 'replace');
  }
  
  $this->validate_unique_many(array('title', 'text'), $p, "Такое поздравление уже есть в базе.");
  
  //photo
  $this->active_fields = array(); 
  $p['photo'] = $this->v_photo();  
  foreach($this->active_fields as $k) { // size, mime, w, h
    $p[$k] = $this->n[$k];
  } 
  
  $p = $this->last_v($p);
  
  
  
  
  
  $err = ($this->v_err) ? 1 : $err;
  $errm = array_merge($errm, $this->v_errm);

  
  if (!$err) {
    $this->insert_row($p);
    $errm[] = "<b>Ваше поздравление успешно <a href='/gratters/'>добавлено</a>.</p>
    <p>Не забудьте теперь отправить копии фотографий на email: <a href='mailto:{$this->email}'>{$this->email}</a> для того чтобы редакция смогла опубликовать ваше поздравление на страницах газеты &laquo;Заполряный вестник&raquo;.</p>";
    $vals = array();
  }
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  $ret['v'] = $vals;
  return $ret;
}

function c_publicview() {
  global $sv, $std, $db;
  
  $id = (isset($sv->_get['id'])) ? intval($sv->_get['id']) : 0;
  $d = $this->get_item($id, 1);
  

  $ret['d'] = $d;

  // комменты   
  $sv->init_class('ipb');
  $sv->ipb->forum_id = 18;
  $sv->ipb->site_object_table = 'gratters';    
  $sv->ipb->topic_title = "".$d['title']." от ".$d['f_date2'];
  $sv->ipb->topic_description = $d['author'];
  $sv->ipb->topic_text = "{$d['html']}<div align='right'>Источник: <a href=\"{$d['url']}\">{$d['url']}</a></div><hr>";        
    
  $ret['ipb'] = $sv->ipb->site_object_cmnts($d);      

  
  $db->q("UPDATE {$sv->t['gratters']} 
  SET `views`=`views`+1, `replycount`='{$ret['ipb']['count']}' WHERE id='{$d['id']}'", __FILE__, __LINE__);
    

  
  
  return $ret;
}

function block() {
  global $sv, $std, $db;
  
  $exp = $sv->post_time - 60*60*24*31;
  $expd = date("Y-m-d H:i:s", $exp);
  $d = $this->get_item_wh("`date`>'{$expd}' ORDER BY RAND() LIMIT 0,1", 1);  
  $ret['d'] = $d;

  return $ret;
}

//eoc
}

?>