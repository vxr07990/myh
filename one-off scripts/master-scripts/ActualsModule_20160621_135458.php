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


//*/
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
//*/

$isNew = false;
$module = Vtiger_Module::getInstance('Actuals');
if (!$module) {
    $module = new Vtiger_Module();
    $module->name = 'Actuals';
    $module->save();
    $module->initTables();
    ModTracker::enableTrackingForModule($module->id);
    $isNew = true;
} else {
    echo "<br>Actuals module already exists<br>";
}

$block190 = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $module);
if ($block190) {
    echo "<br> The LBL_QUOTE_INFORMATION block already exists in ".$module->name." <br>";
} else {
    $block190 = new Vtiger_Block();
    $block190->label = 'LBL_QUOTE_INFORMATION';
    $module->addBlock($block190);
}

$block191 = Vtiger_Block::getInstance('LBL_QUOTES_CONTACTDETAILS', $module);
if ($block191) {
    echo "<br> The LBL_QUOTES_CONTACTDETAILS block already exists in ".$module->name." <br>";
} else {
    $block191 = new Vtiger_Block();
    $block191->label = 'LBL_QUOTES_CONTACTDETAILS';
    $module->addBlock($block191);
}

$block192 = Vtiger_Block::getInstance('LBL_ADDRESS_INFORMATION', $module);
if ($block192) {
    echo "<br> The LBL_ADDRESS_INFORMATION block already exists in ".$module->name." <br>";
} else {
    $block192 = new Vtiger_Block();
    $block192->label = 'LBL_ADDRESS_INFORMATION';
    $module->addBlock($block192);
}

$block193 = Vtiger_Block::getInstance('LBL_QUOTES_LOCALMOVEDETAILS', $module);
if ($block193) {
    echo "<br> The LBL_QUOTES_LOCALMOVEDETAILS block already exists in ".$module->name." <br>";
} else {
    $block193 = new Vtiger_Block();
    $block193->label = 'LBL_QUOTES_LOCALMOVEDETAILS';
    $module->addBlock($block193);
}

$block194 = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $module);
if ($block194) {
    echo "<br> The LBL_QUOTES_INTERSTATEMOVEDETAILS block already exists in ".$module->name." <br>";
} else {
    $block194 = new Vtiger_Block();
    $block194->label = 'LBL_QUOTES_INTERSTATEMOVEDETAILS';
    $module->addBlock($block194);
}

$block195 = Vtiger_Block::getInstance('LBL_QUOTES_COMMERCIALMOVEDETAILS', $module);
if ($block195) {
    echo "<br> The LBL_QUOTES_COMMERCIALMOVEDETAILS block already exists in ".$module->name." <br>";
} else {
    $block195 = new Vtiger_Block();
    $block195->label = 'LBL_QUOTES_COMMERCIALMOVEDETAILS';
    $module->addBlock($block195);
}

$block196 = Vtiger_Block::getInstance('LBL_QUOTES_SITDETAILS', $module);
if ($block196) {
    echo "<br> The LBL_QUOTES_SITDETAILS block already exists in ".$module->name." <br>";
} else {
    $block196 = new Vtiger_Block();
    $block196->label = 'LBL_QUOTES_SITDETAILS';
    $module->addBlock($block196);
}

$block197 = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $module);
if ($block197) {
    echo "<br> The LBL_QUOTES_ACCESSORIALDETAILS block already exists in ".$module->name." <br>";
} else {
    $block197 = new Vtiger_Block();
    $block197->label = 'LBL_QUOTES_ACCESSORIALDETAILS';
    $module->addBlock($block197);
}

$block198 = Vtiger_Block::getInstance('LBL_TERMS_INFORMATION', $module);
if ($block198) {
    echo "<br> The LBL_TERMS_INFORMATION block already exists in ".$module->name." <br>";
} else {
    $block198 = new Vtiger_Block();
    $block198->label = 'LBL_TERMS_INFORMATION';
    $module->addBlock($block198);
}

$block199 = Vtiger_Block::getInstance('LBL_DESCRIPTION_INFORMATION', $module);
if ($block199) {
    echo "<br> The LBL_DESCRIPTION_INFORMATION block already exists in ".$module->name." <br>";
} else {
    $block199 = new Vtiger_Block();
    $block199->label = 'LBL_DESCRIPTION_INFORMATION';
    $module->addBlock($block199);
}

$block200 = Vtiger_Block::getInstance('LBL_ITEM_DETAILS', $module);
if ($block200) {
    echo "<br> The LBL_ITEM_DETAILS block already exists in ".$module->name." <br>";
} else {
    $block200 = new Vtiger_Block();
    $block200->label = 'LBL_ITEM_DETAILS';
    $module->addBlock($block200);
}

$block280 = Vtiger_Block::getInstance('LBL_ESTIMATES_EXTRASTOPS', $module);
if ($block280) {
    echo "<br> The LBL_ESTIMATES_EXTRASTOPS block already exists in ".$module->name." <br>";
} else {
    $block280 = new Vtiger_Block();
    $block280->label = 'LBL_ESTIMATES_EXTRASTOPS';
    $module->addBlock($block280);
}

$block282 = Vtiger_Block::getInstance('LBL_QUOTES_VALUATION', $module);
if ($block282) {
    echo "<br> The LBL_QUOTES_VALUATION block already exists in ".$module->name." <br>";
} else {
    $block282 = new Vtiger_Block();
    $block282->label = 'LBL_QUOTES_VALUATION';
    $module->addBlock($block282);
}

$block283 = Vtiger_Block::getInstance('LBL_QUOTES_ELEVATOR', $module);
if ($block283) {
    echo "<br> The LBL_QUOTES_ELEVATOR block already exists in ".$module->name." <br>";
} else {
    $block283 = new Vtiger_Block();
    $block283->label = 'LBL_QUOTES_ELEVATOR';
    $module->addBlock($block283);
}

$block284 = Vtiger_Block::getInstance('LBL_QUOTES_STAIR', $module);
if ($block284) {
    echo "<br> The LBL_QUOTES_STAIR block already exists in ".$module->name." <br>";
} else {
    $block284 = new Vtiger_Block();
    $block284->label = 'LBL_QUOTES_STAIR';
    $module->addBlock($block284);
}

$block285 = Vtiger_Block::getInstance('LBL_QUOTES_LONGCARRY', $module);
if ($block285) {
    echo "<br> The LBL_QUOTES_LONGCARRY block already exists in ".$module->name." <br>";
} else {
    $block285 = new Vtiger_Block();
    $block285->label = 'LBL_QUOTES_LONGCARRY';
    $module->addBlock($block285);
}

$block286 = Vtiger_Block::getInstance('LBL_ESTIMATES_APPLIANCE', $module);
if ($block286) {
    echo "<br> The LBL_ESTIMATES_APPLIANCE block already exists in ".$module->name." <br>";
} else {
    $block286 = new Vtiger_Block();
    $block286->label = 'LBL_ESTIMATES_APPLIANCE';
    $module->addBlock($block286);
}

$block287 = Vtiger_Block::getInstance('LBL_QUOTES_SITDETAILS2', $module);
if ($block287) {
    echo "<br> The LBL_QUOTES_SITDETAILS2 block already exists in ".$module->name." <br>";
} else {
    $block287 = new Vtiger_Block();
    $block287->label = 'LBL_QUOTES_SITDETAILS2';
    $module->addBlock($block287);
}

$block288 = Vtiger_Block::getInstance('LBL_SPACE_RESERVATION', $module);
if ($block288) {
    echo "<br> The LBL_SPACE_RESERVATION block already exists in ".$module->name." <br>";
} else {
    $block288 = new Vtiger_Block();
    $block288->label = 'LBL_SPACE_RESERVATION';
    $module->addBlock($block288);
}

$block340 = Vtiger_Block::getInstance('LBL_QUOTES_TRANSPORTATIONPRICING', $module);
if ($block340) {
    echo "<br> The LBL_QUOTES_TRANSPORTATIONPRICING block already exists in ".$module->name." <br>";
} else {
    $block340 = new Vtiger_Block();
    $block340->label = 'LBL_QUOTES_TRANSPORTATIONPRICING';
    $module->addBlock($block340);
}

$field1049 = Vtiger_Field::getInstance('subject', $module);
if ($field1049) {
    echo "<br> Field 'subject' is already present <br>";
    //Make sure default value is set correctly
    $db->pquery("UPDATE `vtiger_field` SET defaultvalue='Actuals' WHERE fieldid=?", [$field1049->id]);
} else {
    $field1049 = new Vtiger_Field();
    $field1049->label = 'LBL_QUOTES_SUBJECT';
    $field1049->name = 'subject';
    $field1049->table = 'vtiger_quotes';
    $field1049->column = 'subject';
    $field1049->columntype = 'varchar(100)';
    $field1049->uitype = 2;
    $field1049->typeofdata = 'V~O';
    $field1049->displaytype = 1;
    $field1049->presence = 2;
    $field1049->defaultvalue = 'Actual';
    $field1049->quickcreate = 1;
    $field1049->summaryfield = 0;

    $block190->addField($field1049);

    $module->setEntityIdentifier($field1049);
}

$field1050 = Vtiger_Field::getInstance('potential_id', $module);
if ($field1050) {
    echo "<br> Field 'potential_id' is already present <br>";
} else {
    $field1050 = new Vtiger_Field();
    $field1050->label = 'LBL_QUOTES_POTENTIALNAME';
    $field1050->name = 'potential_id';
    $field1050->table = 'vtiger_quotes';
    $field1050->column = 'potentialid';
    $field1050->columntype = 'int(19)';
    $field1050->uitype = 10;
    $field1050->typeofdata = 'I~O';
    $field1050->displaytype = 1;
    $field1050->presence = 2;
    $field1050->defaultvalue = '';
    $field1050->quickcreate = 1;
    $field1050->summaryfield = 0;

    $block190->addField($field1050);

    $field1050->setRelatedModules(['Opportunities']);
}

$field1051 = Vtiger_Field::getInstance('quote_no', $module);
if ($field1051) {
    echo "<br> Field 'quote_no' is already present <br>";
} else {
    $field1051 = new Vtiger_Field();
    $field1051->label = 'LBL_QUOTES_QUOTENUMBER';
    $field1051->name = 'quote_no';
    $field1051->table = 'vtiger_quotes';
    $field1051->column = 'quote_no';
    $field1051->columntype = 'varchar(100)';
    $field1051->uitype = 4;
    $field1051->typeofdata = 'V~M';
    $field1051->displaytype = 1;
    $field1051->presence = 2;
    $field1051->defaultvalue = '';
    $field1051->quickcreate = 1;
    $field1051->summaryfield = 0;

    $block190->addField($field1051);
    $entity = new CRMEntity();
    $entity->setModuleSeqNumber('configure', $module->name, 'ACT', 1);
}

$field1052 = Vtiger_Field::getInstance('quotestage', $module);
if ($field1052) {
    echo "<br> Field 'quotestage' is already present <br>";
} else {
    $field1052 = new Vtiger_Field();
    $field1052->label = 'LBL_QUOTES_QUOTESTAGE';
    $field1052->name = 'quotestage';
    $field1052->table = 'vtiger_quotes';
    $field1052->column = 'quotestage';
    $field1052->columntype = 'varchar(200)';
    $field1052->uitype = 16;
    $field1052->typeofdata = 'V~M';
    $field1052->displaytype = 1;
    $field1052->presence = 2;
    $field1052->defaultvalue = 'Created';
    $field1052->quickcreate = 0;
    $field1052->summaryfield = 0;

    $block190->addField($field1052);
}

$field1053 = Vtiger_Field::getInstance('validtill', $module);
if ($field1053) {
    echo "<br> Field 'validtill' is already present <br>";
} else {
    $field1053 = new Vtiger_Field();
    $field1053->label = 'LBL_QUOTES_VALIDUTILL';
    $field1053->name = 'validtill';
    $field1053->table = 'vtiger_quotes';
    $field1053->column = 'validtill';
    $field1053->columntype = 'date';
    $field1053->uitype = 5;
    $field1053->typeofdata = 'D~O';
    $field1053->displaytype = 1;
    $field1053->presence = 2;
    $field1053->defaultvalue = '';
    $field1053->quickcreate = 1;
    $field1053->summaryfield = 0;

    $block190->addField($field1053);
}

$field1054 = Vtiger_Field::getInstance('contact_id', $module);
if ($field1054) {
    echo "<br> Field 'contact_id' is already present <br>";
} else {
    $field1054 = new Vtiger_Field();
    $field1054->label = 'LBL_QUOTES_CONTACTNAME';
    $field1054->name = 'contact_id';
    $field1054->table = 'vtiger_quotes';
    $field1054->column = 'contactid';
    $field1054->columntype = 'int(19)';
    $field1054->uitype = 57;
    $field1054->typeofdata = 'V~O';
    $field1054->displaytype = 1;
    $field1054->presence = 2;
    $field1054->defaultvalue = '';
    $field1054->quickcreate = 1;
    $field1054->summaryfield = 0;

    $block190->addField($field1054);
}

$field1055 = Vtiger_Field::getInstance('account_id', $module);
if ($field1055) {
    echo "<br> Field 'account_id' is already present <br>";
} else {
    $field1055 = new Vtiger_Field();
    $field1055->label = 'LBL_QUOTES_ACCOUNTNAME';
    $field1055->name = 'account_id';
    $field1055->table = 'vtiger_quotes';
    $field1055->column = 'accountid';
    $field1055->columntype = 'int(19)';
    $field1055->uitype = 73;
    $field1055->typeofdata = 'I~O';
    $field1055->displaytype = 1;
    $field1055->presence = 2;
    $field1055->defaultvalue = '';
    $field1055->quickcreate = 1;
    $field1055->summaryfield = 0;

    $block190->addField($field1055);
}

$field1056 = Vtiger_Field::getInstance('assigned_user_id', $module);
if ($field1056) {
    echo "<br> Field 'assigned_user_id' is already present <br>";
} else {
    $field1056 = new Vtiger_Field();
    $field1056->label = 'LBL_QUOTES_ASSIGNEDTO';
    $field1056->name = 'assigned_user_id';
    $field1056->table = 'vtiger_crmentity';
    $field1056->column = 'smownerid';
    //$field1056->columntype = 'int(19)';
    $field1056->uitype = 53;
    $field1056->typeofdata = 'V~M';
    $field1056->displaytype = 1;
    $field1056->presence = 2;
    $field1056->defaultvalue = '';
    $field1056->quickcreate = 0;
    $field1056->summaryfield = 0;

    $block190->addField($field1056);
}

$field1057 = Vtiger_Field::getInstance('createdtime', $module);
if ($field1057) {
    echo "<br> Field 'createdtime' is already present <br>";
} else {
    $field1057 = new Vtiger_Field();
    $field1057->label = 'LBL_QUOTES_CREATEDTIME';
    $field1057->name = 'createdtime';
    $field1057->table = 'vtiger_crmentity';
    $field1057->column = 'createdtime';
    //$field1057->columntype = 'datetime';
    $field1057->uitype = 70;
    $field1057->typeofdata = 'DT~O';
    $field1057->displaytype = 2;
    $field1057->presence = 2;
    $field1057->defaultvalue = '';
    $field1057->quickcreate = 1;
    $field1057->summaryfield = 0;

    $block190->addField($field1057);
}

$field1058 = Vtiger_Field::getInstance('modifiedtime', $module);
if ($field1058) {
    echo "<br> Field 'modifiedtime' is already present <br>";
} else {
    $field1058 = new Vtiger_Field();
    $field1058->label = 'LBL_QUOTES_MODIFIEDTIME';
    $field1058->name = 'modifiedtime';
    $field1058->table = 'vtiger_crmentity';
    $field1058->column = 'modifiedtime';
    //$field1058->columntype = 'datetime';
    $field1058->uitype = 70;
    $field1058->typeofdata = 'DT~O';
    $field1058->displaytype = 2;
    $field1058->presence = 2;
    $field1058->defaultvalue = '';
    $field1058->quickcreate = 1;
    $field1058->summaryfield = 0;

    $block190->addField($field1058);
}

$field1059 = Vtiger_Field::getInstance('business_line_est', $module);
if ($field1059) {
    echo "<br> Field 'business_line_est' is already present <br>";
} else {
    $field1059 = new Vtiger_Field();
    $field1059->label = 'LBL_QUOTES_BUSINESSLINE';
    $field1059->name = 'business_line_est';
    $field1059->table = 'vtiger_quotescf';
    $field1059->column = 'business_line_est';
    $field1059->columntype = 'varchar(200)';
    $field1059->uitype = 16;
    $field1059->typeofdata = 'V~O';
    $field1059->displaytype = 1;
    $field1059->presence = 2;
    $field1059->defaultvalue = '';
    $field1059->quickcreate = 1;
    $field1059->summaryfield = 0;

    $block190->addField($field1059);
}

$field1060 = Vtiger_Field::getInstance('is_primary', $module);
if ($field1060) {
    echo "<br> Field 'is_primary' is already present <br>";
} else {
    $field1060 = new Vtiger_Field();
    $field1060->label = 'LBL_QUOTES_ISPRIMARY';
    $field1060->name = 'is_primary';
    $field1060->table = 'vtiger_quotes';
    $field1060->column = 'is_primary';
    $field1060->columntype = 'varchar(3)';
    $field1060->uitype = 56;
    $field1060->typeofdata = 'C~O';
    $field1060->displaytype = 1;
    $field1060->presence = 2;
    $field1060->defaultvalue = '';
    $field1060->quickcreate = 1;
    $field1060->summaryfield = 0;

    $block190->addField($field1060);
}

$field1061 = Vtiger_Field::getInstance('orders_id', $module);
if ($field1061) {
    echo "<br> Field 'orders_id' is already present <br>";
} else {
    $field1061 = new Vtiger_Field();
    $field1061->label = 'LBL_QUOTES_ORDERSID';
    $field1061->name = 'orders_id';
    $field1061->table = 'vtiger_quotes';
    $field1061->column = 'orders_id';
    $field1061->columntype = 'int(19)';
    $field1061->uitype = 10;
    $field1061->typeofdata = 'V~O';
    $field1061->displaytype = 1;
    $field1061->presence = 2;
    $field1061->defaultvalue = '';
    $field1061->quickcreate = 1;
    $field1061->summaryfield = 0;

    $block190->addField($field1061);

    $field1061->setRelatedModules(['Orders']);
}

