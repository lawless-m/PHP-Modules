<?php

require_once 'arrays.php';

function qs($key, $default=false) {
    $v = get($key, $_GET, '');
    if($v == '') {
        return $default;
    }
    return $v;
}

function qs_url() {
    return $_SERVER['QUERY_STRING'];
}


?>