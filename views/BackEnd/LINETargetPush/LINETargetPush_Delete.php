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
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
//SESSION
$user_id = !empty($_SESSION["user_id"]) ? $_SESSION["user_id"] : NULL;
$user_name = !empty($_SESSION["user_name"]) ? $_SESSION["user_name"] : NULL;
$c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
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
//Picture
$pictureFile = new BackEndPictureFileForWork();
/* 圖檔 */
$sql = "SELECT p_id,p_img ";
$sql .= " FROM push_m ";
$sql .= " WHERE p_id = '" . $dataKey . "' ";
$sql .= " AND c_id = ? ";
$initAry = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);
/* 刪除檔案 原圖,縮圖 */
if ($initAry[0][1] != '') {
    $originalpath = $initAry[0][1];
    $thumbnailpath = str_replace("original", "thumbnail", $originalpath);
    $pictureFile->deleteFile($originalpath);
    $pictureFile->deleteFile($thumbnailpath);
}
/* 刪除 */
$sql = "Update push_m set deletestatus = 'Y' WHERE p_id = '" . $dataKey . "' ";
$mysqli->deleteSTMT($sql);
//回原本畫面
header("Location:$fileName.php");
?>
