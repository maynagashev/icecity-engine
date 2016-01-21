<?php
/*

Revision: 1s for PB



ALTER TABLE `accounts` 
ADD `work` VARCHAR( 255 ) NULL AFTER `country` ,
ADD `workplace` VARCHAR( 255 ) NULL AFTER `work`,
ADD `birthday` DATE NULL AFTER `workplace` ,
ADD `city` VARCHAR( 255 ) NULL AFTER `birthday` ,
ADD `interests` text NULL AFTER `city`,
ADD `text` text NULL AFTER `interests` ,
ADD `posts` INT( 11 ) NOT NULL DEFAULT '0' AFTER `text` ,
ADD `fposts` INT( 11 ) NOT NULL DEFAULT '0' AFTER `posts` ,
ADD INDEX ( posts );

*/

class m_account extends class_model {
  
  var $tables = array(
    'accounts' => "
    `id` int(10) NOT NULL auto_increment,
    `login` varchar(128) NOT NULL default '',
    `password` varchar(128) NOT NULL default '',
    `email` varchar(255) NOT NULL default '',
    `group_id` varchar(128) NOT NULL default '',
    
    `name` varchar(255) NOT NULL default '',
    `surname` varchar(255) default NULL,
    `fathername` varchar(255) default NULL,  
    `avatar` varchar(100) default NULL,
    
    `birthday` date null,
    `sex` tinyint(1) not null default '0',    
    `phone` varchar(255) NOT NULL default '',
    `city` varchar(255) null,
    `country` varchar(255) not null default '',
    `work` varchar(255) null,
    `workplace` varchar(255) null,
    `interests` text null,
    
    `text` text null,
    
    `posts` int(11) not null default '0',
    `fposts` int(11) not null default '0',
    
    `last_ip` varchar(255) default NULL,
    `last_agent` varchar(255) default NULL,
    `last_refer` varchar(255) default NULL,
    `last_request` varchar(255) default NULL,
    `last_visit` datetime default NULL,
    `last_time` int(11) NOT NULL default '0',
    `last_fpost` int(11) not null default '0',
    `time_reg` datetime default NULL,
    `time_req` datetime default NULL,
    `active` tinyint(1) not null default '1',
    
    `created_at` datetime default NULL,
    `created_by` int(11) NOT NULL default '0',
    `updated_at` datetime default NULL,
    `updated_by` int(11) NOT NULL default '0',
    `expires_at` datetime default NULL,
    PRIMARY KEY  (`id`), 
    KEY (`email`),
    KEY (`active`),
    KEY (`posts`)
    "
  
  );
  var $groups = array(
    0 => "Гости",
    1 => "Пользователи",
    2 => "Модераторы",
    3 => "Администраторы"
    
  );
  
  var $status = array(
    'active' => 0,   // 0-1-2
    'all' => 0
  
  );
    
  var $default_group = 1;
  var $admin_group = 3;   
  var $min_pass =4;
  
  var $initialized = false;
  
  var $active_list = array('list' => array(), 'count'=>0);

  var $sex_ar = array(
    0 => '-- не выбран --',
    1 => 'мужской', 
    2 => 'женский'
  );
  
  var $ext_ar = array( 'jpg', 'jpeg', 'gif', 'png'  );  
  var $uploads_make_resize = 1;
  var $uploads_w = 80;
  var $uploads_h = 120;
  var $uploads_resize_type = "by_width";  // fixed | by_width | by_height
  
  var $auth_after_reg = 1;

  var $title = 'Учетные записи';
  var $config_vars = array(
    'avatar_maxw' => array('title' => 'Максимальная ширина аватара', 'type' => 'int', 'value' => 100, 'len' => 20),
    'avatar_maxh' => array('title' => 'Максимальная высота аватара', 'type' => 'int', 'value' => 100, 'len' => 20),
    'avatar_maxs' => array('title' => 'Максимальный вес аватара в КБ', 'type' => 'int', 'value' => 100, 'len' => 20),
    
  );
    
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['accounts'];
  $this->per_page = 50;    
    

  $this->uploads_dir = $_SERVER['DOCUMENT_ROOT']."/uploads/avatars/";
  $this->uploads_url = "/uploads/avatars/";  

