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

require_once 'vtlib/Vtiger/Module.php';

$moduleAgents = Vtiger_Module::getInstance('Agents');

$block = Vtiger_Block::getInstance('LBL_AGENTS_INFORMATION', $moduleAgents);
if ($block) {
    echo "<br> The LBL_AGENTS_INFORMATION block already exists in Agents <br>";
} else {
    $block = new Vtiger_Block();
    $block->label = 'LBL_AGENTS_INFORMATION';
    $moduleAgents->addBlock($block);
}

//Status
$field = Vtiger_Field::getInstance('agents_status', $moduleAgents);
if ($field) {
    echo "<br> The agents_status field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_STATUS';
    $field->name = 'agents_status';
    $field->table = 'vtiger_agents';
    $field->column = 'agents_status';
    $field->columntype = 'varchar(10)';
    $field->uitype = 16;
    $field->typeofdata = 'V~M';
    $field->defaultvalue = 'Active';

    $block->addField($field);
    $field->setPicklistValues(['Active', 'Inactive', 'On Hold']);
}

//Grade
$field = Vtiger_Field::getInstance('agents_grade', $moduleAgents);
if ($field) {
    echo "<br> The agents_grade field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_GRADE';
    $field->name = 'agents_grade';
    $field->table = 'vtiger_agents';
    $field->column = 'agents_grade';
    $field->columntype = 'varchar(10)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';

    $block->addField($field);
    $field->setPicklistValues(['A', 'B', 'C']);
}

//Customer Number
$field = Vtiger_Field::getInstance('agents_custnum', $moduleAgents);
if ($field) {
    echo "<br> The agents_custnum field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_CUSTNUM';
    $field->name = 'agents_custnum';
    $field->table = 'vtiger_agents';
    $field->column = 'agents_custnum';
    $field->columntype = 'varchar(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';

    $block->addField($field);
}

//Vendor Number
$field = Vtiger_Field::getInstance('agents_vendornum', $moduleAgents);
if ($field) {
    echo "<br> The agents_vendornum field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_VENDORNUM';
    $field->name = 'agents_vendornum';
    $field->table = 'vtiger_agents';
    $field->column = 'agents_vendornum';
    $field->columntype = 'varchar(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';

    $block->addField($field);
}

//Agent Website
$field = Vtiger_Field::getInstance('agents_website', $moduleAgents);
if ($field) {
    echo "<br> The agents_website field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_WEBSITE';
    $field->name = 'agents_website';
    $field->table = 'vtiger_agents';
    $field->column = 'agents_website';
    $field->columntype = 'varchar(255)';
    $field->uitype = 17;
    $field->typeofdata = 'V~O';

    $block->addField($field);
}

//Service Radius
$field = Vtiger_Field::getInstance('agents_servradius', $moduleAgents);
if ($field) {
    echo "<br> The agents_servradius field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_SERVRADIUS';
    $field->name = 'agents_servradius';
    $field->table = 'vtiger_agents';
    $field->column ='agents_servradius';
    $field->columntype = 'INT(10)';
    $field->uitype = 7;
    $field->typeofdata = 'I~O';

    $block->addField($field);
}

$block = Vtiger_Block::getInstance('LBL_AGENTS_WAREINFO', $moduleAgents);
if ($block) {
    echo "<br> The LBL_AGENTS_WAREINFO block already exists in Agents <br>";
} else {
    $block = new Vtiger_Block();
    $block->label = 'LBL_AGENTS_WAREINFO';
    $moduleAgents->addBlock($block);
}

//After Hours Phone
$field = Vtiger_Field::getInstance('agents_afterhrphone', $moduleAgents);
if ($field) {
    echo "<br> The agents_afterhrphone field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_AFTERHRPHONE';
    $field->name = 'agents_afterhrphone';
    $field->table = 'vtiger_agents';
    $field->column ='agents_afterhrphone';
    $field->columntype = 'varchar(30)';
    $field->uitype = 11;
    $field->typeofdata = 'V~O';

    $block->addField($field);
}

//Scale
$field = Vtiger_Field::getInstance('agents_scale', $moduleAgents);
if ($field) {
    echo "<br> The agents_scale field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_SCALE';
    $field->name = 'agents_scale';
    $field->table = 'vtiger_agents';
    $field->column = 'agents_scale';
    $field->columntype = 'varchar(10)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';

    $block->addField($field);
    $field->setPicklistValues(['Yes', 'No']);
}

