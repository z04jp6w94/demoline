<?php

class BackEndFileForWork {

    private $oldfileName;
    private $newfileName;
    private $tempFilePath;
    private $serverFilePath;

    function __construct($oldfileName, $newfileName, $tempFilePath, $serverFilePath) {
        $this->oldfileName = $oldfileName;
        $this->newfileName = $newfileName;
        $this->tempFilePath = $tempFilePath;
        $this->serverFilePath = $serverFilePath;
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

    public function archive() {
        $this->createFolder($this->serverFilePath);
        //取得副檔名
        $fileNameExtension = $this->getFileNameExtension($this->oldfileName); //所有檔案
        //將原始圖片複製到指定資料夾
        copy($this->tempFilePath, $_SERVER['DOCUMENT_ROOT'] . "/" . $this->serverFilePath . "/" . $this->newfileName . "." . $fileNameExtension);
        return "/" . $this->serverFilePath . "/" . $this->newfileName . "." . $fileNameExtension;
    }

}

?>