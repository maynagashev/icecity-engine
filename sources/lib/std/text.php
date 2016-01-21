<?php

class std_text {

function protect($t) {
  
   $t = preg_replace("#eval#msi", "", $t);
   $t = preg_replace("#cookie#msi", "", $t);
    
   return $t;    
  
}
  
function truncate($text, $limit, $msg="..."){
   if (strlen($text) > $limit){
       $txt1 = wordwrap($text, $limit, '[cut]');
       $txt2 = explode('[cut]', $txt1);
       $ourTxt = $txt2[0];
       $finalTxt = $ourTxt.$msg;
   }
   else{
       $finalTxt = $text;
   }
   return $finalTxt;
}
  
function cut($string, $html = 'cut', $quotes = 'add', $br = false, $unicode = false){
  // 1. HTML
  switch($html) {  
  case 'replace':
    	$string = preg_replace( "/\\\$/"      , "&#036;"        , $string );
    	$string = str_replace( "&#032;", " ", $string );
    	$string = str_replace( "&"            , "&amp;"         , $string );
    	$string = str_replace( ">"            , "&gt;"          , $string );
    	$string = str_replace( "<"            , "&lt;"          , $string );
   break;
   case 'allow':       
      // do nothing
   break;   
   case 'cut': 
      $string = strip_tags($string);
      $string = str_replace( ">"            , ""          , $string );
    	$string = str_replace( "<"            , ""          , $string );
   break;
   
  default:
    die("undefined html parameter in text cut: {$quotes}");
   
  }
  
  // 2. QUOTES
  switch($quotes) {  
  case 'allow':
      // do nothing
  break;
  case 'mstrip':
      $string = (get_magic_quotes_gpc()) ? stripslashes($string) : $string;
  break;      
  case 'strip':
      $string = stripslashes($string);
  break;
  case 'replace':
      $string = str_replace( "\""           , "&quot;"        , $string );    	
    	$string = str_replace( "'"            , "&#39;"         , $string ); // IMPORTANT: It helps to increase sql query safety.
  break;
  case 'mreplace':
      $string = (get_magic_quotes_gpc()) ? stripslashes($string) : $string;
      $string = str_replace( "\""           , "&quot;"        , $string );    	
    	$string = str_replace( "'"            , "&#39;"         , $string ); // IMPORTANT: It helps to increase sql query safety.
  break;
    
  case 'madd':
      $string = (get_magic_quotes_gpc()) ? stripslashes($string) : $string;
      $string = addslashes($string);
  break;     
  case 'mcut':
      $string = (get_magic_quotes_gpc()) ? stripslashes(stripslashes($string)) : $string;
      $string = str_replace( "\""           , ""        , $string );    	
    	$string = str_replace( "'"            , ""         , $string ); // IMPORTANT: It helps to increase sql query safety.  
  break;      
  case 'cut':
      $string = str_replace( "\""           , ""        , $string );    	
    	$string = str_replace( "'"            , ""         , $string ); // IMPORTANT: It helps to increase sql query safety.  
  break;    
  case 'add': 
      $string = addslashes($string);
  break;
  
  default:
    die("undefined quotes parameter in text cut: {$quotes}");
  }     

  // 3. BR
  if ($br) {
		  $string = preg_replace( "/\n/"        , "<br />"        , $string ); // Convert literal newlines
    	$string = preg_replace( "/\r/"        , ""              , $string ); // Remove literal carriage returns
  }
  
  // 4. UNICODE    	
  if ($unicode)	{
		  $string = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $string );
	}
		
  // 5. Swop user inputted backslashes    	
   //$string = preg_replace( "/\\\(?!&amp;#|\?#)/", "&#092;", $string ); 
    	
  return $string;
}

function prepare_login($t) {
  global $std;
  
  $t = $std->cutstr($t);
  
  
  
  return $t;
}

function myip2long($ip)
{
 $ip = explode(".",$ip);
 if (!is_numeric(join(NULL,$ip)) or count($ip) != 4) {return false;}
 else {return $ip[3]+256*$ip[2]+256*256*$ip[1]+256*256*256*$ip[0];}
}

function v_email($email)
{
  $ret = false;
  if (preg_match("/^[A-Za-z0-9\_\-\.]+@[A-Za-z0-9\_\-\.]+\.[A-Za-z]+$/msi", $email)) {
    $ret = true;
  }    
return $ret;
}

