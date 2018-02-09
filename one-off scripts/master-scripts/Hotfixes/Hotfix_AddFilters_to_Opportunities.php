<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

//grab all the instances we need
$opportunitiesInstance = Vtiger_Module::getInstance('Opportunities');
$field1 = Vtiger_Field::getInstance('potentialname', $opportunitiesInstance);
$field10 = Vtiger_Field::getInstance('amount', $opportunitiesInstance);
$field4 = Vtiger_Field::getInstance('related_to', $opportunitiesInstance);
$field20 = Vtiger_Field::getInstance('closingdate', $opportunitiesInstance);
$field11 = Vtiger_Field::getInstance('assigned_user_id', $opportunitiesInstance);
$field3 = Vtiger_Field::getInstance('contact_id', $opportunitiesInstance);
$field8 = Vtiger_Field::getInstance('sales_stage', $opportunitiesInstance);

if (!$opportunitiesInstance) {
    echo "\$opportunitiesInstance does not exist <br />";
} else {
}
if (!$field1) {
    echo "\$field1 does not exist <br />";
}
if (!$field10) {
    echo "\$field10 does not exist <br />";
}
if (!$field4) {
    echo "\$field4 does not exist <br />";
}
if (!$field20) {
    echo "\$field20 does not exist <br />";
}
if (!$field11) {
    echo "\$field11 does not exist <br />";
}
if (!$field3) {
    echo "\$field3 does not exist <br />";
}
if (!$field8) {
    echo "\$field8 does not exist <br />";
}

$filter1 = Vtiger_Filter::getInstance('Prospecting', $opportunitiesInstance);
if ($filter1) {
    echo "<br> Filter exists <br>";
    $filter1->delete();
}// else {
    echo "<br> Adding Filter : Prospecting <br>";
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'Prospecting';
    $filter1->isdefault = true;
    $opportunitiesInstance->addFilter($filter1);
    
    echo "addFilter call completed <br />";

    $filter1->addField($field8)->addField($field1)->addField($field10, 1)->addField($field4, 2)->addField($field20, 3)->addField($field11, 4)->addField($field3, 5)->addRule($field8, 'EQUALS', 'Prospecting');
    //fixing the filter rule to make it the same as it would be if made through the UI because of a bug in vtlib
    $filter = Vtiger_Filter::getInstance('Prospecting', $opportunitiesInstance);
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_cvadvfilter` SET `column_condition` = '' WHERE cvid = ".$filter->id);
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES ( 1, ".$filter->id.", 'and', '0')");
    echo "<br> Added Filter : Prospecting <br>";
//}

$filter2 = Vtiger_Filter::getInstance('Qualification', $opportunitiesInstance);
if ($filter2) {
    echo "<br> Filter exists <br>";
    $filter2->delete();
}// else {
    echo "<br> Adding Filter : Qualification <br>";
    $filter2 = new Vtiger_Filter();
    $filter2->name = 'Qualification';
    $filter2->isdefault = true;
    $opportunitiesInstance->addFilter($filter2);

    $filter2->addField($field8)->addField($field1)->addField($field10, 1)->addField($field4, 2)->addField($field20, 3)->addField($field11, 4)->addField($field3, 5)->addRule($field8, 'EQUALS', 'Qualification');
    //fixing the filter rule to make it the same as it would be if made through the UI because of a bug in vtlib
    $filter = Vtiger_Filter::getInstance('Qualification', $opportunitiesInstance);
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_cvadvfilter` SET `column_condition` = '' WHERE cvid = ".$filter->id);
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES ( 1, ".$filter->id.", 'and', '0')");
    echo "<br> Added Filter : Qualification <br>";
//}

