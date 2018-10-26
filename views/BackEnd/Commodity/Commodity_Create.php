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
        $mysqli = new DatabaseProcessorForWork();
//取得新增參數
        $cc_id = $_REQUEST["cc_id"];
        $cm_name = $_REQUEST["cm_name"];
        $cm_price = $_REQUEST["cm_price"];
        $cm_intro = $_REQUEST["cm_intro"];
        $cm_type = $_REQUEST["cm_type"];
        $cm_url = $_REQUEST["cm_url"];
        $cm_shipping_fee = $_REQUEST["cm_shipping_fee"];
        $cm_current_stock = $_REQUEST["cm_current_stock"];
        $cm_max_buy = $_REQUEST["cm_max_buy"];
        $cm_min_buy = $_REQUEST["cm_min_buy"];
        $cm_date_range = $_REQUEST["cm_date_range"];
        $date_range = explode("-", $cm_date_range);
        $cm_starttime = trim($date_range[0]);
        $cm_endtime = trim($date_range[1]);
        if (!empty($_POST["ct_id"])) {
            $ct_id = $_POST["ct_id"];
            $ctid_str = implode(",", $ct_id);
        } else {
            $ctid_str = "";
        }
//定義時間參數
        $excuteDateTime = date("Y-m-d H:i:s"); //操作日期
        $originalImgName = "";
        $thumbnailImgName = "";
        $UploadCDN = "";

        $oldfileName = $_FILES['cm_img']['name'];
        $newfileName = $c_id . "_" . date("YmdHis");
        $tempFilePath = $_FILES['cm_img']['tmp_name'];
        $serverFilePath = "assets_rear/images/commodity/" . $c_id;
        $ThumbnailSize = 250;
        $pictureFile = new BackEndPictureFileForWork($oldfileName, $newfileName, $tempFilePath, $serverFilePath, $ThumbnailSize);
        $pictureFile->createFolder("assets_rear/images/commodity");
        $originalImgName = $pictureFile->archiveWithoutReSizePictureFile();
        $thumbnailImgName = $pictureFile->archiveWithReSizePictureFile();
        /* 上傳檔案到CDN */
        $UploadDir = ROOT_PATH . '/' . $originalImgName;
        $CDN_FILE_NAME = substr($originalImgName, -37);
        $UploadCDN = CDN_COMMODITY . $c_id . '/' . $CDN_FILE_NAME;
        $s3->putObject($UploadDir, $UploadCDN);
        if ($cm_type == "1") {
            $cm_url = "";
        } else if ($cm_type == "2") {
            $cm_shipping_fee = "";
            $cm_current_stock = "";
            $cm_max_buy = "";
            $cm_min_buy = "";
        }
        $sql = "INSERT INTO commodity_m ";
        $sql .= "( c_id, ct_id, cc_id, cm_name, cm_price, ";
        $sql .= " cm_intro, cm_img, cm_cdn_root, cm_type, cm_url, ";
        $sql .= " cm_shipping_fee, cm_current_stock, cm_max_buy, cm_min_buy, cm_starttime, ";
        $sql .= " cm_endtime, deletestatus, entry_datetime, modify_datetime )";
        $sql .= " VALUES ";
        $sql .= " (?, ?, ?, ?, ?, ";
        $sql .= " ?, ?, ?, ?, ?, ";
        $sql .= " ?, ?, ?, ?, ?, ";
        $sql .= " ?, 'N', ?, ? ) ";
        $Create_Ary = array();
        array_push($Create_Ary, $c_id);
        array_push($Create_Ary, $ctid_str);
        array_push($Create_Ary, $cc_id);
        array_push($Create_Ary, $cm_name);
        array_push($Create_Ary, $cm_price); //5
        array_push($Create_Ary, $cm_intro);
        array_push($Create_Ary, $originalImgName);
        array_push($Create_Ary, $UploadCDN);
        array_push($Create_Ary, $cm_type);
        array_push($Create_Ary, $cm_url); //10
        array_push($Create_Ary, $cm_shipping_fee);
        array_push($Create_Ary, $cm_current_stock);
        array_push($Create_Ary, $cm_max_buy);
        array_push($Create_Ary, $cm_min_buy);
        array_push($Create_Ary, $cm_starttime); //15
        array_push($Create_Ary, $cm_endtime);
        array_push($Create_Ary, $excuteDateTime);
        array_push($Create_Ary, $excuteDateTime);
        $mysqli->createPreSTMT($sql, "ssssssssssssssssss", $Create_Ary);
    }
} else {
    BackToLoginPage();
    exit;
}
//回原本畫面
header("Location:$fileName.php");
?>
