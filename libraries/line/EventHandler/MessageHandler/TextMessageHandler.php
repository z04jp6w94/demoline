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
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use SCRM\BOT\EventHandler;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use SCRM\BOT\ButtonTemplateBuilder;
use SCRM\BOT\CarouselColumnTemplateBuilder;

class TextMessageHandler implements EventHandler {
    /* Default */

    /*
     * User status codes
     * Redis
     */

    const STATUS_NONE = 0;

    /*
     * Postback action codes
     */
    const ACTION_BUSINES_MORE1 = 1;
    const ACTION_BUSINES_MORE3 = 2;
    const ACTION_SHARE = 3;
    const ACTION_MY_ACCOUNT = 4;
    const ACTION_FILL_INFO = 5;
    const ACTION_KEYWORD_CAROUSEL = 6;
    const ACTION_FEEDBACK = 7;

    /** @var LINEBot $bot */
    private $_bot;

    /** @var TextMessage $textMessage */
    private $textMessage;
    private $_databaseProcessor;
    private $_redis;
    private $_i18n;
    private $_channelId;
    private $_access_token;
    private $_s3;

    /**
     * TextMessageHandler constructor.
     * @param TextMessage $textMessage
     * @param $bot
     * @param DatabaseProcessor $databaseProcessor
     * @param \Redis $redis
     * @param I18n $i18n
     * @param integer $channelId
     */
    public function __construct(TextMessage $textMessage, $bot, $databaseProcessor, $redis, $i18n, $channelId, $access_token, $s3) {
        $this->textMessage = $textMessage;
        $this->_bot = $bot;
        $this->_databaseProcessor = $databaseProcessor;
        $this->_redis = $redis;
        $this->_i18n = $i18n;
        $this->_channelId = $channelId;
        $this->_access_token = $access_token;
        $this->_s3 = $s3;
    }

    public function handle() {
        $text = $this->textMessage->getText();
        $userid = $this->textMessage->getUserId();
        $c_id = "1029288508";
        $replyToken = $this->textMessage->getReplyToken();
        $Bitly = new \BitlyShortAPI();
        switch ($text) {
            case '[LIFF]':
                $post_data = array("view" => array("type" => "full", "url" => WEB_HOSTNAME . "/LIFF.html"));
                $ch = curl_init("https://api.line.me/liff/v1/apps");
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json; charset=UTF-8',
                    'Authorization: Bearer ' . $this->_access_token
                ));
                $result = curl_exec($ch);
                curl_close($ch);
                $resp = $this->_bot->replyText($replyToken, $result);
                //full      {"liffId":"1615603328-g6WNyxwK"} - 1615603328-g6WNyxwK
                //tall      
                //compact   1570904158-7JOgL5b4
                return;
            case '[system][create]1':
                $this->_bot->replyMessage($replyToken, new TextMessageBuilder($this->createNewRichmenu1($this->_access_token)));
                //richmenu-8ec951248b78c1e616e87fa521a47d50
                return;
            case '[system][create]2':
                $this->_bot->replyMessage($replyToken, new TextMessageBuilder($this->createNewRichmenu2($this->_access_token)));
                //richmenu-ecd7eaba45636b0485f3ed29096fe5f2
                return;
            case '[system][create]3':
                $this->_bot->replyMessage($replyToken, new TextMessageBuilder($this->createNewRichmenu3($this->_access_token)));
                return;
            case '[system][list]':
                $result = $this->getListOfRichmenu($this->_access_token);
                if (isset($result['richmenus']) && count($result['richmenus']) > 0) {
                    $builders = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
                    $columns = Array();
                    for ($i = 0; $i < count($result['richmenus']); $i++) {
                        $richmenu = $result['richmenus'][$i];
                        $actionArray = array();
                        array_push($actionArray, new MessageTemplateActionBuilder(
                                'upload image', 'upload::' . $richmenu['richMenuId']));
                        array_push($actionArray, new MessageTemplateActionBuilder(
                                'delete', 'delete::' . $richmenu['richMenuId']));
                        array_push($actionArray, new MessageTemplateActionBuilder(
                                'link', 'link::' . $richmenu['richMenuId']));
                        $column = new CarouselColumnTemplateBuilder(
                                null, $richmenu['richMenuId'], null, $actionArray
                        );
                        array_push($columns, $column);
                        if ($i == 4 || $i == count($result['richmenus']) - 1) {
                            $builder = new TemplateMessageBuilder(
                                    'Richmenu', new CarouselTemplateBuilder($columns)
                            );
                            $builders->add($builder);
                            unset($columns);
                            $columns = Array();
                        }
                    }
                    $this->_bot->replyMessage($replyToken, $builders);
                } else {
                    $this->_bot->replyMessage($replyToken, new TextMessageBuilder('No richmenu.'));
                }
                return;

