<?php

//var_dump(apache_note("GEOIP_COUNTRY_CODE"));
//echo "<pre>"; print_r(apache_get_modules()); echo "</pre>";

class std_info {
  
var $last_os = "";
var $last_browser = "";
  
var $last_host = "";
var $host_type = "";
var $v_err = 0;
var $v_errm = array();

function os($agent = "") {
    
  $t = ($agent=='') ? getenv("HTTP_USER_AGENT") : $t;
   
  $r = array(
  "Windows NT 7.0" => "Windows 7",
  "Windows NT 6.0" => "Windows Vista",
  "Windows NT 5.2" => "Windows Server 2003",
  "Windows NT 5.1" => "Windows XP",
  "Windows NT 5.0" => "Windows 2000",
  "Windows NT 4.0" => "Windows NT 4.0"  
  );
  
  
  foreach ($r as $k=>$v) {
    $t = str_replace($k, $v, $t);
  }
  
  // os match
  $os = array("Windows", "Linux", "FreeBSD", "Mac OS", "Debian", "Symbian", "MIDP", "SunOS", "WinNT");
  $os_list = array();
  foreach($os as $k) {
    $cur = ""; $len = 0;
    if (preg_match_all("#(\(|;)([^\(;]*".preg_quote($k)."[^\(;]*)(\)|;)#msi", $t, $m)) {
      foreach($m[2] as $i=>$d) {
        if (strlen($d)>$len) {
          $cur = $d; 
          $len = strlen($d);
        }
      }
    }
    if ($cur!='') {
      $this->last_os = $cur;
      $os_list[] = $cur;
    }
  }
  $ret = implode(", ", $os_list);
    
  return $ret;
}


function browser($agent = "") {
  
  $t = ($agent=='') ? getenv("HTTP_USER_AGENT") : $t;

  $browsers = array('Opera', 'Firefox', 'Safari', 'Netscape', 'Konqueror', 'MSIE');
  $b_list = array();
  foreach($browsers as $k) {
    $q = preg_quote($k);    
    if (preg_match("#(\(|;)([^\(;]*{$q}[^\(;]*)(\)|;)#msi", $t, $m)) {
      $b_list[] = $m[2];
    }   
    elseif (preg_match("#([^\(\);\s]*{$q}[0-9\.\s/]+)#msi", $t, $m))  {
      $b_list[] = trim($m[1]);
    }
    elseif (preg_match("#([^\(\);\s]*{$q}[^\(\);\s]*)#msi", $t, $m)) {
      $b_list[] = $m[1];
    }
    if (count($b_list)>0) {
      $this->last_browser = $k;
      break;
    }
  }
  $ret = implode(", ", $b_list);  
  $ret = str_replace("MSIE", "Microsoft Internet Explorer", $ret);
  
  return $ret;
}  
  
/**
 * code for OS icon
 *
 * @param unknown_type $os
 * @return string: win|linux|mac|sun|bsd|mobile|default
 */
function code_os($os='') { 
  $t = ($os=='') ? $this->last_os : $os;
  
  if (preg_match("#win#si", $t)) {
    $r = 'win';
  }
  elseif(preg_match("#(linux|debian)#si", $t)) {
    $r = 'linux';
  }
  elseif(preg_match("#mac#si", $t)) {
    $r = 'mac';
  }
  elseif(preg_match("#sun#si", $t)) {
    $r = 'sun';
  }
  elseif(preg_match("#bsd#si", $t)) {
    $r = 'bsd';
  }
  elseif(preg_match("#(symbian|midp)#si", $t)) {
    $r = 'mobile';
  }
  else {
    $r = 'default';
  }
  return $r;
}
  
  
/**
 * code for BROWSER icon
 *
 * @param unknown_type $os
 * @return string: firefox|opera|safari|netscape|konqueror|msie
 */
function code_browser($t='') { 
  $t = ($t=='') ? $this->last_browser : $t;
  
  if (preg_match("#(firefox|opera|safari|netscape|konqueror|msie)#si", $t, $m)) {
    $r = strtolower($m[1]);
  }
  else {
    $r = "default";
  }  
  return $r;
}

/**
 * validate host
 *
 * @param unknown_type $host
 * @return unknown
 */
function v_host($host='', $only_root = 0) {
  global $sv, $std;
  
  $host = trim($host);
      
  if ($host=='') {
    return "";
  }
  
  $p = parse_url($host);
  
  if (isset($p['host'])) {
    $t = $p['host'];

  }
  else {
    $t = $p['path'];    
  }
  
 
   
  $t = (preg_match("#^([^/]+)#si", $t, $m)) ? $m[1] : $t;
  $t = preg_replace("#[^a-z0-9\-\_\.]#si", "", $t);
  
  if ($only_root) {
    $t = preg_replace("#^(.*\.)?([^\.]+\.[a-z]+)$#is", "\\2", $t);      
  }  
  
  return $t;
}

/**
 * DNS query
 * 
 *
 * @param unknown_type $host
 */
function dig($host, $server='', $validate = 1) {
  global $sv, $std;
  
  $err = 0; 
  $errm = array();
  
  if ($validate) {
    $host = $this->v_host($host);
    if (!$host) {
      $err = 1; 
      $errm = $this->v_errm;
    }
    
    if ($server!='') {
      $server = $this->v_host($server);
      if (!$server) {
        $err = 1;
        $errm = $this->v_errm;
      }
    }
  }
  
  if (!$err) {
    $s = ($server!='') ? "@{$server} " : "";
    $q = "dig {$s} {$host}";
    $res = shell_exec($q);    
  }
  else {
    $res = "";
    $q = "";
  }
  
  $ret['q'] = $q;
  $ret['res'] = $res;
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  
  
  return $ret;
}

function nslookup($host, $server='', $validate = 1) {
  global $sv, $std;
  
  $err = 0; 
  $errm = array();
  
  if ($validate) {
    $host = $this->v_host($host);
    if (!$host) {
      $err = 1; 
      $errm = $this->v_errm;
    }
    
    if ($server!='') {
      $server = $this->v_host($server);
      if (!$server) {
        $err = 1;
        $errm = $this->v_errm;
      }
    }
  }
  
  if (!$err) {
    $s = ($server!='') ? "{$server} " : "";
    $q = "nslookup {$host} {$s}";
    $res = shell_exec($q);    
  }
  else {
    $res = "";
    $q = "";
  }

  $ret['q'] = $q; 
  $ret['res'] = $res;
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  
  
  return $ret;
}


function ping($host, $validate = 1) {
  global $sv, $std;
  
  $err = 0; 
  $errm = array();
  
  if ($validate) {
    $host = $this->v_host($host);
    if (!$host) {
      $err = 1; 
      $errm = $this->v_errm;
    }   
  }
  
  if (!$err) {   
    $q = "ping -c 10 {$host}";
    $res = shell_exec($q);    
  }
  else {
    $res = "";
    $q = "";
  }
  
  $ret['q'] = $q;
  $ret['res'] = $res;
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  return $ret;
}

function traceroute($host, $validate = 1) {
  global $sv, $std;
  
  $err = 0; 
  $errm = array();
  
  if ($validate) {
    $host = $this->v_host($host);
    if (!$host) {
      $err = 1; 
      $errm = $this->v_errm;
    }   
  }
  
  if (!$err) {   
    $q = "traceroute -m 30 -w 2 {$host}";
    $res = shell_exec($q);    
  }
  else {
    $res = "";
    $q = "";
  }
  
  $ret['q'] = $q;
  $ret['res'] = $res;
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  return $ret;
}

function whois($host, $validate = 1) {
  global $sv, $std;
  
  $err = 0; 
  $errm = array();
  
  if ($validate) {
    $host = $this->v_host($host);
    if (!$host) {
      $err = 1; 
      $errm = $this->v_errm;
    }   
  }
  
  if (!$err) {   
    $q = "whois {$host}";
    $res = shell_exec($q);    
  }
  else {
    $res = "";
    $q = "";
  }
  
  
  $res = preg_replace("#^\%[^\n]+\n#msi", "", $res);
  $res = preg_replace("#^(Domain\:\s+)([^\s]+)#msi", "\\1<a href='http://\\2' target=_blank>\\2</a>", $res);
  
  $ret['exists'] = (preg_match("#No\ entries#si", $res)) ? 0 : 1;
  $ret['q'] = $q;
  $ret['res'] = $res;
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  return $ret;
}
 
function replace_ips($t, $url='') {  
  $t = preg_replace("#\b([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})\b#si", "<a href='{$url}\\1'>\\1</a>", $t);  
return $t;  
}
  //eoc
}

?>