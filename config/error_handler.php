<?php

set_error_handler(function ($errno, $message, $file, $line, array $context) {
    if (!(error_reporting() & $errno)) {
        // The error code is not included in `error_reporting`
        return;
    }

    $code = 0;

    throw new \ErrorException($message, $code, $errno, $file, $line);
});
