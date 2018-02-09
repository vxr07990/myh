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
global $adb;

$moduleInstance = Vtiger_Module::getInstance('Orders');



$MilitaryBlockWithinOrders = Vtiger_Block::getInstance('LBL_MILITARY_INFORMATION', $moduleInstance);
if(!$MilitaryBlockWithinOrders){
    $MilitaryBlockWithinOrders = new Vtiger_Block();
    $MilitaryBlockWithinOrders->label = 'LBL_MILITARY_INFORMATION';
    $moduleInstance->addBlock($MilitaryBlockWithinOrders);
    echo "Create block LBL_MILITARY_INFORMATION on Orders <br>";
}else{
    $sql = "UPDATE `vtiger_blocks` SET `vtiger_blocks`.`sequence`=99 WHERE `vtiger_blocks`.`blockid`=?";
    $adb->pquery($sql,array($MilitaryBlockWithinOrders->id));

    $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=1 WHERE `vtiger_field`.`block`=?";
    $adb->pquery($sql,array($MilitaryBlockWithinOrders->id));
    echo "<li>update presence of all field in LBL_MILITARY_INFORMATION to 1</li>";
}

// create field 'Transferee ssn'
$fieldname = 'transferee_ssn';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$TransfereeSSNField = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($TransfereeSSNField) {
    if ($TransfereeSSNField->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$TransfereeSSNField->id));
    }
    echo "<li>The 'transferee_ssn' field already exists</li><br>";
} else {
    $TransfereeSSNField = new Vtiger_Field();
    $TransfereeSSNField->name = $fieldname;
    $TransfereeSSNField->label = $fieldlabel;
    $TransfereeSSNField->column = $fieldname;
    $TransfereeSSNField->table = 'vtiger_orders';
    $TransfereeSSNField->columntype = 'VARCHAR(11)';
    $TransfereeSSNField->uitype = 7;
    $TransfereeSSNField->typeofdata = 'V~O';
    $TransfereeSSNField->sequence = 1;

    $MilitaryBlockWithinOrders->addField($TransfereeSSNField);
    echo "<li>The '$fieldname' field created done</li><br>";
}


