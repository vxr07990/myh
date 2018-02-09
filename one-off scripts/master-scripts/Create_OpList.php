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


$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';

$isNew = false;
echo "<h1>Starting OPList Creation Script</h1>";

$module = Vtiger_Module::getInstance('OPList');
if ($module) {
    echo "<h2>OPList already exists </h2><br>";
} else {
    $module = new Vtiger_Module();
    $module->name = 'OPList';
    $module->save();

    $module->initTables();
    $isNew = true;
}
$block1 = Vtiger_Block::getInstance('LBL_OPLIST_INFORMATION', $module);
if ($block1) {
    echo "<h3>The LBL_OPLIST_INFORMATION block already exists</h3><br> \n";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_OPLIST_INFORMATION';
    $module->addBlock($block1);
}
$field1 = Vtiger_Field::getInstance('op_name', $module);
if ($field1) {
    echo "<h4>The op_name field already exists</h4><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_OPLIST_OPNAME';
    $field1->name = 'op_name';
    $field1->table = 'vtiger_oplist';
    $field1->column = 'op_name';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 2;
    $field1->typeofdata = 'V~M';
    $field1->summaryfield = 1;
    $block1->addField($field1);
    $module->setEntityIdentifier($field1);
}
$field3 = Vtiger_Field::getInstance('assigned_user_id', $module);
if ($field3) {
    echo "<li>The assigned_user_id field already exists</li><br> \n";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_OPLIST_ASSIGNED_TO';
    $field3->name = 'assigned_user_id';
    $field3->table = 'vtiger_crmentity';
    $field3->column = 'smownerid';
    $field3->uitype = 53;
    $field3->typeofdata = 'V~M';
    $field3->summaryfield = 1;

    $block1->addField($field3);
}
$field4 = Vtiger_Field::getInstance('createdtime', $module);
if ($field4) {
    echo "<li>The createdtime field already exists</li><br> \n";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_OPLIST_CREATEDTIME';
    $field4->name = 'createdtime';
    $field4->table = 'vtiger_crmentity';
    $field4->column = 'createdtime';
    $field4->uitype = 70;
    $field4->typeofdata = 'T~O';
    $field4->displaytype = 2;

    $block1->addField($field4);
}
$field5 = Vtiger_Field::getInstance('modifiedtime', $module);
if ($field5) {
    echo "<li>The modifiedtime field already exists</li><br> \n";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_OPLIST_MODIFIEDTIME';
    $field5->name = 'modifiedtime';
    $field5->table = 'vtiger_crmentity';
    $field5->column = 'modifiedtime';
    $field5->uitype = 70;
    $field5->typeofdata = 'T~O';
    $field5->displaytype = 2;

    $block1->addField($field5);
}
if (getenv('INSTANCE_NAME') == 'sirva') {
    $field6 = Vtiger_Field::getInstance('op_move_type', $module);
    if ($field6) {
        echo "The move_type field already exists<br>\n";
    } else {
        $field6 = new Vtiger_Field();
        $field6->label = 'LBL_OPLIST_MOVETYPE';
        $field6->name = 'op_move_type';
        $field6->table = 'vtiger_oplist';
        $field6->column = 'op_move_type';
        $field6->columntype = 'VARCHAR(255)';
        $field6->uitype = 33;
        $field6->typeofdata = 'V~O';

        $block1->addField($field6);
        $field6->setPicklistvalues([
                                    'Interstate', //SIRVA
                                    'Intrastate', //Agent
                                    'O&I', //Agent
                                    //'Local Canada',
                                    'Local US', //Agent
                                    'Sirva Military', //SIRVA
                                    //'Inter-Provincial',
                                    //'Intra-Provincial',
                                    'Cross Border', //SIRVA
                                    'Alaska', //Agent
                                    'Hawaii', //Agent
                                    'International', //Agent
                                    'Max 3', //Agent
                                    'Max 4', //Agent
                                   ]);
    }
} else {
    $field6 = Vtiger_Field::getInstance('business_line', $module);
    if ($field6) {
        echo "The business_line field already exists<br>\n";
    } else {
        $field6 = new Vtiger_Field();
        $field6->label = 'LBL_OPLIST_BUSINESSLINE';
        $field6->name = 'business_line';
        $field6->table = 'vtiger_oplist';
        $field6->column = 'business_line';
        $field6->columntype = 'VARCHAR(255)';
        $field6->uitype = 33;
        $field6->typeofdata = 'V~O';

        $block1->addField($field6);
        $result = $db->pquery('SELECT business_line FROM vtiger_business_line', []);

        $field6->setPicklistvalues(array_column($result->GetAll(), 'business_line'));
    }
}
if ($isNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $module->addFilter($filter1);
    
    $filter1->addField($field1)->addField($field3, 1)->addField($field6, 2);
    
    $module->setDefaultSharing();
    $module->initWebservice();
    
    $opp = Vtiger_Module::getInstance('Opportunities');
    $opp->setRelatedList(Vtiger_Module::getInstance('OPList'), 'OP Lists', ['SELECT'], 'get_dependents_list');
    
    // Adds the Updates link to the vertical navigation menu on the right.
    ModTracker::enableTrackingForModule($module->id);
    
    $commentsModule = Vtiger_Module::getInstance('ModComments');
    $fieldInstance = Vtiger_Field::getInstance('related_to', $commentsModule);
    $fieldInstance->setRelatedModules(array('OPList'));
    
    //require_once 'modules/ModComments/ModComments.php';
    $detailviewblock = ModComments::addWidgetTo('OPList');
} else {
    $db = &PearDatabase::getInstance();
    $res = $db->pquery('SELECT 1 FROM vtiger_ws_entity WHERE `name`=?',
                       ['OPList']);
    if($db->num_rows($res) == 0)
    {
        $filter1 = new Vtiger_Filter();
        $filter1->name = 'All';
        $filter1->isdefault = true;
        $module->addFilter($filter1);

        $filter1->addField($field1)->addField($field3, 1)->addField($field6, 2);

        $module->setDefaultSharing();
        $module->initWebservice();

        $opp = Vtiger_Module::getInstance('Opportunities');
        //$opp->setRelatedList(Vtiger_Module::getInstance('OPList'), 'OP Lists', ['SELECT'], 'get_dependents_list');

        // Adds the Updates link to the vertical navigation menu on the right.
        ModTracker::enableTrackingForModule($module->id);

        $commentsModule = Vtiger_Module::getInstance('ModComments');
        $fieldInstance = Vtiger_Field::getInstance('related_to', $commentsModule);
        $fieldInstance->setRelatedModules(array('OPList'));

        //require_once 'modules/ModComments/ModComments.php';
        $detailviewblock = ModComments::addWidgetTo('OPList');
    }
}

require_once('one-off scripts/master-scripts/Create_OpListTables.php');
require_once('one-off scripts/master-scripts/Create_OpListAnswerTables.php');
echo "<h1>Finished OPList Creation Script</h1>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";