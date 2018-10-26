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
$user_name = !empty($_SESSION["user_name"]) ? $_SESSION["user_name"] : NULL;
$group_id = !empty($_SESSION["group_id"]) ? $_SESSION["group_id"] : NULL; //使用者群組Menu使用
//取得固定參數
$fileName = $_REQUEST["fileName"];
$program_id = $_REQUEST["program_id"];
$program_name = $_REQUEST["program_name"];
$dataKey = $_REQUEST["dataKey"];
/* Catch 5 */
$fileName = DECCode($fileName);
$program_id = DECCode($program_id);
$program_name = DECCode($program_name);
$dataKey = DECCode($dataKey);
chkSourceFileName('5', $program_id);
//資料庫連線  
$mysqli = new DatabaseProcessorForWork();
//Admin
$sql = "SELECT a.program_id, a.program_name, IFNULL(b.authority_create,'N') AS 'authority_create', IFNULL(b.authority_read,'N') AS 'authority_read', IFNULL(b.authority_update,'N') AS 'authority_update', IFNULL(b.authority_delete,'N') AS 'authority_delete', IFNULL(b.authority_dsp,'N') AS 'authority_dsp'";
$sql .= " FROM sysprogram a";
$sql .= " LEFT JOIN sysauthority b ON a.program_id = b.program_id AND b.group_id = ? ";
$sql .= " WHERE 1 = 1 ";
$sql .= " AND a.program_id in (select program_id from sysmenu where group_id = ? ) ";
$initAry = $mysqli->readArrayPreSTMT($sql, "ss", array($dataKey, $group_id), 7);
$programCount = count($initAry);
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
            function authorityAll(rowNum) {
                if ($("#authority_dsp" + rowNum).prop("checked")) {
                    $("#authority_create" + rowNum).prop("checked", true);
                    $("#authority_read" + rowNum).prop("checked", true);
                    $("#authority_update" + rowNum).prop("checked", true);
                    $("#authority_delete" + rowNum).prop("checked", true);
                } else {
                    $("#authority_create" + rowNum).prop("checked", false);
                    $("#authority_read" + rowNum).prop("checked", false);
                    $("#authority_update" + rowNum).prop("checked", false);
                    $("#authority_delete" + rowNum).prop("checked", false);
                }
            }
            function chkFormField(updateForm) {
                updateForm.submit();
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
                                    <a id="add-row-btn" onClick="chkFormField(updateForm);" class="btn btn-danger">儲存</a>
                                    <a id="add-row-btn" href="<?php echo $fileName . ".php"; ?>" class="btn btn-default">取消</a>
                                    <div class="panel-tools">
                                        <a class="tools-action" href="#" data-toggle="collapse">
                                            <i class="pe-7s-angle-up"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-celled table-striped table-hover">
                                        <form name="updateForm" id="updateForm" method="post" action="Sysauthority_Update.php" enctype="multipart/form-data" >
                                            <input type="hidden" id="fileName" name="fileName" value="<?php echo ENCCode($fileName); ?>" />
                                            <input type="hidden" id="dataKey" name="dataKey" value="<?php echo ENCCode($dataKey); ?>" />
                                            <input type="hidden" id="programCount" name="programCount" value="<?php echo $programCount; ?>" />
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>系統程式名稱</th>
                                                    <th>新增權限</th>
                                                    <th>查詢權限</th>
                                                    <th>修改權限</th>
                                                    <th>刪除權限</th>
                                                    <th>後台是否顯示</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $rowNum = 0; ?>
                                                <?php foreach ($initAry as $rsAry) { ?>
                                                    <?php $rowNum++; ?>
                                                    <tr>
                                                        <th scope="row"><?php echo $rowNum; ?></th>   
                                                        <td><input type="hidden" id="program_id<?php echo $rowNum ?>" name="program_id<?php echo $rowNum ?>" value="<?php echo $rsAry[0]; ?>" /><?php echo $rsAry[1]; ?></td>
                                                        <td>
                                                            <div class="checkbox">
                                                                <label><input type="checkbox" class="checkbox-inverse" name="authority_create<?php echo $rowNum ?>" id="authority_create<?php echo $rowNum ?>" value="Y"<?php if ($rsAry[2] == 'Y') echo " checked"; ?>/>
                                                                    <span class="text"></span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="checkbox">
                                                                <label><input type="checkbox" class="checkbox-inverse" name="authority_read<?php echo $rowNum ?>" id="authority_read<?php echo $rowNum ?>" value="Y"<?php if ($rsAry[3] == 'Y') echo " checked"; ?>/>
                                                                    <span class="text"></span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="checkbox">
                                                                <label><input type="checkbox" class="checkbox-inverse" name="authority_update<?php echo $rowNum ?>" id="authority_update<?php echo $rowNum ?>" value="Y"<?php if ($rsAry[4] == 'Y') echo " checked"; ?>/>
                                                                    <span class="text"></span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="checkbox">
                                                                <label><input type="checkbox" class="checkbox-inverse" name="authority_delete<?php echo $rowNum ?>" id="authority_delete<?php echo $rowNum ?>" value="Y"<?php if ($rsAry[5] == 'Y') echo " checked"; ?>/>
                                                                    <span class="text"></span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="checkbox">
                                                                <label><input type="checkbox" class="checkbox-inverse" name="authority_dsp<?php echo $rowNum ?>" id="authority_dsp<?php echo $rowNum ?>" value="Y"<?php if ($rsAry[6] == 'Y') echo " checked"; ?> onclick="authorityAll(<?php echo $rowNum ?>)"/>
                                                                    <span class="text"></span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </form>
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

        </div>
    </body>
</html>
