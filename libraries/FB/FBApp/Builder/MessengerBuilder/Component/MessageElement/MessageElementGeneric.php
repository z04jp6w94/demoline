<?php

namespace FB\FBApp\Builder\MessengerBuilder\Component\MessageElement;

class MessageElementGeneric {

    private $title = null;
    private $subtitle = null;
    private $item_url = null;
    private $image_url = null;
    private $buttons = [];
    private $default_action = null;

    public function __construct($title, $subtitle, $item_url = '', $image_url = '', $buttons = [], $default_action = null) {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->item_url = $item_url;
        $this->image_url = $image_url;
        $this->buttons = $buttons;
        $this->default_action = $default_action;
    }

    public function getData() {
        $data = [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'item_url' => $this->item_url,
            'image_url' => $this->image_url
        ];
        if (!empty($this->buttons)) {
            $data['buttons'] = [];
            foreach ($this->buttons as $btn) {
                $data['buttons'][] = $btn->getData();
            }
        }
        if (!empty($this->default_action)) {
            $data['default_action'] = $this->default_action->getData();
        }
        return $data;
    }

}
