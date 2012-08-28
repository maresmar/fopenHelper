<?php
/*******************************************************************************
 ******                            fopenhelper                            ******
 *******************************************************************************
 * Martin Mareš, http://profiles.google.com/mmrmartin
 * Writen in 2010.
 *******************************************************************************
 * This class helps browing web throught http handling cookies and redirections.
 *******************************************************************************
 * “THE BEER-WARE LICENSE”:
 * Martin Mareš wrote this file (or after changes by someone else at least a 
 * part of this file). As long as you retain this notice you can do whatever 
 * you want with this stuff. If we meet some day, and you think this stuff is 
 * worth it, you can buy me a beer in return.  
 */
class Cookie {
    var $Dom;
    var $Tit;
    var $Val;
}

function findCookie($host,$header) {
    //Get data from headers
    if (preg_match('~Set-Cookie: ([^=]+)([^;]+)~i', $header, $matches)) {
        $tit = $matches[1];       
        $val = $matches[2];
    }
    if (preg_match('~Domain=([^;]+)~i', $header, $matches)) {
        $host = $matches[1];
    }
    //Save cookie    
    saveCookie($host, $tit, $val);
}

function saveCookie($host, $tit, $val) {
    global $cookies; //Saved cookies
    
    //Find already saved cookie with same title and host
    foreach ($cookies as &$cookie) {
        if(($cookie->Dom===$host)&&($cookie->Tit===$tit)) {
            $cookie->Val = val;
            return;
        }
    }
    
    //Cookie is not alredy saved, so I save it
    $cok = new Cookie;
    $cok -> Dom = $host;
    $cok -> Tit = $tit;
    $cok -> Val = $val;
    $cookies[]=$cok;
}

function deleteAllCookies() {
    global $cookies;
    $cookies = null;
}

//Gets cookies for specific host
function getCookies($host) {
    //I will use alredy saved cookies
    global $cookies; 

    $result=array();
    foreach ($cookies as $cookie) {
        if (strpos($host,$cookie->Dom)!==FALSE) {
            $result[]="".$cookie->Tit.$cookie->Val; 
        }
    }
    return implode("; ", $result);
}

function fopenhelper($url, $opts, &$out_put_data=null, &$domain_name=null) {
    
    //Find domain name
    preg_match('~(https?://?)([^/]+)~i', $url, $matches); 
    $domain_name=$matches[2];
    
    //Create context  
    $context = stream_context_create($opts);
    
    //fopening
    if ($stream = fopen($url,"r",false, $context )) {    
        $meta_data = stream_get_meta_data($stream);
        foreach ($meta_data["wrapper_data"] as $val) {
            //Handlig cookies
            if (strpos($val,"Set-Cookie: ")===0) {
                findCookie($domain_name,$val);
            } 
            else {
                //Handling redirections
                if (preg_match('~Location: (https?://?)([^/]+)~i', $val, $matches)) {
                    $domain_name=$matches[2];
                } 
            }
        }
        
        //Output data
        $out_put_data=stream_get_contents($stream);
        
        fclose($stream);
        return true;
    } 
    else {
        return false; //There is something wrong with fopen
    }
}

/*
 * You can find out more posibilities, like 
 * - uploading/sending file http://php.net/manual/en/function.stream-context-create.php#90411
 * - basic autentization http://www.php.net/manual/en/function.stream-context-create.php#72317
 * - and much more (twitter status update etc...)
 */

?>