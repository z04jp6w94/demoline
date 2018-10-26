<?php

function getYoutubeId($youtubeUrl) {
    switch (true) {
        case iconv_substr($youtubeUrl, 0, 15, "utf-8") == "http://youtu.be":
            $youtubeID = iconv_substr($youtubeUrl, 16, 1, "utf-8");
            return $youtubeID;
        case iconv_substr($youtubeUrl, 0, 16, 'utf-8') == "https://youtu.be":
            $youtubeID = iconv_substr($youtubeUrl, 17, 11, "utf-8");
            return $youtubeID;
        case iconv_substr($youtubeUrl, 0, 22, "utf-8") == "http://www.youtube.com":
            $youtubeID = iconv_substr($youtubeUrl, 31, 11, "utf-8");
            return $youtubeID;
        case iconv_substr($youtubeUrl, 0, 23, "utf-8") == "https://www.youtube.com":
            $youtubeID = iconv_substr($youtubeUrl, 32, 11, "utf-8");
            return $youtubeID;
        case iconv_substr($youtubeUrl, 0, 7, "utf-8") == "<iframe":
            $position1 = strpos($youtubeUrl, 'src="'); //先取得src="位置
            $position2 = strpos(iconv_substr($youtubeUrl, $position1 + 5, strlen($youtubeUrl), "utf-8"), "/embed/"); //再取得/embed/位置
            $position3 = strpos(iconv_substr($youtubeUrl, $position1 + $position2 + 5 + 7, strlen($youtubeUrl), "utf-8"), '"'); //最後取得"位置
            $youtubeID = iconv_substr($youtubeUrl, $position1 + $position2 + 5 + 7, $position3, "utf-8"); //取得/embed/和"之間的youtubeID
            $youtubeID = str_replace("?rel=0", "", $youtubeID); //特殊處理
            return $youtubeID;
        default:
            return "";
    }
}

?>