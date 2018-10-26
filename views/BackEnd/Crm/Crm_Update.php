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
//取得更新參數
$c_status = $_REQUEST["c_status"];
$c_name = $_REQUEST["c_name"];
$c_tel = $_REQUEST["c_tel"];
$c_address = $_REQUEST["c_address"];
$c_mail = $_REQUEST["c_mail"];
$c_remark = $_REQUEST["c_remark"];
$c_line_name = $_REQUEST["c_line_name"];
$c_line_OAID = $_REQUEST["c_line_OAID"];
$c_line_CID = $_REQUEST["c_line_CID"];
$c_line_SECRET = $_REQUEST["c_line_SECRET"];
$c_line_TOKEN = $_REQUEST["c_line_TOKEN"];
$c_linelogin_CID = $_REQUEST["c_linelogin_CID"];
$c_linelogin_SECRET = $_REQUEST["c_linelogin_SECRET"];
$c_fb_appid = $_REQUEST["c_fb_appid"];
$c_fb_secret = $_REQUEST["c_fb_secret"];
$c_fb_token = $_REQUEST["c_fb_token"];
$c_fb_patch = $_REQUEST["c_fb_patch"];
$c_fb_fans = $_REQUEST["c_fb_fans"];
//定義時間參數
$excuteDateTime = date("Y-m-d H:i:s"); //操作日期
//更新
$mysqli = new DatabaseProcessorForWork();
$sql = " UPDATE crm_m SET ";
$sql .= " c_name = ?, c_tel = ?, c_address = ?, c_mail = ?, c_status = ?, ";
$sql .= " c_remark = ?, c_line_OAID = ?, c_line_CID = ?, c_line_SECRET = ?, c_line_TOKEN = ?,  ";
$sql .= " c_linelogin_CID = ?, c_linelogin_SECRET = ?, c_line_name = ?, c_fb_appid = ?, c_fb_secret = ?, ";
$sql .= " c_fb_token = ?, c_fb_patch = ?, c_fb_fans = ? ";
$sql .= " WHERE c_id = ? ";
$mysqli->updatePreSTMT($sql, "sssssssssssssssssss", array($c_name, $c_tel, $c_address, $c_mail, $c_status, $c_remark, $c_line_OAID, $c_line_CID, $c_line_SECRET, $c_line_TOKEN, $c_linelogin_CID, $c_linelogin_SECRET, $c_line_name, $c_fb_appid, $c_fb_secret, $c_fb_token, $c_fb_patch, c_fb_fans, $dataKey));
//使用者權限
if ($c_status == "N") {
    $sql = " UPDATE sysuser SET ";
    $sql .= " user_status = 'N' ";
    $sql .= " WHERE c_id = ? ";
    $mysqli->updatePreSTMT($sql, "s", array($dataKey));
}
//回原本畫面
header("Location:$fileName.php");
?>
