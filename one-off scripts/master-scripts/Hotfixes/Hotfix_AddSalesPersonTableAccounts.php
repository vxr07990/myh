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


//*/
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
//*/
echo "<br><h1>Starting on adding vtiger_account_salespersons table</h1><br>\n";

$moduleName = 'Accounts';
$blockName = 'LBL_ACCOUNT_SALESPERSONS';
$module = Vtiger_Module::getInstance($moduleName);

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    echo "<br> The $blockName block already exists";
} else {
    $block = new Vtiger_Block();
    $block->label = $blockName;
    $module->addBlock($block);
    echo '<p>Add '.$blockName.' block.</p>';
}

if (Vtiger_Utils::CheckTable('vtiger_account_salespersons')) {
    echo "<br>vtiger_account_salespersons already exists! No action taken<br>";
} else {
    echo "<br>vtiger_account_salespersons doesn't exist! Creating it now.<br>";
    Vtiger_Utils::CreateTable('vtiger_account_salespersons', '(
                  id int(11) NOT NULL AUTO_INCREMENT,
                  salesperson_id int(11) NOT NULL,
                  booking_office_id int(11) NOT NULL,
                  business_line int(11) NOT NULL,
                  sales_credit double NOT NULL,
                  sales_comm int(11) NOT NULL,
                  effective_date_from date NOT NULL,
                  effective_date_to date NOT NULL,
                  record_id int(11) NOT NULL,
                  PRIMARY KEY (id)
        )', false);

    echo "<br>vtiger_account_salespersons table created successfully<br>";
}


echo "<br><h1>Done adding vtiger_account_salespersons table</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";