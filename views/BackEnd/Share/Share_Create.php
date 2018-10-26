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
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //取得固定參數
    $c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
    $fileName = $_REQUEST["fileName"];
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
        $sa_title = $_REQUEST["sa_title"];
        $sa_content = $_REQUEST["sa_content"];
        $sa_awards_content = $_REQUEST["sa_awards_content"];
        $sa_standard_number = $_REQUEST["sa_standard_number"];
        $sa_standard_content = $_REQUEST["sa_standard_content"];
//定義時間參數
        $excuteDateTime = date("Y-m-d H:i:s"); //操作日期
        $originalImgName = "";
        $thumbnailImgName = "";
        $UploadCDN = "";

        if (!file_exists($_FILES['sa_awards_img']['tmp_name']) || !is_uploaded_file($_FILES['sa_awards_img']['tmp_name'])) {
            //無上傳檔案;
        } else {
            $oldfileName = $_FILES['sa_awards_img']['name'];
            $newfileName = $c_id . "_" . date("YmdHis");
            $tempFilePath = $_FILES['sa_awards_img']['tmp_name'];
            $serverFilePath = "assets_rear/images/activity_share/" . $c_id;
            $ThumbnailSize = 250;
            $pictureFile = new BackEndPictureFileForWork($oldfileName, $newfileName, $tempFilePath, $serverFilePath, $ThumbnailSize);
            $pictureFile->createFolder("assets_rear/images/activity_share");
            $originalImgName = $pictureFile->archiveWithoutReSizePictureFile();
            $thumbnailImgName = $pictureFile->archiveWithReSizePictureFile();
            /* 上傳檔案到CDN */
            $UploadDir = ROOT_PATH . '/' . $originalImgName;
            $CDN_FILE_NAME = substr($originalImgName, -37);
            $UploadCDN = CDN_SHARE . $CDN_FILE_NAME;
            $s3->putObject($UploadDir, $UploadCDN);
        }
        $sql = "INSERT INTO share_activity ";
        $sql .= "( c_id, sa_title, sa_content, sa_awards_content, sa_awards_img, ";
        $sql .= " sa_cdn_root, sa_standard_number, sa_standard_content, sa_status, deletestatus, ";
        $sql .= " entry_datetime )";
        $sql .= " VALUES ";
        $sql .= " (?, ?, ?, ?, ?, ";
        $sql .= " ?, ?, ?, 'N', 'N', ";
        $sql .= " ? )";
        $mysqli->createPreSTMT($sql, "sssssssss", array($c_id, $sa_title, $sa_content, $sa_awards_content, $originalImgName, $UploadCDN, $sa_standard_number, $sa_standard_content, $excuteDateTime));
    }
}

//回原本畫面
header("Location:$fileName.php");
?>
