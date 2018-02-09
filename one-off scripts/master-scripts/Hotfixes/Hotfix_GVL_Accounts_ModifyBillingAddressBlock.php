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


//OT 16094 - Changing Commodity field to take Business Line multiselect values.

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
include_once 'modules/Settings/Picklist/models/Module.php';
include_once 'modules/Settings/Picklist/models/Field.php';

/*
 * Block Id = 9
 */
echo '<h3>Starting Hotfix_GVL_Accounts_ModifyBillingAddressBlock</h3>';

$moduleName = 'Accounts';
$blockName = 'LBL_ACCOUNT_BILLING_ADDRESS';
$module = Vtiger_Module::getInstance($moduleName);
$fieldName = 'commodity';


$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    echo '<p>'.$blockName.' Already Exists</p>';
} else {
    $block = new Vtiger_Block();
    $block->label = $blockName;
    $block->sequence = '4';
    $module->addBlock($block);

    echo '<p>'.$blockName.' added</p>';
}

$block = Vtiger_Block::getInstance($blockName, $module);

if ($block) {
    if (!Vtiger_Utils::CheckTable('vtiger_accounts_billing_addresses')) {
        //The table doesn't exist let's make it
        echo '<p>Creating the vtiger_accounts_billing_addresses table</p>';
        $db = PearDatabase::getInstance();

        $sql = "CREATE TABLE `vtiger_accounts_billing_addresses` (
                `id` INT NOT NULL AUTO_INCREMENT ,
                 `account_id` INT NOT NULL , 
                 `commodity` TEXT NOT NULL ,
                 `address1` VARCHAR(100) NOT NULL ,
                 `address2` VARCHAR(100) NULL , 
                 `address_desc` VARCHAR(255) NOT NULL , 
                 `city` VARCHAR(60) NOT NULL , 
                 `state` VARCHAR(60) NOT NULL , 
                 `zip` VARCHAR(10) NOT NULL , 
                 `country` VARCHAR(60) NULL , 
                 `active` VARCHAR(3) NOT NULL , 
                 `company` VARCHAR(150) NOT NULL,
                 PRIMARY KEY (`id`), 
                 INDEX (`account_id`)
            );";
        $result = $db->pquery($sql, []);
        echo '<p>vtiger_accounts_billing_addresses table created</p>';
    } else {
        echo '<p>The vtiger_accounts_billing_addresses table already exists. Checking commodity field for business_line compatibility</p>';
        $stmt = 'EXPLAIN `vtiger_accounts_billing_addresses` `'.$fieldName.'`';
        $db = PearDatabase::getInstance();
        if ($res = $db->pquery($stmt)) {
            while ($value = $res->fetchRow()) {
                if ($value['Field'] == $fieldName) {
                    if (strtolower($value['Type']) != "text") {
                        echo "Updating $fieldName to be a text type.<br />\n";
                        $stmt = 'ALTER TABLE `vtiger_accounts_billing_addresses` MODIFY COLUMN `' . $fieldName . '` TEXT NOT NULL';
                        $db->pquery($stmt);
                        echo "$fieldName updated to text type.<br/>\n";
                    } else {
                        echo "$fieldName already text type. No changes. <br/>\n";
                    }
                }
                    //we're only affecting the $fieldName so if we find it just break
                    break;
            }
        } else {
            echo "No results on Explain <br/>\n";
        }
    }
}


echo '<h3>Ending Starting Hotfix_GVL_Accounts_ModifyBillingAddressBlock</h3>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";