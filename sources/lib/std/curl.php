<?php

class std_curl {
  
var $external_binary = "/usr/local/bin/curl";
  
var $verbose = 1;
var $history = array();
var $redirect_limit = 5;
var $redirect_count = 0;

var $timeout = 30;
var $cookie = "";
var $post   = "";  // var1=urlencoded(value1)&var2=urlencoded(value2)
var $referer = "";
var $http_agent = "Mozilla/5.0(Windows;U;WindowsNT5.1;en-US)AppleWebKit/534.3(KHTML,likeGecko)Chrome/6.0.472.63Safari/534.3";
var $tmp_file = "temp.html";
var $return_header = 0;

var $codes = array(
100 => "Continue",
101 => "Switching Protocols",
200 => "OK",
201 => "Created",
202 => "Accepted",
203 => "Non-Authoritative Information",
204 => "No Content",
205 => "Reset Content",
206 => "Partial Content",
300 => "Multiple Choices",
301 => "Moved Permanently",
302 => "Found",
303 => "See Other",
304 => "Not Modified",
305 => "Use Proxy",
306 => "(Unused)",
307 => "Temporary Redirect",
400 => "Bad Request",
401 => "Unauthorized",
402 => "Payment Required",
403 => "Forbidden",
404 => "Not Found",
405 => "Method Not Allowed",
406 => "Not Acceptable",
407 => "Proxy Authentication Required",
408 => "Request Timeout",
409 => "Conflict",
410 => "Gone",
411 => "Length Required",
412 => "Precondition Failed",
413 => "Request Entity Too Large",
414 => "Request-URI Too Long",
415 => "Unsupported Media Type",
416 => "Requested Range Not Satisfiable",
417 => "Expectation Failed",
500 => "Internal Server Error",
501 => "Not Implemented",
502 => "Bad Gateway",
503 => "Service Unavailable",
504 => "Gateway Timeout",
505 => "HTTP Version Not Supported"
);

var $log_all = array();
var $log_session = array();

function get_file($addr) {
  global $std; 
    $err = false;
    $this->log("= CURL opening: {$addr}", 1);
    
    /*
    if (!is_writeable(".")) {
      $this->log("Error current dir is not writable, can't save results.");
      return false;
    }    
   */
    $fn = $this->tmp_file;  
    $fp = fopen($fn, "w");
    $ch = curl_init();       
    curl_setopt($ch, CURLOPT_URL, $addr);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
  
    
    if ($this->http_agent!='') {
      curl_setopt($ch, CURLOPT_USERAGENT, $this->http_agent);
      $this->log("User-agent: {$this->http_agent}");
    }
    if ($this->referer!='') {
      curl_setopt($ch, CURLOPT_REFERER, $this->referer); 
      $this->log("Referer: {$this->referer}");
    }    
    if ($this->cookie!='') {
      curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
      $this->log("Cookie: {$this->cookie}");
    }
    if ($this->post!='') {
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $this->post);
      $this->log("POST: {$this->post}");
    }
    if ($this->return_header)     {
      curl_setopt($ch, CURLOPT_HEADER, 1);
      if ($this->return_header==2) {
        curl_setopt($ch, CURLOPT_NOBODY, 1);
      }
    }    
        
    $res = curl_exec($ch);
          
    if (empty($res)) {       
       $this->log("Error: ".curl_error($ch));
       curl_close($ch); 
    } else {
       $info = curl_getinfo($ch);
       curl_close($ch);           
       $this->info($info);
    }
        
    fclose($fp);    
    $file = file_get_contents($fn);    
    
    return $file;      
}

function raw_dl_file($addr, $path, $replace=1) {
  global $std, $sv;
    
    $this->log("= CURL raw_dl_file: {$addr} TO {$path} \n", 1);
    
    $dir = dirname($path);
    
    if (!is_writeable($dir)) {
      $this->log("= read only dir: {$dir}, breaking\n");
      return false;
    }
    $save_path = $path;
     
    if (file_exists($save_path) && filesize($save_path)>1 && !$replace) {
      $this->log("= file exists, continue \n");
      return $ret;
    }
    
    $fp = fopen($save_path, "w");
    $ch = curl_init();       
    curl_setopt($ch, CURLOPT_URL, $addr);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    
    if (isset($this->timeout)) {
      curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
      $this->log("Timeout: {$this->timeout}");
    }    
    if ($this->http_agent!='') {
      curl_setopt($ch, CURLOPT_USERAGENT, $this->http_agent);
      $this->log("User-agent: {$this->http_agent}");
    }
    if ($this->referer!='') {
      curl_setopt($ch, CURLOPT_REFERER, $this->referer); 
      $this->log("Referer: {$this->referer}");
    }    
    if ($this->cookie!='') {
      curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
      $this->log("Cookie: {$this->cookie}");
    }
  
    $res = curl_exec($ch);
    
    
      
    if (empty($res)) {       
       echo "Error: ".curl_error($ch)."\n";
       curl_close($ch); 
       fclose($fp);  
       return false;
       
    } else {
       $info = curl_getinfo($ch);
       curl_close($ch);    
       fclose($fp);  
       $this->info($info);
    }
      
      
    return true;    
  
}

