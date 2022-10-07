<?php

function get($key, $array, $default=false) {
    if(array_key_exists($key, $array)) {
        return $array[$key];
    }
    return $default;
}

function combine($a1, $a2) {
    foreach($a2 as $k => $v) {
        $a1[$k] = $v;
    }
    return $a1;
}

?>