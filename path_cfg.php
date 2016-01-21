<?php

define("SITE_TITLE", "Городок");
define("SITE_URL", "http://gorodok.n69.ru/");
define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT']."/");
define("TMP_DIR", ROOT_DIR."../tmp/");

define("PUBLIC_URL", SITE_URL);
define("ADMIN_URL", "/admin/");

define("PUBLIC_DIR", ROOT_DIR);
define("ADMIN_DIR", PUBLIC_DIR.'admin/');

define("SOURCES_DIR", PUBLIC_DIR."sources/");
define("LIB_DIR",     SOURCES_DIR."lib/");
define("MODELS_DIR",  SOURCES_DIR."models/");
define("MODULES_DIR", SOURCES_DIR."modules/");
define("DBLOCKS_DIR", SOURCES_DIR."dblocks/");
define("LAYOUTS_DIR", SOURCES_DIR."templates/layouts/");
define("CONFIG_DIR",  SOURCES_DIR."config/");

define("SMARTY_DIR",  LIB_DIR."libsmarty/");


define("UPLOADS_DIR", PUBLIC_DIR."uploads/");
define("UPLOADS_URL", SITE_URL."uploads/");

define("TIME_SHIFT", 60*60*4);

$trace_ips = array('95.188.');
$ip = getenv("REMOTE_ADDR");

$in_list = 0;
foreach($trace_ips as $pat) {
  $pat = preg_quote($pat);
  if (preg_match("#^{$pat}#si", $ip)) {
    $in_list = 1;
  }
}
  
if ($in_list || !$ip) {
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
}
else { 
  error_reporting(0);
}

// require_once PUBLIC_DIR.'sources/bbcode/bbcode.lib.php';

?>