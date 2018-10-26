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
//取得固定參數
$user_id = !empty($_SESSION["user_id"]) ? $_SESSION["user_id"] : NULL;
$user_name = !empty($_SESSION["user_name"]) ? $_SESSION["user_name"] : NULL;
$c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
$programPath = explode(".php", $_SERVER['REQUEST_URI'])[0] . ".php";
$fileName = basename(__FILE__, '.php');
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
//取得內容
$sql = "SELECT user_name, user_email ";
$sql .= " FROM sysuser ";
$sql .= " WHERE user_id = ? ";
$sql .= " AND c_id = ? ";
$initAry = $mysqli->readArrayPreSTMT($sql, "ss", array($user_id, $c_id), 2);
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
    <head>
        <!--
        [1. Meta Tags]
        -->
        <meta charset="utf-8" />
        <title>LINE CRM</title>
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
                <!-- Menu -->
                <div class="sidebar-menu">
                    <ul class="menu">
                        <?php echo initSysMenu(); ?>
                    </ul>
                </div>

                <!-- Menu -->
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
                        <li>
                            <a href="#">
                                <i class="pe-7s-home"></i>
                                <span>Home</span>
                            </a>
                        </li>
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
                            <a href="#" class="navbar-brand">SocialCRM</a>
                        </div>

                    </div>
                </div>
                <!--
                [5.3. Page Body]
                -->
                <div class="content-body">
                    <div class="row">
                        <div class="col-lg-12 col-sm-6 col-xs-12">
<!--                            action="<?php //echo $fileName;   ?>_Update.php"-->
                            <form name="changeForm" id="changeForm" action="<?php echo $fileName?>_Update.php" method="post" autocomplete="off">
                                <input type="hidden" id="fileName" name="fileName" value="<?php echo $fileName;?>">
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <input type="submit" id="BtnSubmit" class="btn btn-danger" value="儲存">
                                        <div class="panel-tools">
                                            <a class="tools-action" href="#" data-toggle="collapse">
                                                <i class="pe-7s-angle-up"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div>
                                            <h4><b>變更密碼</b></h4>
                                            <hr class="full-width" />
                                            <div class="form-group">
                                                <div class="prepend-icon">
                                                    <input type="text" class="form-control" id="userameInput" name="user_name" value="<?php echo $initAry[0][0]; ?>" readonly placeholder="使用者名稱">
                                                    <i class="glyphicon glyphicon-user"></i>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="prepend-icon">                                                    
                                                    <input type="text" class="form-control" id="emailInput" name="email" value="<?php echo $initAry[0][1]; ?>" placeholder="Email Address">
                                                    <i class="fa fa-envelope-o circular"></i>
                                                </div>
                                            </div>
                                            <hr class="full-width" />
                                            <div class="form-group">
                                                <div class="prepend-icon">
                                                    <input type="password" class="form-control" id="password" name="password" placeholder="請輸入新密碼">
                                                    <i class="fa fa-lock circular"></i>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="prepend-icon">
                                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="確認新密碼">
                                                    <i class="fa fa-lock circular"></i>
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
            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/validation/jquery.validate.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/validation/jquery.validate.defaults.js"></script>

            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/pages/formvalidation.js"></script>

        </div>
    </body>
</html>
