<?php

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('date.timezone', 'Asia/Taipei');
ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . '/assets_rear/session/');
session_start();
//函式庫
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/chiliman_config.php");
//判斷是否登入
if (!isset($_SESSION["user_id"])) {
    BackToLoginPage();
}
//取得固定參數
$webcode = isset($_REQUEST["webcode"]) ? $_REQUEST["webcode"] : "";
if ($webcode == '') {
    exit();
}
//資料庫連線
$mysqli = new DatabaseProcessorForWork();

//取得不重複密碼
function CallBackRandPass($mysqli) {
    $RandCode = GetRandNumberPass();
    $sql = " select count(*) from crm_m ";
    $sql .= " where c_id = ? ";
    $row_count = $mysqli->ReadValuePreSTMT($sql, "s", array($RandCode));
    if ($row_count == 0) {
        return $RandCode;
    } else {
        return CallBackRandPass($mysqli);
    }
}

//密碼
$temp_code = CallBackRandPass($mysqli);

echo trim($temp_code);
?>

