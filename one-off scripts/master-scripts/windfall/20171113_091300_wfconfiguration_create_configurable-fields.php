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

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;

global $adb;

$module = Vtiger_Module::getInstance('WFConfiguration');

$create = [
  'WFConfiguration' => [
    'LBL_WFCONFIGURATION_SETUP' => [],
  ],
];

foreach(range(1,20) as $udf) {

  $create['WFConfiguration']['LBL_WFCONFIGURATION_SETUP']['LBL_WFCONFIGURATION_UDF' . $udf . '_MOBILE'] = [
        'name' => 'udf' . $udf . '_mobile',
        'table' => 'vtiger_wfconfiguration',
        'column' => 'udf' . $udf . '_mobile',
        'columntype' => 'VARCHAR(3)',
        'uitype' => 56,
        'typeofdata' => 'V~O',
    ];
    $create['WFConfiguration']['LBL_WFCONFIGURATION_SETUP']['LBL_WFCONFIGURATION_UDF' . $udf . '_REPEAT'] = [
        'name' => 'udf' . $udf . '_repeat',
        'table' => 'vtiger_wfconfiguration',
        'column' => 'udf' . $udf . '_repeat',
        'columntype' => 'VARCHAR(3)',
        'uitype' => 56,
        'typeofdata' => 'V~O',
    ];
    $create['WFConfiguration']['LBL_WFCONFIGURATION_SETUP']['LBL_WFCONFIGURATION_UDF' . $udf . '_PORTAL'] = [
        'name' => 'udf' . $udf . '_portal',
        'table' => 'vtiger_wfconfiguration',
        'column' => 'udf' . $udf . '_portal',
        'columntype' => 'VARCHAR(3)',
        'uitype' => 56,
        'typeofdata' => 'V~O',
    ];
    $create['WFConfiguration']['LBL_WFCONFIGURATION_SETUP']['LBL_WFCONFIGURATION_UDF' . $udf . '_GROUP'] = [
        'name' => 'udf' . $udf . '_group',
        'table' => 'vtiger_wfconfiguration',
        'column' => 'udf' . $udf . '_group',
        'columntype' => 'VARCHAR(3)',
        'uitype' => 56,
        'typeofdata' => 'V~O',
    ];

}

multicreate($create);

$i = 1;
foreach(range(1,20) as $udf) {
  Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `sequence` = $i WHERE `columnname` = 'udf" . $udf . "_label'");
  $i++;
  Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `sequence` = $i WHERE `columnname` = 'udf" . $udf . "_mobile'");
  $i++;
  Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `sequence` = $i WHERE `columnname` = 'udf" . $udf . "_repeat'");
  $i++;
  Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `sequence` = $i WHERE `columnname` = 'udf" . $udf . "_portal'");
  $i++;
  Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `sequence` = $i WHERE `columnname` = 'udf" . $udf . "_group'");
  $i++;
}

$tab = getTabId('WFAccounts');
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_relatedlists` SET `name` = 'get_dependents_list' WHERE `label` = 'Configuration' AND `tabid` = $tab");
