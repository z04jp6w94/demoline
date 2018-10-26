<?php

namespace FB\FBApp\Event\MessengerEvent\MessageMessenger;

use FB\FBApp\Event\MessengerEvent\MessageMessenger;

class AttachmentMessageMessenger extends MessageMessenger {

    public function __construct($event) {
        parent::__construct($event);
    }

    public function getStickerId() {
        return !empty($this->event["entry"][0]["messaging"][0]["message"]["sticker_id"]) ? $this->event["entry"][0]["messaging"][0]["message"]["sticker_id"] : NULL;
    }

    public function getALLAttachmentsAry() {
        return $this->event["entry"][0]["messaging"][0]["message"]["attachments"];
    }

    public function getImageAttachmentsAry() {
        $attachmentsImageAry = [];
        $attachmentsAry = $this->getALLAttachmentsAry();
        foreach ($attachmentsAry as $key => $value) {
            if ($attachmentsAry[$key]["type"] === "image")
                $attachmentsVideoAry[] = $value;
        }
        return $this->event["entry"][0]["messaging"][0]["message"]["attachments"];
    }

    public function getVideoAttachmentsAry() {
        $attachmentsVideoAry = [];
        $attachmentsAry = $this->getALLAttachmentsAry();
        foreach ($attachmentsAry as $key => $value) {
            if ($attachmentsAry[$key]["type"] === "video")
                $attachmentsVideoAry[] = $value;
        }
        return $attachmentsVideoAry;
    }

    public function getAudioAttachmentsAry() {
        $attachmentsAudioAry = [];
        $attachmentsAry = $this->getALLAttachmentsAry();
        foreach ($attachmentsAry as $key => $value) {
            if ($attachmentsAry[$key]["type"] === "audio")
                $attachmentsVideoAry[] = $value;
        }
        return $attachmentsAudioAry;
    }

    public function getFileAttachmentsAry() {
        $attachmentsFileAry = [];
        $attachmentsAry = $this->getALLAttachmentsAry();
        foreach ($attachmentsAry as $key => $value) {
            if ($attachmentsAry[$key]["type"] === "file")
                $attachmentsVideoAry[] = $value;
        }
        return $attachmentsFileAry;
    }

}
