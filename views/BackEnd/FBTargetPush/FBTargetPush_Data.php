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
$dataKey = !empty($_REQUEST["dataKey"]) ? $_REQUEST["dataKey"] : "";
if ($dataKey == '') {
    header("Location:http://" . $_SERVER['HTTP_HOST'] . "/views/BackEnd/Login/Member_Login.php");
    exit;
}
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
/* Data */
$sql = "SELECT ct_id, fpm_name, fpm_content, fpm_url, fpm_img, ";
$sql .= " fpm_cdn_root, fpm_send_status, fpm_push_type, fpm_target_type, fpm_reserve";
$sql .= " FROM fb_push_messenger";
$sql .= " WHERE fpm_id = ?";
$sql .= " AND c_id = ? ";
$FBPMAry = $mysqli->readArrayPreSTMT($sql, "ss", array($dataKey, $c_id), 10);
//SET VALUE
$ct_id = $FBPMAry[0][0];
$fpm_name = $FBPMAry[0][1];
$fpm_content = $FBPMAry[0][2];
$fpm_url = $FBPMAry[0][3];
$fpm_img = $FBPMAry[0][4];
$fpm_cdn_root = $FBPMAry[0][5];
$fpm_send_status = $FBPMAry[0][6];
$fpm_push_type = $FBPMAry[0][7];
$fpm_target_type = $FBPMAry[0][8];
$fpm_reserve = $FBPMAry[0][9];
$ct_str = explode(",", $ct_id);
/* Detail */
$sql = " SELECT fpmd_id, ct_id, fpmd_name, fpmd_content, fpmd_url, ";
$sql .= " fpmd_img, fpmd_cdn_root, fpmd_push_type, fpmd_sort ";
$sql .= " FROM fb_push_messenger_d ";
$sql .= " WHERE fpm_id = ?";
$sql .= " AND deletestatus = 'N' ";
$sql .= " ORDER BY fpmd_sort ";
$FBPMDAry = $mysqli->readArrayPreSTMT($sql, "s", array($dataKey), 9);

//標籤代碼檔
$sql = " SELECT ct_id, ct_name FROM code_tag ";
$sql .= " WHERE c_id = ? ";
$sql .= " AND ct_status = 'Y' ";
$ct_ary = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);

