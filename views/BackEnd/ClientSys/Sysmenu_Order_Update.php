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
$dataKey = $_REQUEST["dataKey"];
//取得更新參數
$sysmeunPara = $_REQUEST['sysmeunPara'];
//參數處理
$onedomainAry = explode(",", $sysmeunPara);
foreach ($onedomainAry as $val) {
    $twodomainAry[] = explode("-", $val);
}
//定義時間參數
$excuteDate = date("Ymd"); //操作日期
$excuteTime = date("His"); //操作時間
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
$menu_folder = "";
$menu_prev_id = 0;
$p_menu_order = 1;
$f_menu_order = 1;
foreach ($twodomainAry as $rsAry) { //$twodomainAry: menu_id, menu_type, menu_folder, menu_prev_id
    if ($rsAry[1] == 'P') { //不需要修改系統選單資料夾名稱
        $sql = "UPDATE sysmenu SET menu_prev_id = ?, menu_order = ? WHERE menu_id = ?";
        $mysqli->updatePreSTMT($sql, "sss", array(0, $p_menu_order, $rsAry[0]));
        $menu_folder = $rsAry[2];
        $menu_prev_id = $rsAry[0];
        $p_menu_order++;
        $f_menu_order = 1;
    }
    if ($rsAry[1] == 'F' && $rsAry[3] == 0) { //不需要修改系統選單資料夾名稱
        $sql = "UPDATE sysmenu SET menu_prev_id = ?, menu_order = ? WHERE menu_id = ?";
        $mysqli->updatePreSTMT($sql, "sss", array(0, $p_menu_order, $rsAry[0]));
        $menu_folder = "";
        $menu_prev_id = 0;
        $p_menu_order++;
        $f_menu_order = 1;
    }
    if ($rsAry[1] == 'F' && $rsAry[3] != 0 && $menu_prev_id == 0) { //需要修改系統選單資料夾名稱
        $sql = "UPDATE sysmenu SET menu_folder = ?, menu_prev_id = ?, menu_order = ? WHERE menu_id = ?";
        $mysqli->updatePreSTMT($sql, "ssss", array($menu_folder, $menu_prev_id, $p_menu_order, $rsAry[0]));
        $p_menu_order++;
    }
    if ($rsAry[1] == 'F' && $rsAry[3] != 0 && $menu_prev_id != 0) { //需要修改系統選單資料夾名稱
        $sql = "UPDATE sysmenu SET menu_folder = ?, menu_prev_id = ?, menu_order = ? WHERE menu_id = ?";
        $mysqli->updatePreSTMT($sql, "ssss", array($menu_folder, $menu_prev_id, $f_menu_order, $rsAry[0]));
        $f_menu_order++;
    }
}
//回原本畫面
header("Location:$fileName.php");
?>
