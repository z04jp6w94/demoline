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
//CheckUrl
$program_ary = chkUserSecurity($mysqli, $user_id, $programPath);
$program_id = $program_ary[0][0];
$program_name = $program_ary[0][1];
/* COOKIE */
setcookie("FilePath", $_SERVER['SCRIPT_NAME'], time() + 3600 * 24, "/");
setcookie("program_id", $program_id, time() + 3600 * 24, "/");
setcookie("program_name", $program_name, time() + 3600 * 24, "/");
/*  */
$sqlFrom = " FROM code_tag ct ";
$sqlWhere = " WHERE 1 = 1";
$sqlWhere .= " AND ct.c_id = '" . $c_id . "' ";
//確認使用者權限
$create = "disabled";
$read = "disabled";
$update = "disabled";
$delete = "disabled";
chkUserFunc($mysqli, $user_id, $programPath);
//取得畫面內容
$sql = " SELECT ct.ct_name, (SELECT count(*) FROM member_list_t mlt WHERE c_id = ? and mlt.ct_id = ct.ct_id) ";
$sql .= $sqlFrom;
$sql .= $sqlWhere;
$sql .= " ORDER BY (SELECT count(*) FROM member_list_t mlt WHERE c_id = ? and mlt.ct_id = ct.ct_id) DESC ";
$initAry = $mysqli->readArrayPreSTMT($sql, "ss", array($c_id, $c_id), 2);
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
        <link rel="stylesheet" href="/assets/css/demo.min.css" type="text/css" />
        <link rel="stylesheet" href="/assets/css/font-awesome.min.css" type="text/css" />
        <link rel="stylesheet" href="/assets/css/pe-icon-7-stroke.css" type="text/css" />
        <script type="text/javascript">
            function excute(action) {
                switch (action) {
                    case "export":
                        Form2.action = '<?php echo $fileName; ?>_Export.php';
                        Form2.submit();
                        break;
                }
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
                        <div class="col-lg-12">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <div class="panel-tools">
                                        <a class="tools-action" href="#" data-toggle="collapse">
                                            <i class="pe-7s-angle-up"></i>
                                        </a>
                                    </div>
                                    <a id="add-row-btn" onClick="excute('export');" class="btn btn-default">匯出</a>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-celled">
                                        <form name="Form2" id="Form2" method="post">
                                            <input type="hidden" id="dataKey" name="dataKey" value="0" />
                                        </form>
                                        <thead>
                                            <tr>
                                                <th>標籤名稱</th>
                                                <th>總點擊次數</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php foreach ($initAry as $rsAry) { ?> 
                                                <tr>
                                                    <td><?php echo $rsAry[0]; ?></td>
                                                    <td><?php echo $rsAry[1]; ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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

            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/datatables/jquery.dataTables.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/datatables/dataTables.responsive.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/datatables/dataTables.bootstrap.min.js"></script>

        </div>
    </body>
</html>
