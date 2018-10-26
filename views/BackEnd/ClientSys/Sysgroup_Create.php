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
//SESSION
$c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
//取得固定參數
$fileName = $_REQUEST["fileName"];
/* 解密 */
$fileName = DECCode($fileName);
chkSourceFileName($fileName, 'Sysgroup');
//取得新增參數
$group_name = $_REQUEST["group_name"];
//定義時間參數
$excuteDate = date("Ymd"); //操作日期
$excuteTime = date("His"); //操作時間
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//資料庫連線
    $mysqli = new DatabaseProcessorForWork();
    $sql = "INSERT INTO sysgroup(group_name, c_id) VALUES(?, ?)";
    $mysqli->createPreSTMT($sql, "ss", array($group_name, $c_id));
}
//回原本畫面
header("Location:$fileName.php");
?>
