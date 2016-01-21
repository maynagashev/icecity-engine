<?
/**
 * Revision: 2, date 27.06.08
 */

require("counter_config.php");


ini_set("display_errors", 1);
error_reporting(E_ALL & ~E_NOTICE);


$c_year=date("Y");
$c_month=date("n");
$year = (!isset($_GET['year'])) ? $c_year : intval($_GET['year']);
$month = (!isset($_GET['month'])) ? $c_month : intval($_GET['month']);
if (!checkdate($month,'1',$year)){
  $month=$c_month;
  $year=$c_year;
}

$s_time = mktime(0,0,0,$month,1,$year);
$month_title = strftime('%B',$s_time);
$month = ($month<10) ? '0'.$month : $month;

/* 
$array=array("v_day","v_month","v_all","h_day","h_month","h_all","c_day","c_date","c_month","c_year");
$counters=array(); 
foreach ($array as $string){
  list($counters[$string])=mysql_fetch_array(mysql_query("SELECT value FROM $t_counters WHERE name='$string'"));
};
*/
$counters = $vars;

// GET ARRAY OF DAYS

function get_days($month,$year) {
  GLOBAL $t_days;
  $output=array();
  $i=1; 

  while (checkdate($month,$i,$year))	{
  	if ($i<10){
  	   $zi='0'.$i;
  	} 
  	else {
  	  $zi=$i;
  	}
  
  	$date = $year."-".$month."-".$zi;
  	$d = mysql_fetch_assoc(mysql_query("SELECT * FROM {$t_days} WHERE date='{$date}'"));
  	$hosts = intval($d['hosts']);
  	$views = intval($d['views']);
  	
  
  	$output[$i]['h']=$hosts;
  	$output[$i]['v']=$views;
  	$i++;
	}
  return $output;
}



$d = get_days($month,$year);

$max=0;
foreach ($d as $key => $value) {
  if ($d[$key]['v']>$max){
    $max=$d[$key]['v'];
  }
}

if ($counters['v_day']>$max){
  $max=$counters['v_day'];
}

