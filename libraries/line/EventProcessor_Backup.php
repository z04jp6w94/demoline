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

namespace LINE\LINEBot;

use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Event\UnfollowEvent;
use LINE\LINEBot\Event\PostbackEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\StickerMessage;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\ButtonTemplateBuilder;
use LINE\LINEBot\CarouselColumnTemplateBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

class EventProcessor {
    /*
     * User status codes
     * Redis
     */

    const STATUS_NONE = 0;
    const STATUS_FILL1 = 4;
    const STATUS_FILL2 = 5;
    const STATUS_FILL3 = 6;
    const STATUS_ASK = 7;
    const STATUS_ASK_CONFIRM = 8;

    /*
     * Postback action codes
     */
    const ACTION_BUSINES_MORE1 = 1;
    const ACTION_BUSINES_MORE3 = 2;
    const ACTION_SHARE = 3;
    const ACTION_MY_ACCOUNT = 4;
    const ACTION_FILL_INFO = 5;
    const ACTION_BIND_MENU = 99;

    /*
     */

    private $_bot = null, $_databaseProcessor = null, $_redis = null, $_i18n = null;
    private $_channelId;
    private $_access_token;
    private $_userStatus = self::STATUS_NONE;
    private $_cid;

    function __construct($bot, $databaseProcessor, $redis, $i18n, $channelId, $access_token, $c_id) {
        $this->_bot = $bot;
        $this->_databaseProcessor = $databaseProcessor;
        $this->_redis = $redis;
        $this->_i18n = $i18n;
        $this->_i18n->load('bot');
        $this->_access_token = $access_token;
        $this->_channelId = $channelId;
        $this->_cid = $c_id;
    }

    public function processEvent($event) {
        if (($event instanceof FollowEvent)) {
            $this->_processFollowEvent($event);
            return;
        }

        if (($event instanceof UnfollowEvent)) {
            $this->_processUnfollowEvent($event);
            return;
        }

        if (($event instanceof PostbackEvent)) {
            $this->_processPostbackEvent($event);
            return;
        }

        if (($event instanceof TextMessage)) {
            $this->_processTextMessage($event);
            return;
        }

        if (($event instanceof StickerMessage)) {
            $this->_processStickerMessage($event);
            return;
        }
    }

    private function _processFollowEvent($event) {
        $GetMemberListCount = $this->_databaseProcessor->GetMemberListCount($event->getUserId(), $this->_cid);
        if ($GetMemberListCount == 0) {
            $this->_databaseProcessor->AddFollowMember($event->getUserId(), $this->_cid);
            $resp = $this->_bot->replyMessage($event->getReplyToken(), new TextMessageBuilder($this->_i18n->show('bot', 'FollowMessage')));
            $resp = $this->_bot->pushMessage(
                    $event->getUserId(), new TemplateMessageBuilder(
                    "填寫資料", new ButtonTemplateBuilder(
                    $this->_i18n->show('bot', 'FillData1'), $this->_i18n->show('bot', 'FillData2'), "", [
                new PostbackTemplateActionBuilder("填寫資料", "action=" . self::ACTION_FILL_INFO)
                    ]
                    )
                    )
            );
            return;
        } else if ($GetMemberListCount == 1) {
            $this->_databaseProcessor->UpdateFollowStatus($event->getUserId(), "Y");
            return;
        }
    }

    private function _processUnfollowEvent($event) {
        $this->_databaseProcessor->UpdateFollowStatus($event->getUserId(), "N");
    }

