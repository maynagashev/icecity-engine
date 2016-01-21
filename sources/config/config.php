<?php
/**
 * Настроечный файл для разарботчиков.
 */

$sv_vars = array(
  'site_url' => "/",
  'site_title' => "SV->vars['site_title']",
  'p_title' => "",
  'styles' => array(),
  'js' => array(),
  'version' => "2.4", 
  'location' => ''
);

/**
 * Различные меню
 * 1) admin_menu - главное админское меню
 * 
 */

$sv_menu = array(
  'admin_main' => array(
                        'pages' => array('title' => 'Структура сайта', 'act' => 'pages'),
                        'editors' =>  array('title' => 'Редакторы', 'act' => 'news'),
                        'users' =>  array('title' => 'Пользователи', 'act' => 'admin'),
                        'options' => array('title' => 'Настройки', 'act' => 'config'),
                        //'afisha' => array('title' => 'Афиша', 'act' => 'halls'),
                        //'blogs' => array('title' => "Блоги", 'act' => 'blogs'),
                        //'victorina' => array('title' => "Викторина", 'act' => 'tquestions'),    
                        //'shop'    => array('title' => 'Магазин', 'act' => 'products'),
                        //'forum'   => array('title' => 'Форум', 'act' => 'forums')
                        )
);


/**
 * подразделы главного меню в админке, указанные модули должны быть прописаны modules_list
 */
 
$sv_admin_menu = array(
  'pages'   => array( 'pages', 'infoblocks', 'urls' ),
  'editors' => array( 'news', 'attaches', 'book' ),
  'users'   => array( 'admin', 'accounts', 'subscribers',  'restores', 'sessions', 'logs', 'searches', 'searchresults'),
  'options' => array( 'config', 'cache', 'antispam' ),
  'afisha'  => array( 'halls', 'events' ),
  'blogs'   => array( 'blogs', 'posts', 'history', 'restores'),
  'victorina' => array( 'tquestions', 'tcats' ),      
  'shop'    => array( 'products', 'orders'),      
  'forum'   => array( 'fcats', 'forums', 'ftopics', 'fposts'),
);
    

/**
 * Дполнительные пункты в подменю (можно добавлять (выделять) непосредственно в модуле, на лету)
 * @var array( 'admin_menu_item' => array( 'uniq_key' => array('module' => 'products', 'title' => 'Название', 'url' => '...', 'selected' => 0 ),
 *                                         'uniq_key' => array('module' => 'products', 'title' => 'Название2', 'url' => '...', 'selected' => 0 ) 
 *                                       ) 
 *            )
 */
$sv_admin_menu_virtuals = array(
  /*
  'shop' => array(
    'test1' => array('module' => 'products', 'title' => 'Дачная и садовая мебель', 'url' => 'index.php?products&set_cat_id=1', 'selected' => 0),
    // ...
  )
  */
);    
    
/**
 * Ярлыки в основных разделах админки
 * @var array( 'admin_menu_item' => array( Название => Адрес, ...) )
 */
$sv_admin_menu_shortcuts = array(
  'forum' => array(
    'Настройки форума' => './?config&cat=%D4%EE%F0%F3%EC'
  )
);    


$sv_parsed = array(
  'wait_div' => "<div class='wait' style='display:none;color: gray;'><img src='/i/wait16.gif' width='16' height='16' 
                  border='0' style='vertical-align:-3px;'> загрузка...</div>",
  'admin_sidebar' => ''
);


$sv_botname_patterns = array(
  "GoogleBot" => "Googlebot",
  "Yandex.Bot" => "Yandex",
  "MSN Bot" => "msn",
  "Yahoo Bot" => "Yahoo",
  "Rambler Crawler" => "Rambler",
  "CLX Bot" => "CLX.ru Bot",
  "CLX PostBot" => "CLX.ru PostBot",
  "CLX" => "CLX.ru",
  "Nor24 Bot" => "norilsk24.ru search engine",
  "Mail.ru Bot" => "Mail.Ru"
);


?>