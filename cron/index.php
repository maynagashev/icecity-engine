#!/usr/local/php5/bin/php -d safe_mode=off
<?php
DEFINE ("EXEC_TIME",microtime());

$root_dir = "/host/www/n69/public/";
$cron_dir = "/host/www/n69/cron/";
$modules_dir = "./sources/modules/";

chdir($cron_dir);

require($root_dir."path_cfg.php");


// DATABASE
require_once(LIB_DIR."class_mysql.php");	

$db = new mysql(PUBLIC_DIR."mysql_cfg.php");
$db->connect();
    

// SV    
require_once(SOURCES_DIR."class_sv.php");
$sv = new class_sv;  

// FUNCTIONS
require_once(LIB_DIR."functions.php");
require_once(LIB_DIR."class_func.php");	
$std = new func;

// STD->
require_once(LIB_DIR."std/time.php");	
$std->time = new std_time;

require_once(LIB_DIR."std/text.php");	
$std->text = new std_text;
  	
require_once(LIB_DIR."std/file.php");	 
$std->file = new std_file;

require_once(LIB_DIR."std/curl.php");	
$std->curl = new std_curl;
$std->curl->tmp_file = "logs/last_curl.html";
$std->curl->http_agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.8.1.16) Gecko/20080702 Firefox/2.0.0.16";

require_once(LIB_DIR."std/flv.php");	
$std->flv = new std_flv;

require_once(LIB_DIR."std/resize.php");	
$std->resize = new std_resize;  	

require_once(LIB_DIR."std/mail.php");	
$std->mail = new std_mail;   	
  	  	
require(LIB_DIR."std/search.php");
$std->search = new std_search();


// ===========================	

// проверка командной строки
if (!isset($_SERVER["argv"])) {
  die("Запуск назначенных заданий из web-интерфейса запрещен.");
}

// считываем называния модулей из названий файлов
$acts = array();
$files = $std->file->file_list($modules_dir, 0, 1);
foreach($files as $fn) {
  if (preg_match("#^([a-z0-9\_\-]+)\.php$#si", $fn, $m)) {
    $acts[] = $m[1];
  }
}

// определяем название модуля для запуска
$act = (isset($argv[1])) ? $argv[1] : ""; 
if (!in_array($act, $acts)) die ("Act not defined: ".strtoupper(implode(", ", $acts))."\n");

echo "Loading: {$act}...\n\n";

$fn = $modules_dir.$act.".php";
if (!file_exists($fn)) {
  echo "{$fn} NOT FOUND\n";
}


  require_once($fn);   
  
  $runme = new $act;  
  $runme->auto_run();
  

echo "Done. \nScript execution time: ".et()."\n";

  

// ==============================================
function cls() { for($i =0; $i<100; $i++) {   echo "\n\r";  }  }

function et() {
GLOBAL $db, $sv;

  $t1=$sv->exec_time;
  $t2=microtime();
  list($m1,$s1)=explode(" ",$t1);
  list($m2,$s2)=explode(" ",$t2);
  $time=($s2-$s1)+($m2-$m1);
  $time=round($time,3);
  
  $out=$time."s. ".$db->query_count."q.";

	return $out;
}

function ec($str, $n=1, $to_file = 0) {
  global $std;
  //$str = convert_cyr_string($str, "w", "d");
  $str = mb_convert_encoding($str, "utf-8", "windows-1251");
  //$str = $std->text->translit($str);
  echo $str;
  if ($n) {echo "\n";}
  
  if ($to_file) {
    $fn = TMP_DIR."cron.log";
    $h = fopen($fn, "a");
    fwrite($h, $str);
    fclose($h);      
  }
}

function pr($ar) {  
  ec ("ARRAY CONTENT:");
  foreach ($ar as $k=>$v) {
    if (is_array($v)) {
      pr($v);
    }
    else {
    ec ("[$k] {$v}\n---------------------------------------");
    }
  }
  
  
}



?>