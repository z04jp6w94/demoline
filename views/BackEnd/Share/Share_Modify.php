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
$fileName = !empty($_REQUEST["fileName"]) ? $_REQUEST["fileName"] : "";
$program_id = !empty($_REQUEST["program_id"]) ? $_REQUEST["program_id"] : "";
$program_name = !empty($_REQUEST["program_name"]) ? $_REQUEST["program_name"] : "";
$dataKey = !empty($_REQUEST["dataKey"]) ? $_REQUEST["dataKey"] : "";
//Turn
$dataKey = DECCode($dataKey);
chkSourceFileName('22', $program_id);
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
/* c_line_name */
$sql = " SELECT c_line_name FROM crm_m ";
$sql .= " WHERE c_id = ? ";
$c_line_name = $mysqli->readValuePreSTMT($sql, "s", array($c_id));
/* Data */
$sql = " SELECT sa_title, sa_content, sa_awards_content, sa_awards_img, sa_cdn_root, ";
$sql .= " sa_standard_number, sa_standard_content ";
$sql .= " FROM share_activity ";
$sql .= " WHERE sa_id = ? ";
$sql .= " AND c_id = ? ";
$initAry = $mysqli->readArrayPreSTMT($sql, "ss", array($dataKey, $c_id), 7);
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
                            <form name="updateForm" id="updateForm" method="post" action="<?php echo $fileName; ?>_Update.php" autocomplete="off" enctype="multipart/form-data">
                                <input type="hidden" id="fileName" name="fileName" value="<?php echo $fileName; ?>" />
                                <input type="hidden" id="dataKey" name="dataKey" value="<?php echo $dataKey; ?>" />
                                <input type="hidden" id="ischange_sa_awards_img" name="ischange_sa_awards_img" value="N" />
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <a id="Save" onclick="chkFormField(updateForm);" class="btn btn-danger">儲存</a>
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
                                            <label for="definput">活動主旨</label>
                                            <input type="text" id="sa_title" name="sa_title" value="<?php echo $initAry[0][0]; ?>" onchange="ChangeValue(this);" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" placeholder="請輸入活動主旨" maxlength="30" >
                                            <hr class="full-width" />
                                            <h5>活動內容(符號請使用半形)</h5>
                                            <textarea placeholder="請輸入活動內容" onchange="ChangeValue(this);" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" id="sa_content" name="sa_content" placeholder="" cols="60" rows="5" maxlength="1500"><?php echo $initAry[0][1]; ?></textarea>
                                            <hr class="full-width" />
                                            <h5>活動獎品內容</h5>
                                            <textarea placeholder="請輸入活動獎品內容" onchange="ChangeValue(this);" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" id="sa_awards_content" name="sa_awards_content" placeholder="" cols="60" rows="5" maxlength="1500"><?php echo $initAry[0][2]; ?></textarea>
                                            <hr class="full-width" />                                            
                                            <label for="definput">活動獎品圖片</label>
                                            <label class="btn btn-danger btn-xs" id="uploadFileChooseButton"><input type="file" name="sa_awards_img" id="sa_awards_img" style="display:none;"></input>選擇檔案</label>
                                            <div id="uploadFilePreviewBlock">
                                                <label id="uploadFileDelete" for="definput"><?php if ($initAry[0][3] != "") echo "<span class='btn btn-danger' id='uploadFileDeleteButton'>刪除</span>"; ?></label>
                                                <label id="uploadFilePreview" for="definput"><?php if ($initAry[0][3] != "") echo "<img src='" . CDN_ROOT_PATH . $initAry[0][4] . "' style='width:300px;'><img>"; ?></label>
                                                <label id="uploadFileMsg" for="definput"></label>
                                            </div>
                                            <hr class="full-width" />
                                            <h5><b>活動規則</b></h5>
                                            <div class="col-md-3">
                                                <label for="definput">分享達標人數<input type="text" id="sa_standard_number" name="sa_standard_number" disabled value="<?php echo $initAry[0][5]; ?>" maxlength="5" placeholder="請輸入達標人數(數字)" class="form-control input-mini" /></label>
                                            </div>
                                            <hr class="full-width" /> 
                                            <h5>達標完成回應訊息</h5>
                                            <textarea placeholder="請輸入達標完成回應訊息" onchange="ChangeValue(this);" class="form-control tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="" id="sa_standard_content" name="sa_standard_content" placeholder="" cols="60" rows="5" maxlength="1500"><?php echo $initAry[0][6]; ?></textarea>
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
            <script src="/assets_front/javascripts/Share/Share_Modify.js"></script>

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
