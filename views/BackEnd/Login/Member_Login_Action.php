<?php

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('date.timezone', 'Asia/Taipei');
ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . '/assets_rear/session/');
session_start();
//函式庫
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/chiliman_config.php");
//取得固定參數
$fileName = $_REQUEST["fileName"];
//取得查詢參數
$user_c_id = $_REQUEST['user_c_id'];
$user_account = $_REQUEST['user_account'];
$user_password = $_REQUEST['user_password'];
$remember = !empty($_REQUEST['remember']) ? $_REQUEST['remember'] : "N";
//判斷前台資料
if ($user_account == '') {
    header("Location:Member_Login.php");
    exit();
}
if ($user_password == '') {
    header("Location:Member_Login.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
    $source_url = dirname($url) . '/' . $fileName . '.php';
    if ($_SERVER['HTTP_REFERER'] != $source_url) {
        BackToLoginPage();
        exit;
    } else {
//資料庫連線
        $mysqli = new DatabaseProcessorForWork();
//判斷帳號密碼
        $encode_user_password = ENCCode($user_password);
        $sql = " SELECT user_id, User_Name, c_id, group_id FROM sysuser ";
        $sql .= " WHERE user_account = ? and user_password = ? and c_id = ? ";
        $sql .= " AND user_status = 'Y' ";
        $user_ary = $mysqli->readArrayPreSTMT($sql, "sss", array($user_account, $encode_user_password, $user_c_id), 4);
        if (count($user_ary) != '1') {
            echo "<script>";
            echo "alert('帳號或密碼錯誤請重新輸入！');";
            echo 'window.history.back();';
            echo "</script>";
            exit();
        } else {
            if ($remember == "Y") {
                setcookie("cookie_c_id", $user_ary[0][2], time() + 3600 * 24 * 30); //30days
            }
            $_SESSION["user_id"] = $user_ary[0][0];
            $_SESSION["user_name"] = $user_ary[0][1];
            $_SESSION["c_id"] = $user_ary[0][2];
            $_SESSION["group_id"] = $user_ary[0][3];
            echo "<script>";
            echo "alert('登入成功！');";
            echo "</script>";
            header("Location:Welcome.php");
            exit();
        }
    }
}
?>