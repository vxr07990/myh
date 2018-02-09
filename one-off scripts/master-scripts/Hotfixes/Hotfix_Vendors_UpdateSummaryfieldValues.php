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


//OT 3265 - Setting vendor fields displaying on search pop ups

$searchColumns = [
    'vendorname',       'type',
    'vendors_business_name',        'icode',
    'agentid',      'vendor_status'
    ];
$dontsearchColumns = [
    'vendor_no',        'phone',
    'email',        'website',
    'glacct',       'category',
    'street',       'pobox',
    'city',     'state',
    'postalcode',   'country',
    'origin_address1',      'origin_address2',
    'origin_city',      'origin_state',
    'origin_zip',       'origin_country',
    'phone2',       'email2',
    'fein',     'date_out_of_service',
    'date_reinstated',      'oos_reason',
    'oos_comments',         'vendors_contractor_type',
    'vendors_in_service_date',      'vendors_cancellation_date',
    'vendors_owner_ssn',       'vendors_owner_birthdate'
    ];

$module = Vtiger_Module::getInstance('Vendors');
if (!$module) {
    return;
}

if (!isset($db)) {
    $db = PearDatabase::getInstance();
}

foreach ($searchColumns as $searchField) {
    $sql = "UPDATE `vtiger_field` SET summaryfield = 1 WHERE fieldname = '".$searchField."' AND tabid = '".$module->id."'";
    $result = $db->pquery($sql);
    if ($result) {
        echo "$searchField successfully updated.<br/>";
    } else {
        echo "$searchField not updated. Something went wrong.<br/>";
    }
}

foreach ($dontsearchColumns as $dontSearchField) {
    $sql = "UPDATE `vtiger_field` SET summaryfield = 0 WHERE fieldname = '".$dontSearchField."' AND tabid = '".$module->id."'";
    $result = $db->pquery($sql);
    if ($result) {
        echo "$dontSearchField successfully updated.<br/>";
    } else {
        echo "$dontSearchField not updated. Something went wrong.<br/>";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";