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



include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

if (Vtiger_Utils::CheckTable('vtiger_rate_type')&&Vtiger_Utils::CheckTable('vtiger_rate_type_seq')) {
    $db = PearDatabase::getInstance();

    $sql = "SELECT * FROM `vtiger_rate_type` WHERE `rate_type` = 'Per Cu Ft'";
    $result = $db->pquery($sql, array());
    $row = $result->fetchRow();
    if (!is_array($row)) {
        //get unique ID for new picklist entry then update sequence table
        $result = $db->pquery('SELECT id FROM `vtiger_rate_type_seq`', array());
        $row = $result->fetchRow();
        $uniqueId = $row[0];
        $uniqueId++;
        $sql = "UPDATE `vtiger_rate_type_seq` SET id = ?";
        $db->pquery($sql, array($uniqueId));




        //grab the highest sortorderid, then increment to get new sortorderid
        $result = $db->pquery('SELECT sortorderid FROM `vtiger_rate_type` WHERE `rate_type` = "Per Cu Ft/Per Day"', array());
        $row = $result->fetchRow();
        $sortId = $row[0];
        if (!is_array($row)) {
            echo "<br/>We are missing the Per Cu Ft/Per Day Field</br>";
        } else {
            $sql    = 'INSERT INTO `vtiger_rate_type` (rate_typeid, rate_type, sortorderid, presence) VALUES (?,?,?,?)';
            $result = $db->pquery($sql, [$uniqueId, 'Per Cu Ft', $sortId, 1]);
            //update the sort so that all of the rows belong are in correct order
            $sql    = 'UPDATE `vtiger_rate_type` SET sortorderid = sortorderid+1 WHERE sortorderid >= ? AND rate_type != "Per Cu Ft"';
            $result = $db->pquery($sql, [$sortId]);
            echo "<br>completed adding Per Cu / Ft to rate_type picklist<br>";
            $tariffServicesModule = Vtiger_Module::getInstance('TariffServices');
            $block = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_CUFT', $tariffServicesModule);
            if (!$block) {
                echo "<br> block doesn't exist. creating it now.<br>";
                $newBlock        = new Vtiger_Block();
                $newBlock->label = 'LBL_TARIFFSERVICES_CUFT';
                $tariffServicesModule->addBlock($newBlock);
                echo "<br>LBL_TARIFFSERVICES_CUFT block creation complete.<br>";
            } else {
                echo "<br>LBL_TARIFFSERVICES_CUFT already exists.<br>";
            }
            $block = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_CUFT', $tariffServicesModule);
            if ($block) {
                $field1 = Vtiger_Field::getInstance('cuft_rate', $tariffServicesModule);
                if ($field1) {
                    echo '<br>tariff_orders_type field exists.<br>';
                } else {
                    echo "<br>Creating orders_type field:<br>";
                    $field1             = new Vtiger_Field();
                    $field1->label      = 'LBL_TARIFFSERVICES_RATE';
                    $field1->name       = 'cuft_rate';
                    $field1->table      = 'vtiger_tariffservices';
                    $field1->column     = 'cuft_rate';
                    $field1->columntype = 'VARCHAR(100)';
                    $field1->uitype     = 71;
                    $field1->typeofdata = 'V~O';
                    $block->addField($field1);
                    echo "<br>orders_type field created!<br>";
                }
            }
        }
    } else {
        echo "<br>Per Cu / Ft already in the rate_type picklist<br>";
    }
} else {
    echo "<br>vtiger_rate_type not found! No action taken<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";