<?php

namespace FB\FBApp\Builder\MessengerBuilder;

class GetStarted {

    /**
     * get_started_type type
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
                $data['get_started'] = ['payload' => $this->res];
                break;
            case self::TYPE_DELETE:
                $data['fields'] = ['get_started'];
                break;
        }
        return $data;
    }

}
