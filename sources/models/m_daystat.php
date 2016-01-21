<?php

/*
Дневная статистика по различным объектам


*/

class m_daystat extends class_model {
  
  var $tables = array(
    'daystats' => "
      `id` bigint(20) NOT NULL auto_increment,
      `page_id` varchar(255) NOT NULL default '',
      `object` int(11) not null default '0',
      `date` date default null,
      `val` bigint(11) not null default '0',
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      
      PRIMARY KEY  (`id`),
      KEY (`page_id`, `object`),
      KEY (`page_id`, `date`)
    "
  );
  
  
  var $page_ar = array(
    'views' => 'Просмотры статей',
    'views_video' => "Просмотры видео",
    'views_post' => "Просмотры записей в блогах",
    'views_page' => "Просмотр разделов сайта",
    'comments' => 'Комментарии в статьях',
    
  );
  var $c_date;
     
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['daystats'];
  $this->c_date = date("Y-m-d", $sv->post_time);
  
  $this->init_field(array(
  'name' => 'page_id',
  'title' => 'Категория',
  'type' => 'varchar',
  'len' => 20,  
  'not_null' => 1,
  'show_in' => array('remove', 'default'),  
  'write_in' => array('create', 'edit'),
  //'belongs_to' => array('list' => $this->page_ar),
  'selector' => 1
  ));      
  
  $this->init_field(array(
  'name' => 'object',
  'title' => 'Объект',
  'type' => 'int',  
  'len' => '10',
  'show_in' => array('default',),
  'write_in' => array('create', 'edit'),
  'selector' => 1
  ));    

  $this->init_field(array(
  'name' => 'date',
  'title' => 'Дата',
  'type' => 'date',  
  'show_in' => array('default',),
  'write_in' => array('create', 'edit')
  ));     
  $this->init_field(array(
  'name' => 'val',
  'title' => 'Значение',
  'type' => 'bigint',
  'default' => 0,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create')
  ));       
  
}

function last_v($p) {
  global $sv, $std, $db;
  
  return $p;
}

/**
 * Сбор статы (фиксация события)
 *
 * @param unknown_type $page
 * @param unknown_type $object
 * @return unknown
 */

function update_stats($page, $object, $check_page = 0) {
  global $sv, $std, $db;
  
  $keys = array_keys($this->page_ar);
  
  if ($check_page && !in_array($page, $keys)) {
    $this->log("daystats bad page_id={$page} not in: ".implode(", ", $keys).".");
    return false;
  }
  
  $page = $db->esc($page);
  
  $object = intval($object);
  if ($object<=0) {
    $this->log("daystats bad object={$object}");
    return false;
  }
  
  // сначало пробуем обновить если не обновилось то скорее всего записи нет такой, вставляем
  $db->q("UPDATE {$this->t} SET `val`=`val`+'1' WHERE page_id='{$page}' AND object='{$object}' AND `date`='{$this->c_date}'", __FILE__, __LINE__);
  $af = $db->af();
  if ($af <= 0) {
    $d = $this->get_item_wh("page_id='{$page}' AND object='{$object}' AND `date`='{$this->c_date}'");
    if (!$d) {
      $p = array(
        'page_id' => $page,
        'object' => $object,
        'date' => $this->c_date,
        'val' => 1
      );
      $af = $this->insert_row($p);
    }
    else {
      $this->log("update stats not affected: ".$db->last_query);
      return false;
    }
  }
  
  return $af;
}

//eoc
}
?>