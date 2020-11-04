<?php

namespace narad1972\TwitterClient\Utils;

function array_get($array, $key, $default) {
    if (array_key_exists($key, $array)) {
        return $array[$key];
    }
    return $default;
}

?>
