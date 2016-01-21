<?php

class urls {
  
function auto_run()   {
  global $sv, $std, $db;  
  $sv->load_model('url');
  return $sv->m['url']->scaffold();   
}
    
}

?>