<?php

namespace FB\FBApp\Builder\MessengerBuilder;

class MessageAttachmentUpload extends Message {

    /**
     * upload type
     */
    const TYPE_IMAGE_UPLOAD = "image";
    const TYPE_AUDIO_UPLOAD = "audio";
    const TYPE_VIDEO_UPLOAD = "video";
    const TYPE_FILE_UPLOAD = "file";

    private $type = null;
    private $res = null;

    public function __construct($type, $res) {
        $this->type = $type;
        $this->res = $res;
    }

    public function getData() {
        switch ($this->type) {
            case self::TYPE_IMAGE_UPLOAD:
                $data['message'] = $this->res->getData();
                break;
            case self::TYPE_AUDIO_UPLOAD:
                $data['message'] = $this->res->getData();
                break;
            case self::TYPE_VIDEO_UPLOAD:
                $data['message'] = $this->res->getData();
                break;
            case self::TYPE_FILE_UPLOAD:
                $data['message'] = $this->res->getData();
                break;
        }
        return $data;
    }

}
