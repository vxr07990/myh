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
$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';

global $adb;

$EmployeesModuleModel=Vtiger_Module::getInstance('Employees');
if (!$EmployeesModuleModel) {
    return;
}

echo "Remove Shared Assigned To <br>";
$field=Vtiger_Field::getInstance('shared_assigned_to', $EmployeesModuleModel);
if ($field) {
    $field->delete();
}

echo 'Deletting Personnel Type Values</br>';
//delete the existing picklist values
$sqlquery = 'DELETE FROM vtiger_employee_type';
Vtiger_Utils::ExecuteQuery($sqlquery);
echo 'OK</br>';

$pickListName = 'employee_type';

if (!$EmployeesModuleModel) {
    echo 'Module Employees not present<br>';
} else {
    echo 'Adding new values</br>';
    $field = Vtiger_Field::getInstance($pickListName, $EmployeesModuleModel);
    if (!$field) {
        echo 'Field Personnel Type not found';
    } else {
        $field->setPicklistValues(array('Employee', 'Contractor'));
    }
}
echo '</br>';
echo 'OK</br>';


// Move assigned_user_id to Record Update Information block
$recordUpdateBlock = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $EmployeesModuleModel);
$assignedToFieldModel=Vtiger_Field::getInstance('assigned_user_id', $EmployeesModuleModel);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET block='".$recordUpdateBlock->id."' WHERE fieldid='".$assignedToFieldModel->id."';");


// Add new fields to Personnel Information
$PersonnelInformationBlock=Vtiger_Block::getInstance('LBL_EMPLOYEES_INFORMATION', $EmployeesModuleModel);

//Mailing Address 1
$field = Vtiger_Field::getInstance('employee_mailingaddress1', $EmployeesModuleModel);
if ($field) {
    echo "<br> The Mailing Address 1 field already exists in EmployeeRoles <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_MAILING_ADDRESS_1';
    $field->name = 'employee_mailingaddress1';
    $field->table = 'vtiger_employeescf';
    $field->column ='employee_mailingaddress1';
    $field->columntype = 'varchar(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~0';
    $field->quickcreate = 0;
    $field->summaryfield = 1;
    $field->sequence = 8;

    $PersonnelInformationBlock->addField($field);
}

//Mailing Address 2
$field = Vtiger_Field::getInstance('employee_mailingaddress2', $EmployeesModuleModel);
if ($field) {
    echo "<br> The Mailing Address 2 field already exists in EmployeeRoles <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_MAILING_ADDRESS_2';
    $field->name = 'employee_mailingaddress2';
    $field->table = 'vtiger_employeescf';
    $field->column ='employee_mailingaddress2';
    $field->columntype = 'varchar(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~0';
    $field->quickcreate = 0;
    $field->summaryfield = 1;
    $field->sequence = 10;
    $PersonnelInformationBlock->addField($field);
}

//Mailing City
$field = Vtiger_Field::getInstance('employee_mailingcity', $EmployeesModuleModel);
if ($field) {
    echo "<br> The Mailing City field already exists in EmployeeRoles <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_MAILING_CITY';
    $field->name = 'employee_mailingcity';
    $field->table = 'vtiger_employeescf';
    $field->column ='employee_mailingcity';
    $field->columntype = 'varchar(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~0';
    $field->quickcreate = 0;
    $field->summaryfield = 1;
    $field->sequence = 12;
    $PersonnelInformationBlock->addField($field);
}


//Mailing State
$field = Vtiger_Field::getInstance('employee_mailingstate', $EmployeesModuleModel);
if ($field) {
    echo "<br> The Mailing State field already exists in EmployeeRoles <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_MAILING_STATE';
    $field->name = 'employee_mailingstate';
    $field->table = 'vtiger_employeescf';
    $field->column ='employee_mailingstate';
    $field->columntype = 'varchar(50)';
    $field->uitype = 1;
    $field->typeofdata = 'V~0';
    $field->quickcreate = 0;
    $field->summaryfield = 1;
    $field->sequence = 14;
    $PersonnelInformationBlock->addField($field);
}


//Mailing Zip
$field = Vtiger_Field::getInstance('employee_mailingzip', $EmployeesModuleModel);
if ($field) {
    echo "<br> The Mailing Zip field already exists in EmployeeRoles <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_MAILING_ZIP';
    $field->name = 'employee_mailingzip';
    $field->table = 'vtiger_employeescf';
    $field->column ='employee_mailingzip';
    $field->columntype = 'varchar(10)';
    $field->uitype = 1;
    $field->typeofdata = 'V~0';
    $field->quickcreate = 0;
    $field->summaryfield = 1;
    $field->sequence = 16;
    $PersonnelInformationBlock->addField($field);
}


