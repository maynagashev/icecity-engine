<?php

/**
 * Если авторизован то работаем через базу,
 * иначе работаем чисто с куками.
 *
 * Тут вызываем только основную функцию работы с корзиной указывая соотвествующий код, там все уже разбирается
 */
class basket {
  
var $codes = array(
  'add', 'update', 'empty', 'order'
);

function auto_run()   {
  global $sv;  

  $sv->load_model('product');
  $sv->load_model('basket');
  
  $code = (in_array($sv->code, $this->codes)) ? $sv->code : 'default';
  $ret = $sv->m['basket']->scaffold("public_".$code);
  
  return $ret;
}


//eoc
}
?>