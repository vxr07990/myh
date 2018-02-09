<?php

namespace MoveCrm\Models;

use JsonSerializable;
use MoveCrm\Traits\Reflection;
use stdClass;
use UnexpectedValueException;
use Vtiger\Models\Activity as VtigerActivity;

class Activity implements JsonSerializable
{
    use Reflection;

    /** @var int */
    public $activityId; //= 0;

    /** @var string */
    public $subject;

    /** @var string */
    public $semodule; //= null;

    /** @var string */
    public $activitytype;

    /** @var string */
    public $date_start;

    /** @var string */
    public $due_date; //= null;

    /** @var string */
    public $time_start; //= null;

    /** @var string */
    public $time_end; //= null;

    /** @var int */
    public $sendnotification; //= 0;

    /** @var int */
    public $duration_hours; //= null;

    /** @var int */
    public $duration_minutes; //= null;

    /** @var string */
    public $status; //= null;

    /** @var string */
    public $eventstatus; //= null;

    /** @var string */
    public $priority; //= null;

    /** @var string */
    public $location; //= null;

    /** @var string A three character max field */
    public $notime; //= 0;

    /** @var string */
    public $visibility; //= 'all';

    /** @var string */
    public $recurringtype; //= null;

    /**
     * Activity constructor.
     *
     * @param array $parameters
     */
    public function __construct($parameters = [])
    {
        ### file_put_contents('_params.log', print_r($parameters, true) . "\n\n", \FILE_APPEND);

        foreach ($parameters as $parameter => $value) {

            // If the provided parameter is not a class property skip it
            if (!property_exists(__CLASS__, $parameter)) {
                continue;
            }

            $this->{$parameter} = $value;
        }
    }

    /**
     * Check if the current model exists in the database.
     *
     * @return bool
     */
    public function exists()
    {
        try {
            $activities = VtigerActivity::all();
            file_put_contents('_vtiger-activity-orm.log', print_r($activities, true), \FILE_APPEND);
            $properties = self::_getPublicProperties();

            foreach ($properties as $property) {
                $name  = $property->name;
                $value = $this->{$name};

                if ($value === null) {
                    continue;
                }

                $activities = $activities->where($name, $value);
            }
        } catch (\Exception $e) {
            throw new UnexpectedValueException($e->getMessage() . ' | ' . $e->getTraceAsString());
        }

        return ($activities->count() >= 1);

        /*
        $properties = self::_getPublicProperties();
        $query      = ['SELECT COUNT(activityid) FROM vtiger_activity', 'WHERE'];
        $parameters = [];

        foreach ($properties as $property) {
            $name  = $property->name;
            $value = $this->{$name};

            if ($value === null) {
                continue;
            }

            $parameters[] = "{$name} = '{$this->{$name}}'";
        }

        $db         = PearDatabase::getInstance();
        $parameters = implode(' AND ', $parameters);
        $query[]    = $parameters;
        $sql        = implode(' ', $query);
        $count      = $db->getOne($sql);

        ### file_put_contents('_json.log', json_encode($this) . "\n\n", \FILE_APPEND);
        ### file_put_contents('_debug.log', "{$sql}\n\n", \FILE_APPEND);

        if (!is_numeric($count)) {
            throw new UnexpectedValueException('Received non-numeric count. ' . "[{$sql}] " . print_r($count, true));
        }

        return ($count >= 1);
        */
    }

    /**
     * Implementation of the `JsonSerializable` interface.
     * @link http://php.net/manual/en/class.jsonserializable.php
     *
     * @return stdClass
     */
    public function jsonSerialize()
    {
        $properties = self::_getPublicProperties();
        $json = new stdClass;

        foreach ($properties as $property) {
            $name = $property->name;
            $json->{$name} = $this->{$name};
        }

        return $json;
    }
}
