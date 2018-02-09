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

$moduleInstance = Vtiger_Module::getInstance('Estimates');
$blockInstance = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $moduleInstance);

$fields = [
    "express_pickup_type" => [
        "label" => "LBL_EXPRESS_PICKUP_TYPE",
        "table" => "vtiger_quotes",
        "columntype" => "VARCHAR(255)",
        "uitype" => "16",
        "typeofdata" => "V~O",
        "picklist" => [
            "Rate/CWT",
            "Rate/CuFt",
            "Flat Charge"
        ]
    ],
    "express_pickup_rate" => [
        "label" => "LBL_EXPRESS_PICKUP_RATE",
        "table" => "vtiger_quotes",
        "columntype" => "DECIMAL(10,2)",
        "uitype" => "71",
        "typeofdata" => "V~O",
    ]
];

echo "Adding new fields to Estimates...";
foreach($fields as $name => $info) {
    $fieldInstance = Vtiger_Field::getInstance($name, $moduleInstance);
    if(!$fieldInstance) {
        $fieldInstance = new Vtiger_Field();

        // Set field info.
        $fieldInstance->name = $name;
        $fieldInstance->column = $name;
        $fieldInstance->label = $info['label'];
        $fieldInstance->columntype = $info['columntype'];
        $fieldInstance->uitype = $info['uitype'];
        $fieldInstance->typeofdata = $info['typeofdata'];

        // Only set picklist values if you need to.
        if(is_array($info['picklist'])) {
            echo "Adding picklist values for field table...";
            $fieldInstance->setPicklistValues($info['picklist']);
        }

        echo "Saving new field $name to Estimates...";
        $blockInstance->addField($fieldInstance);
    }else {
        echo "Field $name already exists in the Estimates module, skipping...";
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
