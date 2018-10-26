<?php

namespace FB\FBApp\EventHandler\MessengerEventHandler\MessageMessenger\bearsevenescape;

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

class BearTextMessageMessengerHandler implements EventHandler {

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
        $senderId = $this->_textMessageMessenger->getSenderId();
        $text = $this->_textMessageMessenger->getText();
        $quickReplyPayload = $this->_textMessageMessenger->getQuickReplyPayload();
        $userStatus = $this->_redis->get("UserStatus-{$senderId}");
        $userProfileAry = $this->_fbApp->get($senderId);
        if (!empty($text) && empty($quickReplyPayload)) {
            switch ($text) {
                case 'delete_button':
                    $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSENGER_PROFILE, "me", new Greeting(Greeting::TYPE_DELETE));
                    break;
                case 'started_button':
                    $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSENGER_PROFILE, "me", new GetStarted(GetStarted::TYPE_CREATE, "bear_start"));
                    break;
                case "started_text":
                    $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSENGER_PROFILE, "me", new Greeting(Greeting::TYPE_CREATE, [new LocalizedGreeting(LocalizedGreeting::TYPE_DEFAULT, "【熊熊來七逃】")]));
                    break;
                case "persistent_menu":
                    $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSENGER_PROFILE, "me", new PersistentMenu(Greeting::TYPE_CREATE, [
                        new LocalizedPersistentMenu(LocalizedPersistentMenu::TYPE_DEFAULT, FALSE, [
                            new MenuItem(MenuItem::TYPE_POSTBACK, "美食", "bear_food"),
                            new MenuItem(MenuItem::TYPE_POSTBACK, "旅遊", "bear_travel"),
                            new MenuItem(MenuItem::TYPE_POSTBACK, '旅遊商品', 'bear_travel_goods'),
                                ]
                        )])
                    );
                    break;
                case "8888":
                    $this->_delUserStatus($senderId);
                    break;
                case "test":
                    $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageText(MessageText::MESSAGING_TYPE_RESPONSE, $senderId, "給熊熊的消息"));
                    break;
                default:
                    break;
            }
        } else if (!empty($text) && !empty($quickReplyPayload)) {
            
        }
    }
}
