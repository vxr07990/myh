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


$leadSourceManager = Vtiger_Module::getInstance('LeadSourceManager');
$newModule         = false;
if ($leadSourceManager) {
    echo "Lead Source Module exists";
} else {
    echo "Adding Lead Source Module";
    $newModule               = true;
    $leadSourceManager       = new Vtiger_Module();
    $leadSourceManager->name = 'LeadSourceManager';
    $leadSourceManager->save();
    $leadSourceManager->initTables();
}
echo "</ul>";
echo "<ul>";
$admin_block = Vtiger_Block::getInstance('LBL_LEADSOURCE_ADMINISTRATIVE', $leadSourceManager);
if ($admin_block) {
    echo "<li>The LBL_LEADSOURCE_ADMINISTRATIVE block already exists</li><br>";
} else {
    echo "<li>Creating LBL_LEADSOURCE_ADMINISTRATIVE block</li><br>";
    $admin_block           = new Vtiger_Block();
    $admin_block->label    = 'LBL_LEADSOURCE_ADMINISTRATIVE';
    $admin_block->sequence = 2;
    $leadSourceManager->addBlock($admin_block);
}
$block = Vtiger_Block::getInstance('LBL_LEADSOURCE_INFORMATION', $leadSourceManager);
if ($block) {
    echo "<li>The LBL_LEADSOURCE_INFORMATION block already exists</li><br>";
} else {
    echo "<li>Creating LBL_LEADSOURCE_INFORMATION block</li><br>";
    $block        = new Vtiger_Block();
    $block->label = 'LBL_LEADSOURCE_INFORMATION';
    $leadSourceManager->addBlock($block);
}
$custom_block = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $leadSourceManager);
if ($custom_block) {
    echo "<li>The LBL_CUSTOM_INFORMATION block already exists</li><br>";
} else {
    echo "<li>Creating LBL_CUSTOM_INFORMATION block</li><br>";
    $custom_block        = new Vtiger_Block();
    $custom_block->label = 'LBL_CUSTOM_INFORMATION';
    $leadSourceManager->addBlock($custom_block);
}
$fields = [
    /*
    'leadsource_id'     => [
        'label'               => 'LBL_LEADSOURCE_ID',
        'name'                => 'leadsource_id',
        'table'               => 'vtiger_leadsourcemanager',
        'column'              => 'leadsource_id',
        'columntype'          => 'INT(19)',
        'uitype'              => 7,
        'typeofdata'          => 'I~M',
        'summaryfield'        => 0,
        'displaytype'         => 0,
        'block'               => $block,
        'setEntityIdentifier' => 1, //norm 0
    ],
    */
    'source_name'       => [
        'label'               => 'LBL_LEADSOURCE_SOURCE_NAME',
        'name'                => 'source_name',
        'table'               => 'vtiger_leadsourcemanager',
        'column'              => 'source_name',
        'columntype'          => 'VARCHAR(50)',
        'uitype'              => 2,
        'typeofdata'          => 'V~M',
        'summaryfield'        => 1,
        'block'               => $block,
        'setEntityIdentifier' => 1, //norm 0
    ],
    'agency_code'       => [
        'label'        => 'LBL_LEADSOURCE_AGENCY_CODE',
        'name'         => 'agency_code',
        'table'        => 'vtiger_leadsourcemanager',
        'column'       => 'agency_code',
        'columntype'   => 'INT(19)',
        'uitype'       => 7,
        'typeofdata'   => 'I~M',
        'summaryfield' => 0,
        'displaytype'  => 2,
        'block'        => $block,
    ],
    //this case is optional, I think there's a script that forces to M I'll have to exclude it there.
    'agentid'           => [
        'label'        => 'Owner Agent',
        'name'         => 'agentid',
        'table'        => 'vtiger_crmentity',
        'column'       => 'agentid',
        'columntype'   => 'INT(10)',
        'uitype'       => 1002,
        'typeofdata'   => 'I~M',
        'summaryfield' => 1,
        'block'        => $block,
    ],
    'vanlinemanager_id' => [
        'label'        => 'LBL_LEADSOURCE_VANLINEID',
        'name'         => 'vanlinemanager_id',
        'table'        => 'vtiger_leadsourcemanager',
        'column'       => 'vanlinemanager_id',
        'columntype'   => 'INT(19)',
        'uitype'       => 7,
        'typeofdata'   => 'I~M',
        'summaryfield' => 0,
        'block'        => $block,
        'displaytype'  => 2,
        //'setRelatedModules' => ['VanlineManager'],
    ],
    'agency_related' => [
        'label'        => 'LBL_LEADSOURCE_AGENCY_RELATED',
        'name'         => 'agency_related',
        'table'        => 'vtiger_leadsourcemanager',
        'column'       => 'agency_related',
        'columntype'   => 'INT(10)',
        'uitype'       => 10,
        'typeofdata'   => 'I~O',
        'summaryfield' => 0,
        'block'        => $block,
        'setRelatedModules' => ['AgentManager'],
    ],
    'vanline_related' => [
        'label'        => 'LBL_LEADSOURCE_VANLINE_RELATED',
        'name'         => 'vanline_related',
        'table'        => 'vtiger_leadsourcemanager',
        'column'       => 'vanline_related',
        'columntype'   => 'INT(10)',
        'uitype'       => 10,
        'typeofdata'   => 'I~M',
        'summaryfield' => 0,
        'block'        => $block,
        'setRelatedModules' => ['VanlineManager'],
    ],
    'brand'             => [ //AVL / NAVL not the full name...
                             'label'        => 'LBL_LEADSOURCE_BRAND',
                             'name'         => 'brand',
                             'table'        => 'vtiger_leadsourcemanager',
                             'column'       => 'brand',
                             'columntype'   => 'VARCHAR(10)',
                             'uitype'       => 2,
                             'typeofdata'   => 'V~M',
                             'summaryfield' => 1,
                             'block'        => $block,
    ],
    'lmp_program_id'    => [
        'label'        => 'LBL_LEADSOURCE_LMP_PROGRAM_ID',
        'name'         => 'lmp_program_id',
        'table'        => 'vtiger_leadsourcemanager',
        'column'       => 'lmp_program_id',
        'columntype'   => 'INT(19)',
        'uitype'       => 7,
        'typeofdata'   => 'I~O',
        'summaryfield' => 0,
        'block'        => $block,
    ],
    'lmp_source_id'     => [
        'label'        => 'LBL_LEADSOURCE_LMP_SOURCE_ID',
        'name'         => 'lmp_source_id',
        'table'        => 'vtiger_leadsourcemanager',
        'column'       => 'lmp_source_id',
        'columntype'   => 'INT(19)',
        'uitype'       => 7,
        'typeofdata'   => 'I~O',
        'summaryfield' => 0,
        'block'        => $block,
    ],
    'marketing_channel' => [
        'label'        => 'LBL_LEADSOURCE_MARKETING_CHANNEL',
        'name'         => 'marketing_channel',
        'table'        => 'vtiger_leadsourcemanager',
        'column'       => 'marketing_channel',
        'columntype'   => 'VARCHAR(50)',
        'uitype'       => 2,
        'typeofdata'   => 'V~M',
        'summaryfield' => 1,
        'block'        => $block,
    ],
    'program_name'      => [
        'label'        => 'LBL_LEADSOURCE_PROGRAM_NAME',
        'name'         => 'program_name',
        'table'        => 'vtiger_leadsourcemanager',
        'column'       => 'program_name',
        'columntype'   => 'VARCHAR(50)',
        'uitype'       => 2,
        'typeofdata'   => 'V~M',
        'summaryfield' => 1,
        'block'        => $block,
    ],
    'program_terms'     => [
        'label'        => 'LBL_LEADSOURCE_PROGRAM_TERMS',
        'name'         => 'program_terms',
        'table'        => 'vtiger_leadsourcemanager',
        'column'       => 'program_terms',
        'columntype'   => 'VARCHAR(255)',
        'uitype'       => 19,
        'typeofdata'   => 'V~M',
        'summaryfield' => 0,
        'block'        => $block,
    ],
    'source_type'       => [
        'label'        => 'LBL_LEADSOURCE_SOURCE_TYPE',
        'name'         => 'source_type',
        'table'        => 'vtiger_leadsourcemanager',
        'column'       => 'source_type',
        'columntype'   => 'VARCHAR(50)',
        'uitype'       => 15,
        'typeofdata'   => 'V~M',
        'defaultvalue' => 'Agent Sourced QLAB',
        'readonly'     => 2,
        'summaryfield' => 1,
        'block'        => $block,
        'picklist'     => [
            'Agent Sourced',
            'Agent Sourced QLAB',
            'Agent Affinity',
            'Corporate Affinity',
            'Hybrid',
            'Marketing',
            'Consumer Direct',
            'Traditional',
        ],
    ],
    'active'            => [
        'label'        => 'LBL_LEADSOURCE_ACTIVE',
        'name'         => 'active',
        'table'        => 'vtiger_leadsourcemanager',
        'column'       => 'active',
        'columntype'   => 'TINYINT(1)',
        'uitype'       => 56,
        'typeofdata'   => 'C~O',
        'summaryfield' => 1,
        'defaultvalue' => 1,
        'block'        => $block,
    ],
    /*
    'description'       => [
        'label'      => 'LBL_LEADSOURCE_NOTES',
        'name'       => 'description',
        'table'      => 'vtiger_crmentity',
        'column'     => 'description',
        'uitype'     => 19,
        'typeofdata' => 'V~O',
        'block'      => $block,
    ],
    */
    'createdtime'       => [
        'label'       => 'Created Time',
        'name'        => 'createdtime',
        'table'       => 'vtiger_crmentity',
        'column'      => 'createdtime',
        'uitype'      => 70,
        'typeofdata'  => 'T~O',
        'displaytype' => 2,
        'block'       => $admin_block,
    ],
    'modifiedtime'      => [
        'label'       => 'Modified Time',
        'name'        => 'modifiedtime',
        'table'       => 'vtiger_crmentity',
        'column'      => 'modifiedtime',
        'uitype'      => 70,
        'typeofdata'  => 'T~O',
        'displaytype' => 2,
        'block'       => $admin_block,
    ],
];
$rField = addFields_LSM($fields, $leadSourceManager);
if ($newModule) {
    echo "<li>Adding Filters for the module</li><br>";
    //only run this stuff if we created the module this pass.
    $filter1            = new Vtiger_Filter();
    $filter1->name      = 'All';
    $filter1->isdefault = true;
    $leadSourceManager->addFilter($filter1);
    $filter1
        ->addField($rField['source_name'], 1)
        ->addField($rField['program_name'], 2)
        ->addField($rField['lmp_source_id'], 3)
        ->addField($rField['lmp_program_id'], 4)
        ->addField($rField['marketing_channel'], 5)
        ->addField($rField['active'], 6);
    $leadSourceManager->setDefaultSharing();
    $leadSourceManager->initWebservice();
    $relationLabel = 'Lead Sources';

    //relate to Vanline Manager
    echo "<li>relating vanline manager</li><br>";
    $vanlineManagerInstance = Vtiger_Module::getInstance('VanlineManager');
    $vanlineManagerInstance->setRelatedList($leadSourceManager, $relationLabel, ['ADD'], 'get_dependents_list');

    //relate to Agent Manager
    echo "<li>relating agent manager</li><br>";
    $agentManagerInstance = Vtiger_Module::getInstance('AgentManager');
    $agentManagerInstance->setRelatedList($leadSourceManager, $relationLabel, ['ADD'], 'get_dependents_list');

    echo "<li>relating ModComments</li><br>";
    ModTracker::enableTrackingForModule($leadSourceManager->id);
    $commentsModule = Vtiger_Module::getInstance('ModComments');
    $fieldInstance  = Vtiger_Field::getInstance('related_to', $commentsModule);
    $fieldInstance->setRelatedModules(['LeadSourceManager']);
    $detailviewblock = ModComments::addWidgetTo('LeadSourceManager');
}
echo "</ul>";