$field1062 = Vtiger_Field::getInstance('pre_tax_total', $module);
if ($field1062) {
    echo "<br> Field 'pre_tax_total' is already present <br>";
} else {
    $field1062 = new Vtiger_Field();
    $field1062->label = 'LBL_QUOTES_PRETAXTOTAL';
    $field1062->name = 'pre_tax_total';
    $field1062->table = 'vtiger_quotes';
    $field1062->column = 'pre_tax_total';
    $field1062->columntype = 'decimal(25,8)';
    $field1062->uitype = 72;
    $field1062->typeofdata = 'N~O';
    $field1062->displaytype = 3;
    $field1062->presence = 2;
    $field1062->defaultvalue = '';
    $field1062->quickcreate = 1;
    $field1062->summaryfield = 0;

    $block190->addField($field1062);
}

$field1063 = Vtiger_Field::getInstance('modifiedby', $module);
if ($field1063) {
    echo "<br> Field 'modifiedby' is already present <br>";
} else {
    $field1063 = new Vtiger_Field();
    $field1063->label = 'LBL_QUOTES_LASTMODIFIEDBY';
    $field1063->name = 'modifiedby';
    $field1063->table = 'vtiger_crmentity';
    $field1063->column = 'modifiedby';
    //$field1063->columntype = 'int(19)';
    $field1063->uitype = 52;
    $field1063->typeofdata = 'V~O';
    $field1063->displaytype = 3;
    $field1063->presence = 2;
    $field1063->defaultvalue = '';
    $field1063->quickcreate = 1;
    $field1063->summaryfield = 0;

    $block190->addField($field1063);
}

$field1064 = Vtiger_Field::getInstance('conversion_rate', $module);
if ($field1064) {
    echo "<br> Field 'conversion_rate' is already present <br>";
} else {
    $field1064 = new Vtiger_Field();
    $field1064->label = 'LBL_QUOTES_CONVERSIONRATE';
    $field1064->name = 'conversion_rate';
    $field1064->table = 'vtiger_quotes';
    $field1064->column = 'conversion_rate';
    $field1064->columntype = 'decimal(10,3)';
    $field1064->uitype = 1;
    $field1064->typeofdata = 'N~O';
    $field1064->displaytype = 3;
    $field1064->presence = 2;
    $field1064->defaultvalue = '1';
    $field1064->quickcreate = 3;
    $field1064->summaryfield = 0;

    $block190->addField($field1064);
}

$field1065 = Vtiger_Field::getInstance('hdnDiscountAmount', $module);
if ($field1065) {
    echo "<br> Field 'hdnDiscountAmount' is already present <br>";
} else {
    $field1065 = new Vtiger_Field();
    $field1065->label = 'LBL_QUOTES_HDNDISCOUNTAMOUNT';
    $field1065->name = 'hdnDiscountAmount';
    $field1065->table = 'vtiger_quotes';
    $field1065->column = 'discount_amount';
    $field1065->columntype = 'decimal(25,8)';
    $field1065->uitype = 72;
    $field1065->typeofdata = 'N~O';
    $field1065->displaytype = 3;
    $field1065->presence = 2;
    $field1065->defaultvalue = '';
    $field1065->quickcreate = 1;
    $field1065->summaryfield = 0;

    $block190->addField($field1065);
}

$field1066 = Vtiger_Field::getInstance('hdnS_H_Amount', $module);
if ($field1066) {
    echo "<br> Field 'hdnS_H_Amount' is already present <br>";
} else {
    $field1066 = new Vtiger_Field();
    $field1066->label = 'LBL_QUOTES_HDNSHAMOUNT';
    $field1066->name = 'hdnS_H_Amount';
    $field1066->table = 'vtiger_quotes';
    $field1066->column = 's_h_amount';
    $field1066->columntype = 'decimal(25,8)';
    $field1066->uitype = 72;
    $field1066->typeofdata = 'N~O';
    $field1066->displaytype = 3;
    $field1066->presence = 2;
    $field1066->defaultvalue = '';
    $field1066->quickcreate = 1;
    $field1066->summaryfield = 0;

    $block190->addField($field1066);
}

$field1067 = Vtiger_Field::getInstance('hdnSubTotal', $module);
if ($field1067) {
    echo "<br> Field 'hdnSubTotal' is already present <br>";
} else {
    $field1067 = new Vtiger_Field();
    $field1067->label = 'LBL_QUOTES_HDNSUBTOTAL';
    $field1067->name = 'hdnSubTotal';
    $field1067->table = 'vtiger_quotes';
    $field1067->column = 'subtotal';
    $field1067->columntype = 'decimal(25,8)';
    $field1067->uitype = 72;
    $field1067->typeofdata = 'N~O';
    $field1067->displaytype = 3;
    $field1067->presence = 2;
    $field1067->defaultvalue = '';
    $field1067->quickcreate = 1;
    $field1067->summaryfield = 0;

    $block190->addField($field1067);
}

$field1068 = Vtiger_Field::getInstance('txtAdjustment', $module);
if ($field1068) {
    echo "<br> Field 'txtAdjustment' is already present <br>";
} else {
    $field1068 = new Vtiger_Field();
    $field1068->label = 'LBL_QUOTES_ADJUSTMENT';
    $field1068->name = 'txtAdjustment';
    $field1068->table = 'vtiger_quotes';
    $field1068->column = 'adjustment';
    $field1068->columntype = 'decimal(25,8)';
    $field1068->uitype = 72;
    $field1068->typeofdata = 'N~O';
    $field1068->displaytype = 3;
    $field1068->presence = 2;
    $field1068->defaultvalue = '';
    $field1068->quickcreate = 1;
    $field1068->summaryfield = 0;

    $block190->addField($field1068);
}

$field1069 = Vtiger_Field::getInstance('hdnGrandTotal', $module);
if ($field1069) {
    echo "<br> Field 'hdnGrandTotal' is already present <br>";
} else {
    $field1069 = new Vtiger_Field();
    $field1069->label = 'LBL_QUOTES_HDNGRANDTOTAL';
    $field1069->name = 'hdnGrandTotal';
    $field1069->table = 'vtiger_quotes';
    $field1069->column = 'total';
    $field1069->columntype = 'decimal(25,8)';
    $field1069->uitype = 72;
    $field1069->typeofdata = 'N~O';
    $field1069->displaytype = 3;
    $field1069->presence = 2;
    $field1069->defaultvalue = '';
    $field1069->quickcreate = 1;
    $field1069->summaryfield = 0;

    $block190->addField($field1069);
}

$field1070 = Vtiger_Field::getInstance('hdnTaxType', $module);
if ($field1070) {
    echo "<br> Field 'hdnTaxType' is already present <br>";
} else {
    $field1070 = new Vtiger_Field();
    $field1070->label = 'LBL_QUOTES_HDNTAXTYPE';
    $field1070->name = 'hdnTaxType';
    $field1070->table = 'vtiger_quotes';
    $field1070->column = 'taxtype';
    $field1070->columntype = 'varchar(25)';
    $field1070->uitype = 16;
    $field1070->typeofdata = 'V~O';
    $field1070->displaytype = 3;
    $field1070->presence = 2;
    $field1070->defaultvalue = '';
    $field1070->quickcreate = 1;
    $field1070->summaryfield = 0;

    $block190->addField($field1070);
}

$field1071 = Vtiger_Field::getInstance('hdnDiscountPercent', $module);
if ($field1071) {
    echo "<br> Field 'hdnDiscountPercent' is already present <br>";
} else {
    $field1071 = new Vtiger_Field();
    $field1071->label = 'LBL_QUOTES_HDNDISCOUNTPERCENT';
    $field1071->name = 'hdnDiscountPercent';
    $field1071->table = 'vtiger_quotes';
    $field1071->column = 'discount_percent';
    $field1071->columntype = 'decimal(25,3)';
    $field1071->uitype = 1;
    $field1071->typeofdata = 'N~O';
    $field1071->displaytype = 3;
    $field1071->presence = 2;
    $field1071->defaultvalue = '';
    $field1071->quickcreate = 1;
    $field1071->summaryfield = 0;

    $block190->addField($field1071);
}

$field1072 = Vtiger_Field::getInstance('currency_id', $module);
if ($field1072) {
    echo "<br> Field 'currency_id' is already present <br>";
} else {
    $field1072 = new Vtiger_Field();
    $field1072->label = 'LBL_QUOTES_CURRENCY';
    $field1072->name = 'currency_id';
    $field1072->table = 'vtiger_quotes';
    $field1072->column = 'currency_id';
    $field1072->columntype = 'int(19)';
    $field1072->uitype = 117;
    $field1072->typeofdata = 'I~O';
    $field1072->displaytype = 3;
    $field1072->presence = 2;
    $field1072->defaultvalue = '';
    $field1072->quickcreate = 3;
    $field1072->summaryfield = 0;

    $block190->addField($field1072);
}

$field1073 = Vtiger_Field::getInstance('bill_street', $module);
if ($field1073) {
    echo "<br> Field 'bill_street' is already present <br>";
} else {
    $field1073 = new Vtiger_Field();
    $field1073->label = 'LBL_QUOTES_BILLINGADDRESS';
    $field1073->name = 'bill_street';
    $field1073->table = 'vtiger_quotesbillads';
    $field1073->column = 'bill_street';
    $field1073->columntype = 'varchar(250)';
    $field1073->uitype = 1;
    $field1073->typeofdata = 'V~O';
    $field1073->displaytype = 1;
    $field1073->presence = 2;
    $field1073->defaultvalue = '';
    $field1073->quickcreate = 1;
    $field1073->summaryfield = 0;

    $block191->addField($field1073);
}

$field1074 = Vtiger_Field::getInstance('bill_city', $module);
if ($field1074) {
    echo "<br> Field 'bill_city' is already present <br>";
} else {
    $field1074 = new Vtiger_Field();
    $field1074->label = 'LBL_QUOTES_BILLINGCITY';
    $field1074->name = 'bill_city';
    $field1074->table = 'vtiger_quotesbillads';
    $field1074->column = 'bill_city';
    $field1074->columntype = 'varchar(30)';
    $field1074->uitype = 1;
    $field1074->typeofdata = 'V~O';
    $field1074->displaytype = 1;
    $field1074->presence = 2;
    $field1074->defaultvalue = '';
    $field1074->quickcreate = 1;
    $field1074->summaryfield = 0;

    $block191->addField($field1074);
}

$field1075 = Vtiger_Field::getInstance('bill_state', $module);
if ($field1075) {
    echo "<br> Field 'bill_state' is already present <br>";
} else {
    $field1075 = new Vtiger_Field();
    $field1075->label = 'LBL_QUOTES_BILLINGSTATE';
    $field1075->name = 'bill_state';
    $field1075->table = 'vtiger_quotesbillads';
    $field1075->column = 'bill_state';
    $field1075->columntype = 'varchar(30)';
    $field1075->uitype = 1;
    $field1075->typeofdata = 'V~O';
    $field1075->displaytype = 1;
    $field1075->presence = 2;
    $field1075->defaultvalue = '';
    $field1075->quickcreate = 1;
    $field1075->summaryfield = 0;

    $block191->addField($field1075);
}

$field1076 = Vtiger_Field::getInstance('bill_code', $module);
if ($field1076) {
    echo "<br> Field 'bill_code' is already present <br>";
} else {
    $field1076 = new Vtiger_Field();
    $field1076->label = 'LBL_QUOTES_BILLINGZIPCODE';
    $field1076->name = 'bill_code';
    $field1076->table = 'vtiger_quotesbillads';
    $field1076->column = 'bill_code';
    $field1076->columntype = 'varchar(30)';
    $field1076->uitype = 1;
    $field1076->typeofdata = 'V~O';
    $field1076->displaytype = 1;
    $field1076->presence = 2;
    $field1076->defaultvalue = '';
    $field1076->quickcreate = 1;
    $field1076->summaryfield = 0;

    $block191->addField($field1076);
}

$field1077 = Vtiger_Field::getInstance('bill_pobox', $module);
if ($field1077) {
    echo "<br> Field 'bill_pobox' is already present <br>";
} else {
    $field1077 = new Vtiger_Field();
    $field1077->label = 'LBL_QUOTES_BILLINGPOBOX';
    $field1077->name = 'bill_pobox';
    $field1077->table = 'vtiger_quotesbillads';
    $field1077->column = 'bill_pobox';
    $field1077->columntype = 'varchar(30)';
    $field1077->uitype = 1;
    $field1077->typeofdata = 'V~O';
    $field1077->displaytype = 1;
    $field1077->presence = 2;
    $field1077->defaultvalue = '';
    $field1077->quickcreate = 1;
    $field1077->summaryfield = 0;

    $block191->addField($field1077);
}

$field1078 = Vtiger_Field::getInstance('bill_country', $module);
if ($field1078) {
    echo "<br> Field 'bill_country' is already present <br>";
} else {
    $field1078 = new Vtiger_Field();
    $field1078->label = 'LBL_QUOTES_BILLINGCOUNTRY';
    $field1078->name = 'bill_country';
    $field1078->table = 'vtiger_quotesbillads';
    $field1078->column = 'bill_country';
    $field1078->columntype = 'varchar(30)';
    $field1078->uitype = 1;
    $field1078->typeofdata = 'V~O';
    $field1078->displaytype = 1;
    $field1078->presence = 2;
    $field1078->defaultvalue = '';
    $field1078->quickcreate = 1;
    $field1078->summaryfield = 0;

    $block191->addField($field1078);
}

$field1079 = Vtiger_Field::getInstance('origin_address1', $module);
if ($field1079) {
    echo "<br> Field 'origin_address1' is already present <br>";
} else {
    $field1079 = new Vtiger_Field();
    $field1079->label = 'LBL_QUOTES_ORIGINADDRESS1';
    $field1079->name = 'origin_address1';
    $field1079->table = 'vtiger_quotescf';
    $field1079->column = 'origin_address1';
    $field1079->columntype = 'varchar(255)';
    $field1079->uitype = 1;
    $field1079->typeofdata = 'V~O~LE~50';
    $field1079->displaytype = 1;
    $field1079->presence = 2;
    $field1079->defaultvalue = '';
    $field1079->quickcreate = 1;
    $field1079->summaryfield = 0;

    $block192->addField($field1079);
}

$field1080 = Vtiger_Field::getInstance('destination_address1', $module);
if ($field1080) {
    echo "<br> Field 'destination_address1' is already present <br>";
} else {
    $field1080 = new Vtiger_Field();
    $field1080->label = 'LBL_QUOTES_DESTINATIONADDRESS1';
    $field1080->name = 'destination_address1';
    $field1080->table = 'vtiger_quotescf';
    $field1080->column = 'destination_address1';
    $field1080->columntype = 'varchar(255)';
    $field1080->uitype = 1;
    $field1080->typeofdata = 'V~O~LE~50';
    $field1080->displaytype = 1;
    $field1080->presence = 2;
    $field1080->defaultvalue = '';
    $field1080->quickcreate = 1;
    $field1080->summaryfield = 0;

    $block192->addField($field1080);
}

$field1081 = Vtiger_Field::getInstance('origin_address2', $module);
if ($field1081) {
    echo "<br> Field 'origin_address2' is already present <br>";
} else {
    $field1081 = new Vtiger_Field();
    $field1081->label = 'LBL_QUOTES_ORIGINADDRESS2';
    $field1081->name = 'origin_address2';
    $field1081->table = 'vtiger_quotescf';
    $field1081->column = 'origin_address2';
    $field1081->columntype = 'varchar(255)';
    $field1081->uitype = 1;
    $field1081->typeofdata = 'V~O~LE~50';
    $field1081->displaytype = 1;
    $field1081->presence = 2;
    $field1081->defaultvalue = '';
    $field1081->quickcreate = 1;
    $field1081->summaryfield = 0;

    $block192->addField($field1081);
}

$field1082 = Vtiger_Field::getInstance('destination_address2', $module);
if ($field1082) {
    echo "<br> Field 'destination_address2' is already present <br>";
} else {
    $field1082 = new Vtiger_Field();
    $field1082->label = 'LBL_QUOTES_DESTINATIONADDRESS2';
    $field1082->name = 'destination_address2';
    $field1082->table = 'vtiger_quotescf';
    $field1082->column = 'destination_address2';
    $field1082->columntype = 'varchar(255)';
    $field1082->uitype = 1;
    $field1082->typeofdata = 'V~O~LE~50';
    $field1082->displaytype = 1;
    $field1082->presence = 2;
    $field1082->defaultvalue = '';
    $field1082->quickcreate = 1;
    $field1082->summaryfield = 0;

    $block192->addField($field1082);
}

$field1083 = Vtiger_Field::getInstance('origin_city', $module);
if ($field1083) {
    echo "<br> Field 'origin_city' is already present <br>";
} else {
    $field1083 = new Vtiger_Field();
    $field1083->label = 'LBL_QUOTES_ORIGINCITY';
    $field1083->name = 'origin_city';
    $field1083->table = 'vtiger_quotescf';
    $field1083->column = 'origin_city';
    $field1083->columntype = 'varchar(255)';
    $field1083->uitype = 1;
    $field1083->typeofdata = 'V~O~LE~50';
    $field1083->displaytype = 1;
    $field1083->presence = 2;
    $field1083->defaultvalue = '';
    $field1083->quickcreate = 1;
    $field1083->summaryfield = 0;

    $block192->addField($field1083);
}

$field1084 = Vtiger_Field::getInstance('destination_city', $module);
if ($field1084) {
    echo "<br> Field 'destination_city' is already present <br>";
} else {
    $field1084 = new Vtiger_Field();
    $field1084->label = 'LBL_QUOTES_DESTINATIONCITY';
    $field1084->name = 'destination_city';
    $field1084->table = 'vtiger_quotescf';
    $field1084->column = 'destination_city';
    $field1084->columntype = 'varchar(255)';
    $field1084->uitype = 1;
    $field1084->typeofdata = 'V~O~LE~50';
    $field1084->displaytype = 1;
    $field1084->presence = 2;
    $field1084->defaultvalue = '';
    $field1084->quickcreate = 1;
    $field1084->summaryfield = 0;

    $block192->addField($field1084);
}

$field1085 = Vtiger_Field::getInstance('origin_state', $module);
if ($field1085) {
    echo "<br> Field 'origin_state' is already present <br>";
} else {
    $field1085 = new Vtiger_Field();
    $field1085->label = 'LBL_QUOTES_ORIGINSTATE';
    $field1085->name = 'origin_state';
    $field1085->table = 'vtiger_quotescf';
    $field1085->column = 'origin_state';
    $field1085->columntype = 'varchar(255)';
    $field1085->uitype = 1;
    $field1085->typeofdata = 'V~O';
    $field1085->displaytype = 1;
    $field1085->presence = 2;
    $field1085->defaultvalue = '';
    $field1085->quickcreate = 1;
    $field1085->summaryfield = 0;

    $block192->addField($field1085);
}

