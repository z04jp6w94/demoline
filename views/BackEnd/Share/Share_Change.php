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
$dataKey = DECCode($dataKey);
//定義時間參數
$excuteDate = date("Ymd"); //操作日期
$excuteTime = date("His"); //操作時間
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
/* ChangeStatus */
$sql = " SELECT sa_id, sa_status ";
$sql .= " FROM share_activity ";
$sql .= " WHERE sa_id = ? ";
$sql .= " AND c_id = ?  ";
$initAry = $mysqli->readArrayPreSTMT($sql, "ss", array($dataKey, $c_id), 2);
if ($initAry[0][1] == "Y") {
    $sql = " Update share_activity SET sa_status = 'N' ";
    $sql .= " WHERE sa_id = ? ";
    $sql .= " AND c_id = ?  ";
    $mysqli->updatePreSTMT($sql, "ss", array($dataKey, $c_id));
} else {
    $sql = " Update share_activity SET sa_status = 'Y' ";
    $sql .= " WHERE sa_id = ? ";
    $sql .= " AND c_id = ?  ";
    $mysqli->updatePreSTMT($sql, "ss", array($dataKey, $c_id));
}
$sql = " Update share_activity SET sa_status = 'N' ";
$sql .= " WHERE sa_id not in (?) ";
$sql .= " AND c_id = ?  ";
$mysqli->updatePreSTMT($sql, "ss", array($dataKey, $c_id));

//回原本畫面
header("Location:$fileName.php");
?>
