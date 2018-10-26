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
/* 現在人員 */
$user_id = !empty($_SESSION["user_id"]) ? $_SESSION["user_id"] : NULL;
$user_name = !empty($_SESSION["user_name"]) ? $_SESSION["user_name"] : NULL;
$c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //取得固定參數
    $fileName = $_REQUEST["fileName"];
    //來源判斷
    $url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
    $source_url = dirname($url) . '/' . $fileName . '.php';
    if ($_SERVER['HTTP_REFERER'] != $source_url) {
        header("Location:https://" . $_SERVER['HTTP_HOST'] . "/views/BackEnd/Login/Member_Logout_Action.php");
        exit;
    }
//取得更新參數
    $email = $_REQUEST["email"];
    $password = $_REQUEST["password"];
    $encode_user_password = ENCCode($password);
//定義時間參數
    $excuteDateTime = date("Y-m-d H:i:s");
//資料庫連線
    $mysqli = new DatabaseProcessorForWork();
    /* update */
    $sql = "UPDATE sysuser SET user_email = ?, user_password = ? WHERE user_id = ?";
    $mysqli->updatePreSTMT($sql, "sss", array($email, $encode_user_password, $user_id));
}
//回原本畫面
header("Location:$fileName.php");
?>
