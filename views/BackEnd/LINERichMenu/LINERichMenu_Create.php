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
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    /* 執行 */
    ob_end_clean();
    header("Connection: close");
    ignore_user_abort(true);
    set_time_limit(0);
//資料庫連線
    $mysqli = new DatabaseProcessorForWork();
//取得固定參數
    $c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
    $fileName = $_REQUEST["fileName"];
//取得新增參數
    $rs_title = $_REQUEST["rs_title"]; /* Menu 標題 */
    $rsm_type = $_REQUEST["rsm_type"]; /* Menu 類型 */
    $useValue = $_REQUEST["useValue"]; /* 套用 */
    $rsm_status = "N"; /* 使用狀態 */
    /* get token */
    $sql = " select c_line_TOKEN from crm_m ";
    $sql .= " where c_id = ? ";
    $accessToken = $mysqli->readValuePreSTMT($sql, "s", array($c_id));
//定義時間參數
    $excuteDateTime = date("Y-m-d H:i:s"); //操作日期
    $originalImgName = "";
    $thumbnailImgName = "";
    //圖檔處理
    $oldfileName = $_FILES['rs_img']['name'];
    $newfileName = date("YmdHis");
    $tempFilePath = $_FILES['rs_img']['tmp_name'];
    $serverFilePath = "assets_rear/images/richmenu/" . $c_id;
    $ThumbnailSize = 250;
    $pictureFile = new BackEndPictureFileForWork($oldfileName, $newfileName, $tempFilePath, $serverFilePath, $ThumbnailSize);
    $pictureFile->createFolder("assets_rear/images/richmenu");
    $originalImgName = $pictureFile->archiveWithoutReSizePictureFile();
    $thumbnailImgName = $pictureFile->archiveWithReSizePictureFile();

    $action_type = '';
    $action_type_option = '';
    $action = '';
    if ($rsm_type == '1') {
        $area_content = '{';
        for ($i = 0; $i < 6; $i++) {
            $number = $i + 1;
            $rst_type = $_REQUEST["rst_type_" . $number];
            if ($rst_type == '1') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = "action=13&key=" . $_REQUEST["lrcm_keyword"][$i];
            } else if ($rst_type == '2') {
                $action_type = 'uri';
                $action_type_option = 'uri';
                $action = $_REQUEST["rst_url"][$i];
            } else if ($rst_type == '3') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = 'action=';
            } else if ($rst_type == '4') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = 'action=14';
            } else if ($rst_type == '5') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = 'action=997';
            }
            if ($i == 0) {
                $area_content .= '"bounds": {"x": 0,"y": 0,"width": 833,"height": 843},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}},';
            } else if ($i == 1) {
                $area_content .= '{"bounds": {"x": 833,"y": 0,"width": 833,"height": 843},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}},';
            } else if ($i == 2) {
                $area_content .= '{"bounds": {"x": 1666,"y": 0,"width": 834,"height": 843},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}},';
            } else if ($i == 3) {
                $area_content .= '{"bounds": {"x": 0,"y": 843,"width": 833,"height": 1686},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}},';
            } else if ($i == 4) {
                $area_content .= '{"bounds": {"x": 833,"y": 843,"width": 833,"height": 1686},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}},';
            } else if ($i == 5) {
                $area_content .= '{"bounds": {"x": 1666,"y": 843,"width": 834,"height": 1686},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}';
            }
        }
        $area_content .= '}';
    } else if ($rsm_type == '2') {
        $area_content = '{';
        for ($i = 0; $i < 4; $i++) {
            $number = $i + 1;
            $rst_type = $_REQUEST["rst_type_" . $number];
            if ($rst_type == '1') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = "action=13&key=" . $_REQUEST["lrcm_keyword"][$i];
            } else if ($rst_type == '2') {
                $action_type = 'uri';
                $action_type_option = 'uri';
                $action = $_REQUEST["rst_url"][$i];
            } else if ($rst_type == '3') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = 'action=';
            } else if ($rst_type == '4') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = 'action=14';
            } else if ($rst_type == '5') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = 'action=997';
            }
            if ($i == 0) {
                $area_content .= '"bounds": {"x": 0,"y": 0,"width": 1250,"height": 843},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}},';
            } else if ($i == 1) {
                $area_content .= '{"bounds": {"x": 1250,"y": 0,"width": 2500,"height": 843},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}},';
            } else if ($i == 2) {
                $area_content .= '{"bounds": {"x": 0,"y": 843,"width": 1250,"height": 1686},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}},';
            } else if ($i == 3) {
                $area_content .= '{"bounds": {"x": 1250,"y": 843,"width": 2500,"height": 1686},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}';
            }
        }
        $area_content .= '}';
    } else if ($rsm_type == '3') {
        $area_content = '{';
        for ($i = 0; $i < 4; $i++) {
            $number = $i + 1;
            $rst_type = $_REQUEST["rst_type_" . $number];
            if ($rst_type == '1') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = "action=13&key=" . $_REQUEST["lrcm_keyword"][$i];
            } else if ($rst_type == '2') {
                $action_type = 'uri';
                $action_type_option = 'uri';
                $action = $_REQUEST["rst_url"][$i];
            } else if ($rst_type == '3') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = 'action=';
            } else if ($rst_type == '4') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = 'action=14';
            } else if ($rst_type == '5') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = 'action=997';
            }
            if ($i == 0) {
                $area_content .= '"bounds": {"x": 0,"y": 0,"width": 2500,"height": 843},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}},';
            } else if ($i == 1) {
                $area_content .= '{"bounds": {"x": 0,"y": 843,"width": 833,"height": 1686},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}},';
            } else if ($i == 2) {
                $area_content .= '{"bounds": {"x": 833,"y": 843,"width": 833,"height": 1686},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}},';
            } else if ($i == 3) {
                $area_content .= '{"bounds": {"x": 1666,"y": 843,"width": 834,"height": 1686},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}';
            }
        }
        $area_content .= '}';
    } else if ($rsm_type == '4') {
        $area_content = '{';
        for ($i = 0; $i < 3; $i++) {
            $number = $i + 1;
            $rst_type = $_REQUEST["rst_type_" . $number];
            if ($rst_type == '1') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = "action=13&key=" . $_REQUEST["lrcm_keyword"][$i];
            } else if ($rst_type == '2') {
                $action_type = 'uri';
                $action_type_option = 'uri';
                $action = $_REQUEST["rst_url"][$i];
            } else if ($rst_type == '3') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = 'action=';
            } else if ($rst_type == '4') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = 'action=14';
            } else if ($rst_type == '5') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = 'action=997';
            }
            if ($i == 0) {
                $area_content .= '"bounds": {"x": 0,"y": 0,"width": 1666,"height": 1666},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}},';
            } else if ($i == 1) {
                $area_content .= '{"bounds": {"x": 1666,"y": 0,"width": 2500,"height": 843},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}},';
            } else if ($i == 2) {
                $area_content .= '{"bounds": {"x": 1666,"y": 843,"width": 2500,"height": 1686},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}';
            }
        }
        $area_content .= '}';
    } else if ($rsm_type == '5') {
        $area_content = '{';
        for ($i = 0; $i < 2; $i++) {
            $number = $i + 1;
            $rst_type = $_REQUEST["rst_type_" . $number];
            if ($rst_type == '1') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = "action=13&key=" . $_REQUEST["lrcm_keyword"][$i];
            } else if ($rst_type == '2') {
                $action_type = 'uri';
                $action_type_option = 'uri';
                $action = $_REQUEST["rst_url"][$i];
            } else if ($rst_type == '3') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = 'action=';
            } else if ($rst_type == '4') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = 'action=14';
            } else if ($rst_type == '5') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = 'action=997';
            }
            if ($i == 0) {
                $area_content .= '"bounds": {"x": 0,"y": 0,"width": 2500,"height": 843},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}},';
            } else if ($i == 1) {
                $area_content .= '{"bounds": {"x": 0,"y": 843,"width": 2500,"height": 1686},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}';
            }
        }
        $area_content .= '}';
    } else if ($rsm_type == '6') {
        $area_content = '{';
        for ($i = 0; $i < 2; $i++) {
            $number = $i + 1;
            $rst_type = $_REQUEST["rst_type_" . $number];
            if ($rst_type == '1') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = "action=13&key=" . $_REQUEST["lrcm_keyword"][$i];
            } else if ($rst_type == '2') {
                $action_type = 'uri';
                $action_type_option = 'uri';
                $action = $_REQUEST["rst_url"][$i];
            } else if ($rst_type == '3') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = 'action=';
            } else if ($rst_type == '4') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = 'action=14';
            } else if ($rst_type == '5') {
                $action_type = 'postback';
                $action_type_option = 'data';
                $action = 'action=997';
            }
            if ($i == 0) {
                $area_content .= '"bounds": {"x": 0,"y": 0,"width": 1250,"height": 1686},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}},';
            } else if ($i == 1) {
                $area_content .= '{"bounds": {"x": 1250,"y": 0,"width": 2500,"height": 1686},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}';
            }
        }
        $area_content .= '}';
    } else if ($rsm_type == '7') {
        $rst_type = $_REQUEST["rst_type_1"]; /* 1:關鍵 ,2:網址 ,3:不設定 */
        if ($rst_type == '1') {
            $action_type = 'postback';
            $action_type_option = 'data';
            $action = "action=13&key=" . $_REQUEST["lrcm_keyword"][0];
        } else if ($rst_type == '2') {
            $action_type = 'uri';
            $action_type_option = 'uri';
            $action = $_REQUEST["rst_url"][0];
        } else if ($rst_type == '3') {
            $action_type = 'postback';
            $action_type_option = 'data';
            $action = 'action=';
        } else if ($rst_type == '4') {
            $action_type = 'postback';
            $action_type_option = 'data';
            $action = 'action=14';
        } else if ($rst_type == '5') {
            $action_type = 'postback';
            $action_type_option = 'data';
            $action = 'action=997';
        }
        $area_content = '{"bounds": {"x": 0,"y": 0,"width": 2500,"height": 1686},"action": {"type": "' . $action_type . '","' . $action_type_option . '": "' . $action . '"}}';
    } else {
        header("Location:$fileName.php");
        exit();
    }
    /* Menu */
    $LineRichMenu = new BackEndLineRichMenuForWork($accessToken);
    /* Create */
    $richMenuId = $LineRichMenu->CreateMenuId($rs_title, $area_content);
    if ($richMenuId === FALSE) {
        error_log($c_id . "RichMenu FALSE!");
        header("Location:$fileName.php");
        exit();
    }

    /* 上傳Menu圖檔 richMenuId */
    $imagePath = ROOT_PATH . $originalImgName;
    $ContentLength = strlen($imagePath);
    $PhotoStatus = $LineRichMenu->UploadMenuPhoto($richMenuId, $imagePath);
    if (!$PhotoStatus) {
        error_log($c_id . "->" . $richMenuId . ": PIC FALSE!");
        exit();
    }
    /* INSERT DATA */
    $sql = "INSERT INTO richmenu_set_m ";
    $sql .= "( richmenu_id, rsm_title, rsm_img, rsm_type, rsm_status, ";
    $sql .= " c_id, entry_datetime ) ";
    $sql .= " VALUES ";
    $sql .= " (?, ?, ?, ?, ?, ";
    $sql .= " ?, ? )";
    $mysqli->createPreSTMT($sql, "sssssss", array($richMenuId, $rs_title, $originalImgName, $rsm_type, $rsm_status, $c_id, $excuteDateTime));
    if ($rsm_type == '1') {
        $count_space = 6;
        $ary = array(array(0, 0, 833, 843), array(833, 0, 833, 843), array(1666, 0, 834, 843), array(0, 843, 833, 1686), array(833, 843, 833, 1686), array(1666, 843, 834, 1686));
    } else if ($rsm_type == '2') {
        $count_space = 4;
        $ary = array(array(0, 0, 1250, 843), array(1250, 0, 2500, 843), array(0, 843, 1250, 1686), array(1250, 843, 2500, 1686));
    } else if ($rsm_type == '3') {
        $count_space = 4;
        $ary = array(array(0, 0, 2500, 843), array(0, 843, 833, 1686), array(833, 843, 833, 1686), array(1666, 843, 834, 1686));
    } else if ($rsm_type == '4') {
        $count_space = 3;
        $ary = array(array(0, 0, 1666, 1686), array(1666, 0, 2500, 843), array(1666, 843, 2500, 1686));
    } else if ($rsm_type == '5') {
        $count_space = 2;
        $ary = array(array(0, 0, 2500, 843), array(0, 843, 2500, 1686));
    } else if ($rsm_type == '6') {
        $count_space = 2;
        $ary = array(array(0, 0, 1250, 1686), array(1250, 0, 2500, 1686));
    } else if ($rsm_type == '7') {
        $count_space = 1;
        $ary = array(array(0, 0, 2500, 1686));
    }

    for ($i = 0; $i < $count_space; $i++) {
        $number = $i + 1;
        $rst_type = $_REQUEST["rst_type_" . $number];
        if ($rst_type == '1') {
            $keyword = $_REQUEST["lrcm_keyword"][$i];
            $url = '';
        } else if ($rst_type == '2') {
            $keyword = '';
            $url = $_REQUEST["rst_url"][$i];
        } else if ($rst_type == '3') {
            $keyword = '';
            $url = '';
        } else if ($rst_type == '4') {
            $keyword = '';
            $url = '';
        } else if ($rst_type == '5') {
            $keyword = '';
            $url = '';
        }
        /*  */
        $x = $ary[$i][0];
        $y = $ary[$i][1];
        $width = $ary[$i][2];
        $height = $ary[$i][3];
        /*  */
        $sql = " INSERT INTO richmenu_set_t ";
        $sql .= " ( richmenu_id, rst_type, lrcm_keyword, rst_url, rst_x, ";
        $sql .= " rst_y, rst_width, rst_height ) ";
        $sql .= " VALUES ";
        $sql .= " (?, ?, ?, ?, ?, ";
        $sql .= " ?, ?, ? )";
        $mysqli->createPreSTMT($sql, "ssssssss", array($richMenuId, $rst_type, $keyword, $url, $x, $y, $width, $height));
    }

    /* Set Rich Menu With User */
    if ($useValue == "1") {
        $post_data = array("c_id" => "$c_id", "richmenu_id" => "$richMenuId");
        $ch = curl_init(WEB_HOSTNAME . "/api/scrm/LINERichMenu/LINERichMenu_API.php");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=UTF-8',
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        /* update */
        $sql = " UPDATE richmenu_set_m SET rsm_status = 'N' WHERE c_id = ?";
        $mysqli->updatePreSTMT($sql, "s", array($c_id));
        $sql = " UPDATE richmenu_set_m SET rsm_status = 'Y' WHERE c_id = ? AND richmenu_id = ? ";
        $mysqli->updatePreSTMT($sql, "ss", array($c_id, $richMenuId));
    }
}

//回原本畫面
header("Location:$fileName.php");
?>
