<?php

class DatabaseProcessorForWork {

    private $_hostForRead = HOSTREAD;
    private $_hostForWrite = HOSTWRITE;
    private $_user = USER;
    private $_password = PASSWORD;
    private $_database = DATEBASE;
    private $_port = PORT;
    private $_mysqliForRead = null;
    private $_mysqliForWrite = null;

    function __construct() {
        
    }

    function __destruct() {
        if ($this->_mysqliForRead != null) {
            $this->_mysqliForRead->close();
        }
        if ($this->_mysqliForWrite != null) {
            $this->_mysqliForWrite->close();
        }
    }

    private function getLinkForRead() {
        return $this->_mysqliForRead;
    }

    private function getLinkForWrite() {
        return $this->_mysqliForWrite;
    }

    private function connectForRead() {
        $this->_mysqliForRead = new mysqli($this->_hostForRead, $this->_user, $this->_password, $this->_database, $this->_port);
        if ($this->_mysqliForRead->connect_error) {
            return false;
        }
        return true;
    }

    private function connectForWrite() {
        $this->_mysqliForWrite = new mysqli($this->_hostForWrite, $this->_user, $this->_password, $this->_database, $this->_port);
        if ($this->_mysqliForWrite->connect_error) {
            return false;
        }
        return true;
    }

    public function connectClose() {
        if ($this->_mysqliForRead != null) {
            $this->_mysqliForRead->close();
        }
        if ($this->_mysqliForWrite != null) {
            $this->_mysqliForWrite->close();
        }
    }

    //資料庫四大指令PreSTMT
    //Create => 傳入參數1: SQL語法, 傳入參數2: 參數型別, 傳入參數3: 參數值陣列
    //Create => 成功回傳true，失敗回傳false
    public function createPreSTMT($sql, $bindpara_type, $bindpara_value_array) {
        if ($this->_mysqliForWrite == null) {
            $this->connectForWrite();
        }
        if (!($stmt = $this->_mysqliForWrite->prepare($sql))) {
            error_log("Prepare failed: ({$this->_mysqliForWrite->errno}) {$this->_mysqliForWrite->error}");
            return false;
        }
        $bindpara_array = array();
        $bindpara_array[] = &$bindpara_type;
        for ($i = 0; $i < count($bindpara_value_array); $i++) {
            $bindpara_array[] = &$bindpara_value_array[$i];
        }
        call_user_func_array(array($stmt, 'bind_param'), $bindpara_array); //使用callback func
        if (!$stmt->execute()) {
            error_log("Execute failed: ({$this->_mysqliForWrite->errno}) {$this->_mysqliForWrite->error}");
            return false;
        }
        $row = $stmt->affected_rows;
        $rs = $row > 0 ? true : false;
        $stmt->close();

        return $rs;
    }

    //ReadValue => 傳入參數1: SQL語法, 傳入參數2: 參數型別, 傳入參數3: 參數值陣列
    //ReadValue => 有值回傳查詢結果值，無值回傳no value
    public function readValuePreSTMT($sql, $bindpara_type, $bindpara_value_array) {
        if ($this->_mysqliForRead == null) {
            $this->connectForRead();
        }
        if (!($stmt = $this->_mysqliForRead->prepare($sql))) {
            error_log("Prepare failed: ({$this->_mysqliForRead->errno}) {$this->_mysqliForRead->error}");
            return false;
        }
        $bindpara_array = array();
        $bindpara_array[] = &$bindpara_type;
        for ($i = 0; $i < count($bindpara_value_array); $i++) {
            $bindpara_array[] = &$bindpara_value_array[$i];
        }
        call_user_func_array(array($stmt, 'bind_param'), $bindpara_array); //使用callback func
        if (!$stmt->execute()) {
            error_log("Execute failed: ({$this->_mysqliForRead->errno}) {$this->_mysqliForRead->error}");
            return false;
        }
        $result = $stmt->get_result();
        $row = $result->fetch_array(MYSQLI_NUM);
        $rs = isset($row) == true ? $row[0] : "";
        $stmt->close();

        return $rs;
    }

    //ReadArray => 傳入參數1: SQL語法, 傳入參數2: 參數型別, 傳入參數3: 參數值陣列, 傳入參數4: 查詢資料表欄位數量
    //ReadArray => 有值回傳查詢結果陣列，無值回傳no array
    public function readArrayPreSTMT($sql, $bindpara_type, $bindpara_value_array, $fieldCount) {
        if ($this->_mysqliForRead == null) {
            $this->connectForRead();
        }
        if (!($stmt = $this->_mysqliForRead->prepare($sql))) {
            error_log("Prepare failed: ({$this->_mysqliForRead->errno}) {$this->_mysqliForRead->error}");
            return false;
        }
        $bindpara_array = array();
        $bindpara_array[] = &$bindpara_type;
        for ($i = 0; $i < count($bindpara_value_array); $i++) {
            $bindpara_array[] = &$bindpara_value_array[$i];
        }
        call_user_func_array(array($stmt, 'bind_param'), $bindpara_array); //使用callback func
        if (!$stmt->execute()) {
            error_log("Execute failed: ({$this->_mysqliForRead->errno}) {$this->_mysqliForRead->error}");
            return false;
        }
        $result = $stmt->get_result();

        $recordCount = 0; //紀錄查詢結果數量
        while ($ary = $result->fetch_array(MYSQLI_NUM)) {
            if ($fieldCount > 1) { //若是查詢資料表欄位數量大於1, 回傳二維陣列
                for ($i = 0; $i < $fieldCount; $i++) {
                    $rs[$recordCount][$i] = $ary[$i];
                }
            } else { //若是查詢資料表欄位數量不大於1, 回傳一維陣列
                $rs[$recordCount][0] = $ary[0];
            }
            $recordCount ++;
        }
        $rs = isset($rs) == true ? $rs : array();
        $stmt->close();

        return $rs;
    }

