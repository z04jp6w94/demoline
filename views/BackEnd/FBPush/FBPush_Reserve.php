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
ini_set('date.timezone', 'Asia/Taipei');
session_start();
//函式庫
include_once("/var/www/html/social_crm_2/web_root/config/chiliman_config.php");
/* CDN S3 */
require_once(AUTOLOAD_PATH);
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
$google_short = new GoogleShortAPI();
$bitly = new BitlyShortAPI();
//變數
$DateTime = date("Y-m-d H:i:s");
error_log("crontab: " . $DateTime);
//預約
$sql = " SELECT p_id, c_id, ct_id, p_name, p_content, ";
$sql .= " p_url, p_cdn_root, p_send_status, p_send_date, lp_type, ";
$sql .= " p_type ";
$sql .= " FROM push_m ";
$sql .= " WHERE 1 = 1 ";
$sql .= " AND p_send_status = '2' ";                //預約
$sql .= " AND p_send_date < '" . $DateTime . "' ";  //發送時間
$sql .= " AND p_reserve = 'N' ";                    //發送狀態
$sql .= " AND deletestatus = 'N' ";                 //刪除狀態
$sql .= " UNION ";
$sql .= " SELECT p_id, c_id, ct_id, p_name, p_content, ";
$sql .= " p_url, p_cdn_root, p_send_status, p_send_date, lp_type, ";
$sql .= " p_type ";
$sql .= " FROM push_m ";
$sql .= " WHERE 1 = 1 ";
$sql .= " AND p_send_status = '2' ";
$sql .= " AND p_send_date = '" . $DateTime . "' ";
$sql .= " AND p_reserve = 'N' ";
$sql .= " AND deletestatus = 'N' ";
$initAry = $mysqli->readArraySTMT($sql, 11);
//推文
$c_id = "";
for ($i = 0; $i < count($initAry); $i++) {
    if ($c_id == "" || $c_id != $initAry[$i][1]) {
        $c_id = $initAry[$i][1];
        /* 人員名單 */
        $sql = " select DISTINCT(mlm_lineid), c_id from member_list_m ";
        $sql .= " where c_id = ? ";
        $mlmAry = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);
        /* get token */
        $sql = " select c_line_TOKEN, c_line_SECRET, c_linelogin_CID from crm_m ";
        $sql .= " where c_id = ? ";
        $Line_Info = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 3);
        /* LINE */
        $access_token = $Line_Info[0][0];
        $secret = $Line_Info[0][1];
        /* LINE_LOGIN */
        $linelogin_id = $Line_Info[0][2];
    }
    /* LINE_BOT */
    $bot = new LINEBot(new \LINE\LINEBot\HTTPClient\CurlHTTPClient($access_token), ['channelSecret' => $secret]);
    //推文
    $p_id = $initAry[$i][0];
    $ct_id = $initAry[$i][2];
    $p_name = $initAry[$i][3];
    $p_content = $initAry[$i][4];
    $p_url = $initAry[$i][5];
    $p_cdn_img = $initAry[$i][6];
    $lp_type = $initAry[$i][9];
    $p_type = $initAry[$i][10];             //ALL,TAG

    if ($lp_type == '1') {
        $response_format_text = new TextMessageBuilder($p_name . "\n\n" . $p_content);
    } else if ($lp_type == '2') {
        $url = $bitly->BitlyShort("https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=" . $linelogin_id . "&redirect_uri=" . urlencode(WEB_HOSTNAME . "/linelogin.php") . "?para=" . rawurlencode(ENCCode($c_id . $p_id)) . "&state=" . md5('chiliman') . "&scope=openid%20profile");
        $response_format_text = new ImagemapMessageBuilder(
                CDN_ROOT_PATH . substr($p_cdn_img, 0, -1), $p_name, new BaseSizeBuilder(1040, 1040), [
            new ImagemapUriActionBuilder(
                    $url, new AreaBuilder(0, 0, 1040, 1040)
            )
                ]
        );
    } else if ($lp_type == '3') {
        $url = $bitly->BitlyShort("https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=" . $linelogin_id . "&redirect_uri=" . urlencode(WEB_HOSTNAME . "/linelogin.php") . "?para=" . rawurlencode(ENCCode($c_id . $p_id)) . "&state=" . md5('chiliman') . "&scope=openid%20profile");
        $response_format_text = new TemplateMessageBuilder(
                $p_name, new ButtonTemplateBuilder(
                $p_name, $p_content, CDN_ROOT_PATH . $p_cdn_img, [
            new UriTemplateActionBuilder("【點我去連結】", $url),
                ]
                )
        );
    }

    if ($p_type == "1") {
        for ($j = 0; $j < count($mlmAry); $j++) {
            $User = $mlmAry[$j][0];
            $bot->pushMessage($User, $response_format_text);
        }
    } else if ($p_type == "2") {
        $sql = " select DISTINCT(mlm_lineid), c_id from member_tag ";
        $sql .= " where ct_id in ($ct_id) ";
        $sql .= " and c_id = ? ";
        $mlmAry = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);

        for ($j = 0; $j < count($mlmAry); $j++) {
            $User = $mlmAry[$j][0];
            $bot->pushMessage($User, $response_format_text);
        }
    }
    //推文發送狀態
    $p_id = $initAry[$i][0];
    $sql = " UPDATE push_m SET p_reserve = 'Y' ";
    $sql .= " WHERE p_id = ? ";
    $sql .= " AND c_id = ? ";
    $mysqli->updatePreSTMT($sql, "ss", array($p_id, $c_id));
}
?>