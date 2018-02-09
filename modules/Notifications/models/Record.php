<?php

/* ********************************************************************************
 * The content of this file is subject to the Notifications ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

/**
 * Class Notifications_Record_Model
 */
class Notifications_Record_Model extends Vtiger_Record_Model
{

    /**
     * const
     */
    const NOTIFICATION_STATUS_NO = 'No';
    const NOTIFICATION_STATUS_YES = 'OK';

    /**
     * Function to get the Detail View url for the record
     * @return string - Record Detail View Url
     */
    public function getDetailViewUrl()
    {
        return 'index.php?module=Notifications&view=Detail&record=' . $this->getId();
    }

    /**
     * @param $userId
     * @return array
     */
    static function getNotificationsByUser($userId)
    {
        $db = PearDatabase::getInstance();
        $instances = array();
        $query = "SELECT * FROM vtiger_notifications AS notifications
                      INNER JOIN vtiger_crmentity AS crmentity ON (notifications.notificationid = crmentity.crmid AND crmentity.deleted = 0)
                    WHERE notifications.notification_status <> ? AND (crmentity.smownerid = ? OR crmentity.smownerid IN 
                          (SELECT groupid FROM vtiger_users2group AS users2group WHERE users2group.userid = ?))
                    GROUP BY crmentity.crmid;";
        $rs = $db->pquery($query, array(self::NOTIFICATION_STATUS_YES, $userId, $userId));
        if ($db->num_rows($rs)) {
            while ($data = $db->fetch_array($rs)) {
                $instances[] = new self($data);
            }
        }
        return $instances;
    }

    /**
     * @param $userId
     * @return int
     */
    static function countNotificationsByUser($userId)
    {
        $db = PearDatabase::getInstance();
        $alias_total = 'total';
        $query = "SELECT COUNT(`notifications`.`notificationid`) AS ? FROM vtiger_notifications AS notifications
                      INNER JOIN vtiger_crmentity AS crmentity ON (notifications.notificationid = crmentity.crmid AND crmentity.deleted = 0)
                    WHERE notifications.notification_status <> ? AND (crmentity.smownerid = ? OR crmentity.smownerid IN 
                          (SELECT groupid FROM vtiger_users2group AS users2group WHERE users2group.userid = ?))
                    GROUP BY crmentity.crmid;";
        $rs = $db->pquery($query, array($alias_total, self::NOTIFICATION_STATUS_YES, $userId, $userId));
        $total = 0;

        if ($db->num_rows($rs)) {
            while ($data = $db->fetch_array($rs)) {
                $total = intval($data[$alias_total]);
                break;
            }
        }

        return $total;
    }

    /**
     * @param int $id
     * @param $status
     * @return bool
     */
    public static function updateNotificationStatus($id, $status)
    {
        $db = PearDatabase::getInstance();
        $sql = "UPDATE vtiger_notifications
                SET notification_status = ?
                WHERE notificationid = ?";
        $params = array($status, $id);
        $result = $db->pquery($sql, $params);
        return $result ? true : false;
    }

}