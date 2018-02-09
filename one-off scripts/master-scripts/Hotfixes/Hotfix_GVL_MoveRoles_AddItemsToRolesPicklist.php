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


// 3258 Add additional roles into the role dropdown in Move Roles block


$moduleName = 'MoveRoles';
$blockName = 'LBL_MOVEROLES_INFORMATION';
$module = Vtiger_Module::getInstance($moduleName);
$addedField = false;

$rolesPickList = [
    'Salesperson',      'Surveyor',
    'Customer Service Coordinator',     'O/A Coordinator',
    'D/A Coordinator',      'Packing',
    'Contractor',       'Claims Rep',
    'Billing Clerk',        'Driver',
    'Customer Service Assistance',      'Admin Support',
    'Split Booking',        'Collecting',
    'Destination',      'Unpacking',
    'Extra Delivery',       'Containerized - DEST',
    'Intermodal-DEST',      'ASO',
    'ASO - 2nd',        'ASO - 3rd',
    'Coordinating',     'Hauling',
    'Split Hauling',        '2nd Split Hauler',
    '3rd Split Hauler',     'Invoicing Billing',
    'Origin',       'Containerized - ORIG',
    'APU',      'Extra Pickup',
    'Radial Dispatch Agent',        'Survey Agent',
    'Warehousing',      'Carrier',
    'Installer',        'Accounting',
    'APU Driver',       'Commercial Svc Coordinator',
    'Helper',       'Authorized Move Coordinator',
    'Admin Support',        'ASO Driver',
    'Shuttle',      'Estimator',
    'GMII Forwarder',       'Storage Pickup Driver',
];



echo "<br>Starting UpdatingMoveRolesRolesPicklist<br>\n";

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    $field0 = Vtiger_Field::getInstance('moveroles_role', $module);
    if ($field0) {
        echo '<p>moveroles_role field exists</p>';
        updatePicklistValuesAITRP($field0, $rolesPickList);
    } else {
        $db = PearDatabase::getInstance();
        $sql = "TRUNCATE TABLE `vtiger_moveroles_role`";
        $db->pquery($sql, array());
        $field0 = new Vtiger_Field();
        $field0->label = 'LBL_MOVEROLES_ROLE';
        $field0->name = 'moveroles_role';
        $field0->table = 'vtiger_moveroles';
        $field0->column = 'moveroles_role';
        $field0->columntype = 'VARCHAR(100)';
        $field0->uitype = '16';
        $field0->typeofdata = 'V~O';
        $block->addField($field0);
        $field0->setPicklistValues($rolesPickList);
        echo '<p>Added Moveroles Role</p>';

        $module->setEntityIdentifier($field0);
    }
} else {
    echo "<br>Fields not added. $blockName not found.<br/>";
}


function updatePicklistValuesAITRP($field, $pickList)
{
    $fieldName = $field->name;
    $tableName = 'vtiger_'.$fieldName;
    //    $keyField = $fieldName.'id';
    $db = PearDatabase::getInstance();
    $sql = "TRUNCATE TABLE `$tableName`";
    $db->pquery($sql, array());
    $field->setPicklistValues($pickList);
    //    $id = 0;
    //    $presenceValue = 1;
    //    foreach ($picklist as $index => $value) {
    //        $insertSql = 'INSERT INTO `'.$tableName.'` SET
    //                `presence` = ?,
    //                `'.$keyField.'` = ?,
    //                `'.$fieldName.'` = ?,
    //                `sortorderid` = ?';
    //        $db->pquery($insertSql, array($presenceValue, $id, $value, $id));
    //        $id++;
    //        }
    echo "<p>Updated $fieldName picklist.</p>";
}



echo "<br>Finished Moveroles Picklist update<br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";