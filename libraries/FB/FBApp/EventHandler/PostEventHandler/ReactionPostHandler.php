<?php

namespace FB\FBApp\EventHandler\PostEventHandler;

use FB\FBApp\Event\PostEvent\ReactionPost;
use FB\FBApp\EventHandler;
use FB\FBApp\EventHandler\PostEventHandler\bearsevenescape\BearReactionPostHandler;
class ReactionPostHandler implements EventHandler {

    private $_reactionPost;
    private $_fbApp = null, $_databaseProcessor = null, $_redis = null, $_i18n = null;
    private $_s3;
    private $_cId;
    private $_fbPageId;

    public function __construct(ReactionPost $reactionPost, $fbApp, $databaseProcessor, $redis, $i18n, $s3, $cId, $fbPageId) {
        $this->_reactionPost = $reactionPost;
        $this->_fbApp = $fbApp;
        $this->_databaseProcessor = $databaseProcessor;
        $this->_redis = $redis;
        $this->_i18n = $i18n;
        $this->_s3 = $s3;
        $this->_cId = $cId;
        $this->_fbPageId = $fbPageId;
    }

    public function handle() {
        $fbPageId = $this->_reactionPost->getPageId();
        $postId = $this->_reactionPost->getPostId();
        $fromId = $this->_reactionPost->getFromId();
        $reactionType = $this->_reactionPost->getReactionType();
    }

}
