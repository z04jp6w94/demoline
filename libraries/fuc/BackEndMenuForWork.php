<?php

function initSysMenu($program_id = "") {
    $user_id = $_SESSION["user_id"];
    getSysMenu($user_id, 0, $program_id);
}

function getSysMenu($user_id, $menu_prev_id, $program_id) {
    $mysqli = new DatabaseProcessorForWork();
    $sql = "SELECT b.menu_id ,b.menu_type, b.menu_folder, c.program_id, c.program_name, ";
    $sql .= " c.program_path, b.menu_prev_id, d.authority_dsp, b.menu_icon, c.program_icon, ";
    $sql .= " ( ";
    $sql .= " select count(*) from sysmenu s WHERE s.menu_prev_id = b.menu_id and s.program_id = ? ";
    $sql .= " ) list ";
    $sql .= " FROM sysuser a";
    $sql .= " LEFT JOIN sysmenu b ON a.group_id = b.group_id";
    $sql .= " LEFT JOIN sysprogram c ON b.program_id = c.program_id";
    $sql .= " LEFT JOIN sysauthority d ON a.group_id = d.group_id AND b.program_id = d.program_id";
    $sql .= " WHERE a.user_id = ? AND b.menu_prev_id = ? ";
    $sql .= "  ORDER BY b.menu_order";

    $initAry = $mysqli->readArrayPreSTMT($sql, "sss", array($program_id, $user_id, $menu_prev_id), 11);
    foreach ($initAry as $rsAry) {
        if ($rsAry[1] == 'P') {
            if ($rsAry[10] >= '1') {
                $out = '<li class="active open">';
            } else {
                $out = '<li >';
            }
            $out .= '<a>';
            $out .= '<i class="' . $rsAry[8] . '"></i>';
            $out .= '<span>' . $rsAry[2] . '</span>';
            $out .= '</a>';
            $out .= '<ul>';
            $out .= '<li class="submenu-title">';
            $out .= '<span>' . $rsAry[2] . '</span>';
            $out .= '</li>';
            echo $out;
            getSysMenu($user_id, $rsAry[0], $program_id);
            $out = '</ul>';
            $out .= '</li>';
            echo $out;
        }
        if ($rsAry[1] == 'F' && $rsAry[6] != 0 && $rsAry[7] == "Y") {
            if ($rsAry[3] == $program_id) {
                $out = '<li class="active open">';
            } else {
                $out = '<li >';
            }
            $out .= '<a href=' . $rsAry[5] . '>';
            $out .= '<i class="' . $rsAry[9] . '"></i>';
            $out .= '<span>' . $rsAry[4] . '</span>';
            $out .= '</a>';
            $out .= '</li>';
            echo $out;
        }
    }
}

function getCurrentMenu($group_id, $menu_prev_id) {
    $mysqli = new DatabaseProcessorForWork();
    $sql = "SELECT a.menu_id, a.menu_type, a.menu_folder, b.program_name, a.menu_prev_id";
    $sql .= " FROM sysmenu a";
    $sql .= " LEFT JOIN sysprogram b ON a.program_id = b.program_id";
    $sql .= " WHERE a.group_id = ? AND a.menu_prev_id = ?";
    $sql .= " ORDER BY a.menu_order";
    $initAry = $mysqli->readArrayPreSTMT($sql, "ss", array($group_id, $menu_prev_id), 5);
    foreach ($initAry as $rsAry) {
        if ($rsAry[1] == 'P') {
            echo '<li style="list-style-type: none;" sysmeunPara="' . $rsAry[0] . "-" . $rsAry[1] . "-" . $rsAry[2] . "-" . $rsAry[4] . '">';
            echo '<img src="/assets_rear/images/s4-19.gif" border="0" width="20" height="22" align="absmiddle">';
            echo '<i class="fa fa-caret-right" style = "font-size:20px;width: 20px;"></i>';
            echo '<i class="fa fa-folder-open" style = "font-size:20px;"></i>';
            echo $rsAry[2];
            echo '</li>';
            getCurrentMenu($group_id, $rsAry[0]);
        }
        if ($rsAry[1] == 'F' && $rsAry[4] != 0) {
            echo '<li style="list-style-type: none;" sysmeunPara="' . $rsAry[0] . "-" . $rsAry[1] . "-" . $rsAry[2] . "-" . $rsAry[4] . '">';
            echo '<img src="/assets_rear/images/s4-19.gif" border="0" width="20" height="22" align="absmiddle">';
            echo '<img src="/assets_rear/images/s4-19.gif" border="0" width="20" height="22" align="absmiddle">';
            echo '<i class = "fa fa-pencil-square-o" style = "font-size:20px;"></i>';
            echo $rsAry[3];
            echo '</li>';
        }
    }
}
?>