  $this->init_field(array(
  'name' => 'login',
  'title' => 'Логин',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '50',
  'show_in' => array('default', 'remove', 'public_profile'),
  'write_in' => array('create', 'edit', 'public_registration'),
  'unique' => 1
  ));    
  
  $this->init_field(array(
  'name' => 'password',
  'title' => 'Пароль',
  'type' => 'varchar',
  'input' => 'password',
  'size' => '255',
  'len' => '30',
  'show_in' => array('remove'),
  'write_in' => array('edit', 'public_registration', 'public_password')
  
  ));  
  
   $this->init_field(array(
  'name' => 'group_id',
  'title' => 'Группа',
  'type' => 'int',
  'input' => 'select',
  'size' => '11',
  'len' => '5',
  'default' => 1,
  'show_in' => array('remove'),
  'write_in' => array('edit'),
  'belongs_to' => array('list'=>$this->groups, 'not_null'=>1)  
  ));  
  
  $this->init_field(array(
  'name' => 'email',
  'title' => 'Email',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit', 'public_registration', 'public_profile')
  ));      
  

    
  $this->init_field(array(
  'name' => 'name',
  'title' => 'Имя',
  'type' => 'varchar',
  'len' => '50',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit', 'public_profile')
  ));    
    
  $this->init_field(array(
  'name' => 'fathername',
  'title' => 'Отчество',
  'type' => 'varchar',
  'len' => '50',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit', 'public_profile')
  ));    
   
  $this->init_field(array(
  'name' => 'surname',
  'title' => 'Фамилия',
  'type' => 'varchar',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit', 'public_profile')
  ));    



  $this->init_field(array(
  'name' => 'avatar',
  'title' => 'Загрузка аватара',
  'type' => 'varchar',
  'input' => 'file',
  'show_in' => array(),
  'write_in' => array('edit', 'public_avatar')
  ));          

  $this->init_field(array(
  'name' => 'avatar_view',
  'title' => 'Аватар',
  'virtual' => 'avatar',
  'show_in' => array('edit'),
  'write_in' => array()
  ));       
  

  $this->init_field(array(
  'name' => 'sex',
  'title' => 'Пол',
  'type' => 'tinyint',
  'input' => 'select',
  'belongs_to' => array('list' => $this->sex_ar),
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit', 'public_profile')
  ));      
  
  $this->init_field(array(
  'name' => 'birthday',
  'title' => 'Дата рождения',
  'type' => 'date',
  'setcurrent' => 1,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create', 'public_profile')
  ));   
    
  $this->init_field(array(
  'name' => 'age',
  'title' => 'Возраст',
  'virtual' => 'birthday',
  'show_in' => array('remove', 'edit'),
  'write_in' => array()
  ));   
    
  $this->init_field(array(
  'name' => 'sign',
  'title' => 'Знак зодиака',
  'virtual' => 'birthday',
  'show_in' => array('remove', 'edit'),
  'write_in' => array()
  ));   
        

  $this->init_field(array(
  'name' => 'phone',
  'title' => 'Контактный телефон',
  'type' => 'varchar',
  'len' => 20,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit', 'public_profile')
  ));    
     

  $this->init_field(array(
  'name' => 'city',
  'title' => 'Город',
  'type' => 'varchar',
  'len' => 30,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit', 'public_profile')
  ));    
       
  $this->init_field(array(
  'name' => 'country',
  'title' => 'Страна',
  'type' => 'varchar',
  'len' => 30,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit', 'public_profile')
  ));    
    
  $this->init_field(array(
  'name' => 'work',
  'title' => 'Профессия',
  'type' => 'varchar',
  'len' => 50,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit', 'public_profile')
  ));  
    
  $this->init_field(array(
  'name' => 'workplace',
  'title' => 'Место работы',
  'type' => 'varchar',
  'len' => 50,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit', 'public_profile')
  ));  
  
