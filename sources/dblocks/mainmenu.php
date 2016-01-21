<?php

/*
$dblock_content2 = "
<ul class='b-menu b-menu-main'>
<li class='item1'><em><span>Банки Иваново</span></em>
  <ul>
    <li><a href='#' title='Банки Иваново'>Банки Иваново</a></li>
    <li><a href='#' title='Банкоматы'>Банкоматы</a></li>
    <li><a href='#' title='Обменные пункты'>Обменные пункты</a></li>
    <li class='active'><a href='#' title='Курсы обмена валюты'>Курсы обмена валюты</a></li>
    <li><a href='#' title='Блоги банков'>Блоги банков</a></li>
    <li><a href='#' title='Интервью'>Интервью</a></li>
    <li><a href='#' title='Вопрос-ответ'>Вопрос-ответ</a></li>
    <li><a href='#' title='Форум'>Форум</a></li>
  </ul>
</li>
<li class='item2'><em><span>Для юридических лиц</span></em>
  <ul>
    <li class='new'><a href='#' title='Депозиты'>Депозиты</a></li>
    <li><a href='#' title='Расчетно-кассовое обслуживание'>Расчетно-кассовое обслуживание</a></li>
    <li><a href='#' title='Кредитование'>Кредитование</a></li>
    <li><a href='#' title='Аренда банковских сейфов'>Аренда банковских сейфов</a></li>
    <li><a href='#' title='Операции с драг. металлами'>Операции с драг. металлами</a></li>
    <li><a href='#' title='Дистанционное обслуживание'>Дистанционное обслуживание</a></li>
    <li><a href='#' title='Инвестиции'>Инвестиции</a></li>
  </ul>
</li>
<li class='item3'><em><span>Для физических лиц</span></em>
  <ul>
    <li><a href='#' title='Депозиты'>Депозиты</a></li>
    <li><a href='#' title='Расчетно-кассовое обслуживание'>Расчетно-кассовое обслуживание</a></li>
    <li><a href='#' title='Кредитование'>Кредитование</a></li>
    <li><a href='#' title='Аренда банковских сейфов'>Аренда банковских сейфов</a></li>
    <li><a href='#' title='Операции с драг. металлами'>Операции с драг. металлами</a></li>
    <li><a href='#' title='Пластиковые карты'>Пластиковые карты</a></li>
    <li><a href='#' title='Денежные переводы'>Денежные переводы</a></li>
  </ul>
</li>
<li class='item4'>
  <ul>
    <li><a href='#' title='О портале'>О портале</a></li>
    <li><a href='#' title='Контактная информация'>Контактная информация</a></li>
    <li><a href='#' title='Презентация'>Презентация</a></li>
    <li><a href='#' title='Сделать стартовой'>Сделать стартовой</a></li>
    <li><a href='#' title='Добавить в закладки'>Добавить в закладки</a></li>
  </ul>
</li>
</ul>
";
*/

$menu = array(
  1 => array(
    '/banks/' => 'Банки Иваново',
    '/banks/branches/' => 'Отделения и филиалы',
    '/banks/bankomats/' => 'Банкоматы',
    '/banks/currency/' => 'Курсы обмена валюты',
    '/banks/exchange/' => 'Адреса обмена валют',
    '/banks/transfers/' => 'Денежные переводы',
    '/read/interview/' => 'Интервью',
  ),
  2 => array(
    '/corporate/deposits/' => "Депозиты",
    '/corporate/rko/' => "Расчетно-кассовое обслуживание",
    '/corporate/credits/' => "Кредитование",
    /* '/corporate/safe/' => "Аренда банковских сейфов",
    '/corporate/metals/' => "Операции с драг. металлами" */
  ),
  3 => array(
    '/individual/vklady/' => "Вклады",
    '/individual/credits/' => "Кредитование",    
    '/individual/safe/' => "Аренда банковских сейфов",
    '/individual/metals/' => "Операции с драг. металлами"
  ),
  4 => array(
    '/forum/' => 'Форум',
    '/about/' => 'О портале',
    '/partners/' => 'Партнеры',    
    '/map/'   => 'Карта сайта',
    '/contacts/' => 'Контактная информация',
    '/splash.html' => "Презентация"
  ),
);

$tr = array();
foreach($menu as $k => $ar) {
  foreach($ar as $url => $t) {
    if ($url=='/banks/' && preg_match("#(branches|bankomats|exchange|currency|transfers)#si", $sv->view->safe_url)) {
       $s = '';
    }
    else {
      $s = (preg_match("#^".preg_quote($url)."#si", $sv->view->safe_url)) ? " class='active'" : "";    
    }
    $b = ($url=='/forum/') ? "<b>":'';
    $tr[$k][] = "<li{$s}>{$b}<a href='{$url}' title='{$t}'>{$t}</a></b></li>";
  }
}

$dblock_content = "
<ul class='b-menu b-menu-main'>
<li class='item1'><em><span>Банки Иваново</span></em>
  <ul>
   ".implode("\n", $tr[1])."

  </ul>
</li>
<li class='item2'><em><span>Для юридических лиц</span></em>
  <ul>
   ".implode("\n", $tr[2])."

  </ul>
</li>
<li class='item3'><em><span>Для физических лиц</span></em>
  <ul>
   ".implode("\n", $tr[3])."
  </ul>
</li>
<li class='item4'>
  <ul>
   ".implode("\n", $tr[4])."
  </ul>
</li>
</ul>

";

?>