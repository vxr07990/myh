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

$moduleInstance = Vtiger_Module_Model::getInstance('WFLocationHistory');
foreach(['date','time','warehouse','locationtag','slot'] as $fieldName) {
  $field = Vtiger_Field::getInstance($fieldName,$moduleInstance);
  if($field) {
    $field->delete();
  }
}
Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_wflocationhistory` DROP COLUMN `date`, DROP COLUMN `time`, DROP COLUMN `warehouse`, DROP COLUMN `locationtag`, DROP COLUMN `slot`;");

$create = ['WFLocationHistory' => [
            'LBL_WFLOCATIONHISTORY_DETAILS' => [
              'LBL_WFLOCATIONHISTORY_DATETIME' => [
                'name' => 'datetime',
                'table' => 'vtiger_wflocationhistory',
                'column' => 'datetime',
                'columntype' => 'DATETIME',
                'uitype' => 6,
                'typeofdata' => 'DT~O',
              ],
              'LBL_WFLOCATIONHISTORY_LOCATION' => [
                'name' => 'location',
                'table' => 'vtiger_wflocationhistory',
                'column' => 'location',
                'columntype' => 'varchar(125)',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'quickcreate' => 0,
                'setRelatedModules' => ['WFLocations'],
                'entityIdentifier' => true,
              ],
              'LBL_WFLOCATIONHISTORY_FROM_LOCATION' => [
                'name' => 'from_location',
                'table' => 'vtiger_wflocationhistory',
                'column' => 'from_location',
                'columntype' => 'varchar(125)',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'quickcreate' => 0,
                'setRelatedModules' => ['WFLocations'],
              ],
              'LBL_WFLOCATIONHISTORY_TO_LOCATION' => [
                'name' => 'to_location',
                'table' => 'vtiger_wflocationhistory',
                'column' => 'to_location',
                'columntype' => 'varchar(125)',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'quickcreate' => 0,
                'setRelatedModules' => ['WFLocations'],
              ],
              'LBL_WFLOCATIONHISTORY_FROM_SLOT' => [
                'name' => 'from_slot',
                'table' => 'vtiger_wflocationhistory',
                'column' => 'from_slot',
                'columntype' => 'varchar(125)',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'quickcreate' => 0,
                'setRelatedModules' => ['WFSlotConfiguration'],
              ],
              'LBL_WFLOCATIONHISTORY_TO_SLOT' => [
                'name' => 'to_slot',
                'table' => 'vtiger_wflocationhistory',
                'column' => 'to_slot',
                'columntype' => 'varchar(125)',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'quickcreate' => 0,
                'setRelatedModules' => ['WFSlotConfiguration'],
              ],
              'LBL_WFLOCATIONHISTORY_FROM_WAREHOUSE' => [
                'name' => 'from_warehouse',
                'table' => 'vtiger_wflocationhistory',
                'column' => 'from_warehouse',
                'columntype' => 'varchar(125)',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'quickcreate' => 0,
                'setRelatedModules' => ['WFWarehouses'],
              ],
              'LBL_WFLOCATIONHISTORY_TO_WAREHOUSE' => [
                'name' => 'to_warehouse',
                'table' => 'vtiger_wflocationhistory',
                'column' => 'to_warehouse',
                'columntype' => 'varchar(125)',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'quickcreate' => 0,
                'setRelatedModules' => ['WFWarehouses'],
              ],
              'LBL_WFLOCATIONHISTORY_FROM_STATUS' => [
                'name' => 'from_status',
                'table' => 'vtiger_wflocationhistory',
                'column' => 'from_status',
                'columntype' => 'varchar(125)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'quickcreate' => 0,
              ],
              'LBL_WFLOCATIONHISTORY_TO_STATUS' => [
                'name' => 'to_status',
                'table' => 'vtiger_wflocationhistory',
                'column' => 'to_status',
                'columntype' => 'varchar(125)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'quickcreate' => 0,
              ],
              'LBL_WFLOCATIONHISTORY_USER' => [
                'name' => 'user',
                'table' => 'vtiger_wflocationhistory',
                'column' => 'user',
                'columntype' => 'varchar(125)',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'setRelatedModules' => ['Users'],
              ],
              'LBL_WFLOCATIONHISTORY_ASSIGNEDTO' => [
                'name' => 'assigned_user_id',
                'table' => 'vtiger_crmentity',
                'column' => 'smownerid',
                'uitype' => 53,
                'typeofdata' => 'V~O',
              ],
            ],
          ],
        ];

multicreate($create);
