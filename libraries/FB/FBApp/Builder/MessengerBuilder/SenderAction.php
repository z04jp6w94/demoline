<?php

namespace FB\FBApp\Builder\MessengerBuilder;

class SenderAction extends Message {

    /**
     * get_sender_action type
     */
    const SENDER_ACTION_MARK_SEEN = "mark_seen";
    const SENDER_ACTION_TYPING_ON = "typing_on";
    const SENDER_ACTION_TYPING_OFF = "typing_off";
    
    public function __construct($messaging_type, $recipient, $sender_action, $user_ref = false, $tag = null, $notification_type = parent::NOTIFY_REGULAR) {
        $this->messaging_type = $messaging_type;
        $this->recipient = $recipient;
        $this->sender_action = $sender_action;
        $this->user_ref = $user_ref;
        $this->tag = $tag;
        $this->notification_type = $notification_type;
    }

    public function getData() {
        $data = [
            'messaging_type' => $this->messaging_type,
            'recipient' => $this->user_ref ? ['user_ref' => $this->recipient] : ['id' => $this->recipient],
            'sender_action' => $this->sender_action,
            'tag' => $this->tag,
            'notification_type' => $this->notification_type
        ];
        return $data;
    }

}
