<?php

class registration {
  
function auto_run() {
  global $sv;    
  
  if ($sv->user['session']['account_id']>0) {
    $sv->view->show_err_page("Вы уже зарегистрированы и авторизованы.<br><br><a href='/log/?action=out&return_url={$sv->view->safe_url}'>Выйти</a>");
  }
  
  $sv->load_model('account');
  return $sv->m['account']->scaffold('public_registration');
  
}

}  
    
?>