$field1086 = Vtiger_Field::getInstance('destination_state', $module);
if ($field1086) {
    echo "<br> Field 'destination_state' is already present <br>";
} else {
    $field1086 = new Vtiger_Field();
    $field1086->label = 'LBL_QUOTES_DESTINATIONSTATE';
    $field1086->name = 'destination_state';
    $field1086->table = 'vtiger_quotescf';
    $field1086->column = 'destination_state';
    $field1086->columntype = 'varchar(255)';
    $field1086->uitype = 1;
    $field1086->typeofdata = 'V~O';
    $field1086->displaytype = 1;
    $field1086->presence = 2;
    $field1086->defaultvalue = '';
    $field1086->quickcreate = 1;
    $field1086->summaryfield = 0;

    $block192->addField($field1086);
}

$field1087 = Vtiger_Field::getInstance('origin_zip', $module);
if ($field1087) {
    echo "<br> Field 'origin_zip' is already present <br>";
} else {
    $field1087 = new Vtiger_Field();
    $field1087->label = 'LBL_QUOTES_ORIGINZIP';
    $field1087->name = 'origin_zip';
    $field1087->table = 'vtiger_quotescf';
    $field1087->column = 'origin_zip';
    $field1087->columntype = 'varchar(30)';
    $field1087->uitype = 1;
    $field1087->typeofdata = 'V~O';
    $field1087->displaytype = 1;
    $field1087->presence = 2;
    $field1087->defaultvalue = '';
    $field1087->quickcreate = 0;
    $field1087->summaryfield = 0;

    $block192->addField($field1087);
}

$field1088 = Vtiger_Field::getInstance('destination_zip', $module);
if ($field1088) {
    echo "<br> Field 'destination_zip' is already present <br>";
} else {
    $field1088 = new Vtiger_Field();
    $field1088->label = 'LBL_QUOTES_DESTINATIONZIP';
    $field1088->name = 'destination_zip';
    $field1088->table = 'vtiger_quotescf';
    $field1088->column = 'destination_zip';
    $field1088->columntype = 'varchar(30)';
    $field1088->uitype = 1;
    $field1088->typeofdata = 'V~O';
    $field1088->displaytype = 1;
    $field1088->presence = 2;
    $field1088->defaultvalue = '';
    $field1088->quickcreate = 0;
    $field1088->summaryfield = 0;

    $block192->addField($field1088);
}

$field1089 = Vtiger_Field::getInstance('origin_phone1', $module);
if ($field1089) {
    echo "<br> Field 'origin_phone1' is already present <br>";
} else {
    $field1089 = new Vtiger_Field();
    $field1089->label = 'LBL_QUOTES_ORIGINPHONE1';
    $field1089->name = 'origin_phone1';
    $field1089->table = 'vtiger_quotescf';
    $field1089->column = 'origin_phone1';
    $field1089->columntype = 'varchar(255)';
    $field1089->uitype = 11;
    $field1089->typeofdata = 'V~O';
    $field1089->displaytype = 1;
    $field1089->presence = 2;
    $field1089->defaultvalue = '';
    $field1089->quickcreate = 1;
    $field1089->summaryfield = 0;

    $block192->addField($field1089);
}

$field1090 = Vtiger_Field::getInstance('destination_phone1', $module);
if ($field1090) {
    echo "<br> Field 'destination_phone1' is already present <br>";
} else {
    $field1090 = new Vtiger_Field();
    $field1090->label = 'LBL_QUOTES_DESTINATIONPHONE1';
    $field1090->name = 'destination_phone1';
    $field1090->table = 'vtiger_quotescf';
    $field1090->column = 'destination_phone1';
    $field1090->columntype = 'varchar(255)';
    $field1090->uitype = 11;
    $field1090->typeofdata = 'V~O';
    $field1090->displaytype = 1;
    $field1090->presence = 2;
    $field1090->defaultvalue = '';
    $field1090->quickcreate = 1;
    $field1090->summaryfield = 0;

    $block192->addField($field1090);
}

$field1091 = Vtiger_Field::getInstance('origin_phone2', $module);
if ($field1091) {
    echo "<br> Field 'origin_phone2' is already present <br>";
} else {
    $field1091 = new Vtiger_Field();
    $field1091->label = 'LBL_QUOTES_ORIGINPHONE2';
    $field1091->name = 'origin_phone2';
    $field1091->table = 'vtiger_quotescf';
    $field1091->column = 'origin_phone2';
    $field1091->columntype = 'varchar(255)';
    $field1091->uitype = 11;
    $field1091->typeofdata = 'V~O';
    $field1091->displaytype = 1;
    $field1091->presence = 2;
    $field1091->defaultvalue = '';
    $field1091->quickcreate = 1;
    $field1091->summaryfield = 0;

    $block192->addField($field1091);
}

$field1092 = Vtiger_Field::getInstance('destination_phone2', $module);
if ($field1092) {
    echo "<br> Field 'destination_phone2' is already present <br>";
} else {
    $field1092 = new Vtiger_Field();
    $field1092->label = 'LBL_QUOTES_DESTINATIONPHONE2';
    $field1092->name = 'destination_phone2';
    $field1092->table = 'vtiger_quotescf';
    $field1092->column = 'destination_phone2';
    $field1092->columntype = 'varchar(255)';
    $field1092->uitype = 11;
    $field1092->typeofdata = 'V~O';
    $field1092->displaytype = 1;
    $field1092->presence = 2;
    $field1092->defaultvalue = '';
    $field1092->quickcreate = 1;
    $field1092->summaryfield = 0;

    $block192->addField($field1092);
}

$field1093 = Vtiger_Field::getInstance('effective_date', $module);
if ($field1093) {
    echo "<br> Field 'effective_date' is already present <br>";
} else {
    $field1093 = new Vtiger_Field();
    $field1093->label = 'LBL_QUOTES_EFFECTIVEDATE';
    $field1093->name = 'effective_date';
    $field1093->table = 'vtiger_quotes';
    $field1093->column = 'effective_date';
    $field1093->columntype = 'date';
    $field1093->uitype = 5;
    $field1093->typeofdata = 'D~O';
    $field1093->displaytype = 1;
    $field1093->presence = 2;
    $field1093->defaultvalue = '';
    $field1093->quickcreate = 1;
    $field1093->summaryfield = 0;

    $block193->addField($field1093);
}

$field1094 = Vtiger_Field::getInstance('weight', $module);
if ($field1094) {
    echo "<br> Field 'weight' is already present <br>";
} else {
    $field1094 = new Vtiger_Field();
    $field1094->label = 'LBL_QUOTES_WEIGHT';
    $field1094->name = 'weight';
    $field1094->table = 'vtiger_quotes';
    $field1094->column = 'weight';
    $field1094->columntype = 'int(10)';
    $field1094->uitype = 7;
    $field1094->typeofdata = 'I~O';
    $field1094->displaytype = 1;
    $field1094->presence = 2;
    $field1094->defaultvalue = '';
    $field1094->quickcreate = 0;
    $field1094->summaryfield = 0;

    $block194->addField($field1094);
}

$field1095 = Vtiger_Field::getInstance('pickup_date', $module);
if ($field1095) {
    echo "<br> Field 'pickup_date' is already present <br>";
} else {
    $field1095 = new Vtiger_Field();
    $field1095->label = 'LBL_QUOTES_PICKUPDATE';
    $field1095->name = 'pickup_date';
    $field1095->table = 'vtiger_quotes';
    $field1095->column = 'pickup_date';
    $field1095->columntype = 'date';
    $field1095->uitype = 5;
    $field1095->typeofdata = 'D~O';
    $field1095->displaytype = 1;
    $field1095->presence = 2;
    $field1095->defaultvalue = '';
    $field1095->quickcreate = 1;
    $field1095->summaryfield = 0;

    $block194->addField($field1095);
}

$field1096 = Vtiger_Field::getInstance('full_pack', $module);
if ($field1096) {
    echo "<br> Field 'full_pack' is already present <br>";
} else {
    $field1096 = new Vtiger_Field();
    $field1096->label = 'LBL_QUOTES_FULLPACKAPPLIED';
    $field1096->name = 'full_pack';
    $field1096->table = 'vtiger_quotes';
    $field1096->column = 'full_pack';
    $field1096->columntype = 'varchar(3)';
    $field1096->uitype = 56;
    $field1096->typeofdata = 'C~O';
    $field1096->displaytype = 1;
    $field1096->presence = 2;
    $field1096->defaultvalue = '';
    $field1096->quickcreate = 0;
    $field1096->summaryfield = 0;

    $block194->addField($field1096);
}

$field1097 = Vtiger_Field::getInstance('valuation_deductible', $module);
if ($field1097) {
    echo "<br> Field 'valuation_deductible' is already present <br>";
} else {
    $field1097 = new Vtiger_Field();
    $field1097->label = 'LBL_QUOTES_VALUATIONDEDUCTIBLE';
    $field1097->name = 'valuation_deductible';
    $field1097->table = 'vtiger_quotes';
    $field1097->column = 'valuation_deductible';
    $field1097->columntype = 'varchar(250)';
    $field1097->uitype = 16;
    $field1097->typeofdata = 'V~O';
    $field1097->displaytype = 1;
    $field1097->presence = 2;
    $field1097->defaultvalue = '';
    $field1097->quickcreate = 2;
    $field1097->summaryfield = 0;

    $block282->addField($field1097);
}

$field1098 = Vtiger_Field::getInstance('full_unpack', $module);
if ($field1098) {
    echo "<br> Field 'full_unpack' is already present <br>";
} else {
    $field1098 = new Vtiger_Field();
    $field1098->label = 'LBL_QUOTES_FULLUNPACKAPPLIED';
    $field1098->name = 'full_unpack';
    $field1098->table = 'vtiger_quotes';
    $field1098->column = 'full_unpack';
    $field1098->columntype = 'varchar(3)';
    $field1098->uitype = 56;
    $field1098->typeofdata = 'C~O';
    $field1098->displaytype = 1;
    $field1098->presence = 2;
    $field1098->defaultvalue = '';
    $field1098->quickcreate = 0;
    $field1098->summaryfield = 0;

    $block194->addField($field1098);
}

$field1099 = Vtiger_Field::getInstance('valuation_amount', $module);
if ($field1099) {
    echo "<br> Field 'valuation_amount' is already present <br>";
} else {
    $field1099 = new Vtiger_Field();
    $field1099->label = 'LBL_QUOTES_VALUATIONAMOUNT';
    $field1099->name = 'valuation_amount';
    $field1099->table = 'vtiger_quotes';
    $field1099->column = 'valuation_amount';
    $field1099->columntype = 'decimal(56,8)';
    $field1099->uitype = 71;
    $field1099->typeofdata = 'N~O';
    $field1099->displaytype = 1;
    $field1099->presence = 2;
    $field1099->defaultvalue = '';
    $field1099->quickcreate = 2;
    $field1099->summaryfield = 0;

    $block282->addField($field1099);
}

$field1100 = Vtiger_Field::getInstance('bottom_line_discount', $module);
if ($field1100) {
    echo "<br> Field 'bottom_line_discount' is already present <br>";
} else {
    $field1100 = new Vtiger_Field();
    $field1100->label = 'LBL_QUOTES_BOTTOMLINEDISCOUNT';
    $field1100->name = 'bottom_line_discount';
    $field1100->table = 'vtiger_quotes';
    $field1100->column = 'bottom_line_discount';
    $field1100->columntype = 'decimal(19,2)';
    $field1100->uitype = 7;
    $field1100->typeofdata = 'N~O';
    $field1100->displaytype = 1;
    $field1100->presence = 2;
    $field1100->defaultvalue = '';
    $field1100->quickcreate = 0;
    $field1100->summaryfield = 0;

    $block194->addField($field1100);
}

$field1101 = Vtiger_Field::getInstance('interstate_mileage', $module);
if ($field1101) {
    echo "<br> Field 'interstate_mileage' is already present <br>";
} else {
    $field1101 = new Vtiger_Field();
    $field1101->label = 'LBL_QUOTES_MILEAGE';
    $field1101->name = 'interstate_mileage';
    $field1101->table = 'vtiger_quotes';
    $field1101->column = 'interstate_mileage';
    $field1101->columntype = 'int(19)';
    $field1101->uitype = 7;
    $field1101->typeofdata = 'I~O';
    $field1101->displaytype = 1;
    $field1101->presence = 2;
    $field1101->defaultvalue = '';
    $field1101->quickcreate = 1;
    $field1101->summaryfield = 0;

    $block194->addField($field1101);
}

$field1102 = Vtiger_Field::getInstance('terms_conditions', $module);
if ($field1102) {
    echo "<br> Field 'terms_conditions' is already present <br>";
} else {
    $field1102 = new Vtiger_Field();
    $field1102->label = 'LBL_QUOTES_TERMSANDCONDITIONS';
    $field1102->name = 'terms_conditions';
    $field1102->table = 'vtiger_quotes';
    $field1102->column = 'terms_conditions';
    $field1102->columntype = 'text';
    $field1102->uitype = 19;
    $field1102->typeofdata = 'V~O';
    $field1102->displaytype = 1;
    $field1102->presence = 2;
    $field1102->defaultvalue = '';
    $field1102->quickcreate = 1;
    $field1102->summaryfield = 0;

    $block198->addField($field1102);
}

$field1103 = Vtiger_Field::getInstance('description', $module);
if ($field1103) {
    echo "<br> Field 'description' is already present <br>";
} else {
    $field1103 = new Vtiger_Field();
    $field1103->label = 'LBL_QUOTES_DESCRIPTION';
    $field1103->name = 'description';
    $field1103->table = 'vtiger_crmentity';
    $field1103->column = 'description';
    //$field1103->columntype = 'text';
    $field1103->uitype = 19;
    $field1103->typeofdata = 'V~O';
    $field1103->displaytype = 1;
    $field1103->presence = 2;
    $field1103->defaultvalue = '';
    $field1103->quickcreate = 1;
    $field1103->summaryfield = 0;

    $block199->addField($field1103);
}

$field1104 = Vtiger_Field::getInstance('tax2', $module);
if ($field1104) {
    echo "<br> Field 'tax2' is already present <br>";
} else {
    $field1104 = new Vtiger_Field();
    $field1104->label = 'LBL_QUOTES_TAX2';
    $field1104->name = 'tax2';
    $field1104->table = 'vtiger_inventoryproductrel';
    $field1104->column = 'tax2';
    //$field1104->columntype = 'decimal(7,3)';
    $field1104->uitype = 83;
    $field1104->typeofdata = 'V~O';
    $field1104->displaytype = 5;
    $field1104->presence = 2;
    $field1104->defaultvalue = '';
    $field1104->quickcreate = 1;
    $field1104->summaryfield = 0;

    $block200->addField($field1104);
}

$field1105 = Vtiger_Field::getInstance('tax3', $module);
if ($field1105) {
    echo "<br> Field 'tax3' is already present <br>";
} else {
    $field1105 = new Vtiger_Field();
    $field1105->label = 'LBL_QUOTES_TAX3';
    $field1105->name = 'tax3';
    $field1105->table = 'vtiger_inventoryproductrel';
    $field1105->column = 'tax3';
    //$field1105->columntype = 'decimal(7,3)';
    $field1105->uitype = 83;
    $field1105->typeofdata = 'V~O';
    $field1105->displaytype = 5;
    $field1105->presence = 2;
    $field1105->defaultvalue = '';
    $field1105->quickcreate = 1;
    $field1105->summaryfield = 0;

    $block200->addField($field1105);
}

$field1106 = Vtiger_Field::getInstance('hdnS_H_Percent', $module);
if ($field1106) {
    echo "<br> Field 'hdnS_H_Percent' is already present <br>";
} else {
    $field1106 = new Vtiger_Field();
    $field1106->label = 'LBL_QUOTES_HDNSHPERCENT';
    $field1106->name = 'hdnS_H_Percent';
    $field1106->table = 'vtiger_quotes';
    $field1106->column = 's_h_percent';
    $field1106->columntype = 'int(11)';
    $field1106->uitype = 1;
    $field1106->typeofdata = 'N~O';
    $field1106->displaytype = 5;
    $field1106->presence = 2;
    $field1106->defaultvalue = '';
    $field1106->quickcreate = 1;
    $field1106->summaryfield = 0;

    $block200->addField($field1106);
}

$field1107 = Vtiger_Field::getInstance('tax1', $module);
if ($field1107) {
    echo "<br> Field 'tax1' is already present <br>";
} else {
    $field1107 = new Vtiger_Field();
    $field1107->label = 'LBL_QUOTES_TAX1';
    $field1107->name = 'tax1';
    $field1107->table = 'vtiger_inventoryproductrel';
    $field1107->column = 'tax1';
    //$field1107->columntype = 'decimal(7,3)';
    $field1107->uitype = 83;
    $field1107->typeofdata = 'V~O';
    $field1107->displaytype = 5;
    $field1107->presence = 2;
    $field1107->defaultvalue = '';
    $field1107->quickcreate = 1;
    $field1107->summaryfield = 0;

    $block200->addField($field1107);
}

$field1108 = Vtiger_Field::getInstance('comment', $module);
if ($field1108) {
    echo "<br> Field 'comment' is already present <br>";
} else {
    $field1108 = new Vtiger_Field();
    $field1108->label = 'LBL_QUOTES_ITEMCOMMENT';
    $field1108->name = 'comment';
    $field1108->table = 'vtiger_inventoryproductrel';
    $field1108->column = 'comment';
    //$field1108->columntype = 'varchar(500)';
    $field1108->uitype = 19;
    $field1108->typeofdata = 'V~O';
    $field1108->displaytype = 5;
    $field1108->presence = 2;
    $field1108->defaultvalue = '';
    $field1108->quickcreate = 1;
    $field1108->summaryfield = 0;

    $block200->addField($field1108);
}

$field1109 = Vtiger_Field::getInstance('productid', $module);
if ($field1109) {
    echo "<br> Field 'productid' is already present <br>";
} else {
    $field1109 = new Vtiger_Field();
    $field1109->label = 'LBL_QUOTES_PRODUCTID';
    $field1109->name = 'productid';
    $field1109->table = 'vtiger_inventoryproductrel';
    $field1109->column = 'productid';
    //$field1109->columntype = 'int(19)';
    $field1109->uitype = 10;
    $field1109->typeofdata = 'V~O';
    $field1109->displaytype = 5;
    $field1109->presence = 2;
    $field1109->defaultvalue = '';
    $field1109->quickcreate = 1;
    $field1109->summaryfield = 0;

    $block200->addField($field1109);

    $field1109->setRelatedModules([]);
}

