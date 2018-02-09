<?php

namespace MoveCrm\Models;

require_once 'include/Webservices/Revise.php';

use InvalidArgumentException;
use MoveCrm\Traits\DateTime;
use PearDatabase;

class Entities
{
    use DateTime;

    public function __construct($id)
    {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException;
        }

        $this->id = $id;
    }

    public function updateModifiedTime()
    {
        $modified_time = $this->getCurrentDateTime();
        $params = [$modified_time, $this->id];
        $sql = 'UPDATE vtiger_crmentity SET modifiedtime = ? WHERE crmid = ?';
        $db = PearDatabase::getInstance();
        $result = $db->pquery($sql, $params);

        return ($result) ? $modified_time : false;
    }
}
