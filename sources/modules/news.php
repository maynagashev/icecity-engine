<?php

/* Редактор новостей */

class news {  
function auto_run()   {
  global $sv;  
  $sv->load_model('news');  
  return $sv->m['news']->scaffold();
}
}

?>