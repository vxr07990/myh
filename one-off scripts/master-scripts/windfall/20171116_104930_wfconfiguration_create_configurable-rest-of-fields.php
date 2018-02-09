<?php

if (function_exists("call_ms_function_ver")) {
    $version = 2;
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

$moduleName = 'WFConfiguration';
$module = Vtiger_Module::getInstance($moduleName);

$create = [
  $moduleName => [
    'LBL_WFCONFIGURATION_INVENTORY_INFORMATION' => [],
    'LBL_WFCONFIGURATION_INVENTORY_DETAILS' => [],
  ],
];
$fieldList = [
    'LBL_WFCONFIGURATION_INVENTORY_INFORMATION' => [
        'ArticleNumber',
        'Description',
    ],
    'LBL_WFCONFIGURATION_INVENTORY_DETAILS' => [
        'Manufacturer',
        'Manufacturer Part Number',
        'Vendor',
        'Vendor Part Number',
        'Part Number',
        'Serial Number',
        'System Number',
        'Secondary Number',
        'Model Number',
        'Bill Of Lading',
        'Receving Report Number',
        'Cost Center Code',
        'Inventory Status',
        'Item Condistion',
    ],
    'LBL_WFCONFIGURATION_UNIT_OF_MEASURES' => [
        'Width',
        'Depth',
        'Height',
        'Square Footage',
        'Cube Footage',
        'Weight',
    ],
    'LBL_WFCONFIGURATION_PHYSICAL LOCATION' => [
        'Building',
        'Department',
        'Floor',
        'Office',
        'Room',
        'Site',
        'Store'
    ],
    'LBL_WFCONFIGURATION_Finish Details' => [
        'Color',
        'Color Code',
        'Fabric',
        'Fabric Color',
        'Finish',
        'Finish Color',
        'Material',
        'Material Color',
        'Locked'
    ],
    'LBL_WFCONFIGURATION_Purchase Details' => [
        'Designer',
        'End User',
        'Price',
        'Year',
        'Destination'
    ],
    'LBL_WFCONFIGURATION_Warehouse Details' => [
        'Installed By',
        'Open and Inspected By',
        'Assembled By',
        'Vaulted By',
        'Warehouse Conditions',
        'Images'
    ],
];

$fieldTypes = [
    'label',
    'repeat',
    'mobile',
    'portal',
    'group'
];
foreach ($fieldList as $blockLabel => $fieldsArray) {
    $blockLabel = strtoupper(str_replace(' ', '_', $blockLabel));
    foreach ($fieldsArray as $fieldName) {
        foreach ($fieldTypes as $type) {
            $fieldNameByType = $fieldName.'_'.$type;
            $fieldNameByType = strtolower(str_replace(' ', '_', $fieldNameByType));
            $fieldLabel = 'LBL_'.strtoupper($moduleName).'_'.strtoupper($fieldNameByType);
            $create[$moduleName][$blockLabel][$fieldLabel] = [
                'name' => $fieldNameByType,
                'table' => 'vtiger_wfconfiguration',
                'column' => $fieldNameByType,
                'columntype' => 'VARCHAR(3)',
                'uitype' => 56,
                'typeofdata' => 'V~O',
            ];
            if ($type == 'label') {
                $create[$moduleName][$blockLabel][$fieldLabel]['readonly'] = 0;
                $create[$moduleName][$blockLabel][$fieldLabel]['columntype'] = 'varchar(100)';
                $create[$moduleName][$blockLabel][$fieldLabel]['uitype'] = 1;
            }
            if (
                $type == 'group' &&
                (
                    $fieldName == 'ArticleNumber' ||
                    $fieldName == 'Description'
                )
             ) {
                $create[$moduleName][$blockLabel][$fieldLabel]['defaultvalue'] = 1;
            }
        }
    }
}

//@NOTE: UDF fields are created in the prior hotfix.
multicreate($create);

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
