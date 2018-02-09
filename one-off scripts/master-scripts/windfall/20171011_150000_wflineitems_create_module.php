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

$create = [
  'WFLineItems' => [
    'LBL_WFLINEITEMS_BLOCK' => [
      'LBL_WFLINEITEMS_WFINVENTORY' => [
        'name' => 'wfinventory',
        'table' => 'vtiger_wflineitems',
        'column' => 'wfinventory',
        'columntype' => 'INT(50)',
        'uitype' => 10,
        'typeofdata' => 'V~O',
        'sequence' => 1,
        'setRelatedModules' => ['WFInventory'],
      ],
      'LBL_WFLINEITEMS_WFARTICLE' => [
        'name' => 'wfarticle',
        'table' => 'vtiger_wflineitems',
        'column' => 'wfarticle',
        'columntype' => 'INT(50)',
        'uitype' => 10,
        'typeofdata' => 'V~O',
        'sequence' => 2,
        'setRelatedModules' => ['WFArticles'],
      ],
      'LBL_WFLINEITEMS_DESCRIPTION' => [
        'name' => 'description',
        'table' => 'vtiger_wflineitems',
        'column' => 'description',
        'columntype' => 'VARCHAR(100)',
        'uitype' => 1,
        'typeofdata' => 'V~O',
        'sequence' => 3,
        'entityIdentifier' => 1,
      ],
      'LBL_WFLINEITEMS_LOCATION' => [
        'name' => 'location',
        'table' => 'vtiger_wflineitems',
        'column' => 'location',
        'columntype' => 'INT(50)',
        'uitype' => 16,
        'typeofdata' => 'V~O',
        'sequence' => 4,
      ],
      'LBL_WFLINEITEMS_ONHAND' => [
        'name' => 'onhand',
        'table' => 'vtiger_wflineitems',
        'column' => 'onhand',
        'columntype' => 'INT(50)',
        'uitype' => 7,
        'typeofdata' => 'I~O~MIN=0',
        'sequence' => 4,
      ],
      'LBL_WFLINEITEMS_REQUESTED' => [
        'name' => 'requested',
        'table' => 'vtiger_wflineitems',
        'column' => 'requested',
        'columntype' => 'INT(50)',
        'uitype' => 7,
        'typeofdata' => 'I~O~MIN=0',
        'sequence' => 4,
      ],
      'LBL_WFLINEITEMS_PROCESSED' => [
        'name' => 'processed',
        'table' => 'vtiger_wflineitems',
        'column' => 'processed',
        'columntype' => 'INT(50)',
        'uitype' => 7,
        'typeofdata' => 'I~O~MIN=0',
        'sequence' => 4,
      ],
    ],
  ],
];

multicreate($create);

$module = Vtiger_Module::getInstance('WFLineItems');

foreach(['workorder','comments'] as $fieldName) {
  $field = Vtiger_Field::getInstance($fieldName,$module);
  if($field) {
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `presence` = 1 WHERE `fieldid` = $field->id");
  }
}
