<?php

class search {
  
var $codes = array('results', 'query');

function auto_run() {
  global $sv;
  
  $code = (in_array($sv->code, $this->codes)) ? $sv->code : 'default';
  $sv->load_model('search');
  
  $sv->m['search']->use_models = array(    
    'bank' => 'Банки',
    'page' => 'Страницы сайта',
    'article' => 'Новости и статьи',
    'forum' => 'Форумы',
    'fpost' => 'Сообщения на форуме'  
  );
  
  return $sv->m['search']->scaffold("public_{$code}");
}
  
//eoc

}

?>