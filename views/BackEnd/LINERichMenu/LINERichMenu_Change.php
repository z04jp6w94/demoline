<?php

header('Content-Type: text/html; charset=utf-8');
ini_set('date.timezone', 'Asia/Taipei');
ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . '/assets_rear/session/');
session_start();
//函式庫
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/chiliman_config.php");
//判斷是否登入
if (!isset($_SESSION["user_id"])) {
    header("Location:http://" . $_SERVER['HTTP_HOST'] . "/views/BackEnd/Login/Member_Login.php");
}
//SESSION
$c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
//取得固定參數
$fileName = $_REQUEST["fileName"];
$dataKey = $_REQUEST["dataKey"];
//Turn
$fileName = DECCode($fileName);
$dataKey = DECCode($dataKey);
//定義時間參數
$excuteDate = date("Ymd"); //操作日期
$excuteTime = date("His"); //操作時間
/* 執行 */
ob_end_clean();
header("Connection: close");
ignore_user_abort(true);
set_time_limit(0);
/* 執行 */
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
/* ChangeStatus */
$sql = " UPDATE richmenu_set_m SET rsm_status = 'N' ";
$sql .= " WHERE rsm_status = 'Y' ";
$sql .= " AND c_id = ?  ";
$mysqli->updatePreSTMT($sql, "s", array($c_id));
$sql = " UPDATE richmenu_set_m SET rsm_status = 'Y' ";
$sql .= " WHERE richmenu_id = ? ";
$sql .= " AND c_id = ?  ";
$mysqli->updatePreSTMT($sql, "ss", array($dataKey, $c_id));
//Change RichMenu
$post_data = array("c_id" => "$c_id", "richmenu_id" => "$dataKey");
$ch = curl_init(WEB_HOSTNAME . "/api/scrm/LINERichMenu/LINERichMenu_API.php");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json; charset=UTF-8',
));
$result = curl_exec($ch);
curl_close($ch);
//回原本畫面
header("Location:$fileName.php");
?>
