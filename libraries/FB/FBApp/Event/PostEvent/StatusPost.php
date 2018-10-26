<?php

namespace FB\FBApp\Event\PostEvent;

use FB\FBApp\Event\PostEvent;

class StatusPost extends PostEvent {

    public function __construct($event) {
        parent::__construct($event);
    }

    public function getMessage() {
        return $this->event["entry"][0]["changes"][0]["value"]["message"];
    }

}
