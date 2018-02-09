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



//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

function createRegistrationBlock($moduleModel, $blockLabel)
{
    $block = Vtiger_Block::getInstance($blockLabel, $moduleModel);
    if ($block) {
        echo "<br>The $blockLabel block already exists<br>";
    } else {
        $block = new Vtiger_Block();
        $block->label = $blockLabel;
        $moduleModel->addBlock($block);
    }
    
    createField($moduleModel, $block, 'vtiger_potential', 'payment_type_sts', 'LBL_OPPORTUNITY_PAYMENTTYPE', 16, 'V~O', 'VARCHAR(255)', 0, array('COD', 'PPD'));
    createField($moduleModel, $block, 'vtiger_potential', 'payment_method', 'LBL_OPPORTUNITY_PAYMENTMETHOD', 16, 'V~O', 'VARCHAR(255)', 0, array('AM', 'AV', 'CCK', 'CHK', 'CRN', 'CSH', 'DN', 'MA', 'MO', 'NO', 'PCK', 'VI'));
    createField($moduleModel, $block, 'vtiger_potential', 'agmt_id', 'LBL_OPPORTUNITY_AGMTID');
    createField($moduleModel, $block, 'vtiger_potential', 'subagmt_nbr', 'LBL_OPPORTUNITY_SUBAGMTNUMBER');
    createField($moduleModel, $block, 'vtiger_potential', 'brand', 'LBL_OPPORTUNITY_BRAND');
    createField($moduleModel, $block, 'vtiger_potential', 'national_account_number', 'LBL_OPPORTUNITY_NATIONALACCOUNTNUMBER');
    createField($moduleModel, $block, 'vtiger_potential', 'self_haul', 'LBL_OPPORTUNITY_SELFHAUL', 56, 'V~O', 'VARCHAR(3)');
    createField($moduleModel, $block, 'vtiger_potential', 'express_shipment', 'LBL_OPPORTUNITY_EXPRESSSHIPMENT', 56, 'V~O', 'VARCHAR(3)');
    createField($moduleModel, $block, 'vtiger_potential', 'cbs_ind', 'LBL_OPPORTUNITY_CBSIND', 56, 'V~O', 'VARCHAR(3)');
    createField($moduleModel, $block, 'vtiger_potential', 'credit_check', 'LBL_OPPORTUNITY_CREDITCHECK', 56, 'V~O', 'VARCHAR(3)');
    createField($moduleModel, $block, 'vtiger_potential', 'billing_apn', 'LBL_OPPORTUNITY_BILLINGAPN');
    createField($moduleModel, $block, 'vtiger_potential', 'ref_number', 'LBL_OPPORTUNITY_REFNUMBER');
    createField($moduleModel, $block, 'vtiger_potential', 'ref_type', 'LBL_OPPORTUNITY_REFTYPE', 16, 'V~O', 'VARCHAR(255)', 0, array('LOA', 'PO'));
    createField($moduleModel, $block, 'vtiger_potential', 'credit_check_amount', 'LBL_OPPORTUNITY_CREDITCHECKAMOUNT');
    createField($moduleModel, $block, 'vtiger_potential', 'ba_code', 'LBL_OPPORTUNITY_BACODE');
    createField($moduleModel, $block, 'vtiger_potential', 'ba_name', 'LBL_OPPORTUNITY_BANAME');
    createField($moduleModel, $block, 'vtiger_potential', 'ba_city', 'LBL_OPPORTUNITY_BACITY');
    createField($moduleModel, $block, 'vtiger_potential', 'ba_state', 'LBL_OPPORTUNITY_BASTATE');
    createField($moduleModel, $block, 'vtiger_potential', 'oa_code', 'LBL_OPPORTUNITY_OACODE');
    createField($moduleModel, $block, 'vtiger_potential', 'oa_name', 'LBL_OPPORTUNITY_OANAME');
    createField($moduleModel, $block, 'vtiger_potential', 'oa_city', 'LBL_OPPORTUNITY_OACITY');
    createField($moduleModel, $block, 'vtiger_potential', 'oa_state', 'LBL_OPPORTUNITY_OASTATE');
    createField($moduleModel, $block, 'vtiger_potential', 'ea_code', 'LBL_OPPORTUNITY_EACODE');
    createField($moduleModel, $block, 'vtiger_potential', 'ea_name', 'LBL_OPPORTUNITY_EANAME');
    createField($moduleModel, $block, 'vtiger_potential', 'ea_city', 'LBL_OPPORTUNITY_EACITY');
    createField($moduleModel, $block, 'vtiger_potential', 'ea_state', 'LBL_OPPORTUNITY_EASTATE');
    createField($moduleModel, $block, 'vtiger_potential', 'ha_code', 'LBL_OPPORTUNITY_HACODE');
    createField($moduleModel, $block, 'vtiger_potential', 'ha_name', 'LBL_OPPORTUNITY_HANAME');
    createField($moduleModel, $block, 'vtiger_potential', 'ha_city', 'LBL_OPPORTUNITY_HACITY');
    createField($moduleModel, $block, 'vtiger_potential', 'ha_state', 'LBL_OPPORTUNITY_HASTATE');
    createField($moduleModel, $block, 'vtiger_potential', 'da_code', 'LBL_OPPORTUNITY_DACODE');
    createField($moduleModel, $block, 'vtiger_potential', 'da_name', 'LBL_OPPORTUNITY_DANAME');
    createField($moduleModel, $block, 'vtiger_potential', 'da_city', 'LBL_OPPORTUNITY_DACITY');
    createField($moduleModel, $block, 'vtiger_potential', 'da_state', 'LBL_OPPORTUNITY_DASTATE');
    createField($moduleModel, $block, 'vtiger_potential', 'registration_date', 'LBL_OPPORTUNITY_REGISTRATIONDATE');
    createField($moduleModel, $block, 'vtiger_potential', 'sts_response', 'LBL_OPPORTUNITY_STSRESPONSE', 19);
    createField($moduleModel, $block, 'vtiger_potential', 'booker_split', 'LBL_OPPORTUNITY_BOOKERSPLIT', 9, 'V~O', 'VARCHAR(255)');
    createField($moduleModel, $block, 'vtiger_potential', 'origin_split', 'LBL_OPPORTUNITY_ORIGINSPLIT', 9, 'V~O', 'VARCHAR(255)');
}

