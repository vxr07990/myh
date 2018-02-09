<?php

class PushNotifications_Module_Model extends Vtiger_Module_Model
{

    /**
     * Function to get the url for list view of the module
     * @return <string> - url
     */
    public function getListViewUrl()
    {
        // Return url for parent record instead of list view since this module should only be accessed through related lists
        $record = $_REQUEST['record'];
        if ($record != null) {
            $db = PearDatabase::getInstance();
            $sql = "SELECT agentid FROM `vtiger_crmentity` WHERE crmid=?";
            $result = $db->pquery($sql, [$record]);

            $parentRecord = $result->fields['agentid'];
            $sql = "SELECT setype FROM `vtiger_crmentity` WHERE crmid=?";
            $result = $db->pquery($sql, [$parentRecord]);
            $parentModule = $result->fields['setype'];
        } else {
            $parentModule = $_REQUEST['sourceModule'];
            $parentRecord = $_REQUEST['sourceRecord'];
        }

        return 'index.php?module='.$parentModule.'&view=Detail&record='.$parentRecord;
    }
}
