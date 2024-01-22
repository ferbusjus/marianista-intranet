<?php

class Nativesession {

    public function __construct() {
        session_start();
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function regenerateId($delOld = false) {
        session_regenerate_id($delOld);
    }

    public function verifica($key) {
        if (!empty($_SESSION[$key])) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($key) {
        unset($_SESSION[$key]);
    }

}
