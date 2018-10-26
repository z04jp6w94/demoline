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
//Turn
$dataKey = DECCode($dataKey);
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
$sql = "SELECT mlm_dsp, mlm_name, mlm_email, mlm_phone, mlm_remark, ";
$sql .= " mlm_lineid ";
$sql .= " FROM member_list_m ";
$sql .= " WHERE mlm_id = ? ";
$sql .= " AND c_id = ? ";
$initAry = $mysqli->readArrayPreSTMT($sql, "ss", array($dataKey, $c_id), 6);
$mlm_lineid = $initAry[0][5];
//Member_Tag
$sql = " SELECT mlm_lineid, ct_id FROM member_tag ";
$sql .= " WHERE mlm_lineid = ? ";
$sql .= " AND c_id = ? ";
$MT_Ary = $mysqli->readArrayPreSTMT($sql, "ss", array($mlm_lineid, $c_id), 2);
$m_tag_ary = array();
for ($i = 0; $i < count($MT_Ary); $i++) {
    array_push($m_tag_ary, $MT_Ary[$i][1]);
}
/* 代碼 */
$sql = " SELECT ct_id, ct_name FROM code_tag";
$sql .= " WHERE c_id = ? ";
$c_ary = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);
/* 被標籤次數 */
$sql = " SELECT ct.ct_name, (SELECT count(*) FROM member_list_t mlt WHERE mlt.mlm_lineid = ? and c_id = ? and mlt.ct_id = ct.ct_id) ct_count ";
$sql .= " FROM code_tag ct ";
$sql .= " WHERE ct.c_id = ? ";
$sql .= " ORDER BY ct_count DESC ";
$tag_list_ary = $mysqli->readArrayPreSTMT($sql, "sss", array($mlm_lineid, $c_id, $c_id), 2);
/* Push */
$sql = " SELECT p.p_name, mpc.mpc_count FROM member_push_click mpc ";
$sql .= " LEFT JOIN member_list_m mlm on mlm.mlm_lineid = mpc.mlm_lineid ";
$sql .= " LEFT JOIN push_m p on p.p_id = mpc.datakey ";
$sql .= " WHERE mpc.mlm_lineid = ? ";
$sql .= " AND mpc.c_id = ? ";
$sql .= " AND mpc.source = '1' ";
$sql .= " GROUP BY mpc.datakey ";
$sql .= " ORDER BY mpc.mpc_count DESC ";
$mlt_count_ary = $mysqli->readArrayPreSTMT($sql, "ss", array($mlm_lineid, $c_id), 2);
/* 總點擊貼文數 */
$sql = " SELECT COUNT(mlm_lineid) FROM member_push_click ";
$sql .= " WHERE mlm_lineid = ? ";
$sql .= " AND c_id = ? ";
$sql .= " AND source = '1' ";
$total_push_clicks = $mysqli->readValuePreSTMT($sql, "ss", array($mlm_lineid, $c_id));
/* 總標籤的數量 */
$sql = " SELECT COUNT(mlm_lineid) FROM member_list_t ";
$sql .= " WHERE mlm_lineid = ? ";
$sql .= " AND c_id = ? ";
$total_tags = $mysqli->readValuePreSTMT($sql, "ss", array($mlm_lineid, $c_id));
/* List */
$sql = " SELECT p.p_name,group_concat(ct.ct_name), mlt.entry_datetime FROM member_list_t mlt ";
$sql .= " LEFT JOIN push_m p on p.p_id = mlt.datakey AND mlt.c_id = ? ";
$sql .= " LEFT JOIN code_tag ct on ct.ct_id = mlt.ct_id ";
$sql .= " WHERE mlt.mlm_lineid = ? ";
$sql .= " AND mlt.source = '1' ";
$sql .= " AND mlt.c_id = ? ";
$sql .= " GROUP BY mlt.datakey, mlt.entry_datetime ";
$sql .= " ORDER BY mlt.entry_datetime desc ";
$mlt_ary = $mysqli->readArrayPreSTMT($sql, "sss", array($c_id, $mlm_lineid, $c_id), 3);
/* 客服歷程 */
$sql = " SELECT lcsmd_dialogue_number, user_name, lcsmd_community_user_name, lcsmd_start_time, lcsmd_end_time ";
$sql .= " FROM lcs_module_dialogue ";
$sql .= " WHERE c_id = ? ";
$sql .= " AND lcsmd_community_user_id = ? ";
$lcsmd_ary = $mysqli->readArrayPreSTMT($sql, "ss", array($c_id, $mlm_lineid), 5);
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
                                            <label for="definput"><b>顧客姓名</b></label>
                                            <input type="text" name="mlm_name" id="mlm_name" maxlength="20" value="<?php echo $initAry[0][1]; ?>" class="form-control" required />
                                            <hr class="full-width" />     
                                            <label for="definput"><b>顧客信箱</b></label>
                                            <input type="text" name="mlm_email" id="mlm_email" maxlength="50" value="<?php echo $initAry[0][2]; ?>" class="form-control" />
                                            <hr class="full-width" />
                                            <label for="definput"><b>顧客電話</b></label>
                                            <input type="text" name="mlm_phone" id="mlm_phone" maxlength="15" value="<?php echo $initAry[0][3]; ?>" class="form-control" />
                                            <hr class="full-width" />
                                            <div class="col-md-6">
                                                <label for="definput"><b>貼文總點擊數</b></label>
                                                <input type="text" value="<?php echo $total_push_clicks; ?>" class="form-control input-mini" disabled />
                                                <hr class="full-width" />
                                            </div>
                                            <div class="col-md-6">
                                                <label for="definput"><b>標籤總數</b></label>
                                                <input type="text" value="<?php echo $total_tags; ?>" class="form-control input-mini" disabled />
                                                <hr class="full-width" />
                                            </div>
                                            <h5><b>顧客註解</b></h5>
                                            <textarea class="form-control" name="mlm_remark" id="mlm_remark" cols="60" rows="5" ><?php echo $initAry[0][4]; ?></textarea>
                                            <hr class="full-width" />
                                            <h5><b>顧客標籤</b></h5>
                                            <select class="e2" id="ct_id" name="ct_id[]" style="width: 100%;" multiple="multiple">
                                                <option value="">請選擇</option>
                                                <?php foreach ($c_ary as $rsAry) { ?>
                                                    <option value="<?php echo $rsAry[0] ?>" 
                                                    <?php
                                                    if (in_array($rsAry[0], $m_tag_ary)) {
                                                        echo 'selected';
                                                    }
                                                    ?>><?php echo $rsAry[1]; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <hr class="full-width" />
                                        </div>
                                        <ul class="ulcss">
                                            <li class="licss licssselect" type="1">活動</li>
                                            <li class="licss" type="2">標籤次數</li>
                                            <li class="licss" type="3">推文點擊次數</li>
                                            <li class="licss" type="4">歷程</li>
                                            <li class="licss" type="5">客服對話記錄</li>
                                        </ul>
                                        <hr class="full-width" />
                                        <div class="divexternalcss">
                                            <div class="divcss1 divinternalcss divinternalcssselect">活動</div>
                                            <div class="divcss2 divinternalcss">
                                                <table class="table7 table table-celled">
                                                    <tr>
                                                        <th>標籤名稱</th>
                                                        <th>次數</th>
                                                    </tr>
                                                    <?php foreach ($tag_list_ary as $rsAry) { ?>
                                                        <tr>
                                                            <td><?php echo $rsAry[0]; ?></td> 
                                                            <td><?php echo $rsAry[1]; ?></td>
                                                        </tr>
                                                    <?php } ?>    
                                                </table>
                                            </div>
                                            <div class="divcss3 divinternalcss">
                                                <table class="table7 table table-celled">
                                                    <tr>
                                                        <th>推文名稱</th>
                                                        <th>點擊次數</th>
                                                    </tr>
                                                    <?php foreach ($mlt_count_ary as $rsAry) { ?>
                                                        <tr>
                                                            <td><?php echo $rsAry[0]; ?></td> 
                                                            <td><?php echo $rsAry[1]; ?></td>
                                                        </tr>
                                                    <?php } ?>    
                                                </table>
                                            </div>
                                            <div class="divcss4 divinternalcss">
                                                <table class="table7 table table-celled">
                                                    <tr>
                                                        <th>推文名稱</th>
                                                        <th>推文標籤</th>
                                                        <th>紀錄時間</th>
                                                    </tr>
                                                    <?php foreach ($mlt_ary as $rsAry) { ?>
                                                        <tr>
                                                            <td><?php echo $rsAry[0]; ?></td> 
                                                            <td><?php echo $rsAry[1]; ?></td>
                                                            <td><?php echo $rsAry[2]; ?></td>
                                                        </tr>
                                                    <?php } ?>    
                                                </table>
                                            </div>
                                            <div class="divcss5 divinternalcss">
                                                <table class="table7 table table-celled">
                                                    <tr>
                                                        <th>客服人員</th>
                                                        <th>開始時間</th>
                                                        <th>結束時間</th>
                                                    </tr>
                                                    <?php foreach ($lcsmd_ary as $rsAry) { ?>
                                                        <tr>
                                                            <td><?php echo $rsAry[1]; ?></td> 
                                                            <td><?php echo $rsAry[3]; ?></td>
                                                            <td><?php echo $rsAry[4]; ?></td>
                                                        </tr>
                                                    <?php } ?>    
                                                </table>
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
            <link rel="stylesheet" type="text/css" href="<?php echo CDN_STATIC_PATH; ?>/assets_rear/stylesheets/tag_list.css" />
            <script type="text/javascript" src="<?php echo CDN_STATIC_PATH; ?>/assets_rear/javascripts/tag_list.js"></script>

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
