<?php

/**
 * Copyright 2017 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */
/**
 * Webhook for  2017 event bot
 */
require_once($_SERVER['DOCUMENT_ROOT'] . "/config/chiliman_config.php");
require_once(AUTOLOAD_PATH);
require LIBRARY_ROOT_PATH . '/S3/s3.php';

use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\Exception\UnknownEventTypeException;
use LINE\LINEBot\Exception\UnknownMessageTypeException;
use SCRM\BOT\DatabaseProcessor;
use SCRM\BOT\EventHandler;
use SCRM\BOT\EventProcessor;
use SCRM\BOT\I18n;

/*
 * Validate signature in HTTP header
 */
if (isset($_SERVER['HTTP_' . HTTPHeader::LINE_SIGNATURE])) {
    $signature = $_SERVER['HTTP_' . HTTPHeader::LINE_SIGNATURE];
}

if (empty($signature)) {
    error_log("Received a request without channel signature");
    http_response_code(400);
    echo "Please provide valid signature";
    exit;
}
/**/
$channel_id = CHANNEL_ID;
$secret = CHANNEL_SECRET;
$access_token = CHANNEL_ACCESS_TOKEN;

$bot = new LINEBot(new \LINE\LINEBot\HTTPClient\CurlHTTPClient($access_token), ['channelSecret' => $secret]);
$httpRequestBody = file_get_contents('php://input');

try {
    $events = $bot->parseEventRequest($httpRequestBody, $signature);
} catch (InvalidSignatureException $e) {
    error_log("Received a request with an invalid channel signature: {$signature}");
    http_response_code(403);
    echo "Invalid signature";
    exit;
} catch (UnknownEventTypeException $e) {
    error_log("Received unknown event type");
    http_response_code(400);
    echo "Unknown event type";
    exit;
} catch (UnknownMessageTypeException $e) {
    error_log("Received unknown message type");
    http_response_code(400);
    echo "Unknown message type";
    exit;
} catch (InvalidEventRequestException $e) {
    error_log("Received invalid event request");
    http_response_code(400);
    echo "Invalid event request";
    exit;
}

session_start();
$sessionId = session_id();

/*
 * Close HTTP connection
 */
ob_start();
echo "ok";
header('Content-Length: ' . ob_get_length());
header('Connection: close');
ob_end_flush();
ob_flush();
flush();
if (session_id()) {
    session_write_close();
}

/*
 * Load necessary libraries
 */
/*
 * Process events
 */
$dataBase = new DatabaseProcessorForWork;
$databaseProcessor = new DatabaseProcessor($dataBase);

$s3 = new s3(CDN_REGION, CDN_VERSION, CDN_PATH, CDN_PROFILE, CDN_BUCKET);

$redis = new \Redis();
if (!$redis->connect(REDIS_HOST, REDIS_PORT, REDIS_TIMEOUT)) {
    error_log("Fail to connect to Redis: " . REDIS_HOST . ":" . REDIS_PORT);
    exit;
}
if (!empty(REDIS_PASSWORD) && !$redis->auth(REDIS_PASSWORD)) {
    error_log("Fail to connect to Redis: incorrect password");
    exit;
}
$i18n = new I18n();
$eventProcesssor = new EventProcessor($bot, $databaseProcessor, $redis, $i18n, $channel_id, $access_token, $s3);

foreach ($events as $event) {
    if ($redis->get("UserSession-{$event->getUserId()}") == false) {
        $redis->set("UserSession-{$event->getUserId()}", $sessionId);
    }
    $redis->expireAt("UserSession-{$event->getUserId()}", time(null) + 86400);

    $eventProcesssor->processEvent($event);
}

$redis->close();
?>