// create field 'transferee_rank_grade'
$fieldname = 'transferee_rank_grade';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$TransfereeRankGrade = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($TransfereeRankGrade) {
    echo "<li>The 'transferee_ssn' field already exists</li><br>";
    if ($TransfereeRankGrade->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$TransfereeRankGrade->id));
        echo "<li>Update presence 'transferee_ssn' field to  2</li><br>";
    }
    if ($TransfereeRankGrade->uitype != 1){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`uitype`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(1,$TransfereeRankGrade->id));
        echo "<li>Update UITYPE 'transferee_ssn' field to  1</li><br>";
    }

    if ($TransfereeRankGrade->sequence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`sequence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$TransfereeRankGrade->id));
        echo "<li>Update sequence 'transferee_ssn' field to  2</li><br>";
    }
} else {
    $TransfereeRankGrade = new Vtiger_Field();
    $TransfereeRankGrade->label = $fieldlabel;
    $TransfereeRankGrade->name = $fieldname;
    $TransfereeRankGrade->table = 'vtiger_orders';
    $TransfereeRankGrade->column = $fieldname;
    $TransfereeRankGrade->columntype = 'VARCHAR(100)';
    $TransfereeRankGrade->uitype = 1;
    $TransfereeRankGrade->typeofdata = 'V~O';
    $TransfereeRankGrade->sequence = 2;

    $MilitaryBlockWithinOrders->addField($TransfereeRankGrade);
    echo "<li>The '$fieldname' field created done</li><br>";
}

// create field 'name_of_agent'
$fieldname = 'name_of_agent';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$NameOfAgent = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($NameOfAgent) {
    echo "<li>The '$fieldname' field already exists</li><br>";
    if ($NameOfAgent->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$NameOfAgent->id));
    }
} else {
    $NameOfAgent = new Vtiger_Field();
    $NameOfAgent->label = $fieldlabel;
    $NameOfAgent->name = $fieldname;
    $NameOfAgent->table = 'vtiger_orders';
    $NameOfAgent->column = $fieldname;
    $NameOfAgent->columntype = 'VARCHAR(100)';
    $NameOfAgent->uitype = 1;
    $NameOfAgent->typeofdata = 'V~O';
    $NameOfAgent->sequence = 3;

    $MilitaryBlockWithinOrders->addField($NameOfAgent);
    echo "<li>The '$fieldname' field created done</li><br>";
}


// create field 'transferee_military_branch'
$fieldname = 'transferee_military_branch';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$TransfereeMilitaryBranch = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($TransfereeMilitaryBranch) {
    echo "<li>The '$fieldname' field already exists</li><br>";
    if ($TransfereeMilitaryBranch->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$TransfereeMilitaryBranch->id));
    }
} else {
    $TransfereeMilitaryBranch = new Vtiger_Field();
    $TransfereeMilitaryBranch->label = $fieldlabel;
    $TransfereeMilitaryBranch->name = $fieldname;
    $TransfereeMilitaryBranch->table = 'vtiger_orders';
    $TransfereeMilitaryBranch->column = $fieldname;
    $TransfereeMilitaryBranch->columntype = 'VARCHAR(100)';
    $TransfereeMilitaryBranch->uitype = 16;
    $TransfereeMilitaryBranch->typeofdata = 'V~O';
    $TransfereeMilitaryBranch->sequence = 4;
    $TransfereeMilitaryBranch->setPicklistValues(array('Air Force','Army','Coast Guard','Marines','Navy','US Navy'));
    $MilitaryBlockWithinOrders->addField($TransfereeMilitaryBranch);
    echo "<li>The '$fieldname' field created done</li><br>";
}


// create field 'carrier_company'
$fieldname = 'carrier_company';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$carrierCompany = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($carrierCompany) {
    echo "<li>The '$fieldname' field already exists</li><br>";
    if ($carrierCompany->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$carrierCompany->id));
    }
} else {
    $carrierCompany = new Vtiger_Field();
    $carrierCompany->label = $fieldlabel;
    $carrierCompany->name = $fieldname;
    $carrierCompany->table = 'vtiger_orders';
    $carrierCompany->column = $fieldname;
    $carrierCompany->columntype = 'VARCHAR(100)';
    $carrierCompany->uitype = 10;
    $carrierCompany->sequence = 5;

    $MilitaryBlockWithinOrders->addField($carrierCompany);

    $carrierCompany->setRelatedModules(array('Carriers'));
    echo "<li>The '$fieldname' field created done</li><br>";
}


// create field 'carrier_scac_code'
$fieldname = 'carrier_scac_code';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$carrierSCACCode = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($carrierSCACCode) {
    if ($carrierSCACCode->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$carrierSCACCode->id));
    }
    echo "<li>The '$fieldname' field already exists</li><br>";
} else {
    $carrierSCACCode = new Vtiger_Field();
    $carrierSCACCode->label = $fieldlabel;
    $carrierSCACCode->name = $fieldname;
    $carrierSCACCode->table = 'vtiger_orders';
    $carrierSCACCode->column = $fieldname;
    $carrierSCACCode->columntype = 'VARCHAR(100)';
    $carrierSCACCode->uitype = 10;
    $carrierSCACCode->sequence = 6;

    $MilitaryBlockWithinOrders->addField($carrierSCACCode);

    $carrierSCACCode->setRelatedModules(array('Carriers'));
    echo "<li>The '$fieldname' field created done</li><br>";
}

// create field 'gbl_number'
$fieldname = 'gbl_number';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$GBLNumber = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($GBLNumber) {
    echo "<li>The '$fieldname' field already exists</li><br>";
    if ($GBLNumber->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$GBLNumber->id));
    }

    if ($GBLNumber->uitype != 1){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`uitype`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(1,$GBLNumber->id));
        echo "<li>Update UITYPE '$fieldname' field to  1</li><br>";
    }

    if ($GBLNumber->sequence != 7){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`sequence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(7,$GBLNumber->id));
        echo "<li>Update sequence '$fieldname' field to  1</li><br>";
    }

} else {
    $GBLNumber = new Vtiger_Field();
    $GBLNumber->label = $fieldlabel;
    $GBLNumber->name = $fieldname;
    $GBLNumber->table = 'vtiger_orders';
    $GBLNumber->column = $fieldname;
    $GBLNumber->columntype = 'VARCHAR(100)';
    $GBLNumber->uitype = 1;
    $GBLNumber->typeofdata = 'V~O';
    $GBLNumber->sequence = 7;

    $MilitaryBlockWithinOrders->addField($GBLNumber);
    echo "<li>The '$fieldname' field created done</li><br>";
}