    private function _processPostbackEvent($event) {
        parse_str($event->getPostbackData(), $postbackData);
        if (isset($postbackData['action'])) {
            switch ($postbackData['action']) {
                case self::ACTION_FILL_INFO:
                    $resp = $this->_bot->replyText($event->getReplyToken(), $this->_i18n->show('bot', 'FillQ1'));
                    $this->_databaseProcessor->InsertAccTemp($event->getUserId(), $this->_cid);
                    $this->_databaseProcessor->UpdateAccData($event->getUserId(), $this->_cid);
                    $this->_setUserStatus($event->getUserId(), self::STATUS_FILL1);
                    return;
                case self::ACTION_BIND_MENU:
                    $Number = $postbackData['Number'];
                    if ($Number == 1) {
                        $this->_bot->replyMessage($event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($this->linkToUser(CHANNEL_ACCESS_TOKEN, $event->getUserId(), 'richmenu-5c1598e9bef081d4ecb0d4b8bb779c0b')));
                    } else if ($Number == 2) {
                        $this->_bot->replyMessage($event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($this->linkToUser(CHANNEL_ACCESS_TOKEN, $event->getUserId(), 'richmenu-cdf5fc07bf3d3d7ee5cece9445c6687c')));
                    } elseif ($Number == 3) {
                        $this->_bot->replyMessage($event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($this->linkToUser(CHANNEL_ACCESS_TOKEN, $event->getUserId(), 'richmenu-764b3e03f4d42e0edad22c8bb3f5524b')));
                    }
                    return;
            }
        }
    }

