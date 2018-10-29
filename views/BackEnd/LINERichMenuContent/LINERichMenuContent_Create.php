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
    $fileName = $_REQUEST["fileName"];
    //來源判斷
    $url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
    $source_url = dirname($url) . '/' . $fileName . '_Add.php';
    if ($_SERVER['HTTP_REFERER'] != $source_url) {
        BackToLoginPage();
        exit;
    } else {
//資料庫連線
        $mysqli = new DatabaseProcessorForWork();
//預設值
        /* Carousel */
        $count_lrcm_img = "";
        $del_text = "";
        /* Default */
        $ctid_str = "";
        $lrcm_img = "";
        $lrcm_cdn_root = "";
        $lrcm_title = "";
        $lrcm_content = "";
        $lrcm_action_type = "";
        $app_id = "";
        $lrcm_url = "";
        $deletestatus = "N";
        $entry_datetime = date("Y-m-d H:i:s");
        $originalImgName = "";
        $thumbnailImgName = "";
//取得新增參數
        $lrcm_keyword = trim($_REQUEST["lrcm_keyword"]); //關鍵字
        $lrcm_keyword = "[" . $lrcm_keyword . "]";
        $lrcm_type = trim($_REQUEST["lrcm_type"]); //類型        
        $sql = " Select count(lrcm_id) from line_richmenu_content_m ";
        $sql .= " where c_id = ? and (lrcm_keyword = ? OR lrcm_keyword = '[提問]') and deletestatus = 'N' ";
        $Rsval = $mysqli->readValuePreSTMT($sql, "ss", array($c_id, $lrcm_keyword));
        if ($Rsval >= '1') {
            echo "<script>";
            echo "alert('關鍵字已經被使用,請重新輸入！');";
            echo "window.location.replace('" . $fileName . ".php'); ";
            echo "</script>";
            exit();
        }
        if ($lrcm_type == '1') {
            $lrcm_title = $_REQUEST["lrcm_title"];
            $lrcm_content = $_REQUEST["lrcm_content"];
        } else if ($lrcm_type == '2') {
            $count_lrcm_img = $_REQUEST["count_lrcm_img"];
            $del_text = $_REQUEST["del_text"];
            $AddBtnAay = array();
            for ($i = 1; $i <= $count_lrcm_img; $i++) {
                array_push($AddBtnAay, $i);
            }
            $DelAry = explode(",", $del_text);
            $AddAry = array();
            $NewArray1 = array_diff($AddBtnAay, $DelAry);
            for ($i = 1; $i <= $count_lrcm_img; $i++) {
                if (isset($NewArray1[$i - 1]) != '') {
                    array_push($AddAry, $NewArray1[$i - 1]);
                }
            }
        } else if ($lrcm_type == '3') {
            if (!empty($_POST["ct_id"])) {
                $ct_id = $_POST["ct_id"];
                $ctid_str = implode(",", $ct_id);
            }
            $lrcm_title = $_REQUEST["lrcm_title"];
            $lrcm_action_type = $_REQUEST["lrcm_action_type"];
            //FILE UPLOAD STATUS
            if (!file_exists($_FILES['lrcm_img']['tmp_name'][0]) || !is_uploaded_file($_FILES['lrcm_img']['tmp_name'][0])) {
                //'No File Upload!';
            } else {
                //FILE
                $oldfileName = $_FILES['lrcm_img']['name'][0];
                $newfileName = $c_id . "_" . date("YmdHis");
                $tempFilePath = $_FILES['lrcm_img']['tmp_name'][0];
                $serverFilePath = "assets_rear/images/line_menu_content_m/" . $c_id;
                $ThumbnailSize = 250;
                $pictureFile = new BackEndPictureFileForWork($oldfileName, $newfileName, $tempFilePath, $serverFilePath, $ThumbnailSize);
                $pictureFile->createFolder("assets_rear/images/line_menu_content_m");
                $pictureFile->createFolder("assets_rear/images/line_menu_content_m/" . $c_id);
                $originalImgName = $pictureFile->archiveWithoutReSizePictureFile();
                $thumbnailImgName = $pictureFile->archiveWithReSizePictureFile();
                /* 上傳檔案到CDN */
//                $UploadDir = ROOT_PATH . '/' . $originalImgName;
//                $CDN_FILE_NAME = substr($originalImgName, -37);
//                $lrcm_cdn_root = CDN_LINE_MENU_CONTENT_M . $c_id . '/' . $CDN_FILE_NAME;
//                $s3->putObject($UploadDir, $lrcm_cdn_root);
                //檔案路徑
                $lrcm_img = $originalImgName;
            }
            if ($lrcm_action_type == '1') {
                $app_id = $_REQUEST["key_id"];
                $lrcm_url = "";
            } else if ($lrcm_action_type == '2') {
                $app_id = "";
                $lrcm_url = !empty($_REQUEST["lrcm_url"]) ? $_REQUEST["lrcm_url"] : "";
            } else if ($lrcm_action_type == '3') {
                $app_id = "";
                $lrcm_url = "";
            } else if ($lrcm_action_type == '4') {
                $app_id = $_REQUEST["cm_id"];
                $lrcm_url = "";
                //商品類
                $sql = " SELECT cm_name, cm_intro, cm_img, cm_cdn_root FROM commodity_m ";
                $sql .= " WHERE c_id = ? ";
                $sql .= " AND cm_id = ? ";
                $cm_ary = $mysqli->readArrayPreSTMT($sql, "ss", array($c_id, $app_id), 4);
                $lrcm_title = $cm_ary[0][0];
                $lrcm_content = $cm_ary[0][1];
                $lrcm_img = $cm_ary[0][2];
                $lrcm_cdn_root = $cm_ary[0][3];
            }
        }
        $sql = " INSERT INTO line_richmenu_content_m ";
        $sql .= " ( c_id, lrcm_type, lrcm_keyword, ct_id, lrcm_img,  ";
        $sql .= " lrcm_cdn_root, lrcm_title, lrcm_content, lrcm_action_type, app_id,  ";
        $sql .= " lrcm_url, deletestatus, entry_datetime, modify_datetime )";
        $sql .= " VALUES ";
        $sql .= " ( ?, ?, ?, ?, ?, ";
        $sql .= " ?, ?, ?, ?, ?, ";
        $sql .= " ?, ?, ?, ? ) ";
        $Create_Ary = array();
        array_push($Create_Ary, $c_id);
        array_push($Create_Ary, $lrcm_type);
        array_push($Create_Ary, $lrcm_keyword);
        array_push($Create_Ary, $ctid_str);
        array_push($Create_Ary, $lrcm_img);
        array_push($Create_Ary, $lrcm_cdn_root);
        array_push($Create_Ary, $lrcm_title);
        array_push($Create_Ary, $lrcm_content);
        array_push($Create_Ary, $lrcm_action_type);
        array_push($Create_Ary, $app_id);
        array_push($Create_Ary, $lrcm_url);
        array_push($Create_Ary, $deletestatus);
        array_push($Create_Ary, $entry_datetime);
        array_push($Create_Ary, $entry_datetime);
        $InsertId = $mysqli->createPreSTMT_CreateId($sql, "ssssssssssssss", $Create_Ary);

        if ($lrcm_type == '2') {
            $lrct_img = "";
            $lrct_cdn_root = "";
            for ($i = 0; $i < count($AddAry); $i++) {
                $lrcm_title = $_REQUEST["lrcm_title"][$i];
                $lrcm_content = $_REQUEST["lrcm_content"][$i];
                if (!empty($_REQUEST["ct_id" . $AddAry[$i]])) {
                    $ct_id = $_REQUEST["ct_id" . $AddAry[$i]];
                    $ctid_str = implode(",", $ct_id);
                } else {
                    $ctid_str = "";
                }
                ${"lrcm_action_type" . $AddAry[$i]} = $_REQUEST["lrcm_action_type" . $AddAry[$i]];
                $lrct_action_type = ${"lrcm_action_type" . $AddAry[$i]};
                $lrct_action_type = $lrct_action_type[0];
                //FILE UPLOAD STATUS
                if (!file_exists($_FILES['lrcm_img']['tmp_name'][$i]) || !is_uploaded_file($_FILES['lrcm_img']['tmp_name'][$i])) {
                    //'No File Upload!';
                } else {
                    $oldfileName = $_FILES['lrcm_img']['name'][$i];
                    /* GetRandNum() 3碼 */
                    $RandomNumber = GetRandNum();
                    $newfileName = $c_id . "_" . date("YmdHis") . $RandomNumber;
                    $tempFilePath = $_FILES['lrcm_img']['tmp_name'][$i];
                    $serverFilePath = "assets_rear/images/line_menu_content_t/" . $c_id;
                    $ThumbnailSize = 250;
                    $pictureFile = new BackEndPictureFileForWork($oldfileName, $newfileName, $tempFilePath, $serverFilePath, $ThumbnailSize);
                    $pictureFile->createFolder("assets_rear/images/line_menu_content_t");
                    $pictureFile->createFolder("assets_rear/images/line_menu_content_t/" . $c_id);
                    $originalImgName = $pictureFile->archiveWithoutReSizePictureFile();
                    $thumbnailImgName = $pictureFile->archiveWithReSizePictureFile();
                    /* 上傳檔案到CDN */
//                    $UploadDir = ROOT_PATH . '/' . $originalImgName;
//                    $CDN_FILE_NAME = substr($originalImgName, -40);
//                    $lrct_cdn_root = CDN_LINE_MENU_CONTENT_T . $c_id . '/' . $CDN_FILE_NAME;
//                    $s3->putObject($UploadDir, $lrct_cdn_root);
                    //檔案路徑
                    $lrct_img = $originalImgName;
                }
                /* 應用 */
                if ($lrct_action_type == '1') {
                    $app_id = !empty($_REQUEST["key_id"][$i]) ? $_REQUEST["key_id"][$i] : "";
                    $lrcm_url = "";
                } else if ($lrct_action_type == '2') {
                    $app_id = "";
                    $lrcm_url = !empty($_REQUEST["lrcm_url"]) ? $_REQUEST["lrcm_url"] : "";
                } else if ($lrct_action_type == '3') {
                    $app_id = "";
                    $lrcm_url = "";
                } else if ($lrct_action_type == '4') {
                    $app_id = !empty($_REQUEST["cm_id"][$i]) ? $_REQUEST["cm_id"][$i] : "";
                    $lrcm_url = "";
                    //商品類
                    $sql = " SELECT cm_name, cm_intro, cm_img, cm_cdn_root FROM commodity_m ";
                    $sql .= " WHERE c_id = ? ";
                    $sql .= " AND cm_id = ? ";
                    $cm_ary = $mysqli->readArrayPreSTMT($sql, "ss", array($c_id, $app_id), 4);
                    $lrcm_title = $cm_ary[0][0];
                    $lrcm_content = $cm_ary[0][1];
                    $lrct_img = $cm_ary[0][2];
                    $lrct_cdn_root = $cm_ary[0][3];
                }
                $lrcm_url = !empty($_REQUEST["lrcm_url"][$i]) ? $_REQUEST["lrcm_url"][$i] : "";
                $lrct_sort = $_REQUEST["lrct_sort"][$i];
                $deletestatus = "N";

                /* INSERT DATA */
                $sql = " INSERT INTO line_richmenu_content_t ";
                $sql .= " ( lrcm_id, c_id, ct_id, lrct_img, lrct_cdn_root, ";
                $sql .= " lrct_title, lrct_content, lrct_action_type, app_id, lrct_url, ";
                $sql .= " lrct_sort, deletestatus, entry_datetime, modify_datetime ) ";
                $sql .= " VALUES ";
                $sql .= " ( ?, ?, ?, ?, ?, ";
                $sql .= " ?, ?, ?, ?, ?, ";
                $sql .= " ?, ?, ?, ? ) ";
                $Create_Ary = array();
                array_push($Create_Ary, $InsertId);
                array_push($Create_Ary, $c_id);
                array_push($Create_Ary, $ctid_str);
                array_push($Create_Ary, $lrct_img);
                array_push($Create_Ary, $lrct_cdn_root);
                array_push($Create_Ary, $lrcm_title);
                array_push($Create_Ary, $lrcm_content);
                array_push($Create_Ary, $lrct_action_type);
                array_push($Create_Ary, $app_id);
                array_push($Create_Ary, $lrcm_url);
                array_push($Create_Ary, $lrct_sort);
                array_push($Create_Ary, $deletestatus);
                array_push($Create_Ary, $entry_datetime);
                array_push($Create_Ary, $entry_datetime);
                $mysqli->createPreSTMT($sql, "ssssssssssssss", $Create_Ary);
            }
        }
    }
}

//回原本畫面
header("Location:$fileName.php");
?>
