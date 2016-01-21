<?php

/**
 Используются:
  geoip.inc               08-Jan-2008 10:37    22k  
  geoipcity.inc           01-Nov-2005 22:06     6k  
  geoipregionvars.php     26-Feb-2007 21:09    84k   

 Базы данных:
  http://www.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz
  http://www.maxmind.com/download/geoip/database/GeoIP.dat.gz 
  
 */
class std_geoip {
  
var $geoip_dir  = "sources/lib/geoip/";
var $dat_file   = "sources/lib/geoip/GeoIP.dat";
var $dat_city   = "sources/lib/geoip/GeoLiteCity.dat";

var $loaded_main = 0;
var $loaded_city = 0;
var $GEOIP_REGION_NAME = array();

function get_country($ip='')   {
  global $sv;
  
  $this->required_main();
  $ip = ($ip=='') ? $sv->ip : $ip; 
  
  $gi = geoip_open($this->dat_file,  GEOIP_STANDARD);
  $ret['code'] = geoip_country_code_by_addr($gi, $ip);
  $ret['name'] = geoip_country_name_by_addr($gi, $ip);
  
  geoip_close($gi);
/*
//Example:
echo geoip_country_code_by_addr($gi, "24.24.24.24") . "\t" .
     geoip_country_name_by_addr($gi, "24.24.24.24") . "\n";
echo geoip_country_code_by_addr($gi, "80.24.24.24") . "\t" .
     geoip_country_name_by_addr($gi, "80.24.24.24") . "\n";
*/
  
  return $ret;
}

function get_city($ip='')   {
  global $sv, $GEOIP_REGION_NAME;
  
  $this->required_city();
  $ip = ($ip=='') ? $sv->ip : $ip;
  
  $gi = geoip_open($this->dat_city,  GEOIP_STANDARD);
  $record = geoip_record_by_addr($gi, $ip);
  geoip_close($gi);
  
  if (!is_object($record)) return false;
  
  $record->region_name = $this->GEOIP_REGION_NAME[$record->country_code][$record->region];
/*
//Example:
print $record->country_code . " " . $record->country_code3 . " " . $record->country_name . "\n";
print $record->region . " " . $GEOIP_REGION_NAME[$record->country_code][$record->region] . "\n";
print $record->city . "\n";
print $record->postal_code . "\n";
print $record->latitude . "\n";
print $record->longitude . "\n";
print $record->dma_code . "\n";
print $record->area_code . "\n";
*/
  
  return $record;
}


function required_city() {
  $this->required_main();  
  if (!$this->loaded_city) {
    require_once($this->geoip_dir."geoipcity.inc");
    $this->loaded_city = 1;
  }  
  $this->GEOIP_REGION_NAME = $GEOIP_REGION_NAME;
}
function required_main() {
  if (!$this->loaded_main) {
    require_once($this->geoip_dir."geoip.inc");
    $this->loaded_main = 1;
  }    
}


}


?>