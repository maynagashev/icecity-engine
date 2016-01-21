<?php

class std_dl {
  
  
function s_connect($url)   {
    
  $err = false;
  $errm = array();
  
  $purl = parse_url($url);
  
  error_reporting(E_ALL);
  
  $errm[] = "TCP/IP Connection";  

  $service_port = getservbyname('www', 'tcp');  

  $address = gethostbyname($purl['host']);
 
  $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
  if ($socket === false) {
    $err = true;
    $errm[] = "socket_create() failed: reason: " . socket_strerror(socket_last_error());
  } else {
    $errm[] = "OK.";
  }
  
  $errm[] = "Attempting to connect to '$address' on port '$service_port'...";
  $result = socket_connect($socket, $address, $service_port);
  if ($result === false) {
      $errm[] = "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket));
  } else {
      $errm[] = "OK.";
  }
  
  t($purl);
  $in = "HEAD / HTTP/1.0\r\n";
  $in .= "Host: {$purl['host']}\r\n";
  $in .= "Connection: Close\r\n\r\n";
  //$in .= "GET {$purl['path']} HTTP/1.0\r\n";

  $errm[] = "Sending HTTP HEAD request... {$purl['host']}";
  socket_write($socket, $in, strlen($in));
  $errm[] = "OK.";
  
  $errm[] = "Reading response:";
  
  $res = "";
  $lim = 4; $i=0;
  echo "<pre>";
  while ($out = socket_read($socket, 2048)) { $i++;
      echo $out;
      $res .= $out;
      if ($i>$lim) break;
  }
  echo "</pre>";
  $errm[] = "<pre>{$res}</pre>";
  
  $errm[] = "Closing socket...";
  socket_close($socket);
  $errm[] = "OK.\n\n";

  return array('err'=> $err, 'errm' => $errm);
}
  
}


?>