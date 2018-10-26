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
$dataKey = $_REQUEST["dataKey"];
//Turn
$dataKey = DECCode($dataKey);
//定義時間參數
$excuteDate = date("Ymd"); //操作日期
$excuteTime = date("His"); //操作時間
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
//Picture
$pictureFile = new BackEndPictureFileForWork();
/* 圖檔 */
$sql = "SELECT sa_id,sa_awards_img ";
$sql .= " FROM share_activity ";
$sql .= " WHERE sa_id = ? ";
$sql .= " AND c_id = ? ";
$initAry = $mysqli->readArrayPreSTMT($sql, "ss", array($dataKey, $c_id), 2);
/* 刪除檔案 原圖,縮圖 */
if ($initAry[0][1] != '') {
    $originalpath = $initAry[0][1];
    $thumbnailpath = str_replace("original", "thumbnail", $originalpath);
    $pictureFile->deleteFile($originalpath);
    $pictureFile->deleteFile($thumbnailpath);
}
/* 刪除 */
$sql = "Update share_activity set deletestatus = 'Y' WHERE sa_id = ? AND c_id = ? ";
$mysqli->deletePreSTMT($sql, "ss", array($dataKey, $c_id));

//回原本畫面
header("Location:$fileName.php");
?>
