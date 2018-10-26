<?php

namespace FB\FBApp\Builder\MessengerBuilder\MessageCreatives;

use FB\FBApp\Builder\MessengerBuilder\MessageCreatives;

class DynamicText extends MessageCreatives {

    private $dynamic_text = null;
    private $fallback_text = null;

    public function __construct($text, $fallback_text) {
        $this->dynamic_text = $text;
        $this->fallback_text = $fallback_text;

    }

    public function getData() {
        $data = [
            'dynamic_text' => [
                'text' => $this->dynamic_text,
                'fallback_text' => $this->fallback_text
            ]
        ];
        return $data;
    }

}
