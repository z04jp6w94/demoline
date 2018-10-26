<?php

namespace FB\FBApp\Builder\MessengerBuilder;

class MessageCreatives extends Message {

    const CREATIVES_TYPE_TEXT = "text";
    const CREATIVES_TYPE_TEMPLATE = "template";

    private $type = null;
    private $res = null;

    public function __construct($type, $res) {
        $this->type = $type;
        $this->res = $res;
    }

    public function getData() {
        switch ($this->type) {
            case self::CREATIVES_TYPE_TEXT:
                $data['messages'][] = $this->res->getData();
                break;
            case self::CREATIVES_TYPE_TEMPLATE:
                $data['messages'][] = $this->res->getData();
                break;
        }
        return $data;
    }

}
