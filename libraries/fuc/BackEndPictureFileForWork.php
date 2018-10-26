<?php

class BackEndPictureFileForWork {

    private $oldfileName;
    private $newfileName;
    private $tempFilePath;
    private $serverFilePath;
    private $ThumbnailSize;

    function __construct($oldfileName = "", $newfileName = "", $tempFilePath = "", $serverFilePath = "", $ThumbnailSize = "") {
        $this->oldfileName = $oldfileName;
        $this->newfileName = $newfileName;
        $this->tempFilePath = $tempFilePath;
        $this->serverFilePath = $serverFilePath;
        $this->ThumbnailSize = $ThumbnailSize;
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
        return strtolower($fileNameExtension);
    }

    public function archiveWithoutReSizePictureFile() {
        $this->createFolder($this->serverFilePath);
        //取得副檔名
        $fileNameExtension = $this->getFileNameExtension($this->oldfileName); //jpg png gif
        //將原始圖片複製到指定資料夾
        copy($this->tempFilePath, $_SERVER['DOCUMENT_ROOT'] . "/" . $this->serverFilePath . "/original" . $this->newfileName . "." . $fileNameExtension);
        return "/" . $this->serverFilePath . "/original" . $this->newfileName . "." . $fileNameExtension;
    }

    public function archiveWithReSizePictureFile() {
        $this->createFolder($this->serverFilePath);
        //取得副檔名
        $fileNameExtension = $this->getFileNameExtension($this->oldfileName); //jpg png gif
        //取得原始圖片
        if ($fileNameExtension === "jpg" || $fileNameExtension === "jpeg") {
            $originalImageSrc = imagecreatefromjpeg($this->tempFilePath);
        }
        if ($fileNameExtension === "png") {
            $originalImageSrc = imagecreatefrompng($this->tempFilePath);
        }
        if ($fileNameExtension === "gif") {
            $originalImageSrc = imagecreatefromgif($this->tempFilePath);
        }
        $originalImageSrcW = imagesx($originalImageSrc);
        $originalImageSrcH = imagesy($originalImageSrc);
        if ($originalImageSrcW > $this->ThumbnailSize || $originalImageSrcH > $this->ThumbnailSize) {
            if ($originalImageSrcW > $originalImageSrcH) {
                $thumbnailImageSrcW = $this->ThumbnailSize;
                $thumbnailImageSrcH = intval($this->ThumbnailSize / $originalImageSrcW * $originalImageSrcH);
            } else {
                $thumbnailImageSrcH = $this->ThumbnailSize;
                $thumbnailImageSrcW = intval($this->ThumbnailSize / $originalImageSrcH * $originalImageSrcW);
            }
        } else {
            $thumbnailImageSrcW = $originalImageSrcW;
            $thumbnailImageSrcH = $originalImageSrcH;
        }
        //建立縮小圖片
        $thumbnailImageSrc = imagecreatetruecolor($thumbnailImageSrcW, $thumbnailImageSrcH);
        //開始縮小圖片
        imagecopyresampled($thumbnailImageSrc, $originalImageSrc, 0, 0, 0, 0, $thumbnailImageSrcW, $thumbnailImageSrcH, $originalImageSrcW, $originalImageSrcH);
        //將縮小圖片儲存到指定資料夾
        imagejpeg($thumbnailImageSrc, $_SERVER['DOCUMENT_ROOT'] . "/" . $this->serverFilePath . "/thumbnail" . $this->newfileName . "." . $fileNameExtension);
        return "/" . $this->serverFilePath . "/thumbnail" . $this->newfileName . "." . $fileNameExtension;
    }

    public function archiveReSizePictureFileTo1040() {
        $this->createFolder($this->serverFilePath);
        //取得副檔名
        $fileNameExtension = $this->getFileNameExtension($this->oldfileName); //jpg png gif
        //取得原始圖片
        if ($fileNameExtension === "jpg") {
            $originalImageSrc = imagecreatefromjpeg($this->tempFilePath);
        }
        if ($fileNameExtension === "png") {
            $originalImageSrc = imagecreatefrompng($this->tempFilePath);
        }
        if ($fileNameExtension === "gif") {
            $originalImageSrc = imagecreatefromgif($this->tempFilePath);
        }
        $originalImageSrcW = imagesx($originalImageSrc);
        $originalImageSrcH = imagesy($originalImageSrc);
        /* Line ImageMap */
        $thumbAry = array(240, 300, 460, 700, 1040);
        for ($i = 0; $i < count($thumbAry); $i++) {
            $thumbnailImageSrcW = $thumbAry[$i];
            $thumbnailImageSrcH = $thumbAry[$i];
            //建立縮小圖片
            $thumbnailImageSrc = imagecreatetruecolor($thumbnailImageSrcW, $thumbnailImageSrcH);
            //開始縮小圖片
            imagecopyresampled($thumbnailImageSrc, $originalImageSrc, 0, 0, 0, 0, $thumbnailImageSrcW, $thumbnailImageSrcH, $originalImageSrcW, $originalImageSrcH);
            //將縮小圖片儲存到指定資料夾
            imagejpeg($thumbnailImageSrc, $_SERVER['DOCUMENT_ROOT'] . "/" . $this->serverFilePath . "/" . $thumbAry[$i]);
            //釋放資源
            imagedestroy($thumbnailImageSrc);
        }

        return "/" . $this->serverFilePath . "/1040";
    }

}
