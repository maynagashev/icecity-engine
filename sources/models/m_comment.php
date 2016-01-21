<?php

/**
 *   
  $sv->load_model('comment');  
  $ret['comments'] = $sv->m['comment']->init_system($this->name, $d['id']);
  

  {include file='blocks/comments.tpl'}
 *
 */

class m_comment extends class_model {
  
  var $tables = array(
    'comments' => "
        `id` bigint(20) NOT NULL auto_increment,        
        `text` text null,
        `time` datetime null,
        `username` varchar(255) null,
        `email` varchar(255) null,
        `www` varchar(255) null,
        `approved` tinyint(1) not null default '1',
        
        `account_id` int(11) not null default '0',
        `parent_id` bigint(20) not null default '0',
        `ip` varchar(255) null,
        
        `model` varchar(255) null,
        `object` bigint(20) not null default '0',
        
        `url` varchar(255) null,
        `object_title` varchar(255) null,
        
        `created_at` datetime default NULL,
        `created_by` int(11) NOT NULL default '0',
        `updated_at` datetime default NULL,
        `updated_by` int(11) NOT NULL default '0',
        `expires_at` datetime default NULL,
        
        PRIMARY KEY  (`id`),
        KEY (`account_id`),
        KEY (`model`, `object`)
    "
  );

var $comment_modes = array(
  'auth',
  'guest',
  'both'
);
  
var $c_mode = ''; // authorized (only) | guest (only) | both
var $c_model = '';
var $c_object = 0;
var $c_object_data = array();

var $per_page = 50;
var $action_url = "";

var $name_max_len = 30;

/**
 * имя метода который следуют вызвать у родительской модели при обновлении списка комментов 
 * $sv->m[$c_model]->$object_update_method('create', $d);
 *
 * @var unknown_type
 */
var $object_update_method = 'comments_update';
var $object_title_field = 'title';

var $session_vars = array('username', 'email', 'www' );

function __construct() {
  global $sv;  
  
  $this->t = $sv->t['comments'];
  $this->action_url = (isset($sv->view->safe_url)) ? $sv->view->safe_url : '';
  
  $this->init_field(array(
  'name' => 'time',
  'title' => 'Время создания',
  'type' => 'datetime',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));  

   
  $this->init_field(array(
  'name' => 'model',
  'title' => 'Модель',
  'type' => 'varchar',
  'len' => 30,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit'),
  'selector' => 1
  ));  
    
  $this->init_field(array(
  'name' => 'object',
  'title' => 'Объект',
  'type' => 'bigint',
  'len' => 30,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));  
    
  $this->init_field(array(
  'name' => 'account_id',
  'title' => 'Пользователь',
  'type' => 'int',
  'len' => 10,
  'input' => 'select',
  'belongs_to' => array('table' => 'accounts', 'field' => 'id', 'return' => 'login'),
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));  
  
  $this->init_field(array(
  'name' => 'account',
  'title' => 'Пользователь',
  'virtual' => 'account_id',
  'show_in' => array('default'),
  'write_in' => array()
  ));  
  
  $this->init_field(array(
  'name' => 'username',
  'title' => 'Имя пользователя',
  'type' => 'varchar',
  'len' => 30,
  'show_in' => array('remove', 'default'),
  'write_in' => array('create', 'edit', 'public_create'),
  'selector' => 1
  ));  
    
  
  $this->init_field(array(
  'name' => 'email',
  'title' => 'Email',
  'type' => 'varchar',
  'len' => 30,
  'show_in' => array('remove', 'default'),
  'write_in' => array('create', 'edit', 'public_create'),
  'selector' => 1
  ));  
  
  $this->init_field(array(
  'name' => 'www',
  'title' => 'Сайт',
  'type' => 'varchar',
  'len' => 30,
  'show_in' => array('remove', 'default'),
  'write_in' => array('create', 'edit', 'public_create'),
  'selector' => 1
  ));  
      
  $this->init_field(array(
  'name' => 'approved',
  'title' => 'Проверен?',
  'type' => 'boolean',  
  'show_in' => array('remove', 'edit'),
  'write_in' => array(),
  'selector' => 1,
  'description' => "<a href='./?commentsedit_approve={$sv->id}'>опубликовать</a> / <a href='./?commentsedit_deprove={$sv->id}'>отправить на модерацию</a>"
  ));    
  
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Текст',
  'type' => 'text',
  'len' => 80,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit', 'public_create')
  ));  

  $this->init_field(array(
  'name' => 'object_title',
  'title' => 'Заголовок объекта',
  'type' => 'varchar',
  'len' => 80,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));  

   
  $this->init_field(array(
  'name' => 'url',
  'title' => 'Ссылка',
  'type' => 'varchar',
  'len' => 80,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));  
  
  $this->init_field(array(
  'name' => 'ip',
  'title' => 'IP',
  'type' => 'varchar',
  'len' => 80,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));  
    
  $this->init_field(array(
  'name' => 'parent_id',
  'title' => 'ID родителя?',
  'type' => 'bigint',
  'len' => 20, 
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit', 'public_create')
  ));    

}

