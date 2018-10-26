<?php

use FB\FBApp;
use FB\FBApp\Builder\MessengerBuilder\Component\MessageButton;
use FB\FBApp\Builder\MessengerBuilder\Component\MessageElement;
use FB\FBApp\Builder\MessengerBuilder\Component\MessageElement\MessageElementGeneric;
use FB\FBApp\Builder\MessengerBuilder\Component\MessageElement\MessageElementList;
use FB\FBApp\Builder\MessengerBuilder\Component\MessageElement\MessageElementMedia;
use FB\FBApp\Builder\MessengerBuilder\Component\QuickReplyButton;
use FB\FBApp\Builder\MessengerBuilder\MessageAttachmentUpload;
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
/* LINE */
require_once(AUTOLOAD_PATH);
//判斷是否登入
if (!isset($_SESSION["user_id"])) {
    BackToLoginPage();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
    $FilePath = !empty($_COOKIE["FilePath"]) ? $_COOKIE["FilePath"] : NULL;
    $fileName = basename($FilePath, '.php');
    //來源判斷
    $url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
    $source_url = dirname($url) . '/' . $fileName . '_Target.php';
    if ($_SERVER['HTTP_REFERER'] != $source_url) {
        BackToLoginPage();
        exit;
    }
    $dataKey = $_REQUEST["dataKey"];
    $fpm_push_type = $_REQUEST["fpm_push_type"];
    $ct_id = $_POST["ct_id"];
    $ct_str = implode(",", $ct_id);
//定義時間參數
    $excuteDateTime = date("Y-m-d H:i:s"); //操作日期
//資料庫連線
    $mysqli = new DatabaseProcessorForWork();
//Google
    $google_short = new GoogleShortAPI();
    $bitly = new BitlyShortAPI();
    /* get token */
    $sql = " select c_fb_appid, c_fb_secret, c_fb_token, c_fb_patch, c_fb_fans ";
    $sql .= " FROM crm_m ";
    $sql .= " WHERE c_id = ? ";
    $FB_Info = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 5);
    $fbApp = new FBApp($FB_Info[0][0], $FB_Info[0][1], $FB_Info[0][2], $FB_Info[0][3]);
    $fbpg_id = $FB_Info[0][4];
    $new_fpm_id = checkfpmid(md5(uniqid(rand(), true)), $mysqli);
    $fpm_reserve = "Y";
    $deletestatus = "N";
    $fpm_target_type = "1";
    $fpm_send_status = "1";
    $entry_datetime = date("Y-m-d H:i:s");
    $fpm_send_datetime = $entry_datetime;
    $broadcast_id = "";
    //資料庫
    $sql = " SELECT DISTINCT(mlm_messengerid), c_id  ";
    $sql .= " FROM fb_member_tag ";
    $sql .= " WHERE ct_id IN ($ct_str) ";
    $sql .= " AND c_id = ? ";
    $mlmAry = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);

    $sql = "SELECT fpm_id, ct_id, fpm_name, fpm_content, fpm_url, ";
    $sql .= " fpm_img, fpm_cdn_root ";
    $sql .= " FROM fb_push_messenger ";
    $sql .= " WHERE fpm_id = ?";
    $sql .= " AND c_id = ? ";
    $sql .= " AND deletestatus = 'N' ";
    $fbpmAry = $mysqli->readArrayPreSTMT($sql, "ss", array($dataKey, $c_id), 7);

    //發送狀態
    switch ($fpm_push_type) {
        case '1'://文字
            $fpm_name = $fbpmAry[0][2];
            $fpm_content = $fbpmAry[0][3];
            $fpm_url = $fbpmAry[0][4];
            $originalImgName = $fbpmAry[0][5];
            $UploadCDN = $fbpmAry[0][6];
            for ($i = 0; $i < count($mlmAry); $i++) {
                $senderId = $mlmAry[$i][0];
                $fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageText(MessageText::MESSAGING_TYPE_RESPONSE, $senderId, $fpm_name . PHP_EOL . $fpm_content));
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
            array_push($Create_Ary, $new_fpm_id); //新的PK
            array_push($Create_Ary, $c_id);
            array_push($Create_Ary, $ct_str); //上新的tag
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
            $mysqli->createPreSTMT_CreateId($sql, "ssssssssssssssss", $Create_Ary);
            break;
        case '2'://圖像                    
            $fpm_name = $fbpmAry[0][2];
            $fpm_content = $fbpmAry[0][3];
            $fpm_url = $fbpmAry[0][4];
            $originalImgName = $fbpmAry[0][5];
            $UploadCDN = $fbpmAry[0][6];
            $result = $fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGE_ATTACHMENTS, "me", new MessageAttachmentUpload(MessageAttachmentUpload::TYPE_IMAGE_UPLOAD, new Image(Image::TYPE_URL, CDN_ROOT_PATH . $UploadCDN)));
            $attachment_id = $result['attachment_id'];
            for ($i = 0; $i < count($mlmAry); $i++) {
                $senderId = $mlmAry[$i][0];
                $fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_IMAGE, new Image(Image::TYPE_ATTACHMENT_ID, $attachment_id)));
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
            array_push($Create_Ary, $new_fpm_id); //新的PK
            array_push($Create_Ary, $c_id);
            array_push($Create_Ary, $ct_str); //上新的tag
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
            $mysqli->createPreSTMT_CreateId($sql, "ssssssssssssssss", $Create_Ary);
            break;
        case '3'://輪播
            $fpm_name = $fbpmAry[0][2];
            $fpm_content = $fbpmAry[0][3];
            $fpm_url = $fbpmAry[0][4];
            $originalImgName = $fbpmAry[0][5];
            $UploadCDN = $fbpmAry[0][6];

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
            array_push($Create_Ary, $new_fpm_id); //新的PK
            array_push($Create_Ary, $c_id);
            array_push($Create_Ary, $ct_str); //上新的tag
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
            $mysqli->createPreSTMT_CreateId($sql, "ssssssssssssssss", $Create_Ary);
            //明細
            $sql = "SELECT fpmd_name, fpmd_content, fpmd_url, fpmd_img, fpmd_cdn_root, ";
            $sql .= " fpmd_sort ";
            $sql .= " FROM fb_push_messenger_d ";
            $sql .= " WHERE fpm_id = ?";
            $sql .= " AND deletestatus = 'N' ";
            $sql .= " ORDER BY fpmd_sort ";
            $fbpmdAry = $mysqli->readArrayPreSTMT($sql, "s", array($dataKey), 6);
            $gen_ary = array();
            for ($i = 0; $i < count($fbpmdAry); $i++) {
                $new_fpmd_id = checkfpmdid(md5(uniqid(rand(), true)), $mysqli);
                $fpmd_name = $fbpmdAry[$i][0];
                $fpmd_content = $fbpmdAry[$i][1];
                $fpmd_url = $fbpmdAry[$i][2];
                $fpmd_img = $fbpmdAry[$i][3];
                $fpmd_cdn_root = $fbpmdAry[$i][4];
                $fpmd_sort = $fbpmdAry[$i][5];
                /* INSERT DATA */
                $sql = " INSERT INTO fb_push_messenger_d ";
                $sql .= " ( fpmd_id, fpm_id, c_id, ct_id, fpmd_name, ";
                $sql .= " fpmd_content, fpmd_url, fpmd_img, fpmd_cdn_root, fpmd_push_type, ";
                $sql .= " fpmd_sort, deletestatus, entry_datetime ) ";
                $sql .= " VALUES ";
                $sql .= " ( ?, ?, ?, ?, ?, ";
                $sql .= " ?, ?, ?, ?, ?, ";
                $sql .= " ?, ?, ? ) ";
                $Create_Ary = array();
                array_push($Create_Ary, $new_fpmd_id);
                array_push($Create_Ary, $new_fpm_id);
                array_push($Create_Ary, $c_id);
                array_push($Create_Ary, $ct_str);
                array_push($Create_Ary, $fpmd_name);
                array_push($Create_Ary, $fpmd_content);
                array_push($Create_Ary, $fpmd_url);
                array_push($Create_Ary, $fpmd_img);
                array_push($Create_Ary, $fpmd_cdn_root);
                array_push($Create_Ary, $fpm_push_type);
                array_push($Create_Ary, $fpmd_sort);
                array_push($Create_Ary, $deletestatus);
                array_push($Create_Ary, $entry_datetime);
                $mysqli->createPreSTMT($sql, "sssssssssssss", $Create_Ary);
                array_push($gen_ary, new MessageElementGeneric($fpmd_name, $fpmd_content, "", CDN_ROOT_PATH . $fpmd_cdn_root, [new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => $fpmd_url])]));
            }
            for ($i = 0; $i < count($mlmAry); $i++) {
                $senderId = $mlmAry[$i][0];
                $fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_GENERIC, [
                    'elements' => $gen_ary
                ])));
            }
            break;
        case '4'://文字+按鈕
            $fpm_name = $fbpmAry[0][2];
            $fpm_content = $fbpmAry[0][3];
            $fpm_url = $fbpmAry[0][4];
            $originalImgName = $fbpmAry[0][5];
            $UploadCDN = $fbpmAry[0][6];

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
            array_push($Create_Ary, $new_fpm_id); //新的PK
            array_push($Create_Ary, $c_id);
            array_push($Create_Ary, $ct_str); //上新的tag
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
            $mysqli->createPreSTMT_CreateId($sql, "ssssssssssssssss", $Create_Ary);

            for ($i = 0; $i < count($mlmAry); $i++) {
                $senderId = $mlmAry[$i][0];
                $fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_BUTTON, [
                    'text' => $fpm_name . PHP_EOL . $fpm_content,
                    'buttons' => [
                        new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "【點擊我】", "url" => $fpm_url])
                    ]
                ])));
            }
            break;
        case '5'://清單型
            $fpm_name = $fbpmAry[0][2];
            $fpm_content = $fbpmAry[0][3];
            $fpm_url = $fbpmAry[0][4];
            $originalImgName = $fbpmAry[0][5];
            $UploadCDN = $fbpmAry[0][6];

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
            array_push($Create_Ary, $new_fpm_id); //新的PK
            array_push($Create_Ary, $c_id);
            array_push($Create_Ary, $ct_str); //上新的tag
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
            $mysqli->createPreSTMT_CreateId($sql, "ssssssssssssssss", $Create_Ary);
            //明細
            $sql = "SELECT fpmd_name, fpmd_content, fpmd_url, fpmd_img, fpmd_cdn_root, ";
            $sql .= " fpmd_sort ";
            $sql .= " FROM fb_push_messenger_d ";
            $sql .= " WHERE fpm_id = ?";
            $sql .= " AND deletestatus = 'N' ";
            $sql .= " ORDER BY fpmd_sort ";
            $fbpmdAry = $mysqli->readArrayPreSTMT($sql, "s", array($dataKey), 6);
            $gen_ary = array();
            for ($i = 0; $i < count($fbpmdAry); $i++) {
                $new_fpmd_id = checkfpmdid(md5(uniqid(rand(), true)), $mysqli);
                $fpmd_name = $fbpmdAry[$i][0];
                $fpmd_content = $fbpmdAry[$i][1];
                $fpmd_url = $fbpmdAry[$i][2];
                $fpmd_img = $fbpmdAry[$i][3];
                $fpmd_cdn_root = $fbpmdAry[$i][4];
                $fpmd_sort = $fbpmdAry[$i][5];
                /* INSERT DATA */
                $sql = " INSERT INTO fb_push_messenger_d ";
                $sql .= " ( fpmd_id, fpm_id, c_id, ct_id, fpmd_name, ";
                $sql .= " fpmd_content, fpmd_url, fpmd_img, fpmd_cdn_root, fpmd_push_type, ";
                $sql .= " fpmd_sort, deletestatus, entry_datetime ) ";
                $sql .= " VALUES ";
                $sql .= " ( ?, ?, ?, ?, ?, ";
                $sql .= " ?, ?, ?, ?, ?, ";
                $sql .= " ?, ?, ? ) ";
                $Create_Ary = array();
                array_push($Create_Ary, $new_fpmd_id);
                array_push($Create_Ary, $new_fpm_id);
                array_push($Create_Ary, $c_id);
                array_push($Create_Ary, $ct_str);
                array_push($Create_Ary, $fpmd_name);
                array_push($Create_Ary, $fpmd_content);
                array_push($Create_Ary, $fpmd_url);
                array_push($Create_Ary, $fpmd_img);
                array_push($Create_Ary, $fpmd_cdn_root);
                array_push($Create_Ary, $fpm_push_type);
                array_push($Create_Ary, $fpmd_sort);
                array_push($Create_Ary, $deletestatus);
                array_push($Create_Ary, $entry_datetime);
                $mysqli->createPreSTMT($sql, "sssssssssssss", $Create_Ary);
                array_push($gen_ary, new MessageElementList($fpmd_name, $fpmd_content, CDN_ROOT_PATH . $fpmd_cdn_root, [
                    new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => $fpmd_url])
                        ], ""));
            }
            for ($i = 0; $i < count($mlmAry); $i++) {
                $senderId = $mlmAry[$i][0];
                $fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_LIST, [
                    'top_element_style' => 'compact',
                    'elements' => $gen_ary
                ])));
            }
            break;
        case '6'://媒體
            $fpm_name = $fbpmAry[0][2];
            $fpm_content = $fbpmAry[0][3];
            $fpm_url = $fbpmAry[0][4];
            $originalImgName = $fbpmAry[0][5];
            $UploadCDN = $fbpmAry[0][6];

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
            array_push($Create_Ary, $new_fpm_id); //新的PK
            array_push($Create_Ary, $c_id);
            array_push($Create_Ary, $ct_str); //上新的tag
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
            $mysqli->createPreSTMT_CreateId($sql, "ssssssssssssssss", $Create_Ary);

            for ($i = 0; $i < count($mlmAry); $i++) {
                $senderId = $mlmAry[$i][0];
                $fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_MEDIA, [
                    'elements' => [
                        new MessageElementMedia(MessageElementMedia::MEDIA_TYPE_VIDEO, $fpm_url, [
                            new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "【影片連結】", "url" => $fpm_url])
                                ])
                    ]
                        ]
                )));
            }
            break;
        default :
            break;
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
