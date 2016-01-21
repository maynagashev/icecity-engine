<?php

class std_ftp {
  
var $cid = false;
var $pwd = "";
var $host;
var $user;
var $pass;
var $root_path = "/";


function connect($host) {
  ec("Connecting to <b>{$host}</b>");
  $this->host = $host;
  $this->cid = ftp_connect($host);    
  return $this->cid;
}

function close() {
  ec("Closing link  to ftp <b>{$this->cid}</b>.");
  return ftp_close($this->cid);
}

function login($login="", $pass="", $get_pwd = 1) {
  
  $this->user = $login = ($login=="") ? "anonymous" : $login;
  $this->pass = $pass = ($pass=="") ? "lifesup@gmail.com" : $pass;
  
  ec("Authorizing... <b>{$login}:*PASSWORD*</b>");
  $ret = ftp_login($this->cid, $login, $pass);  
  
  if ($ret && $get_pwd) {
    $this->pwd();
  }
 
  return $ret;  
}

function pwd() {
  if ($pwd = ftp_pwd($this->cid)) {
    ec("Current path: {$pwd}");
    $this->pwd = $pwd;
    return $pwd;  
  }
  else {
    ec("Can't <b>pwd</b>");
    return false;
  }
  
}

function put($src, $target, $binary = 1) {
  
  $mode = ($binary) ? FTP_BINARY : FTP_ASCII;
  $m = ($binary) ? "binary" : "ascii";
  ec("Uploading file <b>{$src}</b> to <b>{$target}</b> in <b>{$m}</b> mode.");
  return ftp_put($this->cid, $target, $src, $mode);
}

function chdir($fn) {
  ec("Moving to <b>{$fn}</b>...");
  $r = ($ret = ftp_chdir($this->cid, $fn)) 
  ? "<span style='color:green;'>Success.</span>" : "<span style='color:red;'>Error.</span>";
  ec($r);
  return $ret;
  
}
function size($fn, $sprint=1) {
  if ($sprint) {
    $ret =  sprintf ("%u", ftp_size($this->cid, $fn));
  }
  else {
    $ret = ftp_size($this->cid, $fn);  
  }
  return $ret;
}
  
function delete_file($fn) {
  ec("Try to delete file <b>{$fn}</b>...");
  $r = ($ret = ftp_delete($this->cid, $fn)) 
  ? "<span style='color:green;'>Success.</span>" : "<span style='color:red;'>Error.</span>";
  ec($r);
  return $ret;
}

function delete_dir($fn) {  
  ec("Try to delete dir <b>{$fn}</b>...");  
  $r = ($ret = ftp_rmdir($this->cid, $fn))  
  ? "<span style='color:green;'>Success.</span>" : "<span style='color:red;'>Error.</span>";
  ec($r);
  return $ret;
}


function get_size($host, $login="", $pass="", $fn)  {
  
  $ret = -1;
  if ($this->connect($host)) {
    if ($this->login($login, $pass)) {
      $ret = $this->size($fn, 0);
    }
    $this->close();
  }
  
  return $ret;
}

/**
 * checking fir for existance, and try to chdir
 *
 * @param string $path (fullpath from root)
 * @return unknown
 */
function init_dir($path) {
  ec("Going to dir <b>{$path}</b>...");

  if (@ftp_chdir($this->cid, $path))  {
    return true;    
  }
  else {    
    //not working method
    /*
    ec("Trying to make dir <b>{$path}</b>");
    if (ftp_mkdir($this->cid, $path)) {
      ec("Chdir to {$path}");
      ftp_chdir($this->cid, $path);
    }
    else {
      die("Can't make dir <b>{$path}</b> exit.");
    }
    return false;
    */
  }
  
  $c_path = "";
 
  $ar = explode("/", $path);
 
  foreach($ar as $fn) {
    $fn = trim($fn);    
    
    //если пусто то пропускаем
    if ($fn=='') continue;
    
    // добавляем к предыдущему пути файл
    $c_path .= (preg_match("#[/]$#msi", $c_path)) ? $fn : "/".$fn;
    
    // если текущий путь пуст то начинаем с корня
    //$c_path = ($c_path=='') ? "/" : $c_path;
    
    // если мы уже в требуемой папке продолжаем
    //if ($c_path == $this->pwd) continue; 
    
    
    ec("Trying to chdir to <b>{$c_path}</b>");
    if (!@ftp_chdir($this->cid, $c_path)) {  
      ec("False.");
      ec("Trying to make dir <b>{$fn}</b>");
      if (ftp_mkdir($this->cid, $fn)) {
        ec("Success.");
        //$this->pwd();
        
        if (!@ftp_chdir($this->cid, $c_path)) {
          die("Can't chdir to new dir <b>{$c_path}</b> exit.");
        }
        else {
          //nothing go next
        }
      }
      else {
        //nothing go next
        die("Can't make dir <b>{$c_path}</b> exit.");
      }
    }
    else {
      ec("Success.");
    }
  }
  
  $this->pwd();
  return true;
}


//deprecated
function size_from_list($path) {
 
  $dir = dirname($path);
  ec($dir);
  $ar = $this->rawlist_dump($dir);
  
}

function rawlist_dump($path) {
  
  $ftp_rawlist = ftp_rawlist($this->cid, $path);
  $rawlist = array();
  if (is_array($ftp_rawlist)) {
    foreach ($ftp_rawlist as $v) {
      $info = array();
      $vinfo = preg_split("/[\s]+/", $v, 9);
      if ($vinfo[0] !== "total") {
        $info['chmod'] = $vinfo[0];
        $info['num'] = $vinfo[1];
        $info['owner'] = $vinfo[2];
        $info['group'] = $vinfo[3];
        $info['size'] = $vinfo[4];
        $info['month'] = $vinfo[5];
        $info['day'] = $vinfo[6];
        $info['time'] = $vinfo[7];
        $info['name'] = $vinfo[8];
        $rawlist[$info['name']] = $info;
      }
    }
  }
  $dir = array();
  $file = array();
  foreach ($rawlist as $k => $v) {
    if ($v['chmod']{0} == "d") {
      $dir[$k] = $v;
      $is_dir = 1;
    } elseif ($v['chmod']{0} == "-") {
      $file[$k] = $v;
      $is_dir = 0;
    }
    $rawlist['dir'] = $is_dir;
  }
  foreach ($dir as $dirname => $dirinfo) {
    ec("[ $dirname ] " . $dirinfo['chmod'] . " | " . $dirinfo['owner'] . " | " . $dirinfo['group'] . " | " . $dirinfo['month'] . " " . $dirinfo['day'] . " " . $dirinfo['time'] . "<br>");
  }
  foreach ($file as $filename => $fileinfo) {
    ec("$filename " . $fileinfo['chmod'] . " | " . $fileinfo['owner'] . " | " . $fileinfo['group'] . " | " . $fileinfo['size'] . " Byte | " . $fileinfo['month'] . " " . $fileinfo['day'] . " " . $fileinfo['time'] . "<br>");
  }
  
  return array('list' => $rawlist, 'files' => $file, 'dirs' => $dir);
}


}

?>