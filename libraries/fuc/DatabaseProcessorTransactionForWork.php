<?php

class DatabaseProcessorTransactionForWork {

    private $_hostForWrite = HOSTWRITE;
    private $_user = USER;
    private $_password = PASSWORD;
    private $_database = DATEBASE;
    private $_port = PORT;
    private $_mysqliForWrite = null;

    function __construct() {
        
    }

    function __destruct() {
        if ($this->_mysqliForWrite != null) {
            $this->_mysqliForWrite->close();
        }
    }

    private function getLinkForWrite() {
        return $this->_mysqliForWrite;
    }

    private function connectForWrite() {
        $this->_mysqliForWrite = new mysqli($this->_hostForWrite, $this->_user, $this->_password, $this->_database, $this->_port);
        if ($this->_mysqliForWrite->connect_error) {
            return false;
        }
        return true;
    }

    public function connectClose() {
        if ($this->_mysqliForWrite != null) {
            $this->_mysqliForWrite->close();
        }
    }

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

    public function readValuePreSTMT($sql, $bindpara_type, $bindpara_value_array) {
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
        $result = $stmt->get_result();
        $row = $result->fetch_array(MYSQLI_NUM);
        $rs = isset($row) == true ? $row[0] : "";
        $stmt->close();

        return $rs;
    }

    public function readArrayPreSTMT($sql, $bindpara_type, $bindpara_value_array, $fieldCount) {
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

    public function readValueSTMT($sql) {
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
        $result = $stmt->get_result();
        $row = $result->fetch_array(MYSQLI_NUM);
        $rs = isset($row) == true ? $row[0] : "";
        $stmt->close();

        return $rs;
    }

    public function readArraySTMT($sql, $fieldCount) {
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

    public function readDBFieldArraySTMT($sql) {
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
        $result = $stmt->get_result();

        $rs = $result->fetch_fields();
        $rs = isset($rs) == true ? $rs : array();
        $stmt->close();

        return $rs;
    }

    public function openAutoCommit() {
        if ($this->_mysqliForWrite == null) {
            $this->connectForWrite();
        }
        $this->_mysqliForWrite->autocommit(TRUE);
    }

    public function closeAutoCommit() {
        if ($this->_mysqliForWrite == null) {
            $this->connectForWrite();
        }
        $this->_mysqliForWrite->autocommit(FALSE);
    }

    public function rollback() {
        if ($this->_mysqliForWrite == null) {
            $this->connectForWrite();
        }
        $this->_mysqliForWrite->rollback();
    }

    public function commit() {
        if ($this->_mysqliForWrite == null) {
            $this->connectForWrite();
        }
        $this->_mysqliForWrite->commit();
    }

    public function transactionBegin() {
        if ($this->_mysqliForWrite == null) {
            $this->connectForWrite();
        }
        $this->_mysqliForWrite->begin_transaction();
    }

    public function transactionRollback() {
        if ($this->_mysqliForWrite == null) {
            $this->connectForWrite();
        }
        $this->_mysqliForWrite->rollback();
    }

    public function transactionCommit() {
        if ($this->_mysqliForWrite == null) {
            $this->connectForWrite();
        }
        $this->_mysqliForWrite->commit();
    }

}
