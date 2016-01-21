<?php

$limit = (isset($dblock_vars[0]))? intval($dblock_vars[0]) : 10;
$ds_goto = 'site_goto';
$ds_details = 'site_details'; 

$sv->load_model('site');

$tr = array();
$exp = $sv->post_time - 60*60*24;
$exp_date = date("Y-m-d", $exp);
$date = date("Y-m-d", $sv->post_time);

// ds.date>='{$exp_date}' AND ds.date<='{$date}'
$ar = array();
$db->q("
SELECT SUM(ds.val) as sum, s.id, s.title, s.url, s.hosts, s.slug, s.hosts_yesterday FROM {$sv->t['daystats']} ds
LEFT JOIN {$sv->t['sites']} s ON (ds.object=s.id)
WHERE ds.page_id='{$ds_goto}' AND ds.date='{$date}'
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
WHERE ds.page_id='{$ds_details}' AND ds.date='{$date}'
GROUP BY ds.object 
", __FILE__, __LINE__);
while($d = $db->f()) { 
  $id = $d['object'];
  $ar[$id]['clicks'] = (!isset($ar[$id]['clicks'])) ? 0 : $ar[$id]['clicks'];
  $ar[$id]['views'] = $d['sum'];
}
foreach($ar as $k=>$d) { 
  if (!isset($d['title'])) continue;
  $d['views'] = (!isset($d['views'])) ? $d['clicks'] : $d['views'];
  $ar[$k] = $d;
}

uasort($ar, "sort_clicks_views");


$i = 0; $views = 0; $clicks = 0;
foreach($ar as $d) { $i++;
  if (!isset($d['title'])) continue;
  if ($i<=$limit)   {
    $d['views'] = (!isset($d['views'])) ? $d['clicks'] : $d['views'];
    $tr[] = "
    <tr valign='top'><td width='1%' align=right><img src='http://favicon.yandex.net/favicon/{$d['url']}' width=\"16\" height=\"16\"></td>
        <td><a href='{$d['url_goto']}'>{$d['title']}</a></td>
        <td width='1%'>{$d['views']}</td>
        <td width='1%'><b>{$d['clicks']}</td>
    </tr>     
    ";
    $views += $d['views'];
    $clicks += $d['clicks'];
  }
  
  
}

if (count($tr)<=0) {
  $tr[] = "<tr><td><i>Нет данных.</i></td></tr>";
}

$dblock_content = "
<div class='goto cont'> 
  <div class='header'>Популярные сайты сегодня</div>
  <table width='100%'>
  ".implode("\n", $tr)."
  </table>
  <div class='legend'><small>В первой колонке - просмотры ({$views}), во второй - переходы ({$clicks}) за сегодняшний день.</small></div>
</div>
";



?>