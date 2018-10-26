<?php

namespace FB\FBApp\Event\MessengerEvent\MessageMessenger;

use FB\FBApp\Event\MessengerEvent\MessageMessenger;

class TextMessageMessenger extends MessageMessenger {

    public function __construct($event) {
        parent::__construct($event);
    }

    public function getText() {
        return $this->event["entry"][0]["messaging"][0]["message"]["text"];
    }
    //Han new
    public function getQuickReplyPayload() {
        return !empty($this->event["entry"][0]["messaging"][0]["message"]["quick_reply"]["payload"]) ? $this->event["entry"][0]["messaging"][0]["message"]["quick_reply"]["payload"] : NULL;
    }
}
