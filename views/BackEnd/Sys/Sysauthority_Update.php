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
//取得新增參數
$programCount = $_REQUEST["programCount"];
//定義時間參數
$excuteDate = date("Ymd"); //操作日期
$excuteTime = date("His"); //操作時間
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
$sql = "DELETE FROM sysauthority WHERE group_id = ?";
$mysqli->deletePreSTMT($sql, "s", array($dataKey));
$sql_str = "";
$program_id_str = "";
$count = 0;
for ($i = 1; $i <= $programCount; $i++) {
    $program_id = $_REQUEST["program_id" . $i];
    $authority_create = !empty($_REQUEST["authority_create" . $i]) ? $_REQUEST["authority_create" . $i] : "N";
    $authority_read = !empty($_REQUEST["authority_read" . $i]) ? $_REQUEST["authority_read" . $i] : "N";
    $authority_update = !empty($_REQUEST["authority_update" . $i]) ? $_REQUEST["authority_update" . $i] : "N";
    $authority_delete = !empty($_REQUEST["authority_delete" . $i]) ? $_REQUEST["authority_delete" . $i] : "N";
    $authority_dsp = !empty($_REQUEST["authority_dsp" . $i]) ? $_REQUEST["authority_dsp" . $i] : "N";
    if ($sql_str == '') {
        $sql_str .= "('" . $dataKey . "', '" . $program_id . "', '" . $authority_create . "', '" . $authority_read . "', '" . $authority_update . "', '" . $authority_delete . "', '" . $authority_dsp . "')";
    } else {
        $sql_str .= ", ('" . $dataKey . "', '" . $program_id . "', '" . $authority_create . "', '" . $authority_read . "', '" . $authority_update . "', '" . $authority_delete . "', '" . $authority_dsp . "')";
    }

    if (!empty($program_id) && $authority_dsp == "Y") {
        if ($count == 0) {
            $program_id_str .= $program_id;
        } else {
            $program_id_str .= ", " . $program_id;
        }
        $count++;
    }
}
$sql = " INSERT INTO sysauthority(group_id, program_id, authority_create, authority_read, authority_update, authority_delete, authority_dsp) VALUES ";
$sql .= $sql_str;
$mysqli->createSTMT($sql);
/* New Group */
$sql = " SELECT Count(menu_id) FROM sysmenu ";
$sql .= " WHERE group_id = ? ";
$Row_count = $mysqli->readValuePreSTMT($sql, "s", array($dataKey));
if ($Row_count == '0') {
    $sql = " SELECT menu_id,  group_id, program_id, menu_type, menu_folder, menu_prev_id, menu_order, menu_icon from sysmenu ";
    $sql .= " WHERE group_id = '1' ";
    $sql .= " AND menu_type = 'P' ";
    $sql .= " AND menu_id in ( select menu_prev_id from sysmenu WHERE group_id = '1' AND program_id in (" . $program_id_str . ")  )";
    $sql .= " union ";
    $sql .= " SELECT menu_id, group_id, program_id, menu_type, menu_folder, menu_prev_id, menu_order, menu_icon from sysmenu ";
    $sql .= " WHERE group_id = '1' ";
    $sql .= " AND program_id in (" . $program_id_str . ") ";
    $menu_p_ary = $mysqli->readArraySTMT($sql, 8);
    for ($i = 0; $i < count($menu_p_ary); $i++) {
        if ($menu_p_ary[$i][3] == "P") {
            $ori_id = $menu_p_ary[$i][0];
            $sql = " SELECT menu_id, group_id, program_id, menu_type, menu_folder, ";
            $sql .= " menu_prev_id, menu_order, menu_icon ";
            $sql .= " from sysmenu ";
            $sql .= " WHERE group_id = '1' ";
            $sql .= " AND menu_prev_id = ? ";
            $sql .= " AND menu_type = 'F' ";
            $sql .= " AND program_id in (" . $program_id_str . ") ";
            $menu_f_ary = $mysqli->readArrayPreSTMT($sql, "s", array($ori_id), 8);
            /*  */
            $sql = " INSERT INTO sysmenu (group_id, program_id, menu_type, menu_folder, menu_prev_id, menu_order, menu_icon) ";
            $sql .= " VALUES ";
            $sql .= " (?, '0', 'P', ?, '0', ?, ?) ";
            $new_p_menu_id = $mysqli->createPreSTMT_CreateId($sql, "ssss", array($dataKey, $menu_p_ary[$i][4], $menu_p_ary[$i][6], $menu_p_ary[$i][7]));
            if (count($menu_f_ary) >= 1) {
                for ($j = 0; $j < count($menu_f_ary); $j++) {
                    $sql = " INSERT INTO sysmenu (group_id, program_id, menu_type, menu_folder, menu_prev_id, menu_order, menu_icon) ";
                    $sql .= " VALUES ";
                    $sql .= " (?, ?, 'F', '', ?, ?, ?) ";
                    $mysqli->createPreSTMT($sql, "sssss", array($dataKey, $menu_f_ary[$j][2], $new_p_menu_id, $menu_f_ary[$j][6], $menu_f_ary[$j][7]));
                }
            }
        }
    }
} else {
    for ($i = 1; $i <= $programCount; $i++) {
        $authority_dsp = !empty($_REQUEST["authority_dsp" . $i]) ? $_REQUEST["authority_dsp" . $i] : "N";
        $program_id = $_REQUEST["program_id" . $i];
        if ($authority_dsp == "Y") {
            $program_id = $_REQUEST["program_id" . $i];
            $sql = "SELECT count(menu_id) FROM sysmenu WHERE group_id = ? AND program_id = ?";
            $rsVal = $mysqli->readValuePreSTMT($sql, "ss", array($dataKey, $program_id));
            if ($rsVal == 0) {
                $sql = " SELECT IFNULL(max(menu_prev_id), 0) AS 'menu_prev_id', IFNULL(max(menu_order), 0) AS 'menu_order'";
                $sql .= " FROM sysmenu ";
                $sql .= " WHERE group_id = ?";
                $menu_ary = $mysqli->readArrayPreSTMT($sql, "s", array($dataKey), 2);
                $sql = " SELECT menu_prev_id, IFNULL(max(menu_order), 0) AS 'menu_order' ";
                $sql .= " FROM sysmenu ";
                $sql .= " WHERE group_id = ?";
                $sql .= " AND menu_type = 'F' ";
                $sql .= " AND menu_prev_id = ? ";
                $menu_ary = $mysqli->readArrayPreSTMT($sql, "ss", array($dataKey, $menu_ary[0][0]), 2);
                $sql = "INSERT INTO sysmenu(group_id, program_id, menu_type, menu_folder, menu_prev_id, menu_order, menu_icon) VALUES(?, ?, ?, ?, ?, ?, '')";
                $mysqli->createPreSTMT($sql, "ssssss", array($dataKey, $program_id, "F", "", $menu_ary[0][0], $menu_ary[0][1] + 1));
            }
        } else if ($authority_dsp == "N") {
            $sql = "DELETE FROM sysmenu WHERE group_id = ? AND program_id = ? ";
            $mysqli->deletePreSTMT($sql, "ss", array($dataKey, $program_id));
        }
    }
}
//回原本畫面
header("Location:$fileName.php");
?>
