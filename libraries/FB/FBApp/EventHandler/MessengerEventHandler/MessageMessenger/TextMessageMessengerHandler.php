<?php

namespace FB\FBApp\EventHandler\MessengerEventHandler\MessageMessenger;

use FB\FBApp;
use FB\FBApp\Builder\MessengerBuilder\Component\LocalizedGreeting;
use FB\FBApp\Builder\MessengerBuilder\Component\LocalizedPersistentMenu;
use FB\FBApp\Builder\MessengerBuilder\Component\MenuItem;
use FB\FBApp\Builder\MessengerBuilder\Component\MessageButton;
use FB\FBApp\Builder\MessengerBuilder\Component\MessageElement\MessageElementGeneric; //HAN_new
use FB\FBApp\Builder\MessengerBuilder\GetStarted;
use FB\FBApp\Builder\MessengerBuilder\Greeting;
use FB\FBApp\Builder\MessengerBuilder\MessageAttachment;
use FB\FBApp\Builder\MessengerBuilder\MessageAttachment\Template;
use FB\FBApp\Builder\MessengerBuilder\MessageText;
use FB\FBApp\Builder\MessengerBuilder\SenderAction;
use FB\FBApp\Builder\MessengerBuilder\PersistentMenu;
use FB\FBApp\Event\MessengerEvent\MessageMessenger\TextMessageMessenger;
use FB\FBApp\EventHandler;
use FB\FBApp\EventHandler\MessengerEventHandler\MessageMessenger\bearsevenescape\BearTextMessageMessengerHandler;

class TextMessageMessengerHandler implements EventHandler {

    private $_textMessageMessenger;
    private $_fbApp = null, $_databaseProcessor = null, $_redis = null, $_i18n = null;
    private $_s3;
    private $_cId;
    private $_fbPageId;

    public function __construct(TextMessageMessenger $textMessageMessenger, $fbApp, $databaseProcessor, $redis, $i18n, $s3, $cId, $fbPageId) {
        $this->_textMessageMessenger = $textMessageMessenger;
        $this->_fbApp = $fbApp;
        $this->_databaseProcessor = $databaseProcessor;
        $this->_redis = $redis;
        $this->_i18n = $i18n;
        $this->_s3 = $s3;
        $this->_cId = $cId;
        $this->_fbPageId = $fbPageId;
    }

