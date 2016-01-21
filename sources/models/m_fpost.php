<?php

/*
Модель сообщения на форуме
*/


class m_fpost extends class_model {

  var $tables = array(
    'fposts' => "    
      `id` BIGINT( 20 ) NOT NULL AUTO_INCREMENT ,
      `title` varchar(255) null,  
      `text` longtext null,  
      `author_id` int(11) not null default '0',      
      `author_name` varchar(255) null,
      `ip` varchar(255) null,
      `date` datetime null,
      
      `topic_id` int(11) null,
      `new_topic` tinyint(1) not null default '0',
      `parent_id` bigint(20) null,
      
      `created_at` DATETIME NULL ,
      `created_by` INT( 11 ) NULL ,
      `updated_at` DATETIME NULL ,
      `updated_by` INT( 11 ) NULL ,
      
      PRIMARY KEY ( `id` ),
      KEY (`topic_id`)
    "
  );
 
  /**
   * @deprecated use $c_topic
   * @var unknown_type
   */
var $last_topic = array();
var $c_topic = false;

/**
 * поля которые присоединяются в списках сообщений
 *
 * @var unknown_type
 */
var $userfields = array('login', 'avatar', 'fposts', 'time_reg', 'group_id', 'last_time');

function __construct() {
  global $sv;  
  
  $this->t = $sv->t['fposts'];  
  
  $this->init_field(array(
  'name' => 'topic_id',
  'title' => 'Тема',
  'type' => 'int',  
  'input' => 'select',
  'belongs_to' => array('table' => 'ftopics', 'field' => 'id', 'return' => 'title'),
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit', 'public_create'),  
  ));    
    
    
   
  $this->init_field(array(
  'name' => 'date',
  'title' => 'Дата',
  'type' => 'datetime',  
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));      
   
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Заголовок поста',
  'type' => 'varchar',  
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create', 'public_create', 'public_edit'),
  'public_search' => 1
  ));    
    
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Текст сообщения',
  'type' => 'text',  
  'len' => '70',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit', 'public_create', 'public_edit'),
  'public_search' => 1
  ));    
  
  $this->init_field(array(
  'name' => 'new_topic',
  'title' => 'Новая тема?',
  'type' => 'boolean',  
  'default' => 0,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));    
    
  $this->init_field(array(
  'name' => 'author_name',
  'title' => 'Имя автора',
  'type' => 'varchar',  
  'len' => '30',
  'show_in' => array('remove'),
  'write_in' => array('edit'),
  'public_search' => 1
  ));    
  
  $this->init_field(array(
  'name' => 'author_id',
  'title' => 'Пользователь',
  'type' => 'int',  
  'input' => 'select', 
  'belongs_to' => array('table' => 'accounts', 'field' => 'id', 'return' => 'login'),
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));      
  
  $this->init_field(array(
  'name' => 'ip',
  'title' => 'IP',
  'type' => 'varchar',  
  'len' => '20',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));  
   
}

// parsers
function parse($d) {
  global $std, $sv;
  

  $d['f_date'] = $std->time->format($d['date'], 0.5, 1);
  
  
  $d['url_answer'] = (isset($sv->m['forum']->c_forum_url) && $sv->m['forum']->c_forum_url) 
    ? $sv->m['forum']->c_forum_url."?answer&topic={$d['topic_id']}&parent_id={$d['id']}" : "forum_url_not_defined";
  $d['url_quote'] = (isset($sv->m['forum']->c_forum_url) && $sv->m['forum']->c_forum_url) 
    ? $sv->m['forum']->c_forum_url."?answer&topic={$d['topic_id']}&parent_id={$d['id']}&quote" : "forum_url_not_defined";
  
  $d['url_edit'] = (isset($sv->m['forum']->c_forum_url) && $sv->m['forum']->c_forum_url) 
    ? $sv->m['forum']->c_forum_url."?editpost&id={$d['id']}&topic={$d['topic_id']}" : "forum_url_not_defined";
    
  $d['url_delete'] = (isset($sv->m['forum']->c_forum_url) && $sv->m['forum']->c_forum_url) 
    ? $sv->m['forum']->c_forum_url."?removepost&id={$d['id']}&topic={$d['topic_id']}" : "forum_url_not_defined";
        
  $d['f_text'] = $std->markitup->bbcode2html($d['text']);
  
  $d['url'] = $this->get_url($d['id'], $d, 1);
  
  // права доступа и кнопки
  $sv->load_model('forum');
  
  $can_edit = 0;
  $can_delete = 0;
  $can_answer = 0;
  
  
  // ANSWER 
  $can_answer = ($sv->m['forum']->is_user && !$sv->m['forum']->is_banned) ? 1 : 0;
  
  // EDIT
  // модераторы могут редатировать (админы тоже модераторы)
  $can_edit = ($sv->m['forum']->is_moderator) ? 1 : $can_edit;
  // автор может редактировать
  $can_edit = ($sv->user['session']['account_id']==$d['author_id'] && $d['author_id']) ? 1 : $can_edit;
  
  // DELETE
  // на удаление теже права что и на редактировани
  $can_delete = $can_edit;
  // если первое сообщение то его нельзя удалить
  $can_delete = ($d['new_topic'])  ? 0 : $can_delete;
  
  
  $d['can_edit'] = $can_edit;
  $d['can_delete'] = $can_delete;
  $d['can_answer'] = $can_answer;
  return $d;
}

