<?php

/**
 * Редактор настроек сайта
 * 
 */

class config {  
function auto_run()   {
  global $sv;  
  $sv->load_model('config');  
  return $sv->m['config']->scaffold();
}
}

?>