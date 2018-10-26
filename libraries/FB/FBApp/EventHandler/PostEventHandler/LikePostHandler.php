<?php

namespace FB\FBApp\EventHandler\PostEventHandler;

use FB\FBApp\Event\PostEvent\LikePost;
use FB\FBApp\EventHandler;
use FB\FBApp\EventHandler\PostEventHandler\bearsevenescape\BearLikePostHandler;
class LikePostHandler implements EventHandler {

    private $_likePost;
    private $_fbApp = null, $_databaseProcessor = null, $_redis = null, $_i18n = null;
    private $_s3;
    private $_cId;
    private $_fbPageId;

    public function __construct(LikePost $likePost, $fbApp, $databaseProcessor, $redis, $i18n, $s3, $cId, $fbPageId) {
        $this->_likePost = $likePost;
        $this->_fbApp = $fbApp;
        $this->_databaseProcessor = $databaseProcessor;
        $this->_redis = $redis;
        $this->_i18n = $i18n;
        $this->_s3 = $s3;
        $this->_cId = $cId;
        $this->_fbPageId = $fbPageId;
    }

    public function handle() {
        $fbPageId = $this->_likePost->getPageId();
        $postId = $this->_likePost->getPostId();
        $fromId = $this->_likePost->getFromId();
    }

}