// create field 'Service Code'
$fieldname = 'service_code';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$ServiceCode = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($ServiceCode) {
    if ($ServiceCode->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$ServiceCode->id));
    }
    echo "<li>The '$fieldname' field already exists</li><br>";
} else {
    $ServiceCode = new Vtiger_Field();
    $ServiceCode->label = $fieldlabel;
    $ServiceCode->name = $fieldname;
    $ServiceCode->table = 'vtiger_orders';
    $ServiceCode->column = $fieldname;
    $ServiceCode->columntype = 'VARCHAR(10)';
    $ServiceCode->uitype = 1;
    $ServiceCode->typeofdata = 'V~O';
    $ServiceCode->sequence = 8;

    $MilitaryBlockWithinOrders->addField($ServiceCode);
    echo "<li>The '$fieldname' field created done</li><br>";
}


// create field 'tendered_weight'
$fieldname = 'tendered_weight';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$TenderedWeight = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($TenderedWeight) {
    if ($TenderedWeight->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$TenderedWeight->id));
    }
    echo "<li>The '$fieldname' field already exists</li><br>";
} else {
    $TenderedWeight = new Vtiger_Field();
    $TenderedWeight->label = $fieldlabel;
    $TenderedWeight->name = $fieldname;
    $TenderedWeight->table = 'vtiger_orders';
    $TenderedWeight->column = $fieldname;
    $TenderedWeight->columntype = 'INT(10)';
    $TenderedWeight->uitype = 7;
    $TenderedWeight->typeofdata = 'I~O';
    $TenderedWeight->sequence = 9;

    $MilitaryBlockWithinOrders->addField($TenderedWeight);
    echo "<li>The '$fieldname' field created done</li><br>";
}


// create field 'Service Code'
$fieldname = 'professional_weight';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$ProfessionalWeight = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($ProfessionalWeight) {
    if ($ProfessionalWeight->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$ProfessionalWeight->id));
    }
    echo "<li>The '$fieldname' field already exists</li><br>";
} else {
    $ProfessionalWeight = new Vtiger_Field();
    $ProfessionalWeight->label = $fieldlabel;
    $ProfessionalWeight->name = $fieldname;
    $ProfessionalWeight->table = 'vtiger_orders';
    $ProfessionalWeight->column = $fieldname;
    $ProfessionalWeight->columntype = 'INT(10)';
    $ProfessionalWeight->uitype = 7;
    $ProfessionalWeight->typeofdata = 'I~O';
    $ProfessionalWeight->sequence = 10;

    $MilitaryBlockWithinOrders->addField($ProfessionalWeight);
    echo "<li>The '$fieldname' field created done</li><br>";
}


// create field 'date_of_pickup'
$fieldname = 'date_of_pickup';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$DateOfPickup = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($DateOfPickup) {
    if ($DateOfPickup->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$DateOfPickup->id));
    }
    echo "<li>The '$fieldname' field already exists</li><br>";
} else {
    $DateOfPickup = new Vtiger_Field();
    $DateOfPickup->label = $fieldlabel;
    $DateOfPickup->name = $fieldname;
    $DateOfPickup->table = 'vtiger_orders';
    $DateOfPickup->columntype = 'VARCHAR(100)';
    $DateOfPickup->column = $fieldname;
    $DateOfPickup->uitype = 5;
    $DateOfPickup->typeofdata = 'D~O';
    $DateOfPickup->sequence = 11;

    $MilitaryBlockWithinOrders->addField($DateOfPickup);
    echo "<li>The '$fieldname' field created done</li><br>";
}


