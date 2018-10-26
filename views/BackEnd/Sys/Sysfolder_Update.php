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
$group_id = $_REQUEST["group_id"];
$menu_id = $_REQUEST["menu_id"];
$menu_icon = $_REQUEST["menu_icon"];
//取得更新參數
$group_id = $_REQUEST["group_id"];
$menu_folder = $_REQUEST["menu_folder"];
//定義時間參數
$excuteDate = date("Ymd"); //操作日期
$excuteTime = date("His"); //操作時間
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
$sql = "UPDATE sysmenu SET menu_folder = ?, menu_icon = ? WHERE menu_id = ? AND group_id = ? ";
$mysqli->updatePreSTMT($sql, "ssss", array($menu_folder, $menu_icon, $menu_id, $group_id));
//回原本畫面
header("Location:Sysmenu.php");
?>
