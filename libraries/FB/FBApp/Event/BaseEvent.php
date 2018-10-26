<?php

namespace FB\FBApp\Event;

use FB\FBApp\Constant\EventSourceType;
use FB\FBApp\Exception\InvalidEventSourceException;

class BaseEvent {

    protected $event;

    public function __construct($event) {
        $this->event = $event;
    }

    public function isPageEvent() {
        return $this->event["object"] === EventSourceType::PAGE;
    }

    public function getPageId() {
        if (!$this->isPageEvent()) {
            throw new InvalidEventSourceException('This event source is not a page type');
        }
        return array_key_exists("id", $this->event["entry"][0]) ? $this->event["entry"][0]["id"] : null;
    }

    public function getTimestamp() {
        if (!$this->isPageEvent()) {
            throw new InvalidEventSourceException('This event source is not a page type');
        }
        return array_key_exists("time", $this->event["entry"][0]) ? $this->event["entry"][0]["time"] : null;
    }

    public function getEventSourceId() {
        if ($this->isPageEvent()) {
            return $this->getPageId();
        }

        return null;
    }

}
