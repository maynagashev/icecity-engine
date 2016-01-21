<?php

/* главная страница форума, список фрумов */

class forum {

  var $codes = array();
  
function auto_run()   {
  global $sv, $std, $db;
  
  $sv->vars['styles'][] = "forum.css";
  $sv->vars['js'][] = 'forum.js';
  
  $sv->load_model('forum');  
  $ret = $sv->m['forum']->init_forum();
  
  return $ret;
}
  

//eoc
}

?>