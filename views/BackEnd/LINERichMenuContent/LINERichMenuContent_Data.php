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
$dataKey = !empty($_REQUEST["dataKey"]) ? $_REQUEST["dataKey"] : "";
if ($dataKey == '') {
    header("Location:http://" . $_SERVER['HTTP_HOST'] . "/views/BackEnd/Login/Member_Login.php");
    exit;
}
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
/* Data */
$sql = "SELECT ct_id, lrcm_cdn_root, lrcm_title, lrcm_content, lrcm_action_type, ";
$sql .= " lrcm_url, ";
$sql .= " CASE m.lrcm_action_type ";
$sql .= " WHEN '1' THEN (SELECT IFNULL(m.app_id,' ') FROM line_richmenu_content_m d WHERE m.app_id = d.lrcm_id)  ";
$sql .= " WHEN '4' THEN (SELECT IFNULL(m.app_id,' ') FROM commodity_m d WHERE m.app_id = d.cm_id)  ";
$sql .= " ELSE '' ";
$sql .= " END AS 'app_id' ";
$sql .= " FROM line_richmenu_content_m m ";
$sql .= " WHERE lrcm_id = ?";
$sql .= " AND c_id = ? ";
$LRCMAry = $mysqli->readArrayPreSTMT($sql, "ss", array($dataKey, $c_id), 7);
//SET VALUE
$ct_id = $LRCMAry[0][0];
$lrcm_cdn_root = $LRCMAry[0][1];
$lrcm_title = $LRCMAry[0][2];
$lrcm_content = $LRCMAry[0][3];
$lrcm_action_type = $LRCMAry[0][4];
$lrcm_url = $LRCMAry[0][5];
$app_id = $LRCMAry[0][6];
/* Detail */
$sql = " SELECT lrct_id, ct_id, lrct_cdn_root, lrct_title, lrct_content, ";
$sql .= " lrct_action_type, lrct_url, ";
$sql .= " CASE t.lrct_action_type ";
$sql .= " WHEN '1' THEN (SELECT IFNULL(t.app_id,' ') FROM line_richmenu_content_m d WHERE t.app_id = d.lrcm_id)  ";
$sql .= " WHEN '4' THEN (SELECT IFNULL(t.app_id,' ') FROM commodity_m d WHERE t.app_id = d.cm_id)  ";
$sql .= " ELSE '' ";
$sql .= " END AS 'app_id', ";
$sql .= " lrct_sort ";
$sql .= " FROM line_richmenu_content_t t ";
$sql .= " WHERE lrcm_id = ?";
$sql .= " AND c_id = ? ";
$sql .= " AND deletestatus = 'N' ";
$sql .= " ORDER BY lrct_sort ";
$LRCTAry = $mysqli->readArrayPreSTMT($sql, "ss", array($dataKey, $c_id), 9);
//標籤代碼檔
$sql = " select ct_id, ct_name from code_tag ";
$sql .= " where c_id = ? ";
$ct_ary = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);
/* 關鍵字 */
$sql = " SELECT lrcm_id, lrcm_keyword FROm line_richmenu_content_m ";
$sql .= " WHERE c_id = ? AND deletestatus = 'N' ";
$lrcm_ary = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);
/* 商品 上架時間 */
$sql = " SELECT cm_id, cm_name FROM commodity_m ";
$sql .= " WHERE c_id = ? ";
$sql .= " AND deletestatus = 'N' ";
$cm_ary = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);
if ($lrcm_type == '') {
    echo "系統出錯,請重新整理網頁!";
    exit;
}
$str = "";
if ($lrcm_type == 1) {
    $str = '<div>';
    $str .= '<label for="lrcm_title">主旨</label>';
    $str .= '<input type="text" id="lrcm_title" name="lrcm_title" maxlength="35" value="' . $lrcm_title . '" class="form-control" >';
    $str .= '<hr class="full-width" />';
    $str .= '</div>';
    $str .= '<div>';
    $str .= '<h5>內容</h5>';
    $str .= '<textarea class="form-control" id="lrcm_content" name="lrcm_content" cols="60" rows="5" maxlength="1000" >' . $lrcm_content . '</textarea>';
    $str .= '</div>';
} else if ($lrcm_type == 2) {
    for ($i = 0; $i < count($LRCTAry); $i++) {
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
        $lrct_id = $LRCTAry[$i][0];
        $ct_id = $LRCTAry[$i][1];
        $lrct_cdn_root = $LRCTAry[$i][2];
        $lrct_title = $LRCTAry[$i][3];
        $lrct_content = $LRCTAry[$i][4];
        $lrct_action_type = $LRCTAry[$i][5];
        $lrct_url = $LRCTAry[$i][6];
        $app_id = $LRCTAry[$i][7];
        $lrct_sort = $LRCTAry[$i][8];
        $ct_str = explode(",", $ct_id);
        /* init */
        $str .= '<div id="carousel_' . $number . '" >';
        $str .= '<div>';
        $str .= '<a id="add-row-btn" onclick="DeleteCarousel(' . $number . ', ' . $lrct_id . ');" style="float:right;margin-bottom:20px;" class="btn btn-danger btn-sm">刪除訊息</a>';
        $str .= '</div>';
        if ($lrct_action_type == 4) {
            $style = 'style="display:none"';
        } else {
            $style = '';
        }
        $str .= '<div id="title' . $number . '" ' . $style . '>';
        $str .= '<label for="lrcm_title">主旨</label>';
        $str .= '<input type="text" id="lrcm_title' . $number . '" name="lrcm_title[]" onchange="ChangeValue(this);" value="' . $lrct_title . '" maxlength="35" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" >';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        $str .= '<div id="content' . $number . '" ' . $style . '>';
        $str .= '<h5>內容</h5>';
        $str .= '<textarea id="lrcm_content' . $number . '" name="lrcm_content[]" onchange="ChangeValue(this);" cols="60" rows="5" maxlength="60" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" >' . $lrct_content . '</textarea>';
        $str .= '<hr class="full-width" />';
        $str .= '</div>';
        $str .= '<h5>標籤</h5>';
        $str .= '<div class="checkbox">';
        foreach ($ct_ary as $rsAry) {
            $checked = "";
            if (in_array($rsAry[0], $ct_str)) {
                $checked = 'checked';
            }
            $str .= '<label><input type="checkbox" id="ct_id" name="ct_id' . $number . '[]" class="checkbox-info" value="' . $rsAry[0] . '" ' . $checked . ' >';
            $str .= '<span class="text">' . $rsAry[1] . '</span>';
            $str .= '</label>';
        }
        $str .= '</div>';
        $str .= '<hr class="full-width" />';
        $checked_1 = "";
        $checked_2 = "";
        $checked_4 = "";
        if ($lrct_action_type == "1") {
            $checked_1 = 'checked';
        } else if ($lrct_action_type == "2") {
            $checked_2 = 'checked';
        } else if ($lrct_action_type == "4") {
            $checked_4 = 'checked';
        }
        $str .= '<h5><b>應用類型</b></h5>';
        $str .= '<div class="hr-space space-x1"></div>';
        $str .= '<div class="form-group">';
        $str .= '<label>';
        $str .= '<input id ="lrcm_action_type' . $number . '" name="lrcm_action_type' . $number . '[]" class="action_type" value="1" type="radio" ' . $checked_1 . '>';
        $str .= '<span class="text">關鍵字 </span>';
        $str .= '</label>';
        $str .= '<label>';
        $str .= '<input id ="lrcm_action_type' . $number . '" name="lrcm_action_type' . $number . '[]" class="action_type" value="2" type="radio" ' . $checked_2 . '>';
        $str .= '<span class="text">超連結</span>';
        $str .= '</label>';
        $str .= '<label>';
        $str .= '<input id ="lrcm_action_type' . $number . '" name="lrcm_action_type' . $number . '[]" class="action_type" value="4" type="radio" ' . $checked_4 . '>';
        $str .= '<span class="text">商品</span>';
        $str .= '</label>';
        $str .= '</div>';
        if ($lrct_action_type == '1') {
            $str .= '<div id="keyword' . $number . '">';
            $str .= '<h5>關鍵字</h5>';
            $str .= '<select id="key_id' . $number . '" name="key_id[]" onchange="ChangeValue(this);" class="e1 tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" style="width: 100%;" required>';
            $str .= '<option value="">請選擇</option>';
            foreach ($lrcm_ary as $rsAry) {
                if ($rsAry[0] == $app_id) {
                    $str .= '<option value="' . $rsAry[0] . '" selected>' . $rsAry[1] . '</option>';
                } else {
                    $str .= '<option value="' . $rsAry[0] . '" >' . $rsAry[1] . '</option>';
                }
            }
            $str .= '</select>';
            $str .= '<hr class="full-width" />';
            $str .= '</div>';
            $str .= '<div id="url' . $number . '" style="display:none">';
            $str .= '<label for="lrcm_url' . $number . '">超連結</label>';
            $str .= '<input type="text" id="lrcm_url' . $number . '" name="lrcm_url[]" value="' . $lrct_url . '" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入超連結" required>';
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
            $str .= '<label for="lrcm_img' . $number . '"><b>圖片</b></label>';
            $str .= '<label class="btn btn-danger btn-xs" id="uploadFileChooseButton' . $number . '"><input type="file" name="lrcm_img[]" id="lrcm_img' . $number . '" value="' . $lrct_cdn_root . '" class="upload" style="display:none;" required></input>選擇檔案</label>';
            $str .= '<div id="uploadFilePreviewBlock">';
            $str .= '<label id="uploadFileDelete' . $number . '"><span class="btn btn-danger uploadFileDeleteButton" id="uploadFileDeleteButton' . $number . '">刪除</span></label>';
            $str .= '<label id="uploadFilePreview' . $number . '"><img src="' . CDN_ROOT_PATH . $lrct_cdn_root . '" style="width:300px;"></label>';
            $str .= '<label id="uploadFileMsg' . $number . '"></label>';
            $str .= '</div>';
            $str .= '<hr class="full-width" />';
            $str .= '</div>';
            $str .= '<div>';
            $str .= '<label for="lrct_sort' . $number . '">排序</label>';
            $str .= '<input type="text" id="lrct_sort' . $number . '" name="lrct_sort[]" value="' . $lrct_sort . '" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" onKeyUp="return this.value = this.value.replace(/\D/g, \'\')" placeholder="請輸入1-999數字,1為最前面" >';
            $str .= '</div>';
            $str .= '<hr class="full-width" style="color: ' . $color . ';background-color: ' . $color . ';border-color: ' . $color . ';height: 1px;" />';
        } else if ($lrct_action_type == '2') {
            $str .= '<div id="keyword' . $number . '" style="display:none">';
            $str .= '<h5>關鍵字</h5>';
            $str .= '<select id="key_id' . $number . '" name="key_id[]" onchange="ChangeValue(this);" class="e1 tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" style="width: 100%;" required>';
            $str .= '<option value="">請選擇</option>';
            foreach ($lrcm_ary as $rsAry) {
                $str .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
            }
            $str .= '</select>';
            $str .= '<hr class="full-width" />';
            $str .= '</div>';
            $str .= '<div id="url' . $number . '">';
            $str .= '<label for="lrcm_url' . $number . '">超連結</label>';
            $str .= '<input type="text" id="lrcm_url' . $number . '" name="lrcm_url[]" value="' . $lrct_url . '" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入超連結" required>';
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
            $str .= '<label for="lrcm_img' . $number . '"><b>圖片</b></label>';
            $str .= '<label class="btn btn-danger btn-xs" id="uploadFileChooseButton' . $number . '"><input type="file" name="lrcm_img[]" id="lrcm_img' . $number . '" value="' . $lrct_cdn_root . '" class="upload" style="display:none;" required></input>選擇檔案</label>';
            $str .= '<div id="uploadFilePreviewBlock">';
            $str .= '<label id="uploadFileDelete' . $number . '"><span class="btn btn-danger uploadFileDeleteButton" id="uploadFileDeleteButton1">刪除</span></label>';
            $str .= '<label id="uploadFilePreview' . $number . '"><img src="' . CDN_ROOT_PATH . $lrct_cdn_root . '" style="width:300px;"></label>';
            $str .= '<label id="uploadFileMsg' . $number . '"></label>';
            $str .= '</div>';
            $str .= '<hr class="full-width" />';
            $str .= '</div>';
            $str .= '<div>';
            $str .= '<label for="lrct_sort' . $number . '">排序</label>';
            $str .= '<input type="text" id="lrct_sort' . $number . '" name="lrct_sort[]" value="' . $lrct_sort . '" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" onKeyUp="return this.value = this.value.replace(/\D/g, \'\')" placeholder="請輸入1-999數字,1為最前面" >';
            $str .= '</div>';
            $str .= '<hr class="full-width" style="color: ' . $color . ';background-color: ' . $color . ';border-color: ' . $color . ';height: 1px;" />';
        } else if ($lrct_action_type == '4') {
            $str .= '<div id="keyword' . $number . '" style="display:none">';
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
            $str .= '<input type="text" id="lrcm_url' . $number . '" name="lrcm_url[]" value="' . $lrct_url . '" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入超連結" required>';
            $str .= '<hr class="full-width" />';
            $str .= '</div>';
            $str .= '<div id="commodity' . $number . '">';
            $str .= '<h5>商品</h5>';
            $str .= '<select id="cm_id' . $number . '" name="cm_id[]" onchange="ChangeValue(this);" class="e1 tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" style="width: 100%;" required>';
            $str .= '<option value="">請選擇</option>';
            foreach ($cm_ary as $rsAry) {
                if ($rsAry[0] == $app_id) {
                    $str .= '<option value="' . $rsAry[0] . '" selected>' . $rsAry[1] . '</option>';
                } else {
                    $str .= '<option value="' . $rsAry[0] . '" >' . $rsAry[1] . '</option>';
                }
            }
            $str .= '</select>';
            $str .= '<hr class="full-width" />';
            $str .= '</div>';
            $str .= '<div id="img_' . $number . '" class="tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" style="display:none">';
            $str .= '<label for="lrcm_img' . $number . '"><b>圖片</b></label>';
            $str .= '<label class="btn btn-danger btn-xs" id="uploadFileChooseButton' . $number . '"><input type="file" name="lrcm_img[]" id="lrcm_img' . $number . '" value="" class="upload" style="display:none;" required></input>選擇檔案</label>';
            $str .= '<div id="uploadFilePreviewBlock">';
            $str .= '<label id="uploadFileDelete' . $number . '"></label>';
            $str .= '<label id="uploadFilePreview' . $number . '"></label>';
            $str .= '<label id="uploadFileMsg' . $number . '"></label>';
            $str .= '</div>';
            $str .= '<hr class="full-width" />';
            $str .= '</div>';
            $str .= '<div>';
            $str .= '<label for="lrct_sort' . $number . '">排序</label>';
            $str .= '<input type="text" id="lrct_sort' . $number . '" name="lrct_sort[]" value="' . $lrct_sort . '" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" onKeyUp="return this.value = this.value.replace(/\D/g, \'\')" placeholder="請輸入1-999數字,1為最前面" >';
            $str .= '</div>';
            $str .= '<hr class="full-width" style="color: ' . $color . ';background-color: ' . $color . ';border-color: ' . $color . ';height: 1px;" />';
        }
        $str .= '</div>'; //<div id="carousel_' . $number . '" >
    }
} else if ($lrcm_type == 3) {
    $ct_str = explode(",", $ct_id);
    $str = '<div>';
    $str .= '<label for="lrcm_title"><b>主旨</b></label>';
    $str .= '<input type="text" id="lrcm_title" name="lrcm_title" value="' . $lrcm_title . '" maxlength="19" class="form-control" >';
    $str .= '<hr class="full-width" />';
    $str .= '</div>';
    $str .= '<h5><b>標籤</b></h5>';
    $str .= '<div class="checkbox">';
    foreach ($ct_ary as $rsAry) {
        $checked = "";
        if (in_array($rsAry[0], $ct_str)) {
            $checked = 'checked';
        }
        $str .= '<label><input type="checkbox" id="ct_id" name="ct_id[]" class="checkbox-info" value="' . $rsAry[0] . '" ' . $checked . ' >';
        $str .= '<span class="text">' . $rsAry[1] . '</span>';
        $str .= '</label>';
    }
    $str .= '</div>';
    $str .= '<hr class="full-width" />';
    $str .= '<div>';
    $checked_1 = "";
    $checked_2 = "";
    $checked_3 = "";
    $checked_4 = "";
    if ($lrcm_action_type == "1") {
        $checked_1 = 'checked';
    } else if ($lrcm_action_type == "2") {
        $checked_2 = 'checked';
    } else if ($lrcm_action_type == "3") {
        $checked_3 = 'checked';
    } else if ($lrcm_action_type == "4") {
        $checked_4 = 'checked';
    }
    $str .= '<div>';
    $str .= '<h5><b>應用類型</b></h5>';
    $str .= '<div class="hr-space space-x1"></div>';
    $str .= '<div class="form-group">';
    $str .= '<label>';
    $str .= '<input id ="lrcm_action_type1" name="lrcm_action_type" class="action_type" value="1" type="radio" ' . $checked_1 . '>';
    $str .= '<span class="text">關鍵字 </span>';
    $str .= '</label>';
    $str .= '<label>';
    $str .= '<input id ="lrcm_action_type1" name="lrcm_action_type" class="action_type" value="2" type="radio" ' . $checked_2 . '>';
    $str .= '<span class="text">超連結</span>';
    $str .= '</label>';
    $str .= '<label>';
    $str .= '<input id ="lrcm_action_type1" name="lrcm_action_type" class="action_type" value="3" type="radio" ' . $checked_3 . '>';
    $str .= '<span class="text">不要設定</span>';
    $str .= '</label>';
    $str .= '<label>';
    $str .= '<input id ="lrcm_action_type1" name="lrcm_action_type" class="action_type" value="4" type="radio" ' . $checked_4 . '>';
    $str .= '<span class="text">商品</span>';
    $str .= '</label>';
    $str .= '</div>';
    $str .= '</div>';
    if ($lrcm_action_type == '1') {
        $str .= '<div id="keyword1">';
        $str .= '<h5>關鍵字</h5>';
        $str .= '<select id="key_id1" name="key_id" onchange="ChangeValue(this);" class="e1 tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" style="width: 100%;" required>';
        $str .= '<option value="">請選擇</option>';
        foreach ($lrcm_ary as $rsAry) {
            if ($rsAry[0] == $app_id) {
                $str .= '<option value="' . $rsAry[0] . '" selected>' . $rsAry[1] . '</option>';
            } else {
                $str .= '<option value="' . $rsAry[0] . '" >' . $rsAry[1] . '</option>';
            }
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
        $str .= '<label for="lrcm_img1"><b>圖片</b></label>';
        $str .= '<label class="btn btn-danger btn-xs" id="uploadFileChooseButton1"><input type="file" name="lrcm_img[]" id="lrcm_img1" value="' . $lrcm_cdn_root . '" class="upload" style="display:none;" required></input>選擇檔案</label>';
        $str .= '<div id="uploadFilePreviewBlock">';
        $str .= '<label id="uploadFileDelete1"><span class="btn btn-danger uploadFileDeleteButton" id="uploadFileDeleteButton1">刪除</span></label>';
        $str .= '<label id="uploadFilePreview1"><img src="' . CDN_ROOT_PATH . $lrcm_cdn_root . '" style="width:300px;"></label>';
        $str .= '<label id="uploadFileMsg1"></label>';
        $str .= '</div>';
        $str .= '</div>';
    } else if ($lrcm_action_type == '2') {
        $str .= '<div id="keyword1" style="display:none">';
        $str .= '<h5>關鍵字</h5>';
        $str .= '<select id="key_id1" name="key_id" onchange="ChangeValue(this);" class="e1 tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" style="width: 100%;" required>';
        $str .= '<option value="">請選擇</option>';
        foreach ($lrcm_ary as $rsAry) {
            $str .= '<option value="' . $rsAry[0] . '">' . $rsAry[1] . '</option>';
        }
        $str .= '</select>';
        $str .= '</div>';
        $str .= '<div id="url1">';
        $str .= '<label for="lrcm_url1">超連結</label>';
        $str .= '<input type="text" id="lrcm_url1" name="lrcm_url" value="' . $lrcm_url . '" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入超連結" required>';
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
        $str .= '<label for="lrcm_img_1"><b>圖片</b></label>';
        $str .= '<label class="btn btn-danger btn-xs" id="uploadFileChooseButton1"><input type="file" name="lrcm_img[]" id="lrcm_img1" value="' . $lrcm_cdn_root . '" class="upload" style="display:none;" required></input>選擇檔案</label>';
        $str .= '<div id="uploadFilePreviewBlock">';
        $str .= '<label id="uploadFileDelete1"><span class="btn btn-danger uploadFileDeleteButton" id="uploadFileDeleteButton1">刪除</span></label>';
        $str .= '<label id="uploadFilePreview1"><img src="' . CDN_ROOT_PATH . $lrcm_cdn_root . '" style="width:300px;"></label>';
        $str .= '<label id="uploadFileMsg1"></label>';
        $str .= '</div>';
        $str .= '</div>';
    } else if ($lrcm_action_type == '3') {
        $str .= '<div id="keyword1" style="display:none">';
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
        $str .= '<label for="lrcm_img_1"><b>圖片</b></label>';
        $str .= '<label class="btn btn-danger btn-xs" id="uploadFileChooseButton1"><input type="file" name="lrcm_img[]" id="lrcm_img1" value="' . $lrcm_cdn_root . '" class="upload" style="display:none;" required></input>選擇檔案</label>';
        $str .= '<div id="uploadFilePreviewBlock">';
        $str .= '<label id="uploadFileDelete1"><span class="btn btn-danger uploadFileDeleteButton" id="uploadFileDeleteButton1">刪除</span></label>';
        $str .= '<label id="uploadFilePreview1"><img src="' . CDN_ROOT_PATH . $lrcm_cdn_root . '" style="width:300px;"></label>';
        $str .= '<label id="uploadFileMsg1"></label>';
        $str .= '</div>';
        $str .= '</div>';
    } else if ($lrcm_action_type == '4') {
        $str .= '<div id="keyword1" style="display:none">';
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
        $str .= '<div id="commodity1">';
        $str .= '<h5>商品</h5>';
        $str .= '<select id="cm_id1" name="cm_id" onchange="ChangeValue(this);" class="e1 tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" style="width: 100%;" required>';
        $str .= '<option value="">請選擇</option>';
        foreach ($cm_ary as $rsAry) {
            if ($rsAry[0] == $app_id) {
                $str .= '<option value="' . $rsAry[0] . '" selected>' . $rsAry[1] . '</option>';
            } else {
                $str .= '<option value="' . $rsAry[0] . '" >' . $rsAry[1] . '</option>';
            }
        }
        $str .= '</select>';
        $str .= '</div>';
        $str .= '<div id="img_1" class="tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" style="display:none">';
        $str .= '<hr class="full-width" />';
        $str .= '<label for="lrcm_img_1"><b>圖片</b></label>';
        $str .= '<label class="btn btn-danger btn-xs" id="uploadFileChooseButton1"><input type="file" name="lrcm_img[]" id="lrcm_img1" value="" class="upload" style="display:none;" required></input>選擇檔案</label>';
        $str .= '<div id="uploadFilePreviewBlock">';
        $str .= '<label id="uploadFileDelete1"></label>';
        $str .= '<label id="uploadFilePreview1"></label>';
        $str .= '<label id="uploadFileMsg1"></label>';
        $str .= '</div>';
        $str .= '</div>';
    }
//    $str .= '<div id="img_1" style="display:none">';
//    $str .= '<hr class="full-width" />';
//    $str .= '<label for="lrcm_img_1"><b>圖片</b></label>';
//    $str .= '<label class="btn btn-danger btn-xs" id="uploadFileChooseButton1"><input type="file" name="lrcm_img[]" id="lrcm_img1" class="upload" style="display:none;" required></input>選擇檔案</label>';
//    $str .= '<div id="uploadFilePreviewBlock">';
//    $str .= '<label id="uploadFileDelete1"><span class="btn btn-danger" id="uploadFileDeleteButton1">刪除</span></label>';
//    $str .= '<label id="uploadFilePreview1"><img src="' . CDN_ROOT_PATH . $lrcm_cdn_root . '" style="width:300px;"></label>';
//    $str .= '<label id="uploadFileMsg1"></label>';
//    $str .= '</div>';
//    $str .= '</div>';
}

echo trim($str);
?>

