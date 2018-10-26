<?php

class BackEndErrorLogForWork {

    private $database;
    private $program_name;
    private $datetime;

    function __construct($database = "", $program_name = "") {
        $this->database = $database;
        $this->program_name = $program_name;
        $this->datetime = date("Y-m-d H:i:s");
    }

    function __destruct() {
        
    }

    //參數1: 網站根目錄下的完整資料夾或檔案路徑//存在回傳true, 失敗回傳false
    public function InsertErrorLogToDatabase($c_id, $error_string) {
        $sql = " Insert Into backend_error_log (c_id, bel_error_string, entry_datetime) ";
        $sql .= " Values ";
        $sql .= " (?, ?, ?) ";
        $this->database->createPreSTMT($sql, "sss", array($c_id, $error_string, $this->datetime));
    }

}
