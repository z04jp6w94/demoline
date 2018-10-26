<?php

use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
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
    $source_url = dirname($url) . '/' . $fileName . '_EditMessage.php';
    if ($_SERVER['HTTP_REFERER'] != $source_url) {
        BackToLoginPage();
        exit;
    } else {
//資料庫連線
        $mysqli = new DatabaseProcessorForWork();
//取得新增參數
        $dataKey = $_REQUEST["dataKey"];
        $lineid = $_REQUEST["lineid"];
        $send_message = $_REQUEST["send_message"];
        /* Turn */
        $dataKey = DECCode($dataKey);
        $lineid = DECCode($lineid);
//定義時間參數
        $excuteDateTime = date("Y-m-d H:i:s"); //操作日期
        /* get token */
        $sql = " select c_line_TOKEN, c_line_SECRET, c_linelogin_CID from crm_m ";
        $sql .= " where c_id = ? ";
        $Line_Info = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 3);
        /* LINE */
        $access_token = $Line_Info[0][0];
        $secret = $Line_Info[0][1];

        $bot = new LINEBot(new \LINE\LINEBot\HTTPClient\CurlHTTPClient($access_token), ['channelSecret' => $secret]);
        $response_format_text = new TextMessageBuilder($send_message);
        $bot->pushMessage($lineid, $response_format_text);
    }
}
exit();
?>