function dl_image($addr,  $path="",  $replace=1, $random_name=0, $fn="") {
  global $std, $sv;

  $path = preg_replace("#/*$#si", "", $path);
   
  $this->log("= CURL dl_image: {$addr} TO {$path}");
  
  $ext = $std->file->extension($addr);
  
  if ($random_name) {
    $fn = $std->file->rand_name($ext);
  }
  elseif ($fn!='') {
    $fn = $fn;
  }
  else {
    $fn = basename($addr);
  }
  
  if ($path!='') {
    $std->file->create_dirtree($path);
  }
  
  if (!is_writeable($path)) {
    $this->log("= read only dir: {$path}, breaking");
    return false;
  }
  
  $save_path = $path."/".$fn;
    
  $this->log("= SAVE_PATH {$save_path}");
  if (file_exists($save_path) && filesize($save_path)>1 && !$replace) {
    $this->log("= file exists, continue");
    return $ret;
  }
    
  $fp = fopen($save_path, "w");
  $ch = curl_init();       
  curl_setopt($ch, CURLOPT_URL, $addr);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_FILE, $fp);
  curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

  if ($this->http_agent!='') {
    curl_setopt($ch, CURLOPT_USERAGENT, $this->http_agent);
    $this->log("User-agent: {$this->http_agent}");
  }
  if ($this->referer!='') {
    curl_setopt($ch, CURLOPT_REFERER, $this->referer); 
    $this->log("Referer: {$this->referer}");
  }    
  if ($this->cookie!='') {
    curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
    $this->log("Cookie: {$this->cookie}");
  }
  if ($this->post!='') {
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->post);
    $this->log("POST: {$this->post}");
  }      
  if ($this->return_header)     {
    curl_setopt($ch, CURLOPT_HEADER, 1);
    if ($this->return_header==2) {
      curl_setopt($ch, CURLOPT_NOBODY, 1);
    }
  }     

  $res = curl_exec($ch);
    
  if (empty($res)) {       
     $this->log("Error: ".curl_error($ch));
     curl_close($ch); 
  } else {
     $info = curl_getinfo($ch);
     curl_close($ch);   
     $this->info($info);
  }
        
  fclose($fp);
    
  return $save_path;
}

//deprecated  
function dl_file($addr, $path, $replace=1, $create_dirtree=0) {
  global $std, $sv;
  
    $saved_fn = "";      
    
    $fn = basename($addr);
    
    echo "= CURL dl_file: {$addr} TO {$path} \n";
    
    $dir = dirname($path);
    
    if ($create_dirtree) {
      $std->file->create_dirtree($dir);
    }
    
    if (!is_writeable($dir)) {
      echo "= read only dir: {$dir}, breaking\n";
      return false;
    }
    $save_path = $path;
   
    
   
    if (file_exists($save_path) && filesize($save_path)>1 && !$replace) {
      echo "= file exists, continue \n";
      return $ret;
    }

    
    
    $fp = fopen($save_path, "w");
    $ch = curl_init();       
    curl_setopt($ch, CURLOPT_URL, $addr);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
    //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  
    $res = curl_exec($ch);
    
    
      
    if (empty($res)) {       
       echo "Error: ".curl_error($ch)."\n";
       curl_close($ch); 
       fclose($fp);         
       
       return false;
    } else {
       $info = curl_getinfo($ch);
       curl_close($ch);    
       fclose($fp);  
       $this->info($info);
       
       if ($info['http_code']==404) {
         ec("Error: Not found.");
         return false;
       }
      
     
       
    }
         
    
    return true;    
  
}
     


// HEADERS =================================

function rapid_headers($url, $user="", $pass="", $nobody = false)   {
  
    $cookie = ($user!='') ? "user={$user}-{$pass};" : "";
   
    echo"= CURL headers: {$url}\n---------------------------------\n";
    $ch = curl_init();       
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
   
    curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
    if ($nobody) {
      curl_setopt($ch, CURLOPT_NOBODY, 1);    
    }
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);    
    curl_setopt($ch, CURLOPT_RANGE, "0-1024");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    if ($cookie!='') {
      curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    }
  
    $res = curl_exec($ch);
    
   
    if (empty($res)) {       
       echo "Error: ".curl_error($ch)."\n";
       curl_close($ch);   
    } else {
       $info = curl_getinfo($ch);       
       curl_close($ch);    
       $this->info($info);  
    }
        
    
    
    return $res;    
}

