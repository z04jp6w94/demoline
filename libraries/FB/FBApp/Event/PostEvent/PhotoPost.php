<?php

namespace FB\FBApp\Event\PostEvent;

use FB\FBApp\Event\PostEvent;

class PhotoPost extends PostEvent {

    public function __construct($event) {
        parent::__construct($event);
    }

    public function getPhotoId() {
        return $this->event["entry"][0]["changes"][0]["value"]["photo_id"];
    }

    public function getLink() {
        return $this->event["entry"][0]["changes"][0]["value"]["link"];
    }

    public function getMessage() {
        return !empty($this->event["entry"][0]["changes"][0]["value"]["message"]) ? $this->event["entry"][0]["changes"][0]["value"]["message"] : NULL;
    }

}
