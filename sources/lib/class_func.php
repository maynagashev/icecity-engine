<?php
class func
{
  //var $x = 0;
  //var $allow_unicode = "";
  
function truncate($text, $limit, $msg){
   if (strlen($text) > $limit){
       $txt1 = wordwrap($text, $limit, '[cut]');
       $txt2 = explode('[cut]', $txt1);
       $ourTxt = $txt2[0];
       $finalTxt = $ourTxt.$msg;
   }else{
       $finalTxt = $text;
   }
   return $finalTxt;
}
    
function get_age($bd)     {  
  if (is_null($bd)) return 0;      
  $d = $this->bd_info($bd);
  return $d['age'];
}
  
function size_kb($size)
{
	return sprintf("%01.1f", $size/1024);
}  
function size_mb($size)
{
	return sprintf("%01.1f", $size/1024/1024);
}      

function size_gb($size)
{
	return sprintf("%01.1f", $size/1024/1024/1024);
}      

function act_size($size) {
  
  if ($size<1024*1024) {
    $ret = $this->size_kb($size)." Kb";
  }
  elseif($size<1024*1024*1024) {
    $ret = $this->size_mb($size)." Mb";
  }
  else {
    $ret = $this->size_gb($size)." Gb";
  }
  
  return $ret;
}

function act_size_rus($size) {
  
  if ($size<1024*1024) {
    $ret = $this->size_kb($size)." килобайт";
  }
  elseif($size<1024*1024*1024) {
    $ret = $this->size_mb($size)." мегабайт";
  }
  else {
    $ret = $this->size_gb($size)." гигабайт";
  }
  
  return $ret;
}


//=================================================
// CONNETCTION INFO
//=================================================
function parse_con_info($str)
{
	global $sv, $db;

	if (preg_match("#^([^\s\@]+)@([^\s\@]+)$#msi", $str, $m)) {	
  	if (preg_match("#^([^\s\:\@]+)\:([^\s\:\@]+)@([^\s\@]+)$#msi", $str, $m)){
  	  $ret['user'] = $m[1];
  	  $ret['pass'] = $m[2];
  	  $ret['host'] = $m[3];
  	  
    }
    elseif (preg_match("#^([^\s\@]+)@([^\s\@]+)$#msi", $str, $m)) {
  	  $ret['user'] = $m[1];
  	  $ret['pass'] = '';
  	  $ret['host'] = $m[2];
    
    }
    else {
      return false;
    }
	
	}
	else {
	  return false;
	}
  	
	return $ret;
  
}    



	
//=================================================
// ACCESS BOX (WITH CHECKBOXES)
//=================================================
function access_box($ag = 1)
{
	global $sv, $db;
  
  $opt=array();
  foreach ($sv->ag_titles as $i=>$t) {
    if ($i<$sv->user['ag'])  {continue;}
    $s = ($ag==$i) ? 'selected' : '';
    $opt[] = "<option value='{$i}' {$s}>{$t}</option>";
  }
  
  
  
  $options = implode("\n", $opt);
  $out = "
      <select name='new[ag]'>
        {$options}
      </select>";
	return $out;
  
}    

