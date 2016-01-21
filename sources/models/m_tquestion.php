<?php

/*
вопросы тетсирования - викторины
*/
class m_tquestion extends class_model {
   
  var $tables = array(
    'tquestions' => "
      `id` bigint(20) NOT NULL auto_increment,
      `title` varchar(255) null,
      `cat` varchar(255) null,
      `text` text null,
      `views` int(11) not null default '0',
      `active` tinyint(1) not null default '0',
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      
      PRIMARY KEY  (`id`),
      KEY (`cat`),
      KEY (`active`)    
    "
  );

   
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['tquestions'];

  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название вопроса',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '70',
  'show_in' => array('remove', 'default'),
  'write_in' => array('create', 'edit'),
  'unique' => 1
  ));      


  $this->init_field(array(
  'name' => 'cat',
  'title' => 'Категория',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '70',
  'input' => 'select',
  'show_in' => array('remove', 'default'),
  'write_in' => array('create', 'edit'), 
  'belongs_to' => array('table' => 'tcats', 'field' => 'short', 'return' => 'title')
  ));       

  $this->init_field(array(
  'name' => 'text',
  'title' => 'Текст вопроса',
  'type' => 'text',   
  'len'  => '70',
  'show_in' => array('remove', 'default'),
  'write_in' => array('create', 'edit'), 
  ));    
  

    
  $this->init_field(array(
  'name' => 'views',
  'title' => 'Просмотры',
  'type' => 'int',   
  'len'  => '11',
  'show_in' => array('remove', 'default'),
  'write_in' => array('edit'), 
  ));    
  
  $this->init_field(array(
  'name' => 'answers',
  'title' => 'Варианты ответов',
  'virtual' => 'id',
  'show_in' => array('edit')
  ));    


  $this->init_field(array(
  'name' => 'active',
  'title' => 'Активный',
  'type' => 'int',
  'input' => 'boolean',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));        

  
}


function v_text($t) {
  global $std;
  
  
  $t = $std->text->cut($t, 'cut', 'allow');
  $t = trim($t);
  
  if ($t=='') {
    $this->v_err = 1;
    $this->v_errm[] = "Не указан текст вопроса.";
  }
    
  return $t;
}

function last_v($p) {
  global $sv, $std, $db;
  
  return $p;
}

// callbacks
function vcb_answers($id) {  
  return $this->slave_box('tanswer', 'qid', 'tanswers', $id);
}

function df_text($t) {
  return "<div style='text-align:left;'>{$t}</div>";
}


// parsers
function parse($d) {
  global $sv, $std, $db;
  
  $sv->load_model('tcat');
  $cat = $db->esc($d['cat']);
  $d['catd'] = $sv->m['tcat']->get_item_wh("`short`='{$cat}'", 0, 0);

  return $d;
}

//  pre/post
function garbage_collector($d) {  
  global $sv, $std, $db;
  
  $err = false;
  $errm = array();  
  
  $db->q("DELETE FROM {$sv->t['tanswers']} WHERE `qid`='{$d['id']}'", __FILE__, __LINE__);
  $errm[] = "Удалено ответов к данному вопросу: ". $db->af().".";
  
  return array('err'=>$err, 'errm'=>$errm);
}

function after_update($d, $p, $err) {
global $db, $sv;

  if (!$err && $p['active']==1) {  
    $db->q("UPDATE {$this->t} SET `active`='0' WHERE id<>'$this->current_record'", __FILE__, __LINE__);  
  }

}

// stuff 
/**
 * Показ вопроса 
 *
 * @param unknown_type $id
 * @param unknown_type $d
 * @return unknown
 */

function show_question($id, $d=false) {
  global $sv, $std, $db;
  
  if (!$d) {
    $d = $this->get_item($id, 1);
    if (!$d) {
      return false;
    } 
  }
  
  $ret['d'] = $d;
  
  $sv->load_model('tanswer');
  $ret['answers'] = $sv->m['tanswer']->get_answers($d['id']);
  
  $this->update_row(array('views'=>$d['views']+1), $d['id']);

  return $ret;
}


