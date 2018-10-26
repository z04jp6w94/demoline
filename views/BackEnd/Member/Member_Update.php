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
/* 現在人員 */
$user_id = !empty($_SESSION["user_id"]) ? $_SESSION["user_id"] : NULL;
$user_name = !empty($_SESSION["user_name"]) ? $_SESSION["user_name"] : NULL;
$c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
//取得固定參數
$fileName = $_REQUEST["fileName"];
$dataKey = $_REQUEST["dataKey"];
//取得更新參數
$mlm_name = $_REQUEST["mlm_name"];
$mlm_email = $_REQUEST["mlm_email"];
$mlm_phone = $_REQUEST["mlm_phone"];
$mlm_remark = $_REQUEST["mlm_remark"];

if (!empty($_POST["ct_id"])) {
    $ct_id = $_POST["ct_id"];
    $mlm_tag = implode(",", $ct_id);//str
} else {
    $mlm_tag = "";
}
$ct_str = explode(",", $mlm_tag);
//定義時間參數
$excuteDateTime = date("Y-m-d H:i:s");
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
/* data */
$sql = " SELECT mlm_lineid FROM member_list_m ";
$sql .= " WHERE mlm_id = ? ";
$sql .= " AND c_id = ? ";
$mlm_lineid = $mysqli->readValuePreSTMT($sql, "ss", array($dataKey, $c_id));
$sql = " DELETE FROM member_tag ";
$sql .= " WHERE mlm_lineid = ?  ";
$sql .= " AND c_id = ? ";
$mysqli->deletePreSTMT($sql, "ss", array($mlm_lineid, $c_id));
$sql = " INSERT INTO member_tag (mlm_lineid, c_id, ct_id) VALUES ";
for ($i = 1; $i <= count($ct_str); $i++) {
    $position = $i - 1;
    if ($i < count($ct_str)) {
        $sql .= " ('" . $mlm_lineid . "', '" . $c_id . "', '" . $ct_str[$position] . "'), ";
    } else {
        $sql .= " ('" . $mlm_lineid . "', '" . $c_id . "', '" . $ct_str[$position] . "'); ";
    }
}
$mysqli->createSTMT($sql);
/* update */
$sql = "UPDATE member_list_m SET mlm_name = ?, mlm_email = ?, mlm_phone = ?, mlm_remark = ? WHERE mlm_id = ?";
$mysqli->updatePreSTMT($sql, "sssss", array($mlm_name, $mlm_email, $mlm_phone, $mlm_remark, $dataKey));
/* 更新紀錄 */
//$sql = " insert into member_modify_log ";
//$sql .= " (mlm_id, mlm_before_tag, mlm_after_tag, modify_name, modify_datetime) ";
//$sql .= " values ";
//$sql .= " (?, ?, ?, ?, ?) ";
//$mysqli->updatePreSTMT($sql, "sssss", array($dataKey, $old_tag, $mlm_tag, $user_name, $excuteDateTime));
//回原本畫面
header("Location:$fileName.php");
?>