$filter3 = Vtiger_Filter::getInstance('Needs Analysis', $opportunitiesInstance);
if ($filter3) {
    echo "<br> Filter exists <br>";
    $filter3->delete();
} //else {
    echo "<br> Adding Filter : Needs Analysis <br>";
    $filter3 = new Vtiger_Filter();
    $filter3->name = 'Needs Analysis';
    $filter3->isdefault = true;
    $opportunitiesInstance->addFilter($filter3);

    $filter3->addField($field8)->addField($field1)->addField($field10, 1)->addField($field4, 2)->addField($field20, 3)->addField($field11, 4)->addField($field3, 5)->addRule($field8, 'EQUALS', 'Needs Analysis');
    //fixing the filter rule to make it the same as it would be if made through the UI because of a bug in vtlib
    $filter = Vtiger_Filter::getInstance('Needs Analysis', $opportunitiesInstance);
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_cvadvfilter` SET `column_condition` = '' WHERE cvid = ".$filter->id);
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES ( 1, ".$filter->id.", 'and', '0')");
    echo "<br> Added Filter : Needs Analysis <br>";
//}

$filter4 = Vtiger_Filter::getInstance('Value Proposition', $opportunitiesInstance);
if ($filter4) {
    echo "<br> Filter exists <br>";
    $filter4->delete();
} //else {
    echo "<br> Adding Filter : Value Proposition <br>";
    $filter4 = new Vtiger_Filter();
    $filter4->name = 'Value Proposition';
    $filter4->isdefault = true;
    $opportunitiesInstance->addFilter($filter4);

    $filter4->addField($field8)->addField($field1)->addField($field10, 1)->addField($field4, 2)->addField($field20, 3)->addField($field11, 4)->addField($field3, 5)->addRule($field8, 'EQUALS', 'Value Proposition');
    //fixing the filter rule to make it the same as it would be if made through the UI because of a bug in vtlib
    $filter = Vtiger_Filter::getInstance('Value Proposition', $opportunitiesInstance);
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_cvadvfilter` SET `column_condition` = '' WHERE cvid = ".$filter->id);
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES ( 1, ".$filter->id.", 'and', '0')");
    echo "<br> Added Filter : Value Proposition <br>";
//}

$filter5 = Vtiger_Filter::getInstance('Id. Decision Makers', $opportunitiesInstance);
if ($filter5) {
    echo "<br> Filter exists <br>";
    $filter5->delete();
} //else {
    echo "<br> Adding Filter : Id. Decision Makers <br>";
    $filter5 = new Vtiger_Filter();
    $filter5->name = 'Id. Decision Makers';
    $filter5->isdefault = true;
    $opportunitiesInstance->addFilter($filter5);

    $filter5->addField($field8)->addField($field1)->addField($field10, 1)->addField($field4, 2)->addField($field20, 3)->addField($field11, 4)->addField($field3, 5)->addRule($field8, 'EQUALS', 'Id. Decision Makers');
    //fixing the filter rule to make it the same as it would be if made through the UI because of a bug in vtlib
    $filter = Vtiger_Filter::getInstance('Id. Decision Makers', $opportunitiesInstance);
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_cvadvfilter` SET `column_condition` = '' WHERE cvid = ".$filter->id);
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES ( 1, ".$filter->id.", 'and', '0')");
    echo "<br> Added Filter : Id. Decision Makers <br>";
//}

$filter6 = Vtiger_Filter::getInstance('Perception Analysis', $opportunitiesInstance);
if ($filter6) {
    echo "<br> Filter exists <br>";
    $filter6->delete();
} //else {
    echo "<br> Adding Filter : Perception Analysis <br>";
    $filter6 = new Vtiger_Filter();
    $filter6->name = 'Perception Analysis';
    $filter6->isdefault = true;
    $opportunitiesInstance->addFilter($filter6);

    $filter6->addField($field8)->addField($field1)->addField($field10, 1)->addField($field4, 2)->addField($field20, 3)->addField($field11, 4)->addField($field3, 5)->addRule($field8, 'EQUALS', 'Perception Analysis');
    //fixing the filter rule to make it the same as it would be if made through the UI because of a bug in vtlib
    $filter = Vtiger_Filter::getInstance('Perception Analysis', $opportunitiesInstance);
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_cvadvfilter` SET `column_condition` = '' WHERE cvid = ".$filter->id);
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES ( 1, ".$filter->id.", 'and', '0')");
    echo "<br> Added Filter : Perception Analysis <br>";
//}