// PUBLIC CONTROLLERS
function public_active() {
  global $sv, $std, $db;
  
  $d = $this->get_item_wh("`active`='1'", 1);  
  $ret =(!$d) ? false : $this->show_question($d['id'], $d);
  
  return $ret;
}



/**
 * выбранный вопрос
 *
 * @return unknown
 */

function public_question() {
  global $sv, $std, $db;

  $id = (isset($sv->_get['id'])) ? intval($sv->_get['id']) : 0;
  if ($id<=0) {
    die("Неверно заданы параметры.");
  }
  
  return $this->show_question($id);
}


/**
 * Список вопрос по выбранной категории
 *
 * @return unknown
 */
function public_cat() {
  global $sv, $std, $db;
  
  $id = (isset($sv->_get['id'])) ? $sv->_get['id'] : "";
  $id = preg_replace("#[^a-z0-9\_]#si", "", $id);
  $short = $db->esc($id);
  
  $sv->load_model('tcat');
  $cat = $sv->m['tcat']->get_item_wh("`short`='{$short}'", 1);
  if ($cat===false) {
    die("Неверно заданы параметры, выбранная рубрика викторины не найдена.");
  }
  $p = (isset($sv->_get['p'])) ? intval($sv->_get['p']) : 1;
  $ret = $this->item_list_pl("`cat`='{$short}'", "title ASC", 15, $p, 1, $sv->vars['url']."cat/?id={$id}&p=");
  
  $ret['cat'] = $cat;

  return $ret;
}



/**
 * результат ответа
 *
 * @return unknown
 */

function public_submit() {
  global $sv, $std, $db;

  $err = 0;
  $errm = array();
  $n = (isset($sv->_post['new'])) ? $sv->_post['new'] : array();
  
  // $_GET fix
  if (isset($sv->_get['id']) && isset($sv->_get['answer'])) {
    $n['qid'] = intval($sv->_get['id']);
    $n['answer'] = array(intval($sv->_get['answer']));
  }
  
  if (!isset($n['qid']) || !isset($n['answer']) || !is_array($n['answer'])) {
    $err = 1;
    $errm[] = "Неверно заданы параметры.";
  }
  
  if (!$err) {
    $qid = intval($n['qid']);
    $d = $this->get_item($qid, 1);
    if (!$d) {
      $err = 1;
      $errm[] = "Вопрос не найден, неверно заданый параметры.";
    } 
  }  
  $ret['d'] = $d;
  

  $sv->load_model('tanswer');
  $ret['answers'] = $sv->m['tanswer']->parse_results($d['id'], $n['answer'], 1);
  
  $ret['next'] = $this->get_next_id($d['id']);
  return $ret;
}



/**
 * архив, список категорий с последними вопросам
 *
 * @return unknown
 */

function public_archive() {
  global $sv, $std, $db;

  $sv->load_model('tcat');
  $cat = $sv->m['tcat']->item_list("", "`title` ASC", 0, 1);
  
  $cats = array();
  foreach($cat['list'] as $d) {
    $short = $db->esc($d['short']);
    $d['qs'] = $this->item_list("`cat`='{$short}'", "id DESC", 3, 0);    
    $cats[] = $d;
  }
  
  $ret['cats'] = $cats;
  return $ret;
}


/**
 * отправка на email
 *
 * @return unknown
 */