function parse_search($d) {
  global $std;
  
  $title = $d['title'];
  $desc = $std->text->truncate(strip_tags($d['text']), 300);  
  $url = $this->get_url($d['id'], $d); // переделать! дополнительно запрашивается и парсится тема
  
  $p = array(
    'title' => $title, 
    'description' => $desc,
    'url' => $url
  );
  return $p;
}

// validations
function v_text($t) {
  global $std;
  
  $t = $std->text->cut($t, 'cut', 'allow');
  $t = trim($t);
  
  if ($t=='') {
    $this->v_err = 1;
    $this->errm("Не указан текст сообщения.", 1);
  }
  
  
  return $t;
}

function v_topic_id($id) {
  global $sv;
  
  $id = intval($id);
  
  $sv->load_model('ftopic');
  $topic = $sv->m['ftopic']->get_item($id);
  if (!$topic) {
    $this->v_err = 1;
    $this->errm("Указанная тема не найдена в базе, возможно она была удалена.", 1);
  }
  
  
  return $id;
}

function v_title($val) {
  global $std;
  
  $val = $std->text->cut($val, 'cut', 'allow');
  $val = trim($val);
  
  return $val;
}

// PRE POST actions
/**
 * вызывается при любом успешном insert_row
 *
 * @param unknown_type $p
 */
function after_insert($p) {
  global $sv;
  
  // обновляем юзера
  $sv->m['account']->update_row(array('fposts' => $sv->user['account']['fposts']+1, 'last_fpost' => $this->last_insert_id), $sv->user['account']['id']);
  
  //обновляем тему
  $sv->load_model('ftopic');
  $topic = $sv->m['ftopic']->get_item($p['topic_id'], 0);
  
  $t = array( 
    'last_post_id'      => $this->last_insert_id,
    'last_poster_id'    => $p['author_id'],
    'last_poster_name'  => $p['author_name'],
    'ip'                => $p['ip'],
    'last_post_time'    => $sv->post_time,
    'posts'             => $topic['posts']+1
  );
  
  if ($p['new_topic']) {
    $t['first_post_id'] = $this->last_insert_id;
  }  
  $sv->m['ftopic']->update_row($t, $topic['id']);
  
  
  // обновляем форум
  $sv->load_model('forum');
  $forum = $sv->m['forum']->get_item($topic['forum_id'], 0);
  
  $f = array(    
    'last_topic_id'   => $topic['id'],
    'last_topic_title'=> $topic['title'],
    'last_post_id'    => $this->last_insert_id,
    'last_post_time'  => $sv->post_time,
    'last_poster_id'  => $p['author_id'],
    'last_poster_name'=> $p['author_name']
  );
  
  if ($p['new_topic']) {
    $f['topics'] = $forum['topics']+1;
  }
  else {
    $f['posts'] = $forum['posts']+1;
  }
  $sv->m['forum']->update_row($f, $forum['id']);
   
}

/**
 * вызывается в конце remove_item
 *
 * @param unknown_type $d
 * @param unknown_type $err
 */
