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

$db = PearDatabase::getInstance();

//create table if it doesn't exist
echo "<br>begin hotfix, Checking existence of guestmodulerel table";
if (!Vtiger_Utils::CheckTable('vtiger_guestmodulerel')) {
    echo "<br>it doesn't exist, building now...";
    $db->query('CREATE TABLE `vtiger_guestmodulerel` 
        (
            `guestmodulerelid` INT AUTO_INCREMENT PRIMARY KEY, 
            `hostmodule` VARCHAR(100) NOT NULL, 
            `guestmodule` VARCHAR(100) NOT NULL, 
            `blockid` INT NOT NULL, 
            `active` TINYINT NOT NULL
        )'
    );
    echo "done!";
} else {
    echo "<br> it exists, no action taken";
}

echo "<br> end hotfix";
//$ordersInstance = Vtiger_Module::getInstance('Orders');
//$ordersInstance->setGuestBlocks('MoveRoles', ['LBL_MOVEROLES_INFORMATION']);
;


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";