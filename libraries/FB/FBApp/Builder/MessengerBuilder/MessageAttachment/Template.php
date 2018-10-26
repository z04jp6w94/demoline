<?php

namespace FB\FBApp\Builder\MessengerBuilder\MessageAttachment;

use FB\FBApp\Builder\MessengerBuilder\MessageAttachment;

class Template extends MessageAttachment {

    /**
     * template_type type
     */
    const TEMPLATE_TYPE_BUTTON = "button";
    const TEMPLATE_TYPE_GENERIC = "generic";
    const TEMPLATE_TYPE_LIST = "list";
    const TEMPLATE_TYPE_MEDIA = "media";
    const TEMPLATE_TYPE_RECEIPT = "receipt";
    const TEMPLATE_TYPE_AIRLINE_BOARDINGPASS = "airline_boardingpass";
    const IMAGE_ASPECT_RATIO_HORIZONTAL = "horizontal";
    const IMAGE_ASPECT_RATIO_SQUARE = "square";

    private $type = null;
    private $res = null;
    private $sharable = false; //generic|receipt
    private $image_aspect_ratio = self::IMAGE_ASPECT_RATIO_HORIZONTAL; //generic
    private $top_element_style = null; //list
    private $intro_message = null; //airline_boardingpass
    private $locale = null; //airline_boardingpass
    private $theme_color = null; //airline_boardingpass
    //button|list
    private $buttons = [];
    //generic|list|media
    private $elements = [];
    //airline_boardingpass
    private $boarding_pass = [];

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
                if (isset($res['sharable'])) {
                    $this->sharable = $res['sharable'];
                }
                if (isset($res['image_aspect_ratio'])) {
                    $this->image_aspect_ratio = $res['image_aspect_ratio'];
                }
                break;
            case self::TEMPLATE_TYPE_LIST:
                $this->elements = $res['elements'];
                if (isset($res['top_element_style'])) {
                    $this->top_element_style = $res['top_element_style'];
                }
                break;
            case self::TEMPLATE_TYPE_MEDIA:
                $this->elements = $res['elements'];
                break;
            case self::TEMPLATE_TYPE_RECEIPT:
                break;
            case self::TEMPLATE_TYPE_AIRLINE_BOARDINGPASS:
                $this->boarding_pass = $res['boarding_pass'];
                if (isset($res['intro_message'])) {
                    $this->intro_message = $res['intro_message'];
                }
                if (isset($res['locale'])) {
                    $this->locale = $res['locale'];
                }
                if (isset($res['theme_color'])) {
                    $this->theme_color = $res['theme_color'];
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
                $data['attachment']['payload']['sharable'] =  $this->sharable; //Han改,原false
                foreach ($this->elements as $element) {
                    $data['attachment']['payload']['elements'][] = $element->getData();
                }
                break;
            case self::TEMPLATE_TYPE_LIST:
                $data['attachment']['payload']['elements'] = [];
                $data['attachment']['payload']['top_element_style'] = $this->top_element_style;
                foreach ($this->elements as $element) {
                    $data['attachment']['payload']['elements'][] = $element->getData();
                }
                break;
            case self::TEMPLATE_TYPE_MEDIA:
                foreach ($this->elements as $element) {
                    $data['attachment']['payload']['elements'][] = $element->getData();
                }
                break;
            case self::TEMPLATE_TYPE_RECEIPT:
                break;
            case self::TEMPLATE_TYPE_AIRLINE_BOARDINGPASS:
                break;
        }
        return $data;
    }

}
