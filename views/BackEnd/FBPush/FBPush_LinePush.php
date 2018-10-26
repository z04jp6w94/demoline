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
    $ct_id = $_POST["ct_id"];
    $ct_str = implode(",", $ct_id);
//定義時間參數
    $excuteDateTime = date("Y-m-d H:i:s"); //操作日期
//資料庫連線
    $mysqli = new DatabaseProcessorForWork();
//Google
    $google_short = new GoogleShortAPI();
    $bitly = new BitlyShortAPI();
    /* Line */
    $sql = " select c_line_TOKEN, c_line_SECRET from crm_m ";
    $sql .= " where c_id = ? ";
    $Line_Info = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);
    $access_token = $Line_Info[0][0];
    $secret = $Line_Info[0][1];
    $bot = new LINEBot(new \LINE\LINEBot\HTTPClient\CurlHTTPClient($access_token), ['channelSecret' => $secret]);
    /* Data */
    $sql = " Insert into push_m ";
    $sql .= " (c_id, cp_id, ct_id, p_name, p_content, ";
    $sql .= " p_url, p_img, p_cdn_root, p_send_status, p_send_date, ";
    $sql .= " lp_type, p_type, deletestatus, entry_datetime) ";
    $sql .= " select c_id, cp_id, ct_id, p_name, p_content, ";
    $sql .= " p_url, p_img, p_cdn_root, '1', ?, ";
    $sql .= " lp_type, p_type, 'N', ? ";
    $sql .= " from push_m ";
    $sql .= " where p_id = ? ";
    $p_id = $mysqli->createPreSTMT_CreateId($sql, "sss", array($excuteDateTime, $excuteDateTime, $dataKey));

    $sql = "SELECT p_name, p_content, p_url, p_img, p_cdn_root, lp_type  ";
    $sql .= " FROM push_m ";
    $sql .= " WHERE p_id = ?";
    $sql .= " AND c_id = ? ";
    $p_ary = $mysqli->readArrayPreSTMT($sql, "ss", array($p_id, $c_id), 6);

    $sql = " select DISTINCT(mlm_lineid), c_id from member_tag ";
    $sql .= " where ct_id in ($ct_str) ";
    $sql .= " and c_id = ? ";
    $mlmAry = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);

    if ($p_ary[0][5] == '') {
        exit;
    }
    if ($p_ary[0][5] == '1') {
        $response_format_text = new TextMessageBuilder($p_ary[0][0] . "\n\n" . $p_ary[0][1]);
    } else if ($p_ary[0][5] == '2') {
        $url = $bitly->BitlyShort("https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=" . LOGIN_ID . "&redirect_uri=" . urlencode(WEB_HOSTNAME . "/linelogin.php") . "?para=" . rawurlencode(ENCCode($c_id . $p_id)) . "&state=" . md5('chiliman') . "&scope=openid%20profile");
        $response_format_text = new ImagemapMessageBuilder(
                CDN_ROOT_PATH . substr($p_ary[0][4], 0, -1), $p_ary[0][0], new BaseSizeBuilder(1040, 1040), [
            new ImagemapUriActionBuilder(
                    $url, new AreaBuilder(0, 0, 1040, 1040)
            )
                ]
        );
    } else if ($p_ary[0][5] == '3') {
        $url = $bitly->BitlyShort("https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=" . LOGIN_ID . "&redirect_uri=" . urlencode(WEB_HOSTNAME . "/linelogin.php") . "?para=" . rawurlencode(ENCCode($c_id . $p_id)) . "&state=" . md5('chiliman') . "&scope=openid%20profile");
        $response_format_text = new TemplateMessageBuilder(
                $p_ary[0][0], new ButtonTemplateBuilder(
                $p_ary[0][0], $p_ary[0][1], CDN_ROOT_PATH . $p_ary[0][4], [
            new UriTemplateActionBuilder("【點我去連結】", $url),
                ]
                )
        );
    }

    for ($i = 0; $i < count($mlmAry); $i++) {
        $bot->pushMessage($mlmAry[$i][0], $response_format_text);
    }
}
//回原本畫面
header("Location:$fileName.php");
?>