$field1110 = Vtiger_Field::getInstance('listprice', $module);
if ($field1110) {
    echo "<br> Field 'listprice' is already present <br>";
} else {
    $field1110 = new Vtiger_Field();
    $field1110->label = 'LBL_QUOTES_LISTPRICE';
    $field1110->name = 'listprice';
    $field1110->table = 'vtiger_inventoryproductrel';
    $field1110->column = 'listprice';
    //$field1110->columntype = 'decimal(27,8)';
    $field1110->uitype = 71;
    $field1110->typeofdata = 'N~O';
    $field1110->displaytype = 5;
    $field1110->presence = 2;
    $field1110->defaultvalue = '';
    $field1110->quickcreate = 1;
    $field1110->summaryfield = 0;

    $block200->addField($field1110);
}

$field1111 = Vtiger_Field::getInstance('quantity', $module);
if ($field1111) {
    echo "<br> Field 'quantity' is already present <br>";
} else {
    $field1111 = new Vtiger_Field();
    $field1111->label = 'LBL_QUOTES_QUANTITY';
    $field1111->name = 'quantity';
    $field1111->table = 'vtiger_inventoryproductrel';
    $field1111->column = 'quantity';
    //$field1111->columntype = 'decimal(25,3)';
    $field1111->uitype = 7;
    $field1111->typeofdata = 'N~O';
    $field1111->displaytype = 5;
    $field1111->presence = 2;
    $field1111->defaultvalue = '';
    $field1111->quickcreate = 1;
    $field1111->summaryfield = 0;

    $block200->addField($field1111);
}

$field1112 = Vtiger_Field::getInstance('discount_percent', $module);
if ($field1112) {
    echo "<br> Field 'discount_percent' is already present <br>";
} else {
    $field1112 = new Vtiger_Field();
    $field1112->label = 'LBL_QUOTES_ITEMDISCOUNTPERCENT';
    $field1112->name = 'discount_percent';
    $field1112->table = 'vtiger_inventoryproductrel';
    $field1112->column = 'discount_percent';
    //$field1112->columntype = 'decimal(7,3)';
    $field1112->uitype = 7;
    $field1112->typeofdata = 'V~O';
    $field1112->displaytype = 3;
    $field1112->presence = 2;
    $field1112->defaultvalue = '';
    $field1112->quickcreate = 1;
    $field1112->summaryfield = 0;

    $block200->addField($field1112);
}

$field1113 = Vtiger_Field::getInstance('discount_amount', $module);
if ($field1113) {
    echo "<br> Field 'discount_amount' is already present <br>";
} else {
    $field1113 = new Vtiger_Field();
    $field1113->label = 'LBL_QUOTES_DISCOUNT';
    $field1113->name = 'discount_amount';
    $field1113->table = 'vtiger_inventoryproductrel';
    $field1113->column = 'discount_amount';
    //$field1113->columntype = 'decimal(27,8)';
    $field1113->uitype = 71;
    $field1113->typeofdata = 'N~O';
    $field1113->displaytype = 5;
    $field1113->presence = 2;
    $field1113->defaultvalue = '';
    $field1113->quickcreate = 1;
    $field1113->summaryfield = 0;

    $block200->addField($field1113);
}

$field1114 = Vtiger_Field::getInstance('sit_origin_date_in', $module);
if ($field1114) {
    echo "<br> Field 'sit_origin_date_in' is already present <br>";
} else {
    $field1114 = new Vtiger_Field();
    $field1114->label = 'LBL_QUOTES_SITORIGINDATEIN';
    $field1114->name = 'sit_origin_date_in';
    $field1114->table = 'vtiger_quotes';
    $field1114->column = 'sit_origin_date_in';
    $field1114->columntype = 'date';
    $field1114->uitype = 5;
    $field1114->typeofdata = 'D~O';
    $field1114->displaytype = 1;
    $field1114->presence = 2;
    $field1114->defaultvalue = '';
    $field1114->quickcreate = 1;
    $field1114->summaryfield = 0;

    $block196->addField($field1114);
}

$field1115 = Vtiger_Field::getInstance('sit_dest_date_in', $module);
if ($field1115) {
    echo "<br> Field 'sit_dest_date_in' is already present <br>";
} else {
    $field1115 = new Vtiger_Field();
    $field1115->label = 'LBL_QUOTES_SITDESTINATIONDATEIN';
    $field1115->name = 'sit_dest_date_in';
    $field1115->table = 'vtiger_quotes';
    $field1115->column = 'sit_dest_date_in';
    $field1115->columntype = 'date';
    $field1115->uitype = 5;
    $field1115->typeofdata = 'D~O';
    $field1115->displaytype = 1;
    $field1115->presence = 2;
    $field1115->defaultvalue = '';
    $field1115->quickcreate = 1;
    $field1115->summaryfield = 0;

    $block196->addField($field1115);
}

$field1116 = Vtiger_Field::getInstance('sit_origin_pickup_date', $module);
if ($field1116) {
    echo "<br> Field 'sit_origin_pickup_date' is already present <br>";
} else {
    $field1116 = new Vtiger_Field();
    $field1116->label = 'LBL_QUOTES_SITORIGINPICKUPDATE';
    $field1116->name = 'sit_origin_pickup_date';
    $field1116->table = 'vtiger_quotes';
    $field1116->column = 'sit_origin_pickup_date';
    $field1116->columntype = 'date';
    $field1116->uitype = 5;
    $field1116->typeofdata = 'D~O';
    $field1116->displaytype = 1;
    $field1116->presence = 2;
    $field1116->defaultvalue = '';
    $field1116->quickcreate = 1;
    $field1116->summaryfield = 0;

    $block196->addField($field1116);
}

$field1117 = Vtiger_Field::getInstance('sit_dest_delivery_date', $module);
if ($field1117) {
    echo "<br> Field 'sit_dest_delivery_date' is already present <br>";
} else {
    $field1117 = new Vtiger_Field();
    $field1117->label = 'LBL_QUOTES_SITDELIVERYDATE';
    $field1117->name = 'sit_dest_delivery_date';
    $field1117->table = 'vtiger_quotes';
    $field1117->column = 'sit_dest_delivery_date';
    $field1117->columntype = 'date';
    $field1117->uitype = 5;
    $field1117->typeofdata = 'D~O';
    $field1117->displaytype = 1;
    $field1117->presence = 2;
    $field1117->defaultvalue = '';
    $field1117->quickcreate = 1;
    $field1117->summaryfield = 0;

    $block196->addField($field1117);
}

$field1118 = Vtiger_Field::getInstance('sit_origin_weight', $module);
if ($field1118) {
    echo "<br> Field 'sit_origin_weight' is already present <br>";
} else {
    $field1118 = new Vtiger_Field();
    $field1118->label = 'LBL_QUOTES_SITORIGINWEIGHT';
    $field1118->name = 'sit_origin_weight';
    $field1118->table = 'vtiger_quotes';
    $field1118->column = 'sit_origin_weight';
    $field1118->columntype = 'int(10)';
    $field1118->uitype = 7;
    $field1118->typeofdata = 'I~O';
    $field1118->displaytype = 1;
    $field1118->presence = 2;
    $field1118->defaultvalue = '';
    $field1118->quickcreate = 1;
    $field1118->summaryfield = 0;

    $block196->addField($field1118);
}

$field1119 = Vtiger_Field::getInstance('sit_dest_weight', $module);
if ($field1119) {
    echo "<br> Field 'sit_dest_weight' is already present <br>";
} else {
    $field1119 = new Vtiger_Field();
    $field1119->label = 'LBL_QUOTES_SITDESTINATIONWEIGHT';
    $field1119->name = 'sit_dest_weight';
    $field1119->table = 'vtiger_quotes';
    $field1119->column = 'sit_dest_weight';
    $field1119->columntype = 'int(10)';
    $field1119->uitype = 7;
    $field1119->typeofdata = 'I~O';
    $field1119->displaytype = 1;
    $field1119->presence = 2;
    $field1119->defaultvalue = '';
    $field1119->quickcreate = 1;
    $field1119->summaryfield = 0;

    $block196->addField($field1119);
}

$field1120 = Vtiger_Field::getInstance('sit_origin_zip', $module);
if ($field1120) {
    echo "<br> Field 'sit_origin_zip' is already present <br>";
} else {
    $field1120 = new Vtiger_Field();
    $field1120->label = 'LBL_QUOTES_SITORIGINZIP';
    $field1120->name = 'sit_origin_zip';
    $field1120->table = 'vtiger_quotes';
    $field1120->column = 'sit_origin_zip';
    $field1120->columntype = 'int(10)';
    $field1120->uitype = 7;
    $field1120->typeofdata = 'I~O';
    $field1120->displaytype = 1;
    $field1120->presence = 2;
    $field1120->defaultvalue = '';
    $field1120->quickcreate = 1;
    $field1120->summaryfield = 0;

    $block196->addField($field1120);
}

$field1121 = Vtiger_Field::getInstance('sit_dest_zip', $module);
if ($field1121) {
    echo "<br> Field 'sit_dest_zip' is already present <br>";
} else {
    $field1121 = new Vtiger_Field();
    $field1121->label = 'LBL_QUOTES_SITDESTINATIONZIP';
    $field1121->name = 'sit_dest_zip';
    $field1121->table = 'vtiger_quotes';
    $field1121->column = 'sit_dest_zip';
    $field1121->columntype = 'int(10)';
    $field1121->uitype = 7;
    $field1121->typeofdata = 'I~O';
    $field1121->displaytype = 1;
    $field1121->presence = 2;
    $field1121->defaultvalue = '';
    $field1121->quickcreate = 1;
    $field1121->summaryfield = 0;

    $block196->addField($field1121);
}

$field1122 = Vtiger_Field::getInstance('sit_origin_miles', $module);
if ($field1122) {
    echo "<br> Field 'sit_origin_miles' is already present <br>";
} else {
    $field1122 = new Vtiger_Field();
    $field1122->label = 'LBL_QUOTES_SITORIGINMILES';
    $field1122->name = 'sit_origin_miles';
    $field1122->table = 'vtiger_quotes';
    $field1122->column = 'sit_origin_miles';
    $field1122->columntype = 'int(10)';
    $field1122->uitype = 7;
    $field1122->typeofdata = 'I~O';
    $field1122->displaytype = 1;
    $field1122->presence = 2;
    $field1122->defaultvalue = '';
    $field1122->quickcreate = 1;
    $field1122->summaryfield = 0;

    $block196->addField($field1122);
}

$field1123 = Vtiger_Field::getInstance('sit_dest_miles', $module);
if ($field1123) {
    echo "<br> Field 'sit_dest_miles' is already present <br>";
} else {
    $field1123 = new Vtiger_Field();
    $field1123->label = 'LBL_QUOTES_SITDESTINATIONMILES';
    $field1123->name = 'sit_dest_miles';
    $field1123->table = 'vtiger_quotes';
    $field1123->column = 'sit_dest_miles';
    $field1123->columntype = 'int(10)';
    $field1123->uitype = 7;
    $field1123->typeofdata = 'I~O';
    $field1123->displaytype = 1;
    $field1123->presence = 2;
    $field1123->defaultvalue = '';
    $field1123->quickcreate = 1;
    $field1123->summaryfield = 0;

    $block196->addField($field1123);
}

$field1124 = Vtiger_Field::getInstance('sit_origin_number_days', $module);
if ($field1124) {
    echo "<br> Field 'sit_origin_number_days' is already present <br>";
} else {
    $field1124 = new Vtiger_Field();
    $field1124->label = 'LBL_QUOTES_SITORIGINNUMBERDAYS';
    $field1124->name = 'sit_origin_number_days';
    $field1124->table = 'vtiger_quotes';
    $field1124->column = 'sit_origin_number_days';
    $field1124->columntype = 'int(10)';
    $field1124->uitype = 7;
    $field1124->typeofdata = 'I~O';
    $field1124->displaytype = 1;
    $field1124->presence = 2;
    $field1124->defaultvalue = '';
    $field1124->quickcreate = 1;
    $field1124->summaryfield = 0;

    $block196->addField($field1124);
}

$field1125 = Vtiger_Field::getInstance('sit_dest_number_days', $module);
if ($field1125) {
    echo "<br> Field 'sit_dest_number_days' is already present <br>";
} else {
    $field1125 = new Vtiger_Field();
    $field1125->label = 'LBL_QUOTES_SITDESTINATIONNUMBERDAYS';
    $field1125->name = 'sit_dest_number_days';
    $field1125->table = 'vtiger_quotes';
    $field1125->column = 'sit_dest_number_days';
    $field1125->columntype = 'int(10)';
    $field1125->uitype = 7;
    $field1125->typeofdata = 'I~O';
    $field1125->displaytype = 1;
    $field1125->presence = 2;
    $field1125->defaultvalue = '';
    $field1125->quickcreate = 1;
    $field1125->summaryfield = 0;

    $block196->addField($field1125);
}

$field1126 = Vtiger_Field::getInstance('sit_origin_fuel_percent', $module);
if ($field1126) {
    echo "<br> Field 'sit_origin_fuel_percent' is already present <br>";
} else {
    $field1126 = new Vtiger_Field();
    $field1126->label = 'LBL_QUOTES_SITORIGINFUELPERCENT';
    $field1126->name = 'sit_origin_fuel_percent';
    $field1126->table = 'vtiger_quotes';
    $field1126->column = 'sit_origin_fuel_percent';
    $field1126->columntype = 'decimal(10,3)';
    $field1126->uitype = 7;
    $field1126->typeofdata = 'N~O';
    $field1126->displaytype = 1;
    $field1126->presence = 2;
    $field1126->defaultvalue = '';
    $field1126->quickcreate = 1;
    $field1126->summaryfield = 0;

    $block196->addField($field1126);
}

$field1127 = Vtiger_Field::getInstance('sit_dest_fuel_percent', $module);
if ($field1127) {
    echo "<br> Field 'sit_dest_fuel_percent' is already present <br>";
} else {
    $field1127 = new Vtiger_Field();
    $field1127->label = 'LBL_QUOTES_SITDESTINATIONFUELPERCENT';
    $field1127->name = 'sit_dest_fuel_percent';
    $field1127->table = 'vtiger_quotes';
    $field1127->column = 'sit_dest_fuel_percent';
    $field1127->columntype = 'decimal(10,3)';
    $field1127->uitype = 7;
    $field1127->typeofdata = 'N~O';
    $field1127->displaytype = 1;
    $field1127->presence = 2;
    $field1127->defaultvalue = '';
    $field1127->quickcreate = 1;
    $field1127->summaryfield = 0;

    $block196->addField($field1127);
}

$field1128 = Vtiger_Field::getInstance('sit_origin_overtime', $module);
if ($field1128) {
    echo "<br> Field 'sit_origin_overtime' is already present <br>";
} else {
    $field1128 = new Vtiger_Field();
    $field1128->label = 'LBL_QUOTES_SITORIGINOVERTIME';
    $field1128->name = 'sit_origin_overtime';
    $field1128->table = 'vtiger_quotes';
    $field1128->column = 'sit_origin_overtime';
    $field1128->columntype = 'varchar(3)';
    $field1128->uitype = 56;
    $field1128->typeofdata = 'C~O';
    $field1128->displaytype = 1;
    $field1128->presence = 2;
    $field1128->defaultvalue = '';
    $field1128->quickcreate = 1;
    $field1128->summaryfield = 0;

    $block196->addField($field1128);
}

$field1129 = Vtiger_Field::getInstance('sit_dest_overtime', $module);
if ($field1129) {
    echo "<br> Field 'sit_dest_overtime' is already present <br>";
} else {
    $field1129 = new Vtiger_Field();
    $field1129->label = 'LBL_QUOTES_SITDESTINATIONOVERTIME';
    $field1129->name = 'sit_dest_overtime';
    $field1129->table = 'vtiger_quotes';
    $field1129->column = 'sit_dest_overtime';
    $field1129->columntype = 'varchar(3)';
    $field1129->uitype = 56;
    $field1129->typeofdata = 'C~O';
    $field1129->displaytype = 1;
    $field1129->presence = 2;
    $field1129->defaultvalue = '';
    $field1129->quickcreate = 1;
    $field1129->summaryfield = 0;

    $block196->addField($field1129);
}

$field1130 = Vtiger_Field::getInstance('acc_shuttle_origin_weight', $module);
if ($field1130) {
    echo "<br> Field 'acc_shuttle_origin_weight' is already present <br>";
} else {
    $field1130 = new Vtiger_Field();
    $field1130->label = 'LBL_QUOTES_ACCSHUTTLEORIGINWEIGHT';
    $field1130->name = 'acc_shuttle_origin_weight';
    $field1130->table = 'vtiger_quotes';
    $field1130->column = 'acc_shuttle_origin_weight';
    $field1130->columntype = 'int(10)';
    $field1130->uitype = 7;
    $field1130->typeofdata = 'I~O';
    $field1130->displaytype = 1;
    $field1130->presence = 2;
    $field1130->defaultvalue = '';
    $field1130->quickcreate = 1;
    $field1130->summaryfield = 0;

    $block197->addField($field1130);
}

$field1131 = Vtiger_Field::getInstance('acc_shuttle_dest_weight', $module);
if ($field1131) {
    echo "<br> Field 'acc_shuttle_dest_weight' is already present <br>";
} else {
    $field1131 = new Vtiger_Field();
    $field1131->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONWEIGHT';
    $field1131->name = 'acc_shuttle_dest_weight';
    $field1131->table = 'vtiger_quotes';
    $field1131->column = 'acc_shuttle_dest_weight';
    $field1131->columntype = 'int(10)';
    $field1131->uitype = 7;
    $field1131->typeofdata = 'I~O';
    $field1131->displaytype = 1;
    $field1131->presence = 2;
    $field1131->defaultvalue = '';
    $field1131->quickcreate = 1;
    $field1131->summaryfield = 0;

    $block197->addField($field1131);
}

$field1132 = Vtiger_Field::getInstance('acc_shuttle_origin_applied', $module);
if ($field1132) {
    echo "<br> Field 'acc_shuttle_origin_applied' is already present <br>";
} else {
    $field1132 = new Vtiger_Field();
    $field1132->label = 'LBL_QUOTES_ACCSHUTTLEORIGINAPPLIED';
    $field1132->name = 'acc_shuttle_origin_applied';
    $field1132->table = 'vtiger_quotes';
    $field1132->column = 'acc_shuttle_origin_applied';
    $field1132->columntype = 'varchar(3)';
    $field1132->uitype = 56;
    $field1132->typeofdata = 'C~O';
    $field1132->displaytype = 1;
    $field1132->presence = 2;
    $field1132->defaultvalue = '';
    $field1132->quickcreate = 1;
    $field1132->summaryfield = 0;

    $block197->addField($field1132);
}

