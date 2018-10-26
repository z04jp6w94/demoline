<?php

namespace FB\FBApp\EventHandler\PostEventHandler\bearsevenescape;
use FB\FBApp\Event\PostEvent\StatusPost;
use FB\FBApp\EventHandler;

class BearStatusPostHandler implements EventHandler {

    private $_statusPost;
    private $_fbApp = null, $_databaseProcessor = null, $_redis = null, $_i18n = null;
    private $_s3;
    private $_cId;
    private $_fbPageId;

    public function __construct(StatusPost $statusPost, $fbApp, $databaseProcessor, $redis, $i18n, $s3, $cId, $fbPageId) {
        $this->_statusPost = $statusPost;
        $this->_fbApp = $fbApp;
        $this->_databaseProcessor = $databaseProcessor;
        $this->_redis = $redis;
        $this->_i18n = $i18n;
        $this->_s3 = $s3;
        $this->_cId = $cId;
        $this->_fbPageId = $fbPageId;
    }

    public function handle() {
        $fbPageId = $this->_statusPost->getPageId();
        $postId = $this->_statusPost->getPostId();
        $fromId = $this->_statusPost->getFromId();
    }

}
