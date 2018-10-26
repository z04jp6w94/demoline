<?php

use FB\FBApp;
use FB\FBApp\Builder\PostBuilder\Feed;
use FB\FBApp\Builder\PostBuilder\Photo;
use FB\FBApp\Builder\PostBuilder\PrivateReplies;

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
$_RandRoot = date("Ym");
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
        $google_short = new GoogleShortAPI();
//取得新增參數
        $fpa_id = checkfpaid(md5(uniqid(rand(), true)), $mysqli);
        $fpa_name = $_REQUEST["fpa_name"];
        $fpa_type = $_REQUEST["fpa_type"];
        $cp_id = $_REQUEST["cp_id"];
        $fpa_content = $_REQUEST["fpa_content"];
        $fpa_url = $_REQUEST["fpa_url"];
        //私下回覆狀態
        $fpa_private_replies_type = $_REQUEST["fpa_private_replies_type"];
        $fpa_private_replies_keyword = $_REQUEST["fpa_private_replies_keyword"];
        $fpa_private_replies = $_REQUEST["fpa_private_replies"];
        $fpa_push_type = $_REQUEST["fpa_push_type"];
        $scheduled_date = $_REQUEST["scheduled_date"];
        $scheduled_time = $_REQUEST["scheduled_time"];
        $scheduled_datetime = $scheduled_date . ' ' . date("H:i:s", strtotime($scheduled_time));
        $unix_scheduled_datetime = strtotime($scheduled_datetime);
        $deletestatus = "N";
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
        /* get token */
        $sql = " select c_fb_appid, c_fb_secret, c_fb_token, c_fb_patch, c_fb_fans ";
        $sql .= " FROM crm_m ";
        $sql .= " WHERE c_id = ? ";
        $FB_Info = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 5);
        $fbApp = new FBApp($FB_Info[0][0], $FB_Info[0][1], $FB_Info[0][2], $FB_Info[0][3]);
        $fbpg_id = $FB_Info[0][4];
        //推文類型
        if ($fpa_type == '1' or $fpa_type == '3') {
            if ($fpa_push_type == '1') {
                $scheduled_datetime = $excuteDateTime;
                $request = $fbApp->send(FBApp::API_TYPE_POST_FEED, "me", new Feed($fpa_name . PHP_EOL . $fpa_content, $fpa_url, TRUE, ""));
                $fpa_post_id = $request['id'];
            } else if ($fpa_push_type == '2') {
                $request = $fbApp->send(FBApp::API_TYPE_POST_FEED, "me", new Feed($fpa_name . PHP_EOL . $fpa_content, $fpa_url, FALSE, $unix_scheduled_datetime));
                $fpa_post_id = $request['id'];
            }
            $sql = "INSERT INTO fb_post_article ";
            $sql .= "( fpa_id, c_id, fbpg_id, cp_id, ct_id, ";
            $sql .= " fpa_post_id, fpa_name, fpa_content, fpa_type, fpa_url, ";
            $sql .= " fpa_img, fpa_cdn_root, fpa_private_replies_type, fpa_private_replies_keyword, fpa_private_replies, ";
            $sql .= " fpa_push_type, scheduled_datetime, deletestatus, start_datetime, end_datetime, ";
            $sql .= " entry_datetime )";
            $sql .= " VALUES ";
            $sql .= " (?, ?, ?, ?, ?, ";
            $sql .= " ?, ?, ?, ?, ?, ";
            $sql .= " ?, ?, ?, ?, ?, ";
            $sql .= " ?, ?, ?, ?, ?, ";
            $sql .= " ?)";
            $result = $mysqli->createPreSTMT_CreateId($sql, "sssssssssssssssssssss", array($fpa_id, $c_id, $fbpg_id, $cp_id, $ctid_str, $fpa_post_id, $fpa_name, $fpa_content, $fpa_type, $fpa_url, $originalImgName, $UploadCDN, $fpa_private_replies_type, $fpa_private_replies_keyword, $fpa_private_replies, $fpa_push_type, $scheduled_datetime, $deletestatus, "", "", $excuteDateTime));
        } else if ($fpa_type == '2') {
            //圖檔處理
            $oldfileName = $_FILES['fpa_img']['name'];
            $newfileName = $c_id . "_" . date("YmdHis");
            $tempFilePath = $_FILES['fpa_img']['tmp_name'];
            $serverFilePath = "assets_rear/images/fb_post_article/" . $c_id . "/" . $_RandRoot;
            $ThumbnailSize = 250;
            $pictureFile = new BackEndPictureFileForWork($oldfileName, $newfileName, $tempFilePath, $serverFilePath, $ThumbnailSize);
            $pictureFile->createFolder("assets_rear/images/fb_post_article");
            $pictureFile->createFolder("assets_rear/images/fb_post_article/" . $c_id);
            $pictureFile->createFolder($serverFilePath);
            $originalImgName = $pictureFile->archiveWithoutReSizePictureFile();
            $thumbnailImgName = $pictureFile->archiveWithReSizePictureFile();
            $imagetype = $pictureFile->getFileNameExtension($oldfileName);
            /* 上傳檔案到CDN */
            $UploadDir = ROOT_PATH . $originalImgName;
            $UploadDirthumbnail = ROOT_PATH . $thumbnailImgName;
            $UploadCDN = CDN_FB_POST_ARTICLE . $c_id . '/' . $_RandRoot . '/original' . $newfileName . '.' . $imagetype;
            $UploadCDNthumbnail = CDN_FB_POST_ARTICLE . $c_id . '/' . $_RandRoot . '/thumbnail' . $newfileName . '.' . $imagetype;
            $s3->putObject($UploadDir, $UploadCDN);
            $s3->putObject($UploadDirthumbnail, $UploadCDNthumbnail);
            if ($fpa_push_type == '1') {
                $scheduled_datetime = $excuteDateTime;
                $request = $fbApp->send(FBApp::API_TYPE_POST_PHOTOS, "me", new Photo(WEB_HOSTNAME . $originalImgName, $fpa_name . PHP_EOL . $fpa_content, TRUE, ""));
                $fpa_post_id = $request['post_id'];
            } else if ($fpa_push_type == '2') {
                $request = $fbApp->send(FBApp::API_TYPE_POST_PHOTOS, "me", new Photo(WEB_HOSTNAME . $originalImgName, $fpa_name . PHP_EOL . $fpa_content, FALSE, $unix_scheduled_datetime));
                $fpa_post_id = $request['id'];
                $fpa_post_id = $fbpg_id . "_" . $fpa_post_id;
            }
            $sql = "INSERT INTO fb_post_article ";
            $sql .= "( fpa_id, c_id, fbpg_id, cp_id, ct_id, ";
            $sql .= " fpa_post_id, fpa_name, fpa_content, fpa_type, fpa_url, ";
            $sql .= " fpa_img, fpa_cdn_root, fpa_private_replies_type, fpa_private_replies_keyword, fpa_private_replies, ";
            $sql .= " fpa_push_type, scheduled_datetime, deletestatus, start_datetime, end_datetime, ";
            $sql .= " entry_datetime )";
            $sql .= " VALUES ";
            $sql .= " (?, ?, ?, ?, ?, ";
            $sql .= " ?, ?, ?, ?, ?, ";
            $sql .= " ?, ?, ?, ?, ?, ";
            $sql .= " ?, ?, ?, ?, ?, ";
            $sql .= " ?)";
            $result = $mysqli->createPreSTMT_CreateId($sql, "sssssssssssssssssssss", array($fpa_id, $c_id, $fbpg_id, $cp_id, $ctid_str, $fpa_post_id, $fpa_name, $fpa_content, $fpa_type, $fpa_url, $originalImgName, $UploadCDN, $fpa_private_replies_type, $fpa_private_replies_keyword, $fpa_private_replies, $fpa_push_type, $scheduled_datetime, $deletestatus, "", "", $excuteDateTime));
        }
    }
} else {
    BackToLoginPage();
    exit;
}

//回原本畫面
header("Location:$fileName.php");

function checkfpaid($id, $mysqli) {
    $sql = " select fpa_id from fb_post_article ";
    $sql .= " where fpa_id = ? ";
    $exist = $mysqli->readValuePreSTMT($sql, "s", array($id), 1);
    if ($exist) {
        $id = call_user_func("newid", $id, $mysqli);
    }
    return $id;
}

function newid($id, $mysqli) {
    return checkfpaid(md5(uniqid(rand(), true)), $mysqli);
}

?>
