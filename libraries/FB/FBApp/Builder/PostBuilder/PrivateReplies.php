<?php

namespace FB\FBApp\Builder\PostBuilder;

class PrivateReplies {

    private $message = null;

    public function __construct($message) {
        $this->message = $message;
    }

    public function getData() {
        $data = [
            'message' => $this->message
        ];
        return $data;
    }

}