function random_t($lim = 6) {
  $t = md5(uniqid()).md5(uniqid());
  return preg_replace("#^.*(.{{$lim}})$#msi", "\\1", $t); 
}

function split_dir($str, $s = 2) {
  
  
  $s = abs(intval($s));
  $s = ($s>10) ? 10 : $s;
  $et = trim($str);
  $et = preg_replace("#[^a-z0-9\_\-]#msi", "", $et);
  
  if ($et=='' || $s==0) return $str;
  
  $t = $et; $lim = 100; $i=0;
  while (strlen($t)<$s) { $i++; if ($i>$lim) die("bad cycle ".__FILE__.__LINE__);
    $t .= $et;
  }
  
  $ar = array();
  for($i = 0; $i<$s; $i++) {
    $ar[] = $t[$i];
  }
  $ar[] = $str;
  
  $ret = implode("/", $ar);
  
  return $ret;
}  

/**
 * Replace raw url to <a href='url'>url</a>
 *
 * @param unknown_type $str
 * @return unknown
 */
function url2href ($str) {
  
  $p = "#(^|(?<=[^_a-z0-9-=\]\"'/@]|(?<=" . $taglist . ")\]))((https?|ftp|gopher|news|telnet)://|www\.)((\[(?!/)|[^\s[^$!`\"'|{}<>])+)(?!\[/url|\[/img)(?=[,.]*(\)\s|\)$|[\s[]|$))#siU";
  
  $str = preg_replace($p, "<a href='\\2\\4'>\\2\\4</a>", $str);
  
  return $str;
}

/**
 * Find links in html by ext_ar, (include relative)
 *
 * @param unknown_type $html
 * @param unknown_type $ext_ar
 * @param unknown_type $host_url
 * @return unknown
 */
function find_links($html, $ext_ar = array(), $host_url) {  
  
  $base = $this->check_base($html);
    
  $subq = "\.(".implode("|", $ext_ar).")";
  
  $patterns = array(
  "href=\"([^\"]+{$subq})\"",
  "href='([^\']+{$subq})'",
  "href=([^\s\"\']+{$subq})"  
  );
  
  
  
  $ar = array();
  foreach ($patterns as $p) {
    if(preg_match_all("#{$p}#msi", $html, $m)) {
      foreach($m[1] as $url) {
        $ar[] = $this->compile_url($url, $host_url, $base);
      }
    }
  }
  $ar = array_unique($ar);
    
  return $ar;
}

function compile_url($url, $host_url, $base='') {

  $host_url = ($base!='') ? $base : $host_url;

  
  $purl = parse_url($host_url);
  $protocol = (isset($purl['scheme'])) ? $purl['scheme'] : "http";
 
  // with no ending /
  $host_dir = (preg_match("#/$#msi", $host_url)) ? preg_replace("#/$#msi", "", $host_url) : dirname($host_url);
  $host_root = $protocol."://".$purl['host'];

    
  if (preg_match("#^http#msi", $url)) {
    //if absoulte url with http
    $ret = $url;
  } 
  elseif(preg_match("#^/#msi", $url, $m)) {
     // root
    $ret = $host_root.$url;
  }
  else {
    //if absoulte url with http
    $ret = $host_dir."/".$url; 
  }
  
  
  return $ret;
}

function check_base($html) {
  

  $patterns = array(
    "href=\"([^\"]+)\"",
    "href='([^\']+)'",
    "href=([^\s\"\']+)"  
  );
  
  $ar = array(); $ret = "";
  
  foreach ($patterns as $p) {
    if(preg_match_all("#<base[^>]*{$p}#msi", $html, $m)) {
      foreach($m[1] as $url) {
        $ar[] = $url;
        $ret = $url;
      }
    }
  }


  //$ret = preg_replace("#/$#msi", "", $ret);
 
  return $ret;
}

/**
 * Rus-eng hack convert
 */
function str2eng($val) {
      
  $rus = array("А", "а", "В", "Е", "е", "К", "М", "Н", "О", "о", "Р", "р", "С", "с", "Т", "у", "Х", "х");
  $eng = array("A", "a", "B", "E", "e", "K", "M", "H", "O", "o", "P", "p", "C", "c", "T", "y", "X", "x");
  $eng_name = addslashes(str_replace($rus, $eng, $val));
  $rus_name = addslashes(str_replace($eng, $rus, $val));
	  
  return $eng_name;
}

