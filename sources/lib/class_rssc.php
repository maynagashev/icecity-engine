<?php

/**
 * Кастомный класс для разборки RSS
 *
 */

	  
class class_rssc {
  
  var $depth = 0;  
  var $item = array();
  var $items = array();
  var $opened = "";
  
  /* 
    Parse attributes? - if true, then return item fields as: NAME => array('data' => $data, 'attr' => array() )
    else return item fields as: NAME => $data
  */
  var $parse_attributes = 0;
  var $attrs = array();

  
// PARSER 

function parse($d) {
  $this->items = array();
  $xml_parser = xml_parser_create();
  xml_set_element_handler($xml_parser,  array( &$this, "parse_startElement" ), array( &$this, "parse_endElement"));
  xml_set_character_data_handler($xml_parser, array( &$this, "parse_characterData" ));
  xml_parse($xml_parser, $d);
  xml_parser_free($xml_parser);
  return $this->items;
}  

function parse_startElement ( $parser, $name, $attrs) {  
  $this->attrs = $attrs;
  if ($name=='ITEM') {
    $this->item = ($this->parse_attributes) ? array('attr'=>$attrs) : array();
  }
  else {
    $this->opened = $name;
    $this->data = "";
  }       
  $this->depth++;
}

function parse_endElement($parser, $name) {    
  if ($name=='ITEM') {
    $this->items[] = $this->item;
    $this->opened = "";
  }
  else {
    $this->item[$this->opened] = ($this->parse_attributes) ? array('attr' => $this->attrs, 'data' => $this->data) : $this->data;    
    $this->data = "";
  }   
  $this->depth--;  
}

function parse_characterData($parser, $data) {
  $this->data .= $data;  
}




// stuff ==============================================================
function get_insert_sql() {
$ret = array();

$keys = array('title', 'link', 'pubdate', 'description', 'content', 'guid', 'author');

$this->parsed = array();

$i = 0;
foreach ($this->items as $d) {$i++;
  $ar = array('tags'=> array());
  
  foreach ($d as $key=>$val) {
    
    $key_ar = explode(":", $key);
    $key = strtolower($key_ar[0]);        
    
    if ($key=='guid') {
      $ar['guid'] = $val;
    }
    $ar['ar'][$key] = $val;
    if (in_array($key, $keys)) {
      $this->parsed[$i][$key] = $val;
      $val = addslashes($val);
      $ar['tags'][] = "`$key`='{$val}'";
      
    }
  }
  $ar['sql'] = (count($ar['tags'])>0) ? implode(", \n\n", $ar['tags']) : "";      
  $ret[] = $ar;
}
return $ret;
}

function page_insert_sql() {
global $std;    

$ret = array();

$keys = array('title', 'link', 'pubdate', 'description', 'content', 'guid', 'author');

$fields = array('title', 'text', 'time', 'pubdate');


foreach ($this->items as $itemid => $d) {
  $ar = array('tags'=> array());
  $link = "";
  
  foreach ($d as $key=>$val) {        
    $key_ar = explode(":", $key);
    $ckey = strtolower($key_ar[0]); 
 
    switch ($ckey)  {
      case 'title':             
        $val = addslashes($std->textcut($val, 'cut', 'replace')); 
        $ar['title'] = $val;
      break;
      case 'link':     
        $link = addslashes($std->textcut($val, 'cut', 'replace'));            
      break;
      case 'full-text':
        $val = addslashes($std->textcut($val, 'cut', 'replace'));   
        if ($link!="") {
          $val.="<div class=textcopyright><a href='{$link}' target=_blank>{$link}</a></div>";
        }
        $ckey = "text";
        
      break;
      case 'pubdate':       
        $tar = array();
        $tar['time'] = $time = intval(strtotime($val));
        $tar['nf_pubdate'] = $val;
        $val = $tar['pubdate'] = date("Y-m-d H:i:s", $time);
        $ar['tags'][] = "`time`='{$time}'";
        
        $this->items[$itemid]['time'] = $tar;
      break;
      default:
        $ckey = "";
        $val = "";
    }
    
    if (in_array($ckey, $fields)) {
      $ar['tags'][] = "`{$ckey}`='".addslashes($val)."'";
    }
    
  }
  $ar['sql'] = (count($ar['tags'])>0) ? implode(", \n\n", $ar['tags']) : "";      
  $ret[] = $ar;
}

return $ret;
}  
  
}//endofclass 


?>