//Warehouse Size
$field = Vtiger_Field::getInstance('agents_waresize', $moduleAgents);
if ($field) {
    echo "<br> The agents_waresize field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_WARESIZE';
    $field->name = 'agents_waresize';
    $field->table = 'vtiger_agents';
    $field->column ='agents_waresize';
    $field->columntype = 'INT(10)';
    $field->uitype = 7;
    $field->typeofdata = 'I~O';

    $block->addField($field);
}

//Warehouse Military Approved
$field = Vtiger_Field::getInstance('agents_waremilapproved', $moduleAgents);
if ($field) {
    echo "<br> The agents_waremilapproved field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_WAREMILAPPROVED';
    $field->name = 'agents_waremilapproved';
    $field->table = 'vtiger_agents';
    $field->column = 'agents_waremilapproved';
    $field->columntype = 'varchar(10)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';

    $block->addField($field);
    $field->setPicklistValues(['Yes', 'No']);
}

//Warehouse Address
$field = Vtiger_Field::getInstance('agents_wareaddress1', $moduleAgents);
if ($field) {
    echo "<br> The agents_wareaddress1 field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_WAREADDRESS1';
    $field->name = 'agents_wareaddress1';
    $field->table = 'vtiger_agents';
    $field->column ='agents_wareaddress1';
    $field->columntype = 'varchar(100)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';

    $block->addField($field);
}

//Warehouse Address 2
$field = Vtiger_Field::getInstance('agents_wareaddress2', $moduleAgents);
if ($field) {
    echo "<br> The agents_wareaddress2 field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_WAREADDRESS2';
    $field->name = 'agents_wareaddress2';
    $field->table = 'vtiger_agents';
    $field->column ='agents_wareaddress2';
    $field->columntype = 'varchar(100)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';

    $block->addField($field);
}

//Warehouse City
$field = Vtiger_Field::getInstance('agents_warecity', $moduleAgents);
if ($field) {
    echo "<br> The agents_warecity field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_WARECITY';
    $field->name = 'agents_warecity';
    $field->table = 'vtiger_agents';
    $field->column ='agents_warecity';
    $field->columntype = 'varchar(100)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';

    $block->addField($field);
}

//Warehouse State
$field = Vtiger_Field::getInstance('agents_warestate', $moduleAgents);
if ($field) {
    echo "<br> The agents_warestate field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_WARESTATE';
    $field->name = 'agents_warestate';
    $field->table = 'vtiger_agents';
    $field->column ='agents_warestate';
    $field->columntype = 'varchar(100)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';

    $block->addField($field);
}

//Warehouse Zip
$field = Vtiger_Field::getInstance('agents_warezip', $moduleAgents);
if ($field) {
    echo "<br> The agents_warezip field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_WAREZIP';
    $field->name = 'agents_warezip';
    $field->table = 'vtiger_agents';
    $field->column ='agents_warezip';
    $field->columntype = 'varchar(100)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';

    $block->addField($field);
}

//Warehouse Country
$field = Vtiger_Field::getInstance('agents_warecountry', $moduleAgents);
if ($field) {
    echo "<br> The agents_warecountry field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_WARECOUNTRY';
    $field->name = 'agents_warecountry';
    $field->table = 'vtiger_agents';
    $field->column ='agents_warecountry';
    $field->columntype = 'varchar(100)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';

    $block->addField($field);
}

//Open Time MF
$field = Vtiger_Field::getInstance('agents_opentimemf', $moduleAgents);
if ($field) {
    echo "<br> The agents_opentimemf field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_OPENTIMEMF';
    $field->name = 'agents_opentimemf';
    $field->table = 'vtiger_agents';
    $field->column ='agents_opentimemf';
    $field->columntype = 'varchar(100)';
    $field->uitype = 14;
    $field->typeofdata = 'T~O';

    $block->addField($field);
}

//Closed Time MF
$field = Vtiger_Field::getInstance('agents_closetimemf', $moduleAgents);
if ($field) {
    echo "<br> The agents_closetimemf field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_CLOSETIMEMF';
    $field->name = 'agents_closetimemf';
    $field->table = 'vtiger_agents';
    $field->column ='agents_closetimemf';
    $field->columntype = 'varchar(100)';
    $field->uitype = 14;
    $field->typeofdata = 'T~O';

    $block->addField($field);
}

