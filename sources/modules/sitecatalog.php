<?php

class sitecatalog {
 
  var $codes = array(
  'details', 
  'goto'
  );
  
function auto_run()   {
  global $sv;
  
  $sv->code = (in_array($sv->code, $this->codes)) ? $sv->code : "";
  
  if ($sv->code=='') {
    $sv->view->show_err_page('notfound');
  }
  
  $sv->load_model('site');
  $id = (isset($sv->_get['id'])) ? intval($sv->_get['id']) : 0;
  $d = $sv->m['site']->get_item($id, 1);
  
  if (!$d) {
    $sv->view->show_err_page('notfound');
  }
  
  $sv->load_model('daystat');
  
  if ($sv->code=='goto') {
    $sv->m['site']->update_row(array('clicks' => $d['clicks']+1), $id);
    $sv->m['daystat']->update_stats('clicks', $id);
    
    header("Location: {$d['url']}");
    die("Location: {$d['url']}");
  }
  else {
    $sv->m['site']->update_row(array('views' => $d['views']+1), $id);
    $sv->m['daystat']->update_stats('views', $id);
  }
  
  $ret['d'] = $d;
  
  return $ret;
}


}

?>