<?php 
  
class video {
  
function auto_run() {
  global $sv, $std;
  
  $sv->init_class('model');
  $sv->load_model('movie');
  
  $ar = $sv->m['movie']->item_list("`scr_created`='0'", "", 0, 1);
 
  foreach($ar['list'] as $d){
    $this->create_screen($d);
  }
  
  
}

function create_screen($d) {
  global $sv, $std, $db;
  
  if ($d['scr_retries'] >= $sv->m['movie']->max_retries) {
    ec("max_retries {$sv->m['movie']->max_retries} reached, next..");
    return false;
  }
  $p = array();
  $p['scr_retries'] = $d['scr_retries'] + 1;
  $sv->m['movie']->update_row($p, $d['id']);
  
  if (!file_exists($d['save_path'])) {
    $errm = "movie {$d['save_path']} not exists";  ec($errm);
    $sv->m['movie']->update_row(array('last_err' => $errm), $d['id']);   
    return false;
  }
  
  $r = $std->flv->auto_make_screen($d['save_path'], $d['scr_path'], 0.1);
  
  if (!file_exists($d['scr_path']) || filesize($d['scr_path'])<=0) {
    $errm = implode("<br>\n ", $std->flv->last_session);  ec($errm);
    $sv->m['movie']->update_row(array('last_err' => $errm), $d['id']);        
    return false;
  }
  else {
    $len = $std->flv->last_len;
  }
  
  $sv->m['movie']->update_row(array('scr_created' => 1, 'duration' => $len), $d['id']);   
  
  $this->make_tmb($d);
}

function make_tmb($d) {
 global $sv, $std, $db;
  
  if ($d['tmb_retries'] >= $sv->m['movie']->max_retries) {
    ec("max_retries {$sv->m['movie']->max_retries} reached, next..");
    return false;
  }
   
  $p = array();
  $p['tmb_retries'] = $d['tmb_retries'] + 1;  
  $sv->m['movie']->update_row($p, $d['id']);
  

  if (!file_exists($d['scr_path'])) {
    $errm = "screen {$d['scr_path']} not exists";  ec($errm);
    $sv->m['movie']->update_row(array('last_err' => $errm), $d['id']);   
    return false;
  }
  
  $r = $std->resize->auto_fixed($d['scr_path'], $d['tmb_path'], 120, 90);
  if (!file_exists($d['tmb_path']) || filesize($d['tmb_path'])<=0) {
    $errm = implode("<br>\n ", $std->resize->last_session);  ec($errm);
    $sv->m['movie']->update_row(array('last_err' => $errm), $d['id']);        
    return false;
  }
   
  $sv->m['movie']->update_row(array('tmb_created' => 1), $d['id']);   
  

}
}

?>