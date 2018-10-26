<?php

use FB\FBApp;
use FB\FBApp\Builder\PostBuilder\Feed;
use FB\FBApp\Builder\PostBuilder\Photo;
use FB\FBApp\Builder\PostBuilder\PrivateReplies;

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('date.timezone', 'Asia/Taipei');
ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . '/assets_rear/session/');
session_start();
//函式庫
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/chiliman_config.php");
/* CDN S3 */
require_once(AUTOLOAD_PATH);
require LIBRARY_ROOT_PATH . '/S3/s3.php';
//CDN
/* DateNow */

//取得固定參數
$c_id = '1234567899';
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
$google_short = new GoogleShortAPI();
//取得新增參數
//定義時間參數

/* get token */
$sql = " select c_fb_appid, c_fb_secret, c_fb_token, c_fb_patch, c_fb_fans ";
$sql .= " FROM crm_m ";
$sql .= " WHERE c_id = ? ";
$FB_Info = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 5);
$fbApp = new FBApp($FB_Info[0][0], $FB_Info[0][1], $FB_Info[0][2], $FB_Info[0][3]);
$fbpg_id = $FB_Info[0][4];

/* get token */
$sql = " SELECT fpa_name, fpa_content ";
$sql .= " FROM fb_post_article ";
$sql .= " WHERE c_id = ? AND fpa_id = 'ff5f3f2773934c91eb37b3e243938140'";
$FBPOS = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 2);

//推文類型
$request = $fbApp->send(FBApp::API_TYPE_POST_FEED, "me", new Feed($FBPOS[0][0] . PHP_EOL . base64_decode($FBPOS[0][1]), "", TRUE, ""));
$fpa_post_id = $request['id'];
echo $fpa_post_id;
?>