// create field 'Agent / Driver Code'
$fieldname = 'agent_driver_code';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$AgentDriverCode = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($AgentDriverCode) {
    if ($DateOfPickup->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$AgentDriverCode->id));
    }
    echo "<li>The '$fieldname' field already exists</li><br>";
} else {
    $AgentDriverCode = new Vtiger_Field();
    $AgentDriverCode->label = $fieldlabel;
    $AgentDriverCode->name = $fieldname;
    $AgentDriverCode->table = 'vtiger_orders';
    $AgentDriverCode->column = $fieldname;
    $AgentDriverCode->columntype = 'VARCHAR(100)';
    $AgentDriverCode->uitype = 1;
    $AgentDriverCode->typeofdata = 'V~O';
    $AgentDriverCode->sequence = 12;

    $MilitaryBlockWithinOrders->addField($AgentDriverCode);
    echo "<li>The '$fieldname' field created done</li><br>";
}


// create field 'issuing_office_gbloc'
$fieldname = 'issuing_office_gbloc';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$IssuingOfficeGBLOC = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($IssuingOfficeGBLOC) {
    if ($IssuingOfficeGBLOC->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$IssuingOfficeGBLOC->id));
    }
    echo "<li>The '$fieldname' field already exists</li><br>";
} else {
    $IssuingOfficeGBLOC = new Vtiger_Field();
    $IssuingOfficeGBLOC->label = $fieldlabel;
    $IssuingOfficeGBLOC->name = $fieldname;
    $IssuingOfficeGBLOC->table = 'vtiger_orders';
    $IssuingOfficeGBLOC->column = $fieldname;
    $IssuingOfficeGBLOC->columntype = 'VARCHAR(100)';
    $IssuingOfficeGBLOC->uitype = 10;
    $IssuingOfficeGBLOC->sequence = 13;

    $MilitaryBlockWithinOrders->addField($IssuingOfficeGBLOC);

    $IssuingOfficeGBLOC->setRelatedModules(array('MilitaryBases'));
    echo "<li>The '$fieldname' field created done</li><br>";
}

$fieldname = 'responsible_dest_office_gbloc';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$ResponsibleDestOfficeGBLOC = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($ResponsibleDestOfficeGBLOC) {
    if ($ResponsibleDestOfficeGBLOC->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$ResponsibleDestOfficeGBLOC->id));
    }
    echo "<li>The '$fieldname' field already exists</li><br>";
} else {
    $ResponsibleDestOfficeGBLOC = new Vtiger_Field();
    $ResponsibleDestOfficeGBLOC->label = $fieldlabel;
    $ResponsibleDestOfficeGBLOC->name = $fieldname;
    $ResponsibleDestOfficeGBLOC->table = 'vtiger_orders';
    $ResponsibleDestOfficeGBLOC->column = $fieldname;
    $ResponsibleDestOfficeGBLOC->columntype = 'VARCHAR(100)';
    $ResponsibleDestOfficeGBLOC->uitype = 10;
    $ResponsibleDestOfficeGBLOC->sequence = 14;

    $MilitaryBlockWithinOrders->addField($ResponsibleDestOfficeGBLOC);

    $ResponsibleDestOfficeGBLOC->setRelatedModules(array('MilitaryBases'));

    echo "<li>The '$fieldname' field created done</li><br>";
}

