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
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
/* get token */
$sql = " select c_fb_appid, c_fb_secret, c_fb_token, c_fb_patch, c_fb_fans ";
$sql .= " FROM crm_m ";
$sql .= " WHERE c_id = ? ";
$FB_Info = $mysqli->readArrayPreSTMT($sql, "s", array("1234567899"), 5);
$fbApp = new FBApp($FB_Info[0][0], $FB_Info[0][1], $FB_Info[0][2], $FB_Info[0][3]);
$lrct_img = "";
$lrct_cdn_root = "";
$Custom_label_id = "1944614522225852";
$gen_ary = array();
$fpm_url = "https://www.facebook.com/Chiliman.powerline/";
for ($i = 1; $i < 3; $i++) {
    $fpm_name = "abc".$i."個";
    $fpm_content = "內容".$i."個";
//FILE UPLOAD STATUS
//檔案路徑
    $lrct_img = "/assets_rear/images/fb_target_push/1234567899/201805/original1234567899_20180510161340024.jpg";
    //$gen_ary[] = new MessageElementGeneric($fpm_name, $fpm_content, "", WEB_HOSTNAME . $lrct_img, [new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我".$i."個", "url" => $fpm_url])]);
    array_push($gen_ary, new MessageElementGeneric($fpm_name, $fpm_content, "", WEB_HOSTNAME . $lrct_img, [new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我".$i."個", "url" => $fpm_url])]));
}
echo var_dump($gen_ary);
$content = new MessageCreatives(MessageCreatives::CREATIVES_TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_GENERIC, [
    'elements' => 
        $gen_ary
    
        ]));
$response = $fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGE_CREATIVES, "me", $content);
$message_creative_id = $response['message_creative_id'];
echo $message_creative_id;
$response2 = $fbApp->send(FBApp::API_TYPE_MESSENGER_BROADCAST_MESSAGES, "me", new Broadcast(Broadcast::MESSAGING_TYPE_MESSAGE_TAG, $message_creative_id, $Custom_label_id, Broadcast::TAG_NON_PROMOTIONAL_SUBSCRIPTION));
$broadcast_id = $response2['broadcast_id'];
echo $broadcast_id;
?>
