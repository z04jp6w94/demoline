<?php

namespace FB\FBApp\Builder;

class QuickReply extends Message {

    public function __construct($messaging_type, $recipient, $text, $quick_replies = array(), $tag = null, $notification_type = parent::NOTIFY_REGULAR) {
        $this->messaging_type = $messaging_type;
        $this->recipient = $recipient;
        $this->text = $text;
        $this->quick_replies = $quick_replies;
        $this->tag = $tag;
        $this->notification_type = $notification_type;
        //parent::__construct($recipient,$text);
    }

    public function getData() {
        $data = [
            'messaging_type' => $this->messaging_type,
            'recipient' => [
                'id' => $this->recipient
            ],
            'message' => [
                'text' => $this->text
            ],
            'tag' => $this->tag,
            'notification_type' => $this->notification_type
        ];
        foreach ($this->quick_replies as $qr) {
            $data['message']['quick_replies'][] = $qr->getData();
        }
        return $data;
    }

}
