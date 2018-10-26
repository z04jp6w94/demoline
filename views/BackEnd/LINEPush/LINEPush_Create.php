<?php

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('date.timezone', 'Asia/Taipei');
ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . '/assets_rear/session/');
session_start();
//函式庫
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/chiliman_config.php");
/* CDN S3 */
require_once(AUTOLOAD_PATH);
require LIBRARY_ROOT_PATH . '/S3/s3.php';
//CDN
$s3 = new s3(CDN_REGION, CDN_VERSION, CDN_PATH, CDN_PROFILE, CDN_BUCKET);
//判斷是否登入
if (!isset($_SESSION["user_id"])) {
    BackToLoginPage();
}
/* DateTimeNow */
$DateTimeRoot = date("YmdHis");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //取得固定參數
    $c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
    $FilePath = !empty($_COOKIE["FilePath"]) ? $_COOKIE["FilePath"] : NULL;
    $fileName = basename($FilePath, '.php');
    //來源判斷
    $url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
    $source_url = dirname($url) . '/' . $fileName . '_Add.php';
    if ($_SERVER['HTTP_REFERER'] != $source_url) {
        BackToLoginPage();
        exit;
    } else {
//資料庫連線
        /* 執行 */
        ob_end_clean();
        header("Connection: close");
        ignore_user_abort(true);
        set_time_limit(0);
        /* 執行 */
        $mysqli = new DatabaseProcessorForWork();
        $bitly = new BitlyShortAPI();
//取得新增參數
        $lp_type = $_REQUEST["lp_type"];
        $p_name = $_REQUEST["p_name"];
        $cp_id = $_REQUEST["cp_id"];
        $p_content = $_REQUEST["p_content"];
        $p_url = $_REQUEST["p_url"];
        $p_send_status = $_REQUEST["p_send_status"];
        $p_send_date = $_REQUEST["p_send_date"];
        $p_send_time = $_REQUEST["p_send_time"];
        $p_send_datetime = $p_send_date . ' ' . date("H:i:s", strtotime($p_send_time));
        $p_reserve = "Y";
        if (!empty($_POST["ct_id"])) {
            $ct_id = $_POST["ct_id"];
            $ctid_str = implode(",", $ct_id);
        } else {
            $ctid_str = "";
        }
//定義時間參數
        $excuteDateTime = date("Y-m-d H:i:s"); //操作日期
        $originalImgName = "";
        $thumbnailImgName = "";
        $UploadCDN = "";
        //發送狀態
        if ($p_send_status == '1') {
            $p_send_datetime = $excuteDateTime;
        } else {
            $p_reserve = "N";
        }
        //推文類型
        if ($lp_type == '2') {
            //圖檔處理
            $oldfileName = $_FILES['p_img']['name'];
            $newfileName = 1040;
            $tempFilePath = $_FILES['p_img']['tmp_name'];
            $serverFilePath = "assets_rear/images/push/" . $c_id . "/" . $DateTimeRoot;
            $ThumbnailSize = 250;
            $pictureFile = new BackEndPictureFileForWork($oldfileName, $newfileName, $tempFilePath, $serverFilePath, $ThumbnailSize);
            $pictureFile->createFolder("assets_rear/images/push");
            $pictureFile->createFolder("assets_rear/images/push/" . $c_id);
            $pictureFile->createFolder($serverFilePath);
            $originalImgName = $pictureFile->archiveReSizePictureFileTo1040();
            $thumbnailImgName = $pictureFile->archiveWithReSizePictureFile();
            /* 上傳檔案到CDN */
            $UploadDir = ROOT_PATH . '/' . $serverFilePath . '/';
            $UploadCDN = CDN_PUSH . $c_id . '/' . $DateTimeRoot . '/';
            $s3->putObject($UploadDir . '240', $UploadCDN . '240');
            $s3->putObject($UploadDir . '300', $UploadCDN . '300');
            $s3->putObject($UploadDir . '460', $UploadCDN . '460');
            $s3->putObject($UploadDir . '700', $UploadCDN . '700');
            $s3->putObject($UploadDir . '1040', $UploadCDN . '1040');
        } else if ($lp_type == '3') {
            $oldfileName = $_FILES['p_img']['name'];
            $newfileName = $c_id . "_" . date("YmdHis");
            $tempFilePath = $_FILES['p_img']['tmp_name'];
            $serverFilePath = "assets_rear/images/push/" . $c_id;
            $ThumbnailSize = 250;
            $pictureFile = new BackEndPictureFileForWork($oldfileName, $newfileName, $tempFilePath, $serverFilePath, $ThumbnailSize);
            $pictureFile->createFolder("assets_rear/images/push");
            $originalImgName = $pictureFile->archiveWithoutReSizePictureFile();
            $thumbnailImgName = $pictureFile->archiveWithReSizePictureFile();
            /* 上傳檔案到CDN */
            $UploadDir = ROOT_PATH . '/' . $thumbnailImgName;
            $CDN_FILE_NAME = substr($thumbnailImgName, -38);
            $UploadCDN = CDN_PUSH . $c_id . '/' . $CDN_FILE_NAME;
            $s3->putObject($UploadDir, $UploadCDN);
        }

        $sql = "INSERT INTO push_m ";
        $sql .= "( c_id, cp_id, ct_id, p_name, p_content, ";
        $sql .= " p_url, p_img, p_cdn_root, p_send_status, p_send_date, ";
        $sql .= " lp_type, p_type, p_reserve, deletestatus, entry_datetime)";
        $sql .= " VALUES ";
        $sql .= " (?, ?, ?, ?, ?, ";
        $sql .= " ?, ?, ?, ?, ?, ";
        $sql .= " ?, '1', ?, 'N', ? )";
        $p_id = $mysqli->createPreSTMT_CreateId($sql, "sssssssssssss", array($c_id, $cp_id, $ctid_str, $p_name, $p_content, $p_url, $originalImgName, $UploadCDN, $p_send_status, $p_send_datetime, $lp_type, $p_reserve, $excuteDateTime));
        //立即推播
        if ($p_send_status == '1') {
            $post_data = array("p_id" => "$p_id");
            $ch = curl_init(WEB_HOSTNAME . "/api/scrm/LINEPush/LINEPush_API.php");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=UTF-8',
            ));
            $result = curl_exec($ch);
            curl_close($ch);
        }
        //回原本畫面
        header("Location:$fileName.php");
    }
} else {
    BackToLoginPage();
    exit;
}
?>
