<?php

use FB\FBApp;
use FB\FBApp\Builder\MessengerBuilder\Component\MessageButton;
use FB\FBApp\Builder\MessengerBuilder\Component\MessageElement;
use FB\FBApp\Builder\MessengerBuilder\Component\MessageElement\MessageElementGeneric;
use FB\FBApp\Builder\MessengerBuilder\Component\MessageElement\MessageElementList;
use FB\FBApp\Builder\MessengerBuilder\Component\MessageElement\MessageElementMedia;
use FB\FBApp\Builder\MessengerBuilder\Component\QuickReplyButton;
use FB\FBApp\Builder\MessengerBuilder\MessageAttachment;
use FB\FBApp\Builder\MessengerBuilder\MessageAttachment\Image;
use FB\FBApp\Builder\MessengerBuilder\MessageAttachment\Template;
use FB\FBApp\Builder\MessengerBuilder\MessageText;
use FB\FBApp\Builder\MessengerBuilder\QuickReply;
use FB\FBApp\Builder\MessengerBuilder\SenderAction;
use FB\FBApp\Builder\MessengerBuilder\MessageCreatives;
use FB\FBApp\Builder\MessengerBuilder\MessageCreatives\DynamicText;
use FB\FBApp\Builder\MessengerBuilder\Broadcast;

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
        $bitly = new BitlyShortAPI();
//預設值
        /* Carousel */
        $count_lrcm_img = "";
        $del_text = "";
        /* Default */
        $ctid_str = "";
        $lrcm_img = "";
        $fpm_content = "";
        $lrcm_action_type = "";
        $app_id = "";
        $fpm_url = "";
        //操作時間
        $entry_datetime = date("Y-m-d H:i:s");
        $originalImgName = "";
        $thumbnailImgName = "";
        $UploadCDN = "";
        $broadcast_id = "";
