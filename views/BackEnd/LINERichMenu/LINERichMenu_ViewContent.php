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
$dataKey = !empty($_REQUEST["dataKey"]) ? $_REQUEST["dataKey"] : "";
$menu_type = !empty($_REQUEST["menu_type"]) ? $_REQUEST["menu_type"] : "";
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
if ($menu_type == '' || $dataKey == '') {
    exit;
}
/* Data */
$sql = " SELECT lrcm_id, lrcm_keyword FROM line_richmenu_content_m ";
$sql .= " WHERE c_id = ? ";
$sql .= " AND deletestatus = 'N' ";
$lrcm_Ary = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);
/* Detail */
$sql = " SELECT rst_type, ";
$sql .= " CASE rst_type WHEN '1' THEN '關鍵字' WHEN '2' THEN '網址' WHEN '3' THEN '不要設定' WHEN '4' THEN '分享換好康' WHEN '5' THEN '線上客服' END AS TITLE, ";
$sql .= " CASE rst_type WHEN '1' THEN ";
$sql .= " CASE lrcm_keyword WHEN '0' THEN '提問' ";
$sql .= " ELSE ( SELECT lrcm.lrcm_keyword FROM line_richmenu_content_m lrcm WHERE t.lrcm_keyword = lrcm.lrcm_id ) ";
$sql .= " END ";
$sql .= " WHEN '2' THEN rst_url ";
$sql .= " WHEN '3' THEN '' ";
$sql .= " WHEN '4' THEN '' ";
$sql .= " WHEN '5' THEN '線上客服' ";
$sql .= " END AS VALUE ";
$sql .= " FROM richmenu_set_t t ";
$sql .= " WHERE richmenu_id = ? ";
$rst_Ary = $mysqli->readArrayPreSTMT($sql, "s", array($dataKey), 3);

$str = "";
$str2 = "";

for ($i = 0; $i < count($rst_Ary); $i++) {
    $number = $i + 1;
    if ($i == 0) {
        $style = 'inline';
    } else {
        $style = 'none';
    }
    $str2 .= '<div id="radio_choose_' . $number . '" style="display:' . $style . ';" class="col-xs-8">';
    /* radio */
    $str2 .= '<div class="hr-space space-x1"></div>';
    $str2 .= '<label style="margin-right:10px;">';
    $str2 .= '<input id ="rst_type_' . $number . '" name="rst_type_' . $number . '" class="radio_check" value="1" type="radio" checked="checked">';
    $str2 .= '<span class="text">' . $rst_Ary[$i][1] . '</span>';
    $str2 .= '</label>';
    $str2 .= '<div class="input_' . $number . '">';
    $str2 .= '<input type="text" id="default_input" name="default_input" value="' . $rst_Ary[$i][2] . '" readonly maxlength="500" class="form-control" >';
    $str2 .= '</div>';
    /* radio end */
    $str2 .= '</div>';
}

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
} else if ($menu_type == 7) {
    $str = '<div class="col-sm-4">';
    $str .= '<ul class="li7">';
    $str .= '<li class="col-xs-12">';
    $str .= '<a href="#" id="" class="btn-input" value="1" style="font-weight: normal;line-height: 65px; left:30px;">輸入</a>';
    $str .= '</li>';
    $str .= '</li>';
    $str .= '</ul>';
    $str .= '</div>';
}

echo json_encode(array("str1" => $str, "str2" => $str2));
?>

