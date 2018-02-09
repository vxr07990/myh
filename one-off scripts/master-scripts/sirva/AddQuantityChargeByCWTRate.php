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

echo "Adding new block and field to vtiger_tariffservices...<br/>\n";
// Get that db though.
$adb = PearDatabase::getInstance();
$moduleInstance = Vtiger_Module::getInstance('TariffServices');

$blockInstance = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_CWTPERQTY', $moduleInstance);
if(!$blockInstance) {
    echo "Adding LBL_TARIFFSERVICES_CWTPERQTY block...<br/>\n";
    // Add CWT/Quantity, told to be a core field/item, so not conditionalizing.
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_TARIFFSERVICES_CWTPERQTY';
    $moduleInstance->addBlock($blockInstance);
}else{
    echo "LBL_TARIFFSERVICES_CWTPERQTY already exists, skipping...<br/>\n";
}

$field = Vtiger_Field::getInstance('cwtperqty_rate', $moduleInstance);
if(!$field) {
    echo "Adding LBL_TARIFFSERVICES_RATE field...<br/>\n";
    $field = new Vtiger_Field();
    $field->label = 'LBL_TARIFFSERVICES_RATE';
    $field->name = 'cwtperqty_rate';
    $field->table = 'vtiger_tariffservices';
    $field->column = 'cwtperqty_rate';
    $field->columntype = 'DECIMAL(10,2)';
    $field->uitype = 71;
    $field->typeofdata = 'N~O';
    $blockInstance->addField($field);
}else{
    echo "LBL_TARIFFSERVICES_RATE already exists, skipping...<br/>\n";
}
// Done adding CWT/Quantity

$moduleInstance->save();

// Adding rate type entry because it needs to be there
$field_name = 'CWT Per Quantity';
$sql = 'SELECT rate_typeid FROM vtiger_rate_type WHERE rate_type=?';
$result = $adb->pquery($sql, [$field_name]);
if($adb->num_rows($result) == 0) {
    echo "Adding new entry for CWT Per Quantity in vtiger_rate_type...<br/>\n";
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
    echo "Entry for CWT Per Quantity already exists in vtiger_rate_type...<br/>\n";
}

// Add estimates table
echo "Creating vtiger_quotes_cwtperqty if it does not exist...<br/>\n";
$sql = 'CREATE TABLE IF NOT EXISTS `vtiger_quotes_cwtperqty` ( `estimateid` INT(11) NOT NULL , `serviceid` INT(11) NOT NULL , `quantity` INT(12) NOT NULL , `rate` DECIMAL(10,2) NOT NULL );';
$result = $db->query($sql);



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";