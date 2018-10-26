<?php

function Get_oauth_accessToken($post_data) {
//取得AccessToken  V1: https://api.line.me/v2/oauth/accessToken, V2:  https://api.line.me/oauth2/v2.1/token
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.line.me/oauth2/v2.1/token");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function LINE_LOGIN_GetProFile($post_data2, $access_token) {
    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, "https://api.line.me/v2/profile");
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, $post_data2);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $access_token));
    $result2 = curl_exec($ch2);
    curl_close($ch2);
    return $result2;
}

function GetProFile($userid, $accesstoken) {
    $ch = curl_init("https://api.line.me/v2/bot/profile/" . $userid . "");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $accesstoken
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
//    功能簡述    : FB回覆Message
//    撰寫日期    : 20171218
//    撰寫人員    : Momo
//    參數說明    : user:sender, response_format_text:json內容, accessToken:Line ACCESSTOKEN
//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
function FBCommentMSG($sender, $message, $fbaccessToken) {

    $post_data = array("message" => $message);

    $ch = curl_init("https://graph.facebook.com/v2.11/$sender/private_replies?access_token=$fbaccessToken");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
            )
    );
    $answer = curl_exec($ch);
    curl_close($ch);
}

//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
//    功能簡述    : FB發布貼文
//    撰寫日期    : 20171218
//    撰寫人員    : Momo
//    參數說明    : user:sender, response_format_text:json內容, accessToken:Line ACCESSTOKEN
//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
function FBPost($sender, $message, $IMGID, $publish_time, $fbaccessToken, $mysqli) {
    if ($publish_time == '') {
        $post_data = array("message" => $message, "object_attachment" => $IMGID);
        $ch = curl_init("https://graph.facebook.com/v2.11/$sender/feed?access_token=$fbaccessToken");
    } else {
        $post_data = array("message" => $message, "object_attachment" => $IMGID, "scheduled_publish_time" => $publish_time);
        $ch = curl_init("https://graph.facebook.com/v2.11/$sender/feed?access_token=$fbaccessToken&published=0");
    }

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
            )
    );
    $answer = curl_exec($ch);
    curl_close($ch);
    $FBArr = json_decode($answer, true);
    $id = explode('_', $FBArr['id']);

    //儲存LOG
    $sql = "INSERT INTO fb_callback_log(fcl_input, fcl_datetime, mfi_id) VALUES(?, ?, ?)";
    $mysqli->createPreSTMT_CreateId($sql, "sss", array($answer, '', 0));

    return $id[1];
}

//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
//    功能簡述    : FB發布圖片
//    撰寫日期    : 20171218
//    撰寫人員    : Momo
//    參數說明    : user:sender, response_format_text:json內容, accessToken:Line ACCESSTOKEN
//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
function FBPostIMG($sender, $IMG, $publish_time, $fbaccessToken) {
    if ($publish_time == '') {
        $post_data = array("no_story" => true, "url" => "https://social-crm.Work.com.tw" . $IMG);
        $ch = curl_init("https://graph.facebook.com/v2.11/$sender/photos?access_token=$fbaccessToken");
    } else {
        $post_data = array("no_story" => true, "url" => "https://social-crm.Work.com.tw" . $IMG, "scheduled_publish_time" => $publish_time);
        $ch = curl_init("https://graph.facebook.com/v2.11/$sender/photos?access_token=$fbaccessToken&published=0");
    }

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
            )
    );
    $answer = curl_exec($ch);
    curl_close($ch);
    $FBArr = json_decode($answer, true);
    $id = $FBArr['id'];
    return $id;
}
