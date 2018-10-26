<?php

namespace FB\FBApp\Builder\MessengerBuilder\Component\MessageElement;

class MessageElementMedia {

    /**
     * media_type type
     */
    const MEDIA_TYPE_IMAGE = "image";
    const MEDIA_TYPE_VIDEO = "video";

    private $type = null;
    private $url = null;
    private $buttons = [];

    public function __construct($type, $url, $buttons = []) {
        $this->type = $type;
        $this->url = $url;
        $this->buttons = $buttons;
    }

    public function getData() {
        $data = [
            'media_type' => $this->type,
            'url' => $this->url
        ];
        if (!empty($this->buttons)) {
            $data['buttons'] = [];
            foreach ($this->buttons as $btn) {
                $data['buttons'][] = $btn->getData();
            }
        }
        return $data;
    }

}
