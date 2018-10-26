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
$c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
$user_id = !empty($_SESSION["user_id"]) ? $_SESSION["user_id"] : NULL;
$user_name = !empty($_SESSION["user_name"]) ? $_SESSION["user_name"] : NULL;
//取得固定參數
$fileName = $_REQUEST["fileName"];
$program_id = $_REQUEST["program_id"];
$program_name = $_REQUEST["program_name"];
$dataKey = $_REQUEST["dataKey"];
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
$sql = "SELECT c_id, c_name, c_tel, c_address, c_mail, ";
$sql .= " c_status, c_remark, c_line_OAID, c_line_CID, c_line_SECRET, ";
$sql .= " c_line_TOKEN, c_linelogin_CID, c_linelogin_SECRET, c_line_name, c_fb_appid, ";
$sql .= " c_fb_secret, c_fb_token, c_fb_patch, c_fb_fans ";
$sql .= " FROM crm_m ";
$sql .= " WHERE c_id = ?";
$initAry = $mysqli->readArrayPreSTMT($sql, "s", array($dataKey), 19);
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
        <link rel="shortcut icon" href="/assets/img/favicon.ico" type="image/x-icon">
        <!--
        [2. Css References]
        -->
        <link rel="stylesheet" href="/assets/css/bootstrap.min.css" type="text/css" id="link-bootstrap" />
        <link rel="stylesheet" href="/assets/css/animate.min.css" type="text/css" />
        <link rel="stylesheet" href="/assets/css/app.min.css" type="text/css" id="link-app" />
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
                            <form name="form1" id="form1" method="post" action="<?php echo $fileName; ?>_Update.php" autocomplete="off">
                                <input type="hidden" id="fileName" name="fileName" value="<?php echo $fileName; ?>" />
                                <input type="hidden" id="dataKey" name="dataKey" value="<?php echo $dataKey; ?>" />
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <input type="submit" id="BtnSubmit" class="btn btn-danger" value="儲存">
                                        <a id="add-row-btn" href="<?php echo $fileName . ".php"; ?>" class="btn btn-default">取消</a>
                                        <div class="panel-tools">
                                            <a class="tools-action" href="#" data-toggle="collapse">
                                                <i class="pe-7s-angle-up"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div>
                                            <label for="definput"><b>客戶編號</b></label>
                                            <input type="text" class="form-control" id="c_id" name="c_id" value="<?php echo $initAry[0][0]; ?>" placeholder="請點選下方按鈕取得編號" autocomplete="off" readonly />                                       
                                            <hr class="full-width" /> 
                                            <h5><b>是否啟用</b></h5>
                                            <label>
                                                <input name="c_status" type="radio" value="Y" <?php
                                                if ($initAry[0][5] == 'Y') {
                                                    echo 'checked';
                                                }
                                                ?>>
                                                <span class="text">是 </span>
                                            </label>
                                            <label>
                                                <input name="c_status" type="radio" value="N" <?php
                                                if ($initAry[0][5] == 'N') {
                                                    echo 'checked';
                                                }
                                                ?>>
                                                <span class="text">否</span>
                                            </label>
                                            <hr class="full-width" />
                                            <label for="definput"><b>客戶名稱</b></label>
                                            <input type="text" class="form-control" id="c_name" name="c_name" maxlength="50" value="<?php echo $initAry[0][1];?>" placeholder="請輸入客戶名稱" autocomplete="off" title="請輸入客戶名稱" required />
                                            <hr class="full-width" />                                                                                
                                            <label for="definput"><b>客戶電話</b></label>
                                            <input type="text" class="form-control" id="c_tel" name="c_tel" maxlength="20" value="<?php echo $initAry[0][2];?>" placeholder="請輸入客戶電話" autocomplete="off" title="請輸入客戶電話" required />
                                            <hr class="full-width" />
                                            <label for="definput"><b>客戶地址</b></label>
                                            <input type="text" class="form-control" id="c_address" name="c_address" maxlength="250" value="<?php echo $initAry[0][3];?>" placeholder="請輸入客戶地址" autocomplete="off" title="請輸入客戶地址" required />
                                            <hr class="full-width" />
                                            <label for="definput"><b>客戶信箱</b></label>
                                            <input type="text" class="form-control" id="c_mail" name="c_mail" maxlength="100" value="<?php echo $initAry[0][4];?>" placeholder="請輸入客戶信箱" autocomplete="off" title="請輸入客戶信箱" required />
                                            <hr class="full-width" />
                                            <h5><b>備註</b></h5>
                                            <textarea class="form-control" id="c_remark" name="c_remark" placeholder=""><?php echo $initAry[0][6];?></textarea>
                                            <hr class="full-width" />
                                            <h4><b>LINE</b></h4>
                                            <label for="definput"><b>LINE@名稱</b></label>
                                            <input type="text" class="form-control" id="c_line_name" name="c_line_name" value="<?php echo $initAry[0][13];?>" placeholder="" autocomplete="off" />
                                            <label for="definput"><b>LINE_OA_Id</b></label>
                                            <input type="text" class="form-control" id="c_line_OAID" name="c_line_OAID" maxlength="20" value="<?php echo $initAry[0][7];?>" placeholder="" autocomplete="off" required/>
                                            <label for="definput"><b>LINE_Channel_Id</b></label>
                                            <input type="text" class="form-control" id="c_line_CID" name="c_line_CID" minlength="10" maxlength="10" value="<?php echo $initAry[0][8];?>" placeholder="" autocomplete="off" required/>
                                            <label for="definput"><b>LINE_Secret</b></label>
                                            <input type="text" class="form-control" id="c_line_SECRET" name="c_line_SECRET" minlength="32" maxlength="32" value="<?php echo $initAry[0][9];?>" placeholder="" autocomplete="off" required/>
                                            <label for="definput"><b>LINE_Access_Token</b></label>
                                            <input type="text" class="form-control" id="c_line_TOKEN" name="c_line_TOKEN" minlength="172" maxlength="172" value="<?php echo $initAry[0][10];?>" placeholder="" autocomplete="off" required/>
                                            <hr class="full-width" />
                                            <h4><b>LINE_Login</b></h4>
                                            <label for="definput"><b>LINE_Channel_Id</b></label>
                                            <input type="text" class="form-control" id="c_linelogin_CID" name="c_linelogin_CID" minlength="10" maxlength="10" value="<?php echo $initAry[0][11];?>" placeholder="" autocomplete="off" required/>
                                            <label for="definput"><b>LINE_Secret</b></label>
                                            <input type="text" class="form-control" id="c_linelogin_SECRET" name="c_linelogin_SECRET" minlength="32" maxlength="32" value="<?php echo $initAry[0][12];?>" placeholder="" autocomplete="off" required/>
                                            <hr class="full-width" />
                                            <h4><b>FaceBook</b></h4>
                                            <label for="definput"><b>FB 應用程式Id</b></label>
                                            <input type="text" class="form-control" id="c_fb_appid" name="c_fb_appid" value="<?php echo $initAry[0][14];?>" placeholder="" autocomplete="off" />
                                            <label for="definput"><b>FB Secret</b></label>
                                            <input type="text" class="form-control" id="c_fb_secret" name="c_fb_secret" value="<?php echo $initAry[0][15];?>" placeholder="" autocomplete="off" />
                                            <label for="definput"><b>FB Token</b></label>
                                            <input type="text" class="form-control" id="c_fb_token" name="c_fb_token" value="<?php echo $initAry[0][16];?>" placeholder="" autocomplete="off" />
                                            <label for="definput"><b>FB API 版本</b></label>
                                            <input type="text" class="form-control" id="c_fb_patch" name="c_fb_patch" value="<?php echo $initAry[0][17];?>" placeholder="" autocomplete="off" />
                                            <label for="definput"><b>FB粉絲團 ID</b></label>
                                            <input type="text" class="form-control" id="c_fb_fans" name="c_fb_fans" value="<?php echo $initAry[0][18];?>" placeholder="" autocomplete="off" />
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