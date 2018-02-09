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
// Get that db though.
$adb = PearDatabase::getInstance();
$moduleInstance = Vtiger_Module::getInstance('TariffServices');

$blockInstance = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_FLATRATEBYWEIGHT', $moduleInstance);
if(!$blockInstance) {
    echo "Adding LBL_TARIFFSERVICES_FLATRATEBYWEIGHT block...<br/>\n";
    // Add CWT/Quantity, told to be a core field/item, so not conditionalizing.
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_TARIFFSERVICES_FLATRATEBYWEIGHT';
    $moduleInstance->addBlock($blockInstance);
}else{
    echo "LBL_TARIFFSERVICES_FLATRATEBYWEIGHT already exists, skipping...<br/>\n";
}

// Add tariffservices table
echo "Creating vtiger_tariffflatratebyweight if it does not exist...<br/>\n";
$sql = 'CREATE TABLE IF NOT EXISTS `vtiger_tariffflatratebyweight` ( `serviceid` INT(11) NOT NULL , `from_weight` INT(12) NOT NULL , `to_weight` INT(12) NOT NULL , `rate` DECIMAL(10,2) NOT NULL  , `cwt_rate` DECIMAL(10,2) NOT NULL, `line_item_id` INT(12) NOT NULL );';
$result = $db->query($sql);

if (!Vtiger_Utils::CheckTable('vtiger_tariffflatratebyweight_seq')) {
    echo "<br>vtiger_tariffflatratebyweight_seq doesn't exist, creating it now<br>";
    Vtiger_Utils::CreateTable('vtiger_tariffflatratebyweight_seq',
                              '(
								id INT(11)
								)', true);
    Vtiger_Utils::ExecuteQuery('INSERT INTO `vtiger_tariffflatratebyweight_seq` VALUES (0)');
    echo "<br>vtiger_tariffcwtbyweight_seq table created<br>";
} else {
    echo "<br><h1 style='color:orange;'>WARNING: vtiger_tariffflatratebyweight_seq already exists, no action taken</h1><br>";
}

// Adding rate type entry because it needs to be there
$field_name = 'Flat Rate By Weight';
$sql = 'SELECT rate_typeid FROM vtiger_rate_type WHERE rate_type=?';
$result = $adb->pquery($sql, [$field_name]);
if($adb->num_rows($result) == 0) {
    echo "Adding new entry for Flat Rate By Weight in vtiger_rate_type...<br/>\n";
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
    $result = $db->pquery($sql, array($uniqueId, $field_name, $sortId, 1));
}else{
    echo "Entry for Flat Rate By Weight already exists in vtiger_rate_type...<br/>\n";
}

// Add estimates table
echo "Creating vtiger_quotes_flatratebyweight if it does not exist...<br/>\n";
$sql = 'CREATE TABLE IF NOT EXISTS `vtiger_quotes_flatratebyweight` ( `estimateid` INT(11) NOT NULL , `serviceid` INT(11) NOT NULL , `weight` INT(12) NOT NULL , `weight_cap` INT(12) NOT NULL , `rate` DECIMAL(10,2) NOT NULL  , `cwt_rate` DECIMAL(10,2) NOT NULL, `line_item_id` INT(12) NOT NULL );';
$result = $db->query($sql);



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
