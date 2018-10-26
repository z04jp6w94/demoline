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
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
//SESSION
$user_id = !empty($_SESSION["user_id"]) ? $_SESSION["user_id"] : NULL;
$user_name = !empty($_SESSION["user_name"]) ? $_SESSION["user_name"] : NULL;
$c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
//COOKIE
$FilePath = !empty($_COOKIE["FilePath"]) ? $_COOKIE["FilePath"] : NULL;
//CheckUrl
$program_ary = chkUserSecurity($mysqli, $user_id, $FilePath);
$program_id = !empty($_COOKIE["program_id"]) ? $_COOKIE["program_id"] : NULL;
$program_name = !empty($_COOKIE["program_name"]) ? $_COOKIE["program_name"] : NULL;
$fileName = basename($FilePath, '.php');
/*  */
chkValueEmpty($program_id);
chkValueEmpty($program_name);
$BaseUrl = $fileName . "_Modify";
$WebUrl = basename(__FILE__, '.php');
chkSourceUrl($BaseUrl, $WebUrl);
//取得接收參數
$dataKey = !empty($_REQUEST["dataKey"]) ? $_REQUEST["dataKey"] : "";
chkValueEmpty($dataKey);
//分類代碼檔
$sql = " SELECT cp_id, cp_name from code_push ";
$sql .= " where c_id = ? ";
$sql .= " AND cp_status = 'Y' ";
$cp_ary = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);
//標籤代碼檔
$sql = " SELECT ct_id, ct_name from code_tag ";
$sql .= " where c_id = ? ";
$sql .= " AND ct_status = 'Y' ";
$ct_ary = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);
/* Data */
$sql = "SELECT fpa_id, c_id, fbpg_id, cp_id, ct_id, ";
$sql .= " fpa_post_id, fpa_name, fpa_content, fpa_type, fpa_url, ";
$sql .= " fpa_img, fpa_cdn_root, fpa_private_replies_type, fpa_private_replies_keyword, fpa_private_replies, ";
$sql .= " fpa_push_type, scheduled_datetime, deletestatus, start_datetime, end_datetime, ";
$sql .= " entry_datetime ";
$sql .= " FROM fb_post_article ";
$sql .= " WHERE fpa_id = ?";
$sql .= " AND c_id = ? ";
$sql .= " AND deletestatus = 'N' ";
$initAry = $mysqli->readArrayPreSTMT($sql, "ss", array($dataKey, $c_id), 21);
/* 標籤 */
$ct_str = explode(",", $initAry[0][4]);
$datetime = $initAry[0][16];
$sendDate = date('Y-m-d', strtotime($datetime));
$sendTime = date('h:i A', strtotime($datetime));
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
                                <input type="hidden" id="ischange_fpa_img" name="ischange_fpa_img" value="N" />
                                <input type="hidden" id="fileName" name="fileName" value="<?php echo $fileName; ?>" />
                                <input type="hidden" id="dataKey" name="dataKey" value="<?php echo $dataKey; ?>" />
                                <input type="hidden" id="fpa_type" name="fpa_type" value="<?php echo $initAry[0][8]; ?>">
                                <input type="hidden" id="fpa_private_replies_type" name="fpa_private_replies_type" value="<?php echo $initAry[0][12]; ?>">
                                <input type="hidden" id="fpa_push_type" name="fpa_push_type" value="<?php echo $initAry[0][15]; ?>">
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
                                            <h4><b>FaceBook粉絲團貼文</b></h4>
                                            <hr class="full-width" />
                                            <div>
                                                <h5>選擇類型</h5>
                                                <?php
                                                if ($initAry[0][9] == '1') {
                                                    echo '文字&&內容';
                                                } else if ($initAry[0][9] == '2') {
                                                    echo '文字&&單張圖';
                                                } else if ($initAry[0][9] == '3') {
                                                    echo '文字&&網址';
                                                }
                                                ?>
                                                <hr class="full-width" />
                                            </div>
                                            <div id="sh_name">
                                                <label for="definput">貼文標題</label>
                                                <input type="text" id="fpa_name" name="fpa_name" value="<?php echo $initAry[0][6]; ?>" class="form-control" readonly >
                                                <hr class="full-width" />
                                            </div>
                                            <div>
                                                <h5>貼文分類</h5>
                                                <select id="cp_id" name="cp_id" class="e1" style="width: 100%;" disabled>
                                                    <option value="">請選擇</option>
                                                    <?php foreach ($cp_ary as $rsAry) { ?>
                                                        <option value="<?php echo $rsAry[0] ?>" 
                                                        <?php
                                                        if ($rsAry[0] == $initAry[0][3]) {
                                                            echo 'selected';
                                                        }
                                                        ?>><?php echo $rsAry[1]; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                                <hr class="full-width" />
                                            </div>
                                            <div>
                                                <h5>文章標籤</h5>
                                                <div class="checkbox">
                                                    <?php foreach ($ct_ary as $rsAry) { ?>
                                                        <label><input type="checkbox" id="ct_id" name="ct_id[]" class="checkbox-info" value="<?php echo $rsAry[0]; ?>" 
                                                            <?php
                                                            if (in_array($rsAry[0], $ct_str)) {
                                                                echo 'checked';
                                                            }
                                                            ?> disabled>
                                                            <span class = "text"><?php echo $rsAry[1];
                                                            ?></span>
                                                        </label>
                                                    <?php } ?> 
                                                </div>
                                                <hr class="full-width" />
                                            </div>
                                            <div id="sh_content">
                                                <h5>貼文內容</h5>
                                                <textarea class="form-control" id="fpa_content" name="fpa_content" readonly cols="60" rows="5" maxlength="10000"><?php echo $initAry[0][7]; ?></textarea>
                                                <hr class="full-width" />
                                            </div>
                                            <div id="sh_url">
                                                <label for="definput">貼文連結</label>
                                                <input type="text" id="fpa_url" name="fpa_url" value="<?php echo $initAry[0][9]; ?>" class="form-control" readonly>
                                                <hr class="full-width" />
                                            </div>
                                            <div id="sh_img" class="tooltip-warning" data-toggle="tooltip" data-placement="top" data-original-title="">
                                                <label for="definput">推文圖片</label>
                                                <label class="btn btn-danger btn-xs" id="uploadFileChooseButton"><input type="file" name="fpa_img" id="fpa_img" style="display:none;" disabled ></input>選擇檔案</label>
                                                <div id="uploadFilePreviewBlock">
                                                    <label id="uploadFileDelete" for="definput"></label>
                                                    <label id="uploadFilePreview" for="definput"><?php if ($initAry[0][11] != "") echo "<img src='" . CDN_ROOT_PATH . $initAry[0][11] . "' style='width:300px;'><img>"; ?></label>
                                                    <label id="uploadFilePreview" for="definput"></label>
                                                </div>
                                                <hr class="full-width" />
                                            </div>
                                            <div id="">
                                                <h5>私下回覆狀態</h5>
                                                <div class="hr-space space-xl"></div>
                                                <div class="form-group">
                                                    <?php
                                                    if ($initAry[0][12] == '1') {
                                                        echo '留言不啟用私下回覆';
                                                    } else if ($initAry[0][12] == '2') {
                                                        echo '留言皆啟用私下回覆';
                                                    } else if ($initAry[0][12] == '3') {
                                                        echo '留言搭配關鍵字啟用私下回覆';
                                                    }
                                                    ?>
                                                </div>
                                                <hr class="full-width" />
                                            </div>
                                            <div id="sh_keyword">
                                                <label for="definput">關鍵字</label>
                                                <input type="text" id="fpa_private_replies_keyword" name="fpa_private_replies_keyword" value="<?php echo $initAry[0][13]; ?>" class="form-control" readonly>
                                                <hr class="full-width" />
                                            </div> 
                                            <div id="sh_replies">
                                                <h5>私下回覆內容</h5>
                                                <textarea class="form-control" id="fpa_private_replies" name="fpa_private_replies" readonly cols="60" rows="5" maxlength="500"><?php echo $initAry[0][14]; ?></textarea>
                                                <hr class="full-width" />
                                            </div>
                                            <div id="">
                                                <h5>發送狀態</h5>
                                                <div class="hr-space space-xl"></div>
                                                <div class="form-group">
                                                    <?php
                                                    if ($initAry[0][15] == '1') {
                                                        echo '立刻發送';
                                                    } else if ($initAry[0][15] == '2') {
                                                        echo '預約發送';
                                                    }
                                                    ?>
                                                </div>
                                                <hr class="full-width" />
                                            </div>
                                            <div id="send_date">
                                                <h5>發送日期</h5>
                                                <div class="input-group">
                                                    <input class="form-control date-picker" id="scheduled_date" name="scheduled_date" value="<?php echo $sendDate; ?>" type="text" data-date-format="yyyy-mm-dd" readonly>
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                </div>
                                                <h5>發送時間</h5>
                                                <div class="input-group">
                                                    <input class="form-control" id="timepicker" name="scheduled_time" type="text" value="<?php echo $sendTime; ?>" readonly>
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-clock-o"></i>
                                                    </span>
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
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/jquery.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/bootstrap.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/modernizr.custom.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/slimscroll/jquery.slimscroll.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/animsition/animsition.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/main.js"></script>
            <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/views/BackEnd/CommonHeader.php"); ?>    
            <script src="/assets_front/javascripts/FBPostArticle/FBPostArticle_Modify.js"></script>
            <!--
            [7. Page Related Scripts]
            -->

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/select2/select2.full.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/tagsinput/bootstrap-tagsinput.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/datepicker/bootstrap-datepicker.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/timepicker/bootstrap-timepicker.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/moment/moment.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/daterangepicker/daterangepicker.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/autosize/jquery.autosize.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/spinbox/spinbox.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/knob/jquery.knob.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/colorpicker/jquery.minicolors.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/slider/ion.rangeSlider.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/dropzone/dropzone.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/rating/jquery.rateit.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/mockjax/jquery.mockjax.min.js"></script>
            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/lib/xeditable/bootstrap-editable.min.js"></script>

            <script src="<?php echo CDN_STATIC_PATH; ?>/assets/js/pages/formadvancedinputs.js"></script>

        </div>
    </body>
</html>

