<?php

namespace FB\FBApp\Builder\MessengerBuilder\Component;

class LocalizedGreeting {

    /**
     * locale type
     */
    const TYPE_DEFAULT = "default";
    const TYPE_EN_US = "en_US";
    const TYPE_ZH_TW = "zh_TW";

    private $locale;
    private $text;

    public function __construct($locale, $text) {
        $this->locale = $locale;
        $this->text = $text;
    }

    public function getData() {
        $data = [
            'locale' => $this->locale,
            'text' => $this->text
        ];
        return $data;
    }

}
