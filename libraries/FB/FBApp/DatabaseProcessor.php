<?php

namespace FB\FBApp;

class DatabaseProcessor {

    private $_dataBase;
    private $_Date;
    private $_Time;
    private $_DateTime;

    function __construct($database) {
        $this->_dataBase = $database;
        $this->_Date = date("Ymd");
        $this->_Time = date("His");
        $this->_DateTime = date("Y-m-d H:i:s");
    }

    function InsertMemberFBIDInfo($user, $name) {
        $member_id = "FB" . $user;
        $name = removeEmoji($name);
        $datetime = $this->_DateTime;
        $sql = " INSERT INTO member_list_m ";
        $sql .= " ( mlm_id, mlm_source, mlm_dsp, mlm_name, mlm_email, ";
        $sql .= " mlm_phone, mlm_remark, mlm_lineid, mlm_share_lineid, mlm_share_status, ";
        $sql .= " mlm_line_follow_status, mlm_fbid, mlm_messengerid, entry_date ) ";
        $sql .= " SELECT * FROM (SELECT ? as 'mlm_id', '2' as 'mlm_source', 'N' as 'mlm_dsp', ? as 'mlm_name', '' as 'mlm_email', ";
        $sql .= " '' as 'mlm_phone', '' as 'mlm_remark', '' as 'mlm_lineid', '' as 'mlm_share_lineid', '' as 'mlm_share_status', ";
        $sql .= " '' as 'mlm_line_follow_status', ? as 'mlm_fbid', '' as 'mlm_messengerid', ? as 'entry_date' ) AS tmp ";
        $sql .= " WHERE NOT EXISTS ( ";
        $sql .= " SELECT mlm_id FROM member_list_m WHERE mlm_fbid = ? ";
        $sql .= " ) LIMIT 1 ";
        return $this->_dataBase->createPreSTMT($sql, "sssss", array($member_id, $name, $user, $datetime, $user));
    }

    function InsertMemberFBMessengerIDInfo($user, $name) {
        $member_id = "FB" . $user;
        $name = removeEmoji($name);
        $datetime = $this->_DateTime;
        $sql = " INSERT INTO member_list_m ";
        $sql .= " ( mlm_id, mlm_source, mlm_dsp, mlm_name, mlm_email, ";
        $sql .= " mlm_phone, mlm_remark, mlm_lineid, mlm_share_lineid, mlm_share_status, ";
        $sql .= " mlm_line_follow_status, mlm_fbid, mlm_messengerid, entry_date ) ";
        $sql .= " SELECT * FROM (SELECT ? as 'mlm_id', '2' as 'mlm_source', 'N' as 'mlm_dsp', ? as 'mlm_name', '' as 'mlm_email', ";
        $sql .= " '' as 'mlm_phone', '' as 'mlm_remark', '' as 'mlm_lineid', '' as 'mlm_share_lineid', '' as 'mlm_share_status', ";
        $sql .= " '' as 'mlm_line_follow_status', '' as 'mlm_fbid', ? as 'mlm_messengerid', ? as 'entry_date' ) AS tmp ";
        $sql .= " WHERE NOT EXISTS ( ";
        $sql .= " SELECT mlm_id FROM member_list_m WHERE mlm_messengerid = ? ";
        $sql .= " ) LIMIT 1 ";
        return $this->_dataBase->createPreSTMT($sql, "sssss", array($member_id, $name, $user, $datetime, $user));
    }

    function InsertMemberFBMessengerIDInfoFromShare($user, $name, $share_id) {
        $member_id = "FB" . $user;
        $name = removeEmoji($name);
        $datetime = $this->_DateTime;
        $sql = " INSERT INTO member_list_m ";
        $sql .= " ( mlm_id, mlm_source, mlm_dsp, mlm_name, mlm_email, ";
        $sql .= " mlm_phone, mlm_remark, mlm_lineid, mlm_share_lineid, mlm_share_status, ";
        $sql .= " mlm_line_follow_status, mlm_fbid, mlm_messengerid, entry_date ) ";
        $sql .= " SELECT * FROM (SELECT ? as 'mlm_id', '2' as 'mlm_source', 'N' as 'mlm_dsp', ? as 'mlm_name', '' as 'mlm_email', ";
        $sql .= " '' as 'mlm_phone', '' as 'mlm_remark', '' as 'mlm_lineid', ? as 'mlm_share_lineid', '' as 'mlm_share_status', ";
        $sql .= " '' as 'mlm_line_follow_status', '' as 'mlm_fbid', ? as 'mlm_messengerid', ? as 'entry_date' ) AS tmp ";
        $sql .= " WHERE NOT EXISTS ( ";
        $sql .= " SELECT mlm_id FROM member_list_m WHERE mlm_messengerid = ? ";
        $sql .= " ) LIMIT 1 ";
        return $this->_dataBase->createPreSTMT($sql, "ssssss", array($member_id, $name, $share_id, $user, $datetime, $user));
    }

    function InsertFBAskQuestion($user, $problem) {
        $member_id = "FB" . $user;
        $datetime = $this->_DateTime;
        $sql = " INSERT INTO feedback (mlm_id, f_problem, f_status, entry_datetime) ";
        $sql .= " VALUES (?,?,'1',?)";
        $this->_dataBase->createPreSTMT($sql, "sss", array($member_id, $problem, $datetime));
    }

}
