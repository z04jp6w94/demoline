<?php

namespace FB\FBApp\Event\MessengerEvent;

use FB\FBApp\Event\MessengerEvent;

class MessageMessenger extends MessengerEvent {

    public function __construct($event) {
        parent::__construct($event);
    }

    public function getMid() {
        return $this->event["entry"][0]["messaging"][0]["message"]["mid"];
    }

    public function getSeq() {
        return $this->event["entry"][0]["messaging"][0]["message"]["seq"];
    }

}
