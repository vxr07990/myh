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


/**
 * Goal is to add tariff 400NG for NA to tariff manager.
 */


$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
require_once('includes/main/WebUI.php');
require_once('include/Webservices/Create.php');
require_once('modules/Users/Users.php');

if (getenv('INSTANCE_NAME') == 'sirva') {
    print '<h2>Start Generating 400NG Tariffs.</h2>';
    //update the vtiger_custom_tariff_type picklist
    $moduleName = 'TariffManager';

    $tariffBaseName = '400NG'; //has Allied or North American appended
    $tariffType = 'Interstate';
    //$tariffRatingEngine = 'https://awsdev1.movecrm.com/RatingEngineDev/Base400NG/RatingService.svc?wsdl';
    $custom_javascript = 'Estimates_BaseSIRVA_Js';
    $customTariffType = '400NG';

    $picklistFieldName = 'custom_tariff_type';
    $picklistOrder = [
        'TPG',
        'Allied Express',
        'TPG GRR',
        'ALLV-2A',
        'Pricelock',
        'Blue Express',
        'Pricelock GRR',
        'NAVL-12A',
        '400N Base',
        '400N/104G',
        '400NG',
        'Local/Intra',
        'Max 3',
        'Max 4',
        'Intra - 400N',
        'Canada Gov\'t',
        'Canada Non-Govt',
        'UAS',
        'Base',
    ];

    $db = PearDatabase::getInstance();
    $stmt   = "SELECT * FROM `vtiger_tariffmanager` WHERE `tariffmanagername` = '400NG'";
    $result = $db->pquery($stmt, []);

    //I can't remember the right way to check this...
    if (method_exists($result, 'fetchRow') && $row = $result->fetchRow()) {
        print "removing non-aligned 400NG<Br />\n";
        $stmt = 'DELETE FROM `vtiger_tariffmanager` WHERE `tariffmanagerid` = ? LIMIT 1';
        $db->pquery($stmt, [$row['tariffmanagerid']]);
    }

    $sql             = "SELECT vanlinemanagerid FROM `vtiger_vanlinemanager`
            WHERE vanline_name = 'North American Van Lines' OR vanline_id=9
            LIMIT 1";
    $result          = $db->pquery($sql, []);
    $row             = $result->fetchRow();
    $northAmericanId = $row[0];

    $sql      = "SELECT vanlinemanagerid FROM `vtiger_vanlinemanager`
			WHERE vanline_name = 'Allied' OR vanline_id=1
			LIMIT 1";
    $result   = $db->pquery($sql, []);
    $row      = $result->fetchRow();
    $alliedId = $row[0];

    //Allied Tariffs
    //generateTariff($tariffBaseName . ' Allied', $tariffType, $tariffRatingEngine, $custom_javascript, $customTariffType, $alliedId);
    //NA Tariffs
    //generateTariff($tariffBaseName . ' North American', $tariffType, $tariffRatingEngine, $custom_javascript, $customTariffType, $northAmericanId);
    print '<h2>Through Generating 400NG Tariffs.</h2>';

    print '<li>checking 400NG is in the custom_tariff_type picklist</li>';
    updateTariffPicklistFor400NG($picklistFieldName, $moduleName, $picklistOrder);


    print '<li>Ensure Estimates business_line_est Picklist has "Sirva Military"</li>';
    $moduleName = 'Estimates';
    $picklistFieldName = 'business_line_est';
    $picklistOrder = [
        'Local Move',
        'Interstate Move',
        'Intrastate Move',
        'International Move',
        'HHG - International Air',
        'HHG - International Sea',
        'Commercial Move',
        'Commercial - Distribution',
        'Commercial - Record Storage',
        'Commercial - Storage',
        'Commercial - Asset Management',
        'Commercial - Project',
        'Military',
        'Auto Transportation',
    ];
    updateTariffPicklistFor400NG($picklistFieldName, $moduleName, $picklistOrder);
}

function generateTariff($name, $type, $ratingUrl, $customJS, $customType, $vanlineId)
{
    $db = PearDatabase::getInstance();

    $sql = 'SELECT tariffmanagerid FROM `vtiger_tariffmanager` WHERE tariffmanagername = ?';
    $result = $db->pquery($sql, [$name]);
    $row = $result->fetchRow();
    if ($row != null) {
        print '<li>WARNING: an interstate tariff already exists with the name '.$name.'</li>';
        return;
    }

    print "<h2>Begin generation of $name.</h2><br>";
    $data = array(
        'tariffmanagername' => $name,
        'tariff_type' => $type,
        'rating_url' => $ratingUrl,
        'custom_javascript' => $customJS,
        'custom_tariff_type' => $customType,
        'assigned_user_id' => '19x1',
        'Vanline'.$vanlineId.'State' => 'assigned',
        'assignVanline'.$vanlineId.'Agents' => 'on',
    );

    $sql = "SELECT agentmanagerid FROM `vtiger_agentmanager` WHERE vanline_id = ?";
    $result = $db->pquery($sql, array($vanlineId));
    $row = $result->fetchRow();

    while ($row != null) {
        $data['assignAgent'.$row[0]] = 'on';
        $row = $result->fetchRow();
    }

    print "<h2>$name DATA: ".print_r($data, true)."</h2><br>";

    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
    $newService = vtws_create('TariffManager', $data, $current_user);

    print "<h2>$name generated ($newService).</h2><br>";
}

