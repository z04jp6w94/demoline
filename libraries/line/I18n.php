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

namespace SCRM\BOT;

class I18n {

    const DEFAULT_LANGUAGE = 'zh-tw';
    const LANGUAGE_FOLDER = LANGUAGE_ROOT;

    private $_language = array();
    private $_preferredLanguage = null;

    function __construct($lang = '') {
        if (!empty($lang)) {
            if (file_exists(self::LANGUAGE_FOLDER . $lang)) {
                $this->_preferredLanguage = $lang;
                return;
            }
        }

        $this->_preferredLanguage = self::DEFAULT_LANGUAGE;
    }

    public function setPreferredLanguage($lang) {
        $this->_preferredLanguage = $lang;
    }

    public function getPreferredLanguage() {
        return $this->_preferredLanguage;
    }

    public function load($filename) {
        if (!empty($this->_language[$filename])) {
            return true;
        }

        $filepath = self::LANGUAGE_FOLDER . "{$this->_preferredLanguage}/{$filename}.php";
        if (file_exists($filepath)) {
            include($filepath);
            if (isset($lang)) {
                $this->_language[$filename] = $lang;
                unset($lang);
                return true;
            }
        }
        return false;
    }

    public function show($filename, $key) {
        if (isset($this->_language[$filename][$key])) {
            return $this->_language[$filename][$key];
        }
        return null;
    }

}
