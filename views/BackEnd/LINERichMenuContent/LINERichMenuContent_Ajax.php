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
$lrcm_type = !empty($_REQUEST["lrcm_type"]) ? $_REQUEST["lrcm_type"] : "";
$number = !empty($_REQUEST["number"]) ? $_REQUEST["number"] : "1";
$number_carousel = !empty($_REQUEST["number_carousel"]) ? $_REQUEST["number_carousel"] : "1";
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
/* 關鍵字 */
$sql = " SELECT lrcm_id, lrcm_keyword FROm line_richmenu_content_m ";
$sql .= " WHERE c_id = ? AND deletestatus = 'N' ";
$lrcm_ary = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);
/* 商品 上架時間 */
$sql = " SELECT cm_id, cm_name FROM commodity_m ";
$sql .= " WHERE c_id = ? ";
$sql .= " AND deletestatus = 'N' ";
$cm_ary = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);
//標籤代碼檔
$sql = " SELECT ct_id, ct_name FROM code_tag ";
$sql .= " WHERE c_id = ? ";
$ct_ary = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);
//色碼用 
$color = "#FF0000";
if ($number % 2 == "0") {
    $color = "#FFA600";
} else if ($number % 3 == "0") {
    $color = "#FFFF00";
} else if ($number % 5 == "0") {
    $color = "#00FFFF";
} else if ($number % 7 == "0") {
    $color = "#9300FF";
}

