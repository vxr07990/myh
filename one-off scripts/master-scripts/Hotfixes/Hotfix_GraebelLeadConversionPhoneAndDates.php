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


    
    echo "<br>Begin SIRVA Lead Conversion Hotfix for Phones<br>";
    
    include_once('vtlib/Vtiger/Menu.php');
    include_once('vtlib/Vtiger/Module.php');
    include_once('modules/ModTracker/ModTracker.php');
    include_once('modules/ModComments/ModComments.php');
    include_once 'includes/main/WebUI.php';
    include_once 'include/Webservices/Create.php';
    include_once 'modules/Users/Users.php';

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
    
    //lead field-name => [account, contact, opp, is editable ]
    $mappingFieldNames = array(
                               'origin_phone1'=>array('', '', 'origin_phone1', 1),
                               'origin_phone2'=>array('', '', 'origin_phone2', 1),
                               'destination_phone1'=>array('', '', 'destination_phone1', 1),
                               'destination_phone2'=>array('', '', 'destination_phone2', 1),
                               'pack'=>array('', '', 'pack_date', 1),
                               'pack_to'=>array('', '', 'pack_to_date', 1),
                               'preferred_ppdate'=>array('', '', 'preffered_ppdate', 1),
                               'load_from'=>array('', '', 'load_date', 1),
                               'load_to'=>array('', '', 'load_to_date', 1),
                               'preferred_pldate'=>array('', '', 'preferred_pldate', 1),
                               'deliver'=>array('', '', 'deliver_date', 1),
                               'deliver_to'=>array('', '', 'deliver_to_date', 1),
                               'preferred_pddate'=>array('', '', 'preferred_pddate', 1),
                               'origin_country'=>array('', '', 'origin_country', 1),
                               'destination_country'=>array('', '', 'destination_country', 1),
                               'origin_flightsofstairs'=>array('', '', 'origin_flightsofstairs', 1),
                               'destination_flightsofstairs'=>array('', '', 'destination_flightsofstairs', 1),
                               'origin_description'=>array('', '', 'origin_description', 1),
                               'destination_description'=>array('', '', 'destination_description', 1),
                        );

    print_r($mappingFieldNames);

    if (isset($opportunitiesTabid) && isset($leadsTabid) && isset($accountsTabid) && isset($contactsTabid)) {
        //Truncate vtiger_convertleadmapping so that correct mapping may be added
        //$sql = "TRUNCATE TABLE `vtiger_convertleadmapping`";
        //$db->pquery($sql, array());

        foreach ($mappingFieldNames as $fieldName=>$fieldMap) {
            createGraebelMappingRow($fieldName, $fieldMap);
        }
    }
    echo "<br>END SIRVA Lead Conversion Hotfix for Phones<br>";

    function createGraebelMappingRow($fieldName, $fieldMap)
    {
        global $db, $leadsTabid, $accountsTabid, $contactsTabid, $opportunitiesTabid;
        echo "Field $fieldName is being processed<br />";
        $sql = "SELECT fieldid FROM `vtiger_field` WHERE fieldname=? AND tabid=?";
        $leadsResult = $db->pquery($sql, array($fieldName, $leadsTabid));
        $accountsResult = $db->pquery($sql, array($fieldMap[0], $accountsTabid));
        $contactsResult = $db->pquery($sql, array($fieldMap[1], $contactsTabid));
        $opportunitiesResult = $db->pquery($sql, array($fieldMap[2], $opportunitiesTabid));

        $row = $leadsResult->fetchRow();
        if ($row == null) {
            echo "Fieldid not found for Leads field: $fieldName <br />";
            return;
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

        $check = "SELECT `cfmid` FROM `vtiger_convertleadmapping` WHERE leadfid = ? AND  accountfid = ? AND  contactfid = ? AND  potentialfid = ?";
        $res = $db->pquery($check, array($leadsFieldid, $accountsFieldid, $contactsFieldid, $opportunitiesFieldid));
        if ($res && $row = $res->fetchRow()) {
            print "UPDATING EXISTING ROW<br/>";
            $sql = "UPDATE `vtiger_convertleadmapping` SET
                    `leadfid` = ?,
                    `accountfid` = ?,
                    `contactfid` = ?,
                    `potentialfid` = ?,
                    `editable` = ?
                    WHERE `cfmid` = ?
                    LIMIT 1";
            $db->pquery($sql,
                        array($leadsFieldid, $accountsFieldid, $contactsFieldid, $opportunitiesFieldid,
                                    $fieldMap[3], $row['cfmid']));
        } else {
            $sql = "INSERT INTO `vtiger_convertleadmapping` (leadfid, accountfid, contactfid, potentialfid, editable) VALUES (?,?,?,?,?)";
            echo "Preparing to execute query: $sql <br /> with params : $leadsFieldid, $accountsFieldid, $contactsFieldid, $opportunitiesFieldid, ".$fieldMap[3]." <br />";
            $db->pquery($sql, array($leadsFieldid, $accountsFieldid, $contactsFieldid, $opportunitiesFieldid, $fieldMap[3]));
        }
    }


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";