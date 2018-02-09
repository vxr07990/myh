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



echo "Initialize default profile creation <br>";

function createDefaultProfile($profileName)
{
    echo "Creating profile: ".$profileName."<br>";

    //New Security Start

    $adb = PearDatabase::getInstance();

    $result = $adb->pquery("SELECT * FROM `vtiger_profile` WHERE profilename = ?", array($profileName));

    $row = $result->fetchRow();

    if ($row[0]) {
        echo $profileName." already exists, skipping it... <br>";
    } else {
        $profileId1 = $adb->getUniqueID("vtiger_profile");

        //Inserting into vtiger_profile vtiger_table
        $adb->pquery("INSERT INTO vtiger_profile VALUES (".$profileId1.",'".$profileName."','Reserved For System Use', 0)", array());

        //echo "<br><h1>INSERT INTO vtiger_profile VALUES (".$profileId1.",'".$profileName."','Reserved For System Use')</h1><br>";

        //Inserting into vtiger_profile2gloabal permissions
        $adb->pquery("INSERT INTO vtiger_profile2globalpermissions VALUES ('".$profileId1."',1,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2globalpermissions VALUES ('".$profileId1."',2,0)", array());

        //Inserting into vtiger_profile2tab
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",1,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",2,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",3,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",4,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",6,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",7,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",8,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",9,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",10,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",13,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",14,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",15,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",16,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",18,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",19,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",20,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",21,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",22,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",23,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",24,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",25,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",26,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2tab VALUES (".$profileId1.",27,0)", array());

        //Inserting into vtiger_profile2standardpermissions  Adminsitrator

        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",2,0,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",2,1,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",2,2,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",2,3,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",2,4,0)", array());

        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",4,0,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",4,1,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",4,2,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",4,3,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",4,4,0)", array());

        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",6,0,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",6,1,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",6,2,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",6,3,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",6,4,0)", array());

        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",7,0,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",7,1,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",7,2,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",7,3,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",7,4,0)", array());

        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",8,0,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",8,1,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",8,2,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",8,3,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",8,4,0)", array());

        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",9,0,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",9,1,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",9,2,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",9,3,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",9,4,0)", array());

        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",13,0,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",13,1,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",13,2,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",13,3,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",13,4,0)", array());

        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",14,0,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",14,1,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",14,2,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",14,3,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",14,4,0)", array());

        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",15,0,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",15,1,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",15,2,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",15,3,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",15,4,0)", array());

        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",16,0,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",16,1,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",16,2,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",16,3,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",16,4,0)", array());

        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",18,0,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",18,1,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",18,2,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",18,3,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",18,4,0)", array());

        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",19,0,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",19,1,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",19,2,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",19,3,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",19,4,0)", array());

        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",20,0,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",20,1,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",20,2,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",20,3,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",20,4,0)", array());

        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",21,0,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",21,1,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",21,2,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",21,3,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",21,4,0)", array());

        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",22,0,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",22,1,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",22,2,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",22,3,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",22,4,0)", array());

        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",23,0,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",23,1,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",23,2,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",23,3,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",23,4,0)", array());

        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",26,0,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",26,1,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",26,2,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",26,3,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2standardpermissions VALUES (".$profileId1.",26,4,0)", array());

        //Inserting into vtiger_profile 2 utility Admin
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",2,5,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",2,6,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",4,5,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",4,6,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",6,5,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",6,6,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",7,5,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",7,6,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",8,6,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",7,8,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",6,8,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",4,8,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",13,5,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",13,6,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",13,8,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",14,5,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",14,6,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",7,9,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",18,5,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",18,6,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",7,10,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",6,10,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",4,10,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",2,10,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",13,10,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",14,10,0)", array());
        $adb->pquery("INSERT INTO vtiger_profile2utility VALUES (".$profileId1.",18,10,0)", array());

        echo "Creation of ".$profileName." profile complete! <br>";
    }
}

createDefaultProfile('Vanline Profile');
createDefaultProfile('Vanline User Profile');
createDefaultProfile('Agent Family Administrator Profile');
createDefaultProfile('Agent Administrator Profile');
createDefaultProfile('Agent 2 Profile');
createDefaultProfile('Sales Manager Profile');
createDefaultProfile('Agency User Profile');
createDefaultProfile('Sales Person Profile');
createDefaultProfile('Read-only User Profile');

echo "All default profiles complete!<br>";
//set the mail server smtp settings
Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_systems` (id,server,server_port,server_username,server_password,server_type,smtp_auth,server_path,from_email_field) VALUES (1,'smtp02.moverdocs.com',0,'','','email',0,NULL,'')");
if (!Vtiger_Utils::CheckTable('vtiger_systems_seq')) {
    echo "<li>creating vtiger_systems_seq </li><br>";
    Vtiger_Utils::CreateTable('vtiger_systems_seq',
                              '(
							    id INT(11)
								)', true);
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_systems_seq` VALUES (1)");
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";