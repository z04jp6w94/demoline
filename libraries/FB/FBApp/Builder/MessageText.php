<?php

namespace FB\FBApp\Builder;

class MessageText extends Message {

    public function __construct($messaging_type, $recipient, $text, $user_ref = false, $tag = null, $notification_type = parent::NOTIFY_REGULAR) {
        $this->messaging_type = $messaging_type;
        $this->recipient = $recipient;
        $this->text = $text;
        $this->user_ref = $user_ref;
        $this->tag = $tag;
        $this->notification_type = $notification_type;
    }

    public function getData() {
        $data = [
            'messaging_type' => $this->messaging_type,
            'recipient' => $this->user_ref ? ['user_ref' => $this->recipient] : ['id' => $this->recipient],
            'message' => [
                'text' => $this->text
            ],
            'tag' => $this->tag,
            'notification_type' => $this->notification_type
        ];
        return $data;
    }

}