function get_headers($url, $nobody = false, $cookie = '')   {
  
    echo"= CURL headers: {$url}\n---------------------------------\n";
    $ch = curl_init();       
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 1);   
    curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
    if ($nobody) {
      curl_setopt($ch, CURLOPT_NOBODY, 1);    
    }
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);    
    curl_setopt($ch, CURLOPT_RANGE, "0-1024");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    if ($cookie!='') {
      curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    }
  
    $res = curl_exec($ch);
    
   
    if (empty($res)) {       
       echo "Error: ".curl_error($ch)."\n";
       curl_close($ch);   
    } else {
       $info = curl_getinfo($ch);       
       curl_close($ch);    
       $this->info($info);  
    }
        
    
    
    return $res;    
}

function get_follow_headers($url) {
  
  $last =  (in_array($url, $this->history))  ? 1 : 0;
  $last = ($this->redirect_count>$this->redirect_limit) ? 1 : $last;
  
  $this->history[] = $url; 
  $h = get_headers($url);    
  $pr = $this->parse_headers($h);  
  
  if ($pr['location']!='' && !$last) {
    $url = $pr['location'];    
    $this->redirect_count++;
    $pr = $this->get_follow_headers($url);
  }  
  
  $pr['url'] = $url;   
  $pr['headers'] = $h;
  return $pr;
}

/**
 * if headers returned in page
 *
 * @param unknown_type $t
 * @param unknown_type $cut_html
 * @return unknown
 */
function parse_headers_from_page($t, $cut_html = 1) {
  
  if ($cut_html) {
    $t = preg_replace("#<html[^>]*>.*$#si", "", $t);
    $r = preg_replace("#<body[^>]*>.*$#si", "", $t);    
  }
  
  $ar = explode("\n", $t);
  foreach($ar as $k=>$v) {
    $ar[$k] = trim($v);
  }
  return $this->parse_headers($ar);
}
  
/**
 * parse headers from rows array (returned by get_headers())
 *
 * @param unknown_type $r
 * @return unknown
 */
function parse_headers($r) {
    
  $ret = array(
    'code' => 0,
    'accept_ranges' => 0,
    'size' => '',
    'type' => '',
    'desc' => '',
    'location' => '',
    'fn' => '',
    'cookies' => array(),
    'cookies_str' => ''
  );
  
  $size = 0;
  $ranges = 0;
  $type = '';
  $location = "";
  $fn = "";
  

  if (preg_match("#^HTTP/[0-9\.]+\s+([0-9]+)(.*)$#", $r[0], $m))  {
    $code = $m[1];
    $desc = trim($m[2]);
  }
  else {
    $code = 0;
    $desc = "";
  }  
  
  foreach ($r as $h) {
    
    if (preg_match("#Content\-Length\:\s+([0-9]+)#msi", $h, $m)) {
      $size = $m[1];
    }
    
    if (preg_match("#Content\-Type\:\s+([^\;\s]+)#msi", $h, $m)) {
      $type = trim($m[1]);
    }
    
    if (preg_match("#Location\:\s+([^\s]+)#msi", $h, $m)) {
      $location = trim($m[1]);
    }
    
    if (preg_match("#filename=\"([^\"]+)\"#msi", $h, $m)) {
      $fn = trim($m[1]);
    }    
    elseif (preg_match("#filename='([^']+)'#msi", $h, $m)) {
      $fn = trim($m[1]);
    }
    elseif (preg_match("#filename=([^\s]+)#msi", $h, $m)) {
      $fn = trim($m[1]);
    }
    
    if (preg_match("#Accept\-Ranges\:\ bytes#msi", $h, $m)) {
      $ranges = 1;
    }
    
    if (preg_match("#Set\-Cookie\:\s*([a-z0-9\_\-]+)=([^\;]+)\;(.*)$#msi", $h, $m)) {
      $name = trim($m[1]);
      $c = array( 'value' => trim($m[2]) );
      $other_info = $m[3];
      
      // other cookie info 
      if (preg_match_all("#(expires|path|domain)=([^;]+)#si", $other_info, $m)) {
        foreach($m[1] as $i => $v) {
          $c[$v] = trim($m[2][$i]);
        }
      }
      
      $ret['cookies'][$name] = $c;
    }
   
  }
  
  $ret['accept_ranges'] = $ranges;
  $ret['code'] = $code;
  $ret['size'] = $size;
  $ret['type'] = $type;
  $ret['desc'] = $desc;
  $ret['location'] = $location;
  $ret['fn'] = $fn;
  
  $c_ar = array();
  foreach($ret['cookies'] as $name => $d) {
    $c_ar[] = urlencode($name)."=".urlencode($d['value']).";";
  }
  $ret['cookie_str'] = implode(" ", $c_ar);
  return $ret; 
}
// MISC ===================================

