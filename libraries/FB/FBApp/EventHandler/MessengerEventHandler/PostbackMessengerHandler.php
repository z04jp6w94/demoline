<?php

namespace FB\FBApp\EventHandler\MessengerEventHandler;

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
use FB\FBApp\Builder\MessengerBuilder\QuickReply;
use FB\FBApp\Builder\MessengerBuilder\SenderAction;
use FB\FBApp\Event\MessengerEvent\PostbackMessenger;
use FB\FBApp\EventHandler;
use FB\FBApp\EventHandler\MessengerEventHandler\MessageMessenger\bearsevenescape\BearPostbackMessengerHandler;

class PostbackMessengerHandler implements EventHandler {

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
        if ($fbPageId === "228037034339317") {
            $handler = new BearPostbackMessengerHandler($this->_postbackMessenger, $this->_fbApp, $this->_databaseProcessor, $this->_redis, $this->_i18n, $this->_s3, $this->_cId, $this->_fbPageId);
            $handler->handle();
        } else {
            $senderId = $this->_postbackMessenger->getSenderId();
            $payLoad = $this->_postbackMessenger->getPayload();
            $userStatus = $this->_getUserStatus($senderId);
            $userProfileAry = $this->_fbApp->get($senderId);
            $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES,"me",new SenderAction(SenderAction::MESSAGING_TYPE_RESPONSE, $senderId, SenderAction::SENDER_ACTION_TYPING_ON));
            switch ($payLoad) {
                case "linebar_start":
                    $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    break;
                case "linebar_intro":
                    if (empty($userStatus) || $userStatus[1] !== "privacy") {
                        $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    } else {
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
                    }
                    break;
                case "linebar_intro_what":
                    if (empty($userStatus) || $userStatus[1] !== "privacy") {
                        $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    } else {
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_BUTTON, [
                            'text' => "“ 賴吧“ 為一個架構在 LINE官方帳號與FB粉絲頁及messenger的 CRM軟體系統。企業透過 “賴吧 Social CRM” 及 API 連結其官方帳號或粉絲頁，一站式管理與客戶的互動，包括『訊息推播』『分眾推播』『顧客喜好分析』與『客戶服務等作業』，將打造您獨特的精準銷售體驗，拉近與潛在客戶的距離，拓展全新準確商機。如果您已磨拳擦掌，準備讓您的品牌發光發熱，快與我們聯繫！您可以透過選單右下方的『提問』或加入齊力樂門科技LINE@官方帳號與我們聯繫。LINE@：https://line.me/R/ti/p/%40tsz6789s",
                            'buttons' => [
                                new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "【官網連結】", "url" => "http://www.powerline.com.tw/"])
                            ]
                        ])));
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES,"me",new SenderAction(SenderAction::MESSAGING_TYPE_RESPONSE, $senderId, SenderAction::SENDER_ACTION_TYPING_ON));
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_MEDIA, [
                            'elements' => [
                                new MessageElementMedia(MessageElementMedia::MEDIA_TYPE_VIDEO, "https://www.facebook.com/Chiliman.powerline/videos/872839522903809/", [
                                    new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "【賴吧影片連結】", "url" => "https://www.youtube.com/watch?v=i9Nw3NkMHLM"])
                                        ])
                            ]
                                ], [
                            new QuickReplyButton(QuickReplyButton::CONTENT_TYPE_TEXT, "回到賴吧介紹", "linebar_intro", ""),
                                ]
                        )));
                        $this->_setUserStatus($senderId, array("", "privacy", ""));
                    }
                    break;
                case "linebar_intro_feature":
                    if (empty($userStatus) || $userStatus[1] !== "privacy") {
                        $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    } else {
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_IMAGE, new Image(Image::TYPE_ATTACHMENT_ID, "875458662641895", [
                            new QuickReplyButton(QuickReplyButton::CONTENT_TYPE_TEXT, "回到賴吧介紹", "linebar_intro", ""),
                        ])));
                        $this->_setUserStatus($senderId, array("", "privacy", ""));
                    }
                    break;
                case "linebar_intro_function":
                    if (empty($userStatus) || $userStatus[1] !== "privacy") {
                        $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    } else {
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_IMAGE, new Image(Image::TYPE_ATTACHMENT_ID, "875458439308584", [
                            new QuickReplyButton(QuickReplyButton::CONTENT_TYPE_TEXT, "回到賴吧介紹", "linebar_intro", ""),
                        ])));
                        $this->_setUserStatus($senderId, array("", "privacy", ""));
                    }
                    break;
                case "linebar_intro_about":
                    if (empty($userStatus) || $userStatus[1] !== "privacy") {
                        $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    } else {
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_GENERIC, [
                            'elements' => [
                                new MessageElementGeneric("About Us", "我們是齊力樂門科技，我們提供LINE官方帳號，FaceBook粉絲頁與Messenger等社群系統話與AI聊天機器人建置", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/LINEBAR_INTRO/newabout1.jpg", [
                                    new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "http://www.chiliman.com.tw/"])
                                        ]),
                                new MessageElementGeneric("應用案例", "點選下方按鈕，可以實際體驗我們的應用案例", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/LINEBAR_INTRO/about2.jpg", [
                                    new MessageButton(MessageButton::TYPE_POSTBACK, ["title" => "點擊我", "payload" => "linebar_intro_about_case"])
                                        ]),
                                new MessageElementGeneric("聯絡我們", "點選下方按鈕，透過我們的LINE@官方帳號與我們聯繫或撥打(02)2272-2300", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/LINEBAR_INTRO/newabout1.jpg", [
                                    new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://line.me/R/ti/p/@tsz6789s"])
                                        ])
                            ]
                        ])));
                        $this->_setUserStatus($senderId, array("", "privacy", ""));
                    }
                    break;
                case "linebar_intro_about_case":
                    if (empty($userStatus) || $userStatus[1] !== "privacy") {
                        $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    } else {
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_GENERIC, [
                            'elements' => [
                                new MessageElementGeneric("費列羅業務巡訪bot", "知名巧克力品牌，透過【業務尋訪系統】協助公司掌握業務人員的工作進度及效率。", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/CASE/ferrero_horizontal.jpg", [
                                    new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/1464586273564575/"])
                                        ])
                                ,
                                new MessageElementGeneric("巧虎好朋友（巧連智）", "日本最大教育集團，日商倍樂生台北分公司所屬的粉絲團，透過舉辦線上活動來匯集人氣，並與粉絲更進一步的互動。", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/CASE/benesse_horizontal.jpg", [
                                    new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/BenesseTaiwan/photos/a.1082123465208170.1073741828.1075557715864745/1645632772190567/?type=3&theater"])
                                        ])
                            ]
                        ])));
                        $this->_setUserStatus($senderId, array("", "privacy", ""));
                    }
                    break;
                case "ec_mod":
                    if (empty($userStatus) || $userStatus[1] !== "privacy") {
                        $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    } else {
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_GENERIC, [
                            'elements' => [
                                new MessageElementGeneric("電商模組體驗_一次展現多樣商品", "透過“賴吧”後台，不需要程式人員，您就可以自定義商品展示給客戶的樣式。按“點擊我“感受您的客戶端體驗", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/EC_MOD/multiple.jpg", [
                                    new MessageButton(MessageButton::TYPE_POSTBACK, ["title" => "點擊我", "payload" => "ec_mod_multiple"])
                                        ]),
                                new MessageElementGeneric("電商模組體驗_主推單樣商品", "透過“賴吧”後台，不需要程式人員，您就可以自定義商品展示給客戶的樣式。按“點擊我“感受您的客戶端體驗", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/EC_MOD/single.jpg", [
                                    new MessageButton(MessageButton::TYPE_POSTBACK, ["title" => "點擊我", "payload" => "ec_mod_single"])
                                        ])
                            ]
                        ])));
                        $this->_setUserStatus($senderId, array("", "privacy", ""));
                    }
                    break;
                case "ec_mod_multiple":
                    if (empty($userStatus) || $userStatus[1] !== "privacy") {
                        $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    } else {
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_GENERIC, [
                            'elements' => [
                                new MessageElementGeneric("達克瓦滋／Dacquoise 2入 (客戶自有連結)", "主廚小松真次郎，在無數次的配方調整、食材選用，一顆顆珍珠般的結晶附著於表面，成就出「達克瓦茲」那夢幻般的美名—「珍珠盤」", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/EC_MOD/multiple1.jpg", [
                                    new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://ismpastry.oddle.me/"])
                                        ]),
                                new MessageElementGeneric("維納斯｜ Coquille (系統預設電商模組)", "頂級杏仁粉配上杏仁酒，入口後濃厚的杏仁香氣在嘴裡散開。\n規格：NT$50／1入", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/EC_MOD/multiple2.jpg", [
                                    new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://social-crm-demo.chiliman.com.tw/controllers/fbController.php?psid=" . $senderId . "&psname=" . $userProfileAry['last_name'] . $userProfileAry['first_name'] . "&cm_type=ec&cm_key=1"])
                                        ]),
                                new MessageElementGeneric("和｜Japonais 抹茶煉乳慕斯/5吋 (客戶自有連結)", "以九州八女抹茶粉製作出風味十足的抹茶慕斯與抹茶蛋糕基底。\n抹茶慕斯中搭配北海道產蜜黑豆與煉乳內餡，茶香與香甜煉乳風味交", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/EC_MOD/multiple3.jpg", [
                                    new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://ismpastry.oddle.me/"])
                                        ]),
                                new MessageElementGeneric("美禰藍莓果園 - 夏蜜柑果醬 (系統預設電商模組)", "Mimaki Blueberry Garden 使用新鮮自家栽培的的優質水果，灌注熱忱與純淨的心製成品質優良且無添加防腐", "", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/EC_MOD/multiple4.jpg", [
                                    new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://social-crm-demo.chiliman.com.tw/controllers/fbController.php?psid=" . $senderId . "&psname=" . $userProfileAry['last_name'] . $userProfileAry['first_name'] . "&cm_type=ec&cm_key=2"])
                                        ])
                            ]
                        ])));
                        $this->_setUserStatus($senderId, array("", "privacy", ""));
                    }
                    break;
                case "ec_mod_single":
                    if (empty($userStatus) || $userStatus[1] !== "privacy") {
                        $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    } else {
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_GENERIC, [
                            'elements' => [
                                new MessageElementGeneric("【北海道奶油起司蛋糕捲 ｜Rouleau Fromage】", "", "https://www.facebook.com/patisserieism/", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/EC_MOD/single1.jpg", [
                                        ])
                            ]
                        ])));
                        $this->_setUserStatus($senderId, array("", "privacy", ""));
                    }
                    break;
                case "qa_mod":
                    if (empty($userStatus) || $userStatus[1] !== "privacy") {
                        $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    } else {
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageText(MessageText::MESSAGING_TYPE_RESPONSE, $senderId, "請輸入問題敘述："));
                        $this->_setUserStatus($senderId, array("qa_mod", "privacy", ""));
                    }
                    break;
                case "qa_mod_ask":
                    if (empty($userStatus) || $userStatus[1] !== "privacy") {
                        $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    } else {
                        if (!empty($userStatus) && $userStatus[0] === "qa_mod") {
                            //存到DB
                            $this->_databaseProcessor->InsertFBAskQuestion($senderId, $userStatus[2]);
                            $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageText(MessageText::MESSAGING_TYPE_RESPONSE, $senderId, "我們已收到您的訊息，我們將盡速回覆您!若要繼續使用其他功能，請點選下方選單↓。謝謝您~🙂🙂"));
                            $this->_setUserStatus($senderId, array("", "privacy", ""));
                        } else {
                            $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageText(MessageText::MESSAGING_TYPE_RESPONSE, $senderId, "請輸入問題敘述："));
                            $this->_setUserStatus($senderId, array("qa_mod", "privacy", ""));
                        }
                    }
                    break;
                case "qa_mod_cancel":
                    if (empty($userStatus) || $userStatus[1] !== "privacy") {
                        $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    } else {
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageText(MessageText::MESSAGING_TYPE_RESPONSE, $senderId, "已經取消目前動作，請重新點選按鈕。"));
                        $this->_setUserStatus($senderId, array("", "privacy", ""));
                    }
                    break;
                case "case":
                    if (empty($userStatus) || $userStatus[1] !== "privacy") {
                        $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    } else {
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_LIST, [
                            'top_element_style' => 'compact',
                            'elements' => [
                                new MessageElementList("費列羅業務巡訪bot", "知名巧克力品牌，透過【業務尋訪系統】協助公司掌握業務人員的工作進度及效率。", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/CASE/ferrero_square.jpg", [
                                    new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/1464586273564575/"])
                                        ], new MessageButton(MessageButton::TYPE_WEB_URL, ["url" => "https://www.facebook.com/1464586273564575/"])),
                                new MessageElementList("巧虎好朋友（巧連智）", "日本最大教育集團，日商倍樂生台北分公司所屬的粉絲團，透過舉辦線上活動來匯集人氣，並與粉絲更進一步的互動。", "https://social-crm-demo.chiliman.com.tw/assets_rear/images/CASE/benesse_square.jpg", [
                                    new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://www.facebook.com/BenesseTaiwan/photos/a.1082123465208170.1073741828.1075557715864745/1645632772190567/?type=3&theater"])
                                        ], new MessageButton(MessageButton::TYPE_WEB_URL, ["url" => "https://www.facebook.com/BenesseTaiwan/photos/a.1082123465208170.1073741828.1075557715864745/1645632772190567/?type=3&theater"]))
                            ]
                        ])));
                        $this->_setUserStatus($senderId, array("", "privacy", ""));
                    }
                    break;
                case "md_mod1":
                    if (empty($userStatus) || $userStatus[1] !== "privacy") {
                        $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    } else {
                        /* 抽抽樂 */
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_GENERIC, [
                            'elements' => [
                                new MessageElementGeneric("【抽抽樂】", "", "https://social-crm-demo.chiliman.com.tw/controllers/fbController.php?psid=" . $senderId . "&psname=" . $userProfileAry['last_name'] . $userProfileAry['first_name'] . "&cm_type=md_mod1", CDN_ROOT_PATH . "power_line/PUMPING/pumping-01.jpg", [
                                        ])
                            ]
                        ])));
                        $this->_setUserStatus($senderId, array("", "privacy", ""));
                    }
                    break;
                case "md_mod2":
                    if (empty($userStatus) || $userStatus[1] !== "privacy") {
                        $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    } else {
                        /* 發票抽獎 */
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_GENERIC, [
                            'elements' => [
                                new MessageElementGeneric("【發票抽獎】", "", "https://social-crm-demo.chiliman.com.tw/controllers/fbController.php?psid=" . $senderId . "&psname=" . $userProfileAry['last_name'] . $userProfileAry['first_name'] . "&cm_type=md_mod2", CDN_ROOT_PATH . "power_line/INVOICE/invoice-01.jpg", [
                                        ])
                            ]
                        ])));
                        $this->_setUserStatus($senderId, array("", "privacy", ""));
                    }
                    break;
                case "md_mod3":
                    if (empty($userStatus) || $userStatus[1] !== "privacy") {
                        $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    } else {
                        /* 分享換好康 */
                        $TITLE = "快邀請您的好友加入賴吧";
                        $CONTENT = "『分享換好康』為賴吧商品模組中的其中一項功能。\n\n您可以設定一個主題與獎項，透過既有粉絲的力量，邀請身邊好友加入您的官方帳號，可增加粉絲人數外，也提升與您的忠實粉絲的互動性。\n\n本示範活動設定達標人數為1人，您可以立即分享給好友，以進一步了解此分享模組功能。";
                        $AWARDS_CONTENT = "這裡可以自定義文字內容，顯示分享達標之後可以獲得什麼樣的禮品或優惠";
                        $message = "您好！我是" . $userProfileAry['last_name'] . $userProfileAry['first_name'] . PHP_EOL . PHP_EOL;
                        $message .= "我參加了" . $TITLE . "活動，" . $CONTENT . "快點選以下連結跟我一起享好康～";
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_GENERIC, [
                            'elements' => [
                                new MessageElementGeneric("分享換好康", "快邀請您的好友加入賴吧", "", CDN_ROOT_PATH . "power_line/SHARE/share.png", [
                                    new MessageButton(MessageButton::TYPE_POSTBACK, ["title" => "【活動說明】", "payload" => "md_mod3_explain"]),
                                    new MessageButton(MessageButton::TYPE_ELEMENT_SHARE, new Template(Template::TEMPLATE_TYPE_GENERIC, [
                                        'elements' => [
                                            new MessageElementGeneric($TITLE, $message, "", CDN_ROOT_PATH . "power_line/SHARE/share.png", [
                                                new MessageButton(MessageButton::TYPE_WEB_URL, ["title" => "點擊我", "url" => "https://social-crm-demo.chiliman.com.tw/controllers/fbController.php?psid=" . $senderId . "&cm_type=md_mod3"])
                                                    ])
                                        ]]))
                                        ])
                            ]
                        ])));
                        $this->_setUserStatus($senderId, array("", "privacy", ""));
                    }
                    break;
                case "md_mod3_explain":
                    if (empty($userStatus) || $userStatus[1] !== "privacy") {
                        $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    } else {
                        /* 分享換好康活動說明 */
                        $TITLE = "快邀請您的好友加入賴吧";
                        $CONTENT = "『分享換好康』為賴吧商品模組中的其中一項功能。\n\n您可以設定一個主題與獎項，透過既有粉絲的力量，邀請身邊好友加入您的官方帳號，可增加粉絲人數外，也提升與您的忠實粉絲的互動性。\n\n本示範活動設定達標人數為1人，您可以立即分享給好友，以進一步了解此分享模組功能。";
                        $AWARDS_CONTENT = "這裡可以自定義文字內容，顯示分享達標之後可以獲得什麼樣的禮品或優惠";
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageText(MessageText::MESSAGING_TYPE_RESPONSE, $senderId, $CONTENT));
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES,"me",new SenderAction(SenderAction::MESSAGING_TYPE_RESPONSE, $senderId, SenderAction::SENDER_ACTION_TYPING_ON));
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageText(MessageText::MESSAGING_TYPE_RESPONSE, $senderId, $AWARDS_CONTENT));
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES,"me",new SenderAction(SenderAction::MESSAGING_TYPE_RESPONSE, $senderId, SenderAction::SENDER_ACTION_TYPING_ON));
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_IMAGE, new Image(Image::TYPE_ATTACHMENT_ID, "878672198987208")));
                        $this->_setUserStatus($senderId, array("", "privacy", ""));
                    }
                    break;
                case "md_mod4":
                    if (empty($userStatus) || $userStatus[1] !== "privacy") {
                        $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    } else {
                        /* 優惠卷 */
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_IMAGE, new Image(Image::TYPE_ATTACHMENT_ID, "879039422283819")));
                        $this->_setUserStatus($senderId, array("", "privacy", ""));
                    }
                    break;
                case "scrm_know":
                    if (empty($userStatus) || $userStatus[1] !== "privacy") {
                        $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    } else {
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_BUTTON, [
                            'text' => "【賴吧】是一套首創將LINE及Facebook整合為一套後台的「客戶管理整合系統」分析客戶的偏好、興趣及點擊歷程，讓您再行銷上更加精準。",
                            'buttons' => [
                                new MessageButton(MessageButton::TYPE_POSTBACK, ["title" => "【深入了解】", "payload" => "linebar_intro"])
                            ]
                        ])));
                        $this->_setUserStatus($senderId, array("", "privacy", ""));
                    }
                    break;
                case "scrm_unknow":
                    if (empty($userStatus) || $userStatus[1] !== "privacy") {
                        $this->privateDeclare($senderId, $userProfileAry['last_name'], $userProfileAry['first_name']);
                    } else {
                        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageAttachment(MessageAttachment::MESSAGING_TYPE_RESPONSE, $senderId, MessageAttachment::TYPE_TEMPLATE, new Template(Template::TEMPLATE_TYPE_BUTTON, [
                            'text' => "CRM的意思是「客戶管理整合系統」，基本的CRM能記錄客戶的基本資料、訂單資訊等等。【賴吧】不單是基本CRM的服務，更能分析客戶的偏好、興趣及點擊歷程，讓您再行銷上更加精準。【賴吧】首創將LINE及Facebook整合為一套後台的「客戶管理整合系統」",
                            'buttons' => [
                                new MessageButton(MessageButton::TYPE_POSTBACK, ["title" => "【深入了解】", "payload" => "linebar_intro"])
                            ]
                        ])));
                        $this->_setUserStatus($senderId, array("", "privacy", ""));
                    }
                    break;
                default:
                    break;
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
        //$this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new MessageText(MessageText::MESSAGING_TYPE_RESPONSE, $senderId, "Hi，" . $last_name . $first_name . PHP_EOL . "感謝您加入本官方帳號！當您開始使用本官方帳號服務時，即表示您信賴並同意本服務對您個人資訊的處理方式。為了協助您瞭解本服務收集的資料類型以及這些資料的用途，請撥冗詳閱《隱私權條款》：https://goo.gl/6ffcJj"));
        $this->_fbApp->send(FBApp::API_TYPE_MESSENGER_MESSAGES, "me", new QuickReply(QuickReply::MESSAGING_TYPE_RESPONSE, $senderId, "Hi，" . $last_name . $first_name . PHP_EOL . "感謝您加入本官方帳號！當您開始使用本官方帳號服務時，即表示您信賴並同意本服務對您個人資訊的處理方式。為了協助您瞭解本服務收集的資料類型以及這些資料的用途，請撥冗詳閱《隱私權條款》：https://goo.gl/6ffcJj", [
            new QuickReplyButton(QuickReplyButton::CONTENT_TYPE_TEXT, "賴吧介紹", "linebar_intro", ""),
        ]));
        $this->_setUserStatus($senderId, array("", "privacy", ""));
    }

}
