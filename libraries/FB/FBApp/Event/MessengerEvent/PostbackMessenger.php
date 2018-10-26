<?php

namespace FB\FBApp\Event\MessengerEvent;

use FB\FBApp\Event\MessengerEvent;

class PostbackMessenger extends MessengerEvent {

    public function __construct($event) {
        parent::__construct($event);
    }

    public function getTitle() {
        return $this->event["entry"][0]["messaging"][0]["postback"]["title"];
    }

    public function getPayload() {
        return $this->event["entry"][0]["messaging"][0]["postback"]["payload"];
    }

}
