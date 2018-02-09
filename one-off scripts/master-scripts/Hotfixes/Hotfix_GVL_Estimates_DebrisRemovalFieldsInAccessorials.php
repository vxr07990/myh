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


//16261 Missing tariff items in the UI for 1950-B. Adding fields for debris removal.

echo "<p>Starting adding debris removal/minimum unpacking fields to Accessorials in Estimates/Actuals<br>\n";


$validPicks = [0,1,2,3,4];

$modules = ['Actuals', 'Estimates'];
$blockName = 'LBL_QUOTES_ACCESSORIALDETAILS';
$fields = ['acc_debris_reg', 'acc_debris_ot', 'acc_debris_dod'];
$failed = false;

foreach ($modules as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        echo 'Module ' . $moduleName . ' does not exist'.PHP_EOL;
        $failed = true;
        break;
    }
    $block = Vtiger_Block::getInstance($blockName, $module);
    if (!$block) {
        echo 'Block ' . $blockName . ' does not exist'.PHP_EOL;
        $failed = true;
        break;
    }
}

if (!$failed) {
    foreach ($modules as $moduleName) {
        $module = Vtiger_Module::getInstance($moduleName);
        $block = Vtiger_Block::getInstance($blockName, $module);
        foreach ($fields as $fieldName) {
            $field = Vtiger_Field::getInstance($fieldName, $module);
            if ($field) {
                echo "The $fieldName field already exists<br>\n";
            } else {
                $field             = new Vtiger_Field();
                $field->label      = 'LBL_'.strtoupper($fieldName);
                $field->name       =  $fieldName;
                $field->table      = 'vtiger_quotes';
                $field->column     =  $fieldName;
                $field->columntype = 'INT(1)';
                $field->uitype     = 16;
                $field->typeofdata = 'I~O';
                $block->addField($field);
                $field->setPicklistValues($validPicks);
                echo "The $fieldName field was added to $moduleName.<br>\n";
            }
        }
    }
} else {
    echo "Update failed due to missing block or module.<br>\n";
}

echo "<p>Finished adding debris removal/minimum unpacking fields to Accessorials in Estimates/Actuals";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";