<?php

class advstreams {
  
function auto_run()   {
  global $sv;  
  $sv->load_model('advstream');
  return $sv->m['advstream']->scaffold();   
}
    
}

?>