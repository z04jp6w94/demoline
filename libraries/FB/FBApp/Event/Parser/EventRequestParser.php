<?php

namespace FB\FBApp\Event\Parser;

use FB\FBApp\Exception\InvalidEventRequestException;
use FB\FBApp\Exception\InvalidFBPageIdException;
use FB\FBApp\Exception\UnknownPageEventTypeException;

class EventRequestParser {

    private static $pageEventTypeclass = [
        "changes" => "\FB\FBApp\Event\PostEvent",
        "messaging" => "\FB\FBApp\Event\MessengerEvent",
    ];
    private static $postEventTypeclass = [
        "status" => "\FB\FBApp\Event\PostEvent\StatusPost", //貼文
        "share" => "\FB\FBApp\Event\PostEvent\SharePost", //貼文
        "photo" => "\FB\FBApp\Event\PostEvent\PhotoPost", //貼文
        "like" => "\FB\FBApp\Event\PostEvent\LikePost", //貼文、留言
        "reaction" => "\FB\FBApp\Event\PostEvent\ReactionPost", //貼文、留言
        "comment" => "\FB\FBApp\Event\PostEvent\CommentPost", //留言
    ];
    private static $messengerEventTypeclass = [
        "message" => "\FB\FBApp\Event\MessengerEvent\MessageMessenger",
        "postback" => "\FB\FBApp\Event\MessengerEvent\PostbackMessenger",
    ];
    private static $messengerMessageEventTypeclass = [
        "text" => "\FB\FBApp\Event\MessengerEvent\MessageMessenger\TextMessageMessenger",
        "attachments" => "\FB\FBApp\Event\MessengerEvent\MessageMessenger\AttachmentMessageMessenger",
    ];

    public static function parseEventRequest($body, $fb_page_id) { //changes、messaging
        if (!isset($fb_page_id)) {
            throw new InvalidFBPageIdException('Request does not contain fb page id');
        }

        $events = [];

        $parsedReq = json_decode($body, true, 512, JSON_BIGINT_AS_STRING);

        if (!isset($parsedReq["object"]) || !isset($parsedReq["entry"])) {
            throw new InvalidEventRequestException("InvalidEventRequestException Please Check FB API");
        }

        if ($parsedReq["entry"][0]["id"] !== $fb_page_id) {
            throw new InvalidFBPageIdException('Invalid fb page id has given');
        }

        $isFlag = TRUE;

        foreach ($parsedReq["entry"][0] as $key => $val) {
            if (!array_key_exists($key, self::$pageEventTypeclass)) {
                continue;
            }
            if ($key === "changes") {
                $isFlag = FALSE;
                $events[] = self::parsePostEvent($parsedReq);
                break;
            }
            if ($key === "messaging") {
                $isFlag = FALSE;
                $events[] = self::parseMessengerEvent($parsedReq);
                break;
            }
        }

        if ($isFlag)
            throw new UnknownPageEventTypeException("UnknownPageEventTypeException Please Check eventTypeclass of EventRequestParser");

        return $events;
    }

    private static function parsePostEvent($postEventData) { //status、share、photo、like、comment
        $postEventType = $postEventData["entry"][0]["changes"][0]["value"]["item"];

            if (!array_key_exists($postEventType, self::$postEventTypeclass)) {
            throw new UnknownPageEventTypeException("UnknownPageEventTypeException Please Check postEventTypeclass of EventRequestParser");
        }

        $postEventClass = self::$postEventTypeclass[$postEventType];

        return new $postEventClass($postEventData);
    }

    private static function parseMessengerEvent($messengerEventData) { //message、postback
        $isFlag = TRUE;

        foreach ($messengerEventData["entry"][0]["messaging"][0] as $key => $val) {
            if (!array_key_exists($key, self::$messengerEventTypeclass)) {
                continue;
            }

            if ($key === "message") {
                $isFlag = FALSE;
                $messengerEventClass = self::parseMessengerMessageEvent($messengerEventData);
                break;
            }
            if ($key === "postback") {
                $isFlag = FALSE;
                $messengerEventClass = self::$messengerEventTypeclass[$key];
                break;
            }
        }

        if ($isFlag)
            throw new UnknownPageEventTypeException("UnknownPageEventTypeException Please Check messengerEventTypeclass of EventRequestParser");

        return new $messengerEventClass($messengerEventData);
    }

    private static function parseMessengerMessageEvent($messengerMessageEventData) { //text、attachments
        $isFlag = TRUE;

        foreach ($messengerMessageEventData["entry"][0]["messaging"][0]["message"] as $key => $val) {
            if (!array_key_exists($key, self::$messengerMessageEventTypeclass)) {
                continue;
            }

            if ($key === "text") {
                $isFlag = FALSE;
                $messengerMessageEventClass = self::$messengerMessageEventTypeclass[$key];
                break;
            }
            if ($key === "attachments") {
                $isFlag = FALSE;
                $messengerMessageEventClass = self::$messengerMessageEventTypeclass[$key];
                break;
            }
        }

        if ($isFlag)
            throw new UnknownPageEventTypeException("UnknownPageEventTypeException Please Check messengerMessageEventTypeclass of EventRequestParser");

        return new $messengerMessageEventClass($messengerMessageEventData);
    }

}