//Open Time Sat
$field = Vtiger_Field::getInstance('agents_opentimesat', $moduleAgents);
if ($field) {
    echo "<br> The agents_opentimesat field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_OPENTIMESAT';
    $field->name = 'agents_opentimesat';
    $field->table = 'vtiger_agents';
    $field->column ='agents_opentimesat';
    $field->columntype = 'varchar(100)';
    $field->uitype = 14;
    $field->typeofdata = 'T~O';

    $block->addField($field);
}

//Closed Time Sat
$field = Vtiger_Field::getInstance('agents_closetimesat', $moduleAgents);
if ($field) {
    echo "<br> The agents_closetimesat field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_CLOSETIMESAT';
    $field->name = 'agents_closetimesat';
    $field->table = 'vtiger_agents';
    $field->column ='agents_closetimesat';
    $field->columntype = 'varchar(100)';
    $field->uitype = 14;
    $field->typeofdata = 'T~O';

    $block->addField($field);
}

//Open Time Sun
$field = Vtiger_Field::getInstance('agents_opentimesun', $moduleAgents);
if ($field) {
    echo "<br> The agents_opentimesun field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_OPENTIMESUN';
    $field->name = 'agents_opentimesun';
    $field->table = 'vtiger_agents';
    $field->column ='agents_opentimesun';
    $field->columntype = 'varchar(100)';
    $field->uitype = 14;
    $field->typeofdata = 'T~O';

    $block->addField($field);
}

//Closed Time Sun
$field = Vtiger_Field::getInstance('agents_closetimesun', $moduleAgents);
if ($field) {
    echo "<br> The agents_closetimesun field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_CLOSETIMESUN';
    $field->name = 'agents_closetimesun';
    $field->table = 'vtiger_agents';
    $field->column ='agents_closetimesun';
    $field->columntype = 'varchar(100)';
    $field->uitype = 14;
    $field->typeofdata = 'T~O';

    $block->addField($field);
}

//Warehouse Info
$field = Vtiger_Field::getInstance('agents_wareinfo', $moduleAgents);
if ($field) {
    echo "<br> The agents_wareinfo field already exists in Agents <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_WAREINFO';
    $field->name = 'agents_wareinfo';
    $field->table = 'vtiger_agents';
    $field->column ='agents_wareinfo';
    $field->columntype = 'text';
    $field->uitype = 19;
    $field->typeofdata = 'V~O';

    $block->addField($field);
}

$block = Vtiger_Block::getInstance('LBL_AGENTS_RECORDUPDATE', $moduleAgents);
if ($block) {
    echo "<br> The LBL_AGENTS_RECORDUPDATE block already exists in Vehicles <br>";
} else {
    $block = new Vtiger_Block();
    $block->label = 'LBL_AGENTS_RECORDUPDATE';
    $moduleAgents->addBlock($block);
}

//Created By
$field = Vtiger_Field::getInstance('createdby', $moduleAgents);
if ($field) {
    echo "<li>The createdby field already exists</li><br> \n";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTS_CREATEDBY';
    $field->name = 'createdby';
    $field->table = 'vtiger_crmentity';
    $field->column = 'smcreatorid';
    $field->uitype = 52;
    $field->typeofdata = 'V~O';
    $field->displaytype = 2;

    $block->addField($field);
}

//Add Block to Remove unwanted Fields
$block = Vtiger_Block::getInstance('LBL_AGENTS_FIELDS_TO_REMOVE', $moduleAgents);
if ($block) {
    echo "<br> The LBL_AGENTS_FIELDS_TO_REMOVE block already exists in Agents <br>";
} else {
    $block = new Vtiger_Block();
    $block->label = 'LBL_AGENTS_FIELDS_TO_REMOVE';
    $moduleAgents->addBlock($block);
}

//Remove Related List Trips from Agents Module
$moduleRel = Vtiger_Module::getInstance('Trips');
if ($moduleRel) {
    $moduleAgents->unsetRelatedList($moduleRel, 'LBL_TRIPS', 'get_dependents_list');
    echo "<h2>Trips module successfully removed from Agents Related List</h2><br>";
} else {
    echo "<h2>Unable to unset Related List Trips from Agents as the Trips module does not exist</h2><br>";
}

//Remove Related List Branch Defaults from Agents Module
$moduleRel = Vtiger_Module::getInstance('BranchDefaults');
if ($moduleRel) {
    $moduleAgents->unsetRelatedList($moduleRel, 'Branch Defaults', 'get_related_list');
    echo "<h2>Branch Defaults module successfully removed from Agents Related List</h2><br>";
} else {
    echo "<h2>Unable to unset Related List Branch Defaults from Agents as the Branch Defaults module does not exist</h2><br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";