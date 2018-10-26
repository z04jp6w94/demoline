<?php

namespace FB\FBApp\Event;

use FB\FBApp\Constant\EventSourceType;
use FB\FBApp\Exception\InvalidEventSourceException;

class PostEvent extends BaseEvent {

    public function __construct($event) {
        parent::__construct($event);
    }

    public function isPostvent() {
        $eventSourceTypeAry = $this->event["entry"][0];
        foreach ($eventSourceTypeAry as $key => $val) {
            if ($key === EventSourceType::POST)
                return TRUE;
        }
        return FALSE;
    }

    public function getPostId() {
        if (!$this->isPostvent()) {
            throw new InvalidEventSourceException('This event source is not a post type');
        }
        return array_key_exists('post_id', $this->event["entry"][0]["changes"][0]["value"]) ? $this->event["entry"][0]["changes"][0]["value"]["post_id"] : null;
    }

    public function getPostItem() {
        if (!$this->isPostvent()) {
            throw new InvalidEventSourceException('This event source is not a post type');
        }
        return array_key_exists('item', $this->event["entry"][0]["changes"][0]["value"]) ? $this->event["entry"][0]["changes"][0]["value"]["item"] : null;
    }

    public function getPostVerb() {
        if (!$this->isPostvent()) {
            throw new InvalidEventSourceException('This event source is not a post type');
        }
        return array_key_exists('item', $this->event["entry"][0]["changes"][0]["value"]) ? $this->event["entry"][0]["changes"][0]["value"]["verb"] : null;
    }

    public function getPostCreateTime() {
        if (!$this->isPostvent()) {
            throw new InvalidEventSourceException('This event source is not a post type');
        }
        return array_key_exists("created_time", $this->event["entry"][0]["changes"][0]["value"]) ? $this->event["entry"][0]["changes"][0]["value"]["created_time"] : null;
    }

    public function getFromId() {
        if (!$this->isPostvent()) {
            throw new InvalidEventSourceException('This event source is not a post type');
        }
        return array_key_exists("id", $this->event["entry"][0]["changes"][0]["value"]["from"]) ? $this->event["entry"][0]["changes"][0]["value"]["from"]["id"] : null;
    }

    public function getFromName() {
        if (!$this->isPostvent()) {
            throw new InvalidEventSourceException('This event source is not a post type');
        }
        return array_key_exists("name", $this->event["entry"][0]["changes"][0]["value"]["from"]) ? $this->event["entry"][0]["changes"][0]["value"]["from"]["name"] : null;
    }

}
