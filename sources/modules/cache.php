<?php

class cache {
  
function auto_run()   {
  global $sv;
  $sv->load_model('cache'); 
  return $sv->m['cache']->scaffold();
}

}

?>