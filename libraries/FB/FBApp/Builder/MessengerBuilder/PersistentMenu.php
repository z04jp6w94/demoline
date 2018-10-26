<?php

namespace FB\FBApp\Builder\MessengerBuilder;

class PersistentMenu {

    /**
     * persistent_menu_type type
     */
    const TYPE_CREATE = "create";
    const TYPE_DELETE = "delete";

    protected $type = null;
    protected $res = null;

    public function __construct($type, $res = '') {
        $this->type = $type;
        $this->res = $res;
    }

    public function getType() {
        return $this->type;
    }

    public function getData() {
        $data = [];
        switch ($this->type) {
            case self::TYPE_CREATE:
                $data['persistent_menu'] = [];
                foreach ($this->res as $localizedPersistentMenu) {
                    $data['persistent_menu'][] = $localizedPersistentMenu->getData();
                }
                break;
            case self::TYPE_DELETE:
                $data['fields'] = ['persistent_menu'];
                break;
        }
        return $data;
    }

}