$field1133 = Vtiger_Field::getInstance('acc_shuttle_dest_applied', $module);
if ($field1133) {
    echo "<br> Field 'acc_shuttle_dest_applied' is already present <br>";
} else {
    $field1133 = new Vtiger_Field();
    $field1133->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONAPPLIED';
    $field1133->name = 'acc_shuttle_dest_applied';
    $field1133->table = 'vtiger_quotes';
    $field1133->column = 'acc_shuttle_dest_applied';
    $field1133->columntype = 'varchar(3)';
    $field1133->uitype = 56;
    $field1133->typeofdata = 'C~O';
    $field1133->displaytype = 1;
    $field1133->presence = 2;
    $field1133->defaultvalue = '';
    $field1133->quickcreate = 1;
    $field1133->summaryfield = 0;

    $block197->addField($field1133);
}

$field1134 = Vtiger_Field::getInstance('acc_shuttle_origin_ot', $module);
if ($field1134) {
    echo "<br> Field 'acc_shuttle_origin_ot' is already present <br>";
} else {
    $field1134 = new Vtiger_Field();
    $field1134->label = 'LBL_QUOTES_ACCSHUTTLEORIGINOT';
    $field1134->name = 'acc_shuttle_origin_ot';
    $field1134->table = 'vtiger_quotes';
    $field1134->column = 'acc_shuttle_origin_ot';
    $field1134->columntype = 'varchar(3)';
    $field1134->uitype = 56;
    $field1134->typeofdata = 'C~O';
    $field1134->displaytype = 1;
    $field1134->presence = 2;
    $field1134->defaultvalue = '';
    $field1134->quickcreate = 1;
    $field1134->summaryfield = 0;

    $block197->addField($field1134);
}

$field1135 = Vtiger_Field::getInstance('acc_shuttle_dest_ot', $module);
if ($field1135) {
    echo "<br> Field 'acc_shuttle_dest_ot' is already present <br>";
} else {
    $field1135 = new Vtiger_Field();
    $field1135->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONOT';
    $field1135->name = 'acc_shuttle_dest_ot';
    $field1135->table = 'vtiger_quotes';
    $field1135->column = 'acc_shuttle_dest_ot';
    $field1135->columntype = 'varchar(3)';
    $field1135->uitype = 56;
    $field1135->typeofdata = 'C~O';
    $field1135->displaytype = 1;
    $field1135->presence = 2;
    $field1135->defaultvalue = '';
    $field1135->quickcreate = 1;
    $field1135->summaryfield = 0;

    $block197->addField($field1135);
}

$field1136 = Vtiger_Field::getInstance('acc_shuttle_origin_over25', $module);
if ($field1136) {
    echo "<br> Field 'acc_shuttle_origin_over25' is already present <br>";
} else {
    $field1136 = new Vtiger_Field();
    $field1136->label = 'LBL_QUOTES_ACCSHUTTLEORIGINOVER25';
    $field1136->name = 'acc_shuttle_origin_over25';
    $field1136->table = 'vtiger_quotes';
    $field1136->column = 'acc_shuttle_origin_over25';
    $field1136->columntype = 'varchar(3)';
    $field1136->uitype = 56;
    $field1136->typeofdata = 'C~O';
    $field1136->displaytype = 1;
    $field1136->presence = 2;
    $field1136->defaultvalue = '';
    $field1136->quickcreate = 1;
    $field1136->summaryfield = 0;

    $block197->addField($field1136);
}

$field1137 = Vtiger_Field::getInstance('acc_shuttle_dest_over25', $module);
if ($field1137) {
    echo "<br> Field 'acc_shuttle_dest_over25' is already present <br>";
} else {
    $field1137 = new Vtiger_Field();
    $field1137->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONOVER25';
    $field1137->name = 'acc_shuttle_dest_over25';
    $field1137->table = 'vtiger_quotes';
    $field1137->column = 'acc_shuttle_dest_over25';
    $field1137->columntype = 'varchar(3)';
    $field1137->uitype = 56;
    $field1137->typeofdata = 'C~O';
    $field1137->displaytype = 1;
    $field1137->presence = 2;
    $field1137->defaultvalue = '';
    $field1137->quickcreate = 1;
    $field1137->summaryfield = 0;

    $block197->addField($field1137);
}

$field1138 = Vtiger_Field::getInstance('acc_shuttle_origin_miles', $module);
if ($field1138) {
    echo "<br> Field 'acc_shuttle_origin_miles' is already present <br>";
} else {
    $field1138 = new Vtiger_Field();
    $field1138->label = 'LBL_QUOTES_ACCSHUTTLEORIGINMILES';
    $field1138->name = 'acc_shuttle_origin_miles';
    $field1138->table = 'vtiger_quotes';
    $field1138->column = 'acc_shuttle_origin_miles';
    $field1138->columntype = 'int(10)';
    $field1138->uitype = 7;
    $field1138->typeofdata = 'I~O';
    $field1138->displaytype = 1;
    $field1138->presence = 2;
    $field1138->defaultvalue = '';
    $field1138->quickcreate = 1;
    $field1138->summaryfield = 0;

    $block197->addField($field1138);
}

$field1139 = Vtiger_Field::getInstance('acc_shuttle_dest_miles', $module);
if ($field1139) {
    echo "<br> Field 'acc_shuttle_dest_miles' is already present <br>";
} else {
    $field1139 = new Vtiger_Field();
    $field1139->label = 'LBL_QUOTES_ACCSHUTTLEDESTINATIONMILES';
    $field1139->name = 'acc_shuttle_dest_miles';
    $field1139->table = 'vtiger_quotes';
    $field1139->column = 'acc_shuttle_dest_miles';
    $field1139->columntype = 'int(10)';
    $field1139->uitype = 7;
    $field1139->typeofdata = 'I~O';
    $field1139->displaytype = 1;
    $field1139->presence = 2;
    $field1139->defaultvalue = '';
    $field1139->quickcreate = 1;
    $field1139->summaryfield = 0;

    $block197->addField($field1139);
}

$field1140 = Vtiger_Field::getInstance('acc_ot_origin_weight', $module);
if ($field1140) {
    echo "<br> Field 'acc_ot_origin_weight' is already present <br>";
} else {
    $field1140 = new Vtiger_Field();
    $field1140->label = 'LBL_QUOTES_ACCOTORIGINWEIGHT';
    $field1140->name = 'acc_ot_origin_weight';
    $field1140->table = 'vtiger_quotes';
    $field1140->column = 'acc_ot_origin_weight';
    $field1140->columntype = 'int(10)';
    $field1140->uitype = 7;
    $field1140->typeofdata = 'I~O';
    $field1140->displaytype = 1;
    $field1140->presence = 2;
    $field1140->defaultvalue = '';
    $field1140->quickcreate = 1;
    $field1140->summaryfield = 0;

    $block197->addField($field1140);
}

$field1141 = Vtiger_Field::getInstance('acc_ot_dest_weight', $module);
if ($field1141) {
    echo "<br> Field 'acc_ot_dest_weight' is already present <br>";
} else {
    $field1141 = new Vtiger_Field();
    $field1141->label = 'LBL_QUOTES_ACCOTDESTINATIONWEIGHT';
    $field1141->name = 'acc_ot_dest_weight';
    $field1141->table = 'vtiger_quotes';
    $field1141->column = 'acc_ot_dest_weight';
    $field1141->columntype = 'int(10)';
    $field1141->uitype = 7;
    $field1141->typeofdata = 'I~O';
    $field1141->displaytype = 1;
    $field1141->presence = 2;
    $field1141->defaultvalue = '';
    $field1141->quickcreate = 1;
    $field1141->summaryfield = 0;

    $block197->addField($field1141);
}

$field1142 = Vtiger_Field::getInstance('acc_ot_origin_applied', $module);
if ($field1142) {
    echo "<br> Field 'acc_ot_origin_applied' is already present <br>";
} else {
    $field1142 = new Vtiger_Field();
    $field1142->label = 'LBL_QUOTES_ACCOTORIGINAPPLIED';
    $field1142->name = 'acc_ot_origin_applied';
    $field1142->table = 'vtiger_quotes';
    $field1142->column = 'acc_ot_origin_applied';
    $field1142->columntype = 'varchar(3)';
    $field1142->uitype = 56;
    $field1142->typeofdata = 'C~O';
    $field1142->displaytype = 1;
    $field1142->presence = 2;
    $field1142->defaultvalue = '';
    $field1142->quickcreate = 1;
    $field1142->summaryfield = 0;

    $block197->addField($field1142);
}

$field1143 = Vtiger_Field::getInstance('acc_ot_dest_applied', $module);
if ($field1143) {
    echo "<br> Field 'acc_ot_dest_applied' is already present <br>";
} else {
    $field1143 = new Vtiger_Field();
    $field1143->label = 'LBL_QUOTES_ACCOTDESTINATIONAPPLIED';
    $field1143->name = 'acc_ot_dest_applied';
    $field1143->table = 'vtiger_quotes';
    $field1143->column = 'acc_ot_dest_applied';
    $field1143->columntype = 'varchar(3)';
    $field1143->uitype = 56;
    $field1143->typeofdata = 'C~O';
    $field1143->displaytype = 1;
    $field1143->presence = 2;
    $field1143->defaultvalue = '';
    $field1143->quickcreate = 1;
    $field1143->summaryfield = 0;

    $block197->addField($field1143);
}

$field1144 = Vtiger_Field::getInstance('acc_selfstg_origin_weight', $module);
if ($field1144) {
    echo "<br> Field 'acc_selfstg_origin_weight' is already present <br>";
} else {
    $field1144 = new Vtiger_Field();
    $field1144->label = 'LBL_QUOTES_ACCSELFSTGORIGINWEIGHT';
    $field1144->name = 'acc_selfstg_origin_weight';
    $field1144->table = 'vtiger_quotes';
    $field1144->column = 'acc_selfstg_origin_weight';
    $field1144->columntype = 'int(10)';
    $field1144->uitype = 7;
    $field1144->typeofdata = 'I~O';
    $field1144->displaytype = 1;
    $field1144->presence = 2;
    $field1144->defaultvalue = '';
    $field1144->quickcreate = 1;
    $field1144->summaryfield = 0;

    $block197->addField($field1144);
}

$field1145 = Vtiger_Field::getInstance('acc_selfstg_dest_weight', $module);
if ($field1145) {
    echo "<br> Field 'acc_selfstg_dest_weight' is already present <br>";
} else {
    $field1145 = new Vtiger_Field();
    $field1145->label = 'LBL_QUOTES_ACCSELFSTGDESTINATIONWEIGHT';
    $field1145->name = 'acc_selfstg_dest_weight';
    $field1145->table = 'vtiger_quotes';
    $field1145->column = 'acc_selfstg_dest_weight';
    $field1145->columntype = 'int(10)';
    $field1145->uitype = 7;
    $field1145->typeofdata = 'I~O';
    $field1145->displaytype = 1;
    $field1145->presence = 2;
    $field1145->defaultvalue = '';
    $field1145->quickcreate = 1;
    $field1145->summaryfield = 0;

    $block197->addField($field1145);
}

$field1146 = Vtiger_Field::getInstance('acc_selfstg_origin_applied', $module);
if ($field1146) {
    echo "<br> Field 'acc_selfstg_origin_applied' is already present <br>";
} else {
    $field1146 = new Vtiger_Field();
    $field1146->label = 'LBL_QUOTES_ACCSELFSTGORIGINAPPLIED';
    $field1146->name = 'acc_selfstg_origin_applied';
    $field1146->table = 'vtiger_quotes';
    $field1146->column = 'acc_selfstg_origin_applied';
    $field1146->columntype = 'varchar(3)';
    $field1146->uitype = 56;
    $field1146->typeofdata = 'C~O';
    $field1146->displaytype = 1;
    $field1146->presence = 2;
    $field1146->defaultvalue = '';
    $field1146->quickcreate = 1;
    $field1146->summaryfield = 0;

    $block197->addField($field1146);
}

$field1147 = Vtiger_Field::getInstance('acc_selfstg_dest_applied', $module);
if ($field1147) {
    echo "<br> Field 'acc_selfstg_dest_applied' is already present <br>";
} else {
    $field1147 = new Vtiger_Field();
    $field1147->label = 'LBL_QUOTES_ACCSELFSTGDESTINATIONAPPLIED';
    $field1147->name = 'acc_selfstg_dest_applied';
    $field1147->table = 'vtiger_quotes';
    $field1147->column = 'acc_selfstg_dest_applied';
    $field1147->columntype = 'varchar(3)';
    $field1147->uitype = 56;
    $field1147->typeofdata = 'C~O';
    $field1147->displaytype = 1;
    $field1147->presence = 2;
    $field1147->defaultvalue = '';
    $field1147->quickcreate = 1;
    $field1147->summaryfield = 0;

    $block197->addField($field1147);
}

$field1148 = Vtiger_Field::getInstance('acc_selfstg_origin_ot', $module);
if ($field1148) {
    echo "<br> Field 'acc_selfstg_origin_ot' is already present <br>";
} else {
    $field1148 = new Vtiger_Field();
    $field1148->label = 'LBL_QUOTES_ACCSELFSTGORIGINOT';
    $field1148->name = 'acc_selfstg_origin_ot';
    $field1148->table = 'vtiger_quotes';
    $field1148->column = 'acc_selfstg_origin_ot';
    $field1148->columntype = 'varchar(3)';
    $field1148->uitype = 56;
    $field1148->typeofdata = 'C~O';
    $field1148->displaytype = 1;
    $field1148->presence = 2;
    $field1148->defaultvalue = '';
    $field1148->quickcreate = 1;
    $field1148->summaryfield = 0;

    $block197->addField($field1148);
}

$field1149 = Vtiger_Field::getInstance('acc_selfstg_dest_ot', $module);
if ($field1149) {
    echo "<br> Field 'acc_selfstg_dest_ot' is already present <br>";
} else {
    $field1149 = new Vtiger_Field();
    $field1149->label = 'LBL_QUOTES_ACCSELFSTGDESTINATIONOT';
    $field1149->name = 'acc_selfstg_dest_ot';
    $field1149->table = 'vtiger_quotes';
    $field1149->column = 'acc_selfstg_dest_ot';
    $field1149->columntype = 'varchar(3)';
    $field1149->uitype = 56;
    $field1149->typeofdata = 'C~O';
    $field1149->displaytype = 1;
    $field1149->presence = 2;
    $field1149->defaultvalue = '';
    $field1149->quickcreate = 1;
    $field1149->summaryfield = 0;

    $block197->addField($field1149);
}

$field1150 = Vtiger_Field::getInstance('acc_exlabor_origin_hours', $module);
if ($field1150) {
    echo "<br> Field 'acc_exlabor_origin_hours' is already present <br>";
} else {
    $field1150 = new Vtiger_Field();
    $field1150->label = 'LBL_QUOTES_ACCEXLABORORIGINHOURS';
    $field1150->name = 'acc_exlabor_origin_hours';
    $field1150->table = 'vtiger_quotes';
    $field1150->column = 'acc_exlabor_origin_hours';
    $field1150->columntype = 'int(5)';
    $field1150->uitype = 7;
    $field1150->typeofdata = 'I~O';
    $field1150->displaytype = 1;
    $field1150->presence = 2;
    $field1150->defaultvalue = '';
    $field1150->quickcreate = 1;
    $field1150->summaryfield = 0;

    $block197->addField($field1150);
}

$field1151 = Vtiger_Field::getInstance('acc_exlabor_dest_hours', $module);
if ($field1151) {
    echo "<br> Field 'acc_exlabor_dest_hours' is already present <br>";
} else {
    $field1151 = new Vtiger_Field();
    $field1151->label = 'LBL_QUOTES_ACCEXLABORDESTINATIONHOURS';
    $field1151->name = 'acc_exlabor_dest_hours';
    $field1151->table = 'vtiger_quotes';
    $field1151->column = 'acc_exlabor_dest_hours';
    $field1151->columntype = 'int(5)';
    $field1151->uitype = 7;
    $field1151->typeofdata = 'I~O';
    $field1151->displaytype = 1;
    $field1151->presence = 2;
    $field1151->defaultvalue = '';
    $field1151->quickcreate = 1;
    $field1151->summaryfield = 0;

    $block197->addField($field1151);
}

$field1152 = Vtiger_Field::getInstance('acc_exlabor_ot_origin_hours', $module);
if ($field1152) {
    echo "<br> Field 'acc_exlabor_ot_origin_hours' is already present <br>";
} else {
    $field1152 = new Vtiger_Field();
    $field1152->label = 'LBL_QUOTES_ACCEXLABOROTORIGINHOURS';
    $field1152->name = 'acc_exlabor_ot_origin_hours';
    $field1152->table = 'vtiger_quotes';
    $field1152->column = 'acc_exlabor_ot_origin_hours';
    $field1152->columntype = 'int(5)';
    $field1152->uitype = 7;
    $field1152->typeofdata = 'I~O';
    $field1152->displaytype = 1;
    $field1152->presence = 2;
    $field1152->defaultvalue = '';
    $field1152->quickcreate = 1;
    $field1152->summaryfield = 0;

    $block197->addField($field1152);
}

$field1153 = Vtiger_Field::getInstance('acc_exlabor_ot_dest_hours', $module);
if ($field1153) {
    echo "<br> Field 'acc_exlabor_ot_dest_hours' is already present <br>";
} else {
    $field1153 = new Vtiger_Field();
    $field1153->label = 'LBL_QUOTES_ACCEXLABOROTDESTINATIONHOURS';
    $field1153->name = 'acc_exlabor_ot_dest_hours';
    $field1153->table = 'vtiger_quotes';
    $field1153->column = 'acc_exlabor_ot_dest_hours';
    $field1153->columntype = 'int(5)';
    $field1153->uitype = 7;
    $field1153->typeofdata = 'I~O';
    $field1153->displaytype = 1;
    $field1153->presence = 2;
    $field1153->defaultvalue = '';
    $field1153->quickcreate = 1;
    $field1153->summaryfield = 0;

    $block197->addField($field1153);
}

$field1154 = Vtiger_Field::getInstance('acc_wait_origin_hours', $module);
if ($field1154) {
    echo "<br> Field 'acc_wait_origin_hours' is already present <br>";
} else {
    $field1154 = new Vtiger_Field();
    $field1154->label = 'LBL_QUOTES_ACCWAITORIGINHOURS';
    $field1154->name = 'acc_wait_origin_hours';
    $field1154->table = 'vtiger_quotes';
    $field1154->column = 'acc_wait_origin_hours';
    $field1154->columntype = 'int(5)';
    $field1154->uitype = 7;
    $field1154->typeofdata = 'I~O';
    $field1154->displaytype = 1;
    $field1154->presence = 2;
    $field1154->defaultvalue = '';
    $field1154->quickcreate = 1;
    $field1154->summaryfield = 0;

    $block197->addField($field1154);
}

