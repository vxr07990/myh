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

Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_convertleadmapping` (leadfid, accountfid, contactfid, potentialfid, editable)
							SELECT  
								(SELECT `fieldid`
									FROM `vtiger_field`
									WHERE `columnname` = 'primary_phone_type'
										AND `tabid` = (SELECT `tabid`
															FROM `vtiger_tab`
															WHERE `name` = 'Leads')) as c1,
								'0' as c2,
								(SELECT `fieldid`
									FROM `vtiger_field`
									WHERE `columnname` = 'primary_phone_type'
										AND `tabid` = (SELECT `tabid`
															FROM `vtiger_tab`
															WHERE `name` = 'Contacts')) as c3,
								'0' as c4,
								1
							FROM `vtiger_convertleadmapping`
							WHERE NOT EXISTS (
								SELECT * FROM `vtiger_convertleadmapping`
									WHERE `leadfid` = (SELECT `fieldid`
										FROM `vtiger_field`
										WHERE `columnname` = 'primary_phone_type'
											AND `tabid` = (SELECT `tabid`
																FROM `vtiger_tab`
																WHERE `name` = 'Leads'))
									AND `contactfid` = (SELECT `fieldid`
										FROM `vtiger_field`
										WHERE `columnname` = 'primary_phone_type'
											AND `tabid` = (SELECT `tabid`
																FROM `vtiger_tab`
																WHERE `name` = 'Contacts'))
								) LIMIT 1");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";