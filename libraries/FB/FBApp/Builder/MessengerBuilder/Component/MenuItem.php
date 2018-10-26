<?php

namespace FB\FBApp\Builder\MessengerBuilder\Component;

class MenuItem {

    /**
     * type type
     */
    const TYPE_WEB_URL = "web_url";
    const TYPE_POSTBACK = "postback";
    const TYPE_NESTED = "nested";

    protected $type;
    protected $title;
    protected $res = null;
    protected $webview_height_ratio = null;
    protected $messenger_extensions = false;
    protected $fallback_url = null;
    protected $webview_share_button = null;

    public function __construct($type, $title, $res, $webview_height_ratio = '', $messenger_extensions = false, $fallback_url = '', $webview_share_button = null) {
        $this->type = $type;
        $this->title = $title;
        $this->res = $res;
        $this->webview_height_ratio = $webview_height_ratio;
        $this->messenger_extensions = $messenger_extensions;
        $this->fallback_url = $fallback_url;
        $this->webview_share_button = $webview_share_button;
    }

    public function getData() {
        $data = [
            'type' => $this->type,
            'title' => $this->title
        ];
        switch ($this->type) {
            case self::TYPE_WEB_URL:
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
                $data['payload'] = $this->res;
                break;
            case self::TYPE_NESTED:
                foreach ($this->res as $menuItem) {
                    $data['call_to_actions'][] = $menuItem->getData();
                }
                break;
        }
        return $data;
    }

}
