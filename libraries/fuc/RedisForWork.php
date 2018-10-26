<?php

class RedisForWork {

    private $_redisConnect; //儲存Redis連線

    function __construct() {
        $this->_redisConnect = new Redis();
        if (!$this->_redisConnect->connect(REDIS_HOST, REDIS_PORT, REDIS_TIMEOUT)) {
            error_log("Fail to connect to Redis: " . REDIS_HOST . ":" . REDIS_PORT);
            exit;
        }
        if (!empty(REDIS_PASSWORD) && !$this->_redisConnect->auth(REDIS_PASSWORD)) {
            error_log("Fail to connect to Redis: incorrect password");
            exit;
        }
    }

    public function getRedisSession($key) {
        return $this->_redisConnect->get($key);
    }

    public function setRedisSession($key, $value) {
        $this->_redisConnect->set($key, $value);
    }

    public function login($key, $value, $expire = 3600 * 24) {
        $this->_redisConnect->set($key, $value);
        $this->_redisConnect->expire($key, $expire);
    }

    public function logout($key) {
        $this->_redisConnect->del($key);
    }

}

?>