function createField($moduleModel, $blockModel, $tableName, $fieldName, $fieldLabel, $UIType = 1, $typeOfData = 'V~O', $cloumnType = 'VARCHAR(255)', $quickCreate = 0, $picklistValues = false)
{
    $field = Vtiger_Field::getInstance($fieldName, $moduleModel);
    if ($field) {
        echo "<br> opps $fieldName field already exists.<br>";
    } else {
        echo "<br> opps special_terms field doesn't exist, adding it now.<br>";
        $field = new Vtiger_Field();
        $field->label = $fieldLabel;
        $field->name = $fieldName;
        $field->table = $tableName;
        $field->column = $fieldName;
        $field->columntype = $cloumnType;
        $field->uitype = $UIType;
        $field->typeofdata = $typeOfData;
        $field->quickcreate = 0;

        $blockModel->addField($field);
        if ($UIType == 16) {
            $field->setPicklistValues($picklistValues);
        }
        echo "<br> opps $fieldName field added.<br>";
    }
}

echo "<br>Begin Sirva modifications to opportunity fields<br>";

$db = PearDatabase::getInstance();

$oppsModule = Vtiger_Module::getInstance('Opportunities');

$potsModule = Vtiger_Module::getInstance('Potentials');

function newOppSourceEntry($name, $sequence)
{
    $db = PearDatabase::getInstance();
    Vtiger_Utils::ExecuteQuery('UPDATE vtiger_leadsource_seq SET id = id + 1');
    Vtiger_Utils::ExecuteQuery('UPDATE vtiger_picklistvalues_seq SET id = id + 1');
    echo "<br>updated sequence tables<br>";
    $result = $db->pquery('SELECT id FROM `vtiger_leadsource_seq`', array());
    $row = $result->fetchRow();
    $leadSourceId = $row[0];
    echo "<br>lead source id set: ".$leadSourceId."<br>";
    $result = $db->pquery('SELECT id FROM `vtiger_picklistvalues_seq`', array());
    $row = $result->fetchRow();
    $picklistValueId = $row[0];
    echo "<br>picklist value id set: ".$picklistValueId."<br>";
    $sql = 'INSERT INTO `vtiger_leadsource` (leadsourceid, leadsource, presence, picklist_valueid, sortorderid) VALUES (?, ?, 1, ?, ?)';
    $db->pquery($sql, array($leadSourceId, $name, $picklistValueId, $sequence));
}

