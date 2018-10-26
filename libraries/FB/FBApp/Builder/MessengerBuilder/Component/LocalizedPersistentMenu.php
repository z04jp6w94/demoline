<?php

namespace FB\FBApp\Builder\MessengerBuilder\Component;

class LocalizedPersistentMenu {

    /**
     * locale type
     */
    const TYPE_DEFAULT = "default";
    const TYPE_EN_US = "en_US";
    const TYPE_ZH_TW = "zh_TW";

    private $locale;
    private $composer_input_disabled;
    private $menuItems;

    public function __construct($locale, $composer_input_disabled, $menuItems = null) {
        $this->locale = $locale;
        $this->composer_input_disabled = $composer_input_disabled;
        $this->menuItems = $menuItems;
    }

    public function getData() {
        $data = [
            'locale' => $this->locale,
            'composer_input_disabled' => $this->composer_input_disabled
        ];
        if (isset($this->menuItems)) {
            foreach ($this->menuItems as $menuItem) {
                $data['call_to_actions'][] = $menuItem->getData();
            }
        }
        return $data;
    }

}
