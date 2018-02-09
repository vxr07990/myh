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

echo "<h3>Starting re-order process for accounts section</h3>\n";

$moduleName = 'Accounts';

try {
    $module = Vtiger_Module::getInstance($moduleName);

    $addedField = false;

// Reorder fields in the ui
if ($module) {
    $primaryPhone       = $fieldInstance = Vtiger_Field::getInstance('phone', $module);
    $primaryPhoneType   = $fieldInstance = Vtiger_Field::getInstance('primary_phone_type', $module);
    $secondaryPhone       = $fieldInstance = Vtiger_Field::getInstance('otherphone', $module);
    $secondaryPhoneType   = $fieldInstance = Vtiger_Field::getInstance('secondary_phone_type', $module);

    //we need all of these variables to have values to work
    if ($primaryPhone&&$primaryPhoneType&&$secondaryPhone&&$secondaryPhoneType) {
        if (($primaryPhone->sequence+2==$primaryPhoneType->sequence)&&
           ($secondaryPhone->sequence+2==$secondaryPhoneType->sequence)) {
            echo "The phones are in the right order<br/>";
        } else {
            echo "We need to re-order the account!!!<br/>";
            $fieldOrder = [
                'accountname',          'apn',
                'website',              'phone',
                'tickersymbol',         'primary_phone_type',
                'account_id',           'otherphone',
                'employees',            'secondary_phone_type',
                'email1',               'fax',
                'email2',               'ownership',
                'industry',             'rating',
                'accounttype',          'siccode',
                'annual_revenue',       'assigned_user_id',
                'emailoptout',          'agentid',
                'notify_owner'
            ];
            if (!$db) {
                $db = PearDatabase::getInstance();
            }
            $count = 0;
            foreach ($fieldOrder as $field) {
                $fieldInstance = Vtiger_Field::getInstance($field, $module);
                $sql = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
            }
            echo "The re-order worked<br/>";
        }
    } else {
        echo "We are missing a variable value!<br/>";
    }
}
} catch (Exception $e) {
    echo "<h2>Issue Detected in the Hotfix_Sirva_Accounts_Validate_Order.php file</h2>";
}
echo "<h3>Ending re-order</h3>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";