  $this->init_field(array(
  'name' => 'interests',
  'title' => 'Интересы',
  'type' => 'text',
  'len' => 50,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit', 'public_profile')
  ));    
    
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Дополнительная информация',
  'type' => 'text',
  'len' => 50,
  'show_in' => array(),
  'write_in' => array('edit', 'public_profile')
  ));    
    
  $this->init_field(array(
  'name' => 'last_visit',
  'title' => 'Дата последнего посещения',
  'type' => 'datetime',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit') 
  ));    
        
  $this->init_field(array(
  'name' => 'last_ip',
  'title' => 'Последний IP',
  'type' => 'varchar',
  'show_in' => array('default', 'edit', 'remove')
  ));    

  $this->init_field(array(
  'name' => 'last_agent',
  'title' => 'Сведения об агенте',
  'type' => 'varchar',
  'show_in' => array('edit', 'remove')
  ));       

  $this->init_field(array(
  'name' => 'last_request',
  'title' => 'Последний адрес',
  'type' => 'varchar',
  'show_in' => array('edit', 'remove')
  ));       
  
  $this->init_field(array(
  'name' => 'last_refer',
  'title' => 'Последний рефер',
  'type' => 'varchar',
  'show_in' => array('edit', 'remove')
  ));       
      
  $this->init_field(array(
  'name' => 'last_fpost',
  'title' => 'Последнее сообщение на форуме',
  'len' => 10,
  'type' => 'int',
  'show_in' => array('edit', 'remove')
  ));    
  
  $this->init_field(array(
  'name' => 'time_reg',
  'title' => 'Дата регистрации',
  'type' => 'datetime',
  'show_in' => array('edit', 'remove')
  ));    
        
 
  $this->init_field(array(
  'name' => 'time_req',
  'title' => 'Дата запроса на активацию',
  'type' => 'datetime',
  'show_in' => array()
  ));    
        
 
  $this->init_field(array(
  'name' => 'posts',
  'title' => 'Сообщений',
  'type' => 'int',
  'len' => 10,
  'show_in' => array('default'),
  'write_in' => array('edit'),
  ));    
          
  $this->init_field(array(
  'name' => 'fposts',
  'title' => 'Сообщений на форуме',
  'type' => 'int',
  'len' => 10,
  'show_in' => array('default'),
  'write_in' => array('edit'),
  ));    
            
   

  $this->init_field(array(
  'name' => 'active',
  'title' => 'Активен',
  'type' => 'boolean',
  'default' => 1,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));       


    
      
}

function parse($d) {
  global $std;
  
  $d['f_group'] = (isset($this->groups[$d['group_id']])) ? $this->groups[$d['group_id']] : $d['group_id'];
  $d['img_avatar'] = $this->img_avatar($d['avatar']);
  $d['f_time_reg'] = (!$d['time_reg']) ? "-" : $std->time->format($d['time_reg'], 0.5, 1);
  $d['f_last_time'] = $std->time->format($d['last_time'], 0.5, 0);
  
  //$d['age'] = $std->time->age($d['birthday']);
  //$d['sign'] = $std->time->sign($d['birthday']);
    
  return $d;
}

// VALIDATIONS

function v_login($val) {  
  $val = trim($val);
  
  if ($val=='') {
    $this->v_err = true;
    $this->v_errm[] = "Не указан логин.";
  }
  elseif (!preg_match("#^[A-Za-z0-9\-\_]{3,20}$#msi", $val)) {
		$this->v_err = true;
		$this->v_errm[] = "Логин может содержать только латинские буквы, цифры, дефис и знак подчеркивания, длина логина от 3 до 20 символов.";
		
	}
  
  return $val;  
}

function v_email($val) {
  global $std;
  
  $val = trim($val);
 
  
  if (!$std->text->v_email($val)) {
    $this->v_err = ($this->code!='edit') ? true : $this->v_err;
    $this->v_errm[] = "Email не соответствует формату.";
    //$val = (isset($this->d['email'])) ? $this->d['email'] : "";    
  }  
  else {
    $email = addslashes($val);
    $d = $this->get_item_wh("`email`='{$email}' AND id<>'{$this->current_record}'");
    if ($d!==false) {
      $this->v_err = 1;
      $this->v_errm[] = "Пользователь с таким Email уже зарегистрирован на сайте.";
    }
  }
  
  return $val;
}

