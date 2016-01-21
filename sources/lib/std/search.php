<?php 

class std_search  {
  
  
function parse_sstr($sstr = "") {
  
  $ar = explode(" ", $sstr) ;
  $words = array();
  
  // выбираем только значимые слова
  foreach ($ar as $d) {
    $d = trim($d);
    if ($d!='') {
      $words[] = $d;
    }    
  }

  $s = count($words);
  //if no words break
 // if ( $s < 1 ) return false;
  
  //if <3 around spaces
  if ($s == 1 && strlen($words[0])<2) {    
    $words[0] = " {$words[0]} ";   
  }
  
  //escaping
  foreach ($words as $k=>$v) {
    $words[$k] = $this->escape($v);
  }
   
  
 
  
  
  return $words;
  
}

// #############################################################################
// makes a word or phrase safe to put into a LIKE sql condition
function escape($word){

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

	// replace MySQL wildcards
	$word = str_replace($find, $replace, addslashes($word));

	return $word;
}
  


  
  
  
  
  //endofclass
}



?>