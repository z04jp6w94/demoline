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
use LINE\LINEBot\Event\PostbackEvent;
use SCRM\BOT\EventHandler;
use SCRM\BOT\EventProcessor;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
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

class PostbackEventHandler implements EventHandler {
    /*
     * User status codes
     * Redis
     */

    const STATUS_NONE = 0;
    const STATUS_ORDER_TIME = 1;

    /*
     * Postback action codes
     */
    const ACTION_TEXT = 1;
    const ACTION_IMAGE = 2;
    const ACTION_CAROUSEL = 3;
    const ACTION_LIFF = 4;
    const ACTION_GIFT = 5;
    const ACTION_MENU2 = 6;
    const ACTION_ORDER = 7;
    const ACTION_MENU1 = 8;
    const ACTION_ORDER_TIME = 9;

    /** @var LINEBot $bot */
    private $_bot;

    /** @var PostbackEvent $postbackEvent */
    private $postbackEvent;
    private $_databaseProcessor;
    private $_redis;
    private $_i18n;
    private $_channelId;
    private $_access_token;
    private $_s3;

    /**
     * PostbackEventHandler constructor.
     * @param PostbackEvent $postbackEvent
     * @param LINEBot $bot
     * @param DatabaseProcessor $databaseProcessor
     * @param \Redis $redis
     * @param I18n $i18n
     * @param integer $channelId
     */
    public function __construct(PostbackEvent $postbackEvent, $bot, $databaseProcessor, $redis, $i18n, $channelId, $access_token, $s3) {
        $this->postbackEvent = $postbackEvent;
        $this->_bot = $bot;
        $this->_databaseProcessor = $databaseProcessor;
        $this->_redis = $redis;
        $this->_i18n = $i18n;
        $this->_channelId = $channelId;
        $this->_access_token = $access_token;
        $this->_s3 = $s3;
    }

