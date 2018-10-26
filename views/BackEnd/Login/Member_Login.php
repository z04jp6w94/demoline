<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('date.timezone', 'Asia/Taipei');
ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . '/assets_rear/session/');
session_start();
//函式庫
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/chiliman_config.php");
//取得固定參數
$user_id = !empty($_SESSION["user_id"]) ? $_SESSION["user_id"] : NULL;
$programPath = explode(".php", $_SERVER['REQUEST_URI'])[0] . ".php";
$fileName = basename(__FILE__, '.php');
//GetCookie
$cookie_c_id = !empty($_COOKIE["cookie_c_id"]) ? $_COOKIE["cookie_c_id"] : NULL;
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
    <head>
        <meta charset="utf-8" />
        <title>LINE CRM</title>
        <meta name="description" content="" />
        <meta name="keywords" content="" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <link rel="shortcut icon" href="/assets/img/favicon.ico" type="image/x-icon">

        <!--Css Files-->
        <link rel="stylesheet" href="/assets/css/bootstrap.min.css" type="text/css" />
        <link rel="stylesheet" href="/assets/css/animate.min.css" type="text/css" />
        <link rel="stylesheet" href="/assets/css/app.min.css" type="text/css" />
        <link href="/assets/css/font-awesome.min.css" rel="stylesheet" />
        <link href="/assets/css/pe-icon-7-stroke.css" rel="stylesheet" />
    </head>
    <body>
        <div class="row membership">
            <div class="col-lg-8 col-md-6 hidden-sm hidden-xs membership-brand">
                <div class="brand-wrapper">
                    <div class="brand-container">
                        <a href="">
                            <img class="brand-logo" src="<?php echo CDN_STATIC_PATH;?>/assets/img/logo.png" alt="Yima - Admin Web App" />
                        </a>
                        <div class="brand-title">
                            Welcome to LINE-CRM
                        </div>
                        <div class="brand-subtitle">
                            Login Your Account.
                        </div>
                        <div class="brand-description">
                            LINE-CRM是通過LINE與客戶建立緊密聯繫，使用者的互動將紀錄於平台，針對使用者喜好推出不同行銷活動。
                        </div>
                        <div class="brand-action">
                            <input type="button" onclick="javascript:location.href = 'https://line.me/R/ti/p/@tjb5269a'" class="btn btn-info" value="加入LINE@好友">
                        </div>
                        <ul class="brand-links">>
                            <li>
                                <a href="">聯絡我們</a>
                            </li>
                            <li>
                                <a href="">問題協助</a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 membership-container">
                <a class="hidden" id="toregister"></a>
                <a class="hidden" id="tologin"></a>
                <a href="" class="hidden-lg hidden-md">
                    <img class="brand-logo" src="<?php echo CDN_STATIC_PATH;?>/assets/img/logo.png" alt="CR Web" />
                </a>

                <form name="form1" id="form1" method="post" action="<?php echo $fileName; ?>_Action.php" autocomplete="off">
                    <input type="hidden" id="fileName" name="fileName" value="<?php echo $fileName; ?>" />
                    <div class="login animated">
                        <div class="membership-title">使用者登入</div>   
                        <div class="membership-input">                            
                            <input type="text" class="form-control" name="user_c_id" placeholder="客戶號" autocomplete="off" value="<?php echo $cookie_c_id; ?>" title="請輸入客戶號" required minlength="10" />                        
                        </div>
                        <div class="membership-input">
                            <input type="text" class="form-control" maxlength="12" name="user_account" placeholder="帳號" autocomplete="off" title="請輸入帳號" required />
                        </div>
                        <div class="membership-input">
                            <input type="password" class="form-control" name="user_password" placeholder="密碼" autocomplete="off" title="請輸入密碼" required />
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="membership-input">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" id="remember" name="remember" value="Y">
                                            <span class="text">記住我</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="membership-forgot pull-right">
                                    <a class="btn btn-sm" href="Forgot.php"><i class="fa fa-question">忘記密碼</i></a>
                                </div>
                            </div>
                        </div>

                        <div class="membership-submit">
                            <input type="submit" id="BtnSubmit" class="btn btn-primary btn-lg btn-block" value="登入">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!--Js Files-->
        <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/jquery.min.js"></script>
        <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/bootstrap.min.js"></script>
        <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/modernizr.custom.js"></script>
        <!-- FormVaildation -->
        <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/slimscroll/jquery.slimscroll.min.js"></script>
        <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/animsition/animsition.min.js"></script>
        <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/main.js"></script>
        <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/validation/jquery.validate.min.js"></script>
        <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/lib/validation/jquery.validate.defaults.js"></script>

        <script src="<?php echo CDN_STATIC_PATH;?>/assets/js/pages/formvalidation.js"></script>
    </body>
</html>