function v_password($val) {
  global $sv;
  
  if ($this->code!='edit') {
    if(strlen($val)<$this->min_pass) {
      $this->v_err = true;
      $this->v_errm[] = "Пароль слишком короткий, требуется минимум {$this->min_pass} символов.";
    }
    
    if ($sv->code=='password') {     
      if ($this->n['password2']!=$val) {
        $this->v_err = 1;
        $this->v_errm[] = "Введенные пароли не совпадают.";
      }      
    }
    
    if (!$this->v_err) {
      $val = $this->password_hash($val);
    }
    
  }
  // admin checks
  else {
    
    if ($val=='') {
      //not update
      $this->v_errm[] = "Пароль не будет изменен.";
      return null;
    }
    else {
     $val = $this->password_hash($val);
     $this->v_errm[] = "Пароль будет изменен...";   
    }
  }
  
  return $val;
}

function v_name($val) {
  global $std;
    
  $val = trim($std->text->cut($val, 'cut', 'cut'));
  if ($val=='') {
    //$this->v_err = true;
    $this->v_errm[] = "Имя не указано.";
  }
  
  return $val;
}

function v_sex($val) {
  $val = intval($val);
  if ($val<0 || $val>2) {
    $val = 0;    
  }
  
  return $val;
}

function v_surname($val) {
  global $std;

  $val = trim($std->text->cut($val, 'cut', 'cut'));
  
  return $val;
}

function v_fathername($val) {
  global $std;
    
  $val = trim($std->text->cut($val, 'cut', 'cut'));
  if ($val=='') {
    //$this->v_err = true;
    //$this->v_errm[] = "Отчество не указано.";
  }  
  return $val;
}

function v_dogovor($val) {
  global $std;
    
  $val = intval(abs($val));
  
  if ($val<=0) {
    $this->v_err = true;
    $this->v_errm[] = "Номер договора не указан."; 
  }
  return $val;
}

function v_avatar() {  
  return $this->ev_file(0);  
}

function v_birthday($date) {
 
  return $date;
}

function v_phone($t) {
  
  $t = trim(preg_replace("#[^0-9\ \-\+\(\)]#si", '', $t));
  
  return $t;
}

function v_city($t) {
  global $std; 
  $t = trim($std->text->cut($t, 'cut', 'cut'));  
  return $t;
}

function v_country($t) {
  global $std; 
  $t = trim($std->text->cut($t, 'cut', 'cut'));  
  return $t;
}

function v_interests($t) {
  global $std; 
  $t = trim($std->text->cut($t, 'cut', 'cut'));  
  return $t;
}

function v_text($t) {
  global $std; 
  $t = trim($std->text->cut($t, 'cut', 'cut'));  
  return $t;
}

function v_work($t) {
  global $std; 
  $t = trim($std->text->cut($t, 'cut', 'cut'));  
  return $t;
}

function v_workplace($t) {
  global $std; 
  $t = trim($std->text->cut($t, 'cut', 'cut'));  
  return $t;
}


function last_v($p) {
  global $sv, $std, $db;
  
  if (preg_match("#registration#si", $this->code)) {    
         	
    $p2 = $this->password_hash($this->n['password_confirm']);
    if ($p['password']!==$p2) {
      $this->v_err = true;
      $this->v_errm[] = "Введенные пароли не совпадают.";
    }
    
    $sv->load_model('antispam');
    if (!$sv->m['antispam']->v_code()) {
      $this->v_err = 1;
      $this->v_errm[] = "Неверно указан антиспам-код, попробуйте еще раз.";
    }
    
    if (!$this->v_err && $this->is_login_exists($p['login'])) {
      $this->v_err = true;
      $this->v_errm[] = "Логин <b>{$p['login']}</b> уже занят.";
    }
    

	  $p['active'] = 1;
	  $p['time_reg'] = $sv->date_time;
	  $p['time_req'] = $sv->date_time;
	  $p['group_id'] = $this->default_group;	 
	  $p['last_ip'] = $sv->ip;
	  $p['last_agent'] = $sv->user_agent;
	  $p['last_request'] = $sv->request_url;
    $p['last_refer'] = $sv->refer;

  }
  
  return $p;
}