$fieldname = 'issuing_gbloc_location';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$IssuingGBLOCLocation = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($IssuingGBLOCLocation) {
    if ($IssuingGBLOCLocation->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$IssuingGBLOCLocation->id));
    }
    echo "<li>The '$fieldname' field already exists</li><br>";
} else {
    $IssuingGBLOCLocation = new Vtiger_Field();
    $IssuingGBLOCLocation->label = $fieldlabel;
    $IssuingGBLOCLocation->name = $fieldname;
    $IssuingGBLOCLocation->table = 'vtiger_orders';
    $IssuingGBLOCLocation->column = $fieldname;
    $IssuingGBLOCLocation->columntype = 'VARCHAR(100)';
    $IssuingGBLOCLocation->uitype = 1;
    $IssuingGBLOCLocation->sequence = 15;
    $IssuingGBLOCLocation->setRelatedModules(array('MilitaryBase'));

    $MilitaryBlockWithinOrders->addField($IssuingGBLOCLocation);
    echo "<li>The '$fieldname' field created done</li><br>";
}


$fieldname = 'dest_gbloc_location';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$DestGBLOCLocation = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($DestGBLOCLocation) {
    if ($DestGBLOCLocation->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$DestGBLOCLocation->id));
    }
    echo "<li>The '$fieldname' field already exists</li><br>";
} else {
    $DestGBLOCLocation = new Vtiger_Field();
    $DestGBLOCLocation->label = $fieldlabel;
    $DestGBLOCLocation->name = $fieldname;
    $DestGBLOCLocation->table = 'vtiger_orders';
    $DestGBLOCLocation->column = $fieldname;
    $DestGBLOCLocation->columntype = 'VARCHAR(100)';
    $DestGBLOCLocation->uitype = 1;
    $DestGBLOCLocation->sequence = 16;

    $MilitaryBlockWithinOrders->addField($DestGBLOCLocation);

    $DestGBLOCLocation->setRelatedModules(array('MilitaryBase'));
    echo "<li>The '$fieldname' field created done</li><br>";
}

// create field 'shipment_type'
$fieldname = 'shipment_type';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$ShipmentType = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($ShipmentType) {
    if ($ShipmentType->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$ShipmentType->id));
    }
    echo "<li>The '$fieldname' field already exists</li><br>";
} else {
    $ShipmentType = new Vtiger_Field();
    $ShipmentType->label = $fieldlabel;
    $ShipmentType->name = $fieldname;
    $ShipmentType->table = 'vtiger_orders';
    $ShipmentType->column = $fieldname;
    $ShipmentType->columntype = 'VARCHAR(100)';
    $ShipmentType->uitype = 16;
    $ShipmentType->typeofdata = 'V~O';
    $ShipmentType->sequence = 17;
    $ShipmentType->setPicklistValues(array('Household Good','Unaccompianed Baggage'));
    $MilitaryBlockWithinOrders->addField($ShipmentType);
    echo "<li>The '$fieldname' field created done</li><br>";
}

// create field 'market'
$fieldname = 'market';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$Market = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($Market) {
    if ($Market->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$Market->id));
    }
    echo "<li>The '$fieldname' field already exists</li><br>";
} else {
    $Market = new Vtiger_Field();
    $Market->label = $fieldlabel;
    $Market->name = $fieldname;
    $Market->table = 'vtiger_orders';
    $Market->column = $fieldname;
    $Market->columntype = 'VARCHAR(100)';
    $Market->uitype = 16;
    $Market->typeofdata = 'V~O';
    $Market->sequence = 18;
    $Market->setPicklistValues(array('International Shipment','Domestic Shipment','International Unaccompanied Baggage'));
    $MilitaryBlockWithinOrders->addField($Market);
    echo "<li>The '$fieldname' field created done</li><br>";
}

// create field 'travel_order_type'
$fieldname = 'travel_order_type';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$TravelOrderType = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($TravelOrderType) {
    if ($TravelOrderType->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$TravelOrderType->id));
    }
    echo "<li>The '$fieldname' field already exists</li><br>";
} else {
    $TravelOrderType = new Vtiger_Field();
    $TravelOrderType->label = $fieldlabel;
    $TravelOrderType->name = $fieldname;
    $TravelOrderType->table = 'vtiger_orders';
    $TravelOrderType->column = $fieldname;
    $TravelOrderType->columntype = 'VARCHAR(100)';
    $TravelOrderType->uitype = 1;
    $TravelOrderType->typeofdata = 'V~O';
    $TravelOrderType->sequence = 19;
    $MilitaryBlockWithinOrders->addField($TravelOrderType);
    echo "<li>The '$fieldname' field created done</li><br>";
}

