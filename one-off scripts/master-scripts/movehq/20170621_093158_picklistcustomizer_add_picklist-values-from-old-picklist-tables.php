<?php

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "RUNNING: " . __FILE__ . "<br />\n";

// OT3997 - Picklist Customizer Updates
// OT4807 - Change picklist customizer to use the new picklist uitype
//remove agentmanager_id column from those tables

echo 'Start: Migrate Picklist Values <br />';

$fieldsToMigrate = [
    'leadsource',
    'reason_cancelled',
    'opportunityreason'
];

$db = PearDatabase::getInstance();
foreach( $fieldsToMigrate as $fieldName ){
    //get the values from the original tables
    $valuesToMigrate = [];
    $sql = "SELECT * FROM vtiger_$fieldName WHERE agentmanager_id IS NOT NULL";
    $result = $db->pquery($sql);
    if($db->num_rows($result) > 0){
        while($row = $db->fetchByAssoc($result)){
            $valuesToMigrate[$row['agentmanager_id']][] = $row[$fieldName.'id'];
        }
        echo "$fieldName values loaded from vtiger_$fieldName<br>";
    }
    
    //save the values to the custompicklist table
    if ( count($valuesToMigrate) > 0 ) {
        $sqlInsert = "INSERT INTO vtiger_custompicklist (fieldid, agentid, valueid) VALUES ";
        foreach ($valuesToMigrate as $agentid => $data ) {
            foreach ($data as $valueid ) {
                $sqlInsert .= "('$fieldName', $agentid, $valueid), ";
            }
        }
        $sqlInsert = substr($sqlInsert, 0,-2);
        $insert = $db->pquery($sqlInsert);
        echo "$fieldName values inserted into vtiger_custompicklist<br>";
    }
    
    //remove agentmanager_id column from those tables
    $sqlAlter = "ALTER TABLE vtiger_$fieldName DROP COLUMN agentmanager_id ";
    $alter = $db->pquery($sqlAlter);
}

echo 'End: Migrate Picklist Values <br />';