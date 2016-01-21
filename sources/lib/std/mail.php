<?php

class std_mail {

var $from_name = "";
var $from_address = "";

/**
 * Use google server, ONLY for windows servers!
 *
 * @var boolean
 */
var $google_smtp = 0;  
     
var $charset_code = "koi";   // win | iso | koi
var $content_type = "text";  // text | html

var $debug = 0;


function send($to, $subject, $body) {
  
  $eol="\n";
  $mime_boundary = md5(time());
  
  switch ($this->charset_code) {
    case 'iso':
      $convert_key = "i";
      $charset = "iso-8859-5";
    break;
    case 'koi':
      $convert_key = "k";
      $charset = "koi8-r";
    break;
    case 'win': default:
      $convert_key = "w";
      $charset = "cp1251"; 
    break;
  }
  $content = ($this->content_type=='html') ? "text/html" : "text/plain";  
  
  $headers = "";    
  $fromaddress = $this->from_address;
  $fromname = $this->from_name;
  
  
  // Common Headers
 
  if ($fromaddress!='' && $fromname!='') {
    //$headers .= "Message-ID: <".time()."-".$fromaddress.">".$eol;
    $headers .= "From: ".$fromname." <".$fromaddress.">".$eol;
    $headers .= "Reply-To: ".$fromname." <".$fromaddress.">".$eol;
    $headers .= "Return-Path: ".$fromname." <".$fromaddress.">".$eol;  
  
  }
  elseif($fromaddress!='') {
    //$headers .= "Message-ID: <".time()."-".$fromaddress.">".$eol;
    $headers .= "From: ".$fromaddress."".$eol;
    $headers .= "Reply-To: ".$fromaddress."".$eol;
    $headers .= "Return-Path: ".$fromaddress."".$eol;    
  
  }
  $headers .= "MIME-Version: 1.0".$eol; 
  $headers .= "Content-Type: {$content}; charset={$charset}".$eol;
  $headers .= "X-Mailer: PHP v".phpversion().$eol; 
  
 
  $headers .= "\n"; // gmail fix
  
  $msg = $body;
  
  // if not Windows-1251
  if ($this->charset_code != "win") {    
    if ($this->charset_code=='koi') {
      $subject = '=?koi8-r?B?'.base64_encode(convert_cyr_string($subject, "w", "k")).'?=';
    }
    else {
      $subject = convert_cyr_string($subject, "w", $convert_key);      
    }
    $headers = convert_cyr_string($headers, "w", $convert_key);
    $msg = convert_cyr_string($msg, "w", $convert_key);
  }
  else {
    $subject = '=?koi8-r?B?'.base64_encode(convert_cyr_string($subject, "w", "k")).'?=';
  }

  if ($this->google_smtp) { // onyl windows servers
    ini_set("SMTP", "aspmx.l.google.com");
  }
  
  if ($fromaddress!='') {
    ini_set("sendmail_from", $fromaddress);  // the INI lines are to force the From Address to be used !    
    $mail_sent = ($this->debug) ? mail($to, $subject, $msg, $headers) : @mail($to, $subject, $msg, $headers);
    ini_restore("sendmail_from");
  }
  else {
    $mail_sent = ($this->debug) ? mail($to, $subject, $msg, $headers) : @mail($to, $subject, $msg, $headers);
  }
  
  return $mail_sent;
}

/**
 * @deprecated 
 *
 * @param unknown_type $to
 * @param unknown_type $body
 * @param unknown_type $subject
 * @return unknown
 */
function from_default($to, $body, $subject) {  
  return $this->mail($to, $body, $subject, $this->from_address, $this->from_name);   
}

/**
 * Standart mail 
 * @deprecated 
 */
function mail($to, $body, $subject, $fromaddress='', $fromname='') {
  
  $eol="\r\n";
  $mime_boundary = md5(time());
  $headers = "";
    
  // Common Headers
  if ($fromaddress!='' && $fromname!='') {
    $headers .= "From: ".$fromname." <".$fromaddress.">".$eol;
    $headers .= "Reply-To: ".$fromname." <".$fromaddress.">".$eol;
    $headers .= "Return-Path: ".$fromname." <".$fromaddress.">".$eol;    // these two to set reply address
   }
  $headers .= "Content-Type: text/plain; charset=windows-1251".$eol;
  //$headers .= "Message-ID: <".time()."-".$fromaddress.">".$eol;
  //$headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters

  
  $msg = $body;
  

  //ini_set("SMTP", "aspmx.l.google.com");
  
  if ($fromaddress!='') {
    ini_set("sendmail_from",$fromaddress);  // the INI lines are to force the From Address to be used !
    $mail_sent = mail($to, $subject, $msg, $headers);
    ini_restore("sendmail_from");
  }
  else {
    $mail_sent = mail($to, $subject, $msg, $headers);
  }
  
  return $mail_sent;
}

//eoc  
}


?>