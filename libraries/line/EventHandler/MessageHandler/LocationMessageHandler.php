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
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use SCRM\BOT\EventHandler;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;

class LocationMessageHandler implements EventHandler {

    /** @var LINEBot $bot */
    private $_bot;

    /** @var LocationMessage $event */
    private $locationMessage;
    private $_databaseProcessor;
    private $_redis;
    private $_i18n;
    private $_channelId;
    private $_access_token;
    private $_s3;

    /**
     * LocationMessageHandler constructor.
     * @param LocationMessage $locationMessage
     * @param LINEBot $bot
     * @param DatabaseProcessor $databaseProcessor
     * @param \Redis $redis
     * @param I18n $i18n
     * @param integer $channelId
     */
    public function __construct(LocationMessage $locationMessage, $bot, $databaseProcessor, $redis, $i18n, $channelId, $access_token, $s3) {
        $this->locationMessage = $locationMessage;
        $this->_bot = $bot;
        $this->_databaseProcessor = $databaseProcessor;
        $this->_redis = $redis;
        $this->_i18n = $i18n;
        $this->_channelId = $channelId;
        $this->_access_token = $access_token;
        $this->_s3 = $s3;
    }

    public function handle() {
        $replyToken = $this->locationMessage->getReplyToken();
        $title = $this->locationMessage->getTitle();
        $address = $this->locationMessage->getAddress();
        $latitude = $this->locationMessage->getLatitude();
        $longitude = $this->locationMessage->getLongitude();

        $this->_bot->replyMessage(
                $replyToken, new LocationMessageBuilder($title, $address, $latitude, $longitude)
        );
    }

}
