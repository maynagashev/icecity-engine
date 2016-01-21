<?php

class std_time {
  
/**
 * Форматирование времени
 *
 * @param unknown_type $time
 * @param unknown_type $vid
 * @param unknown_type $strtotime
 * @return unknown
 */
function format( $time, $vid, $strtotime = 0) {
  global $sv;
	
  if ($strtotime) {
   $time = strtotime($time);
  }
  
  $post_time = $sv->post_time;
  
  // указанное время
  $a = getdate($time);
  
  //текущее время
  $b = getdate($post_time);
  
  $min = $a["minutes"];
  $min = ( $min < 10 ) ? "0".$min : $min;  
  $dnn = $this->dntorus($a['wday']);
	 
switch ($vid) {
  
  // 0 краткие цифровые записи
	case 0:	$output="$a[mday].$a[mon].$a[year] $a[hours]:$min"; break;
	case 0.5: $output = date("d.m.Y, H:i", $time); break;
	case 0.6: $output = date("d.m.Y", $time); break;
	case 0.7: $output = date("d.m.y", $time); break;
	case 0.8: $output = date("d.m", $time); break;
	case 0.9: $output = date("H:i, d.m.Y", $time); break;
	
  // подробные текстовые на русском
	case 1: $output="$a[mday] ".$this->rus_month($a["mon"])." $a[year] года";break;
	
	case 1.1: 
     $dnn = $this->dntorus_short($a['wday']);	        
	   $output ="{$dnn}, $a[mday] ".$this->rus_month($a["mon"])." $a[year] года";	 
	break;
		
	case 1.5: 
     
	   $lim = $this->get_limits();
	 
	   //secho $time; print_r($lim);
     if ($time >= $lim[0][1] && $time <= $lim[0][2]) {
       $pre = "Сегодня, ";
     }
     elseif ($time >= $lim[1][1] && $time <= $lim[1][2]) {
       $pre = "Завтра, ";
     }
     elseif ($time >= $lim[2][1] && $time <= $lim[2][2]) {
       $pre = "Послезавтра, ";
     }
     else {
       $pre = "";
     }
    
     
	   $output="{$pre} $a[mday] ".$this->rus_month($a["mon"])." $a[year] года";
	 
	break;
	
	
	
	case 1.6: 
     $dnn = $this->dntorus_short($a['wday']);
	   $lim = $this->get_limits();
	 
	   //secho $time; print_r($lim);
     if ($time >= $lim[0][1] && $time <= $lim[0][2]) {
       $pre = "[cегодня]";
     }
     elseif ($time >= $lim[1][1] && $time <= $lim[1][2]) {
       $pre = "[завтра]";
     }
     elseif ($time >= $lim[2][1] && $time <= $lim[2][2]) {
       $pre = "[послезавтра]";
     }
     else {
       $pre = "";
     }
    
     
	   $output = ucfirst($dnn).", $a[mday] ".$this->rus_month($a["mon"])." $a[year] года ".strtolower($pre)."";
	 
	break;
		
case 1.7: 
     $dnn = $this->dntorus_short($a['wday']);	   
	   $lim = $this->get_limits();
	 
	   //secho $time; print_r($lim);
     if ($time >= $lim[0][1] && $time <= $lim[0][2]) {
       $pre = "Сегодня, ";
     }
     elseif ($time >= $lim[1][1] && $time <= $lim[1][2]) {
       $pre = "Завтра, ";
     }
     elseif ($time >= $lim[2][1] && $time <= $lim[2][2]) {
       $pre = "Послезавтра, ";
     }
     else {
       $pre = "{$dnn}, ";
     }
    
     
	   $output="{$pre} $a[mday] ".$this->rus_month($a["mon"])." $a[year] года";
	 
	break;

	
	case 2: $output="$a[mday] ".$this->rus_month($a["mon"])." $a[year] года, $a[hours]:$min";break;
	case 3: $output="$a[mday] ".$this->rus_month($a["mon"])." $a[year] года, $dnn, $a[hours]:$min";break;
	
	// разница в часах в течении суток
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
			
  // 01.01.2001
	case 6:	 
			$a['mon'] = ($a['mon']<10) ? '0'.$a['mon'] : $a['mon'];
			$output="{$a['mday']}.{$a['mon']}.{$a['year']}"; 
			break;
  
  // час:минуты
  case 7:	
    $output="{$a['hours']}:{$min}"; 
  break;			
  
  // +/- количество дней от сегодняшнего дня
	case 8: 
			$r = $post_time - $time;	
			
			$d = floor($r / 86400);
			if ($d<0) {
			  $d = "+ ".abs($d)." дн.";
			}
			elseif ($d>0) {
			  $d = "- ".abs($d)." дн.";
			}
			else {
			  $d = "сегодня";
			}
		  
			$output = $d;
			break;
						
	};
	return $output;
}

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

function dntorus_short($dn)
{
 if ($dn=="1"){$dn="Пн";};
 if ($dn=="2"){$dn="Вт";};
 if ($dn=="3"){$dn="Ср";};
 if ($dn=="4"){$dn="Чт";};
 if ($dn=="5"){$dn="Пт";};
 if ($dn=="6"){$dn="Сб";};
 if ($dn=="7"){$dn="Вс";};
 if ($dn=="0"){$dn="Вс";};
return $dn;

}

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

/**
 * Номер месяца по названию
 *
 * @param unknown_type $t
 * @return unknown
 */
function rustomonth($t) {
  
  $patterns = array(
    1 => "нвар",
    2 => "февр",
    3 => "март",
    4 => "апрел",
    5 => "ма(й|я)",
    6 => "июн",
    7 => "июл",
    8 => "август",
    9 => "сент",
    10 => "окт",
    11 => "нояб",
    12 => "декаб"
  );
  
  foreach ($patterns as $id => $p) {
    if (preg_match("#{$p}#si", $t)) {
      return $id;
    }
  }
  
  return 0;
}


/**
 * Возвращает unix_stamp от даты заданной в формате:
 *  27 Ноября 2008, 17:35
 *  Сегодня, 16:32
 * 
 *
 * @param unknown_type $t
 * @param unknown_type $time
 */
function str2date1($t, $time = 0) {
  
  $time = ($time) ? $time : time();
  $set_current = 0;
  
  // $date_str,$hour:$min
  if (preg_match("#^(.*),\s*([0-9]{1,2})\:([0-9]{1,2})$#", $t, $m)) {
    $date_str = trim($m[1]);
    
    $hour = $m[2];
    $min = $m[3];
    
    
    if (preg_match("#сегодн#si", $date_str, $m)) {
      $day = date("d", $time);
      $month = date("m", $time);
      $year = date("Y", $time);
    }
    // 27 Ноября 2008
    elseif(preg_match("#^([0-9]{1,2})([^0-9]+)([0-9]{2,4})$#si", $date_str, $m)) {
      $day = $m[1];
      $year = $m[3];
      $month_str = trim($m[2]);
      $month = $this->rustomonth($month_str);
      $month = ($month) ? $month : date("m", $time);      
    }
    else {
      $set_current = 1;
    }
  }
  else {
    $set_current = 1;
  }
   
  if ($set_current) {
    $ret = $time;
  }
  else {
    $ret = mktime($hour, $min, 0, $month, $day, $year);
  }
  
  return $ret;
}


function year_suf($y) {

  switch ($y) {
    case 1: case 21: case 31: case 41: case 51: case 61: $suf = 'год'; break;    
    case 2: case 3: case 4: case 22: case 23: case 24: case 32: case 33: case 34:    $suf = 'года'; break;
      
    case 5: case 6: case 7: case 8: case 9: case 10: case 25: case 26: case 27: case 28: case 29: case 30: 
    case 40: case 50: case 60: case 70:    $suf = 'лет'; break;    
  }
  $suf = ($y>=10 && $y<=20) ? "лет" : $suf;
  if ($suf =='') {
    $sub = intval(substr($y, -1));
    if ($sub>1 && $sub<5) {
      $suf = 'года';
    }
    elseif ($sub>4 && $sub<=9) {
      $suf = 'лет';
    }
  }
  


return $suf;  
}

function get_limits() {
  global $sv;
  
  $time = $sv->post_time;
  
  $ret = array();
  for($i=0; $i<3; $i++) {
    
    $t = array();
    $t[1] = mktime(0, 0, 0, date("m", $time), date("d", $time)+$i,   date("Y", $time));
    $t[2] = mktime(23, 59, 59, date("m", $time), date("d", $time)+$i,   date("Y", $time));
    $ret[$i] = $t;
    
    unset($t);
  }
  
  
  return $ret;
  
}

function calendar($month, $year, $url)
{
  global $std, $sv;
 
  $date = $this->get_date();
  
  
  
 
  $car = $this->calendar_ar;
  
  $day_seans_count=4;
  $tohtml=""; $bgcolor='#efefef';
  
  
  if ($month==12){ 
    $max=31;
  }
  else {			
  	$next_month=$month+1;
  	$max = date("d",mktime(0,0,0,$next_month,0,$year)); // last_day
  }

  $week=1;	
  $k=0; 
  $m=0;
  
  //PARSING
  $first_day=date("w", mktime(0,0,0,$month,1,$year)); 
  for ( $i=1; $i < $max+1; $i++) {
  	$dn = date("w", mktime(0,0,0,$month,$i,$year));
  	if ($dn==0) $dn=7;
	  if ($first_day!="1" && $week==1 && $k==0) {
		  for ($k=1;$k<$dn;$k++){$month_array[$k][$week]=""; }
		}
	  if ($i==$max && $dn!=7)	{
      for($m=$dn;$m<8;$m++){ $month_array[$m][$week]=""; }
    }
	  
	  $month_array[$dn][$week]=$i;	
	  if ($dn==7 && $i!=$max){
      $week++; 
      $first_day=date("w", mktime(0,0,0,$month,$i,$year));
    }
  }

  //FORMATTING  
  for ($i=1;$i<$week+1;$i++):
  	$tohtml.="<tr>";
  	
  	for ($j=1; $j<8; $j++){
  	  
  	  
  		$chislo = $month_array[$j][$i];
      
  		$time = mktime(0, 0, 0, $month, $chislo, $year);
  	  
  		$month  = (strlen($month)==1) ? '0'.$month : $month;
  		$chislo = (strlen($chislo)==1) ? '0'.$chislo : $chislo;
  		$day_id = $year."-".$month."-".$chislo;
  		
  		
  		if ($chislo!=""){  			
        
  		  
        $future = ($time>$sv->post_time-60*60*24) ? "_future" : 0;
        
  		  $sun = ($j>5 && $j<8) ? "_sun" : "";
			  $sel = ($date && $year==$date['y'] && $month==$date['m'] && $chislo==$date['d']) ? "_sel" : "";
			  $sel = ($year==date("Y") && $month==date("n") && $chislo==date("j")) ? "_cur'" : $sel;
				$tohtml.="
				  <td class='cl_cell{$sun}{$sel}' {$cur}><a href='{$url}&date=$day_id' class='cl_link{$future}'>$chislo</a></b></td>";
			
  		}
  		else {
  		  $tohtml.="<td align=center></td>";
  		}
  		
  	}
  	$tohtml.="</tr>";
  endfor;

  $month_title=$this->monthtorus(date("n", mktime(0,0,0,$month,1,$year)));
  
  $output="
  <table width=170 border=0>
  	<tr><td colspan=7 align=right><b>$month_title, $year</td></tr>
  	<tr>
  	 <td align=center class='cl_title'>Пн</td>
  	 <td align=center class='cl_title'>Вт</td>
  	 <td align=center class='cl_title'>Ср</td>
  	 <td align=center class='cl_title'>Чт</td>
  	 <td align=center class='cl_title'>Пт</td>
  	 <td align=center class='cl_title_sun'>Сб</td>
  	 <td align=center class='cl_title_sun'>Вс</td>
  	</tr>
  $tohtml
  </table>


  ";
return $output;
}    

function get_date() {
  global $sv;
  
  if (!isset($sv->_get['date'])) return false;
  
  
  if (!preg_match("#^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2})$#si",  $sv->_get['date'], $m)) return false;
  
  if (!checkdate($m[2], $m[3], $m[1])) return false;
  
  $ret = array('d'=>$m[3], 'm'=>$m[2], 'y'=>$m[1]);
  
  
  return $ret;
  
  
}

function get_week_ar($time=0) {
  global $sv;
  
  $time = ($time<=0) ? $sv->post_time : $time;
  
  $j = date("w", $time);
  $j = ($j==0) ? 7 : $j;
  
  $d['d'] = date("d", $time);
  $d['m'] = date("m", $time);
  $d['y'] = date("Y", $time);
  
  $ret = array();
  for($i=1; $i<=7; $i++) {
    $fix = ($i-$j);
    $t = mktime(0, 0, 0, $d['m'], $d['d']+$fix, $d['y']);
    $ret[] = date("Y-m-d", $t);
  }
   
  
  return $ret;
}

function format_date($string)
{
	$time=explode("-",$string);
	$dn = $this->dntorus(date("w",mktime(0,0,0,$time[1],$time[2],$time[0])));
	$output=$time[2]." ".$this->rus_month($time[1])." ".$time[0]." года, $dn ";

	return $output;
}  

function f_date($string, $x)
{
  
  if (strtotime($string)) {
    $t = strtotime($string);
  }
  else {
  	$time=explode("-",$string);
  	$t = mktime(0,0,0,$time[1],$time[2],$time[0]);
  }
	
	$ret = $this->format($t, $x);
 
	return $ret;
}  

function check_date($date='2000-01-01')	{
		
	$out=$date;	
	$time=explode("-", $date);	
	if (!checkdate($time['1'],$time['2'],$time['0']))		{
    $out=date("Y-m-d");
  }

return $out;

}

function day_selector($addr="", $cdate, $s_prev=0, $s_next=0, $format=1.6) {
  global $sv, $std, $db;
    
     
    $opt = array();
    $dates = array();
    
    //PREVISIOS
    $t = $sv->post_time-60*60*24*$s_prev;
    for ($i=0; $i<$s_prev-1; $i++) {      
      $t = $t + 60*60*24;        
      $dates[] = $t;      
      
    }
 
    
    //CURRENT
    $t = $sv->post_time;
    $dates[] = $t;
   
    
    //NEXT 
    for ($i=0; $i<$s_next; $i++) {      
      $t = $t + 60*60*24*1;
      $dates[] = $t;      
    }
  
    $selected = false;
    foreach ($dates as $t) {
      $ft = $std->time->format($t, $format);
      $date = date("Y-m-d", $t);
      
      $s = ($cdate==$date) ? " selected" :"";
      $selected = ($s!='') ? true : $selected;
      $opt[] = "<option value='{$date}'{$s}>{$ft}</option>";
    }
   
    $add = (!$selected) ? "<option value='{$cdate}' style='font-size:150%;'>&#8734;</option>" : "";
    $ret = "<select style='width:100px;' name=''  onchange=\"window.location.href='{$addr}'+this.options[this.selectedIndex].value\">
    {$add}
    ".implode("\n", $opt)."
  
    
    </select>";
    
    return $ret;   
}
  
function month_fd($ct) {
  global $sv, $std, $db;
  
  $t = mktime(0,0,0,date("m", $ct), 1, date("Y", $ct));
  return date("Y-m-d", $t);
  
}

function date_box($time, $str=0, $name="time", $cur=0, $setcurrent = 0) {
 
  if ($str) {
    if ($time=='0000-00-00') {
      $time = 0;
    }
    else {
      $time = strtotime($time);
    }
  }
  
  $time = ($time==0 && $cur) ? time() : $time;
  
  if ($time>0) {
    $y = date('Y', $time);
    $m = date('n', $time); 
    $d = date('j', $time);
  }
  else {
    $y = $m = $d = "-";
  }
  
 //echo "{$y}={$m}={$d}";
  // year
  $y_opt = "<option value='0000'>-</option>";
  for ($i = 1900; $i<2020; $i++) {
     $sel = ($i==$y) ? " selected" : "";
     $y_opt .= "<option value='{$i}'{$sel}>{$i}</option>";
  }
  $y_sel = "<select name='{$name}[year]'>".$y_opt."</select>";
  
  
  //month
  $m_opt = "<option value='00'>-</option>"; 
  for ($i = 1; $i<=12; $i++) {
     $sel = ($i==$m) ? " selected" : "";
     $m_opt .= "<option value='{$i}'{$sel}>".$this->monthtorus($i)."</option>";
  }
  $m_sel = "<select name='{$name}[month]'>".$m_opt."</select>";

  //day
  $d_opt = "<option value='00'>-</option>";
  for ($i = 1; $i<=31; $i++) {
     $sel = ($i==$d) ? " selected" : "";
     $d_opt .= "<option value='{$i}'{$sel}>".$i."</option>";
  }
  $d_sel = "<select name='{$name}[day]'>".$d_opt."</select>";

  
  $setcurrent_checkbox = ($setcurrent) ? "<td nowrap><input type='checkbox' name='{$name}[setcurrent]'><small>&nbsp;выставить&nbsp;текущую&nbsp;дату</small></td>" : "";
    
  $ret = "
  <table cellpadding=0 cellspacing=0 border=0>
    <tr><td>{$d_sel}</td><td>{$m_sel}</td><td>{$y_sel}</td>
    {$setcurrent_checkbox}
    </tr>    
  </table>
  ";
    
  return $ret;  
    
  
}

function datetime_box($time, $str=0, $name="time", $cur=0, $setcurrent = 0) {
  
  if ($str) {
    if ($time=='0000-00-00 00:00:00') {
      $time = 0;
    }
    else {
      $time = strtotime($time);
      if ($time===false) {
        $time = 0;
      }
    }
  }

  
  $time = ($time==0 && $cur) ? time() : $time;
  
  if ($time>0) {
    $year = date('Y', $time);
    $month = date('n', $time); 
    $day = date('j', $time);
    $h = date("H", $time);
    $m = date("i", $time);
    $s = date("s", $time);
  }
  else {
    $year = $month = $day = "-";
    $h = $m = $s = "00";
  }
  
  //echo "{$year}={$month}={$day} {$h}-{$i}-{$s}";
 
  // year
  $year_opt = "<option value='0000'>-</option>";
  for ($i = 1970; $i<2020; $i++) {
     $sel = ($i==$year) ? " selected" : "";
     $year_opt .= "<option value='{$i}'{$sel}>{$i}</option>";
  }
  $year_sel = "<select name='{$name}[year]'>".$year_opt."</select>";
  
  
  //month
  $month_opt = "<option value='00'>-</option>"; 
  for ($i = 1; $i<=12; $i++) {
     $sel = ($i==$month) ? " selected" : "";
     $month_opt .= "<option value='{$i}'{$sel}>".$this->monthtorus($i)."</option>";
  }
  $month_sel = "<select name='{$name}[month]'>".$month_opt."</select>";

  //day
  $day_opt = "<option value='00'>-</option>";
  for ($i = 1; $i<=31; $i++) {
     $sel = ($i==$day) ? " selected" : "";
     $day_opt .= "<option value='{$i}'{$sel}>".$i."</option>";
  }
  $day_sel = "<select name='{$name}[day]'>".$day_opt."</select>";

  
  $h_sel = "<input type='text' size='2' value='{$h}' name='{$name}[h]' style='text-align:center;'>";
  $m_sel = "<input type='text' size='2' value='{$m}' name='{$name}[m]' style='text-align:center;'>";
  $s_sel = "<input type='text' size='2' value='{$s}' name='{$name}[s]' style='text-align:center;'>";
  
  $setcurrent_checkbox = ($setcurrent) ? "<td nowrap><input type='checkbox' name='{$name}[setcurrent]'><small>&nbsp;выставить&nbsp;текущее</small></td>" : "";
  
  $ret = "
  <table cellpadding=0 cellspacing=0 border=0>
    <tr><td>{$day_sel}</td><td>{$month_sel}</td><td>{$year_sel}</td>
    <td>{$h_sel}</td><td>:</td><td>{$m_sel}</td><td>:</td><td>{$s_sel}</td>
    {$setcurrent_checkbox}
    </tr>
  </table>
  ";
    
  return $ret;  
    
  
}

function mm_ss($sec, $sep = ":") {
  
  $m = floor($sec/60);
  $s = round($sec-60*$m);
  
  $h = floor($m/60);
  
  $ss = ($s<10) ? '0'.$s : $s;
  $mm = ($m<10) ? '0'.$m : $m;
  $hh = ($h<10) ? '0'.$h : $h;
    
  $ret = ($h>0) ? $hh.$sep.$mm.$sep.$ss : $mm.$sep.$ss;
  return $ret;
}

/**
 * Возраст по дате
 *
 * @param date $date
 * @return array('age' => $n, 'suf' => $rus_suf, 'date' => $bd, 'f_time' => 'Пон, 17 марта 1984 года.')
 */
function age( $date ) {
  
  if (!preg_match("#^[0-9]+\-[0-9]+\-[0-9]+$#si", $date)) return false;
  
  $ret = array();
  $ret['date'] = $date;
  
  list($y, $m, $d) = explode("-", $date);
  
  $m = intval($m);
  $d = intval($d);
  $y = intval($y);
  $my = ($y<1900) ? date("Y") : $y;  
  
  $ret['time'] = $time = mktime(0, 0, 0, $m, $d, $my);
  
  $ret['f_time'] = $this->format($time, 1.1);

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
  
  switch ($ry) {
    case 1: case 21: case 31: case 41: case 51: case 61: $suf = 'год'; break;    
    case 2: case 3: case 4: case 22: case 23: case 24: case 32: case 33: case 34:    $suf = 'года'; break;      
    case 5: case 6: case 7: case 8: case 9: case 10: case 25: case 26: case 27: case 28: case 29: case 30: case 40: case 50: case 60: case 70:    $suf = 'лет'; break;    
    default: $suf = '';
  }
  $suf = ($ry>=10 && $ry<=20) ? "лет" : $suf;
  if ($suf =='') {
    $sub = intval(substr($ry, -1));
    if ($sub==0) {
      $suf = 'лет';
    }
    elseif ($sub==1) {
      $suf = 'год';
    }
    elseif ($sub>1 && $sub<5) {
      $suf = 'года';
    }
    elseif ($sub>4 && $sub<=9) {
      $suf = 'лет';
    }
  }
  $ret['suf'] = $suf;
  return $ret;
}

/**
 * Знак зодиака по дате
 *
 * @param date $date
 * @return array('sign' => $n, 'title' => $title)
 */
function sign($date) {
  if (!preg_match("#^[0-9]+\-[0-9]+\-[0-9]+$#si", $date)) return false;
  
  list($xy, $xm, $xd) = explode("-", $date);  
  $xy = ($xy<1970) ? date("Y") : $xy;
      
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


//endof class  
}




?>