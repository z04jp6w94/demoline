<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//PHPMailer
require_once($_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php");

$mail = new PHPMailer(true);
try {
    //Server settings
    $mail->SMTPDebug = 2;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'z04jp6w944@gmail.com';                 // SMTP username
    $mail->Password = 'qk81382238';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to
    //Recipients
    $mail->setFrom('from@example.com', 'Mailer');
    $mail->addAddress('z04rmpm06@gmail.com', 'Fan');     // Add a recipient
    $mail->addAddress('z04jp6w944@gmail.com');               // Name is optional
    //$mail->addReplyTo('info@example.com', 'Information');
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    //Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
}
/*
  $PHPMailer->SMTPDebug = 2; //Debug模式設定: 0 => off, 1 => client messages, 2 = client and server messages
  $PHPMailer->IsSMTP(); //設定使用SMTP方式寄信
  $PHPMailer->SMTPAuth = true; //設定SMTP需要驗證
  $PHPMailer->Host = "smtp.gmail.com"; //設定HOST(在這裡使用Gmail的SMTP主機，以下是相關設定)
  $PHPMailer->Port = 587;  //設定SMTP埠號
  $PHPMailer->SMTPSecure = "tls"; //設定要使用SSL連線
  $PHPMailer->Username = "z04jp6w944@gmail.com"; //設定SMTP username
  $PHPMailer->Password = "qk81382238"; //設定SMTP password

  $PHPMailer->CharSet = "utf-8"; //設定郵件編碼
  $PHPMailer->From = "z04jp6w944@gmail.com"; //設定寄件者信箱
  $PHPMailer->FromName = "利威特汽車科技"; //設定寄件者姓名
  $PHPMailer->Subject = "xxx"; //設定郵件標題
  $PHPMailer->Body = "<p>xxx</p>"; //設定郵件內容
  $PHPMailer->IsHTML(true); //設定郵件內容為HTML
  $PHPMailer->WordWrap = 50; //設定每幾個字自動換行
  //設定郵件附加檔案(可以多個)
  //if ($emailAttachmentTempName) {
  //    $PHPMailer->addAttachment($emailAttachmentTempName, $emailAttachmentName);
  //}
  //設定收件者信箱及姓名(可以多個)
  $PHPMailer->AddAddress("z04rmpm06@gmail.com", "范鈞源");
  //設定密件副本收件者信箱
  $PHPMailer->AddBCC("z04jp6w944@gmail.com");
  //回信Email及名稱
  $PHPMailer->AddReplyTo("z04jp6w944@gmail.com", "利威特汽車科技"); //設定回信Email及名稱
  //寄出信件
  if (!$PHPMailer->Send()) {
  echo "郵件無法順利寄出，請通知齊力樂門工程師處理";
  echo "Mailer ErrorInfo: " . $PHPMailer->ErrorInfo;
  exit();
  }
 * 
 */
?>