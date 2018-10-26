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

namespace SCRM\BOT\LOGIN;

use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

class Push {

    /** @var LINEBot $bot */
    private $_bot;

    /** @var */
    private $_mysqli;
    private $_OA_Id;
    private $_access_token;
    private $_stable_url;
    private $p_id;
    private $c_id;
    private $_para;

    /**
     * @param TextMessage $textMessage
     * @param $bot
     * @param DatabaseProcessor $databaseProcessor
     * @param \Redis $redis
     * @param I18n $i18n
     * @param integer $channelId
     */
    public function __construct($bot, $mysqli, $OA_Id, $access_token, $stable_url, $p_id, $c_id, $para) {
        $this->_bot = $bot;
        $this->_mysqli = $mysqli;
        $this->_OA_Id = $OA_Id;
        $this->_access_token = $access_token;
        $this->_stable_url = $stable_url;
        $this->p_id = $p_id;
        $this->c_id = $c_id;
        $this->_para = $para;
    }

    public function PushToUser() {
        $para = rawurlencode($this->_para);
        $post_data = $this->_stable_url . "?para=$para";
        $result = Get_oauth_accessToken($post_data);
        $jsonObj = json_decode($result);
        $access_token = $jsonObj->{"access_token"};
        $id_token = $jsonObj->{"id_token"};
        $scope = $jsonObj->{"scope"};
        $sql = " select c_id, p_url from push_m ";
        $sql .= " where p_id = ? ";
        $p_ary = $this->_mysqli->readArrayPreSTMT($sql, "s", array($p_id), 2);
        //ProFile
        $result2 = LINE_LOGIN_GetProFile($this->_stable_url, $access_token);
        $jsonObj2 = json_decode($result2);
        $userId = $jsonObj2->{"userId"};
        $displayName = $jsonObj2->{"displayName"};
        $pictureUrl = $jsonObj2->{"pictureUrl"};
        //
        $sql = " SELECT COUNT(mlm_id) FROM member_list_m ";
        $sql .= " WHERE c_id = ? ";
        $sql .= " AND mlm_lineid = ? ";
        $rsVal = $this->_mysqli->readValuePreSTMT($sql, "ss", array($c_id, $userId));
        if ($rsVal == '0') {
            header("location:line://ti/p/" . $this->_OA_Id . "");
            exit();
        }
        $linelogin = new \LineLogin($this->_mysqli);
        $linelogin->_INTO_MEMBER_TAG($p_id, $c_id, $userId, "1");
        $url = $p_ary[0][1];
        header("location:" . $url . "");
        exit();
    }

}
