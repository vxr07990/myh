<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 9/29/2016
 * Time: 10:06 AM
 */

namespace MoveCrm;

class LogUtils
{
    public static function LogToFile($envVar, $data, $backTrace = false, $fileMode = FILE_APPEND, $options = DEBUG_BACKTRACE_IGNORE_ARGS)
    {
        if (!getenv($envVar)) {
            return;
        }

        $logFile = getenv($envVar.'_FILE');
        if (!$logFile) {
            return;
        }

        if(!is_string($data))
        {
            $data = print_r($data, true);
        }

        if (!preg_match('/\n$/', $data)) {
            $data .= PHP_EOL;
        }
        file_put_contents($logFile, $data, $fileMode);
        if ($backTrace) {
            file_put_contents($logFile, "DEBUG TRACE".PHP_EOL.print_r(debug_backtrace($options), true).PHP_EOL, FILE_APPEND);
        }
    }
}