if ($oppsModule) {
    echo "<br>Opps module exists! Modifying fields.<br>";
    $oppsInfo = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $oppsModule);
    if (!$oppsInfo) {
        $oppsInfo = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $oppsModule);
    }
    if ($oppsInfo) {
        echo "<br>Opps information block exists<br>";
        
        //label/content swap for lead source
        $oppLeadSource = Vtiger_Field::getInstance('leadsource', $oppsModule);
        if ($oppLeadSource) {
            echo "<br>leadsource exists continuing with label swap<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_OPPORTUNITY_MARKETINGCHANNEL' WHERE fieldlabel = 'Lead Source' AND tablename = 'vtiger_potential'");
            echo "<br>leadsource label swap done, updating picklist values<br>";
            Vtiger_Utils::ExecuteQuery('TRUNCATE TABLE `vtiger_leadsource`');
            $newSources = ['Affinity', 'Interactive', 'Lead Buying', 'Other', 'Referrals', 'Telephone'];
            foreach ($newSources as $index => $status) {
                echo "<br> adding status: ".$status;
                newOppSourceEntry($status, $index+1);
            }
        }
        
        $oppCloseDate = Vtiger_Field::getInstance('closingdate', $oppsModule);
        if ($oppCloseDate) {
            echo "<br>closingdate exists continuing with label swap<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_OPPORTUNITY_FULFILLMENTDATE' WHERE fieldlabel = 'Expected Close Date' AND tablename = 'vtiger_potential' AND fieldname = 'closingdate'");
            echo "<br>closingdate label swap done<br>";
        }

        //create field for opportunity disposition
        $oppDisposition = Vtiger_Field::getInstance('opportunity_disposition', $oppsModule);
        if ($oppDisposition) {
            echo "<br> Field 'opportunity_disposition' is already present. <br>";
        } else {
            echo "<br> Field 'opportunity_disposition' not present. Creating it now<br>";
            $oppDisposition = new Vtiger_Field();
            $oppDisposition->label = 'LBL_OPPORTUNITY_OPPORTUNITYDISPOSITION';
            $oppDisposition->name = 'opportunity_disposition';
            $oppDisposition->table = 'vtiger_potential';
            $oppDisposition->column = 'opportunity_disposition';
            $oppDisposition->columntype = 'VARCHAR(255)';
            $oppDisposition->uitype = 16;
            $oppDisposition->typeofdata = 'V~O';
            $oppDisposition->quickcreate = 0;

            $oppsInfo->addField($oppDisposition);
            $oppDisposition->setPicklistValues(array('New', 'Pending', 'Booked', 'Not PT but NA Mobe', 'Duplicate', 'Incomplete Customer Info', 'Estimate Provided'));
            echo "<br> Field 'opportunity_disposition' added.<br>";
        }
        
        //create field for order number
        $orderNumber = Vtiger_Field::getInstance('order_number', $oppsModule);
        if ($orderNumber) {
            echo "<br> Field 'order_number' is already present. <br>";
        } else {
            echo "<br> Field 'order_number' not present. Creating it now<br>";
            $orderNumber = new Vtiger_Field();
            $orderNumber->label = 'LBL_OPPORTUNITY_ORDERNUMBER';
            $orderNumber->name = 'order_number';
            $orderNumber->table = 'vtiger_potential';
            $orderNumber->column = 'order_number';
            $orderNumber->columntype = 'VARCHAR(255)';
            $orderNumber->uitype = 1;
            $orderNumber->typeofdata = 'V~O';
            $orderNumber->quickcreate = 0;

            $oppsInfo->addField($orderNumber);
            echo "<br> Field 'order_number' added.<br>";
        }
        
        //create field for opportunity detail disposition
        $oppDetailDisposition = Vtiger_Field::getInstance('opportunity_detail_disposition', $oppsModule);
        if ($oppDetailDisposition) {
            echo "<br> Field 'opportunity_detail_disposition' is already present. <br>";
        } else {
            echo "<br> Field 'opportunity_detail_disposition' not present. Creating it now<br>";
            $oppDetailDisposition = new Vtiger_Field();
            $oppDetailDisposition->label = 'LBL_OPPORTUNITY_OPPORTUNITYDETAILDISPOSITION';
            $oppDetailDisposition->name = 'opportunity_detail_disposition';
            $oppDetailDisposition->table = 'vtiger_potential';
            $oppDetailDisposition->column = 'opportunity_detail_disposition';
            $oppDetailDisposition->columntype = 'VARCHAR(255)';
            $oppDetailDisposition->uitype = 16;
            $oppDetailDisposition->typeofdata = 'V~O';
            $oppDetailDisposition->quickcreate = 0;

            $oppsInfo->addField($oppDetailDisposition);
            $oppDetailDisposition->setPicklistValues(array('Could Not Connect With Customer', 'Booked With Another Mover', 'Move Dates Have Passed', 'Pricing', 'Capacity / Scheduling', 'Other', 'Self Haul', 'No Longer Moving', 'Cancelled Appointment'));
            echo "<br> Field 'opportunity_detail_disposition' added.<br>";
        }
        
        //create field for move type
        $moveType = Vtiger_Field::getInstance('move_type', $oppsModule);
        if ($moveType) {
            echo "<br> Field 'move_type' is already present. <br>";
        } else {
            echo "<br> Field 'move_type' not present. Creating it now<br>";
            $moveType = new Vtiger_Field();
            $moveType->label = 'LBL_OPPORTUNITY_MOVETYPE';
            $moveType->name = 'move_type';
            $moveType->table = 'vtiger_potential';
            $moveType->column = 'move_type';
            $moveType->columntype = 'VARCHAR(255)';
            $moveType->uitype = 16;
            $moveType->typeofdata = 'V~M';
            $moveType->quickcreate = 0;

            $oppsInfo->addField($moveType);
            $moveType->setPicklistValues(array('Interstate', 'Intrastate', 'O&I', 'Local Canada', 'Local US', 'Interstate', 'Inter-Provincial', 'Intra-Provincial', 'Cross Border', 'Alaska', 'Hawaii', 'International'));
            echo "<br> Field 'move_type' added.<br>";
        }
        
        //create field for business channel
        $businessChannel = Vtiger_Field::getInstance('business_channel', $oppsModule);
        if ($businessChannel) {
            echo "<br> Field 'business_channel' is already present. <br>";
        } else {
            echo "<br> Field 'business_channel' not present. Creating it now<br>";
            $businessChannel = new Vtiger_Field();
            $businessChannel->label = 'LBL_OPPORTUNITY_BUSINESSCHANNEL';
            $businessChannel->name = 'business_channel';
            $businessChannel->table = 'vtiger_potential';
            $businessChannel->column = 'business_channel';
            $businessChannel->columntype = 'VARCHAR(255)';
            $businessChannel->uitype = 16;
            $businessChannel->typeofdata = 'V~O';
            $businessChannel->quickcreate = 0;

            $oppsInfo->addField($businessChannel);
            $businessChannel->setPicklistValues(array('Consumer', 'Corporate', 'Military', 'Government'));
            echo "<br> Field 'business_channel' added.<br>";
        }
        
        //create date field for assigned date
        $assignedDate = Vtiger_Field::getInstance('assigned_date', $oppsModule);
        if ($assignedDate) {
            echo "<br> Field 'assigned_date' is already present. <br>";
        } else {
            echo "<br> Field 'assigned_date' not present. Creating it now<br>";
            $assignedDate = new Vtiger_Field();
            $assignedDate->label = 'LBL_OPPORTUNITY_ASSIGNEDDATE';
            $assignedDate->name = 'assigned_date';
            $assignedDate->table = 'vtiger_potential';
            $assignedDate->column = 'assigned_date';
            $assignedDate->columntype = 'VARCHAR(255)';
            $assignedDate->uitype = 5;
            $assignedDate->typeofdata = 'D~O';
            $assignedDate->quickcreate = 0;

            $oppsInfo->addField($assignedDate);
            echo "<br> Field 'assigned_date' added.<br>";
        }
        
        //create checkbox for funded
        $funded = Vtiger_Field::getInstance('funded', $oppsModule);
        if ($funded) {
            echo "<br> Field 'funded' is already present. <br>";
        } else {
            echo "<br> Field 'funded' not present. Creating it now<br>";
            $funded = new Vtiger_Field();
            $funded->label = 'LBL_OPPORTUNITY_FUNDED';
            $funded->name = 'funded';
            $funded->table = 'vtiger_potential';
            $funded->column = 'funded';
            $funded->columntype = 'VARCHAR(255)';
            $funded->uitype = 1;
            $funded->typeofdata = 'V~O';
            $funded->quickcreate = 0;

            $oppsInfo->addField($funded);
            echo "<br> Field 'funded' added.<br>";
        }
        
        //create date field for receive date
        $recieveDate = Vtiger_Field::getInstance('receive_date', $oppsModule);
        if ($recieveDate) {
            echo "<br> Field 'receive_date' is already present. <br>";
        } else {
            echo "<br> Field 'receive_date' not present. Creating it now<br>";
            $recieveDate = new Vtiger_Field();
            $recieveDate->label = 'LBL_OPPORTUNITY_RECEIVEDATE';
            $recieveDate->name = 'receive_date';
            $recieveDate->table = 'vtiger_potential';
            $recieveDate->column = 'receive_date';
            $recieveDate->columntype = 'VARCHAR(255)';
            $recieveDate->uitype = 5;
            $recieveDate->typeofdata = 'D~O';
            $recieveDate->quickcreate = 0;

            $oppsInfo->addField($recieveDate);
            echo "<br> Field 'vtiger_potential' added.<br>";
        }
        
        //create checkbox for moving a vehicle
        $moveVehicle = Vtiger_Field::getInstance('moving_a_vehicle', $oppsModule);
        if ($moveVehicle) {
            echo "<br> Field 'moving_a_vehicle' is already present. <br>";
        } else {
            echo "<br> Field 'moving_a_vehicle' not present. Creating it now<br>";
            $moveVehicle = new Vtiger_Field();
            $moveVehicle->label = 'LBL_OPPORTUNITY_MOVINGAVEHICLE';
            $moveVehicle->name = 'moving_a_vehicle';
            $moveVehicle->table = 'vtiger_potential';
            $moveVehicle->column = 'moving_a_vehicle';
            $moveVehicle->columntype = 'VARCHAR(3)';
            $moveVehicle->uitype = 56;
            $moveVehicle->typeofdata = 'V~O';
            $moveVehicle->quickcreate = 0;

            $oppsInfo->addField($moveVehicle);
            echo "<br> Field 'moving_a_vehicle' added.<br>";
        }
        
        //create field for promotion code
        $promotionCode = Vtiger_Field::getInstance('promotion_code', $oppsModule);
        if ($promotionCode) {
            echo "<br> Field 'promotion_code' is already present. <br>";
        } else {
            echo "<br> Field 'promotion_code' not present. Creating it now<br>";
            $promotionCode = new Vtiger_Field();
            $promotionCode->label = 'LBL_OPPORTUNITY_PROMOTIONCODE';
            $promotionCode->name = 'promotion_code';
            $promotionCode->table = 'vtiger_potential';
            $promotionCode->column = 'promotion_code';
            $promotionCode->columntype = 'VARCHAR(255)';
            $promotionCode->uitype = 1;
            $promotionCode->typeofdata = 'V~O';
            $promotionCode->quickcreate = 0;

            $oppsInfo->addField($promotionCode);
            echo "<br> Field 'promotion_code' added.<br>";
        }
        
        //create field for promotion code
        $oppType = Vtiger_Field::getInstance('opp_type', $oppsModule);
        if ($oppType) {
            echo "<br> Field 'opp_type' is already present. <br>";
        } else {
            echo "<br> Field 'opp_type' not present. Creating it now<br>";
            $oppType = new Vtiger_Field();
            $oppType->label = 'LBL_OPPORTUNITY_OPPTYPE';
            $oppType->name = 'opp_type';
            $oppType->table = 'vtiger_potential';
            $oppType->column = 'opp_type';
            $oppType->columntype = 'VARCHAR(255)';
            $oppType->uitype = 16;
            $oppType->typeofdata = 'V~O';
            $oppType->quickcreate = 0;

            $oppsInfo->addField($oppType);
            $oppType->setPicklistValues(array('Consumer', 'National Account', 'OA Survey'));
            echo "<br> Field 'opp_type' added.<br>";
        }
        
        //create checkbox for out_of_area (non conforming field)
        $outOfArea = Vtiger_Field::getInstance('out_of_area', $oppsModule);
        if ($outOfArea) {
            echo "<br> Field 'out_of_area' is already present. <br>";
        } else {
            echo "<br> Field 'out_of_area' not present. Creating it now<br>";
            $outOfArea = new Vtiger_Field();
            $outOfArea->label = 'LBL_OPPORTUNITY_OUTOFAREA';
            $outOfArea->name = 'out_of_area';
            $outOfArea->table = 'vtiger_potential';
            $outOfArea->column = 'out_of_area';
            $outOfArea->columntype = 'VARCHAR(3)';
            $outOfArea->uitype = 56;
            $outOfArea->typeofdata = 'V~O';
            $outOfArea->quickcreate = 0;

            $oppsInfo->addField($outOfArea);
            echo "<br> Field 'out_of_area' added.<br>";
        }
        
        //create checkbox for out_of_origin (non conforming field)
        $outOfOrigin = Vtiger_Field::getInstance('out_of_origin', $oppsModule);
        if ($outOfOrigin) {
            echo "<br> Field 'out_of_origin' is already present. <br>";
        } else {
            echo "<br> Field 'out_of_origin' not present. Creating it now<br>";
            $outOfOrigin = new Vtiger_Field();
            $outOfOrigin->label = 'LBL_OPPORTUNITY_OUTOFORIGIN';
            $outOfOrigin->name = 'out_of_origin';
            $outOfOrigin->table = 'vtiger_potential';
            $outOfOrigin->column = 'out_of_origin';
            $outOfOrigin->columntype = 'VARCHAR(3)';
            $outOfOrigin->uitype = 56;
            $outOfOrigin->typeofdata = 'V~O';
            $outOfOrigin->quickcreate = 0;

            $oppsInfo->addField($outOfOrigin);
            echo "<br> Field 'out_of_origin' added.<br>";
        }
        
        //create checkbox for small_move (non conforming field)
        $smallMove = Vtiger_Field::getInstance('small_move', $oppsModule);
        if ($smallMove) {
            echo "<br> Field 'small_move' is already present. <br>";
        } else {
            echo "<br> Field 'small_move' not present. Creating it now<br>";
            $smallMove = new Vtiger_Field();
            $smallMove->label = 'LBL_OPPORTUNITY_SMALLMOVE';
            $smallMove->name = 'small_move';
            $smallMove->table = 'vtiger_potential';
            $smallMove->column = 'small_move';
            $smallMove->columntype = 'VARCHAR(3)';
            $smallMove->uitype = 56;
            $smallMove->typeofdata = 'V~O';
            $smallMove->quickcreate = 0;

            $oppsInfo->addField($smallMove);
            echo "<br> Field 'small_move' added.<br>";
        }
        
        //create checkbox for phone_estimate (non conforming field)
        $phoneEstimate = Vtiger_Field::getInstance('phone_estimate', $oppsModule);
        if ($phoneEstimate) {
            echo "<br> Field 'phone_estimate' is already present. <br>";
        } else {
            echo "<br> Field 'phone_estimate' not present. Creating it now<br>";
            $phoneEstimate = new Vtiger_Field();
            $phoneEstimate->label = 'LBL_OPPORTUNITY_PHONEESTIMATE';
            $phoneEstimate->name = 'phone_estimate';
            $phoneEstimate->table = 'vtiger_potential';
            $phoneEstimate->column = 'phone_estimate';
            $phoneEstimate->columntype = 'VARCHAR(3)';
            $phoneEstimate->uitype = 56;
            $phoneEstimate->typeofdata = 'V~O';
            $phoneEstimate->quickcreate = 0;

            $oppsInfo->addField($phoneEstimate);
            echo "<br> Field 'phone_estimate' added.<br>";
        }
        
        //create multi-line textbox for program terms
        $programTerms = Vtiger_Field::getInstance('program_terms', $oppsModule);
        if ($programTerms) {
            echo "<br> opps program_terms field already exists.<br>";
        } else {
            echo "<br> opps program_terms field doesn't exist, adding it now.<br>";
            $programTerms = new Vtiger_Field();
            $programTerms->label = 'LBL_OPPORTUNITY_PROGRAMTERMS';
            $programTerms->name = 'program_terms';
            $programTerms->table = 'vtiger_potential';
            $programTerms->column = 'program_terms';
            $programTerms->columntype = 'VARCHAR(255)';
            $programTerms->uitype = 19;
            $programTerms->typeofdata = 'V~O';
            $programTerms->quickcreate = 0;

            $oppsInfo->addField($programTerms);
            echo "<br> opps program_terms field added.<br>";
        }
    } else {
        echo "<br>Opps information block doesn't exist!<br>";
    }
    
    $oppsAddr = Vtiger_Block::getInstance('LBL_POTENTIALS_ADDRESSDETAILS', $oppsModule);
    if ($oppsAddr) {
        echo "<br>Opps address block exists.<br>";
        //make destination country mandatory
        $oppDestinationCountry = Vtiger_Field::getInstance('destination_country', $oppsModule);
        if ($oppDestinationCountry) {
            echo "<br>opp destination_country exists converting to mandatory<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata = 'V~M' WHERE fieldlabel = 'LBL_POTENTIALS_DESTINATIONADDRESSCOUNTRY'");
            echo "<br>opp destination_country mandatory swap done<br>";
        }
        //make origin country mandatory
        $oppOriginCountry = Vtiger_Field::getInstance('origin_country', $oppsModule);
        if ($oppOriginCountry) {
            echo "<br>opp origin_country exists converting to mandatory<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata = 'V~M' WHERE fieldlabel = 'LBL_POTENTIALS_ORIGINADDRESSCOUNTRY'");
            echo "<br>opp origin_country mandatory swap done<br>";
        }
        //make origin city mandatory
        $leadOriginCity = Vtiger_Field::getInstance('origin_city', $oppsModule);
        if ($leadOriginCity) {
            echo "<br>opp origin_city exists converting to mandatory<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata = 'V~M' WHERE fieldlabel = 'LBL_POTENTIALS_ORIGINCITY'");
            echo "<br>opp origin_city mandatory swap done<br>";
        }
        //make origin state mandatory
        $leadOriginState = Vtiger_Field::getInstance('origin_state', $oppsModule);
        if ($leadOriginState) {
            echo "<br>opp origin_city exists converting to mandatory<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata = 'V~M' WHERE fieldlabel = 'LBL_POTENTIALS_ORIGINSTATE'");
            echo "<br>opp origin_city mandatory swap done<br>";
        }
        //make origin zip mandatory
        $oppOriginZip = Vtiger_Field::getInstance('origin_zip', $oppsModule);
        if ($oppOriginZip) {
            echo "<br>opp origin_zip exists converting to mandatory<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata = 'V~M' WHERE fieldlabel = 'LBL_POTENTIALS_ORIGINZIP'");
            echo "<br>opp origin_zip mandatory swap done<br>";
        }
        //make origin address 1 mandatory
        $oppOriginAddress = Vtiger_Field::getInstance('origin_address1', $oppsModule);
        if ($oppOriginAddress) {
            echo "<br>opp origin_address1 exists converting to mandatory<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata = 'V~M~LE~50' WHERE fieldlabel = 'LBL_POTENTIALS_ORIGINADDRESS1'");
            echo "<br>opp origin_address1 mandatory swap done<br>";
        }
        //make destination address 1 mandatory
        $oppDestinationAddress = Vtiger_Field::getInstance('destination_address1', $oppsModule);
        if ($oppDestinationAddress) {
            echo "<br>opp destination_address1 exists converting to mandatory<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata = 'V~M~LE~50', defaultvalue = 'Will Advise' WHERE fieldlabel = 'LBL_POTENTIALS_DESTINATIONADDRESS1'");
            echo "<br>opp destination_address1 mandatory swap done<br>";
        }
        
        //convert origin country to picklist
        $result = $db->pquery("SELECT uitype FROM `vtiger_field` WHERE fieldlabel = 'LBL_POTENTIALS_ORIGINADDRESSCOUNTRY'", array());
        $row = $result->fetchRow();
        $originCountryUIType = $row[0];
        
        if ($originCountryUIType != 16) {
            $oppOriginCountry = Vtiger_Field::getInstance('origin_country', $oppsModule);
            if ($oppOriginCountry) {
                echo "<br>opp origin_country exists converting to picklist<br>";
                Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET uitype = '16' WHERE fieldlabel = 'LBL_POTENTIALS_ORIGINADDRESSCOUNTRY'");
                $oppOriginCountry->setPicklistValues(array('United States', 'Canada'));
                echo "<br>opp origin_country picklist conversion done<br>";
            }
        } else {
            echo "<br>opp origin_country already a picklist<br>";
        }
        
        //convert destination country to picklist
        $result = $db->pquery("SELECT uitype FROM `vtiger_field` WHERE fieldlabel = 'LBL_POTENTIALS_DESTINATIONADDRESSCOUNTRY'", array());
        $row = $result->fetchRow();
        $destinationCountryUIType = $row[0];
        
        if ($destinationCountryUIType != 16) {
            $oppDestinationCountry = Vtiger_Field::getInstance('destination_country', $oppsModule);
            if ($oppDestinationCountry) {
                echo "<br>opp destination_country exists converting to picklist<br>";
                Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET uitype = '16' WHERE fieldlabel = 'LBL_POTENTIALS_DESTINATIONADDRESSCOUNTRY'");
                $oppDestinationCountry->setPicklistValues(array('United States', 'Canada'));
                echo "<br>opp destination_country picklist conversion done<br>";
            }
        } else {
            echo "<br>opp destination_country already a picklist<br>";
        }
        
        //field for origin fax num
        $originFaxNum = Vtiger_Field::getInstance('origin_fax', $oppsModule);
        if ($originFaxNum) {
            echo "<br> Field origin_fax already exists<br>";
        } else {
            $missingFields++;
            echo "<br> Field 'origin_fax' not present. Creating it now<br>";
            $originFaxNum = new Vtiger_Field();
            $originFaxNum->label = 'LBL_OPPORTUNITY_ORIGINFAX';
            $originFaxNum->name = 'origin_fax';
            $originFaxNum->table = 'vtiger_potential';
            $originFaxNum->column = 'origin_fax';
            $originFaxNum->columntype = 'VARCHAR(255)';
            $originFaxNum->uitype = 1;
            $originFaxNum->typeofdata = 'V~O';
            $originFaxNum->quickcreate = 0;
            $oppsAddr->addField($originFaxNum);
            echo "<br> Field 'origin_fax' added.<br>";
        }
        
        //field for destinaiton fax num
        $destFaxNum = Vtiger_Field::getInstance('destination_fax', $oppsModule);
        if ($destFaxNum) {
            echo "<br> Field destination_fax already exists<br>";
        } else {
            $missingFields++;
            echo "<br> Field 'destination_fax' not present. Creating it now<br>";
            $destFaxNum = new Vtiger_Field();
            $destFaxNum->label = 'LBL_OPPORTUNITY_DESTINATIONFAX';
            $destFaxNum->name = 'destination_fax';
            $destFaxNum->table = 'vtiger_potential';
            $destFaxNum->column = 'destination_fax';
            $destFaxNum->columntype = 'VARCHAR(255)';
            $destFaxNum->uitype = 1;
            $destFaxNum->typeofdata = 'V~O';
            $destFaxNum->quickcreate = 0;
            $oppsAddr->addField($destFaxNum);
            echo "<br> Field 'destination_fax' added.<br>";
        }
        
        //phone type field for origin phone 1
        $phoneType1 = Vtiger_Field::getInstance('origin_phone1_type', $oppsModule);
        if ($phoneType1) {
            echo "<br> Field 'origin_phone1_type' is already present. <br>";
        } else {
            $missingFields++;
            echo "<br> Field 'origin_phone1_type' not present. Creating it now<br>";
            $phoneType1 = new Vtiger_Field();
            $phoneType1->label = 'LBL_OPPORTUNITY_ORIGINPHONE1TYPE';
            $phoneType1->name = 'origin_phone1_type';
            $phoneType1->table = 'vtiger_potential';
            $phoneType1->column = 'origin_phone1_type';
            $phoneType1->columntype = 'VARCHAR(255)';
            $phoneType1->uitype = 16;
            $phoneType1->typeofdata = 'V~O';
            $phoneType1->quickcreate = 0;

            $oppsAddr->addField($phoneType1);
            
            $phoneType1->setPicklistValues(array('Home', 'Work', 'Cell'));
            echo "<br> Field 'origin_phone1_type' added.<br>";
        }
        
        //phone type field for origin phone 2
        $phoneType2 = Vtiger_Field::getInstance('origin_phone2_type', $oppsModule);
        if ($phoneType2) {
            echo "<br> Field 'origin_phone2_type' is already present. <br>";
        } else {
            $missingFields++;
            echo "<br> Field 'origin_phone2_type' not present. Creating it now<br>";
            $phoneType2 = new Vtiger_Field();
            $phoneType2->label = 'LBL_OPPORTUNITY_ORIGINPHONE2TYPE';
            $phoneType2->name = 'origin_phone2_type';
            $phoneType2->table = 'vtiger_potential';
            $phoneType2->column = 'origin_phone2_type';
            $phoneType2->columntype = 'VARCHAR(255)';
            $phoneType2->uitype = 16;
            $phoneType2->typeofdata = 'V~O';
            $phoneType2->quickcreate = 0;

            $oppsAddr->addField($phoneType2);
            $phoneType2->setPicklistValues(array('Home', 'Work', 'Cell'));
            echo "<br> Field 'origin_phone2_type' added.<br>";
        }
        
        //phone type field for destination phone 1
        $phoneType3 = Vtiger_Field::getInstance('destination_phone1_type', $oppsModule);
        if ($phoneType3) {
            echo "<br> Field 'destination_phone1_type' is already present. <br>";
        } else {
            $missingFields++;
            echo "<br> Field 'destination_phone1_type' not present. Creating it now<br>";
            $phoneType3 = new Vtiger_Field();
            $phoneType3->label = 'LBL_OPPORTUNITY_DESTINATIONPHONE1TYPE';
            $phoneType3->name = 'destination_phone1_type';
            $phoneType3->table = 'vtiger_potential';
            $phoneType3->column = 'destination_phone1_type';
            $phoneType3->columntype = 'VARCHAR(255)';
            $phoneType3->uitype = 16;
            $phoneType3->typeofdata = 'V~O';
            $phoneType3->quickcreate = 0;

            $oppsAddr->addField($phoneType3);
            $phoneType3->setPicklistValues(array('Home', 'Work', 'Cell'));
            echo "<br> Field 'destination_phone1_type' added.<br>";
        }
        
        //phone type field for destination phone 2
        $phoneType4 = Vtiger_Field::getInstance('destination_phone2_type', $oppsModule);
        if ($phoneType4) {
            echo "<br> Field 'destination_phone2_type' is already present. <br>";
        } else {
            $missingFields++;
            echo "<br> Field 'destination_phone2_type' not present. Creating it now<br>";
            $phoneType4 = new Vtiger_Field();
            $phoneType4->label = 'LBL_OPPORTUNITY_DESTINATIONPHONE2TYPE';
            $phoneType4->name = 'destination_phone2_type';
            $phoneType4->table = 'vtiger_potential';
            $phoneType4->column = 'destination_phone2_type';
            $phoneType4->columntype = 'VARCHAR(255)';
            $phoneType4->uitype = 16;
            $phoneType4->typeofdata = 'V~O';
            $phoneType4->quickcreate = 0;

            $oppsAddr->addField($phoneType4);
            $phoneType4->setPicklistValues(array('Home', 'Work', 'Cell'));
            echo "<br> Field 'destination_phone2_type' added.<br>";
        }
        
        //resequence fields to put the phone type and fax fields into place
        //start by grabbing block ID
        $blockId = $oppsAddr->id;
            
        //then grab all the block's fields (except for the new ones)in order of sequence
        $addressFields = array();
        $sql = "SELECT fieldlabel, fieldname FROM vtiger_field WHERE block = ? AND fieldlabel != 'LBL_OPPORTUNITY_ORIGINFAX' AND fieldlabel != 'LBL_OPPORTUNITY_DESTINATIONFAX' AND fieldlabel != 'LBL_OPPORTUNITY_ORIGINPHONE1TYPE' AND fieldlabel != 'LBL_OPPORTUNITY_ORIGINPHONE2TYPE' AND fieldlabel != 'LBL_OPPORTUNITY_DESTINATIONPHONE1TYPE' AND fieldlabel != 'LBL_OPPORTUNITY_DESTINATIONPHONE2TYPE' ORDER BY sequence ASC";
        $result = $db->pquery($sql, array($blockId));
        $row = $result->fetchRow();
        
        while ($row != null) {
            $addressFields[] = array($row[0], $row[1]);
            $row = $result->fetchRow();
        }
        
        //this section inserts the new fields to their appropriate locations relative to the old fields
        $index = array_search(array('LBL_POTENTIALS_ORIGINPHONE2', 'origin_phone2'), $addressFields);
        
        array_splice($addressFields, $index+2, 0, array(array('LBL_OPPORTUNITY_ORIGINFAX', 'origin_fax')));
        
        $index = array_search(array('LBL_POTENTIALS_DESTINATIONPHONE2', 'destination_phone2'), $addressFields);
        
        array_splice($addressFields, $index+2, 0, array(array('LBL_OPPORTUNITY_DESTINATIONFAX', 'destination_fax')));
        
        $index = array_search(array('LBL_POTENTIALS_ORIGINPHONE1', 'origin_phone1'), $addressFields);
        
        array_splice($addressFields, $index+2, 0, array(array('LBL_OPPORTUNITY_ORIGINPHONE1TYPE', 'origin_phone1_type')));
        
        $index = array_search(array('LBL_POTENTIALS_DESTINATIONPHONE1', 'destination_phone1'), $addressFields);
        
        array_splice($addressFields, $index+2, 0, array(array('LBL_OPPORTUNITY_DESTINATIONPHONE1TYPE', 'destination_phone1_type')));
                
        $index = array_search(array('LBL_POTENTIALS_ORIGINPHONE2', 'origin_phone2'), $addressFields);
        
        array_splice($addressFields, $index+2, 0, array(array('LBL_OPPORTUNITY_ORIGINPHONE2TYPE', 'origin_phone2_type')));
        
        $index = array_search(array('LBL_POTENTIALS_DESTINATIONPHONE2', 'destination_phone2'), $addressFields);
        
        array_splice($addressFields, $index+2, 0, array(array('LBL_OPPORTUNITY_DESTINATIONPHONE2TYPE', 'destination_phone2_type')));
        
        //now the finished array is used to resequence all the block's fields
        foreach ($addressFields as $key => $field) {
            $sql = "UPDATE `vtiger_field` SET sequence = ? WHERE fieldlabel = ? AND fieldname = ? AND (tablename='vtiger_potential' OR tablename='vtiger_potentialscf')";
            //file_put_contents('logs/devLog.log', "\n SQL: $sql KEY: $key LABEL: ".$field[0]." NAME: ".$field[1], FILE_APPEND);
            $result = $db->pquery($sql, array($key, $field[0], $field[1]));
            echo "<br> $field[1] resequenced to $key <br>";
        }
        
        //ext field for origin phone 1
        $phoneExt1 = Vtiger_Field::getInstance('origin_phone1_ext', $oppsModule);
        if ($phoneExt1) {
            echo "<br> Field 'origin_phone1_ext' is already present. <br>";
        } else {
            echo "<br> Field 'origin_phone1_ext' not present. Creating it now<br>";
            $phoneExt1 = new Vtiger_Field();
            $phoneExt1->label = 'LBL_OPPORTUNITY_ORIGINPHONE1EXT';
            $phoneExt1->name = 'origin_phone1_ext';
            $phoneExt1->table = 'vtiger_potential';
            $phoneExt1->column = 'origin_phone1_ext';
            $phoneExt1->columntype = 'VARCHAR(255)';
            $phoneExt1->uitype = 1;
            $phoneExt1->typeofdata = 'V~O';
            $phoneExt1->quickcreate = 0;

            $oppsAddr->addField($phoneExt1);
            
            echo "<br> Field 'origin_phone1_ext' added.<br>";
        }
        
        //ext field for origin phone 2
        $phoneExt2 = Vtiger_Field::getInstance('origin_phone2_ext', $oppsModule);
        if ($phoneExt2) {
            echo "<br> Field 'origin_phone2_ext' is already present. <br>";
        } else {
            echo "<br> Field 'origin_phone2_ext' not present. Creating it now<br>";
            $phoneExt2 = new Vtiger_Field();
            $phoneExt2->label = 'LBL_OPPORTUNITY_ORIGINPHONE2EXT';
            $phoneExt2->name = 'origin_phone2_ext';
            $phoneExt2->table = 'vtiger_potential';
            $phoneExt2->column = 'origin_phone2_ext';
            $phoneExt2->columntype = 'VARCHAR(255)';
            $phoneExt2->uitype = 1;
            $phoneExt2->typeofdata = 'V~O';
            $phoneExt2->quickcreate = 0;

            $oppsAddr->addField($phoneExt2);
            echo "<br> Field 'origin_phone2_ext' added.<br>";
        }
        
        //ext field for destination phone 1
        $phoneExt3 = Vtiger_Field::getInstance('destination_phone1_ext', $oppsModule);
        if ($phoneExt3) {
            echo "<br> Field 'destination_phone1_ext' is already present. <br>";
        } else {
            echo "<br> Field 'destination_phone1_ext' not present. Creating it now<br>";
            $phoneExt3 = new Vtiger_Field();
            $phoneExt3->label = 'LBL_OPPORTUNITY_DESTINATIONPHONE1EXT';
            $phoneExt3->name = 'destination_phone1_ext';
            $phoneExt3->table = 'vtiger_potential';
            $phoneExt3->column = 'destination_phone1_ext';
            $phoneExt3->columntype = 'VARCHAR(255)';
            $phoneExt3->uitype = 1;
            $phoneExt3->typeofdata = 'V~O';
            $phoneExt3->quickcreate = 0;

            $oppsAddr->addField($phoneExt3);
            echo "<br> Field 'destination_phone1_ext' added.<br>";
        }
        
        //ext field for destination phone 2
        $phoneExt4 = Vtiger_Field::getInstance('destination_phone2_ext', $oppsModule);
        if ($phoneExt4) {
            echo "<br> Field 'destination_phone2_ext' is already present. <br>";
        } else {
            echo "<br> Field 'destination_phone2_ext' not present. Creating it now<br>";
            $phoneExt4 = new Vtiger_Field();
            $phoneExt4->label = 'LBL_OPPORTUNITY_DESTINATIONPHONE2EXT';
            $phoneExt4->name = 'destination_phone2_ext';
            $phoneExt4->table = 'vtiger_potential';
            $phoneExt4->column = 'destination_phone2_ext';
            $phoneExt4->columntype = 'VARCHAR(255)';
            $phoneExt4->uitype = 1;
            $phoneExt4->typeofdata = 'V~O';
            $phoneExt4->quickcreate = 0;

            $oppsAddr->addField($phoneExt4);
            echo "<br> Field 'destination_phone2_ext' added.<br>";
        }
    } else {
        echo "<br>Opps address block doesn't exist!<br>";
    }
    
    $oppsDates = Vtiger_Block::getInstance('LBL_POTENTIALS_DATES', $oppsModule);
    if ($oppsDates) {
        echo "<br>Opps dates block exists.<br>";
        $oppLoadDate = Vtiger_Field::getInstance('preferred_pldate', $oppsModule);
        if ($oppLoadDate) {
            echo "<br>preferred_pldate exists continuing with label swap<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_OPPORTUNITY_REQUESTEDMOVEDATE' WHERE fieldlabel = 'LBL_POTENTIAL_PLDATE'");
            echo "<br>preferred_pldate label swap done<br>";
        }
        $oppDeliveryDate = Vtiger_Field::getInstance('preferred_pddate', $oppsModule);
        if ($oppDeliveryDate) {
            echo "<br>preferred_pddate exists continuing with label swap<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_OPPORTUNITY_EXPECTEDDELIVERYDATE' WHERE fieldlabel = 'LBL_POTENTIALS_PDDELIVER' OR fieldlabel = 'LBL_POTENTIALS_PDDATE'");
            echo "<br>preferred_pddate label swap done<br>";
        }
        //create days to move field to hold autgenerated info
        $oppDaysToMove = Vtiger_Field::getInstance('days_to_move', $oppsModule);
        if ($oppDaysToMove) {
            echo "<br> opp days_to_move field already exists.<br>";
        } else {
            echo "<br> opp days_to_move field doesn't exist, adding it now.<br>";
            $oppDaysToMove = new Vtiger_Field();
            $oppDaysToMove->label = 'LBL_OPPORTUNITY_DAYSTOMOVE';
            $oppDaysToMove->name = 'days_to_move';
            $oppDaysToMove->table = 'vtiger_potential';
            $oppDaysToMove->column = 'days_to_move';
            $oppDaysToMove->columntype = 'VARCHAR(255)';
            $oppDaysToMove->uitype = 1;
            $oppDaysToMove->typeofdata = 'V~O';
            $oppDaysToMove->displaytype = 1;
            $oppDaysToMove->quickcreate = 0;

            $oppsDates->addField($oppDaysToMove);
            echo "<br> opp days_to_move field added.<br>";
        }
    } else {
        echo "<br>Opps dates block doesn't exist!<br>";
    }
    
    //create new employer assisting block
    $employerAssisting = Vtiger_Block::getInstance('LBL_OPPORTUNITY_EMPLOYERASSISTING', $oppsModule);
    if ($employerAssisting) {
        echo "<br>The LBL_OPPORTUNITY_EMPLOYERASSISTING block already exists<br>";
    } else {
        $employerAssisting = new Vtiger_Block();
        $employerAssisting->label = 'LBL_OPPORTUNITY_EMPLOYERASSISTING';
        $oppsModule->addBlock($employerAssisting);
    }
    
    //creating fields for new employer assisting block
    $enabled = Vtiger_Field::getInstance('enabled', $oppsModule);
    if ($enabled) {
        echo "<br> opps enabled field already exists.<br>";
    } else {
        echo "<br> opps enabled field doesn't exist, adding it now.<br>";
        $enabled = new Vtiger_Field();
        $enabled->label = 'LBL_OPPORTUNITY_ENABLED';
        $enabled->name = 'enabled';
        $enabled->table = 'vtiger_potential';
        $enabled->column = 'enabled';
        $enabled->columntype = 'VARCHAR(3)';
        $enabled->uitype = 56;
        $enabled->typeofdata = 'V~O';
        $enabled->quickcreate = 0;

        $employerAssisting->addField($enabled);
        echo "<br> opps enabled field added.<br>";
    }
    
    $contactName = Vtiger_Field::getInstance('contact_name', $oppsModule);
    if ($contactName) {
        echo "<br> opps contact_name field already exists.<br>";
    } else {
        echo "<br> opps contact_name field doesn't exist, adding it now.<br>";
        $contactName = new Vtiger_Field();
        $contactName->label = 'LBL_OPPORTUNITY_CONTACTNAME';
        $contactName->name = 'contact_name';
        $contactName->table = 'vtiger_potential';
        $contactName->column = 'contact_name';
        $contactName->columntype = 'VARCHAR(255)';
        $contactName->uitype = 1;
        $contactName->typeofdata = 'V~O';
        $contactName->quickcreate = 0;

        $employerAssisting->addField($contactName);
        echo "<br> opps contact_name field added.<br>";
    }
    
    $contactEmail = Vtiger_Field::getInstance('contact_email', $oppsModule);
    if ($contactEmail) {
        echo "<br> opps contact_email field already exists.<br>";
    } else {
        echo "<br> opps contact_email field doesn't exist, adding it now.<br>";
        $contactEmail = new Vtiger_Field();
        $contactEmail->label = 'LBL_OPPORTUNITY_CONTACTEMAIL';
        $contactEmail->name = 'contact_email';
        $contactEmail->table = 'vtiger_potential';
        $contactEmail->column = 'contact_email';
        $contactEmail->columntype = 'VARCHAR(255)';
        $contactEmail->uitype = 1;
        $contactEmail->typeofdata = 'V~O';
        $contactEmail->quickcreate = 0;

        $employerAssisting->addField($contactEmail);
        echo "<br> opps contact_email field added.<br>";
    }
    
    $companyName = Vtiger_Field::getInstance('company_name', $oppsModule);
    if ($companyName) {
        echo "<br> opps company_name field already exists.<br>";
    } else {
        echo "<br> opps company_name field doesn't exist, adding it now.<br>";
        $companyName = new Vtiger_Field();
        $companyName->label = 'LBL_OPPORTUNITY_COMPANYNAME';
        $companyName->name = 'company_name';
        $companyName->table = 'vtiger_potential';
        $companyName->column = 'company_name';
        $companyName->columntype = 'VARCHAR(255)';
        $companyName->uitype = 1;
        $companyName->typeofdata = 'V~O';
        $companyName->quickcreate = 0;

        $employerAssisting->addField($companyName);
        echo "<br> opps company_name field added.<br>";
    }
    
    $contactPhone = Vtiger_Field::getInstance('contact_phone', $oppsModule);
    if ($contactPhone) {
        echo "<br> opps contact_phone field already exists.<br>";
    } else {
        echo "<br> opps contact_phone field doesn't exist, adding it now.<br>";
        $contactPhone = new Vtiger_Field();
        $contactPhone->label = 'LBL_OPPORTUNITY_CONTACTPHONE';
        $contactPhone->name = 'contact_phone';
        $contactPhone->table = 'vtiger_potential';
        $contactPhone->column = 'contact_phone';
        $contactPhone->columntype = 'VARCHAR(255)';
        $contactPhone->uitype = 1;
        $contactPhone->typeofdata = 'V~O';
        $contactPhone->quickcreate = 0;

        $employerAssisting->addField($contactPhone);
        echo "<br> opps contact_phone field added.<br>";
    }
    
    $comments = Vtiger_Field::getInstance('employer_comments', $oppsModule);
    if ($comments) {
        echo "<br> opps employer_comments field already exists.<br>";
    } else {
        echo "<br> opps employer_comments field doesn't exist, adding it now.<br>";
        $comments = new Vtiger_Field();
        $comments->label = 'LBL_OPPORTUNITY_EMPLOYERCOMMENTS';
        $comments->name = 'employer_comments';
        $comments->table = 'vtiger_potential';
        $comments->column = 'employer_comments';
        $comments->columntype = 'VARCHAR(255)';
        $comments->uitype = 19;
        $comments->typeofdata = 'V~O';
        $comments->quickcreate = 0;

        $employerAssisting->addField($comments);
        echo "<br> opps employer_comments field added.<br>";
    }
    
    $specialTerms = Vtiger_Field::getInstance('special_terms', $oppsModule);
    if ($specialTerms) {
        echo "<br> opps special_terms field already exists.<br>";
    } else {
        echo "<br> opps special_terms field doesn't exist, adding it now.<br>";
        $specialTerms = new Vtiger_Field();
        $specialTerms->label = 'LBL_OPPORTUNITY_SPECIALTERMS';
        $specialTerms->name = 'special_terms';
        $specialTerms->table = 'vtiger_potential';
        $specialTerms->column = 'special_terms';
        $specialTerms->columntype = 'VARCHAR(255)';
        $specialTerms->uitype = 19;
        $specialTerms->typeofdata = 'V~O';
        $specialTerms->quickcreate = 0;

        $employerAssisting->addField($specialTerms);
        echo "<br> opps special_terms field added.<br>";
    }
    
    createRegistrationBlock($oppsModule, 'LBL_OPPORTUNITY_REGISTERSTS');
    //createRegistrationBlock($potsModule, 'LBL_OPPORTUNITY_REGISTERSTS');
} else {
    echo "<br>OPPS MODULE DOESN'T EXIST! FIELDS NOT MODIFIED<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";