<?php

namespace FB\FBApp\Builder\MessengerBuilder;

class Broadcast extends Message {

    private $message_creative_id = null;
    private $custom_label_id = null;

    public function __construct($messaging_type, $message_creative_id, $custom_label_id, $tag = null, $notification_type = parent::NOTIFY_REGULAR) {
        $this->messaging_type = $messaging_type;
        $this->message_creative_id = $message_creative_id;
        $this->custom_label_id = $custom_label_id;
        $this->tag = $tag;
        $this->notification_type = $notification_type;
    }

    public function getData() {
        $data = [
            'messaging_type' => $this->messaging_type,
            'message_creative_id' => $this->message_creative_id,
            'custom_label_id'=> $this->custom_label_id,
            'tag' => $this->tag,
            'notification_type' => $this->notification_type
        ];
        return $data;
    }

}
