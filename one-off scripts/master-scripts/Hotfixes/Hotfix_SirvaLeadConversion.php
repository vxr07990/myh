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


    
    echo "<br>Begin SIRVA Lead Conversion Hotfix<br>";
    
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
                               'pack'=>array('', '', 'pack_date', 1),
                               'pack_to'=>array('', '', 'pack_to_date', 1),
                               'load_from'=>array('', '', 'load_date', 1),
                               'load_to'=>array('', '', 'load_to_date', 1),
                               'deliver'=>array('', '', 'deliver_date', 1),
                               'deliver_to'=>array('', '', 'deliver_to_date', 1),
                               //'survey_date'=>array('', '', 'survey_date', 1),
                               //'survey_time'=>array('', '', 'survey_time', 1),
                               'follow_up'=>array('', '', 'followup_date', 1),
                               'decision'=>array('', '', 'decision_date', 1),
                               'shipper_type'=>array('', '', 'shipper_type', 1),
                               'sales_person'=>array('', '', 'sales_person', 1),
                               'funded'=>array('', '', 'funded', 1),
                               'move_type'=>array('', '', 'move_type', 1),
                               'days_to_move'=>array('', '', 'days_to_move', 1),
                               'out_of_area'=>array('', '', 'out_of_area', 1),
                               'out_of_origin'=>array('', '', 'out_of_origin', 1),
                               'small_move'=>array('', '', 'small_move', 1),
                               'phone_estimate'=>array('', '', 'phone_estimate', 1),
                               'enabled'=>array('', '', 'enabled', 1),
                               'contact_name'=>array('', '', 'contact_name', 1),
                               'contact_email'=>array('', '', 'contact_email', 1),
                               'contact_phone'=>array('', '', 'contact_phone', 1),
                               'employer_comments'=>array('', '', 'employer_comments', 1),
                               'special_terms'=>array('', '', 'special_terms', 1),
                               'origin_phone1_type'=>array('', '', 'origin_phone1_type', 1),
                               'origin_phone1_ext'=>array('', '', 'origin_phone1_ext', 1),
                               'origin_phone2_type'=>array('', '', 'origin_phone2_type', 1),
                               'origin_phone2_ext'=>array('', '', 'origin_phone2_ext', 1),
                               'origin_description'=>array('', '', 'origin_description', 1),
                               'destination_phone1_type'=>array('', '', 'destination_phone1_type', 1),
                               'destination_phone1_ext'=>array('', '', 'destination_phone1_ext', 1),
                               'destination_phone2_type'=>array('', '', 'destination_phone2_type', 1),
                               'destination_phone2_ext'=>array('', '', 'destination_phone2_ext', 1),
                               'destination_description'=>array('', '', 'destination_description', 1),
                               'origin_fax'=>array('', '', 'origin_fax', 1),
                               'destination_fax'=>array('', '', 'destination_fax', 1),
                               'origin_country'=>array('', '', 'origin_country', 1),
                               'destination_country'=>array('', '', 'destination_country', 1),
                               'lead_type'=>array('', '', 'opp_type', 1),
                               'company'=>array('', '', 'company_name', 0),
                               'origin_phone1'=>array('', '', 'origin_phone1', 1),
                               'origin_phone2'=>array('', '', 'origin_phone2', 1),
                               'destination_phone1'=>array('', '', 'destination_phone1', 1),
                               'destination_phone2'=>array('', '', 'destination_phone2', 1),
                               'languages'=>array('', '', 'preferred_language', 1),
                               'agentid'=>array('agentid', 'agentid', 'agentid', 1),
                               'business_channel'=>array('', '', 'business_channel', 1),
                        );

    print_r($mappingFieldNames);

    if (isset($opportunitiesTabid) && isset($leadsTabid) && isset($accountsTabid) && isset($contactsTabid)) {
        //Truncate vtiger_convertleadmapping so that correct mapping may be added
        $sql = "TRUNCATE TABLE `vtiger_convertleadmapping`";
        $db->pquery($sql, array());

        foreach ($mappingFieldNames as $fieldName=>$fieldMap) {
            createSirvaMappingRow($fieldName, $fieldMap);
        }

        createSirvaMappingRow('load_from', array('', '', 'closingdate', 1));
    }

    function createSirvaMappingRow($fieldName, $fieldMap)
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

        $sql = "INSERT INTO `vtiger_convertleadmapping` (leadfid, accountfid, contactfid, potentialfid, editable) VALUES (?,?,?,?,?)";
        echo "Preparing to execute query: $sql <br /> with params : $leadsFieldid, $accountsFieldid, $contactsFieldid, $opportunitiesFieldid, ".$fieldMap[3]." <br />";
        $db->pquery($sql, array($leadsFieldid, $accountsFieldid, $contactsFieldid, $opportunitiesFieldid, $fieldMap[3]));
    }


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";