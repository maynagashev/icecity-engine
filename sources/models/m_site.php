<?php

class m_site extends class_model {

  var $title = "Сайты в каталоге";
  
  var $tables = array(
    'sites' => "
      `id` bigint(20) NOT NULL auto_increment,
      
      `title` varchar(255) default NULL,
      `url` varchar(255) NOT NULL default '',
      `description` varchar(255) NOT NULL default '0',
      `text` text null,
      
      `cat_id` varchar(255) null,
      `status_id` tinyint(1) not null default '1',
      `views` int(11) not null default '0',
      `clicks` int(11) not null default '0',
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      
      PRIMARY KEY  (`id`),
      KEY (`cat_id`)
    "
  );

  
  var $cats = array(
    'towns' => 'Сайты районов и городов-спутников',
    'special' => 'Узкоспециализированные',    
    'newspapapers' => 'Газеты и информационные агентства',
    'transport' => 'Грузоперевозки, транспорт',
    'homepages' => 'Домашние страницы',
    'services' => 'Интернет-сервисы',
    'nn' => 'Норильский никель',
    'shops' => 'Магазины',
    'bbs' => 'Объявления',
    'politics' => 'Политические',
    'media' => 'Радио и телевидение',
    'sport' => 'Спорт и активный отдых',
    'telecoms' => 'Телекоммуникации',
    'uslugi' => 'Услуги',
    'education' => 'Учебные заведения',
    'etc' => "Другое"
  );
  var $status_ar = array(
    0 => 'Черновик',
    1 => 'Опубликован',
    2 => 'Отключен'
  );
  
    
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['sites'];
  $this->per_page = 150;    
    
  
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название',
  'type' => 'varchar',
  'len' => '40',
  'default' => '',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'unique' => 1
  ));  
  
  $this->init_field(array(
  'name' => 'description',
  'title' => 'Краткое описание',
  'type' => 'varchar',
  'len' => '40',
  'default' => '',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));  
    
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Текст',
  'type' => 'text',
  'len' => '70',
  'default' => '',
  'show_in' => array( 'remove'),
  'write_in' => array('create', 'edit')
  ));  
    
    
  $this->init_field(array(
  'name' => 'url',
  'title' => 'Адрес',
  'type' => 'varchar',  
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'unique' => 1
  ));    
  
  $this->init_field(array(
  'name' => 'cat_id',
  'title' => 'Категория',
  'type' => 'varchar',
  'input' => 'select',
  
  'belongs_to' => array('list' => $this->cats),
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')  
  ));  

  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Статус',
  'type' => 'varchar',
  'input' => 'select',
  'default' => 1,
  'belongs_to' => array('list' => $this->status_ar),
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')  
  ));  
  
  $this->init_field(array(
  'name' => 'views',
  'title' => 'Показы',
  'type' => 'int',
  'len' => '10',
  'show_in' => array('default', 'remove'),
  'write_in' => array( 'edit')  
  ));    
  
  $this->init_field(array(
  'name' => 'clicks',
  'title' => 'Клики',
  'type' => 'int',
  'len' => '10',
  'show_in' => array('default', 'remove'),
  'write_in' => array( 'edit')  
  ));   
  
}


function parse($d) {
  global $sv; 
  
  $d['url_goto'] = "/catalog/goto/?id={$d['id']}";
  $d['url_details'] = "/catalog/details/?id={$d['id']}";
  
  
  return $d;
}

}

?>