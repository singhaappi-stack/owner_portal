<?php
require_once 'abstract.php';
class routing extends abstractRouting {

    private string $url = "https://ihr-mail.sakura.ne.jp/appi_portal/api/owner/";

    function get($submode){
        $params = $_REQUEST;
        $params['owner_str'] = $_SESSION['owner']['login_str'];

        $url = $this->url . "$submode?" . http_build_query($params);
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($response, true);
        new apiReturn(200, 200, $res);
    }

    function post($submode){
        $params = $_REQUEST;
        $params['owner_str'] = $_SESSION['owner']['login_str'];
        $ch = curl_init($this->url . "$submode");

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($response, true);
        new apiReturn(200, 200, $res);
    }

}