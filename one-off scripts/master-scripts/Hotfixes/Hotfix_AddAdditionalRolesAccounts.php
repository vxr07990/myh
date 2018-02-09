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
include_once 'modules/Settings/Picklist/models/Module.php';
include_once 'modules/Settings/Picklist/models/Field.php';


echo '<h3>Starting Hotfix_AddAdditionalRolesAccounts</h3>';

$moduleName = 'Accounts';
$blockName = 'LBL_ACCOUNTS_ADDITIONAL_ROLES';
$module = Vtiger_Module::getInstance($moduleName);


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
    if (!Vtiger_Utils::CheckTable('vtiger_additional_roles')) {
        //The table doesn't exist let's make it
        echo '<p>Creating the vtiger_additional_roles table</p>';
        $db = PearDatabase::getInstance();

        $sql = "CREATE TABLE `vtiger_additional_roles` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `account_id` INT NOT NULL,
                `commodity` VARCHAR(60) NOT NULL,
                `user` VARCHAR(100) NOT NULL,
                `role` VARCHAR(100) NOT NULL,
                PRIMARY KEY (`id`),
                INDEX (`account_id`)
            );";
        $result = $db->query($sql);
        echo '<p>vtiger_additional_roles table created</p>';
    } else {
        echo '<p>The vtiger_additional_roles table already exists</p>';
    }

    // Account Name Field
    $field = Vtiger_Field::getInstance('accounts_role', $module);
    if ($field) {
        echo '<p> accounts_role Field already present</p>';
    } else {
        $picklistOptions = [
            'Coordinator',
            'Biller',
        ];

        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_ROLE';
        $field->name = 'accounts_role';
        $field->table = 'vtiger_account';
        $field->column = 'accounts_role';
        $field->columntype = 'VARCHAR(150)';
        $field->uitype = '16';
        $field->typeofdata = 'V~O';
        $field->setPicklistValues($picklistOptions);

        $block->addField($field);

        echo '<p>Added accounts_role field to accounts</p>';
    }
}


echo '<h3>Ending Hotfix_AddAdditionalRolesAccounts</h3>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";