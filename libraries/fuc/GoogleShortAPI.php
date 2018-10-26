<?php

class GoogleShortAPI {

    function __construct() {
        $this->apikey = GOOGLE_SHORT_API;
    }

    function __destruct() {
        
    }

    //參數1: 網站根目錄下的完整資料夾或檔案路徑//存在回傳true, 失敗回傳false
    public function GoogleShort($Url) {
        $postData = array('longUrl' => $Url, 'key' => $this->apikey);
        $jsonData = json_encode($postData);
        $curlObj = curl_init();
        curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?key=' . $this->apikey);
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlObj, CURLOPT_HEADER, 0);
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
        curl_setopt($curlObj, CURLOPT_POST, 1);
        curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($curlObj);

        //change the response json string to object
        $json = json_decode($response);
        curl_close($curlObj);

        return $json->id;
    }

    public function GoogleQrCode($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://chart.apis.google.com/chart");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "chs=300x300&cht=qr&choe=UTF-8&chl=" . urlencode($url));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $img = curl_exec($ch);
        curl_close($ch);
        return $img;
    }

}