function init_system($model, $object, $mode = '') {
  global $sv, $std, $db;

  $this->code = 'public_create';
  $this->c_model = $model;
  $this->c_object = intval($object);
  $this->c_mode = (in_array($mode, $this->comment_modes)) ? $mode : 'guest';
  
  $sv->vars['styles'][] = "bbcode.css";
  
  $sv->load_model('account');
  
  // инициализация и считывание объекта коммента
  $this->c_object_data = $this->object_init($this->c_model, $this->c_object);
  if (!$this->c_object_data) {
    $this->errm("Не возможно инициализировать объект комментариев: {$this->c_model} = {$this->c_object}", 1);
    return false;  
  }
  
  // проверяем наличие у объекта this->object_update_method метода 
  $this->object_update();
  
  //submit check && submit processing
  $s = $this->init_submit_create(1, 1);
  
  // выбираем логины и аватары у кого указан аккаунт
  $this->joins = array(
    'f' => ", a.login, a.avatar",
    'j' => "LEFT JOIN {$sv->t['accounts']} a ON ({$this->t}.account_id=a.id)"
  );
  
  // собственно выбираем комменты
  $wh = array();
  $wh[] = "`model`='{$this->c_model}'";
  $wh[] = "`object`='{$this->c_object}'";
  // если гость то дополнительно скрываем непровернные чужие
  if ($sv->user['session']['group_id']!=3) {
    if (isset($_SESSION['email']) && $std->text->v_email($_SESSION['email'])) {
      $email = $db->esc($_SESSION['email']);
      $wh[] = "(`approved`='1' OR (`approved`='0' AND {$this->t}.email='{$email}'))";
    }
    else {
      $wh[] = "`approved`='1'";
    }
  }
  $where = implode(" AND ", $wh);
  $ret = $this->item_list($where, "`time` ASC", 0, 1);
  
  // form  
  $ret['markitup'] = $std->markitup->markitup_code("#comment_text", 'bbcode');
  
  
  
  // восстаналиваем сохраненные данные 
  $session = array();
  foreach ($this->session_vars as $k) {
    $session[$k] = (isset($_SESSION[$k])) ? $_SESSION[$k] : '';
    if (!$s['v'][$k]) {
      $s['v'][$k] = $this->escape_val($session[$k]);
    }
  }
  
  $ret['session'] = $session;
  $ret['s'] = $s;
  $ret['action_url'] = $this->action_url;
  $ret['mode'] = $this->c_mode;

  return $ret;
}

function parse($d) {
  global $std, $sv;
  
  $d['f_text'] = $this->apply_filter($d['text'], 'bbcode');
  $d['f_time'] = $std->time->format($d['time'], 0.5, 1);  
  $d['f_date'] = $d['f_time']; // compiliance
  $d['c_url'] = $this->action_url."#comment-{$d['id']}";
  $d['f_url'] = $d['url']."#comment-{$d['id']}";
  
  // имя пользователя
  if ($d['username']) {
    // ничего не делаем все ок
  }
  elseif($d['login']) {
    $d['username'] = $d['login'];
  }
  else {
    $d['username'] = "Безимянный";
  }
  
  
  
  if ($d['www']!='') {    
    // добавляем протокол
    $d['www_full'] = (!preg_match("#^([a-z0-9]{2,10})\://#si", $d['www'])) ? "http://".$d['www'] : $d['www'];    
    // укорчиваем
    $d['www_cut'] = preg_replace("#^(.{15}).*(.{10})$#si", "\\1...\\2", $d['www_full']);
  }
  else {
    $d['www_full'] = '';
    $d['www_cut'] = '';
  }
  

  // если не задан емайл и задан аватар оформляем аватар из аккаунта
  if (!$d['email'] && isset($d['avatar']) && $d['avatar']) {
    $d['img_avatar'] = (isset($d['avatar'])) ? $sv->m['account']->img_avatar($d['avatar']) : '';
  }
  else {
    $d['email_hash'] =  md5( strtolower( trim( $d['email'] ) ) );  
    $d['img_avatar'] = "<a href='http://ru.gravatar.com/' target=_blank rel='nofollow'><img src='http://www.gravatar.com/avatar/{$d['email_hash']}?s=80&r=pg&d=mm' border='0' width='80' height='80'></a>";
  }
  
  return $d;
}
// VALIDATIONS

