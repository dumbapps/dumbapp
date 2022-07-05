<?php

class Request {
    public static function get($var, $default = null) {
        if(isset($_GET[$var]) ) {
            return $_GET[$var];
        } else {
            return $default;
        }
    }

    public static function post($var, $default = null) {
        if(isset($_POST[$var]) ) {
            return $_POST[$var];
        } else {
            return $default;
        }
    }

    public static function all($var, $default = null) {
        if(isset($_GET[$var]) ) {
            return $_GET[$var];
        } else if(isset($_POST[$var]) ) {
            return $_POST[$var];
        } else {
            return $default;
        }
    }

    public static function file($var) {
        if(isset($_FILES[$var]) && $_FILES[$var]["error"] == 0) {
            return $_FILES[$var];
        } else {
            return null;
        }
    }

    public static function cookie($var) {
        if(isset($_COOKIE[$var])) {
            return $_COOKIE[$var];
        } else {
            return null;
        }
    }

    public static function addcookie($var, $value, $days) {
        setcookie($var, $value, time() + (86400 * $days), "/");
    }
}