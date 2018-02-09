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

$module = Vtiger_Module::getInstance('Estimates');
$block = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATE_SERVICECHARGES', $module);
if ($block) {
    echo "<h3>The LBL_QUOTES_INTERSTATE_SERVICECHARGES block already exists</h3><br> \n";
} else {
    $block        = new Vtiger_Block();
    $block->label = 'LBL_QUOTES_INTERSTATE_SERVICECHARGES';
    $block->sequence = 6;
    $module->addBlock($block);
}

if (Vtiger_Utils::CheckTable('vtiger_quotes_inter_servchg')) {
    echo "<br>vtiger_quotes_inter_servchg already exists! No action taken<br>";
} else {
    echo "<li>creating vtiger_quotes_inter_servchg </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_inter_servchg',
                              '(
							    quoteid INT(19),
								serviceid INT(19),
								is_dest TINYINT,
								service_description VARCHAR(250),
								always_used TINYINT,
								charge DECIMAL(10,2),
								minimum INT(19),
								service_weight INT(19),
								applied TINYINT,
								PRIMARY KEY(quoteid,serviceid,is_dest)
								)', true);
    echo "<br>vtiger_quotes_inter_servchg table created successfully<br>";
}


echo "<br><h1>Done adding vtiger_quotes_inter_servchg table</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";