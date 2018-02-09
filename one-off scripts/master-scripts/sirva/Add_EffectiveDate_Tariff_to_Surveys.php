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

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('vtlib/Vtiger/Block.php');

// Survey Module
$module = Vtiger_Module::getInstance('Cubesheets');
// Survey block
$block = Vtiger_Block::getInstance('LBL_CUBESHEETS_INFORMATION', $module);
if(!$block) {
    echo "Surveys base block does not exist, cannot add field. Frankly, I don't know how this happened.<br/>\n";
    return;
}

$fields = [
    'effective_date' => [
        'label' => 'LBL_SURVEYS_EFFECTIVEDATE',
        'uitype' => 5,
        'datatype' => 'D~M',
        'table' => 'vtiger_cubesheets',
        'columntype' => 'VARCHAR(200)'
    ],
    'effective_tariff' => [
        'label' => 'LBL_SURVEYS_EFFECTIVETARIFF',
        'uitype' => 16,
        'datatype' => 'I~M',
        'table' => 'vtiger_cubesheets',
        'columntype' => 'VARCHAR(200)'
    ]
];

foreach($fields as $field => $info) {
    $fieldInstance = VTiger_Field::getInstance($field, $module);
    if(!$fieldInstance) {
        echo "Adding ".$info['label']." field...<br/>\n";
        $fieldInstance = new VTiger_Field();
        $fieldInstance->label = $info['label'];
        $fieldInstance->name = $field;
        $fieldInstance->table = $info['table'];
        $fieldInstance->column = $field;
        $fieldInstance->columntype = $info['columntype'];
        $fieldInstance->uitype = $info['uitype'];
        $fieldInstance->typeofdata = $info['datatype'];
        $block->addField($fieldInstance);
    }else {
        echo "Field ".$info['label']." already exists.<br/>\n";
    }
}
// Update all past Cubesheet records to have an effective date because this will not be editable
Vtiger_Utils::ExecuteQuery("UPDATE vtiger_cubesheets JOIN vtiger_crmentity on vtiger_cubesheets.cubesheetsid = vtiger_crmentity.crmid SET vtiger_cubesheets.effective_date = date(vtiger_crmentity.createdtime)");

echo "Done.<br/>\n";



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";