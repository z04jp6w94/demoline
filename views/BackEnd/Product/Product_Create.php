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
$fileName = $_REQUEST["fileName"];
//取得新增參數
$p_number = trim($_REQUEST["p_number"]);
$p_name = $_REQUEST["p_name"];
$p_price = $_REQUEST["p_price"];
$p_remark = $_REQUEST["p_remark"];
//定義時間參數
$excuteDateTime = date("Y-m-d H:i:s"); //操作日期
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
$sql = "INSERT INTO product_m(p_number, p_name, p_price, p_remark) VALUES(?, ?, ?, ?)";
$mysqli->createPreSTMT($sql, "ssss", array($p_number, $p_name, $p_price, $p_remark));
//回原本畫面
header("Location:$fileName.php");
?>
