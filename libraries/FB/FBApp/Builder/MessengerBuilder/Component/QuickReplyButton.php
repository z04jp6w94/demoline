<?php

namespace FB\FBApp\Builder\MessengerBuilder\Component;

class QuickReplyButton {

    /**
     * content_type type
     */
    const CONTENT_TYPE_TEXT = "text";
    const CONTENT_TYPE_LOCATION = "location";
    const CONTENT_TYPE_USER_PHONE_NUMBER = "user_phone_number";
    const CONTENT_TYPE_USER_EMAIL = "user_email";

    private $type = null;
    private $title = null;
    private $payload = null;
    private $image_url = false;

    public function __construct($type, $title = '', $payload = null, $image_url = null) {
        $this->type = $type;
        $this->title = $title;
        $this->payload = $payload;
        $this->image_url = $image_url;
    }

    public function getData() {
        $data = [
            'content_type' => $this->type
        ];
        switch ($this->type) {
            case self::CONTENT_TYPE_TEXT:
                $data['title'] = $this->title;
                $data['payload'] = $this->payload;
                $data['image_url'] = $this->image_url;
                break;
            case self::CONTENT_TYPE_LOCATION:
                $data['image_url'] = $this->image_url;
                break;
        }
        return $data;
    }

}