// create field 'order_number'
$fieldname = 'order_number';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$OrderNumber = Vtiger_Field::getInstance('order_number', $moduleInstance);
if ($OrderNumber) {
    if ($OrderNumber->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$OrderNumber->id));
    }
    echo "<li>The '$fieldname' field already exists</li><br>";
} else {
    $OrderNumber = new Vtiger_Field();
    $OrderNumber->label = $fieldlabel;
    $OrderNumber->name = $fieldname;
    $OrderNumber->table = 'vtiger_orders';
    $OrderNumber->column = $fieldname;
    $OrderNumber->columntype = 'VARCHAR(100)';
    $OrderNumber->uitype = 1;
    $OrderNumber->typeofdata = 'V~O';
    $OrderNumber->sequence = 20;
    $MilitaryBlockWithinOrders->addField($OrderNumber);
    echo "<li>The '$fieldname' field created done</li><br>";
}


// create field 'tac'
$fieldname = 'tac';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$TAC = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($TAC) {
    if ($TAC->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$TAC->id));
    }
    echo "<li>The '$fieldname' field already exists</li><br>";
} else {
    $TAC = new Vtiger_Field();
    $TAC->label = $fieldlabel;
    $TAC->name = $fieldname;
    $TAC->table = 'vtiger_orders';
    $TAC->column = $fieldname;
    $TAC->columntype = 'VARCHAR(100)';
    $TAC->uitype = 1;
    $TAC->typeofdata = 'V~O';
    $TAC->sequence = 21;
    $MilitaryBlockWithinOrders->addField($TAC);
    echo "<li>The '$fieldname' field created done</li><br>";
}

// create field 'appn_number'
$fieldname = 'appn_number';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$APPNNumber = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($APPNNumber) {
    if ($APPNNumber->presence != 2){
        $sql = "UPDATE `vtiger_field` SET `vtiger_field`.`presence`=? WHERE `vtiger_field`.`fieldid`=?";
        $adb->pquery($sql, array(2,$APPNNumber->id));
    }
    echo "<li>The '$fieldname' field already exists</li><br>";
} else {
    $APPNNumber = new Vtiger_Field();
    $APPNNumber->label = $fieldlabel;
    $APPNNumber->name = $fieldname;
    $APPNNumber->table = 'vtiger_orders';
    $APPNNumber->column = $fieldname;
    $APPNNumber->columntype = 'VARCHAR(100)';
    $APPNNumber->uitype = 1;
    $APPNNumber->typeofdata = 'V~O';
    $APPNNumber->sequence = 22;
    $MilitaryBlockWithinOrders->addField($APPNNumber);
    echo "<li>The '$fieldname' field created done</li><br>";
}



$MilitaryPostMoveSurveyBlock = Vtiger_Block::getInstance('LBL_MILITARY_POST_MOVE_SURVEY', $moduleInstance);
if(!$MilitaryPostMoveSurveyBlock){
    $MilitaryPostMoveSurveyBlock = new Vtiger_Block();
    $MilitaryPostMoveSurveyBlock->label = 'LBL_MILITARY_POST_MOVE_SURVEY';
    $MilitaryPostMoveSurveyBlock->sequence = 100;
    $moduleInstance->addBlock($MilitaryPostMoveSurveyBlock);
    echo "Create block LBL_MILITARY_POST_MOVE_SURVEY on Orders <br>";
}else{
    $sql = "UPDATE `vtiger_blocks` SET `vtiger_blocks`.`sequence`=100 WHERE `vtiger_blocks`.`blockid`=?";
    $adb->pquery($sql,array($MilitaryPostMoveSurveyBlock->id));
}

