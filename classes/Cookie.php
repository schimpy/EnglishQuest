<?php

class Cookie
{
    public static function exists($name) {
        return (isset($_COOKIE[$name])) ? true : false;
    }

    public static function get($name) {
        return $_COOKIE[$name];
    }

    public static function put($name, $value, $expiration) {

        if(setcookie($name, $value, time() + $expiration, '/')) {
            return true;
        }

        return false;
    }

    public static function delete($name) {
        self::put($name, '', time() - 1);
    }
}