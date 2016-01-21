<?php

/*

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `slug` varchar(255) default NULL,
  `username` varchar(255) default NULL,
  `sex` tinyint(1) NOT NULL default '0',
  `photo` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `r_reason` text,
  `approved` tinyint(1) NOT NULL default '0',
  `login` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `sid` varchar(255) NOT NULL,
  `last_ip` varchar(255) NOT NULL,
  `last_visit` datetime default NULL,
  `last_agent` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  `last_update_time` int(11) NOT NULL default '0',
  `last_update_post` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `slug` (`slug`,`sid`),
  KEY `last_update_time` (`last_update_time`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=cp1251 AUTO_INCREMENT=10 ;

*/

class m_blog extends class_model {
   
  var $per_page = 5;
  var $uid = 0; // current user
  

  var $url = "http://norilsk-zv.ru/blog/";
  
  var $user = false;
  var $bloguser = false;
  var $post = false;
  var $bu_act = "";
  var $bu_code = "";
  var $bu_id = 0;
  
  var $bu_acts = array('default', 'post', 'options', 'view', 'tag', 'rss');
  
  var $author_mode = 0;
  
  //const
  var $blog_page = 16;  
  var $user_page = 18;
  
  var $sex_ar = array(
    0 => "не определен",
    1 => "мужской",
    2 => "женский"
  
  );
  
  var $photo_dir = "uploads/blogs/";
  var $photo_url = "uploads/blogs/";
  var $photo_p_dir = "uploads/blogs/p/";
  var $photo_p_url = "uploads/blogs/p/";
  
  var $ext_ar = array(
    'jpg', 'jpeg', 'png', 'gif'
  );
  
  // need to update
  var $last_update = array();
  
function __construct() {
  global $sv, $std;  
  
  $this->t = $sv->t['blogs'];
  $std->mail->from_name = "Заполярный вестник";
  $std->mail->from_address = "blog@norilsk-zv.ru";
  
  
  $this->photo_dir = PUBLIC_DIR.$this->photo_dir;
  $this->photo_url = PUBLIC_URL.$this->photo_url;

  $this->photo_p_dir = PUBLIC_DIR.$this->photo_p_dir;
  $this->photo_p_url = PUBLIC_URL.$this->photo_p_url;

  
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название',
  'type' => 'varchar',
  'size' => '255',
  'len' => '50',
  'default' => '',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit', 'registration'),
  'unique' => 1  
  ));  

  $this->init_field(array(
  'name' => 'slug',
  'title' => 'Адрес блога',
  'type' => 'varchar',
  'size' => '255',
  'len' => '50',
  'default' => '',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit', 'registration'),
  'unique' => 1  
  ));     
  
  
  $this->init_field(array(
  'name' => 'username',
  'title' => 'Отображаемое имя автора',
  'type' => 'varchar',
  'len' => '50',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit', 'registration')
  ));  

  $this->init_field(array(
  'name' => 'sex',
  'title' => 'Пол',
  'type' => 'tinyint',
  'input' => 'select',
  'belongs_to' => array('list' => $this->sex_ar),
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));  

  $this->init_field(array(
  'name' => 'photo',
  'title' => 'Фотография',
  'type' => 'file',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));  
  
  $this->init_field(array(
  'name' => 'photo_view',
  'title' => 'Препросмотр файла',
  'show_in' => array('default', 'edit'),
  'virtual' => 'photo'
  ));      

  
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Биография',
  'type' => 'text',
  'len' => 60,
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));  

  

   
  
  
  $this->init_field(array(
  'name' => 'r_reason',
  'title' => 'Цели регистрации',
  'type' => 'text',
  'len' => 70,
  'show_in' => array('remove'),
  'write_in' => array('edit', 'registration')
  ));  

  $this->init_field(array(
  'name' => 'approved',
  'title' => 'Подтвержден?',
  'type' => 'boolean',
  'default' => '0',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));  


  
   
  //auth fields   
  $this->init_field(array(
  'name' => 'login',
  'title' => 'Логин',
  'type' => 'varchar',
  'size' => '255',
  'len' => '50',
  'default' => '',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit', 'registration')
  ));  
  
  $this->init_field(array(
  'name' => 'pass',
  'title' => 'Пароль',
  'type' => 'varchar',
  'size' => '255',
  'len' => '50',
  'default' => '',
  'input' => 'password',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit', 'registration')
  ));  
  
  $this->init_field(array(
  'name' => 'email',
  'title' => 'Email',
  'type' => 'varchar',
  'size' => '255',
  'len' => '50',
  'default' => '',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit', 'registration')
  ));  
    
  
  
  $this->init_field(array(
  'name' => 'sid',
  'title' => 'Идентификатор сессии',
  'type' => 'varchar',
  'size' => '255',
  'len' => '50',
  'default' => '',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));  
  
  $this->init_field(array(
  'name' => 'last_ip',
  'title' => 'Последний айпишник',
  'type' => 'varchar',
  'size' => '255',
  'len' => '50',
  'default' => '',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));  

  $this->init_field(array(
  'name' => 'last_agent',
  'title' => 'Броузер',
  'type' => 'varchar',
  'size' => '255',
  'len' => '100',
  'default' => '',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));  
    
  $this->init_field(array(
  'name' => 'last_visit',
  'title' => 'last_visit',
  'type' => 'datetime',
  'setcurrent' => 1,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));  

  
 $this->init_field(array(
  'name' => 'show_on_main',
  'title' => 'Показывать на главной',
  'type' => 'boolean',
  'default' => 1,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));  
    
  
}

//validations
function v_title($t) {
  
  $t = trim($t);
  if ($t=='') {
    $this->v_err = true;
    $this->v_errm[] = "Название блога не указано.";
  }
  
  return $t;
}

function v_slug($t) {
  global $std;
  
  $t = trim($t);
  $t = $std->text->translit($t);
  $t = strtolower($t);
  
  $t = preg_replace("#[^a-z0-9\_\-]#msi", "", $t);
  
  if ($t=='') {
    $this->v_err = 1;
    $this->v_errm[] = "Адрес блога не указан.";
  }
  
  return $t;
}

function v_username($t) {
  
  $t = trim($t);
  if ($t=='') {
    $this->v_err = 1;
    $this->v_errm[] = "Вы не указали свое имя.";
  }
  
  return $t;
}

function v_r_reason($t) {
  
  $t = trim($t);
  if ($t=='') {
    $this->v_err = 1;
    $this->v_errm[] = "Не указана цель регистрации.";
  }
  
  return $t;
}

