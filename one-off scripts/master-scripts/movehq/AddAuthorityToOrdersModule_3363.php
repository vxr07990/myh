<?php
if (function_exists("call_ms_function_ver")) {
    $version = 4;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$moduleOrders = Vtiger_Module::getInstance('Orders');
if (!$moduleOrders) {
    return;
}

$blockInstance = Vtiger_Block::getInstance('LBL_ORDERS_INFORMATION', $moduleOrders);

if(!$blockInstance)
{
    return;
}

$field = Vtiger_Field::getInstance('authority', $moduleOrders);
if(!$field){
    $field = new Vtiger_Field();
    $field->label = 'LBL_ORDERS_AUTHORITY';
    $field->name = 'authority';
    $field->table = 'vtiger_orders';
    $field->column ='authority';
    $field->columntype = 'VARCHAR(50)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->quickcreate = 0;
    $field->summaryfield = 1;

    $blockInstance->addField($field);
}

$db = &PearDatabase::getInstance();
$db->pquery('UPDATE vtiger_orders SET authority=COALESCE(authority,orderspriority)');

$field1480 = Vtiger_Field::getInstance('orders_discount', $moduleOrders); // 19
$field1454 = Vtiger_Field::getInstance('actualenddate', $moduleOrders); // 20
$field1452 = Vtiger_Field::getInstance('startdate', $moduleOrders); // 22
$field1538 = Vtiger_Field::getInstance('business_line', $moduleOrders); // 23
$field1453 = Vtiger_Field::getInstance('targetenddate', $moduleOrders); // 24
$field1463 = Vtiger_Field::getInstance('progress', $moduleOrders); // 18
$field1483 = Vtiger_Field::getInstance('orders_relatedorders', $moduleOrders); // 25

if ($field1480) {
    echo "<h4>Update requence of fields on Order Details block</h4><br>";
    if ($field1463) {
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence='".($field1480->sequence - 1)."' WHERE fieldid='".$field1463->id."';");
    }
    if ($field1454) {
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence='".($field1480->sequence + 1)."' WHERE fieldid='".$field1454->id."';");
    }
    if ($field1462) {
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence='".($field1480->sequence + 2)."' WHERE fieldid='".$field1462->id."';");
    }
    if ($field1452) {
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence='".($field1480->sequence + 3)."' WHERE fieldid='".$field1452->id."';");
    }
    if ($field1538) {
        if ($blockInstance) {
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET block='".$blockInstance->id."', sequence='".($field1480->sequence + 4)."' WHERE fieldid='".$field1538->id."';");
        }
    }
    if ($field1453) {
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence='".($field1480->sequence + 5)."' WHERE fieldid='".$field1453->id."';");
    }
    if ($field1483) {
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence='".($field1480->sequence + 6)."' WHERE fieldid='".$field1483->id."';");
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";