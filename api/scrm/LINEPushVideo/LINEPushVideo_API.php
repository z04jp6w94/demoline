<?php

use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use SCRM\BOT\ButtonTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('date.timezone', 'Asia/Taipei');
ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . '/assets_rear/session/');
session_start();
//函式庫
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/chiliman_config.php");
//Composer
require_once(AUTOLOAD_PATH);
//GET 
$httpRequestBody = file_get_contents('php://input');
//$String = '{"p_id":"12"}';
/* Json */
if (!isJSON($httpRequestBody)) {
    exit();
}
$json = json_decode($httpRequestBody);
$lpv_id = $json->{'lpv_id'}; 
/* DateTimeNow */
$DateTimeRoot = date("YmdHis");
//Erase the output buffer
ob_start();
header('Content-Length: ' . ob_get_length());
header('Connection: close');
ob_end_flush();
ob_flush();
flush();
//資料庫連線
$mysqli = new DatabaseProcessorForChiliman();
$bitly = new BitlyShortAPI();
/* 取值 */
$sql = " SELECT c_id, lpv_img_cdn_root, lpv_video_cdn_root ";
$sql .= " FROM line_push_video ";
$sql .= " WHERE lpv_id = ? ";
$lpv_ary = $mysqli->readArrayPreSTMT($sql, "s", array($lpv_id), 3);
$c_id = $lpv_ary[0][0];
/* get token */
$sql = " select c_line_TOKEN, c_line_SECRET, c_linelogin_CID from crm_m ";
$sql .= " where c_id = ? ";
$Line_Info = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 3);
/* LINE */
$access_token = $Line_Info[0][0];
$secret = $Line_Info[0][1];
/* LINE_LOGIN */
$linelogin_id = $Line_Info[0][2];
/* LINE_LOGIN */
$bot = new LINEBot(new \LINE\LINEBot\HTTPClient\CurlHTTPClient($access_token), ['channelSecret' => $secret]);
//DATA
$img_cdn_root = $lpv_ary[0][1];
$video_cdn_root = $lpv_ary[0][2];
$response_format_text = new VideoMessageBuilder(CDN_ROOT_PATH . $video_cdn_root, CDN_ROOT_PATH . $img_cdn_root);
$sql = " SELECT DISTINCT(mlm_lineid), c_id FROM member_list_m ";
$sql .= " WHERE c_id = ? ";
$sql .= " AND mlm_source = '1' ";
$mlmAry = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);

$member_count = count($mlmAry);
$multicastAry_count = $member_count % 150;
if ($multicastAry_count != 0) {
    $action_count = (int) (intval($member_count / 150) + 1);
} else {
    $action_count = $member_count / 150;
}

$actionArray = array();
for ($i = 0; $i < $action_count; $i++) {
    if ($action_count <= "1") {
        $stable_row = (int) ($i) * 150;
        $member_row = (int) ($i + 1) * 150;
        for ($j = $stable_row; $j < $member_row; $j++) {
            $member_person = !empty($mlmAry[$j][0]) ? $mlmAry[$j][0] : "";
            if ($member_person != "") {
                array_push($actionArray, $mlmAry[$j][0]);
            }
        }
    } else {
        $stable_row = (int) ($i) * 150;
        $member_row = (int) ($i + 1) * 150;
        if ($stable_row < $member_count) {
            for ($j = $stable_row; $j < $member_row; $j++) {
                $member_person = !empty($mlmAry[$j][0]) ? $mlmAry[$j][0] : "";
                if ($member_person != "") {
                    array_push($actionArray, $mlmAry[$j][0]);
                }
            }
        }
    }
    $bot->multicast($actionArray, $response_format_text);
    $actionArray = array();
}
?>