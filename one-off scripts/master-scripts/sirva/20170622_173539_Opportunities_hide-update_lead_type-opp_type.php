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

//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('include/database/PearDatabase.php');

if(!$db) {
    $db = PearDatabase::getInstance();
}

$fields = [
    'opp_type' => [
        'table' => 'vtiger_potential',
        'callback' => function(&$fields) {
            $fields['presence'] = 1;
            // Change sequence to avoid collision
            $fields['sequence'] = -1;
        }
    ],
    'lead_type' => [
        'table' => 'vtiger_potential',
        'callback' => function(&$fields) {
            $fields['sequence'] = 34;
        }
    ]
];

foreach($fields as $field => $info) {
    if(!$info['callback']) {
        continue;
    }

    $sql = "SELECT fieldid, typeofdata, presence, displaytype, sequence FROM vtiger_field WHERE fieldname = ?";
    $params = [$field];

    if($info['table']) {
        $sql .= " AND tablename = ?";
        $params[] = $info['table'];
    }

    $res = $db->pquery($sql, $params);
    if($res && $db->num_rows($res) > 0) {
        $info['callback']($res->fields);

        $sql = "UPDATE vtiger_field SET typeofdata = ?, presence = ?, displaytype = ?, sequence = ? WHERE fieldid = ?";
        $db->pquery($sql, [$res->fields['typeofdata'], $res->fields['presence'], $res->fields['displaytype'], $res->fields['sequence'], $res->fields['fieldid']]);
    }else {
        // Doesn't really matter.
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
