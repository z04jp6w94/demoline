<?php

namespace FB\FBApp\EventHandler\MessengerEventHandler\MessageMessenger\bearsevenescape;

use FB\FBApp;
use FB\FBApp\Builder\MessengerBuilder\Component\MessageButton;
use FB\FBApp\Builder\MessengerBuilder\Component\MessageElement\MessageElementGeneric;
use FB\FBApp\Builder\MessengerBuilder\Component\MessageElement\MessageElementList;
use FB\FBApp\Builder\MessengerBuilder\Component\MessageElement\MessageElementMedia;
use FB\FBApp\Builder\MessengerBuilder\Component\QuickReplyButton;
use FB\FBApp\Builder\MessengerBuilder\MessageAttachment;
use FB\FBApp\Builder\MessengerBuilder\MessageAttachment\Image;
use FB\FBApp\Builder\MessengerBuilder\MessageAttachment\Template;
use FB\FBApp\Builder\MessengerBuilder\MessageText;
use FB\FBApp\Builder\MessengerBuilder\SenderAction;
use FB\FBApp\Event\MessengerEvent\PostbackMessenger;
use FB\FBApp\EventHandler;

class BearPostbackMessengerHandler implements EventHandler {

    private $_postbackMessenger;
    private $_fbApp = null, $_databaseProcessor = null, $_redis = null, $_i18n = null;
    private $_s3;
    private $_cId;
    private $_fbPageId;

    public function __construct(PostbackMessenger $postbackMessenger, $fbApp, $databaseProcessor, $redis, $i18n, $s3, $cId, $fbPageId) {
        $this->_postbackMessenger = $postbackMessenger;
        $this->_fbApp = $fbApp;
        $this->_databaseProcessor = $databaseProcessor;
        $this->_redis = $redis;
        $this->_i18n = $i18n;
        $this->_s3 = $s3;
        $this->_cId = $cId;
        $this->_fbPageId = $fbPageId;
    }

    public function handle() {
        $fbPageId = $this->_postbackMessenger->getPageId();
        $senderId = $this->_postbackMessenger->getSenderId();
        $payLoad = $this->_postbackMessenger->getPayload();
        $userStatus = $this->_redis->get("UserStatus-{$senderId}");
        $userProfileAry = $this->_fbApp->get($senderId);
        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES,"me",new SenderAction(SenderAction::MESSAGING_TYPE_RESPONSE, $senderId, SenderAction::SENDER_ACTION_TYPING_ON));
        switch ($payLoad) {
            case "bear_start":
                $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageText(MessageText::MESSAGING_TYPE_RESPONSE, $senderId, $userProfileAry['last_name'] . $userProfileAry['first_name'] . "您好" . PHP_EOL . "謝謝你對【熊熊來七逃】的支持。" . PHP_EOL . PHP_EOL . "👇快點選左下角選單，有更多資訊讓你參考喔！"));
                break;
            case "bear_food":
                $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_GENERIC, [
                    'elements' => [
                        new MessageElementGeneric("敲我烘培工作室", "", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/BEAR/bear0004.jpg", [
                            new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/beargotravelling/videos/384797298663289/"])
                                ]),
                        new MessageElementGeneric("青花驕麻辣火鍋", "", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/BEAR/bear0006.jpg", [
                            new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/beargotravelling/videos/381722675637418/"])
                                ]),
                        new MessageElementGeneric("好盤美式廚房", "", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/BEAR/bear0007.jpg", [
                            new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/beargotravelling/videos/380954859047533/"])
                                ]),
                        new MessageElementGeneric("麗星郵輪", "", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/BEAR/bear0012.jpg", [
                            new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/beargotravelling/videos/355911608218525/"])
                                ]),
                        new MessageElementGeneric("泰國-Royal Cliff Hotels Group", "", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/BEAR/bear0008.jpg", [
                            new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/beargotravelling/videos/376933469449672/"])
                                ]),
                        new MessageElementGeneric("帝王食補", "", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/BEAR/bear0001.jpg", [
                            new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/beargotravelling/videos/388042825005403/"])
                                ])
                    ]
                ])));
                $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageText(MessageText::MESSAGING_TYPE_RESPONSE, $senderId, "下方選項有更多資訊喔！".PHP_EOL."或是你也可以跟熊熊聊天～"));
                break;
            case "bear_travel":
                $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_GENERIC, [
                    'elements' => [
                        new MessageElementGeneric("海芋", "", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/BEAR/bear0002.jpg", [
                            new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/beargotravelling/videos/388562838286735/"])
                                ]),
                        new MessageElementGeneric("紫藤花", "", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/BEAR/bear0005.jpg", [
                            new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/beargotravelling/videos/382978822178470/"])
                                ]),
                        new MessageElementGeneric("沖繩", "", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/BEAR/bear0003.jpg", [
                            new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/beargotravelling/videos/369497703526582/"])
                                ]),
                        new MessageElementGeneric("泰國", "", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/BEAR/bear0009.jpg", [
                            new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/beargotravelling/videos/376016139541405/"])
                                ])
                    ]
                ])));
                $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageText(MessageText::MESSAGING_TYPE_RESPONSE, $senderId, "下方選項有更多資訊喔！".PHP_EOL."或是你也可以跟熊熊聊天～"));
                break;
            case "bear_gift":
                $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_GENERIC, [
                    'elements' => [
                        new MessageElementGeneric("青花驕麻辣火鍋", "", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/BEAR/bear0006.jpg", [
                            new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/beargotravelling/videos/384797298663289/"])
                                ]),
                        new MessageElementGeneric("敲我烘培工作室", "", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/BEAR/bear0004.jpg", [
                            new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/beargotravelling/videos/381722675637418/"])
                                ])
                    ]
                ])));
                $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageText(MessageText::MESSAGING_TYPE_RESPONSE, $senderId, "下方選項有更多資訊喔！".PHP_EOL."或是你也可以跟熊熊聊天～"));
                break;
            case "bear_travel_goods":
                $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_GENERIC, [
                    'elements' => [
                        new MessageElementGeneric("飛機枕頭", "", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/BEAR/bear0010.jpg", [
                            new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/beargotravelling/videos/375326632943689/"])
                                ]),
                        new MessageElementGeneric("飛機護頸", "", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/BEAR/bear0014.jpg", [
                            new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/beargotravelling/videos/329574930852193/"])
                                ]),
                        new MessageElementGeneric("行李箱", "", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/BEAR/bear0013.jpg", [
                            new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/beargotravelling/videos/335175250292161/"])
                                ]),
                        new MessageElementGeneric("真空收納機", "", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/BEAR/bear0015.jpg", [
                            new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/beargotravelling/videos/327016974441322/"])
                                ]),
                        new MessageElementGeneric("筆記本", "", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/BEAR/bear0011.jpg", [
                            new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/beargotravelling/videos/359375157872170/"])
                                ])
                    ]
                ])));
                $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageText(MessageText::MESSAGING_TYPE_RESPONSE, $senderId, "下方選項有更多資訊喔！".PHP_EOL."或是你也可以跟熊熊聊天～"));
                break;
            default:
                break;
        }
    }

}