// special validations
function is_login_exists($val) {
  global $db;
    
  $rus = array("А", "а", "В", "Е", "е", "К", "М", "Н", "О", "о", "Р", "р", "С", "с", "Т", "у", "Х", "х");
  $eng = array("A", "a", "B", "E", "e", "K", "M", "H", "O", "o", "P", "p", "C", "c", "T", "y", "X", "x");
  $eng_name = $db->esc(str_replace($rus, $eng, $val));
  $rus_name = $db->esc(str_replace($eng, $rus, $val));
	  
  $val = $db->esc($val);
 
	  
	$db->q("SELECT id FROM {$this->t} WHERE login='{$val}' OR login='{$eng_name}' OR login='{$rus_name}'", __FILE__, __LINE__);
	if ($db->nr()>0)	{
		return true;
	}
	else {
	  return false;
	}  
}

// callbacks
function vcb_avatar_view($val) {
  return $this->ev_file_view($this->current_callback);
}

function vcb_login($t) {
  return "<b>{$t}</b>";
}

function vcb_birthday($t) {
  
  //$t = ($this->d['age']) ? "{$this->d['age']['f_time']}"
  return $t;
}

function vcb_age($date) {
  global $std;  
  $d = $std->time->age($date);
  $ret = ($d) ? "{$d['age']} {$d['suf']}" : "";
  return $ret;
}

function vcb_sign($date) {
  global $std;
  
  $d = $std->time->sign($date);
  $ret = ($d) ? "{$d['title']}" : "";
  return $ret;
}

// pre/post actions
function before_update() {
  global $sv, $db;
  
  $this->ev_init_file_remove('avatar');
}

function before_upload_avatar($file) {
  global $sv;
  
  $err = 0;
  $errm = array();
  
  $tmp_name = $file['tmp_name'];
  $d = getimagesize($tmp_name);
  $w = $d[0];
  $h = $d[1];
  $s = number_format(filesize($tmp_name)/1024, 2);
  
  if ($w>$sv->cfg['avatar_maxw']) {
    $err = 1;
    $errm[] = "Ширина аватара <b>{$w}px</b> больше максимально допустимой: {$sv->cfg['avatar_maxw']}px.";
  }
  if ($h>$sv->cfg['avatar_maxh']) {
    $err = 1;
    $errm[] = "Высота аватара <b>{$h}px</b> больше максимально допустимой: {$sv->cfg['avatar_maxh']}px.";
  }
  if ($w>$sv->cfg['avatar_maxw']) {
    $err = 1;
    $errm[] = "Размер файла аватара <b>{$s}KB</b> больше максимально допустимого: {$sv->cfg['avatar_maxs']}KB.";
  }    
 
  return array('err' => $err, 'errm' => $errm);
}

// PUBLIC CONTROLLERS
/**
 * module: reminder
 */
