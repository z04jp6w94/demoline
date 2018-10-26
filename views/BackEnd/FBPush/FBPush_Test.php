<?php

use FB\FBApp;
use FB\FBApp\Builder\MessengerBuilder\Component\MessageButton;
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

/* DateNow */
$_RandRoot = date("Ym");

//資料庫連線
$mysqli = new DatabaseProcessorForWork();
$google_short = new GoogleShortAPI();
$bitly = new BitlyShortAPI();
//取得新增參數
$sql = "SELECT fpm_name, fpm_content FROM fb_push_messenger WHERE fpm_id = '9cca9fcea467ee6303ae5272d611daeb'";
$re = $mysqli->readArraySTMT($sql, 2);
$fpm_name = $re[0][0];
$fpm_push_type = '1';
$fpm_content = $re[0][1];
$fpm_content = json_decode($fpm_content);
$fpm_send_status = '1';

//定義時間參數
$excuteDateTime = date("Y-m-d H:i:s"); //操作日期
$originalImgName = "";
$thumbnailImgName = "";
$UploadCDN = "";
$Custom_label_id = "1944614522225852";
/* get token */
$sql = " select c_fb_appid, c_fb_secret, c_fb_token, c_fb_patch, c_fb_fans ";
$sql .= " FROM crm_m ";
$sql .= " WHERE c_id = ? ";
$FB_Info = $mysqli->readArrayPreSTMT($sql, "s", array('1234567899'), 5);
$fbApp = new FBApp($FB_Info[0][0], $FB_Info[0][1], $FB_Info[0][2], $FB_Info[0][3]);
$fbpg_id = $FB_Info[0][4];
//發送狀態
switch ($fpm_push_type) {
    case '1'://文字
        if ($fpm_send_status == '1') {
            $fpm_send_datetime = $excuteDateTime;
            $content = new MessageCreatives(MessageCreatives::CREATIVES_TYPE_TEXT, new DynamicText($fpm_name . PHP_EOL . $fpm_content, $fpm_name . PHP_EOL . $fpm_content));
            $response = $fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGE_CREATIVES, "me", $content);
            $message_creative_id = $response['message_creative_id'];
            $response2 = $fbApp->send(FBApp::API_TYPE_MESSENGER_BROADCAST_MESSAGES, "me", new Broadcast(Broadcast::MESSAGING_TYPE_MESSAGE_TAG, $message_creative_id, $Custom_label_id, Broadcast::TAG_NON_PROMOTIONAL_SUBSCRIPTION));
            $broadcast_id = $response2['broadcast_id'];
            echo $broadcast_id."<br>";
            echo $fpm_content;
        } else if ($fpm_send_status == '2') {
            $fpm_reserve = "N";
        }
        break;
    case '2'://圖像
        //圖檔處理
        $oldfileName = $_FILES['fpm_img']['name'];
        $newfileName = $c_id . "_" . date("YmdHis");
        $tempFilePath = $_FILES['fpm_img']['tmp_name'];
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
        $UploadCDN = CDN_FB_PUSH . $c_id . '/' . $_RandRoot . '/original' . $newfileName . '.' . $imagetype;
        $UploadCDNthumbnail = CDN_FB_PUSH . $c_id . '/' . $_RandRoot . '/thumbnail' . $newfileName . '.' . $imagetype;
        $s3->putObject($UploadDir, $UploadCDN);
        $s3->putObject($UploadDirthumbnail, $UploadCDNthumbnail);
        if ($fpm_send_status == '1') {
            $fpm_send_datetime = $excuteDateTime;
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
        $sql .= " fpm_push_type, fpm_target_type, fpm_reserve, deletestatus, entry_datetime )";
        $sql .= " VALUES ";
        $sql .= " (?, ?, ?, ?, ?, ";
        $sql .= " ?, ?, ?, ?, ?, ";
        $sql .= " ?, ?, ?, ?, ? )";
        $result = $mysqli->createPreSTMT_CreateId($sql, "sssssssssssssss", array($fpm_id, $c_id, $ctid_str, $fpm_name, $fpm_content, $fpm_url, $originalImgName, $UploadCDN, $fpm_send_status, $fpm_send_datetime, $fpm_push_type, $fpm_target_type, $fpm_reserve, $deletestatus, $excuteDateTime));
        break;
    case '3'://輪播
        //圖檔處理
        $oldfileName = $_FILES['fpm_img']['name'];
        $newfileName = $c_id . "_" . date("YmdHis");
        $tempFilePath = $_FILES['fpm_img']['tmp_name'];
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
        $UploadCDN = CDN_FB_PUSH . $c_id . '/' . $_RandRoot . '/original' . $newfileName . '.' . $imagetype;
        $UploadCDNthumbnail = CDN_FB_PUSH . $c_id . '/' . $_RandRoot . '/thumbnail' . $newfileName . '.' . $imagetype;
        $s3->putObject($UploadDir, $UploadCDN);
        $s3->putObject($UploadDirthumbnail, $UploadCDNthumbnail);
        if ($fpm_send_status == '1') {
            $fpm_send_datetime = $excuteDateTime;
            $content = new MessageCreatives(MessageCreatives::CREATIVES_TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_GENERIC, [
                'elements' => [
                    new MessageElementGeneric($fpm_name, $fpm_content, "", WEB_HOSTNAME . $originalImgName, [
                        new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => $fpm_url])
                            ])
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
        $sql .= " fpm_push_type, fpm_target_type, fpm_reserve, deletestatus, entry_datetime )";
        $sql .= " VALUES ";
        $sql .= " (?, ?, ?, ?, ?, ";
        $sql .= " ?, ?, ?, ?, ?, ";
        $sql .= " ?, ?, ?, ?, ? )";
        $result = $mysqli->createPreSTMT_CreateId($sql, "sssssssssssssss", array($fpm_id, $c_id, $ctid_str, $fpm_name, $fpm_content, $fpm_url, $originalImgName, $UploadCDN, $fpm_send_status, $fpm_send_datetime, $fpm_push_type, $fpm_target_type, $fpm_reserve, $deletestatus, $excuteDateTime));
        break;
    case '4'://文字+按鈕
        if ($fpm_send_status == '1') {
            $fpm_send_datetime = $excuteDateTime;
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
        $sql .= " fpm_push_type, fpm_target_type, fpm_reserve, deletestatus, entry_datetime )";
        $sql .= " VALUES ";
        $sql .= " (?, ?, ?, ?, ?, ";
        $sql .= " ?, ?, ?, ?, ?, ";
        $sql .= " ?, ?, ?, ?, ? )";
        $result = $mysqli->createPreSTMT_CreateId($sql, "sssssssssssssss", array($fpm_id, $c_id, $ctid_str, $fpm_name, $fpm_content, $fpm_url, $originalImgName, $UploadCDN, $fpm_send_status, $fpm_send_datetime, $fpm_push_type, $fpm_target_type, $fpm_reserve, $deletestatus, $excuteDateTime));
        break;
    case '5'://清單型
        //圖檔處理
        $oldfileName = $_FILES['fpm_img']['name'];
        $newfileName = $c_id . "_" . date("YmdHis");
        $tempFilePath = $_FILES['fpm_img']['tmp_name'];
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
        $UploadCDN = CDN_FB_PUSH . $c_id . '/' . $_RandRoot . '/original' . $newfileName . '.' . $imagetype;
        $UploadCDNthumbnail = CDN_FB_PUSH . $c_id . '/' . $_RandRoot . '/thumbnail' . $newfileName . '.' . $imagetype;
        $s3->putObject($UploadDir, $UploadCDN);
        $s3->putObject($UploadDirthumbnail, $UploadCDNthumbnail);
        if ($fpm_send_status == '1') {
            $fpm_send_datetime = $excuteDateTime;
            $content = new MessageCreatives(MessageCreatives::CREATIVES_TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_LIST, [
                'top_element_style' => 'compact',
                'elements' => [
                    new MessageElementList($fpm_name, $fpm_content, WEB_HOSTNAME . $originalImgName, [
                        new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => $fpm_url])
                            ], new MessageButton(MessageButton::TYPE_WEB_URL, ["url" => $fpm_url])),
                    new MessageElementList($fpm_name, $fpm_content, WEB_HOSTNAME . $originalImgName, [
                        new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => $fpm_url])
                            ], new MessageButton(MessageButton::TYPE_WEB_URL, ["url" => $fpm_url]))
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
        $sql .= " fpm_push_type, fpm_target_type, fpm_reserve, deletestatus, entry_datetime )";
        $sql .= " VALUES ";
        $sql .= " (?, ?, ?, ?, ?, ";
        $sql .= " ?, ?, ?, ?, ?, ";
        $sql .= " ?, ?, ?, ?, ? )";
        $result = $mysqli->createPreSTMT_CreateId($sql, "sssssssssssssss", array($fpm_id, $c_id, $ctid_str, $fpm_name, $fpm_content, $fpm_url, $originalImgName, $UploadCDN, $fpm_send_status, $fpm_send_datetime, $fpm_push_type, $fpm_target_type, $fpm_reserve, $deletestatus, $excuteDateTime));
        break;
    case '6'://媒體
        if ($fpm_send_status == '1') {
            $fpm_send_datetime = $excuteDateTime;
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
        $sql .= " fpm_push_type, fpm_target_type, fpm_reserve, deletestatus, entry_datetime )";
        $sql .= " VALUES ";
        $sql .= " (?, ?, ?, ?, ?, ";
        $sql .= " ?, ?, ?, ?, ?, ";
        $sql .= " ?, ?, ?, ?, ? )";
        $result = $mysqli->createPreSTMT_CreateId($sql, "sssssssssssssss", array($fpm_id, $c_id, $ctid_str, $fpm_name, $fpm_content, $fpm_url, $originalImgName, $UploadCDN, $fpm_send_status, $fpm_send_datetime, $fpm_push_type, $fpm_target_type, $fpm_reserve, $deletestatus, $excuteDateTime));
        break;
    default :
        break;
}
?>
