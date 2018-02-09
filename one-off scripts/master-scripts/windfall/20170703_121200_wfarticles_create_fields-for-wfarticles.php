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
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$Vtiger_Utils_Log = true;

$create = ['WFArticles' => [
            'LBL_WFARTICLES_ARTICLE_INFORMATION' =>[
              'LBL_WFARTICLES_ACCOUNT' => [
                'name' => 'account',
                'table' => 'vtiger_wfarticles',
                'column' => 'account',
                'uitype' => 10,
                'typeofdata' => 'V~M',
                'sequence' => 1,
                'columntype' => 'INT(19)',
                'summaryfield' => 1,
                'displaytype' => 1,
                'setRelatedModules' => 'Accounts',
              ],
              'LBL_WFARTICLES_STATUS' => [
                'name' => 'article_status',
                'table' => 'vtiger_wfarticles',
                'column' => 'article_status',
                'uitype' => 16,
                'typeofdata' => 'V~M',
                'sequence' => 2,
                'columntype' => 'VARCHAR(100)',
                'summaryfield' => 1,
                'displaytype' => 1,
                'setPicklistValues' => ['Active','Inactive'],
              ],
              'LBL_WFARTICLES_ARTICLE_NUMBER' => [
                'name' => 'article_num',
                'table' => 'vtiger_wfarticles',
                'column' => 'article_num',
                'uitype' => 2,
                'typeofdata' => 'V~M',
                'sequence' => 3,
                'columntype' => 'VARCHAR(255)',
                'summaryfield' => 1,
                'displaytype' => 1,
                'filterSequence' => 1,
              ],
              'LBL_WFARTICLES_CATEGORY' => [
                'name' => 'category',
                'table' => 'vtiger_wfarticles',
                'column' => 'category',
                'uitype' => 2,
                'typeofdata' => 'V~M',
                'sequence' => 4,
                'columntype' => 'VARCHAR(255)',
                'summaryfield' => 1,
                'displaytype' => 1,
                'filterSequence' => 2,
              ],
              'LBL_WFARTICLES_TYPE' => [
                'name' => 'type',
                'table' => 'vtiger_wfarticles',
                'column' => 'type',
                'uitype' => 2,
                'typeofdata' => 'V~M',
                'sequence' => 5,
                'columntype' => 'VARCHAR(255)',
                'summaryfield' => 1,
                'displaytype' => 1,
                'filterSequence' => 3,
              ],
              'LBL_WFARTICLES_DESCRIPTION' => [
                'name' => 'description',
                'table' => 'vtiger_crmentity',
                'column' => 'description',
                'uitype' => 19,
                'typeofdata' => 'V~M',
                'sequence' => 6,
                'columntype' => 'TEXT',
                'summaryfield' => 1,
                'displaytype' => 1,
                'filterSequence' => 4,
              ],
              'LBL_WFARTICLES_READER_DESCRIPTION' => [
                'name' => 'reader_description',
                'table' => 'vtiger_wfarticles',
                'column' => 'reader_description',
                'uitype' => 19,
                'typeofdata' => 'V~M',
                'sequence' => 7,
                'columntype' => 'TEXT',
                'summaryfield' => 1,
                'displaytype' => 1,
              ],
            ],
            'LBL_WFARTICLES_ARTICLE_DETAILS' => [
              'LBL_WFARTICLES_MANUFACTURER' => [
                'name' => 'manufacturer',
                'table' => 'vtiger_wfarticles',
                'column' => 'manufacturer',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'sequence' => 1,
                'summaryfield' => 1,
                'columntype' => 'VARCHAR(255)',
                'displaytype' => 1,
                'filterSequence' => 5,
              ],
              'LBL_WFARTICLES_PART_NUMBER' => [
                'name' => 'part_num',
                'table' => 'vtiger_wfarticles',
                'column' => 'part_num',
                'uitype' => 7,
                'typeofdata' => 'N~O~MIN=0',
                'sequence' => 2,
                'columntype' => 'INT(10)',
                'summaryfield' => 1,
                'displaytype' => 1,
                'filterSequence' => 6,
              ],
              'LBL_WFARTICLES_VENDOR' => [
                'name' => 'vendor',
                'table' => 'vtiger_wfarticles',
                'column' => 'vendor',
                'uitype' => 2,
                'typeofdata' => 'V~M',
                'sequence' => 3,
                'columntype' => 'VARCHAR(255)',
                'summaryfield' => 1,
                'displaytype' => 1,
              ],
              'LBL_WFARTICLES_VENDOR_NUMBER' => [
                'name' => 'vendor_num',
                'table' => 'vtiger_wfarticles',
                'column' => 'vendor_num',
                'uitype' => 2,
                'typeofdata' => 'V~M',
                'sequence' => 4,
                'columntype' => 'VARCHAR(255)',
                'summaryfield' => 1,
                'displaytype' => 1,
              ],
              'LBL_WFARTICLES_PART_NUMBER' => [
                'name' => 'part_num',
                'table' => 'vtiger_wfarticles',
                'column' => 'part_num',
                'uitype' => 2,
                'typeofdata' => 'V~M',
                'sequence' => 5,
                'columntype' => 'VARCHAR(255)',
                'summaryfield' => 1,
                'displaytype' => 1,
              ],
              'LBL_WFARTICLES_WIDTH' => [
                'name' => 'width',
                'table' => 'vtiger_wfarticles',
                'column' => 'width',
                'uitype' => 7,
                'typeofdata' => 'N~O~MIN=0',
                'sequence' => 6,
                'columntype' => 'INT(10)',
                'summaryfield' => 1,
                'displaytype' => 1,
                'filterSequence' => 7,
              ],
              'LBL_WFARTICLES_DEPTH' => [
                'name' => 'depth',
                'table' => 'vtiger_wfarticles',
                'column' => 'depth',
                'uitype' => 7,
                'typeofdata' => 'N~O~MIN=0',
                'columntype' => 'INT(10)',
                'sequence' => 7,
                'summaryfield' => 1,
                'displaytype' => 1,
                'filterSequence' => 8,
              ],
              'LBL_WFARTICLES_HEIGHT' => [
                'name' => 'height',
                'table' => 'vtiger_wfarticles',
                'column' => 'height',
                'uitype' => 7,
                'typeofdata' => 'N~O~MIN=0',
                'columntype' => 'INT(10)',
                'sequence' => 8,
                'summaryfield' => 1,
                'displaytype' => 1,
                'filterSequence' => 9,
              ],
              'LBL_WFARTICLES_SQ_FT' => [
                'name' => 'sq_ft',
                'table' => 'vtiger_wfarticles',
                'column' => 'sq_ft',
                'uitype' => 7,
                'typeofdata' => 'N~O~MIN=0',
                'sequence' => 9,
                'columntype' => 'INT(10)',
                'summaryfield' => 1,
                'displaytype' => 1,
                'filterSequence' => 10,
              ],
              'LBL_WFARTICLES_CU_FT' => [
                'name' => 'cu_ft',
                'table' => 'vtiger_wfarticles',
                'column' => 'cu_ft',
                'uitype' => 7,
                'typeofdata' => 'N~O~MIN=0',
                'columntype' => 'INT(15)',
                'sequence' => 10,
                'summaryfield' => 1,
                'displaytype' => 1,
              ],
              'LBL_WFARTICLES_WEIGHT' => [
                'name' => 'weight',
                'table' => 'vtiger_wfarticles',
                'column' => 'weight',
                'uitype' => 7,
                'typeofdata' => 'N~O~MIN=0',
                'columntype' => 'INT(10)',
                'sequence' => 11,
                'summaryfield' => 1,
                'displaytype' => 1,
              ],
            ],
            'LBL_WFARTICLES_ARTICLE_ATTRIBUTES' => [
              'LBL_WFARTICLES_ATTRIBUTE_1' => [
                'name' => 'attribute_1',
                'table' => 'vtiger_wfarticles',
                'column' => 'attribute_1',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'columntype' => 'VARCHAR(255)',
                'sequence' => 1,
                'summaryfield' => 1,
                'displaytype' => 1,
              ],
              'LBL_WFARTICLES_ATTRIBUTE_2' => [
                'name' => 'attribute_2',
                'table' => 'vtiger_wfarticles',
                'column' => 'attribute_2',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'sequence' => 2,
                'columntype' => 'VARCHAR(255)',
                'summaryfield' => 1,
                'displaytype' => 1,
              ],
              'LBL_WFARTICLES_ATTRIBUTE_3' => [
                'name' => 'attribute_3',
                'table' => 'vtiger_wfarticles',
                'column' => 'attribute_3',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'sequence' => 3,
                'columntype' => 'VARCHAR(255)',
                'summaryfield' => 1,
                'displaytype' => 1,
              ],
              'LBL_WFARTICLES_ATTRIBUTE_4' => [
                'name' => 'attribute_4',
                'table' => 'vtiger_wfarticles',
                'column' => 'attribute_4',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'sequence' => 4,
                'columntype' => 'VARCHAR(255)',
                'summaryfield' => 1,
                'displaytype' => 1,
              ],
              'LBL_WFARTICLES_ATTRIBUTE_5' => [
                'name' => 'attribute_5',
                'table' => 'vtiger_wfarticles',
                'column' => 'attribute_5',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'sequence' => 5,
                'columntype' => 'VARCHAR(255)',
                'summaryfield' => 1,
                'displaytype' => 1,
              ],
              'LBL_WFARTICLES_ATTRIBUTE_6' => [
                'name' => 'attribute_6',
                'table' => 'vtiger_wfarticles',
                'column' => 'attribute_6',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'sequence' => 6,
                'columntype' => 'VARCHAR(255)',
                'summaryfield' => 1,
                'displaytype' => 1,
              ],
              'LBL_WFARTICLES_ATTRIBUTE_7' => [
                'name' => 'attribute_7',
                'table' => 'vtiger_wfarticles',
                'column' => 'attribute_7',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'sequence' => 7,
                'columntype' => 'VARCHAR(255)',
                'summaryfield' => 1,
                'displaytype' => 1,
              ],
              'LBL_WFARTICLES_ATTRIBUTE_8' => [
                'name' => 'attribute_8',
                'table' => 'vtiger_wfarticles',
                'column' => 'attribute_8',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'sequence' => 8,
                'columntype' => 'VARCHAR(255)',
                'summaryfield' => 1,
                'displaytype' => 1,
              ],
            ],
          ],
        ];

multicreate($create);

$moduleInstance = Vtiger_Module::getInstance('WFArticles');
$filter = Vtiger_Filter::getInstance('All', $moduleInstance);

foreach($create as $module=>$blocks) {
  foreach($blocks as $label=>$fields) {
    foreach($fields as $fieldLabel=>$fieldData) {
      if(!isset($fieldData['filterSequence'])) {
        continue;
      }
      $field = Vtiger_Field::getInstance($fieldData['name'], $moduleInstance);
      if($field) {
        $filter->addField($field,$fieldData['filterSequence']);
      }
    }
  }
}

$field = Vtiger_Field::getInstance('wfaccount',$moduleInstance);
if($field) {
    $field->delete();
}

$field = Vtiger_Field::getInstance('assigned_user_id',$moduleInstance);
$block = Vtiger_Block::getInstance('LBL_WFARTICLES_ARTICLE_INFORMATION',$moduleInstance);

if($field && $block) {
  Vtiger_Utils::ExecuteQuery("UPDATE vtiger_field SET `block` = $block->id, `sequence` = 8 WHERE `fieldid` = $field->id");
}
