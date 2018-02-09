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



//A_Create_OASurveyRequestsModule

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$moduleInstance = Vtiger_Module::getInstance('OASurveyRequests');
if (!$moduleInstance) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'OASurveyRequests';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $moduleInstance->initWebservice();
}

$blockInstance = Vtiger_Block::getInstance('LBL_OASURVEYREQUESTS_INFORMATION', $moduleInstance);
if (!$blockInstance) {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_OASURVEYREQUESTS_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
}

$blockInstance2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleInstance);
if (!$blockInstance2) {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_CUSTOM_INFORMATION';
    $moduleInstance->addBlock($blockInstance2);
}

$field1 = Vtiger_Field::getInstance('oasurveyrequests_id', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_OASURVEYREQUESTS_ID';
    $field1->name = 'oasurveyrequests_id';
    $field1->table = 'vtiger_oasurveyrequests';
    $field1->column = 'oasurveyrequests_id';
    $field1->columntype = 'VARCHAR(100)';
    $field1->uitype = 4;
    $field1->typeofdata = 'V~M';
    $field1->summaryfield = 1;

    $blockInstance->addField($field1);

    $moduleInstance->setEntityIdentifier($field1);
    $entity = new CRMEntity();
    $entity->setModuleSeqNumber('configure', $moduleInstance->name, 'MS', 1);
}

$field2 = Vtiger_Field::getInstance('requestor_user_id', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_REQUESTOR_USER_ID';
    $field2->name = 'requestor_user_id';
    $field2->table = 'vtiger_oasurveyrequests';
    $field2->column = 'requestor_user_id';
    $field2->columntype = 'INT(19)';
    $field2->uitype = 53;
    $field2->typeofdata = 'I~M';
    $field2->summaryfield = 1;

    $blockInstance->addField($field2);

    //$field2->setRelatedModules(Array('Users'));
}

$field4 = Vtiger_Field::getInstance('requestor_agency_id', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_REQUESTOR_AGENCY_ID';
    $field4->name = 'requestor_agency_id';
    $field4->table = 'vtiger_oasurveyrequests';
    $field4->column = 'requestor_agency_id';
    $field4->columntype = 'INT(19)';
    $field4->uitype = 10;
    $field4->typeofdata = 'V~O';
    $field4->summaryfield = 1;

    $blockInstance->addField($field4);

    $field4->setRelatedModules(array('AgentManager'));
}

$field8 = Vtiger_Field::getInstance('related_record', $moduleInstance);
if (!$field8) {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_RELATED_RECORD';
    $field8->name = 'related_record';
    $field8->table = 'vtiger_oasurveyrequests';
    $field8->column = 'related_record';
    $field8->columntype = 'INT(19)';
    $field8->uitype = 10;
    $field8->typeofdata = 'I~O';

    $blockInstance->addField($field8);
    
    $field8->setRelatedModules(array('Potentials', 'Orders'));
}

$field9 = Vtiger_Field::getInstance('requested_agency', $moduleInstance);
if (!$field9) {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_REQUESTED_AGENCY';
    $field9->name = 'requested_agency';
    $field9->table = 'vtiger_oasurveyrequests';
    $field9->column = 'requested_agency';
    $field9->columntype = 'INT(19)';
    $field9->uitype = 10;
    $field9->typeofdata = 'V~M';
    $field9->summaryfield = 1;

    $blockInstance->addField($field9);
    
    $field9->setRelatedModules(array('Agents'));
}

$field10 = Vtiger_Field::getInstance('view_level', $moduleInstance);
if (!$field10) {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_VIEW_LEVEL';
    $field10->name = 'view_level';
    $field10->table = 'vtiger_oasurveyrequests';
    $field10->column = 'view_level';
    $field10->columntype = 'VARCHAR(30)';
    $field10->uitype = 15;
    $field10->typeofdata = 'V~O';

    $blockInstance->addField($field10);

    $field10->setPicklistValues(array('Full', 'Read-Only', 'No-Rates', 'No-Access'));
}

$field11 = Vtiger_Field::getInstance('oasurveyrequests_status', $moduleInstance);
if (!$field11) {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_OASURVEYREQUEST_STATUS';
    $field11->name = 'oasurveyrequests_status';
    $field11->table = 'vtiger_oasurveyrequests';
    $field11->column = 'oasurveyrequests_status';
    $field11->columntype = 'VARCHAR(30)';
    $field11->uitype = 15;
    $field11->typeofdata = 'V~O';

    $blockInstance->addField($field11);

    $field11->setPicklistValues(array('Pending', 'Accepted', 'Declined', 'Removed'));
}

$field12 = Vtiger_Field::getInstance('message', $moduleInstance);
if (!$field12) {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_MESSAGE';
    $field12->name = 'message';
    $field12->table = 'vtiger_oasurveyrequests';
    $field12->column = 'message';
    $field12->columntype = 'VARCHAR(255)';
    $field12->uitype = 2;
    $field12->typeofdata = 'V~O';

    $blockInstance->addField($field12);
}

$field15 = Vtiger_Field::getInstance('oasurveyrequests_agent_type', $moduleInstance);
if (!$field15) {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_OASURVEYREQUEST_AGENT_TYPE';
    $field15->name = 'oasurveyrequests_agent_type';
    $field15->table = 'vtiger_oasurveyrequests';
    $field15->column = 'oasurveyrequests_agent_type';
    $field15->columntype = 'VARCHAR(50)';
    $field15->uitype = 15;
    $field15->typeofdata = 'V~O';

    $blockInstance->addField($field15);

    $field15->setPicklistValues(array('Booking Agent', 'Destination Agent', 'Destination Storage Agent', 'Hauling Agent', 'Invoicing Agent', 'Origin Agent', 'Origin Storage Agent'));
}