function public_send() {
  global $sv, $std, $db;

  $err = 0;
  $errm = array();
  $vals = array();
  $sv->load_model('tanswer');
  
  $id = (isset($sv->_get['id'])) ? intval($sv->_get['id']) : 0;
  $d = $this->get_item($id, 1);
  if ($d===false) {
    die("Неверно заданы параметры, вопрос не найден.");
  }
  else {
    $d['answers'] = $sv->m['tanswer']->item_list("`qid`='{$d['id']}'", "title ASC", 0, 1);
  }
  
  $n = (isset($sv->_post['new'])) ? $sv->_post['new'] : array();  
  $submited = (isset($n['email']) && isset($n['from']) && isset($n['text'])) ? 1 : 0;
  
  if ($submited) {
    
    foreach ($n as $k=>$v) {
      $n[$k] = $std->text->cut($v, 'cut', 'mstrip');
      $vals[$k] = $std->text->cut($n[$k], 'replace', 'replace');
    }
    $n['text'] = trim($n['text']);
    
    if (!$std->text->v_email($n['email'])) {
      $err = 1;
      $errm[] = "Почтовый адрес получателя указан неверно.";
    }
    
    if (!$std->text->v_email($n['from'])) {
      $err = 1;
      $errm[] = "Обратный адрес email (ваш) указан не верно.";
    }
 
    if (!$err) {
      if (!$this->send_question($n['email'], $n['from'], $n['text'], $d)) {
        $err = 1;
        $errm[] = "Не удалось отправить вопрос на адрес <b>{$n['email']}</b>, 
        возможно почтовый сервер недоступен в данный момент, попробуйте повторить операцию позднее.";
      }
      else {
        $next = $this->get_next_id($d['id']);
        $errm[] = "Вопрос успешно отправлен на адрес <b>{$n['email']}</b>";
        if ($next>0) {
          $errm[] = "<a href='{$sv->vars['url']}question/?id={$next}'>Перейти к следующему вопросу</a> &rarr;";
        }
      }
    }
    
  }
  else {
    
    
    
    
  }
  
  
  
  $ret['d'] = $d;
  $ret['submited']  = $submited;
  $ret['vals'] = $vals;
  $ret['err'] = $err;
  $ret['err_box'] = $std->err_box($err, $errm);
  return $ret;
}


function send_question($email, $from, $text='', $q) {
  global $sv, $std, $db;
  
$text = ($text=='') ? ":" : ", комментарий к письму:
{$text}";

$tr = array(); $i=0;
foreach($q['answers']['list'] as $d) {$i++;
  $tr[] = "{$i}. {$d['title']}\n      {$d['url']}";
}
$y = date("Y", $sv->post_time);
  
$body = "Доброго времени суток!

Пользователь с адресом lifesup@gmail.com хочет чтобы вы приняли участие в интеллектуальной викторине
на сайте \"Заполярный Вестник\" <http://norilsk-zv.ru/victorina/>{$text}

---

{$q['catd']['title']}: {$q['title']}
{$q['text']}

".implode("\n\n", $tr)."

Пройдите по ссылке соответствующей верному, на ваш взгляд, ответу и узнайте правильный он или нет.

--- 

Архив вопросов викторины: http://norilsk-zv.ru/victorina/archive/

«Заполярный вестник», 663300, Норильск, Комсомольская 33а, (3919)465900, e-mail: zv@nrd.ru
Copyright {$y} «Заполярный вестник»
";

$sent = 0;

$name = (preg_match("#^([^@]+)@#si", $from, $m)) ? $m[1] : "anonymous";
$std->mail->from_name = ucfirst($m[1]);
$std->mail->from_address = $from;

$body = str_replace("\r\n", "\n", $body);

$sent = $std->mail->send($email, "Заполярный вестник - ВИКТОРИНА", $body);

return $sent;
}

function get_next_id($id) {
  global $sv, $std, $db;
  
  $id = intval($id);
    
  //next
  $db->q("SELECT id FROM {$this->t} WHERE id>'{$id}' ORDER BY id ASC", __FILE__, __LINE__);
  $s = $db->nr();
  if ($s <=0) {
    $db->q("SELECT id FROM {$this->t} WHERE id<'{$id}' ORDER BY id ASC", __FILE__, __LINE__);
    $s = $db->nr();
  }
  if ($s>0) {
    $d = $db->f();
    $ret = $d['id'];
  }
  else {
    $ret = 0;
  }
   
  return $ret;
}


function block() {
  global $sv, $std, $db;
  
  
  $ar = $this->item_list("", "id DESC", 5, 0);
  $i = array_rand($ar['list']);
  
  if (!isset($ar['list'][$i])) {
    return false;
  }
  
  $d = $ar['list'][$i];
  
  $sv->load_model('tanswer');
  $d['answers'] = $sv->m['tanswer']->get_answers($d['id']);
  
 
  
  return $d;
}
//eoc
}

?>