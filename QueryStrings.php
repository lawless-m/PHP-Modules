<?php

require_once 'arrays.php';

function qs($key, $default=false) {
    return get($key, $_GET, $default);
}

function qs_url() {
    return $_SERVER['QUERY_STRING'];
}


?>