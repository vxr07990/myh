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
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');

try {
    if (!$db) {
        $db = PearDatabase::getInstance();
    }
    $estModule = Vtiger_Module::getInstance('Estimates');
    if ($estModule) {
        $truckload = Vtiger_Field::getInstance('express_truckload', $estModule);
        $oldBlock = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $estModule);
        $newBlock = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $estModule);
        if ($truckload&&$oldBlock) {
            if ($oldBlock->id == $truckload->block->id) {
                $db->pquery("UPDATE vtiger_field SET block = ? WHERE fieldid = ?", [$newBlock->id, $truckload->id]);
                echo "The block was updated<br/>";
            } else {
                echo "The block was already updated<br/>";
            }

            $checkTruckload = Vtiger_Field::getInstance('express_truckload', $estModule);
            $effective_tariff = Vtiger_Field::getInstance('effective_tariff', $estModule);
            
            if ($checkTruckload&&$effective_tariff) {
            }
        } else {
            echo "Truckload or Old block does not exist<br/>";
        }
    } else {
        echo "Estimate module does not exists<br/>";
    }




    $quotesModule = Vtiger_Module::getInstance('Quotes');
    if ($quotesModule) {
        $truckload = Vtiger_Field::getInstance('express_truckload', $quotesModule);
        $oldBlock = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $quotesModule);
        $newBlock = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $estModule);
        if ($truckload&&$oldBlock) {
            if ($oldBlock->id == $truckload->block->id) {
                $db->pquery("UPDATE vtiger_field SET block = ? WHERE fieldid = ?", [$newBlock->id, $truckload->id]);
                echo "The block was updated<br/>";
            } else {
                echo "The block was already updated<br/>";
            }


            $checkTruckload = Vtiger_Field::getInstance('express_truckload', $estModule);
            $effective_tariff = Vtiger_Field::getInstance('effective_tariff', $estModule);
        } else {
            echo "Truckload does not exist<br/>";
        }
    } else {
        echo "Quotes module does not exists<br/>";
    }
} catch (Exception $e) {
    echo "There was an error while trying to move the truckload field<br/>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";