$fieldname = 'survey_date';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$fileInstance = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($fileInstance){
    echo "<li>The '$fieldname' field already exists</li><br>";
}else {
    $newField = new Vtiger_Field();
    $newField->label = $fieldlabel;
    $newField->name = $fieldname;
    $newField->table = 'vtiger_orders';
    $newField->column = $fieldname;
    $newField->uitype = 5;
    $newField->columntype = 'VARCHAR(100)';
    $newField->typeofdata = 'D~O';
    $newField->sequence = 1;
    $MilitaryPostMoveSurveyBlock->addField($newField);
    echo "<li>The '$fieldname' field created done</li><br>";
}

$fieldname = 'customer_gives_permission_to_contact';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$fileInstance = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($fileInstance){
    echo "<li>The '$fieldname' field already exists</li><br>";
}else {
    $newField = new Vtiger_Field();
    $newField->label = $fieldlabel;
    $newField->name = $fieldname;
    $newField->table = 'vtiger_orders';
    $newField->column = $fieldname;
    $newField->columntype = 'VARCHAR(100)';
    $newField->uitype = 16;
    $newField->typeofdata = 'V~O';
    $newField->sequence = 2;
    $MilitaryPostMoveSurveyBlock->addField($newField);
    $newField->setPicklistValues(array('Yes','No'));
    echo "<li>The '$fieldname' field created done</li><br>";
}

$fieldname = 'q4';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$fileInstance = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($fileInstance){
    echo "<li>The '$fieldname' field already exists</li><br>";
}else {
    $newField = new Vtiger_Field();
    $newField->label = $fieldlabel;
    $newField->name = $fieldname;
    $newField->table = 'vtiger_orders';
    $newField->column = $fieldname;
    $newField->columntype = 'VARCHAR(100)';
    $newField->uitype = 16;
    $newField->typeofdata = 'V~O';
    $newField->sequence = 3;
    $MilitaryPostMoveSurveyBlock->addField($newField);
    $newField->setPicklistValues(array('N/A','1','2','3','4','5','6','7','8','9','10','11','12'));
    echo "<li>The '$fieldname' field created done</li><br>";
}


$fieldname = 'q5';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$fileInstance = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($fileInstance){
    echo "<li>The '$fieldname' field already exists</li><br>";
}else {
    $newField = new Vtiger_Field();
    $newField->label = $fieldlabel;
    $newField->name = $fieldname;
    $newField->table = 'vtiger_orders';
    $newField->column = $fieldname;
    $newField->columntype = 'VARCHAR(100)';
    $newField->uitype = 16;
    $newField->typeofdata = 'V~O';
    $newField->sequence = 4;
    $MilitaryPostMoveSurveyBlock->addField($newField);
    $newField->setPicklistValues(array('N/A','1','2','3','4','5','6','7','8','9','10','11','12'));
    echo "<li>The '$fieldname' field created done</li><br>";
}


$fieldname = 'q6';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$fileInstance = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($fileInstance){
    echo "<li>The '$fieldname' field already exists</li><br>";
}else {
    $newField = new Vtiger_Field();
    $newField->label = $fieldlabel;
    $newField->name = $fieldname;
    $newField->table = 'vtiger_orders';
    $newField->column = $fieldname;
    $newField->uitype = 16;
    $newField->typeofdata = 'V~O';
    $newField->columntype = 'VARCHAR(100)';
    $newField->sequence = 5;
    $MilitaryPostMoveSurveyBlock->addField($newField);
    $newField->setPicklistValues(array('N/A','1','2','3','4','5','6','7','8','9','10','11','12'));
    echo "<li>The '$fieldname' field created done</li><br>";
}

$fieldname = 'q7';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$fileInstance = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($fileInstance){
    echo "<li>The '$fieldname' field already exists</li><br>";
}else {
    $newField = new Vtiger_Field();
    $newField->label = $fieldlabel;
    $newField->name = $fieldname;
    $newField->table = 'vtiger_orders';
    $newField->column = $fieldname;
    $newField->columntype = 'VARCHAR(100)';
    $newField->uitype = 16;
    $newField->typeofdata = 'V~O';
    $newField->sequence = 6;
    $MilitaryPostMoveSurveyBlock->addField($newField);
    $newField->setPicklistValues(array('N/A','1','2','3','4','5','6','7','8','9','10','11','12'));
    echo "<li>The '$fieldname' field created done</li><br>";
}


