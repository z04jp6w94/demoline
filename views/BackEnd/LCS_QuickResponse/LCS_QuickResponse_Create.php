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
/* DateNow */
$_RandRoot = date("YmdHis");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //取得固定參數
    $c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
    $FilePath = !empty($_COOKIE["FilePath"]) ? $_COOKIE["FilePath"] : NULL;
    $fileName = basename($FilePath, '.php');
    //來源判斷
    $url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
    $source_url = dirname($url) . '/' . $fileName . '_Add.php';
    if ($_SERVER['HTTP_REFERER'] != $source_url) {
        BackToLoginPage();
        exit;
    } else {
//資料庫連線
        $mysqli = new DatabaseProcessorForWork();
//取得新增參數
        $lcsmc_content = $_REQUEST["lcsmc_content"];
//定義時間參數
        $excuteDateTime = date("Y-m-d H:i:s"); //操作日期
        /* get token */
        $sql = " SELECT lcsm_id FROM lcs_module ";
        $sql .= " WHERE c_id = ? ";
        $lcsm_id = $mysqli->readValuePreSTMT($sql, "s", array($c_id));

        $sql = "INSERT INTO lcs_module_config ";
        $sql .= "( lcsm_id, c_id, lcsmc_community_type, lcsmc_key, lcsmc_command, ";
        $sql .= " lcsmc_content, lcsmc_create_time, lcsmc_update_time ) ";
        $sql .= " VALUES ";
        $sql .= " (?, ?, 'LINE', 'QUICK_RESPONSE', 'TEXT', ";
        $sql .= " ?, ?, ? )";
        $mysqli->createPreSTMT($sql, "sssss", array($lcsm_id, $c_id, $lcsmc_content, $excuteDateTime, $excuteDateTime));
    }
} else {
    BackToLoginPage();
    exit;
}

//回原本畫面
header("Location:$fileName.php");
?>
