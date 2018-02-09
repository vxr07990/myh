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



$correctOrder = ['orders_ldate', 'orders_ltdate', 'orders_pldate', 'orders_ddate', 'orders_dtdate', 'orders_pddate'];

$seq = 1;

$ordModule = Vtiger_Module::getInstance('Orders');

foreach ($correctOrder as $fieldname) {
    $field = Vtiger_Field::getInstance($fieldname, $ordModule);
    if ($field) {
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence=".$seq." WHERE fieldid=".$field->id);
        $seq++;
    }
}

$correctOrder = ['load_date', 'load_to_date', 'preferred_pldate', 'deliver_date', 'deliver_to_date', 'preferred_pddate', 'decision_date', 'followup_date'];

$seq = 1;

$oppModule = Vtiger_Module::getInstance('Opportunities');

foreach ($correctOrder as $fieldname) {
    $field = Vtiger_Field::getInstance($fieldname, $oppModule);
    if ($field) {
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence=".$seq." WHERE fieldid=".$field->id);
        $seq++;
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";