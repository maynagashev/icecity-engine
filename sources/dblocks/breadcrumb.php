<?php

/**
 * Строка навигации с путем до текущего раздела 
 */

$ar = $sv->m['page']->build_breadcrumb(0, 1, 0); // page_id, with_home, parse

if (isset($sv->vars['breadcrumbs']) && is_array($sv->vars['breadcrumbs'])) {
  $ar = array_merge($ar, $sv->vars['breadcrumbs']);
}

if (!is_array($ar)) {
  $dblock_content = "";
}
else {

  $tr = array();
  foreach($ar as $d) {    
    $d['url'] = (isset($d['url'])) ? $d['url'] : '';
    if ($d['url']=='/') continue;
    
    $d['url'] = ($d['url']!='' && !preg_match("#/$#si", $d['url'])) ? $d['url']."/" : $d['url'];
    $link = ($d['url']!='') ? "<a href='{$d['url']}'>" : "";
    $tr[] = "<li>{$link}{$d['title']}</a></li>";
  }
  
  $dblock_content = "<ul class='b-menu b-menu-pathway'>".implode("\n", $tr)."</ul>";
}


?>