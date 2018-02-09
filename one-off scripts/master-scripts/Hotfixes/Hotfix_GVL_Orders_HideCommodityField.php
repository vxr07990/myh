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


//OT 16633 On Orders Commodity Field needs to be removed

$module = Vtiger_Module::getInstance('Orders');
$field1 = Vtiger_Field::getInstance('commodity', $module);

if ($field1) {
    $sql    = "UPDATE `vtiger_field`
        SET   `presence` = '1'
        WHERE  `fieldid` = ?";
    $db    = PearDatabase::getInstance();
    $query = $db->pquery($sql, [$field1->id]);
    echo $db->getAffectedRowCount($query).' Rows were updated in vtiger_field where label = '.$field1->label;
}

echo "<br><h1>Finished </h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";