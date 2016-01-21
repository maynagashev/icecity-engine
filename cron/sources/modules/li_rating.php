<?php

class li_rating {
  
function auto_run()   {
  global $sv, $std;
  
  $urls = array(
    "http://www.liveinternet.ru/rating/ru/index.html?search=%D0%B0%D0%B1%D0%B0%D0%BA%D0%B0%D0%BD;only=off",
    "http://www.liveinternet.ru/rating/ru/index.html?search=%D0%B0%D0%B1%D0%B0%D0%BA%D0%B0%D0%BD;only=off;page=2",
    "http://www.liveinternet.ru/rating/ru/index.html?search=%D0%B0%D0%B1%D0%B0%D0%BA%D0%B0%D0%BD;only=off;page=3"
    );
    
    
  $ar = array();
  foreach($urls as $url) {
    $tar = $this->parse_page($url);
    $ar = array_merge($ar, $tar);
  }

  if (count($ar)>0) {
    $tr = array();
    foreach($ar as $d) {
      $tr[] = "<tr><tD class='r-i'>{$d['i']}.</td><td class='r-title'>{$d['title']}</td><td class='r-url'>{$d['url']}</td><td class='r-count'>{$d['count']}</td></tr>";
    }
    echo $body = "<table>".implode("\n", $tr)."</table>
    <div class='update-time'><span>{$sv->date_time}</span></div>
    ";
   
    // записываем
    $body = mb_convert_encoding($body, "cp1251", "utf8");
    $sv->load_model('cache');
    $sv->m['cache']->sync('li_rating', $body);
    $sv->m['cache']->write_with_check('li_rating', $body);
    
  }   
  else {
    ec("Nothing to write.");
  }
}


function parse_page($url) {
  global $std;
  
  $f = $std->curl->get_file($url);

  $ret = array();
  if (preg_match_all("#([0-9]+)\.\ &nbsp;</font></td>\s*<td width=450>&nbsp;<a name=\"[^\"]*\"[^>]*href=\"([^\"]*)\"[^>]*>([^<]*|[^<]*<font[^>]*>[^<]*</font>[^<]*)</a></td>\s*<TD align=right width=100>([0-9\,\.]+)</td>\s*<TD align=right width=20><a href=\"([^\"]*)\"><img width=12 height=12\s*src=\"([^\"]*)\"#si", $f, $m)) {
   // print_r($m); 
    foreach($m[1] as $k => $i) {
      $url_full =  strip_tags($m[2][$k]);
      $url = preg_replace("#^http\://#si", "", $url_full);
      $url = preg_replace("#/$#si", "", $url);
      
      $p = array(
        'i' => $i,
        'title' => strip_tags($m[3][$k]),
        'url' => $url,
        'url_full' => $url_full,
        'count' => $m[4][$k],
        'stat_url' => strip_tags("http://www.liveinternet.ru".$m[5][$k]),
        'public' => ((preg_match("#public#si", $m[6][$k])) ? 1 : 0)
      );
      $ret[$i] = $p;
    }
  }  
  return $ret;
}

}

?>

