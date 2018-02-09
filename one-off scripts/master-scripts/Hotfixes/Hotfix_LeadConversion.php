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


    
    echo "Begin Lead Conversion Hotfix";
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
    $mappingFieldNames = [
        'company'                     => ['accountname', '', 'potentialname', 0],
        'phone'                       => ['phone', 'phone', '', null],
        'email'                       => ['email1', 'email', '', 0],
        'description'                 => ['description', 'description', 'description', 1],
        'firstname'                   => ['', 'firstname', '', 0],
        'lastname'                    => ['', 'lastname', '', 0],
        'mobile'                      => ['', 'mobile', '', 1],
        'secondaryemail'              => ['', 'secondaryemail', '', 1],
        'leadsource'                  => ['', 'leadsource', 'leadsource', 1],
        'business_line'               => ['', '', 'business_line', 1],
        'origin_address1'             => ['', '', 'origin_address1', 1],
        'destination_address1'        => ['', '', 'destination_address1', 1],
        'origin_address2'             => ['', '', 'origin_address2', 1],
        'destination_address2'        => ['', '', 'destination_address2', 1],
        'origin_city'                 => ['', '', 'origin_city', 1],
        'destination_city'            => ['', '', 'destination_city', 1],
        'origin_state'                => ['', '', 'origin_state', 1],
        'destination_state'           => ['', '', 'destination_state', 1],
        'origin_zip'                  => ['', '', 'origin_zip', 1],
        'destination_zip'             => ['', '', 'destination_zip', 1],
        'pack'                        => ['', '', 'pack_date', 1],
        'pack_to'                     => ['', '', 'pack_to_date', 1],
        'load_from'                   => ['', '', 'load_date', 1],
        'load_to'                     => ['', '', 'load_to_date', 1],
        'deliver'                     => ['', '', 'deliver_date', 1],
        'deliver_to'                  => ['', '', 'deliver_to_date', 1],
        //'survey_date'=>array('', '', 'survey_date', 1),
        //'survey_time'=>array('', '', 'survey_time', 1),
        'follow_up'                   => ['', '', 'followup_date', 1],
        'decision'                    => ['', '', 'decision_date', 1],
        'shipper_type'                => ['', '', 'shipper_type', 1],
        'sales_person'                => ['', '', 'sales_person', 1],
        'agentid'                     => ['agentid', 'agentid', 'agentid', 1],
        'origin_phone1'               => ['', '', 'origin_phone1', 1],
        'destination_phone1'          => ['', '', 'destination_phone1', 1],
        'origin_phone2'               => ['', '', 'origin_phone2', 1],
        'destination_phone2'          => ['', '', 'destination_phone2', 1],
        'origin_country'              => ['', '', 'origin_country', 1],
        'destination_country'         => ['', '', 'destination_country', 1],
        'origin_description'          => ['', '', 'origin_description', 1],
        'destination_description'     => ['', '', 'destination_description', 1],
        'origin_flightsofstairs'      => ['', '', 'origin_flightsofstairs', 1],
        'destination_flightsofstairs' => ['', '', 'destination_flightsofstairs', 1],
        //preferred dates
        'preferred_ppdate'            => ['', '', 'preffered_ppdate', 1],  //not a typo
        'preferred_pldate'            => ['', '', 'preferred_pldate', 1],
        'preferred_pddate'            => ['', '', 'preferred_pddate', 1],
    ];
    
print_r($mappingFieldNames);
if (isset($opportunitiesTabid) && isset($leadsTabid) && isset($accountsTabid) && isset($contactsTabid)) {
    //Truncate vtiger_convertleadmapping so that correct mapping may be added
    $sql = "TRUNCATE TABLE `vtiger_convertleadmapping`";
    $db->pquery($sql, array());

    foreach ($mappingFieldNames as $fieldName=>$fieldMap) {
        createMappingRow($fieldName, $fieldMap);
    }

    createMappingRow('load_from', array('', '', 'closingdate', 1));
}

    function createMappingRow($fieldName, $fieldMap)
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