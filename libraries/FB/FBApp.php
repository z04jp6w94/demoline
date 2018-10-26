<?php

namespace FB;

use FB\FBApp\Event\Parser\EventRequestParser;
use Facebook\Facebook;

class FBApp {

    /**
     * api_type type
     */
    const API_TYPE_POST_FEED = "feed";
    const API_TYPE_POST_PHOTOS = "photos";
    const API_TYPE_POST_PRIVATE_REPLIES = "private_replies";
    const API_TYPE_MESSENGER_MESSAGES = "messages";
    const API_TYPE_MESSENGER_MESSENGER_PROFILE = "messenger_profile";
    const API_TYPE_MESSENGER_MESSAGE_CREATIVES = "message_creatives";
    const API_TYPE_MESSENGER_MESSAGE_ATTACHMENTS = "message_attachments";
    const API_TYPE_MESSENGER_BROADCAST_MESSAGES = "broadcast_messages";
    const API_TYPE_MESSENGER_INSIGHTS_MESSAGES_SENT = "insights/messages_sent";

    private $appId;
    private $secret;
    private $accessToken;
    private $graphVersion;

    public function __construct($appId, $secret, $accessToken, $graphVersion) {
        $this->appId = $appId;
        $this->secret = $secret;
        $this->accessToken = $accessToken;
        $this->graphVersion = $graphVersion;
    }

    public function parseEventRequest($body, $fb_page_id) {
        return EventRequestParser::parseEventRequest($body, $fb_page_id);
    }

    public function send($type, $target, $message) {
        switch ($type) {
            case self::API_TYPE_POST_FEED:
                return $this->post('/' . $target . '/feed', $message->getData());
            case self::API_TYPE_POST_PHOTOS:
                return $this->post('/' . $target . '/photos', $message->getData());
            case self::API_TYPE_POST_PRIVATE_REPLIES:
                return $this->post('/' . $target . '/private_replies', $message->getData());
            case self::API_TYPE_MESSENGER_MESSAGES:
                return $this->post('/' . $target . '/messages', $message->getData());
            case self::API_TYPE_MESSENGER_MESSENGER_PROFILE:
                if ($message->getType() === "create")
                    return $this->post('/' . $target . '/messenger_profile', $message->getData());
                if ($message->getType() === "delete")
                    return $this->delete('/' . $target . '/messenger_profile', $message->getData());
            case self::API_TYPE_MESSENGER_MESSAGE_ATTACHMENTS:
                return $this->post('/' . $target . '/message_attachments', $message->getData());
            case self::API_TYPE_MESSENGER_MESSAGE_CREATIVES:
                return $this->post('/' . $target . '/message_creatives', $message->getData());
            case self::API_TYPE_MESSENGER_BROADCAST_MESSAGES:
                return $this->post('/' . $target . '/broadcast_messages', $message->getData());
            case self::API_TYPE_MESSENGER_INSIGHTS_MESSAGES_SENT:
                return $this->post('/' . $target . '/insights/messages_sent', $message->getData());
        }
    }

    public function get($id) {
        $fb = new Facebook([
            'app_id' => $this->appId,
            'app_secret' => $this->secret,
            'default_access_token' => $this->accessToken,
            'default_graph_version' => $this->graphVersion
        ]);

        try {
            $response = $fb->get($id);
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $graphNode = $response->getGraphNode();
        $fieldsAry = ["first_name", "last_name", "profile_pic", "locale", "timezone", "gender", "is_payment_enabled", "last_ad_referral"];
        $data = [];
        foreach ($fieldsAry as $field) {
            $data[$field] = !empty($graphNode[$field]) ? $graphNode[$field] : "";
        }
        return $data;
    }

    public function post($uri, $data) {
        $fb = new Facebook([
            'app_id' => $this->appId,
            'app_secret' => $this->secret,
            'default_access_token' => $this->accessToken,
            'default_graph_version' => $this->graphVersion
        ]);

        try {
            $response = $fb->post($uri, $data);
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $graphNode = $response->getGraphNode();
        return $graphNode;
    }

    public function delete($uri, $data) {
        $fb = new Facebook([
            'app_id' => $this->appId,
            'app_secret' => $this->secret,
            'default_access_token' => $this->accessToken,
            'default_graph_version' => $this->graphVersion
        ]);

        try {
            $response = $fb->delete($uri, $data);
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $graphNode = $response->getGraphNode();
        return $graphNode;
    }

}
