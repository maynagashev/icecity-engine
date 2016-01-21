<?php

class tools {
  
var $codes = array(
  'captcha',
  'email'
);
  
function auto_run() {
  global $sv;
  
  $sv->load_model('antispam');
  $sv->m['antispam']->bg_red = 255;
  $sv->m['antispam']->bg_green = 255;
  $sv->m['antispam']->bg_blue = 255;
  
  
  $sv->code = (in_array($sv->code, $this->codes)) ? $sv->code : "";
  switch ($sv->code) {
    
    // Показ капчи
    case 'captcha':      
      $sv->m['antispam']->paint_captcha();
    break;
    
    // Показ email'a
    case 'email':
      $sv->m['antispam']->paint_email();
    break;
    
    default:
      $sv->view->show_err_page("notfound");
    break;
  }
  
  
  exit("--stop--");
}


}

?>