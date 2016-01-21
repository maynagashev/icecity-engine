<?php

/**
 * Публчный архив голосований
 *
 */
class polllist {
  
function auto_run()   {
  global $sv, $std, $db;

  // $sv->vars['styles'][] = "poll.css"; // @deprecated (preload.php)
 
  $sv->load_model('poll');
  $ret = $sv->m['poll']->scaffold('public_list');
  
  return $ret;
}

}


?>