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
        $line_push_type = $_REQUEST["line_push_type"];
        $OA = !empty($_REQUEST["OA"]) ? $_REQUEST["OA"] : "";
        $pc_content = $_REQUEST["pc_content"];
        //標籤
        if (!empty($_POST["ct_id"])) {
            $ct_id = $_POST["ct_id"];
            $ct_str = implode(",", $ct_id);
        } else {
            $ct_str = "";
        }
        //商品
        if (!empty($_POST["cm_id"])) {
            $cm_id = $_POST["cm_id"];
            $cmid_str = implode(",", $cm_id);
        } else {
            $cmid_str = "";
        }
//定義時間參數
        $excuteDateTime = date("Y-m-d H:i:s"); //操作日期
        $originalImgName = "";
        $thumbnailImgName = "";
        $UploadCDN = "";
        /* 取值 */
        $sql = " SELECT cm_id, cm_name, cm_intro, cm_img, cm_cdn_root ";
        $sql .= " FROM commodity_m ";
        $sql .= " WHERE cm_id IN ($cmid_str) ";
        $sql .= " AND deletestatus = 'N' ";
        $cm_ary = $mysqli->readArraySTMT($sql, 5);

        if ($line_push_type == '1') {
            //圖檔處理
            $oldfileName = ROOT_PATH . $cm_ary[0][3];
            $newfileName = 1040;
            $tempFilePath = ROOT_PATH . $cm_ary[0][3];
            $serverFilePath = "assets_rear/images/commodity/" . $c_id . "/" . $_RandRoot;
            $ThumbnailSize = 250;
            $pictureFile = new BackEndPictureFileForWork($oldfileName, $newfileName, $tempFilePath, $serverFilePath, $ThumbnailSize);
            $pictureFile->createFolder("assets_rear/images/commodity");
            $pictureFile->createFolder("assets_rear/images/commodity/" . $c_id);
            $pictureFile->createFolder($serverFilePath);
            $originalImgName = $pictureFile->archiveReSizePictureFileTo1040();
            $thumbnailImgName = $pictureFile->archiveWithReSizePictureFile();
            /* 上傳檔案到CDN */
            $UploadDir = ROOT_PATH . '/' . $serverFilePath . '/';
            $UploadCDN = CDN_COMMODITY . $c_id . '/' . $_RandRoot . '/';
            $s3->putObject($UploadDir . '240', $UploadCDN . '240');
            $s3->putObject($UploadDir . '300', $UploadCDN . '300');
            $s3->putObject($UploadDir . '460', $UploadCDN . '460');
            $s3->putObject($UploadDir . '700', $UploadCDN . '700');
            $s3->putObject($UploadDir . '1040', $UploadCDN . '1040');
        } else if ($line_push_type == '2') {
            
        } else if ($line_push_type == '3') {
            
        }
        $sql = "INSERT INTO push_commodity ";
        $sql .= "( c_id, line_push_type, ct_id, cm_id, pc_content, pc_cdn_root, entry_datetime )";
        $sql .= " VALUES ";
        $sql .= " ( ?, ?, ?, ?, ?, ?, ? )";
        $pc_id = $mysqli->createPreSTMT_CreateId($sql, "sssssss", array($c_id, $line_push_type, $ct_str, $cmid_str, $pc_content, $UploadCDN, $excuteDateTime));
        //推播
        $post_data = array("pc_id" => "$pc_id", "OA" => "$OA");
        $ch = curl_init(WEB_HOSTNAME . "/api/scrm/CommodityPush/CommodityPush_API.php");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=UTF-8',
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        //回原本畫面
        header("Location:$fileName.php");
        exit();
    }
} else {
    BackToLoginPage();
    exit();
}
?>
