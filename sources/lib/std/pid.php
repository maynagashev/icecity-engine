<?php

class std_flv {

var $verbose = 1;  
var $log_all = array();
var $last_session = array();

var $pid_file = "";
var $data = "";

function init_pid($file="") {  
  if ($file=='') {
    die("not specified file name for PID in init_pid");
  }
  $this->pid_file = $file;
  
  if(file_exists($this->pid_file)) {
    $data = $this->read_pid();
    
  }
  else {
    $this->write_pid();
  }
}

function write_pid(){ 
  
}


}

?>