<?php

/**
 * Грабер новостей с ИА Таймырский телеграф
 *
 */
class tt {
  
function auto_run() {
  global $sv, $std;

  $addr = "http://www.ttelegraf.ru/rss.php5";
  
  $f = $std->curl->get_file($addr);
  if ($f===false) die("cant get file: {$addr}");  
  
 // $r = iconv("windows-1251", "UTF-8", $r);
  
  $sv->init_class("rssc"); 
  $ar = $sv->rssc->parse($f);  

 
  foreach ($ar as $d) {     
    foreach($d as $k=>$v) {
      unset($d[$k]);              
      $v = iconv("UTF-8", "WINDOWS-1251", $v);     
      //$v = convert_cyr_string($v, "w", "d");
      $d[strtolower($k)] = $v;      
    }   
    $this->insert_row($d);
  }
    
}
  
function insert_row($d) {
  global $sv, $std, $db;
 
  if (!isset($d['guid'])) {
    $d['guid'] = (preg_match("#read\=([0-9]+)#msi", $d['link'], $m)) ? $m[1] : $d['link'];
  }
  $d['date'] = date("Y-m-d H:i:s", strtotime($d['pubdate']));  
  $d['active'] = 1;
  $d['source'] = "www.ttelegraf.ru";
  $d['created_at'] = $sv->date_time;
  
  $keys = array('guid', 'title', 'link', 'author', 'pubdate', 'date', 'active', 'source', 'description', 'created_at');
  
  $s = array();
  foreach ($keys as $k) { 
    $p[$k] = $v = strip_tags($d[$k]);
    $s[] = "`{$k}`='".addslashes($v)."'";
  }
  
  
  if ($p['title']=='' || $p['description']=='') {
    ec("{$d['guid']} passed becouse title or text is NULL");
    return false;
  }
  
  $q = "SELECT 0 FROM {$sv->t['rss_data']} WHERE guid='".addslashes($d['guid'])."'";
  $db->q($q);
  if ($db->nr()>0) {
    ec("{$d['guid']} exists, next"); return false;
  }
  else {
    $q = "INSERT INTO {$sv->t['rss_data']} SET ".implode(", ", $s);
    $db->q($q);
    ec("{$d['guid']} inserted"); return  true;
  }
  
}
  
}

?>