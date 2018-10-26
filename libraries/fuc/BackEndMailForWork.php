<?php

function SendMail($from, $mailto, $subject, $msg, $filename = "") { // 傳送 mail
    $subject = '=?utf-8?B?' . base64_encode("$subject") . '?='; // 標題加密(防亂碼)
    $boundary = uniqid(""); // 產生分隔字串
    // 設定MAIL HEADER
    $headers = '';
    $headers .= 'MIME-Version: 1.0' . "\n";
    $headers .= 'Content-type: multipart/mixed; boundary="' . $boundary . '"; charset="UTF-8"' . "\n"; //宣告分隔字串
    $headers .= 'From:' . $from . "\n"; // 設定寄件者
    $headers .= 'X-Mailer: PHP/' . phpversion() . "\n";
    // 信件內容開始
    $body = '--' . $boundary . "\n";
    $body .= 'Content-type: text/plain; charset="UTF-8"' . "\n"; // 信件本文header
    $body .= 'Content-Transfer-Encoding: 7bit' . "\n\n"; // 信件本文header
    $body .= $msg . "\n"; // 本文內容
    //附加檔案處理
    if ($filename) {
        $mimeType = mime_content_type($filename); // 判斷檔案類型
        if (!$mimeType)
            $mimeType = "application/unknown"; // 若判斷不出則設為未知
        $fp = fopen($filename, "r"); // 開啟檔案
        $read = fread($fp, filesize($filename)); // 取得檔案內容 
        fclose($fp); // 關閉檔案
        $read = base64_encode($read); //使用base64編碼
        $read = chunk_split($read);  //把檔案所轉成的長字串切開成多個小字串
        $file = basename($filename); //傳回不包含路徑的檔案名稱(mail中會顯示的檔名)
        // 附檔處理開始
        $body .= '--' . $boundary . "\n";
        // 設定附加檔案HEADER
        $body .= 'Content-type: ' . $mimeType . '; name=' . $file . "\n";
        $body .= 'Content-transfer-encoding: base64' . "\n";
        $body .= 'Content-disposition: attachment; filename=' . $file . "\n\n";
        // 加入附加檔案內容
        $body .= $read . "\n";
    }//處理附加檔案完畢
    $body .= "--$boundary--"; //郵件結尾

    mail($mailto, $subject, $body, $headers); // 寄出信件
}

function SendForgotMail($name, $mail, $pw) {
    $subject = SUBJECT_MAIL;
    $body = "<div style='padding:10px 10px;background-color:#f3f3f3'>
                <div style='padding:0px 20px'>
                    <p style='margin:0;font-size:0.96em'><span style='padding:10px;color:#f44336;font-weight:bold'>" . SUBJECT_MAIL . "</span></p>
                </div>
                <div style='margin:10px 10px'>
                    <p>親愛的" . $name . "，您好：</p>
                    <p style='margin:16px 0px'>登入帳號密碼為 : 【".$pw."】！</p>
                    <p style='margin:25px 0px;border-top:1px dashed #cacaca;'></p>
                    <p style='line-height:22px;font-size:0.94em'>
                        此信件為系統自動發送，請勿直接回覆！
                    </p>
                </div>
            </div>";
    $additional_headers = "MIME-Version: 1.0" . "\r\n";
    $additional_headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $additional_headers .= "From:" . FROM_MAIL;

    mail($mail, $subject, $body, $additional_headers); // 寄出信件
}

function SendVerificationMail($name, $mail, $url) {
    $subject = SUBJECT_MAIL;
    $body = "<div style='padding:10px 10px;background-color:#f3f3f3'>
                <div style='padding:0px 20px'>
                    <p style='margin:0;font-size:0.96em'><span style='padding:10px;color:#f44336;font-weight:bold'>易福網 - EMAIL驗證信通知</span></p>
                </div>
                <div style='margin:10px 10px'>
                    <p>親愛的" . $name . "，您好：</p>
                    <p style='margin:16px 0px'>感謝您加入易福網 IF 會員，我們已經收到您的申請資料，為了確認您的Email正確無誤，請點選以下連結，完成加入會員動作！</p>
                    <p style='margin:25px 0px;border-top:1px dashed #cacaca;'></p>
                    <p style='margin:16px 0px'>綁定程序</p>
                    <p style='margin:16px 0px; padding:0px 12px;'><a href='".$url."' style='text-decoration:none;color:#0047ff'>確認信箱無誤</a></p>
                    <table style='width:100%;border:0px solid black'>
			<tbody></tbody>
                    </table>
                    <p style='margin:25px 0px;border-top:1px dashed #cacaca;'></p>
                    <p style='line-height:22px;font-size:0.94em'>
			<span style='color:#f44336;'>
                            如果您無法按下以上連結，請直接將下列網址複製貼到瀏覽器網址列：
                            <br>
                            ".$url."
			</span>
                    </p>
                    <p style='line-height:22px;font-size:0.94em'>
                        此信件為系統自動發送，請勿直接回覆！
                    </p>
                </div>
            </div>";
    $additional_headers = "MIME-Version: 1.0" . "\r\n";
    $additional_headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $additional_headers .= "From:" . FROM_MAIL;

    mail($mail, $subject, $body, $additional_headers); // 寄出信件
}

?>