function after_remove($d, $err) {
  global $sv;

  // если пост удален 
  if (!$err) {
    $remove_topic = 0;
    $reset_forum = 0;
    
    // обновляем стату юзера
    $user_id = intval($d['author_id']);
    $user = $sv->m['account']->get_item($user_id, 0);
    $posts = $this->item_list("`author_id`='{$user_id}'", "`date` DESC", 1, 0);
    $post = each($posts['list']);
    $last_post = ($post) ? $post['value']['id'] : 0;
    
    if ($user) {
      $sv->m['account']->update_row(array('fposts' => $user['fposts']-1, 'last_fpost' => $last_post), $sv->user['account']['id']);
    }
      
    //обновляем тему
    $sv->load_model('ftopic');    
    $topic = $sv->m['ftopic']->get_item($d['topic_id'], 0);
    if (!$topic) {
      $sv->view->show_err_page('topic nor found in '.__FUNCTION__."(post{$d['id']})");
    }
    
    // если это был первый пост, то
    if ($d['new_topic']) {
      $remove_topic = 1;
    }
    // если пост не первый
    else {
      // ищем предыдущий пост в этой теме чтобы прописать его параметры
      $posts = $this->item_list("`topic_id`='{$d['topic_id']}'", "`date` DESC", 1, 0);
      $post = each($posts['list']);
           
      // если нашли предыдущий пост в этой теме
      if ($post) {
        $p = $post['value'];
        $new_topic_vars = array( 
          'last_post_id'      => $p['id'],
          'last_post_time'    => strtotime($p['date']),
          'last_poster_id'    => $p['author_id'],
          'last_poster_name'  => $p['author_name'],
          'ip'                => $p['ip'],
          'posts'             => $topic['posts']-1
        );
        
        $sv->m['ftopic']->update_row($new_topic_vars, $topic['id']);
      }
      
    }
    
    // если тема уже удаляется и пост был удален в связи с этим то повтрно тему не надо отправлять на удаление
    $remove_topic = ($sv->act=='ftopics') ? 0 : $remove_topic;

    // если запрошено удаление темы и это не редактор тем
    if ($remove_topic) {
      //проверяем и пробуем удалить все другие посты оставшиеся в теме      // todo уведомление в garbage!!!)
      $r = $sv->m['ftopic']->remove_item($d['topic_id']);            
      
      // $this->v_errm = array_merge($this->v_errm, $sv->m['ftopic']->v_errm); @deprecated
      
      if ($r['affected']) {
        $this->errm("Запись с соответствующей темой удалена.");
      }
      else {
        $this->errm("Не удалось удалить связанную тему.");
      }
      
      // ищем другую последнюю тему в этом форуме, в форум запишем все данные с ее счетчиков
      $topics =  $sv->m['ftopic']->item_list("`forum_id`='{$topic['forum_id']}'", "`last_post_time` DESC", 0);
      $e = each($topics['list']);
      // если другая тема в этом форуме найдена берем ее
      if ($e) {
        $topic = $e['value'];
      }      
      else {
        //если не найдена, значит очищаем счетчики форума
        $topic = array(
          'id' => 0,
          'title' => '',
          'last_post_id' => 0,
          'last_post_time' => 0,
          'last_poster_id' => 0,
          'last_poster_name' => '',                    
        );
        $reset_forum = 1;
      }
    }
    
    // обновляем форум
    $sv->load_model('forum');
    $forum = $sv->m['forum']->get_item($topic['forum_id'], 0);
          
    // если есть новые парамметры темы 
    if (isset($new_topic_vars)) {
      foreach($new_topic_vars as $k=>$v) { $topic[$k] = $v; }        
    }
          
    $new_forum_vars = array(    
      'last_topic_id'   => $topic['id'],
      'last_topic_title'=> $topic['title'],
      'last_post_id'    => $topic['last_post_id'],
      'last_post_time'  => $topic['last_post_time'],
      'last_poster_id'  => $topic['last_poster_id'],
      'last_poster_name'=> $topic['last_poster_name']
    );
    if ($remove_topic) {
      $new_forum_vars['topics'] = $forum['topics']-1;
    }
    else {
      $new_forum_vars['posts'] = $forum['posts']-1;
    }
    if ($reset_forum) {
      $new_forum_vars['topics'] = 0;
      $new_forum_vars['posts'] = 0;
    }
    $sv->m['forum']->update_row($new_forum_vars, $forum['id']);
  }
  
  

}

