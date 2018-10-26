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
//取得固定參數
$c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
//取得接受參數
$menu_type = !empty($_REQUEST["menu_type"]) ? $_REQUEST["menu_type"] : "";
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
if ($menu_type == '') {
    exit;
}
/* Data */
$sql = " SELECT '0', '[提問]' ";
$sql .= " FROM line_richmenu_content_m ";
$sql .= " UNION ";
$sql .= " (SELECT lrcm_id, lrcm_keyword FROM line_richmenu_content_m ";
$sql .= " WHERE c_id = ? ";
$sql .= " AND deletestatus = 'N') ";
$lrcm_Ary = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);

$str = "";
$str2 = "";
if ($menu_type == 1) {
    $str = '<div class="col-sm-4">';
    $str .= '<ul class="li1_1">';
    $str .= '<li class="col-xs-4">';
    $str .= '<a href="#" id="" class="btn-input" value="1" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '<li class="col-xs-4">';
    $str .= '<a href="#" id="" class="btn-input" value="2" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '<li class="col-xs-4">';
    $str .= '<a href="#" id="" class="btn-input" value="3" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '</ul>';
    $str .= '<ul class="li1_2">';
    $str .= '<li class="col-xs-4">';
    $str .= '<a href="#" id="" class="btn-input" value="4" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '<li class="col-xs-4">';
    $str .= '<a href="#" id="" class="btn-input" value="5" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '<li class="col-xs-4">';
    $str .= '<a href="#" id="" class="btn-input" value="6" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '</ul>';
    $str .= '</div>';
    //space
    $str2 = '<div id="radio_choose_1" style="display:inline;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_1_1" class="input_1" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';
    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_1_2" class="input_1" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    $str2 .= '<div id="radio_choose_2" style="display:none;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_2_1" class="input_2" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_2_2" class="input_2" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    $str2 .= '<div id="radio_choose_3" style="display:none;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_3" name="rst_type_3" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_3" name="rst_type_3" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_3" name="rst_type_3" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_3" name="rst_type_3" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_3" name="rst_type_3" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_3_1" class="input_3" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_3_2" class="input3" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    $str2 .= '<div id="radio_choose_4" style="display:none;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_4" name="rst_type_4" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_4" name="rst_type_4" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_4" name="rst_type_4" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_4" name="rst_type_4" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_4" name="rst_type_4" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_4_1" class="input_4" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_4_2" class="input_4" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    $str2 .= '<div id="radio_choose_5" style="display:none;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_5" name="rst_type_5" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_5" name="rst_type_5" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_5" name="rst_type_5" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_5" name="rst_type_5" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_5" name="rst_type_5" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_5_1" class="input_5" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_5_2" class="input_5" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    $str2 .= '<div id="radio_choose_6" style="display:none;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_6" name="rst_type_6" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_6" name="rst_type_6" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_6" name="rst_type_6" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_6" name="rst_type_6" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_6" name="rst_type_6" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_6_1" class="input_6" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_6_2" class="input_6" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    //space 
} else if ($menu_type == 2) {
    $str = '<div class="col-sm-4">';
    $str .= '<ul class="li2_1">';
    $str .= '<li class="col-xs-6">';
    $str .= '<a href="#" id="" class="btn-input" value="1" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '<li class="col-xs-6">';
    $str .= '<a href="#" id="" class="btn-input" value="2" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '</ul>';
    $str .= '<ul class="li2_2">';
    $str .= '<li class="col-xs-6">';
    $str .= '<a href="#" id="" class="btn-input" value="3" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '<li class="col-xs-6">';
    $str .= '<a href="#" id="" class="btn-input" value="4" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '</ul>';
    $str .= '</div>';
    //space
    $str2 = '<div id="radio_choose_1" style="display:inline;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_1_1" class="input_1" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_1_2" class="input_1" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    $str2 .= '<div id="radio_choose_2" style="display:none;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_2_1" class="input_2" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_2_2" class="input_2" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    $str2 .= '<div id="radio_choose_3" style="display:none;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_3" name="rst_type_3" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_3" name="rst_type_3" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_3" name="rst_type_3" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_3" name="rst_type_3" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_3" name="rst_type_3" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_3_1" class="input_3" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_3_2" class="input_3" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    $str2 .= '<div id="radio_choose_4" style="display:none;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_4" name="rst_type_4" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_4" name="rst_type_4" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_4" name="rst_type_4" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_4" name="rst_type_4" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_4" name="rst_type_4" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_4_1" class="input_4" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_4_2" class="input_4" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    //space 
} else if ($menu_type == 3) {
    $str = '<div class="col-sm-4">';
    $str .= '<ul>';
    $str .= '<li class="col-xs-12" style="display:inline;height:60px;background-color: #FFC0CB;">';
    $str .= '<a href="#" id="" class="btn-input" value="1" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '</ul>';
    $str .= '<ul class="li3">';
    $str .= '<li class="col-xs-4">';
    $str .= '<a href="#" id="" class="btn-input" value="2" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '<li class="col-xs-4">';
    $str .= '<a href="#" id="" class="btn-input" value="3" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '<li class="col-xs-4">';
    $str .= '<a href="#" id="" class="btn-input" value="4" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '</ul>';
    $str .= '</div>';
    //space
    $str2 = '<div id="radio_choose_1" style="display:inline;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_1_1" class="input_1" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_1_2" class="input_1" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    $str2 .= '<div id="radio_choose_2" style="display:none;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_2_1" class="input_2" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_2_2" class="input_2" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    $str2 .= '<div id="radio_choose_3" style="display:none;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_3" name="rst_type_3" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_3" name="rst_type_3" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_3" name="rst_type_3" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_3" name="rst_type_3" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_3" name="rst_type_3" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_3_1" class="input_3" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_3_2" class="input_3" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    $str2 .= '<div id="radio_choose_4" style="display:none;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_4" name="rst_type_4" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_4" name="rst_type_4" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_4" name="rst_type_4" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_4" name="rst_type_4" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_4" name="rst_type_4" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_4_1" class="input_4" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_4_2" class="input_4" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    //space
} else if ($menu_type == 4) {
    $str = '<div class="col-sm-4">';
    $str .= '<ul class="li4">';
    $str .= '<li class="col-sm-8">';
    $str .= '<a href="#" id="" class="btn-input" value="1"" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '<li class="col-xs-4">';
    $str .= '<a href="#" id="" class="btn-input" value="2" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '<li class="col-xs-4">';
    $str .= '<a href="#" id="" class="btn-input" value="3"" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '</ul>';
    $str .= '</div>';
    //space
    $str2 = '<div id="radio_choose_1" style="display:inline;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_1_1" class="input_1" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_1_2" class="input_1" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    $str2 .= '<div id="radio_choose_2" style="display:none;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_2_1" class="input_2" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_2_2" class="input_2" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    $str2 .= '<div id="radio_choose_3" style="display:none;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_3" name="rst_type_3" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_3" name="rst_type_3" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_3" name="rst_type_3" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_3" name="rst_type_3" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_3" name="rst_type_3" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_3_1" class="input_3" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_3_2" class="input_3" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    //space
} else if ($menu_type == 5) {
    $str = '<div class="col-sm-4">';
    $str .= '<ul class="li5">';
    $str .= '<li class="col-xs-12">';
    $str .= '<a href="#" id="" class="btn-input" value="1" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '<li class="col-xs-12">';
    $str .= '<a href="#" id="" class="btn-input" value="2" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '</ul>';
    $str .= '</div>';
    //space
    $str2 = '<div id="radio_choose_1" style="display:inline;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_1_1" class="input_1" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_1_2" class="input_1" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    $str2 .= '<div id="radio_choose_2" style="display:none;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_2_1" class="input_2" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_2_2" class="input_2" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    //space
} else if ($menu_type == 6) {
    $str = '<div class="col-sm-4">';
    $str .= '<ul class="li6">';
    $str .= '<li class="col-xs-6">';
    $str .= '<a href="#" id="" class="btn-input" value="1" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '<li class="col-xs-6">';
    $str .= '<a href="#" id="" class="btn-input" value="2" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '</ul>';
    $str .= '</div>';
    //space
    $str2 = '<div id="radio_choose_1" style="display:inline;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_1_1" class="input_1" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_1_2" class="input_1" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    $str2 .= '<div id="radio_choose_2" style="display:none;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_2" name="rst_type_2" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_2_1" class="input_2" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_2_2" class="input_2" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    //space
} else if ($menu_type == 7) {
    $str = '<div class="col-sm-4">';
    $str .= '<ul class="li7">';
    $str .= '<li class="col-xs-12">';
    $str .= '<a href="#" id="" class="btn-input" value="1" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '</li>';
    $str .= '</ul>';
    $str .= '</div>';
    //space
    $str2 = '<div id="radio_choose_1" style="display:inline;" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">關鍵字</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="2" type="radio">';
    $str2 .= '<span class="text">網址</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="3" type="radio">';
    $str2 .= '<span class="text">不要設定</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="4" type="radio">';
    $str2 .= '<span class="text">分享換好康</span>';
    $str2 .= '</label>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_1" name="rst_type_1" class="radio_check" value="5" type="radio">';
    $str2 .= '<span class="text">線上客服</span>';
    $str2 .= '</label>';
    $str2 .= '<div id="input_1_1" class="input_1" style="display:inline;">';
    $str2 .= '<select id="lrcm_keyword" name="lrcm_keyword[]" class="e1" style="width: 100%;">';

    foreach ($lrcm_Ary as $rsAry) {
        $str2 .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str2 .= '</select>';
    $str2 .= '</div>';
    $str2 .= '<div id="input_1_2" class="input_1" style="display:none;">';
    $str2 .= '<input type="text" id="rst_url" name="rst_url[]"  maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
    //space
}

echo json_encode(array("str1" => $str, "str2" => $str2));
?>

