<?php

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('date.timezone', 'Asia/Taipei');
ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . '/assets_rear/session/');
session_start();
//函式庫
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/chiliman_config.php");
//Composer
require_once(AUTOLOAD_PATH);
//GET 
$httpRequestBody = file_get_contents('php://input');
//$String = '{"c_id":"12", "richmenu_id":"richmenuid-"}';
/* Json */
if (!isJSON($httpRequestBody)) {
    exit();
}
$json = json_decode($httpRequestBody);
$c_id = $json->{'c_id'}; // 客戶編號
$richmenu_id = $json->{'richmenu_id'};
/* DateTimeNow */
$DateTimeRoot = date("YmdHis");
//Erase the output buffer
ob_start();
header('Content-Length: ' . ob_get_length());
header('Connection: close');
ob_end_flush();
ob_flush();
flush();
//資料庫連線
$mysqli = new DatabaseProcessorForChiliman();
/* get token */
$sql = " select c_line_TOKEN from crm_m ";
$sql .= " where c_id = ? ";
$accessToken = $mysqli->readValuePreSTMT($sql, "s", array($c_id));
/* Menu */
$LineRichMenu = new BackEndLineRichMenuForChiliman($accessToken);
/* 人員名單 */
$sql = " SELECT DISTINCT(mlm_lineid), c_id FROM member_list_m ";
$sql .= " WHERE c_id = ? ";
$sql .= " AND mlm_source = '1' ";
$mlmAry = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);
for ($i = 0; $i < count($mlmAry); $i++) {
    $User = $mlmAry[$i][0];
    $UserStatus = $LineRichMenu->LinkMenuToUser($richmenu_id, $User);
    if (!$UserStatus) {
        error_log($c_id . "->" . $richmenu_id . "->" . $User . ": RichMenu FALSE!");
    }
}
?>