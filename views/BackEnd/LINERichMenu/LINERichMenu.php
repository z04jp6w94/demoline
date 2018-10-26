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
/*  */
$sqlFrom = " FROM richmenu_set_m ";
$sqlWhere = " WHERE 1 = 1";
$sqlWhere .= " AND c_id = '" . $c_id . "' ";
//確認使用者權限
$create = "disabled";
$read = "disabled";
$update = "disabled";
$delete = "disabled";
chkUserFunc($mysqli, $user_id, $programPath);
//取得畫面內容
$sql = "SELECT richmenu_id, rsm_title, rsm_status, entry_datetime ";
$sql .= $sqlFrom;
$sql .= $sqlWhere;
$sql .= " ORDER BY entry_datetime DESC ";
$initAry = $mysqli->readArraySTMT($sql, 4);
$menu_count = count($initAry);
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
            var menu_count = '<?php echo $menu_count; ?>';
            function excute(action) {
                switch (action) {
                    case "add":
                        if (menu_count < 10) {
                            Form2.action = '<?php echo $fileName; ?>_Add.php';
                            Form2.submit();
                        } else {
                            $('#add-row-btn').tooltip().attr('data-original-title', 'MENU設定最多10筆,請先刪除其他資料！');
                            $('#add-row-btn').tooltip("show");
                        }
                        break;
                    case "delete":
                        Form2.action = '<?php echo $fileName; ?>_Delete.php';
                        if (confirm('確定要刪除點選的項目嗎?')) {
                            $('#add-row-btn').tooltip("hide");
                            Form2.submit();
                        }
                        break;
                    case "update":
                        Form2.action = '<?php echo $fileName; ?>_Modify.php';
                        Form2.submit();
                        break;
                    case "change":
                        Form2.action = '<?php echo $fileName; ?>_Change.php';
                        Form2.submit();
                        break;
                }
            }
            function setKey(dataKey) {
                Form2.dataKey.value = dataKey;
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
                                    <?php if ($create == "abled") { ?><a id="add-row-btn" onClick="excute('add');" class="btn btn-success tooltip-warning" data-toggle="tooltip" data-placement="right" data-original-title="">建立</a><?php } ?>
                                    <div class="panel-tools">
                                        <a class="tools-action" href="#" data-toggle="collapse">
                                            <i class="pe-7s-angle-up"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-celled">
                                        <form name="Form2" id="Form2" method="post">
                                            <input type="hidden" id="fileName" name="fileName" value="<?php echo ENCCode($fileName); ?>" />
                                            <input type="hidden" id="program_id" name ="program_id" value="<?php echo ENCCode($program_id); ?>" />
                                            <input type="hidden" id="program_name" name ="program_name" value="<?php echo ENCCode($program_name); ?>" />
                                            <input type="hidden" id="dataKey" name="dataKey" value="0" />
                                        </form>
                                        <thead>
                                            <tr>
                                                <th>編輯</th>
                                                <th>Menu名稱</th>
                                                <th>狀態</th>
                                                <th>建立時間</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php foreach ($initAry as $rsAry) { ?> 
                                                <tr>
                                                    <td>
                                                        <?php if ($update == "abled") { ?><a onClick="setKey('<?php echo ENCCode($rsAry[0]); ?>');excute('update');" class="btn btn-info btn-sm"><i class="fa fa-search"></i>檢視</a><?php } ?>
                                                        <?php if ($delete == "abled" && $rsAry[2] == "N") { ?><a onClick="setKey('<?php echo ENCCode($rsAry[0]); ?>');excute('delete');" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i>刪除</a><?php } ?>
                                                    </td>
                                                    <td><?php echo $rsAry[1]; ?></td>
                                                    <td>
                                                        <?php if ($rsAry[2] == "Y") { ?>
                                                            <a onClick="setKey('<?php echo ENCCode($rsAry[0]); ?>');excute('change');" class="btn btn-inverse btn-sm"><i class="fa fa-check"></i></a>
                                                        <?php } else { ?>
                                                            <a onClick="setKey('<?php echo ENCCode($rsAry[0]); ?>');excute('change');" class="btn btn-inverse btn-sm"><i class="fa fa-close"></i></a>
                                                            <?php } ?>
                                                    </td>
                                                    <td><?php echo $rsAry[3]; ?></td>
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
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/jquery.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/bootstrap.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/modernizr.custom.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/slimscroll/jquery.slimscroll.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/animsition/animsition.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/main.js"></script>

            <!--
            [7. Page Related Scripts]
            -->

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/datatables/jquery.dataTables.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/datatables/dataTables.responsive.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/datatables/dataTables.bootstrap.min.js"></script>

        </div>
    </body>
</html>