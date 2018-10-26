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

namespace SCRM\BOT\EventHandler\MessageHandler;

use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\ImageMessage;
use SCRM\BOT\EventHandler;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;

class ImageMessageHandler implements EventHandler {
    /*
     * User status codes
     * Redis
     */

    const STATUS_NONE = 0;
    const STATUS_LCS = 999;

    /** @var LINEBot $bot */
    private $_bot;

    /** @var ImageMessage $imageMessage */
    private $imageMessage;
    private $_databaseProcessor;
    private $_redis;
    private $_i18n;
    private $_channelId;
    private $_access_token;
    private $_s3;

    /**
     * ImageMessageHandler constructor.
     * @param ImageMessage $imageMessage
     * @param LINEBot $bot
     * @param DatabaseProcessor $databaseProcessor
     * @param \Redis $redis
     * @param I18n $i18n
     * @param integer $channelId
     */
    public function __construct(ImageMessage $imageMessage, $bot, $databaseProcessor, $redis, $i18n, $channelId, $access_token, $s3) {
        $this->imageMessage = $imageMessage;
        $this->_bot = $bot;
        $this->_databaseProcessor = $databaseProcessor;
        $this->_redis = $redis;
        $this->_i18n = $i18n;
        $this->_channelId = $channelId;
        $this->_access_token = $access_token;
        $this->_s3 = $s3;
    }

    public function handle() {
        $contentId = $this->imageMessage->getMessageId();
        $image = $this->_bot->getMessageContent($contentId)->getRawBody();
        $replyToken = $this->imageMessage->getReplyToken();
        $userid = $this->imageMessage->getUserId();
        $timestamp = $this->imageMessage->getTimestamp();
    }

    private function _getUserStatus($userId) {
        return $this->_redis->get("UserStatus-{$userId}");
    }

}
