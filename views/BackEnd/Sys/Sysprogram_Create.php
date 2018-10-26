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
$program_name = $_REQUEST["program_name"];
$program_path = $_REQUEST["program_path"];
$program_icon = $_REQUEST["program_icon"];
//定義時間參數
$excuteDate = date("Ymd"); //操作日期
$excuteTime = date("His"); //操作時間
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
$sql = "INSERT INTO sysprogram(program_name, program_path, program_icon) VALUES(?, ?, ?)";
$mysqli->createPreSTMT($sql, "sss", array($program_name, $program_path, $program_icon));
//回原本畫面
header("Location:$fileName.php");
?>
