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




echo "<br><h2>Starting on adding orders_sequence column to vtiger_orders table</h2><br>\n";
echo '<ol>';
if (Vtiger_Utils::CheckTable('vtiger_orders')) {
    echo "<br><li>vtiger_orders exists! Adding new column.</li><br>";
    
    Vtiger_Utils::AddColumn('vtiger_orders', 'orders_sequence', 'INT(11)');
    
    echo "<br><li>orders_sequence column added successfully.</li><br>";
} else {
    echo "<br><li>vtiger_orders doesn't exist! No action taken.</li><br>";
}
echo '</ol>';
echo "<br><h2>Done adding orders_sequence column</h2><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";