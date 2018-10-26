<?php

function chkUserSecurity($mysqli, $user_id, $programPath) {
    $sql = " SELECT c.program_id, c.program_name FROM sysuser a ";
    $sql .= " LEFT JOIN sysauthority b ON a.group_id = b.group_id";
    $sql .= " LEFT JOIN sysprogram c ON b.program_id = c.program_id";
    $sql .= " WHERE a.user_id = ? AND c.program_path = ?";
    $sql .= " AND a.user_status = 'Y' ";
    $program_ary = $mysqli->readArrayPreSTMT($sql, "ss", array($user_id, $programPath), 2);
    if ($program_ary < 1) {
        header("location:https://" . $_SERVER ['HTTP_HOST'] . "/views/BackEnd/Login/Welcome.php");
        exit();
    } else {
        return $program_ary;
    }
}

function chkValueEmpty($value) {
    if ($value == "") {
        header("location:https://" . $_SERVER ['HTTP_HOST'] . "/views/BackEnd/Login/Welcome.php");
        exit();
    }
}

function chkSourceFileName($source_str, $catch_str) {
    if ($source_str != $catch_str) {
        header("location:https://" . $_SERVER ['HTTP_HOST'] . "/views/BackEnd/Login/Welcome.php");
        exit();
    }
}

function chkSourceUrl($base, $web) {
    if ($base != $web) {
        header("location:https://" . $_SERVER ['HTTP_HOST'] . "/views/BackEnd/Login/Welcome.php");
        exit();
    }
}

function BackToLoginPage() {
    header("Location:https://" . $_SERVER['HTTP_HOST'] . "/views/BackEnd/Login/Member_Logout_Action.php");
    exit;
}

?>