<?php

if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "RUNNING: " . __FILE__ . "<br />\n";

$moduleName = 'Estimates';
$fieldName = 'local_bl_discount';
$newTypeOfData = 'N~O~MIN=0~STEP=.01';

$module = Vtiger_Module::getInstance($moduleName);
if(!$module){
    echo "Module $moduleName not found.";
    return;
}

//updating uitype
$field = Vtiger_Field::getInstance($fieldName, $module);
if(!$field){
    echo "Field $fieldName don't exist.<br>";
    return;
}

if ($field->typeofdata != $newTypeOfData) {
    $sql    = "UPDATE vtiger_field SET typeofdata = ? WHERE fieldid = ? LIMIT 1";
    $result = $db->pquery($sql, [$newTypeOfData,$field->id]);
    echo "<li>$fieldName field typeofdata updated to: $newTypeOfData<br>".PHP_EOL;
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
