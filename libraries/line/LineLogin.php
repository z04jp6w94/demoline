<?php

class LineLogin {
    /*
     */

    private $_databaseProcessor = null;
    private $_DateTime;

    function __construct($databaseProcessor) {
        $this->_databaseProcessor = $databaseProcessor;
        $this->_DateTime = date("Y-m-d H:i:s");
    }

    public function _INTO_MEMBER_TAG($datakey, $c_id, $user_id, $source) {
        $datetime = $this->_DateTime;
        /* 貼文次數 */
        $sql = " INSERT INTO member_push_click (mlm_lineid, c_id, datakey, source, mpc_count) VALUES (?, ?, ?, ?, '1') ";
        $sql .= " ON DUPLICATE KEY UPDATE mpc_count = mpc_count+1 ";
        $this->_databaseProcessor->createPreSTMT($sql, "ssss", array($user_id, $c_id, $datakey, $source));

        /* TAG */
        if ($source == "1") {
            $sql = " select ct_id from push_m ";
            $sql .= " where p_id = ? ";
        } else if ($source == "2") {
            $sql = " select ct_id from line_richmenu_content_m ";
            $sql .= " where lrcm_id = ? ";
        }
        $ct_id = $this->_databaseProcessor->readValuePreSTMT($sql, "s", array($datakey));
        $ct_str = explode(",", $ct_id);

        /* 標籤次數 */
        if ($ct_id != "") {
            $sql = " INSERT INTO member_list_t (mlm_lineid, c_id, datakey, source, ct_id, entry_datetime) VALUES ";
            for ($i = 1; $i <= count($ct_str); $i++) {
                $position = $i - 1;
                if ($i < count($ct_str)) {
                    $sql .= " ('" . $user_id . "', '" . $c_id . "', '" . $datakey . "', '" . $source . "', '" . $ct_str[$position] . "', '" . $datetime . "'), ";
                } else {
                    $sql .= " ('" . $user_id . "', '" . $c_id . "', '" . $datakey . "', '" . $source . "', '" . $ct_str[$position] . "', '" . $datetime . "'); ";
                }
            }
            $this->_databaseProcessor->createSTMT($sql);
        }
        /* 標籤 */
        for ($i = 0; $i < count($ct_str); $i++) {
            $sql = " INSERT INTO member_tag (mlm_lineid, c_id, ct_id) ";
            $sql .= " SELECT * FROM (SELECT '" . $user_id . "' as '1', '" . $c_id . "' as '2', '" . $ct_str[$i] . "' as '3') AS tmp ";
            $sql .= " WHERE NOT EXISTS ( ";
            $sql .= " SELECT mlm_lineid FROM member_tag WHERE mlm_lineid = ? and c_id = ? and ct_id = ? ";
            $sql .= " ) LIMIT 1 ";
            $this->_databaseProcessor->createPreSTMT($sql, "sss", array($user_id, $c_id, $ct_str[$i]));
        }
    }

}