/**
 * Форма ответа в тему
 * ! вызывается из forum->c_public_answer!!
 * @return unknown
 */
function c_public_create() {  
  global $sv, $std, $db;
  
  $sv->load_model('ftopic');
  // читаем запись топика
  $topic_id = (isset($sv->_get['topic'])) ? intval($sv->_get['topic']) : 0;
  $topic = $sv->m['ftopic']->get_item($topic_id, 1);
  if (!$topic) {
    $sv->view->show_err_page('notfound');
  }
  
  // тайтл страницы
  $sv->vars['p_title'] = $topic['title'];
    
  // родительский пост
  $parent = false;
  if (isset($sv->_get['parent_id'])) {
    $parent = $this->get_item($sv->_get['parent_id'], 1);
    if ($parent['topic_id']!=$topic_id) {
      $parent = false;
    }
  } 
  
  // данные по умолчанию в форму
  $this->d['title'] = ($parent) ? "Re: ".$parent['title'] : "Re: ".$topic['title'];
  $this->d['text'] = ($parent && isset($sv->_get['quote'])) ? "[quote]{$parent['text']}[/quote]" : "";
  
  // список последних постов
  $this->list_before();
  $ret['last'] = $this->item_list("`topic_id`='{$topic['id']}'", "`date` DESC", 10, 1);
  $ret['last']['list'] = $this->list_after($ret['last']['list']);
  
  $ret['topic'] = $topic;
  $ret['parent'] = $parent;
  
  // submit check 
  $this->code = "public_create";
  $this->init_controllers();
  
  // submit
  $s = $this->init_submit(1);
  
  $ret['s'] = $s;
  
  return $ret;
}

function sc_public_create() {
   global $sv, $std, $db;
   
   $err = 0;

  // validation
  if (!$err) {
    $p = $this->validate_active_fields(1);   
  }
  
  // validation errors
  $err = ($this->v_err) ? true : $err;
  
  // inserting if no errors
  if (!$err) {    
    // передаем управление в функцию
    $this->create_post($p); 
    $err = ($this->err('create_post')) ? 1 : $err;
  }  
  
  if ($err) $this->errs[] = __FUNCTION__;  
  
  $ret['v'] = $this->vals;  
  return $ret;  
  
}


/**
 * Форма редактирования сообщения в теме
 * ! вызывается из forum->c_public_editpost!!
 * @return unknown
 */
function c_public_edit() {  
  global $sv, $std, $db;
  
  $id = (isset($sv->_get['id'])) ? intval($sv->_get['id']) : 0;
  $post = $this->get_item($id, 1);
  if (!$post) {
    // пост не найден
    $sv->view->show_err_page('notfound');
  }
  
  // если нет прав
  if (!$post['can_edit']) {
    $sv->view->show_err_page('forbidden');
  }
  
  // читаем запись топика
  $sv->load_model('ftopic');
  $topic_id = (isset($sv->_get['topic'])) ? intval($sv->_get['topic']) : 0;
  $this->c_topic = $sv->m['ftopic']->get_item($topic_id, 1);

  // если топик не найден
  if (!$this->c_topic) {    
    $sv->view->show_err_page('notfound');
  }
  // или если не соответствует
  elseif ($post['topic_id']!=$this->c_topic['id']) {
    $sv->view->show_err_page('badrequest');
  }

  // доп обработка первого поста в теме - загловок и описание синхронизируются с темой
  if ($post['new_topic']) {
    $this->vals['title'] = $this->c_topic['title'];
    $this->vals['description'] = $this->c_topic['description'];
  }

  // тайтл страницы
  $sv->vars['p_title'] = $this->c_topic['title'];
  
    
  // родительский пост
  $parent = false;
  if (isset($sv->_get['parent_id'])) {
    $parent = $this->get_item($sv->_get['parent_id'], 1);
    if ($parent['topic_id']!=$topic_id) {
      $parent = false;
    }
  } 
  
  // список последних постов
  $this->list_before();
  $ret['last'] = $this->item_list("`topic_id`='{$this->c_topic['id']}'", "`date` DESC", 10, 1);
  $ret['last']['list'] = $this->list_after($ret['last']['list']);
  
  $ret['topic'] = $this->c_topic;
  $ret['parent'] = $parent;
  $ret['d'] = $post;
  
  // submit check 
  $this->code = "public_edit";
  $this->init_controllers();
  $s = $this->init_submit(1);
  $ret['s'] = $s;
  
  return $ret;
}

