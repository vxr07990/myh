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


$module = Vtiger_Module::getInstance('Users');
$block1 = Vtiger_Block::getInstance('LBL_USER_ADV_OPTIONS', $module);

$field1 = Vtiger_Field::getInstance('sts_user_id_nvl', $module);
if ($field1) {
    echo "<li>The sts_user_id_nvl field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_STSUSERIDNVL';
    $field1->name = 'sts_user_id_nvl';
    $field1->table = 'vtiger_users';
    $field1->column = 'sts_user_id_nvl';
    $field1->columntype = 'VARCHAR(25)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;

    $block1->addField($field1);
}
$field2 = Vtiger_Field::getInstance('sts_agent_id_nvl', $module);
if ($field2) {
    echo "<li>The sts_agent_id_nvl field already exists</li><br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_STSAGENTIDNVL';
    $field2->name = 'sts_agent_id_nvl';
    $field2->table = 'vtiger_users';
    $field2->column = 'sts_agent_id_nvl';
    $field2->columntype = 'VARCHAR(25)';
    $field2->uitype = 1;
    $field2->typeofdata = 'V~O';
    $field2->displaytype = 1;

    $block1->addField($field2);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";