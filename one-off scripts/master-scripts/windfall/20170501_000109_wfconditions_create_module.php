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




$create = ['WFConditions' => [
            'LBL_WFCONDITIONS_DETAILS' => []
            ]
          ];

$isNew = false;
foreach($create as $module=>$data) {
  $moduleInstance = Vtiger_Module::getInstance($module);
  if ($moduleInstance) {
    echo "<h2>$module already exists </h2><br>";
  } else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $module;
    $moduleInstance->save();
    $moduleInstance->initTables();
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
    $isNew = true;
  }

  foreach ($data as $blockLabel=>$fields) {
    $blockInstance = Vtiger_Block::getInstance($blockLabel, $moduleInstance);

    if ($blockInstance) {
      echo "<h3>The $blockLabel block already exists</h3><br> \n";
    } else {
      $blockInstance = new Vtiger_Block();
      $blockInstance->label = $blockLabel;
      $moduleInstance->addBlock($blockInstance);
    }

    if(empty($fields)) {
      continue;
    }

    foreach($fields as $fieldLabel=>$fieldAttributes) {
      $field = Vtiger_Field::getInstance($fieldLabel, $moduleInstance);
      if ($field) {
        echo "<br> $fieldLabel already exists <br>";
      } else {
        $field = new Vtiger_Field();
        $field->label = $fieldLabel;
        $field->name = $fieldAttributes['name'];
        $field->table = $fieldAttributes['table'];
        $field->column = $fieldAttributes['column'];
        $field->columntype = $fieldAttributes['columntype'];
        $field->uitype = $fieldAttributes['uitype'];
        $field->typeofdata = $fieldAttributes['typeofdata'];
        $field->displaytype = isset($fieldAttributes['displaytype']) ? $fieldAttributes['displaytype'] : 1;
        $field->presence = isset($fieldAttributes['presence']) ? $fieldAttributes['presence'] : 0;
        $field->sequence = $fieldAttributes['sequence'];
        $blockInstance->addField($field);
        if($fieldLabel == '') {
          $moduleInstance->setEntityIdentifier($field);
        }
        if(isset($fieldAttributes['setRelatedModules'])) {
          $field->setRelatedModules($fieldAttributes['setRelatedModules']);
        }
        if(isset($fieldAttributes['setPicklistValues'])) {
          $field->setPicklistValues($fieldAttributes['setPicklistValues']);
        }
      }
      if ($isNew && $field) {
        $filter = Vtiger_Filter::getInstance('All', $moduleInstance);
        if(!$filter) {
          $filter = new Vtiger_Filter();
          $filter->name = 'All';
          $filter->isdefault = true;
          $moduleInstance->addFilter($filter);
        }

        $filter->addField($field);
      }
    }
    //Menu
    $parentLabel = 'COMPANY_ADMIN_TAB';
    $db = PearDatabase::getInstance();
    if ($db) {
        $stmt = 'UPDATE vtiger_tab SET parent=? WHERE tabid=?';
        $db->pquery($stmt, [$parentLabel, $moduleInstance->id]);
    } else {
        Vtiger_Utils::ExecuteQuery("UPDATE vtiger_tab SET parent='" . $parentLabel . "' WHERE tabid=" . $moduleInstance->id);
    }
  }
}
