<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/30/2017
 * Time: 10:28 AM
 */
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('includes/main/WebUI.php');

$db = &PearDatabase::getInstance();

$moduleInstance = Vtiger_Module::getInstance('VehicleLookup');

if (!$moduleInstance) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'VehicleTransportation';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $new_module = true;
} else {
    $db->pquery('ALTER TABLE `vtiger_vehiclelookup` CHANGE `vehicleid` `vehiclelookupid` INT(11) NOT NULL');
    $moduleInstance->initTables();
}

$block = Vtiger_Block::getInstance('LBL_VEHICLELOOKUP_INFORMATION', $moduleInstance);
if (!$block) {
    $block = new Vtiger_Block();
    $block->label = 'LBL_VEHICLELOOKUP_INFORMATION';
    $moduleInstance->addBlock($block);
}


$field1 = Vtiger_Field::getInstance('agentid', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'Owner';
    $field1->name = 'agentid';
    $field1->table = 'vtiger_crmentity';
    $field1->column = 'agentid';
    $field1->uitype = 1002;
    $field1->typeofdata = 'I~M';

    $block->addField($field1);
}
$field2 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'Assigned To';
    $field2->name = 'assigned_user_id';
    $field2->table = 'vtiger_crmentity';
    $field2->column = 'smownerid';
    $field2->uitype = 53;
    $field2->typeofdata = 'V~M';

    $block->addField($field2);
}

//                                crmid INT(11),
$field = Vtiger_Field::getInstance('vehiclelookup_relcrmid', $moduleInstance);
if ($field) {
    echo "The vehiclelookup_relcrmid field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_VEHICLE_RELATED';
    $field->name       = 'vehiclelookup_relcrmid';
    $field->table      = 'vtiger_vehiclelookup';
    $field->column     = 'vehiclelookup_relcrmid';
    $field->columntype = 'INT(11), ADD INDEX (vehiclelookup_relcrmid)';
    $field->uitype     = 10;
    $field->typeofdata = 'I~M';
    $block->addField($field);
    $field->setRelatedModules(array('Leads','Opportunities','Orders','Estimates'));
}
//								vehicle_make VARCHAR(50),
$field = Vtiger_Field::getInstance('vehiclelookup_make', $moduleInstance);
if ($field) {
    echo "The vehiclelookup_make field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_VEHICLE_MAKE';
    $field->name       = 'vehiclelookup_make';
    $field->table      = 'vtiger_vehiclelookup';
    $field->column     = 'vehiclelookup_make';
    $field->columntype = 'VARCHAR(50)';
    $field->uitype     = 1;
    $field->typeofdata = 'V~O';
    $block->addField($field);
}
//								vehicle_model VARCHAR(100),
$field = Vtiger_Field::getInstance('vehiclelookup_model', $moduleInstance);
if ($field) {
    echo "The vehiclelookup_model field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_VEHICLE_MODEL';
    $field->name       = 'vehiclelookup_model';
    $field->table      = 'vtiger_vehiclelookup';
    $field->column     = 'vehiclelookup_model';
    $field->columntype = 'VARCHAR(100)';
    $field->uitype     = 1;
    $field->typeofdata = 'V~O';
    $block->addField($field);
}
//								vehicle_year INT(6),
$field = Vtiger_Field::getInstance('vehiclelookup_year', $moduleInstance);
if ($field) {
    echo "The vehiclelookup_year field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_VEHICLE_YEAR';
    $field->name       = 'vehiclelookup_year';
    $field->table      = 'vtiger_vehiclelookup';
    $field->column     = 'vehiclelookup_year';
    $field->columntype = 'INT(6)';
    $field->uitype     = 7;
    $field->typeofdata = 'I~O';
    $block->addField($field);
}
//								vehicle_vin VARCHAR(20),
$field = Vtiger_Field::getInstance('vehiclelookup_vin', $moduleInstance);
if ($field) {
    echo "The vehiclelookup_make field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_VEHICLE_VIN';
    $field->name       = 'vehiclelookup_vin';
    $field->table      = 'vtiger_vehiclelookup';
    $field->column     = 'vehiclelookup_vin';
    $field->columntype = 'VARCHAR(20)';
    $field->uitype     = 1;
    $field->typeofdata = 'V~O';
    $block->addField($field);
    $moduleInstance->setEntityIdentifier($field);
}
//								vehicle_color VARCHAR(25),
$field = Vtiger_Field::getInstance('vehiclelookup_color', $moduleInstance);
if ($field) {
    echo "The vehiclelookup_color field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_VEHICLE_COLOR';
    $field->name       = 'vehiclelookup_color';
    $field->table      = 'vtiger_vehiclelookup';
    $field->column     = 'vehiclelookup_color';
    $field->columntype = 'VARCHAR(25)';
    $field->uitype     = 1;
    $field->typeofdata = 'V~O';
    $block->addField($field);
}
//								vehicle_odometer DECIMAL(10,1),
$field = Vtiger_Field::getInstance('vehiclelookup_odometer', $moduleInstance);
if ($field) {
    echo "The vehiclelookup_odometer field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_VEHICLE_ODOMETER';
    $field->name       = 'vehiclelookup_odometer';
    $field->table      = 'vtiger_vehiclelookup';
    $field->column     = 'vehiclelookup_odometer';
    $field->columntype = 'DECIMAL(10,1)';
    $field->uitype     = 7;
    $field->typeofdata = 'N~O';
    $block->addField($field);
}
//								license_state VARCHAR(30),
$field = Vtiger_Field::getInstance('vehiclelookup_license_state', $moduleInstance);
if ($field) {
    echo "The vehiclelookup_license_state field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_VEHICLE_LSTATE';
    $field->name       = 'vehiclelookup_license_state';
    $field->table      = 'vtiger_vehiclelookup';
    $field->column     = 'vehiclelookup_license_state';
    $field->columntype = 'VARCHAR(30)';
    $field->uitype     = 1;
    $field->typeofdata = 'V~O';
    $block->addField($field);
}
//								license_number VARCHAR(30),
$field = Vtiger_Field::getInstance('vehiclelookup_license_number', $moduleInstance);
if ($field) {
    echo "The vehiclelookup_license_number field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_VEHICLE_LNUMBER';
    $field->name       = 'vehiclelookup_license_number';
    $field->table      = 'vtiger_vehiclelookup';
    $field->column     = 'vehiclelookup_license_number';
    $field->columntype = 'VARCHAR(30)';
    $field->uitype     = 1;
    $field->typeofdata = 'V~O';
    $block->addField($field);
}
//								vehicle_type VARCHAR(10),
$field = Vtiger_Field::getInstance('vehiclelookup_type', $moduleInstance);
if ($field) {
    echo "The vehiclelookup_type field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_VEHICLE_TYPE';
    $field->name       = 'vehiclelookup_type';
    $field->table      = 'vtiger_vehiclelookup';
    $field->column     = 'vehiclelookup_type';
    $field->columntype = 'VARCHAR(10)';
    $field->uitype     = 16;
    $field->typeofdata = 'V~O';
    $block->addField($field);
    $field->setPicklistValues(['Car','Truck','SUV','Auto Trailer']);
}

