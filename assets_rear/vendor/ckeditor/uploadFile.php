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
$cKEditor = $_REQUEST["CKEditor"];
$cKEditorFuncNum = $_REQUEST["CKEditorFuncNum"];
$langCode = $_REQUEST["langCode"];
$ckCsrfToken = $_REQUEST["ckCsrfToken"];
$sourceType = $_REQUEST["sourceType"]; //判斷檔案來源類型
if ($sourceType == "file") {
    //檔案處理
    $oldfileName = $_FILES['upload']['name'];
    $newfileName = date("YmdHis");
    $tempFilePath = $_FILES['upload']['tmp_name'];
    $serverFilePath = "assets_rear/vendor/ckeditor/files";
    $file = new BackEndFileForChiliman($oldfileName, $newfileName, $tempFilePath, $serverFilePath);
    $fileName = $file->archive();
    echo '<script type="text/javascript">';
    echo 'window.parent.CKEDITOR.tools.callFunction(' . $cKEditorFuncNum . ', "' . $fileName . '", "");';
    echo '</script>';
}
if ($sourceType == "fileImage") {
    //圖檔處理
    $oldfileName = $_FILES['upload']['name'];
    $newfileName = date("YmdHis");
    $tempFilePath = $_FILES['upload']['tmp_name'];
    $serverFilePath = "assets_rear/vendor/ckeditor/images";
    $ThumbnailSize = 250;
    $pictureFile = new BackEndPictureFileForChiliman($oldfileName, $newfileName, $tempFilePath, $serverFilePath, $ThumbnailSize);
    $fileNameExtension = $pictureFile->getFileNameExtension($oldfileName);
    if ($fileNameExtension == "jpg" || $fileNameExtension == "png" || $fileNameExtension == "gif") {
        $originalImgName = $pictureFile->archiveWithoutReSizePictureFile();
        echo '<script type="text/javascript">';
        echo 'window.parent.CKEDITOR.tools.callFunction(' . $cKEditorFuncNum . ', "' . $originalImgName . '", "");';
        echo '</script>';
    } else {
        echo '<script type="text/javascript">';
        echo 'window.parent.CKEDITOR.tools.callFunction(' . $cKEditorFuncNum . ', "", "請選擇正確的檔案格式(jpg/png/gif)");';
        echo '</script>';
    }
}
?>

