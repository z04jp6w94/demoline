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
//定義時間參數
$excuteDateTime = date("Y-m-d H:i:s"); //操作日期
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
$BaseUrl = $fileName . "_Export";
$WebUrl = basename(__FILE__, '.php');
chkSourceUrl($BaseUrl, $WebUrl);
/*  */
$sqlFrom = " FROM push_m p ";
$sqlLeftJoin = " LEFT JOIN code_push cp on cp.cp_id = p.cp_id ";
$sqlWhere = " WHERE 1 = 1 ";
$sqlWhere .= " AND p.c_id = '" . $c_id . "' ";
$sqlWhere .= " AND p.deletestatus = 'N' ";
//資料庫
$sql = " SELECT p.p_name, cp.cp_name, (SELECT ifnull(sum(mpc_count),0) FROM member_push_click mpc WHERE mpc.datakey = p.p_id AND mpc.source = '1' ) as temp ";
$sql .= $sqlFrom;
$sql .= $sqlLeftJoin;
$sql .= $sqlWhere;
$sql .= " ORDER BY temp DESC ";
$initAry = $mysqli->readArraySTMT($sql, 3);
//PHPExcel
require_once($_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php");
$PHPExcel = new PHPExcel();
//設定Excel詳細資料(PHPExcel_DocumentProperties)
$PHPExcel->getProperties()->setTitle("Title") //標題
        ->setSubject("Subject") //主旨   
        ->setKeywords("Keywords") //標籤
        ->setCategory("Category") //類別
        ->setDescription("Description") //註解
        ->setCreator("Creator") //作者
        ->setLastModifiedBy("LastModifiedBy"); //上次存檔者
//指定目前要編輯的工作表
$PHPExcel->setActiveSheetIndex(0);
//設定工作表名稱
$PHPExcel->getActiveSheet()->setTitle("標籤報表點擊量");
//設定儲存格內容
$PHPExcel->getActiveSheet()->setCellValue("A1", "匯出時間：" . $excuteDateTime);
$PHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setName("標楷體");
$PHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize("12");
$PHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
$PHPExcel->getActiveSheet()->getStyle("A1")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
//合併或分離儲存格
$PHPExcel->getActiveSheet()->mergeCells("A1:C1");
//設定儲存格內容
$PHPExcel->getActiveSheet()->setCellValue("A2", "推文名稱");
$PHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(40);
$PHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setName("標楷體");
$PHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setSize("10");
$PHPExcel->getActiveSheet()->getStyle("A2")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
$PHPExcel->getActiveSheet()->getStyle("A2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$PHPExcel->getActiveSheet()->setCellValue("B2", "推文分類");
$PHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(20);
$PHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setName("標楷體");
$PHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setSize("10");
$PHPExcel->getActiveSheet()->getStyle("B2")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
$PHPExcel->getActiveSheet()->getStyle("B2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$PHPExcel->getActiveSheet()->setCellValue("C2", "總點擊次數");
$PHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(20);
$PHPExcel->getActiveSheet()->getStyle("C2")->getFont()->setName("標楷體");
$PHPExcel->getActiveSheet()->getStyle("C2")->getFont()->setSize("10");
$PHPExcel->getActiveSheet()->getStyle("C2")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
$PHPExcel->getActiveSheet()->getStyle("C2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//設定儲存格內容
$rowPotion = 3;
foreach ($initAry as $rsAry) {
    $PHPExcel->getActiveSheet()->setCellValue("A" . $rowPotion, $rsAry[0]);
    $PHPExcel->getActiveSheet()->getStyle("A" . $rowPotion)->getFont()->setName("標楷體");
    $PHPExcel->getActiveSheet()->getStyle("A" . $rowPotion)->getFont()->setSize("10");
    $PHPExcel->getActiveSheet()->getStyle("A" . $rowPotion)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $PHPExcel->getActiveSheet()->setCellValue("B" . $rowPotion, $rsAry[1]);
    $PHPExcel->getActiveSheet()->getStyle("B" . $rowPotion)->getFont()->setName("標楷體");
    $PHPExcel->getActiveSheet()->getStyle("B" . $rowPotion)->getFont()->setSize("10");
    $PHPExcel->getActiveSheet()->getStyle("B" . $rowPotion)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $PHPExcel->getActiveSheet()->setCellValue("C" . $rowPotion, $rsAry[2]);
    $PHPExcel->getActiveSheet()->getStyle("C" . $rowPotion)->getFont()->setName("標楷體");
    $PHPExcel->getActiveSheet()->getStyle("C" . $rowPotion)->getFont()->setSize("10");
    $PHPExcel->getActiveSheet()->getStyle("C" . $rowPotion)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $rowPotion++;
}

//Excel 2003
ob_end_clean();
$filename = iconv("UTF-8", "BIG5", "推文報表 - " . $excuteDateTime . ".xls");
header("Content-Type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=" . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5');
$objWriter->save('php://output');
exit();
?>