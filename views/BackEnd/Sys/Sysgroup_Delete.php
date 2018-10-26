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
//COOKIE
$FilePath = !empty($_COOKIE["FilePath"]) ? $_COOKIE["FilePath"] : NULL;
//CheckUrl
$program_ary = chkUserSecurity($mysqli, $user_id, $FilePath);
$program_id = !empty($_COOKIE["program_id"]) ? $_COOKIE["program_id"] : NULL;
$program_name = !empty($_COOKIE["program_name"]) ? $_COOKIE["program_name"] : NULL;
$fileName = basename($FilePath, '.php');
//取得接收參數
$dataKey = !empty($_REQUEST["dataKey"]) ? $_REQUEST["dataKey"] : "";
chkValueEmpty($dataKey);
//定義時間參數
$excuteDate = date("Ymd"); //操作日期
$excuteTime = date("His"); //操作時間
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
$sql = "SELECT count(user_id) FROM sysuser WHERE group_id = '" . $dataKey . "'";
$rsVal = $mysqli->readValueSTMT($sql);
if ($rsVal > 0) {
    echo '<script>';
    echo 'alert("請先刪除此系統群組對應的系統使用者資料，避免造成程式錯誤");';
    echo 'window.history.back();';
    echo '</script>';
    exit();
}
$sql = "DELETE FROM sysauthority WHERE group_id = '" . $dataKey . "'";
$mysqli->deleteSTMT($sql);
$sql = "DELETE FROM sysmenu WHERE group_id = '" . $dataKey . "'";
$mysqli->deleteSTMT($sql);
$sql = "DELETE FROM sysgroup WHERE group_id = '" . $dataKey . "'";
$mysqli->deleteSTMT($sql);
//回原本畫面
header("Location:$fileName.php");
?>
