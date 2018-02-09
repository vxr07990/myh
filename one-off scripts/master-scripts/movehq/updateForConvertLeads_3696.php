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

$oppModule = Vtiger_Module::getInstance('Opportunities');
if (!$oppModule) {
    print "Failed to find the Opportunities module\n";
    return;
}
$opportunitiesTabid = $oppModule->getId();

$leadsModule = Vtiger_Module::getInstance('Leads');
if (!$leadsModule) {
    print "Failed to find the Leads module\n";
    return;
}
$leadsTabid = $leadsModule->getId();

$fieldName = 'business_line2';
$correctedTypeOfData = 'V~M';

if (isset($opportunitiesTabid) && isset($leadsTabid)) {
    $db = PearDatabase::getInstance();
    $leadsField = Vtiger_Field::getInstance($fieldName, $leadsModule);
    if (!$leadsField) {
        print "Failed to find $fieldName for Leads\n";
        return;
    }
    $leadsFieldid = $leadsField->id;

    $opportunitiesField = Vtiger_Field::getInstance($fieldName, $oppModule);
    if (!$opportunitiesField) {
        print "Failed to find $fieldName for Opportunities\n";
        return;
    }
    $opportunitiesFieldid = $opportunitiesField->id;

    //Update the typeofdata.
    print "Checking typeof data for fields\n";
    if ($leadsField->typeofdata != $correctedTypeOfData) {
        $sql = 'UPDATE `vtiger_field` set `typeofdata` = ? WHERE `fieldid` = ? LIMIT 1';
        $db->pquery($sql, [$correctedTypeOfData, $leadsField->id]);
    }
    if ($opportunitiesField->typeofdata != $correctedTypeOfData) {
        $sql = 'UPDATE `vtiger_field` set `typeofdata` = ? WHERE `fieldid` = ? LIMIT 1';
        $db->pquery($sql, [$correctedTypeOfData, $opportunitiesField->id]);
    }

    $stmt = 'SELECT * FROM `vtiger_convertleadmapping` WHERE `leadfid` = ? AND `potentialfid`=? LIMIT 1';
    $checkRes = $db->pquery($stmt, [$leadsFieldid, $opportunitiesFieldid]);
    if ($checkRes && method_exists($checkRes, 'fetchRow')) {
        $row = $checkRes->fetchRow();
        if ($row != null) {
            print "Mapping already exists!\n";
            return;
        }
    }
    $sql = "INSERT INTO `vtiger_convertleadmapping` (leadfid, accountfid, contactfid, potentialfid, editable) VALUES (?,?,?,?,?)";
    $db->pquery($sql, [$leadsFieldid, 0, 0, $opportunitiesFieldid, 0]);
    print "Updated conversion mapping for leads to opportunities.\n";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";