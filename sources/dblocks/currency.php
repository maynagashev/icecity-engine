<?php

$sv->load_model('cache');
$p = $sv->m['cache']->read('currency', 1, 1);

$ar = array('usd' => '&#036;', 'euro' => '&euro;');
$tr = array();
foreach($ar as $k=>$t) {
  if (!isset($p[$k])) continue;
  $d = $p[$k];
  $class = ($d['class']=='green') ? 'up' : 'down';
  $date = str_replace("/", ".", $d['date']);
  $tr[] = "
    <tr class='{$class}'>
    <td class='currency'>{$t}</td>
    <td class='date'>{$date}</td>
    <td class='delta'>{$d['delta']}</td>
    <td>{$d['val']}</td>
    </tr>    
  ";
}

$dblock_content = "
<p class='title'>Курсы валют ЦБ РФ</p>
<div class='content'>
<table class='b-table'>
".implode("\n", $tr)."
</table>
<!--<p><a href='#' title='Котировки в банках Иваново'>Котировки в банках Иваново</a></p>-->
</div>
";



?>