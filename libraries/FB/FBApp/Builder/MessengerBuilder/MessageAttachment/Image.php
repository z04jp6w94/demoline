<?php

namespace FB\FBApp\Builder\MessengerBuilder\MessageAttachment;

use FB\FBApp\Builder\MessengerBuilder\MessageAttachment;

class Image extends MessageAttachment {

    /**
     * type type
     */
    const TYPE_URL = "url";
    const TYPE_ATTACHMENT_ID = "attachment_id";

    private $type = null;
    private $res = null;

    public function __construct($type, $res, $quick_replies = array()) {
        $this->type = $type;
        $this->res = $res;
        $this->quick_replies = $quick_replies;
    }

    public function getData() {
        $data = [
            'attachment' => [
                'type' => MessageAttachment::TYPE_IMAGE,
                'payload' => [
                    'is_reusable' => true
                ]
            ]
        ];
        switch ($this->type) {
            case self::TYPE_URL:
                $data['attachment']['payload']['url'] = $this->res;
                break;
            case self::TYPE_ATTACHMENT_ID:
                $data['attachment']['payload']['attachment_id'] = $this->res;
                break;
        }
        foreach ($this->quick_replies as $qr) {
            $data['quick_replies'][] = $qr->getData();
        }
        return $data;
    }

}
