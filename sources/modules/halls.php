<?php

/* Редактор концертных-кино-выставочных площадок */

class  halls {  
function auto_run()   {
  global $sv;  
  $sv->load_model('hall');  
  return $sv->m['hall']->scaffold();
}
}

?>