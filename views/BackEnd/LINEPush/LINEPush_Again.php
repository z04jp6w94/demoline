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
    if (!empty($_POST["ct_id"])) {
        $ct_id = $_POST["ct_id"];
        $ct_str = implode(",", $ct_id);
    } else {
        $ct_str = "";
    }
//定義時間參數
    $excuteDateTime = date("Y-m-d H:i:s"); //操作日期
//資料庫連線
    $mysqli = new DatabaseProcessorForWork();
    $bitly = new BitlyShortAPI();
    /* Data */
    $sql = " Insert into push_m ";
    $sql .= " (c_id, cp_id, ct_id, p_name, p_content, ";
    $sql .= " p_url, p_img, p_cdn_root, p_send_status, p_send_date, ";
    $sql .= " lp_type, p_type, p_reserve, deletestatus, entry_datetime) ";
    $sql .= " select c_id, cp_id, ct_id, p_name, p_content, ";
    $sql .= " p_url, p_img, p_cdn_root, '1', ?, ";
    $sql .= " lp_type, p_type, 'Y', 'N', ? ";
    $sql .= " from push_m ";
    $sql .= " where p_id = ? ";
    $p_id = $mysqli->createPreSTMT_CreateId($sql, "sss", array($excuteDateTime, $excuteDateTime, $dataKey));
    /*  */
    $post_data = array("p_id" => "$p_id", "ct_str" => "$ct_str");
    $ch = curl_init(WEB_HOSTNAME . "/api/scrm/LINEPush/LINEPush_Again_API.php");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charset=UTF-8',
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    /*  */
}
//回原本畫面
header("Location:$fileName.php");
?>
