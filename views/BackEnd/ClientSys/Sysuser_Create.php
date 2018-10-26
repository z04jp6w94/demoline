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
$c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
$fileName = $_REQUEST["fileName"];
/* 解密 */
$fileName = DECCode($fileName);
chkSourceFileName($fileName, 'Sysuser');
//取得新增參數
$group_id = $_REQUEST["group_id"];
$user_account = $_REQUEST["user_account"];
$user_password = $_REQUEST["user_password"];
$encode_user_password = ENCCode($user_password);
$user_name = $_REQUEST["user_name"];
$user_address = $_REQUEST["user_address"];
$user_email = $_REQUEST["user_email"];
$user_phone = $_REQUEST["user_phone"];
$user_status = $_REQUEST["user_status"];
//定義時間參數
$excuteDate = date("Ymd"); //操作日期
$excuteTime = date("His"); //操作時間
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//資料庫連線
    $mysqli = new DatabaseProcessorForWork();
    $error = new BackEndErrorLogForWork($mysqli, $fileName);
    /* 來源判斷 */
    $url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
    $source_url = dirname($url) . '/' . $fileName . '_Add.php';
    if ($_SERVER['HTTP_REFERER'] != $source_url) {
        $error->InsertErrorLogToDatabase($c_id, "客戶編號:" . $c_id . " 新增使用者->POST來源錯誤");
        BackToLoginPage();
        exit;
    }
    $sql = " Select count(group_id) from sysgroup ";
    $sql .= " where c_id = ? ";
    $sql .= " and group_id = ? ";
    $value = $mysqli->readValuePreSTMT($sql, "ss", array($c_id, $group_id));
    if ($value != '1') {
        $error->InsertErrorLogToDatabase($c_id, "客戶編號:" . $c_id . " 新增使用者->群組編號錯誤");
        BackToLoginPage();
        exit();
    }
    $sql = " Select count(user_id) from sysuser";
    $sql .= " Where user_account = '" . $user_account . "' ";
    $sql .= " And c_id = '" . $c_id . "' ";
    $account = $mysqli->readValueSTMT($sql);
    if ($account >= 1) {
        echo "<script>";
        echo "alert('帳號已經被建立,請重新輸入！');";
        echo "window.location.replace('" . $fileName . "_Add.php'); ";
        echo "</script>";
        exit();
    }
    $sql = "INSERT INTO sysuser(group_id, user_account, user_password, user_name, user_address, user_email, user_phone, user_status, c_id, user_level) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, 'normal' )";
    $mysqli->createPreSTMT($sql, "sssssssss", array($group_id, $user_account, $encode_user_password, $user_name, $user_address, $user_email, $user_phone, $user_status, $c_id));
}
//回原本畫面
header("Location:$fileName.php");
?>