function updateTariffPicklistFor400NG($picklistFieldName, $moduleName, $picklistOrder)
{
    $module = Vtiger_Module::getInstance($moduleName);
    $field1 = Vtiger_Field::getInstance($picklistFieldName, $module);
    if ($field1) {
        $picklistValues = [];
        $i              = 1;
        foreach ($picklistOrder as $value) {
            if ($value) {
                $id = getPicklistId($picklistFieldName, $value);
                if ($id === false) {
                    //so we didn't find the ID we assume it doesn't exist and add it
                    print "<br> Adding $value value to picklist field $picklistFieldName. <br>";
                    $id = addNewPicklistItem($value, $picklistFieldName, $moduleName);
                }
                if ($id !== false) {
                    //ensure we skip anything that failed to create so we don't do something unexpected
                    $picklistValues[$id] = $i++;
                }
            }
        }
        if ($picklistFieldName && is_array($picklistValues) && count($picklistValues) > 0) {
            updatePicklistOrder($picklistFieldName, $picklistValues);
        } else {
            print '<h2 style="color:orange;">ERROR: Unable to update picklist order. one of these is wrong: </h2><br />';
            print "<li>picklistFieldName => $picklistFieldName</li><br />";
            print '<li>picklistValues => '.print_r($picklistValues, true).'</li><br />';
        }
    } else {
        print "Creating new field: $picklistFieldName.<br />";
    }
}

/**
 * function to add a new picklist using their framework to ensure linkage
 * adapted from modules/Settings/Picklist/actions/SaveAjax.php
 *
 * EXAMPLE DATA:
 * (SaveAjax.php:65): moduleName (TariffManager)
 * (SaveAjax.php:66): pickListName (custom_tariff_type)
 * (SaveAjax.php:67) newValue : test blah
 *
 * @param string $newValue
 * @param string $pickListName
 * @param string $moduleName
 * @return int|bool $response
 */
function addNewPicklistItem($newValue, $pickListName, $moduleName)
{
    $id = false;
    $moduleModel = Settings_Picklist_Module_Model::getInstance($moduleName);
    $fieldModel = Settings_Picklist_Field_Model::getInstance($pickListName, $moduleModel);
    $rolesSelected = array();
    if ($fieldModel->isRoleBased()) {
        print "This field is role based which this script is not prepared to handle.<br />";
    } else {
        try {
            $response = $moduleModel->addPickListValues($fieldModel, $newValue, $rolesSelected);
            $id = $response['id'];
            print "<li>Successfully added new picklist value ($newValue) => ($id) for $pickListName in $moduleName. </li><br />";
        } catch (Exception $e) {
            print '<h2 style="color:orange;">ERROR (failed to add new picklist value - ' . $newValue. '): '
                  . $e->getCode() . ' -- ' . $e->getMessage() . '</h2><br />';
        }
    }
    return $id;
}

/*
 * function to update the picklist to the new order
 * adapted from modules/Settings/Picklist/actions/SaveAjax.php
 *
 * EXAMPLE DATA:
(SaveAjax.php:167): pickListFieldName (custom_tariff_type)
(SaveAjax.php:169) picklistValues : Array [picklist ID] => [order sequence]
(
[1] => 1
[2] => 2
[3] => 3
)
 *
 * @param string $pickListFieldName
 * @param array $picklistValues
 * @return bool response
 */
function updatePicklistOrder($pickListFieldName, $picklistValues)
{
    $response = false;
    $moduleModel = new Settings_Picklist_Module_Model();

    try {
        $moduleModel->updateSequence($pickListFieldName, $picklistValues);
        print "Successfully updated picklist sequence for $pickListFieldName. <br />";
        $response = true;
    } catch (Exception $e) {
        print '<h2 style="color:orange;">ERROR (Failed to update picklist seq): '
              . $e->getCode() . ' -- ' . $e->getMessage() . '</h2><br />';
    }
    return $response;
}

/**
 * function to pull the picklist's id's by name
 *
 * @param string $picklistFieldName
 * @param string $picklistValue
 *
 * @return int
 */
function getPicklistId($picklistFieldName, $picklistValue)
{
    $rv = false;
    $db = PearDatabase::getInstance();
    //return * so we don't have to rely on escapeDbName here too.
    $sql = 'SELECT * FROM ' . $db->escapeDbName('vtiger_' . $picklistFieldName)
           . ' WHERE ' . $db->escapeDbName($picklistFieldName) . ' = ?'
           . ' LIMIT 1';
    //print "$sql<br />";
    //print "$picklistValue<br />";
    $result = $db->pquery($sql, [$picklistValue]);
    if ($db->num_rows($result) > 0) {
        $row = $result->fetchRow();
        $rv  = $row[$picklistFieldName.'id'];
    }

    return $rv;
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";