////Vtiger_Utils::AddColumn('vtiger_vehiclelookup', 'is_non_standard', 'TINYINT(1)');
$field = Vtiger_Field::getInstance('vehiclelookup_is_non_standard', $moduleInstance);
if ($field) {
    echo "The vehiclelookup_is_non_standard field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_VEHICLE_ISNONSTANDARD';
    $field->name       = 'vehiclelookup_is_non_standard';
    $field->table      = 'vtiger_vehiclelookup';
    $field->column     = 'vehiclelookup_is_non_standard';
    $field->columntype = 'VARCHAR(3)';
    $field->uitype     = 56;
    $field->typeofdata = 'V~O';
    $block->addField($field);
}
////Vtiger_Utils::AddColumn('vtiger_vehiclelookup', 'inoperable', 'TINYINT(1)');
$field = Vtiger_Field::getInstance('vehiclelookup_inoperable', $moduleInstance);
if ($field) {
    echo "The vehiclelookup_inoperable field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_VEHICLE_INOPERABLE';
    $field->name       = 'vehiclelookup_inoperable';
    $field->table      = 'vtiger_vehiclelookup';
    $field->column     = 'vehiclelookup_inoperable';
    $field->columntype = 'VARCHAR(3)';
    $field->uitype     = 56;
    $field->typeofdata = 'V~O';
    $block->addField($field);
}
//

$block->save($moduleInstance);

$moduleInstance->setDefaultSharing();
$moduleInstance->initWebservice();

$relatedModuleNames = ['Leads','Opportunities','Orders','Estimates'];

foreach ($relatedModuleNames as $moduleName)
{
    $relatedModule = Vtiger_Module::getInstance($moduleName);
    if(!$relatedModule)
    {
        continue;
    }
    $relatedModule->setGuestBlocks('VehicleLookup', ['LBL_VEHICLELOOKUP_INFORMATION']);
}

$result = $db->pquery('SELECT * FROM vtiger_vehiclelookup WHERE crmid IS NOT NULL LIMIT 1',[]);
while($row = $result->fetchRow())
{
    $rec = Vtiger_Record_Model::getCleanInstance('VehicleLookup');
    $rec->set('vehiclelookup_relcrmid', $row['crmid']);
    $rec->set('vehiclelookup_make', $row['vehicle_make']);
    $rec->set('vehiclelookup_model', $row['vehicle_model']);
    $rec->set('vehiclelookup_year', $row['vehicle_year']);
    $rec->set('vehiclelookup_vin', $row['vehicle_vin']);
    $rec->set('vehiclelookup_color', $row['vehicle_color']);
    $rec->set('vehiclelookup_odometer', $row['vehicle_odometer']);
    $rec->set('vehiclelookup_license_state', $row['license_state']);
    $rec->set('vehiclelookup_license_number', $row['license_number']);
    $rec->set('vehiclelookup_type', $row['vehicle_type']);
    $rec->set('vehiclelookup_is_non_standard', $row['is_non_standard']);
    $rec->set('vehiclelookup_inoperable', $row['inoperable']);
    $rec->set('assigned_user_id', 1);
    $rec->save();
    if($rec->getId()) {
        $db->pquery('DELETE FROM vtiger_vehiclelookup WHERE vehiclelookupid=?', [$row['vehiclelookupid']]);
    } else {
        echo 'Error converting VehicleLookup entry!<br>'.PHP_EOL;
        return;
    }
    $result = $db->pquery('SELECT * FROM vtiger_vehiclelookup WHERE crmid IS NOT NULL LIMIT 1',[]);
}



//$estInstance = Vtiger_Module::getInstance('Estimates');
//$estInstance->setGuestBlocks('VehicleTransportation', ['LBL_VEHICLETRANSPORTATION_INFORMATION']);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";