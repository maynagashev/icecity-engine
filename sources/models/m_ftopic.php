<?php

/*
Модель отдельного форума
*/


class m_ftopic extends class_model {

  var $tables = array(
    'ftopics' => "    
      `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
      `title` varchar(255) not null default '',  
      `description` varchar(255) not null default '',  
      `slug` varchar(255) not null default '',      
      
      `status_id` tinyint(1) not null default 0,
      `pinned`  int(11) not null default '0',
      
      `forum_id` int(11) not null default '0',
      `forum_slug` varchar(255) not null default '',
      
      `posts`  int(11) not null default '0',
      `replycount` int(11) not null default '0',
      `views` bigint(20) not null default '0',
      
      `starter_id` int(11) null,
      `starter_name` varchar(255) null,
      `start_time` int(11) null,      
      `ip` varchar(255) null,
      
      `first_post_id` bigint(20) null,
      
      `last_post_id` bigint(20) null, 
      `last_post_time` int(11) null,
      `last_poster_id` int(11) null, 
      `last_poster_name` varchar(255) null,
      
      `created_at` DATETIME NULL ,
      `created_by` INT( 11 ) NULL ,
      `updated_at` DATETIME NULL ,
      `updated_by` INT( 11 ) NULL ,
      
      PRIMARY KEY ( `id` ),      
      KEY (`slug`),
      KEY (`status_id`), 
      KEY (`pinned`)
    "
  );
  
  var $status_ar = array(
    0 => "Черновик", 
    1 => "Открыта",
    2 => "Закрыта"
    
  );
  
  var $per_page = 30;
  
  var $filters = array(
  'raw' => "обычный HTML",
  'nl2br' => "автоперенос на новую строку",
  );  
  
  /**
   * Параметр для передачи в create_topic текущего slug из v_forum_id
   *
   * @var unknown_type
   * @deprecated 
   */
  var $last_forum_slug = "";
  
  
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['ftopics'];  
  
   
  $this->init_field(array(
  'name' => 'post_time',
  'virtual' => 'last_post_time',
  'title' => 'Время последнего сообщения',
  'show_in' => array('default'),
  ));     

   
  $this->init_field(array(
  'name' => 'forum_id',
  'title' => 'Форум',
  'type' => 'int',  
  'input' => 'select',
  'belongs_to' => array('table' => 'forums', 'field' => 'id', 'return' => 'title'),
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));      
  
  $this->init_field(array(
  'name' => 'forum_slug',
  'title' => 'Идентификатор форума',
  'type' => 'varchar',  
  'elan' => 20,
  'show_in' => array('edit'),
  'write_in' => array()
  ));      
    
   
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название темы',
  'type' => 'varchar',  
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create', 'public_newtopic')
  ));    
    
  $this->init_field(array(
  'name' => 'description',
  'title' => 'Описание темы',
  'type' => 'varchar',  
  'len' => '50',
  'show_in' => array( 'remove'),
  'write_in' => array('create', 'edit', 'public_newtopic')
  ));    
  
  
  $this->init_field(array(
  'name' => 'forum',
  'title' => 'Форум',
  'virtual' => 'forum_id',  
  'show_in' => array('default')
  ));    
  
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Текст поста',
  'virtual' => 'id',  
  'type' => 'text',
  'len' => 70,
  'write_in' => array('create', 'public_newtopic')
  ));  
    
  
  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Статус',
  'type' => 'tinyint',  
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
  'name' => 'pinned',
  'title' => 'Важная',
  'type' => 'boolean',  
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));
    
  
  $this->init_field(array(
  'name' => 'ip',
  'title' => 'IP',
  'type' => 'varchar',  
  'len' => '20',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));
      
  
  $this->init_field(array(
  'name' => 'last_post_time',
  'title' => 'Время последнего сообщения',
  'type' => 'int',  
  'input' => 'time',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));     
  
  
  $this->init_field(array(
  'name' => 'posts',
  'title' => 'Ответов',
  'type' => 'int',  
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));     
  $this->init_field(array(
  'name' => 'views',
  'title' => 'Просмотров',
  'type' => 'int',  
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));     
      
}

// parsing
function parse($d) {
  global $sv, $std;
  
  $sv->load_model('forum');
  
  $d['url'] = $this->get_url($d);
  $d['f_last_post_time'] = $std->time->format($d['last_post_time'], 0.5);
  $d['last_post_url'] = $d['url']."&post={$d['last_post_id']}#post{$d['last_post_id']}";
  
  $last_visit =  ($sv->user['session']['account_id']>0) ? strtotime($sv->user['account']['last_visit']) : 0;
  $d['is_new'] = ($last_visit < $d['last_post_time']) ? 1 : 0;
  
  
  return $d;
}

function get_url($d) {
  global $sv;
  
  // если не утановлен пробуем последнюю вставленную
  $id = (isset($d['id'])) ? $d['id'] : $this->last_insert_id;
  
  // если нет forum_slug то пробуем последний
  $slug = (isset($d['forum_slug']) && $d['forum_slug']!='') ? $d['forum_slug'] : $this->last_forum_slug;
  
  return $sv->m['forum']->forum_url."/{$slug}/?topic={$id}";
}

// validations
function v_title($t) {
  global $sv, $std;
  
  $t = $std->text->cut($t, 'cut', 'allow');
  $t = trim($t);
  
  if ($t=='') {
    $this->v_err = 1;
    $this->errm("Не указано название темы.", 1);
  }
  return $t;
}

function v_description($t) {
  global $std;
  
  
  $t = $std->text->cut($t, 'cut', 'allow');
  $t = trim($t);
  
  return $t;
}

function v_pinned($val) {  
  $val = ($val) ? 1 : 0;
  return $val;
}

function v_forum_id($val) {
  global $sv;
  
  $val = intval($val);
  $sv->load_model('forum');
  $d = $sv->m['forum']->get_item($val);
  if (!$d) {
    $this->v_err = 1;
    $this->errm("Форум ({$val}) в котором необходимо создать тему не найден.", 1);
  }
  else {
    $this->last_forum_slug = $d['slug'];
  }
  
  return $val;
}

function last_v($p) {
  global $sv;
  
  if ($this->code=='edit') {
    $sv->load_model('forum');
    $forum = $sv->m['forum']->get_item($p['forum_id']);
    if ($forum) {
      $p['forum_slug'] = $forum['slug'];
    }
  }
  
  return $p;
}

// pre-post
function before_create() {
  global $sv, $std, $db;
  
  
}

function after_update($d, $p, $err) {
  global $sv, $db;
  
  if (!$err) {
    // если тема была перемещена в другой форум
    if ($d['forum_id']!=$p['forum_id']) {
      $sv->load_model('forum');
      
      $sv->m['forum']->recount_forum($d['forum_id']);
      $sv->m['forum']->recount_forum($p['forum_id']);
    }
    
  }
}

function garbage_collector($d) {
  global $sv, $db;
  
  $err = 0;
    
  // удаляем сообщения, в том чсиле удаляется первое и счетчики сами обновляются
  $sv->load_model('fpost');
  $posts = $sv->m['fpost']->item_list("`topic_id`='{$d['id']}'", "`new_topic` ASC, `date` ASC", 0); // от старых к новым, главное в конце

  if ($posts['count']>0) {
    foreach($posts['list'] as $post) {
      
      // удаляем очередное сообщение
      $r = $sv->m['fpost']->remove_item($post['id'], $post);
      $err = ($sv->m['fpost']->err("remove_item{$post['id']}")) ? 1 : $err;
      
      if ($r['affected']) {
        $this->errm("Сообщение удалено #{$post['id']}, {$post['author_name']} от {$post['date']}.");
      }
      else {
        $this->errm("Не удалось удалить сообщение #{$post['id']}, {$post['author_name']} от {$post['date']}.", 1);
      }   
       
    }
    $this->errm_push($sv->m['fpost']->v_errm);
  }
  else {
    $this->errm("Прикрепленные, к выбранной теме, сообщения отсутствуют.");
  }
  
  if ($err) $this->errs[] = __FUNCTION__;
}

// callbacks
function df_post_time($t) {
  global $std;
  
  return $std->time->format($t, 0.5);
}


//controllers 

/*
Служебный контроллер создания темы
автоматически создается связанный fpost
с topic_id = insert_id() и 
new_topic=1, 
text = new[text], 
parent_id = _get[parent_id] 
title = RE: parent.title ? parent.title : topic.title
*/
function c_create() {
  global $sv, $std, $db;
 
  $s = $this->init_submit();
 
  if ($s['submited'] && !$s['err']) {
    if (isset($sv->_post['commit'])) {
      //return to index
      header("Location: ".su($sv->act).$this->slave_url_addon);
      exit();
    }
  }
    
  $rows = array();
    
  /**
   * таблица с формой
   */
  foreach($this->fields as $f) {   
    if (in_array($this->code, $f['write_in'])) {    
      $val = (isset($s['v'][$f['name']])) ? $s['v'][$f['name']] : $f['default'];
      $rows[] = $this->wrow($f['name'], $f['input'], $val, $f['title'], $f['len']);
    } 
    if (in_array($this->code, $f['show_in'])) {       
      $rows[] = $this->row($f['title'], "", $f['name']);
    }           
  }
 
  $ret['s'] = $s;
  $ret['form'] = $this->table($rows, "Создать");
  return $ret;
}

/**
 * сабмит контроллер 
 *
 */
function sc_create($err, $errm, $n, $d=false) {
   global $sv, $std, $db;

  if (method_exists($this, "before_create")) {
    $this->before_create();
  }

  // validation
  if (!$err) {
    $p = $this->validate_active_fields(1);
    
  }
 
  // predefined values
  foreach($this->predefined as $k=>$v) {
    if (!isset($p[$k])) {
      $this->vals[$k] = $p[$k] = $v;
    }
  }
 
  if (method_exists($this, 'last_v')) {
    $r = $this->last_v($p);  
    if ($r) {
      $p = $r;
    }
  }  
  
  
  // validation errors
  $err = ($this->v_err) ? true : $err;
  $this->errm($this->v_errm, $err);
  
  // inserting if no errors
  if (!$err) {
    // передаем управление в функцию
    $r = $this->create_topic($p);
    $err = ($r['err']) ? 1 : $err;
    $errm = array_merge($errm, $r['errm']);
   
  }  
  
  $r = $this->after_create($p, $err);
  if (isset($r['errm'])) {
    $errm = array_merge($errm, $r['errm']);
  }
  
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  
  $ret['v'] = $this->vals;  
  return $ret;
  
}

// Публичные контроллеры
/**
 * Создание темы
 *
 * @return unknown
 */
function c_public_newtopic() {
  global $sv, $std, $db;
  
  $this->code = "public_newtopic";
  
  $this->init_controllers();
  
  $s = $this->init_submit();
  $ret['s'] = $s;  
  return $ret;
}

function sc_public_newtopic() {
   global $sv, $std, $db;
   
   $err = 0;

  // validation
  if (!$err) {
    $p = $this->validate_active_fields(1);
    $p['forum_id'] = $sv->m['forum']->c_forum['id'];
  }
  
  // validation errors
  $err = ($this->v_err) ? 1 : $err;
  
  // inserting if no errors
  if (!$err) {
    // передаем управление в функцию
    $this->create_topic($p);
    $err = ($this->err('create_topic')) ? 1 : $err;
  }  
  
  if ($err) $this->errs[] = __FUNCTION__;  
  
  $ret['v'] = $this->vals;  
  
  return $ret;  
  
}

/**
 * Просмотр темы
 *
 * @return unknown
 */
function c_public_view() {  
  global $sv, $std, $db;
  
  // читаем запись топика
  $id = (isset($sv->_get['topic'])) ? intval($sv->_get['topic']) : 0;
  $topic = $this->get_item($id, 1);
  if (!$topic) {
    $sv->view->show_err_page('notfound');
  }
  
  // тайтл и обновление статистики
  $sv->vars['p_title'] = $topic['title'];
  $this->update_row(array('views' => $topic['views']+1), $topic['id']);
  
  // подготовка пользовательских данных
  $sv->load_model('account');
  $userfields = array('login', 'avatar', 'posts', 'time_reg', 'group_id', 'last_time');
  $fields = array();
  foreach ($userfields as $fn) {
  	$fields[] = "a.{$fn}";
  }
  
  // сообщения
  $sv->load_model('fpost');
  $ret = $sv->m['fpost']->list_for_topic($topic);
    
  // Пересчитать посты в топике + последний пост
  $this->recount_topic($topic['id']);
  return $ret;
}

// stuff 

/**
 * Список тем на основе записи форума
 *
 * @param unknown_type $forum
 * @return unknown
 */
function topic_list($forum) {
  
  $ret = $this->topic_list_wh("`forum_id`='{$forum['id']}'", "`pinned` DESC, `last_post_time` DESC");
    
  return $ret;
}

/**
 * Список тем без указания форума
 * универсальный
 * @param unknown_type $wh
 */
function topic_list_wh( $wh = '', $order = "`last_post_time` DESC", $use_pinned = 0) {
  global $sv;
  
  if ($use_pinned) {
    $order = ($order!='') ? "pinned DESC, ".$order : 'pinned DESC';
  }
  
  $ret =  $this->item_list_pls($wh, $order, $this->per_page, 1, -1, $sv->m['forum']->url."&page=");  
  
  return $ret;
}

/**
 * Общая функция создания топиков
 *
 * @param $title & $desc = ALREADY VALIDATED and no errors
 * 
 * автоматически создается связанный fpost
с topic_id = insert_id() и 
new_topic=1, 
text = new[text], 
post.title = topic.title 
 *  
 */
function create_topic($p, $text = false) {
  global $sv, $std, $db;
  
  $err = 0;
  $topic_id = 0;
  
  // проверка параметров (которые уже должны быть отвалидированы)
  $rq = array('title', 'description', 'forum_id');
  foreach($rq as $k) {
    if (!isset($p[$k])) {
      die("не хватает параметра <b>{$k}</b> в ".__FILE__." ".__FUNCTION__);
    }
  }


  $forum = $sv->m['forum']->get_item($p['forum_id'], 1);
  if (!$forum) {
    $err = 1;
    $this->errm("Форум в котором необходимо создать тему не найден (2)", $err);
  }
  
  
  // Валидация и парсинг поста
  $sv->load_model('fpost');
  $post = array();
  
  // если текст не задан берем из сабмита
  if ($text===false) {
    $text = (isset($this->n['text'])) ? $this->n['text'] : "";
  }
  // валидация текста
  $post['text'] = $this->vals['text'] = $sv->m['fpost']->validate_field('text', $text);     
  
  // другие данные поста 
  $post['title'] = $p['title'];
  $post['new_topic'] = 1;
  $post['date'] = $sv->date_time;
  $post['ip'] = $sv->ip;
   
  // общие данные
  $p['starter_id']    = $post['author_id']    = $sv->user['session']['account_id'];
  $p['starter_name']  = $post['author_name']  = $sv->user['session']['login'];  
   
  // данные темы
  $p['start_time'] = $sv->post_time;
  $p['status_id'] = 1;
  $p['forum_slug'] = $forum['slug'];
  
  // обьединение стеков ошибок
  $err = ($sv->m['fpost']->v_err) ? 1 : $err;  
  $this->errm_push($sv->m['fpost']->v_errm, 1);
  $sv->m['fpost']->v_errm = array();
  
  // проверка существования такой темы в выбранном форуме
  if (!$err) {
    $tmp = $this->get_item_wh("`forum_id`='".$db->esc($p['forum_id'])."' AND `title`='".$db->esc($p['title'])."'");
    if ($tmp) {
      $err = 1;
      $this->errm("Тема с таким названием уже существует в выбранном форуме.", $err);
    }
  }
  
  
  
  // если нет ошибок создаем запись
  if (!$err) {
    if ($this->insert_row($p)) {
      $post['topic_id'] = $topic_id = $this->last_insert_id;
           
      if ($sv->m['fpost']->insert_row($post)) {
        $post['id'] = $sv->m['fpost']->last_insert_id;
     
        $url = $this->get_url($p);
        $this->errm("Новая тема успешно создана.");
        $this->errm("<a href='{$url}'>Посмотреть</a>");
        
        //обнуляем текущие значения
        foreach($this->vals as $k=>$v) {
          $this->vals[$k] = '';
        }
        
      }
      else {
        $err = 1;
        $this->errm("Не удалось создать прикрепленный к теме пост, сообщите об ошибке администратору.", 1);
        $log = dump2txt($post);
        $this->log("Не удалось создать прикрепленный к теме пост, сообщите об ошибке администратору.\n{$log} ");
      }
    }
    else {
      $err = 1;
      $this->errm("Ошибка базы данных не удалось создать тему.", $err);
    }
  }
  
  
  if ($err) $this->errs[] = __FUNCTION__;
  $this->errm_push($sv->m['fpost']->v_errm);
  
  $ret['topic_id'] = $topic_id;
  
  return $ret;
}


/**
 * Пересчитать посты в топике
 * + последний пост
 * 
 * все считаем с таблицы fposts 
 *
 */
function recount_topic($id = 0) {
  global $sv, $std, $db;

  $sv->load_model('fpost');
  $id = intval($id);
  
  // posts 
  $db->q("SELECT count(*) as posts FROM {$sv->t['fposts']} WHERE topic_id='{$id}' AND new_topic='0'", __FILE__, __LINE__);
  $p = $db->f();
    
  // last_post
  $d = $sv->m['fpost']->get_item_wh("`topic_id`='{$id}' ORDER BY date DESC", 1);
  if ($d) {
    $p['last_post_id'] = $d['id'];
    $p['last_post_time'] = strtotime($d['date']);
    $p['last_poster_id'] = $d['author_id'];
    $p['last_poster_name'] = $d['author_name'];
  }
  else {
    $p['last_post_id'] = 0;
    $p['last_post_time'] = 0;
    $p['last_poster_id'] = 0;
    $p['last_poster_name'] = '';
  }

  // обновляем счетчики у темы       
  $this->update_row($p, $id);
  
}



//eoc
}
?>