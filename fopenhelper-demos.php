<?php
/*******************************************************************************
 ******                        fopenhelper - demos                        ******
 *******************************************************************************
 * Martin Mareš, http://profiles.google.com/mmrmartin
 * Writen in 2010.
 *******************************************************************************
 * Demos whith demonstrating using fopenhelper
 *******************************************************************************
 * “THE BEER-WARE LICENSE”:
 * Martin Mareš wrote this file (or after changes by someone else at least a 
 * part of this file). As long as you retain this notice you can do whatever 
 * you want with this stuff. If we meet some day, and you think this stuff is 
 * worth it, you can buy me a beer in return.
 */
require "fopenhelper.php";

function mainpage() {
  global $dom; //Lastet host
  global $cookies; //Saved cookies
  
  //Manual setted cookie
  $cok = new Cookie;
  $cok -> Dom = ".example.com";
  $cok -> Tit = "cookiesEnabled";
  $cok -> Val = "1";
  $cookies[]=$cok;
  
  //Opening the main page
  $opts = array(
  'http'=>array(
     'header'=>"Accept-language: en\r\n".
     "Cookie: ". getCookies($pg) ."\r\n"                
  )
  );
  if (!fopenhelper("http://www.example.com", $opts, $data, $dom)) {
    return "fopen doesn't work";
  }
  return true;
}

function login() {
  global $dom; //Lastet host
  $data = array (
                 'username' => 'user', 
		 'password' => 'pass'
		);
  $data = http_build_query($data);
  $opts = array (
   'http' => array (
            'method' => 'POST',
            'header'=> 
                "Accept-language: en\r\n" .
                "Cookie: ". getCookies("https://".$dom."/login") ."\r\n".
                "Content-type: application/x-www-form-urlencoded\r\n" .
                "Content-Length: " . strlen($data) . "\r\n",
            'content' => $data
            )
  );
  if (fopenhelper("https://".$dom."/login",$opts,$data,$dom)) {
    echo "<HR> Printout page contect after login:$data\n";
    return true;
  }
  else {
    return "fopen doesn't work";
  }
}

function logout() {
  global $dom; //Lastest host
  global $cookies; //Saved cookies
  $opts = array (
        'http' => array (
            'header'=> 
                "Accept-language: en\r\n" .
                "Cookie: ". getCookies("https://".$dom."/logout") ."\r\n"
            )
        );
  fopenhelper("https://".$dom."/logout",$opts, $dom );
  $cookies=null;
  fclose($stream);
}

mainpage();
login();
logout();
?>