$field16 = Vtiger_Field::getInstance('oasurveyrequests_timeout', $moduleInstance);
if (!$field16) {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_OASURVEYREQUEST_TIMEOUT';
    $field16->name = 'oasurveyrequests_timeout';
    $field16->table = 'vtiger_oasurveyrequests';
    $field16->column = 'oasurveyrequests_timeout';
    $field16->columntype = 'DATE';
    $field16->uitype = 5;
    $field16->typeofdata = 'D~O';

    $blockInstance->addField($field16);
}

$field36 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if (!$field36) {
    $field36 = new Vtiger_Field();
    $field36->label = 'Assigned To';
    $field36->name = 'assigned_user_id';
    $field36->table = 'vtiger_crmentity';
    $field36->column = 'smownerid';
    $field36->uitype = 53;
    $field36->typeofdata = 'V~M';

    $blockInstance->addField($field36);
}

$field37 = Vtiger_Field::getInstance('createdtime', $moduleInstance);
if (!$field37) {
    $field37 = new Vtiger_Field();
    $field37->label = 'Created Time';
    $field37->name = 'createdtime';
    $field37->table = 'vtiger_crmentity';
    $field37->column = 'createdtime';
    $field37->uitype = 70;
    $field37->typeofdata = 'T~O';
    $field37->displaytype = 2;

    $blockInstance->addField($field37);
}

$field38 = Vtiger_Field::getInstance('modifiedtime', $moduleInstance);
if (!$field38) {
    $field38 = new Vtiger_Field();
    $field38->label = 'Modified Time';
    $field38->name = 'modifiedtime';
    $field38->table = 'vtiger_crmentity';
    $field38->column = 'modifiedtime';
    $field38->uitype = 70;
    $field38->typeofdata = 'T~O';
    $field38->displaytype = 2;

    $blockInstance->addField($field38);
}

$field39 = Vtiger_Field::getInstance('agentid', $moduleInstance);
if (!$field39) {
    $field39 = new Vtiger_Field();
    $field39->label = 'Owner Agent';
    $field39->name = 'agentid';
    $field39->table = 'vtiger_crmentity';
    $field39->column = 'agentid';
    $field39->uitype = 1002;
    $field39->typeofdata = 'I~M';

    $blockInstance->addField($field39);
}

$moduleInstance = Vtiger_Module::getInstance('OASurveyRequests');
if (!$moduleInstance) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field1)->addField($field2, 1)->addField($field4, 2);

    $moduleInstance->initWebservice();
    $moduleInstance->setDefaultSharing();
}

require_once('vtlib/Vtiger/Link.php');
Vtiger_Link::addLink($moduleInstance->id, 'HEADERSCRIPT', 'AORequest', 'layouts/vlayout/modules/OASurveyRequests/resources/OASurveyRequestsJS.js', '', '', '');


// Add the OASurveys to the calendar feed
$db = PearDatabase::getInstance();
$sql = 'SELECT id FROM `vtiger_calendar_default_activitytypes` WHERE fieldname = ?';
$result = $db->pquery($sql, 'OASurveyRequests');
$activityId = false;

if ($result && $db->num_rows($result) >0) {
    $row = $result->fetchRow();

    $activityId = $row[0];
    $color = '#C71585';
} else {
   
    //Need to add the new activity type
    $activityId = $adb->getUniqueID('vtiger_calendar_default_activitytypes');
    $color = '#C71585';
    $db->pquery('INSERT INTO vtiger_calendar_default_activitytypes (id, module, fieldname, defaultcolor) VALUES (?,?,?,?)', array($activityId, 'OASurveyRequests', 'OA Survey Requests', $color));
}

//Add the event type to all coordinators

$result = $db->pquery("SELECT userid FROM vtiger_user2role 
    INNER JOIN vtiger_role ON vtiger_user2role.roleid = vtiger_role.roleid 
    WHERE depth = 6 
    AND userid NOT IN (SELECT userid FROM vtiger_calendar_user_activitytypes INNER JOIN vtiger_calendar_default_activitytypes ON vtiger_calendar_user_activitytypes.defaultid = vtiger_calendar_default_activitytypes.id WHERE module='OASurveyRequests' ) ");

if ($result && $db->num_rows($result)>0 && $activityId) {
    while ($row1 = $db->fetch_row($result)) {
        $userId = $row1['userid'];
        $db->pquery('INSERT INTO vtiger_calendar_user_activitytypes (id, defaultid, userid, color) VALUES (?,?,?,?)', array($db->getUniqueID('vtiger_calendar_user_activitytypes'), $activityId, $userId, $color));
    }
}





//adding Email field to agents

$agentInstance = Vtiger_Module::getInstance('Agents');
$agentsBlockInstance = Vtiger_Block::getInstance('LBL_AGENTS_INFORMATION', $agentInstance);

//$field1 = Vtiger_Field::getInstance('agent_email',$agentInstance);
//if(!$field1) {
//    $field1 = new Vtiger_Field();
//    $field1->label = 'Email';
//    $field1->name = 'agent_email';
//    $field1->table = 'vtiger_agents';
//    $field1->column = 'agent_email';
//    $field1->columntype = 'VARCHAR(100)';
//    $field1->uitype = 13;
//    $field1->typeofdata = 'V~M';
//
//    $agentsBlockInstance->addField($field1);
//}

$field1 = Vtiger_Field::getInstance('agent_agentmanagerid', $agentInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'Agent Manager ID';
    $field1->name = 'agent_agentmanagerid';
    $field1->table = 'vtiger_agents';
    $field1->column = 'agent_agentmanagerid';
    $field1->columntype = 'INT(19)';
    $field1->uitype = 10;
    $field1->typeofdata = 'I~O';

    $agentsBlockInstance->addField($field1);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";