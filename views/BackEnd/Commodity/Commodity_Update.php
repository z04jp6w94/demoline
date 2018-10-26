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
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //取得固定參數
    $c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
    $FilePath = !empty($_COOKIE["FilePath"]) ? $_COOKIE["FilePath"] : NULL;
    $fileName = basename($FilePath, '.php');
    $dataKey = $_REQUEST["dataKey"];
    chkValueEmpty($dataKey);
    //來源判斷
    $url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
    $source_url = dirname($url) . '/' . $fileName . '_Modify.php';
    if ($_SERVER['HTTP_REFERER'] != $source_url) {
        BackToLoginPage();
        exit;
    } else {
//資料庫連線
        $mysqli = new DatabaseProcessorForWork();
//取得新增參數
        $cc_id = $_REQUEST["cc_id"];
        $cm_name = $_REQUEST["cm_name"];
        $cm_price = $_REQUEST["cm_price"];
        $cm_intro = $_REQUEST["cm_intro"];
        $cm_type = $_REQUEST["cm_type"];
        $cm_url = $_REQUEST["cm_url"];
        $cm_shipping_fee = $_REQUEST["cm_shipping_fee"];
        $cm_current_stock = $_REQUEST["cm_current_stock"];
        $cm_max_buy = $_REQUEST["cm_max_buy"];
        $cm_min_buy = $_REQUEST["cm_min_buy"];
        $cm_date_range = $_REQUEST["cm_date_range"];
        $date_range = explode("-", $cm_date_range);
        $cm_starttime = trim($date_range[0]);
        $cm_endtime = trim($date_range[1]);
        if (!empty($_POST["ct_id"])) {
            $ct_id = $_POST["ct_id"];
            $ctid_str = implode(",", $ct_id);
        } else {
            $ctid_str = "";
        }
        $ischange_cm_img = $_REQUEST["ischange_cm_img"];
//定義時間參數
        $excuteDateTime = date("Y-m-d H:i:s"); //操作日期
        $originalImgName = "";
        $thumbnailImgName = "";
        $UploadCDN = "";
        if ($cm_type == "1") {
            $cm_url = "";
        } else if ($cm_type == "2") {
            $cm_shipping_fee = "";
            $cm_current_stock = "";
            $cm_max_buy = "";
            $cm_min_buy = "";
        }
        if ($ischange_cm_img == 'Y') {
            if (!file_exists($_FILES['cm_img']['tmp_name']) || !is_uploaded_file($_FILES['cm_img']['tmp_name'])) {
                //無上傳檔案;
            } else {
                $oldfileName = $_FILES['cm_img']['name'];
                $newfileName = $c_id . "_" . date("YmdHis");
                $tempFilePath = $_FILES['cm_img']['tmp_name'];
                $serverFilePath = "assets_rear/images/commodity/" . $c_id;
                $ThumbnailSize = 250;
                $pictureFile = new BackEndPictureFileForWork($oldfileName, $newfileName, $tempFilePath, $serverFilePath, $ThumbnailSize);
                $pictureFile->createFolder("assets_rear/images/commodity");
                $pictureFile->createFolder("assets_rear/images/commodity/" . $c_id);
                $originalImgName = $pictureFile->archiveWithoutReSizePictureFile();
                $thumbnailImgName = $pictureFile->archiveWithReSizePictureFile();
                /* 上傳檔案到CDN */
                $UploadDir = ROOT_PATH . '/' . $originalImgName;
                $CDN_FILE_NAME = substr($originalImgName, -37);
                $UploadCDN = CDN_COMMODITY . $c_id . '/' . $CDN_FILE_NAME;
                $s3->putObject($UploadDir, $UploadCDN);
                /* UPDATE */
                $sql = " UPDATE commodity_m ";
                $sql .= " SET ct_id = ?, ";
                $sql .= " cc_id = ?, ";
                $sql .= " cm_name = ?, ";
                $sql .= " cm_price = ?, ";
                $sql .= " cm_intro = ?, ";
                $sql .= " cm_img = ?, ";
                $sql .= " cm_cdn_root = ?, ";
                $sql .= " cm_type = ?, ";
                $sql .= " cm_url = ?, ";
                $sql .= " cm_shipping_fee = ?, ";
                $sql .= " cm_current_stock = ?, ";
                $sql .= " cm_max_buy = ?, ";
                $sql .= " cm_min_buy = ?, ";
                $sql .= " cm_starttime = ?, ";
                $sql .= " cm_endtime = ?, ";
                $sql .= " modify_datetime = ? ";
                $sql .= " WHERE cm_id = ? ";
                $sql .= " AND c_id = ? ";
                $Create_Ary = array();
                array_push($Create_Ary, $ctid_str);
                array_push($Create_Ary, $cc_id);
                array_push($Create_Ary, $cm_name);
                array_push($Create_Ary, $cm_price);
                array_push($Create_Ary, $cm_intro); //5
                array_push($Create_Ary, $originalImgName);
                array_push($Create_Ary, $UploadCDN);
                array_push($Create_Ary, $cm_type);
                array_push($Create_Ary, $cm_url);
                array_push($Create_Ary, $cm_shipping_fee); //10
                array_push($Create_Ary, $cm_current_stock);
                array_push($Create_Ary, $cm_max_buy);
                array_push($Create_Ary, $cm_min_buy);
                array_push($Create_Ary, $cm_starttime);
                array_push($Create_Ary, $cm_endtime); //15
                array_push($Create_Ary, $excuteDateTime);
                array_push($Create_Ary, $dataKey);
                array_push($Create_Ary, $c_id);
                $mysqli->updatePreSTMT($sql, "ssssssssssssssssss", $Create_Ary);
                /* 更新關鍵字主檔 */
                $sql = " UPDATE line_richmenu_content_m SET ";
                $sql .= " lrcm_cdn_root = ?, ";
                $sql .= " modify_datetime = ? ";
                $sql .= " WHERE lrcm_action_type = '4' ";
                $sql .= " AND app_id = ? ";
                $sql .= " AND c_id = ? ";
                $mysqli->updatePreSTMT($sql, "ssss", array($UploadCDN, $excuteDateTime, $dataKey, $c_id));
                /* 更新關鍵字明細檔 */
                $sql = " UPDATE line_richmenu_content_t SET ";
                $sql .= " lrct_cdn_root = ?, ";
                $sql .= " modify_datetime = ? ";
                $sql .= " WHERE lrct_action_type = '4' ";
                $sql .= " AND app_id = ? ";
                $sql .= " AND c_id = ? ";
                $mysqli->updatePreSTMT($sql, "ssss", array($UploadCDN, $excuteDateTime, $dataKey, $c_id));
            }
        } else {
            $sql = " UPDATE commodity_m ";
            $sql .= " SET ct_id = ?, ";
            $sql .= " cc_id = ?, ";
            $sql .= " cm_name = ?, ";
            $sql .= " cm_price = ?, ";
            $sql .= " cm_intro = ?, ";
            $sql .= " cm_type = ?, ";
            $sql .= " cm_url = ?, ";
            $sql .= " cm_shipping_fee = ?, ";
            $sql .= " cm_current_stock = ?, ";
            $sql .= " cm_max_buy = ?, ";
            $sql .= " cm_min_buy = ?, ";
            $sql .= " cm_starttime = ?, ";
            $sql .= " cm_endtime = ?, ";
            $sql .= " modify_datetime = ? ";
            $sql .= " WHERE cm_id = ? ";
            $sql .= " AND c_id = ? ";
            $Create_Ary = array();
            array_push($Create_Ary, $ctid_str);
            array_push($Create_Ary, $cc_id);
            array_push($Create_Ary, $cm_name);
            array_push($Create_Ary, $cm_price);
            array_push($Create_Ary, $cm_intro); //5
            array_push($Create_Ary, $cm_type);
            array_push($Create_Ary, $cm_url);
            array_push($Create_Ary, $cm_shipping_fee);
            array_push($Create_Ary, $cm_current_stock);
            array_push($Create_Ary, $cm_max_buy); //10
            array_push($Create_Ary, $cm_min_buy);
            array_push($Create_Ary, $cm_starttime);
            array_push($Create_Ary, $cm_endtime);
            array_push($Create_Ary, $excuteDateTime);
            array_push($Create_Ary, $dataKey); //15
            array_push($Create_Ary, $c_id);
            $mysqli->updatePreSTMT($sql, "ssssssssssssssss", $Create_Ary);
        }
    }
}

//回原本畫面
header("Location:$fileName.php");
?>