$field1155 = Vtiger_Field::getInstance('acc_wait_dest_hours', $module);
if ($field1155) {
    echo "<br> Field 'acc_wait_dest_hours' is already present <br>";
} else {
    $field1155 = new Vtiger_Field();
    $field1155->label = 'LBL_QUOTES_ACCWAITDESTINATIONHOURS';
    $field1155->name = 'acc_wait_dest_hours';
    $field1155->table = 'vtiger_quotes';
    $field1155->column = 'acc_wait_dest_hours';
    $field1155->columntype = 'int(5)';
    $field1155->uitype = 7;
    $field1155->typeofdata = 'I~O';
    $field1155->displaytype = 1;
    $field1155->presence = 2;
    $field1155->defaultvalue = '';
    $field1155->quickcreate = 1;
    $field1155->summaryfield = 0;

    $block197->addField($field1155);
}

$field1156 = Vtiger_Field::getInstance('acc_wait_ot_origin_hours', $module);
if ($field1156) {
    echo "<br> Field 'acc_wait_ot_origin_hours' is already present <br>";
} else {
    $field1156 = new Vtiger_Field();
    $field1156->label = 'LBL_QUOTES_ACCWAITOTORIGINHOURS';
    $field1156->name = 'acc_wait_ot_origin_hours';
    $field1156->table = 'vtiger_quotes';
    $field1156->column = 'acc_wait_ot_origin_hours';
    $field1156->columntype = 'int(5)';
    $field1156->uitype = 7;
    $field1156->typeofdata = 'I~O';
    $field1156->displaytype = 1;
    $field1156->presence = 2;
    $field1156->defaultvalue = '';
    $field1156->quickcreate = 1;
    $field1156->summaryfield = 0;

    $block197->addField($field1156);
}

$field1157 = Vtiger_Field::getInstance('acc_wait_ot_dest_hours', $module);
if ($field1157) {
    echo "<br> Field 'acc_wait_ot_dest_hours' is already present <br>";
} else {
    $field1157 = new Vtiger_Field();
    $field1157->label = 'LBL_QUOTES_ACCWAITOTDESTINATIONHOURS';
    $field1157->name = 'acc_wait_ot_dest_hours';
    $field1157->table = 'vtiger_quotes';
    $field1157->column = 'acc_wait_ot_dest_hours';
    $field1157->columntype = 'int(5)';
    $field1157->uitype = 7;
    $field1157->typeofdata = 'I~O';
    $field1157->displaytype = 1;
    $field1157->presence = 2;
    $field1157->defaultvalue = '';
    $field1157->quickcreate = 1;
    $field1157->summaryfield = 0;

    $block197->addField($field1157);
}

$field1158 = Vtiger_Field::getInstance('local_bl_discount', $module);
if ($field1158) {
    echo "<br> Field 'local_bl_discount' is already present <br>";
} else {
    $field1158 = new Vtiger_Field();
    $field1158->label = 'LBL_QUOTES_LOCALBLDISCOUNT';
    $field1158->name = 'local_bl_discount';
    $field1158->table = 'vtiger_quotes';
    $field1158->column = 'local_bl_discount';
    $field1158->columntype = 'decimal(12,3)';
    $field1158->uitype = 7;
    $field1158->typeofdata = 'NN~O';
    $field1158->displaytype = 1;
    $field1158->presence = 2;
    $field1158->defaultvalue = '';
    $field1158->quickcreate = 1;
    $field1158->summaryfield = 0;

    $block193->addField($field1158);
}

$field1711 = Vtiger_Field::getInstance('load_date', $module);
if ($field1711) {
    echo "<br> Field 'load_date' is already present <br>";
} else {
    $field1711 = new Vtiger_Field();
    $field1711->label = 'LBL_QUOTES_LOAD_DATE';
    $field1711->name = 'load_date';
    $field1711->table = 'vtiger_quotes';
    $field1711->column = 'load_date';
    $field1711->columntype = 'date';
    $field1711->uitype = 5;
    $field1711->typeofdata = 'D~O';
    $field1711->displaytype = 1;
    $field1711->presence = 2;
    $field1711->defaultvalue = '';
    $field1711->quickcreate = 2;
    $field1711->summaryfield = 0;

    $block190->addField($field1711);
}

$field1712 = Vtiger_Field::getInstance('contract', $module);
if ($field1712) {
    echo "<br> Field 'contract' is already present <br>";
} else {
    $field1712 = new Vtiger_Field();
    $field1712->label = 'LBL_QUOTES_CONTRACT';
    $field1712->name = 'contract';
    $field1712->table = 'vtiger_quotes';
    $field1712->column = 'contract';
    $field1712->columntype = 'int(19)';
    $field1712->uitype = 10;
    $field1712->typeofdata = 'V~O';
    $field1712->displaytype = 1;
    $field1712->presence = 2;
    $field1712->defaultvalue = '';
    $field1712->quickcreate = 1;
    $field1712->summaryfield = 0;

    $block190->addField($field1712);

    $field1712->setRelatedModules(['Contracts']);
}

$field1713 = Vtiger_Field::getInstance('irr_charge', $module);
if ($field1713) {
    echo "<br> Field 'irr_charge' is already present <br>";
} else {
    $field1713 = new Vtiger_Field();
    $field1713->label = 'LBL_QUOTES_IRR';
    $field1713->name = 'irr_charge';
    $field1713->table = 'vtiger_quotes';
    $field1713->column = 'irr_charge';
    $field1713->columntype = 'decimal(7,2)';
    $field1713->uitype = 9;
    $field1713->typeofdata = 'N~O';
    $field1713->displaytype = 1;
    $field1713->presence = 2;
    $field1713->defaultvalue = '';
    $field1713->quickcreate = 1;
    $field1713->summaryfield = 0;

    $block194->addField($field1713);
}

$field1714 = Vtiger_Field::getInstance('linehaul_disc', $module);
if ($field1714) {
    echo "<br> Field 'linehaul_disc' is already present <br>";
} else {
    $field1714 = new Vtiger_Field();
    $field1714->label = 'LBL_QUOTES_LINEHAUL_DISC';
    $field1714->name = 'linehaul_disc';
    $field1714->table = 'vtiger_quotes';
    $field1714->column = 'linehaul_disc';
    $field1714->columntype = 'decimal(7,2)';
    $field1714->uitype = 9;
    $field1714->typeofdata = 'N~O';
    $field1714->displaytype = 1;
    $field1714->presence = 2;
    $field1714->defaultvalue = '';
    $field1714->quickcreate = 1;
    $field1714->summaryfield = 0;

    $block194->addField($field1714);
}

$field1715 = Vtiger_Field::getInstance('accessorial_disc', $module);
if ($field1715) {
    echo "<br> Field 'accessorial_disc' is already present <br>";
} else {
    $field1715 = new Vtiger_Field();
    $field1715->label = 'LBL_QUOTES_ACCESSORIAL_DISC';
    $field1715->name = 'accessorial_disc';
    $field1715->table = 'vtiger_quotes';
    $field1715->column = 'accessorial_disc';
    $field1715->columntype = 'decimal(7,2)';
    $field1715->uitype = 9;
    $field1715->typeofdata = 'N~O';
    $field1715->displaytype = 1;
    $field1715->presence = 2;
    $field1715->defaultvalue = '';
    $field1715->quickcreate = 1;
    $field1715->summaryfield = 0;

    $block194->addField($field1715);
}

$field1716 = Vtiger_Field::getInstance('packing_disc', $module);
if ($field1716) {
    echo "<br> Field 'packing_disc' is already present <br>";
} else {
    $field1716 = new Vtiger_Field();
    $field1716->label = 'LBL_QUOTES_PACKING_DISC';
    $field1716->name = 'packing_disc';
    $field1716->table = 'vtiger_quotes';
    $field1716->column = 'packing_disc';
    $field1716->columntype = 'decimal(7,2)';
    $field1716->uitype = 9;
    $field1716->typeofdata = 'N~O';
    $field1716->displaytype = 1;
    $field1716->presence = 2;
    $field1716->defaultvalue = '';
    $field1716->quickcreate = 1;
    $field1716->summaryfield = 0;

    $block194->addField($field1716);
}

$field1717 = Vtiger_Field::getInstance('sit_disc', $module);
if ($field1717) {
    echo "<br> Field 'sit_disc' is already present <br>";
} else {
    $field1717 = new Vtiger_Field();
    $field1717->label = 'LBL_QUOTES_SIT_DISC';
    $field1717->name = 'sit_disc';
    $field1717->table = 'vtiger_quotes';
    $field1717->column = 'sit_disc';
    $field1717->columntype = 'decimal(7,2)';
    $field1717->uitype = 9;
    $field1717->typeofdata = 'N~O';
    $field1717->displaytype = 1;
    $field1717->presence = 2;
    $field1717->defaultvalue = '';
    $field1717->quickcreate = 1;
    $field1717->summaryfield = 0;

    $block194->addField($field1717);
}

$field1727 = Vtiger_Field::getInstance('interstate_effective_date', $module);
if ($field1727) {
    echo "<br> Field 'interstate_effective_date' is already present <br>";
} else {
    $field1727 = new Vtiger_Field();
    $field1727->label = 'LBL_QUOTES_EFFECTIVEDATE';
    $field1727->name = 'interstate_effective_date';
    $field1727->table = 'vtiger_quotes';
    $field1727->column = 'interstate_effective_date';
    $field1727->columntype = 'date';
    $field1727->uitype = 5;
    $field1727->typeofdata = 'D~O';
    $field1727->displaytype = 1;
    $field1727->presence = 2;
    $field1727->defaultvalue = '';
    $field1727->quickcreate = 0;
    $field1727->summaryfield = 0;

    $block194->addField($field1727);
}

$field1739 = Vtiger_Field::getInstance('bulky_article_changes', $module);
if ($field1739) {
    echo "<br> Field 'bulky_article_changes' is already present <br>";
} else {
    $field1739 = new Vtiger_Field();
    $field1739->label = 'LBL_BULKY_ARTICLE_CHANGES';
    $field1739->name = 'bulky_article_changes';
    $field1739->table = 'vtiger_quotes';
    $field1739->column = 'bulky_article_changes';
    $field1739->columntype = 'float(10,4)';
    $field1739->uitype = 7;
    $field1739->typeofdata = 'V~O';
    $field1739->displaytype = 1;
    $field1739->presence = 2;
    $field1739->defaultvalue = '';
    $field1739->quickcreate = 1;
    $field1739->summaryfield = 0;

    $block197->addField($field1739);
}

$field1741 = Vtiger_Field::getInstance('elevator_origin_occurrence', $module);
if ($field1741) {
    echo "<br> Field 'elevator_origin_occurrence' is already present <br>";
} else {
    $field1741 = new Vtiger_Field();
    $field1741->label = 'LBL_QUOTES_ELEVATOR_ORIGIN_OCCURRENCE';
    $field1741->name = 'elevator_origin_occurrence';
    $field1741->table = 'vtiger_quotes';
    $field1741->column = 'elevator_origin_occurrence';
    $field1741->columntype = 'int(19)';
    $field1741->uitype = 7;
    $field1741->typeofdata = 'V~O';
    $field1741->displaytype = 1;
    $field1741->presence = 2;
    $field1741->defaultvalue = '';
    $field1741->quickcreate = 1;
    $field1741->summaryfield = 0;

    $block283->addField($field1741);
}

$field1743 = Vtiger_Field::getInstance('elevator_destination_occurrence', $module);
if ($field1743) {
    echo "<br> Field 'elevator_destination_occurrence' is already present <br>";
} else {
    $field1743 = new Vtiger_Field();
    $field1743->label = 'LBL_QUOTES_ELEVATOR_DESTINATION_OCCURRENCE';
    $field1743->name = 'elevator_destination_occurrence';
    $field1743->table = 'vtiger_quotes';
    $field1743->column = 'elevator_destination_occurrence';
    $field1743->columntype = 'int(19)';
    $field1743->uitype = 7;
    $field1743->typeofdata = 'V~O';
    $field1743->displaytype = 1;
    $field1743->presence = 2;
    $field1743->defaultvalue = '';
    $field1743->quickcreate = 1;
    $field1743->summaryfield = 0;

    $block283->addField($field1743);
}

$field1745 = Vtiger_Field::getInstance('elevator_origin_CTW', $module);
if ($field1745) {
    echo "<br> Field 'elevator_origin_CTW' is already present <br>";
} else {
    $field1745 = new Vtiger_Field();
    $field1745->label = 'LBL_QUOTES_ELEVATOR_ORIGIN_CTW';
    $field1745->name = 'elevator_origin_CTW';
    $field1745->table = 'vtiger_quotes';
    $field1745->column = 'elevator_origin_CTW';
    $field1745->columntype = 'int(19)';
    $field1745->uitype = 7;
    $field1745->typeofdata = 'V~O';
    $field1745->displaytype = 1;
    $field1745->presence = 2;
    $field1745->defaultvalue = '';
    $field1745->quickcreate = 1;
    $field1745->summaryfield = 0;

    $block283->addField($field1745);
}

$field1747 = Vtiger_Field::getInstance('elevator_destination_CTW', $module);
if ($field1747) {
    echo "<br> Field 'elevator_destination_CTW' is already present <br>";
} else {
    $field1747 = new Vtiger_Field();
    $field1747->label = 'LBL_QUOTES_ELEVATOR_DESTINATION_CTW';
    $field1747->name = 'elevator_destination_CTW';
    $field1747->table = 'vtiger_quotes';
    $field1747->column = 'elevator_destination_CTW';
    $field1747->columntype = 'int(19)';
    $field1747->uitype = 7;
    $field1747->typeofdata = 'V~O';
    $field1747->displaytype = 1;
    $field1747->presence = 2;
    $field1747->defaultvalue = '';
    $field1747->quickcreate = 1;
    $field1747->summaryfield = 0;

    $block283->addField($field1747);
}

$field1749 = Vtiger_Field::getInstance('stair_origin_occurrence', $module);
if ($field1749) {
    echo "<br> Field 'stair_origin_occurrence' is already present <br>";
} else {
    $field1749 = new Vtiger_Field();
    $field1749->label = 'LBL_QUOTES_STAIR_ORIGIN_OCCURRENCE';
    $field1749->name = 'stair_origin_occurrence';
    $field1749->table = 'vtiger_quotes';
    $field1749->column = 'stair_origin_occurrence';
    $field1749->columntype = 'int(19)';
    $field1749->uitype = 7;
    $field1749->typeofdata = 'V~O';
    $field1749->displaytype = 1;
    $field1749->presence = 2;
    $field1749->defaultvalue = '';
    $field1749->quickcreate = 1;
    $field1749->summaryfield = 0;

    $block284->addField($field1749);
}

$field1751 = Vtiger_Field::getInstance('stair_destination_occurrence', $module);
if ($field1751) {
    echo "<br> Field 'stair_destination_occurrence' is already present <br>";
} else {
    $field1751 = new Vtiger_Field();
    $field1751->label = 'LBL_QUOTES_STAIR_DESTINATION_OCCURRENCE';
    $field1751->name = 'stair_destination_occurrence';
    $field1751->table = 'vtiger_quotes';
    $field1751->column = 'stair_destination_occurrence';
    $field1751->columntype = 'int(19)';
    $field1751->uitype = 7;
    $field1751->typeofdata = 'V~O';
    $field1751->displaytype = 1;
    $field1751->presence = 2;
    $field1751->defaultvalue = '';
    $field1751->quickcreate = 1;
    $field1751->summaryfield = 0;

    $block284->addField($field1751);
}

$field1753 = Vtiger_Field::getInstance('stair_origin_CTW', $module);
if ($field1753) {
    echo "<br> Field 'stair_origin_CTW' is already present <br>";
} else {
    $field1753 = new Vtiger_Field();
    $field1753->label = 'LBL_QUOTES_STAIR_ORIGIN_CTW';
    $field1753->name = 'stair_origin_CTW';
    $field1753->table = 'vtiger_quotes';
    $field1753->column = 'stair_origin_CTW';
    $field1753->columntype = 'int(19)';
    $field1753->uitype = 7;
    $field1753->typeofdata = 'V~O';
    $field1753->displaytype = 1;
    $field1753->presence = 2;
    $field1753->defaultvalue = '';
    $field1753->quickcreate = 1;
    $field1753->summaryfield = 0;

    $block284->addField($field1753);
}

$field1755 = Vtiger_Field::getInstance('stair_destination_CTW', $module);
if ($field1755) {
    echo "<br> Field 'stair_destination_CTW' is already present <br>";
} else {
    $field1755 = new Vtiger_Field();
    $field1755->label = 'LBL_QUOTES_STAIR_DESTINATION_CTW';
    $field1755->name = 'stair_destination_CTW';
    $field1755->table = 'vtiger_quotes';
    $field1755->column = 'stair_destination_CTW';
    $field1755->columntype = 'int(19)';
    $field1755->uitype = 7;
    $field1755->typeofdata = 'V~O';
    $field1755->displaytype = 1;
    $field1755->presence = 2;
    $field1755->defaultvalue = '';
    $field1755->quickcreate = 1;
    $field1755->summaryfield = 0;

    $block284->addField($field1755);
}

$field1757 = Vtiger_Field::getInstance('longcarry_origin_occurrence', $module);
if ($field1757) {
    echo "<br> Field 'longcarry_origin_occurrence' is already present <br>";
} else {
    $field1757 = new Vtiger_Field();
    $field1757->label = 'LBL_QUOTES_LONGCARRY_ORIGIN_OCCURRENCE';
    $field1757->name = 'longcarry_origin_occurrence';
    $field1757->table = 'vtiger_quotes';
    $field1757->column = 'longcarry_origin_occurrence';
    $field1757->columntype = 'int(19)';
    $field1757->uitype = 7;
    $field1757->typeofdata = 'V~O';
    $field1757->displaytype = 1;
    $field1757->presence = 2;
    $field1757->defaultvalue = '';
    $field1757->quickcreate = 1;
    $field1757->summaryfield = 0;

    $block285->addField($field1757);
}

$field1759 = Vtiger_Field::getInstance('longcarry_destination_occurrence', $module);
if ($field1759) {
    echo "<br> Field 'longcarry_destination_occurrence' is already present <br>";
} else {
    $field1759 = new Vtiger_Field();
    $field1759->label = 'LBL_QUOTES_LONGCARRY_DESTINATION_OCCURRENCE';
    $field1759->name = 'longcarry_destination_occurrence';
    $field1759->table = 'vtiger_quotes';
    $field1759->column = 'longcarry_destination_occurrence';
    $field1759->columntype = 'int(19)';
    $field1759->uitype = 7;
    $field1759->typeofdata = 'V~O';
    $field1759->displaytype = 1;
    $field1759->presence = 2;
    $field1759->defaultvalue = '';
    $field1759->quickcreate = 1;
    $field1759->summaryfield = 0;

    $block285->addField($field1759);
}