    public function handle() {
        $fbPageId = $this->_textMessageMessenger->getPageId();
        if ($fbPageId === "228037034339317") {
            $handler = new BearTextMessageMessengerHandler($this->_textMessageMessenger, $this->_fbApp, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_s3, $this->_cId, $this->_fbPageId);
            $handler->handle();
        } else {
            $senderId = $this->_textMessageMessenger->getSenderId();
            $text = $this->_textMessageMessenger->getText();
            $quickReplyPayload = $this->_textMessageMessenger->getQuickReplyPayload();
            $userStatus = $this->_getUserStatus($senderId);
            $userProfileAry = $this->_fbApp->get($senderId);
            if (!empty($userStatus) && $userStatus[1] === "privacy") {  //判斷有沒有讀過隱私政策
                if ($userStatus[0] === "qa_mod") {
                    $textlen = mb_strlen($text, "utf-8");
                    $redistext = mb_substr($text, 0, $textlen, "utf-8");
                    if ($textlen > 500) {
                        $text = mb_substr($text, 0, 500, "utf-8") . "...";
                    }
                    //存到DB
                    $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new SenderAction(SenderAction::MESSAGING_TYPE_RESPONSE, $senderId, SenderAction::SENDER_ACTION_TYPING_ON));
                    $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_BUTTON, [
                        'text' => "您的問題為：\n" . $text . "？\n確認請點選【確認】,否則請點選【取消】",
                        'buttons' => [
                            new MessageButton(MessageButton::TYPE_POSTBACK, ["title" => "【確認】", "payload" => "qa_mod_ask"]),
                            new MessageButton(MessageButton::TYPE_POSTBACK, ["title" => "【取消】", "payload" => "qa_mod_cancel"])
                        ]
                    ])));
                    $this->_setUserStatus($senderId, array("qa_mod", "privacy", $redistext));
                } else if (!empty($text) && empty($quickReplyPayload)) {
                    switch ($text) {
                        case 'started_button':
                            $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSENGER_PROFILE, "me", new GetStarted(GetStarted::TYPE_CREATE, "linebar_start"));
                            break;
                        case "started_text":
                            $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSENGER_PROFILE, "me", new Greeting(Greeting::TYPE_CREATE, [new LocalizedGreeting(LocalizedGreeting::TYPE_DEFAULT, "賴吧")]));
                            echo "ok";
                            break;
                        case "persistent_menu":
                            $Bitly = new \BitlyShortAPI();
                            $md_mod5url = $Bitly->BitlyShort("https://docs.google.com/forms/d/1QFOlkB1p-e9_FVrOeIg4jI-g3jFUvSvz7JK93D6Yvqk/edit?ts=5ad6be70");
                            $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSENGER_PROFILE, "me", new PersistentMenu(Greeting::TYPE_CREATE, [
                                new LocalizedPersistentMenu(LocalizedPersistentMenu::TYPE_DEFAULT, FALSE, [
                                    new MenuItem(MenuItem::TYPE_POSTBACK, "賴吧介紹", "linebar_intro"),
                                    new MenuItem(MenuItem::TYPE_NESTED, "模組類型", [
                                        new MenuItem(MenuItem::TYPE_NESTED, "行銷模組", [
                                            new MenuItem(MenuItem::TYPE_POSTBACK, '抽抽樂', 'md_mod1'),
                                            new MenuItem(MenuItem::TYPE_POSTBACK, '發票抽獎', 'md_mod2'),
                                            new MenuItem(MenuItem::TYPE_POSTBACK, '分享換好康', 'md_mod3'),
                                            new MenuItem(MenuItem::TYPE_POSTBACK, '優惠卷', 'md_mod4'),
                                            new MenuItem(MenuItem::TYPE_WEB_URL, '問卷調查', $md_mod5url),
                                                ]),
                                        new MenuItem(MenuItem::TYPE_POSTBACK, '電商模組', 'ec_mod'),
                                        new MenuItem(MenuItem::TYPE_POSTBACK, '提問模組', 'qa_mod')
                                            ]),
                                    new MenuItem(MenuItem::TYPE_POSTBACK, '實際案例', 'case'),
                                        ]
                                )])
                            );
                            break;
                        case "8888":
                            $this->_delUserStatus($senderId);
                            break;
                        default:
                            break;
                    }
                    $queryString = $text;
                    $sessionId = session_id();
                    $speech = $this->DialogflowQuery($queryString, $sessionId);
                    $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new SenderAction(SenderAction::MESSAGING_TYPE_RESPONSE, $senderId, SenderAction::SENDER_ACTION_TYPING_ON));
                    $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageText(MessageText::MESSAGING_TYPE_RESPONSE, $senderId, $speech));
                } else if (!empty($text) && !empty($quickReplyPayload)) {
                    $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new SenderAction(SenderAction::MESSAGING_TYPE_RESPONSE, $senderId, SenderAction::SENDER_ACTION_TYPING_ON));
                    switch ($quickReplyPayload) {
                        case 'linebar_intro':
                            $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_GENERIC, [
                                'elements' => [
                                    new MessageElementGeneric("賴吧是什麼？", "「賴吧」為一個CRM軟體系統，主要架構於 LINE官方帳號與FB粉絲頁及messenger中管理客戶資料。能夠使用現下最有效的平台，更完善的精準行銷。點選下方按鈕了解更多～", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/LINEBAR_INTRO/what.jpg", [
                                        new MessageButton(MessageButton::TYPE_POSTBACK, ["title" => "點擊我", "payload" => "linebar_intro_what"])
                                            ]),
                                    new MessageElementGeneric("賴吧特色", "市面唯一跨平台雙程式管理，協助用戶管理與客戶的互動，打造您獨特的精準銷售體驗，拉近與潛在客戶的距離，拓展全新準確商機。點選下方按鈕了解更多～", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/LINEBAR_INTRO/feature.jpg", [
                                        new MessageButton(MessageButton::TYPE_POSTBACK, ["title" => "點擊我", "payload" => "linebar_intro_feature"])
                                            ]),
                                    new MessageElementGeneric("賴吧功能", "賴吧提供多樣化的模組內容，可以依據不同需求選擇您想要的方案。點選下方按鈕以了解更多", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/LINEBAR_INTRO/function.jpg", [
                                        new MessageButton(MessageButton::TYPE_POSTBACK, ["title" => "點擊我", "payload" => "linebar_intro_function"])
                                            ]),
                                    new MessageElementGeneric("關於我們", "我們是齊力樂門科技，提供LINE官方帳號，FaceBook粉絲頁與Messenger等社群系統話與AI聊天機器人建置服務，協助您的產業運用科技的技術，將產樣更加推廣行銷出去。", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/LINEBAR_INTRO/newabout1.jpg", [
                                        new MessageButton(MessageButton::TYPE_POSTBACK, ["title" => "點擊我", "payload" => "linebar_intro_about"])
                                            ])
                                ]
                            ])));
                            $this->_setUserStatus($senderId, array("", "privacy", ""));
                            break;
                        default:
                            break;
                    }
                }
            } else {            //沒讀過隱私權
                if (!empty($text)) {
                    $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new SenderAction(SenderAction::MESSAGING_TYPE_RESPONSE, $senderId, SenderAction::SENDER_ACTION_TYPING_ON));
                    $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageText(MessageText::MESSAGING_TYPE_RESPONSE, $senderId, "Hi，" . $userProfileAry['last_name'] . $userProfileAry['first_name'] . PHP_EOL . "感謝您的回應，當您開始使用本官方帳號服務時，即表示您信賴並同意本服務對您個人資訊的處理方式。" . PHP_EOL . "為協助您瞭解本服務收集的資料類型以及資料用途，請撥冗詳閱" . PHP_EOL . "《隱私權條款》：https://goo.gl/6ffcJj"));
                    $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new SenderAction(SenderAction::MESSAGING_TYPE_RESPONSE, $senderId, SenderAction::SENDER_ACTION_TYPING_ON));
                    $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_BUTTON, [
                        'text' => "你知道什麼是CRM嗎？",
                        'buttons' => [
                            new MessageButton(MessageButton::TYPE_POSTBACK, ["title" => "【知道】", "payload" => "scrm_know"]),
                            new MessageButton(MessageButton::TYPE_POSTBACK, ["title" => "【不知道】", "payload" => "scrm_unknow"])
                        ]
                    ])));
                    $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                }
            }
        }
    }

    /* Redis Function */

    private function _setUserStatus($key, $valAry) {
        //0=>問答狀態,1=>是否看過隱私權,2=>問答的問題
        $jsonStr = json_encode($valAry);
        return $this->_redis->set("UserStatus-{$key}", $jsonStr);
    }

    private function _getUserStatus($key) {
        $jsonStr = $this->_redis->get("UserStatus-{$key}");
        return json_decode($jsonStr);
    }

    private function _delUserStatus($key) {
        return $this->_redis->del("UserStatus-{$key}");
    }

    private function privateDeclare($senderId, $last_name, $first_name) {
        //存到DB
        $this->_databaseProcessor->InsertMemberFBMessengerIDInfo($senderId, $last_name . $first_name);
//        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageText(MessageText::MESSAGING_TYPE_RESPONSE, $senderId, "Hi，" . $last_name . $first_name . PHP_EOL . "感謝您加入本官方帳號！當您開始使用本官方帳號服務時，即表示您信賴並同意本服務對您個人資訊的處理方式。為了協助您瞭解本服務收集的資料類型以及這些資料的用途，請撥冗詳閱《隱私權條款》：https://goo.gl/6ffcJj"));
        $this->_setUserStatus($senderId, array("", "privacy", ""));
    }

    /* Dialogflow */

    private function DialogflowQuery($queryString, $sessionId) {
        $NLP_CLIENT_ACCESS_TOKEN = NLP_CLIENT_ACCESS_TOKEN;
        $NLP_DEP_ACCESS_TOKEN = NLP_DEP_ACCESS_TOKEN;

        $postData = array('lang' => "zh-TW", 'query' => $queryString, 'sessionId' => $sessionId, 'action' => "input.welcome", 'timezone' => "Asia/Hong_Kong");
        $jsonData = json_encode($postData);
        $ch = curl_init("https://api.dialogflow.com/v1/query?v=20150910");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $NLP_CLIENT_ACCESS_TOKEN,
            'Content-Type: application/json'
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($result);
        $speech = $output->{'result'}->{'fulfillment'}->{'messages'}[0]->{'speech'};
        return $speech;
    }

    private function CurlToLCS($jsonString) {
        $ch = curl_init("https://line-server-dep.chiliman.com.tw:8033/");
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