function str2rus($val) {
      
  $rus = array("А", "а", "В", "Е", "е", "К", "М", "Н", "О", "о", "Р", "р", "С", "с", "Т", "у", "Х", "х");
  $eng = array("A", "a", "B", "E", "e", "K", "M", "H", "O", "o", "P", "p", "C", "c", "T", "y", "X", "x");
  $eng_name = addslashes(str_replace($rus, $eng, $val));
  $rus_name = addslashes(str_replace($eng, $rus, $val));
	  
  return $rus_name;
}

/**
 * Translit
 *
 * @param unknown_type $string
 * @return unknown
 */
function translit($string) {
	$string=str_replace("й","y",$string);
	$string=str_replace("ц","c",$string);
	$string=str_replace("у","u",$string);
	$string=str_replace("к","k",$string);
	$string=str_replace("е","e",$string);
	$string=str_replace("н","n",$string);
	$string=str_replace("г","g",$string);
	$string=str_replace("ш","sh",$string);
	$string=str_replace("щ","sh",$string);
	$string=str_replace("з","z",$string);
	$string=str_replace("х","h",$string);
	$string=str_replace("ъ","",$string);
	$string=str_replace("ф","f",$string);
	$string=str_replace("ы","y",$string);
	$string=str_replace("в","v",$string);
	$string=str_replace("а","a",$string);
	$string=str_replace("п","p",$string);
	$string=str_replace("р","r",$string);
	$string=str_replace("о","o",$string);
	$string=str_replace("л","l",$string);
	$string=str_replace("д","d",$string);
	$string=str_replace("ж","zh",$string);
	$string=str_replace("э","e",$string);
	$string=str_replace("я","ya",$string);
	$string=str_replace("ч","ch",$string);
	$string=str_replace("с","s",$string);
	$string=str_replace("м","m",$string);
	$string=str_replace("и","i",$string);
	$string=str_replace("т","t",$string);
	$string=str_replace("ь","",$string);
	$string=str_replace("б","b",$string);
	$string=str_replace("ю","yu",$string);
	$string=str_replace("ё","yo",$string);
	$string=str_replace("Ё","Yo",$string);
	$string=str_replace("Й","iy",$string);
	$string=str_replace("Ц","C",$string);
	$string=str_replace("У","U",$string);
	$string=str_replace("К","K",$string);
	$string=str_replace("Е","E",$string);
	$string=str_replace("Н","N",$string);
	$string=str_replace("Г","G",$string);
	$string=str_replace("Ш","Sh",$string);
	$string=str_replace("Щ","Sh",$string);
	$string=str_replace("З","Z",$string);
	$string=str_replace("Х","H",$string);
	$string=str_replace("Ъ","",$string);
	$string=str_replace("Ф","F",$string);
	$string=str_replace("Ы","Y",$string);
	$string=str_replace("В","V",$string);
	$string=str_replace("А","A",$string);
	$string=str_replace("П","P",$string);
	$string=str_replace("Р","R",$string);
	$string=str_replace("О","O",$string);
	$string=str_replace("Л","L",$string);
	$string=str_replace("Д","D",$string);
	$string=str_replace("Ж","Zh",$string);
	$string=str_replace("Э","E",$string);
	$string=str_replace("Я","Ya",$string);
	$string=str_replace("Ч","Ch",$string);
	$string=str_replace("С","S",$string);
	$string=str_replace("М","M",$string);
	$string=str_replace("И","I",$string);
	$string=str_replace("Т","T",$string);
	$string=str_replace("Ь","",$string);
	$string=str_replace("Б","B",$string);
	$string=str_replace("Ю","Yu",$string);
  
  return $string;
}
	
/**
 * Pass hash
 *
 * @param unknown_type $val
 * @return unknown
 */
function password_hash($val) {
  if ($val=='') return "";

  $ret = $this->salt($val);
  
  return $ret;
}

function salt($pass){
  
  $salt = "lkasd9123cjasd439masdasdasd";  
  $spec=array('~','!','@','#','$','%','^','&','*','?');
  $crypted=md5(md5($salt).md5($pass));
  $c_text=md5($pass);
  for ($i=0;$i<strlen($crypted);$i++){
      if (ord($c_text[$i])>=48 and ord($c_text[$i])<=57){
          @$temp.=$spec[$c_text[$i]];
      }elseif(ord($c_text[$i])>=97 and ord($c_text[$i])<=100){
          @$temp.=strtoupper($crypted[$i]);
      }else{
          @$temp.=$crypted[$i];
      }
  }
  return md5($temp);
}

