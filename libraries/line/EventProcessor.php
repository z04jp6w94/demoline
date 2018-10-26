<?php

/**
 * Copyright 2017 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace SCRM\BOT;

use LINE\LINEBot;
use LINE\LINEBot\Event\BeaconDetectionEvent;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Event\JoinEvent;
use LINE\LINEBot\Event\LeaveEvent;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\AudioMessage;
use LINE\LINEBot\Event\MessageEvent\ImageMessage;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\Event\MessageEvent\StickerMessage;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\VideoMessage;
use LINE\LINEBot\Event\PostbackEvent;
use LINE\LINEBot\Event\UnfollowEvent;
use SCRM\BOT\EventHandler;
use SCRM\BOT\EventHandler\BeaconEventHandler;
use SCRM\BOT\EventHandler\FollowEventHandler;
use SCRM\BOT\EventHandler\UnfollowEventHandler;
use SCRM\BOT\EventHandler\PostbackEventHandler;
use SCRM\BOT\EventHandler\JoinEventHandler;
use SCRM\BOT\EventHandler\LeaveEventHandler;
use SCRM\BOT\EventHandler\MessageHandler\AudioMessageHandler;
use SCRM\BOT\EventHandler\MessageHandler\ImageMessageHandler;
use SCRM\BOT\EventHandler\MessageHandler\LocationMessageHandler;
use SCRM\BOT\EventHandler\MessageHandler\StickerMessageHandler;
use SCRM\BOT\EventHandler\MessageHandler\TextMessageHandler;
use SCRM\BOT\EventHandler\MessageHandler\VideoMessageHandler;

class EventProcessor {

    private $_bot = null, $_databaseProcessor = null, $_redis = null, $_i18n = null;
    private $_channelId;
    private $_access_token;
    private $_s3;

    function __construct($bot, $databaseProcessor, $redis, $i18n, $channelId, $access_token, $s3) {
        $this->_bot = $bot;
        $this->_databaseProcessor = $databaseProcessor;
        $this->_redis = $redis;
        $this->_i18n = $i18n;
        $this->_i18n->load('bot');
        $this->_access_token = $access_token;
        $this->_channelId = $channelId;
        $this->_s3 = $s3;
    }

    public function processEvent($event) {
        /** @var EventHandler $handler */
        $handler = null;

        if ($event instanceof MessageEvent) {
            if ($event instanceof TextMessage) {
                //$resp = $this->bot->replyText($event->getReplyToken(), "text");
                /*
                  -here- About..
                 */
                $handler = new TextMessageHandler($event, $this->_bot, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_channelId, $this->_access_token, $this->_s3);
            } elseif ($event instanceof StickerMessage) {
                $handler = new StickerMessageHandler($event, $this->_bot, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_channelId, $this->_access_token, $this->_s3);
            } elseif ($event instanceof LocationMessage) {
                $handler = new LocationMessageHandler($event, $this->_bot, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_channelId, $this->_access_token, $this->_s3);
            } elseif ($event instanceof ImageMessage) {
                $handler = new ImageMessageHandler($event, $this->_bot, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_channelId, $this->_access_token, $this->_s3);
            } elseif ($event instanceof AudioMessage) {
                $handler = new AudioMessageHandler($event, $this->_bot, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_channelId, $this->_access_token, $this->_s3);
            } elseif ($event instanceof VideoMessage) {
                $handler = new VideoMessageHandler($event, $this->_bot, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_channelId, $this->_access_token, $this->_s3);
            } else {
                // Just in case...
                error_log('Unknown message type has come');
            }
        } elseif ($event instanceof UnfollowEvent) {
            $handler = new UnfollowEventHandler($event, $this->_bot, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_channelId, $this->_access_token, $this->_s3);
        } elseif ($event instanceof FollowEvent) {
            $handler = new FollowEventHandler($event, $this->_bot, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_channelId, $this->_access_token, $this->_s3);
            /*
              } elseif ($event instanceof JoinEvent) {
              $handler = new JoinEventHandler($event, $this->_bot, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_channelId, $this->_access_token, $this->_s3, $this->_cid);
              } elseif ($event instanceof LeaveEvent) {
              $handler = new LeaveEventHandler($event, $this->_bot, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_channelId, $this->_access_token, $this->_s3, $this->_cid);
             * 
             */
        } elseif ($event instanceof PostbackEvent) {
            $handler = new PostbackEventHandler($event, $this->_bot, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_channelId, $this->_access_token, $this->_s3);
            /*
              PostBack Event
             */
            /*
              } elseif ($event instanceof BeaconDetectionEvent) {
              $handler = new BeaconEventHandler($event, $this->_bot, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_channelId, $this->_access_token, $this->_s3, $this->_cid);
             * 
             */
        } else {
            // Just in case...
            $error_log('Unknown event type has come');
            return;
        }

        $handler->handle();
    }

}