function log($str, $new_sess = 0) {
  
  if ($this->verbose>0) {
    $eol = ($this->verbose==1) ? "\n" : "<br>";
    echo $str.$eol;
  }
  
  if ($new_sess) {
    $this->log_session = array();
  }
  
  $this->log_session[] = $str;
  $this->log_all[] = $str;
  
  return true;
}
    
function info($info, $verbose = 1) {
  global $std;
  
  
  if (empty($info['http_code'])) {
    $res = "No HTTP code was returned."; 
  } 
  else { 
    $res = "The server responded: ";
    $res .= $info['http_code'] . " " . $this->codes[$info['http_code']];
    
    $kb1 = $std->size_kb($info['size_download']);
    $kb2 = $std->size_kb($info['speed_download']);    
    $this->log("= Size: {$kb1}Kb with {$kb2}Kb/sec (total: {$info['total_time']} sec)\n");
  }  
  
  $this->log("= Result: {$res}");
}
  

/**
 * Ставим ignore_user_abort
 *
 */
function init_dl_mode() {
  
  ec("Инициализируем режим скачки:");
  
  $c = ignore_user_abort();  
  ec("IGNORE_USER_ABORT = ".(($c) ? 1 : 0));
  
  if (!$c) {
    ec("Пробуем включить IGNORE_USER_ABORT...");
    ignore_user_abort(1);
    $n = ignore_user_abort();
    $res = ($n) ? " -- успешно -- " : " -- ошибка --";
    ec($res);
    
    if (!$n) {
      die("Система остановлена");
    }
  }
    
}

function set_dl_timeout($sec = 300) {
  $sec = intval($sec);
  
  $c = ini_get("max_execution_time");
  ec("Текущее значение MAX_EXECUTION_TIME = {$c}");
  
  ec("Устанавливаем таймут для скачки в {$sec} сек...");
  set_time_limit($sec);
  
  $n = ini_get("max_execution_time");
  ec("Новое значение MAX_EXECUTION_TIME = {$n}");
  
  $this->timeout = $sec;
}

function get_free_space($path = ".") {
  
  ec("Свободное место в <b>{$path}</b>:");
  ec("<pre>".shell_exec("df -h {$path}")."</pre>");
  
  ec("Занято в <b>{$path}</b>:");  
  ec("<pre>".shell_exec("du -h {$path}")."</pre>");
  
}



// Using external_binary functions 

/**
 * Sending SINGLE file
 *
 * @param unknown_type $local_path
 * @param unknown_type $remote_path
 * @param unknown_type $user
 * @param unknown_type $pass
 * @return unknown
 */
function ext_ftp_upload($local_path, $remote_path, $user, $pass) {
  
  $this->log("Sending single file from {$local_path} to {$remote_path}");
  if (!file_exists($local_path)) {
    $this->log("Local file not found: {$local_path}");
    return false;
  }
    
  $curl_query = "{$this->external_binary} --ftp-create-dirs -u {$user}:{$pass} -T {$local_path} {$remote_path}";
  
  $this->log($curl_query);
  shell_exec($curl_query);
  
  return true;
}


/**
 * external sending ar of files
 * ar = array(
 *  0 => array('local' => './local/path1.ext', 'remote' => 'ftp://remote.host/dir/r_path1.ext'),
 *  1 => array('local' => './local/path2.ext', 'remote' => 'ftp://remote.host/dir/r_path2.ext'),
 *  ...
 * )
 * 
 * @param unknown_type $user
 * @param unknown_type $pass
 * @param (array) $ar
 * @return unknown
 */
function ext_ftp_upload_ar($user, $pass, $ar) {
  
  $this->log("Sending array of files to remote FTP");
  if (!is_array($ar) || count($ar)<=0) {
    $this->log("\$ar is not array or empty");
    return false;
  }
  
  $q = array();
  foreach ($ar as $d) {
    $q[] = "-T {$d['local']} {$d['remote']}";
  }
  
  
  $curl_query = "{$this->external_binary}  --ftp-create-dirs -u {$user}:{$pass} ".implode(" ", $q);
 
  $this->log($curl_query);
  shell_exec($curl_query);
    
  return true;
}



  
//eof   
}


?>