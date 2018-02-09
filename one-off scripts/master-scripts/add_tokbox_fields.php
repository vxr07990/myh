<?php
if (function_exists("call_ms_function_ver")) {
    $version = 3;
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
include_once('vtlib/Vtiger/Link.php');

$surveysModule = Vtiger_Module::getInstance('Surveys');

$surveysBlock = Vtiger_Block::getInstance('LBL_SURVEYS_INFORMATION', $surveysModule);

$surveyType = Vtiger_Field::getInstance('survey_type', $surveysModule);
if ($surveyType) {
	echo "<h4>Field <b>survey_type</b> already exists!</h4>";
} else {
	$surveyType = new Vtiger_Field();
	$surveyType->label = 'LBL_SURVEYS_TYPE';
	$surveyType->name = 'survey_type';
	$surveyType->table = 'vtiger_surveys';
	$surveyType->column = 'survey_type';
	$surveyType->columntype = 'VARCHAR(255)';
	$surveyType->uitype = 16;
	$surveyType->typeofdata = 'V~M';

	$surveysBlock->addField($surveyType);
    $surveyType->setPicklistValues(array('On-site', 'Virtual'));
}
/*
$cubesheetId = Vtiger_Field::getInstance('cubesheet_id', $surveysModule);
if($cubesheetId) {
	echo "<h4>Field <b>cubesheet_id</b> already exists!</h4>";
} else {
	$cubesheetId = new Vtiger_Field();
	$cubesheetId->label = 'LBL_SURVEYS_CUBESHEETID';
	$cubesheetId->name = 'cubesheet_id';
	$cubesheetId->table = 'vtiger_surveys';
	$cubesheetId->column = 'cubesheet_id';
	$cubesheetId->columntype = 'INT(19)';
	$cubesheetId->uitype = 10;
	$cubesheetId->typeofdata = 'V~O';

	$surveysBlock->addField($cubesheetId);
	$cubesheetId->setRelatedModules(Array('Cubesheets'));
}
*/
$cubesheetsModule = Vtiger_Module::getInstance('Cubesheets');

$block = Vtiger_Block::getInstance('LBL_CUBESHEETS_INFORMATION', $cubesheetsModule);

$field1 = Vtiger_Field::getInstance('tokbox_sessionid', $cubesheetsModule);
if ($field1) {
	echo "<h4>Field <b>tokbox_sessionid</b> already exists!</h4>";
} else {
	$field1 = new Vtiger_Field();
	$field1->label = 'LBL_CUBESHEETS_TOKBOX_SESSIONID';
	$field1->name = 'tokbox_sessionid';
	$field1->table = 'vtiger_cubesheets';
	$field1->column = 'tokbox_sessionid';
	$field1->columntype = 'VARCHAR(500)';
	$field1->uitype = 1;
	$field1->typeofdata = 'V~O';
	$field1->displaytype = 3;

	$block->addField($field1);
}

$field2 = Vtiger_Field::getInstance('tokbox_servertoken', $cubesheetsModule);
if ($field2) {
	echo "<h4>Field <b>tokbox_servertoken</b> already exists!</h4>";
} else {
	$field2 = new Vtiger_Field();
	$field2->label = 'LBL_CUBESHEETS_TOKBOX_SERVERTOKEN';
	$field2->name = 'tokbox_servertoken';
	$field2->table = 'vtiger_cubesheets';
	$field2->column = 'tokbox_servertoken';
	$field2->columntype = 'VARCHAR(500)';
	$field2->uitype = 1;
	$field2->typeofdata = 'V~O';
	$field2->displaytype = 3;

	$block->addField($field2);
}

$field3 = Vtiger_Field::getInstance('tokbox_clienttoken', $cubesheetsModule);
if ($field3) {
	echo "<h4>Field <b>tokbox_clienttoken</b> already exists!</h4>";
} else {
	$field3 = new Vtiger_Field();
	$field3->label = 'LBL_CUBESHEETS_TOKBOX_CLIENTTOKEN';
	$field3->name = 'tokbox_clienttoken';
	$field3->table = 'vtiger_cubesheets';
	$field3->column = 'tokbox_clienttoken';
	$field3->columntype = 'VARCHAR(500)';
	$field3->uitype = 1;
	$field3->typeofdata = 'V~O';
	$field3->displaytype = 3;

	$block->addField($field3);

	$cubesheetsModule->addLink('DETAILVIEWSIDEBARWIDGET', 'LBL_CUBESHEETS_TOKBOX', 'module=Cubesheets&view=VideoFeed');
}

$field4 = Vtiger_Field::getInstance('tokbox_devicecode', $cubesheetsModule);
if ($field4) {
	echo "<h4>Field <b>tokbox_devicecode</b> already exists!</h4>";
	echo "<h4>Checking column type for tokbox_devicecode</h4>";

	$fieldName = 'tokbox_devicecode';
	$tableName = 'vtiger_cubesheets';
	$columnType = 'varchar(20)';

    if (!$db) {
		$db = PearDatabase::getInstance();
	}

	$sql = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME=? AND COLUMN_NAME=? AND TABLE_SCHEMA=?";
	$result = $db->pquery($sql, [$tableName, $fieldName, getenv('DB_NAME')]);

	$type = $result->fields['COLUMN_TYPE'];

    if (strtolower($type) == strtolower($columnType)) {
		echo "<br />";
		echo "The column_type is correct for $fieldName";
		echo "<br />";
//		return;
	} else {
		$sql = "ALTER TABLE $tableName CHANGE COLUMN $fieldName $fieldName $columnType";
		echo "<br />";
		echo 'Running query '.$sql;
		echo "<br />";
		$db->query($sql);
	}

	$db->pquery("UPDATE `vtiger_field` SET displaytype=1 WHERE fieldid=?", [$field4->id]);
} else {
	$field4 = new Vtiger_Field();
	$field4->label = 'LBL_CUBESHEETS_TOKBOX_DEVICECODE';
	$field4->name = 'tokbox_devicecode';
	$field4->table = 'vtiger_cubesheets';
	$field4->column = 'tokbox_devicecode';
	$field4->columntype = 'VARCHAR(20)';
	$field4->uitype = 1;
	$field4->typeofdata = 'V~O';
	$field4->displaytype = 1;

	$block->addField($field4);
}

$field5 = Vtiger_Field::getInstance('tokbox_code_expiration', $cubesheetsModule);
if ($field5) {
	echo "<h4>Field <b>tokbox_code_expiration</b> already exists!</h4>";
} else {
	$field5 = new Vtiger_Field();
	$field5->label = 'LBL_CUBESHEETS_CODE_EXPIRATION';
	$field5->name = 'tokbox_code_expiration';
	$field5->table = 'vtiger_cubesheets';
	$field5->column = 'tokbox_code_expiration';
	$field5->columntype = 'INT(10)';
	$field5->uitype = 7;
	$field5->typeofdata = 'I~O';
	$field5->displaytype = 3;

	$block->addField($field5);
}

$field6 = Vtiger_Field::getInstance('survey_type', $cubesheetsModule);
if ($field6) {
	echo "<h4>Field <b>survey_type</b> already exists!</h4>";
} else {
	$field6 = new Vtiger_Field();
	$field6->label = 'LBL_CUBESHEETS_SURVEY_TYPE';
	$field6->name = 'survey_type';
	$field6->table = 'vtiger_cubesheets';
	$field6->column = 'survey_type';
	$field6->columntype = 'VARCHAR(255)';
	$field6->uitype = 16;
	$field6->typeofdata = 'V~O';

	$block->addField($field6);
}

$field7 = Vtiger_Field::getInstance('survey_appointment_id', $cubesheetsModule);
if ($field7) {
	echo "<h4>Field <b>survey_id</b> already exists!</h4>";
} else {
	$field7 = new Vtiger_Field();
	$field7->label = 'LBL_CUBESHEETS_SURVEY';
	$field7->name = 'survey_appointment_id';
	$field7->table = 'vtiger_cubesheets';
	$field7->column = 'survey_appointment_id';
	$field7->columntype = 'INT(11)';
	$field7->uitype = 10;
	$field7->typeofdata = 'I~O';

	$block->addField($field7);

	$field7->setRelatedModules(array('Surveys'));
}

$usersModule = Vtiger_Module::getInstance('Users');

$usersBlock = Vtiger_Block::getInstance('LBL_USER_ADV_OPTIONS', $usersModule);

$usersField = Vtiger_Field::getInstance('tokbox_permitted', $usersModule);
if ($usersField) {
	echo "<h4>Field <b>tokbox_permitted</b> already exists!</h4>";
} else {
	$usersField = new Vtiger_Field();
	$usersField->label = 'LBL_USERS_TOKBOX_PERMITTED';
	$usersField->name = 'tokbox_permitted';
	$usersField->table = 'vtiger_users';
	$usersField->column = 'tokbox_permitted';
	$usersField->columntype = 'VARCHAR(3)';
	$usersField->uitype = 156;
	$usersField->typeofdata = 'V~O';

	$usersBlock->addField($usersField);
}

if (!Vtiger_Utils::CheckTable('vtiger_tokbox_archives')) {
	echo "<h4>Creating table vtiger_tokbox_archives</h4>";
	Vtiger_Utils::CreateTable('vtiger_tokbox_archives',
							  '(
							    sessionid VARCHAR(200) NOT NULL,
							    archiveid VARCHAR(100) NOT NULL,
							    created_at DATETIME NOT NULL,
							    PRIMARY KEY(sessionid, archiveid)
							   )', true);
}

$updatingFields = [
    'survey_appointment_id',
    'cubesheets_orderid'
];

print "<h2>updating reference fields  </h2>\n";
foreach ($updatingFields as $fieldName) {
    correctTypeOfDataATF($cubesheetsModule->name, $fieldName);
}
print "<h2>done updating reference fields </h2>\n";

function correctTypeOfDataATF($moduleName, $fieldName)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $referenceField = Vtiger_Field::getInstance($fieldName, $module);
        if ($referenceField) {
            $typeOfData = $referenceField->typeofdata;
            if (!is_null($typeOfData) or $typeOfData != '') {
                $isMatch = preg_match('/V~/', $typeOfData);
            } else {
                $typeOfData = 'I~O';
                $isMatch = true;
            }
            if ($isMatch === false) {
                print "ERROR: couldn't preg_match?";
            } elseif ($isMatch) {
                $typeOfData = preg_replace('/V~/', 'I~', $typeOfData);
                print "<br>$moduleName $fieldName needs converting to I~<br>\n";
                $stmt = "UPDATE `vtiger_field` SET `typeofdata` = ?"
                        //. " `quickcreate` = 1"
                        ." WHERE `fieldid` = ? LIMIT 1";
                print "$stmt\n";
                print "$typeOfData, " . $referenceField->id  ."<br />\n";
                $db->pquery($stmt, [$typeOfData, $referenceField->id]);
                print "<br>$moduleName $fieldName is converted to I~<br>\n";
            } else {
                print "<br>$moduleName $fieldName is already I~<br>\n";
            }
        } else {
            print "<br />failed to find: $fieldName in $moduleName<br />\n";
        }
    } else {
        print "<br />failed to load module $moduleName<br />\n";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";