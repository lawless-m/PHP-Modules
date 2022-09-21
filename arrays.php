<?php

function get($key, $array, $default=false) {
    if(array_key_exists($key, $array)) {
        return $array[$key];
    }
    return $default;
}
?>