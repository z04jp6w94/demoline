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
//取得固定參數
$c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
//取得接受參數
$keyword = !empty($_REQUEST["keyword"]) ? $_REQUEST["keyword"] : "";
$keyword = '[' . $keyword . ']';
if ($keyword == '') {
    exit;
}
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
/* 關鍵字 */
$sql = " select count(lrcm_id) from line_richmenu_content_m ";
$sql .= " where c_id = ? and lrcm_keyword = ? and deletestatus = 'N' ";
$RsVal = $mysqli->readValuePreSTMT($sql, "ss", array($c_id, $keyword), 2);
$str = "";
if ($RsVal == '0') {
    $str = "1";
} else {
    $str = "2";
}

echo json_encode(array("status" => $str));
?>

