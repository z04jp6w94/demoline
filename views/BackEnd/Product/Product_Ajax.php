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
    exit;
}
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
//取得不重複密碼
$sql = " select (p_number)+1 from product_m ";
$sql .= " where 1 = 1 ";
$sql .= " order by p_number desc ";
$sql .= " limit 1 ";
$temp_code = $mysqli->ReadValueSTMT($sql);

$temp_number = str_pad($temp_code, 3, '0', STR_PAD_LEFT);

echo trim($temp_number);
?>

