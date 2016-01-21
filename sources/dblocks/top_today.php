<?php
$limit = 30;

$sv->load_model('site');

$tr = array();
$exp = $sv->post_time - 60*60*24;
$exp_date = date("Y-m-d", $exp);
$date = date("Y-m-d", $sv->post_time);

$ar = array();
$db->q("
SELECT SUM(ds.val) as sum, s.id, s.title, s.url FROM {$sv->t['daystats']} ds
LEFT JOIN {$sv->t['sites']} s ON (ds.object=s.id)
WHERE ds.page_id='clicks' AND ds.date>='{$exp_date}' AND ds.date<='{$date}'
GROUP BY ds.object ORDER BY ds.val DESC
", __FILE__, __LINE__);
while($d = $db->f()) {  
  $d['clicks'] = $d['sum'];
  $d = $sv->m['site']->parse($d);
  unset($d['val']);
  $ar[$d['id']] = $d;
}

$db->q("
SELECT SUM(ds.val) as sum, ds.object FROM {$sv->t['daystats']} ds
WHERE ds.page_id='views' AND ds.date>='{$exp_date}' AND ds.date<='{$date}'
GROUP BY ds.object 
", __FILE__, __LINE__);
while($d = $db->f()) { 
  $id = $d['object'];
  $ar[$id]['clicks'] = (!isset($ar[$id]['clicks'])) ? 0 : $ar[$id]['clicks'];
  $ar[$id]['views'] = $d['sum'];
}

uasort($ar, "sort_clicks_views");


$i = 0;
foreach($ar as $d) { $i++;
  $d['views'] = (!isset($d['views'])) ? $d['clicks'] : $d['views'];
  $tr[] = "
  <tr><td width='1%' align=right>{$i}.</td>
      <td><a href='{$d['url_details']}'>{$d['title']}</a></td>
      <td width='1%' align=center style='border:1px solid white;'>{$d['views']}</td>
      <td width='1%' align=center style='border:1px solid white;'><b>{$d['clicks']}</td>
  </tr>     
  ";
  if ($i>=$limit)   {
    break;
  }
}



$dblock_content = "
<h2>Популярные сайты сегодня</h2>
<table width='300' cellpadding=3 cellspacing=2>
".implode("\n", $tr)."
</table>
<div style='margin-top: 10px;'><small><b>Обозначения:</b> <br>
в первой колонке - просмотры, во второй - переходы.</small></div>
";

?>