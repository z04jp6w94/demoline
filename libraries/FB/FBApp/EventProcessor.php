<?php

namespace FB\FBApp;

use FB\FBApp\Event\MessengerEvent;
use FB\FBApp\Event\MessengerEvent\MessageMessenger;
use FB\FBApp\Event\MessengerEvent\MessageMessenger\AttachmentMessageMessenger;
use FB\FBApp\Event\MessengerEvent\MessageMessenger\TextMessageMessenger;
use FB\FBApp\Event\MessengerEvent\PostbackMessenger;
use FB\FBApp\Event\PostEvent;
use FB\FBApp\Event\PostEvent\CommentPost;
use FB\FBApp\Event\PostEvent\LikePost;
use FB\FBApp\Event\PostEvent\PhotoPost;
use FB\FBApp\Event\PostEvent\SharePost;
use FB\FBApp\Event\PostEvent\StatusPost;
use FB\FBApp\EventHandler\MessengerEventHandler\MessageMessenger\AttachmentMessageMessengerHandler;
use FB\FBApp\EventHandler\MessengerEventHandler\MessageMessenger\TextMessageMessengerHandler;
use FB\FBApp\EventHandler\MessengerEventHandler\PostbackMessengerHandler;
use FB\FBApp\EventHandler\PostEventHandler\CommentPostHandler;
use FB\FBApp\EventHandler\PostEventHandler\LikePostHandler;
use FB\FBApp\EventHandler\PostEventHandler\PhotoPostHandler;
use FB\FBApp\EventHandler\PostEventHandler\SharePostHandler;
use FB\FBApp\EventHandler\PostEventHandler\StatusPostHandler;

class EventProcessor {

    private $_fbApp = null, $_databaseProcessor = null, $_redis = null, $_i18n = null;
    private $_s3;
    private $_cId;
    private $_fbPageId;

    function __construct($fbApp, $databaseProcessor, $redis, $i18n, $s3, $cId, $fbPageId) {
        $this->_fbApp = $fbApp;
        $this->_databaseProcessor = $databaseProcessor;
        $this->_redis = $redis;
        $this->_i18n = $i18n;
        $this->_s3 = $s3;
        $this->_cId = $cId;
        $this->_fbPageId = $fbPageId;
    }

    public function processEvent($event) {
        if ($event instanceof MessengerEvent) {
            if ($event instanceof MessageMessenger) {
                if ($event instanceof AttachmentMessageMessenger) {
                    $handler = new AttachmentMessageMessengerHandler($event, $this->_fbApp, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_s3, $this->_cId, $this->_fbPageId);
                    $handler->handle();
                }
                if ($event instanceof TextMessageMessenger) {
                    $handler = new TextMessageMessengerHandler($event, $this->_fbApp, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_s3, $this->_cId, $this->_fbPageId);
                    $handler->handle();
                }
            }
            if ($event instanceof PostbackMessenger) {
                $handler = new PostbackMessengerHandler($event, $this->_fbApp, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_s3, $this->_cId, $this->_fbPageId);
                $handler->handle();
            }
        }
        if ($event instanceof PostEvent) {
            if ($event instanceof CommentPost) {
                $handler = new CommentPostHandler($event, $this->_fbApp, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_s3, $this->_cId, $this->_fbPageId);
                $handler->handle();
            }
            if ($event instanceof LikePost) {
                $handler = new LikePostHandler($event, $this->_fbApp, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_s3, $this->_cId, $this->_fbPageId);
                $handler->handle();
            }
            if ($event instanceof PhotoPost) {
                $handler = new PhotoPostHandler($event, $this->_fbApp, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_s3, $this->_cId, $this->_fbPageId);
                $handler->handle();
            }
            if ($event instanceof SharePost) {
                $handler = new SharePostHandler($event, $this->_fbApp, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_s3, $this->_cId, $this->_fbPageId);
                $handler->handle();
            }
            if ($event instanceof StatusPost) {
                $handler = new StatusPostHandler($event, $this->_fbApp, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_s3, $this->_cId, $this->_fbPageId);
                $handler->handle();
            }
        }
    }

}