/**
 * replace all spec symbols to #
 *
 * @param unknown_type $str
 */
function mask($str) {
  
  $str = str_replace(" ", "_", $str);
  $str = preg_replace("~[^A-Za-zА-Яа-я0-9\-\_]~ms", "#", $str);
  
  return $str;
}

function mask_password($str) {
  
  $str = str_replace(" ", "_", $str);
  $str = preg_replace("~[^A-Za-zА-Яа-я0-9\-\_]~ms", "#", $str);
  
  $len = round(strlen($str)/3);
  $str = substr($str, 0, $len)."*";
  
  return $str;
  
}

/**
 * удяляет все нежелательные символы кроме довзолненных в урл
 *
 * @param unknown_type $t
 * @param unknown_type $mstrip - делать проверку magic_quotes?
 * @return unknown
 */
function escape_url($t, $mstrip = 0 ) {
  if ($mstrip) {
    $t = (get_magic_quotes_gpc()) ? stripslashes($t) : $t;
  }
  $t = preg_replace("#[^a-z0-9\_\-\.\/\?\=\%\:\&]#si", "", $t);
  return $t;
}

function escape_search_word($word){

	$find = array(
		'\\\*',	// remove escaped wildcard
		'%',	// escape % symbols
		'_' 	// escape _ symbols		
	);
	$replace = array(
		'*',	// remove escaped wildcard
		'\%',	// escape % symbols
		'\_' 	// escape _ symbols
	);
	
	$word = str_replace($find, $replace, addslashes($word));  
	return $word;
}
    
/**
 * Генерация адреса для страницы
 * + проверка по указанной таблице и полю если таблица указана
 * @param mixed $src
 * @param unknown_type $ftable
 * @param unknown_type $fieldname
 */
function gen_slug($src, $ftable = '', $fname = 'slug') {
  global $db;
  
  // генерируем первичный транслит
  $title = $src;
  if (is_array($src)) {
    foreach($src as $t) {
      if ($t!='') {
        $title = $t;
        break;
      }
    }
  }
  else {
    $title = $src;
  }
  $title = trim($title);
  
  $translit = strtolower($this->translit($title));
  $translit = preg_replace("#\s#si", "-", $translit);
  $translit = preg_replace("#[^a-z0-9\-\_]#si", "", $translit);
  
  if ($translit=='') {
    $translit = uniqid();
  }
  $slug = $translit; 
  
  // если задано - проверям в таблице есть ли такие адреса, если есть наращиваем
  if ($ftable!='') {
    $db->q("SELECT `{$fname}` FROM `{$ftable}` WHERE `{$fname}`='".$db->esc($slug)."'");
    $nr = $db->nr();
    $i = 1; 
    $tslug = $slug;
    while($nr>0 && $i<1000) { $i++; 
      $tslug = $slug."-".$i;
      $db->q("SELECT `{$fname}` FROM `{$ftable}` WHERE `{$fname}`='".$db->esc($tslug)."'");
      $nr = $db->nr();      
    }
    // если до сих пор есть в таблице, выдаем левый рандом
    $slug = ($nr>0) ? uniqid() : $tslug;
  }
 
  return $slug;
}

/**
 * Проверка строки для адреса
 * 1) не должна быть пуста и иметь англ символы цифры
 * 2) не должно быть повторений в таблице
 *
 * @param unknown_type $slug
 * @param unknown_type $ftable
 * @param unknown_type $fname
 * @param unknown_type $id
 * @return unknown
 */
function is_valid_slug($slug, $ftable = '', $fname = 'slug', $id = 0) {
  global $db;
  
  $tslug = preg_replace("#[^a-z0-9\_\-]#si", "", $slug);
  // нет значимых букв или цифр на англ. 
  if ($tslug=='') {    
    return false;
  }
  
  // если указана таблица проверяем по ней
  if ($ftable!='') {    
    $eslug = $db->esc($slug);
    $id = intval($id);
    $db->q("SELECT `{$fname}` FROM `{$ftable}` WHERE `{$fname}`='".$db->esc($slug)."' AND `id`<>'{$id}'");
    if ($db->nr()>0) {
      // уже существуют другие записи
      return false;
    }
  }
  
  return true;
}

function clean_slug($slug) {
  
  $slug = str_replace("/", "_", $slug);  
  $slug = preg_replace("#[^a-z0-9\_\-\.]#si", "", $slug);
  
  return $slug;
}
//eoc    
}


?>