function v_login($t) {
  
  $t = trim($t);
  if ($t=='') {
    $this->v_err = 1;
    $this->v_errm[] = "Не указан логин для входа";
  }
  
  return $t;
}

function v_pass($t) {
  global $std;

  $t = $std->text->cut($this->n['pass'], 'allow', 'mstrip');     
  if (($this->code=='registration' || $this->code=='recovery') && strlen($t)<6) {
    $this->v_err = 1;
    $this->v_errm[] = "Пароль короче 6 символов.";
    $ret = "";
  }
      
  if ($t=='') {
    $this->v_errm[] = "Пароль не изменен.";
    $ret = "";
  }
  else {

    $t = $std->text->password_hash($t);
    if ($this->code!='registration') {
      $this->v_errm[] = "Установлен новый пароль.";
    }
    $ret = $t;
  }
  
  return $ret;
}

function v_email($t) {
  global $std;
  
  $t = trim($t);
  if ($t=='') {
    $this->v_err = 1;
    $this->v_errm[] = "Не указан email адрес.";
    return "";
  }
  if (!$std->text->v_email($t)) {
    $this->v_err = 1;
    $this->v_errm[] = "Неправильный формат email адреса.";
  }
  
  return $t;
}

function v_sex($val) {
  
  $val = intval($val);
  $keys = array_keys($this->sex_ar);
  if (!in_array($val, $keys) || $val==0) {
    $ret = 0;
    $this->v_errm[] = "Пол не указан.";
  }
  else {
    $ret = $val;
  }
  
  return $ret;  
}

