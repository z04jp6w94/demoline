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
$fileName = DECCode($fileName);
$dataKey = DECCode($dataKey);
//定義時間參數
$excuteDate = date("Ymd"); //操作日期
$excuteTime = date("His"); //操作時間
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
//Picture
$pictureFile = new BackEndPictureFileForWork();
/* get token */
$sql = " select c_line_TOKEN from crm_m ";
$sql .= " where c_id = ? ";
$accessToken = $mysqli->readValuePreSTMT($sql, "s", array($c_id));
/* Menu */
$LineRichMenu = new BackEndLineRichMenuForWork($accessToken);
$DeleteRichMenu = $LineRichMenu->DeleteRichMenu($dataKey);
if ($DeleteRichMenu === true) {
    /* 圖檔 */
    $sql = "SELECT richmenu_id, rsm_img ";
    $sql .= " FROM richmenu_set_m ";
    $sql .= " WHERE richmenu_id = '" . $dataKey . "' ";
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
    $sql = "DELETE FROM richmenu_set_m WHERE richmenu_id = '" . $dataKey . "' ";
    $mysqli->deleteSTMT($sql);
    /* 刪除 */
    $sql = "DELETE FROM richmenu_set_t WHERE richmenu_id = '" . $dataKey . "' ";
    $mysqli->deleteSTMT($sql);
} else {
    error_log($c_id . "->" . $dataKey . "RichMenu DELETE FALSE!");
}

//回原本畫面
header("Location:$fileName.php");
?>