$fieldname = 'q8';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$fileInstance = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($fileInstance){
    echo "<li>The '$fieldname' field already exists</li><br>";
}else {
    $newField = new Vtiger_Field();
    $newField->label = $fieldlabel;
    $newField->name = $fieldname;
    $newField->table = 'vtiger_orders';
    $newField->columntype = 'VARCHAR(100)';
    $newField->column = $fieldname;
    $newField->uitype = 16;
    $newField->typeofdata = 'V~O';
    $newField->sequence = 7;
    $MilitaryPostMoveSurveyBlock->addField($newField);
    $newField->setPicklistValues(array('N/A','1','2','3','4','5','6','7','8','9','10','11','12'));
    echo "<li>The '$fieldname' field created done</li><br>";
}

$fieldname = 'q9';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$fileInstance = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($fileInstance){
    echo "<li>The '$fieldname' field already exists</li><br>";
}else {
    $newField = new Vtiger_Field();
    $newField->label = $fieldlabel;
    $newField->name = $fieldname;
    $newField->table = 'vtiger_orders';
    $newField->column = $fieldname;
    $newField->columntype = 'VARCHAR(100)';
    $newField->uitype = 16;
    $newField->typeofdata = 'V~O';
    $newField->sequence = 8;
    $MilitaryPostMoveSurveyBlock->addField($newField);
    $newField->setPicklistValues(array('N/A','1','2','3','4','5','6','7','8','9','10','11','12'));
    echo "<li>The '$fieldname' field created done</li><br>";
}


$fieldname = 'total';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$fileInstance = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($fileInstance){
    echo "<li>The '$fieldname' field already exists</li><br>";
}else {
    $newField = new Vtiger_Field();
    $newField->label = $fieldlabel;
    $newField->name = $fieldname;
    $newField->table = 'vtiger_orders';
    $newField->column = $fieldname;
    $newField->columntype = 'VARCHAR(100)';
    $newField->uitype = 7;
    $newField->columntype = 'DECIMAL(11,3)';
    $newField->typeofdata = 'V~O';
    $newField->sequence = 9;
    $MilitaryPostMoveSurveyBlock->addField($newField);
    echo "<li>The '$fieldname' field created done</li><br>";
}

$fieldname = 'notes';
$fieldlabel = 'LBL_ORDERS_'.strtoupper($fieldname);
$fileInstance = Vtiger_Field::getInstance($fieldname, $moduleInstance);
if ($fileInstance){
    echo "<li>The '$fieldname' field already exists</li><br>";
}else {
    $newField = new Vtiger_Field();
    $newField->label = $fieldlabel;
    $newField->name = $fieldname;
    $newField->table = 'vtiger_orders';
    $newField->columntype = 'VARCHAR(100)';
    $newField->column = $fieldname;
    $newField->uitype = 19;
    $newField->typeofdata = 'V~O';
    $newField->sequence = 10;
    $MilitaryPostMoveSurveyBlock->addField($newField);
    echo "<li>The '$fieldname' field created done</li><br>";
}

// Set popup fields of Military and Carrier
$adb->pquery("UPDATE `vtiger_field` SET `summaryfield`='1' WHERE (`tabid`=? AND fieldname IN (?,?));", array(getTabid('MilitaryBases'),'gbloc','location'));
$adb->pquery("UPDATE `vtiger_field` SET `summaryfield`='1' WHERE (`tabid`=? AND fieldname IN (?,?));", array(getTabid('Carriers'),'company','scac_code'));

$AddressInformationBlockWithinOrders = Vtiger_Block::getInstance('LBL_ORDERS_ORIGINADDRESS', $moduleInstance);

$sql = "UPDATE `vtiger_blocks` SET `vtiger_blocks`.`sequence`=101 WHERE `vtiger_blocks`.`blockid`=?";
$adb->pquery($sql,array($AddressInformationBlockWithinOrders->id));

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";