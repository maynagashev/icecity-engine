<?php
/**
 * PUBLIC - INDEX
 * модули загружаются в зависимости от sv->view->init_page
 *
 */
define("EXEC_TIME", microtime());
	
require("path_cfg.php");

require_once(LIB_DIR."class_mysql.php");	
$db = new mysql(PUBLIC_DIR."mysql_cfg.php");	
$db->connect();

require(SOURCES_DIR."class_sv.php");
$sv = new class_sv();

require(LIB_DIR."class_func.php");
$std = new func();

require(LIB_DIR."functions.php");

require(LIB_DIR."std/file.php");
$std->file = new std_file();


require(LIB_DIR."std/text.php");
$std->text = new std_text();

require(LIB_DIR."std/time.php");
$std->time = new std_time();

require(LIB_DIR."std/resize.php");
$std->resize = new std_resize();

require(LIB_DIR."std/mail.php");
$std->mail = new std_mail();

require(LIB_DIR."std/markitup.php");
$std->markitup = new std_markitup();

// INSTALL TABLES
if ($sv->debug && $sv->act=='install') {
  $sv->install_tables();
}


$sv->init_class('model');
$sv->init_class('view');
$sv->init_class('modules');
$sv->modules->load(SOURCES_DIR."modules_list.php");

// VARS
$sv->view->display_order = array('infoblocks', 'dblocks'); //, 'advstreams'
$sv->view->use_routes_before = 0;
$sv->view->use_routes_after = 1;
$sv->view->return_url_success = "/";
$sv->view->return_url_err = "/log/";

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

// SESSION
$sv->load_model('session');
$sv->m['session']->start();

// CONFIG
$sv->load_model('config');
$sv->cfg = $sv->m['config']->load_cfg();
$sv->vars['site_title'] = $sv->cfg['site_title'];


$std->mail->charset_code = 'koi';
$std->mail->from_name = $sv->cfg['short_title'];
$std->mail->from_address = $sv->cfg['email'];

$sv->view->init_page();

include("sources/preload.php");

$sv->view->load_content();
$sv->view->load_layout();

include("sources/afterload.php");

$sv->m['session']->update_session();

$sv->view->display();

// functions 
function ec($t, $show = 1, $to_file = 1) {  
  $str = "{$t}\n";
  if ($show) {
    echo nl2br($str);  
  }
  if ($to_file) {
    $fn = TMP_DIR."public.log";
    $h = fopen($fn, "a");
    fwrite($h, $str);
    fclose($h);  
  }
}

?>