function c_public_reminder() {
  global $sv;

  $sv->load_model('restore');
  $sv->m['restore']->table = 'accounts';
  $sv->m['restore']->activate_url = $sv->view->full_url."?key=";

  
  if (isset($sv->_get['key']))  {
    $sv->_post['new']['key'] = $sv->_get['key'];   
    $ret['todo'] = 'activate';
  }
  else {
    $ret['todo'] = 'sendemail';
  }
  
  $s = $this->init_submit();
  
	    
  $ret['s'] = $s;
  return $ret;
}
function sc_public_reminder($n) {
  global $std, $sv, $db;

  $err = 0;

  $vals = array();
  if (isset($n['key'])) {
    $ret['show_form'] = 1;
    
    //activate     
    $key = $std->text->cut($n['key'], 'allow', 'mstrip');
    
    // check key
    $restore = $sv->m['restore']->check_key($key, 1);    
    $err = ($restore['err']) ? 1 : $err;
    $this->errm($restore['errm'], $err);
        
    if (!$err) {
      $user = $this->get_item($restore['d']['uid']);
      if ($user===false) { 
        $err = 1;
        $this->errm("Пользователь не найден в нашей базе данных, 
        возможно он был удален, либо ошибка восстановления, сообщите администратору", $err);
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
        $this->errm("Введенные пароли не совпадают.", $err);
      }
      else {
        $n['pass'] = $std->text->cut($n['pass'], 'allow', 'mstrip');
        $r = $this->set_new_password($n['pass'], $user['id']);
        $err = ($r['err']) ? 1 : $err;
        $this->errm($r['errm'], $err);
        
      }
      
      if (!$err) {
        $r = $sv->m['restore']->set_used_key($key);
        $err = ($r['err']) ? 1 : $err;
        $this->errm($r['errm'], $err);
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
            $this->errm("Логин не найден в нашей базе данных.", $err);
          }
        break;
        case 'email':
          $str = $std->text->cut($n['email'], 'allow', 'madd');
          $d = $this->get_item_wh("`email`='{$str}'");
          if ($d===false) { 
            $err = 1;
            $this->errm("Такой адрес не найден в нашей базе данных.", $err);
          }
        break;
        default:
          $err = 1;
          $this->errm("Неверно заданы параметры.", $err);
      }
      
    
      if (!$err) {
        $r = $sv->m['restore']->send_key($d['email'], $d['id'], $d['login']);
        $err = ($r['err']) ? 1 : $err;
        $this->errm($r['errm']);
      }
  }    
 	
	if ($err) $this->errs[] = __FUNCTION__;
	
	$ret['v'] = $vals;

	return $ret;
}

/**
 * module: profile
 */
function c_public_profile() {
  global $sv, $std, $db;
  
  $d = $this->get_item($sv->user['session']['account_id'], 1);
  $ret['table'] = $this->compile_edit_table($d, 'Применить', 0);
  
  $s = $this->init_submit_update(1);
  if ($s['submited'] && !$s['err']) {
    $v = $this->get_item($d['id'], 1);
    $ret['table'] = $this->compile_edit_table($v, 'Применить', 0);
  }

  $ret['s'] = $s;
    
  return $ret;
}

/**
 * module: profile
 */
function c_public_password() {
  global $sv, $std, $db;
  $sv->view->page['title'] = "Смена пароля";
  
  $d = $this->get_item($sv->user['session']['account_id'], 1);
  $s = $this->init_submit_update();
  
  $ret['s'] = $s;
    
  return $ret;
}

/**
 * module: profile
 */
function c_public_avatar() {
  global $sv, $std, $db;
  
  $ret['d'] = $this->get_item($sv->user['session']['account_id'], 1);
  $ret['s'] = $this->init_submit_update();
  if ($ret['s']['submited']) {
    $ret['d'] = $this->get_item($sv->user['session']['account_id'], 1);
  }
  
  $ret['ext_row'] = implode(", ", $this->ext_ar);
  
  return $ret;
}

/**
 * module: registration
 */
function c_public_registration() {
  global $sv, $std, $db;
  
  $ret['s'] = $this->init_submit_create(1, 0);
  $s = &$ret['s'];

  $ret['return_url'] = (isset($sv->_get['return_url'])) ? $std->text->escape_url($sv->_get['return_url'], 1) : $sv->view->return_url_success;
  
  // если успешная регистрация
  if ($s['submited'] && !$s['err']) {
    if ($this->auth_after_reg) {
      $sv->load_model('session');
      $account = $sv->m['session']->auth_user($s['insert_id']);
    }
  }
  else {  
    // инициируем капчу для формы
    $sv->load_model('antispam');
    $ret['captcha'] = $sv->m['antispam']->init_captcha();
  }
  
 return $ret;
}


// other
function set_new_password($pass, $uid) {
  global $sv, $std, $db;
  
  $err = 0;
  $errm = array();
  
  $pass = $this->v_password($pass);
  $r = $this->update_row(array('password' => $pass), $uid);
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

function password_hash($val) {
  if ($val=='') return "";
  
  $ret = $this->salt($val);
  
  return $ret;
}

function salt($pass){
  
  $salt = "dlksdf9234fdkmsdkl4";  
  $spec=array('~','!','@','#','$','%','^','&','*','?');
  $crypted=md5(md5($salt).md5($pass));
  $c_text=md5($pass);
  for ($i=0;$i<strlen($crypted);$i++){
      if (ord($c_text[$i])>=48 and ord($c_text[$i])<=57){
          @$temp.=$spec[$c_text[$i]];
      }elseif(ord($c_text[$i])>=97 and ord($c_text[$i])<=100){
          @$temp.=strtoupper($crypted[$i]);
      }else{
          @$temp.=$crypted[$i];
      }
  }
  return md5($temp);
}

function img_avatar($avatar) {
  $ret = ($avatar!='') ? "<img src='{$this->uploads_url}{$avatar}'>" : '';
  
  return $ret;
}

function online_ar() {
  global $sv, $std, $db;
  
  $exp = $sv->post_time - 60*15;
  $ar = array();
  $db->q("SELECT *, count(*) as size FROM {$sv->t['session']} WHERE time>'{$exp}' GROUP BY ip, account_id ORDER BY time DESC", __FILE__, __LINE__);
  while($d = $db->f()) { 
    $ar[] = $d;
  }
  $ret['list'] = $ar;
  $ret['count'] = count($ar);
   

  /*
    $ar = array();
  $db->q("SELECT * FROM {$sv->t['session']} WHERE time>'{$exp}' ORDER BY time DESC", __FILE__, __LINE__);
  while($d = $db->f()) { 
    $d['f_time'] = date("Y-m-d H:i:d", $d['time']);
    $ar[] = $d;
  }
 */
  
  return $ret;
}

function admin_activations() {
  global $sv, $std, $db;

  if (isset($sv->_post['new']['todo']) && $sv->_post['new']['todo']=='activate') {
    $ar = (isset($sv->_post['new']['items']) && is_array($sv->_post['new']['items'])) ? $sv->_post['new']['items'] : array();
    foreach ($ar as $uid => $act) {
      if ($act=='approve') {
        $this->approve_user($uid);
      }
      elseif($act=='hide') {
        $this->hide_user($uid);
      }
      elseif($act=='remove') {
        $this->remove_user($uid);
      }
    }
  }
  
  
  $ret = $this->item_list("`active`='0' AND time_req IS NOT NULL", "time_req ASC", 0, 1);
  
  
  return $ret;
}

function approve_user($id) {
  global $db;  
  $id = intval($id);
  $db->q("UPDATE {$this->t} SET `active`='1' WHERE id='{$id}'", __FILE__, __LINE__);
  return $db->af();
}

function hide_user($id) {
  global $db;  
  $id = intval($id);
  $db->q("UPDATE {$this->t} SET time_req=NULL WHERE id='{$id}'", __FILE__, __LINE__);
  return $db->af();
}

function remove_user($id) {
  global $db;  
  $id = intval($id);
  $db->q("DELETE FROM {$this->t} WHERE id='{$id}'", __FILE__, __LINE__);
  return $db->af();
}

function init_account($code="") {
  global $sv, $db, $std;
  
  if ($this->initialized) {
    return $this->status;
  }
  else {
    $this->initialized = true;
  }
    
  $uid = intval($sv->user['session']['account_id']);
  $this->d = $d = $this->get_item($uid, 1);
  $this->current_record = $d['id'];
  if ($d===false) die("Невозможно инициализировать учетную запись пользователя: {$uid}.");
  
  $this->load_active_list();
  
  // общее количество закачек
  $db->q("SELECT 0 FROM {$sv->t['files']} WHERE user='{$this->current_record}'", __FILE__, __LINE__);
  $this->status['all'] = $db->nr();
  
  return $this->status;
}

function load_active_list($uid=0) {
  global $sv, $std, $db;
    
  $uid = intval($uid);
  $uid = ($uid<=0) ? $this->current_record : $uid;
  
  // количество активных закачек
  $db->q("SELECT * FROM {$sv->t['files']} WHERE user='{$uid}' AND status_id IN ('0', '1', '2', '3') ORDER BY req_time ASC", __FILE__, __LINE__);
  $this->status['active'] = $db->nr();
  
  $ar = array();
  while($d = $db->f()) {
    $d = $sv->m['file']->parse($d);
    $d = $sv->m['file']->full_parse($d, 1);
    //t($d);
    $ar[] = $d;
  }
  $this->active_list['list'] = $ar;
  $this->active_list['count'] = count($ar);

  return $this->active_list;   
}

function mail2admins($title, $text) {
  global $sv, $std, $db;
  
  $ar = array();
  $db->q("SELECT email FROM {$this->t} WHERE group_id='{$this->admin_group}'", __FILE__, __LINE__);
  while ($d = $db->f()) {
    if ($std->text->v_email($d['email'])) {
      $ar[] = $d['email'];
    }
  }
  
  if (count($ar)>0) {
    $to = implode(", ", $ar);
    $std->mail->from_default($to, $text, $title);    
  }
}


 
/**
 * @deprecated use c_public_registration
  */
function registration() {
  global $sv, $db, $std;   
  
  $this->code = 'registration';
  
	$submited = false;	
	$err = false;
	$errm = array();
	$p = $vals = array();
	
	if (!isset($sv->_post['new'])) {
	  $err = true;	 
	}
	else {
	  $submited = true;
    $n = $sv->_post['new'];
    foreach ($n as $k=>$v) {
      $n[$k] = trim($std->text->cut($v, 'cut', 'mstrip'));
    }
	}
	
	if (!$err) {
	  $fs = $this->get_active_fields('write');	
	  foreach ($fs as $f) {
	    $n[$f] = (isset($n[$f])) ? $n[$f] : "";
	    if (method_exists($this, "v_{$f}")) {
	  	  eval("\$p[\$f] = \$this->v_{$f}(\$n[\$f]);");
	  	  $vals[$f] = $std->text->cut($p[$f], 'replace', 'replace');
	  	}
	  	else {
	  	  die("warn: method <b>v_{$f}</b> not exists");
	  	}
	  }
	  $err = ($this->v_err) ? true : $err;
	  $errm = array_merge($errm, $this->v_errm);
	}
	
  if ($submited) {
    if ($this->is_login_exists($p['login'])) {
      $err = true;
      $errm[] = "Логин <b>{$p['login']}</b> уже занят.";
    }
  }
	
	if ($submited) {
    $p2 = $this->password_hash($n['password_confirm']);
    if ($p['password']!==$p2) {
      $err = true;
      $errm[] = "Введенные пароли не совпадают.";
    }
	}

	
	
	if (!$err) {
	  $p['active'] = 1;
	  $p['time_reg'] = $sv->date_time;
	  $p['time_req'] = $sv->date_time;
	  $p['group_id'] = 1;	 
	  $p['last_ip'] = $sv->ip;
	  $p['last_agent'] = $sv->user_agent;
	  $p['last_request'] = $sv->request_url;
    $p['last_refer'] = $sv->refer;
	}
	
	if (!$err) {
	  $af = $this->insert_row($p);
	  if ($af>0) {
	    $errm[] = "Вы успешно зарегистрированы в системе, можете <a href='/'>войти</a>.";
	    $v = array();
	  }	  
	  else {
	    $err = true;
	    $errm[] = "Ошибка базы данных при регистрации, сообщите администратору.";
	  }
	}
	
    
  $ret['v'] = $vals;
  $ret['err'] = $err;
  $ret['submited'] = $submited;
  $ret['err_box'] = $std->err_box($err, $errm);
    
  return $ret;
  
}

/**
 * @deprecated use this->c_password
 */
function change_pass()   {
  global $sv, $std, $db;
  
  $err = false; 
  $errm = array();
  
  if (isset($sv->_post['submit_pass'])) {    
    
    $n = $sv->_post['new'];      
    $p = array();
    
    //CHECK CURRENT
    $cur = $this->password_hash($n['old']);
    $uid = intval($sv->user['session']['account_id']);
    
    $db->q("SELECT `password` FROM {$this->t} WHERE id='{$uid}'", __FILE__, __LINE__);
    if ($db->nr()<=0) {
      die("Пользователь не найден.");
    }
    $d = $db->f();
    if ($d['password']!==$cur) {
      $err = true;
      $errm[] = "Текущий пароль не верен.";      
    }
        
  	// PASSWORD
  	$p1 = $this->password_hash($n['pass']);
  	$p2 = $this->password_hash($n['pass2']);
  	if ($p1!==$p2) {
  	  $err = true;
  	  $errm[] = "Введенные пароли не идентичны.";
  	}	
    if (strlen($n['pass'])<8) {
      $err = true;
      $errm[] = "Пароль короче 8-ми символов.";
    } 
    if (!$err) {
      $p['password'] = addslashes($p1);
    }
        
    
    if (!$err) {      
      $db->q("UPDATE {$this->t} SET `password`='{$p['password']}' WHERE id='{$uid}'", __FILE__, __LINE__);
      $errm[] = "Новый пароль успешно установлен.";
    }
    
  }
 
  $ret['err_box'] = $std->err_box($err, $errm);
  return $ret;
}

//eoc
}  
  
?>