$filter7 = Vtiger_Filter::getInstance('Proposal or Price Quote', $opportunitiesInstance);
if ($filter7) {
    echo "<br> Filter exists <br>";
    $filter7->delete();
} //else {
    echo "<br> Adding Filter : Proposal or Price Quote <br>";
    $filter7 = new Vtiger_Filter();
    $filter7->name = 'Proposal or Price Quote';
    $filter7->isdefault = true;
    $opportunitiesInstance->addFilter($filter7);

    $filter7->addField($field8)->addField($field1)->addField($field10, 1)->addField($field4, 2)->addField($field20, 3)->addField($field11, 4)->addField($field3, 5)->addRule($field8, 'EQUALS', 'Proposal or Price Quote');
    //fixing the filter rule to make it the same as it would be if made through the UI because of a bug in vtlib
    $filter = Vtiger_Filter::getInstance('Proposal or Price Quote', $opportunitiesInstance);
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_cvadvfilter` SET `column_condition` = '' WHERE cvid = ".$filter->id);
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES ( 1, ".$filter->id.", 'and', '0')");
    echo "<br> Added Filter : Proposal or Price Quote <br>";
//}

$filter8 = Vtiger_Filter::getInstance('Negotiation or Review', $opportunitiesInstance);
if ($filter8) {
    echo "<br> Filter exists <br>";
    $filter8->delete();
} //else {
    echo "<br> Adding Filter : Negotiation or Review <br>";
    $filter8 = new Vtiger_Filter();
    $filter8->name = 'Negotiation or Review';
    $filter8->isdefault = true;
    $opportunitiesInstance->addFilter($filter8);

    $filter8->addField($field8)->addField($field1)->addField($field10, 1)->addField($field4, 2)->addField($field20, 3)->addField($field11, 4)->addField($field3, 5)->addRule($field8, 'EQUALS', 'Negotiation or Review');
    //fixing the filter rule to make it the same as it would be if made through the UI because of a bug in vtlib
    $filter = Vtiger_Filter::getInstance('Negotiation or Review', $opportunitiesInstance);
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_cvadvfilter` SET `column_condition` = '' WHERE cvid = ".$filter->id);
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES ( 1, ".$filter->id.", 'and', '0')");
    echo "<br> Added Filter : Negotiation or Review <br>";
//}

$filter9 = Vtiger_Filter::getInstance('Closed Won', $opportunitiesInstance);
if ($filter9) {
    echo "<br> Filter exists <br>";
    $filter9->delete();
} //else {
    echo "<br> Adding Filter : Closed Won <br>";
    $filter9 = new Vtiger_Filter();
    $filter9->name = 'Closed Won';
    $filter9->isdefault = true;
    $opportunitiesInstance->addFilter($filter9);

    $filter9->addField($field8)->addField($field1)->addField($field10, 1)->addField($field4, 2)->addField($field20, 3)->addField($field11, 4)->addField($field3, 5)->addRule($field8, 'EQUALS', 'Closed Won');
    //fixing the filter rule to make it the same as it would be if made through the UI because of a bug in vtlib
    $filter = Vtiger_Filter::getInstance('Closed Won', $opportunitiesInstance);
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_cvadvfilter` SET `column_condition` = '' WHERE cvid = ".$filter->id);
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES ( 1, ".$filter->id.", 'and', '0')");
    echo "<br> Added Filter : Closed Won <br>";
//}

$filter10 = Vtiger_Filter::getInstance('Closed Lost', $opportunitiesInstance);
if ($filter10) {
    echo "<br> Filter exists <br>";
    $filter10->delete();
} //else {
    echo "<br> Adding Filter : Closed Lost <br>";
    $filter10 = new Vtiger_Filter();
    $filter10->name = 'Closed Lost';
    $filter10->isdefault = true;
    $opportunitiesInstance->addFilter($filter10);

    $filter10->addField($field8)->addField($field1)->addField($field10, 1)->addField($field4, 2)->addField($field20, 3)->addField($field11, 4)->addField($field3, 5)->addRule($field8, 'EQUALS', 'Closed Lost');
    //fixing the filter rule to make it the same as it would be if made through the UI because of a bug in vtlib
    $filter = Vtiger_Filter::getInstance('Closed Lost', $opportunitiesInstance);
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_cvadvfilter` SET `column_condition` = '' WHERE cvid = ".$filter->id);
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES ( 1, ".$filter->id.", 'and', '0')");
    echo "<br> Added Filter : Closed Lost <br>";
//}
echo "<br> Finished adding Sales Stage Filters to Opportunities.";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";