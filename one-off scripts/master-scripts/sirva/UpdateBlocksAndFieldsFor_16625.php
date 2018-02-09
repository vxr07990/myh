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

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;

echo '<h2>Update Opportunity UI</h2>';
echo '<h3>Make the fields of ExtraStops as mandatory</h3>';
$moduleModel = Vtiger_Module_Model::getInstance('ExtraStops');
echo '<li>City</li><br>';
$cityFieldModel = $moduleModel->getField("extrastops_city");
if ($cityFieldModel) {
    $cityFieldModel->updateTypeofDataFromMandatory("M")
        ->set('quickcreate', '2');
    $cityFieldModel->save();
}

echo '<li>State</li><br>';
$StateFieldModel = $moduleModel->getField("extrastops_state");
if ($StateFieldModel) {
    $StateFieldModel->updateTypeofDataFromMandatory("M")
        ->set('quickcreate', '2');
    $StateFieldModel->save();
}

echo '<li>Zip</li><br>';
if ($ZipFieldModel) {
    $ZipFieldModel = $moduleModel->getField("extrastops_zip");
    $ZipFieldModel->updateTypeofDataFromMandatory("M")
        ->set('quickcreate', '2');
    $ZipFieldModel->save();
}

echo '<li>Country</li><br>';
if ($CountryFieldModel) {
    $CountryFieldModel = $moduleModel->getField("extrastops_country");
    $CountryFieldModel->updateTypeofDataFromMandatory("M")
        ->set('quickcreate', '2');
    $CountryFieldModel->save();
}

echo '<li>Location Type</li><br>';
if ($LocationTypeFieldModel) {
    $LocationTypeFieldModel = $moduleModel->getField("extrastops_type");
    $LocationTypeFieldModel->updateTypeofDataFromMandatory("M")
        ->set('quickcreate', '2');
    $LocationTypeFieldModel->save();
}

echo '<h3>Convert “Sequence” field in “Extra Stops” to picklist.</h3>';
//Check if "Sequence" is picklist
$rsCheck=$adb->pquery("SELECT * FROM vtiger_field WHERE fieldname=? AND uitype=? AND tabid=?", array('extrastops_sequence', '16', getTabid("ExtraStops")));
if ($adb->num_rows($rsCheck)>0) {
    echo '<li>“Sequence” field is picklist</li><br>';
} else {
    $adb->pquery("update `vtiger_field` set `uitype`='16' WHERE fieldname=? AND tabid=? ", array('extrastops_sequence', getTabid("ExtraStops")));
    if (!Vtiger_Utils::CheckTable('vtiger_extrastops_sequence')) {
        Vtiger_Utils::CreateTable(
            'vtiger_extrastops_sequence',
            "(extrastops_sequenceid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				extrastops_sequence VARCHAR(200) NOT NULL,
				sortorderid INT(11),
				presence INT (11) NOT NULL DEFAULT 1)",
            true);
        // Add value to picklist now
        $values= array(2,3,4,5,6,7,8,9,10,11,12,13,14,15,16);
        $sortid = 1;
        foreach ($values as $value) {
            $presence = 1; // 0 - readonly, Refer function in include/ComboUtil.php
            $new_id = $adb->getUniqueId($picklist_table);
            $adb->pquery("INSERT INTO vtiger_extrastops_sequence (extrastops_sequenceid, extrastops_sequence, sortorderid, presence) VALUES(?,?,?,?)",
                array($new_id, $value, $sortid, $presence));

            $sortid = $sortid+1;
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";