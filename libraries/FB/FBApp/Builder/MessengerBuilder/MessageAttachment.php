<?php

namespace FB\FBApp\Builder\MessengerBuilder;

class MessageAttachment extends Message {

    /**
     * type type
     */
    const TYPE_IMAGE = "image";
    const TYPE_AUDIO = "audio";
    const TYPE_VIDEO = "video";
    const TYPE_FILE = "file";
    const TYPE_TEMPLATE = "template";

    private $type = null;
    private $res = null;

    public function __construct($messaging_type, $recipient, $type, $res, $quick_replies = array(), $tag = null, $notification_type = parent::NOTIFY_REGULAR) {
        $this->messaging_type = $messaging_type;
        $this->recipient = $recipient;
        $this->type = $type;
        $this->res = $res;
        $this->quick_replies = $quick_replies;
        $this->tag = $tag;
        $this->notification_type = $notification_type;
    }

    public function getData() {
        $data = [
            'messaging_type' => $this->messaging_type,
            'recipient' => [
                'id' => $this->recipient
            ],
            'tag' => $this->tag,
            'notification_type' => $this->notification_type
        ];
        switch ($this->type) {
            case self::TYPE_IMAGE:
                $data['message'] = $this->res->getData();
                break;
            case self::TYPE_AUDIO:
                $data['message'] = $this->res->getData();
                break;
            case self::TYPE_VIDEO:
                $data['message'] = $this->res->getData();
                break;
            case self::TYPE_FILE:
                $data['message'] = $this->res->getData();
                break;
            case self::TYPE_TEMPLATE:
                $data['message'] = $this->res->getData();
                break;
        }
        return $data;
    }

}
