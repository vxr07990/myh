<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
		print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

//OT3996 Menu Creator - Add "Menu Editor" from CRM Settings to module 

$db = PearDatabase::getInstance();

$db->query("CREATE TABLE `vtiger_menueditor` (
	`menueditorid` int(11) NOT NULL,
  	`selected_modules` text NOT NULL,
  	`menucreator_id` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");

$db->query("ALTER TABLE `vtiger_menueditor` ADD PRIMARY KEY (`menueditorid`);");

$db->query("ALTER TABLE `vtiger_menueditor` MODIFY `menueditorid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;");