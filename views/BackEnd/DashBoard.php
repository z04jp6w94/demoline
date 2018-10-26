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
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
?>
<!DOCTYPE html>

<html lang="en">
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
        <link rel="stylesheet" href="/assets/css/demo.min.css" type="text/css" />
        <link rel="stylesheet" href="/assets/css/font-awesome.min.css" type="text/css" />
        <link rel="stylesheet" href="/assets/css/pe-icon-7-stroke.css" type="text/css" />
        <link media="all" type="text/css" rel="stylesheet" href="/assets_rear/stylesheets/amchart/plugins/export/export.css">
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

                        </div>

                    </div>
                </div>
                <!--
                [5.3. Page Body]
                -->
                <div class="content-body">
                    <table id="search-textbox-table" class="table table-celled">
                        <thead>
                            <tr>
                                <th>/</th>
                                <th>總人數</th>
                                <th>LINE</th>
                                <th>Facebook</th>
                            </tr>
                        </thead>

                        <tbody>                          
                            <tr>
                                <td>顧客來源</td>
                                <td>97 人</td>
                                <td>45 人</td>
                                <td>52 人</td>
                            </tr>
                            <tr>
                                <td>被標籤數量</td>
                                <td>197 次</td>
                                <td>123 次</td>
                                <td>74 次</td>
                            </tr>
                            <tr>
                                <td>最多人點擊的貼文</td>
                                <td>曼谷自助好旅行</td>
                                <td>曼谷自助好旅行</td>
                                <td>日本自助遊好旅行</td>
                            </tr>
                            <tr>
                                <td>最多人點擊的標籤</td>
                                <td>旅遊商品</td>
                                <td>旅遊商品</td>
                                <td>實體商品</td>
                            </tr>
                        </tbody>
                    </table>
                    <hr class="full-width" /> 
                    <div class="row">
                        <div class="col-lg-12">
                            <!--begin::Portlet-->
                            <div class="m-portlet m-portlet--tab">
                                <div class="m-portlet__head">
                                    <div class="m-portlet__head-caption">
                                        <div class="m-portlet__head-title">
                                            <span class="m-portlet__head-icon m--hide">
                                                <i class="la la-gear"></i>
                                            </span>
                                            <h3 class="m-portlet__head-text">
                                                時間性分析
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-portlet__body">
                                    <div id="m_amcharts_1" style="height: 500px;"></div>
                                </div>
                            </div>
                            <!--end::Portlet-->
                            <!--begin::Portlet-->
                            <div class="m-portlet m-portlet--tab">
                                <div class="m-portlet__head">
                                    <div class="m-portlet__head-caption">
                                        <div class="m-portlet__head-title">
                                            <span class="m-portlet__head-icon m--hide">
                                                <i class="la la-gear"></i>
                                            </span>
                                            <h3 class="m-portlet__head-text">
                                                區域性分析
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-portlet__body">
                                    <div id="m_amcharts_2" style="height: 500px;"></div>
                                </div>
                            </div>
                            <!--end::Portlet-->
                            <!--begin::Portlet-->
                            <div class="m-portlet m-portlet--tab">
                                <div class="m-portlet__head">
                                    <div class="m-portlet__head-caption">
                                        <div class="m-portlet__head-title">
                                            <span class="m-portlet__head-icon m--hide">
                                                <i class="la la-gear"></i>
                                            </span>
                                            <h3 class="m-portlet__head-text">
                                                文章點閱率分析
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-portlet__body">
                                    <div id="m_amcharts_3" style="height: 500px;"></div>
                                </div>
                            </div>
                            <!--end::Portlet-->
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
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets_rear/javascripts/amchart/amcharts.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets_rear/javascripts/amchart/serial.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets_rear/javascripts/amchart/radar.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets_rear/javascripts/amchart/pie.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets_rear/javascripts/amchart/plugins/tools/polarScatter.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets_rear/javascripts/amchart/plugins/animate/animate.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets_rear/javascripts/amchart/plugins/export/export.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets_rear/javascripts/amchart/themes/light.js"></script>

            <!--end::Page Vendors -->  
            <!--begin::Page Resources -->
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets_rear/javascripts/amchart/charts.js"></script>
            <!--
            [7. Page Related Scripts]
            -->

        </div>
    </body>
</html>