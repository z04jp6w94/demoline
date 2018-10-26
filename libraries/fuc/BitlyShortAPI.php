<?php

class BitlyShortAPI {

    function __construct() {
        $this->access_token = BITLY_ACCESS_TOKEN;
    }

    function __destruct() {
        
    }

    //參數1: 網站根目錄下的完整資料夾或檔案路徑//存在回傳true, 失敗回傳false
    public function BitlyShort($longUrl, $domain = '') {
        $result = array();
        $url = "https://api-ssl.bit.ly/v3/shorten?access_token=" . $this->access_token . "&format=json&longUrl=" . urlencode($longUrl);
        if ($domain != '') {
            $url .= "&domain=" . $domain;
        }
        $output = json_decode($this->bitly_get_curl($url));
        if (isset($output->{'data'}->{'hash'})) {
            $result['url'] = $output->{'data'}->{'url'};
            $result['hash'] = $output->{'data'}->{'hash'};
            $result['global_hash'] = $output->{'data'}->{'global_hash'};
            $result['long_url'] = $output->{'data'}->{'long_url'};
            $result['new_hash'] = $output->{'data'}->{'new_hash'};
        }
        $jsonString = json_encode($result, true);
        $json = json_decode($jsonString, true);
        return $json['url'];
    }

    public function bitly_get_curl($uri) {
        $output = "";
        try {
            $ch = curl_init($uri);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 25);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $output = curl_exec($ch);
        } catch (Exception $e) {
            
        }
        return $output;
    }

}