/* SEC */
if ($lrcm_type == '') {
    exit;
}
$str = "";
if ($lrcm_type == 1) {
    $str = '<div>';
    $str .= '<label for="lrcm_title">主旨</label>';
    $str .= '<input type="text" id="lrcm_title" name="lrcm_title" maxlength="35" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入主旨" >';
    $str .= '<hr class="full-width" />';
    $str .= '</div>';
    $str .= '<div>';
    $str .= '<h5>內容</h5>';
    $str .= '<textarea class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" id="lrcm_content" name="lrcm_content" placeholder="請輸入內容" cols="60" rows="5" maxlength="1000" ></textarea>';
    $str .= '</div>';
} else if ($lrcm_type == 2) {
    $str = '<div id="carousel_' . $number . '" >';
    $str .= '<div>';
    $str .= '<a id="add-row-btn" onclick="DeleteCarousel(' . $number . ');" style="float:right;margin-bottom:20px;" class="btn btn-danger btn-sm">刪除訊息</a>';
    $str .= '</div>';
    $str .= '<div id="title' . $number . '">';
    $str .= '<label for="lrcm_title">主旨</label>';
    $str .= '<input type="text" id="lrcm_title' . $number . '" name="lrcm_title[]" onchange="ChangeValue(this);" maxlength="35" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入主旨" required >';
    $str .= '<hr class="full-width" />';
    $str .= '</div>';
    $str .= '<div id="content' . $number . '">';
    $str .= '<h5>內容</h5>';
    $str .= '<textarea id="lrcm_content' . $number . '" name="lrcm_content[]" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" onchange="ChangeValue(this);" placeholder="限制字數60" cols="60" rows="5" maxlength="60" required></textarea>';
    $str .= '<hr class="full-width" />';
    $str .= '</div>';
    $str .= '<div>';
    $str .= '<h5>標籤</h5>';
    $str .= '<div class="checkbox">';
    foreach ($ct_ary as $rsAry) {
        $str .= '<label><input type="checkbox" id="ct_id' . $number . '" name="ct_id' . $number . '[]" class="checkbox-info" value="' . $rsAry[0] . '">';
        $str .= '<span class="text">' . $rsAry[1] . '</span>';
        $str .= '</label>';
    }
    $str .= '</div>';
    $str .= '<hr class="full-width" />';
    $str .= '</div>';
    $str .= '<div>';
    $str .= '<h5>應用類型</h5>';
    $str .= '<div class="hr-space space-x1"></div>';
    $str .= '<div class="form-group">';
    $str .= '<label>';
    $str .= '<input id ="lrcm_action_type' . $number . '" name="lrcm_action_type' . $number . '[]" class="action_type" value="1" type="radio" checked="checked">';
    $str .= '<span class="text">關鍵字 </span>';
    $str .= '</label>';
    $str .= '<label>';
    $str .= '<input id ="lrcm_action_type' . $number . '" name="lrcm_action_type' . $number . '[]" class="action_type" value="2" type="radio">';
    $str .= '<span class="text">超連結</span>';
    $str .= '</label>';
    $str .= '<label>';
    $str .= '<input id ="lrcm_action_type' . $number . '" name="lrcm_action_type' . $number . '[]" class="action_type" value="4" type="radio">';
    $str .= '<span class="text">商品</span>';
    $str .= '</label>';
    $str .= '</div>';
    $str .= '</div>';
    $str .= '<div id="keyword' . $number . '">';
    $str .= '<h5>關鍵字</h5>';
    $str .= '<select id="key_id' . $number . '" name="key_id[]" onchange="ChangeValue(this);" class="e1 tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" style="width: 100%;" required>';
    $str .= '<option value="">請選擇</option>';
    foreach ($lrcm_ary as $rsAry) {
        $str .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str .= '</select>';
    $str .= '<hr class="full-width" />';
    $str .= '</div>';
    $str .= '<div id="url' . $number . '" style="display:none">';
    $str .= '<label for="lrcm_url' . $number . '">超連結</label>';
    $str .= '<input type="text" id="lrcm_url' . $number . '" name="lrcm_url[]" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入超連結" required>';
    $str .= '<hr class="full-width" />';
    $str .= '</div>';
    $str .= '<div id="commodity' . $number . '" style="display:none">';
    $str .= '<h5>商品</h5>';
    $str .= '<select id="cm_id' . $number . '" name="cm_id[]" onchange="ChangeValue(this);" class="e1 tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" style="width: 100%;" required>';
    $str .= '<option value="">請選擇</option>';
    foreach ($cm_ary as $rsAry) {
        $str .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str .= '</select>';
    $str .= '<hr class="full-width" />';
    $str .= '</div>';
    $str .= '<div id="img_' . $number . '" class="tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="">';
    $str .= '<label for="lrcm_img' . $number . '">圖片</label>';
    $str .= '<label class="btn btn-danger btn-xs" id="uploadFileChooseButton' . $number . '"><input type="file" name="lrcm_img[]" id="lrcm_img' . $number . '" value="" class="upload need" style="display:none;" required></input>選擇檔案</label>';
    $str .= '<div id="uploadFilePreviewBlock">';
    $str .= '<label id="uploadFileDelete' . $number . '"></label>';
    $str .= '<label id="uploadFilePreview' . $number . '"></label>';
    $str .= '<label id="uploadFileMsg' . $number . '"></label>';
    $str .= '</div>';
    $str .= '<hr class="full-width" />';
    $str .= '</div>';
    $str .= '<div>';
    $str .= '<label for="lrct_sort' . $number . '">排序</label>';
    $str .= '<input type="text" id="lrct_sort' . $number . '" name="lrct_sort[]" value="' . $number_carousel . '" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" onKeyUp="return this.value = this.value.replace(/\D/g, \'\')" placeholder="請輸入1-999數字,1為最前面" >';
    $str .= '</div>';
    $str .= '<hr class="full-width" style="color: ' . $color . ';background-color: ' . $color . ';border-color: ' . $color . ';height: 1px;" />';
    $str .= '</div>';
} else if ($lrcm_type == 3) {
    $str = '<div id="title1">';
    $str .= '<label for="lrcm_title">主旨</label>';
    $str .= '<input type="text" id="lrcm_title" name="lrcm_title" maxlength="19" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入主旨" required>';
    $str .= '<hr class="full-width" />';
    $str .= '</div>';
    $str .= '<div>';
    $str .= '<h5>標籤</h5>';
    $str .= '<div class="checkbox">';
    foreach ($ct_ary as $rsAry) {
        $str .= '<label><input type="checkbox" id="ct_id" name="ct_id[]" class="checkbox-info" value="' . $rsAry[0] . '">';
        $str .= '<span class="text">' . $rsAry[1] . '</span>';
        $str .= '</label>';
    }
    $str .= '</div>';
    $str .= '<hr class="full-width" />';
    $str .= '</div>';
    $str .= '<div>';
    $str .= '<div>';
    $str .= '<h5>應用類型</h5>';
    $str .= '<div class="hr-space space-x1"></div>';
    $str .= '<div class="form-group">';
    $str .= '<label>';
    $str .= '<input id ="lrcm_action_type1" name="lrcm_action_type" class="action_type" value="1" type="radio" checked="checked">';
    $str .= '<span class="text">關鍵字 </span>';
    $str .= '</label>';
    $str .= '<label>';
    $str .= '<input id ="lrcm_action_type1" name="lrcm_action_type" class="action_type" value="2" type="radio">';
    $str .= '<span class="text">超連結</span>';
    $str .= '</label>';
    $str .= '<label>';
    $str .= '<input id ="lrcm_action_type1" name="lrcm_action_type" class="action_type" value="3" type="radio">';
    $str .= '<span class="text">不要設定</span>';
    $str .= '</label>';
    $str .= '<label>';
    $str .= '<input id ="lrcm_action_type1" name="lrcm_action_type" class="action_type" value="4" type="radio">';
    $str .= '<span class="text">商品</span>';
    $str .= '</label>';
    $str .= '</div>';
    $str .= '</div>';
    $str .= '<div id="keyword1">';
    $str .= '<h5>關鍵字</h5>';
    $str .= '<select id="key_id1" name="key_id" onchange="ChangeValue(this);" class="e1 tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" style="width: 100%;" required>';
    $str .= '<option value="">請選擇</option>';
    foreach ($lrcm_ary as $rsAry) {
        $str .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str .= '</select>';
    $str .= '</div>';
    $str .= '<div id="url1" style="display:none">';
    $str .= '<label for="lrcm_url1">超連結</label>';
    $str .= '<input type="text" id="lrcm_url1" name="lrcm_url" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入超連結" required>';
    $str .= '</div>';
    $str .= '<div id="commodity1" style="display:none">';
    $str .= '<h5>商品</h5>';
    $str .= '<select id="cm_id1" name="cm_id" onchange="ChangeValue(this);" class="e1 tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" style="width: 100%;" required>';
    $str .= '<option value="">請選擇</option>';
    foreach ($cm_ary as $rsAry) {
        $str .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
    }
    $str .= '</select>';
    $str .= '</div>';
    $str .= '<div id="img_1" class="tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="">';
    $str .= '<hr class="full-width" />';
    $str .= '<label for="lrcm_img1">圖片</label>';
    $str .= '<label class="btn btn-danger btn-xs" id="uploadFileChooseButton1"><input type="file" name="lrcm_img[]" id="lrcm_img1" value="" class="upload" style="display:none;" required></input>選擇檔案</label>';
    $str .= '<div id="uploadFilePreviewBlock">';
    $str .= '<label id="uploadFileDelete1"></label>';
    $str .= '<label id="uploadFilePreview1"></label>';
    $str .= '<label id="uploadFileMsg1"></label>';
    $str .= '</div>';
    $str .= '</div>';
}

echo trim($str);
?>

