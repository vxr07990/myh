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

global $adb;

$moduleName = 'Locations';
$blockName = 'LBL_LOCATIONS_DETAILS';
$moduleLocationsInstance = Vtiger_Module::getInstance($moduleName);

$filterAll= Vtiger_Filter::getInstance('All',$moduleLocationsInstance);
if($filterAll) $filterAll->delete();
$filterAll = new Vtiger_Filter();
$filterAll->name = 'All';
$filterAll->isdefault = true;
$moduleLocationsInstance->addFilter($filterAll);

if($moduleLocationsInstance){
    $blockInstance = Vtiger_Block::getInstance($blockName,$moduleLocationsInstance);
    if ($blockInstance) {
        $fieldName = "locations_id";
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if ($field) {
            $field->delete();
            $sql = "ALTER TABLE `vtiger_locations` DROP COLUMN `$fieldName`";
            $adb->pquery($sql);
        }

        $fieldName = "locations_type";
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if ($field) {
            $field->delete();
            $sql = "ALTER TABLE `vtiger_locations` DROP COLUMN `$fieldName`";
            $adb->pquery($sql);
        }


        // Create new field module
        $fieldName = "location_type";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'VARCHAR(100)';
            $field->uitype = 10;
            $field->typeofdata = 'V~M';
            $field->sequence = 1;
            $blockInstance->addField($field);
            $moduleLocationsInstance->setEntityIdentifier($field);
            $field->setRelatedModules(array('LocationTypes'));
            $filterAll->addField($field,1);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }

        // Create new field module
        $fieldName = "location_tag";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'VARCHAR(60)';
            $field->uitype = 1;
            $field->typeofdata = 'V~O';
            $field->sequence = 2;
            $blockInstance->addField($field);


            $filterAll->addField($field,2);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {

            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }

        // Create new field module
        $fieldName = "location_description";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'VARCHAR(255)';
            $field->uitype = 1;
            $field->typeofdata = 'V~O';
            $field->sequence = 3;
            $blockInstance->addField($field);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {

            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }

        // Create new field module
        $fieldName = "location_combination";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'VARCHAR(255)';
            $field->uitype = 1;
            $field->typeofdata = 'V~O';
            $field->sequence = 4;
            $blockInstance->addField($field);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {

            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }


        // Create new field module
        $fieldName = "location_agent";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'VARCHAR(100)';
            $field->uitype = 10;
            $field->typeofdata = 'V~O';
            $field->sequence = 5;
            $blockInstance->addField($field);
            $field->setRelatedModules(array('Agents'));
            $filterAll->addField($field,3);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }


        // Create new field module
        $fieldName = "location_warehouse";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'VARCHAR(100)';
            $field->uitype = 10;
            $field->typeofdata = 'V~O';
            $field->sequence = 6;
            $blockInstance->addField($field);
            $field->setRelatedModules(array('Warehouse'));

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }

        $fieldName = "location_slot_configuration";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'VARCHAR(100)';
            $field->uitype = 10;
            $field->typeofdata = 'V~O';
            $field->sequence = 7;
            $blockInstance->addField($field);
            $field->setRelatedModules(array('SlotConfiguration'));

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }


        $fieldName = "location_active";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'VARCHAR(3)';
            $field->uitype = 56;
            $field->typeofdata = 'V~O';
            $field->sequence = 8;
            $blockInstance->addField($field);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }

        $fieldName = "location_base";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'VARCHAR(100)';
            $field->uitype = 10;
            $field->typeofdata = 'V~O';
            $field->sequence = 9;
            $blockInstance->addField($field);
            $moduleLocationsInstance->setEntityIdentifier($field);
            $field->setRelatedModules(array('Locations'));
            $filterAll->addField($field,1);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }


        // Create new field module
        $fieldName = "location_slot";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'VARCHAR(100)';
            $field->uitype = 1;
            $field->typeofdata = 'V~O';
            $field->sequence = 10;
            $blockInstance->addField($field);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {

            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }

        $fieldName = "location_reserved";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'VARCHAR(3)';
            $field->uitype = 56;
            $field->typeofdata = 'V~O';
            $field->sequence = 11;
            $blockInstance->addField($field);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }

        $fieldName = "location_offsite";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'VARCHAR(3)';
            $field->uitype = 56;
            $field->typeofdata = 'V~O';
            $field->sequence = 12;
            $blockInstance->addField($field);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }


        $fieldName = "location_squarefeet";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'NUMERIC(65)';
            $field->uitype = 1;
            $field->typeofdata = 'N~O';
            $field->sequence = 13;
            $blockInstance->addField($field);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }


        $fieldName = "location_cubefeet";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'NUMERIC(65)';
            $field->uitype = 1;
            $field->typeofdata = 'N~O';
            $field->sequence = 14;
            $blockInstance->addField($field);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }

        $fieldName = "location_cost";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'NUMERIC(65)';
            $field->uitype = 1;
            $field->typeofdata = 'N~O';
            $field->sequence = 15;
            $blockInstance->addField($field);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }

        $fieldName = "location_percentused";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'Varchar(100)';
            $field->uitype = 9;
            $field->typeofdata = 'V~O';
            $field->sequence = 16;
            $blockInstance->addField($field);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }

        $fieldName = "location_percentusedoverride";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'Varchar(3)';
            $field->uitype = 56;
            $field->typeofdata = 'V~O';
            $field->sequence = 17;
            $blockInstance->addField($field);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }


        $fieldName = "location_row";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'Varchar(15)';
            $field->uitype = 1;
            $field->typeofdata = 'V~O';
            $field->sequence = 18;
            $blockInstance->addField($field);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }

        $fieldName = "location_bay";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'Varchar(15)';
            $field->uitype = 1;
            $field->typeofdata = 'V~O';
            $field->sequence = 19;
            $blockInstance->addField($field);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }


        $fieldName = "location_level";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'Varchar(15)';
            $field->uitype = 1;
            $field->typeofdata = 'V~O';
            $field->sequence = 20;
            $blockInstance->addField($field);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }



        $fieldName = "location_double_high";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'Varchar(3)';
            $field->uitype = 56;
            $field->typeofdata = 'V~O';
            $field->sequence = 21;
            $blockInstance->addField($field);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }


        $fieldName = "location_container_capacity_on";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'Varchar(3)';
            $field->uitype = 56;
            $field->typeofdata = 'V~O';
            $field->sequence = 22;
            $blockInstance->addField($field);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }


        $fieldName = "location_container_capacity";
        $fieldLabel = "LBL_" . strtoupper($fieldName);
        $field = Vtiger_Field::getInstance($fieldName, $moduleLocationsInstance);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_locations';
            $field->column = $fieldName;
            $field->columntype = 'Varchar(3)';
            $field->uitype = 56;
            $field->typeofdata = 'V~O';
            $field->sequence = 23;
            $blockInstance->addField($field);

            echo "<br>create '$fieldName' in $moduleName Module<br>";
        } else {
            echo "<br>'$fieldName' in $moduleName Module have already<br>";
        }
    }
}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";