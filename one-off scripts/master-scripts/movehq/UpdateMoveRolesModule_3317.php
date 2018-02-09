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
error_reporting(E_ERROR);
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
global $adb;
$moduleInstance = Vtiger_Module::getInstance('MoveRoles');
if (!$moduleInstance) {
    echo "<br> The MoveRoles Module not exists <br>";
} else {
    $fieldMoveRole= Vtiger_Field::getInstance('moveroles_role', $moduleInstance);
    if ($fieldMoveRole) {
        $adb->pquery("UPDATE `vtiger_field` SET `uitype`=?, `typeofdata`=? WHERE `tabid`=? AND `fieldname`=?", array(10, 'I~M', $moduleInstance->id, 'moveroles_role'));

        $checkres = $adb->pquery('SELECT * FROM vtiger_fieldmodulerel WHERE fieldid=? AND module=? AND relmodule=?',
            array($fieldMoveRole->id, 'MoveRoles', 'EmployeeRoles'));
        // If relation not exist
        if ($adb->num_rows($checkres)==0) {
            $adb->pquery('INSERT INTO vtiger_fieldmodulerel(`fieldid`, `module`, `relmodule`) VALUES(?,?,?)',
                array($fieldMoveRole->id, 'MoveRoles', 'EmployeeRoles'));
        }
        $adb->pquery("UPDATE `vtiger_field` SET `typeofdata`='V~M' WHERE `fieldname`=? AND `tabid`=?",array('moveroles_employees',$moduleInstance->id));
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";