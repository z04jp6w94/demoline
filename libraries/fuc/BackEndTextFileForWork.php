<?php

class BackEndTextFileForWork {

    function __construct() {
        
    }

    function __destruct() {
        
    }

    //參數1: 網站根目錄下的完整資料夾或檔案路徑//存在回傳true, 失敗回傳false
    public function fileExist($folderfilePath) {
        $folderfilePath = $_SERVER['DOCUMENT_ROOT'] . "/" . $folderfilePath;
        if (file_exists($folderfilePath)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    //參數1: 網站根目錄下的完整資料夾路徑//成功回傳true, 失敗回傳false
    public function createFolder($folderPath) {
        $folderPath = $_SERVER['DOCUMENT_ROOT'] . "/" . $folderPath;
        if (file_exists($folderPath)) {
            return FALSE;
        } else {
            mkdir($folderPath);
            return TRUE;
        }
    }

    //參數1: 網站根目錄下的完整資料夾路徑//成功回傳true, 失敗回傳false
    public function deleteFolder($folderPath) {
        $folderPath = $_SERVER['DOCUMENT_ROOT'] . "/" . $folderPath;
        if (file_exists($folderPath)) {
            $files = glob($folderPath . "/*");
            foreach ($files as $file) {
                unlink($file);
            }
            rmdir($folderPath);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    //參數1: 網站根目錄下的完整檔案路徑//成功回傳true, 失敗回傳false
    public function createFile($filePath) {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . "/" . $filePath;
        if (file_exists($filePath)) {
            return FALSE;
        } else {
            touch($filePath);
            return TRUE;
        }
    }

    //參數1: 網站根目錄下的完整檔案路徑//成功回傳true, 失敗回傳false
    public function deleteFile($filePath) {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . "/" . $filePath;
        if (file_exists($filePath)) {
            unlink($filePath);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    //參數1: 網站根目錄下的完整檔案路徑//回傳檔名
    public function getFileName($filePath) {
        $pathAry = explode('/', $filePath);
        $fileName = $pathAry[count($pathAry) - 1];
        return $fileName;
    }

    //參數1: 網站根目錄下的完整檔案路徑//回傳副檔名
    public function getFileNameExtension($filePath) {
        $pathAry = explode('/', $filePath);
        $fileName = $pathAry[count($pathAry) - 1];
        $fileNameExtension = explode('.', $fileName)[1];
        return $fileNameExtension;
    }

    //參數1: 網站根目錄下的完整檔案路徑//成功回傳讀出內容, 失敗回傳錯誤訊息
    public function readFile($filePath) {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . "/" . $filePath;
        if (!$handle = fopen($filePath, 'r')) {
            return "readFile fopen fail";
        }
        if (filesize($filePath) == 0) {
            fclose($handle);
            return "readFile filesize is zero";
        }
        if (!$contents = fread($handle, filesize($filePath))) {
            fclose($handle);
            return "readFile fread fail";
        }
        fclose($handle);
        return $contents;
    }

    //參數1: 網站根目錄下的完整檔案路徑, 參數2: 寫入內容//成功回傳寫入字數, 失敗回傳錯誤訊息
    public function writeFile($filePath, $content) {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . "/" . $filePath;
        if (!$handle = fopen($filePath, 'a')) {
            return "writeFile fopen fail";
        }
        if (!$counts = fwrite($handle, $content)) {
            fclose($handle);
            return "writeFile fwrite fail";
        }
        fclose($handle);
        return $counts;
    }

}
