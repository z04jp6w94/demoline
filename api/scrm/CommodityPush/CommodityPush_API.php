<?php

use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;
use SCRM\BOT\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
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
//$String = '{"pc_id":"12", "OA":"1"}';
/* Json */
if (!isJSON($httpRequestBody)) {
    exit();
}
$json = json_decode($httpRequestBody);
$pc_id = $json->{'pc_id'};
$OA = $json->{'OA'}; //官方全部 or tag
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
$sql = " select c_id, line_push_type, ct_id, cm_id, pc_cdn_root, ";
$sql .= " pc_content ";
$sql .= " from push_commodity ";
$sql .= " where pc_id = ? ";
$pc_ary = $mysqli->readArrayPreSTMT($sql, "s", array($pc_id), 6);
$c_id = $pc_ary[0][0];
$line_push_type = $pc_ary[0][1];
$ct_str = $pc_ary[0][2];
$cmid_str = $pc_ary[0][3];
$pc_cdn_root = $pc_ary[0][4];
$pc_content = $pc_ary[0][5];
/* 商品 */
$sql = " SELECT cm_id, cm_name, cm_intro, cm_img, cm_cdn_root ";
$sql .= " FROM commodity_m ";
$sql .= " WHERE cm_id IN ($cmid_str) ";
$sql .= " AND deletestatus = 'N' ";
$cm_ary = $mysqli->readArraySTMT($sql, 5);
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

if ($line_push_type == '1') {
    $cm_key = $cm_ary[0][0];
    $cm_name = $cm_ary[0][1];
    $url = $bitly->BitlyShort("https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=" . $linelogin_id . "&redirect_uri=" . urlencode(WEB_HOSTNAME . "/linelogin.php") . "?commdity_para=" . rawurlencode(ENCCode($c_id . $cm_key)) . "&state=" . md5('chiliman') . "&scope=openid%20profile");
    $response_format_text = new ImagemapMessageBuilder(
            CDN_ROOT_PATH . substr($pc_cdn_root, 0, -1), $cm_name, new BaseSizeBuilder(1040, 1040), [
        new ImagemapUriActionBuilder(
                $url, new AreaBuilder(0, 0, 1040, 1040)
        )
            ]
    );
} else if ($line_push_type == '2') {
    $cm_key = $cm_ary[0][0];
    $cm_name = $cm_ary[0][1];
    $cm_intro = $cm_ary[0][2];
    $cm_cdn_img = $cm_ary[0][4];
    $url = $bitly->BitlyShort("https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=" . $linelogin_id . "&redirect_uri=" . urlencode(WEB_HOSTNAME . "/linelogin.php") . "?commdity_para=" . rawurlencode(ENCCode($c_id . $cm_key)) . "&state=" . md5('chiliman') . "&scope=openid%20profile");
    $response_format_text = new TemplateMessageBuilder(
            $cm_name, new ButtonTemplateBuilder(
            $cm_name, $cm_intro, CDN_ROOT_PATH . $cm_cdn_img, [
        new UriTemplateActionBuilder("【點我去連結】", $url),
            ]
            )
    );
} else if ($line_push_type == '3') {
    $builders = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
    $columns = Array();
    for ($i = 0; $i < count($cm_ary); $i++) {
        $cm_key = $cm_ary[$i][0];
        $cm_name = $cm_ary[$i][1];
        $cm_intro = $cm_ary[$i][2];
        $cm_cdn_img = $cm_ary[$i][4];
        $url = $bitly->BitlyShort("https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=" . $linelogin_id . "&redirect_uri=" . urlencode(WEB_HOSTNAME . "/linelogin.php") . "?commdity_para=" . rawurlencode(ENCCode($c_id . $cm_key)) . "&state=" . md5('chiliman') . "&scope=openid%20profile");
        $actionArray = array();
        array_push($actionArray, new UriTemplateActionBuilder('【點擊我】', $url));
        $column = new CarouselColumnTemplateBuilder(
                $cm_name, $cm_intro, CDN_ROOT_PATH . $cm_cdn_img, $actionArray
        );
        array_push($columns, $column);
        if ($i == 4 || $i == count($cm_ary) - 1) {
            $builder = new TemplateMessageBuilder(
                    $cm_name, new CarouselTemplateBuilder($columns)
            );
            $builders->add($builder);
            unset($columns);
            $columns = Array();
        }
    }
    $response_format_text = $builders;
}
if ($OA == "") {
    $sql = " SELECT DISTINCT(mlm_lineid), c_id FROM member_tag ";
    $sql .= " WHERE ct_id in ($ct_str) ";
    $sql .= " AND c_id = ? ";
} else {
    $sql = " SELECT DISTINCT(mlm_lineid), c_id FROM member_list_m ";
    $sql .= " WHERE c_id = ? ";
    $sql .= " AND mlm_source = '1' ";
}
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
exit();
?>