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

/*
 * Block Id = 9
 */

$moduleName = 'Accounts';
$blockName = 'LBL_ACCOUNT_INFORMATION';
$module = Vtiger_Module::getInstance($moduleName);
$db = PearDatabase::getInstance();

echo '<h3>Starting AddReorderAccountInfo Accounts</h3>';

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {

    // Account Name Field
    $field = Vtiger_Field::getInstance('accountname', $module);
    if ($field) {
        echo '<p>accountname Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_ACCOUNTNAME';
        $field->name = 'accountname';
        $field->table = 'vtiger_account';
        $field->column = 'accountname';
        $field->columntype = 'VARCHAR(150)';
        $field->uitype = '1';
        $field->typeofdata = 'V~M';
        $block->addField($field);

        echo '<p>Added accountname field to accounts</p>';
    }

    // national_account_number  Field
    $field = Vtiger_Field::getInstance('national_account_number', $module);
    if ($field) {
        echo '<p>national_account_number Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_NATIONAL_ACCOUNT_NUMBER';
        $field->name = 'national_account_number';
        $field->table = 'vtiger_account';
        $field->column = 'national_account_number';
        $field->columntype = 'VARCHAR(150)';
        $field->uitype = '1';
        $field->typeofdata = 'N~O';
        $block->addField($field);

        echo '<p>Added national_account_number field to accounts</p>';
    }




    // Account Name Field
    $field = Vtiger_Field::getInstance('account_status', $module);
    if ($field) {
        echo '<p> account_status Field already present</p>';
    } else {
        $picklistOptions = [
            'New',
            'Approved',
            'Inactive',
        ];

        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_STATUS';
        $field->name = 'account_status';
        $field->table = 'vtiger_account';
        $field->column = 'account_status';
        $field->columntype = 'VARCHAR(150)';
        $field->uitype = '16';
        $field->typeofdata = 'V~O';
        $field->setPicklistValues($picklistOptions);

        $block->addField($field);

        echo '<p>Added account_status field to accounts</p>';
    }

    // Address 1 Field
    $field = Vtiger_Field::getInstance('address1', $module);
    if ($field) {
        echo '<p>address1 field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_ADDRESS1';
        $field->name = 'address1';
        $field->table = 'vtiger_account';
        $field->column = 'address1';
        $field->columntype = 'VARCHAR(150)';
        $field->uitype = '1';
        $field->typeofdata = 'V~O';

        $block->addField($field);

        echo '<p>Added address1 field to accounts</p>';
    }

    // Address 2 Field
    $field = Vtiger_Field::getInstance('address2', $module);
    if ($field) {
        echo '<p>address2 field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_ADDRESS2';
        $field->name = 'address2';
        $field->table = 'vtiger_account';
        $field->column = 'address2';
        $field->columntype = 'VARCHAR(150)';
        $field->uitype = '1';
        $field->typeofdata = 'V~O';

        $block->addField($field);

        echo '<p>Added address2 field to accounts</p>';
    }

    // City Field
    $field = Vtiger_Field::getInstance('city', $module);
    if ($field) {
        echo '<p>city field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_CITY';
        $field->name = 'city';
        $field->table = 'vtiger_account';
        $field->column = 'city';
        $field->columntype = 'VARCHAR(60)';
        $field->uitype = '1';
        $field->typeofdata = 'V~O';

        $block->addField($field);

        echo '<p>Added city field to accounts</p>';
    }

    // state Field
    $field = Vtiger_Field::getInstance('state', $module);
    if ($field) {
        echo '<p>state field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_STATE';
        $field->name = 'state';
        $field->table = 'vtiger_account';
        $field->column = 'state';
        $field->columntype = 'VARCHAR(60)';
        $field->uitype = '1';
        $field->typeofdata = 'V~O';

        $block->addField($field);

        echo '<p>Added state field to accounts</p>';
    }

    // Zip Field
    $field = Vtiger_Field::getInstance('zip', $module);
    if ($field) {
        echo '<p>zip field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_ZIP';
        $field->name = 'zip';
        $field->table = 'vtiger_account';
        $field->column = 'zip';
        $field->columntype = 'VARCHAR(10)';
        $field->uitype = '1';
        $field->typeofdata = 'V~O';

        $block->addField($field);

        echo '<p>Added zip field to accounts</p>';
    }

    // country Field
    $field = Vtiger_Field::getInstance('country', $module);
    if ($field) {
        echo '<p>country field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_COUNTRY';
        $field->name = 'country';
        $field->table = 'vtiger_account';
        $field->column = 'country';
        $field->columntype = 'VARCHAR(60)';
        $field->uitype = '1';
        $field->typeofdata = 'V~O';

        $block->addField($field);

        echo '<p>Added country field to accounts</p>';
    }

    // phone Field
    $field = Vtiger_Field::getInstance('phone', $module);
    if ($field) {
        echo '<p>phone field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_PHONE';
        $field->name = 'phone';
        $field->table = 'vtiger_account';
        $field->column = 'phone';
        $field->columntype = 'VARCHAR(20)';
        $field->uitype = '1';
        $field->typeofdata = 'V~O';

        $block->addField($field);

        echo '<p>Added phone field to accounts</p>';
    }

    // other phone Field
    $field = Vtiger_Field::getInstance('otherphone', $module);
    if ($field) {
        echo '<p>otherphone field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_OTHERPHONE';
        $field->name = 'otherphone';
        $field->table = 'vtiger_account';
        $field->column = 'otherphone';
        $field->columntype = 'VARCHAR(20)';
        $field->uitype = '1';
        $field->typeofdata = 'V~O';

        $block->addField($field);

        echo '<p>Added otherphone field to accounts</p>';
    }

    // fax Field
    $field = Vtiger_Field::getInstance('fax', $module);
    if ($field) {
        echo '<p>fax field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_FAX';
        $field->name = 'fax';
        $field->table = 'vtiger_account';
        $field->column = 'fax';
        $field->columntype = 'VARCHAR(20)';
        $field->uitype = '1';
        $field->typeofdata = 'V~O';

        $block->addField($field);

        echo '<p>Added fax field to accounts</p>';
    }

    // email Field
    $field = Vtiger_Field::getInstance('email1', $module);
    if ($field) {
        echo '<p>email1 field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_EMAIL';
        $field->name = 'email1';
        $field->table = 'vtiger_account';
        $field->column = 'email1';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '1';
        $field->typeofdata = 'E~O';

        $block->addField($field);

        echo '<p>Added email1 field to accounts</p>';
    }

    // email2 Field
    $field = Vtiger_Field::getInstance('email2', $module);
    if ($field) {
        echo '<p>email2 field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_PHONE';
        $field->name = 'email2';
        $field->table = 'vtiger_account';
        $field->column = 'email2';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '1';
        $field->typeofdata = 'E~O';

        $block->addField($field);

        echo '<p>Added email2 field to accounts</p>';
    }

    // member_of Field
    $field = Vtiger_Field::getInstance('member_of', $module);
    if ($field) {
        echo '<p>member_of field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_MEMBEROF';
        $field->name = 'member_of';
        $field->table = 'vtiger_account';
        $field->column = 'member_of';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '1';
        $field->typeofdata = 'V~O';

        $block->addField($field);

        echo '<p>Added member_of field to accounts</p>';
    }

    // customer_number  Field
    $field = Vtiger_Field::getInstance('customer_number', $module);
    if ($field) {
        echo '<p>customer_number field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ACCOUNTS_CUSTOMER_NUMBER';
        $field->name = 'customer_number';
        $field->table = 'vtiger_account';
        $field->column = 'customer_number';
        $field->columntype = 'INT';
        $field->uitype = '1';
        $field->typeofdata = 'I~O';

        $block->addField($field);

        echo '<p>Added customer_number field to accounts</p>';
    }

    // Reorder Fields
    $orderOfFields = ['accountname', 'account_status', 'national_account_number', 'customer_number', 'address1', 'address2', 'city', 'state', 'zip', 'country', 'phone', 'otherphone', 'fax', 'email1', 'email2', 'emailoptout', 'business_line', 'billing_type', 'notify_owner', 'member_of', 'agentid', 'assigned_user_id'];

    $count = 0;
    foreach ($orderOfFields as $val) {
        $field = Vtiger_Field::getInstance($val, $module);
        if ($field) {
            $count++;
            $params = [$count, $field->id];
            $sql = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
            $db->pquery($sql, $params);
            echo '<p>UPDATED '.$val.'to the sequence</p>';
        } else {
            echo '<p>'.$val.' Field don\'t exists</p>';
        }
    }

    $removeFields = ['employees', 'ownership', 'industry', 'rating', 'accounttype', 'siccode', 'annual_revenue', 'invoice_format', 'invoice_pkg_format', 'duns_number', 'parentid'];
    foreach ($removeFields as $val) {
        $field = Vtiger_Field::getInstance($val, $module);
        if ($field) {
            $params = [1, $field->id];
            $sql = 'UPDATE `vtiger_field` SET presence = ? WHERE fieldid = ?';
            $db->pquery($sql, $params);
            echo '<p>Moved '.$val.'to the LBL_ACCOUNT_INFORMATION block</p>';
        } else {
            echo '<p>'.$val.' Field don\'t exists</p>';
        }
    }

    // Not sure why this field was not set above but whatever
    $sql = 'UPDATE `vtiger_field` SET presence = ? WHERE columnname = ? AND tablename = ? LIMIT 1';
    $params = ['1', 'parentid', 'vtiger_account'];
    $db->pquery($sql, $params);
} else {
    echo '<p>LBL_ACCOUNT_INFORMATION Block not found</p>';
}

echo '<h3>Ending AddReorderAccountInfo Accounts</h3>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";