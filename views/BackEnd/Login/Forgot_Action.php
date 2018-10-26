<?php

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('date.timezone', 'Asia/Taipei');
ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] . '/assets_rear/session/');
session_start();
//函式庫
include_once($_SERVER['DOCUMENT_ROOT'] . "/config/chiliman_config.php");
//取得固定參數
$fileName = $_REQUEST["fileName"];
//取得查詢參數
$user_c_id = $_REQUEST['user_c_id'];
$user_account = $_REQUEST['user_account'];
$user_email = $_REQUEST['user_email'];
//判斷前台資料
if ($user_c_id == '' || $user_account == '' || $user_email == '') {
    header("Location:Member_Login.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
    $source_url = dirname($url) . '/' . $fileName . '.php';
    if ($_SERVER['HTTP_REFERER'] != $source_url) {
        BackToLoginPage();
        exit;
    } else {
//資料庫連線
        $mysqli = new DatabaseProcessorForWork();
//判斷帳號密碼
        $sql = " SELECT c.c_name, c.c_mail, su.user_password FROM sysuser su ";
        $sql .= " LEFT JOIN crm_m c on c.c_id = su.c_id ";
        $sql .= " WHERE su.user_account = ? AND su.user_email = ? AND su.c_id = ? ";
        $user_ary = $mysqli->readArrayPreSTMT($sql, "sss", array($user_account, $user_email, $user_c_id), 3);
        if (count($user_ary) != '1') {
            echo "<script>";
            echo "alert('客戶號或帳號錯誤請重新輸入！');";
            echo 'window.history.back();';
            echo "</script>";
            exit();
        } else {
            //
            require_once(AUTOLOAD_PATH);
            $name = $user_ary[0][0];
            $mailto = $user_ary[0][1];
            $pw = DECCode($user_ary[0][2]);
            $body = "<div style='padding:10px 10px;background-color:#f3f3f3'>
                <div style='padding:0px 20px'>
                    <p style='margin:0;font-size:0.96em'><span style='padding:10px;color:#f44336;font-weight:bold'>" . SUBJECT_MAIL . "</span></p>
                </div>
                <div style='margin:10px 10px'>
                    <p>親愛的" . $name . "，您好：</p>
                    <p style='margin:16px 0px'>登入帳號密碼為 : 【" . $pw . "】！</p>
                    <p style='margin:25px 0px;border-top:1px dashed #cacaca;'></p>
                    <p style='line-height:22px;font-size:0.94em'>
                        此信件為系統自動發送，請勿直接回覆！
                    </p>
                </div>
            </div>";
            $PHPMailer = new PHPMailer\PHPMailer\PHPMailer();
            $PHPMailer->SMTPDebug = 0; //Debug模式設定: 0 => off, 1 => client messages, 2 = client and server messages
            $PHPMailer->IsSMTP(); //設定使用SMTP方式寄信              
            $PHPMailer->SMTPAuth = true; //設定SMTP需要驗證      
            $PHPMailer->Host = "smtp.gmail.com"; //設定HOST(在這裡使用Gmail的SMTP主機，以下是相關設定)  
            $PHPMailer->Port = 465;  //設定SMTP埠號
            $PHPMailer->SMTPSecure = "ssl"; //設定要使用SSL連線
            $PHPMailer->Username = "service@romobi.com"; //設定SMTP username       
            $PHPMailer->Password = "romobi53333652"; //設定SMTP password

            $PHPMailer->CharSet = "utf-8"; //設定郵件編碼 
            $PHPMailer->From = "developer.chiliman@gmail.com"; //設定寄件者信箱        
            $PHPMailer->FromName = FROM_MAIL; //設定寄件者姓名    
            $PHPMailer->Subject = SUBJECT_MAIL; //設定郵件標題        
            $PHPMailer->Body = $body; //設定郵件內容        
            $PHPMailer->IsHTML(true); //設定郵件內容為HTML     
            $PHPMailer->WordWrap = 50; //設定每幾個字自動換行 
//設定收件者信箱及姓名(可以多個)
            $PHPMailer->AddAddress($mailto, $name);
//設定密件副本收件者信箱
            $PHPMailer->AddBCC("tai@chiliman.com.tw");
//寄出信件
            if (!$PHPMailer->Send()) {
                echo "郵件無法順利寄出，請通知齊力樂門工程師處理";
                echo "Mailer ErrorInfo: " . $PHPMailer->ErrorInfo;
                exit();
            }

//            $name = $user_ary[0][0];
//            $mailto = $user_ary[0][1];
//            $pw = DECCode($user_ary[0][2]);
//            SendForgotMail($name, $mailto, $pw);
            echo "<script>";
            echo "alert('已經寄密碼信至您的信箱！若無請至垃圾郵件查看！');";
            echo "window.location.replace('Member_Login.php'); ";
            echo "</script>";
            exit();
        }
    }
}
?>