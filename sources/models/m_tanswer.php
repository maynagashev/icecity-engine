<?php

/*

ответы тестирования - викторины

*/
class m_tanswer extends class_model {
   
  var $tables = array(
    'tanswers' => "
      `id` bigint(20) NOT NULL auto_increment,
      `title` varchar(255) null,
      `qid` int(11) not null default '0',
      `text` text null,
      `views` int(11) not null default '0',
      `submits` int(11) not null default '0',
      `right` tinyint(1) not null default '0',
          
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      
      PRIMARY KEY  (`id`)    
    "
  );

   
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['tanswers'];

  $this->init_field(array(
  'name' => 'title',
  'title' => 'Текст ответа',
  'type' => 'varchar',  
  'len' => '70',
  'show_in' => array('remove', 'default'),
  'write_in' => array('create', 'edit')
  ));      


  $this->init_field(array(
  'name' => 'qid',
  'title' => 'Идентификатор вопроса',
  'type' => 'int',  
  'show_in' => array('remove'),
  'write_in' => array(),
  'belongs_to' => array('table' => 'tquestions', 'field' => 'id', 'return' => 'title', 'mode' => 'slave')
  ));       

  $this->init_field(array(
  'name' => 'text',
  'title' => 'Текст сообщения при выборе данного ответа',
  'type' => 'text',   
  'len'  => '70',
  'show_in' => array('remove', 'default'),
  'write_in' => array('create', 'edit'), 
  ));    
  
  /*
  $this->init_field(array(
  'name' => 'views',
  'title' => 'Показы',
  'type' => 'int',   
  'len'  => '11',
  'show_in' => array('remove', 'default'),
  'write_in' => array('edit'), 
  ));    
  */
  
  $this->init_field(array(
  'name' => 'submits',
  'title' => 'Клики',
  'type' => 'int',   
  'len'  => '11',
  'show_in' => array('remove', 'default'),
  'write_in' => array('edit'), 
  ));    
  

  $this->init_field(array(
  'name' => 'right',
  'title' => 'Правильный ответ?',
  'type' => 'tinyint',   
  'input' => 'boolean',
  'show_in' => array('remove', 'default'),
  'write_in' => array('create', 'edit'), 
  ));    

  
}
function parse($d) {
  global $sv, $std, $db;
  
  $d['url'] = "{$sv->vars['site_url']}victorina/submit/?id={$d['qid']}&answer={$d['id']}";
  
  return $d;
}
function df_text($t) {
  return "<div style='text-align:left;'>{$t}</div>";
}


function get_answers($qid) {
  global $sv, $std, $db;
  
  $qid = intval($qid);
  
  $ar = $this->item_list("`qid`='{$qid}'", "`title` asc", 0, 1);
  $right = 0;
  foreach($ar['list'] as $d) {
    if ($d['right']) {
      $right++;
    }
  }
  
  $ar['right'] = $right;
  return $ar;
}

/**
 * Возвращает массив ответов выбранных в форме
 *
 * @param unknown_type $qid
 * @param unknown_type $answers - массив идентификаторов ответов
 * @return unknown
 */
function parse_results($qid, $answers, $update = 0) {
  global $sv, $std, $db;
  
  $qid = intval($qid);
  
  $ar = $this->item_list("`qid`='{$qid}'", "`title` asc", 0, 1);
  
  $ret = array();
  foreach($ar['list'] as $d) {
    if (in_array($d['id'], $answers)) {
      if ($update) {
        $this->update_row(array('submits'=>$d['submits']+1), $d['id']);
      }
      $ret[] = $d;
    }
  }
  
  
  return $ret;
}
//eoc
}

?>