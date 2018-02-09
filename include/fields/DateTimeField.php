<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'include/utils/utils.php';

class DateTimeField
{
    protected static $databaseTimeZone = null;
    protected $datetime;
    private static $cache            = [];

    /**
     * @param type $value
     */
    public function __construct($value)
    {
        if (empty($value)) {
            $value = date("Y-m-d H:i:s");
        }
        $this->date     = null;
        $this->time     = null;
        $this->datetime = $value;
    }

    /** Function to set date values compatible to database (YY_MM_DD)
     *
     * @param $user -- value :: Type Users
     * @returns $insert_date -- insert_date :: Type string
     */
    public function getDBInsertDateValue($user = null)
    {
        global $log;
        $log->debug("Entering getDBInsertDateValue(".$this->datetime.") method ...");
        $value = explode(' ', $this->datetime);
        if (count($value) == 2) {
            $value[0] = self::convertToUserFormat($value[0]);
        }
        $insert_time = '';
        if ($value[1] != '') {
            $date        = self::convertToDBTimeZone($this->datetime, $user);
            $insert_date = $date->format('Y-m-d');
        } else {
            $insert_date = self::convertToDBFormat($value[0]);
        }
        $log->debug("Exiting getDBInsertDateValue method ...");

        return $insert_date;
    }

    /**
     * @param Users $user
     *
     * @return String
     */
    public function getDBInsertDateTimeValue($user = null)
    {
        return $this->getDBInsertDateValue($user).' '.
               $this->getDBInsertTimeValue($user);
    }

    public function getDisplayDateTimeValue($user = null, $doConvert = false)
    {
        return $this->getDisplayDate($user).' '.$this->getDisplayTime($user, $doConvert);
    }

    public function getFullcalenderDateTimevalue($user = null)
    {
        return $this->getDisplayDate($user).' '.$this->getFullcalenderTime($user);
    }

    /**
     * @global Users $current_user
     *
     * @param type   $date
     * @param Users  $user
     *
     * @return type
     */
    public static function convertToDBFormat($date, $user = null)
    {
        global $current_user;
        if (empty($user)) {
            $user = $current_user;
        }
        $format = $user->date_format;
        if (empty($format)) {
            $format = 'dd-mm-yyyy';
        }
        // We need to avoid cases where the date is already in yyyy-mm-dd, even when the user's date format is not.
        if(strlen(explode('-',$date)[0]) == 4 && $format != 'yyyy-mm-dd') {
            return $date;
        }
        return self::__convertToDBFormat($date, $format);
    }

    /**
     * @param type   $date
     * @param string $format
     *
     * @return string
     */
    public static function __convertToDBFormat($date, $format)
    {
        if ($format == '') {
            $format = 'dd-mm-yyyy';
        }
        $dbDate = '';
        if ($format == 'dd-mm-yyyy') {
            list($d, $m, $y) = explode('-', $date);
        } elseif ($format == 'mm-dd-yyyy') {
            list($m, $d, $y) = explode('-', $date);
        } elseif ($format == 'yyyy-mm-dd') {
            list($y, $m, $d) = explode('-', $date);
        }
        if (!$y && !$m && !$d) {
            $dbDate = '';
        } else {
            // to allow people to enter less than 4 digit years
            $len = strlen($y);
            if($len < 4)
            {
                $n = date('Y');
                $y = substr($n,0,4 - $len) . $y;
            }
            $dbDate = $y.'-'.$m.'-'.$d;
        }

        return $dbDate;
    }

    /**
     * @param Mixed $date
     *
     * @return Array
     */
    public static function convertToInternalFormat($date)
    {
        if (!is_array($date)) {
            $date = explode(' ', $date);
        }

        return $date;
    }

    /**
     * @global Users $current_user
     *
     * @param type   $date
     * @param Users  $user
     *
     * @return type
     */
    public static function convertToUserFormat($date, $user = null)
    {
        global $current_user;
        if (empty($user)) {
            $user = $current_user;
        }
        $format = $user->date_format;
        if (empty($format)) {
            $format = 'dd-mm-yyyy';
        }

        return self::__convertToUserFormat($date, $format);
    }

