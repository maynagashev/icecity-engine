<?php

 
  
/*
<A HREF="http://stock.rbc.ru/demo/cb.0/daily/USD.rus.shtml?show=3M">USD ЦБ РФ</A></span></div>
</TD>
<TD>01/09</TD>

<TD align=right>31.8397</TD>
<TD align=right class="green">0.271</TD>
</TR>
<TR>
<TD><div class="indexes green"><i></i><span><A HREF="http://stock.rbc.ru/demo/cb.0/daily/EUR.rus.shtml?show=3M">EUR ЦБ РФ</A></span></div>
</TD>
<TD>29/08</TD>
<TD align=right>45.3011</TD>
<TD align=right class="green">0.2229</TD>

*/
  

class currency {
  
function auto_run() {
  global $sv, $std, $db;
  
  // get data
  $f = $std->curl->get_file('http://www.rbc.ru');
  
  $p = array('usd' => false, 'euro' => false);
  
  //usd
  if (preg_match("#show=3M\">USD ЦБ РФ</A></span></div>\s*</TD>\s*<TD>([^<>]+)</TD>\s*<TD align=right>([^<>]+)</TD>\s*<TD align=right class=\"([^\"]+)\">([^<>]+)</TD>\s*#si", $f, $m)) {
    $p['usd'] = array('date' => $m[1], 'val' => $m[2], 'class' => $m[3], 'delta' => $m[4]);
  }
  //euro
  if (preg_match("#show=3M\">EUR ЦБ РФ</A></span></div>\s*</TD>\s*<TD>([^<>]+)</TD>\s*<TD align=right>([^<>]+)</TD>\s*<TD align=right class=\"([^\"]+)\">([^<>]+)</TD>\s*#si", $f, $m)) {
    $p['euro'] = array('date' => $m[1], 'val' => $m[2], 'class' => $m[3], 'delta' => $m[4]);
  }
   
  
  $sv->init_class('model');
  $sv->load_model('cache');
  $old = $sv->m['cache']->read('currency', 1, 1);  
  
  foreach($p as $k => $ar) {
    if ($ar) {
      $old[$k] = $ar;
    }
  }
  
  $sv->m['cache']->write('currency', $old, 1);
  print_r($old);
}

}

?>