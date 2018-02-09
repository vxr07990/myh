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



// $Vtiger_Utils_Log = true;

// include_once('vtlib/Vtiger/Menu.php');
// include_once('vtlib/Vtiger/Module.php');
// include_once('modules/ModTracker/ModTracker.php');
// include_once('modules/ModComments/ModComments.php');
// include_once 'includes/main/WebUI.php';
// include_once 'include/Webservices/Create.php';
// include_once 'modules/Users/Users.php';


$module = Vtiger_Module::getInstance('Tariffs');

$block = Vtiger_Block::getInstance('LBL_TARIFFS_INFORMATION', $module);

if($block) {
    $tariffStatusField = Vtiger_Field::getInstance('status', $module);
    if($tariffStatusField) {
        echo "<br> Field 'status' is already present <br>";
    } else {
        $tariffStatusField = Vtiger_Field::getInstance('status', $module);
        if ($tariffStatusField) {
            echo "The status field already exists<br>\n";
        } else {
            $tariffStatusField             = new Vtiger_Field();
            $tariffStatusField->label      = 'LBL_TARIFFS_STATUS';
            $tariffStatusField->name       = 'tariff_status';
            $tariffStatusField->table      = 'vtiger_tariffs';
            $tariffStatusField->column     = 'tariff_status';
            $tariffStatusField->columntype = 'VARCHAR(255)';
            $tariffStatusField->uitype     = 16;
            $tariffStatusField->typeofdata = 'V~M';
            $block->addField($tariffStatusField);
            $tariffStatusField->setPicklistValues(['Active', 'Inactive']);

            global $adb;
            $adb->query("UPDATE `vtiger_tariffs` SET `tariff_status`='Active'");
            $result = $adb->query("SELECT cvid FROM `vtiger_customview` WHERE viewname='All' AND entitytype='Tariffs'");
            if($result && $adb->num_rows($result)) {
                $cvid = $result->fields['cvid'];
                $adb->pquery("INSERT INTO `vtiger_cvadvfilter` (`cvid`, `columnindex`, `columnname`, `comparator`, `value`, `groupid`, `column_condition`) VALUES (?,?,?,?,?,?,?)", [$cvid, 0, 'vtiger_tariffs:tariff_status:tariff_status:Tariffs_LBL_TARIFFS_STATUS:V', 'n', 'Inactive', 1, '']);
                $adb->pquery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES (?,?,?,?)", [1, $cvid, 'and', '0']);
            }
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