function last_v($p) {
  global $sv, $std, $db;
  
  if ($this->code=='public_create') {
    $p['account_id'] = $sv->user['session']['account_id'];
    $p['model'] = $this->c_model;
    $p['object'] = $this->c_object;
    $p['time'] = $sv->date_time;
    $p['url'] = $this->action_url;
    $p['ip'] = $sv->ip;
    $p['object_title'] = ($this->object_title_field=='p_title') ? $sv->vars['p_title'] : $this->c_object_data[$this->object_title_field];
    
    // проверен модератором?
    $sv->load_model('trustemail');
    $trust = $sv->m['trustemail']->get_trust($p['email']);
    $p['approved'] = ($trust) ? 1 : 0;
    
    $item = $this->get_item_wh("
      `model`='".$db->esc($p['model'])."' 
      AND `object`='".$db->esc($p['object'])."' 
      AND `account_id`='".$db->esc($p['account_id'])."'
      AND `text`='".$db->esc($p['text'])."' 
      ");
    if ($item) { 
      $this->v_err = 1;
      $this->errm("Это сообщение уже было добавлено ранее.", 1);
    }

  }
  
  return $p;
}

function v_username($t)  {
  global $std;
  $t = strip_tags($t);
  $t = preg_replace("#[^A-Яа-я0-9a-z\-\_\.\ \(\)\[\]\:]#si", "", $t);
  $t = trim($t);    
  $t = (strlen($t)>$this->name_max_len) ? substr($t, 0, $this->name_max_len) : $t;
  
  if ($t=='') {
    $this->v_err = 1;
    $this->errm("Вы не указали свое имя.", 1);
  }
  elseif (!preg_match("#[a-zа-я]#si", $t)) {
    $this->v_err = 1;
    $this->errm("Имена состоящие только из цифр или спецсимволов не допустимы.", 1);
  }  
  
  return $t;
}

function v_text($t) {
  global $std;
  
  $t = trim($t);  
  $t = strip_tags($t);
  
  $temp = preg_replace("#[^a-zа-я0-9]#si", "", $t);
  if ($temp=='') {
    $this->v_err = 1;
    $this->errm("Текст сообщения не указан, либо не содержит обычного текста, напишите хоть что-нибудь.", 1);
  }
    
  return $t;
}

function v_email($t) {
  global $std;
  
  if ( !$std->text->v_email($t) ) {
    $this->v_err = 1;
    $this->errm("Неверно указан email.", 1);
    $t = '';
  }
  
  return $t;
}

function v_www($t) {
  global $std;
  
  $t = strip_tags($t);
  $t = trim($t);
    
  return $t;
}

function v_parent_id($id) {
  
  $id = abs(intval($id));

  return $id;
}

// callbacks
function  df_url($t) {
  $ret = ($t!='') ? "<a href='{$t}#comment-{$this->d['id']}' target=_blank>ссылка</a>" : '';
  return $ret;
}

function df_text($t) {
  global $std;
  
  $t = $std->text->cut($t, 'cut', 'cut');
  return $t;
}

// PRE/POST ACTIONS
function after_create($p, $err) { 
  global $sv;
  
  if (!$err) {    
    if (!$p['approved']) {
      $this->errm("Ваш комментарий отправлен, <b>после проверки администратором сайта он будет показан в списке комментариев</b>.", 0);
    }
        
    $p[$this->primary_field] = $this->current_record;
    $this->after_change($p);
  }
}

function before_create() {
  global $sv;
  
  
  // сохраняем в сессию последние данные  
  foreach($this->session_vars as $k) {
    if (isset($this->n[$k])) {
      $_SESSION[$k] = $this->n[$k];
    }
  }
}

// CONTROLLERS 

function c_moderation() {
  global $sv, $std, $db;
  
  $s = $this->init_submit();
    
  $ar = $this->item_list("`approved`='0'", "`id` DESC", 0, 1);
  
  $sv->load_model('trustemail');
  foreach($ar['list'] as $k => $d) {
    $d['trust'] = $sv->m['trustemail']->get_trust($d['email']);
    
    $ar['list'][$k] = $d;
  }
  $ar['s'] = $s;
  
  return $ar;
}

function sc_moderation() {
  global $sv, $std, $db; $ret = array();
  
  $err = 0;
  $errm = array();
  if (!isset($sv->_post['new']['items']) || !is_array($sv->_post['new']['items'])) {
    $err = 1;
    $errm[] = "Ничего не выбрано.";
  }
  else {
    $items = $sv->_post['new']['items'];
  }
  
  $approved = 0;
  $removed = 0;
  $skipped = 0;
  
  if (!$err) {
    
    foreach($items as $id => $p) {
      switch($p['approve']) {

        
        // just approve
        case 3:          
          $this->approve_post($id, $p, 0);
          $approved++;
        break;
                  
        // approve default & add to trus
        case 1:
          $this->approve_post($id, $p, 1);
          $approved++;
        break;
        
        // remove
        case 2: 
          $this->remove_post($id);
          $removed++;
        break;
        
          
        // skip
        case 0: default:
          $skipped++;
          
        break;
      }
    }
    
  }
  
  $errm[] = "
  <p>Одобрено: <b>{$approved}</b></p>
  <p>Удалено: <b>{$removed}</b></p>
  <p>Пропущено: <b>{$skipped}</b></p>
  ";
  
  $ret['err'] = $err;
  $ret['errm'] = $errm;

  return $ret;
}

function c_approve() {
  global $sv;
  $id = intval($sv->id);
  $this->approve_post($id);
} 

function c_deprove() {
  global $sv;
  $id = intval($sv->id);
  $this->deprove_post($id);
}

// ACTIONS
function approve_post($id, $n = array(), $add_to_trust = 1) {
  global $sv, $std, $db;
  
  $id = intval($id);
  $d = $this->get_item($id, 0, 0);
  if (!$d || $d['approved']==1) {
    return false;
  }
  
  $p = array('approved' => 1);
  
  $ret = $this->update_row($p, $id);
  
  // повышаем счетчик у связанного объекта
  $d['action'] = 'approve';
  $this->object_update($d);
  
  // добавляем в траст
  if ($add_to_trust && $std->text->v_email($d['email'])) {
    $sv->load_model('trustemail');    
    $sv->m['trustemail']->add_email($d['email'], $d['ip']);  
  }
  
  return $ret;
}

function deprove_post($id) {
  global $sv, $std, $db;
  
  $id = intval($id);
  $d = $this->get_item($id, 0, 0);
  if (!$d || $d['approved']==0) {
    return false;
  }
  
  $p = array('approved' => 0);
  
  $ret = $this->update_row($p, $id);
  
  // снижаем счетчик у связанного объекта
  $d['action'] = 'deprove';
  $this->object_update($d);
  
  return $ret;
}

function remove_post($id) {
  global $sv, $std;
  
  $d = $this->get_item($id);
  if (!$d) {
    return false;
  }
  
  $this->before_remove();
  
  $ret = $this->remove_row($id);
  
  $d['action'] = 'remove';
  $this->object_update($d);
  
  return $ret;
}

function object_init($model, $id) {
  global $sv;
  
  $sv->load_model($model);
  $d = $sv->m[$model]->get_item($id, 1);
  return $d;
}
/**
 * обновление статистики родительской таблицы
 * инициализирует связанную модель, и вызывает метод (с указанием текущего кода и данных)
 *
 * если не заданы данные то вызывает модель и проверяет метод, не вызывая его
 * 
 * @param unknown_type $d - актуальная запись измененного коментария, если не задано, то просто проверяется наличие соотв. метода
 */
function object_update($d = false) {
  global $sv;
  
  $model = (!$d) ? $this->c_model : $d['model'];
  $method = $this->object_update_method;
  
  $sv->load_model($model);
  
  if (method_exists($sv->m[$model], $method)) {
    if ($d) {
      $sv->m[$model]->$method($this->code, $d);
    }
  }
  else {
    t("Метод для обновления статистики родительской таблицы: {$this->c_model}->{$method}(code, comment) - не задан.", 1);
  }
    
}

/**
* success after_create, succes after_update, success after_remove
*/
function after_change($d) {
  $this->object_update($d);
}

// CRON
function cron_notify($last_date) {
  global $sv, $db; $ret = '';
  
  $date = $db->esc($last_date);

  $tr = array();
  $i = 0;
  $ar = $this->item_list("`approved`='0' AND `time`>'{$date}'", "id DESC", 0, 1); 
  foreach($ar['list'] as $d) { $i++;
    $tr[] = "<tr valign=top>
        <td width='1%'>{$i}.</td>
        <td><a href='{$sv->vars['site_url']}admin/?commentsedit_edit={$d['id']}' target=_blank>{$d['username']}</a> / {$d['email']} / {$d['site']} / ip: {$d['ip']}
            <p style='background-color: #efefef;padding: 10px;'>{$d['f_text']}</p>
        </td>
        </tr>";
  }
  
  if (count($tr)>0) {
    $ret = "
    <b><a href='{$sv->vars['site_url']}admin/index.php?commentsedit_moderation' target=_blank>Новые комментарии для проверки</a></b>
    <table width=100%>
    ".implode("\n", $tr)."
    </table>
    "; 
  }

  return $ret;
}



function preload_admin() {
  global $sv;
  
  $sv->parsed['not_approved_comments'] = $this->count_wh("`approved`='0'");  
  
}

//eoc
}


?>