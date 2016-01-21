<?php

/**
 * публичная выдача RSS ленты
 *
 */

class feed {
  
function auto_run()   {
  global $sv, $std, $db;  
  $sv->load_model('feed');
  return $sv->m['feed']->scaffold('public_default');   
}
    
}

?>