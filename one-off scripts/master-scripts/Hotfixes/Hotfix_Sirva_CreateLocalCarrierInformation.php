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


$localCarrier = Vtiger_Module::getInstance('LocalCarrier');
$newModule    = false;
if ($localCarrier) {
    echo "Module exists";
} else {
    $newModule          = true;
    $localCarrier       = new Vtiger_Module();
    $localCarrier->name = 'LocalCarrier';
    $localCarrier->save();
    $localCarrier->initTables();
}
$admin_block = Vtiger_Block::getInstance('LBL_LOCALCARRIER_ADMINISTRATIVE', $localCarrier);
if ($admin_block) {
    echo "<li>The LBL_LOCALCARRIER_ADMINISTRATIVE block already exists</li><br>";
} else {
    $admin_block           = new Vtiger_Block();
    $admin_block->label    = 'LBL_LOCALCARRIER_ADMINISTRATIVE';
    $admin_block->sequence = 2;
    $localCarrier->addBlock($admin_block);
}
$block = Vtiger_Block::getInstance('LBL_LOCALCARRIER_INFORMATION', $localCarrier);
if ($block) {
    echo "<li>The LBL_LOCALCARRIER_INFORMATION block already exists</li><br>";
} else {
    $block        = new Vtiger_Block();
    $block->label = 'LBL_LOCALCARRIER_INFORMATION';
    $localCarrier->addBlock($block);
}
$custom_block = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $localCarrier);
if ($custom_block) {
    echo "<li>The LBL_CUSTOM_INFORMATION block already exists</li><br>";
} else {
    $custom_block        = new Vtiger_Block();
    $custom_block->label = 'LBL_CUSTOM_INFORMATION';
    $localCarrier->addBlock($custom_block);
}
$fields = [
    'carrier_name' => [
        'label'               => 'LBL_LOCALCARRIER_NAME',
        'name'                => 'carrier_name',
        'table'               => 'vtiger_localcarrier',
        'column'              => 'carrier_name',
        'columntype'          => 'VARCHAR(50)',
        'uitype'              => 1,
        'typeofdata'          => 'V~M',
        'summaryfield'        => 1,
        'block'               => $block,
        'setEntityIdentifier' => 1, //norm 0
        'setRelatedModules'   => [],
    ],
    'agentid'      => [
        'label'      => 'Owner Agent',
        'name'       => 'agentid',
        'table'      => 'vtiger_crmentity',
        'column'     => 'agentid',
        'columntype' => 'INT(10)',
        'uitype'     => 1002,
        'typeofdata' => 'I~M',
        'block'      => $block,
    ],
    /*
    'vanline_id'   => [
        'label'             => 'LBL_LOCALCARRIER_VANLINEID',
        'name'              => 'vanline_id',
        'table'             => 'vtiger_localcarrier',
        'column'            => 'vanline_id',
        'columntype'        => 'VARCHAR(255)',
        'uitype'            => 10,
        'typeofdata'        => 'V~O',
        'summaryfield'      => 1,
        'block'             => $block,
        'setRelatedModules' => ['VanlineManager'],
    ],
    */
    'address1'     => [
        'label'      => 'LBL_LOCALCARRIER_ADDRESS1',
        'name'       => 'address1',
        'table'      => 'vtiger_localcarrier',
        'column'     => 'address1',
        'columntype' => 'VARCHAR(100)',
        'uitype'     => 2,
        'typeofdata' => 'V~O',
        'block'      => $block,
    ],
    'address2'     => [
        'label'      => 'LBL_LOCALCARRIER_ADDRESS2',
        'name'       => 'address2',
        'table'      => 'vtiger_localcarrier',
        'column'     => 'address2',
        'columntype' => 'VARCHAR(100)',
        'uitype'     => 1,
        'typeofdata' => 'V~O',
        'block'      => $block,
    ],
    'city'         => [
        'label'      => 'LBL_LOCALCARRIER_CITY',
        'name'       => 'city',
        'table'      => 'vtiger_localcarrier',
        'column'     => 'city',
        'columntype' => 'VARCHAR(100)',
        'uitype'     => 2,
        'typeofdata' => 'V~O',
        'block'      => $block,
    ],
    'state'        => [
        'label'      => 'LBL_LOCALCARRIER_STATE',
        'name'       => 'state',
        'table'      => 'vtiger_localcarrier',
        'column'     => 'state',
        'columntype' => 'VARCHAR(100)',
        'uitype'     => 2,
        'typeofdata' => 'V~O',
        'block'      => $block,
    ],
    'zip'          => [
        'label'      => 'LBL_LOCALCARRIER_ZIP',
        'name'       => 'zip',
        'table'      => 'vtiger_localcarrier',
        'column'     => 'zip',
        'columntype' => 'VARCHAR(10)',
        'uitype'     => 2,
        'typeofdata' => 'V~O',
        'block'      => $block,
    ],
    'country'      => [
        'label'      => 'LBL_LOCALCARRIER_COUNTRY',
        'name'       => 'country',
        'table'      => 'vtiger_localcarrier',
        'column'     => 'country',
        'columntype' => 'VARCHAR(100)',
        'uitype'     => 2,
        'typeofdata' => 'V~O',
        'block'      => $block,
    ],
    'phone1'       => [
        'label'        => 'LBL_LOCALCARRIER_PHONE1',
        'name'         => 'phone1',
        'table'        => 'vtiger_localcarrier',
        'column'       => 'phone1',
        'columntype'   => 'VARCHAR(20)',
        'uitype'       => 11,
        'typeofdata'   => 'V~O',
        'block'        => $block,
        'summaryfield' => 1,
    ],
    'fax'          => [
        'label'      => 'LBL_LOCALCARRIER_FAX',
        'name'       => 'fax',
        'table'      => 'vtiger_localcarrier',
        'column'     => 'fax',
        'columntype' => 'VARCHAR(20)',
        'uitype'     => 11,
        'typeofdata' => 'V~O',
        'block'      => $block,
    ],
    'email'        => [
        'label'      => 'LBL_LOCALCARRIER_EMAIL',
        'name'       => 'email',
        'table'      => 'vtiger_localcarrier',
        'column'     => 'email',
        'columntype' => 'VARCHAR(200)',
        'uitype'     => 13,
        'typeofdata' => 'V~O',
        'block'      => $block,
    ],
    'website'      => [
        'label'      => 'LBL_LOCALCARRIER_WEBSITE',
        'name'       => 'website',
        'table'      => 'vtiger_localcarrier',
        'column'     => 'website',
        'columntype' => 'VARCHAR(255)',
        'uitype'     => 1,
        'typeofdata' => 'V~O',
        'block'      => $block,
    ],
    'mc_number'    => [
        'label'        => 'LBL_LOCALCARRIER_MC_NUMBER',
        'name'         => 'mc_number',
        'table'        => 'vtiger_localcarrier',
        'column'       => 'mc_number',
        'columntype'   => 'VARCHAR(255)',
        'uitype'       => 1,
        'typeofdata'   => 'V~O',
        'summaryfield' => 1,
        'block'        => $block,
    ],
    'dot_number'   => [
        'label'        => 'LBL_LOCALCARRIER_DOT_NUMBER',
        'name'         => 'dot_number',
        'table'        => 'vtiger_localcarrier',
        'column'       => 'dot_number',
        'columntype'   => 'VARCHAR(255)',
        'uitype'       => 1,
        'typeofdata'   => 'V~O',
        'summaryfield' => 1,
        'block'        => $block,
    ],
    'description'  => [
        'label'      => 'LBL_LOCALCARRIER_NOTES',
        'name'       => 'description',
        'table'      => 'vtiger_crmentity',
        'column'     => 'description',
        'uitype'     => 19,
        'typeofdata' => 'V~O',
        'block'      => $block,
    ],
    'createdtime'  => [
        'label'       => 'Created Time',
        'name'        => 'createdtime',
        'table'       => 'vtiger_crmentity',
        'column'      => 'createdtime',
        'uitype'      => 70,
        'typeofdata'  => 'T~O',
        'displaytype' => 2,
        'block'       => $admin_block,
    ],
    'modifiedtime' => [
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
$rField = addFields($fields, $localCarrier);
if ($newModule) {
    //only run this stuff if we created the module this pass.
    $filter1            = new Vtiger_Filter();
    $filter1->name      = 'All';
    $filter1->isdefault = true;
    $localCarrier->addFilter($filter1);
    $filter1
        ->addField($rField['carrier_name'], 1)
        ->addField($rField['agentid'], 2)
        ->addField($rField['phone1'], 3)
        ->addField($rField['email'], 4)
        ->addField($rField['mc_number'], 5)
        ->addField($rField['dot_number'], 6);
    $localCarrier->setDefaultSharing();
    $localCarrier->initWebservice();
    $accountInstance = Vtiger_Module::getInstance('AgentManager');
    $relationLabel   = 'Local Carriers';
    $accountInstance->setRelatedList($localCarrier, $relationLabel, ['Add']);
    ModTracker::enableTrackingForModule($localCarrier->id);
    $commentsModule = Vtiger_Module::getInstance('ModComments');
    $fieldInstance  = Vtiger_Field::getInstance('related_to', $commentsModule);
    $fieldInstance->setRelatedModules(['LocalCarrier']);
    $detailviewblock = ModComments::addWidgetTo('LocalCarrier');
}
function addFields($fields, $module)
{
    $returnFields = [];
    foreach ($fields as $field_name => $data) {
        $field0 = Vtiger_Field::getInstance($field_name, $module);
        if ($field0) {
            echo "<li>The $field_name field already exists</li><br>";
            $returnFields[$field_name] = $field0;
        } else {
            $field0               = new Vtiger_Field();
            $field0->label        = $data['label'];
            $field0->name         = $data['name'];
            $field0->table        = $data['table'];
            $field0->column       = $data['column'];
            $field0->columntype   = $data['columntype'];
            $field0->uitype       = $data['uitype'];
            $field0->typeofdata   = $data['typeofdata'];
            $field0->summaryfield = ($data['summaryfield']?1:0);
            $field0->displaytype  = ($data['displaytype']?$data['displaytype']:1);
            $data['block']->addField($field0);
            if ($data['setEntityIdentifier'] == 1) {
                $module->setEntityIdentifier($field0);
            }
            if (
                array_key_exists('setRelatedModules', $data) &&
                $data['setRelatedModules'] &&
                count($data['setRelatedModules']) > 0
            ) {
                $field0->setRelatedModules($data['setRelatedModules']);
            }
            $returnFields[$field_name] = $field0;
        }
    }

    return $returnFields;
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";