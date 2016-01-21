<?php

if ($sv->view->page['slug']!='news') {
  
  $sv->load_model('rss_data');
  
  
  $err = 0;
  $db->q("SELECT * FROM {$sv->t['rss_data']} ORDER BY date DESC LIMIT 0, 10", __FILE__, __LINE__);
  if ($db->nr()>0) {
    $d = $db->f();
    $f = $sv->m['rss_data']->parse($d);
    
  }
  else {
    $err = 1;
  }
  
  if (!$err) {
    $tr = array();
    while($d = $db->f()) {
       $d = $sv->m['rss_data']->parse($d);
       $tr[] = "<tr><td style='padding: 5px 0;'><b><small><span style='color:yellow;'>{$d['f_date']}</small></span></b><br>
        <a href='{$d['url']}'>{$d['title']}</a></td></tr>";
    }
    
    
    $mlist = $sv->m['rss_data']->month_list();
    $years = array();
    foreach($mlist as $year => $months) {
      $links = array();
      foreach($months as $m_id => $m) {
        $links[] = "<a href='{$sv->m['rss_data']->news_url}month/?id={$m_id}&year={$year}'>{$m['title']}</a>";
      }
      $years[] = "<tr><td width='10%'>{$year}г.</td><td>".implode(", ", $links)."</td>";
    }
    
    $dblock_content = "
    <h2><a href='/news/' style='color:white;'>Новости Норильска</a></h2>
    <table width='300' cellpadding=3 cellspacing=2 style='margin-bottom:30px;'>
    
    <tr><td><h5>{$f['title']}</h5>
    {$f['ann']}
    <div style='text-align:right;'><i><a href='{$f['url']}'>{$f['f_date']}</a></i></div>
    
    </td></tr>
    <tr><tD>
      <table width='100%'>
        ".implode("\n", $tr)."
      </table>
    </td></tr>
    <tr><td><b><a href='/news/'>Архив новостей</a></b> &rarr;</td></tr>
    <tr><td>
      <table width='100%'>
        ".implode("\n", $years)."
      </table>
    </td></tr>
    </table>
    ";
    
  }
}
else {
  $err = 1;
}

if ($err) {
  $dblock_content = "";
}
?>