    /**
     * @param type $date
     * @param type $format
     *
     * @return type
     */
    public static function __convertToUserFormat($date, $format)
    {
        //@NOTE: This assumes input date is yyyy-mm-dd
        $date = self::convertToInternalFormat($date);
        list($y, $m, $d) = explode('-', $date[0]);

        if ($format == 'dd-mm-yyyy') {
            $date[0] = $d.'-'.$m.'-'.$y;
        } elseif ($format == 'mm-dd-yyyy') {
            $date[0] = $m.'-'.$d.'-'.$y;
        } elseif ($format == 'yyyy-mm-dd') {
            $date[0] = $y.'-'.$m.'-'.$d;
        }
        if ($date[1] != '') {
            $userDate = $date[0].' '.$date[1];
        } else {
            $userDate = $date[0];
        }

        return $userDate;
    }

    /**
     * @param $date
     * @param $format
     *
     * @return string
     */
    public static function __convertToYMDTimeFormat($date, $format)
    {
        //@NOTE: if format is not matched then the effect is to strip anything that might be past the "time"
        $date = self::convertToInternalFormat($date);
        //This is spliting date time into split on ' '
        // date[0] = date
        // date[1] = time.

        $ymdDate = $date[0];
        list($one, $two, $three) = explode('-', $date[0]);

        if ($format == 'dd-mm-yyyy') {
            $ymdDate = $three.'-'.$two.'-'.$one;
        } elseif ($format == 'mm-dd-yyyy') {
            $ymdDate = $three.'-'.$one.'-'.$two;
        } elseif ($format == 'yyyy-mm-dd') {
            $ymdDate = $one.'-'.$two.'-'.$three;
        }

        if ($date[1] != '') {
            $ymdDate .= ' '.$date[1];
        }

        return $ymdDate;
    }


    /**
     * @global Users $current_user
     *
     * @param type   $value
     * @param Users  $user
     */
    public static function convertToUserTimeZone($value, $user = null)
    {
        global $current_user, $default_timezone;
        if (empty($user)) {
            $user = $current_user;
        }
        $timeZone = $user->time_zone?$user->time_zone:$default_timezone;

        return DateTimeField::convertTimeZone($value, self::getDBTimeZone(), $timeZone);
    }

    /**
     * @global Users $current_user
     *
     * @param type   $value
     * @param Users  $user
     */
    public static function convertToDBTimeZone($value, $user = null)
    {
        global $current_user, $default_timezone;
        if (empty($user)) {
            $user = $current_user;
        }
        $timeZone = $user->time_zone?$user->time_zone:$default_timezone;
        $value    = self::sanitizeDate($value, $user);

        return DateTimeField::convertTimeZone($value, $timeZone, self::getDBTimeZone());
    }

    /**
     * @param type $time
     * @param type $sourceTimeZoneName
     * @param type $targetTimeZoneName
     *
     * @return DateTime
     */
    public static function convertTimeZone($time, $sourceTimeZoneName, $targetTimeZoneName)
    {
        // TODO Caching is causing problem in getting the right date time format in Calendar module.
        // Need to figure out the root cause for the problem. Till then, disabling caching.
        //if(empty(self::$cache[$time][$targetTimeZoneName])) {
        // create datetime object for given time in source timezone
        $sourceTimeZone = new DateTimeZone($sourceTimeZoneName);
        if ($time == '24:00') {
            $time = '00:00';
        }
        $myDateTime = new DateTime($time, $sourceTimeZone);
        // convert this to target timezone using the DateTimeZone object
        $targetTimeZone = new DateTimeZone($targetTimeZoneName);
        $myDateTime->setTimeZone($targetTimeZone);
        self::$cache[$time][$targetTimeZoneName] = $myDateTime;
        //}
        $myDateTime = self::$cache[$time][$targetTimeZoneName];

        return $myDateTime;
    }

