<?php

function log_v($v) {
    error_log(var_export($v, true), 0);
}

function print_v($v) {
    print(var_export($v, true));
}

?>