$field1761 = Vtiger_Field::getInstance('longcarry_origin_CTW', $module);
if ($field1761) {
    echo "<br> Field 'longcarry_origin_CTW' is already present <br>";
} else {
    $field1761 = new Vtiger_Field();
    $field1761->label = 'LBL_QUOTES_LONGCARRY_ORIGIN_CTW';
    $field1761->name = 'longcarry_origin_CTW';
    $field1761->table = 'vtiger_quotes';
    $field1761->column = 'longcarry_origin_CTW';
    $field1761->columntype = 'int(19)';
    $field1761->uitype = 7;
    $field1761->typeofdata = 'V~O';
    $field1761->displaytype = 1;
    $field1761->presence = 2;
    $field1761->defaultvalue = '';
    $field1761->quickcreate = 1;
    $field1761->summaryfield = 0;

    $block285->addField($field1761);
}

$field1763 = Vtiger_Field::getInstance('longcarry_destination_CTW', $module);
if ($field1763) {
    echo "<br> Field 'longcarry_destination_CTW' is already present <br>";
} else {
    $field1763 = new Vtiger_Field();
    $field1763->label = 'LBL_QUOTES_LONGCARRY_DESTINATION_CTW';
    $field1763->name = 'longcarry_destination_CTW';
    $field1763->table = 'vtiger_quotes';
    $field1763->column = 'longcarry_destination_CTW';
    $field1763->columntype = 'int(19)';
    $field1763->uitype = 7;
    $field1763->typeofdata = 'V~O';
    $field1763->displaytype = 1;
    $field1763->presence = 2;
    $field1763->defaultvalue = '';
    $field1763->quickcreate = 1;
    $field1763->summaryfield = 0;

    $block285->addField($field1763);
}

$field1764 = Vtiger_Field::getInstance('rush_shipment_fee', $module);
if ($field1764) {
    echo "<br> Field 'rush_shipment_fee' is already present <br>";
} else {
    $field1764 = new Vtiger_Field();
    $field1764->label = 'LBL_RUSH_SHIPMENT_FEE';
    $field1764->name = 'rush_shipment_fee';
    $field1764->table = 'vtiger_quotes';
    $field1764->column = 'rush_shipment_fee';
    $field1764->columntype = 'varchar(3)';
    $field1764->uitype = 56;
    $field1764->typeofdata = 'C~O';
    $field1764->displaytype = 1;
    $field1764->presence = 2;
    $field1764->defaultvalue = '';
    $field1764->quickcreate = 1;
    $field1764->summaryfield = 0;

    $block197->addField($field1764);
}

$field1766 = Vtiger_Field::getInstance('appliance_service', $module);
if ($field1766) {
    echo "<br> Field 'appliance_service' is already present <br>";
} else {
    $field1766 = new Vtiger_Field();
    $field1766->label = 'LBL_APPLIANCE_SERVICE';
    $field1766->name = 'appliance_service';
    $field1766->table = 'vtiger_quotes';
    $field1766->column = 'appliance_service';
    $field1766->columntype = 'int(10)';
    $field1766->uitype = 7;
    $field1766->typeofdata = 'V~O';
    $field1766->displaytype = 1;
    $field1766->presence = 2;
    $field1766->defaultvalue = '';
    $field1766->quickcreate = 1;
    $field1766->summaryfield = 0;

    $block286->addField($field1766);
}

$field1767 = Vtiger_Field::getInstance('appliance_reservice', $module);
if ($field1767) {
    echo "<br> Field 'appliance_reservice' is already present <br>";
} else {
    $field1767 = new Vtiger_Field();
    $field1767->label = 'LBL_APPLIANCE_RESERVICE';
    $field1767->name = 'appliance_reservice';
    $field1767->table = 'vtiger_quotes';
    $field1767->column = 'appliance_reservice';
    $field1767->columntype = 'int(10)';
    $field1767->uitype = 7;
    $field1767->typeofdata = 'V~O';
    $field1767->displaytype = 1;
    $field1767->presence = 2;
    $field1767->defaultvalue = '';
    $field1767->quickcreate = 1;
    $field1767->summaryfield = 0;

    $block286->addField($field1767);
}

$field1770 = Vtiger_Field::getInstance('ori_sit2_date_in', $module);
if ($field1770) {
    echo "<br> Field 'ori_sit2_date_in' is already present <br>";
} else {
    $field1770 = new Vtiger_Field();
    $field1770->label = 'LBL_SIT_DATE_IN';
    $field1770->name = 'ori_sit2_date_in';
    $field1770->table = 'vtiger_quotes';
    $field1770->column = 'ori_sit2_date_in';
    $field1770->columntype = 'date';
    $field1770->uitype = 5;
    $field1770->typeofdata = 'D~O';
    $field1770->displaytype = 1;
    $field1770->presence = 2;
    $field1770->defaultvalue = '';
    $field1770->quickcreate = 1;
    $field1770->summaryfield = 0;

    $block287->addField($field1770);
}

$field1771 = Vtiger_Field::getInstance('des_sit2_date_in', $module);
if ($field1771) {
    echo "<br> Field 'des_sit2_date_in' is already present <br>";
} else {
    $field1771 = new Vtiger_Field();
    $field1771->label = 'LBL_DES_SIT_DATE_IN';
    $field1771->name = 'des_sit2_date_in';
    $field1771->table = 'vtiger_quotes';
    $field1771->column = 'des_sit2_date_in';
    $field1771->columntype = 'date';
    $field1771->uitype = 5;
    $field1771->typeofdata = 'D~O';
    $field1771->displaytype = 1;
    $field1771->presence = 2;
    $field1771->defaultvalue = '';
    $field1771->quickcreate = 1;
    $field1771->summaryfield = 0;

    $block287->addField($field1771);
}

$field1774 = Vtiger_Field::getInstance('ori_sit2_pickup_date', $module);
if ($field1774) {
    echo "<br> Field 'ori_sit2_pickup_date' is already present <br>";
} else {
    $field1774 = new Vtiger_Field();
    $field1774->label = 'LBL_SIT_PICKUP_DATE';
    $field1774->name = 'ori_sit2_pickup_date';
    $field1774->table = 'vtiger_quotes';
    $field1774->column = 'ori_sit2_pickup_date';
    $field1774->columntype = 'date';
    $field1774->uitype = 5;
    $field1774->typeofdata = 'D~O';
    $field1774->displaytype = 1;
    $field1774->presence = 2;
    $field1774->defaultvalue = '';
    $field1774->quickcreate = 1;
    $field1774->summaryfield = 0;

    $block287->addField($field1774);
}

$field1775 = Vtiger_Field::getInstance('des_sit2_pickup_date', $module);
if ($field1775) {
    echo "<br> Field 'des_sit2_pickup_date' is already present <br>";
} else {
    $field1775 = new Vtiger_Field();
    $field1775->label = 'LBL_DES_SIT_PICKUP_DATE';
    $field1775->name = 'des_sit2_pickup_date';
    $field1775->table = 'vtiger_quotes';
    $field1775->column = 'des_sit2_pickup_date';
    $field1775->columntype = 'date';
    $field1775->uitype = 5;
    $field1775->typeofdata = 'D~O';
    $field1775->displaytype = 1;
    $field1775->presence = 2;
    $field1775->defaultvalue = '';
    $field1775->quickcreate = 1;
    $field1775->summaryfield = 0;

    $block287->addField($field1775);
}

$field1778 = Vtiger_Field::getInstance('ori_sit2_number_days', $module);
if ($field1778) {
    echo "<br> Field 'ori_sit2_number_days' is already present <br>";
} else {
    $field1778 = new Vtiger_Field();
    $field1778->label = 'LBL_SIT_NUM_DAYS';
    $field1778->name = 'ori_sit2_number_days';
    $field1778->table = 'vtiger_quotes';
    $field1778->column = 'ori_sit2_number_days';
    $field1778->columntype = 'int(10)';
    $field1778->uitype = 7;
    $field1778->typeofdata = 'V~O';
    $field1778->displaytype = 1;
    $field1778->presence = 2;
    $field1778->defaultvalue = '';
    $field1778->quickcreate = 1;
    $field1778->summaryfield = 0;

    $block287->addField($field1778);
}

$field1779 = Vtiger_Field::getInstance('des_sit2_number_days', $module);
if ($field1779) {
    echo "<br> Field 'des_sit2_number_days' is already present <br>";
} else {
    $field1779 = new Vtiger_Field();
    $field1779->label = 'LBL_DES_SIT_NUM_DAYS';
    $field1779->name = 'des_sit2_number_days';
    $field1779->table = 'vtiger_quotes';
    $field1779->column = 'des_sit2_number_days';
    $field1779->columntype = 'int(10)';
    $field1779->uitype = 7;
    $field1779->typeofdata = 'V~O';
    $field1779->displaytype = 1;
    $field1779->presence = 2;
    $field1779->defaultvalue = '';
    $field1779->quickcreate = 1;
    $field1779->summaryfield = 0;

    $block287->addField($field1779);
}

$field1782 = Vtiger_Field::getInstance('ori_sit2_weight', $module);
if ($field1782) {
    echo "<br> Field 'ori_sit2_weight' is already present <br>";
} else {
    $field1782 = new Vtiger_Field();
    $field1782->label = 'LBL_SIT_WEIGHT';
    $field1782->name = 'ori_sit2_weight';
    $field1782->table = 'vtiger_quotes';
    $field1782->column = 'ori_sit2_weight';
    $field1782->columntype = 'int(10)';
    $field1782->uitype = 7;
    $field1782->typeofdata = 'V~O';
    $field1782->displaytype = 1;
    $field1782->presence = 2;
    $field1782->defaultvalue = '';
    $field1782->quickcreate = 1;
    $field1782->summaryfield = 0;

    $block287->addField($field1782);
}

$field1783 = Vtiger_Field::getInstance('des_sit2_weight', $module);
if ($field1783) {
    echo "<br> Field 'des_sit2_weight' is already present <br>";
} else {
    $field1783 = new Vtiger_Field();
    $field1783->label = 'LBL_DES_SIT_WEIGHT';
    $field1783->name = 'des_sit2_weight';
    $field1783->table = 'vtiger_quotes';
    $field1783->column = 'des_sit2_weight';
    $field1783->columntype = 'int(10)';
    $field1783->uitype = 7;
    $field1783->typeofdata = 'V~O';
    $field1783->displaytype = 1;
    $field1783->presence = 2;
    $field1783->defaultvalue = '';
    $field1783->quickcreate = 1;
    $field1783->summaryfield = 0;

    $block287->addField($field1783);
}

$field1786 = Vtiger_Field::getInstance('ori_sit2_container_or_warehouse', $module);
if ($field1786) {
    echo "<br> Field 'ori_sit2_container_or_warehouse' is already present <br>";
} else {
    $field1786 = new Vtiger_Field();
    $field1786->label = 'LBL_SIT_CONTAINER_WAREHOUSE';
    $field1786->name = 'ori_sit2_container_or_warehouse';
    $field1786->table = 'vtiger_quotes';
    $field1786->column = 'ori_sit2_container_or_warehouse';
    $field1786->columntype = 'varchar(3)';
    $field1786->uitype = 56;
    $field1786->typeofdata = 'C~O';
    $field1786->displaytype = 1;
    $field1786->presence = 2;
    $field1786->defaultvalue = '';
    $field1786->quickcreate = 1;
    $field1786->summaryfield = 0;

    $block287->addField($field1786);
}

$field1787 = Vtiger_Field::getInstance('des_sit2_container_or_warehouse', $module);
if ($field1787) {
    echo "<br> Field 'des_sit2_container_or_warehouse' is already present <br>";
} else {
    $field1787 = new Vtiger_Field();
    $field1787->label = 'LBL_DES_SIT_CONTAINER_WAREHOUSE';
    $field1787->name = 'des_sit2_container_or_warehouse';
    $field1787->table = 'vtiger_quotes';
    $field1787->column = 'des_sit2_container_or_warehouse';
    $field1787->columntype = 'varchar(3)';
    $field1787->uitype = 56;
    $field1787->typeofdata = 'C~O';
    $field1787->displaytype = 1;
    $field1787->presence = 2;
    $field1787->defaultvalue = '';
    $field1787->quickcreate = 1;
    $field1787->summaryfield = 0;

    $block287->addField($field1787);
}

$field1790 = Vtiger_Field::getInstance('ori_sit2_container_number', $module);
if ($field1790) {
    echo "<br> Field 'ori_sit2_container_number' is already present <br>";
} else {
    $field1790 = new Vtiger_Field();
    $field1790->label = 'LBL_SIT_CONTAINER_NUMBER';
    $field1790->name = 'ori_sit2_container_number';
    $field1790->table = 'vtiger_quotes';
    $field1790->column = 'ori_sit2_container_number';
    $field1790->columntype = 'varchar(15)';
    $field1790->uitype = 7;
    $field1790->typeofdata = 'V~O';
    $field1790->displaytype = 1;
    $field1790->presence = 2;
    $field1790->defaultvalue = '';
    $field1790->quickcreate = 1;
    $field1790->summaryfield = 0;

    $block287->addField($field1790);
}

$field1791 = Vtiger_Field::getInstance('des_sit2_container_number', $module);
if ($field1791) {
    echo "<br> Field 'des_sit2_container_number' is already present <br>";
} else {
    $field1791 = new Vtiger_Field();
    $field1791->label = 'LBL_DES_SIT_CONTAINER_NUMBER';
    $field1791->name = 'des_sit2_container_number';
    $field1791->table = 'vtiger_quotes';
    $field1791->column = 'des_sit2_container_number';
    $field1791->columntype = 'varchar(15)';
    $field1791->uitype = 7;
    $field1791->typeofdata = 'V~O';
    $field1791->displaytype = 1;
    $field1791->presence = 2;
    $field1791->defaultvalue = '';
    $field1791->quickcreate = 1;
    $field1791->summaryfield = 0;

    $block287->addField($field1791);
}

$field1795 = Vtiger_Field::getInstance('accesorial_ot_loading', $module);
if ($field1795) {
    echo "<br> Field 'accesorial_ot_loading' is already present <br>";
} else {
    $field1795 = new Vtiger_Field();
    $field1795->label = 'LBL_ACC_OT_LOADING';
    $field1795->name = 'accesorial_ot_loading';
    $field1795->table = 'vtiger_quotes';
    $field1795->column = 'accesorial_ot_loading';
    $field1795->columntype = 'varchar(15)';
    $field1795->uitype = 7;
    $field1795->typeofdata = 'V~O';
    $field1795->displaytype = 1;
    $field1795->presence = 2;
    $field1795->defaultvalue = '';
    $field1795->quickcreate = 1;
    $field1795->summaryfield = 0;

    $block197->addField($field1795);
}

$field1797 = Vtiger_Field::getInstance('accesorial_ot_unloading', $module);
if ($field1797) {
    echo "<br> Field 'accesorial_ot_unloading' is already present <br>";
} else {
    $field1797 = new Vtiger_Field();
    $field1797->label = 'LBL_ACC_OT_UNLOADING';
    $field1797->name = 'accesorial_ot_unloading';
    $field1797->table = 'vtiger_quotes';
    $field1797->column = 'accesorial_ot_unloading';
    $field1797->columntype = 'varchar(15)';
    $field1797->uitype = 7;
    $field1797->typeofdata = 'V~O';
    $field1797->displaytype = 1;
    $field1797->presence = 2;
    $field1797->defaultvalue = '';
    $field1797->quickcreate = 1;
    $field1797->summaryfield = 0;

    $block197->addField($field1797);
}

$field1799 = Vtiger_Field::getInstance('accesorial_fuel_surcharge', $module);
if ($field1799) {
    echo "<br> Field 'accesorial_fuel_surcharge' is already present <br>";
} else {
    $field1799 = new Vtiger_Field();
    $field1799->label = 'LBL_FUEL_SURCHARGE';
    $field1799->name = 'accesorial_fuel_surcharge';
    $field1799->table = 'vtiger_quotes';
    $field1799->column = 'accesorial_fuel_surcharge';
    $field1799->columntype = 'decimal(5,4)';
    $field1799->uitype = 9;
    $field1799->typeofdata = 'V~O';
    $field1799->displaytype = 1;
    $field1799->presence = 2;
    $field1799->defaultvalue = '';
    $field1799->quickcreate = 0;
    $field1799->summaryfield = 0;

    $block197->addField($field1799);
}

$field1801 = Vtiger_Field::getInstance('space_reserve_bool', $module);
if ($field1801) {
    echo "<br> Field 'space_reserve_bool' is already present <br>";
} else {
    $field1801 = new Vtiger_Field();
    $field1801->label = 'LBL_SPACE_RESERVE_BOOL';
    $field1801->name = 'space_reserve_bool';
    $field1801->table = 'vtiger_quotes';
    $field1801->column = 'space_reserve_bool';
    $field1801->columntype = 'varchar(3)';
    $field1801->uitype = 56;
    $field1801->typeofdata = 'V~O';
    $field1801->displaytype = 1;
    $field1801->presence = 2;
    $field1801->defaultvalue = '';
    $field1801->quickcreate = 1;
    $field1801->summaryfield = 0;

    $block288->addField($field1801);
}

$field1803 = Vtiger_Field::getInstance('space_reserve_cf', $module);
if ($field1803) {
    echo "<br> Field 'space_reserve_cf' is already present <br>";
} else {
    $field1803 = new Vtiger_Field();
    $field1803->label = 'LBL_SPACE_RESERVE_CF';
    $field1803->name = 'space_reserve_cf';
    $field1803->table = 'vtiger_quotes';
    $field1803->column = 'space_reserve_cf';
    $field1803->columntype = 'int(11)';
    $field1803->uitype = 7;
    $field1803->typeofdata = 'V~O';
    $field1803->displaytype = 1;
    $field1803->presence = 2;
    $field1803->defaultvalue = '';
    $field1803->quickcreate = 1;
    $field1803->summaryfield = 0;

    $block288->addField($field1803);
}

$field1805 = Vtiger_Field::getInstance('accesorial_expedited_service', $module);
if ($field1805) {
    echo "<br> Field 'accesorial_expedited_service' is already present <br>";
} else {
    $field1805 = new Vtiger_Field();
    $field1805->label = 'LBL_EXPEDITED_SERVICE';
    $field1805->name = 'accesorial_expedited_service';
    $field1805->table = 'vtiger_quotes';
    $field1805->column = 'accesorial_expedited_service';
    $field1805->columntype = 'varchar(3)';
    $field1805->uitype = 56;
    $field1805->typeofdata = 'V~O';
    $field1805->displaytype = 1;
    $field1805->presence = 2;
    $field1805->defaultvalue = '';
    $field1805->quickcreate = 1;
    $field1805->summaryfield = 0;

    $block197->addField($field1805);
}

