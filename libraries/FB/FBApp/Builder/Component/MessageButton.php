<?php

namespace FB\FBApp\Builder\Component;

class MessageButton {

    /**
     * type type
     */
    const TYPE_WEB_RUL = "web_url";
    const TYPE_POSTBACK = "postback";
    const TYPE_ELEMENT_SHARE = "element_share";
    const TYPE_PATMENT = "payment";
    const TYPE_PHONE_NUMBER = "phone_number";
    const TYPE_ACCOUNT_LINK = "account_link";
    const TYPE_ACCOUNT_UNLINK = "account_unlink";

    private $type = null;
    private $title = null;
    private $res = null;
    private $webview_height_ratio = null;
    private $messenger_extensions = false;
    private $fallback_url = null;
    private $share_contents = null;
    private $webview_share_button = null;

    public function __construct($type, $title = '', $res = '', $webview_height_ratio = '', $messenger_extensions = false, $fallback_url = '', $share_contents = null, $webview_share_button = null) {
        $this->type = $type;
        $this->title = $title;
        $this->res = $res;
        $this->webview_height_ratio = $webview_height_ratio;
        $this->messenger_extensions = $messenger_extensions;
        $this->fallback_url = $fallback_url;
        $this->share_contents = $share_contents;
        $this->webview_share_button = $webview_share_button;
    }

    public function getData() {
        $data = [
            'type' => $this->type
        ];
        switch ($this->type) {
            case self::TYPE_WEB_RUL:
                $data['title'] = $this->title;
                $data['url'] = $this->res;
                if ($this->webview_height_ratio) {
                    $data['webview_height_ratio'] = $this->webview_height_ratio;
                }
                if ($this->messenger_extensions) {
                    $data['messenger_extensions'] = $this->messenger_extensions;
                    $data['fallback_url'] = $this->fallback_url;
                }
                if ($this->webview_share_button) {
                    $data['webview_share_button'] = $this->webview_share_button;
                }
                break;
            case self::TYPE_POSTBACK:
                $data['title'] = $this->title;
                $data['payload'] = $this->res;
                break;
        }
        return $data;
    }

}
