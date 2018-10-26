<?php

namespace FB\FBApp\Builder\MessengerBuilder\Component;

class MessageButton {

    /**
     * type type
     */
    const TYPE_WEB_URL = "web_url";
    const TYPE_POSTBACK = "postback";
    const TYPE_ELEMENT_SHARE = "element_share";
    const TYPE_PATMENT = "payment"; //USA
    const TYPE_PHONE_NUMBER = "phone_number";
    const TYPE_ACCOUNT_LINK = "account_link";
    const TYPE_ACCOUNT_UNLINK = "account_unlink";

    private $type = null;
    private $res = null;

    public function __construct($type, $res = '') {
        $this->type = $type;
        $this->res = $res;
    }

    public function getData() {
        $data = [
            'type' => $this->type
        ];
        switch ($this->type) {
            case self::TYPE_WEB_URL:
                if (!empty($this->res["title"])) {
                    $data['title'] = $this->res["title"];
                }
                $data['url'] = $this->res['url'];
                if (!empty($this->res["webview_height_ratio"])) {
                    $data['webview_height_ratio'] = $this->res["webview_height_ratio"];
                }
                if (!empty($this->res["messenger_extensions"])) {
                    $data['messenger_extensions'] = $this->res["messenger_extensions"];
                    $data['fallback_url'] = $this->res["fallback_url"];
                }
                if (!empty($this->res["webview_share_button"])) {
                    $data['webview_share_button'] = $this->res["webview_share_button"];
                }
                break;
            case self::TYPE_POSTBACK:
                $data['title'] = $this->res['title'];
                $data['payload'] = $this->res['payload'];
                break;
            case self::TYPE_ELEMENT_SHARE:
                $data['share_contents'] = $this->res->getData(); //Template::TEMPLATE_TYPE_GENERIC|MessageButton::TYPE_WEB_RUL
                break;
            case self::TYPE_PATMENT:
                //USA
                break;
            case self::TYPE_PHONE_NUMBER:
                $data['title'] = $this->res['title'];
                $data['payload'] = $this->res['payload'];
                break;
            case self::TYPE_ACCOUNT_LINK:

                break;
            case self::TYPE_ACCOUNT_UNLINK:
                break;
        }
        return $data;
    }

}
