<?php
/**
 * Модуль для копированания новостей по темам с рсс каналов.
 * rev. 2
 * 
 * @deprecated лучше использовать cron движок с каждый грабером по отдельности
 * 
 */
class rssgrab { 

var $sites = array(

  'yandex_auto' => array('cat' => 'auto',  'url' => 'http://news.yandex.ru/Russia/auto.rss'),
  'yandex_politics' => array('cat' => 'politics',  'url' => 'http://news.yandex.ru/Russia/politics.rss'),
  'yandex_computers' => array('cat' => 'computers',  'url' => 'http://news.yandex.ru/Russia/computers.rss'),
  'yandex_sport' => array('cat' => 'sport',  'url' => 'http://news.yandex.ru/Russia/sport.rss'),
  'yandex_culture' => array('cat' => 'culture',  'url' => 'http://news.yandex.ru/Russia/culture.rss'),
  'glamour' => array('cat' => 'celebrity',  'url' => 'http://www.glamour.ru/celebrity/news/rss.php'),
  'ttelegraf' => array('cat' => 'norilsk', 'url' => 'http://www.ttelegraf.ru/rss.php5')
);

var $c_site;
var $update_existed = 0;

var $files_url = "uploads/rss/";
var $files_dir = "uploads/rss/";


function auto_run() {
  global $sv, $std, $db;    

  $keys = array_keys($this->sites);
  $this->process_grabers($keys);
  
}

/**
 * Общая функция для обработка списка гребров
 *
 * @param unknown_type $list
 */
function process_grabers($list = array()) {
  global $sv, $std;
  
  $std->curl->tmp_file = "tmp/rssdata.xml";
  
  $sv->load_model('rss_data');
  $sv->init_class('rssc');
  
  $ret = array();
  foreach($list as $graber_id) {
    $this->c_site = $graber_id;
    if (!isset($this->sites[$graber_id])) {
      ec("-- Undefined graber_id = {$graber_id} --");
      continue;
    }
    $d = $this->sites[$graber_id];
    
    // спец обработка яндексовских каналов
    if (preg_match("#yandex#si", $graber_id)) {      
      $ret[] = $this->grab_yandex_news($d);
    }    
    else {
      $call = "grab_{$graber_id}";
      if (!method_exists($this, $call)) {
        ec("-- Method {$call} not exists --");
        continue;
      }
      else {
        $ret[] = $this->$call($d);
      }
    }
  } // foreach  
  
  return $ret;
}

// Граберы
function grab_yandex_news($site) {
  global $sv, $std, $db;
  
  $f = $std->curl->get_file($site['url']);
  if ($f === false) {
    ec("Can't get index file from {$site['url']}");
    return false;
  }
  
  $items = $sv->rssc->parse($f);
  
  if (count($items)<=0) {
    ec("####### Error! no records found. ##########");
    return false;
  }

  foreach($items as $d) {
    
    foreach($d as $k=>$v) {
      $d[$k] = mb_convert_encoding($v, "cp1251", "utf8");
    }
    
    $p = array();
    $p['title'] = strip_tags($d['TITLE']);
    $p['guid'] = $d['GUID'];
    $p['description'] = strip_tags($d['DESCRIPTION']);
    $p['pubdate'] = $d['PUBDATE'];
    $p['link'] = $d['LINK'];
    
    
    $p['site_id'] = $this->c_site;
    $p['date'] = date("Y-m-d H:i:s", strtotime($d['PUBDATE']));
    $p['cat'] = $site['cat'];
    $p['source'] = $site['url'];
    $p['active'] = 1;
    
    if (!$sv->m['rss_data']->is_exists($this->c_site, $p['guid'])) {
      $sv->m['rss_data']->insert_row($p);
      ec("INSERT - {$p['title']}");
    }
    else {
      if ($this->update_existed) {
        $sv->m['rss_data']->update_row($p);
        ec("UPDATE - {$p['title']}");
      }
      else {
        ec("SKIP --- {$p['title']}");
      }
    }
  }
  
}

function grab_glamour($site) {
  global $sv, $std, $db;
  
  $f = $std->curl->get_file($site['url']);
  if ($f === false) {
    ec("Can't get index file from {$site['url']}");
    return false;
  }
  
  $sv->rssc->parse_attributes = 1;
  $items = $sv->rssc->parse($f);
  
  if (count($items)<=0) {
    ec("####### Error! no records found. ##########");
    return false;
  }

  foreach($items as $d) {
   

    
    
    $p = array();
    $p['title'] = strip_tags($d['TITLE']['data']);
    $p['guid'] = $d['LINK']['data'];
    $p['description'] = strip_tags($d['DESCRIPTION']['data']);
    $p['pubdate'] = $d['PUBDATE']['data'];
    $p['link'] = $d['LINK']['data'];
    
    
    $p['site_id'] = $this->c_site;
    $p['date'] = date("Y-m-d H:i:s", strtotime($d['PUBDATE']['data']));
    $p['cat'] = $site['cat'];
    $p['source'] = $site['url'];
    $p['active'] = 1;
    
    foreach($p as $k=>$v) {
      $p[$k] = mb_convert_encoding($v, "cp1251", "utf8");
    }
    
    $exists = $sv->m['rss_data']->is_exists($this->c_site, $p['guid']);
    
    if ($exists && !$this->update_existed) {
      ec("SKIP --- {$p['title']}");
      continue;
    }
    
    // adding image to text
    $img = $d['ENCLOSURE']['attr'];
    if (isset($img['URL'])) {
      if ($exists) {
        $local_url = (preg_match("#'(http\://img.norcom.ru[^']+)#si", $sv->m['rss_data']->d['description'], $m)) ? $m[1] : "";
        ec(" = image from prev scan: {$local_url}");
      }
      else {
        $local_url = $this->dl_img($img['URL']);
      }
      if ($local_url!='') {
        $p['description'].= "\n<div class='rss-image'><img src='{$local_url}' border='0'></div>";
      }
    }
        
    if (!$exists) {
      $sv->m['rss_data']->insert_row($p);
      ec("INSERT - {$p['title']}");
    }
    else {      
      //$sv->m['rss_data']->update_row($p);
      ec("UPDATE - {$p['title']}");     
    }
    
   
  }//foreach
  
}

function grab_ttelegraf($site) {
  global $sv;
  
  $items = $this->parse_rss($site['url']);
  
  t($items);
  
}

/**
 * Скачивание изображения в заданную папку
 *
 * @param unknown_type $url
 * @return unknown
 */
function dl_img($url) {
  global $std;
  
  $url = trim($url);
  if (!preg_match("#\.(jpg|jpeg|gif|png)$#si", $url, $m)) {
    return "";
  }
  else {
    $ext = strtolower($m[1]);
  }
  
  $path = $std->text->split_dir($std->text->random_t(10), 3).".{$ext}";
  
  $local_path = $this->files_dir.$path;
  $local_url = $this->files_url.$path;
  
  $dir = dirname($local_path);
  mkdir($dir, 0775, 1);
  
  if (!file_exists($dir) || !is_dir($dir)) {
    ec("Can't create dir: {$dir}");
    return "";
  }
  
  if (!$std->curl->raw_dl_file($url, $local_path)) {
    ec("Can't download file: {$url}");
    return "";
  }
  
  return $local_url;
}

/**
 * Получение списка записей с рсс
 *
 * @param unknown_type $url
 * @return unknown
 */
function parse_rss($url, $parse_attributes = 1) {
  global $sv, $std;
  
    
  $f = $std->curl->get_file($url);
  if ($f === false) {
    ec("Can't get index file from {$url}");
    return array();
  }
  
  $sv->rssc->parse_attributes = ($parse_attributes) ?  1 : 0;
  $items = $sv->rssc->parse($f);
  
  if (count($items)<=0) {
    ec("-- Error! no records found --");
  }
  
  return $items;
}

//eoc
}

?>