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



$accounts = Vtiger_Module::getInstance('Accounts'); // The module1 your blocks and fields will be in.

if ($accounts) {
    $block = Vtiger_Block::getInstance('LBL_ACCOUNT_DETAILS', $accounts);
    if ($block) {
        $field = Vtiger_Field::getInstance('gvl_account_type', $accounts);
        if ($field) {
            $db = PearDatabase::getInstance();
            echo "The gvl_account_type field already exists<br>\n";
            //check and see if it's the dumb one...
            if ($field->table == 'vtiger_accounts') {
                echo "Correcting Vtiger_field for this field: gvl_account_type<br />\n";
                $stmt = 'UPDATE `vtiger_field` set `tablename`=? WHERE `fieldid`=? LIMIT 1';
                $db->pquery($stmt, ['vtiger_account', $field->id]);
            }
            $stmt = 'EXPLAIN `vtiger_account` `gvl_account_type`';
            $res = $db->pquery($stmt);
            if ($res && $row = $res->fetchRow()) {
                echo "The field exists in the table.<br />\n";
            } else {
                echo "adding field to table?<br />\n";
                $stmt = 'ALTER TABLE `vtiger_account` ADD COLUMN `gvl_account_type` VARCHAR(255) DEFAULT NULL';
                $db->pquery($stmt);
            }
        } else {
            $field             = new Vtiger_Field();
            $field->label      = 'LBL_ACCOUNTS_GVL_ACCOUNT_TYPE';
            $field->name       = 'gvl_account_type';
            $field->table      = 'vtiger_account';
            $field->column     = 'gvl_account_type';
            $field->columntype = 'VARCHAR(255)';
            $field->uitype     = 16;
            $field->typeofdata = 'V~O';
            $block->addField($field);

            $field->setPicklistValues(['COD', 'Account', 'Client']);
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";