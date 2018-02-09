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



$createToDoArray = [
    'include' => [
        'Leads',
        'Accounts',
        'Contacts',
        'HelpDesk',
        'Campaigns',
        'PurchaseOrder',
        'SalesOrder',
        'Invoice',
        'Opportunities',
        'Estimates',
        'Orders',
        'Documents',
    ],
    'exclude' => [
        'Calendar',
        'FAQ',
        'Events'
    ]
];

$createEventArray = [
    'include' => [
        'Leads',
        'Accounts',
        'Contacts',
        'HelpDesk',
        'Campaigns',
        'Opportunities',
        'Orders',
        'Documents',
    ],
    'exclude' => [
        'Calendar',
        'FAQ',
        'Events'
    ]
];

Vtiger_Utils::ExecuteQuery("UPDATE `com_vtiger_workflow_tasktypes` SET modules='".json_encode($createToDoArray)."' WHERE id=3 AND label='Create Todo'");
Vtiger_Utils::ExecuteQuery("UPDATE `com_vtiger_workflow_tasktypes` SET modules='".json_encode($createEventArray)."' WHERE id=4 AND label='Create Event'");

$sql = "SELECT fieldtypeid FROM `vtiger_ws_fieldtype` WHERE uitype=?";
$result = $db->pquery($sql, [66]);
$fieldTypeId = $result->fields['fieldtypeid'];

addRelatedModuleToFieldType($db, $fieldTypeId, 'Opportunities');
addRelatedModuleToFieldType($db, $fieldTypeId, 'Orders');
addRelatedListIfNotExists($db, 'Orders', 'Calendar', 'Activities', 'get_activities', ['ADD'], 1);



function addRelatedModuleToFieldType($db, $fieldTypeId, $moduleName)
{
    $sql = "SELECT * FROM `vtiger_ws_referencetype` WHERE fieldtypeid=? AND `type`=?";
    $result = $db->pquery($sql, [$fieldTypeId, $moduleName]);
    if ($result->numRows() > 0) {
        echo "<br />$moduleName module is already present for uitype 66 fields<br />";
    } else {
        $sql = "INSERT INTO `vtiger_ws_referencetype` VALUES (?,?)";
        $db->pquery($sql, [$fieldTypeId, $moduleName]);
    }
}

function addRelatedListIfNotExists($db, $moduleName, $relModuleName, $label, $name, $actions, $sequence=null)
{
    $module = Vtiger_Module::getInstance($moduleName);
    $relModule = Vtiger_Module::getInstance($relModuleName);

    if ($module && $relModule) {
        $sql = "SELECT * FROM `vtiger_relatedlists` WHERE tabid=? AND related_tabid=?";
        $result = $db->pquery($sql, [$module->getId(), $relModule->getId()]);

        if ($result->numRows() > 0) {
            echo "<br />Related list for $relModuleName already exists in $moduleName<br />";
            return;
        }
        $module->setRelatedList($relModule, $label, $actions, $name);
        if ($sequence == null) {
            return;
        }

        $sql = "UPDATE `vtiger_relatedlists` SET sequence=sequence+1 WHERE tabid=? AND sequence>=?";
        $db->pquery($sql, [$module->getId(), $sequence]);

        $sql = "UPDATE `vtiger_relatedlists` SET sequence=? WHERE tabid=? AND related_tabid=? AND label=?";
        $db->pquery($sql, [$sequence, $module->getId(), $relModule->getId(), $label]);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";