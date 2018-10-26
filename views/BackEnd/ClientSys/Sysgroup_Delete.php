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
/* Catch 5 */
$fileName = DECCode($fileName);
$dataKey = DECCode($dataKey);
//定義時間參數
$excuteDate = date("Ymd"); //操作日期
$excuteTime = date("His"); //操作時間
/* Method */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //來源判斷
    $url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
    $source_url = dirname($url) . '/' . $fileName . '.php';
    if ($_SERVER['HTTP_REFERER'] != $source_url) {
        BackToLoginPage();
        exit;
    }
    $c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
//資料庫連線
    $mysqli = new DatabaseProcessorForWork();
    $sql = "SELECT count(user_id) FROM sysuser WHERE and c_id = '" . $c_id . "' and group_id = '" . $dataKey . "'";
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
}
//回原本畫面
header("Location:$fileName.php");
?>