    /** Function to set timee values compatible to database (GMT)
     *
     * @param $user -- value :: Type Users
     * @returns $insert_date -- insert_date :: Type string
     */
    public function getDBInsertTimeValue($user = null)
    {
        global $log;
        $log->debug("Entering getDBInsertTimeValue(".$this->datetime.") method ...");
        $date = self::convertToDBTimeZone($this->datetime, $user);
        $log->debug("Exiting getDBInsertTimeValue method ...");

        return $date->format("H:i:s");
    }

    /**
     * This function returns the date in user specified format.
     * @global type  $log
     * @global Users $current_user
     * @return string
     */
    public function getDisplayDate($user = null)
    {
        global $log;
        $log->debug("Entering getDisplayDate(".$this->datetime.") method ...");
        $date_value = explode(' ', $this->datetime);
        if ($date_value[1] != '') {
            $date       = new DateTime($this->datetime);//self::convertToUserTimeZone($this->datetime, $user);
            $date_value = $date->format('Y-m-d');
        }
        $display_date = self::convertToUserFormat($date_value, $user);
        $log->debug("Exiting getDisplayDate method ...");

        return $display_date;
    }

    public function getDisplayDateInTimeZone($timezone=null, $user=null) {
        if($timezone == null) {
            return $this->getDisplayDate($user);
        }

        $datetime = self::convertTimeZone($this->datetime, self::getDBTimeZone(), $timezone);

        if(!$user) {
            $user = Users_Privileges_Model::getCurrentUserModel();
        }

        return Vtiger_Date_UIType::getDisplayDateValue($datetime->format('Y-m-d'));
    }

    public function getDisplayTimeInTimeZone($timezone=null) {
        if($timezone == null) {
            return $this->getDisplayTime();
        }

        $date = self::convertTimeZone($this->datetime, self::getDBTimeZone(), $timezone);
        $time = $date->format('H:i:s');

        $userModel = Users_Privileges_Model::getCurrentUserModel();
        if ($userModel->get('hour_format') == '12') {
            $time = Vtiger_Time_UIType::getTimeValueInAMorPM($time);
        }

        return $time;
    }

    public function getDisplayTime($user = null, $doConvert = false)
    {
        global $log;
        $log->debug("Entering getDisplayTime(".$this->datetime.") method ...");
        if($doConvert) {
            $date = self::convertToUserTimeZone($this->datetime, $user);
        } else {
            $date = self::convertTimeZone($this->datetime, self::getDBTimeZone(), self::getDBTimeZone());
        }
        $time = $date->format("H:i:s");
        $log->debug("Exiting getDisplayTime method ...");
        //Convert time to user preferred value
        $userModel = Users_Privileges_Model::getCurrentUserModel();
        if ($userModel->get('hour_format') == '12') {
            $time = Vtiger_Time_UIType::getTimeValueInAMorPM($time);
        }

        return $time;
    }

    public function getFullcalenderTime($user = null)
    {
        global $log;
        $log->debug("Entering getDisplayTime(".$this->datetime.") method ...");
        $date = self::convertToUserTimeZone($this->datetime, $user);
        $time = $date->format("H:i:s");
        $log->debug("Exiting getDisplayTime method ...");

        return $time;
    }

    public static function getDBTimeZone()
    {
        if (empty(self::$databaseTimeZone)) {
            $defaultTimeZone = date_default_timezone_get();
            if (empty($defaultTimeZone)) {
                $defaultTimeZone = 'UTC';
            }
            self::$databaseTimeZone = $defaultTimeZone;
        }

        return self::$databaseTimeZone;
    }

    public static function getPHPDateFormat($user = null)
    {
        global $current_user;
        if (empty($user)) {
            $user = $current_user;
        }

        return str_replace(['yyyy', 'mm', 'dd'], ['Y', 'm', 'd'], $user->date_format);
    }

    private static function sanitizeDate($value, $user)
    {
        global $current_user;
        if (empty($user)) {
            $user = $current_user;
        }
        if ($user->date_format == 'mm-dd-yyyy') {
            list($date, $time) = explode(' ', $value);
            if (!empty($date)) {
                list($m, $d, $y) = explode('-', $date);
                if (strlen($m) < 3) {
                    $time  = ' '.$time;
                    $value = "$y-$m-$d".rtrim($time);
                }
            }
        }

        return $value;
    }
}
