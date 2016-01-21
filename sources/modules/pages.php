<?php

class pages {
  
function auto_run() {
  global $sv, $db;
    
  $sv->vars['js'][] = "pages.js";
  
  $sv->load_model('page');    
  $ret = $sv->m['page']->scaffold();

  
  return $ret;
  
  
}
  
}

?>