//Mailing Country
$field = Vtiger_Field::getInstance('employee_mailingcountry', $EmployeesModuleModel);
if ($field) {
    echo "<br> The Mailing Country field already exists in EmployeeRoles <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_MAILING_COUNTRY';
    $field->name = 'employee_mailingcountry';
    $field->table = 'vtiger_employeescf';
    $field->column ='employee_mailingcountry';
    $field->columntype = 'varchar(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~0';
    $field->quickcreate = 0;
    $field->summaryfield = 1;
    $field->sequence = 18;
    $PersonnelInformationBlock->addField($field);
}

// Remove fields from Personnel Information block
//date_out
$adb->pquery("UPDATE `vtiger_field` SET presence=1 WHERE fieldname IN ('date_out','time_out','date_in')");

// Update sequence of other fields.
$adb->pquery("UPDATE `vtiger_field` SET sequence= CASE
              WHEN fieldname='name' THEN 1
              WHEN fieldname='employee_lastname' THEN 2
              WHEN fieldname='employee_email' THEN 3
              WHEN fieldname='employee_status' THEN 4
              WHEN fieldname='employee_mphone' THEN 5
              WHEN fieldname='employee_hphone' THEN 6
              WHEN fieldname='address1' THEN 7
              WHEN fieldname='address2' THEN 9
              WHEN fieldname='city' THEN 11
              WHEN fieldname='state' THEN 13
              WHEN fieldname='zip' THEN 15
              WHEN fieldname='country' THEN 17 END
              WHERE fieldname IN ('name','employee_lastname','employee_email','employee_status','employee_mphone','employee_hphone','address1','city','state','zip','country') AND tabid='{$EmployeesModuleModel->getId()}' AND block='{$PersonnelInformationBlock->id}'");


// Detailed Information block
$DetailedInformation=Vtiger_Block::getInstance("LBL_EMPLOYEES_DETAILINFO", $EmployeesModuleModel);
// Move Birthday to Detailed Information
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET block='".$DetailedInformation->id."', sequence='7' WHERE fieldname='employee_bdate' AND tabid='{$EmployeesModuleModel->getId()}';");

//Primary Role
$field = Vtiger_Field::getInstance('employee_primaryrole', $EmployeesModuleModel);
if ($field) {
    echo "<br> The Primary Role field already exists in EmployeeRoles <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_PRIMARY_ROLE';
    $field->name = 'employee_primaryrole';
    $field->table = 'vtiger_employeescf';
    $field->column ='employee_primaryrole';
    $field->columntype = 'varchar(100)';
    $field->uitype = 10;
    $field->typeofdata = 'V~M';
    $field->quickcreate = 0;
    $field->summaryfield = 1;
    $field->sequence = 1;
    $DetailedInformation->addField($field);
    $field->setRelatedModules(array('EmployeeRoles'));
}

//Secondary Role
$field = Vtiger_Field::getInstance('employee_secondaryrole', $EmployeesModuleModel);
if ($field) {
    echo "<br> The Secondary Role field already exists in EmployeeRoles <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_SECONDARY_ROLE';
    $field->name = 'employee_secondaryrole';
    $field->table = 'vtiger_employeescf';
    $field->column ='employee_secondaryrole';
    $field->columntype = 'text';
    $field->uitype = 1989;
    $field->typeofdata = 'V~O';
    $field->quickcreate = 0;
    $field->summaryfield = 1;
    $field->sequence = 2;
    $DetailedInformation->addField($field);
    $field->setRelatedModules(array('EmployeeRoles'));
}

// Schedule Priority
$field = Vtiger_Field::getInstance('employee_schedulepriority', $EmployeesModuleModel);
if ($field) {
    echo "<br> The Schedule Priority field already exists in EmployeeRoles <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_SCHEDULE_PRIORITY';
    $field->name = 'employee_schedulepriority';
    $field->table = 'vtiger_employeescf';
    $field->column ='employee_schedulepriority';
    $field->columntype = 'INT(10)';
    $field->uitype = 7;
    $field->typeofdata = 'I~O';
    $field->quickcreate = 0;
    $field->summaryfield = 1;
    $field->sequence = 4;
    $DetailedInformation->addField($field);
}

// Commission Plan
$field = Vtiger_Field::getInstance('employee_commissionplan', $EmployeesModuleModel);
if ($field) {
    echo "<br> The Commission Plan field already exists in EmployeeRoles <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_COMMISSION_PLAN';
    $field->name = 'employee_commissionplan';
    $field->table = 'vtiger_employeescf';
    $field->column ='employee_commissionplan';
    $field->columntype = 'varchar(100)';
    $field->uitype = 10;
    $field->typeofdata = 'V~O';
    $field->quickcreate = 0;
    $field->summaryfield = 1;
    $field->sequence = 9;
    $DetailedInformation->addField($field);
    $field->setRelatedModules(array('CommissionPlans'));
}


// Vendor Number
$field = Vtiger_Field::getInstance('employee_vendornumber', $EmployeesModuleModel);
if ($field) {
    echo "<br> The Vendor Number field already exists in EmployeeRoles <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_VENDOR_NUMBER';
    $field->name = 'employee_vendornumber';
    $field->table = 'vtiger_employeescf';
    $field->column ='employee_vendornumber';
    $field->columntype = 'varchar(200)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';
    $field->quickcreate = 0;
    $field->summaryfield = 1;
    $field->sequence = 10;
    $DetailedInformation->addField($field);
}

// Available for Local Dispatch
$field = Vtiger_Field::getInstance('employee_available_localdispatch', $EmployeesModuleModel);
if ($field) {
    echo "<br> The Available for Local Dispatch field already exists in EmployeeRoles <br>";
    if ($field->uitype == 1) {
        //delete the existing picklist values
        $sqlquery = 'DELETE FROM vtiger_employee_available_localdispatch';
        Vtiger_Utils::ExecuteQuery($sqlquery);
        $field->setPicklistValues(array('Yes', 'No'));
        Vtiger_Utils::ExecuteQuery("update `vtiger_field` set `uitype`='16' where `fieldid`='{$field->id}' ");
    }
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AVAILABLE_LOCAL_DISPATCH';
    $field->name = 'employee_available_localdispatch';
    $field->table = 'vtiger_employeescf';
    $field->column ='employee_available_localdispatch';
    $field->columntype = 'varchar(10)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->quickcreate = 0;
    $field->summaryfield = 1;
    $field->sequence = 11;
    $DetailedInformation->addField($field);
    $field->setPicklistValues(array('Yes', 'No'));
}


// Available for Long Distance Dispatch
$field = Vtiger_Field::getInstance('employee_available_longdispatch', $EmployeesModuleModel);
if ($field) {
    echo "<br> The Available for Long Distance Dispatch field already exists in EmployeeRoles <br>";
    if ($field->uitype == 1) {
        //delete the existing picklist values
        $sqlquery = 'DELETE FROM vtiger_employee_available_longdispatch';
        Vtiger_Utils::ExecuteQuery($sqlquery);
        $field->setPicklistValues(array('Yes', 'No'));
        Vtiger_Utils::ExecuteQuery("update `vtiger_field` set `uitype`='16' where `fieldid`='{$field->id}' ");
    }
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AVAILABLE_LONG_DISPATCH';
    $field->name = 'employee_available_longdispatch';
    $field->table = 'vtiger_employeescf';
    $field->column ='employee_available_longdispatch';
    $field->columntype = 'varchar(10)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->quickcreate = 0;
    $field->summaryfield = 1;
    $field->sequence = 12;
    $DetailedInformation->addField($field);
    $field->setPicklistValues(array('Yes', 'No'));
}

// Update sequence of other fields.
$adb->pquery("UPDATE `vtiger_field` SET sequence= CASE
              WHEN fieldname='employee_no' THEN 3
              WHEN fieldname='employee_hdate' THEN 5
              WHEN fieldname='employee_tdate' THEN 6
              WHEN fieldname='employee_rdate' THEN 8 END
              WHERE fieldname IN ('employee_no','employee_hdate','employee_tdate','employee_rdate') AND tabid='{$EmployeesModuleModel->getId()}' AND block='{$DetailedInformation->id}'");
// Remove old Primary Role, Secondary Role fields
$adb->pquery("UPDATE `vtiger_field` SET presence=1 WHERE fieldname IN ('employee_prole','employee_srole')");



// Contractors Detailed Information Block
$ContractorsDetailedInformation=Vtiger_Block::getInstance("LBL_CONTRACTORS_DETAILINFO", $EmployeesModuleModel);
// Remove fields from Contractors Detailed Information
$adb->pquery("UPDATE `vtiger_field` SET presence=1 WHERE fieldname IN ('contractor_enum','contractor_prole','contractor_hdate','contractor_tdate','contractor_status','contractor_rdate','contractor_trucknumber','contractor_trailernumber')");



// Driver Information Block
$DriverInformation = Vtiger_Block::getInstance('LBL_DRIVER_INFORMATION', $EmployeesModuleModel);
// Remove fields from Driver Information
$adb->pquery("UPDATE `vtiger_field` SET presence=1 WHERE fieldname IN ('trailer','carb_compliant','fleet_type','notes','employees_committedstatus')");

// Update sequence of blocks
$arrBlocks=array('LBL_CUSTOM_INFORMATION','LBL_EMPLOYEES_INFORMATION','LBL_EMPLOYEES_DETAILINFO','LBL_DRIVER_INFORMATION','LBL_CONTRACTORS_DETAILINFO','LBL_EMPLOYEES_EMERGINFO','LBL_EMPLOYEES_LICENSEINFO','LBL_EMPLOYEES_SAFETYDETAILS','LBL_EMPLOYEES_AVAILABILITY','LBL_EMPLOYEES_RECORDUPDATE','LBL_EMPLOYEES_PHOTO','LBL_EMPLOYEES_PRODASSOCIATEOOS');
foreach ($arrBlocks as $seq => $blocklbl) {
    $adb->pquery("update `vtiger_blocks` set `sequence`=? where `blocklabel`=? AND tabid=?", array($seq, $blocklbl, $EmployeesModuleModel->id));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";