    //Update => 傳入參數1: SQL語法, 傳入參數2: 參數型別, 傳入參數3: 參數值陣列
    //Update => 成功回傳true，失敗回傳false
    public function updatePreSTMT($sql, $bindpara_type, $bindpara_value_array) {
        if ($this->_mysqliForWrite == null) {
            $this->connectForWrite();
        }
        if (!($stmt = $this->_mysqliForWrite->prepare($sql))) {
            error_log("Prepare failed: ({$this->_mysqliForWrite->errno}) {$this->_mysqliForWrite->error}");
            return false;
        }
        $bindpara_array = array();
        $bindpara_array[] = &$bindpara_type;
        for ($i = 0; $i < count($bindpara_value_array); $i++) {
            $bindpara_array[] = &$bindpara_value_array[$i];
        }
        call_user_func_array(array($stmt, 'bind_param'), $bindpara_array); //使用callback func
        if (!$stmt->execute()) {
            error_log("Execute failed: ({$this->_mysqliForWrite->errno}) {$this->_mysqliForWrite->error}");
            return false;
        }
        $row = $stmt->affected_rows;
        $rs = $row > 0 ? true : false;
        $stmt->close();

        return $rs;
    }

    //Delete => 傳入參數1: SQL語法, 傳入參數2: 參數型別, 傳入參數3: 參數值陣列
    //Delete => 成功回傳true，失敗回傳false
    public function deletePreSTMT($sql, $bindpara_type, $bindpara_value_array) {
        if ($this->_mysqliForWrite == null) {
            $this->connectForWrite();
        }
        if (!($stmt = $this->_mysqliForWrite->prepare($sql))) {
            error_log("Prepare failed: ({$this->_mysqliForWrite->errno}) {$this->_mysqliForWrite->error}");
            return false;
        }
        $bindpara_array = array();
        $bindpara_array[] = &$bindpara_type;
        for ($i = 0; $i < count($bindpara_value_array); $i++) {
            $bindpara_array[] = &$bindpara_value_array[$i];
        }
        call_user_func_array(array($stmt, 'bind_param'), $bindpara_array); //使用callback func
        if (!$stmt->execute()) {
            error_log("Execute failed: ({$this->_mysqliForWrite->errno}) {$this->_mysqliForWrite->error}");
            return false;
        }
        $row = $stmt->affected_rows;
        $rs = $row > 0 ? true : false;
        $stmt->close();

        return $rs;
    }

    //資料庫四大指令STMT
    //Create => 傳入參數1: SQL語法
    //Create => 成功回傳true，失敗回傳false
    public function createSTMT($sql) {
        if ($this->_mysqliForWrite == null) {
            $this->connectForWrite();
        }
        if (!($stmt = $this->_mysqliForWrite->prepare($sql))) {
            error_log("Prepare failed: ({$this->_mysqliForWrite->errno}) {$this->_mysqliForWrite->error}");
            return false;
        }
        if (!$stmt->execute()) {
            error_log("Execute failed: ({$this->_mysqliForWrite->errno}) {$this->_mysqliForWrite->error}");
            return false;
        }
        $row = $stmt->affected_rows;
        $rs = $row > 0 ? true : false;
        $stmt->close();

        return $rs;
    }

    //ReadValue => 傳入參數1: SQL語法
    //ReadValue => 有值回傳查詢結果值，無值回傳no value
    public function readValueSTMT($sql) {
        if ($this->_mysqliForRead == null) {
            $this->connectForRead();
        }
        if (!($stmt = $this->_mysqliForRead->prepare($sql))) {
            error_log("Prepare failed: ({$this->_mysqliForRead->errno}) {$this->_mysqliForRead->error}");
            return false;
        }
        if (!$stmt->execute()) {
            error_log("Execute failed: ({$this->_mysqliForRead->errno}) {$this->_mysqliForRead->error}");
            return false;
        }
        $result = $stmt->get_result();
        $row = $result->fetch_array(MYSQLI_NUM);
        $rs = isset($row) == true ? $row[0] : "";
        $stmt->close();

        return $rs;
    }

