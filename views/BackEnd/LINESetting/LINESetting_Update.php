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
//取得更新參數
$ls_follow_content = $_REQUEST["ls_follow_content"];
//定義時間參數
$excuteDateTime = date("Y-m-d H:i:s");
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
/* get token */
$sql = " SELECT c_line_TOKEN FROM crm_m ";
$sql .= " WHERE c_id = ? ";
$accessToken = $mysqli->readValuePreSTMT($sql, "s", array($c_id));
/* LINE SETTING */
$sql = " SELECT ls_customer_richmenu_id, ls_customer_richmenu_img ";
$sql .= " FROM line_setting ";
$sql .= " WHERE c_id = ? ";
$ls_Ary = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);
$ori_customer_richmenu_id = $ls_Ary[0][0];
$ori_customer_richmenu_img = $ls_Ary[0][1];
//圖檔處理
if (!file_exists($_FILES['ls_customer_richmenu_img']['tmp_name']) || !is_uploaded_file($_FILES['ls_customer_richmenu_img']['tmp_name'])) {
    //無上傳檔案;
    $customer_richmenu_id = $ori_customer_richmenu_id;
    $customer_richmenu_img = $ori_customer_richmenu_img;
} else {
    $originalImgName = "";
    $thumbnailImgName = "";
    $oldfileName = $_FILES['ls_customer_richmenu_img']['name'];
    $newfileName = date("YmdHis");
    $tempFilePath = $_FILES['ls_customer_richmenu_img']['tmp_name'];
    $serverFilePath = "assets_rear/images/richmenu/" . $c_id;
    $ThumbnailSize = 250;
    $pictureFile = new BackEndPictureFileForWork($oldfileName, $newfileName, $tempFilePath, $serverFilePath, $ThumbnailSize);
    $pictureFile->createFolder("assets_rear/images/richmenu");
    $originalImgName = $pictureFile->archiveWithoutReSizePictureFile();
    $thumbnailImgName = $pictureFile->archiveWithReSizePictureFile();
    /* Menu */
    $LineRichMenu = new BackEndLineRichMenuForWork($accessToken);
    /* Create */
    $title = '客服連線';
    $content = '{"bounds": {"x": 0,"y": 0,"width": 2500,"height": 1686},"action": {"type": "postback","data": "action=998"}}';
    $richMenuId = $LineRichMenu->CreateMenuId($title, $content);
    if ($richMenuId === FALSE) {
        error_log($c_id . "LineSetting Create RichMenu FALSE!");
    } else {
        /* 上傳Menu圖檔 richMenuId */
        $imagePath = ROOT_PATH . $originalImgName;
        $ContentLength = strlen($imagePath);
        $PhotoStatus = $LineRichMenu->UploadMenuPhoto($richMenuId, $imagePath);
        if (!$PhotoStatus) {
            error_log($c_id . "->" . $richMenuId . ": PIC FALSE!");
        } else {
            $DeleteRichMenu = $LineRichMenu->DeleteRichMenu($ori_customer_richmenu_id);
            if ($DeleteRichMenu === true) {
                /* 刪除檔案 原圖,縮圖 */
            }
            //取代richmenu_id, richmenu_img
            $customer_richmenu_id = $richMenuId;
            $customer_richmenu_img = $originalImgName;
        }
    }
}
/* update */
$sql = " UPDATE line_setting SET ls_follow_content = ?, ls_customer_richmenu_id = ?, ls_customer_richmenu_img = ?, modify_datetime = ? WHERE c_id = ? ";
$mysqli->updatePreSTMT($sql, "sssss", array($ls_follow_content, $customer_richmenu_id, $customer_richmenu_img, $excuteDateTime, $c_id));
//回原本畫面
header("Location:$fileName.php");
?>
