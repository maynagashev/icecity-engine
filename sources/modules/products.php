<?php

/**
 * Реадактор товаров для магазина, админ.
 *
 */
class products {
  
function auto_run()   {
  global $sv;  
  $sv->load_model('product');
  return $sv->m['product']->scaffold();    
}
     
}

?>