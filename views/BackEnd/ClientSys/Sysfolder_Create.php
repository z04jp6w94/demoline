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
$fileName = DECCode($fileName);
//取得新增參數
$group_id = $_REQUEST["group_id"];
$group_id = DECCode($group_id);
$menu_folder = $_REQUEST["menu_folder"];
$menu_icon = $_REQUEST["menu_icon"];
//定義時間參數
$excuteDate = date("Ymd"); //操作日期
$excuteTime = date("His"); //操作時間
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
//資料夾排序
$sql = " select max(menu_order)+1 from sysmenu ";
$sql .= " where group_id = ? ";
$sql .= " and menu_type = 'P' ";
$menu_order = $mysqli->readValuePreSTMT($sql, "s", array($group_id));
//建立資料夾
$sql = "INSERT INTO sysmenu(group_id, program_id, menu_type, menu_folder, menu_prev_id, menu_order, menu_icon) VALUES(?, '0', 'P', ?, '0', ?, ?)";
$mysqli->createPreSTMT($sql, "ssss", array($group_id, $menu_folder, $menu_order, $menu_icon));
//回原本畫面
header("Location:Sysmenu.php");
?>
