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


// we don't actually want these fields hidden

return;


$module = Vtiger_Module::getInstance('Orders');
$fieldsToHide = ['orders_vanlineregnum', 'targetenddate'];

foreach ($fieldsToHide as $field) {
    $field = Vtiger_Field::getInstance($field, $module);
    if ($field) {
        $sql   = "UPDATE `vtiger_field`
        SET   `presence` = '1'
        WHERE  `fieldid` = ?";
        $db    = PearDatabase::getInstance();
        $query = $db->pquery($sql, [$field->id]);
        echo $db->getAffectedRowCount($query)." Rows were updated in vtiger_field where label = $field->label <br/>\n";
    }
}
echo "<br>Finished RUNNING: " . __FILE__ . " <br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";