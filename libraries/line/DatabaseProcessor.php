<?php

/**
 * Copyright 2017 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace SCRM\BOT;

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

    function GetFollowContent($c_id) {
        $sql = " SELECT ls_follow_content FROM line_setting ";
        $sql .= " WHERE c_id = ? ";
        $value = $this->_dataBase->readValuePreSTMT($sql, "s", array($c_id));
        return $value;
    }

    function GET_LOGIN_ID($c_id) {
        $sql = " SELECT c_linelogin_CID from crm_m ";
        $sql .= " WHERE c_id = ? ";
        $value = $this->_dataBase->readValuePreSTMT($sql, "s", array($c_id));
        return $value;
    }

    function GetMemberList($user, $c_id) {
        $sql = " SELECT COUNT(mlm_id) FROM member_list_m ";
        $sql .= " where mlm_lineid = ? and c_id = ? ";
        $value = $this->_dataBase->readValuePreSTMT($sql, "ss", array($user, $c_id));
        return $value;
    }

    function InsertMemberInfo($user, $name, $c_id) {
        $name = removeEmoji($name);
        $datetime = $this->_DateTime;
        $sql = " INSERT INTO member_list_m (c_id, mlm_source, mlm_dsp, mlm_name, mlm_email, ";
        $sql .= " mlm_phone, mlm_remark, mlm_lineid, ";
        $sql .= " mlm_line_follow_status, mlm_fbid, mlm_messengerid, entry_date )";
        $sql .= " SELECT * FROM (SELECT ? as 'c_id', '1' as 'mlm_source', 'N' as 'mlm_dsp', ? as 'mlm_name', '' as 'mlm_email', ";
        $sql .= " '' as 'mlm_phone', '' as 'mlm_remark', ? as 'mlm_lineid', ";
        $sql .= " 'Y' as 'mlm_line_follow_status', '' as 'mlm_fbid', '' as 'mlm_messengerid', ? as 'entry_date' ) AS tmp ";
        $sql .= " WHERE NOT EXISTS ( ";
        $sql .= " SELECT mlm_id FROM member_list_m WHERE c_id = ? and mlm_lineid = ? ";
        $sql .= " ) LIMIT 1 ";
        return $this->_dataBase->createPreSTMT($sql, "ssssss", array($c_id, $name, $user, $datetime, $c_id, $user));
    }

    function ShareActAry($key) {
        $sql = " SELECT sa_title, sa_standard_number FROM share_activity ";
        $sql .= " WHERE sa_id = ? ";
        $Ary = $this->_dataBase->readArrayPreSTMT($sql, "s", array($key), 2);
        return $Ary;
    }

    function InsertShareCourse($shareid, $be_share_id, $c_id, $sa_id) {
        $datetime = $this->_DateTime;
        $sql = " INSERT INTO share_course ( sa_id, c_id, share_lineid, be_share_lineid, entry_datetime ) ";
        $sql .= " SELECT * FROM (SELECT '" . $sa_id . "' as '1', '" . $c_id . "' as '2', '" . $shareid . "' as '3', '" . $be_share_id . "' as '4', '" . $datetime . "' as '5') AS tmp ";
        $sql .= " WHERE NOT EXISTS ( ";
        $sql .= " SELECT sc_id FROM share_course WHERE sa_id = ? and c_id = ? and share_lineid = ? and be_share_lineid = ?  ";
        $sql .= " ) LIMIT 1 ";
        $this->_dataBase->createPreSTMT($sql, "ssss", array($sa_id, $c_id, $shareid, $be_share_id));
    }

    function SER_Count($sa_id, $c_id, $share_id) {
        $sql = " SELECT COUNT(sa_id) FROM share_exchange_record ";
        $sql .= " WHERE sa_id = ? ";
        $sql .= " AND c_id = ? ";
        $sql .= " AND mlm_lineid = ? ";
        $value = $this->_dataBase->readValuePreSTMT($sql, "sss", array($sa_id, $c_id, $share_id));
        return $value;
    }

    function SC_Count($sa_id, $c_id, $share_id) {
        $sql = " SELECT COUNT(sc_id) FROM share_course ";
        $sql .= " WHERE sa_id = ? ";
        $sql .= " AND c_id = ? ";
        $sql .= " AND share_lineid = ? ";
        $value = $this->_dataBase->readValuePreSTMT($sql, "sss", array($sa_id, $c_id, $share_id));
        return $value;
    }

    function GetRandPW($c_id, $random_code) {
        $sql = " SELECT COUNT(sa_id) FROM share_exchange_record ";
        $sql .= " WHERE c_id = ? ";
        $sql .= " AND ser_random_code = ? ";
        $value = $this->_dataBase->readValuePreSTMT($sql, "ss", array($c_id, $random_code));
        return $value;
    }

    function InsertExchangeRecord($sa_id, $c_id, $share_id, $random_code) {
        $datetime = $this->_DateTime;
        $sql = " INSERT INTO share_exchange_record  ";
        $sql .= " ( sa_id, c_id, mlm_lineid, ser_apply_status, ser_random_code, ";
        $sql .= " ser_status, entry_datetime, apply_datetime ) ";
        $sql .= " VALUES ";
        $sql .= " ( ?, ?, ?, 'N', ?, ";
        $sql .= " 'N', ?, ? ) ";
        $this->_dataBase->createPreSTMT($sql, "ssssss", array($sa_id, $c_id, $share_id, $random_code, $datetime, $datetime));
    }

    function UpdateShareStatus($user, $c_id) {
        $sql = " Update member_list_m set mlm_share_status = '' where mlm_lineid = ? and c_id = ? ";
        $this->_dataBase->updatePreSTMT($sql, "ss", array($user, $c_id));
    }

    function AddFollowMember($user, $c_id, $name) {
        $name = removeEmoji($name);
        $datetime = $this->_DateTime;
        $sql = " INSERT INTO member_list_m ";
        $sql .= " (c_id, mlm_source, mlm_dsp, mlm_name, mlm_email, ";
        $sql .= " mlm_phone, mlm_remark, mlm_lineid, ";
        $sql .= " mlm_line_follow_status, mlm_fbid, mlm_messengerid, entry_date ) ";
        $sql .= " VALUES ";
        $sql .= " (?, '1', 'N', ?, '', ";
        $sql .= " '' ,'' ,? , ";
        $sql .= " 'Y', '', '', ? ) ";
        $this->_dataBase->createPreSTMT($sql, "ssss", array($c_id, $name, $user, $datetime));
    }

    function UpdateFollowStatus($user, $name, $c_id, $status) {
        if ($status == "Y") {
            $sql = " Update member_list_m set mlm_name = ?, mlm_line_follow_status = ? where mlm_lineid = ? and c_id = ? ";
            $bindpara_type = "ssss";
            $bindpara_value_array = array($name, $status, $user, $c_id);
        } else {
            $sql = " Update member_list_m set mlm_line_follow_status = ? where mlm_lineid = ? and c_id = ? ";
            $bindpara_type = "sss";
            $bindpara_value_array = array($status, $user, $c_id);
        }
        $this->_dataBase->createPreSTMT($sql, $bindpara_type, $bindpara_value_array);
    }

    function RichMenuId($c_id) {
        $sql = " SELECT richmenu_id FROM richmenu_set_m ";
        $sql .= " WHERE c_id = ? ";
        $sql .= " AND rsm_status = 'Y' ";
        $value = $this->_dataBase->readValuePreSTMT($sql, "s", array($c_id));
        return $value;
    }

    function GetCustomerRichMenuId($c_id) {
        $sql = " SELECT ls_customer_richmenu_id FROM line_setting ";
        $sql .= " WHERE c_id = ? ";
        $value = $this->_dataBase->readValuePreSTMT($sql, "s", array($c_id));
        return $value;
    }

    function InsertAccTemp($user, $c_id) {
        $sql = " INSERT INTO member_line_qa (mlq_id,c_id,mlq_lineid,mlq_a1,mlq_a2,mlq_a3,mlq_a4,mlq_a5) ";
        $sql .= " SELECT * FROM (SELECT '0' as '1','" . $c_id . "' as '2', '" . $user . "' as '3', '' as '4', '' as '5', '' as '6', '' as '7', '' as '8') AS tmp ";
        $sql .= " WHERE NOT EXISTS ( ";
        $sql .= " SELECT mlq_lineid FROM member_line_qa WHERE mlq_lineid = ? and c_id = ? ";
        $sql .= " ) LIMIT 1 ";
        $this->_dataBase->createPreSTMT($sql, "ss", array($user, $c_id));
    }

    function UpdateAccData($user, $c_id) {
        $sql = "UPDATE member_line_qa set mlq_a1 = '', mlq_a2 = '', mlq_a3 = '', mlq_a4 = '', mlq_a5 = '' ";
        $sql .= " WHERE mlq_lineid = ? and c_id = ? ";
        $bindpara_type = "ss";
        $bindpara_value_array = array($user, $c_id);
        $this->_dataBase->createPreSTMT($sql, $bindpara_type, $bindpara_value_array);
    }

    function WriteAns($user, $c_id, $text, $num) {
        $text = removeEmoji($text);
        $sql = "UPDATE member_line_qa SET mlq_a" . $num . " = ? WHERE mlq_lineid = ? and c_id = ? ";
        $this->_dataBase->createPreSTMT($sql, "sss", array($text, $user, $c_id));
    }

    function UpdateMember($user, $c_id) {
        $sql = " UPDATE member_list_m A ";
        $sql .= " inner join member_line_qa B ";
        $sql .= " on B.mlq_lineid = A.mlm_lineid and B.c_id = A.c_id";
        $sql .= " SET";
        $sql .= " A.mlm_email = B.mlq_a2, ";
        $sql .= " A.mlm_phone = B.mlq_a3, ";
        $sql .= " A.mlm_dsp = 'Y' ";
        $sql .= " WHERE B.mlq_lineid = ? AND B.c_id = ? ";
        $this->_dataBase->createPreSTMT($sql, "ss", array($user, $c_id));
    }

    function InsertAskQuestion($user, $c_id) {
        $datetime = $this->_DateTime;
        $sql = " INSERT INTO feedback (f_id,mlm_lineid,c_id,f_problem,f_status,entry_datetime) ";
        $sql .= " SELECT '0','" . $user . "','" . $c_id . "',mlq_a1,'1','" . $datetime . "' ";
        $sql .= " FROM member_line_qa where mlq_lineid = ? and c_id = ? ";
        $this->_dataBase->createPreSTMT($sql, "ss", array($user, $c_id));
    }

    function GetTempCount($user, $c_id) {
        $sql = " SELECT count(*) FROM member_line_qa ";
        $sql .= " where mlq_lineid = ? and c_id = ? ";
        $bindpara_type = "ss";
        $bindpara_value_array = array($user, $c_id);
        $value = $this->_dataBase->readValuePreSTMT($sql, $bindpara_type, $bindpara_value_array);
        return $value;
    }

    function GetRichMenuKeyWord($c_id) {
        $sql = " SELECT DISTINCT t.lrcm_keyword, l.lrcm_id, l.lrcm_type FROM richmenu_set_m m ";
        $sql .= " LEFT JOIN richmenu_set_t t on t.richmenu_id = m.richmenu_id ";
        $sql .= " LEFT JOIN line_richmenu_content_m l on l.lrcm_keyword = t.lrcm_keyword AND l.deletestatus = 'N' AND l.c_id = ? ";
        $sql .= " WHERE m.c_id = ? ";
        $sql .= " AND m.rsm_status = 'Y' ";
        $sql .= " AND t.rst_type = '1' ";
        $Ary = $this->_dataBase->readArrayPreSTMT($sql, "ss", array($c_id, $c_id), 3);
        return $Ary;
    }

    function KeyWordTextContent($key, $c_id) {
        $sql = " SELECT lrcm_type, lrcm_cdn_root, lrcm_title, lrcm_content ";
        $sql .= " FROM line_richmenu_content_m m  ";
        $sql .= " WHERE m.lrcm_id = ? ";
        $sql .= " AND m.c_id = ? ";
        $sql .= " AND m.deletestatus = 'N' ";
        $Ary = $this->_dataBase->readArrayPreSTMT($sql, "ss", array($key, $c_id), 4);
        return $Ary;
    }

    function KeyWordCarouselContent($key, $c_id) {
        $sql = " SELECT lrct_id, lrct_title, lrct_content, lrct_cdn_root, lrct_action_type, ";
        $sql .= " app_id, lrct_url ";
        $sql .= " FROM line_richmenu_content_t ";
        $sql .= " WHERE lrcm_id = ? ";
        $sql .= " AND c_id = ? ";
        $sql .= " AND deletestatus = 'N' ";
        $sql .= " ORDER BY lrct_sort ";
        $Ary = $this->_dataBase->readArrayPreSTMT($sql, "ss", array($key, $c_id), 7);
        return $Ary;
    }

    function KeyWordImgContent($key, $c_id) {
        $sql = " SELECT lrcm_type, lrcm_cdn_root, lrcm_title, lrcm_content, lrcm_action_type, ";
        $sql .= " app_id, lrcm_url ";
        $sql .= " FROM line_richmenu_content_m m  ";
        $sql .= " WHERE m.lrcm_id = ? ";
        $sql .= " AND m.c_id = ? ";
        $sql .= " AND m.deletestatus = 'N' ";
        $Ary = $this->_dataBase->readArrayPreSTMT($sql, "ss", array($key, $c_id), 7);
        return $Ary;
    }

    function KeyIdChangeKeyWord($key, $c_id) {
        $sql = " SELECT lrcm_keyword  FROM line_richmenu_content_m ";
        $sql .= " WHERE lrcm_id = ? ";
        $sql .= " AND c_id = ? ";
        $value = $this->_dataBase->readValuePreSTMT($sql, "ss", array($key, $c_id));
        return $value;
    }

    function GetKeyWordContent($text, $c_id) {
        $sql = " SELECT lrcm_id, lrcm_type ";
        $sql .= " FROM line_richmenu_content_m ";
        $sql .= " WHERE lrcm_keyword = ? ";
        $sql .= " AND c_id = ? ";
        $sql .= " AND deletestatus = 'N' ";
        $Ary = $this->_dataBase->readArrayPreSTMT($sql, "ss", array($text, $c_id), 2);
        return $Ary;
    }

    function GetTextWordContent($key, $c_id) {
        $sql = " SELECT lrcm_keyword, lrcm_type ";
        $sql .= " FROM line_richmenu_content_m ";
        $sql .= " WHERE lrcm_id = ? ";
        $sql .= " AND c_id = ? ";
        $sql .= " AND deletestatus = 'N' ";
        $Ary = $this->_dataBase->readArrayPreSTMT($sql, "ss", array($key, $c_id), 2);
        return $Ary;
    }

    // 2018/05/24 New PostBack
    function GetLineRichMenuContent($lrcm_id, $c_id) {
        $sql = " SELECT lrcm_keyword, lrcm_type, lrcm_cdn_root, lrcm_title, lrcm_content, ";
        $sql .= " lrcm_action_type, app_id, lrcm_url ";
        $sql .= " FROM line_richmenu_content_m ";
        $sql .= " WHERE lrcm_id = ? ";
        $sql .= " AND c_id = ? ";
        $sql .= " AND deletestatus = 'N' ";
        $Ary = $this->_dataBase->readArrayPreSTMT($sql, "ss", array($lrcm_id, $c_id), 8);
        return $Ary;
    }

    function GetLRCMCarouselContent($lrcm_id, $c_id) {
        $sql = " SELECT lrct_id, lrct_title, lrct_content, lrct_cdn_root, lrct_action_type, ";
        $sql .= " app_id, lrct_url ";
        $sql .= " FROM line_richmenu_content_t ";
        $sql .= " WHERE lrcm_id = ? ";
        $sql .= " AND c_id = ? ";
        $sql .= " AND deletestatus = 'N' ";
        $sql .= " ORDER BY lrct_sort ";
        $Ary = $this->_dataBase->readArrayPreSTMT($sql, "ss", array($lrcm_id, $c_id), 7);
        return $Ary;
    }

    // 2018/05/24 New PostBack

    function ShareActivity($c_id) {
        $sql = " SELECT sa_id, sa_title FROM share_activity ";
        $sql .= " WHERE c_id = ? ";
        $sql .= " AND deletestatus = 'N' ";
        $sql .= " AND sa_status = 'Y' ";
        $Ary = $this->_dataBase->readArrayPreSTMT($sql, "s", array($c_id), 2);
        return $Ary;
    }

    function ShareContentAry($key) {
        $sql = " SELECT sa_id, sa_title, sa_content, sa_awards_content, sa_cdn_root, ";
        $sql .= " sa_standard_number, sa_standard_content ";
        $sql .= " FROM share_activity ";
        $sql .= " WHERE sa_id = ? ";
        $sql .= " AND deletestatus = 'N' ";
        $sql .= " AND sa_status = 'Y' ";
        $Ary = $this->_dataBase->readArrayPreSTMT($sql, "s", array($key), 7);
        return $Ary;
    }

    function ShareSearch($key, $user, $c_id) {
        $sql = " SELECT COUNT(sc_id) FROM share_course ";
        $sql .= " WHERE sa_id = ? ";
        $sql .= " AND c_id = ? ";
        $sql .= " AND share_lineid = ? ";
        $value = $this->_dataBase->readValuePreSTMT($sql, "sss", array($key, $c_id, $user));
        return $value;
    }

    function ShareQrCode($user, $c_id, $key) {
        $sql = " SELECT sa_id, sq_cdn_root FROM share_qrcode ";
        $sql .= " WHERE mlm_lineid = ? ";
        $sql .= " AND c_id = ? ";
        $sql .= " AND sa_id = ? ";
        $Ary = $this->_dataBase->readArrayPreSTMT($sql, "sss", array($user, $c_id, $key), 2);
        return $Ary;
    }

    function ShareQrCodeInfo($key) {
        $sql = " SELECT sa_id, sq_cdn_root FROM share_qrcode ";
        $sql .= " AND sa_id = ? ";
        $Ary = $this->_dataBase->readArrayPreSTMT($sql, "sss", array($user, $c_id, $key), 2);
        return $Ary;
    }

    function InsertShareQrCode($user, $c_id, $key, $local_file, $cdn_file) {
        $sql = " INSERT INTO share_qrcode ";
        $sql .= " ( sa_id, c_id, mlm_lineid, sq_img, sq_cdn_root )";
        $sql .= " VALUES ";
        $sql .= " ( ?, ?, ?, ?, ? ) ";
        $bindpara_type = "sssss";
        $bindpara_value_array = array($key, $c_id, $user, $local_file, $cdn_file);
        $this->_dataBase->createPreSTMT($sql, $bindpara_type, $bindpara_value_array);
    }

    function MEMBER_STATUS($user, $c_id) {
        $sql = " SELECT COUNT(mlm_id) FROM member_list_m ";
        $sql .= " WHERE mlm_lineid = ? ";
        $sql .= " AND c_id = ? ";
        $sql .= " AND mlm_dsp = 'Y' ";
        $value = $this->_dataBase->readValuePreSTMT($sql, "ss", array($user, $c_id));
        return $value;
    }

    function UPDATE_SHARE_RECORD($key, $user, $c_id) {
        $datetime = $this->_DateTime;
        $sql = " UPDATE share_exchange_record SET ";
        $sql .= " ser_apply_status = 'Y', ";
        $sql .= " apply_datetime = ? ";
        $sql .= " WHERE sa_id = ? ";
        $sql .= " AND c_id = ? ";
        $sql .= " AND mlm_lineid = ? ";
        $this->_dataBase->updatePreSTMT($sql, "ssss", array($datetime, $key, $c_id, $user));
    }

}
