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



echo "<br>begin new CWT by Weight hotfix<br>";

// include_once('vtlib/Vtiger/Menu.php');
// include_once('vtlib/Vtiger/Module.php');
// include_once('modules/ModTracker/ModTracker.php');
// include_once('modules/ModComments/ModComments.php');
// include_once 'includes/main/WebUI.php';
// include_once 'include/Webservices/Create.php';
// include_once 'modules/Users/Users.php';

if (Vtiger_Utils::CheckTable('vtiger_rate_type')) {
    $db = PearDatabase::getInstance();
    
    //check to see if CWT by weight already exists
    $result = $db->pquery('SELECT * FROM `vtiger_rate_type` WHERE rate_type="CWT by Weight"', array());
    $row = $result->fetchRow();
    
    if (!$row[0]) {
        //get unique ID for new picklist entry then update sequence table
        $result = $db->pquery('SELECT id FROM `vtiger_rate_type_seq`', array());
        $row = $result->fetchRow();
        $uniqueId = $row[0];
        $uniqueId++;
        $sql = "UPDATE `vtiger_rate_type_seq` SET id = ?";
        $db->pquery($sql, array($uniqueId));
        
        //grab the highest sortorderid, then increment to get new sortorderid
        $result = $db->pquery('SELECT sortorderid FROM `vtiger_rate_type` ORDER BY sortorderid DESC', array());
        $row = $result->fetchRow();
        $sortId = $row[0];
        $sortId++;
        
        $sql = 'INSERT INTO `vtiger_rate_type` (rate_typeid, rate_type, sortorderid, presence) VALUES (?,?,?,?)';
        $result = $db->pquery($sql, array($uniqueId, 'CWT by Weight', $sortId, 1));
        echo "<br>CWT by weight added to tariff service picklist<br>";
    } else {
        echo "<br>CWT by weight already in tariff service picklist!<br>";
    }
} else {
    echo "<br>vtiger_rate_type not found! No action taken<br>";
}

if (!Vtiger_Utils::CheckTable('vtiger_tariffcwtbyweight')) {
    echo "<br>vtiger_tariffcwtbyweight doesn't exist, creating it now<br>";
    Vtiger_Utils::CreateTable('vtiger_tariffcwtbyweight',
                              '(
								serviceid INT(11),
							    from_weight INT(11),
							    to_weight INT(11),
								rate INT(11),
								line_item_id INT(11)
								)', true);
    echo "<br>vtiger_tariffcwtbyweight table created<br>";
} else {
    echo "<br>vtiger_tariffcwtbyrate already exists, no action taken<br>";
}

$tariffServicesModule = Vtiger_Module::getInstance('TariffServices');

$CWTBlock = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_CWTBYWEIGHT', $tariffServicesModule);

if (!$CWTBlock) {
    echo "<br> block doesn't exist. creating it now.<br>";
    $CWTBlock = new Vtiger_Block();
    $CWTBlock->label = 'LBL_TARIFFSERVICES_CWTBYWEIGHT';
    $tariffServicesModule->addBlock($CWTBlock);
    echo "<br>LBL_TARIFFSERVICES_CWTBYWEIGHT block creation complete.<br>";
} else {
    echo "<br>LBL_TARIFFSERVICES_CWTBYWEIGHT already exists.<br>";
}

if (!Vtiger_Utils::CheckTable('vtiger_quotes_cwtbyweight')) {
    echo "<br>vtiger_quotes_cwtbyweight doesn't exist, creating it now<br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_cwtbyweight',
                              '(
							    estimateid INT(11),
							    serviceid INT(11),
								weight INT(11),
								rate DECIMAL(19,2)
								)', true);
    echo "<br>vtiger_quotes_cwtbyweight table created<br>";
} else {
    echo "<br>vtiger_quotes_cwtbyweight already exists, no action taken<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";