<?php

namespace FB\FBApp\EventHandler\PostEventHandler\bearsevenescape;

use FB\FBApp;
use FB\FBApp\Builder\PostBuilder\PrivateReplies;
use FB\FBApp\Event\PostEvent\CommentPost;
use FB\FBApp\EventHandler;

class BearCommentPostHandler implements EventHandler {

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
        $postId = $this->_commentPost->getPostId();
        $fromId = $this->_commentPost->getFromId();
        $fromName = $this->_commentPost->getFromName();
        $commentId = $this->_commentPost->getCommentId();
        $message = $this->_commentPost->getMessage();
        $userStatus = $this->_getUserStatus($fromId);
        $postIdAry = array("228037034339317_384797951996557", "228037034339317_390715421404810", "228037034339317_390652041411148", "228037034339317_388563091620043", "228037034339317_388874928255526", "228037034339317_394338634375822");
        //判斷$postId和$message//若是符合才回丟
        if (!empty($message)) {
            if (in_array($postId, $postIdAry)) {
                $this->_fbApp->send(FBApp::API_TYPE_POST_PRIVATE_REPLIES, $commentId, new PrivateReplies($fromName . "您好" . PHP_EOL . "謝謝你對【熊熊來七逃】的支持。" . PHP_EOL . PHP_EOL . "👇快點選左下角選單，有更多資訊讓你參考喔！"));
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

}
