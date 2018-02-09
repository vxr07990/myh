<?php

//Hotfix_LDActuals_AddCustomTables.php

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "SKIPPING: " . __FILE__ . "<br />\n";
        return;
    }
}
print "RUNNING: " . __FILE__ . "<br />\n";

$moduleName = 'LocalDispatchActuals';

$moduleInstance = Vtiger_Module::getInstance($moduleName);
if ($moduleInstance) {
    echo "Module already present - choose a different name.";
   
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $moduleName;
    $moduleInstance->parent = '';
    $moduleInstance->save();

    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = 'OPERATIONS_TAB' WHERE name = 'LocalDispatchActuals'");
    echo "OK\n";
}



$db = PearDatabase::getInstance();
echo "<br /> Adding vtiger_orderstask_cpus table (Local Dispatch Actual) <br />";

$db->pquery("CREATE TABLE IF NOT EXISTS `vtiger_orderstask_cpus` (
  `id` int(11) NOT NULL,
  `orderstaskid` int(19) NOT NULL,
  `jsonCPU` text NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;", array());

echo "<br /> Done!  <br />";

echo "<br /> Adding vtiger_orderstask_equipments table (Local Dispatch Actual) <br />";

$db->pquery("CREATE TABLE IF NOT EXISTS `vtiger_orderstask_equipments` (
  `id` int(11) NOT NULL,
  `orderstaskid` int(19) NOT NULL,
  `jsonEquipment` text NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;", array());

echo "<br /> Done!  <br />";