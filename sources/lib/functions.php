<?php
    
       
function t($var,$rows=10, $dump=false )   {
  global $trace_ips;
  
  $ip = getenv("REMOTE_ADDR");
  if (!isset($trace_ips) || !is_array($trace_ips)) return false;
  
  $in_list = 0;
  foreach($trace_ips as $pat) {
    $pat = preg_quote($pat);
    if (preg_match("#^{$pat}#si", $ip)) {
      $in_list = 1;
    }
  }
  if (!$in_list) { return false; }
  
  ob_start();     
    if ($dump) {
      var_dump($var);
    }
    else {
      print_r($var);
    }
    $ret = ob_get_contents();
  ob_end_clean();

  
  echo "<textarea style='width:100%; font-size: 10px;' rows={$rows}>{$ret}</textarea>";
  return $ret;
}

function dump2txt( $var, $var_dump = 0 )   {  
  ob_start();     
    if ($var_dump) {
      var_dump($var);
    }
    else {
      print_r($var);
    }
    $ret = ob_get_contents();
  ob_end_clean();

  return $ret;
}

// ----------------------------------------------------------------------------
  function u($act="", $code="", $id="",  $other="" ){
    global $sv;
    
    if ($sv->use_rewrite) {      
      $ret = "/";
      if ($act!='') {
        $ret .= $act."/";
        $code = ($code=='' && $id!='') ? "default" : $code;
        if ($code!='') {
          $ret .= $code."/";
          if ($id!='') {
            $ret .= $id."/";
            if ($other!='') {
              $ret .= $other;
            }
          }
        }       
      }
      
    }
    else {
      $script = (defined("DISPATCH")) ? DISPATCH : "index.php";
      $ret = $script.
        (($act!="") ? "?".$act : "").
        (($code!="") ? "_".$code : "").
        (($id!="") ? "=".$id : "").
        (($other!="") ? "&".$other : ""); 
    }
    return $ret;      
  }
  

  function su($act="", $code="", $id="",  $other="" ){   
    global $sv;
    $file = basename(getenv("SCRIPT_NAME"));
    
    
    if ($sv->use_rewrite) {      
      $ret = "/";
      if ($act!='') {
        $ret .= $act."/";
        $code = ($code=='' && $id!='') ? "default" : $code;
        if ($code!='') {
          $ret .= $code."/";
          if ($id!='') {
            $ret .= $id."/";
            if ($other!='') {
              $ret .= $other;
            }
          }
        }       
      }
      
    }
    else {      
      
      if (isset($sv->sub_url) && $sv->sub_url!==false) {      
        $ret = $sv->sub_url.
        //(($act!="") ? "?".$act : "").
        (($code!="") ? "&sub=".$code : "").
        (($id!="") ? "&id=".$id : "").
        (($other!="") ? "&".$other : "");       
        
      }
      else {          
        $ret = $file.
          (($act!="") ? "?".$act : "?").
          (($code!="") ? "_".$code : "").
          (($id!="") ? "=".$id : "").
          (($other!="") ? "&".$other : ""); 
      }
    }
    return $ret;      
  }
  

  function url($ar){
    global $sv;
    $act = $ar['act'];
    $code = $ar['code'];
    $id = $ar['id'];
    $other = $ar['other'];
      
 
    if ($sv->use_rewrite) {      
      $ret = "/";
      if ($act!='') {
        $ret .= $act."/";
        $code = ($code=='' && $id!='') ? "default" : $code;
        if ($code!='') {
          $ret .= $code."/";
          if ($id!='') {
            $ret .= $id."/";
            if ($other!='') {
              $ret .= $other;
            }
          }
        }       
      }
      
    }
    else {
      $script = (defined("DISPATCH")) ? DISPATCH : "index.php";
      $ret = $script.
        (($act!="") ? "?".$act : "").
        (($code!="") ? "_".$code : "").
        (($id!="") ? "=".$id : "").
        (($other!="") ? "&".$other : ""); 
    }
    return $ret;      
  }
  

  function sub_url($ar){
    global $sv;
    
    $act = $ar['act'];
    $code = $ar['code'];
    $id = $ar['id'];
    $other = $ar['other'];
    $file = basename(getenv("SCRIPT_NAME"));
    
  
 
    if ($sv->use_rewrite) {      
      $ret = "/";
      if ($act!='') {
        $ret .= $act."/";
        $code = ($code=='' && $id!='') ? "default" : $code;
        if ($code!='') {
          $ret .= $code."/";
          if ($id!='') {
            $ret .= $id."/";
            if ($other!='') {
              $ret .= $other;
            }
          }
        }       
      }
      
    }
    else {
      
      if (isset($sv->sub_url) && $sv->sub_url!==false) {     
        
        $ret = $sv->sub_url.
        //(($act!="") ? "?".$act : "").
        (($code!="") ? "&sub=".$code : "").
        (($id!="") ? "&id=".$id : "").
        (($other!="") ? "&".$other : "");       
        
      }
      else {      
        $ret = $file.
          (($act!="") ? "?".$act : "").
          (($code!="") ? "_".$code : "").
          (($id!="") ? "=".$id : "").
          (($other!="") ? "&".$other : ""); 
      }
      
    }
    
    
    return $ret;      
  }
    