function addFields_LSM($fields, $module)
{
    $returnFields = [];
    foreach ($fields as $field_name => $data) {
        $field0 = Vtiger_Field::getInstance($field_name, $module);
        if ($field0) {
            echo "<li>The $field_name field already exists</li><br>";
            $returnFields[$field_name] = $field0;
        } else {
            echo "<li>Creating $field_name field</li><br>";
            //@TODO: check data validity
            $field0               = new Vtiger_Field();
            //these are assumed to be filled.
            $field0->label        = $data['label'];
            $field0->name         = $data['name'];
            $field0->table        = $data['table'];
            $field0->column       = $data['column'];
            $field0->columntype   = $data['columntype'];
            $field0->uitype       = $data['uitype'];
            $field0->typeofdata   = $data['typeofdata'];

            $field0->summaryfield = ($data['summaryfield']?1:0);
            $field0->defaultvalue = $data['defaultvalue'];
            //these three MUST have values or it doesn't pop vtiger_field.
            $field0->displaytype  = ($data['displaytype']?$data['displaytype']:1);
            $field0->readonly     = ($data['readonly']?$data['readonly']:1);
            $field0->presence     = ($data['presence']?$data['presence']:2);

            $data['block']->addField($field0);
            if ($data['setEntityIdentifier'] == 1) {
                $module->setEntityIdentifier($field0);
            }
            //just completely ensure there's stuff in the array before doing it.
            if (
                array_key_exists('setRelatedModules', $data) &&
                $data['setRelatedModules'] &&
                count($data['setRelatedModules']) > 0
            ) {
                $field0->setRelatedModules($data['setRelatedModules']);
            }
            if (
                array_key_exists('picklist', $data) &&
                $data['picklist'] &&
                count($data['picklist']) > 0
            ) {
                $field0->setPicklistValues($data['picklist']);
            }
            $returnFields[$field_name] = $field0;
        }
    }

    return $returnFields;
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";