function v_text($t) {
  global $std;
  
  $t = $std->text->cut($t, 'cut', 'allow');
  $t = trim($t);
  
  if ($t=='') {
    //$this->v_err = 1;
    $this->v_errm[] = "Не указан текст биографии.";
  }
  
  return $t;
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
    
      $r = $this->create_files($file['savename']);
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


//pre post eactions

function after_update($d, $p, $err) {
  global $sv, $std, $db;
  
  $errm = array();
  
  $approved = ($err) ? $d['approved'] : $p['approved'];
  
  $this->sync_addr($d['id'], $approved);

  if (!$err && $d['approved']!=$p['approved']) {
    $act = ($p['approved']==1) ? "подтвердил регистрацию" : "заблокировал регистрацию";
    
	    // write to history
	    $sv->load_model('history');
	    $sv->m['history']->write(
	    "Администратор <i>{$act}</i> блога <b>{$this->d['username']}</b>.", "update", $this->d['id']);
    
	    
	    
	  if ($p['approved']==1 && $std->text->v_email($p['email'])) {	    
	    $date = $std->time->format($sv->post_time, 0.5);
$text = "
Ваш блог \"{$p['title']}\" на сайте \"Заполярный вестник\" (http://norilsk-zv.ru/blog/) был проверен
администратором и подтвержден. Теперь вы можете полноценно размещать публикации и заметки от своего имени.

С уважением, 
администрация сайта \"Заполярный вестник\"
{$date}
";
      if ($this->mail2user($p['email'], "Ваш блог на сайте \"Заполярный вестник\" подтвержден.", $text)) {
	      $errm[] = "Уведомление отправлено на <b>{$p['email']}</b>";
      }
	  }	    
  }
  $ret['errm'] = $errm;
  return $ret;
}

function after_remove($d, $err) {
  global $sv;
  
  if (!$err) {
      
	    // write to history
	    $sv->load_model('history');
	    $sv->m['history']->write(
	    "Администратор удалил блог <b>{$this->d['title']}</b>.", "remove", $d['id']);    
    
  }
  
  
}

function garbage_collector($d) {  
  global $sv, $db;
  
  $err = false;
  $errm = array();  
  
  if ($d['photo']!='') {
    $r = $this->remove_files($d['photo']);
    //$err = ($r['err']) ? 1 : $err;
    $errm = array_merge($errm, $r['errm']);
  }

  //removing url
  if ($d['slug']!='') {
    $url = addslashes("/blog/{$d['slug']}");
    $db->q("DELETE FROM {$sv->t['urls']} WHERE `url`='{$url}'", __FILE__, __LINE__);
  }
  
  
  //removing posts
  $sv->load_model('post');
  $posts = $sv->m['post']->item_list("`uid`='{$d['id']}'", "", 0);
  
  
  foreach($posts['list'] as $d) {
    $r = $sv->m['post']->esc_remove($err, $errm, array(), $d);
    $err = $r['err'];
    $errm = $r['errm'];
  }
  
  return array('err'=>$err, 'errm'=>$errm);
}

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


// special validations
function is_login_exists($val) {
  global $db, $std;
  
  $eng_name = addslashes($std->text->str2eng($val));
  $rus_name = addslashes($std->text->str2rus($val));
	  
  $val = addslashes($val); 
	  
	$db->q("SELECT id FROM {$this->t} 
	 WHERE login='{$val}' OR login='{$eng_name}' OR login='{$rus_name}'", __FILE__, __LINE__);
	
	if ($db->nr()>0)	{
		return true;
	}
	else {
	  return false;
	}  
}

function is_username_exists($val) {
  global $db, $std;
  
  $eng_name = addslashes($std->text->str2eng($val));
  $rus_name = addslashes($std->text->str2rus($val));
	  
  $val = addslashes($val); 
	  
	$db->q("SELECT id FROM {$this->t} 
	 WHERE username='{$val}' OR username='{$eng_name}' OR username='{$rus_name}'", __FILE__, __LINE__);
	
	if ($db->nr()>0)	{
		return true;
	}
	else {
	  return false;
	}  
}



//callbacks
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




// Common Controllers =============================================================

/**
 * Default page / start
 *
 */
function c_start() {
  global $sv, $std, $db;

  // posts
  $sv->load_model('post');
  $p = (isset($sv->_get['p'])) ? intval($sv->_get['p']) : 1;
  
  if (isset($sv->_get['tag'])) {
    $tag = $std->text->cut($sv->_get['tag'], 'cut', 'mstrip');    
    $etag = urlencode($tag);
    $stag = addslashes($std->search->escape($tag)); 
    $ret = $sv->m['post']->post_list_pl("p.status_id='1' AND p.tags LIKE (\"%{$stag}%\")", "p.date DESC", $this->per_page, $p, 1, "/blog/?tag={$etag}&p=");    
    $ret['bytag'] = $std->text->cut($tag, 'replace', 'replace');
  }
  else {
    $ret = $sv->m['post']->post_list_pl("p.status_id='1'", "p.date DESC", $this->per_page, $p, 1, "/blog/?p=");
    $ret['bytag'] = false;
  }
  
  $ret = $this->init_blog_blocks($ret);

  $this->recount_last_updates();
  if ($p==1) {
    $this->update_last_times();
  }
  return $ret;
}


/**
 * Register new blog
 *
 * @return unknown
 */
function c_registration() {
  global $sv;
  
  if ($this->user!==false) {
    $sv->view->show_err_page('forbidden');
  }
  $s = $this->init_submit();
  
	    
  $ret = $this->init_blog_blocks($ret);
  $ret['s'] = $s;
  return $ret;
}

function sc_registration($err, $errm, $n) {
  global $std, $sv, $db;

  //common
  foreach ($n as $k=>$v) {
    $n[$k] = trim($std->text->cut($v, 'cut', 'mstrip'));
  }	
  
  // call validations
	if (!$err) {
	  $fs = $this->get_active_fields('write');
	 
	  $v = array();
	  
	  foreach ($fs as $f) {
	  	if (method_exists($this, "v_{$f}")) {
	  	  eval("\$p[\$f] = \$this->v_{$f}(\$n[\$f]);");	  	  
	  	  $v[$f] = $std->text->cut($p[$f], 'replace', 'replace');
	  	}
	  	else {
	  	  die("error: method <b>v_{$f}</b> not exists");
	  	}
	  }
	  $err = ($this->v_err) ? true : $err;
	  $errm = array_merge($errm, $this->v_errm);
	}

	
	if (!$err) {
	  if ($this->is_login_exists($p['login'])) {
	    $err = true;
	    $errm[] = "Логин <b>{$p['login']}</b> уже занят.";
	  }
	}
	
	if (!$err) {
	  if ($this->is_username_exists($p['username'])) {
	    $err = true;
	    $errm[] = "Имя пользователя <b>{$p['username']}</b> уже занято.";
	  }
	}
	
	
	if (!$err) {
	  $t = addslashes($p['title']);
	  $db->q("SELECT * FROM {$this->t} WHERE `title`='{$t}'", __FILE__, __LINE__);
	  if ($db->nr()>0) {
	    $err = 1;
	    $errm[] = "Блог с таким названием <b>{$t}</b> уже существует.";
	  }	  
	}
		
	if (!$err) {
	  $t = addslashes($p['slug']);
	  $db->q("SELECT * FROM {$this->t} WHERE `slug`='{$t}'", __FILE__, __LINE__);
	  if ($db->nr()>0) {
	    $err = 1;
	    $errm[] = "Такой адрес <b>{$t}</b> уже используется.";
	  }	  
	}
	
	
	if (!$err) {
	  $p2 = $std->text->cut($n['pass_confirm'], 'allow', 'mstrip');
	  $p2 = $std->text->password_hash($p2);
	  if ($p['pass']!==$p2) {
	    $err = true;
	    $errm[] = "Введенные пароли не совпадают.";
	  }
	}
	
	if (!$err) {
	  $p['sid'] = $this->sid;
	  $p['last_ip'] = $sv->ip;
	  $p['last_visit'] = $sv->date_time;
	  $p['last_agent'] = $sv->user_agent;
	  
	  //remove other auth
	  $db->q("UPDATE {$this->t} SET sid='' WHERE sid='{$p['sid']}'");
	}	
	
	if (!$err) {
	  $af = $this->insert_row($p);
	  if ($af>0) {
	    $errm[] = "Ваша заявка успешно отправлена, вы сможете создавать заметки - как только администратор проверит и одобрит вашу регистрацию.";
	    $v = array();
	    
	    $iid = $db->insert_id();
	    
      $this->mail2admins("Новый блог на ЗВ [{$sv->date_time}]", 
	    "Требуется проверка новой регистрации блога на сайте {$sv->site_url}.\n".
	    "Название: {$p['title']}\n".
	    "Пользователь: {$p['username']}\n".
	    "Email: {$p['email']}\n\n".
	    "Перейти к проверке: <a href='http://admin.norilsk-zv.ru/index.php?blogs_edit={$iid}'>http://admin.norilsk-zv.ru/index.php?blogs_edit={$iid}</a> (требуется предварительно авторизоваться.)"
	    );

	    // write to history
	    $sv->load_model('history');
	    $sv->m['history']->write("{$p['username']} зарегистрировал(а) новый блог <b>{$p['title']}</b>.
	    Регистрация отправлена на модерацию.", "create", $iid);
	    
	  }	  
	  else {
	    $err = true;
	    $errm[] = "Ошибка базы данных при регистрации, сообщите администратору.";
	  }
	}
	
	$ret['err'] = $err;
	$ret['errm'] = $errm;
	$ret['v'] = $v;
	
	return $ret;
}

/**
 * Password recovery
 *
 * @return unknown
 */
function c_recovery() {
  global $sv;
  
  $sv->load_model('restore');
  $sv->m['restore']->table = 'blogs';
  $sv->m['restore']->activate_url = $this->url.$sv->code."/?key=";

  
  if (isset($sv->_get['key']))  {
    $sv->_post['new']['key'] = $sv->_get['key'];   
    $ret['todo'] = 'activate';
  }
  else {
    $ret['todo'] = 'sendemail';
  }
  
  $s = $this->init_submit();
  
	    
  $ret = $this->init_blog_blocks($ret);
  $ret['s'] = $s;
  return $ret;
}
function sc_recovery($err, $errm, $n) {
  global $std, $sv, $db;

 
  if (isset($n['key'])) {
    $ret['show_form'] = 1;
    
    //activate     
    $key = $std->text->cut($n['key'], 'allow', 'mstrip');
    
    // check key
    $restore = $sv->m['restore']->check_key($key, 1);
    
    $err = ($restore['err']) ? 1 : $err;
    $errm = array_merge($errm, $restore['errm']);
        
    if (!$err) {
      $user = $this->get_item($restore['d']['uid']);
      if ($user===false) { 
        $err = 1;
        $errm[] = "Пользователь не найден в нашей базе данных, 
        возможно он был удален, либо ошибка восстановления, сообщите администратору";
      }
      else {          
        $ret['key'] = $key;
        $ret['user'] = $user;
      }
    }
    if ($err) {
      $ret['show_form'] = 0;
    }
    
    // on submit
    if (!$err && (isset($n['pass']) && isset($n['pass2']))) {
      if ($n['pass']!==$n['pass2']) {
        $err = 1; 
        $errm[] = "Введенные пароли не совпадают.";
      }
      else {
        $n['pass'] = $std->text->cut($n['pass'], 'allow', 'mstrip');
        $r = $this->set_new_password($n['pass'], $user['id']);
        $err = ($r['err']) ? 1 : $err;
        $errm = array_merge($errm, $r['errm']);
        
      }
      
      if (!$err) {
        $r = $sv->m['restore']->set_used_key($key);
        $err = ($r['err']) ? 1 : $err;
        $errm = array_merge($errm, $r['errm']);
      }
      
      if (!$err) {
        $ret['show_form'] = 0;
      }
    }
    
    
    
  }
  else {        
    // send email
      switch($n['use']){
        case 'login':
          $str = $std->text->cut($n['login'], 'allow', 'madd');
          $d = $this->get_item_wh("`login`='{$str}'");
          if ($d===false) { 
            $err = 1;
            $errm[] = "Логин не найден в нашей базе данных.";
          }
        break;
        case 'email':
          $str = $std->text->cut($n['email'], 'allow', 'madd');
          $d = $this->get_item_wh("`email`='{$str}'");
          if ($d===false) { 
            $err = 1;
            $errm[] = "Такой адрес не найден в нашей базе данных.";
          }
        break;
        default:
          $err = 1;
          $errm[] = "Неверно заданы параметры.";
      }
      
    
      if (!$err) {
        $r = $sv->m['restore']->send_key($d['email'], $d['id'], $d['login']);
        $err = ($r['err']) ? 1 : $err;
        $errm = array_merge($errm, $r['errm']);
      }
  }    
 	
	$ret['err'] = $err;
	$ret['errm'] = $errm;
	$ret['v'] = $v;
	
	return $ret;
}


/**
 * Password change
 *
 * @return unknown
 */
function c_changepass() {
  global $sv;

  if (!$this->user) {
    $sv->view->show_err_page('forbidden');
  }
  
  $s = $this->init_submit();
  
	    
  $ret = $this->init_blog_blocks($ret);
  $ret['s'] = $s;
  return $ret;
}
function sc_changepass($err, $errm, $n) {
  global $std, $sv, $db;


    $user = $this->user;
    $ret['show_form'] = 1;
    
    // on submit
    if (!$err && (isset($n['pass']) && isset($n['pass2']))) {
      if ($n['pass']!==$n['pass2']) {
        $err = 1; 
        $errm[] = "Введенные пароли не совпадают.";
      }
      else {
        $n['pass'] = $std->text->cut($n['pass'], 'allow', 'mstrip');
        $r = $this->set_new_password($n['pass'], $user['id']);
        $err = ($r['err']) ? 1 : $err;
        $errm = array_merge($errm, $r['errm']);        
      }
     
      if (!$err) {
        $ret['show_form'] = 0;
      }
    }
    
	$ret['err'] = $err;
	$ret['errm'] = $errm;
	$ret['v'] = $v;
	
	return $ret;
}


/**
 * Login
 */
function c_login() {
  global $sv, $std, $db;
  
  $n = (isset($sv->_post['new'])) ? $sv->_post['new'] : array();
  if (isset($n['login']) && isset($n['pass'])) {
    $login = addslashes($std->text->cut($n['login'], 'allow', 'mstrip'));
    $pass = $std->text->cut($n['pass'], 'allow', 'mstrip');
    $hash = $std->text->password_hash($pass);
    
    $db->q("SELECT * FROM {$this->t} WHERE `login`='{$login}' AND `pass`='{$hash}'", __FILE__, __LINE__);
    if ($db->nr()>0) {
      $d = $db->f();
      
      $this->uid = $d['id'];
      $this->user = $d;      
      
      $db->q("UPDATE {$this->t} SET sid='{$this->sid}' WHERE id='{$d['id']}'", __FILE__, __LINE__); 
      $msg = "Вы вошли как <b>{$d['username']}</b>.";
      header("Location: /blog/");
    }
    else {
      $msg = "Неправильная комбинация логин/пароль.";
    }
  }
  else {
    $msg = "Не заданы параметры.";
  }
  
  die("
  {$msg}
  <br><br>
  <a href='/blog/'>Вернуться на главную</a>
  ");
}


/**
 * Logout
 */
function c_logout() {
  global $sv, $std, $db;
  
  if ($this->uid>0) {
    $db->q("UPDATE {$this->t} SET sid='' WHERE id='{$this->uid}'", __FILE__, __LINE__);
    $this->uid = 0;
  }
  
  header("Location: /blog/"); 
  exit();
}

/**
 * List of blogs
 */
function c_list() {
  global $sv, $std, $db;
  
  $p = (isset($sv->_get['p']))  ? $sv->_get['p'] : 1;
  $ret = $this->item_list_pl("`approved`='1'", "`created_at` DESC", 20, $p, 1, "/blog/list/?p=");
  
   
  $ret['na'] = $this->item_list("`approved`='0'", "`created_at` DESC", 0, 1);


  $ret = $this->init_blog_blocks($ret);
  return $ret;
}

/**
 * History full list
 */
function c_history() {
  global $sv, $std, $db;
  
  $sv->load_model('history');
  
  // $sv->m['history']->per_page
  $p = (isset($sv->_get['p']))  ? $sv->_get['p'] : 1;
  $ret = $sv->m['history']->item_list_pl("", "`date` DESC", $sv->m['history']->per_page, $p, 1, "/blog/history/?p=");
  
  $ret = $this->init_blog_blocks($ret);
  return $ret;
}

/**
 * HELP page
 */
function c_help() {
  global $sv, $std, $db;
 
  $ret = array();
  
  $ret = $this->init_blog_blocks($ret);
  return $ret;
}

function c_rss() {
  global $sv, $std, $db;

  require(LIB_DIR."class_rss2.php");
  header("Content-Type: text/html; charset=utf-8");
  
  $sv->load_model('post');  
  $ar = $sv->m['post']->item_list("`status_id`='1'", "`date` DESC", 20, 1);
  
  $t1 =  mb_convert_encoding('Заполярный вестник', "utf-8", 'cp1251');
  $t2 =  mb_convert_encoding('Новые публикации на блоге "ЗВ"', "utf-8", 'cp1251');
  
  $rss = new class_rss();
  $channel_id = $rss->create_add_channel( array( 'title'       => $t1,
 											  	 'link'        => 'http://norilsk-zv.ru/blog/rss/',
 											     'description' => $t2,
 											     'pubDate'     => $rss->rss_unix_to_rfc( time() ),
 											     'webMaster'   => 'mailbox@icecity.ru (Icecity.ru)' ) );
  
  foreach($ar['list'] as $d) {
    foreach($d as $k=>$v) {
      $d[$k] = mb_convert_encoding($v, "utf-8", 'cp1251');
    }
    $rss->create_add_item( $channel_id, array( 'title'       => $d['title'],
										     'link'        => $d['url'],
 										     'description' => $d['p_text'],
 										     'copyright'     => $d['user_slug'],
 										     'pubDate'	   => $rss->rss_unix_to_rfc( strtotime($d['date']) ) ,
 										     'guid' => $d['id']) );    
    
    
  }
  
  $rss->rss_create_document();

  print $rss->rss_document;

  exit();
    
  
}

// USER CONTROLLERS ==========================================================

/**
 * BlogUser - init and call SubControllers
 * @return unknown
 */
function c_bloguser() {
  global $sv, $std, $db;
  
  $sv->load_model('post');
    
  $this->init_bloguser();
  $sv->view->page['title'] = $this->bloguser['title'];
  
  $r = $this->parse_input();
 
  $this->bu_act = $r['act'];
  $this->bu_code = $r['code'];
  $this->bu_id = $r['id'];
  
  if (!in_array($this->bu_act, $this->bu_acts)) {
    $sv->view->show_err_page('notfound');
  }
  else {
    $c = "bu_".$this->bu_act;
    if (method_exists($this, $c)) {
      $ret = $this->$c();
    }
    else {
      t("Bloguser controller \"{$c}\" not exists.", 3);
      $ret = array();
    }
  }
  
  
  
  return $ret;
}

/**
 * Bloguser Default post list
 *
 * @return unknown
 */
function bu_default() {
  global $sv, $std, $db;
  
//t($this->user);
//t($this->bloguser);

  $wh = ($this->author_mode) ? "" : " AND p.status_id='1'";

  $p = (isset($sv->_get['p'])) ? intval($sv->_get['p']) : 1;
  

  if (isset($sv->_get['tag'])) {
    $tag = $std->text->cut($sv->_get['tag'], 'cut', 'mstrip');    
    $etag = urlencode($tag);
    $stag = addslashes($std->search->escape($tag)); 
    $ret = $sv->m['post']->post_list_pl(
      "p.uid='{$this->bloguser['id']}' {$wh} AND p.tags LIKE (\"%{$stag}%\")",
      "`date` DESC", $this->per_page, $p, 1, $this->bloguser['url']."?default&p=");
    $ret['bytag'] = $std->text->cut($tag, 'replace', 'replace');
  }
  else {
    $ret = $sv->m['post']->post_list_pl("p.uid='{$this->bloguser['id']}' {$wh}", "p.date DESC", $this->per_page, $p, 1, $this->bloguser['url']."?default&p=");
    $ret['bytag'] = false;
  }


  $ret = $this->init_bloguser_blocks($ret);
  
  $this->update_last_time_by_blog($this->bloguser['id']);
  if ($p==1) {
    //$this->update_last_times();
  }
  
  return $ret;
}

/**
 * Post Managemnt / new, edit, remove
 *
 * @return unknown
 */
function bu_post() {
  global $sv, $std, $db;
  
  
  if (!$this->user || ($this->bloguser['id']!==$this->user['id'])) {
    if ($sv->ip!='172.22.255.210') {
    $sv->view->show_err_page('forbidden');
    }
  }
    
  //$sv->vars['js'][] = "../markitup/jquery.markitup.js";
  //$sv->vars['js'][] = "../markitup/sets/default/set.js";
  //$sv->vars['styles'][] = "bbcode-skin.css";
  $sv->load_model('history');  
    
  $codes = array('new', 'edit', 'remove');
  if (!in_array($this->bu_code, $codes)) {
    $sv->view->show_err_page('notfound');
    exit();
  }
  
  $c = "post_".$this->bu_code;
  if (method_exists($this, $c)) {
    $ret = $this->$c();
  }
  else {
    t("Bloguser controller \"{$c}\" not exists.", 3);
    $ret = array();
  }
    
  return $ret;
}

function submit_post($err, $errm, $n) {
  global $sv, $std, $db;
  

  $post = &$sv->m['post'];
  
  $act = "undefined";
  switch($this->bu_code) {
    case 'new':
      $keys = array('title', 'text', 'tags', 'status_id');
      $post->code = 'new';
    break;
    case 'edit':
      $keys = array('title', 'text', 'tags', 'date', 'status_id');
      $post->code = 'edit';
      
    break;
    default:
      $err = 1;
      $errm[] = "Неверно заданы параметры.";
  }
  
  $p = array();
  $vals = array();
  
  if (!$err) {
    // validations
    foreach($keys as $k) {
      $v = "cv_{$k}";
      if (method_exists($sv->m['post'], $v)) {
        $p[$k] = $post->$v($n[$k], $act);
        $vals[$k] = $std->text->cut($p[$k], 'replace', 'replace');
      }
      else {
        die("post&rarr;{$v} not defined.");
      }
    }
    $err = ($post->v_err) ? 1 : $err;
    $errm = array_merge($errm, $post->v_errm);    
  }
  
  if (!$err) {
    $p['uid'] = $this->bloguser['id'];
    
    $p['user_slug'] = $this->bloguser['slug'];
    $p['ip'] = $sv->ip;
    $p['agent'] = $sv->user_agent;
    $p['refer'] = $sv->refer;
    if ($post->code=='new') {
      $p['slug'] = $post->get_next_slug($p['uid']);
      $p['date'] = $sv->date_time;
    }
    
  }

  if (!$err) {
    
    // INSERTING
    if ($post->code=='new') {      
      if ($post->insert_row($p)) {
        $iid = $db->insert_id();
        $errm[] = "Запись успешно создана.";
        
        $post->d = $d = $post->get_item($iid, 1);
        if (!$d) {
          $err = 1;
          $errm[] = "Ошибка! Публикация не найдена {$iid} после успешного создания, сообщите администратору.";
        }
        else {
          $errm[] = "<a href='{$d['view_url']}'>Посмотреть на результат</a>.";
          $errm[] = "<a href='{$d['edit_url']}'>Продолжить редактирование публикации</a>.";
          $errm[] = "<a href='{$this->user['url']}'>Перейти к списку всех записей</a>.";
          $ret['hide_form'] = 1;
        }
        
        if ($p['status_id']==1 && !$err) {
          $sv->m['history']->write(
            "{$this->user['username']} опубликовал(а) заметку <b><a href='{$d['url']}'>{$d['title']}</a></b>.", "publicate", $this->bloguser['id']);
        }
        
        $this->update_last_time_by_blog($this->user['id']);
      
      }
      else {
        $err = 1;
        $errm[] = "Ошибка базы данных, не удалось создать публикацию, сообщите об ошибке администртору.";
      }
    }
    // UPDATING
    else {
      
      if ($post->update_row($p, $post->d['id'])) {
        $errm[] = "Данные сохранены [{$sv->date_time}]";
        
        if ($p['status_id']==1 && $post->d['status_id']<>1) {
          $sv->m['history']->write(
            "{$this->user['username']} опубликовал(а) заметку <b><A href='{$post->d['url']}'>{$p['title']}</a></b>.", "publicate", $this->bloguser['id']);
        }
        elseif($p['status_id']<>1 && $post->d['status_id']==1) {
          $sv->m['history']->write(
            "{$this->user['username']} скрыл(а) публикацию <b>{$p['title']}</b>.", "update", $this->bloguser['id']); 
        }
        $this->update_last_time_by_blog($this->user['id']);
      }
      else {
        $errm[] = "Информация не была изменена [{$sv->date_time}]";
      }
    }
      
  }
  
  
  if (!$err) {    
    $sv->load_model('tag');   
    
    $sv->m['tag']->save_object_tags("blog", $post->d['id'], $p['tags']);
    $sv->m['tag']->save_object_tags("bloguser-{$this->bloguser['id']}", $post->d['id'], $p['tags']);
    
  }
  
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  $ret['v'] = $vals;
  return $ret;
}

function post_new() {
  global $sv, $std, $db;
  
  $this->submit_call = "submit_post";
  $ret['s'] = $this->init_submit();
  
  return $ret;
}

function post_edit() {
  global $sv, $std, $db;
 
  $d = $sv->m['post']->get_item($this->bu_id, 1);  
  if (!$d) {
    $sv->view->show_err_page('notfound');
  }
  else {
    $sv->m['post']->d = $d;
  }

  //attaches 
  $sv->load_model('attach');
  $sv->m['attach']->action_url = $d['edit_url'];
  $ret['attach'] = $sv->m['attach']->init_object('post', $d['id'], $this->user['id']);

  
  if ($sv->m['attach']->code!='iframe')  {
    $this->submit_call = "submit_post";
    $s = $this->init_submit();
  }
  
  if (!$s['submited']) {
    foreach($d as $k=>$v) {
      $s['v'][$k] = $std->text->cut($v, 'replace', 'replace');
    }
  }
  
  $ret['date_box'] = $std->time->datetime_box($s['v']['date'], 1, 'new[date]', 0, 1);
  $ret['s'] = $s;
  $ret['d'] = $d;
  

  
  return $ret;
}

function post_remove() {
  global $sv, $std, $db;
  
  $d = $sv->m['post']->get_item($this->bu_id, 1);  
  if (!$d) {
    $sv->view->show_err_page('notfound');
  }
  else {
    $sv->m['post']->d = $d;
  }
   
  $this->submit_call = "sc_remove_post";
  $s = $this->init_submit();
    
  $ret['s'] = $s;
  $ret['d'] = $d;
  return $ret;
}

function sc_remove_post($err, $errm, $n) {
  global $sv, $std, $db;
  
  $post = &$sv->m['post'];
  
  
  if (!isset($n['approved'])) {
    $err = 1; 
    $errm[] = "Не получено подтверждение на удаление, обратитесь к администратору.";
  }
  
  if (!$err) {    
    if ($post->remove_row($post->d['id'])) {
      $errm[] = "Запись успешно удалена.";    
      $sv->m['history']->write(
        "{$this->user['username']} удалил(а) публикацию <b>{$post->d['title']}</b>.", "remove", $this->bloguser['id']); 
    }
    else {
      $err = 1;
      $errm[]  = "Не удалось удалить запись.";
    }
  }
  
  $errm[] = "<a href='{$this->user['url']}'>Перейти к просмотру других записей</a> &rarr;";
  
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  $ret['v'] = array();
  return $ret;
}

/**
 * Detail post view 
 *
 * @return unknown
 */
function bu_view() {
  global $sv, $std, $db;
  
  $sv->vars['styles'][] = "fcomments.css";
    
  $id = intval($this->bu_id);
  $d = $sv->m['post']->get_item_wh("`slug`='{$id}' AND `uid`='{$this->bloguser['id']}'", 1);
  if ($d===false) {
    $sv->view->show_err_page('notfound');
  }
  
  $ret['d'] = $d;
  $this->post = $d;
  $sv->view->page['title'] = $d['title'];
  
  // комменты   
  $sv->init_class('ipb');
  $sv->ipb->post_callback = "blog_history";
  $sv->ipb->forum_id = 17;
  $sv->ipb->site_object_table = 'posts';    
  $sv->ipb->topic_title = $d['title'];
  $sv->ipb->topic_description = $std->time->format($d['date'], 0.5, 1);
  $sv->ipb->topic_text = "<a href=\"{$d['url']}\">{$d['title']}</a><br><br>".
                         "<hr><br>";
  $ret['ipb'] = $sv->ipb->site_object_cmnts($d);
  
  
  //stats
  $db->q("UPDATE {$sv->t['posts']} 
  SET `views`=`views`+1, `replycount`='{$ret['ipb']['count']}' WHERE id='{$d['id']}'", __FILE__, __LINE__);
   
  $sv->load_model('daystat');
  $r = $sv->m['daystat']->update_stats("views_post", $d['id']);
 
  
  $ret = $this->init_bloguser_blocks($ret);
  return $ret;
}

/**
 * anketa
 *
 * @return unknown
 */
function bu_options() {
  global $sv, $std;
  
  if (!$this->user || ($this->bloguser['id']!==$this->user['id'])) {
    $sv->view->show_err_page('forbidden');
  }
  
  $this->submit_call = "submit_options";
  $s = $this->init_submit();

  $d = $this->bloguser;

  if (!$s['submited']) {
    foreach($d as $k=>$v) {
      $s['v'][$k] = $std->text->cut($v, 'replace', 'replace');
    }
  }
  else {
    //reinit bloguser
    $d = $this->reinit_bloguser();   
  }

  $ret = array();
  $ret['d'] = $d;
  $ret['s'] = $s;

  
  $ret = $this->init_bloguser_blocks($ret);
  return $ret;
}

/**
 * RSS
 *
 */
function bu_rss() {
  global $sv, $std, $db;

  require(LIB_DIR."class_rss2.php");
  
  
  $sv->load_model('post');  
  $ar = $sv->m['post']->item_list("`uid`='{$this->bloguser['id']}' AND `status_id`='1'", "`date` DESC", 20, 1);
  
  $t1 =  mb_convert_encoding($this->bloguser['title'], "utf-8", 'cp1251');
  $t2 =  mb_convert_encoding("(c) {$this->bloguser['username']}", "utf-8", 'cp1251');
    
  $rss = new class_rss();
  $channel_id = $rss->create_add_channel( array( 'title'       => $t1,
 											  	 'link'        => "http://norilsk-zv.ru{$this->bloguser['url']}",
 											     'description' => $t2,
 											     'pubDate'     => $rss->rss_unix_to_rfc( time() ),
 											     'webMaster'   => 'mailbox@icecity.ru (Icecity.ru)' ) );
  
  foreach($ar['list'] as $d) {
    foreach($d as $k=>$v) {
      $d[$k] = mb_convert_encoding($v, "utf-8", 'cp1251');
    }
    $rss->create_add_item( $channel_id, array( 'title'       => $d['title'],
										     'link'        => $d['url'],
 										     'description' => $d['p_text'],
 										     'copyright'     => $d['username'],
 										     'pubDate'	   => $rss->rss_unix_to_rfc( strtotime($d['date']) ) ,
 										     'guid' => $d['id']) );    
    
    
  }
  
  $rss->rss_create_document();

  header("Content-Type: text/html; charset=utf-8");
  print $rss->rss_document;

  exit();
    
    
}


function submit_options($err, $errm, $n) {
  global $sv, $db, $std;   
  
	$p = array();
	$vals = array();


  $keys = array('username', 'title', 'email', 'sex', 'text', 'photo');
  
  if (!$err) {
    // validations
    foreach($keys as $k) {
      $v = "v_{$k}";
      if (method_exists($this, $v)) {
        $p[$k] = $this->$v($n[$k]);
        $vals[$k] = $std->text->cut($p[$k], 'replace', 'replace');
      }
      else {
        die("blog&rarr;{$v} not defined.");
      }
    }
    $err = ($this->v_err) ? 1 : $err;
    $errm = array_merge($errm, $this->v_errm);    
  }
  	
	if (!$err) {
	  $af = $this->update_row($p, $this->bloguser['id']);
	  if ($af) {
	    $errm[] = "Информация обновлена.";
	  }
	  else {
	    $errm[] = "Информация не обновлена.";
	  }
	}
	
  $ret['v'] = $vals;
  $ret['err'] = $err; 
  $ret['errm'] = $errm;
 
  return $ret;
}

// parsers
function parse($d) {
  global $sv, $std;
  
  $d['url'] = "/blog/{$d['slug']}/";
  $d['full_url'] = $sv->site_url."blog/{$d['slug']}/";
  
  $d['f_created_at'] = $std->time->format($d['created_at'], 0.5, 1);
  
  $d['t_last_visit'] = strtotime($d['last_visit']);
  $d['online'] = ($d['t_last_visit'] > $sv->post_time-60*15) ? 1 : 0;
  $d['f_last_visit'] = ($d['online']) 
    ? "<span style='color: gray;'>сейчас на сайте</span>" : $std->time->format($d['last_visit'], 0.5, 1);
   
  $d = $this->parse_photo($d);
 
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
  $d['img_icon'] = "<img src='{$d['icon']}' alt='' width='50' height='50' border='0'>";

  return $d; 
}


//blocks
function block_last_updates($lim = 5) {
  global $sv, $std, $db;
  
  $lim = intval($lim);
  $sv->load_model('post');
  
  $db->q("
    SELECT b.*, p.*
    FROM {$this->t} b
    INNER JOIN {$sv->t['posts']} p ON (b.last_update_post=p.id)
    WHERE b.show_on_main='1'
    ORDER BY b.last_update_time DESC 
    LIMIT 0, {$lim}", __FILE__, __LINE__);
  
  while($d = $db->f()) {
    unset($d['text']);
    $d = $sv->m['post']->parse($d);
    $d = $this->parse_photo($d);
    
    $ar[] = $d;
  }
  
  $ret['list'] = $ar;
  $ret['count'] = count($ar);
  return $ret;
}

//stuff
function init_bloguser() {
  global $sv;

  if (!preg_match("#/blog/([a-z0-9\_\-]+)/?#si", $sv->view->url, $m)) {
    $sv->view->show_err_page('notfound'); exit();
  }
  else {
    $slug = addslashes($m[1]);    
  }

  $bloguser = $this->get_item_wh("`slug`='{$slug}'", 1);
  if (!$bloguser) {
    $sv->view->show_err_page('notfound'); exit();
  }

  if ($this->user!==false && $bloguser['id']===$this->user['id']) {
    $this->author_mode = 1;
  }
  
  $this->bloguser = $bloguser;
  $sv->m['post']->uid = $bloguser['id'];
  
  return true;
}

function reinit_bloguser() {
  
  $id = intval($this->bloguser['id']);
  if ($id>0) {
    $this->bloguser = $this->get_item($id, 1);
  }
  else {
    die("Can't reinit bloguser: {$id}");
  }
  
  return $this->bloguser;
}

function init_blog_blocks($ret) {
  global $sv;

  // history block
  $sv->load_model('history');
  $ret['history'] = $sv->m['history']->item_list("", "`date` DESC, `id` DESC", 5, 1);
  
  //new blogs block
  $ret['new_blogs'] = $this->item_list("approved='1'", "created_at DESC, id DESC", 3);
 
  // tag cloud
  $sv->load_model('cache');
  $ret['cloud'] = $sv->m['cache']->get_data('tag_cloud_blog');
    
  $ret['blog_last_updates'] = $this->block_last_updates(5);
  
  return $ret;
}

function init_bloguser_blocks($ret) {
  global $sv;

  // block history
  $sv->load_model('history');
  $ret['history'] = $sv->m['history']->item_list("", "`date` DESC, `id` DESC", 5, 1);
  
  // tag cloud
  $sv->load_model('cache');
  $ret['cloud'] = $sv->m['cache']->get_data("tag_cloud_bloguser-{$this->bloguser['id']}");
    
  return $ret;
}



function parse_input() {
  global $sv;
  
  $purl = parse_url($sv->view->uri);

  $str = (isset($purl['query'])) ? $purl['query'] : "";
  
  $fstr = (preg_match("|^([A-Za-z0-9\_\-]*)|si", $str, $m)) ? $m[1] : "";
  $ar = explode("_", $fstr);
  $act = @$ar[0];
  $act = ($act=='') ? 'default' : $act;
  $code = @$ar[1];  
  $code = (is_null($code) || $code=='') ? "default" : $code;
  
  $id = (isset($sv->_get[@$ar[0]."_".@$ar[1]])) ? $sv->_get[@$ar[0]."_".@$ar[1]] : 0;
  $id = ($id==0 && isset($sv->_get[@$ar[0]]) ) ? $sv->_get[@$ar[0]] : $id;
  $id = intval($id);
  
  $ret = array(
    'act' => $act, 
    'code' => $code, 
    'id' => $id
  );
  
  return $ret;
}

function init_auth() {
  global $sv, $std, $db;
  
  //start session
  $exp = 60*60*24*360;
  session_set_cookie_params($exp);		
  session_start();
  
  $this->sid = addslashes(session_id());
  
  
  //check auth
  $db->q("SELECT * FROM {$this->t} WHERE sid='{$this->sid}'", __FILE__, __LINE__);
  if ($db->nr()>0) {
    $d = $db->f();
    $this->current_record = $d['id'];
    $this->uid = $d['id'];
    $this->user = $this->parse($d);
    
    //update data 
    $p = array();
    $p['last_agent'] = $sv->user_agent;
    $p['last_ip'] = $sv->ip;
    $p['last_visit'] = $sv->date_time;
    $this->update_row($p, $d['id']);
  }
  
}


function sync_addr($id, $approved = 1) {
  global $db, $sv;
  
  $sv->load_model('url');
  
  $id = intval($id);
  $blog = $this->get_item($id);
  if ($d===false) return false;
  
  $url = "/blog/{$blog['slug']}";
  $eurl = addslashes($url);
  
  $db->q("SELECT * FROM {$sv->t['urls']} WHERE url='{$eurl}'", __FILE__, __LINE__);
  if ($db->nr()>0) {
    //exists
    $d = $db->f();
    
    if ($approved) {
      //nothing, update?
      $p['url'] = $url;
      $p['page'] = $this->user_page;
      $p['title'] = "Блог <b>{$blog['slug']}</b>";
      $sv->m['url']->update_row($p, $d['id']);      
    }
    else {
      $db->q("DELETE FROM {$sv->t['urls']} WHERE id='{$d['id']}'", __FILE__, __LINE__);
    }
  }
  else {
    //new 
    if ($approved) {
    
      $p = array();
      $p['url'] = $url;
      $p['page'] = $this->user_page;
      $p['title'] = "Блог <b>{$blog['slug']}</b>";
      $sv->m['url']->insert_row($p);
    }
    
    
  }
}

function mail2admins($title, $text) {
  global $std, $sv;  
  $sv->load_model('user');
  return $sv->m['user']->mail2admins($title, $text);  
}

function mail2user($email, $title, $text) {
  global $std;  
  return $std->mail->from_default($email, $text, $title);
}

function set_new_password($pass, $uid) {
  global $sv, $std, $db;
  
  $err = 0;
  $errm = array();
  
  $pass = $this->v_pass($pass);
  $r = $this->update_row(array('pass' => $pass), $uid);
  if ($r) {
    $errm[] = "Пароль изменен, можете войти под своим логином и новым паролем.";
  }
  else {
    $errm[] = "Пароль не изменен.";
  }
  
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  
  return $ret;
}

function check_last_update($post_date, $last_time, $uid, $post_id) {
  
  $blog_date = date("Y-m-d H:i:s", $last_time);  // convert time to date
  
 // t("check: $post_date, $blog_date, $uid, $id", 1);
  /*
  if (isset($this->last_update[$d['uid']]) && $last_date==$post_date) {
    return false;
  }
  */
  $need_to_update = 0;
  // если нет других и дата в блоге отличается от даты поста
  if (!isset($this->last_update[$uid]) && $blog_date!=$post_date) {
    $need_to_update = 1;
  }
  // если есть записи в очереди на изменение и проверяемая дата (post_date) той что в очереди
  elseif ( $post_date > $this->last_update[$uid]['post_date'] ) {
    $need_to_update = 1;
  }
            
  if ($need_to_update) {
    $this->last_update[$uid] = array(
    'post_date' => $post_date,
    'blog_date' => $blog_date,
    'post_id' => $post_id
    );  
  }  

}



function update_last_times() {
  global $sv, $std, $db;
  
  /*
  foreach($this->last_update as $id => $d) {
    if ($d['blog_date']==$d['post_date']) continue;

    t("update: ", 1);
    t($d);
    $id = intval($id);
    $p['last_update_time'] = strtotime($d['post_date']); // convert date to time
    $p['last_update_post'] = $d['post_id'];
    $this->update_row($p, $id);
  
  }
  
  */
}

function update_last_time_by_blog($id) {
  global $sv, $std, $db;
  
  $id = intval($id);
  
  $q = "SELECT * FROM {$sv->t['posts']} WHERE uid='{$id}' AND status_id='1' ORDER BY date DESC LIMIT 0,1";
  $db->q($q, __FILE__, __LINE__);
  
  if ($db->nr()>0) {
    $d = $db->f();
    $time = intval(strtotime($d['date']));
    $post_id = intval($d['id']);   
  }
  else {
    $time = 0;
    $post_id = 0;
  }
  
  return $this->update_row(array('last_update_time' => $time, 'last_update_post' => $post_id), $id);  
}

function recount_last_updates($lim = 20) {
  global $sv, $std, $db;

  $lim = intval($lim);
  
  $ar = array();
  $db->q("SELECT id, date, uid, title FROM {$sv->t['posts']} WHERE status_id='1' ORDER BY date DESC  LIMIT 0, {$lim}", __FILE__, __LINE__);
  while($d = $db->f()) {
    if (!isset($ar[$d['uid']])) {
      $ar[$d['uid']] = $d;
    }
  }
  $db->q("UPDATE {$this->t} SET `last_update_time`='00-00-00 00:00:00', `last_update_post`='0'");
  
  foreach($ar as $uid => $d) {
    $uid = intval($uid);
    $time = strtotime($d['date']);
    $post_id = $db->esc($d['id']);
    $db->q("UPDATE {$this->t} SET `last_update_time`='{$time}', `last_update_post`='{$post_id}' WHERE id='{$uid}'");
  }
  
 
  
}
//eoc
}

?>