function get_auth_headers() {
  header("WWW-authenticate: basic realm='Authorization required...'");
  header("HTTP/1.0 401 Unauthorized");

  echo  "<br /><center><b>Введены некорректные данные для доступа к ресурсу.</center>\n";
  echo  "<br /><center><b><a href='javascript: history.go(-1)'>Вернуться</a></center>\n";
  exit();
}    



function json_encode_string($in_str) {
  mb_internal_encoding("UTF-8");
  $convmap = array(0x80, 0xFFFF, 0, 0xFFFF);
  $str = "";
  for($i=mb_strlen($in_str)-1; $i>=0; $i--)
  {
    $mb_char = mb_substr($in_str, $i, 1);
    if(mb_ereg("&#(\\d+);", mb_encode_numericentity($mb_char, $convmap, "UTF-8"), $match))  {
      $str = sprintf("\\u%04x", $match[1]) . $str;
    }
    else  {
      $str = $mb_char . $str;
    }
  }
  return $str;
}

function php_json_encode($arr) {
  $json_str = "";
  if(is_array($arr)) {
    $pure_array = true;
    $array_length = count($arr);
    for($i=0;$i<$array_length;$i++) {
      if(! isset($arr[$i])) {
        $pure_array = false;
        break;
      }
    }
    if($pure_array)   {
      $json_str ="[";
      $temp = array();
      for($i=0;$i<$array_length;$i++)   {
        $temp[] = sprintf("%s", php_json_encode($arr[$i]));
      }
      $json_str .= implode(",",$temp);
      $json_str .="]";
    }
    else   {
      $json_str ="{";
      $temp = array();
      foreach($arr as $key => $value)     {
        $temp[] = sprintf("\"%s\":%s", $key, php_json_encode($value));
      }
      $json_str .= implode(",",$temp);
      $json_str .="}";
    }
  }
  else  {
    if(is_string($arr))    {
      $json_str = "\"". json_encode_string($arr) . "\"";
    }
    else if(is_numeric($arr))    {
      $json_str = $arr;
    }
    else    {
      $json_str = "\"". json_encode_string($arr) . "\"";
    }
  }
  return $json_str;
}
  


function sort_clicks_views($a, $b) {
  if ($a['clicks'] == $b['clicks']) { 
    if (isset($a['views']) && isset($b['views']) && $a['views']!=$b['views']) {
      return ($a['views']<$b['views']) ? 1 : -1;
    }
    else {
      return 0; 
    }
  }
  return ($a['clicks'] < $b['clicks']) ? 1 : -1;
}


?>