<?php

namespace FB\FBApp\Builder\PostBuilder;

class Photo {

    private $url = null;
    private $caption = null;
    private $published = null;
    private $scheduled_publish_time = null;

    public function __construct($url, $caption, $published, $scheduled_publish_time) {
        $this->url = $url;
        $this->caption = $caption;
        $this->published = $published;
        $this->scheduled_publish_time = $scheduled_publish_time;
    }

    public function getData() {
        $data = [
            'url' => $this->url,
            'caption' => $this->caption,
            'published' => $this->published,
            'scheduled_publish_time' => $this->scheduled_publish_time
        ];
        return $data;
    }

}