function sc_public_edit() {
  global $sv, $std, $db;
   
  $err = 0;
  $ret = array();
  
  // validation
  if (!$err) {
    $p = $this->validate_active_fields(1);   
  }
  
  // validation errors
  $err = ($this->v_err) ? true : $err;
  
  // updating if no errors
  if (!$err) {  
    if ($this->update_row($p, $this->d['id'])) {
      $this->errm("Сообщение успешно отредактировано.");
      $this->errm("<A href='{$this->c_topic['url']}'>Вернуться к теме</a>");
      
      // доп обработка первого сообщения темы, обновляем название темы и описание
      if ($this->d['new_topic']) {
        $sv->m['ftopic']->update_row(array('title' => $p['title'], 'description' => $sv->m['ftopic']->validate_field('description', $this->n['description'], 1)), $this->c_topic['id']);
      }
    }
    else {
      $this->errm("Сообщение не обновлено, текст не был изменен.");
    }
  }  
  
  
  // доп обработка первого сообщения темы, выводим заголовок и описание темы, вместо заголовка поста
  if ($this->d['new_topic']) {
    $this->c_topic = $sv->m['ftopic']->get_item($this->d['topic_id'], 1);
    $this->vals['title'] = $this->c_topic['title'];
    $this->vals['description'] = $this->c_topic['description'];
  }
      
  if ($err) $this->errs[] = __FUNCTION__;
  
  return $ret;  
  
}

/**
 * Спписок постов для вывода темы, форум должен быть инициирован
 *
 * @param unknown_type $topic
 * @return unknown
 */
function list_for_topic($topic) {
  global $sv, $db;
  
  $this->c_topic = $topic;

  
  // первое
  $first = $this->get_item($topic['first_post_id'], 1);
  $first['user'] = $sv->m['account']->get_item($first['author_id'], 1);
  $first['first'] = 1;
    
  // подготовка
  $this->list_before();
  
  // список (все кроме первого)
  $url = $sv->m['forum']->c_forum_url."?topic={$topic['id']}";
  $ret = $this->item_list_pls("`topic_id`='{$topic['id']}' AND {$this->t}.id<>'{$topic['first_post_id']}'", "{$this->t}.date ASC", $this->per_page, 1, $sv->_get['page'], $url."&page=");

  // постобработка
  $ret['list'] = $this->list_after($ret['list']);

  $ret['d'] = $topic;
  $ret['first'] = $first;
  $ret['url'] = $url;
    
  return $ret;
}

/**
 * список сообщений пользователя
 *
 * @param unknown_type $user
 * @param unknown_type $mode (all|topics)
 * @return unknown
 */
function list_for_userposts($user, $mode = 'all') {
  global $sv;
  
  // подготовка
  $this->list_before();
  
  // список
  $wh = ($mode=='all') ? "" : " AND {$this->t}.new_topic='1'";
  $url = $sv->m['forum']->root_url."?userposts={$user['id']}&mode={$mode}";
  $ret = $this->item_list_pls("{$this->t}.author_id='{$user['id']}'{$wh}", "{$this->t}.date ASC", $this->per_page, 1, $sv->_get['page'], $url."&page=");

  // постобработка
  $ret['list'] = $this->list_after($ret['list']);
  
  // обновляем счетчик сообщений юзера
  if ($mode=='all') {
    $sv->m['account']->update_row(array('fposts' => $ret['pl']['size']), $user['id']);
  }
  
  $ret['mode'] = $mode;
  $ret['user'] = $user;
    
  return $ret;
}

/**
 * функция вызываемая перед генерацией списков
 * чтобы подготовить джойны по списку полей
 */
