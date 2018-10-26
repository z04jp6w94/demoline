<?php

namespace FB\FBApp\Builder\MessengerBuilder;

class Greeting {

    /**
     * greeting_type type
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
        switch ($this->type) {
            case self::TYPE_CREATE:
                $data['greeting'] = [];
                foreach ($this->res as $localizedGreeting) {
                    $data['greeting'][] = $localizedGreeting->getData();
                }
                break;
            case self::TYPE_DELETE:
                $data['fields'] = ['greeting'];
                break;
        }
        return $data;
    }

}
