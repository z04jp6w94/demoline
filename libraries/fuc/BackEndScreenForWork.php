<?php

function chkUserFunc($mysqli, $user_id, $programPath) {
    global $create, $read, $update, $delete;
    $sql = "SELECT b.authority_create, b.authority_read, b.authority_update, b.authority_delete FROM sysuser a";
    $sql .= " LEFT JOIN sysauthority b ON a.group_id = b.group_id";
    $sql .= " LEFT JOIN sysprogram c ON b.program_id = c.program_id";
    $sql .= " WHERE a.user_id = ? AND c.program_path = ?";
    $initAry = $mysqli->readArrayPreSTMT($sql, "ss", array($user_id, $programPath), 4);
    $create = $initAry[0][0] == 'Y' ? "abled" : "disabled";
    $read = $initAry[0][1] == 'Y' ? "abled" : "disabled";
    $update = $initAry[0][2] == 'Y' ? "abled" : "disabled";
    $delete = $initAry[0][3] == 'Y' ? "abled" : "disabled";
}

function BreadCrumb($program_id) {
    $user_id = $_SESSION["user_id"];
    $mysqli = new DatabaseProcessorForWork();
    $sql = " SELECT b.menu_id, b.menu_prev_id ";
    $sql .= " FROM sysuser a ";
    $sql .= " LEFT JOIN sysmenu b ON a.group_id = b.group_id ";
    $sql .= " LEFT JOIN sysprogram c ON b.program_id = c.program_id ";
    $sql .= " LEFT JOIN sysauthority d ON a.group_id = d.group_id AND b.program_id = d.program_id ";
    $sql .= " WHERE a.user_id = ? AND b.program_id = ? ";
    $Menu_Ary = $mysqli->readArrayPreSTMT($sql, "ss", array($user_id, $program_id), 2);
    $sql = " SELECT menu_folder, menu_icon from sysmenu ";
    $sql .= " where menu_id = ? ";
    $sql .= " and menu_type = 'P' ";
    $sql .= " union ";
    $sql .= " SELECT p.program_name, p.program_icon from sysmenu m ";
    $sql .= " left join sysprogram p on p.program_id = m.program_id ";
    $sql .= " where menu_id = ? ";
    $sql .= " and menu_type = 'F' ";
    $Bread_Ary = $mysqli->readArrayPreSTMT($sql, "ss", array($Menu_Ary[0][1], $Menu_Ary[0][0]), 2);

    $home = '<li>';
    $home .= '<a href="#">';
    $home .= '<i class="pe-7s-home"></i>';
    $home .= '<span>Home</span>';
    $home .= '</a>';
    $home .= '</li>';
    
    $out = "";
    foreach ($Bread_Ary as $rsAry) {
        $out .= '<li>';
        $out .= '<a href = "#">';
        $out .= '<i class = "' . $rsAry[1] . '"></i>';
        $out .= '<span>' . $rsAry[0] . '</span>';
        $out .= '</a>';
        $out .= '</li>';
    }
    echo $home . $out;
}

function setScreenPage($mysqli, $sqlFrom, $sqlWhere) {
    global $totalRecord, $perPageRecord, $totalPage, $currentPage, $firstRecord, $currentPageRecord;
    $sql = "SELECT count(*)";
    $sql .= $sqlFrom;
    $sql .= $sqlWhere;
    $totalRecord = $mysqli->readValueSTMT($sql);
    $totalPage = ceil($totalRecord / $perPageRecord);
    $currentPage = $currentPage < 1 ? 1 : $currentPage; //目前頁數判斷
    $currentPage = $currentPage > $totalPage && $totalPage != 0 ? $totalPage : $currentPage; //目前頁數判斷
    $firstRecord = $currentPage == 1 ? 0 : ($currentPage - 1) * $perPageRecord;
    $currentPageRecord = $currentPage == $totalPage ? $totalRecord % $perPageRecord : $perPageRecord; //目前頁面筆數判斷
    $currentPageRecord = $currentPageRecord == 0 ? $perPageRecord : $currentPageRecord; //目前頁面筆數判斷
}

?>