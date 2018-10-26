<?php

use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;

header('Content-Type: text/html; charset=utf-8');
ini_set('date.timezone', 'Asia/Taipei');
session_start();
//函式庫
include_once("/var/www/html/social_crm_2/web_root/config/chiliman_config.php");
/* CDN S3 */
require_once(AUTOLOAD_PATH);
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
$bitly = new BitlyShortAPI();
//變數
$DateTime = date("Y-m-d H:i:s");
//預約
$sql = " SELECT lpv_id, c_id, lpv_img_cdn_root, lpv_video_cdn_root ";
$sql .= " FROM line_push_video ";
$sql .= " WHERE 1 = 1 ";
$sql .= " AND lpv_send_status = '2' ";                //預約
$sql .= " AND lpv_send_date < '" . $DateTime . "' ";  //發送時間
$sql .= " AND lpv_reserve = 'N' ";                    //發送狀態
$sql .= " AND deletestatus = 'N' ";                 //刪除狀態
$sql .= " UNION ";
$sql .= " SELECT lpv_id, c_id, lpv_img_cdn_root, lpv_video_cdn_root ";
$sql .= " FROM line_push_video ";
$sql .= " WHERE 1 = 1 ";
$sql .= " AND lpv_send_status = '2' ";
$sql .= " AND lpv_send_date = '" . $DateTime . "' ";
$sql .= " AND lpv_reserve = 'N' ";
$sql .= " AND deletestatus = 'N' ";
$initAry = $mysqli->readArraySTMT($sql, 4);
//推文
$c_id = "";
for ($i = 0; $i < count($initAry); $i++) {
    $c_id = $initAry[$i][1];
    /* 人員名單 */
    $sql = " SELECT DISTINCT(mlm_lineid), c_id FROM member_list_m ";
    $sql .= " WHERE c_id = ? ";
    $sql .= " AND mlm_source = '1' ";
    $mlmAry = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);
    /* get token */
    $sql = " SELECT c_line_TOKEN, c_line_SECRET, c_linelogin_CID FROM crm_m ";
    $sql .= " WHERE c_id = ? ";
    $Line_Info = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 3);
    /* LINE */
    $access_token = $Line_Info[0][0];
    $secret = $Line_Info[0][1];
    /* LINE_LOGIN */
    $linelogin_id = $Line_Info[0][2];
    /* LINE_BOT */
    $bot = new LINEBot(new \LINE\LINEBot\HTTPClient\CurlHTTPClient($access_token), ['channelSecret' => $secret]);
    //推文
    $video_cdn_root = $initAry[$i][3];
    $img_cdn_root = $initAry[$i][2];

    $response_format_text = new VideoMessageBuilder(CDN_ROOT_PATH . $video_cdn_root, CDN_ROOT_PATH . $img_cdn_root);

    for ($j = 0; $j < count($mlmAry); $j++) {
        $User = $mlmAry[$j][0];
        $bot->pushMessage($User, $response_format_text);
    }

    //推文發送狀態
    $lpv_id = $initAry[$i][0];
    $sql = " UPDATE line_push_video SET lpv_reserve = 'Y' ";
    $sql .= " WHERE lpv_id = ? ";
    $sql .= " AND c_id = ? ";
    $mysqli->updatePreSTMT($sql, "ss", array($lpv_id, $c_id));
}
?>