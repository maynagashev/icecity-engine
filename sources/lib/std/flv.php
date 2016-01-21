<?php

class std_flv {

var $verbose = 1;  
var $log_all = array();
var $last_session = array();

var $mplayer_shot_file = "00000001.jpg";

var $last_info = array();
var $last_steps = array();
var $last_len = 0;

function get_len($fn)   {
  
  $ar = $this->get_info($fn);
  $ret = (isset($ar['ID_LENGTH'])) ? floatval($ar['ID_LENGTH']) : 0;
  
  $this->last_len = $ret;
  return $ret;
}

function get_info($fn) {
  
  if (!file_exists($fn)) {
    echo "File {$fn} not exists in: ".__CLASS__."::".__FUNCTION__;
    return false;
  }
  
  $info =  shell_exec("/usr/local/bin/mplayer -identify \"{$fn}\" -ao null -vo null -frames 0 2>/dev/null");
  
  
  $ar = explode("\n", $info);
  
  
  $raw = array();
  foreach($ar as $row) {
    if (preg_match("#^\s*([^\s=]+)\s*=\s*(.+)\s*$#msi", $row, $m)) {
      $m[1] = trim($m[1]);
      $ret[$m[1]] = trim($m[2]);
      $raw[] = "{$m[1]} = {$m[2]}";
    }
  }  
  
  $this->last_info = $raw;
  
  return $ret;
}
  

function calc_step($count, $len, $tab) {
 
  $range = $len - $tab*2;
  
  $step = ($count==0) ? $range : $range/$count;
  
  return $step;
}
  
function parse_steps($count, $len, $tab_p = 0.05) {
   
  $count = $count - 1; // частей 5, внутренних границ на 1 меньше
  
  $tab_p = abs(floatval($tab_p));
  $tab_p = ($tab_p>1) ? 0.05 : $tab_p;
  
  $tab = $len*$tab_p;
  
  $step = $this->calc_step($count, $len, $tab);
  
  $ret = array();
  
  //tab
  $x = $tab;
  $ret[] = round($x, 2);
  
  //steps
  for ($i = 0; $i<$count; $i++) {
    $x = $x+$step;
    $ret[] = round($x, 2);
  }
  
  $this->last_steps = $ret;
  return $ret;
}
  

function make_shot($src, $ss, $fn, $rewrite = 1) {
  
  if (!file_exists($src)) {
    $this->log("SRC file {$src} not exists in: ".__CLASS__."::".__FUNCTION__);
    return false;
  }
  
  $this->log("Making screenshot {$fn} from {$src}, SEEK: {$ss}", 1);
  
  if (file_exists($this->mplayer_shot_file)) {
    unlink($this->mplayer_shot_file);
  }
  
  $str = "/usr/local/bin/mplayer -nosound -vo jpeg \"{$src}\" -frames 1 -ss {$ss}";
  $sh = shell_exec($str);
  $this->last_make_shot = $sh; 
  if (file_exists($this->mplayer_shot_file)) {
    
    if (file_exists($fn)) {
      if ($rewrite) {
        $this->log("{$fn} exists - rewriting.");
        unlink($fn);
      }
      else {
        $this->log("{$fn} exists - breaking.");
        return false;
      }
    }
    
    // копируем 
    if (copy($this->mplayer_shot_file, $fn)) {      
      return true;      
    }
    else {
      $this->log("Can't copy {$this->mplayer_shot_file} to {$fn}");
      return false;
    }
  }
  else {
    $this->log("Error: mplayer not created shot {$this->mplayer_shot_file}.");
    return false;
  }
  
  
  return true;
}


function log($str, $new_sess = 0) {
  
  if ($this->verbose>0) {
    $eol = ($this->verbose==1) ? "\n" : "<br>";
    //echo $str.$eol;
    ec($str);
  }
  
  if ($new_sess) {
    $this->last_session = array();
  }
  
  $this->last_session[] = $str;
  $this->log_all[] = $str;
  
  return true;
}
    




}

?>