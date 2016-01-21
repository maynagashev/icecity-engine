<?php

/* Список каналов */

class channels {  
function auto_run()   {
  global $sv;  
  $sv->load_model('channel');  
  return $sv->m['channel']->scaffold();
}
}

?>