    public function handle() {
        $userid = $this->postbackEvent->getUserId();
        $replyToken = $this->postbackEvent->getReplyToken();
        $c_id = "1029288508";
        parse_str($this->postbackEvent->getPostbackData(), $postbackData);
        $GET_LOGIN_ID = $this->_databaseProcessor->GET_LOGIN_ID($c_id);
        $Bitly = new \BitlyShortAPI();
        /* Redis */
        $Redis_Status = $this->_getUserStatus($userid);
        /* Redis */
        /* 未加入的人抓取名單用 */
        $response = $this->_bot->getProfile($userid);
        if ($response->isSucceeded()) {
            $profile = $response->getJSONDecodedBody();
            $user_name = $profile['displayName'];
//$user_name = removeEmoji($user_name);
        }
        /* Line Link To Menu ? */
        $AffectRow = $this->_databaseProcessor->InsertMemberInfo($userid, $user_name, $c_id);
        if ($AffectRow === true) {
            /* Step */
            $RichMenuId = $this->_databaseProcessor->RichMenuId($c_id);
            if ($RichMenuId != "") {
                $this->linkToUser($this->_access_token, $userid, $RichMenuId);
            }
        }
        /* Line Link To Menu */
        if (isset($postbackData['action'])) {
            switch ($postbackData['action']) {
                case self::ACTION_TEXT:
                    $greetingStr = "{$user_name}您好: 請點選其他按鈕測試功能~";
                    $this->_bot->replyMessage($replyToken, new TextMessageBuilder($greetingStr));
                    return;
                case self::ACTION_IMAGE:
                    $IMG_ROOT = WEB_HOSTNAME . "/assets_front/images/imagemap";
                    $LINE_URL = "https://fun-night.com.tw/fun-night.php";
                    $LINE_URL = $Bitly->BitlyShort($LINE_URL);
                    $resp = $this->_bot->replyMessage(
                            $replyToken, new ImagemapMessageBuilder(
                            $IMG_ROOT, "連接網址", new BaseSizeBuilder(1040, 1040), [
                        new ImagemapUriActionBuilder(
                                $LINE_URL, new AreaBuilder(0, 0, 1040, 1040)
                        )
                            ]
                            )
                    );
                    return;
                case self::ACTION_CAROUSEL:
                    $builders = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
                    $columns = Array();
                    $TITLE_ARY = Array('1', '2', '3', '4', '5');
                    $CONTENT_ARY = Array('1', '2', '3', '4', '5');
                    $IMG_URL_ARY = Array('1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg');
                    $ACTION_ARY = Array('https://www.google.com.tw/', 'https://www.google.com.tw/', 'https://www.google.com.tw/', 'https://www.google.com.tw/', 'https://www.google.com.tw/');
                    $IMG_ROOT = WEB_HOSTNAME . "/assets_front/images/carousel/";
                    for ($i = 0; $i < 5; $i++) {
                        $TITLE = $TITLE_ARY[$i];
                        $CONTENT = $CONTENT_ARY[$i];
                        $IMG_URL = $IMG_ROOT . $IMG_URL_ARY[$i];
                        $ACTION = $ACTION_ARY[$i];
                        $actionArray = array();
                        array_push($actionArray, new UriTemplateActionBuilder('【連結】', $ACTION));
                        $column = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder(
                                $TITLE, $CONTENT, $IMG_URL, $actionArray
                        );
                        array_push($columns, $column);
                        if ($i == 5 || $i == 5 - 1) {
                            $builder = new TemplateMessageBuilder(
                                    "應用範例", new CarouselTemplateBuilder($columns)
                            );
                            $builders->add($builder);
                            unset($columns);
                            $columns = Array();
                        }
                    }
                    $this->_bot->replyMessage($replyToken, $builders);
                    return;
                case self::ACTION_LIFF:
                    $resp = $this->_bot->replyMessage(
                            $replyToken, new TemplateMessageBuilder(
                            "Liff Web", new ButtonTemplateBuilder(
                            "Liff Web", "", "", [
                        new UriTemplateActionBuilder("【連接LIFF WEB】", "line://app/1615603328-g6WNyxwK"),
                            ]
                            )
                            )
                    );
                    return;
                case self::ACTION_GIFT:
                    $builder = array(
                        "type" => "flex",
                        "altText" => "this is a flex message",
                        "contents" => array(
                            "type" => "bubble",
                            "header" => array(
                                "type" => "box",
                                "layout" => "horizontal",
                                "contents" => array(
                                    array(
                                        "type" => "text",
                                        "text" => "Welcome",
                                        "weight" => "bold",
                                        "color" => "#aaaaaa",
                                        "size" => "sm"
                                    )
                                )
                            ),
                            "hero" => array(
                                "type" => "image",
                                "url" => "https://scdn.line-apps.com/n/channel_devcenter/img/fx/01_4_news.png",
                                "size" => "full",
                                "aspectRatio" => "20:13",
                                "action" => array(
                                    "type" => "uri",
                                    "uri" => "http://linecorp.com/"
                                )
                            ),
                            "body" => array(
                                "type" => "box",
                                "layout" => "horizontal",
                                "spacing" => "md",
                                "contents" => array(
                                    array(
                                        "type" => "box",
                                        "layout" => "vertical",
                                        "flex" => 1,
                                        "contents" => array(
                                            array(
                                                "type" => "image",
                                                "url" => "https://scdn.line-apps.com/n/channel_devcenter/img/fx/02_1_news_thumbnail_1.png",
                                                "aspectMode" => "cover",
                                                "aspectRatio" => "4:3",
                                                "size" => "sm",
                                                "gravity" => "bottom"
                                            ),
                                            array(
                                                "type" => "image",
                                                "url" => "https://scdn.line-apps.com/n/channel_devcenter/img/fx/02_1_news_thumbnail_2.png",
                                                "aspectMode" => "cover",
                                                "aspectRatio" => "4:3",
                                                "margin" => "md",
                                                "size" => "sm"
                                            )
                                        )
                                    ),
                                    array(
                                        "type" => "box",
                                        "layout" => "vertical",
                                        "flex" => 2,
                                        "contents" => array(
                                            array(
                                                "type" => "text",
                                                "text" => "7 Things to Know for Today",
                                                "gravity" => "top",
                                                "size" => "xs",
                                                "flex" => 1
                                            ),
                                            array(
                                                "type" => "separator"
                                            ),
                                            array(
                                                "type" => "text",
                                                "text" => "Hay fever goes wild",
                                                "gravity" => "center",
                                                "size" => "xs",
                                                "flex" => 2
                                            ),
                                            array(
                                                "type" => "separator"
                                            ),
                                            array(
                                                "type" => "text",
                                                "text" => "LINE Pay Begins Barcode Payment Service",
                                                "gravity" => "center",
                                                "size" => "xs",
                                                "flex" => 2
                                            ),
                                            array(
                                                "type" => "separator"
                                            ),
                                            array(
                                                "type" => "text",
                                                "text" => "LINE Adds LINE Wallet",
                                                "gravity" => "bottom",
                                                "size" => "xs",
                                                "flex" => 1
                                            )
                                        )
                                    )
                                )
                            ),
                            "footer" => array(
                                "type" => "box",
                                "layout" => "horizontal",
                                "contents" => array(
                                    array(
                                        "type" => "button",
                                        "action" => array(
                                            "type" => "uri",
                                            "label" => "More",
                                            "uri" => "https://linecorp.com"
                                        )
                                    )
                                )
                            )
                        )
                    );

                    $post_data = array("to" => $userid, "messages" => array($builder));

                    $ch = curl_init("https://api.line.me/v2/bot/message/push");
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
                    return;
                case self::ACTION_MENU2:
                    $this->linkToUser($this->_access_token, $userid, "richmenu-ecd7eaba45636b0485f3ed29096fe5f2");
                    return;
                case self::ACTION_ORDER:
                    $builders = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
                    $columns = Array();
                    $TITLE_ARY = Array('餐廳1號', '餐廳2號', '餐廳3號');
                    $CONTENT_ARY = Array('台北寒舍艾美酒店', '豐Food', '飯店');
                    $IMG_URL_ARY = Array('order1.png', 'order2.png', 'order3.png');
                    $ACTION_ARY = Array('9', '9', '9');
                    $ACTION_TIME_ARY = Array('1', '2', '3');
                    $IMG_ROOT = WEB_HOSTNAME . "/assets_front/images/order/";
                    for ($i = 0; $i < 3; $i++) {
                        $TITLE = $TITLE_ARY[$i];
                        $CONTENT = $CONTENT_ARY[$i];
                        $IMG_URL = $IMG_ROOT . $IMG_URL_ARY[$i];
                        $ACTION = $ACTION_ARY[$i];
                        $ACTION_TIME = $ACTION_TIME_ARY[$i];
                        $actionArray = array();
                        array_push($actionArray, new PostbackTemplateActionBuilder('【訂位去】', "action={$ACTION}&timepicker={$ACTION_TIME}"));
                        $column = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder(
                                $TITLE, $CONTENT, $IMG_URL, $actionArray
                        );
                        array_push($columns, $column);
                        if ($i == 3 || $i == 3 - 1) {
                            $builder = new TemplateMessageBuilder(
                                    "訂位訂餐系統", new CarouselTemplateBuilder($columns)
                            );
                            $builders->add($builder);
                            unset($columns);
                            $columns = Array();
                        }
                    }
                    $this->_bot->replyMessage($replyToken, $builders);
                    return;
                case self::ACTION_MENU1:
                    $this->linkToUser($this->_access_token, $userid, "richmenu-8ec951248b78c1e616e87fa521a47d50");
                    return;
                case self::ACTION_ORDER_TIME:
                    $timepickerType = $postbackData['timepicker'];
                    $this->_setUserStatus($userid, self::STATUS_ORDER_TIME);
                    $this->_setUserAction($userid, $timepickerType);
                    $resp = $this->_bot->replyMessage(
                            $replyToken, new TemplateMessageBuilder(
                            "訂位時段", new ButtonTemplateBuilder(
                            "訂位時段", "", "", [
                        new PostbackTemplateActionBuilder("【時段1】", "action="),
                        new PostbackTemplateActionBuilder("【時段2】", "action="),
                        new PostbackTemplateActionBuilder("【時段3】", "action="),
                        new PostbackTemplateActionBuilder("【時段4】", "action="),
                            ]
                            )
                            )
                    );
                    return;
            }
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

}
