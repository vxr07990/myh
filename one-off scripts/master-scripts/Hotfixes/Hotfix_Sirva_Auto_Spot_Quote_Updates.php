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

include_once('vtlib/Vtiger/Module.php');

$db = PearDatabase::getInstance();

//Drop the potential field and relations
Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_autospotquote` DROP `potential_id`");
Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_fieldmodulerel` WHERE `module` LIKE 'AutoSpotQuote' AND `relmodule` LIKE 'Opportunities'");
Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_field` WHERE `tabid` = (SELECT `tabid` FROM `vtiger_tab` WHERE `name` = 'AutoSpotQuote') AND `columnname` = 'potential_id'");

$moduleAutoSpotQuote = Vtiger_Module::getInstance('AutoSpotQuote');
$blockInstance = Vtiger_Block::getInstance('LBL_AUTOSPOTQUOTEDETAILS', $moduleAutoSpotQuote);

$field1 = Vtiger_Field::getInstance('estimate_id', $moduleAutoSpotQuote);
if ($field1) {
    echo "<li>The estimate_id field already exists</li><br> \n";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_AUTOSPOTQUOTEESTIMATE';
    $field1->name = 'estimate_id';
    $field1->table = 'vtiger_autospotquote';
    $field1->column = 'estimate_id';
    $field1->columntype = 'INT(19)';
    $field1->uitype = 10;
    $field1->typeofdata = 'V~M';
    $field1->summaryfield = 0;

    $blockInstance->addField($field1);

    $field1->setRelatedModules(['Estimates']);
    Vtiger_Module::getInstance('Estimates')->setRelatedList($moduleAutoSpotQuote, 'Auto Spot Quote', 'get_related_list');
    Vtiger_Module::getInstance('Opportunities')->unsetRelatedList($moduleAutoSpotQuote, 'Auto Spot Quote', array('ADD'), 'get_related_list');
}

$field = Vtiger_Field::getInstance('registration_number', $moduleAutoSpotQuote);
if ($field) {
    echo "<br> The registration_number field already exists in AutoSpotQuote <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AUTOSPOTQUOTE_STSREG';
    $field->name = 'registration_number';
    $field->table = 'vtiger_autospotquote';
    $field->column ='registration_number';
    $field->columntype = 'varchar(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';
    $field->displaytype = 1;


    $blockInstance->addField($field);
}

$block327 = Vtiger_Block::getInstance('LBL_AUTOSPOTQUOTEDETAILS', $moduleAutoSpotQuote);
$field2360 = Vtiger_Field::getInstance('auto_make', $moduleAutoSpotQuote);
$field2364 = Vtiger_Field::getInstance('auto_year', $moduleAutoSpotQuote);
$field2366 = Vtiger_Field::getInstance('auto_rush_fee', $moduleAutoSpotQuote);
$field2367 = Vtiger_Field::getInstance('auto_transport_type', $moduleAutoSpotQuote);
$field2368 = Vtiger_Field::getInstance('auto_condition', $moduleAutoSpotQuote);
$field2370 = Vtiger_Field::getInstance('auto_comment', $moduleAutoSpotQuote);
$field2361 = Vtiger_Field::getInstance('auto_model', $moduleAutoSpotQuote);
$field2365 = Vtiger_Field::getInstance('auto_smf', $moduleAutoSpotQuote);
$field2369 = Vtiger_Field::getInstance('auto_load_from', $moduleAutoSpotQuote);
$field2362 = Vtiger_Field::getInstance('assigned_user_id', $moduleAutoSpotQuote);
$field2794 = Vtiger_Field::getInstance('estimate_id', $moduleAutoSpotQuote);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field2360->id." THEN 1 WHEN fieldid=".$field2364->id." THEN 3 WHEN fieldid=".$field2366->id." THEN 5 WHEN fieldid=".$field2367->id." THEN 7 WHEN fieldid=".$field2368->id." THEN 9 WHEN fieldid=".$field2370->id." THEN 11 WHEN fieldid=".$field2361->id." THEN 2 WHEN fieldid=".$field2365->id." THEN 4 WHEN fieldid=".$field2369->id." THEN 6 WHEN fieldid=".$field2362->id." THEN 8 WHEN fieldid=".$field2794->id." THEN 10 END, block=CASE WHEN fieldid=".$field2360->id." THEN ".$block327->id." WHEN fieldid=".$field2364->id." THEN ".$block327->id." WHEN fieldid=".$field2366->id." THEN ".$block327->id." WHEN fieldid=".$field2367->id." THEN ".$block327->id." WHEN fieldid=".$field2368->id." THEN ".$block327->id." WHEN fieldid=".$field2370->id." THEN ".$block327->id." WHEN fieldid=".$field2361->id." THEN ".$block327->id." WHEN fieldid=".$field2365->id." THEN ".$block327->id." WHEN fieldid=".$field2369->id." THEN ".$block327->id." WHEN fieldid=".$field2362->id." THEN ".$block327->id." WHEN fieldid=".$field2794->id." THEN ".$block327->id." END WHERE fieldid IN (".$field2360->id.",".$field2364->id.",".$field2366->id.",".$field2367->id.",".$field2368->id.",".$field2370->id.",".$field2361->id.",".$field2365->id.",".$field2369->id.",".$field2362->id.",".$field2794->id.")");


    $sql    = "SELECT * FROM `vtiger_tariffmanager` WHERE `tariffmanagername` = 'Autos Only'";
    $result = $db->pquery($sql, []);

    if ($db->num_rows($result) < 1) {
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
        $data = array(
            'tariffmanagername' => 'Autos Only',
            'tariff_type' => 'Interstate',
            'rating_url' => 'https://awsdev1.movecrm.com/RatingEngine/SIRVA',
            'custom_javascript' => 'Estimates_BaseSIRVA_Js',
            'custom_tariff_type' => 'Autos Only',
            'assigned_user_id' => '19x1'
        );
        $newService = vtws_create('TariffManager', $data, $current_user);
    }

    //Insert 204-A
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_agrmt_cod` (agrmt_codid, agrmt_cod, sortorderid, presence) SELECT id + 2, '204-A', id + 2, 1 FROM `vtiger_agrmt_cod_seq` WHERE NOT EXISTS (SELECT * FROM `vtiger_agrmt_cod` WHERE agrmt_cod = '204-A')");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";