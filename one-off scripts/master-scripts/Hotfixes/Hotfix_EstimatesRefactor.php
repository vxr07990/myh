<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 12/13/2016
 * Time: 3:13 PM
 */
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = &PearDatabase::getInstance();
$moduleNames = ['Estimates', 'Actuals'];

foreach($moduleNames as $moduleName)
{
    $module = Vtiger_Module::getInstance($moduleName);
    if(!$module)
    {
        continue;
    }

    $block = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $module);
    if(!$block)
    {
        echo 'Failed to find Block to create effective_tariff field!<br>'.PHP_EOL;
        continue;
    }
    $field = Vtiger_Field::getInstance('effective_tariff', $module);
    if ($field) {
        echo "The effective_tariff field already exists<br>\n";
        $db->pquery('TRUNCATE TABLE vtiger_effective_tariff');
        $field->setPicklistValues([]);
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_QUOTES_EFFECTIVE_TARIFF';
        $field->name       = 'effective_tariff';
        $field->table      = 'vtiger_quotes';
        $field->column     = 'effective_tariff';
        $field->columntype = 'INT(11)';
        $field->uitype     = 16;
        $field->typeofdata = 'I~O';
        $block->addField($field);
        $field->setPicklistValues([]);
    }
}










print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";