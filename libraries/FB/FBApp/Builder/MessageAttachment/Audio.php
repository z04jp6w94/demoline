<?php

namespace FB\FBApp\Builder\MessageAttachment;

use FB\FBApp\Builder\MessageAttachment;

class Audio extends MessageAttachment {

    private $url = null;

    public function __construct($url, $quick_replies = array()) {
        $this->url = $url;
        $this->quick_replies = $quick_replies;
    }

    public function getData() {
        $data = [
            'attachment' => [
                'type' => MessageAttachment::TYPE_IMAGE,
                'payload' => $this->url
            ]
        ];
        foreach ($this->quick_replies as $qr) {
            $data['quick_replies'][] = $qr->getData();
        }
        return $data;
    }

}
