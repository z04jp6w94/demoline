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
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
$sql = "SELECT group_id, user_status, user_account, user_password, user_name, user_address, user_email, user_phone, c_id FROM sysuser WHERE user_id = ?";
$initAry = $mysqli->readArrayPreSTMT($sql, "s", array($dataKey), 9);
$sql = "SELECT group_id, group_name FROM sysgroup";
$sql .= " where c_id = ? ";
$sql .= " and group_id != '1' ";
$group_Ary = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);
$sql = " SELECT c_id, c_name from crm_m ";
$sql .= " WHERE c_id = ? ";
$userAry = $mysqli->readArrayPreSTMT($sql, "s", array($initAry[0][8]), 2);
$crm_name = !empty($userAry[0][1]) ? trim($userAry[0][1]) : '';
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
                                            <h5><b>系統群組名稱</b></h5>
                                            <select class="e1" id="group_id" name="group_id" style="width: 100%;" required>
                                                <option value="">請選擇</option>
                                                <?php foreach ($group_Ary as $rsAry) { ?>
                                                    <option value="<?php echo $rsAry[0]; ?>" <?php
                                                    if ($rsAry[0] == $initAry[0][0]) {
                                                        echo 'selected';
                                                    }
                                                    ?>><?php echo $rsAry[1] ?></option>
                                                        <?php } ?>
                                            </select>
                                            <hr class="full-width" />  
                                            <h5><b>客戶</b></h5>
                                            <b>【<?php echo $crm_name; ?>】</b>
                                            <hr class="full-width" /> 
                                            <h5><b>帳號是否啟用</b></h5>
                                            <div class="radio">
                                                <label>
                                                    <input name="user_status" type="radio" value="Y" <?php if ($initAry[0][1] == 'Y') {
                                                            echo 'checked';
                                                        } ?>>
                                                    <span class="text">是 </span>
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input name="user_status" type="radio" value="N" <?php if ($initAry[0][1] == 'N') {
                                                            echo 'checked';
                                                        } ?>>
                                                    <span class="text">否</span>
                                                </label>
                                            </div>
                                            <hr class="full-width" />
                                            <label for="definput"><b>系統使用者帳號</b></label>
                                            <input type="text" class="form-control" id="definput" name="user_account" value="<?php echo $initAry[0][2]; ?>" placeholder="請輸入使用者帳號" autocomplete="off" title="請輸入使用者帳號" required />
                                            <hr class="full-width" />                                                                                
                                            <label for="definput"><b>系統使用者密碼</b></label>
                                            <input type="text" class="form-control" id="definput" name="user_password" value="<?php echo DECCode($initAry[0][3]); ?>" placeholder="請輸入使用者密碼" autocomplete="off" title="請輸入使用者密碼" required />
                                            <hr class="full-width" />
                                            <label for="definput"><b>系統使用者姓名</b></label>
                                            <input type="text" class="form-control" id="definput" name="user_name" value="<?php echo $initAry[0][4]; ?>" placeholder="請輸入使用者姓名" autocomplete="off" title="請輸入使用者姓名" required />
                                            <hr class="full-width" />
                                            <label for="definput"><b>系統使用者地址</b></label>
                                            <input type="text" class="form-control" id="definput" name="user_address" value="<?php echo $initAry[0][5]; ?>" placeholder="請輸入使用者地址" autocomplete="off" title="請輸入使用者地址" />
                                            <hr class="full-width" />
                                            <label for="definput"><b>系統使用者信箱</b></label>
                                            <input type="email" class="form-control" id="definput" name="user_email" value="<?php echo $initAry[0][6]; ?>" placeholder="請輸入使用者信箱" autocomplete="off" title="請輸入使用者信箱" required />
                                            <hr class="full-width" />
                                            <label for="definput"><b>系統使用者電話</b></label>
                                            <input type="text" class="form-control" id="definput" name="user_phone" value="<?php echo $initAry[0][7]; ?>" placeholder="請輸入使用者電話" autocomplete="off" title="請輸入使用者電話" />
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
            <!-- icon -->


        </div>
    </body>
</html>