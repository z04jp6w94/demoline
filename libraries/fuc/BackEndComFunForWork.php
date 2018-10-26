<?php

//    Function Index
//    日期
//        DspDate                  : 將日期字串YYYYMMDD轉換成YYYY/MM/DD / 輸出：ex:YYYY/MM/DD
//        DspTime                  : 將時間字串HHMMSS轉換成HH:MM:SS
//	  AddDate		   : 增加日期 
//    文字
//        GetTitle                 : 指定要擷取的最少字數, 低於最少字數會以最少字數抓取
//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
//    功能簡述    : 將日期字串YYYYMMDD轉換成YYYY/MM/DD / 輸出：ex:YYYY/MM/DD
//    撰寫日期    : 20131125
//    撰寫人員    : JimmyChao 整理
//    參數說明    : MyDateStr        / String    / 日期
//                : SymbolStr        / String    / 符號樣式例如 : / 或 - (預設值為 / )
//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
function DspDate($MyDateStr, $MyTypeStr) {
    switch (strlen($MyDateStr)) {
        case '6':
        case '7':
        case '8':
            return substr($MyDateStr, 0, 4) . $MyTypeStr . substr($MyDateStr, 4, 2) . $MyTypeStr . substr($MyDateStr, 6, 2);
            break;
    }
}

//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
//    功能簡述    : 將時間字串HHmmSS轉換成HH:mm:SS / 輸出：ex:HH:MM:SS
//    撰寫日期    : 20131125
//    撰寫人員    : JimmyChao 整理
//    參數說明    : MyTimeStr        / String    / 時間
//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
function DspTime($MyTimeStr, $MyTypeStr) {
    switch (strlen($MyTimeStr)) {
        case '4':
        case '6':
            return substr($MyTimeStr, 0, 2) . $MyTypeStr . substr($MyTimeStr, 2, 2) . $MyTypeStr . substr($MyTimeStr, 4, 2);
            break;
    }
}

//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
//    功能簡述    : 將時間字串HHmmSS轉換成HH:mm:SS / 輸出：ex:HH:MM:SS
//    撰寫日期    : 20131125
//    撰寫人員    : JimmyChao 整理
//    參數說明    : MyTimeStr        / String    / 時間
//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
function AddDate($Date, $AddDate) {
    if (empty($Date) || empty($AddDate)) {
        return "";
    } else {
        return date('Ymd', strtotime($Date . ' + ' . $AddDate . ' days'));
    }
}

//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
//    功能簡述    : 指定要擷取的最少字數, 低於最少字數會以最少字數抓取
//    撰寫日期    : 20131125
//    撰寫人員    : JimmyChao 整理
//    參數說明    : pStr                / String    / 原始字串
//                : minChars            / long        / 最低字數
//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
function GetTitle($pStr, $minChars) {
    if (strlen($pStr) <= $minChars) {
        return $pStr;
    } else {
        return iconv_substr($pStr, 0, $minChars, 'utf-8') . '...'; //substr($pStr , 0 , $minChars);
    }
}

//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
//    功能簡述    : 建立資料夾
//    撰寫日期    : 180530
//    撰寫人員    : T 整理
//    參數說明    : folderPath         / String    / 路徑
//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
function CREATE_FOLDER($folderPath) {
    if (file_exists($folderPath)) {
        return FALSE;
    } else {
        mkdir($folderPath);
        return TRUE;
    }
}

//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
//    功能簡述    : 加密
//    撰寫日期    : 20170712
//    撰寫人員    : T 整理
//    參數說明    : Str    /    String    /    
//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
function ENCCode($Str) {
    $secretKey = pack('H*', SECRETKEY);
    $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_RAND);
    $encrypted = trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $secretKey, $Str, MCRYPT_MODE_ECB, $iv)));
    return trim($encrypted);
}

//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
//    功能簡述    : 解密
//    撰寫日期    : 20170712
//    撰寫人員    : T 整理
//    參數說明    : Str    /    String    /    
//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
function DECCode($Str) {
    $secretKey = pack('H*', SECRETKEY);
    $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_RAND);
    $decoded = base64_decode($Str);
    $decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $secretKey, $decoded, MCRYPT_MODE_ECB, $iv));
    return trim($decrypted);
}

//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
//    功能簡述    : Random 密碼(3)
//    撰寫日期    : 20170712
//    撰寫人員    : T 整理
//    參數說明    :    /        /    
//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
function GetRandNum() {
    $password_len = 3;
    $password = '';
    $fNumber = microtime();
    $_num = substr(strrchr($fNumber, "."), 1);
    $word = str_replace(" ", "", $_num);
    $len = strlen($word);
    for ($i = 0; $i < $password_len; $i++) {
        $password .= $word[rand() % $len];
    }
    return $password;
}

//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
//    功能簡述    : 取得英數字字串 
//    撰寫日期    : 20170930
//    撰寫人員    : T 整理
//    參數說明    : length   /        /    
//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
function GetRandPass($length) {
    $password_len = $length;
    $password = '';
    $word = 'abcdefghijkmnpqrstuvwxyz0123456789';
    $len = strlen($word);
    for ($i = 0; $i < $password_len; $i++) {
        $password .= $word[rand() % $len];
    }
    return $password;
}

//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
//    功能簡述    : 取得英數字字串 
//    撰寫日期    : 20170930
//    撰寫人員    : T 整理
//    參數說明    : length   /        /    
//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
function GetRandNumberPass() {
    $password_len = 10;
    $password = '';
    $word = '0123456789';
    $len = strlen($word);
    for ($i = 0; $i < $password_len; $i++) {
        $password .= $word[rand() % $len];
    }
    return $password;
}

//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
//	功能簡述	: replace
//	撰寫日期	: 20171102
//	撰寫人員	: Tai 整理
//	參數說明	: str			/ String	/ replace 文字
//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
function TextToString($str) {
    $str = nl2br($str);
    return $str;
}

//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
//	功能簡述	: 移除表情符號
//	撰寫日期	: 20171102
//	撰寫人員	: Tai 整理
//	參數說明	: text			/ String	/ 表情符號
//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
function removeEmoji($text) {
    $cleanText = "";

    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $cleanText = preg_replace($regexEmoticons, '', $text);

    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $cleanText = preg_replace($regexSymbols, '', $cleanText);

    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
    $cleanText = preg_replace($regexTransport, '', $cleanText);

    return $cleanText;
}

//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
//	功能簡述	: 判斷字串是否為Json
//	撰寫日期	: 20180601
//	撰寫人員	: Tai 整理
//	參數說明	: string        / String	/ JSON 字串
//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''

function isJSON($string) {
    return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
}