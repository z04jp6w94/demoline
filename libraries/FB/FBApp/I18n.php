<?php

namespace FB\FBApp;

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
