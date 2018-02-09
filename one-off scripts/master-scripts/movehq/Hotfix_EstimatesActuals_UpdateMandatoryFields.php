<?php
if (function_exists("call_ms_function_ver")) {
    $version = 3;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

if(!$db) {
    $db = PearDatabase::getInstance();
}

$fieldMap = [
    'Estimates' => [
        'business_line_est2' => 'M',
        'quotestage' => 'O',
        'load_date' => 'M',
        'billing_type' => 'M',
        'authority' => 'O',
        'effective_tariff' => 'O'
    ],
    'Actuals' => [
        'actuals_stage' => 'O',
        'authority' => 'O'
    ],
    'Orders' => [
        'authority' => 'O'
    ]
];

foreach($fieldMap as $moduleName=>$fields) {
    $module = Vtiger_Module::getInstance($moduleName);
    foreach($fields as $fieldName=>$mandatory) {
        $field = Vtiger_Field::getInstance($fieldName, $module);
        if($field) {
            $typeofdata = explode('~', $field->typeofdata);
            $typeofdata[1] = $mandatory;
            $db->pquery("UPDATE `vtiger_field` SET typeofdata=? WHERE fieldid=?", [implode('~', $typeofdata), $field->id]);
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";