<?php

namespace FB\FBApp\EventHandler\MessengerEventHandler\MessageMessenger;

use FB\FBApp\Event\MessengerEvent\MessageMessenger\AttachmentMessageMessenger;
use FB\FBApp\EventHandler;
use FB\FBApp\EventHandler\MessengerEventHandler\MessageMessenger\bearsevenescape\BearAttachmentMessageMessengerHandler;

class AttachmentMessageMessengerHandler implements EventHandler {

    private $_attachmentMessageMessenger;
    private $_fbApp = null, $_databaseProcessor = null, $_redis = null, $_i18n = null;
    private $_s3;
    private $_cId;
    private $_fbPageId;

    public function __construct(AttachmentMessageMessenger $attachmentMessageMessenger, $fbApp, $databaseProcessor, $redis, $i18n, $s3, $cId, $fbPageId) {
        $this->_attachmentMessageMessenger = $attachmentMessageMessenger;
        $this->_fbApp = $fbApp;
        $this->_databaseProcessor = $databaseProcessor;
        $this->_redis = $redis;
        $this->_i18n = $i18n;
        $this->_s3 = $s3;
        $this->_cId = $cId;
        $this->_fbPageId = $fbPageId;
    }

    public function handle() {
        $fbPageId = $this->_attachmentMessageMessenger->getPageId();
        if ($fbPageId === "228037034339317") {
            $handler = new BearAttachmentMessageMessengerHandler($this->_attachmentMessageMessenger, $this->_fbApp, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_s3, $this->_cId, $this->_fbPageId);
            $handler->handle();
        } else {
            $senderId = $this->_attachmentMessageMessenger->getSenderId();
            $userStatus = $this->_redis->get("UserStatus-{$senderId}");
        }
    }

}
