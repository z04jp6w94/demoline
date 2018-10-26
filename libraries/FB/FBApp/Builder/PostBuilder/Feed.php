<?php

namespace FB\FBApp\Builder\PostBuilder;

class Feed {

    private $message = null;
    private $link = null;
    private $published = null;
    private $scheduled_publish_time = null;

    public function __construct($message, $link, $published, $scheduled_publish_time) {
        $this->message = $message;
        $this->link = $link;
        $this->published = $published;
        $this->scheduled_publish_time = $scheduled_publish_time;
    }

    public function getData() {
        $data = [
            'message' => $this->message,
            'link' => $this->link,
            'published' => $this->published,
            'scheduled_publish_time' => $this->scheduled_publish_time
        ];
        return $data;
    }

}
