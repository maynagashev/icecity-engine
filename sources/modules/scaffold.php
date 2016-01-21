<?php

/**
 * Стандартный модуль редакторов.
 *
 */
class scaffold {
  
function auto_run()   {
  global $sv;  
  
  $model = $this->vars['model'];
  $sv->load_model($model);
  
  return $sv->m[$model]->scaffold();    
}
     
}

?>