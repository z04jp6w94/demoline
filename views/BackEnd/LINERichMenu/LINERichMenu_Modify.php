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
//SESSION
$user_id = !empty($_SESSION["user_id"]) ? $_SESSION["user_id"] : NULL;
$user_name = !empty($_SESSION["user_name"]) ? $_SESSION["user_name"] : NULL;
$c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
//取得固定參數
$fileName = $_REQUEST["fileName"];
$program_id = $_REQUEST["program_id"];
$program_name = $_REQUEST["program_name"];
$dataKey = $_REQUEST["dataKey"];
/* Catch 17 */
$fileName = DECCode($fileName);
$program_id = DECCode($program_id);
$program_name = DECCode($program_name);
$dataKey = DECCode($dataKey);
chkSourceFileName('17', $program_id);
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
/* Data */
$sql = "SELECT richmenu_id, rsm_title, rsm_img, rsm_type, rsm_status ";
$sql .= " FROM richmenu_set_m ";
$sql .= " WHERE richmenu_id = ?";
$sql .= " AND c_id = ? ";
$initAry = $mysqli->readArrayPreSTMT($sql, "ss", array($dataKey, $c_id), 5);
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
        <style>
            .li1_1 li:nth-child(1){ 
                display:inline;
                height:60px;
                background-color: #FFC0CB;
                border-right-color:#FFFAF0;
                border-right-style:solid;
                border-right-width:1px;
                border-bottom-color:#FFFAF0;
                border-bottom-style:solid;
                border-bottom-width:1px;
            }
            .li1_1 li:nth-child(2){ 
                display:inline;
                height:60px;
                background-color: #D3D3D3;
                border-bottom-color:#FFFAF0;
                border-bottom-style:solid;
                border-bottom-width:1px;
            }
            .li1_1 li:nth-child(3){ 
                display:inline;
                height:60px;
                background-color: #D3D3D3;
                border-left-color:#FFFAF0;
                border-left-style:solid;
                border-left-width:1px;
                border-bottom-color:#FFFAF0;
                border-bottom-style:solid;
                border-bottom-width:1px;
            }
            .li1_2 li:nth-child(1){ 
                display:inline;
                height:60px;
                background-color: #D3D3D3;
                border-right-color:#FFFAF0;
                border-right-style:solid;
                border-right-width:1px;
                border-top-color:#FFFAF0;
                border-top-style:solid;
                border-top-width:1px;
            }
            .li1_2 li:nth-child(2){ 
                display:inline;
                height:60px;
                background-color: #D3D3D3;
                border-top-color:#FFFAF0;
                border-top-style:solid;
                border-top-width:1px;
            }
            .li1_2 li:nth-child(3){ 
                display:inline;
                height:60px;
                background-color: #D3D3D3;
                border-left-color:#FFFAF0;
                border-left-style:solid;
                border-left-width:1px;
                border-top-color:#FFFAF0;
                border-top-style:solid;
                border-top-width:1px;
            }
            .li2_1 li:nth-child(1){
                display:inline;
                height:60px;
                background-color: #FFC0CB;
                border-right-color:#FFFAF0;
                border-right-style:solid;
                border-right-width:1px;
                border-bottom-color:#FFFAF0;
                border-bottom-style:solid;
                border-bottom-width:1px;
            }
            .li2_1 li:nth-child(2){
                display:inline;
                height:60px;
                background-color: #D3D3D3;
                border-left-color:#FFFAF0;
                border-left-style:solid;
                border-left-width:1px;
                border-bottom-color:#FFFAF0;
                border-bottom-style:solid;
                border-bottom-width:1px;
            }
            .li2_2 li:nth-child(1){
                display:inline;
                height:60px;
                background-color: #D3D3D3;
                border-right-color:#FFFAF0;
                border-right-style:solid;
                border-right-width:1px;
                border-top-color:#FFFAF0;
                border-top-style:solid;
                border-top-width:1px;
            }
            .li2_2 li:nth-child(2){
                display:inline;
                height:60px;
                background-color: #D3D3D3;
                border-left-color:#FFFAF0;
                border-left-style:solid;
                border-left-width:1px;
                border-top-color:#FFFAF0;
                border-top-style:solid;
                border-top-width:1px;
            }
            .li3 li:nth-child(1){ 
                display:inline;
                height:60px;
                background-color: #D3D3D3;
                border-right-color:#FFFAF0;
                border-right-style:solid;
                border-right-width:1px;
                border-top-color:#FFFAF0;
                border-top-style:solid;
                border-top-width:1px;
            }
            .li3 li:nth-child(2){ 
                display:inline;
                height:60px;
                background-color: #D3D3D3;
                border-top-color:#FFFAF0;
                border-top-style:solid;
                border-top-width:1px;

            }
            .li3 li:nth-child(3){ 
                display:inline;
                height:60px;
                background-color: #D3D3D3;
                border-left-color:#FFFAF0;
                border-left-style:solid;
                border-left-width:1px;
                border-top-color:#FFFAF0;
                border-top-style:solid;
                border-top-width:1px;
            }
            .li4 li:nth-child(1){ 
                display:inline;
                height:120px;
                background-color: #FFC0CB;
                border-right-color:#FFFAF0;
                border-right-style:solid;
                border-right-width:1px;              
            }
            .li4 li:nth-child(2){ 
                float:left;
                display:inline;
                height:60px;
                background-color: #D3D3D3;
                border-left-color:#FFFAF0;
                border-left-style:solid;
                border-left-width:1px;
                border-bottom-color:#FFFAF0;
                border-bottom-style:solid;
                border-bottom-width:1px;
            }
            .li4 li:nth-child(3){ 
                float:left;
                display:inline;
                height:60px;
                background-color: #D3D3D3;
                border-left-color:#FFFAF0;
                border-left-style:solid;
                border-left-width:1px;
                border-top-color:#FFFAF0;
                border-top-style:solid;
                border-top-width:1px;                
            }
            .li5 li:nth-child(1){ 
                display:inline;
                height:60px;
                background-color: #FFC0CB;
                border-bottom-color:#FFFAF0;
                border-bottom-style:solid;
                border-bottom-width:1px;         
            }
            .li5 li:nth-child(2){ 
                display:inline;
                height:60px;
                background-color: #D3D3D3;
                border-top-color:#FFFAF0;
                border-top-style:solid;
                border-top-width:1px;
            }
            .li6 li:nth-child(1){ 
                display:inline;
                height:120px;
                background-color: #FFC0CB;
                border-right-color:#FFFAF0;
                border-right-style:solid;
                border-right-width:1px;         
            }
            .li6 li:nth-child(2){ 
                float:left;
                display:inline;
                height:120px;
                background-color: #D3D3D3;
                border-left-color:#FFFAF0;
                border-left-style:solid;
                border-left-width:1px;
            }
            .li7 li:nth-child(1){ 
                display:inline;
                height:120px;
                background-color: #FFC0CB;
                border-right-color:#FFFAF0;
                border-right-style:solid;
                border-right-width:1px;         
            }
            .select{
                background-color: #FFC0CB !important;
            }
            .unselect{
                background-color: #D3D3D3 !important;
            }
        </style>
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
                            <form name="createForm" id="createForm" method="post" action="<?php echo $fileName; ?>.php" autocomplete="off" enctype="multipart/form-data">
                                <input type="hidden" id="fileName" name="fileName" value="<?php echo $fileName; ?>" />
                                <input type="hidden" id="dataKey" name="dataKey" value="<?php echo $dataKey; ?>" />                            
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
                                            <label for="definput">Menu標題</label>
                                            <input type="text" id="rs_title" name="rs_title" value="<?php echo $initAry[0][1]; ?>" readonly maxlength="10" class="form-control" >
                                            <hr class="full-width" />
                                            <div id="tip_rs_img" class="tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title=""><label for="definput">Menu圖片</label></div>
                                            <div id="uploadFilePreviewBlock">
                                                <label id="uploadFilePreview" for="definput"><?php if ($initAry[0][2] != "") echo "<img src='" . $initAry[0][2] . "' style='width:300px;border:1px black solid;'><img>"; ?></label>
                                                <label id="uploadFileMsg" for="definput"></label>
                                            </div>
                                            <hr class="full-width" />
                                            <h5>版型選擇</h5>
                                            <div class="hr-space space-x1"></div>
                                            <div class="form-group">
                                                <label>
                                                    <input id ="rsm_type" name="rsm_type" class="menu_type" value="<?php echo $initAry[0][3]; ?>" type="radio" checked="checked">
                                                    <span class="text"><?php echo $initAry[0][3]; ?></span>
                                                    <img src="<?php echo CDN_STATIC_PATH;?>/assets_rear/images/default_menu/type_richmenu_0<?php echo $initAry[0][3]; ?>.png" style="width:146px;height: 96px;">
                                                </label>                                                
                                            </div>
                                            <hr class="full-width" />
                                            <h5>選單內容設定</h5>
                                            <div id="menu_setting">

                                            </div>
                                            <div id="menu_content">

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
            <script src="/assets_front/javascripts/LineMenu/LineMenu_Modify.js"></script>

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