            case '取消':
                $resp = $this->_bot->replyText($replyToken, $this->_i18n->show('bot', 'undo'));
                $this->_setUserStatus($userid, self::STATUS_NONE);
                if ($resp->getHTTPStatus() != 200) {
                    error_log("Fail to reply message: {$resp->getHTTPStatus()} - {$resp->getRawBody()}");
                }
                return;
            default:
                /* Menu */
                if (substr($text, 0, 8) === 'upload::') {
                    $this->_bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($this->uploadRandomImageToRichmenu($this->_access_token, substr($text, 8))));
                } else if (substr($text, 0, 8) === 'delete::') {
                    $this->_bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($this->deleteRichmenu($this->_access_token, substr($text, 8))));
                } else if (substr($text, 0, 6) === 'link::') {
                    $this->_bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($this->linkToUser($this->_access_token, $userid, substr($text, 6))));
                }
                /* 未加入的人抓取名單用 */
                $response = $this->_bot->getProfile($userid);
                if ($response->isSucceeded()) {
                    $profile = $response->getJSONDecodedBody();
                    $user_name = $profile['displayName'];
                    //$user_name = removeEmoji($user_name);
                }

                /* Redis */
                $Redis_Status = $this->_getUserStatus($userid);
                /* Line Link To Menu */
                $AffectRow = $this->_databaseProcessor->InsertMemberInfo($userid, $user_name, $c_id);
                if ($AffectRow === true) {
                    /* Step */
                    $RichMenuId = $this->_databaseProcessor->RichMenuId($c_id);
                    if ($RichMenuId != "") {
                        $this->linkToUser($this->_access_token, $userid, $RichMenuId);
                    }
                }
            /* Line Link To Menu */
        }
    }

    /* Redis Function */

    private function _setUserStatus($userid, $status) {
        return $this->_redis->set("UserStatus-{$userid}", $status);
    }

    private function _getUserStatus($userid) {
        return $this->_redis->get("UserStatus-{$userid}");
    }

    private function _setUserAction($userid, $action) {
        return $this->_redis->set("UserAction-{$userid}", $action);
    }

    private function _getUserAction($userid) {
        return $this->_redis->get("UserAction-{$userid}");
    }

    private function isRichmenuIdValid($string) {
        if (preg_match('/^[a-zA-Z0-9-]+$/', $string)) {
            return true;
        } else {
            return false;
        }
    }

    private function createNewRichmenu1($channelAccessToken) {
        $sh = <<< EOF
  curl -X POST \
  -H 'Authorization: Bearer $channelAccessToken' \
  -H 'Content-Type:application/json' \
  -d '{"size": {"width": 2500,"height": 1686},"selected": false,"name": "「功能展示」","chatBarText": "「功能展示」","areas": [{"bounds": {"x": 0,"y": 0,"width": 833,"height": 843},"action": {"type": "postback","data": "action=1"}},{"bounds": {"x": 833,"y": 0,"width": 833,"height": 843},"action": {"type": "postback","data": "action=2"}},{"bounds": {"x": 1666,"y": 0,"width": 834,"height": 843},"action": {"type": "postback","data": "action=3"}},{"bounds": {"x": 0,"y": 843,"width": 833,"height": 843},"action": {"type": "postback","data": "action=4"}},{"bounds": {"x": 833,"y": 843,"width": 833,"height": 843},"action": {"type": "postback","data": "action=5"}},{"bounds": {"x": 1666,"y": 843,"width": 834,"height": 843},"action": {"type": "postback","data": "action=6"}}]}' https://api.line.me/v2/bot/richmenu;
EOF;
        $result = json_decode(shell_exec(str_replace('\\', '', str_replace(PHP_EOL, '', $sh))), true);
        if (isset($result['richMenuId'])) {
            return $result['richMenuId'];
        } else {
            return $result['message'];
        }
    }

    private function createNewRichmenu2($channelAccessToken) {
        $sh = <<< EOF
  curl -X POST \
  -H 'Authorization: Bearer $channelAccessToken' \
  -H 'Content-Type:application/json' \
  -d '{"size": {"width": 2500,"height": 1686},"selected": false,"name": "「功能展示-2」","chatBarText": "「功能展示-2」","areas": [{"bounds": {"x": 0,"y": 0,"width": 1666,"height": 1666},"action": {"type": "postback","data": "action=7"}},{"bounds": {"x": 1666,"y": 0,"width": 2500,"height": 843},"action": {"type": "uri","uri": "https://fun-night.com.tw/fun-night.php"}},{"bounds": {"x": 1666,"y": 843,"width": 2500,"height": 1686},"action": {"type": "postback","data": "action=8"}}]}' https://api.line.me/v2/bot/richmenu;
EOF;
        $result = json_decode(shell_exec(str_replace('\\', '', str_replace(PHP_EOL, '', $sh))), true);
        if (isset($result['richMenuId'])) {
            return $result['richMenuId'];
        } else {
            return $result['message'];
        }
    }

    private function createNewRichmenu3($channelAccessToken) {
        $sh = <<< EOF
  curl -X POST \
  -H 'Authorization: Bearer $channelAccessToken' \
  -H 'Content-Type:application/json' \
  -d '{"size": {"width": 2500,"height": 1686},"selected": false,"name": "客服連線","chatBarText": "客服連線","areas": [{"bounds": {"x": 0,"y": 0,"width": 2500,"height": 1686},"action": {"type": "postback","data": "action=998"}}]}' https://api.line.me/v2/bot/richmenu;
EOF;
        $result = json_decode(shell_exec(str_replace('\\', '', str_replace(PHP_EOL, '', $sh))), true);
        if (isset($result['richMenuId'])) {
            return $result['richMenuId'];
        } else {
            return $result['message'];
        }
    }

    private function getListOfRichmenu($channelAccessToken) {
        $sh = <<< EOF
  curl \
  -H 'Authorization: Bearer $channelAccessToken' \
  https://api.line.me/v2/bot/richmenu/list;
EOF;
        $result = json_decode(shell_exec(str_replace('\\', '', str_replace(PHP_EOL, '', $sh))), true);
        return $result;
    }

    private function deleteRichmenu($channelAccessToken, $richmenuId) {
        if (!$this->isRichmenuIdValid($richmenuId)) {
            return 'invalid richmenu id';
        }
        $sh = <<< EOF
  curl -X DELETE \
  -H 'Authorization: Bearer $channelAccessToken' \
  https://api.line.me/v2/bot/richmenu/$richmenuId
EOF;
        $result = json_decode(shell_exec(str_replace('\\', '', str_replace(PHP_EOL, '', $sh))), true);
        if (isset($result['message'])) {
            return $result['message'];
        } else {
            return 'success';
        }
    }

    private function uploadRandomImageToRichmenu($channelAccessToken, $richmenuId) {
        if (!$this->isRichmenuIdValid($richmenuId)) {
            return 'invalid richmenu id';
        }
        $imagePath = realpath('') . '/' . 'demo_menu2.png';
        $sh = <<< EOF
  curl -v -X POST https://api.line.me/v2/bot/richmenu/$richmenuId/content \
  -H "Authorization: Bearer $channelAccessToken" \
  -H "Content-Type: image/png" \
  -T $imagePath 
EOF;
//        $sh = <<< EOF
//  curl -X POST \
//  -H 'Authorization: Bearer $channelAccessToken' \
//  -H 'Content-Type: image/png' \
//  -H 'Expect:' \
//  -T $imagePath \
//  https://api.line.me/v2/bot/richmenu/$richmenuId/content
//EOF;
        $result = json_decode(shell_exec(str_replace('\\', '', str_replace(PHP_EOL, '', $sh))), true);
        if (isset($result['message'])) {
            return $result['message'];
        } else {
            return 'success. Image #0 has uploaded onto ' . $richmenuId;
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

    private function GetFileContent($messageid, $accessToken) {
        $ch = curl_init("https://api.line.me/v2/bot/message/" . $messageid . "/content");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $accessToken
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    private function CallBackCount() {
        $GetRandCode = GetRandCode();
        $CodeCount = $this->_databaseProcessor->CodeCount($GetRandCode);
        if ($CodeCount >= 1) {
            return $this->CallBackCount();
        } else {
            return $GetRandCode;
        }
    }

    private function CurlToLCS($jsonString) {
        $ch = curl_init("https://line-server-dep.chiliman.com.tw:8032/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonString);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        $result = curl_exec($ch);
        curl_close($ch);
    }

}