    private function _processTextMessage($event) {
        switch ($event->getText()) {
            case '綁定1':
                $resp = $this->_bot->replyMessage(
                        $event->getReplyToken(), new TemplateMessageBuilder(
                        "綁定一", new ButtonTemplateBuilder(
                        "是否綁定第一個?", "", "", [
                    new PostbackTemplateActionBuilder("【綁定】", "action=" . self::ACTION_BIND_MENU . '&Number=1'),
                        ]
                        )
                        )
                );
                return;
            case '綁定2':
                $resp = $this->_bot->replyMessage(
                        $event->getReplyToken(), new TemplateMessageBuilder(
                        "綁定二", new ButtonTemplateBuilder(
                        "是否綁定第二個?", "", "", [
                    new PostbackTemplateActionBuilder("【綁定】", "action=" . self::ACTION_BIND_MENU . '&Number=2'),
                        ]
                        )
                        )
                );
                return;
            case '綁定3':
                $resp = $this->_bot->replyMessage(
                        $event->getReplyToken(), new TemplateMessageBuilder(
                        "綁定三", new ButtonTemplateBuilder(
                        "是否綁定第三個?", "", "", [
                    new PostbackTemplateActionBuilder("【綁定】", "action=" . self::ACTION_BIND_MENU . '&Number=3'),
                        ]
                        )
                        )
                );
                return;
            case '[system][create]':
                $this->_bot->replyMessage($event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($this->createNewRichmenu(CHANNEL_ACCESS_TOKEN)));
                return;
            case '[system][list]':
                $this->getListOfRichmenu(CHANNEL_ACCESS_TOKEN);
                $result = $this->getListOfRichmenu(CHANNEL_ACCESS_TOKEN);
                if (isset($result['richmenus']) && count($result['richmenus']) > 0) {
                    $builders = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
                    $columns = Array();
                    for ($i = 0; $i < count($result['richmenus']); $i++) {
                        $richmenu = $result['richmenus'][$i];
                        $actionArray = array();
                        array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder(
                                'upload image', 'upload::' . $richmenu['richMenuId']));
                        array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder(
                                'delete', 'delete::' . $richmenu['richMenuId']));
                        array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder(
                                'link', 'link::' . $richmenu['richMenuId']));
                        $column = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder(
                                null, $richmenu['richMenuId'], null, $actionArray
                        );
                        array_push($columns, $column);
                        if ($i == 4 || $i == count($result['richmenus']) - 1) {
                            $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
                                    'Richmenu', new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder($columns)
                            );
                            $builders->add($builder);
                            unset($columns);
                            $columns = Array();
                        }
                    }
                    $this->_bot->replyMessage($event->getReplyToken(), $builders);
                } else {
                    $this->_bot->replyMessage($event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('No richmenu.'));
                }
                return;
            case '[system][unlink]':
                $this->_bot->replyMessage($event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($this->unlinkFromUser(CHANNEL_ACCESS_TOKEN, $event->getUserId())));
                return;
            case '[system][check]':
                $this->_bot->replyMessage($event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($this->checkRichmenuOfUser(CHANNEL_ACCESS_TOKEN, $event->getUserId())));
                return;
            case '[testtest]':
                $resp = $this->_bot->replyMessage($event->getReplyToken(), new ImagemapMessageBuilder(WEB_HOSTNAME . '/assets_rear/images/push/20171208180659', '好康來囉', new BaseSizeBuilder(1040, 1040), [
                    new ImagemapUriActionBuilder(
                            'https://www.google.com.tw/', new AreaBuilder(0, 0, 520, 1040)
                    ),
                    new ImagemapMessageActionBuilder(
                            'Fortune', new AreaBuilder(520, 0, 520, 1040)
                    ),
                        ]
                        )
                );
                return;
            case '[客服]':
                $resp = $this->_bot->replyText($event->getReplyToken(), "客服");
                $this->_databaseProcessor->InsertAccTemp($event->getUserId(), $this->_cid);
                $this->_databaseProcessor->UpdateAccData($event->getUserId(), $this->_cid);
                return;
            case '[使用說明]':
                $resp = $this->_bot->replyText($event->getReplyToken(), $this->_i18n->show('bot', 'Instructions'));
                $this->_setUserStatus($event->getUserId(), self::STATUS_NONE);
                $this->_databaseProcessor->UpdateAccData($event->getUserId(), $this->_cid);
                return;
            case '[提問]':
                $resp = $this->_bot->replyText($event->getReplyToken(), $this->_i18n->show('bot', 'Ask'));
                $this->_databaseProcessor->UpdateAccData($event->getUserId(), $this->_cid);
                $this->_setUserStatus($event->getUserId(), self::STATUS_ASK);
                return;
            case '取消':
                $this->_databaseProcessor->UpdateAccData($event->getUserId());
                $resp = $this->_bot->replyText($event->getReplyToken(), $this->_i18n->show('bot', 'undo'));
                $this->_setUserStatus($event->getUserId(), self::STATUS_NONE);
                if ($resp->getHTTPStatus() != 200) {
                    error_log("Fail to reply message: {$resp->getHTTPStatus()} - {$resp->getRawBody()}");
                }
                return;
            default:
                if ($event->getText() === 'check') {
                    $this->_bot->replyMessage($event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($this->checkRichmenuOfUser(CHANNEL_ACCESS_TOKEN, $event->getUserId())));
                } else if (substr($event->getText(), 0, 8) === 'upload::') {
                    $this->_bot->replyMessage($event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($this->uploadRandomImageToRichmenu(CHANNEL_ACCESS_TOKEN, substr($event->getText(), 8))));
                } else if (substr($event->getText(), 0, 8) === 'delete::') {
                    $this->_bot->replyMessage($event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($this->deleteRichmenu(CHANNEL_ACCESS_TOKEN, substr($event->getText(), 8))));
                } else if (substr($event->getText(), 0, 6) === 'link::') {
                    $this->_bot->replyMessage($event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($this->linkToUser(CHANNEL_ACCESS_TOKEN, $event->getUserId(), substr($event->getText(), 6))));
                } else {
                    $this->_bot->replyMessage($event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder(
                            '"create" - create new Richmenu to channel.' . PHP_EOL .
                            '"list" - show all Richmenu created via API' . PHP_EOL .
                            '"list > upload" - upload image to Richmenu. Image choosen randomly' . PHP_EOL .
                            '"list > delete" - delete Richmenu' . PHP_EOL .
                            '"list > link" - link Richmenu to user(you)' . PHP_EOL .
                            '"unlink" - remove link to Richmenu of user(you)' . PHP_EOL .
                            '"check" - show Richmenu ID linked to user(you)' . PHP_EOL
                    ));
                }
        }
        switch (strtolower($event->getText())) {
            case 'aaa':
                
                return;
            default:
                if ($this->_getUserStatus($event->getUserId()) == self::STATUS_FILL1) {
                    $this->_databaseProcessor->WriteAns($event->getUserId(), $this->_cid, $event->getText(), 1);
                    $resp = $this->_bot->replyText($event->getReplyToken(), $this->_i18n->show('bot', 'FillQ2'));
                    $this->_setUserStatus($event->getUserId(), self::STATUS_FILL2);
                    return;
                }
                if ($this->_getUserStatus($event->getUserId()) == self::STATUS_FILL2) {
                    if (filter_var($event->getText(), FILTER_VALIDATE_EMAIL)) {
                        $this->_databaseProcessor->WriteAns($event->getUserId(), $this->_cid, $event->getText(), 2);
                        $resp = $this->_bot->pushMessage($event->getUserId(), new TextMessageBuilder($this->_i18n->show('bot', 'FillQ3')));
                        $this->_setUserStatus($event->getUserId(), self::STATUS_FILL3);
                    } else {
                        $resp = $this->_bot->replyMessage($event->getReplyToken(), new TextMessageBuilder($this->_i18n->show('bot', 'MailFormat')));
                        $resp = $this->_bot->pushMessage($event->getUserId(), new TextMessageBuilder($this->_i18n->show('bot', 'FillQ2')));
                        return;
                    }
                    return;
                }
                if ($this->_getUserStatus($event->getUserId()) == self::STATUS_FILL3) {
                    if (is_numeric(trim($event->getText()))) {
                        $this->_databaseProcessor->WriteAns($event->getUserId(), $this->_cid, $event->getText(), 3);
                        $this->_databaseProcessor->UpdateMember($event->getUserId(), $this->_cid);

                        $resp = $this->_bot->replyText($event->getReplyToken(), $this->_i18n->show('bot', 'Fill_Success'));
                        $this->_databaseProcessor->UpdateAccData($event->getUserId(), $this->_cid);
                        $this->_setUserStatus($event->getUserId(), self::STATUS_NONE);
                    } else {
                        $resp = $this->_bot->replyMessage($event->getReplyToken(), new TextMessageBuilder($this->_i18n->show('bot', 'PhoneFormat')));
                        $resp = $this->_bot->pushMessage($event->getUserId(), new TextMessageBuilder($this->_i18n->show('bot', 'FillQ3')));
                        return;
                    }
                    return;
                }
                if ($this->_getUserStatus($event->getUserId()) == self::STATUS_ASK) {
                    $this->_databaseProcessor->WriteAns($event->getUserId(), $this->_cid, $event->getText(), 1);
                    $resp = $this->_bot->replyMessage(
                            $event->getReplyToken(), new TemplateMessageBuilder(
                            "問題確認", new ButtonTemplateBuilder(
                            "問題為:\n" . $event->getText() . "?\n確認請點選【發問】,否則請點選【取消】", "", "", [
                        new MessageTemplateActionBuilder("【發問】", "發問"),
                        new MessageTemplateActionBuilder("【取消】", "取消"),
                            ]
                            )
                            )
                    );
                    $this->_setUserStatus($event->getUserId(), self::STATUS_ASK_CONFIRM);
                    return;
                }
                if ($this->_getUserStatus($event->getUserId()) == self::STATUS_ASK_CONFIRM) {
                    if ($event->getText() == '發問') {
                        $this->_databaseProcessor->InsertAskQuestion($event->getUserId(), $this->_cid);
                        $this->_databaseProcessor->UpdateAccData($event->getUserId(), $this->_cid);
                        $resp = $this->_bot->replyText($event->getReplyToken(), $this->_i18n->show('bot', 'Ask_Success'));
                        $this->_setUserStatus($event->getUserId(), self::STATUS_NONE);
                    } else {
                        $resp = $this->_bot->replyText($event->getReplyToken(), $this->_i18n->show('bot', 'Click_Warning'));
                    }
                    return;
                }

                return;
            /* ProFile */
            /*
              $ProString = $this->Profile($event->getUserId(), CHANNEL_ACCESS_TOKEN);
              $ProAry = json_decode($ProString);
              $displayName = $ProAry->{"displayName"};  //Name
              $pictureUrl = $ProAry->{"pictureUrl"};   //PIC
              $ExDate = date("Ymd");
              $ExTime = date("His");
              $random = GetRandNum();
              return;
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_POST, 0);
              curl_setopt($ch, CURLOPT_URL, $pictureUrl);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
              $file_content = curl_exec($ch);
              curl_close($ch);
              $UploadDir = ROOT_PATH . MEMBER;
              MMkDir($UploadDir);
              $downloaded_file = fopen($_SERVER['DOCUMENT_ROOT'] . MEMBER . $ExDate . $ExTime . $random . '.jpg', 'w');
              fwrite($downloaded_file, $file_content);
              fclose($downloaded_file);

              $this->_databaseProcessor->WriteAns($event->getUserId(), $ExDate . $ExTime . $random . '.jpg', 4);
             */
        }
    }

    private function _processStickerMessage($event) {
        $resp = $this->_bot->replyText($event->getReplyToken(), "這是貼圖????hee??????hee??");
    }

    private function _setUserStatus($userId, $status) {
        return $this->_redis->set("UserStatus-{$userId}", $status);
    }

    private function _getUserStatus($userId) {
        return $this->_redis->get("UserStatus-{$userId}");
    }

    private function GetCode() {
        $password_len = 7;
        $password = '';
        $fNumber = microtime();
        $_num = substr(strrchr($fNumber, "."), 1);
        $word = str_replace(" ", "", $_num);
        $len = strlen($word);
        for ($i = 0; $i < $password_len; $i++) {
            $password .= $word[rand() % $len];
        }
        return $password;
    }

    private function Profile($user, $accessToken) {
        $ch = curl_init("https://api.line.me/v2/bot/profile/" . $user . "");
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

    private function createNewRichmenu($channelAccessToken) {
        $sh = <<< EOF
  curl -X POST \
  -H 'Authorization: Bearer $channelAccessToken' \
  -H 'Content-Type:application/json' \
  -d '{"size": {"width": 2500,"height": 1686},"selected": false,"name": "Controller","chatBarText": "Controller","areas": [{"bounds": {"x": 551,"y": 325,"width": 321,"height": 321},"action": {"type": "message","text": "up"}},{"bounds": {"x": 876,"y": 651,"width": 321,"height": 321},"action": {"type": "message","text": "right"}},{"bounds": {"x": 551,"y": 972,"width": 321,"height": 321},"action": {"type": "message","text": "down"}},{"bounds": {"x": 225,"y": 651,"width": 321,"height": 321},"action": {"type": "message","text": "left"}},{"bounds": {"x": 1433,"y": 657,"width": 367,"height": 367},"action": {"type": "message","text": "btn b"}},{"bounds": {"x": 1907,"y": 657,"width": 367,"height": 367},"action": {"type": "message","text": "btn a"}}]}' https://api.line.me/v2/bot/richmenu;
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

    private function checkRichmenuOfUser($channelAccessToken, $userId) {
        $sh = <<< EOF
  curl \
  -H 'Authorization: Bearer $channelAccessToken' \
  https://api.line.me/v2/bot/user/$userId/richmenu
EOF;
        $result = json_decode(shell_exec(str_replace('\\', '', str_replace(PHP_EOL, '', $sh))), true);
        if (isset($result['richMenuId'])) {
            return $result['richMenuId'];
        } else {
            return $result['message'];
        }
    }

    private function unlinkFromUser($channelAccessToken, $userId) {
        $sh = <<< EOF
  curl -X DELETE \
  -H 'Authorization: Bearer $channelAccessToken' \
  https://api.line.me/v2/bot/user/$userId/richmenu
EOF;
        $result = json_decode(shell_exec(str_replace('\\', '', str_replace(PHP_EOL, '', $sh))), true);
        if (isset($result['message'])) {
            return $result['message'];
        } else {
            return 'success';
        }
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

    private function uploadRandomImageToRichmenu($channelAccessToken, $richmenuId) {
        if (!$this->isRichmenuIdValid($richmenuId)) {
            return 'invalid richmenu id';
        }
        $randomImageIndex = rand(1, 5);
        $imagePath = realpath('') . '/' . 'controller_0' . $randomImageIndex . '.png';
        //return "1.imagePath:".$imagePath."\n2.richmenuId:".$richmenuId."\n3.randomImageIndex:".$randomImageIndex."\n4.channelAccessToken:".$channelAccessToken;
        $sh = <<< EOF
  curl -v -X POST https://api.line.me/v2/bot/richmenu/$richmenuId/content \
  -H "Authorization: Bearer $channelAccessToken" \
  -H "Content-Type: image/jpeg" \
  -T $imagePath 
EOF;
        $result = json_decode(shell_exec(str_replace('\\', '', str_replace(PHP_EOL, '', $sh))), true);
        if (isset($result['message'])) {
            return $result['message'];
        } else {
            return 'success. Image #0' . $randomImageIndex . ' has uploaded onto ' . $richmenuId;
        }
    }

    private function isRichmenuIdValid($string) {
        if (preg_match('/^[a-zA-Z0-9-]+$/', $string)) {
            return true;
        } else {
            return false;
        }
    }

}