$field1815 = Vtiger_Field::getInstance('billing_type', $module);
if ($field1815) {
    echo "<br> Field 'billing_type' is already present <br>";
} else {
    $field1815 = new Vtiger_Field();
    $field1815->label = 'LBL_QUOTES_BILLINGTYPE';
    $field1815->name = 'billing_type';
    $field1815->table = 'vtiger_quotes';
    $field1815->column = 'billing_type';
    $field1815->columntype = 'varchar(255)';
    $field1815->uitype = 16;
    $field1815->typeofdata = 'V~O';
    $field1815->displaytype = 1;
    $field1815->presence = 2;
    $field1815->defaultvalue = '';
    $field1815->quickcreate = 1;
    $field1815->summaryfield = 0;

    $block190->addField($field1815);
}

$field1859 = Vtiger_Field::getInstance('pricing_type', $module);
if ($field1859) {
    echo "<br> Field 'pricing_type' is already present <br>";
} else {
    $field1859 = new Vtiger_Field();
    $field1859->label = 'LBL_QUOTES_PRICING';
    $field1859->name = 'pricing_type';
    $field1859->table = 'vtiger_quotes';
    $field1859->column = 'pricing_type';
    $field1859->columntype = 'varchar(200)';
    $field1859->uitype = 16;
    $field1859->typeofdata = 'V~O';
    $field1859->displaytype = 1;
    $field1859->presence = 2;
    $field1859->defaultvalue = '';
    $field1859->quickcreate = 1;
    $field1859->summaryfield = 0;

    $block194->addField($field1859);
}

$field1912 = Vtiger_Field::getInstance('agentid', $module);
if ($field1912) {
    echo "<br> Field 'agentid' is already present <br>";
} else {
    $field1912 = new Vtiger_Field();
    $field1912->label = 'Owner Agent';
    $field1912->name = 'agentid';
    $field1912->table = 'vtiger_crmentity';
    $field1912->column = 'agentid';
    //$field1912->columntype = 'int(11)';
    $field1912->uitype = 1002;
    $field1912->typeofdata = 'I~M';
    $field1912->displaytype = 1;
    $field1912->presence = 2;
    $field1912->defaultvalue = '';
    $field1912->quickcreate = 1;
    $field1912->summaryfield = 0;

    $block190->addField($field1912);
}

$field2031 = Vtiger_Field::getInstance('billed_weight', $module);
if ($field2031) {
    echo "<br> Field 'billed_weight' is already present <br>";
} else {
    $field2031 = new Vtiger_Field();
    $field2031->label = 'LBL_QUOTES_BILLED_WEIGHT';
    $field2031->name = 'billed_weight';
    $field2031->table = 'vtiger_quotes';
    $field2031->column = 'billed_weight';
    $field2031->columntype = 'int(10)';
    $field2031->uitype = 7;
    $field2031->typeofdata = 'C~O';
    $field2031->displaytype = 1;
    $field2031->presence = 2;
    $field2031->defaultvalue = '';
    $field2031->quickcreate = 1;
    $field2031->summaryfield = 0;

    $block194->addField($field2031);
}

$field2039 = Vtiger_Field::getInstance('quotation_type', $module);
if ($field2039) {
    echo "<br> Field 'quotation_type' is already present <br>";
} else {
    $field2039 = new Vtiger_Field();
    $field2039->label = 'LBL_ORDERS_QUOTATION_TYPE';
    $field2039->name = 'quotation_type';
    $field2039->table = 'vtiger_quotes';
    $field2039->column = 'quotation_type';
    $field2039->columntype = 'varchar(100)';
    $field2039->uitype = 16;
    $field2039->typeofdata = 'V~O';
    $field2039->displaytype = 1;
    $field2039->presence = 2;
    $field2039->defaultvalue = '';
    $field2039->quickcreate = 1;
    $field2039->summaryfield = 0;

    $block190->addField($field2039);
}

$field2040 = Vtiger_Field::getInstance('estimate_type', $module);
if ($field2040) {
    echo "<br> Field 'estimate_type' is already present <br>";
} else {
    $field2040 = new Vtiger_Field();
    $field2040->label = 'LBL_ORDERS_ESTIMATE_TYPE';
    $field2040->name = 'estimate_type';
    $field2040->table = 'vtiger_quotes';
    $field2040->column = 'estimate_type';
    $field2040->columntype = 'varchar(255)';
    $field2040->uitype = 16;
    $field2040->typeofdata = 'V~O';
    $field2040->displaytype = 1;
    $field2040->presence = 2;
    $field2040->defaultvalue = '';
    $field2040->quickcreate = 1;
    $field2040->summaryfield = 0;

    $block190->addField($field2040);
}

$field2062 = Vtiger_Field::getInstance('estimate_cube', $module);
if ($field2062) {
    echo "<br> Field 'estimate_cube' is already present <br>";
} else {
    $field2062 = new Vtiger_Field();
    $field2062->label = 'LBL_QUOTES_ESTIMATE_CUBE';
    $field2062->name = 'estimate_cube';
    $field2062->table = 'vtiger_quotes';
    $field2062->column = 'estimate_cube';
    $field2062->columntype = 'varchar(10)';
    $field2062->uitype = 1;
    $field2062->typeofdata = 'I~O';
    $field2062->displaytype = 1;
    $field2062->presence = 2;
    $field2062->defaultvalue = '';
    $field2062->quickcreate = 1;
    $field2062->summaryfield = 0;

    $block194->addField($field2062);
}

$field2063 = Vtiger_Field::getInstance('estimate_piece_count', $module);
if ($field2063) {
    echo "<br> Field 'estimate_piece_count' is already present <br>";
} else {
    $field2063 = new Vtiger_Field();
    $field2063->label = 'LBL_QUOTES_ESTIMATE_PIECE_COUNT';
    $field2063->name = 'estimate_piece_count';
    $field2063->table = 'vtiger_quotes';
    $field2063->column = 'estimate_piece_count';
    $field2063->columntype = 'varchar(10)';
    $field2063->uitype = 1;
    $field2063->typeofdata = 'I~O';
    $field2063->displaytype = 1;
    $field2063->presence = 2;
    $field2063->defaultvalue = '';
    $field2063->quickcreate = 1;
    $field2063->summaryfield = 0;

    $block194->addField($field2063);
}

$field2064 = Vtiger_Field::getInstance('estimate_pack_count', $module);
if ($field2064) {
    echo "<br> Field 'estimate_pack_count' is already present <br>";
} else {
    $field2064 = new Vtiger_Field();
    $field2064->label = 'LBL_QUOTES_ESTIMATE_PACK_COUNT';
    $field2064->name = 'estimate_pack_count';
    $field2064->table = 'vtiger_quotes';
    $field2064->column = 'estimate_pack_count';
    $field2064->columntype = 'varchar(10)';
    $field2064->uitype = 1;
    $field2064->typeofdata = 'I~O';
    $field2064->displaytype = 1;
    $field2064->presence = 2;
    $field2064->defaultvalue = '';
    $field2064->quickcreate = 1;
    $field2064->summaryfield = 0;

    $block194->addField($field2064);
}

$field2185 = Vtiger_Field::getInstance('sit_distribution_discount', $module);
if ($field2185) {
    echo "<br> Field 'sit_distribution_discount' is already present <br>";
} else {
    $field2185 = new Vtiger_Field();
    $field2185->label = 'LBL_ESTIMATES_SIT_DISTRIBUTION_DISCOUNT';
    $field2185->name = 'sit_distribution_discount';
    $field2185->table = 'vtiger_quotes';
    $field2185->column = 'sit_distribution_discount';
    $field2185->columntype = 'int(11)';
    $field2185->uitype = 9;
    $field2185->typeofdata = 'N~O';
    $field2185->displaytype = 1;
    $field2185->presence = 2;
    $field2185->defaultvalue = '';
    $field2185->quickcreate = 1;
    $field2185->summaryfield = 0;

    $block194->addField($field2185);
}

$field2186 = Vtiger_Field::getInstance('bottom_line_distribution_discount', $module);
if ($field2186) {
    echo "<br> Field 'bottom_line_distribution_discount' is already present <br>";
} else {
    $field2186 = new Vtiger_Field();
    $field2186->label = 'LBL_ESTIMATES_BOTTOM_LINE_DISTRIBUTION_DISCOUNT';
    $field2186->name = 'bottom_line_distribution_discount';
    $field2186->table = 'vtiger_quotes';
    $field2186->column = 'bottom_line_distribution_discount';
    $field2186->columntype = 'int(11)';
    $field2186->uitype = 9;
    $field2186->typeofdata = 'N~O';
    $field2186->displaytype = 1;
    $field2186->presence = 2;
    $field2186->defaultvalue = '';
    $field2186->quickcreate = 1;
    $field2186->summaryfield = 0;

    $block194->addField($field2186);
}

$field2201 = Vtiger_Field::getInstance('valuation_discounted', $module);
if ($field2201) {
    echo "<br> Field 'valuation_discounted' is already present <br>";
} else {
    $field2201 = new Vtiger_Field();
    $field2201->label = 'LBL_QUOTES_VALUATIONDISCOUNTED';
    $field2201->name = 'valuation_discounted';
    $field2201->table = 'vtiger_quotes';
    $field2201->column = 'valuation_discounted';
    $field2201->columntype = 'varchar(3)';
    $field2201->uitype = 56;
    $field2201->typeofdata = 'V~O';
    $field2201->displaytype = 1;
    $field2201->presence = 2;
    $field2201->defaultvalue = '';
    $field2201->quickcreate = 1;
    $field2201->summaryfield = 0;

    $block282->addField($field2201);
}

$field2202 = Vtiger_Field::getInstance('valuation_discount_amount', $module);
if ($field2202) {
    echo "<br> Field 'valuation_discount_amount' is already present <br>";
} else {
    $field2202 = new Vtiger_Field();
    $field2202->label = 'LBL_QUOTES_VALUATIONDISCOUNTAMOUNT';
    $field2202->name = 'valuation_discount_amount';
    $field2202->table = 'vtiger_quotes';
    $field2202->column = 'valuation_discount_amount';
    $field2202->columntype = 'decimal(10,2)';
    $field2202->uitype = 71;
    $field2202->typeofdata = 'V~O';
    $field2202->displaytype = 1;
    $field2202->presence = 2;
    $field2202->defaultvalue = '';
    $field2202->quickcreate = 1;
    $field2202->summaryfield = 0;

    $block282->addField($field2202);
}

$field2213 = Vtiger_Field::getInstance('guaranteed_price', $module);
if ($field2213) {
    echo "<br> Field 'guaranteed_price' is already present <br>";
} else {
    $field2213 = new Vtiger_Field();
    $field2213->label = 'LBL_QUOTES_GUARANTEED_PRICE';
    $field2213->name = 'guaranteed_price';
    $field2213->table = 'vtiger_quotes';
    $field2213->column = 'guaranteed_price';
    $field2213->columntype = 'decimal(56,8)';
    $field2213->uitype = 71;
    $field2213->typeofdata = 'N~O';
    $field2213->displaytype = 1;
    $field2213->presence = 2;
    $field2213->defaultvalue = '';
    $field2213->quickcreate = 1;
    $field2213->summaryfield = 0;

    $block194->addField($field2213);
}

$field2215 = Vtiger_Field::getInstance('small_shipment', $module);
if ($field2215) {
    echo "<br> Field 'small_shipment' is already present <br>";
} else {
    $field2215 = new Vtiger_Field();
    $field2215->label = 'LBL_QUOTES_SMALLSHIPMENT';
    $field2215->name = 'small_shipment';
    $field2215->table = 'vtiger_quotes';
    $field2215->column = 'small_shipment';
    $field2215->columntype = 'varchar(3)';
    $field2215->uitype = 56;
    $field2215->typeofdata = 'V~O';
    $field2215->displaytype = 1;
    $field2215->presence = 2;
    $field2215->defaultvalue = '';
    $field2215->quickcreate = 1;
    $field2215->summaryfield = 0;

    $block340->addField($field2215);
}

$field2216 = Vtiger_Field::getInstance('small_shipment_miles', $module);
if ($field2216) {
    echo "<br> Field 'small_shipment_miles' is already present <br>";
} else {
    $field2216 = new Vtiger_Field();
    $field2216->label = 'LBL_QUOTES_SMALLSHIPMENTMILES';
    $field2216->name = 'small_shipment_miles';
    $field2216->table = 'vtiger_quotes';
    $field2216->column = 'small_shipment_miles';
    $field2216->columntype = 'int(10)';
    $field2216->uitype = 7;
    $field2216->typeofdata = 'I~O';
    $field2216->displaytype = 1;
    $field2216->presence = 2;
    $field2216->defaultvalue = '';
    $field2216->quickcreate = 1;
    $field2216->summaryfield = 0;

    $block340->addField($field2216);
}

$field2217 = Vtiger_Field::getInstance('small_shipment_ot', $module);
if ($field2217) {
    echo "<br> Field 'small_shipment_ot' is already present <br>";
} else {
    $field2217 = new Vtiger_Field();
    $field2217->label = 'LBL_QUOTES_SMALLSHIPMENTOT';
    $field2217->name = 'small_shipment_ot';
    $field2217->table = 'vtiger_quotes';
    $field2217->column = 'small_shipment_ot';
    $field2217->columntype = 'varchar(3)';
    $field2217->uitype = 56;
    $field2217->typeofdata = 'V~O';
    $field2217->displaytype = 1;
    $field2217->presence = 2;
    $field2217->defaultvalue = '';
    $field2217->quickcreate = 1;
    $field2217->summaryfield = 0;

    $block340->addField($field2217);
}

$field2218 = Vtiger_Field::getInstance('priority_shipping', $module);
if ($field2218) {
    echo "<br> Field 'priority_shipping' is already present <br>";
} else {
    $field2218 = new Vtiger_Field();
    $field2218->label = 'LBL_QUOTES_PRIORITYSHIPPING';
    $field2218->name = 'priority_shipping';
    $field2218->table = 'vtiger_quotes';
    $field2218->column = 'priority_shipping';
    $field2218->columntype = 'varchar(3)';
    $field2218->uitype = 56;
    $field2218->typeofdata = 'V~O';
    $field2218->displaytype = 1;
    $field2218->presence = 2;
    $field2218->defaultvalue = '';
    $field2218->quickcreate = 1;
    $field2218->summaryfield = 0;

    $block340->addField($field2218);
}

$field2219 = Vtiger_Field::getInstance('pshipping_booker_commission', $module);
if ($field2219) {
    echo "<br> Field 'pshipping_booker_commission' is already present <br>";
} else {
    $field2219 = new Vtiger_Field();
    $field2219->label = 'LBL_QUOTES_PSHIPPINGBOOKERCOMMISSION';
    $field2219->name = 'pshipping_booker_commission';
    $field2219->table = 'vtiger_quotes';
    $field2219->column = 'pshipping_booker_commission';
    $field2219->columntype = 'varchar(50)';
    $field2219->uitype = 16;
    $field2219->typeofdata = 'V~O';
    $field2219->displaytype = 1;
    $field2219->presence = 2;
    $field2219->defaultvalue = '400.00';
    $field2219->quickcreate = 1;
    $field2219->summaryfield = 0;

    $block340->addField($field2219);
}

$field2220 = Vtiger_Field::getInstance('pshipping_origin_miles', $module);
if ($field2220) {
    echo "<br> Field 'pshipping_origin_miles' is already present <br>";
} else {
    $field2220 = new Vtiger_Field();
    $field2220->label = 'LBL_QUOTES_PSHIPPINGORIGINMILES';
    $field2220->name = 'pshipping_origin_miles';
    $field2220->table = 'vtiger_quotes';
    $field2220->column = 'pshipping_origin_miles';
    $field2220->columntype = 'int(10)';
    $field2220->uitype = 7;
    $field2220->typeofdata = 'I~O';
    $field2220->displaytype = 1;
    $field2220->presence = 2;
    $field2220->defaultvalue = '';
    $field2220->quickcreate = 1;
    $field2220->summaryfield = 0;

    $block340->addField($field2220);
}

$field2221 = Vtiger_Field::getInstance('pshipping_destination_miles', $module);
if ($field2221) {
    echo "<br> Field 'pshipping_destination_miles' is already present <br>";
} else {
    $field2221 = new Vtiger_Field();
    $field2221->label = 'LBL_QUOTES_PSHIPPINGDESTINATIONMILES';
    $field2221->name = 'pshipping_destination_miles';
    $field2221->table = 'vtiger_quotes';
    $field2221->column = 'pshipping_destination_miles';
    $field2221->columntype = 'int(10)';
    $field2221->uitype = 7;
    $field2221->typeofdata = 'I~O';
    $field2221->displaytype = 1;
    $field2221->presence = 2;
    $field2221->defaultvalue = '';
    $field2221->quickcreate = 1;
    $field2221->summaryfield = 0;

    $block340->addField($field2221);
}

$field2222 = Vtiger_Field::getInstance('crating_disc', $module);
if ($field2222) {
    echo "<br> Field 'crating_disc' is already present <br>";
} else {
    $field2222 = new Vtiger_Field();
    $field2222->label = 'LBL_QUOTES_CRATING_DISC';
    $field2222->name = 'crating_disc';
    $field2222->table = 'vtiger_quotes';
    $field2222->column = 'crating_disc';
    $field2222->columntype = 'decimal(7,2)';
    $field2222->uitype = 9;
    $field2222->typeofdata = 'N~O';
    $field2222->displaytype = 1;
    $field2222->presence = 2;
    $field2222->defaultvalue = '';
    $field2222->quickcreate = 1;
    $field2222->summaryfield = 0;

    $block194->addField($field2222);
}

if ($isNew) {
    $filter1            = new Vtiger_Filter();
    $filter1->name      = 'All';
    $filter1->isdefault = true;
    $module->addFilter($filter1);
    $filter1->addField($field1049)->addField($field1052, 1)->addField($field1050, 2)->addField($field1055, 3)->addField($field1069, 4)->addField($field1056, 5);
    $module->setDefaultSharing();
    $module->initWebservice();
    $module->setRelatedList(Vtiger_Module::getInstance('Calendar'), 'Activities', ['ADD'], 'get_activities');
    $module->setRelatedList(Vtiger_Module::getInstance('Documents'), 'Documents', ['ADD', 'SELECT'], 'get_attachments');

    $ordersModule = Vtiger_Module::getInstance('Orders');
    $ordersModule->setRelatedList($module, 'Actuals', ['ADD'], 'get_dependents_list');

    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_entityname` SET entityidfield='quoteid', entityidcolumn='quote_id' WHERE tabid=".$module->id);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";