    //ReadArray => 傳入參數1: SQL語法, 傳入參數2: 查詢資料表欄位數量
    //ReadArray => 有值回傳查詢結果陣列，無值回傳no array
    public function readArraySTMT($sql, $fieldCount) {
        if ($this->_mysqliForRead == null) {
            $this->connectForRead();
        }
        if (!($stmt = $this->_mysqliForRead->prepare($sql))) {
            error_log("Prepare failed: ({$this->_mysqliForRead->errno}) {$this->_mysqliForRead->error}");
            return false;
        }
        if (!$stmt->execute()) {
            error_log("Execute failed: ({$this->_mysqliForRead->errno}) {$this->_mysqliForRead->error}");
            return false;
        }
        $result = $stmt->get_result();

        $recordCount = 0; //紀錄查詢結果數量
        while ($ary = $result->fetch_array(MYSQLI_NUM)) {
            if ($fieldCount > 1) { //若是查詢資料表欄位數量大於1, 回傳二維陣列
                for ($i = 0; $i < $fieldCount; $i++) {
                    $rs[$recordCount][$i] = $ary[$i];
                }
            } else { //若是查詢資料表欄位數量不大於1, 回傳一維陣列
                $rs[$recordCount][0] = $ary[0];
            }
            $recordCount ++;
        }
        $rs = isset($rs) == true ? $rs : array();
        $stmt->close();

        return $rs;
    }

    //Update => 傳入參數1: SQL語法
    //Update => 成功回傳true，失敗回傳false
    public function updateSTMT($sql) {
        if ($this->_mysqliForWrite == null) {
            $this->connectForWrite();
        }
        if (!($stmt = $this->_mysqliForWrite->prepare($sql))) {
            error_log("Prepare failed: ({$this->_mysqliForWrite->errno}) {$this->_mysqliForWrite->error}");
            return false;
        }
        if (!$stmt->execute()) {
            error_log("Execute failed: ({$this->_mysqliForWrite->errno}) {$this->_mysqliForWrite->error}");
            return false;
        }
        $row = $stmt->affected_rows;
        $rs = $row > 0 ? true : false;
        $stmt->close();

        return $rs;
    }

    //Delete => 傳入參數1: SQL語法
    //Delete => 成功回傳true，失敗回傳false
    public function deleteSTMT($sql) {
        if ($this->_mysqliForWrite == null) {
            $this->connectForWrite();
        }
        if (!($stmt = $this->_mysqliForWrite->prepare($sql))) {
            error_log("Prepare failed: ({$this->_mysqliForWrite->errno}) {$this->_mysqliForWrite->error}");
            return false;
        }
        if (!$stmt->execute()) {
            error_log("Execute failed: ({$this->_mysqliForWrite->errno}) {$this->_mysqliForWrite->error}");
            return false;
        }
        $row = $stmt->affected_rows;
        $rs = $row > 0 ? true : false;
        $stmt->close();

        return $rs;
    }

    //資料庫四大指令PreSTMT
    //Create => 傳入參數1: SQL語法, 傳入參數2: 參數型別, 傳入參數3: 參數值陣列
    //Create => 成功回傳true，失敗回傳false
    public function createPreSTMT_CreateId($sql, $bindpara_type, $bindpara_value_array) {
        if ($this->_mysqliForWrite == null) {
            $this->connectForWrite();
        }
        if (!($stmt = $this->_mysqliForWrite->prepare($sql))) {
            error_log("Prepare failed: ({$this->_mysqliForWrite->errno}) {$this->_mysqliForWrite->error}");
            return false;
        }
        $bindpara_array = array();
        $bindpara_array[] = &$bindpara_type;
        for ($i = 0; $i < count($bindpara_value_array); $i++) {
            $bindpara_array[] = &$bindpara_value_array[$i];
        }
        call_user_func_array(array($stmt, 'bind_param'), $bindpara_array); //使用callback func
        if (!$stmt->execute()) {
            error_log("Execute failed: ({$this->_mysqliForWrite->errno}) {$this->_mysqliForWrite->error}");
            return false;
        }
        $rs = $stmt->insert_id;
        $stmt->close();

        return $rs;
    }

    //資料庫四大指令PreSTMT
    //ReadArray => 傳入參數1: SQL語法
    //ReadArray => 有值回傳查詢結果一維陣列，無值回傳一維空陣列
    public function readDBFieldArraySTMT($sql) {
        if ($this->_mysqliForRead == null) {
            $this->connectForRead();
        }
        if (!($stmt = $this->_mysqliForRead->prepare($sql))) {
            error_log("Prepare failed: ({$this->_mysqliForRead->errno}) {$this->_mysqliForRead->error}");
            return false;
        }
        if (!$stmt->execute()) {
            error_log("Execute failed: ({$this->_mysqliForRead->errno}) {$this->_mysqliForRead->error}");
            return false;
        }
        $result = $stmt->get_result();

        $rs = $result->fetch_fields();
        $rs = isset($rs) == true ? $rs : array();
        $stmt->close();

        return $rs;
    }

}
