<?php
if (isset($_SERVER['HTTP_ORIGIN'])){
    $origin  = $_SERVER['HTTP_ORIGIN'];
} else {
    $origin = '';
}
$domain = str_replace(["https://", "http://"], "", $origin);


// if (in_array($domain, ORIGIN_ALLOW_ARR)){
//     header("Access-Control-Allow-Origin: $origin");
//     // header("Access-Control-Allow-Origin: *");
// } else {
//     if (isset($_SERVER['HTTP_ORIGIN_TOKEN']) && $_SERVER['HTTP_ORIGIN_TOKEN'] == 'e1799db603c5023e6a356484544bf2af'){
//         header("Access-Control-Allow-Origin: *");
//     }
//     // header("Access-Control-Allow-Origin: *");
//     // failure			
// }
// header("X-Frame-Options: SAMEORIGIN"); // 防止iframe hijack
// header('X-Content-Type-Options: nosniff'); // 防止 MIME
// header('X-Powered-By: ');
// header("Set-Cookie: key=value; path=/; domain=".MASTER_DOMAIN."; HttpOnly; SameSite=Lax");
//header("strict-transport-security: max-age=600")
date_default_timezone_set('Asia/Tokyo');
?>