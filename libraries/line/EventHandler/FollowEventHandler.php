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

namespace SCRM\BOT\EventHandler;

use LINE\LINEBot;
use LINE\LINEBot\Event\FollowEvent;
use SCRM\BOT\EventHandler;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\StickerMessage;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use SCRM\BOT\ButtonTemplateBuilder;
use SCRM\BOT\CarouselColumnTemplateBuilder;

class FollowEventHandler implements EventHandler {
    /*
     * User status codes
     * Redis
     */

    const STATUS_NONE = 0;

    /** @var LINEBot $bot */
    private $_bot;

    /** @var FollowEvent $followEvent */
    private $followEvent;
    private $_databaseProcessor;
    private $_redis;
    private $_i18n;
    private $_channelId;
    private $_access_token;
    private $_s3;

    /**
     * FollowEventHandler constructor.
     * @param FollowEvent $followEvent
     * @param LINEBot $bot
     * @param DatabaseProcessor $databaseProcessor
     * @param \Redis $redis
     * @param I18n $i18n
     * @param integer $channelId
     */
    public function __construct(FollowEvent $followEvent, $bot, $databaseProcessor, $redis, $i18n, $channelId, $access_token, $s3) {
        $this->followEvent = $followEvent;
        $this->_bot = $bot;
        $this->_databaseProcessor = $databaseProcessor;
        $this->_redis = $redis;
        $this->_i18n = $i18n;
        $this->_channelId = $channelId;
        $this->_access_token = $access_token;
        $this->_s3 = $s3;
    }

    public function handle() {
        $userid = $this->followEvent->getUserId();            /* $event->getUserId() */
        $replyToken = $this->followEvent->getReplyToken();
        $c_id = "1029288508";
        $GetMemberCount = $this->_databaseProcessor->GetMemberList($userid, $c_id);
        $response = $this->_bot->getProfile($userid);
        if ($response->isSucceeded()) {
            $profile = $response->getJSONDecodedBody();
            $user_name = $profile['displayName'];
            $user_name = removeEmoji($user_name);
        }
        if ($GetMemberCount == 0) {
            $this->_databaseProcessor->AddFollowMember($userid, $c_id, $user_name);
            $follow_message = $this->_databaseProcessor->GetFollowContent($c_id);
            $resp = $this->_bot->replyMessage($replyToken, new TextMessageBuilder($follow_message));
            $this->_setUserStatus($userid, self::STATUS_NONE);
        } else if ($GetMemberCount == 1) {
            $this->_databaseProcessor->UpdateFollowStatus($userid, $user_name, $c_id, "Y");
            $follow_message = $this->_databaseProcessor->GetFollowContent($c_id);
            $resp = $this->_bot->replyMessage($replyToken, new TextMessageBuilder($follow_message));
        }

        /* Step */
        $RichMenuId = $this->_databaseProcessor->RichMenuId($c_id);
        if ($RichMenuId != "") {
            $this->linkToUser($this->_access_token, $userid, $RichMenuId);
            /* RedisEmpty */
            $redis_status = $this->_getUserStatus($userid);
            if ($redis_status == false) {
                $this->_setUserStatus($userid, self::STATUS_NONE);
            }
        }
        return;
        if ($resp->getHTTPStatus() != 200) {
            error_log("Fail to reply message: {$resp->getHTTPStatus()} - {$resp->getRawBody()}");
        }
    }

    private function isRichmenuIdValid($string) {
        if (preg_match('/^[a-zA-Z0-9-]+$/', $string)) {
            return true;
        } else {
            return false;
        }
    }

    private function linkToUser($channelAccessToken, $userId, $richmenuId) {
        if (!$this->isRichmenuIdValid($richmenuId)) {
            return 'invalid richmenu id';
        }
        $sh = <<< EOF
  curl -X POST \
  -H 'Authorization: Bearer $channelAccessToken' \
  -H 'Content-Length: 0' \
  https://api.line.me/v2/bot/user/$userId/richmenu/$richmenuId
EOF;
        $result = json_decode(shell_exec(str_replace('\\', '', str_replace(PHP_EOL, '', $sh))), true);
        if (isset($result['message'])) {
            return $result['message'];
        } else {
            return 'success';
        }
    }

    /* Redis Function */

    private function _setUserStatus($userid, $status) {
        return $this->_redis->set("UserStatus-{$userid}", $status);
    }

    private function _getUserStatus($userid) {
        return $this->_redis->get("UserStatus-{$userid}");
    }

}
