<?php

namespace FB\FBApp\Event;

use FB\FBApp\Constant\EventSourceType;
use FB\FBApp\Exception\InvalidEventSourceException;

class MessengerEvent extends BaseEvent {

    public function __construct($event) {
        parent::__construct($event);
    }

    public function isMessengerEvent() {
        $eventSourceTypeAry = $this->event["entry"][0];
        foreach ($eventSourceTypeAry as $key => $val) {
            if ($key === EventSourceType::MESSENGER)
                return TRUE;
        }
        return FALSE;
    }

    public function getSenderId() {
        if (!$this->isMessengerEvent()) {
            throw new InvalidEventSourceException('This event source is not a messenger type');
        }
        return array_key_exists("id", $this->event["entry"][0]["messaging"][0]["sender"]) ? $this->event["entry"][0]["messaging"][0]["sender"]["id"] : null;
    }

    public function getRecipientId() {
        if (!$this->isMessengerEvent()) {
            throw new InvalidEventSourceException('This event source is not a messenger type');
        }
        return array_key_exists("id", $this->event["entry"][0]["messaging"][0]["recipient"]) ? $this->event["entry"][0]["messaging"][0]["recipient"]["id"] : null;
    }

    public function getRecipientTimestamp() {
        if (!$this->isMessengerEvent()) {
            throw new InvalidEventSourceException('This event source is not a messenger type');
        }
        return array_key_exists("timestamp", $this->event["entry"][0]["messaging"][0]) ? $this->event["entry"][0]["messaging"][0]["timestamp"] : null;
    }

    
}
