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
//客戶
$c_id = !empty($_SESSION["c_id"]) ? $_SESSION["c_id"] : NULL;
//資料庫連線
$mysqli = new DatabaseProcessorForWork();
$sql = "SELECT mlm_id, mlm_lineid, mlm_name, mlm_email, mlm_phone, ";
$sql .= " case mlm_source ";
$sql .= " when '1' then 'Line' when '2' then 'Fb' when '3' then 'IG' when '98' then '系統' when '99' then '匯入' else '其他' end as 'source', ";
$sql .= " entry_date ";
$sql .= " FROM member_list_m ";
$sql .= " WHERE 1 = 1 ";
$sql .= " AND c_id = ? ";
$initAry = $mysqli->readArrayPreSTMT($sql, "s", array($c_id), 7);

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
$PHPExcel->getActiveSheet()->setTitle("顧客匯出紀錄表");
//設定儲存格內容
$PHPExcel->getActiveSheet()->setCellValue("A1", "匯出時間：" . $excuteDateTime);
$PHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setName("標楷體");
$PHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize("12");
$PHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
$PHPExcel->getActiveSheet()->getStyle("A1")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
//合併或分離儲存格
$PHPExcel->getActiveSheet()->mergeCells("A1:C1");
//標題
$PHPExcel->getActiveSheet()->setCellValue("A2", "Line_ID");
$PHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(35);
$PHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setName("標楷體");
$PHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setSize("10");
$PHPExcel->getActiveSheet()->getStyle("A2")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
$PHPExcel->getActiveSheet()->getStyle("A2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$PHPExcel->getActiveSheet()->setCellValue("B2", "顧客名稱");
$PHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(20);
$PHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setName("標楷體");
$PHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setSize("10");
$PHPExcel->getActiveSheet()->getStyle("B2")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
$PHPExcel->getActiveSheet()->getStyle("B2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$PHPExcel->getActiveSheet()->setCellValue("C2", "顧客Email");
$PHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(30);
$PHPExcel->getActiveSheet()->getStyle("C2")->getFont()->setName("標楷體");
$PHPExcel->getActiveSheet()->getStyle("C2")->getFont()->setSize("10");
$PHPExcel->getActiveSheet()->getStyle("C2")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
$PHPExcel->getActiveSheet()->getStyle("C2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$PHPExcel->getActiveSheet()->setCellValue("D2", "顧客電話");
$PHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(25);
$PHPExcel->getActiveSheet()->getStyle("D2")->getFont()->setName("標楷體");
$PHPExcel->getActiveSheet()->getStyle("D2")->getFont()->setSize("10");
$PHPExcel->getActiveSheet()->getStyle("D2")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
$PHPExcel->getActiveSheet()->getStyle("D2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$PHPExcel->getActiveSheet()->setCellValue("E2", "顧客來源");
$PHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(20);
$PHPExcel->getActiveSheet()->getStyle("E2")->getFont()->setName("標楷體");
$PHPExcel->getActiveSheet()->getStyle("E2")->getFont()->setSize("10");
$PHPExcel->getActiveSheet()->getStyle("E2")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
$PHPExcel->getActiveSheet()->getStyle("E2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$PHPExcel->getActiveSheet()->setCellValue("F2", "加入時間");
$PHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(30);
$PHPExcel->getActiveSheet()->getStyle("F2")->getFont()->setName("標楷體");
$PHPExcel->getActiveSheet()->getStyle("F2")->getFont()->setSize("10");
$PHPExcel->getActiveSheet()->getStyle("F2")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
$PHPExcel->getActiveSheet()->getStyle("F2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//設定儲存格內容
$rowPotion = 3;
foreach ($initAry as $rsAry) {
    $PHPExcel->getActiveSheet()->setCellValue("A" . $rowPotion, $rsAry[1]);
    $PHPExcel->getActiveSheet()->getStyle("A" . $rowPotion)->getFont()->setName("標楷體");
    $PHPExcel->getActiveSheet()->getStyle("A" . $rowPotion)->getFont()->setSize("10");
    $PHPExcel->getActiveSheet()->getStyle("A" . $rowPotion)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $PHPExcel->getActiveSheet()->setCellValue("B" . $rowPotion, $rsAry[2]);
    $PHPExcel->getActiveSheet()->getStyle("B" . $rowPotion)->getFont()->setName("標楷體");
    $PHPExcel->getActiveSheet()->getStyle("B" . $rowPotion)->getFont()->setSize("10");
    $PHPExcel->getActiveSheet()->getStyle("B" . $rowPotion)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $PHPExcel->getActiveSheet()->setCellValue("C" . $rowPotion, $rsAry[3]);
    $PHPExcel->getActiveSheet()->getStyle("C" . $rowPotion)->getFont()->setName("標楷體");
    $PHPExcel->getActiveSheet()->getStyle("C" . $rowPotion)->getFont()->setSize("10");
    $PHPExcel->getActiveSheet()->getStyle("C" . $rowPotion)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $PHPExcel->getActiveSheet()->setCellValue("D" . $rowPotion, $rsAry[4]);
    $PHPExcel->getActiveSheet()->getStyle("D" . $rowPotion)->getFont()->setName("標楷體");
    $PHPExcel->getActiveSheet()->getStyle("D" . $rowPotion)->getFont()->setSize("10");
    $PHPExcel->getActiveSheet()->getStyle("D" . $rowPotion)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $PHPExcel->getActiveSheet()->setCellValue("E" . $rowPotion, $rsAry[5]);
    $PHPExcel->getActiveSheet()->getStyle("E" . $rowPotion)->getFont()->setName("標楷體");
    $PHPExcel->getActiveSheet()->getStyle("E" . $rowPotion)->getFont()->setSize("10");
    $PHPExcel->getActiveSheet()->getStyle("E" . $rowPotion)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $PHPExcel->getActiveSheet()->setCellValue("F" . $rowPotion, $rsAry[6]);
    $PHPExcel->getActiveSheet()->getStyle("F" . $rowPotion)->getFont()->setName("標楷體");
    $PHPExcel->getActiveSheet()->getStyle("F" . $rowPotion)->getFont()->setSize("10");
    $PHPExcel->getActiveSheet()->getStyle("F" . $rowPotion)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $rowPotion++;
}
//Excel 2003
ob_end_clean();
$filename = iconv("UTF-8", "BIG5", "顧客匯出紀錄表 - " . $excuteDateTime . ".xls");
header("Content-Type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=" . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5');
$objWriter->save('php://output');
exit();
?>
