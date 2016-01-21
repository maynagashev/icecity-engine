<?php

class errpage {

  var $return_url = '/';
  
function auto_run()   {
  global $sv, $std, $db;  

  
  // rewrite return folders fix
  if (isset($sv->vars['log_return_url']) && $sv->vars['log_return_url']!='') {
    $this->return_url = $sv->vars['log_return_url'];
  }

  
  $ret['errm'] = $sv->vars['errm'];
  $ret['return_url']  = $this->return_url;
   
  return $ret;
}
    
}

?>