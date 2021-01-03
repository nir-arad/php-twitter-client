<?php

namespace NirArad\TwitterClient;

class Utils {

    static function array_get($array, $key, $default) {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        return $default;
    }
    
}

