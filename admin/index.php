<?php
/**
 * ADMIN-INDEX файл
 * в отличии от публичного вызывает админский шаблон 
 * 
 * модули загружаются в зависимости от modules->get_current
 *
 */
define("EXEC_TIME", microtime());
define("IN_ADMIN", 1);

require($_SERVER['DOCUMENT_ROOT']."/path_cfg.php");

require_once(LIB_DIR."class_mysql.php");	
$db = new mysql(PUBLIC_DIR."mysql_cfg.php");	
$db->connect();
// /* $db->q("SET NAMES 'cp1251'"); */ 
    
require(SOURCES_DIR."class_sv.php");
$sv = new class_sv();

require(LIB_DIR."class_func.php");
$std = new func();

require(LIB_DIR."functions.php");

require(LIB_DIR."std/file.php");
$std->file = new std_file();

require(LIB_DIR."std/text.php");
$std->text = new std_text();

require(LIB_DIR."std/search.php");
$std->search = new std_search();

require(LIB_DIR."std/time.php");
$std->time = new std_time();

require(LIB_DIR."std/resize.php");
$std->resize = new std_resize();

require(LIB_DIR."std/mail.php");
$std->mail = new std_mail();

require(LIB_DIR."std/markitup.php");
$std->markitup = new std_markitup();
$std->markitup->content_css = "/css/style.css";
$std->markitup->edit_width = '100%'; 

// INSTALL TABLES
if ($sv->debug && $sv->act=='install') {
  $sv->install_tables();
}

$sv->init_class('model');
$sv->init_class('view');
$sv->view->display_order = array();//, 'infoblocks', 'dblocks' 'advstreams'  - трабл с отображениями в инпутах

//TEMPLATES
require(SMARTY_DIR.'Smarty.class.php');
$smarty = new Smarty;  
$smarty->template_dir = SOURCES_DIR.'templates/';
$smarty->compile_dir = SOURCES_DIR.'smarty/templates_c/';
$smarty->config_dir = SOURCES_DIR.'smarty/configs/';
$smarty->cache_dir = SOURCES_DIR.'smarty/cache/';
$smarty->caching = false;  
$smarty->register_function("u", "url");
$smarty->register_function("su", "sub_url");

// CONFIG
$sv->load_model('config');
$sv->cfg = $sv->m['config']->load_cfg();
// t($sv->cfg);

// MODULES	
$sv->init_class("modules");
$sv->modules->load(SOURCES_DIR."modules_list.php");
$sv->modules->default = 'auth';
$sv->modules->default_ar[3] = 'admin';

// SESSION
$sv->load_model('session');
$sv->m['session']->start();

$sv->act = $sv->modules->get_current();
$sv->m['session']->update_session();

if ($sv->modules->err) {
  $sv->view->show_err_page($sv->act);
}

include(SOURCES_DIR.'preload_admin.php');

$body = $sv->modules->auto_run();

if ($body) {
  $sv->init_admin_menu();
  $smarty->assign('ar', $body);    
  $smarty->assign('sv', $sv);      
  $html = $smarty->fetch("admin.tpl");    
  $html = $sv->view->render($html);
  echo $html;
}

if ($sv->debug)   {
  $d = $std->debug_info();
  echo "<div style='position:absolute; color: gray; top: 70px; right: 245px; font-size: 13px; font-family: Verdana;'>{$d}</div>";
}
  
// functions 
function ec($t, $show = 1, $to_file=1, $filename = 'admin.log') {  
  $str = "{$t}\n";
  if ($show) {
    echo nl2br($str);  
  }
  if ($to_file) {
    $fn = TMP_DIR.$filename;
    $h = fopen($fn, "a");
    fwrite($h, $str);
    fclose($h);  
  }
}
?>