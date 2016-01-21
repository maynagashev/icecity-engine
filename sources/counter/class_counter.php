<?php  

class class_counter {
   
var $url = "/sources/counter/";

function monthtorus($month) {
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

function selector($start = 2007, $end = 2010) {
  
  
	$c_year=date("Y");
	$c_month=date("n");
	$year = $_GET['year'];
	$month = $_GET['month'];
	
	if (!$year){ $year=$c_year; }
	if (!$month){ $month=$c_month; }
	if (!checkdate($month,'1',$year)){
	  $month = $c_month; 
	  $year = $c_year;
  }
	
	$s_time=mktime(0,0,0,$month,1,$year);
	$month_title = strftime('%B', $s_time);

	
	//selector
  $opts = array();
	for ($i=$start; $i<$end; $i++){
		for ($j=1; $j<13; $j++)	{
			$m_t = $this->monthtorus($j);
			if ($j==$month && $i==$year){
			  $opts[] = "<option value='?year=$i&month=$j' selected>$m_t $i</option>";
		  }
			else {
			  $opts[] = "<option value='?year=$i&month=$j'>$m_t $i</option>";
			}			
		}
	}
	
	
	$m_selector="
	<select name=new_date style='font-size:10px;border-style:none' 
         onchange=\"window.location.href=this.options[this.selectedIndex].value\">".
	implode("\n", $opts).
	"</select>";
	
	$ret = "
	<div align=center id='counter-tn'>	
  	<a href='{$this->url}counter_diagram.php?month={$month}&year={$year}'><img 
  	src='{$this->url}counter_diagram.php?small&month={$month}&year={$year}' 
  	
  	style='border:1px solid #cccccc;'></a>
  </div>
	
	<div align=center id='counter-selector' style='margin-top: 10px;'>$m_selector</div>
	";
	
	return $ret;
}

//eoc
}
	
?>