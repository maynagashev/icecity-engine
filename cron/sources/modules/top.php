<?php

class top {
  
function auto_run() {
  global $sv, $std, $db;
  
  $sv->init_class('model');
     
  $sv->load_model('cache');
  $sv->load_model('daystat');
  $sv->load_model('article');
  $sv->load_model('movie');
  $sv->load_model('post');
 
 
  
  $exp = $sv->post_time - 60*60*24*2;
  $exp_date = date("Y-m-d", $exp);
  
  
  $cache = array(
    'articles' => array(),
    'posts' => array(),
    'video' => array()
  );
  
  // articles
  $q = "SELECT s.page_id, s.object, s.val, SUM(s.val) as sum,  
        o.title, o.slug, c.url as cat_url, o.replycount, o.author 
        FROM {$sv->t['daystats']} s
        INNER JOIN {$sv->t['articles']} o ON (s.object=o.id)
        LEFT JOIN {$sv->t['maincats']} c ON (o.cat_id=c.id)
        WHERE s.page_id='views' AND s.date>'{$exp_date}' AND s.date<=NOW()
        GROUP BY s.object ORDER BY s.val DESC LIMIT 0, 10
        ";
  $ar = array();
  $db->q($q, __FILE__, __LINE__);
  while($d = $db->f()) {  
    $d = $sv->m['article']->parse($d);
    $ar[] = $d;
  }
  usort($ar, "sort_sum");  
  $cache['articles'] = $ar;
  
  // posts
  $q = "SELECT s.page_id, s.object, s.val, SUM(s.val) as sum, 
        o.title, o.slug, o.user_slug, o.replycount
        FROM {$sv->t['daystats']} s
        INNER JOIN {$sv->t['posts']} o ON (s.object=o.id)
        WHERE s.page_id='views_post' AND s.date>'{$exp_date}' AND s.date<=NOW()
        GROUP BY s.object ORDER BY s.val DESC LIMIT 0, 10
        ";
  $ar = array();
  $db->q($q, __FILE__, __LINE__);
  while($d = $db->f()) {   
    $d = $sv->m['post']->parse($d, 1); 
    $ar[] = $d;
  }
  usort($ar, "sort_sum");  
  $cache['posts'] = $ar;
   
  
  // video
  $q = "SELECT s.page_id, s.object, s.val, SUM(s.val) as sum, 
        o.title, o.id, o.replycount  
        FROM {$sv->t['daystats']} s
        INNER JOIN {$sv->t['movies']} o ON (s.object=o.id)
        WHERE s.page_id='views_video' AND s.date>'{$exp_date}' AND s.date<=NOW()
        GROUP BY s.object ORDER BY s.val DESC LIMIT 0, 10
        ";
  $ar = array();
  $db->q($q, __FILE__, __LINE__);
  while($d = $db->f()) {
    $d = $sv->m['movie']->parse($d, 1);
    $ar[] = $d;
  }
  usort($ar, "sort_sum");  
  $cache['video'] = $ar;
    
 
  $sv->m['cache']->sync("top");
  $sv->m['cache']->write('top', $cache, 1);
  ec("Cache writed.");
}


  
}


function sort_sum($a, $b) {
  if ($a['sum'] == $b['sum']) { 
    if (isset($a['replycount']) && isset($b['replycount']) && $a['replycount']!=$b['replycount']) {
      return ($a['replycount']<$b['replycount']) ? 1 : -1;
    }
    else {
      return 0; 
    }
  }
  return ($a['sum'] < $b['sum']) ? 1 : -1;
}

?>