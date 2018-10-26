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
$dataKey = $_REQUEST["dataKey"];
//取得更新參數
$cbr_price = $_REQUEST["cbr_price"];
$cbr_date_range = $_REQUEST["cbr_date_range"];
$date_range = explode("-", $cbr_date_range);
$cbr_st_date = trim($date_range[0]);
$cbr_end_date = trim($date_range[1]);
//定義時間參數
$excuteDate = date("Y-m-d");
$excuteDateTime = date("Y-m-d H:i:s"); //操作日期
//更新
$mysqli = new DatabaseProcessorForWork();
$sql = "SELECT cbr_price, cbr_date ";
$sql .= " FROM crm_buy_records ";
$sql .= " WHERE cbr_id = ?";
$initAry = $mysqli->readArrayPreSTMT($sql, "s", array($dataKey), 2);

$sql = "UPDATE crm_buy_records SET cbr_price = ?, cbr_st_date = ?, cbr_end_date = ? WHERE cbr_id = ?";
$mysqli->updatePreSTMT($sql, "ssss", array($cbr_price, $cbr_st_date, $cbr_end_date, $dataKey));
$sql = "INSERT INTO crm_buy_records_log (cbr_id, cbr_price, cbrl_date, cbr_st_date, cbr_end_date, cbr_modify_date) VALUES(?, ?, ?, ?, ?, ?)";
$mysqli->createPreSTMT($sql, "ssssss", array($dataKey, $cbr_price, $initAry[0][1], $cbr_st_date, $cbr_end_date, $excuteDateTime));

//回原本畫面
header("Location:$fileName.php");
?>
