
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  sequence = 1 WHERE fieldname = 'za_zone'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  sequence = 2 WHERE fieldname = 'zoneadmin_id'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  sequence = 3 WHERE fieldname = 'zip_code'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  sequence = 4 WHERE fieldname = 'za_state'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
