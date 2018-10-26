<?php

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('date.timezone', 'Asia/Taipei');
ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . '/assets_rear/session/');
session_start();
//函式庫
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/chiliman_config.php");
/* CDN S3 */
require_once(AUTOLOAD_PATH);
require LIBRARY_ROOT_PATH . '/S3/s3.php';
//CDN
$s3 = new s3(CDN_REGION, CDN_VERSION, CDN_PATH, CDN_PROFILE, CDN_BUCKET);
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
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //來源判斷
    $url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
    $source_url = dirname($url) . '/' . $fileName . '.php';
    if ($_SERVER['HTTP_REFERER'] != $source_url) {
        BackToLoginPage();
        exit;
    }
//資料庫連線
    $mysqli = new DatabaseProcessorForWork();
//Picture
    $pictureFile = new BackEndPictureFileForWork();
    /* 圖檔 */
    $sql = "SELECT lrcm_id, lrcm_type, lrcm_img, lrcm_cdn_root  ";
    $sql .= " FROM line_richmenu_content_m ";
    $sql .= " WHERE lrcm_id = ? ";
    $sql .= " AND c_id = ? ";
    $initAry = $mysqli->readArrayPreSTMT($sql, "ss", array($dataKey, $c_id), 4);
    if ($initAry[0][1] == '1') {
        $sql = "Update line_richmenu_content_m set deletestatus = 'Y' WHERE lrcm_id = '" . $dataKey . "' AND c_id = '" . $c_id . "' ";
        $mysqli->deleteSTMT($sql);
    } else if ($initAry[0][1] == '2') {
        $sql = "Update line_richmenu_content_m set deletestatus = 'Y' WHERE lrcm_id = '" . $dataKey . "' AND c_id = '" . $c_id . "' ";
        $mysqli->deleteSTMT($sql);
        $sql = " SELECT lrct_id, lrct_img from line_richmenu_content_t ";
        $sql .= " WHERE lrcm_id = ? ";
        $LRCT_Ary = $mysqli->readArrayPreSTMT($sql, "s", array($dataKey), 2);
        for ($i = 0; $i < count($LRCT_Ary); $i++) {
            $originalpath = $LRCT_Ary[$i][1];
            $thumbnailpath = str_replace("original", "thumbnail", $originalpath);
            $pictureFile->deleteFile($originalpath);
            $pictureFile->deleteFile($thumbnailpath);
        }

        $sql = "Update line_richmenu_content_t set deletestatus = 'Y' WHERE lrcm_id = '" . $dataKey . "' AND c_id = '" . $c_id . "' ";
        $mysqli->deleteSTMT($sql);
    } else if ($initAry[0][1] == '3') {
        $originalpath = $initAry[0][2];
        $thumbnailpath = str_replace("original", "thumbnail", $originalpath);
        $pictureFile->deleteFile($originalpath);
        $pictureFile->deleteFile($thumbnailpath);
        //$result = $s3->deleteObect(CDN_LINE_MENU_CONTENT . $value);
        /* 刪除狀態 */
        $sql = "Update line_richmenu_content_m set deletestatus = 'Y' WHERE lrcm_id = '" . $dataKey . "' AND c_id = '" . $c_id . "' ";
        $mysqli->deleteSTMT($sql);
    }
}
//回原本畫面
header("Location:$fileName.php");
?>