function list_before() {
  global $sv;
   
  // подготовка пользовательских данных
  $sv->load_model('account');
  $fields = array();
  foreach ($this->userfields as $fn) {
  	$fields[] = "a.{$fn}";
  }
  
  $this->joins = array(
    'f' => ", ".implode(", ", $fields), 
    'j' => "LEFT JOIN {$sv->t['accounts']} a ON ({$this->t}.author_id=a.id)");

  return true;
}

/**
 * функция вызываемая после генерации списков, чтобы добавить отдельный массив с полями юзера
 *
 * @param array $list
 * @return array $list
 */
function list_after($list) {
  global $sv;
  
  // обрабатываем пользовательские поля в сообщениях
  foreach($list as $k=>$d) {
    $user = array();
    foreach ($this->userfields as $fn) {
      $user[$fn] = $d[$fn];
    }
    $list[$k]['user'] = $sv->m['account']->parse($user);
  }
  
  return $list;
}
/**
 * Самостоятельная функция создания постов
 *
 * @param $title & &text $topic_id  = ALREADY VALIDATED and no errors
 * 
 *  
 */
function create_post($post) {
  global $sv, $std, $db;
  
  $err = 0;
  
  // проверка параметров (которые уже должны быть отвалидированы)
  $rq = array('title', 'text', 'topic_id');
  foreach($rq as $k) {
    if (!isset($post[$k])) {
      die("не хватает параметра <b>{$k}</b> в ".__FILE__." ".__FUNCTION__);
    }
  }

  $sv->load_model('ftopic');
  
  $topic = $sv->m['ftopic']->get_item($post['topic_id'], 1);
  if (!$topic) {
    $err = 1;
    $this->errm("Тема в которой необходимо создать пост не найдена (2)", $err);
  }

  
  if (!$err && $topic['status_id']==2) {
    $err = 1;
    $this->errm("Тема закрыта, добавление новых сообщений запрещено.", $err);
  }
   
  // дополнительные параметры поста
  $post['new_topic'] = 0;
  $post['date'] = $sv->date_time;
  
  // общие данные
  $post['author_id']    = $sv->user['session']['account_id'];
  $post['author_name']  = $sv->user['session']['login'];  
  $post['ip'] = $sv->ip;
    
  // обьединение стеков ошибок
  $err = ($this->v_err) ? 1 : $err;
  
  // проверка существования такого постав в выбранной теме
  if (!$err) {
    $tmp = $this->get_item_wh("`topic_id`='".$db->esc($post['topic_id'])."' AND `text`='".$db->esc($post['text'])."'");
    if ($tmp) {
      $err = 1;
      $this->errm("Сообщение с таким текстом уже существует в выбранной теме.", $err);
    }
  }
  
  // если нет ошибок создаем запись
  if (!$err) {    
    if ($this->insert_row($post)) {
      $post['id'] = $this->last_insert_id;
      
      $this->errm("Ваше сообщение успешно добавлено.");
      $this->errm("<A href='{$topic['url']}'>Вернуться к теме</a>");   
        
      //обнуляем текущие значения
      foreach($this->vals as $k=>$v) {
        $this->vals[$k] = '';
      }
     
    }
    else {
      $err = 1;
      $this->errm("Ошибка базы данных не удалось добавить сообщение.", $err);
    }
  }
  
  if ($err) $this->errs[] = __FUNCTION__;  
  $ret = ($err) ? 0 : 1;
  return $ret;
}

// STD
/**
 * Получение полного и правильного урла сообщения по идентификатору
 * @param unknown_type $id - пост
 * @param unknown_type $d - пост
 * @param unknown_type $use_c_topic - использовать this->c_topic
 */
function get_url($id = 0, $d = false, $use_c_topic = 0) {
  global $sv, $db;
  $id = ($d) ? $d['id'] : intval($id);
  $topic_url = '';
  
  // если использовать предустановленную тему то берем данные оттуда
  if ($use_c_topic && $this->c_topic) {
    $topic_url = $this->c_topic['url'];
  }
  // иначе
  else{    
    if(!$d) {
      $d = $this->get_item($id, 0);
    }
    if ($d) {
      $sv->load_model('ftopic');
      $topic = $sv->m['ftopic']->get_item($d['topic_id'], 1);
      $topic_url = $topic['url'];
    }
  }
  $ret = ($topic_url) ? $topic_url."&post={$id}#post{$id}" : '';
  
  return $ret;
}

//eoc
}
?>