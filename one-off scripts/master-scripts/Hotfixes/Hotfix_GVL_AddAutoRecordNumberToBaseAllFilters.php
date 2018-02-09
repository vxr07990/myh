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

/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 10/18/2016
 * Time: 5:21 PM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = &PearDatabase::getInstance();

$res = $db->pquery('SELECT * FROM vtiger_tab INNER JOIN vtiger_field ON (vtiger_tab.tabid=vtiger_field.tabid) WHERE uitype=4 AND isentitytype=1 AND vtiger_field.presence<>1');

while ($row = $res->fetchRow()) {
    $moduleInstance = Vtiger_Module::getInstance($row['name']);
    if (!$moduleInstance) {
        continue;
    }
    $viewRes = $db->pquery('SELECT * FROM vtiger_customview WHERE entitytype=? AND userid=1',
                           [$row['name']]);
    while ($row2 = $viewRes->fetchRow()) {
        $columnRes = $db->pquery('SELECT * FROM vtiger_cvcolumnlist WHERE cvid=? AND columnname LIKE ?',
                                 [$row2['cvid'], '%:'.$row['fieldname'].':%' ]);
        if (!$columnRes->fetchRow()) {
            // add uitype 4 column to view
            $filterInstance = Vtiger_Filter::getInstance($row2['cvid']);
            $fieldInstance = Vtiger_Field::getInstance($row['fieldname'], $moduleInstance);
            if ($filterInstance && $fieldInstance) {
                echo 'Adding auto number field to default filter view for ' . $moduleInstance->name . PHP_EOL;
                $filterInstance->addField($fieldInstance, 0);
            }
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";