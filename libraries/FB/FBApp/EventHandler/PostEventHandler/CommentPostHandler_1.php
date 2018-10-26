<?php

namespace FB\FBApp\EventHandler\PostEventHandler;

use FB\FBApp;
use FB\FBApp\Builder\PostBuilder\PrivateReplies;
use FB\FBApp\Event\PostEvent\CommentPost;
use FB\FBApp\EventHandler;
use FB\FBApp\EventHandler\PostEventHandler\bearsevenescape\BearCommentPostHandler;

class CommentPostHandler implements EventHandler {

    private $_commentPost;
    private $_fbApp = null, $_databaseProcessor = null, $_redis = null, $_i18n = null;
    private $_s3;
    private $_cId;
    private $_fbPageId;

    public function __construct(CommentPost $commentPost, $fbApp, $databaseProcessor, $redis, $i18n, $s3, $cId, $fbPageId) {
        $this->_commentPost = $commentPost;
        $this->_fbApp = $fbApp;
        $this->_databaseProcessor = $databaseProcessor;
        $this->_redis = $redis;
        $this->_i18n = $i18n;
        $this->_s3 = $s3;
        $this->_cId = $cId;
        $this->_fbPageId = $fbPageId;
    }

    public function handle() {
        $fbPageId = $this->_commentPost->getPageId();
        if ($fbPageId === "228037034339317") {
            $handler = new BearCommentPostHandler($this->_commentPost, $this->_fbApp, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_s3, $this->_cId, $this->_fbPageId);
            $handler->handle();
        } else {
            $postId = $this->_commentPost->getPostId();
            $fromId = $this->_commentPost->getFromId();
            $fromName = $this->_commentPost->getFromName();
            $commentId = $this->_commentPost->getCommentId();
            $message = $this->_commentPost->getMessage();
            $userStatus = $this->_getUserStatus($fromId);
            //判斷$postId和$message//若是符合才回丟
            if (!empty($message) && $postId === "872827279571700_874946592693102") {
                if (preg_match("/\我想了解/i", $message)) {
//                if (empty($userStatus) || $userStatus[2] !== "again") {
                    $this->_databaseProcessor->InsertMemberFBIDInfo($fromId, $fromName);
                    $this->_fbApp->send(FBApp::API_TYPE_POST_PRIVATE_REPLIES, $commentId, new PrivateReplies($fromName . "您好" . PHP_EOL . "【賴吧】是我們齊力樂門科技公司獨家研發的CRM產品" . PHP_EOL . "請問您是否有再經營LINE@或是FACEBOOK粉絲團呢？" . PHP_EOL . "輸入1=>有" . PHP_EOL . "輸入2=>沒有"));
                    $this->_delUserStatus($fromId);
//                    $this->_setUserStatus($fromId, array("", "", "again"));
//                } 
//                else {
//                    $this->_fbApp->send(FBApp::API_TYPE_POST_PRIVATE_REPLIES, $commentId, new PrivateReplies("您可以透過點選左下角的選單來了解【賴吧】！！"));
//                }
                }
                //else {
//                if($message === "clear_redis"){
//                    $this->_fbApp->send(FBApp::API_TYPE_POST_PRIVATE_REPLIES, $commentId, new PrivateReplies("清除完成"));
                $this->_delUserStatus($fromId);
//                }
//            }
            }
        }
    }

    /* Redis Function */

    private function _setUserStatus($key, $valAry) {
        $jsonStr = json_encode($valAry);
        return $this->_redis->set("UserStatus-{$key}", $jsonStr);
    }

    private function _getUserStatus($key) {
        $jsonStr = $this->_redis->get("UserStatus-{$key}");
        return json_decode($jsonStr);
    }

    private function _delUserStatus($key) {
        return $this->_redis->del("UserStatus-{$key}");
    }

    private function privateDeclare($senderId, $last_name, $first_name) {
        //存到DB
        $this->_databaseProcessor->InsertMemberFBMessengerIDInfo($senderId, $last_name . $first_name);
        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageText(MessageText::MESSAGING_TYPE_RESPONSE, $senderId, "Hi，" . $last_name . $first_name . PHP_EOL . "感謝您加入本官方帳號！當您開始使用本關方帳號服務時，即表示您信賴並同意本服務對您個人資訊的處理方式。為了協助您瞭解本服務收集的資料類型以及這些資料的用途，請撥冗詳閱《隱私權條款》：https://goo.gl/6ffcJj"));
        $this->_setUserStatus($senderId, array("", "privacy"));
    }

}
