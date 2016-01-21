<?php

/*
Результат поискового запроса
*/
class m_searchresult extends class_model {
  var $tables = array(
    'searchresults' => "
      `id` bigint(20) NOT NULL auto_increment,
      `sid` varchar(255) NOT NULL default '',      
      `model` varchar(255) not null default '',
      `title` varchar(255) default NULL,
      `description` text default NULL,
      `url` varchar(255) default null,
      `relevance` int(11) not null default '0',
      `time` int(11) not null default '0',
      `date` datetime null,
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      
      PRIMARY KEY  (`id`),
      KEY (`sid`, `relevance`, `date`),
      KEY (`time`)
    "
  );
  
  var $c_search = false;
  var $per_page = 30;
  
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['searchresults'];
 
  $this->init_field(array(
  'name' => 'time',
  'title' => 'Время (unix)',
  'type' => 'int',
  'inut' => 'time',
  'show_in' => array('remove', 'default'),  
  'write_in' => array('edit')
  )); 
    
  $this->init_field(array(
  'name' => 'sid',
  'title' => 'Идентификатор сессии поиска',
  'type' => 'varchar',
  'not_null' => 1,  
  'belongs_to' => array('table' => 'searches', 'field' => 'id', 'return' => 'query'),
  'show_in' => array('remove', 'default'),  
  'write_in' => array('create', 'edit')
  ));      
  
  $this->init_field(array(
  'name' => 'model',
  'title' => 'Модель',
  'type' => 'varchar',  
  'len' => '70',
  'show_in' => array('default',),
  'write_in' => array('create', 'edit')
  ));    
    
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Заголовок',
  'type' => 'varchar',  
  'len' => '70',
  'show_in' => array('default',),
  'write_in' => array('create', 'edit')
  ));      
  
    
  $this->init_field(array(
  'name' => 'description',
  'title' => 'Описание',
  'type' => 'text',  
  'len' => '70',
  'show_in' => array('default',),
  'write_in' => array('create', 'edit')
  ));      
    
    
  $this->init_field(array(
  'name' => 'url',
  'title' => 'Ссылка',
  'type' => 'varchar',  
  'len' => '70',
  'show_in' => array('default',),
  'write_in' => array('create', 'edit')
  ));        
  
  $this->init_field(array(
  'name' => 'date',
  'title' => 'Дата материала',
  'type' => 'datetime',  
  'show_in' => array('default',),
  'write_in' => array('create', 'edit')
  ));        

   
  $this->init_field(array(
  'name' => 'relevance',
  'title' => 'Релевантность',
  'type' => 'int',  
  'len' => '10',
  'show_in' => array('default',),
  'write_in' => array('create', 'edit')
  ));        
  
}

function c_public_list() {
  global $sv;
  $s = &$this->c_search;
  if (!$s) return false;
  
  $ret = $this->item_list_pls("`sid`='{$s['id']}'", "`relevance` ASC, `date` DESC", $this->per_page, 1, 0, $s['url_results']."&page=");
  
  return $ret;
}
/**
 * сбор результатов по записи поиска
 *
 */
function process_search($search) {
  global $sv;

  $err = 0;
    
  $models_ar = explode(",", $search['models']);

  // собираем списки sql запросов по каждой модели
  $sqls = array();
  foreach($models_ar as $model) {
    $sv->load_model($model);
    
    // если в модели отсутствует метода парсинга результатов - пропускаем 
    if (!method_exists($sv->m[$model], 'parse_search')) {
      die("В модели \"{$model}\" отсутствует метод для парсинга результатов поиска \"{$model}-&gt;parse_search(\$d)\".");
      continue;
    }
    else {
      $m_sqls = $sv->m[$model]->get_public_search_sql($search);
      
      // если отсутствуют поля для поиска у модели
      if (count($m_sqls)<=0) {
        die("В модели \"{$model}\" отсутствует поля для публичного поиска \"public_search=1\".");
        continue;
      }
      else {
        $sqls[$model] = $m_sqls;  
      }
    }
  }
  
  $inserted = $this->execute_search_sqls($search, $sqls);
  
  if ($err) $this->errs[] = __FUNCTION__;
  
  return $inserted;
}

function execute_search_sqls($search, $sqls) {
  global $sv, $db;
  
  // удаляем предыдущие результаты по указанному запросу
  $this->remove_rows_wh("`sid`='".$db->esc($search['id'])."'");
  
  $i = 0; 
  $inserted = 0;

  foreach($sqls as $model => $qs) { $i++;    
    $results = array();
    foreach($qs as $k => $q) { 
      $relevance = ($k*100)+$i;
      //t("{$relevance}: ".$q);
      
      //запрашиваем записи у выбранной модели по выбранному алгоритму с расчитанной релевантностью      
      $db->q($q, __FILE__, __LINE__);
      while($d = $db->f()) {
        $d = $sv->m[$model]->parse($d);
        $id = $sv->m[$model]->primary_field;
        $d['relevance'] = $relevance;
        if (!isset($results[$d[$id]])) {
          $results[$d[$id]] = $d;
        } 
      }
    }
    
    // записываем в базу
    foreach($results as $d) {
      $r = $sv->m[$model]->parse_search($d);
      $p = array(
        'sid' => $search['id'],
        'time' => $sv->post_time,
        'model' => $model,
        'relevance' => $d['relevance'],
        'title' => $r['title'],
        'description' => $r['description'],
        'url' => $r['url'],
        'date' => $d['created_at'] 
      );
      $this->insert_row($p);
      $inserted++;
    }
  }
  
  return $inserted;
}
//eoc
}
?>