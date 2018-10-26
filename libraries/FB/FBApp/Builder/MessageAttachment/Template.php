<?php

namespace FB\FBApp\Builder\MessageAttachment;

use FB\FBApp\Builder\MessageAttachment;

class Template extends MessageAttachment {

    /**
     * template_type type
     */
    const TEMPLATE_TYPE_BUTTON = "button";
    const TEMPLATE_TYPE_GENERIC = "generic";
    const TEMPLATE_TYPE_LIST = "list";
    const TEMPLATE_TYPE_RECEIPT = "receipt";
    const IMAGE_ASPECT_RATIO_HORIZONTAL = "horizontal";
    const IMAGE_ASPECT_RATIO_SQUARE = "square";

    private $type = null;
    private $res = null;
    private $image_aspect_ratio = self::IMAGE_ASPECT_RATIO_HORIZONTAL;
    //button
    private $buttons = [];
    //generic
    private $elements = [];

    public function __construct($type, $res, $quick_replies = array()) {
        $this->type = $type;
        $this->res = $res;
        $this->quick_replies = $quick_replies;

        switch ($type) {
            case self::TEMPLATE_TYPE_BUTTON:
                $this->text = $res['text'];
                $this->buttons = $res['buttons'];
                break;
            case self::TEMPLATE_TYPE_GENERIC:
                $this->elements = $res['elements'];
                if (isset($res['image_aspect_ratio'])) {
                    $this->image_aspect_ratio = $res['image_aspect_ratio'];
                }
                break;
        }
    }

    public function getData() {
        $data = [
            'attachment' => [
                'type' => MessageAttachment::TYPE_TEMPLATE,
                'payload' => [
                    'template_type' => $this->type
                ]
            ]
        ];
        foreach ($this->quick_replies as $qr) {
            $data['quick_replies'][] = $qr->getData();
        }
        switch ($this->type) {
            case self::TEMPLATE_TYPE_BUTTON:
                $data['attachment']['payload']['text'] = $this->text;
                $data['attachment']['payload']['buttons'] = [];
                foreach ($this->buttons as $btn) {
                    $data['attachment']['payload']['buttons'][] = $btn->getData();
                }
                break;
            case self::TEMPLATE_TYPE_GENERIC:
                $data['attachment']['payload']['elements'] = [];
                $data['attachment']['payload']['image_aspect_ratio'] = $this->image_aspect_ratio;
                foreach ($this->elements as $btn) {
                    $data['attachment']['payload']['elements'][] = $btn->getData();
                }
                break;
        }
        return $data;
    }

}
