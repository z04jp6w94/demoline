<?php

use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

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
//取得固定參數
$c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
$fileName = $_REQUEST["fileName"];
$dataKey = $_REQUEST["dataKey"];
//取得更新參數
$fc_response = $_REQUEST["fc_response"];
$f_status = $_REQUEST["f_status"];
$sendstatus = $_REQUEST["sendstatus"];
//定義時間參數
$excuteDateTime = date("Y-m-d H:i:s"); //操作日期
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
/* 更新 */
$sql = " UPDATE feedback SET f_status = ? ";
$sql .= " WHERE f_id = ? ";
$mysqli->updatePreSTMT($sql, "ss", array($f_status, $dataKey));
if ($sendstatus == 'Y' && $fc_response != '') {
    /* Course */
    $sql = " INSERT INTO feedback_course  ";
    $sql .= " (f_id, fc_response, modify_datetime) ";
    $sql .= " VALUES ";
    $sql .= " (?, ?, ?) ";
    $mysqli->createPreSTMT($sql, "sss", array($dataKey, $fc_response, $excuteDateTime));

    $sql = " SELECT mlm.mlm_lineid, mlm.mlm_name, f.c_id, f_problem, f.entry_datetime ";
    $sql .= " from feedback f ";
    $sql .= " left join member_list_m mlm on mlm.mlm_lineid = f.mlm_lineid and mlm.c_id = f.c_id  ";
    $sql .= " WHERE f_id = ? ";
    $sql .= " AND f.c_id = ? ";
    $initAry = $mysqli->readArrayPreSTMT($sql, "ss", array($dataKey, $c_id), 5);
    /* LINE BOT */
    $sql = " select c_line_TOKEN, c_line_SECRET from crm_m ";
    $sql .= " where c_id = ? ";
    $Line_Info = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);
    $access_token = $Line_Info[0][0];
    $secret = $Line_Info[0][1];
    $bot = new LINEBot(new \LINE\LINEBot\HTTPClient\CurlHTTPClient($access_token), ['channelSecret' => $secret]);

    $title = "Dear " . $initAry[0][1] . " (先生/小姐)您好";
    $content = "於" . $initAry[0][4] . "\n";
    $content .= "提問的問題:\n【" . $initAry[0][3] . "】\n";
    $content .= "回覆為:\n";
    $content .= "【" . $fc_response . "】\n";
    $content .= "謝謝您的發問􀄃􀆀sparkling eyes􏿿";

    $response_format_text = new TextMessageBuilder($title . "\n" . $content);
    $bot->pushMessage($initAry[0][0], $response_format_text);
}
//回原本畫面
header("Location:$fileName.php");
?>
