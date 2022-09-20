<?php

function log_v($v) {
    error_log(var_export($v, true));
}

?>