<?php

namespace FB\FBApp\Builder;

abstract class Message {

    /**
     * messaging_type type
     */
    const MESSAGING_TYPE_RESPONSE = "RESPONSE";
    const MESSAGING_TYPE_UPDATE = "UPDATE";
    const MESSAGING_TYPE_MESSAGE_TAG = "MESSAGE_TAG";

    /**
     * tag type
     */
    const TAG_SHIPPING_UPDATE = "SHIPPING_UPDATE";
    const TAG_RESERVATION_UPDATE = "RESERVATION_UPDATE";
    const TAG_ISSUE_RESOLUTION = "ISSUE_RESOLUTION";

    /**
     * Notification type
     */
    const NOTIFY_REGULAR = "REGULAR";
    const NOTIFY_SILENT_PUSH = "SILENT_PUSH";
    const NOTIFY_NO_PUSH = "NO_PUSH";

    protected $messaging_type = null;
    protected $recipient = null;
    protected $text = null;
    protected $user_ref = false;
    protected $tag = null;
    protected $notification_type = null;
    protected $quick_replies = null;

    public function __construct() {
        
    }

    public function getData() {
        
    }

}