$str = "";
switch ($fpm_push_type) {
    case "1"://文字
        $str = '<div>';
        $str .= '<label for="fpm_name">推文標題</label>';
        $str .= '<input type="text" id="fpm_name" name="fpm_name" maxlength="50" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入推文標題" value="' . $fpm_name . '" readonly>';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        $str .= '<div>';
        $str .= '<h5>推文內容</h5>';
        $str .= '<textarea class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" id="fpm_content" name="fpm_content" placeholder="請輸入推文內容" cols="60" rows="5" maxlength="500" readonly>' . $fpm_content . '</textarea>';
        $str .= '</div>';
        $str .= '<div>';
        $str .= '<h5>標籤</h5>';
        $str .= '<fieldset disabled>';
        $str .= '<div class="checkbox">';
        foreach ($ct_ary as $rsAry) {
            $checked = "";
            if (in_array($rsAry[0], $ct_str)) {
                $checked = 'checked';
            }
            $str .= '<label><input type="checkbox" id="ct_id" name="ct_id[]" class="checkbox-info" value="' . $rsAry[0] . '"' . $checked . ' disabled>';
            $str .= '<span class="text">' . $rsAry[1] . '</span>';
            $str .= '</label>';
        }
        $str .= '</div>';
        $str .= '</fieldset>';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        break;
    case "2"://圖像
        $str = '<div>';
        $str .= '<label for="fpm_name">推文標題</label>';
        $str .= '<input type="text" id="fpm_name" name="fpm_name" maxlength="50" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入推文標題" value=' . $fpm_name . ' readonly>';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        $str .= '<div>';
        $str .= '<h5>標籤</h5>';
        $str .= '<fieldset disabled>';
        $str .= '<div class="checkbox">';
        foreach ($ct_ary as $rsAry) {
            $checked = "";
            if (in_array($rsAry[0], $ct_str)) {
                $checked = 'checked';
            }
            $str .= '<label><input type="checkbox" id="ct_id" name="ct_id[]" class="checkbox-info" value="' . $rsAry[0] . '"' . $checked . ' disabled>';
            $str .= '<span class="text">' . $rsAry[1] . '</span>';
            $str .= '</label>';
        }
        $str .= '</div>';
        $str .= '</fieldset>';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        $str .= '<div id="img_1" class="tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="">';
        $str .= '<hr class="full-width" />';
        $str .= '<label for="lrcm_img1">圖片</label>';
        $str .= '<label class="btn btn-danger btn-xs" id="uploadFileChooseButton1"><input type="file" name="lrcm_img[]" id="lrcm_img1" value="' . $fpm_cdn_root . '" class="upload" style="display:none;" disabled></input>選擇檔案</label>';
        $str .= '<div id="uploadFilePreviewBlock">';
        $str .= '<label id="uploadFileDelete1"></label>';
        $str .= '<label id="uploadFilePreview1"><img src="' . CDN_ROOT_PATH . $fpm_cdn_root . '" style="width:300px;"></label>';
        $str .= '<label id="uploadFileMsg1"></label>';
        $str .= '</div>';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        break;
    case "3"://輪播
        for ($i = 0; $i < count($FBPMDAry); $i++) {
            $number = (int) $i + 1;
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
            /* DATA */
            $fpmd_id = $FBPMDAry[$i][0];
            $ct_id = $FBPMDAry[$i][1];
            $fpmd_name = $FBPMDAry[$i][2];
            $fpmd_content = $FBPMDAry[$i][3];
            $fpmd_url = $FBPMDAry[$i][4];
            $fpmd_img = $FBPMDAry[$i][5];
            $fpmd_cdn_root = $FBPMDAry[$i][6];
            $fpmd_push_type = $FBPMDAry[$i][7];
            $fpmd_sort = $FBPMDAry[$i][8];
            $ct_str = explode(",", $ct_id);
            $str .= '<div id="carousel_' . $number . '" >';
//            $str .= '<div>';
//            $str .= '<a id="add-row-btn" onclick="DeleteCarousel(' . $number . ');" style="float:right;margin-bottom:20px;" class="btn btn-danger btn-sm">刪除訊息</a>';
//            $str .= '</div>';
            $str .= '<div>';
            $str .= '<label for="fpm_name">推文標題</label>';
            $str .= '<input type="text" id="fpm_name" name="fpm_name[]" onchange="ChangeValue(this);" maxlength="50" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入推文標題" value="' . $fpmd_name . '" readonly >';
            $str .= '<hr class="full-width" />';
            $str .= '</div>';
            $str .= '<div>';
            $str .= '<h5>推文內容</h5>';
            $str .= '<textarea class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" id="fpm_content" name="fpm_content[]" onchange="ChangeValue(this);" placeholder="限制字數80" cols="60" rows="5" maxlength="80" readonly>' . $fpmd_content . '</textarea>';
            $str .= '<hr class="full-width" />';
            $str .= '</div>';
            $str .= '<div>';
            $str .= '<h5>標籤</h5>';
            $str .= '<fieldset disabled>';
            $str .= '<div class="checkbox">';
            foreach ($ct_ary as $rsAry) {
                $checked = "";
                if (in_array($rsAry[0], $ct_str)) {
                    $checked = 'checked';
                }
                $str .= '<label><input type="checkbox" id="ct_id' . $number . '" name="ct_id' . $number . '[]" class="checkbox-info" value="' . $rsAry[0] . '" ' . $checked . ' disabled>';
                $str .= '<span class="text">' . $rsAry[1] . '</span>';
                $str .= '</label>';
            }
            $str .= '</div>';
            $str .= '</fieldset>';
            $str .= '<hr class="full-width" />';
            $str .= '</div>';
            $str .= '<div>';
            $str .= '<div id="url' . $number . '">';
            $str .= '<label for="fpm_url' . $number . '">推文連結</label>';
            $str .= '<input type="text" id="fpm_url' . $number . '" name="fpm_url[]" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入推文連結" value="' . $fpmd_url . '" readonly>';
            $str .= '<hr class="full-width" />';
            $str .= '</div>';
            $str .= '<div id="img_' . $number . '" class="tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="">';
            $str .= '<label for="lrcm_img' . $number . '">圖片</label>';
            $str .= '<label class="btn btn-danger btn-xs" id="uploadFileChooseButton' . $number . '"><input type="file" name="lrcm_img[]" id="lrcm_img' . $number . '" value="' . $fpmd_cdn_root . '" class="upload need" style="display:none;" disabled></input>選擇檔案</label>';
            $str .= '<div id="uploadFilePreviewBlock">';
            $str .= '<label id="uploadFileDelete' . $number . '"></label>';
            $str .= '<label id="uploadFilePreview' . $number . '"><img src="' . CDN_ROOT_PATH . $fpmd_cdn_root . '" style="width:300px;"></label>';
            $str .= '<label id="uploadFileMsg' . $number . '"></label>';
            $str .= '</div>';
            $str .= '<hr class="full-width" />';
            $str .= '</div>';
            $str .= '<div>';
            $str .= '<label for="fpmd_sort' . $number . '">排序</label>';
            $str .= '<input type="text" id="fpmd_sort' . $number . '" name="fpmd_sort[]" value="' . $fpmd_sort . '" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" onKeyUp="return this.value = this.value.replace(/\D/g, \'\')" placeholder="請輸入1-999數字,1為最前面" readonly>';
            $str .= '</div>';
            $str .= '<hr class="full-width" style="color: ' . $color . ';background-color: ' . $color . ';border-color: ' . $color . ';height: 1px;" />';
            $str .= '</div>';
        }
        break;
    case "4"://文字&&按鈕
        $str = '<div>';
        $str .= '<label for="fpm_name">推文標題</label>';
        $str .= '<input type="text" id="fpm_name" name="fpm_name" maxlength="50" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入推文標題" value="' . $fpm_name . '" readonly>';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        $str .= '<div>';
        $str .= '<h5>推文內容</h5>';
        $str .= '<textarea class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" id="fpm_content" name="fpm_content" placeholder="請輸入推文內容" cols="60" rows="5" maxlength="500" readonly>' . $fpm_content . '</textarea>';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        $str .= '<div id="url1">';
        $str .= '<label for="fpm_url1">推文連結</label>';
        $str .= '<input type="text" id="fpm_url1" name="fpm_url" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入推文連結" value="' . $fpm_url . '" readonly>';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        $str .= '<div>';
        $str .= '<h5>標籤</h5>';
        $str .= '<fieldset disabled>';
        $str .= '<div class="checkbox">';
        foreach ($ct_ary as $rsAry) {
            $checked = "";
            if (in_array($rsAry[0], $ct_str)) {
                $checked = 'checked';
            }
            $str .= '<label><input type="checkbox" id="ct_id" name="ct_id[]" class="checkbox-info" value="' . $rsAry[0] . '" ' . $checked . ' disabled>';
            $str .= '<span class="text">' . $rsAry[1] . '</span>';
            $str .= '</label>';
        }
        $str .= '</div>';
        $str .= '</fieldset>';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        break;
    case "5"://清單型
        for ($i = 0; $i < count($FBPMDAry); $i++) {
            $number = (int) $i + 1;
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
            /* DATA */
            $fpmd_id = $FBPMDAry[$i][0];
            $ct_id = $FBPMDAry[$i][1];
            $fpmd_name = $FBPMDAry[$i][2];
            $fpmd_content = $FBPMDAry[$i][3];
            $fpmd_url = $FBPMDAry[$i][4];
            $fpmd_img = $FBPMDAry[$i][5];
            $fpmd_cdn_root = $FBPMDAry[$i][6];
            $fpmd_push_type = $FBPMDAry[$i][7];
            $fpmd_sort = $FBPMDAry[$i][8];
            $ct_str = explode(",", $ct_id);
            $str .= '<div id="carousel_' . $number . '" >';
//            $str .= '<div>';
//            $str .= '<a id="add-row-btn" onclick="DeleteCarousel(' . $number . ');" style="float:right;margin-bottom:20px;" class="btn btn-danger btn-sm">刪除訊息</a>';
//            $str .= '</div>';
            $str .= '<div>';
            $str .= '<label for="fpm_name">推文標題</label>';
            $str .= '<input type="text" id="fpm_name" name="fpm_name[]" onchange="ChangeValue(this);" maxlength="50" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入推文標題" value="' . $fpmd_name . '" readonly >';
            $str .= '<hr class="full-width" />';
            $str .= '</div>';
            $str .= '<div>';
            $str .= '<h5>推文內容</h5>';
            $str .= '<textarea class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" id="fpm_content" name="fpm_content[]" onchange="ChangeValue(this);" placeholder="限制字數80" cols="60" rows="5" maxlength="80" readonly>' . $fpmd_content . '</textarea>';
            $str .= '<hr class="full-width" />';
            $str .= '</div>';
            $str .= '<div>';
            $str .= '<h5>標籤</h5>';
            $str .= '<fieldset disabled>';
            $str .= '<div class="checkbox">';
            foreach ($ct_ary as $rsAry) {
                $checked = "";
                if (in_array($rsAry[0], $ct_str)) {
                    $checked = 'checked';
                }
                $str .= '<label><input type="checkbox" id="ct_id' . $number . '" name="ct_id' . $number . '[]" class="checkbox-info" value="' . $rsAry[0] . '" ' . $checked . ' disabled>';
                $str .= '<span class="text">' . $rsAry[1] . '</span>';
                $str .= '</label>';
            }
            $str .= '</div>';
            $str .= '</fieldset>';
            $str .= '<hr class="full-width" />';
            $str .= '</div>';
            $str .= '<div>';
            $str .= '<div id="url' . $number . '">';
            $str .= '<label for="fpm_url' . $number . '">推文連結</label>';
            $str .= '<input type="text" id="fpm_url' . $number . '" name="fpm_url[]" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入推文連結" value="' . $fpmd_url . '" readonly>';
            $str .= '<hr class="full-width" />';
            $str .= '</div>';
            $str .= '<div id="img_' . $number . '" class="tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="">';
            $str .= '<label for="lrcm_img' . $number . '">圖片</label>';
            $str .= '<label class="btn btn-danger btn-xs" id="uploadFileChooseButton' . $number . '"><input type="file" name="lrcm_img[]" id="lrcm_img' . $number . '" value="' . $fpmd_cdn_root . '" class="upload need" style="display:none;" disabled></input>選擇檔案</label>';
            $str .= '<div id="uploadFilePreviewBlock">';
            $str .= '<label id="uploadFileDelete' . $number . '"></label>';
            $str .= '<label id="uploadFilePreview' . $number . '"><img src="' . CDN_ROOT_PATH . $fpmd_cdn_root . '" style="width:300px;"></label>';
            $str .= '<label id="uploadFileMsg' . $number . '"></label>';
            $str .= '</div>';
            $str .= '<hr class="full-width" />';
            $str .= '</div>';
            $str .= '<div>';
            $str .= '<label for="fpmd_sort' . $number . '">排序</label>';
            $str .= '<input type="text" id="fpmd_sort' . $number . '" name="fpmd_sort[]" value="' . $fpmd_sort . '" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" onKeyUp="return this.value = this.value.replace(/\D/g, \'\')" placeholder="請輸入1-999數字,1為最前面" readonly>';
            $str .= '</div>';
            $str .= '<hr class="full-width" style="color: ' . $color . ';background-color: ' . $color . ';border-color: ' . $color . ';height: 1px;" />';
            $str .= '</div>';
        }
        break;
    case "6"://媒體
        $str = '<div>';
        $str .= '<label for="fpm_name">推文標題</label>';
        $str .= '<input type="text" id="fpm_name" name="fpm_name" maxlength="50" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入推文標題" value="' . $fpm_name . '" readonly>';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        $str .= '<div id="url1">';
        $str .= '<label for="fpm_url1">推文連結<span style="color:red;">(限定已上傳至粉絲團的影片連結)</span></label>';
        $str .= '<input type="text" id="fpm_url1" name="fpm_url" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入推文連結" value="' . $fpm_url . '" readonly>';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        $str .= '<div>';
        $str .= '<h5>標籤</h5>';
        $str .= '<fieldset disabled>';
        $str .= '<div class="checkbox">';
        foreach ($ct_ary as $rsAry) {
            $checked = "";
            if (in_array($rsAry[0], $ct_str)) {
                $checked = 'checked';
            }
            $str .= '<label><input type="checkbox" id="ct_id" name="ct_id[]" class="checkbox-info" value="' . $rsAry[0] . '" ' . $checked . ' disabled>';
            $str .= '<span class="text">' . $rsAry[1] . '</span>';
            $str .= '</label>';
        }
        $str .= '</div>';
        $str .= '</fieldset>';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        break;
    default :
        break;
}

echo trim($str);
?>

