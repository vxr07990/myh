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

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

//Creating new field
$create = ['WFArticles' =>
            ['LBL_WFARTICLES_ARTICLE_INFORMATION' =>
              ['LBL_WFARTICLES_WFACCOUNT' => [
                'name'              => 'wfaccount',
                'table'             => 'vtiger_wfarticles',
                'column'            => 'wfaccount',
                'columntype'        => 'varchar(255)',
                'uitype'            => 10,
                'typeofdata'        => 'V~M',
                'sequence'          => 1,
                'setRelatedModules' => ["WFAccounts"],
                ],
              ],
            'LBL_WFARTICLES_ARTICLE_DETAILS' =>
              ['LBL_WFARTICLES_MANUFACTURER_PART_NUMBER' => [
                'name'              => 'manufacturer_part_num',
                'table'             => 'vtiger_wfarticles',
                'column'            => 'manufacturer_part_num',
                'columntype'        => 'VARCHAR(100)',
                'uitype'            => 1,
                'typeofdata'        => 'V~O',
                'sequence'          => 2,
                ],
              ],
            ],
          ];

multicreate($create);

$db = PearDatabase::getInstance();

//Reordering fields to take the new field into account
$reorder = [
  'manufacturer' => 1,
  'manufacturer_part_num' => 2,
  'vendor' => 3,
  'vendor_num' => 4,
  'part_num' => 5,
  'width' => 6,
  'depth' => 7,
  'height' => 8,
  'sq_ft' => 9,
  'cu_ft' => 10,
  'weight' => 11,
];

$module = Vtiger_Module::getInstance('WFArticles');

foreach($reorder as $field=>$seq) {
  $fieldInstance = Vtiger_Field::getInstance($field, $module);
  $db->pquery("UPDATE vtiger_field SET sequence = ? WHERE fieldid = ?;",[$seq,$fieldInstance->id]);
}