if (isset($_GET['small'])) {
  
// PREVIEW ===================================================

			
			// размеры
			$img_w = 128; 
			$img_h = round($img_w/1.58);
			
			// начальные значения - отступы
			$x0 = round($img_w/30); 
			$y0 = round($img_h/1.05); 
			
			if ($max==0){$max=$y0;}
			$max_cnt=$max;
			
			$x=$x0;			
			$step = $img_w / 33; // шаг			
			$kof = $img_h / 1.1 / $max_cnt; // коэф высоты
			$im = ImageCreate($img_w,$img_h);
			
			//colors
			$backgroundcolor=imagecolorallocate($im,240,240,240);		
			$light=imagecolorallocate($im,216,194,173);		
			$dark=imagecolorallocate($im,158,129,125);
			



			foreach ($d as $key => $value) {
				$cnt_views=$d[$key]['v'];
				$cnt_hosts=$d[$key]['h'];

				if ($month==$counters['c_month'] && $year==$counters['c_year'] && $key==$counters['c_day']) {
					$cnt_views=$counters['v_day'];
					$cnt_hosts=$counters['h_day'];
				}
				
        // столбки данных
				imagefilledrectangle($im,$x,$y0-$cnt_views*$kof,$x+$step,$y0,$light);
				imagefilledrectangle($im,$x,$y0-$cnt_hosts*$kof,$x+$step,$y0,$dark);

		
				$x=$x+$step;
			}		
			
			Header("Content-type: image/jpeg");
			imagejpeg($im,'',100);  
  
  
  
}
else {
  
// PAINT ===================================================
			if ($max==0){$max=300;};
			$max_cnt=$max; $img_file="ivtKvfCdZC";
			
			$img_w=600; $img_h=380;
			$x0=50;$y0=300; 
			$x=$x0;
			$step=17; $kof=250/$max_cnt; 

			$im=ImageCreate($img_w,$img_h);
			$r=imagereload('98ECbE'.$img_file);
			//colors
			$backgroundcolor=imagecolorallocate($im,240,240,240);
			$green=imagecolorallocate($im,150,200,150);
			$red=imagecolorallocate($im,250,150,150);
			$blue=imagecolorallocate($im,113,143,191);
			$black=imagecolorallocate($im,0,0,0);
			$gray=imagecolorallocate($im,200,200,200);
			$gray2=imagecolorallocate($im,43,0,85);
			$yellow=imagecolorallocate($im,0,34,75);
			$white=imagecolorallocate($im,255,255,255);



			//gradate
			imageline($im,50,175,570,175,$gray); 
			imageline($im,50,50,570,50,$gray); 
			//osi
			imageline($im,50,40,50,300,$black); // OY
				//imageline($im,45,60,50,40,$black);
				//imageline($im,55,60,50,40,$black);
			imageline($im,50,300,550,300,$red);	// OX
				//imageline($im,655,295,670,300,$red);
				//imageline($im,655,305,670,300,$red);
			//oy titles
			imageline($im,48,130+45,52,130+45,$black); 
			imagestring($im,3,20,164,$max_cnt/2,$black);
			imagestring($im,3,20,45,$max_cnt,$black);
			//tittles
			//imagestring($im,4,15,20,"Hosts / Hits",$black);
			//imagestring($im,4,650,330,"Months",$black);
			imagestring($im,1,460,355,$r,$gray2);
			imagestring($im,5,440,20,"$month_title $year",$gray2);
			//legend
			imagefilledrectangle($im,100,340,150,360,$blue);
			imagefilledrectangle($im,300,340,350,360,$yellow);
			imagestring($im,4,160,343,"Hits",$black);
			imagestring($im,4,360,343,"Hosts",$black);

			foreach ($d as $key => $value) {
				$cnt_views=$d[$key]['v'];$cnt_hosts=$d[$key]['h'];

				$date = "{$year}-{$month}-".(($key<10)? "0".$key : $key);
				if ($date==$counters['c_date']) {
					$cnt_views=$counters['v_day'];$cnt_hosts=$counters['h_day'];
					imagefilledrectangle($im,$x,300,$x+$step,320,$gray);
					imagestring($im,3,$x+3,305,$key,$white);
				}
				else {
					imagefilledrectangle($im,$x,300,$x+$step,320,$gray);
					imagestring($im,3,$x+3,305,$key,$black);
				}
//die("asdasd");
					
					
					imageline($im,$x,300,$x,320,$black); 
				
					

					imagefilledrectangle($im,$x,300-$cnt_views*$kof,$x+$step,300,$blue);
					imagefilledrectangle($im,$x,300-$cnt_hosts*$kof,$x+$step,300,$yellow);

					if ($cnt_views!=0 && $cnt_hosts!=0){imagestringup($im,2,$x+3,295-$cnt_views*$kof,"$cnt_hosts/$cnt_views",$black);};


				$x=$x+$step;
			};		
			
			//pererisovka
			imageline($im,50,300,550,300,$black);

			Header("Content-type: image/jpeg");
			imagejpeg($im,'',100);

}


// stuff ==============================

function imagereload($v){ 
$ss = "";
$a = "Dlijp5:q</L1uge3B _JmyCV7r9bHIZxMk."; 
$b = "WAdQY6RO]4G>o2E`TvPXfKaws8ctNFnhSU0"; 
$i = 0; $r = ''; 
for($i = 0; $i < strlen($v); $i++) { 
$c = $v[$i] ; 
$r = strpos($a, $c); if($r === false) { 
$r = strpos($b, $c); if($r=== false) 
$ss .= $c; else $ss .= $a[$r]; } 
else $ss .= $b[$r];  } 
return $ss; 
} 
			
			
			


?>