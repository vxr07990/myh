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

$module = Vtiger_Module_Model::getInstance('WFInventory');

foreach(['status','condition','comment'] as $fieldname) {
    $field = Vtiger_Field::getInstance($fieldname, $module);
    if($field) {
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence = 1 WHERE `fieldid` = $field->id");
    }
}

$create = [
    'WFInventory' => [
        'LBL_WFINVENTORY_USER_DEFINED' => [
            'LBL_WFINVENTORY_UDF_1' => [
                'name' => 'udf_1',
                'table' => 'vtiger_wfinventory',
                'column' => 'udf_1',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'sequence' => 1,
            ],
            'LBL_WFINVENTORY_UDF_2' => [
                'name' => 'udf_2',
                'table' => 'vtiger_wfinventory',
                'column' => 'udf_2',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'sequence' => 2,
            ],
            'LBL_WFINVENTORY_UDF_3' => [
                'name' => 'udf_3',
                'table' => 'vtiger_wfinventory',
                'column' => 'udf_3',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'sequence' => 3,
            ],
            'LBL_WFINVENTORY_UDF_4' => [
                'name' => 'udf_4',
                'table' => 'vtiger_wfinventory',
                'column' => 'udf_4',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'sequence' => 4,
            ],
            'LBL_WFINVENTORY_UDF_5' => [
                'name' => 'udf_5',
                'table' => 'vtiger_wfinventory',
                'column' => 'udf_5',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'sequence' => 5,
            ],
            'LBL_WFINVENTORY_UDF_6' => [
                'name' => 'udf_6',
                'table' => 'vtiger_wfinventory',
                'column' => 'udf_6',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'sequence' => 6,
            ],
            'LBL_WFINVENTORY_UDF_7' => [
                'name' => 'udf_7',
                'table' => 'vtiger_wfinventory',
                'column' => 'udf_7',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'sequence' => 7,
            ],
            'LBL_WFINVENTORY_UDF_8' => [
                'name' => 'udf_8',
                'table' => 'vtiger_wfinventory',
                'column' => 'udf_8',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'sequence' => 8,
            ],
            'LBL_WFINVENTORY_UDF_9' => [
                'name' => 'udf_9',
                'table' => 'vtiger_wfinventory',
                'column' => 'udf_9',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'sequence' => 9,
            ],
            'LBL_WFINVENTORY_UDF_10' => [
                'name' => 'udf_10',
                'table' => 'vtiger_wfinventory',
                'column' => 'udf_10',
                'columntype' => 'VARCHAR(100)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'sequence' => 10,
            ],
            'LBL_WFINVENTORY_UDF_11' => [
                'name' => 'udf_11',
                'table' => 'vtiger_wfinventory',
                'column' => 'udf_11',
                'columntype' => 'DEC(50)',
                'uitype' => 7,
                'typeofdata' => 'N~O',
                'sequence' => 11,
            ],
            'LBL_WFINVENTORY_UDF_12' => [
                'name' => 'udf_12',
                'table' => 'vtiger_wfinventory',
                'column' => 'udf_12',
                'columntype' => 'DEC(50)',
                'uitype' => 7,
                'typeofdata' => 'N~O',
                'sequence' => 12,
            ],
            'LBL_WFINVENTORY_UDF_13' => [
                'name' => 'udf_13',
                'table' => 'vtiger_wfinventory',
                'column' => 'udf_13',
                'columntype' => 'DEC(50)',
                'uitype' => 7,
                'typeofdata' => 'N~O',
                'sequence' => 13,
            ],
            'LBL_WFINVENTORY_UDF_14' => [
                'name' => 'udf_14',
                'table' => 'vtiger_wfinventory',
                'column' => 'udf_14',
                'columntype' => 'date',
                'uitype' => 5,
                'typeofdata' => 'D~O',
                'sequence' => 14,
            ],
            'LBL_WFINVENTORY_UDF_15' => [
                'name' => 'udf_15',
                'table' => 'vtiger_wfinventory',
                'column' => 'udf_15',
                'columntype' => 'date',
                'uitype' => 5,
                'typeofdata' => 'D~O',
                'sequence' => 15,
            ],
            'LBL_WFINVENTORY_UDF_16' => [
                'name' => 'udf_16',
                'table' => 'vtiger_wfinventory',
                'column' => 'udf_16',
                'columntype' => 'VARCHAR(3)',
                'uitype' => 56,
                'typeofdata' => 'C~O',
                'sequence' => 16,
            ],
            'LBL_WFINVENTORY_UDF_17' => [
                'name' => 'udf_17',
                'table' => 'vtiger_wfinventory',
                'column' => 'udf_17',
                'columntype' => 'VARCHAR(3)',
                'uitype' => 56,
                'typeofdata' => 'C~O',
                'sequence' => 17,
            ],
            'LBL_WFINVENTORY_UDF_18' => [
                'name' => 'udf_18',
                'table' => 'vtiger_wfinventory',
                'column' => 'udf_18',
                'columntype' => 'VARCHAR(3)',
                'uitype' => 56,
                'typeofdata' => 'C~O',
                'sequence' => 18,
            ],
            'LBL_WFINVENTORY_UDF_19' => [
                'name' => 'udf_19',
                'table' => 'vtiger_wfinventory',
                'column' => 'udf_19',
                'columntype' => 'VARCHAR(3)',
                'uitype' => 56,
                'typeofdata' => 'C~O',
                'sequence' => 19,
            ],
            'LBL_WFINVENTORY_UDF_20' => [
                'name' => 'udf_20',
                'table' => 'vtiger_wfinventory',
                'column' => 'udf_20',
                'columntype' => 'VARCHAR(3)',
                'uitype' => 56,
                'typeofdata' => 'C~O',
                'sequence' => 20,
            ],

        ],
    ],
];

multicreate($create);
