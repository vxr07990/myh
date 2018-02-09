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

$db = PearDatabase::getInstance();

$moduleName = 'Orders';
$module = Vtiger_Module_Model::getInstance($moduleName);

/* Order from ticket (*items* are guest blocks):
Order Details
Address
*Extra Stops*
Date Details
Order Weights
Billing Information
Valuation
*Participating Agents*
*Move Roles*
Long Distance Dispatch (if applicable)
Description
*/

$newBlockOrder = [
    'LBL_ORDERS_INFORMATION',
    'LBL_ORDER_ACCOUNT_ADDRESS',
    'LBL_ORDERS_ORIGINADDRESS',
    'LBL_ORDERS_DATES',
    'LBL_ORDERS_WEIGHTS',
    'LBL_ORDERS_INVOICE',
    'LBL_ORDERS_BLOCK_VALUATION',
    'LBL_LONGDISPATCH_INFO',
    'LBL_ORDERS_DESCRIPTION',
    'LBL_RECORD_UPDATE_INFORMATION',
    'LBL_ORDERS_PARTICIPANTS',
    'LBL_GSA_INFORMATION',
    'LBL_MILITARY_INFORMATION',
    'LBL_MILITARY_POST_MOVE_SURVEY',
    'LBL_CUSTOM_INFORMATION',
    'LBL_ORDERS_EXTRASTOPS'
];

//Update after_block value for MoveRoles

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_guestmodulerel` SET after_block = 'LBL_ORDERS_BLOCK_VALUATION' WHERE hostmodule = '$moduleName' AND guestmodule = 'MoveRoles'");

//Update block order
$sequence = 1;
$tabId = $module->getId();
foreach($newBlockOrder as $blockName){
    if(!Vtiger_Block_Model::getInstance($blockName, $module)){
        print "Could not find $blockName in $moduleName <br />\n";
        continue;
    }
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET sequence = '$sequence' WHERE tabid = $tabId AND blocklabel = '$blockName'" );
    $sequence++;
}

$blockName = 'LBL_ORDERS_INFORMATION';
$block = Vtiger_Block_Model::getInstance($blockName, $module);

if(!$block) {
    print "Unable to find $blockName in $moduleName. Skipping field re-order.<br />\n";
    return;
}

// Contact Name, Business Line, Billing Type, Authority, Assigned to and Owner.
$count = 1;
$newFieldOrder = [
    'orders_contacts',
    'business_line2',
    'billing_type',
    'authority',
    'assigned_user_id',
    'agentid',
    'orders_ponumber',
    'orders_bolnumber',
    'orders_vanlineregnum',
    'ordersstatus',
    'order_reason',
    'orders_opportunities',
    'orders_relatedorders',
    'projectid',
    'orders_account',
    'account_contract',
    'orders_elinehaul',
    'national_account_number',
    'orders_etotal',
    'orders_discount',
    'effective_tariff_custom_type',
    'orders_sit',
    'competitive',
];

foreach ($newFieldOrder as $val) {
    $field = Vtiger_Field::getInstance($val, $module);
    if ($field) {
        $params = [$block->id, $count, $field->id];
        $sql = 'UPDATE `vtiger_field` SET block = ?, sequence = ? WHERE fieldid = ?';
        $db->pquery($sql, $params);
        print 'Moved '.$val.' to the '.$blockName.' block <br />\n';
        $count++;
    } else {
        print $val.' Field doesn\'t exist. Skipping. <br />\n';
    }
}

AddMandatoryCORBAF($moduleName, 'authority');

function AddMandatoryCORBAF($moduleName, $fieldName)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $changingField = Vtiger_Field::getInstance($fieldName, $module);
        if ($changingField) {
            $typeOfData = $changingField->typeofdata;
            $isMatch = preg_match('/~O/', $typeOfData);
            if ($isMatch === false) {
                print "ERROR: couldn't preg_match?";
            } elseif ($isMatch) {
                $typeOfData = preg_replace('/~O/', '~M', $typeOfData);
                print "<br>$moduleName $fieldName needs converting to mandatory<br>\n";
                $stmt = "UPDATE `vtiger_field` SET `typeofdata` = ?"
                        //. " `quickcreate` = 1"
                        ." WHERE `fieldid` = ? LIMIT 1";
                print "$stmt\n";
                print "$typeOfData, " . $changingField->id  ."<br />\n";
                $db->pquery($stmt, [$typeOfData, $changingField->id]);
                print "<br>$moduleName $fieldName is converted to mandatory<br>\n";
            } else {
                print "<br>$moduleName $fieldName is already mandatory<br>\n";
            }
        } else {
            print "<br />failed to find: $fieldName in $moduleName<br />\n";
        }
    } else {
        print "<br />failed to load module $moduleName<br />\n";
    }
}
