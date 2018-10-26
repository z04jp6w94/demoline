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
$c_id = isset($_REQUEST["c_id"]) ? $_REQUEST["c_id"] : "";
$sa_id = isset($_REQUEST["sa_id"]) ? $_REQUEST["sa_id"] : "";
$lineid = isset($_REQUEST["lineid"]) ? $_REQUEST["lineid"] : "";
$status = isset($_REQUEST["status"]) ? $_REQUEST["status"] : "";
if ($c_id == '' || $sa_id == '' || $lineid == '' || $status == '') {
    exit();
}
$lineid = DECCode($lineid);
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
if ($status == "Y") {
    $sql = " Update share_exchange_record SET ser_status = 'N' ";
    $sql .= " WHERE sa_id = ? ";
    $sql .= " AND c_id = ?  ";
    $sql .= " AND mlm_lineid = ? ";
    $mysqli->updatePreSTMT($sql, "sss", array($sa_id, $c_id, $lineid));
} else {
    $sql = " Update share_exchange_record SET ser_status = 'Y' ";
    $sql .= " WHERE sa_id = ? ";
    $sql .= " AND c_id = ?  ";
    $sql .= " AND mlm_lineid = ? ";
    $mysqli->updatePreSTMT($sql, "sss", array($sa_id, $c_id, $lineid));
}

exit();
?>

