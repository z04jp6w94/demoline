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
$menu_id = $_REQUEST["dataKey"];
$program_id = $_REQUEST["program_id"];
$program_name = $_REQUEST["program_name"];
$group_id = $_REQUEST["group_id"];
//Turn
$fileName = DECCode($fileName);
$program_id = DECCode($program_id);
$program_name = DECCode($program_name);
$group_id = DECCode($group_id);
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
$sql = "SELECT menu_id, menu_folder, menu_icon FROM sysmenu WHERE menu_id = ? ";
$initAry = $mysqli->readArrayPreSTMT($sql, "s", array($menu_id), 3);
//Icons
$sql = " SELECT icons_id, icons_name FROM sysicons";
$icon_ary = $mysqli->readArraySTMT($sql, 2);
?>
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
        <script type="text/javascript">
            function BackToPage(form1) {
                form1.action = "Sysfolder.php";
                form1.submit();
            }
        </script>
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
                                <input type="hidden" id="fileName" name="fileName" value="<?php echo ENCCode($fileName); ?>" />
                                <input type="hidden" id="dataKey" name="dataKey" value="<?php echo ENCCode($group_id); ?>" />
                                <input type="hidden" id="program_id" name="program_id" value="<?php echo ENCCode($program_id); ?>" />
                                <input type="hidden" id="program_name" name="program_name" value="<?php echo ENCCode($program_name); ?>" />
                                <input type="hidden" id="group_id" name="group_id" value="<?php echo ENCCode($group_id); ?>" />
                                <input type="hidden" id="menu_id" name="menu_id" value="<?php echo ENCCode($menu_id); ?>" />
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <input type="submit" id="BtnSubmit" class="btn btn-danger" value="儲存">
                                        <a id="add-row-btn" onclick="BackToPage(form1);" class="btn btn-default">取消</a>
                                        <div class="panel-tools">
                                            <a class="tools-action" href="#" data-toggle="collapse">
                                                <i class="pe-7s-angle-up"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div>
                                            <label for="definput">主選單名稱</label>
                                            <input type="text" class="form-control" id="definput" name="menu_folder" value="<?php echo $initAry[0][1]; ?>" placeholder="請輸入主選單名稱" autocomplete="off" title="請輸入主選單名稱" required />
                                            <hr class="full-width" />
                                            <h5>選擇Icon圖</h5>
                                            <div class="row">
                                                <div class="form-group">
                                                    <?php $i = 0; ?>
                                                    <?php foreach ($icon_ary as $i_ary) { ?>
                                                        <div class="radio radio-inline">
                                                            <label>
                                                                <input name="menu_icon" type="radio" value="<?php echo $i_ary[1]; ?>" <?php
                                                                if ($initAry[0][2] == $i_ary[1]) {
                                                                    echo 'checked';
                                                                }
                                                                ?>/>
                                                                <span class="text"><i id="select_icon" style="font-size: 32px;" class="<?php echo $i_ary[1]; ?>"></i></span>
                                                            </label>  
                                                        </div>
                                                        <?php $i++; ?>
                                                    <?php } ?>
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
            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/jquery.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/bootstrap.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/modernizr.custom.js"></script>
            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/slimscroll/jquery.slimscroll.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/animsition/animsition.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/main.js"></script>

            <!--
            [7. Page Related Scripts]
            -->

        </div>
    </body>
</html>

