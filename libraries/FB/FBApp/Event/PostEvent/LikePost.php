<?php

namespace FB\FBApp\Event\PostEvent;

use FB\FBApp\Event\PostEvent;

class LikePost extends PostEvent {

    public function __construct($event) {
        parent::__construct($event);
    }

    public function getParentId() {
        return $this->event["entry"][0]["changes"][0]["value"]["parent_id"];
    }

    public function getCommentId() {
        return !empty($this->event["entry"][0]["changes"][0]["value"]["comment_id"]) ? $this->event["entry"][0]["changes"][0]["value"]["comment_id"] : NULL;
    }

}
