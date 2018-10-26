<?php

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('date.timezone', 'Asia/Taipei');
ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . '/assets_rear/session/');
session_start();
//函式庫
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/chiliman_config.php");
//判斷是否登入
if (!isset($_SESSION["user_id"])) {
    BackToLoginPage();
}
//取得固定參數
$fileName = $_REQUEST["fileName"];
//取得新增參數
$c_id = trim($_REQUEST["c_id"]);
$c_status = $_REQUEST["c_status"];
$c_name = $_REQUEST["c_name"];
$c_tel = $_REQUEST["c_tel"];
$c_address = $_REQUEST["c_address"];
$c_mail = $_REQUEST["c_mail"];
$c_remark = $_REQUEST["c_remark"];
$c_line_name = $_REQUEST["c_line_name"];
$c_line_OAID = $_REQUEST["c_line_OAID"];
$c_line_CID = $_REQUEST["c_line_CID"];
$c_line_SECRET = $_REQUEST["c_line_SECRET"];
$c_line_TOKEN = $_REQUEST["c_line_TOKEN"];
$c_linelogin_CID = $_REQUEST["c_linelogin_CID"];
$c_linelogin_SECRET = $_REQUEST["c_linelogin_SECRET"];
$c_fb_appid = $_REQUEST["c_fb_appid"];
$c_fb_secret = $_REQUEST["c_fb_secret"];
$c_fb_token = $_REQUEST["c_fb_token"];
$c_fb_patch = $_REQUEST["c_fb_patch"];
$c_fb_fans = $_REQUEST["c_fb_fans"];
//客服資料
$lcsm_mode_type = $_REQUEST["lcsm_mode_type"];
//定義時間參數
$excuteDateTime = date("Y-m-d H:i:s"); //操作日期
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
$sql = " SELECT COUNT(c_id) from crm_m ";
$sql .= " WHERE c_id = ? ";
$c_row_count = $mysqli->readValuePreSTMT($sql, "s", array($c_id));
if ($c_row_count >= '1') {
    echo "<script>";
    echo "alert(客戶編號重複,請重新輸入！');";
    echo 'window.history.back();';
    echo "</script>";
    exit();
}
$sql = "INSERT INTO crm_m ";
$sql .= " (c_id, c_name, c_tel, c_address, c_mail, ";
$sql .= " c_status, c_remark, c_line_OAID ,c_line_CID, c_line_SECRET, ";
$sql .= " c_line_TOKEN, c_linelogin_CID, c_linelogin_SECRET, c_line_name, c_fb_appid, ";
$sql .= " c_fb_secret, c_fb_token, c_fb_patch, c_fb_fans, entry_datetime) ";
$sql .= " VALUES ";
$sql .= " (?, ?, ?, ?, ?, ";
$sql .= " ?, ?, ?, ?, ?, ";
$sql .= " ?, ?, ?, ?, ?, ";
$sql .= " ? ,? ,?, ?, ? )";
$mysqli->createPreSTMT($sql, "ssssssssssssssssssss", array($c_id, $c_name, $c_tel, $c_address, $c_mail, $c_status, $c_remark, $c_line_OAID, $c_line_CID, $c_line_SECRET, $c_line_TOKEN, $c_linelogin_CID, $c_linelogin_SECRET, $c_line_name, $c_fb_appid, $c_fb_secret, $c_fb_token, $c_fb_patch, $c_fb_fans, $excuteDateTime));
/* Menu */
$accessToken = $c_line_TOKEN;
$LineRichMenu = new BackEndLineRichMenuForWork($accessToken);
/* Create */
//$title = '客服連線';
//$content = '{"bounds": {"x": 0,"y": 0,"width": 2500,"height": 1686},"action": {"type": "postback","data": "action=998"}}';
//$richMenuId = $LineRichMenu->CreateMenuId($title, $content);
//if ($richMenuId === FALSE) {
//    
//} else {
//    /* 上傳Menu圖檔 richMenuId */
//    $imagePath = ROOT_PATH . '/' . 'customer.jpg';
//    $ContentLength = strlen($imagePath);
//    $PhotoStatus = $LineRichMenu->UploadMenuPhoto($richMenuId, $imagePath);
//    if (!$PhotoStatus) {
//        //error_log($c_id . "->" . $richMenuId . ": PIC FALSE!");
//    }
//}
//Line Follow Content 
$richMenuId = "";
$sql = " INSERT INTO line_setting ";
$sql .= " (c_id, ls_follow_content, ls_customer_richmenu_id, ls_customer_richmenu_img, entry_datetime, ";
$sql .= " modify_datetime) ";
$sql .= " VALUES ";
$sql .= " (?, '感謝您加入此 Line@ 帳號 􀄃􀇅Cony attracted􏿿􀄃􀇅Cony attracted􏿿', ?, '', ?, ";
$sql .= " ?) ";
$mysqli->createPreSTMT($sql, "ssss", array($c_id, $richMenuId, $excuteDateTime, $excuteDateTime));
/* LCS Table */
//$lcsm_community_type = "LINE";
//$login_api_url = WEB_HOSTNAME . "/api/LCS/login.php";
//$logout_api_url = WEB_HOSTNAME . "/api/LCS/disconnect.php";
/* lcs_module */
//$sql = " INSERT INTO lcs_module ";
//$sql .= " ( c_id, lcsm_mode_type, lcsm_community_type, lcsm_community_name, lcsm_community_access_token, ";
//$sql .= " lcsm_community_login_api_url, lcsm_community_logout_api_url, lcsm_remark, lcsm_create_time, lcsm_update_time ) ";
//$sql .= " VALUES ";
//$sql .= " ( ?, ?, ?, ?, ?, ";
//$sql .= " ?, ?, '', ?, ? ) ";
//$lcsm_id = $mysqli->createPreSTMT_CreateId($sql, "sssssssss", array($c_id, $lcsm_mode_type, $lcsm_community_type, $c_line_name, $c_line_TOKEN, $login_api_url, $logout_api_url, $excuteDateTime, $excuteDateTime));
/* lcs_module_config */
//$sql = " INSERT INTO lcs_module_config (lcsm_id, c_id, lcsmc_community_type, lcsmc_key, lcsmc_command, ";
//$sql .= " lcsmc_content, lcsmc_create_time, lcsmc_update_time ) ";
//$sql .= " VALUES ";
//$sql .= " ('$lcsm_id', '$c_id', 'LINE', 'SETTING', 'SERVICE_WORK_START_TIME', '080000', '$excuteDateTime', '$excuteDateTime'), ";
//$sql .= " ('$lcsm_id', '$c_id', 'LINE', 'SETTING', 'SERVICE_WORK_END_TIME', '170000', '$excuteDateTime', '$excuteDateTime'), ";
//$sql .= " ('$lcsm_id', '$c_id', 'LINE', 'RESPONSE', 'SERVICE_EMPTY', '目前無客服人員在線上，請稍後再試！', '$excuteDateTime', '$excuteDateTime'), ";
//$sql .= " ('$lcsm_id', '$c_id', 'LINE', 'RESPONSE', 'SERVICE_PICK_UP', '您好，我是客服人員%name%，請問有什麼地方可以協助您的?', '$excuteDateTime', '$excuteDateTime'), ";
//$sql .= " ('$lcsm_id', '$c_id', 'LINE', 'RESPONSE', 'SERVICE_HANG_UP', '客服人員已離開，您的交談已結束！', '$excuteDateTime', '$excuteDateTime'), ";
//$sql .= " ('$lcsm_id', '$c_id', 'LINE', 'RESPONSE', 'SERVICE_REST', '目前是下班時間，請您於上班時間 8點 至 17點 時再試。', '$excuteDateTime', '$excuteDateTime'), ";
//$sql .= " ('$lcsm_id', '$c_id', 'LINE', 'RESPONSE', 'SERVICE_BUSY', '目前客服人員均在忙線中，請稍後再試！', '$excuteDateTime', '$excuteDateTime'), ";
//$sql .= " ('$lcsm_id', '$c_id', 'LINE', 'QUICK_RESPONSE', 'TEXT', '您好，請問有什麼地方可以協助您的?', '$excuteDateTime', '$excuteDateTime') ";
//$mysqli->createSTMT($sql);
//回原本畫面
header("Location:$fileName.php");
?>
