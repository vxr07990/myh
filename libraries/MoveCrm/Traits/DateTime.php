<?php

namespace MoveCrm\Traits;

use PearDatabase;

trait DateTime
{
    public function getCurrentDateTime()
    {
        $db = PearDatabase::getInstance();
        $strip_quotes = true;
        $date_time = date('Y-m-d H:i:s');

        return $db->formatDate($date_time, $strip_quotes);
    }
}
