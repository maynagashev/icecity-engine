<?php

/**
 * Админская часть "Заказы"
 *
 */

class orders {
  
function auto_run()   {
  global $sv, $std, $db;  
  $sv->load_model('order');
  return $sv->m['order']->scaffold();   
}
    
}

?>