<?php

class advshow {
  
function auto_run()   {
  global $sv;  
  $sv->load_model('advshow');
  return $sv->m['advshow']->scaffold();   
}
    
}

?>