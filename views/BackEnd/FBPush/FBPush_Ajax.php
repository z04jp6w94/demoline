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
$fpm_push_type = !empty($_REQUEST["fpm_push_type"]) ? $_REQUEST["fpm_push_type"] : "";
$number = !empty($_REQUEST["number"]) ? $_REQUEST["number"] : "1";
$number_carousel = !empty($_REQUEST["number_carousel"]) ? $_REQUEST["number_carousel"] : "1";
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
//標籤代碼檔
$sql = " SELECT ct_id, ct_name FROM code_tag ";
$sql .= " WHERE c_id = ? ";
$sql .= " AND ct_status = 'Y' ";
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

$str = "";
switch ($fpm_push_type) {
    case "1"://文字
        $str = '<div>';
        $str .= '<label for="fpm_name">推文標題</label>';
        $str .= '<input type="text" id="fpm_name" name="fpm_name" maxlength="50" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入推文標題" >';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        $str .= '<div>';
        $str .= '<h5>推文內容</h5>';
        $str .= '<textarea class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" id="fpm_content" name="fpm_content" placeholder="請輸入推文內容" cols="60" rows="5" maxlength="500" ></textarea>';
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
        break;
    case "2"://圖像
        $str = '<div>';
        $str .= '<label for="fpm_name">推文標題</label>';
        $str .= '<input type="text" id="fpm_name" name="fpm_name" maxlength="50" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入推文標題" required>';
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
        $str .= '<div id="img_1" class="tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="">';
        $str .= '<hr class="full-width" />';
        $str .= '<label for="lrcm_img1">圖片</label>';
        $str .= '<label class="btn btn-danger btn-xs" id="uploadFileChooseButton1"><input type="file" name="lrcm_img[]" id="lrcm_img1" value="" class="upload" style="display:none;" required></input>選擇檔案</label>';
        $str .= '<div id="uploadFilePreviewBlock">';
        $str .= '<label id="uploadFileDelete1"></label>';
        $str .= '<label id="uploadFilePreview1"></label>';
        $str .= '<label id="uploadFileMsg1"></label>';
        $str .= '</div>';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        break;
    case "3"://輪播
        $str = '<div id="carousel_' . $number . '" >';
        $str .= '<div>';
        $str .= '<a id="add-row-btn" onclick="DeleteCarousel(' . $number . ');" style="float:right;margin-bottom:20px;" class="btn btn-danger btn-sm">刪除訊息</a>';
        $str .= '</div>';
        $str .= '<div>';
        $str .= '<label for="fpm_name">推文標題</label>';
        $str .= '<input type="text" id="fpm_name" name="fpm_name[]" onchange="ChangeValue(this);" maxlength="50" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入推文標題" required >';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        $str .= '<div>';
        $str .= '<h5>推文內容</h5>';
        $str .= '<textarea class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" id="fpm_content" name="fpm_content[]" onchange="ChangeValue(this);" placeholder="限制字數80" cols="60" rows="5" maxlength="80" required></textarea>';
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
        $str .= '<div id="url' . $number . '">';
        $str .= '<label for="fpm_url' . $number . '">推文連結</label>';
        $str .= '<input type="text" id="fpm_url' . $number . '" name="fpm_url[]" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入推文連結" required>';
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
        $str .= '<label for="fpmd_sort' . $number . '">排序</label>';
        $str .= '<input type="text" id="fpmd_sort' . $number . '" name="fpmd_sort[]" value="' . $number_carousel . '" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" onKeyUp="return this.value = this.value.replace(/\D/g, \'\')" placeholder="請輸入1-999數字,1為最前面" >';
        $str .= '</div>';
        $str .= '<hr class="full-width" style="color: ' . $color . ';background-color: ' . $color . ';border-color: ' . $color . ';height: 1px;" />';
        $str .= '</div>';
        break;
    case "4"://文字&&按鈕
        $str = '<div>';
        $str .= '<label for="fpm_name">推文標題</label>';
        $str .= '<input type="text" id="fpm_name" name="fpm_name" maxlength="50" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入推文標題" >';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        $str .= '<div>';
        $str .= '<h5>推文內容</h5>';
        $str .= '<textarea class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" id="fpm_content" name="fpm_content" placeholder="請輸入推文內容" cols="60" rows="5" maxlength="500" ></textarea>';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        $str .= '<div id="url1">';
        $str .= '<label for="fpm_url1">推文連結</label>';
        $str .= '<input type="text" id="fpm_url1" name="fpm_url" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入推文連結" required>';
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
        break;
    case "5"://清單型
        $str = '<div id="carousel_' . $number . '" >';
        $str .= '<div>';
        $str .= '<a id="add-row-btn" onclick="DeleteCarousel(' . $number . ');" style="float:right;margin-bottom:20px;" class="btn btn-danger btn-sm">刪除訊息</a>';
        $str .= '</div>';
        $str .= '<div>';
        $str .= '<label for="fpm_name">推文標題</label>';
        $str .= '<input type="text" id="fpm_name" name="fpm_name[]" onchange="ChangeValue(this);" maxlength="50" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入推文標題" required >';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        $str .= '<div>';
        $str .= '<h5>推文內容</h5>';
        $str .= '<textarea class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" id="fpm_content" name="fpm_content[]" onchange="ChangeValue(this);" placeholder="限制字數80" cols="60" rows="5" maxlength="80" required></textarea>';
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
        $str .= '<div id="url' . $number . '">';
        $str .= '<label for="fpm_url' . $number . '">推文連結</label>';
        $str .= '<input type="text" id="fpm_url' . $number . '" name="fpm_url[]" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入推文連結" required>';
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
        $str .= '<label for="fpmd_sort' . $number . '">排序</label>';
        $str .= '<input type="text" id="fpmd_sort' . $number . '" name="fpmd_sort[]" value="' . $number_carousel . '" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" onKeyUp="return this.value = this.value.replace(/\D/g, \'\')" placeholder="請輸入1-999數字,1為最前面" >';
        $str .= '</div>';
        $str .= '<hr class="full-width" style="color: ' . $color . ';background-color: ' . $color . ';border-color: ' . $color . ';height: 1px;" />';
        $str .= '</div>';
        break;
    case "6"://媒體
        $str = '<div>';
        $str .= '<label for="fpm_name">推文標題</label>';
        $str .= '<input type="text" id="fpm_name" name="fpm_name" maxlength="50" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入推文標題" >';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        $str .= '<div id="url1">';
        $str .= '<label for="fpm_url1">推文連結<span style="color:red;">(限定已上傳至粉絲團的影片連結)</span></label>';
        $str .= '<input type="text" id="fpm_url1" name="fpm_url" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入推文連結" required>';
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
        break;
    default :
        break;
}

echo trim($str);
?>

