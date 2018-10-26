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
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
//SESSION
$user_id = !empty($_SESSION["user_id"]) ? $_SESSION["user_id"] : NULL;
$user_name = !empty($_SESSION["user_name"]) ? $_SESSION["user_name"] : NULL;
$c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
//COOKIE
$FilePath = !empty($_COOKIE["FilePath"]) ? $_COOKIE["FilePath"] : NULL;
//CheckUrl
$program_ary = chkUserSecurity($mysqli, $user_id, $FilePath);
$program_id = !empty($_COOKIE["program_id"]) ? $_COOKIE["program_id"] : NULL;
$program_name = !empty($_COOKIE["program_name"]) ? $_COOKIE["program_name"] : NULL;
$fileName = basename($FilePath, '.php');
/*  */
chkValueEmpty($program_id);
chkValueEmpty($program_name);
$BaseUrl = $fileName . "_Modify";
$WebUrl = basename(__FILE__, '.php');
chkSourceUrl($BaseUrl, $WebUrl);
//取得接收參數
$dataKey = !empty($_REQUEST["dataKey"]) ? $_REQUEST["dataKey"] : "";
chkValueEmpty($dataKey);
/* c_line_name */
$sql = " select c_line_name from crm_m ";
$sql .= " where c_id = ? ";
$c_line_name = $mysqli->readValuePreSTMT($sql, "s", array($c_id));
/* Data */
$sql = "SELECT cm_id, ct_id, cc_id, cm_name, cm_price, ";
$sql .= " cm_intro, cm_cdn_root, cm_type, cm_url, cm_shipping_fee, ";
$sql .= " cm_current_stock, cm_max_buy, cm_min_buy, cm_starttime, cm_endtime ";
$sql .= " FROM commodity_m ";
$sql .= " WHERE cm_id = ?";
$sql .= " AND c_id = ? ";
$initAry = $mysqli->readArrayPreSTMT($sql, "ss", array($dataKey, $c_id), 15);
//分類代碼檔
$sql = " select cc_id, cc_name from code_commodity ";
$sql .= " where c_id = ? ";
$sql .= " and cc_status = 'Y' ";
$cc_ary = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);
//標籤代碼檔
$sql = " select ct_id, ct_name from code_tag ";
$sql .= " where c_id = ? ";
$sql .= " and ct_status = 'Y' ";
$ct_ary = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);
/* 標籤 */
$ct_str = explode(",", $initAry[0][1]);
$datetime = $initAry[0][8];
$sendDate = date('Y-m-d', strtotime($datetime));
$sendTime = date('h:i A', strtotime($datetime));
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
    <head>
        <!--
        [1. Meta Tags]
        -->
        <meta charset="utf-8" />
        <title><?php echo HEADTITLE; ?></title>
        <meta name="description" content="" />
        <meta name="keywords" content="" /> 
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <link rel="shortcut icon" href="/assets/img/favicon.ico" type="image/x-icon"/>
        <!--
        [2. Css References]
        -->
        <link rel="stylesheet" href="/assets/css/bootstrap.min.css" type="text/css" id="link-bootstrap" />
        <link rel="stylesheet" href="/assets/css/animate.min.css" type="text/css" />
        <link rel="stylesheet" href="/assets/css/app.min.css" type="text/css" id="link-app" />
        <link rel="stylesheet" href="/assets/css/demo.min.css" type="text/css" />
        <link rel="stylesheet" href="/assets/css/font-awesome.min.css" type="text/css" />
        <link rel="stylesheet" href="/assets/css/pe-icon-7-stroke.css" type="text/css" />

    </head>
    <body>
        <div class="animsition">
            <!--
            [3. Sidebar Menu]
            -->
            <div class="sidebar menu">               
                <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/views/BackEnd/MenuHeader.php"); ?>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <?php echo initSysMenu($program_id); ?>
                    </ul>
                </div>
                <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/views/BackEnd/MenuFooter.php"); ?>
            </div>
            <!--
            [4. Sidebar Form]
            -->
            <div class="sidebar form collapsed">
            </div>
            <!--
            [5. Main Page Content]
            -->
            <div class="main-content">
                <!--
                [5.1. Page Header]
                -->
                <div class="content-header">
                    <!--
                    [5.1.1. BreadCrumb]
                    -->
                    <ul class="breadcrumb">
                        <?php echo BreadCrumb($program_id); ?>      
                    </ul>
                    <!--
                    [5.1.2. Header Buttons]
                    -->
                    <ul class="header-actions">
                        <li class="actions-stretch-menu" id="action-stretch-menu">
                            <div class="icon"></div>
                        </li>
                        <li id="action-menu-collapse">
                            <a>
                                <i class="pe-7s-menu"></i>
                            </a>
                        </li>
                    </ul>
                </div>

                <!--
                [5.2. Page Navbar]
                -->
                <div class="content-nav">
                    <div class="navbar navbar-default content-nav-navbar">
                        <div class="navbar-header">
                            <a data-toggle="collapse" data-target="#navbar-collapse-2" class="navbar-toggle collapsed">
                                <div class="icon"></div>
                            </a>
                            <a href="#" class="navbar-brand"><?php echo $program_name; ?></a>
                        </div>
                    </div>
                </div>
                <!--
                [5.3. Page Body]
                -->
                <div class="content-body">
                    <div class="row">
                        <div class="col-lg-12 col-sm-6 col-xs-12">
                            <form name="updateForm" id="updateForm" method="post" action="<?php echo $fileName; ?>_Update.php" autocomplete="off" enctype="multipart/form-data">
                                <input type="hidden" id="dataKey" name="dataKey" value="<?php echo $dataKey;?>" />
                                <input type="hidden" id="cm_type_value" name="cm_type_value" value="<?php echo $initAry[0][7]; ?>">
                                <input type="hidden" id="ischange_cm_img" name="ischange_cm_img" value="N" />
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <a id="Save" onclick="chkFormField(updateForm);" class="btn btn-danger">儲存</a>
                                        <a id="add-row-btn" href="<?php echo $fileName . ".php"; ?>" class="btn btn-default">取消</a>
                                        <div class="panel-tools">
                                            <a class="tools-action" href="#" data-toggle="collapse">
                                                <i class="pe-7s-angle-up"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div>
                                            <label for="definput">商品名稱</label>
                                            <input type="text" id="cm_name" name="cm_name" value="<?php echo $initAry[0][3]; ?>" onchange="ChangeValue(this);" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入商品名稱" maxlength="35" >
                                            <hr class="full-width" />
                                            <label for="definput"><b>上架時間 - 下架時間</b></label>
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                                <input type="text" name="cm_date_range" value="<?php echo str_replace("-", "/", substr($initAry[0][13], 0, 10)); ?> - <?php echo str_replace("-", "/", substr($initAry[0][14], 0, 10)); ?>" class="form-control" id="daterangepicker" />
                                            </div>
                                            <hr class="full-width" />
                                            <h5>商品分類</h5>
                                            <select id="cc_id" name="cc_id" class="e1 tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" style="width: 100%;">
                                                <option value="">請選擇</option>
                                                <?php foreach ($cc_ary as $rsAry) { ?>
                                                    <option value="<?php echo $rsAry[0]; ?>" 
                                                    <?php
                                                    if ($rsAry[0] == $initAry[0][2]) {
                                                        echo 'selected';
                                                    }
                                                    ?>>
                                                        <?php echo $rsAry[1]; ?></option>
                                                <?php } ?>
                                            </select>
                                            <hr class="full-width" />
                                            <h5>商品標籤</h5>
                                            <div class="checkbox">
                                                <?php foreach ($ct_ary as $rsAry) { ?>
                                                    <label><input type="checkbox" id="ct_id" name="ct_id[]" class="checkbox-info" value="<?php echo $rsAry[0]; ?>" 
                                                        <?php
                                                        if (in_array($rsAry[0], $ct_str)) {
                                                            echo 'checked';
                                                        }
                                                        ?>>
                                                        <span class="text"><?php echo $rsAry[1]; ?></span>
                                                    </label>
                                                <?php } ?> 
                                            </div>
                                            <hr class="full-width" />
                                            <label for="definput">商品價格</label>
                                            <input type="text" id="cm_price" name="cm_price" value="<?php echo $initAry[0][4]; ?>" onchange="ChangeValue(this);" onKeyUp="return this.value = this.value.replace(/\D/g, '')" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入商品價格" maxlength="10" >
                                            <hr class="full-width" />
                                            <h5>商品介紹</h5>
                                            <textarea class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" id="cm_intro" name="cm_intro" placeholder="限制字數60" cols="60" rows="5" maxlength="60"><?php echo $initAry[0][5]; ?></textarea>
                                            <hr class="full-width" />
                                            <div id="cm_img_tooltip" class="tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="">
                                                <label for="definput">商品圖片</label>
                                                <label class="btn btn-danger btn-xs" id="uploadFileChooseButton"><input type="file" name="cm_img" id="cm_img" style="display:none;"></input>選擇檔案</label>
                                                <div id="uploadFilePreviewBlock">
                                                    <label id="uploadFileDelete" for="definput"><?php if ($initAry[0][6] != "") echo "<span class='btn btn-danger' id='uploadFileDeleteButton'>刪除</span>"; ?></label>
                                                    <label id="uploadFilePreview" for="definput"><?php if ($initAry[0][6] != "") echo "<img src='" . CDN_ROOT_PATH . $initAry[0][6] . "' style='width:300px;'><img>"; ?></label>
                                                    <label id="uploadFileMsg" for="definput"></label>
                                                </div>
                                                <hr class="full-width" />
                                            </div>
                                            <h5>商品位置</h5>
                                            <div class="form-group">
                                                <label>
                                                    <input id ="cm_type" name="cm_type" value="1" type="radio" <?php
                                                    if ($initAry[0][7] == "1") {
                                                        echo 'checked';
                                                    }
                                                    ?>>
                                                    <span class="text">本系統預設</span>
                                                </label>
                                                <label>
                                                    <input id ="cm_type" name="cm_type" value="2" type="radio" <?php
                                                    if ($initAry[0][7] == "2") {
                                                        echo 'checked';
                                                    }
                                                    ?>>
                                                    <span class="text">外部連結</span>
                                                </label>
                                            </div>
                                            <hr class="full-width" />
                                            <div id="type1">
                                                <label for="definput">商品運費</label>
                                                <input type="text" id="cm_shipping_fee" name="cm_shipping_fee" value="<?php echo $initAry[0][9];?>" onchange="ChangeValue(this);" onKeyUp="return this.value = this.value.replace(/\D/g, '')" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入商品運費" maxlength="10" >
                                                <hr class="full-width" />
                                                <label for="definput">商品庫存量</label>
                                                <input type="text" id="cm_current_stock" name="cm_current_stock" value="<?php echo $initAry[0][10];?>" onchange="ChangeValue(this);" onKeyUp="return this.value = this.value.replace(/\D/g, '')" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入商品庫存量" maxlength="10" >
                                                <hr class="full-width" />
                                                <label for="definput">最大購買數量</label>
                                                <input type="text" id="cm_max_buy" name="cm_max_buy" value="<?php echo $initAry[0][11];?>" onchange="ChangeValue(this);" onKeyUp="return this.value = this.value.replace(/\D/g, '')" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入最大購買數量" maxlength="10" >
                                                <hr class="full-width" />
                                                <label for="definput">最小購買數量</label>
                                                <input type="text" id="cm_min_buy" name="cm_min_buy" value="<?php echo $initAry[0][12];?>" onchange="ChangeValue(this);" onKeyUp="return this.value = this.value.replace(/\D/g, '')" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入最小購買數量" maxlength="10" >
                                                <hr class="full-width" />
                                            </div>
                                            <div id="type2">
                                                <label for="definput">商品連結</label>
                                                <input type="text" id="cm_url" name="cm_url" value="<?php echo $initAry[0][8];?>" onchange="ChangeValue(this);" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入商品連結" maxlength="500" >
                                                <hr class="full-width" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--
            [6. JavaScript References]
            -->
            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/jquery.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/bootstrap.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/modernizr.custom.js"></script>
            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/slimscroll/jquery.slimscroll.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/animsition/animsition.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/main.js"></script>
            <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/views/BackEnd/CommonHeader.php"); ?>    
            <script src="/assets_front/javascripts/Commodity/Commodity_Modify.js"></script>

            <!--
            [7. Page Related Scripts]
            -->
            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/select2/select2.full.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/tagsinput/bootstrap-tagsinput.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/datepicker/bootstrap-datepicker.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/timepicker/bootstrap-timepicker.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/moment/moment.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/daterangepicker/daterangepicker.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/autosize/jquery.autosize.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/spinbox/spinbox.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/knob/jquery.knob.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/colorpicker/jquery.minicolors.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/slider/ion.rangeSlider.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/dropzone/dropzone.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/rating/jquery.rateit.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/mockjax/jquery.mockjax.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/xeditable/bootstrap-editable.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/pages/formadvancedinputs.js"></script>

        </div>
    </body>
</html>
