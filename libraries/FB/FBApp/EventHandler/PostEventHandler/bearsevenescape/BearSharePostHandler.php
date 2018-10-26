<?php

namespace FB\FBApp\EventHandler\PostEventHandler\bearsevenescape;
use FB\FBApp\Event\PostEvent\SharePost;
use FB\FBApp\EventHandler;

class BearSharePostHandler implements EventHandler {

    private $_sharePost;
    private $_fbApp = null, $_databaseProcessor = null, $_redis = null, $_i18n = null;
    private $_s3;
    private $_cId;
    private $_fbPageId;

    public function __construct(SharePost $sharePost, $fbApp, $databaseProcessor, $redis, $i18n, $s3, $cId, $fbPageId) {
        $this->_sharePost = $sharePost;
        $this->_fbApp = $fbApp;
        $this->_databaseProcessor = $databaseProcessor;
        $this->_redis = $redis;
        $this->_i18n = $i18n;
        $this->_s3 = $s3;
        $this->_cId = $cId;
        $this->_fbPageId = $fbPageId;
    }

    public function handle() {
        $fbPageId = $this->_sharePost->getPageId();
        $postId = $this->_sharePost->getPostId();
        $fromId = $this->_sharePost->getFromId();
    }

}