	var $allow_unicode = "";
	

// ========================
// GET access group
// ========================
function get_ag($group) {
 global $sv;
 foreach ($sv->ag as $ag => $ar) {
  if (in_array($group, $ar)) {
    $ret = $ag;
    break;
  }
  $ret = $ag;
}   
    
    
    return $ret;
}

// ========================================
// TIME BOX
// ========================================
function time_box($time = 0) {

  $time = ($time==0) ? time() : $time;
  $y = date('Y', $time);
  $m = date('n', $time); 
  $d = date('j', $time);
  $h = date('G', $time);
  $min = date('i', $time);
  $sec = date('s', $time);
  
  // year
  $y_opt = "";
  for ($i = 1998; $i<2010; $i++) {
     $sel = ($i==$y) ? " selected" : "";
     $y_opt .= "<option value='{$i}'{$sel}>{$i}</option>";
  }
  $y_sel = "<select name='time[year]'>".$y_opt."</select>";
  
  
  //month
  $m_opt = ""; 
  for ($i = 1; $i<=12; $i++) {
     $sel = ($i==$m) ? " selected" : "";
     $m_opt .= "<option value='{$i}'{$sel}>".$this->monthtorus($i)."</option>";
  }
  $m_sel = "<select name='time[month]'>".$m_opt."</select>";

  //day
  $d_opt = "";
  for ($i = 1; $i<=31; $i++) {
     $sel = ($i==$d) ? " selected" : "";
     $d_opt .= "<option value='{$i}'{$sel}>".$i."</option>";
  }
  $d_sel = "<select name='time[day]'>".$d_opt."</select>";

  
  $ret = "
  <table cellpadding=0 cellspacing=0 border=0>
      <tr><td nowrap>
  <input type='text' name='time[hour]' size='3' value='{$h}' style='text-align:center'> : 
      </td><td nowrap>
  <input type='text' name='time[min]' size='3' value='{$min}' style='text-align:center'> : 
      </td><td nowrap>
  <input type='text' name='time[sec]' size='3' value='{$sec}' style='text-align:center'>
      </td><td>
  &nbsp; &nbsp;  &nbsp; 
      </td><td>
  {$d_sel}
        </td><td>
 {$m_sel}
       </td><td>
 {$y_sel}
       </td></tr>
  </table>
  ";
    
  return $ret;  
}



// ========================================
// TEXT CUT 
// html = cut|replace|allow 
// quotes = add|strip|replace
//=====================================
function textcut($string, $html = 'cut', $quotes = 'add', $br = false, $unicode = false){
  // 1. HTML
  switch($html) {  
  case 'replace':
    	$string = preg_replace( "/javascript/i" , "j&#097;v&#097;script", $string );
  		$string = preg_replace( "/alert/i"      , "&#097;lert"          , $string );
  		$string = preg_replace( "/about:/i"     , "&#097;bout:"         , $string );
  		$string = preg_replace( "/onmouseover/i", "&#111;nmouseover"    , $string );
  		$string = preg_replace( "/onclick/i"    , "&#111;nclick"        , $string );
  		$string = preg_replace( "/onload/i"     , "&#111;nload"         , $string );
  		$string = preg_replace( "/onsubmit/i"   , "&#111;nsubmit"       , $string );
  		$string = preg_replace( "/<body/i"      , "&lt;body"            , $string );
  		$string = preg_replace( "/<html/i"      , "&lt;html"            , $string );
  		$string = preg_replace( "/document\./i" , "&#100;ocument."      , $string );
    	$string = preg_replace( "/<script/i"  , "&#60;script"   , $string );
    	$string = preg_replace( "/\\\$/"      , "&#036;"        , $string );
    	$string = str_replace( "&#032;", " ", $string );
    	$string = str_replace( "&"            , "&amp;"         , $string );
    	$string = str_replace( "<!--"         , "&#60;&#33;--"  , $string );
    	$string = str_replace( "-->"          , "--&#62;"       , $string );
    	$string = str_replace( ">"            , "&gt;"          , $string );
    	$string = str_replace( "<"            , "&lt;"          , $string );
    	$string = str_replace( "!"            , "&#33;"         , $string );
    	
    	$string = str_replace( "\\"            , "&#092;"         , $string );
   break;
   case 'allow':       
      // do nothing
   break;   
   case 'cut': default: 
      $string = strip_tags($string);
  }
  
  // 2. QUOTES
  switch($quotes) {  
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
  case 'add': default:
      $string = addslashes($string);
  break;
  
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



	//====================
	// cutstr
	//=====================
	
	function cutstr($string)
	{
		
		$string=addslashes($string);
	
	return $string;
	
	}

	//====================
	// BUTTON
	//=====================
	
	function btn($val, $url)
	{
	   $ret = "<input type='button' value='{$val}' onclick=\"window.location.href='{$url}'\">";
	return $ret;
	
	}
  
  	function btn_buffer($val, $str, $k=0)
	{
	   $ret = "
     <input type='hidden' name='buffer{$k}' value=\"{$str}\">
     <input type='button' onClick=\"buffer{$k}.createTextRange().execCommand('Copy')\" value=\"{$val}\" >";
	return $ret;
	
	}
    
  /*-------------------------------------------------------------------------*/
    // Makes incoming info "safe"              
    /*-------------------------------------------------------------------------*/
    
    function parse_incoming()
    {
		global $sv;
    	
    	$this->get_magic_quotes = get_magic_quotes_gpc();
    	
    	$return = array('act'=>'','partner'=>'');
    	
		if( is_array($_GET) )
		{
			while( list($k, $v) = each($_GET) )
			{
				if ( is_array($_GET[$k]) )
				{
					while( list($k2, $v2) = each($_GET[$k]) )
					{
						$return[ $this->clean_key($k) ][ $this->clean_key($k2) ] = $this->clean_value($v2);
					}
				}
				else
				{
					$return[ $this->clean_key($k) ] = $this->clean_value($v);
				}
			}
		}
		
		//-----------------------------------------
		// Overwrite GET data with post data
		//-----------------------------------------
		
		if( is_array($_POST) )
		{
			while( list($k, $v) = each($_POST) )
			{
				if ( is_array($_POST[$k]) )
				{
					while( list($k2, $v2) = each($_POST[$k]) )
					{
						$return[ $this->clean_key($k) ][ $this->clean_key($k2) ] = $this->clean_value($v2);
					}
				}
				else
				{
					$return[ $this->clean_key($k) ] = $this->clean_value($v);
				}
			}
		}
		
		$return['request_method'] = strtolower($_SERVER['REQUEST_METHOD']);
		
		return $return;
	}

    /*-------------------------------------------------------------------------*/
    // Key Cleaner - ensures no funny business with form elements             
    /*-------------------------------------------------------------------------*/
    
    function clean_key($key)
    {
    	if ($key == "")
    	{
    		return "";
    	}
    	
    	$key = htmlspecialchars(urldecode($key));
    	$key = preg_replace( "/\.\./"           , ""  , $key );
    	$key = preg_replace( "/\_\_(.+?)\_\_/"  , ""  , $key );
    	$key = preg_replace( "/^([\w\.\-\_]+)$/", "$1", $key );
    	
    	return $key;
    }
 
    /*-------------------------------------------------------------------------*/
    // Clean evil tags
    /*-------------------------------------------------------------------------*/
    
    function clean_evil_tags( $t )
    {
    	$t = preg_replace( "/javascript/i" , "j&#097;v&#097;script", $t );
		$t = preg_replace( "/alert/i"      , "&#097;lert"          , $t );
		$t = preg_replace( "/about:/i"     , "&#097;bout:"         , $t );
		$t = preg_replace( "/onmouseover/i", "&#111;nmouseover"    , $t );
		$t = preg_replace( "/onclick/i"    , "&#111;nclick"        , $t );
		$t = preg_replace( "/onload/i"     , "&#111;nload"         , $t );
		$t = preg_replace( "/onsubmit/i"   , "&#111;nsubmit"       , $t );
		$t = preg_replace( "/<body/i"      , "&lt;body"            , $t );
		$t = preg_replace( "/<html/i"      , "&lt;html"            , $t );
		$t = preg_replace( "/document\./i" , "&#100;ocument."      , $t );
		
		return $t;
    }
    
    /*-------------------------------------------------------------------------*/
    // Clean value
    /*-------------------------------------------------------------------------*/
    
    function clean_value($val)
    {
		global $sv;
    	
    	if ($val == "")
    	{
    		return "";
    	}
    
    	$val = str_replace( "&#032;", " ", $val );
    	
    
    	$val = str_replace( "&"            , "&amp;"         , $val );
    	$val = str_replace( "<!--"         , "&#60;&#33;--"  , $val );
    	$val = str_replace( "-->"          , "--&#62;"       , $val );
    	$val = preg_replace( "/<script/i"  , "&#60;script"   , $val );
    	$val = str_replace( ">"            , "&gt;"          , $val );
    	$val = str_replace( "<"            , "&lt;"          , $val );
    	$val = str_replace( "\""           , "&quot;"        , $val );
    	$val = preg_replace( "/\n/"        , "<br />"        , $val ); // Convert literal newlines
    	$val = preg_replace( "/\\\$/"      , "&#036;"        , $val );
    	$val = preg_replace( "/\r/"        , ""              , $val ); // Remove literal carriage returns
    	$val = str_replace( "!"            , "&#33;"         , $val );
    	$val = str_replace( "'"            , "&#39;"         , $val ); // IMPORTANT: It helps to increase sql query safety.
    	
    	// Ensure unicode chars are OK
    	
    	if ( $this->allow_unicode )
		{
			$val = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $val );
		}
		
		// Strip slashes if not already done so.
		
    	if ( $this->get_magic_quotes )
    	{
			if (!is_array($val)){		$val = stripslashes($val);};
    	}
    	
    	// Swop user inputted backslashes
    	
    	$val = preg_replace( "/\\\(?!&amp;#|\?#)/", "&#092;", $val ); 
    	
    	return $val;
    }
    
    function remove_tags($text="")
    {
    	// Removes < BOARD TAGS > from posted forms
    	
    	$text = preg_replace( "/(<|&lt;)% (MEMBER BAR|BOARD FOOTER|BOARD HEADER|CSS|JAVASCRIPT|TITLE|BOARD|STATS|GENERATOR|COPYRIGHT|NAVIGATION) %(>|&gt;)/i", "&#60;% \\2 %&#62;", $text );
    	
    	//$text = str_replace( "<%", "&#60;%", $text );
    	
    	return $text;
    }
    
    function is_number($number="")
    {
    
    	if ($number == "") return -1;
    	
    	if ( preg_match( "/^([0-9]+)$/", $number ) )
    	{
    		return $number;
    	}
    	else
    	{
    		return "";
    	}
    }
    

	 /*-------------------------------------------------------------------------*/
    // print debug info             
    /*-------------------------------------------------------------------------*/
    
    function debug_info()
    {
		GLOBAL $db,$sv;
		
		$t1=$sv->exec_time;
		$t2=microtime();
		list($m1,$s1)=explode(" ",$t1);
		list($m2,$s2)=explode(" ",$t2);
		$time=($s2-$s1)+($m2-$m1);
		$time=round($time,3);

		$out=$time."s. ".$db->query_count."q.";
		
    	return $out;
    }

	 /*-------------------------------------------------------------------------*/
    // print debug info             
    /*-------------------------------------------------------------------------*/
    
    function get_online_tr()
    {
		GLOBAL $db,$sv;
    		
    		$out="";$k=1;
    		$exp=$sv->post_time-60*15;
        $parsed = array();
        
        
        
    		$res=$db->query("SELECT s.*,a.login as a_login,m.title FROM {$sv->t['session']} s 
    		LEFT JOIN {$sv->t['account']} a ON (a.id=s.account_id) 
    		LEFT JOIN {$sv->t['group']} m ON (m.id=s.group_id) 
    		WHERE s.time>'{$exp}'  ORDER BY s.engine DESC, s.time DESC", __FILE__, __LINE__);
    		
        
    		$list = array(); 
    		while ($data=$db->f()) {
    		  $data['count'] = 1;        
    			
    			//lastact ================
    			list($lastact2, $lastblog, $lastpost)=explode(":", $data['lastact']);
    			$lastact = strtolower($sv->modules[$lastact2]);
    			$data['lastact'] = ($lastblog!=0 && $lastact2 == "main") 
    			   ? "просмотр блога ".$sv->blogs[$lastblog]['login'] : $lastact;
    			
    			$lastact=strtolower($sv->modules[$data['lastact']]);
    			if ($data['login']!=$data['a_login'] && $data['account_id']!=0){$pre=" style='color:red;'";} else {$pre="";}
    			if ($data['login']=='guest'){$title=$data['ip'];} else {$title=$data['login'];};


          $out.="<tr><td align=center valign=top width=1% style='padding-left:0px;'> $k.</td>
          <td>$title</span></td></tr>";
          $data['k'] = $k;
    		 
          $list[] = $data;
    		}
    	
    		
    		$ar = array();
    		foreach ($list as $d) {
    		  $ar[] = "  <a href='".u('user', $d['account_id'])."' style='color: ".$sv->ag_colors[$d['ag']]."'
          >".(($d['login']=='guest') ? $d['ip'] : $d['login'])."</a>".(($d['count']>1) ? " (".$d['count'].")": "")."</small>";
    		  
    		}
    		
    		
    		
    		$sv->parsed['online'] = $list;
        $sv->parsed['online_im'] = implode(", ",$ar);
       //print_r($list);
    	return $out;
    }


	 /*-------------------------------------------------------------------------*/
    // print debug info             
    /*-------------------------------------------------------------------------*/
    
    function get_online_count() {
		GLOBAL $db,$sv;
		
      
      $exp=$sv->post_time-60*15;
      
      $db->q("SELECT account_id FROM {$sv->t['session']}  WHERE time>'{$exp}'");      
      $ret['size'] = $db->nr();
      $ret['reg'] = array(0=>0, 1=>0);
      while ($d = $db->f()) {
        if($d['account_id']>0) $ret['reg'][1]++;
        else $ret['reg'][0]++;
      }
      
      return $ret;
    }




//###############################################################
function getTime($time,$vid)
{
	 
	 GLOBAL $sv;

	$post_time=$sv->post_time;

	 $a = getdate($time);
     $b = getdate($post_time);
	 $min=$a["minutes"];
     if ($min<10){$min="0".$min;};
	 $dnn=$this->dntorus($a['wday']);
switch ($vid)
	{
	case 0:	$output="$a[mday].$a[mon].$a[year] $a[hours]:$min"; break;
  case 0.5: $output=date("d.m.Y H:i", $time); break;
	case 7:	$output="$a[hours]:$min"; break;
	case 6:	 
			if ($a['mon']<10){$a['mon']='0'.$a['mon'];}
			$output="$a[mday].$a[mon].$a[year]"; 
			
			break;

	case 1: $output="$a[mday] ".$this->rus_month($a["mon"])." $a[year] года";break;
	case 1.5: $output="$a[mday] ".$this->rus_month($a["mon"])." $a[year] года, $dnn";break;
	case 2: $output="$a[mday] ".$this->rus_month($a["mon"])." $a[year] года, $a[hours]:$min";break;
	case 3: $output="$a[mday] ".$this->rus_month($a["mon"])." $a[year] года, $dnn, $a[hours]:$min";break;
	case 4: 
			$c_h=date('G');
			$b_h=date('G',$time);
			$c_m=date('i');
			$b_m=date('i',$time);
			
			if ($c_h<$b_h){$c_h=$c_h+24;};
			$h=$c_h-$b_h;
			if ($c_m<$b_m){$c_m=$c_m+60;};
			$m=$c_m-$b_m;
			if ($h!=0){$h="$h ч ";} else {$h="";};
			if ($m!=0){$m="$m мин ";} else {$m="";};
			if ($h.$m!=""){$output="$h $m назад";} else {$output="сейчас на сайте";};
			break;
	case 5: 
			$r=$post_time-$time;	
			$d=floor($r / 86400);
			if ($d > 0){ $dt="$d дн назад";} else {$dt="";};
	
			$c_h=date('G');
			$b_h=date('G',$time);
			$c_m=date('i');
			$b_m=date('i',$time);
			
			if ($c_h<$b_h){$c_h=$c_h+24;};
			$h=$c_h-$b_h;
			if ($c_m<$b_m){$c_m=$c_m+60;$h--;};
			$m=$c_m-$b_m;
			if ($h!=0){$h="$h ч ";} else {$h="";};
			if ($m!=0){$m="$m мин ";} else {$m="";};
			
			$dr_day=$b['yday']-$a['yday'];
			if ($dr_day==1){$dt="вчера";};
			if ($dr_day==0){$dt="сегодня";};
			if ($dr_day==2){$dt="позавчера";};
			if ($dr_day>2){$dt="$dr_day дн. назад";};

			$output="$dt в $a[hours]:$min";
			if ($time==0) $output = "никогда";
			break;
	};
	
	$output = ($time==0) ? 'не доступно' : $output;
	return $output;
}


//#######################################################################
function rus_month($month)
{
 if ($month=="1"){$month="января";};
 if ($month=="2"){$month="февраля";};
 if ($month=="3"){$month="марта";};
 if ($month=="4"){$month="апреля";};
 if ($month=="5"){$month="мая";};
 if ($month=="6"){$month="июня";};
 if ($month=="7"){$month="июля";}; 
 if ($month=="8"){$month="августа";};
 if ($month=="9"){$month="сентября";};
 if ($month=="10"){$month="октября";};
 if ($month=="11"){$month="ноября";};
 if ($month=="12"){$month="декабря";};
 return $month;
}



//#############################################################33
function dntorus($dn)
{
 if ($dn=="1"){$dn="понедельник";};
 if ($dn=="2"){$dn="вторник";};
 if ($dn=="3"){$dn="среда";};
 if ($dn=="4"){$dn="четверг";};
 if ($dn=="5"){$dn="пятница";};
 if ($dn=="6"){$dn="суббота";};
 if ($dn=="7"){$dn="воскресенье";};
 if ($dn=="0"){$dn="воскресенье";};
return $dn;

}

//#######################################################################
function monthtorus($month)
{
 if ($month=="1"){$month="январь";};
 if ($month=="2"){$month="февраль";};
 if ($month=="3"){$month="март";};
 if ($month=="4"){$month="апрель";};
 if ($month=="5"){$month="май";};
 if ($month=="6"){$month="июнь";};
 if ($month=="7"){$month="июль";}; 
 if ($month=="8"){$month="август";};
 if ($month=="9"){$month="сентябрь";};
 if ($month=="10"){$month="октябрь";};
 if ($month=="11"){$month="ноябрь";};
 if ($month=="12"){$month="декабрь";};
 return $month;
}

//#######################################################################
function delete_dir($dir)
{
	if (substr($dir,-1)=="/" || substr($dir,-1)=="\\"){$l=strlen($dir); $dir=substr($dir,0,$l-1);};
	if (!file_exists($dir)){return FALSE;};

	if (is_dir($dir))
	{
		
		$dh=opendir($dir);$files=array();
		while ($file_name=readdir($dh)):			
			if (($file_name!=".") && ($file_name!="..")){$files[]=$file_name;};
		endwhile;
		closedir($dh);

		foreach ($files as $file_name)	
		{
						if (is_dir($dir."/".$file_name))
						{
							$this->delete_dir($dir."/".$file_name);
						}
					else
						{
							unlink($dir."/".$file_name);
						};
							

		}
		
		$res=rmdir($dir);
	}
	else
	{
		unlink($dir);
	};



}


//#######################################################################
function parse_post($data,$how=0)
{
	GLOBAL $sv,$db;
	// ==========================
	// variables
	// =========================
	$more = "
	<div class=more_link align=left>[ <a href=".u('post', '', $data['id']).">Просмотреть текст полностью</a> ]</div>";
	
  
	//===============================
	// parsing text
	//===============================
		$text=$data['text'];
		//$text=strip_tags($text,"<table></table><td><tr></td></tr>");
	
		if ($how==0) {
			if (!strpos($text,"[border]")===FALSE)			{
				$t=explode("[border]",$text);
				$text=$t[0].$more;					
			}				
		} 
		else {
			$text=str_replace("[border]","",$text);		
		}

		if ($data['br']=='1'){ $text=nl2br($text); }

    $text = preg_replace("#\[url\](.*)\[/url\]#esiU", "handle_bbcode_url('\\1', '', 'url')", $text);
    $text = preg_replace("#\[php\](<br>|<br />|\r\n|\n|\r)??(.*)(<br>|<br />|\r\n|\n|\r)??\[/php\]#esiU",
                           "highlight_phpcode('\\2')", $text);	
    $text = preg_replace('#\[url=(&quot;|"|\'|)(.*)\\1\](.*)\[/url\]#esiU', "handle_bbcode_url('\\3', '\\2', 'url')", $text);
    $text = preg_replace('#\[quote\](<br>|<br />|\r\n|\n|\r)??(.*)(<br>|<br />|\r\n|\n|\r)??\[/quote\]#esiU',
                           "handle_bbcode_quote('\\2')", $text);
    $text = preg_replace(
      '#\[quote=(&quot;|"|\'|)(.*)\\1\](<br>|<br />|\r\n|\n|\r)??(.*)(<br>|<br />|\r\n|\n|\r)??\[/quote\]#esiU', 
        "handle_bbcode_quote('\\4', '\\2')", $text);
    
    // my replaces
    $text=str_replace("[bigquote]","<div class=bigquote>",$text);
    $text=str_replace("[/bigquote]","</div>",$text);
    
    $text=str_replace("[video]","<embed width=500 height=400 src='",$text);
    $text=str_replace("[/video]","' autostart='false' type='application/x-shockwave-video'>",$text);

	


  
$rep = <<<EOD
      
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="320" height="280" id="index" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="FlashVars" value="my_url=\\1" />
<param name="movie" value="flv.swf" />
<param name="quality" value="high" /> 
<param name="bgcolor" value="#cccccc" />  
<embed src="flv.swf" FlashVars="my_url=\\1"  quality="high" bgcolor="#cccccc" width="320" height="280" name="index" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>
      
EOD;
  

  
$rep = <<<EOD
      

<embed src="flvplayer.swf" width="425" height="350" 
type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" 
flashvars="file=\\1&allowfullscreen=true" />

</object>
 

EOD;
  




  $text = preg_replace_callback("#\[flv\]([^\[\]]+)\[\/flv\]#msi", array($this, "callback_flv"), $text);
  
		//$text=eregi_replace('back','bаck',$text);			

		$sv->current_nid = $data['id'];
	  
		$text = $this->parse_dblocks($text);
		
		if (preg_match_all("#\[img\_id\=([0-9]+)\]#msi", $text, $m)) { 
		  $ar = $m[1];
		  foreach($ar as $id) {
		    $sv->render->ar['image']['post'][$data['id']][$id] = false;
		    $sv->render->ar['image']['news'][$data['id']][$id] = false;
		  }
		}
		if (preg_match_all("#\[preview\=([0-9]+)\]#msi", $text, $m)) { 
		  $ar = $m[1];
		  foreach($ar as $id) {
		    $sv->render->ar['image']['post'][$data['id']][$id] = false;
		    $sv->render->ar['image']['news'][$data['id']][$id] = false;
		  }
		}
		
		if (preg_match_all("#\[attache\=([0-9]+)\]#msi", $text, $m)) { 
		  $ar = $m[1];
		  foreach($ar as $id) {
		    $sv->render->ar['image']['post'][$data['id']][$id] = false;		    
		  }
		}		
		
		$text = $sv->share->parse_text($text);
	
	$data['text']=$text;

return $data;
}



//#######################################################################
function get_president()
{
	GLOBAL $sv,$db;

	$out="";
	$res=$db->query("SELECT name FROM {$sv->t['presidents']} WHERE ip='{$sv->ip}'");
	if (mysql_num_rows($res)>0)
	{
		list($out)=mysql_fetch_array($res);	
	}
	else
	{
		$res=$db->query("SELECT id,name FROM {$sv->t['presidents']} WHERE ip=''");
		if (mysql_num_rows($res)>0)
		{
			list($id,$out)=mysql_fetch_array($res);	
			$db->query("UPDATE {$sv->t['presidents']} SET ip='{$sv->ip}' WHERE id='{$id}'");
		}
		else
		{
			$res=$db->query("SELECT id,name FROM {$sv->t['presidents']} ORDER BY RAND() LIMIT 1");
			if (mysql_num_rows($res)>0)
			{
			list($id,$out)=mysql_fetch_array($res);	
			$db->query("UPDATE {$sv->t['presidents']} SET ip='{$sv->ip}' WHERE id='{$id}'");
			};
		};
	};
if ($out==""){$out="Anonimous";};

return $out;
}


//=====================================
// inection protect
//=====================================
function protect($string)
{
$string=addslashes($string);


return $string;
}


// cmnt text process
//=====================================
function cmnt_cut($string)
{
$string=addslashes($string);
$string=strip_tags($string);
//$string=$this->convert_url_to_bbcode_callback($string);

return $string;
}

// cmnt text process
//=====================================
function cmnt_textcut($string)
{

    	$string = preg_replace( "/javascript/i" , "j&#097;v&#097;script", $string );
		$string = preg_replace( "/alert/i"      , "&#097;lert"          , $string );
		$string = preg_replace( "/about:/i"     , "&#097;bout:"         , $string );
		$string = preg_replace( "/onmouseover/i", "&#111;nmouseover"    , $string );
		$string = preg_replace( "/onclick/i"    , "&#111;nclick"        , $string );
		$string = preg_replace( "/onload/i"     , "&#111;nload"         , $string );
		$string = preg_replace( "/onsubmit/i"   , "&#111;nsubmit"       , $string );
		$string = preg_replace( "/<body/i"      , "&lt;body"            , $string );
		$string = preg_replace( "/<html/i"      , "&lt;html"            , $string );
		$string = preg_replace( "/document\./i" , "&#100;ocument."      , $string );

    	$string = str_replace( "&#032;", " ", $string );
    	
    
    	$string = str_replace( "&"            , "&amp;"         , $string );
    	$string = str_replace( "<!--"         , "&#60;&#33;--"  , $string );
    	$string = str_replace( "-->"          , "--&#62;"       , $string );
    	$string = preg_replace( "/<script/i"  , "&#60;script"   , $string );
    	$string = str_replace( ">"            , "&gt;"          , $string );
    	$string = str_replace( "<"            , "&lt;"          , $string );
    	$string = str_replace( "\""           , "&quot;"        , $string );
    	$string = preg_replace( "/\\\$/"      , "&#036;"        , $string );
    	$string = str_replace( "!"            , "&#33;"         , $string );
    	$string = str_replace( "'"            , "&#39;"         , $string ); // IMPORTANT: It helps to increase sql query safety.

		//$string = preg_replace( "/\n/"        , "<br />"        , $string ); // Convert literal newlines
    	//$string = preg_replace( "/\r/"        , ""              , $string ); // Remove literal carriage returns
    	
    	// Ensure unicode chars are OK
    	
    	if ( $this->allow_unicode )
		{
			$string = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $string );
		}
		
		// Strip slashes if not already done so.
		
    	if ( $this->get_magic_quotes )
    	{
    		$string = stripslashes($string);
    	}
    	
    	// Swop user inputted backslashes
    	
    	$string = preg_replace( "/\\\(?!&amp;#|\?#)/", "&#092;", $string ); 
    	



$string=addslashes($string);
$string=strip_tags($string);
$string=$this->convert_url_to_bbcode_callback($string);

return $string;
}


//=====================================
// page_list
//=====================================
function pagelist($size,$limit,$page, $url = "")
{
GLOBAL $sv;

if ($sv->valid_ttitle!=""){
	$sv->valid_ttitle=ucfirst($sv->valid_ttitle).".";
	$topic = str_replace("&topic=", "", $sv->valid_topic);
	$url = u('main', 'topic', $topic, "page=");
};
if ($size<=$limit){return array("","");};

$url = ($url=="") ? u($sv->act, $sv->code, $sv->id, "page=") : $url;
$page=floor($page);
$ost=$size%$limit; 

if ($ost!=0){$pages=(($size-$ost)/$limit)+1;} else {$pages=$size/$limit;};
if ($page<1 || $page>$pages){$page=1;} else {$sv->valid_page="&page=".$page;};
	


$pgs=array();
for ($i=1;$i<$pages+1;$i++)
{
$pgs[$i]['end_title']=$i*$limit;
$pgs[$i]['end']=$pgs[$i]['end_title']-1;
$pgs[$i]['start']=$pgs[$i]['end_title']-$limit;
$pgs[$i]['start_title']=$pgs[$i]['start']+1;

if ($pgs[$i]['end_title']>$size){$pgs[$i]['end_title']=$size;};
};

$out_first="<td class=pagelisttd_light><a href={$url}1 class='pagelist_link' title='c {$pgs[1]['start_title']} по {$pgs[1]['end_title']} из {$size}'>первая</a></td>";
$out_last="<td class=pagelisttd_light><a href={$url}{$pages} class='pagelist_link' title='c {$pgs[$pages]['start_title']} по {$pgs[$pages]['end_title']} из {$size}'>последняя</a></td>";


// do ==========
$k=0; $do=""; $do_start=$page-3; if ($do_start<1){$do_start=1;};
for ($i=$do_start;$i<$page;$i++)
{
$k++; if ($k>3){break;};
$do.="<td class=pagelisttd_light><a href='{$url}{$i}' title='Открыть c {$pgs[$i]['start_title']} по {$pgs[$i]['end_title']}, всего {$size}' class=pagelist_link>$i</a></td>";

if ($i==1){$out_first="";};
}

// posle ==========
$k=0; $posle="";
for ($i=$page+1;$i<$pages+1;$i++)
{
$k++; if ($k>3){break;};
$posle.="<td class=pagelisttd_light><a href='{$url}{$i}' title='Открыть c {$pgs[$i]['start_title']} по {$pgs[$i]['end_title']}, всего {$size}' class=pagelist_link>$i</a></td>";
if ($i==$pages){$out_last="";};
}


$out_list=$do."<td class=pagelisttd_current><span title='Всего найдено {$size}, показано c {$pgs[$page]['start_title']} по {$pgs[$page]['end_title']}'>$page</span></td>".$posle;


if ($page==1){$out_first="";};
if ($page==$pages){$out_last="";};

$prev=$page-1;
$next=$page+1;

if ($prev<1){$out_prev="";} else {$out_prev="<td class=pagelisttd_light><a href={$url}{$prev} title='Предыдущая страница - c {$pgs[$prev]['start_title']} по {$pgs[$prev]['end_title']} из {$size}' class=pagelist_link>&lt;</a></td>";};
if ($next>$pages){$out_next="";} else {$out_next="<td class=pagelisttd_light><a href={$url}{$next} title='Следующая страница - c {$pgs[$next]['start_title']} по {$pgs[$next]['end_title']} из {$size}' class=pagelist_link>&gt;</a></td>";};


$out="<table class='pagelist' cellpadding=5><tr><td class=pagelisttd>{$sv->valid_ttitle} Страница $page из $pages</td>".$out_first.$out_prev.$out_list.$out_next.$out_last."</tr></table>";

return array($out," LIMIT {$pgs[$page]['start']},{$limit}",($limit*$page-$limit));
}


//=====================================
// topiclist
//=====================================
function  topiclist($min)
{
	GLOBAL $sv,$DB;
		
	if ($sv->blogid==0){return "";};

	$list="";
	$res=$DB->query("SELECT * FROM {$sv->t['topics']} WHERE user='{$sv->blogid}' AND count>='{$min}' ORDER BY count DESC");
	while ($data=$DB->fetch_row())
	{
		$list.="
			<tr>
				<td width=99%><a href={$sv->rq}topic={$data['id']} style='color:white;'> {$data['title']} </td>
				<td width=1% align=right valign=top><span style='color:white'>{$data['count']}</td>
			</tr>";
	
	};	

return $list;
}



//=======================================
// GET story
//=======================================

function get_stats($url)
{
		GLOBAL $sv, $std;


		$c_year=date("Y");
		$c_month=date("n");
		$year = intval($sv->_get['year']);
		$month = intval($sv->_get['month']);
		
		if (!checkdate($month, 1,$year)) {		  
		  $month=$c_month;
		  $year=$c_year;
		}

		$s_time=mktime(0,0,0,$month,1,$year);
		$month_title=strftime('%B',$s_time);


		//selector
		$m_selector="<select name=new_date style='font-size:10px;border-style:none' onchange=\"window.location.href=this.options[this.selectedIndex].value\">";
		for ($i=2007;$i<2010;$i++){
			for ($j=1;$j<13;$j++)	{
  			$m_t = $this->monthtorus($j);
  			if ($j==$month && $i==$year){
  			   $m_selector.="<option value='{$url}&year=$i&month=$j' selected>$m_t $i</otion>";
  			}
  			else{
  			  $m_selector.="<option value='{$url}&year=$i&month=$j'>$m_t $i</otion>";
  			}			
			}

		}
		$m_selector.="</select>";


	
	
		$ret = "


		<table border=0 cellspacing=1 cellpadding=5>
		

		<tr><td style='padding:10px;padding-top:3px;' align=center>
		<small>
		<a href='sources/counter/counter_diagram.php?month={$month}&year={$year}'><img src='sources/counter/counter_diagram.php?code=small&month={$month}&year={$year}' width=100 style='border:1px solid #cccccc;'></a>
		
		</td></tr>

		<tr><td align=center colspan=2>$m_selector</td></tr>

		</table>


		";


		

	
	return $ret;
	}



// ###################### Start convert_url_to_bbcode_callback #######################
function convert_url_to_bbcode_callback($messagetext)
{
	// the auto parser - adds [url] tags around neccessary things
	$messagetext = str_replace('\"', '"', $messagetext);
	$prepend = str_replace('\"', '"', $prepend);

	static $urlSearchArray, $urlReplaceArray, $emailSearchArray, $emailReplaceArray;
	if (empty($urlSearchArray))
	{
		$taglist = '\[b|\[i|\[u|\[left|\[center|\[right|\[indent|\[quote|\[highlight|\[\*' .
			'|\[/b|\[/i|\[/u|\[/left|\[/center|\[/right|\[/indent|\[/quote|\[/highlight';
		$urlSearchArray = array(
			"#(^|(?<=[^_a-z0-9-=\]\"'/@]|(?<=" . $taglist . ")\]))((https?|ftp|gopher|news|telnet)://|www\.)((\[(?!/)|[^\s[^$!`\"'|{}<>])+)(?!\[/url|\[/img)(?=[,.]*(\)\s|\)$|[\s[]|$))#siU"
		);

		$urlReplaceArray = array(
			"[url]\\2\\4[/url]"
		);

		//$urlReplaceArray = array(
		//	"<a href='\\2\\4'>\\2\\4</a>"
		//);

	
		$emailSearchArray = array(
			"/([ \n\r\t])([_a-z0-9-]+(\.[_a-z0-9-]+)*@[^\s]+(\.[a-z0-9-]+)*(\.[a-z]{2,4}))/si",
			"/^([_a-z0-9-]+(\.[_a-z0-9-]+)*@[^\s]+(\.[a-z0-9-]+)*(\.[a-z]{2,4}))/si"
		);

		$emailReplaceArray = array(
			"\\1[email]\\2[/email]",
			"[email]\\0[/email]"
		);
	}

	$text = preg_replace($urlSearchArray, $urlReplaceArray, $messagetext);
	if (strpos($text, "@"))
	{
		$text = preg_replace($emailSearchArray, $emailReplaceArray, $text);
	}

	return $text;
}



//=====================================
// get msgtoip
//=====================================
function get_msgtoip()
{
	GLOBAL $DB,$sv;

	while($data=$DB->fetch_row())
	{
		$data['text']=$this->parse_bbcode($data['text']);
		$time=$this->gettime($data['time'],0)." (".$this->gettime($data['time'],5).") ";
		$sv->msgtoip.="

		<table width=80% align=center cellpadding=5 style='margin:20px; border: 1px dashed #999999;'>
		<tr><td bgcolor=#E3F5D6 width=50%><b>Сообщение от {$data['username']}</td>
		<td bgcolor=#E3F5D6 align=right  width=50%><b>$time</td></tr>

		<tr><td class=content colspan=2>{$data['text']}</td></tr>
		
		<tr><td colspan=2 class=smallfont align=right>[ <a href={$sv->rq}act=$sv->act&delmsgtoip={$data['id']}>удалить</a> ]</td></tr>
		</table>
		
		";
	};



}

//=====================================
// delete msgtoip
//=====================================
function delete_msgtoip()
{
	GLOBAL $DB,$sv;

	$id=$sv->input['delmsgtoip'];
	$id=$this->protect($id);
	
	if (trim($id)!="")
	{
	$DB->query("DELETE FROM {$sv->t['msgtoip']} WHERE ip='{$sv->user['session']['ip']}' AND id='{$id}'");
	$res=$DB->query("SELECT m.*,a.login as username FROM {$sv->t['msgtoip']} m LEFT JOIN {$sv->t['account']} a ON (a.id=m.user) WHERE m.ip='{$sv->user['session']['ip']}' ORDER BY m.time DESC");
	};
	
}
//=====================================
// parsing bbcode
//=====================================
function parse_bbcode($text)
{

	// URL
	$text = preg_replace("#\[url\](.*)\[/url\]#esiU", "handle_bbcode_url('\\1', '', 'url')", $text);	
	$text = preg_replace('#\[url=(&quot;|"|\'|)(.*)\\1\](.*)\[/url\]#esiU', "handle_bbcode_url('\\3', '\\2', 'url')", $text);

	// QUOTE
	$text = preg_replace('#\[quote\](<br>|<br />|\r\n|\n|\r)??(.*)(<br>|<br />|\r\n|\n|\r)??\[/quote\]#esiU',"handle_bbcode_quote('\\2')", $text);
	
	$text = preg_replace('#\[quote=(&quot;|"|\'|)(.*)\\1\](<br>|<br />|\r\n|\n|\r)??(.*)(<br>|<br />|\r\n|\n|\r)??\[/quote\]#esiU',"handle_bbcode_quote('\\4', '\\2')", $text);

	// \n\r to <br>
	$text=nl2br($text);



return $text;
}
//=====================================
// del _quote
//=====================================
function del_quote($text)
{

	// QUOTE
	$text = preg_replace('#\[quote\](<br>|<br />|\r\n|\n|\r)??(.*)(<br>|<br />|\r\n|\n|\r)??\[/quote\]#esiU',"", $text);
	
	$text = preg_replace('#\[quote=(&quot;|"|\'|)(.*)\\1\](<br>|<br />|\r\n|\n|\r)??(.*)(<br>|<br />|\r\n|\n|\r)??\[/quote\]#esiU',"", $text);

	
return $text;	
}
// ====================================
// savepost_cut
// ===================================
function savepost_cut($text)
{
  global $sv;
  //echo $text."\n\r";
  //echo "str_replace('src=\"{$sv->siteurl}", "src=\"', );";
  $text = str_replace("http://fmradio/", "/", $text);
  $text = str_replace("{$sv->siteurl}", "/", $text);
  $text = preg_replace("#news_managment\/blankpost\/[0-9]+\/#msi", "", $text);
  //echo $text."\n\r";
	$text=$this->convert_url_to_bbcode_callback($text);
	//$text=strip_tags($text,"<table></table><td><tr></td></tr><blockquote></blockquote><b></b><i></i><u></u><center></center><div></div><span></span><hr>");

return $text;
}


//=====================================
// extension
//=====================================
function file_extension($filename)
{

	$filename=basename($filename);
	$ex=explode(".",$filename);
	if (is_array($ex))
	{
		$i=sizeof($ex)-1;
		$def=$ex[$i];
	} else {$def="";};

	
return $def;	
}

//===============================
// READ SMILES
//===============================

function read_smiles()
{
  GLOBAL $sv;
  $sm = array();
  $k=0;
  $path = "sources/html/smiles/";

$res=mysql_query("SELECT * FROM `smiles` ORDER BY file");

while ($d = mysql_fetch_array($res))
{

	$sm[$k]['find']=$d['code'];
	$sm[$k]['replace']="<img src='".$path.$d['file']."'>";	
	$k++;
}

$sm[$k]['find']="=)))"; 
$sm[$k]['replace']="<img src='{$path}14biggrin.gif'>";
$k++;

$sm[$k]['find']="=\("; 
$sm[$k]['replace']="<img src='{$path}03sad.gif'>";
$k++;

$sm[$k]['find']="=\)"; 
$sm[$k]['replace']="<img src='{$path}01smile.gif'>";
$k++;

$sm[$k]['find']=":\)"; 
$sm[$k]['replace']="<img src='{$path}01smile.gif'>";
$k++;

$sm[$k]['find']=";\)"; 
$sm[$k]['replace']="<img src='{$path}02wink.gif'>";
$k++;

$sm[$k]['find']=":-\)"; 
$sm[$k]['replace']="<img src='{$path}01smile.gif'>";
$k++;




//print_r($sm);
return $sm;
}


// ================================
function fullurlencode($string)
{
	
	$string=rawurlencode($string);
	$string=str_replace("%2F","/",$string);

return $string;

}


// ================================
function get_trusted($blogid)
{
	GLOBAL $sv, $db;

    $id = intval($blogid);
    $ret = array();
		$res=$db->q("SELECT user FROM {$sv->t['trusted']} WHERE blogid='{$id}'", __FILE__, __LINE__);
		while ($d = $db->f())	{
			$ret[]=$d['user'];
		}

		
return $ret;

}


// ================================

//===============================
// function add queue
//===============================

function add_queue($code)
{
	GLOBAL $sv,$DB;
	
	$code=addslashes($code);
	$q=array_keys($sv->queue);
	//print_r($q); echo $code;
	if (in_array($code,$q))	
	{
	
		$res=$DB->query("SELECT id FROM {$sv->t['queue']} WHERE code='{$code}' AND (status='0' OR status='1')");
		$size=mysql_num_rows($res);

		if (mysql_num_rows($res)==0)
		{
			$DB->query("INSERT INTO {$sv->t['queue']}(code,time,status) VALUES ('{$code}','{$sv->post_time}','0')");		
		};
	
	};
}

function showto_query()
{
GLOBAL $sv;

	switch ($sv->user['session']['group_id'])
		{
			case "1":	$showto="(n.showto='1' OR n.showto='0')"; break;			
			case "2":	$showto="('1'='1')"; break;		
			case "3":	$showto="(n.showto='1' OR n.showto='0')"; break;		
			default:	$showto="(n.showto='0')";		
		};
	return $showto;
}

function get_stars($r,$blogid=0,$type=0)
{
GLOBAL $sv;
	
	
	$html="";
	if($type!=0){$step=$sv->stars_news;}
	else 
	{
		if (in_array($blogid,$sv->news_blogs))
		{$step=$sv->stars_step;}
		else{$step=$sv->stars_step_nonews;};
		
	};
	
	for ($i=1;$i<6;$i++)
	{
		$limit=$i*$step;

		//echo "$limit<br>";
		if ($r>=$limit){$html.="<img src='{$sv->r}image/star2.gif' border=0>";};	
	};

	if ($r<0)
	{
		$r=-$r;
		if ($sv->stars_minn<2){$step=2;} else {$step=$sv->stars_minn;};
		for ($i=1;$i<6;$i++)
		{
			$limit=$i*$step;

			//echo "$limit<br>";
			if ($r>=$limit){$html.="<img src='{$sv->r}image/thumbdown.gif' border=0>";};	
		};

	
	};

	return $html;
}


//===============================
// last forumposts
//===============================

function forum_lastposts($limit=10)
{
	GLOBAL $sv,$DB,$std;
	
	$t_limit=40;
	$res=$DB->query("SELECT f.threadid,f.lastposter,f.title,f.views,f.lastpost,f.forumid,s.title FROM forums_thread f LEFT JOIN forums_forum s ON (f.forumid=s.forumid) ORDER BY f.lastpost DESC LIMIT 0,10");
	while(list($t_id,$p_author,$t_title,$t_views,$p_time,$f_id,$f_title)=mysql_fetch_array($res)) 
	{
	$p_time=$std->gettime($p_time,'5');

		//text limit
			$text=$t_title;
			if (strlen($text)>$t_limit)
			{
			$t1=substr($text,0,$t_limit);
			$t2=substr($text,$t_limit,strlen($text)-$t_limit);
			$t2=explode(" ",$t2);
			$text=$t1.$t2[0]."...";		

			};
			$t_title=$text;

		$t_posts=mysql_num_rows(mysql_query("SELECT postid FROM forums_post WHERE threadid='$t_id'"))-1;
		$forum_last10.="
		<TR>
			<td rowspan=2 valign=middle align=center>#</td>
			<td width=65% valign=top style='padding:5px;padding-bottom:0px;font-size:12px;'>
				<a href=forums/showthread.php?goto=newpost&t=$t_id>$t_title</a> <small style='color:#999999;font-size:11px;'>($t_posts/$t_views)</td>

									
		<td nowrap rowspan=2 align=right style='padding-right:5px;color:#cccccc;'>[ <a href=forums/forumdisplay.php?f=$f_id>$f_title</a> ]</td>

		</tr>
		<TR>		
			<td valign=top style='padding:0px;padding-left:5px; padding-bottom:2px;'>
				<span style='font:normal 10px georgia, verdana, sans-serif;color:#999999;'>$p_author, $p_time
			</td>

			</tr>
			";



	};

	$forum_last10="
		
	<table width=90%>
		<tr><td colspan=2 style='padding-left:5px;color:#999999;'><b>НАШ ФОРУМ</td>
			<td nowrap align=right style='padding-right:7px;color:#cccccc;'>[ <b><a href='forums/'>index</a></b> ]</td></tr>
			{$forum_last10}
	</table>";
		
	
	
return $forum_last10;	
}




//===============================
// get_html get HTML whith replaced vars
//===============================
function get_html($file, $path = "", $vars = array())
{
  global $sv;  

  $ret = file_get_contents($file);
  extract ($vars);
  
    
  $ret = preg_replace("/href=[\"\']?([^\s\.]*)\.css[\"\']?/msi", "href=\"{$path}\\1.css\"", $ret);
  $ret = preg_replace("/href=[\"\']?([^\s\.]*)\.js[\"\']?/msi", "href=\"{$path}\\1.js\"", $ret);
  
  $ret = preg_replace("/src=[\"|\']?([^\s]*)[\"|\']?/msi", "src=\"{$path}\\1", $ret);

   
  if (preg_match("/\[debug\]/msi", $ret)) {
    $debug="<hr><div style='padding-left:20px;' align=center>Блоки доступные данному шаблону</div><hr>";    
    $i = 0;
    $keys = array_keys($vars);
    foreach ($keys as $k){
      $i++;    
      $vars[$k] = str_replace("/textarea", "/text-area", $vars[$k]);
      $debug.="<div style='padding:20px;' align=center><b>$i. {$k}</b><br><textarea cols=70 rows=10>".$vars[$k]."</textarea></div>";
    }    
    $ret = preg_replace("/\[debug\]/mie", "", $ret);
  }

  

  $ret = preg_replace("/\[([^\]]{0,15})\]/mie", "\$\\1", $ret);
  $ret.=$debug;
  return $ret;
}

//=================================================
// GET TOPICS MENU
//=================================================

function get_topics_menu()
{
	global $sv, $db,$std;
	 
	$out = "";
	$res=$db->query("SELECT * FROM {$sv->t['topics']} WHERE `show`='1' ORDER BY place"); 
	while ($d = $db->fetch_row()){
    $title=stripslashes($d['title']);
    $out.="<tr><td><a href='".u('main', 'topic', $d['id'])."'>{$title}</a></td></tr>\n\r";
  }
  $out.="<tr><td style='padding-top:10px;'><a href='".u('video')."'>Список видео</a></td></tr>\n\r";
  $out.="<tr><td><a href='".u('video', 'anonce')."'>Анонсы</a></td></tr>\n\r";
  return $out;
}

//=================================================
// GET POST
//=================================================

function get_post($postid)
{
	global $sv, $db, $std;

  $res=$db->query("SELECT n.*,t.title as ttitle FROM {$sv->t['news']} n LEFT JOIN {$sv->t['topics']} t ON (n.topic=t.id)  WHERE n.id='{$postid}'");
	if (mysql_num_rows($res)>0)
	{	
		  $data=$db->fetch_row();
    
		  if ($sv->user['session']['group_id']==$this->admin_group){
        $edit_link="<center>[<a href={$sv->rpath}/news_managment/edit/{$data['id']}/>edit</a>]</center>";  } 
      else {
        $edit_link="";  }

			$time=$std->gettime($data['time'],'2');
			$data=$std->parse_post($data,1);
			$out=$data['text'].$edit_link; 
	}
  else {$out="указанный блок недоступен";};

  return $out;
}    

function get_position($p)
{
  switch ($p) {
    case 0: $ret = 'left'; break;
    case 1: $ret = 'center'; break;
    case 2: $ret = 'right'; break;
    
    
  }
  return $ret;
}
//=================================================
// GET INFOBLOCKS
//=================================================

function get_infoblocks()
{
	global $sv, $db,$std;
	
	$ret = array(); $blocks = array();
	$num = array('left' =>1 , 'center' =>1, 'right'=>1);
	$out = ""; $posts=array();
	
	$res=$db->q("SELECT * FROM {$sv->t['infoblocks']} WHERE `show`='1' ORDER BY place"); 
	while ($d = $db->f()){
    $posts[$d['id']] = $d;
    $posts[$d['id']]['title'] =  stripslashes($d['title']);    
    $posts[$d['id']]['position'] =  $d['position'];    
  }
  //print_r($posts);
  $k = 0;
  foreach ($posts as $id => $ar) {
    $k++;
    $post = $this->get_post($ar['postid']);
    
    $pos = $this->get_position($ar['position']);    
    $bcode = $pos."_".$num[$pos]."_";
    $num[$pos]++;        
    $blocks[$bcode."title"] = $ar['title'];
    $blocks[$bcode."text"] = $post;
    
    //$post = $this->parse_post($post);
    /*
    if (preg_match_all("#\[img\_id\=([0-9]+)\]#msi", $post, $m)) { 
		  $ar = $m[1];
		  foreach($ar as $id) {
		    $sv->render->ar['image']['posts'][$data['id']][$id] = false;
		  }
		}
		if (preg_match_all("#\[preview\=([0-9]+)\]#msi", $post, $m)) { 
		  $ar = $m[1];
		  foreach($ar as $id) {
		    $sv->render->ar['image']['posts'][$data['id']][$id] = false;
		  }
		}
		*/
		
      
		if ($sv->cfg['news']['edit_ag']>=$sv->user['ag']){
      $edit="<center><small>[<a href=".u('news', 'edit', $ar['postid']).">править этот блок</a>]</small>\n\r";  } 
    else {
      $edit="";  }
    
    $ret[] = array(
      'title' => $ar['title'],
      'text' => $post,
      'edit' => $edit,
      'position' => $ar['position']
    );
   
    
    $i++;
          
      
  }
  //echo "<pre>"; print_r($blocks);
  $sv->parsed['infoblocks'] = $ret;
  $sv->parsed['blocks'] = $blocks;

}

//=================================================
// GET POST
//=================================================

function parse_topics($ar)
{
	global $sv, $db, $std;
  //echo "<pre>";print_r($ar);
  $keys = array_keys($ar);
  $ar2= array();  
  $this->cmp = create_function('$a, $b', 'return strnatcmp($a["place"], $b["place"]);');

  foreach ($ar as $k=>$v) {
    if (in_array($v['pid'], $keys) && $v['pid']!=$k) {
        $ar[$v['pid']]['childs'][$k] = $v;
    }
    else {
      $ar2[$k]=$v; 
    }        
  }
  $this->topics = $ar;
  uasort($ar2, $this->cmp);   
  $this->ar = array(); 
  $this->step = 0;
  
  foreach ($ar2 as $k=>$v) {
     $this->ar[$k]=$v;
     $this->ar[$k]['step'] = $this->step;
     
     if (isset($ar[$k]['childs']) && is_array($ar[$k]['childs'])) {
       $this->parse_topics_childs($ar[$k]['childs'], $ar);          
     }
  }

return $this->ar;
} 
function parse_topics_childs($childs, $ar)
{    
  $this->step++;
  uasort($childs, $this->cmp); 
  foreach ($childs as $k=>$v) {
     $this->ar[$k]=$v;
     $this->ar[$k]['step'] = $this->step;
     if (isset($ar[$k]['childs']) && is_array($ar[$k]['childs'])) {
       $this->parse_topics_childs($ar[$k]['childs'], $ar);          
     }
  }  
  $this->step--;
}    

//=================================================
// GET TOPICS
//=================================================

function get_topics($show=0)
{
	global $sv, $db,$std;
	 
	$wh = ($show==1) ? " WHERE `show`='1' " :  "";
	$res=$db->query("SELECT * FROM {$sv->t['topics']} {$wh} ORDER BY place"); 
	while ($d = $db->fetch_row()){
    $out[$d['id']] = $d;
  }

  return $out;
}   

//=================================================
// PARSE DBLOCKS
//=================================================

function parse_dblocks($text)
{
	global $sv, $db,$std;
  
  if (!isset($sv->dblocks) || !is_object($sv->dblocks)) {
    include("sources/auxcl/dblocks.php");
    $sv->dblocks = new dblocks;
  }
  
  $methods = get_class_methods('dblocks');
   
	preg_match_all("|#dblock\(([^\(\)]+)\)#|msiU", $text, $keys, PREG_SET_ORDER); 
  if (is_array($keys) && count($keys)>0) {
    foreach ($keys as $k) {
      $str = $k[1];
      $err=false;
      
      $ch = explode(",", $str);
      $i = 0; $p = array();
      foreach($ch as $k=>$v){ $i++;
        $ch[$k]=trim($v); 
        if ($i>1) {
          $p[] = "'".$ch[$k]."'";
        }
      }      
      
      $par = implode(", ", $p);
      $func = $ch[0];
      if (!in_array($func, $methods)) { $err = true; }
      
      $eval = "\$sv->dblocks->".$func."(".$par.");";
      
      if (!$err) {
        eval($eval);
        $result = $sv->dblocks->ret;
      }
      else {
        $result = "";
      }  
      
      $text = preg_replace("|#dblock\({$str}\)#|msiU", $result, $text); 
        
    }
  }
  
  //preg_replace("|#dblock\(([^\(\)]+)\)#|msiU", $text, $keys, PREG_SET_ORDER); 
  

  return $text;
}    



//=====================================
// pl
//=====================================
function pl($size, $limit, $page, $url = "index.php?page=", $addon="") {
  global $sv;
  
  $ret = array();
  $ret['vars'] = array('do', 'posle', 'next', 'prev', 'current', 'first', 'last');
  $ret['size'] = $size;
  $ret['limit'] = $limit;
  $ret['res'] = true;
  $ret['ql'] = "";
  $ret['k'] = 0;
  $ret['links'] = $ret['pgs'] = array();
  $ret['page'] = $page = intval($page);
  $ret['ost'] = $ost = $size%$limit; 
  $ret['pages'] = $pages = ($ost!=0) ? (($size-$ost)/$limit)+1 : $size/$limit;    
  $ret['page'] = $page = ($page<1 || $page>$pages) ? 1 : $page;
      
  if ($size<=$limit){ 
    $ret['res'] = false; 
    return $ret;
  }


      
  $pgs=array();
  for ($i=1; $i<$pages+1; $i++){
    $end = $i*$limit;
    $st = $end - $limit;
    $pgs[$i]['start']=$st;
    $pgs[$i]['end']=$end-1;
    $pgs[$i]['start_title']=$st+1;
    $pgs[$i]['end_title']=$end;
    
    if ($pgs[$i]['end_title']>$size){$pgs[$i]['end_title']=$size;};
  }
  
  $ret['pgs'] = $pgs;
  
  $ret['prev'] = $prev=$page-1;
  $ret['next'] = $next=$page+1;
  
  $links = array();
  
  $first['url'] = $url."1".$addon;
  $first['ot'] = $pgs[1]['start_title'];
  $first['do'] = $pgs[1]['end_title'];
  $first['desc'] = 'first';
  $first['num'] = 1;
  
  $last['url'] = $url.$pages.$addon;
  $last['ot'] = $pgs[$pages]['start_title'];
  $last['do'] = $pgs[$pages]['end_title'];
  $last['desc'] = 'last';
  $last['num'] = $pages;

  if ($page>1) $links[] = $first;
  
  // prev =================
  if ($prev >= 1) {
    $ar = array();
    $ar['url'] = $url.$prev.$addon;
    $ar['ot'] = $pgs[$prev]['start_title'];
    $ar['do'] = $pgs[$prev]['end_title'];
    $ar['desc'] = 'prev';
    $ar['num'] = $prev;
    $links[] = $ar;      
  }
    
  // do ==========
  $k=0; 
  $do_start = $page-3; 
  $do_start = ($do_start<1) ? 1 : $do_start;
  
  for ($i=$do_start; $i<$page; $i++) {  $k++; 
    if ($k>3) break;
    $ar = array();
    $ar['url'] = $url.$i.$addon;
    $ar['ot'] = $pgs[$i]['start_title'];
    $ar['do'] = $pgs[$i]['end_title'];
    $ar['desc'] = 'do';
    $ar['num'] = $i;
    $links[] = $ar;
  }



  // current ======================
    $ar = array();
    $ar['url'] = $url.$page.$addon;
    $ar['ot'] = $pgs[$page]['start_title'];
    $ar['do'] = $pgs[$page]['end_title'];
    $ar['desc'] = 'current';
    $ar['num'] = $page;
    $links[] = $ar;

  // posle ==========
  $k=0; 
  for ($i=$page+1;$i<$pages+1;$i++){ $k++; 
      if ($k>3){break;};
      $ar = array();
      $ar['url'] = $url.$i.$addon;
      $ar['ot'] = $pgs[$i]['start_title'];
      $ar['do'] = $pgs[$i]['end_title'];
      $ar['desc'] = 'posle';
      $ar['num'] = $i;
      $links[] = $ar;
  }
  
  // next =================
  if ($next <= $pages) {
    $ar = array();
    $ar['url'] = $url.$next.$addon;
    $ar['ot'] = $pgs[$next]['start_title'];
    $ar['do'] = $pgs[$next]['end_title'];
    $ar['desc'] = 'next';
    $ar['num'] = $next;
    $links[] = $ar;      
  }


  if ($page<$pages)  $links[] = $last;
$ret['links'] = $links;
$ret['ql'] = " LIMIT {$pgs[$page]['start']},{$limit}";
$ret['k'] = ($limit*$page-$limit);
  
return $ret;


} 


function pl2html($pl) {
  
  if (!$pl['res']) return "";
  
  $td = array();
  
  $text = (!isset($pl['back']) || (isset($pl['back']) && $pl['back']==0)) ? "Страница {$pl['page']} из {$pl['pages']}" : "Страницы:";
  $td[] = "<td class='text'>{$text}&nbsp;</td>";
  
  foreach ($pl['links'] as $d) {
    switch($d['desc']) {
      case 'first':
        $td[] = "<td class='num'><a href='{$d['url']}'>первая</a></td>";
      break;
      case 'prev':
        $td[] = "<td class='num'><a href='{$d['url']}'>&laquo;</a></td>";
      break;
      case 'do':
        $td[] = "<td class='num'><a href='{$d['url']}'>{$d['num']}</a></td>";
      break;
      case 'current':
        $td[] = "<td class='num selected'><a href='{$d['url']}'>{$d['num']}</a></td>";
      break;      
      case 'posle':
        $td[] = "<td class='num'><a href='{$d['url']}'>{$d['num']}</a></td>";
      break;       
      case 'next':
        $td[] = "<td class='num'><a href='{$d['url']}'>&raquo;</a></td>";
      break;   
      case 'last':
        $td[] = "<td class='num'><a href='{$d['url']}'>последняя</a></td>";
      break;         
    }
  } 

  $ret = "<table class='pagelist'><tr>".implode("\n", $td)."</tr></table>";
  return $ret;
}


// ===============================
// TRANSLIT
// ===============================
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


// ===============================
// GET USERINFO
// ===============================
function get_userinfo() {
  global $sv, $db;
  
  $a = false;
  $s = $sv->user['session'];
  if ($s['account_id']>0) {
    $db->q("SELECT * FROM {$sv->t['account']} WHERE id=".intval($s['account_id'])."");
    if ($db->nr()>0) {
      $d = $db->f();
      $d['avatar'] = (!file_exists($d['avatar'])) ? "" :  $d['avatar'];
    }
  }
  //print_r($a);
  $sv->user['account'] = $a;
  
}


// ======================
// GET ICON
// =======================
function get_icon($ext) {
  global $sv;
  
  $id =$sv->cfg['paths']['file_icons'];
  if (file_exists($id.$ext.".gif")) {
    $ret = $id.$ext.".gif";    
  }
  else {
    $ret = $id."attach.gif";
  }
  
  $ret = "<img src='{$ret}' border=0>";
  return $ret;
}

// ===========================
// STATISTICS
// ===========================
function counter_box($start=2006) {
  global $sv, $std;
    $dir = "sources/counter/";
		$c_year = date("Y");
		$c_month = date("n");
		$year=$sv->_get['year'];
		$month=$sv->_get['month'];
		
		if ($year==""){$year=$c_year;};
		if ($month==""){$month=$c_month;};
		if (!checkdate($month,'1',$year)){ $month=$c_month; $year=$c_year;}

		$s_time=mktime(0,0,0,$month,1,$year);
		$month_title=strftime('%B',$s_time);


		
		$m_selector="<select name=new_date style='font-size:10px;border-style:none'
		              onchange=\"window.location.href=this.options[this.selectedIndex].value\">";
		for ($i=$start; $i<$start+3; $i++) {
			for ($j=1; $j<13; $j++) {
  			$m_t=$std->monthtorus($j);
  			if ($j==$month && $i==$year){$m_selector.="<option value='index.php?year=$i&month=$j' selected>$m_t $i</otion>";}
  			else{$m_selector.="<option value='index.php?year=$i&month=$j'>$m_t $i</otion>";};			
			}
		}
		
		$m_selector.="</select>";

		$output="
		<table width=100% border=\"0\" cellspacing=\"1\" cellpadding=\"5\">
	
		<tr><td style='padding:10px;padding-top:3px;' align=center><small>
		  <img 
		    src='{$dir}counter_diagram.php?month={$month}&year={$year}' style='border:1px solid #cccccc;'>
		</td></tr>
		<tr><td align=center colspan=2>$m_selector
		</td></tr>
		</table>
		";


		

	
		return $output;
	}
  //=====================================
  // validate email
  //=====================================
  function validate_email($email)
  {
    $ret = false;
    if (preg_match("/^[A-Za-z0-9\_\-]+@[A-Za-z0-9\_\-\.]+\.[A-Za-z]+$/msi", $email)) {
      $ret = true;
    }    
  return $ret;
  }

  //=====================================
  // mysql_date
  //=====================================
  function mysql_date($time='')
  {
    global $sv;
    
    $time = ($time=='') ? $sv->post_time : intval($time);
    
    
    $date = date("Y-m-d", $time);
    
    return $date;
  }
  

// =======================
// WHO TRUST
// =======================
function who_trust () {
global $sv, $db;
  
  $ret = array();
  $uid = intval($sv->user['session']['account_id']);
  
  if ($uid==0) return $ret;
  
  $db->q("SELECT blogid FROM {$sv->t['trusted']} WHERE user='{$uid}'", __FILE__, __LINE__);
  while ($d = $db->f()) {
    $ret[] = $d['blogid'];
  }
  $ret[] = $uid;
  return $ret;
    
}  


// =====================================
// user_stars
// ======================================
function user_stars($x = 0)
{
GLOBAL $sv;
	$ar = array(
  
10 => array('img'=>'10000000.gif', 'size'=>1),
100 => array('img'=>'10000000.gif', 'size'=>2),
300 => array('img'=>'10000000.gif', 'size'=>3),
500 => array('img'=>'10000000.gif', 'size'=>4),
750 => array('img'=>'10000000.gif', 'size'=>5),
1000 => array('img'=>'20000000.gif', 'size'=>1),
1250 => array('img'=>'20000000.gif', 'size'=>2),
1500 => array('img'=>'20000000.gif', 'size'=>3),
1750 => array('img'=>'20000000.gif', 'size'=>4),
2000 => array('img'=>'20000000.gif', 'size'=>5),
2250 => array('img'=>'30000000.gif', 'size'=>1),
2500 => array('img'=>'30000000.gif', 'size'=>2),
2750 => array('img'=>'30000000.gif', 'size'=>3),
3000 => array('img'=>'30000000.gif', 'size'=>4),
3250 => array('img'=>'30000000.gif', 'size'=>5),
3500 => array('img'=>'40000000.gif', 'size'=>1),
3750 => array('img'=>'40000000.gif', 'size'=>2),
4000 => array('img'=>'40000000.gif', 'size'=>3),
4250 => array('img'=>'40000000.gif', 'size'=>4),
4500 => array('img'=>'40000000.gif', 'size'=>5),
4750 => array('img'=>'50000000.gif', 'size'=>1),
5000 => array('img'=>'50000000.gif', 'size'=>2),
5250 => array('img'=>'50000000.gif', 'size'=>3),
5500 => array('img'=>'50000000.gif', 'size'=>4),
5750 => array('img'=>'50000000.gif', 'size'=>5),
6000 => array('img'=>'60000000.gif', 'size'=>1),
6250 => array('img'=>'60000000.gif', 'size'=>2),
6500 => array('img'=>'60000000.gif', 'size'=>3),
6750 => array('img'=>'60000000.gif', 'size'=>4),
7000 => array('img'=>'60000000.gif', 'size'=>5),
7250 => array('img'=>'70000000.gif', 'size'=>1),
7500 => array('img'=>'70000000.gif', 'size'=>2),
7750 => array('img'=>'70000000.gif', 'size'=>3),
8000 => array('img'=>'70000000.gif', 'size'=>4),
8250 => array('img'=>'70000000.gif', 'size'=>5),
8500 => array('img'=>'16000000.gif', 'size'=>1),
8750 => array('img'=>'16000000.gif', 'size'=>2),
9000 => array('img'=>'16000000.gif', 'size'=>3),
9250 => array('img'=>'16000000.gif', 'size'=>4),
9500 => array('img'=>'16000000.gif', 'size'=>5),
9750 => array('img'=>'10000001.gif', 'size'=>1),
10000=> array('img'=>'10000001.gif', 'size'=>2),
10250=> array('img'=>'10000001.gif', 'size'=>3),
10500=> array('img'=>'10000001.gif', 'size'=>4),
10750=> array('img'=>'10000001.gif', 'size'=>5),
11000=> array('img'=>'11000000.gif', 'size'=>1),
11500=> array('img'=>'11000000.gif', 'size'=>2),
12000=> array('img'=>'11000000.gif', 'size'=>3),
12500=> array('img'=>'11000000.gif', 'size'=>4),
13000=> array('img'=>'11000000.gif', 'size'=>5),
13500=> array('img'=>'12000000.gif', 'size'=>1),
14000=> array('img'=>'12000000.gif', 'size'=>2),
14500=> array('img'=>'12000000.gif', 'size'=>3),
15000=> array('img'=>'12000000.gif', 'size'=>4),
15500=> array('img'=>'12000000.gif', 'size'=>5),
16000=> array('img'=>'13000000.gif', 'size'=>1),
16500=> array('img'=>'13000000.gif', 'size'=>2),
17000=> array('img'=>'13000000.gif', 'size'=>3),
17500=> array('img'=>'13000000.gif', 'size'=>4),
18000=> array('img'=>'13000000.gif', 'size'=>5),
19000=> array('img'=>'17000000.gif', 'size'=>1),
20000=> array('img'=>'17000000.gif', 'size'=>2),
21000=> array('img'=>'17000000.gif', 'size'=>3),
22000=> array('img'=>'17000000.gif', 'size'=>4),
23000=> array('img'=>'17000000.gif', 'size'=>5),
24000=> array('img'=>'15000000.gif', 'size'=>1),
25000=> array('img'=>'15000000.gif', 'size'=>2),
26000=> array('img'=>'15000000.gif', 'size'=>3),
27000=> array('img'=>'15000000.gif', 'size'=>4),
28000=> array('img'=>'15000000.gif', 'size'=>5)
);
	
	$ar_keys = array_keys($ar);
	$rar = array_reverse($ar_keys);
	
	
 	$ret = "";
 	foreach ($rar as $d) {
    if ($x>=$d) {
       $img = $ar[$d]['img'];
       $size = intval($ar[$d]['size']);
       
       $stars = array();
       $stars = array_pad($stars, $size, "<img src='image/stars/{$img}' border=0>");
     
       $ret = implode("", $stars);
       break;
    }
  }
  
  
	
	
	return $ret;
}


function get_sign($date) {
  if (!$date) return false;
  
  $ar = array(
  1 => array('start'=>"21.03", 'end'=>"20.04", 'title'=>'Овен'),
  2 => array('start'=>"21.04", 'end'=>"20.05", 'title'=>'Телец'),
  3 => array('start'=>"21.05", 'end'=>"21.06", 'title'=>'Близнецы'),
  4 => array('start'=>"22.06", 'end'=>"22.07", 'title'=>'Рак'),
  5 => array('start'=>"23.07", 'end'=>"23.08", 'title'=>'Лев'),
  6 => array('start'=>"24.08", 'end'=>"23.09", 'title'=>'Дева'),
  7 => array('start'=>"24.09", 'end'=>"23.10", 'title'=>'Весы'),
  8 => array('start'=>"24.10", 'end'=>"22.11", 'title'=>'Скорпион'),
  9 => array('start'=>"23.11", 'end'=>"21.12", 'title'=>'Стрелец'),
  10 => array('start'=>"22.12", 'end'=>"20.01", 'title'=>'Козерог'),
  11 => array('start'=>"21.01", 'end'=>"20.02", 'title'=>'Водолей'),
  12 => array('start'=>"21.02", 'end'=>"20.03", 'title'=>'Рыбы') 
      
  );
  
  $ret = false;
  foreach ($ar as $k=>$d) {
    list($d1, $m1) = explode(".", $d['start']);
    list($d2, $m2) = explode(".", $d['end']);
    
    list($xy, $xm, $xd) = explode("-", $date);  
    $xy = ($xy<1970) ? date("Y") : $xy;
      
    $x = mktime(1, 1, 1, $xm, $xd, $xy);
    
    $x1y = ($k==10 && $xm==1) ? $xy-1 : $xy;
    $t1 = mktime(0, 0, 0, $m1, $d1, $x1y);
    
    $x2y = ($k==10 && $xm==12) ? $xy+1 : $xy;
    $t2 = mktime(23, 59, 59, $m2, $d2, $x2y);
    
        
    //echo $this->gettime($x, 3);
    if ($x>$t1 && $x<$t2) {
      $ret = array('sign' => $k, 'title' => $d['title']); 
    }
  }
  
  return $ret;
}

function prepare_filename($filename) {
  global $sv;
  
  if ($sv->cfg['image']['random_names']==1) {
    $filename = uniqid(rand());
  }
  else {
    $filename = $filename;
  }

	$filename = strtolower(str_replace(" ","_",$filename));
	$filename = str_replace("'",'',$filename);
	$filename = str_replace('"','',$filename);
	$filename = $this->translit($filename); 
  $filename = preg_replace("|[^A-Za-z0-9\_\-]|msi", "", $filename);  		
	$filename = preg_replace("|^[^A-Za-z0-9]+|msi", "", $filename);  		
	
  $filename = ($filename=='') ?  uniqid(rand()) : $filename;
  
  return $filename;
  
}
  
function replace_bbcode($text='') {
  
  
$text = preg_replace('#\[b\](.*)\[/b\]#esiU', 
  "handle_bbcode_parameter('\\1','" . str_replace("'", "\'", '<b>\1</b>') . "')", $text);
$text = preg_replace('#\[u\](.*)\[/u\]#esiU', 
  "handle_bbcode_parameter('\\1','" . str_replace("'", "\'", '<u>\1</u>') . "')", $text);
$text = preg_replace('#\[i\](.*)\[/i\]#esiU', 
  "handle_bbcode_parameter('\\1','" . str_replace("'", "\'", '<i>\1</i>') . "')", $text);


$text = preg_replace("#\[url\](.*)\[/url\]#esiU", 
  "handle_bbcode_url('\\1', '', 'url')", $text);
$text = preg_replace('#\[url=(&quot;|"|\'|)(.*)\\1\](.*)\[/url\]#esiU', 
  "handle_bbcode_url('\\3', '\\2', 'url')", $text);

$text = preg_replace('#\[quote\](<br>|<br />|\r\n|\n|\r)??(.*)(<br>|<br />|\r\n|\n|\r)??\[/quote\]#esiU',
  "handle_bbcode_quote('\\2')", $text);


$text=preg_replace('#\[quote=(&quot;|"|\'|)(.*)\\1\](<br>|<br />|\r\n|\n|\r)??(.*)(<br>|<br />|\r\n|\n|\r)??\[/quote\]#esiU',
  "handle_bbcode_quote('\\4', '\\2')", $text);
			
  return $text;
}


function cut_bbcode($text='') {
  
  // QUOTE
	$text = preg_replace('#\[quote\](<br>|<br />|\r\n|\n|\r)??(.*)(<br>|<br />|\r\n|\n|\r)??\[/quote\]#esiU',"", $text);	
	$text = preg_replace('#\[quote=(&quot;|"|\'|)(.*)\\1\](<br>|<br />|\r\n|\n|\r)??(.*)(<br>|<br />|\r\n|\n|\r)??\[/quote\]#esiU',"", $text);
  
  return $text;
}
//=====================================
// extension
//=====================================
function unq($string)
{

	$string = (get_magic_quotes_gpc()) ? stripslashes($string) : $string;

return $string;	
}

function bd_info($bd=false) {
  $ret = array();
  $ret['date'] = $bd;
  
  list($y, $m, $d) = explode("-", $bd);
  
  $m = intval($m);
  $d = intval($d);
  $y = intval($y);
  $my = ($y<1970) ? date("Y") : $y;
  //var_dump(checkdate($m, $d, $y));
  
  
  
  $ret['time'] = $time = mktime(0, 0, 0, $m, $d, $my);
  
  $ret['f_time'] = $this->gettime($time, 1.5);

  
  $cy = date("Y");
  $cm = date("m");
  $cd = date("d");
  
  $ry = $cy-$y;
  if ($cm<$m) {
    $ry = $ry-1;
    
  }
  elseif ($cm==$m && $cd<$d) {
      $ry = $ry-1;    
  }
  
  $ret['age'] = $ry;
  
  $suf;
  switch ($ry) {
    case 1: case 21: case 31: case 41: case 51: case 61: $suf = 'год'; break;    
    case 2: case 3: case 4: case 22: case 23: case 24: case 32: case 33: case 34:    $suf = 'года'; break;
      
    case 5: case 6: case 7: case 8: case 9: case 10: case 25: case 26: case 27: case 28: case 29: case 30: 
    case 40: case 50: case 60: case 70:    $suf = 'лет'; break;    
  }
  $suf = ($ry>=10 && $ry<=20) ? "лет" : $suf;
  if ($suf =='') {
    $sub = intval(substr($ry, -1));
    if ($sub>1 && $sub<5) {
      $suf = 'года';
    }
    elseif ($sub>4 && $sub<=9) {
      $suf = 'лет';
    }
  }
  $ret['suf'] = $suf;
  return $ret;
}


function check_ban($user, $page='photo') {
  global $sv, $std, $db;
  
  $ar = array();
  $db->q("SELECT * fROM {$sv->t['ban']} WHERE user='{$user['id']}' AND page='{$page}'");
  while ($d = $db->f()) {
    $ar[$d['type']][] = $d['str'];
  }
  
  $ban = false;
  $ban_match = "";
  
  foreach ($ar['user'] as $str) {
    if ($ban) break;
    if ($sv->user['session']['account_id']==$str) {
      $ban = true;
      $ban_match = "user: {$str}";
    }
  }
  
  if (!$ban) {
    foreach ($ar['ip'] as $str) {
      if ($ban) break;
      
      
      $p = preg_quote($str);      
      $p = str_replace("\*", "[0-9]+", $p);
      
      if (preg_match("#^{$p}$#msi", $sv->ip)) {
        $ban = true;
        $ban_match = "ip: {$str}";
      }
      
    }
  }
  
  
  if ($ban) {
    echo "Доступ к данной странице ограничен.<br><br><a href='./?sendmsg={$user['id']}'>Написать письмо автору</a>
    <br><br>{$ban_match}";
    exit();
  }
  
}

function callback_flv($m) {
  
  $this->x++;
  
$ret = <<<EOD
      
<div id="player{$this->x}" style='color:red;'>[ Для просмотра видео нужно установить, либо обновить <a href="http://www.macromedia.com/go/getflashplayer">Flash Player</a> ]</div>

<script type="text/javascript">
var FO = { movie:"flvplayer.swf",width:"425",allowfullscreen:"true",height:"350",majorversion:"7",build:"{$this->x}",
flashvars:"height=350&width=425&file={$m[1]}" };
UFO.create(FO,"player{$this->x}");
</script>




EOD;



  
  return $ret;
}


function err_box($err, $errm) {
  
  if (!is_array($errm)) { 
    $errm = array($errm);
  }
  
  $ret = "";
  if (count($errm)<=0)  {
    $ret = "";
  }
  else {
    $c = ($err) ? "red" : "green";
    $c2 = ($err) ? "#ffdddd" : "#ddffdd";
    
    foreach($errm as $m) {
      $tr[] = "<tr><tD>{$m}</td></tr>";
    }
    
    $ret = "
    <div class='err_box'>
      <table width='100%' cellpadding='10' bgcolor='{$c2}' style='border:1px solid {$c};'><tr><td>
        <table width='100%' cellpadding='3' style='border:0;'>".implode("\n", $tr)."</table>
      </td></tr></table>
    </div>
    ";
  }
  
 
  return $ret;
}


function err_box_sv() {
  global $sv;
  
  $ret = "";
  if ($sv->msgs_count>0) {
    $tr = array();
    foreach($sv->msgs as $d) {
      $class = ($d['err']) ? "class='err'" : '';
      $tr[] = "<div {$class}>{$d['text']}</div>";
    }
    $ret = "
<style>
  div.msgs_box {display: block; margin: 5px 0; padding:10px; border: 1px solid green;  background-color: #ddffdd;}
  div.msgs_box div {display: block; margin: 2px 0; padding: 5px;}
  div.msgs_box div.err {background-color: #ffdddd;}
</style>
    
<div class='msgs_box'>".implode("\n", $tr)."</div>

";
  }
  
  return $ret;
}
// ========= END OF CLASS =================
}

?>