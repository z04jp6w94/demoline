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
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //取得固定參數
    $c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
    $FilePath = !empty($_COOKIE["FilePath"]) ? $_COOKIE["FilePath"] : NULL;
    $fileName = basename($FilePath, '.php');
    $dataKey = $_REQUEST["dataKey"];
    chkValueEmpty($dataKey);
    //來源判斷
    $url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
    $source_url = dirname($url) . '/' . $fileName . '_Modify.php';
    if ($_SERVER['HTTP_REFERER'] != $source_url) {
        BackToLoginPage();
        exit;
    } else {
//資料庫連線
        $mysqli = new DatabaseProcessorForWork();
//取得新增參數
        $lcsmc_type = $_REQUEST["lcsmc_type"];
        $lcsmc_content = $_REQUEST["lcsmc_content"];
        if ($lcsmc_type == "1" || $lcsmc_type == "2") {
            $lcsmc_content = str_replace(":", "", $lcsmc_content);
            $lcsmc_content = str_pad($lcsmc_content, 6, '0', STR_PAD_RIGHT);
        }

//定義時間參數
        $excuteDateTime = date("Y-m-d H:i:s"); //操作日期

        $sql = " UPDATE lcs_module_config ";
        $sql .= " SET lcsmc_content = ?, ";
        $sql .= " lcsmc_update_time = ? ";
        $sql .= " WHERE lcsmc_id = ? ";
        $sql .= " AND c_id = ? ";
        $mysqli->updatePreSTMT($sql, "ssss", array($lcsmc_content, $excuteDateTime, $dataKey, $c_id));
    }
}

//回原本畫面
header("Location:$fileName.php");
?>
