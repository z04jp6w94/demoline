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
$sql = " SELECT lpv_id, lpv_name, lpv_img_cdn_root, lpv_video_cdn_root, lpv_send_status, ";
$sql .= " lpv_send_date ";
$sql .= " FROM line_push_video ";
$sql .= " WHERE lpv_id = ? ";
$sql .= " AND c_id = ? ";
$initAry = $mysqli->readArrayPreSTMT($sql, "ss", array($dataKey, $c_id), 6);
$datetime = $initAry[0][5];
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
                            <form name="updateForm" id="updateForm" method="post" action="<?php echo $fileName; ?>.php" autocomplete="off">
                                <input type="hidden" id="lpv_send_status" name="lpv_send_status" value="<?php echo $initAry[0][4]; ?>">
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <a id="add-row-btn" href="<?php echo $fileName . ".php"; ?>" class="btn btn-default">取消</a>
                                        <div class="panel-tools">
                                            <a class="tools-action" href="#" data-toggle="collapse">
                                                <i class="pe-7s-angle-up"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div>
                                            <h4><b><?php echo "LINE@:【" . $c_line_name . "】"; ?></b></h4>
                                            <hr class="full-width" />                                            
                                            <label for="definput">影片名稱</label>
                                            <input type="text" id="lpv_name" name="lpv_name" onchange="ChangeValue(this);" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入影片名稱" maxlength="35" >
                                            <hr class="full-width" />
                                            <div id="tool_tip_img" class="tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="">
                                                <label for="definput">影片預覽圖片</label>
                                                <label class="btn btn-danger btn-xs" id="uploadFileChooseButton"><input type="file" name="lpv_img" id="lpv_img" style="display:none;" disabled></input>選擇檔案</label>
                                                <div id="uploadFilePreviewBlock">
                                                    <label id="uploadFileDelete" for="definput"></label>
                                                    <label id="uploadFilePreview" for="definput"><?php if ($initAry[0][2] != "") echo "<img src='" . CDN_ROOT_PATH . $initAry[0][2] . "' style='width:300px;'><img>"; ?></label>
                                                    <label id="uploadFileMsg" for="definput"></label>
                                                </div>
                                                <hr class="full-width" />
                                            </div>
                                            <div id="tool_tip_video" class="tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="">
                                                <label for="definput">影片檔案(1分鐘,mp4,10MB)</label>
                                                <label class="btn btn-danger btn-xs" id="uploadVideoChooseButton"><input type="file" name="lpv_video" id="lpv_video" style="display:none;" disabled></input>選擇檔案</label>
                                                <div id="uploadVideoPreviewBlock">
                                                    <label id="uploadVideoDelete" for="definput"></label>
                                                    <label id="uploadVideoPreview" for="definput">
                                                        <video width="400" controls>
                                                            <?php if ($initAry[0][3] != "") echo "<source src='" . CDN_ROOT_PATH . $initAry[0][3] . "' type='video/mp4'>"; ?>
                                                        </video>    
                                                    </label>
                                                    <label id="uploadVideoMsg" for="definput"></label>
                                                </div>
                                                <hr class="full-width" />
                                            </div>
                                            <h5><b>發送狀態</b></h5>
                                            <?php
                                            if ($initAry[0][4] == '1') {
                                                echo '立刻發送';
                                            } else if ($initAry[0][4] == '2') {
                                                echo '預約發送';
                                            }
                                            ?>
                                            <hr class="full-width" />
                                            <div id="send_date">
                                                <h5>發送日期</h5>
                                                <div class="input-group">
                                                    <input class="form-control date-picker tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" id="lpv_send_date" name="lpv_send_date" value="<?php echo $sendDate; ?>" type="text" data-date-format="yyyy-mm-dd" readonly>
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                </div>
                                                <h5>發送時間</h5>
                                                <div class="input-group">
                                                    <input class="form-control" id="timepicker" name="lpv_send_time" type="text" value="<?php echo $sendTime; ?>" readonly>
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-clock-o"></i>
                                                    </span>
                                                </div>
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
            <script src="/assets_front/javascripts/LINEPushVideo/LINEPushVideo_Modify.js"></script>

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
