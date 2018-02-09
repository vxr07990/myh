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


//@NOTE: In this case this field was entirely wrong and it is used nowhere.
// so: we are deleting this field

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$moduleName = 'Leads';
$module = Vtiger_Module::getInstance($moduleName);
if (!$module) {
    print "failed to open module ($moduleName).\n";
}

$db  = PearDatabase::getInstance();

foreach (['related_account'] as $fieldName) {
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        $field->delete();
        print "Deleting $fieldName in $moduleName\n";
    }
}
echo "<h3>Ending ". __FILE__ . "</h3>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";