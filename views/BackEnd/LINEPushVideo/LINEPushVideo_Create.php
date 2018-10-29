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
        /* 執行 */
        ob_end_clean();
        header("Connection: close");
        ignore_user_abort(true);
        set_time_limit(0);
        /* 執行 */
        $mysqli = new DatabaseProcessorForWork();
        $bitly = new BitlyShortAPI();
//取得新增參數
        $lpv_name = $_REQUEST["lpv_name"];
        $lpv_send_status = $_REQUEST["lpv_send_status"];
        $lpv_send_date = $_REQUEST["lpv_send_date"];
        $lpv_send_time = $_REQUEST["lpv_send_time"];
        $lpv_send_datetime = $lpv_send_date . ' ' . date("H:i:s", strtotime($lpv_send_time));
        $lpv_reserve = "Y";
//定義時間參數
        $excuteDateTime = date("Y-m-d H:i:s"); //操作日期
        $originalImgName = "";
        $thumbnailImgName = "";
        $UploadImageCDN = "";
        $UploadVideoCDN = "";
        //發送狀態
        if ($lpv_send_status == '1') {
            $lpv_send_datetime = $excuteDateTime;
        } else {
            $lpv_reserve = "N";
        }
        //預覽圖
        $oldfileName = $_FILES['lpv_img']['name'];
        $newfileName = $c_id . "_" . date("YmdHis");
        $tempFilePath = $_FILES['lpv_img']['tmp_name'];
        $serverFilePath = "assets_rear/images/push_video/" . $c_id;
        $ThumbnailSize = 250;
        $pictureFile = new BackEndPictureFileForWork($oldfileName, $newfileName, $tempFilePath, $serverFilePath, $ThumbnailSize);
        $pictureFile->createFolder("assets_rear/images/push_video");
        $originalImgName = $pictureFile->archiveWithoutReSizePictureFile();
        $thumbnailImgName = $pictureFile->archiveWithReSizePictureFile();
        /* 上傳檔案到CDN */
//        $UploadDir = ROOT_PATH . '/' . $thumbnailImgName;
//        $CDN_FILE_NAME = substr($thumbnailImgName, -38);
//        $UploadImageCDN = CDN_PUSH_VIDEO . $c_id . '/' . $CDN_FILE_NAME;
//        $s3->putObject($UploadDir, $UploadImageCDN);
        //影片
        $oldfileName = $_FILES['lpv_video']['name'];
        $newfileName = $c_id . "_" . date("YmdHis");
        $tempFilePath = $_FILES['lpv_video']['tmp_name'];
        $serverFilePath = "assets_rear/files/push_video/" . $c_id;
        $VideoFile = new BackEndFileForWork($oldfileName, $newfileName, $tempFilePath, $serverFilePath);
        $VideoFile->createFolder("assets_rear/files/push_video");
        $originalVideoName = $VideoFile->archive();
        /* 上傳檔案到CDN */
//        $UploadDir = ROOT_PATH . '/' . $originalVideoName;
//        $CDN_FILE_NAME = substr($originalVideoName, -29);
//        $UploadVideoCDN = CDN_PUSH_VIDEO . $c_id . '/' . $CDN_FILE_NAME;
//        $s3->putObject($UploadDir, $UploadVideoCDN);
        //資料庫
        $sql = " INSERT INTO line_push_video ";
        $sql .= "( c_id, lpv_name, lpv_img, lpv_img_cdn_root, lpv_video, ";
        $sql .= " lpv_video_cdn_root, lpv_send_status, lpv_send_date, lpv_reserve, deletestatus, ";
        $sql .= " entry_datetime )";
        $sql .= " VALUES ";
        $sql .= " (?, ?, ?, ?, ?, ";
        $sql .= " ?, ?, ?, ?, 'N', ";
        $sql .= " ? )";
        $lpv_id = $mysqli->createPreSTMT_CreateId($sql, "ssssssssss", array($c_id, $lpv_name, $originalImgName, $UploadImageCDN, $originalVideoName, $UploadVideoCDN, $lpv_send_status, $lpv_send_datetime, $lpv_reserve, $excuteDateTime));
        //立即推播
        if ($lpv_send_status == '1') {
            $post_data = array("lpv_id" => "$lpv_id");
            $ch = curl_init(WEB_HOSTNAME . "/api/scrm/LINEPushVideo/LINEPushVideo_API.php");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=UTF-8',
            ));
            $result = curl_exec($ch);
            curl_close($ch);
        }
        //回原本畫面
        header("Location:$fileName.php");
    }
} else {
    BackToLoginPage();
    exit;
}
?>