//取得新增參數
        $fpm_push_type = trim($_REQUEST["fpm_push_type"]); //類型    
        $fpm_send_status = $_REQUEST["fpm_send_status"];
        $fpm_send_date = $_REQUEST["fpm_send_date"];
        $fpm_send_time = $_REQUEST["fpm_send_time"];
        $fpm_send_datetime = $fpm_send_date . ' ' . date("H:i:s", strtotime($fpm_send_time));
        $deletestatus = "N";
        $fpm_reserve = "Y";
        $fpm_target_type = "1";
        $Custom_label_id = "1944614522225852";
        /* get token */
        $sql = " select c_fb_appid, c_fb_secret, c_fb_token, c_fb_patch, c_fb_fans ";
        $sql .= " FROM crm_m ";
        $sql .= " WHERE c_id = ? ";
        $FB_Info = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 5);
        $fbApp = new FBApp($FB_Info[0][0], $FB_Info[0][1], $FB_Info[0][2], $FB_Info[0][3]);
        $fbpg_id = $FB_Info[0][4];
        $fpm_id = checkfpmid(md5(uniqid(rand(), true)), $mysqli);
        $fpm_name = $_REQUEST["fpm_name"];
        //發送狀態
        switch ($fpm_push_type) {
            case '1'://文字
                $fpm_content = $_REQUEST["fpm_content"];
                if (!empty($_POST["ct_id"])) {
                    $ct_id = $_POST["ct_id"];
                    $ctid_str = implode(",", $ct_id);
                }
                if ($fpm_send_status == '1') {
                    $fpm_send_datetime = $entry_datetime;
                    $content = new MessageCreatives(MessageCreatives::CREATIVES_TYPE_TEXT, new DynamicText($fpm_name . PHP_EOL . $fpm_content, $fpm_name . PHP_EOL . $fpm_content));
                    $response = $fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGE_CREATIVES, "me", $content);
                    $message_creative_id = $response['message_creative_id'];
                    $response2 = $fbApp->send(FBApp::API_TYPE_MESSENGER_BROADCAST_MESSAGES, "me", new Broadcast(Broadcast::MESSAGING_TYPE_MESSAGE_TAG, $message_creative_id, $Custom_label_id, Broadcast::TAG_NON_PROMOTIONAL_SUBSCRIPTION));
                    $broadcast_id = $response2['broadcast_id'];
                } else if ($fpm_send_status == '2') {
                    $fpm_reserve = "N";
                }
                $sql = "INSERT INTO fb_push_messenger ";
                $sql .= "( fpm_id, c_id, ct_id, fpm_name, fpm_content, ";
                $sql .= " fpm_url, fpm_img, fpm_cdn_root, fpm_send_status, fpm_send_date, ";
                $sql .= " fpm_push_type, fpm_target_type, fpm_reserve, fpm_broadcast_id, deletestatus,";
                $sql .= " entry_datetime )";
                $sql .= " VALUES ";
                $sql .= " (?, ?, ?, ?, ?, ";
                $sql .= " ?, ?, ?, ?, ?, ";
                $sql .= " ?, ?, ?, ?, ?, ";
                $sql .= " ? )";
                $Create_Ary = array();
                array_push($Create_Ary, $fpm_id);
                array_push($Create_Ary, $c_id);
                array_push($Create_Ary, $ctid_str);
                array_push($Create_Ary, $fpm_name);
                array_push($Create_Ary, $fpm_content);
                array_push($Create_Ary, $fpm_url);
                array_push($Create_Ary, $originalImgName);
                array_push($Create_Ary, $UploadCDN);
                array_push($Create_Ary, $fpm_send_status);
                array_push($Create_Ary, $fpm_send_datetime);
                array_push($Create_Ary, $fpm_push_type);
                array_push($Create_Ary, $fpm_target_type);
                array_push($Create_Ary, $fpm_reserve);
                array_push($Create_Ary, $broadcast_id);
                array_push($Create_Ary, $deletestatus);
                array_push($Create_Ary, $entry_datetime); //16
                $result = $mysqli->createPreSTMT_CreateId($sql, "ssssssssssssssss", $Create_Ary);
                break;
            case '2'://圖像                    
                if (!empty($_POST["ct_id"])) {
                    $ct_id = $_POST["ct_id"];
                    $ctid_str = implode(",", $ct_id);
                }
                //圖檔處理
                $oldfileName = $_FILES['lrcm_img']['name'][0];
                $newfileName = $c_id . "_" . date("YmdHis");
                $tempFilePath = $_FILES['lrcm_img']['tmp_name'][0];
                $serverFilePath = "assets_rear/images/fb_push/" . $c_id . "/" . $_RandRoot;
                $ThumbnailSize = 250;
                $pictureFile = new BackEndPictureFileForWork($oldfileName, $newfileName, $tempFilePath, $serverFilePath, $ThumbnailSize);
                $pictureFile->createFolder("assets_rear/images/fb_push");
                $pictureFile->createFolder("assets_rear/images/fb_push/" . $c_id);
                $pictureFile->createFolder($serverFilePath);
                $originalImgName = $pictureFile->archiveWithoutReSizePictureFile();
                $thumbnailImgName = $pictureFile->archiveWithReSizePictureFile();
                $imagetype = $pictureFile->getFileNameExtension($oldfileName);
                /* 上傳檔案到CDN */
                $UploadDir = ROOT_PATH . $originalImgName;
                $UploadDirthumbnail = ROOT_PATH . $thumbnailImgName;
                $UploadCDN = CDN_FB_TARGETPUSH . $c_id . '/' . $_RandRoot . '/original' . $newfileName . '.' . $imagetype;
                $UploadCDNthumbnail = CDN_FB_TARGETPUSH . $c_id . '/' . $_RandRoot . '/thumbnail' . $newfileName . '.' . $imagetype;
                $s3->putObject($UploadDir, $UploadCDN);
                $s3->putObject($UploadDirthumbnail, $UploadCDNthumbnail);
                if ($fpm_send_status == '1') {
                    $fpm_send_datetime = $entry_datetime;
                    $content = new MessageCreatives(MessageCreatives::CREATIVES_TYPE_TEMPLATE, new Image(Image::TYPE_URL, WEB_HOSTNAME . $originalImgName));
                    $response = $fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGE_CREATIVES, "me", $content);
                    $message_creative_id = $response['message_creative_id'];
                    $response2 = $fbApp->send(FBApp::API_TYPE_MESSENGER_BROADCAST_MESSAGES, "me", new Broadcast(Broadcast::MESSAGING_TYPE_MESSAGE_TAG, $message_creative_id, $Custom_label_id, Broadcast::TAG_NON_PROMOTIONAL_SUBSCRIPTION));
                    $broadcast_id = $response2['broadcast_id'];
                } else if ($fpm_send_status == '2') {
                    $fpm_reserve = "N";
                }
                $sql = "INSERT INTO fb_push_messenger ";
                $sql .= "( fpm_id, c_id, ct_id, fpm_name, fpm_content, ";
                $sql .= " fpm_url, fpm_img, fpm_cdn_root, fpm_send_status, fpm_send_date, ";
                $sql .= " fpm_push_type, fpm_target_type, fpm_reserve, fpm_broadcast_id, deletestatus,";
                $sql .= " entry_datetime )";
                $sql .= " VALUES ";
                $sql .= " (?, ?, ?, ?, ?, ";
                $sql .= " ?, ?, ?, ?, ?, ";
                $sql .= " ?, ?, ?, ?, ?, ";
                $sql .= " ? )";
                $Create_Ary = array();
                array_push($Create_Ary, $fpm_id);
                array_push($Create_Ary, $c_id);
                array_push($Create_Ary, $ctid_str);
                array_push($Create_Ary, $fpm_name);
                array_push($Create_Ary, $fpm_content);
                array_push($Create_Ary, $fpm_url);
                array_push($Create_Ary, $originalImgName);
                array_push($Create_Ary, $UploadCDN);
                array_push($Create_Ary, $fpm_send_status);
                array_push($Create_Ary, $fpm_send_datetime);
                array_push($Create_Ary, $fpm_push_type);
                array_push($Create_Ary, $fpm_target_type);
                array_push($Create_Ary, $fpm_reserve);
                array_push($Create_Ary, $broadcast_id);
                array_push($Create_Ary, $deletestatus);
                array_push($Create_Ary, $entry_datetime); //16
                $result = $mysqli->createPreSTMT_CreateId($sql, "ssssssssssssssss", $Create_Ary);
                break;
            case '3'://輪播
                $fpm_name = $_REQUEST["fpm_name"][0];
                $fpm_content = "";
                $fpm_url = "";
                $count_lrcm_img = $_REQUEST["count_lrcm_img"];
                $del_text = $_REQUEST["del_text"];
                $AddBtnAay = array();
                for ($i = 1; $i <= $count_lrcm_img; $i++) {
                    array_push($AddBtnAay, $i);
                }
                $DelAry = explode(",", $del_text);
                $AddAry = array();
                $NewArray1 = array_diff($AddBtnAay, $DelAry);
                for ($i = 1; $i <= $count_lrcm_img; $i++) {
                    if (isset($NewArray1[$i - 1]) != '') {
                        array_push($AddAry, $NewArray1[$i - 1]);
                    }
                }
                if ($fpm_send_status == '1') {
                    $fpm_send_datetime = $entry_datetime;
                } else if ($fpm_send_status == '2') {
                    $fpm_reserve = "N";
                }
                $sql = "INSERT INTO fb_push_messenger ";
                $sql .= "( fpm_id, c_id, ct_id, fpm_name, fpm_content, ";
                $sql .= " fpm_url, fpm_img, fpm_cdn_root, fpm_send_status, fpm_send_date, ";
                $sql .= " fpm_push_type, fpm_target_type, fpm_reserve, fpm_broadcast_id, deletestatus,";
                $sql .= " entry_datetime )";
                $sql .= " VALUES ";
                $sql .= " (?, ?, ?, ?, ?, ";
                $sql .= " ?, ?, ?, ?, ?, ";
                $sql .= " ?, ?, ?, ?, ?, ";
                $sql .= " ? )";
                $Create_Ary = array();
                array_push($Create_Ary, $fpm_id);
                array_push($Create_Ary, $c_id);
                array_push($Create_Ary, $ctid_str);
                array_push($Create_Ary, $fpm_name);
                array_push($Create_Ary, $fpm_content); //5
                array_push($Create_Ary, $fpm_url);
                array_push($Create_Ary, $originalImgName);
                array_push($Create_Ary, $UploadCDN);
                array_push($Create_Ary, $fpm_send_status);
                array_push($Create_Ary, $fpm_send_datetime); //10
                array_push($Create_Ary, $fpm_push_type);
                array_push($Create_Ary, $fpm_target_type);
                array_push($Create_Ary, $fpm_reserve);
                array_push($Create_Ary, $broadcast_id);
                array_push($Create_Ary, $deletestatus);
                array_push($Create_Ary, $entry_datetime); //16
                $result = $mysqli->createPreSTMT_CreateId($sql, "ssssssssssssssss", $Create_Ary);
                $lrct_img = "";
                $lrct_cdn_root = "";
                $gen_ary = array();
                for ($i = 0; $i < count($AddAry); $i++) {
                    $fpmd_id = checkfpmdid(md5(uniqid(rand(), true)), $mysqli);
                    $fpm_name = $_REQUEST["fpm_name"][$i];
                    $fpm_content = $_REQUEST["fpm_content"][$i];
                    if (!empty($_REQUEST["ct_id" . $AddAry[$i]])) {
                        $ct_id = $_REQUEST["ct_id" . $AddAry[$i]];
                        $ctid_str = implode(",", $ct_id);
                    } else {
                        $ctid_str = "";
                    }
                    //FILE UPLOAD STATUS
                    if (!file_exists($_FILES['lrcm_img']['tmp_name'][$i]) || !is_uploaded_file($_FILES['lrcm_img']['tmp_name'][$i])) {
                        //'No File Upload!';
                    } else {
                        $oldfileName = $_FILES['lrcm_img']['name'][$i];
                        /* GetRandNum() 3碼 */
                        $RandomNumber = GetRandNum();
                        $newfileName = $c_id . "_" . date("YmdHis") . $RandomNumber;
                        $tempFilePath = $_FILES['lrcm_img']['tmp_name'][$i];
                        $serverFilePath = "assets_rear/images/fb_push/" . $c_id . "/" . $_RandRoot;
                        $ThumbnailSize = 250;
                        $pictureFile = new BackEndPictureFileForWork($oldfileName, $newfileName, $tempFilePath, $serverFilePath, $ThumbnailSize);
                        $pictureFile->createFolder("assets_rear/images/fb_push");
                        $pictureFile->createFolder("assets_rear/images/fb_push/" . $c_id);
                        $pictureFile->createFolder($serverFilePath);
                        $originalImgName = $pictureFile->archiveWithoutReSizePictureFile();
                        $thumbnailImgName = $pictureFile->archiveWithReSizePictureFile();
                        $imagetype = $pictureFile->getFileNameExtension($oldfileName);

                        /* 上傳檔案到CDN */
                        $UploadDir = ROOT_PATH . $originalImgName;
                        $UploadDirthumbnail = ROOT_PATH . $thumbnailImgName;
                        $UploadCDN = CDN_FB_TARGETPUSH . $c_id . '/' . $_RandRoot . '/original' . $newfileName . '.' . $imagetype;
                        $UploadCDNthumbnail = CDN_FB_TARGETPUSH . $c_id . '/' . $_RandRoot . '/thumbnail' . $newfileName . '.' . $imagetype;
                        $s3->putObject($UploadDir, $UploadCDN);
                        $s3->putObject($UploadDirthumbnail, $UploadCDNthumbnail);

                        //檔案路徑
                        $lrct_img = $originalImgName;
                    }
                    $fpm_url = !empty($_REQUEST["fpm_url"][$i]) ? $_REQUEST["fpm_url"][$i] : "";
                    $fpmd_sort = $_REQUEST["fpmd_sort"][$i];
                    $deletestatus = "N";
                    /* INSERT DATA */
                    $sql = " INSERT INTO fb_push_messenger_d ";
                    $sql .= " ( fpmd_id, fpm_id, c_id, ct_id, fpmd_name,  ";
                    $sql .= " fpmd_content, fpmd_url, fpmd_img, fpmd_cdn_root, fpmd_push_type, ";
                    $sql .= " fpmd_sort, deletestatus, entry_datetime ) ";
                    $sql .= " VALUES ";
                    $sql .= " ( ?, ?, ?, ?, ?, ";
                    $sql .= " ?, ?, ?, ?, ?, ";
                    $sql .= " ?, ?, ? ) ";
                    $Create_Ary = array();
                    array_push($Create_Ary, $fpmd_id);
                    array_push($Create_Ary, $fpm_id);
                    array_push($Create_Ary, $c_id);
                    array_push($Create_Ary, $ctid_str);
                    array_push($Create_Ary, $fpm_name);
                    array_push($Create_Ary, $fpm_content);
                    array_push($Create_Ary, $fpm_url);
                    array_push($Create_Ary, $lrct_img);
                    array_push($Create_Ary, $UploadCDN);
                    array_push($Create_Ary, $fpm_push_type);
                    array_push($Create_Ary, $fpmd_sort);
                    array_push($Create_Ary, $deletestatus);
                    array_push($Create_Ary, $entry_datetime);
                    $mysqli->createPreSTMT($sql, "sssssssssssss", $Create_Ary);
                    array_push($gen_ary, new MessageElementGeneric($fpm_name, $fpm_content, "", WEB_HOSTNAME . $lrct_img, [new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => $fpm_url])]));
                }
                //立即建造template
                if ($fpm_send_status == '1') {
                    $fpm_send_datetime = $entry_datetime;
                    $content = new MessageCreatives(MessageCreatives::CREATIVES_TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_GENERIC, [
                        'elements' => $gen_ary
                    ]));
                    $response = $fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGE_CREATIVES, "me", $content);
                    $message_creative_id = $response['message_creative_id'];
                    $response2 = $fbApp->send(FBApp::API_TYPE_MESSENGER_BROADCAST_MESSAGES, "me", new Broadcast(Broadcast::MESSAGING_TYPE_MESSAGE_TAG, $message_creative_id, $Custom_label_id, Broadcast::TAG_NON_PROMOTIONAL_SUBSCRIPTION));
                    $broadcast_id = $response2['broadcast_id'];
                }
                break;
            case '4'://文字+按鈕
                $fpm_content = $_REQUEST["fpm_content"];
                $fpm_url = $_REQUEST["fpm_url"];
                if (!empty($_POST["ct_id"])) {
                    $ct_id = $_POST["ct_id"];
                    $ctid_str = implode(",", $ct_id);
                }
                if ($fpm_send_status == '1') {
                    $fpm_send_datetime = $entry_datetime;
                    $content = new MessageCreatives(MessageCreatives::CREATIVES_TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_BUTTON, [
                        'text' => $fpm_name . PHP_EOL . $fpm_content,
                        'buttons' => [
                            new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "【點擊我】", "url" => $fpm_url])
                        ]
                    ]));
                    $response = $fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGE_CREATIVES, "me", $content);
                    $message_creative_id = $response['message_creative_id'];
                    $response2 = $fbApp->send(FBApp::API_TYPE_MESSENGER_BROADCAST_MESSAGES, "me", new Broadcast(Broadcast::MESSAGING_TYPE_MESSAGE_TAG, $message_creative_id, $Custom_label_id, Broadcast::TAG_NON_PROMOTIONAL_SUBSCRIPTION));
                    $broadcast_id = $response2['broadcast_id'];
                } else if ($fpm_send_status == '2') {
                    $fpm_reserve = "N";
                }
                $sql = "INSERT INTO fb_push_messenger ";
                $sql .= "( fpm_id, c_id, ct_id, fpm_name, fpm_content, ";
                $sql .= " fpm_url, fpm_img, fpm_cdn_root, fpm_send_status, fpm_send_date, ";
                $sql .= " fpm_push_type, fpm_target_type, fpm_reserve, fpm_broadcast_id, deletestatus,";
                $sql .= " entry_datetime )";
                $sql .= " VALUES ";
                $sql .= " (?, ?, ?, ?, ?, ";
                $sql .= " ?, ?, ?, ?, ?, ";
                $sql .= " ?, ?, ?, ?, ?, ";
                $sql .= " ? )";
                $Create_Ary = array();
                array_push($Create_Ary, $fpm_id);
                array_push($Create_Ary, $c_id);
                array_push($Create_Ary, $ctid_str);
                array_push($Create_Ary, $fpm_name);
                array_push($Create_Ary, $fpm_content);
                array_push($Create_Ary, $fpm_url);
                array_push($Create_Ary, $originalImgName);
                array_push($Create_Ary, $UploadCDN);
                array_push($Create_Ary, $fpm_send_status);
                array_push($Create_Ary, $fpm_send_datetime);
                array_push($Create_Ary, $fpm_push_type);
                array_push($Create_Ary, $fpm_target_type);
                array_push($Create_Ary, $fpm_reserve);
                array_push($Create_Ary, $broadcast_id);
                array_push($Create_Ary, $deletestatus);
                array_push($Create_Ary, $entry_datetime); //16
                $result = $mysqli->createPreSTMT_CreateId($sql, "ssssssssssssssss", $Create_Ary);
                break;
            case '5'://清單型
                $fpm_name = $_REQUEST["fpm_name"][0];
                $fpm_content = "";
                $fpm_url = "";
                $count_lrcm_img = $_REQUEST["count_lrcm_img"];
                $del_text = $_REQUEST["del_text"];
                $AddBtnAay = array();
                for ($i = 1; $i <= $count_lrcm_img; $i++) {
                    array_push($AddBtnAay, $i);
                }
                $DelAry = explode(",", $del_text);
                $AddAry = array();
                $NewArray1 = array_diff($AddBtnAay, $DelAry);
                for ($i = 1; $i <= $count_lrcm_img; $i++) {
                    if (isset($NewArray1[$i - 1]) != '') {
                        array_push($AddAry, $NewArray1[$i - 1]);
                    }
                }
                if ($fpm_send_status == '1') {
                    $fpm_send_datetime = $entry_datetime;
                } else if ($fpm_send_status == '2') {
                    $fpm_reserve = "N";
                }
                $sql = "INSERT INTO fb_push_messenger ";
                $sql .= "( fpm_id, c_id, ct_id, fpm_name, fpm_content, ";
                $sql .= " fpm_url, fpm_img, fpm_cdn_root, fpm_send_status, fpm_send_date, ";
                $sql .= " fpm_push_type, fpm_target_type, fpm_reserve, fpm_broadcast_id, deletestatus,";
                $sql .= " entry_datetime )";
                $sql .= " VALUES ";
                $sql .= " (?, ?, ?, ?, ?, ";
                $sql .= " ?, ?, ?, ?, ?, ";
                $sql .= " ?, ?, ?, ?, ?, ";
                $sql .= " ? )";
                $Create_Ary = array();
                array_push($Create_Ary, $fpm_id);
                array_push($Create_Ary, $c_id);
                array_push($Create_Ary, $ctid_str);
                array_push($Create_Ary, $fpm_name);
                array_push($Create_Ary, $fpm_content); //5
                array_push($Create_Ary, $fpm_url);
                array_push($Create_Ary, $originalImgName);
                array_push($Create_Ary, $UploadCDN);
                array_push($Create_Ary, $fpm_send_status);
                array_push($Create_Ary, $fpm_send_datetime); //10
                array_push($Create_Ary, $fpm_push_type);
                array_push($Create_Ary, $fpm_target_type);
                array_push($Create_Ary, $fpm_reserve);
                array_push($Create_Ary, $broadcast_id);
                array_push($Create_Ary, $deletestatus);
                array_push($Create_Ary, $entry_datetime); //16
                $result = $mysqli->createPreSTMT_CreateId($sql, "ssssssssssssssss", $Create_Ary);
                $lrct_img = "";
                $lrct_cdn_root = "";
                $gen_ary = array();
                for ($i = 0; $i < count($AddAry); $i++) {
                    $fpmd_id = checkfpmdid(md5(uniqid(rand(), true)), $mysqli);
                    $fpm_name = $_REQUEST["fpm_name"][$i];
                    $fpm_content = $_REQUEST["fpm_content"][$i];
                    if (!empty($_REQUEST["ct_id" . $AddAry[$i]])) {
                        $ct_id = $_REQUEST["ct_id" . $AddAry[$i]];
                        $ctid_str = implode(",", $ct_id);
                    } else {
                        $ctid_str = "";
                    }
                    //FILE UPLOAD STATUS
                    if (!file_exists($_FILES['lrcm_img']['tmp_name'][$i]) || !is_uploaded_file($_FILES['lrcm_img']['tmp_name'][$i])) {
                        //'No File Upload!';
                    } else {
                        $oldfileName = $_FILES['lrcm_img']['name'][$i];
                        /* GetRandNum() 3碼 */
                        $RandomNumber = GetRandNum();
                        $newfileName = $c_id . "_" . date("YmdHis") . $RandomNumber;
                        $tempFilePath = $_FILES['lrcm_img']['tmp_name'][$i];
                        $serverFilePath = "assets_rear/images/fb_push/" . $c_id . "/" . $_RandRoot;
                        $ThumbnailSize = 250;
                        $pictureFile = new BackEndPictureFileForWork($oldfileName, $newfileName, $tempFilePath, $serverFilePath, $ThumbnailSize);
                        $pictureFile->createFolder("assets_rear/images/fb_push");
                        $pictureFile->createFolder("assets_rear/images/fb_push/" . $c_id);
                        $pictureFile->createFolder($serverFilePath);
                        $originalImgName = $pictureFile->archiveWithoutReSizePictureFile();
                        $thumbnailImgName = $pictureFile->archiveWithReSizePictureFile();
                        $imagetype = $pictureFile->getFileNameExtension($oldfileName);

                        /* 上傳檔案到CDN */
                        $UploadDir = ROOT_PATH . $originalImgName;
                        $UploadDirthumbnail = ROOT_PATH . $thumbnailImgName;
                        $UploadCDN = CDN_FB_TARGETPUSH . $c_id . '/' . $_RandRoot . '/original' . $newfileName . '.' . $imagetype;
                        $UploadCDNthumbnail = CDN_FB_TARGETPUSH . $c_id . '/' . $_RandRoot . '/thumbnail' . $newfileName . '.' . $imagetype;
                        $s3->putObject($UploadDir, $UploadCDN);
                        $s3->putObject($UploadDirthumbnail, $UploadCDNthumbnail);

                        //檔案路徑
                        $lrct_img = $originalImgName;
                    }
                    $fpm_url = !empty($_REQUEST["fpm_url"][$i]) ? $_REQUEST["fpm_url"][$i] : "";
                    $fpmd_sort = $_REQUEST["fpmd_sort"][$i];
                    $deletestatus = "N";
                    /* INSERT DATA */
                    $sql = " INSERT INTO fb_push_messenger_d ";
                    $sql .= " ( fpmd_id, fpm_id, c_id, ct_id, fpmd_name,  ";
                    $sql .= " fpmd_content, fpmd_url, fpmd_img, fpmd_cdn_root, fpmd_push_type, ";
                    $sql .= " fpmd_sort, deletestatus, entry_datetime ) ";
                    $sql .= " VALUES ";
                    $sql .= " ( ?, ?, ?, ?, ?, ";
                    $sql .= " ?, ?, ?, ?, ?, ";
                    $sql .= " ?, ?, ? ) ";
                    $Create_Ary = array();
                    array_push($Create_Ary, $fpmd_id);
                    array_push($Create_Ary, $fpm_id);
                    array_push($Create_Ary, $c_id);
                    array_push($Create_Ary, $ctid_str);
                    array_push($Create_Ary, $fpm_name);
                    array_push($Create_Ary, $fpm_content);
                    array_push($Create_Ary, $fpm_url);
                    array_push($Create_Ary, $lrct_img);
                    array_push($Create_Ary, $UploadCDN);
                    array_push($Create_Ary, $fpm_push_type);
                    array_push($Create_Ary, $fpmd_sort);
                    array_push($Create_Ary, $deletestatus);
                    array_push($Create_Ary, $entry_datetime);
                    $mysqli->createPreSTMT($sql, "sssssssssssss", $Create_Ary);
                    array_push($gen_ary, new MessageElementList($fpm_name, $fpm_content, WEB_HOSTNAME . $lrct_img, [
                        new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => $fpm_url])
                            ], ""));
                }
                //立即建造template
                if ($fpm_send_status == '1') {
                    $content = new MessageCreatives(MessageCreatives::CREATIVES_TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_LIST, [
                        'top_element_style' => 'compact',
                        'elements' => $gen_ary
                    ]));
                    $response = $fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGE_CREATIVES, "me", $content);
                    $message_creative_id = $response['message_creative_id'];
                    $response2 = $fbApp->send(FBApp::API_TYPE_MESSENGER_BROADCAST_MESSAGES, "me", new Broadcast(Broadcast::MESSAGING_TYPE_MESSAGE_TAG, $message_creative_id, $Custom_label_id, Broadcast::TAG_NON_PROMOTIONAL_SUBSCRIPTION));
                    $broadcast_id = $response2['broadcast_id'];
                }
                break;
            case '6'://媒體
                $fpm_url = $_REQUEST["fpm_url"];
                if (!empty($_POST["ct_id"])) {
                    $ct_id = $_POST["ct_id"];
                    $ctid_str = implode(",", $ct_id);
                }
                if ($fpm_send_status == '1') {
                    $fpm_send_datetime = $entry_datetime;
                    $content = new MessageCreatives(MessageCreatives::CREATIVES_TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_MEDIA, [
                        'elements' => [
                            new MessageElementMedia(MessageElementMedia::MEDIA_TYPE_VIDEO, $fpm_url, [
                                new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "【影片連結】", "url" => $fpm_url])
                                    ])
                        ]
                            ]
                    ));
                    $response = $fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGE_CREATIVES, "me", $content);
                    $message_creative_id = $response['message_creative_id'];
                    $response2 = $fbApp->send(FBApp::API_TYPE_MESSENGER_BROADCAST_MESSAGES, "me", new Broadcast(Broadcast::MESSAGING_TYPE_MESSAGE_TAG, $message_creative_id, $Custom_label_id, Broadcast::TAG_NON_PROMOTIONAL_SUBSCRIPTION));
                    $broadcast_id = $response2['broadcast_id'];
                } else if ($fpm_send_status == '2') {
                    $fpm_reserve = "N";
                }
                $sql = "INSERT INTO fb_push_messenger ";
                $sql .= "( fpm_id, c_id, ct_id, fpm_name, fpm_content, ";
                $sql .= " fpm_url, fpm_img, fpm_cdn_root, fpm_send_status, fpm_send_date, ";
                $sql .= " fpm_push_type, fpm_target_type, fpm_reserve, fpm_broadcast_id, deletestatus,";
                $sql .= " entry_datetime )";
                $sql .= " VALUES ";
                $sql .= " (?, ?, ?, ?, ?, ";
                $sql .= " ?, ?, ?, ?, ?, ";
                $sql .= " ?, ?, ?, ?, ?, ";
                $sql .= " ? )";
                $Create_Ary = array();
                array_push($Create_Ary, $fpm_id);
                array_push($Create_Ary, $c_id);
                array_push($Create_Ary, $ctid_str);
                array_push($Create_Ary, $fpm_name);
                array_push($Create_Ary, $fpm_content);
                array_push($Create_Ary, $fpm_url);
                array_push($Create_Ary, $originalImgName);
                array_push($Create_Ary, $UploadCDN);
                array_push($Create_Ary, $fpm_send_status);
                array_push($Create_Ary, $fpm_send_datetime);
                array_push($Create_Ary, $fpm_push_type);
                array_push($Create_Ary, $fpm_target_type);
                array_push($Create_Ary, $fpm_reserve);
                array_push($Create_Ary, $broadcast_id);
                array_push($Create_Ary, $deletestatus);
                array_push($Create_Ary, $entry_datetime); //16
                $result = $mysqli->createPreSTMT_CreateId($sql, "ssssssssssssssss", $Create_Ary);
                break;
            default :
                break;
        }
    }
}

//回原本畫面
header("Location:$fileName.php");

function checkfpmid($id, $mysqli) {
    $sql = " select fpm_id from fb_push_messenger ";
    $sql .= " where fpm_id = ? ";
    $exist = $mysqli->readValuePreSTMT($sql, "s", array($id), 1);
    if ($exist) {
        $id = call_user_func("newid", $id, $mysqli);
    }
    return $id;
}

function newid($id, $mysqli) {
    return checkfpmid(md5(uniqid(rand(), true)), $mysqli);
}

function checkfpmdid($id, $mysqli) {
    $sql = " select fpmd_id from fb_push_messenger_d ";
    $sql .= " where fpmd_id = ? ";
    $exist = $mysqli->readValuePreSTMT($sql, "s", array($id), 1);
    if ($exist) {
        $id = call_user_func("newdid", $id, $mysqli);
    }
    return $id;
}

function newdid($id, $mysqli) {
    return checkfpmdid(md5(uniqid(rand(), true)), $mysqli);
}

?>
