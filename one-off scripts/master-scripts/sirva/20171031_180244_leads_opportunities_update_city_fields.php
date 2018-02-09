<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('include/database/PearDatabase.php');

if(!$db) {
    $db = PearDatabase::getInstance();
}

$map = [
    'Leads' => [
        'origin_city',
        'destination_city'
    ],
    'Opportunities' => [
        'origin_city',
        'destination_city'
    ]
];

foreach($map as $module => $fields) {
    $sql = "SELECT tabid FROM vtiger_tab WHERE name = '$module'";
    $tabid = $db->getOne($sql);
    echo "Updating $module...<br/>\n";
    foreach($fields as $field) {
        echo "Updating field {$field} on {$tabid}...<br/>\n";
        $sql = "SELECT fieldid, typeofdata FROM vtiger_field WHERE fieldname = '$field' AND tabid = '$tabid'";
        if($res = $db->query($sql)) {
            while($row = $res->fetchRow()) {
                $id = $row['fieldid'];
                $tod = $row['typeofdata'];

                if(updateTypeOfData($tod, "M", "O")) {
                    $sql = "UPDATE vtiger_field SET typeofdata = ? WHERE fieldid = ?";
                    if(!$db->pquery($sql, [$tod, $id])) {
                        echo "Failed attempting to update typeofdata for field {$id}.<br/>\n";
                    }
                }else {
                    echo "Field {$id} is already optional.<br/>\n";
                }
            }
        }else {
            echo "Could not get fields.<br/>\n";
        }
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";

