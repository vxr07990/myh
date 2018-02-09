<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";



include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');

$db = PearDatabase::getInstance();

    $sql = "SELECT tabid FROM `vtiger_tab` WHERE name=?";
    $result = $db->pquery($sql, array('Opportunities'));
    $row = $result->fetchRow();
    if ($row != null) {
        $opportunitiesTabid = $row[0];
    }
    
    $result = $db->pquery($sql, array('Leads'));
    $row = $result->fetchRow();
    if ($row != null) {
        $leadsTabid = $row[0];
    }
    
    $result = $db->pquery($sql, array('Accounts'));
    $row = $result->fetchRow();
    if ($row != null) {
        $accountsTabid = $row[0];
    }
    
    $result = $db->pquery($sql, array('Contacts'));
    $row = $result->fetchRow();
    if ($row != null) {
        $contactsTabid = $row[0];
    }
    
    $mapCount = 0;
    
    $mappingFieldNames = array('company'=>array('accountname', '', 'potentialname', 0),
                               'phone'=>array('phone', 'phone', '', null),
                               'email'=>array('email1', 'email', '', 0),
                               'description'=>array('description', 'description', 'description', 1),
                               'firstname'=>array('', 'firstname', '', 0),
                               'lastname'=>array('', 'lastname', '', 0),
                               'mobile'=>array('', 'mobile', '', 1),
                               'secondaryemail'=>array('', 'secondaryemail', '', 1),
                               'leadsource'=>array('', 'leadsource', 'leadsource', 1),
                               'business_line'=>array('', '', 'business_line', 1),
                               'origin_address1'=>array('', '', 'origin_address1', 1),
                               'destination_address1'=>array('', '', 'destination_address1', 1),
                               'origin_address2'=>array('', '', 'origin_address2', 1),
                               'destination_address2'=>array('', '', 'destination_address2', 1),
                               'origin_city'=>array('', '', 'origin_city', 1),
                               'destination_city'=>array('', '', 'destination_city', 1),
                               'origin_state'=>array('', '', 'origin_state', 1),
                               'destination_state'=>array('', '', 'destination_state', 1),
                               'origin_zip'=>array('', '', 'origin_zip', 1),
                               'destination_zip'=>array('', '', 'destination_zip', 1),
                               'origin_country'=>array('', '', 'origin_country', 1),
                               'destination_country'=>array('', '', 'destination_country', 1),
                               'origin_description'=>array('', '', 'origin_description', 1),
                               'destination_description'=>array('', '', 'destination_description', 1),
                               'origin_phone1'=>array('', '', 'origin_phone1', 1),
                               'origin_phone2'=>array('', '', 'origin_phone2', 1),
                               'destination_phone1'=>array('', '', 'destination_phone1', 1),
                               'destination_phone2'=>array('', '', 'destination_phone2', 1),
                               'preferred_pldate'=>array('', '', 'preferred_pldate', 1),
                               'preferred_pddate'=>array('', '', 'preferred_pddate', 1),
                               //'pack'=>array('', '', 'pack_date', 1),
                               //'pack_to'=>array('', '', 'pack_to_date', 1),
                               'load_from'=>array('', '', 'load_date', 1),
                               'load_to'=>array('', '', 'load_to_date', 1),
                               'deliver'=>array('', '', 'deliver_date', 1),
                               'deliver_to'=>array('', '', 'deliver_to_date', 1),
                               //'survey_date'=>array('', '', 'survey_date', 1),
                               //'survey_time'=>array('', '', 'survey_time', 1),
                               'follow_up'=>array('', '', 'followup_date', 1),
                               'decision'=>array('', '', 'decision_date', 1),
                               'load_from#2'=>array('', '', 'closingdate', 1),
                               'sales_person'=>array('', '', 'sales_person', 1)
                        );
    
    if (isset($opportunitiesTabid) && isset($leadsTabid) && isset($accountsTabid) && isset($contactsTabid)) {
        //Truncate vtiger_convertleadmapping so that correct mapping may be added
        $sql = "TRUNCATE TABLE `vtiger_convertleadmapping`";
        $db->pquery($sql, array());
        
        foreach ($mappingFieldNames as $fieldName=>$fieldMap) {
            if ($mapCount == 2) {
                $sql = "INSERT INTO `vtiger_convertleadmapping` (leadfid, accountfid, contactfid, potentialfid, editable) VALUES (0, 0, 0, 0,".DB_DataObject_Cast::sql('NULL').")";
                $db->pquery($sql, array());
                $mapCount++;
                continue;
            }
            $fName = strstr($fieldName, '#', true);
            
            $sql = "SELECT fieldid FROM `vtiger_field` WHERE fieldname=? AND tabid=?";
            $leadsResult = $db->pquery($sql, array(($fName === false ? $fieldName : $fName), $leadsTabid));
            $accountsResult = $db->pquery($sql, array($fieldMap[0], $accountsTabid));
            $contactsResult = $db->pquery($sql, array($fieldMap[1], $contactsTabid));
            $opportunitiesResult = $db->pquery($sql, array($fieldMap[2], $opportunitiesTabid));
            
            $row = $leadsResult->fetchRow();
            if ($row == null) {
                continue;
            }
            $leadsFieldid = $row[0];
            
            $row = $accountsResult->fetchRow();
            if ($row == null) {
                $accountsFieldid = 0;
            } else {
                $accountsFieldid = $row[0];
            }
            
            $row = $contactsResult->fetchRow();
            if ($row == null) {
                $contactsFieldid = 0;
            } else {
                $contactsFieldid = $row[0];
            }
            
            $row = $opportunitiesResult->fetchRow();
            if ($row == null) {
                $opportunitiesFieldid = 0;
            } else {
                $opportunitiesFieldid = $row[0];
            }
            
            $sql = "INSERT INTO `vtiger_convertleadmapping` (leadfid, accountfid, contactfid, potentialfid, editable) VALUES (?,?,?,?,?)";
            $db->pquery($sql, array($leadsFieldid, $accountsFieldid, $contactsFieldid, $opportunitiesFieldid, $fieldMap[3]));
        }
    }


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";