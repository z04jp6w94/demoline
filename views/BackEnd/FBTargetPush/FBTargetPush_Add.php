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
$BaseUrl = $fileName . "_Add";
$WebUrl = basename(__FILE__, '.php');
chkSourceUrl($BaseUrl, $WebUrl);
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
                            <form name="createForm" id="createForm" class="push_1" method="post" action="<?php echo $fileName; ?>_Create.php" autocomplete="off" enctype="multipart/form-data">
                                <input type="hidden" id="fileName" name="fileName" value="<?php echo $fileName; ?>" />
                                <input type="hidden" id="count_lrcm_img" name="count_lrcm_img" value="0" />
                                <input type="hidden" id="count_carousel" name="count_carousel" value="0" />
                                <input id="del_text" name="del_text" type="hidden" value="">
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <a id="Save" onclick="chkFormField(createForm);" class="btn btn-danger">儲存</a>
                                        <a id="Cancel" href="<?php echo $fileName . ".php"; ?>" class="btn btn-default">取消</a>
                                        <div class="panel-tools">
                                            <a class="tools-action" href="#" data-toggle="collapse">
                                                <i class="pe-7s-angle-up"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div>
                                            <h4><b>FB Messenger目標推播</b></h4>
                                            <hr class="full-width" />
                                            <div>
                                                <h5>發送狀態</h5>
                                                <div class="hr-space space-xl"></div>
                                                <div class="form-group">
                                                    <label>
                                                        <input id ="fpm_send_status" name="fpm_send_status" value="1" type="radio" checked="checked">
                                                        <span class="text">立刻發送 </span>
                                                    </label>
                                                    <label>
                                                        <input id ="fpm_send_status" name="fpm_send_status" value="2" type="radio">
                                                        <span class="text">預約發送</span>
                                                    </label>
                                                </div>
                                                <hr class="full-width" />
                                            </div>
                                            <div id="send_date">
                                                <h5>發送日期</h5>
                                                <div class="input-group">
                                                    <input class="form-control date-picker tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" id="fpm_send_date" name="fpm_send_date" type="text" data-date-format="yyyy-mm-dd" readonly>
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                </div>
                                                <h5>發送時間</h5>
                                                <div class="input-group">
                                                    <input class="form-control" id="timepicker" name="fpm_send_time" type="text" readonly>
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-clock-o"></i>
                                                    </span>
                                                </div>
                                                <hr class="full-width" />
                                            </div>
                                            <div>
                                                <h5>選擇類型</h5>
                                                <select id="fpm_push_type" name="fpm_push_type" class="e1 tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" style="width: 100%;" required>
                                                    <option value="">請選擇</option>
                                                    <option value="1">文字</option>
                                                    <option value="2">圖像</option>
                                                    <option value="3">輪播</option>
                                                    <option value="4">文字&&按鈕</option>
                                                    <option value="5">清單型</option>
                                                    <option value="6">媒體</option>
                                                </select>                                            
                                                <hr class="full-width" />
                                            </div>    
                                            <div id="menu_content">

                                            </div>

                                            <div id="AddButton">

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
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/jquery.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/bootstrap.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/modernizr.custom.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/slimscroll/jquery.slimscroll.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/animsition/animsition.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/main.js"></script>
            <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/views/BackEnd/CommonHeader.php"); ?>    
            <script src="/assets_front/javascripts/FBTargetPush/FBTargetPush_Add.js"></script>

            <!--
            [7. Page Related Scripts]
            -->
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/select2/select2.full.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/tagsinput/bootstrap-tagsinput.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/datepicker/bootstrap-datepicker.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/timepicker/bootstrap-timepicker.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/moment/moment.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/daterangepicker/daterangepicker.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/autosize/jquery.autosize.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/spinbox/spinbox.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/knob/jquery.knob.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/colorpicker/jquery.minicolors.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/slider/ion.rangeSlider.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/dropzone/dropzone.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/rating/jquery.rateit.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/mockjax/jquery.mockjax.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/xeditable/bootstrap-editable.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/pages/formadvancedinputs.js"></script>

        </div>
    </body>
</html>
