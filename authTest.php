<?php
$username = 'mham';
$password      = 'a';

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'?'https://':'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']);
//$baseUrl = 'https://sirva-qa.movecrm.com';
$webserviceURL = $baseUrl.'/webservice.php';
$syncserviceURL = $baseUrl.'/syncwebservice.php';
$docUploadUrl = $baseUrl.'/documentmoduleupload.php';

print "webserviceURL: $webserviceURL<br />";

$ch             = curl_init();
curl_setopt($ch, CURLOPT_URL, $webserviceURL."?operation=getchallenge&username=".$username);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$curlResult = curl_exec($ch);
print "1: $curlResult<br />";
curl_close($ch);
$challengeResponse = json_decode($curlResult);
$element           = json_encode(['username' => $username, 'password' => $password]);
$post_string       = "mode=auth&element=$element";
$ch                = curl_init();
curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$curlResult = curl_exec($ch);
print "2: $curlResult<br />";
curl_close($ch);
$curlJSON     = json_decode($curlResult, true);
$accesskey    = $curlJSON['result']['accesskey'];
$generatedkey = md5($challengeResponse->result->token.$accesskey);
$post_string  = "operation=login&username=".$username."&accessKey=".$generatedkey;
$ch           = curl_init();
curl_setopt($ch, CURLOPT_URL, $webserviceURL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$curlResult = curl_exec($ch);
print "3: $curlResult<br />";
curl_close($ch);
$loginResponse = json_decode($curlResult);
$sessionId     = $loginResponse->result->sessionName;
$crmUserId     = $loginResponse->result->userId;
echo "<h1><b>sessionName : </b>".$sessionId."</h1>";
$thingsToTest = [
    //'describeLead',
    //'syncOpListAnswers',
    //'syncEstimate',
    //'getTariffsByType',
    //'getUserDepth',
    //'getOpListsByAgentCode',
    //'SaveOpListAnswers',
    //'testOpList',
    //'retrieveSurvey',
    //'retrieveOpportunityByLeadId',
    //'deleteLead',
    //'retrieveLead',
    //'CreateMilitaryOpportunity',
    //'UpdateLead',
    //'CreateLead',
    //'CreateLeadSource',
    //'RetrieveLeadSource',
    //'getlocaltariffs',
    //'createestimate',
    //'getimage',
    //'getwalks',
    //'query',
    //'getadmin',
    //'testTariffServicesStandard',
    //'createOrderTest',
    //methods that need to have an automated test
    //'getsurveys',
    //'retrieveratinglineitems',
    //'getstopscount',
    //'getpreshipchecklist',
    //'createvehicle',
    //'retrievevehicles',
    //'updatevehicle',
    //'deletevehicle',
    //'retrieveleadactivities',
    //'seteffectivetariff',
    //'getagentsbyuser',
    //'getevents',
    //'getstops',
    //'isadminonlytariff',
    //'geteventsbyid',
    //'checkrecordfordeletion',
    //'docUpload',
    //'migrateDocumentsForOrders',
    //please add anything new you're testing to this list and comment out the things you don't want to test
    //'rateEstimate',
    //'retrieveOpportunity'
];
if (in_array('docUpload', $thingsToTest)) {
    //testing docUpload
    $post_string = "sessionName=$sessionId&element=";
    $element     = "{
    \"filename\":\"Test\",
    \"doctitle\":\"Title\",
    \"filetype\":\"image\/png\",
    \"userid\":\"19x1\",
    \"agentid\":\"39x33264\",
    \"data\":\"iVBORw0KGgoAAAA\",
    \"parentid\":\"46x31773\"
}";
    /*$element = "{
"filename":"Test",
"doctitle":"Title",
"filetype":"image\/png",
"Userid":"19x1",
“agentid”:” 39x15”,
"data":"iVBORw0KGgoAAAA,
"Parentid": [
"1x23909",
"1x24240",
"1x22326"
]

}
";*/
    $post_string .= $element;
    echo "<br><b>post string for docUpload : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $docUploadURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('describeLead', $thingsToTest)) {
    $post_string = "?sessionName=$sessionId&element=";
    $testJSON    = "";
    echo "<br><b>post string for describeLead : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $webserviceURL.$post_string);
    //curl_setopt($ch, CURLOPT_POST, true);
    //curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    echo "<br><b>describeLead Response : </b>".$curlResult."<br>";
}
if (in_array('retrieveratinglineitems', $thingsToTest)) {
    //testing retrieveratinglineitems
    $element     = json_encode(['OpportunityId' => '46x1573']);
    $post_string = "mode=retrieveratinglineitems&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for retrieveratinglineitems : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('getsurveys', $thingsToTest)) {
    //testing getsurveys
    $element     = json_encode(['username' => 'lzumstein@igcsoftware.com']);
    $post_string = "mode=getsurveys&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for getsurveys : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('testTariffServicesStandard', $thingsToTest)) {
    $element     = "{
    \"service_name\":\"Actual Valuation\",
    \"tariff_section\":\"42x3300\",
    \"effective_date\":\"43x3301\",
    \"related_tariff\":\"41x3296\",
    \"rate_type\":\"Charge Per $100 (Valuation)\",
    \"applicability\":\"All Locations\",
    \"is_required\":\"0\",
    \"assigned_user_id\":\"19x1\",
    \"numChargePer100\":\"1\",
    \"chargePerHundredDeductible1\":\"700\",
    \"chargePerHundredRate1\":\"0.75\"
}";
    $post_string = "operation=create&sessionName=$sessionId&element=$element&elementType=TariffServices";
    echo "<br><b>post string for test Tariff Services Standard: </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $webserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('createOrderTest', $thingsToTest)) {
    $element = "{
	\"account_contacts\": \"\",
	\"account_contract\": \"\",
	\"agentid\": \"300\",
	\"billing_type\": \"RMC\",
	\"bill_city\": \"ASHBURN\",
	\"bill_code\": \"20147\",
	\"bill_country\": \"US\",
	\"bill_pobox\": \"ATTN  JEAN PANZA\",
	\"bill_state\": \"VA\",
	\"bill_street\": \"C/O AMBASSADOR WORLDWIDE MOVING,ATTN  JEAN PANZA\",
	\"business_line\": \"Interstate Move\",
	\"commodity\": \"HHG\",
	\"competitive\": \"\",
	\"createdtime\": \"2016-08-16\",
	\"description\": \"\",
	\"destination_address1\": \"5630 Calugas Court\",
	\"destination_address2\": \"\",
	\"destination_city\": \"FT SAM HOUSTON\",
	\"destination_country\": \"US\",
	\"destination_description\": \"Single Family\",
	\"destination_phone1\": \"\",
	\"destination_phone2\": \"\",
	\"destination_scac_code\": \"687500250\",
	\"destination_state\": \"TX\",
	\"destination_zip\": \"782\",
	\"estimate_type\": \"ESTIMATE\",
	\"gbl_number\": \"24079\",
	\"invoice_delivery_format\": \"EMAIL\",
	\"invoice_document_format\": \"PDF\",
	\"invoice_finance_charge\": \"\",
	\"invoice_format\": \"2\",
	\"invoice_pkg_format\": \"\",
	\"modifiedby\": null,
	\"modifiedtime\": \"2016-08-16\",
	\"ordering_installation_name\": \"\",
	\"ordersstatus\": \"Delivered\",
	\"orders_account\": \"\",
	\"orders_actualpudate\": \"2016-08-01\",
	\"orders_apu\": \"2016-07-15\",
	\"orders_assignedtrip\": \"\",
	\"orders_aweight\": \"0\",
	\"orders_bolnumber\": \"215929\",
	\"orders_contacts\": \"\",
	\"orders_ddate\": \"\",
	\"orders_discount\": \"59\",
	\"orders_dtdate\": \"\",
	\"orders_ecube\": \"\",
	\"orders_elinehaul\": \"\",
	\"orders_etotal\": \"\",
	\"orders_eweight\": \"\",
	\"orders_gweight\": \"47140\",
	\"orders_ldate\": \"2016-07-15\",
	\"orders_ldd_pdconfirmed\": \"\",
	\"orders_ldd_pddate\": \"\",
	\"orders_ldd_plconfirmed\": \"\",
	\"orders_ldd_pldate\": \"\",
	\"orders_ltdate\": \"\",
	\"orders_miles\": \"\",
	\"orders_minweight\": \"\",
	\"orders_netweight\": \"\",
	\"orders_no\": \"\",
	\"orders_onhold\": \"\",
	\"orders_opportunities\": \"\",
	\"orders_otherstatus\": \"\",
	\"orders_pcount\": \"\",
	\"orders_pdate\": \"\",
	\"orders_pddate\": \"\",
	\"orders_pldate\": \"\",
	\"orders_ponumber\": \"CNNQ0293175/AMNQ\",
	\"orders_ppdate\": \"\",
	\"orders_ptdate\": \"\",
	\"orders_pudate\": \"2016-07-15\",
	\"orders_relatedorders\": \"\",
	\"orders_rgweight\": \"\",
	\"orders_rnetweight\": \"\",
	\"orders_rtweight\": \"\",
	\"orders_sit\": \"\",
	\"orders_surveyd\": \"\",
	\"orders_surveyt\": \"\",
	\"orders_trip\": \"\",
	\"orders_tweight\": \"\",
	\"orders_vanlineregnum\": \"3356704\",
	\"origin_address1\": \"\",
	\"origin_address2\": \"\",
	\"origin_city\": \"RIENZI\",
	\"origin_country\": \"ALCORN\",
	\"origin_description\": \"Single Family\",
	\"origin_phone1\": \"\",
	\"origin_phone2\": \"\",
	\"origin_scac_code\": \"481270000\",
	\"origin_state\": \"MS\",
	\"origin_zip\": \"388\",
	\"payment_terms\": \"30 NET\",
	\"payment_type\": \"C\",
	\"personal_hhg_weight\": \"7360\",
	\"pro_gear_weights\": \"\",
	\"received_date\": \"2016-07-26\",
	\"registered_on\": \"\",
	\"assigned_user_id\": \"19x900\",
	\"targetenddate\": \"\",
	\"tariff_id\": \"0\",
	\"to_phone_number\": \"\",
	\"transferee_rank_grade\": \"\",
	\"transportation_officer\": \"\",
	\"numAgents\": \"8\",
	\"participantId_1\": \"none\",
	\"agent_type_1\": \"Booking Agent\",
	\"agents_id_1\": \"515\",
	\"agents_id_1_display\": \"Graebel San Antonio Movers LLC. (956)\",
	\"agent_permission_1\": \"full\",
	\"participantId_2\": \"\",
	\"agent_type_2\": \"\",
	\"agents_id_2\": \"\",
	\"agents_id_2_display\": \"\",
	\"agent_permission_2\": \"\",
	\"participantId_3\": \"\",
	\"agent_type_3\": \"\",
	\"agents_id_3\": \"\",
	\"agents_id_3_display\": \"\",
	\"agent_permission_3\": \"\",
	\"participantId_4\": \"none\",
	\"agent_type_4\": \"Destination Agent\",
	\"agents_id_4\": \"515\",
	\"agents_id_4_display\": \"Graebel San Antonio Movers LLC. (956)\",
	\"agent_permission_4\": \"full\",
	\"participantId_5\": \"none\",
	\"agent_type_5\": \"Warehouse Agent\",
	\"agents_id_5\": \"515\",
	\"agents_id_5_display\": \"Graebel San Antonio Movers LLC. (956)\",
	\"agent_permission_5\": \"full\",
	\"participantId_6\": \"\",
	\"agent_type_6\": \"\",
	\"agents_id_6\": \"\",
	\"agents_id_6_display\": \"\",
	\"agent_permission_6\": \"\",
	\"participantId_7\": \"\",
	\"agent_type_7\": \"\",
	\"agents_id_7\": \"\",
	\"agents_id_7_display\": \"\",
	\"agent_permission_7\": \"\",
	\"participantId_8\": \"none\",
	\"agent_type_8\": \"Sales Org\",
	\"agents_id_8\": \"515\",
	\"agents_id_8_display\": \"Graebel San Antonio Movers LLC. (956)\",
	\"agent_permission_8\": \"full\",
	\"id\": \"\"
}";
    $post_string = "operation=create&sessionName=$sessionId&element=$element&elementType=Orders";
    echo "<br><b>post string for test Create Estimates: </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $webserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('rateEstimate', $thingsToTest)) {
    $element = '{
    "id":"45x182468",
    "business_line_est":"Interstate Move",
    "local_tariff":"",
    "effective_tariff":"738"
    }';
    $post_string = "mode=rateEstimate&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for rateEstimate : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('syncEstimate', $thingsToTest)) {
    //testing syncEstimate
    //"id":"45x1663",
    $element = "{
   \"accesorial_exclusive_vehicle\":\"0\",
   \"accesorial_expedited_service\":\"0\",
   \"accesorial_fuel_surcharge\":null,
   \"accesorial_ot_loading\":\"0\",
   \"accesorial_ot_packing\":\"0\",
   \"accesorial_ot_unloading\":\"0\",
   \"accesorial_ot_unpacking\":\"0\",
   \"accessorial_disc\":0.0,
   \"accessorial_space_reserve_bool\":\"0\",
   \"account_id\":\"\",
   \"acc_exlabor_dest_hours\":\"0\",
   \"acc_exlabor_origin_hours\":\"0\",
   \"acc_exlabor_ot_dest_hours\":\"0\",
   \"acc_exlabor_ot_origin_hours\":\"0\",
   \"acc_ot_dest_applied\":\"0\",
   \"acc_ot_dest_weight\":\"0\",
   \"acc_ot_origin_applied\":\"0\",
   \"acc_ot_origin_weight\":\"0\",
   \"acc_selfstg_dest_applied\":\"0\",
   \"acc_selfstg_dest_ot\":\"0\",
   \"acc_selfstg_dest_weight\":\"0\",
   \"acc_selfstg_origin_applied\":\"0\",
   \"acc_selfstg_origin_ot\":\"0\",
   \"acc_selfstg_origin_weight\":\"0\",
   \"acc_shuttle_dest_applied\":\"0\",
   \"acc_shuttle_dest_miles\":\"0\",
   \"acc_shuttle_dest_ot\":\"0\",
   \"acc_shuttle_dest_over25\":\"0\",
   \"acc_shuttle_dest_weight\":\"0\",
   \"acc_shuttle_origin_applied\":\"0\",
   \"acc_shuttle_origin_miles\":\"0\",
   \"acc_shuttle_origin_ot\":\"0\",
   \"acc_shuttle_origin_over25\":\"0\",
   \"acc_shuttle_origin_weight\":\"0\",
   \"acc_wait_dest_hours\":\"0\",
   \"acc_wait_origin_hours\":\"0\",
   \"acc_wait_ot_dest_hours\":\"0\",
   \"acc_wait_ot_origin_hours\":\"0\",
   \"txtAdjustment\":\"0.00000000\",
   \"agentid\":\"16\",
   \"appliance_reservice\":\"0\",
   \"appliance_service\":\"0\",
   \"apply_exlabor_ot_rate_dest\":\"0\",
   \"apply_exlabor_ot_rate_origin\":\"0\",
   \"apply_exlabor_rate_dest\":\"0\",
   \"apply_exlabor_rate_origin\":\"0\",
   \"apply_free_fvp\":\"0\",
   \"apply_full_pack_rate_override\":\"0\",
   \"apply_sit_addl_day_dest\":\"0\",
   \"apply_sit_addl_day_origin\":\"0\",
   \"apply_sit_cartage_dest\":\"0\",
   \"apply_sit_cartage_origin\":\"0\",
   \"apply_sit_first_day_dest\":\"0\",
   \"apply_sit_first_day_origin\":\"0\",
   \"billing_type\":\"COD\",
   \"bill_city\":null,
   \"bill_code\":null,
   \"bill_country\":\"\",
   \"bill_pobox\":null,
   \"bill_state\":null,
   \"bill_street\":null,
   \"bottom_line_discount\":0.0,
   \"bulky_article_changes\":\"0.0000\",
   \"business_line_est\":\"Local Move\",
   \"comment\":\"\",
   \"consumption_fuel\":\"0\",
   \"contact_id\":\"\",
   \"contract\":\"\",
   \"conversion_rate\":\"1.000\",
   \"createdtime\":\"2016-04-12 17:58:54\",
   \"currency_id\":\"\",
   \"declared_value\":\"0\",
   \"demand_color\":\"\",
   \"description\":\"\",
   \"desired_total\":0.0,
   \"destination_address1\":\"12333 main st.\",
   \"destination_address2\":null,
   \"destination_city\":\"Reynoldsburg\",
   \"destination_phone1\":null,
   \"destination_phone2\":null,
   \"destination_state\":\"OH\",
   \"destination_zip\":\"43068\",
   \"des_sit2_container_number\":\"\",
   \"des_sit2_container_or_warehouse\":\"0\",
   \"des_sit2_date_in\":\"\",
   \"des_sit2_number_days\":\"0\",
   \"des_sit2_pickup_date\":\"\",
   \"des_sit2_weight\":\"0\",
   \"hdnDiscountAmount\":\"0.00000000\",
   \"hdnDiscountPercent\":\"0.000\",
   \"effective_date\":\"2016-04-13\",
   \"elevator_destination_occurrence\":\"0\",
   \"elevator_origin_occurrence\":\"0\",
   \"estimates_destination_country\":\"\",
   \"estimates_destination_county\":\"\",
   \"estimates_origin_country\":\"\",
   \"estimates_origin_county\":\"\",
   \"estimate_type\":\"\",
   \"exlabor_flat_dest\":\"0.00\",
   \"exlabor_flat_origin\":\"0.00\",
   \"exlabor_ot_flat_dest\":\"0.00\",
   \"exlabor_ot_flat_origin\":\"0.00\",
   \"exlabor_ot_rate_dest\":\"0.00\",
   \"exlabor_ot_rate_origin\":\"0.00\",
   \"exlabor_rate_dest\":\"0.00\",
   \"exlabor_rate_origin\":\"0.00\",
   \"express_truckload\":\"0\",
   \"flat_smf\":0.0,
   \"free_valuation_limit\":\"0\",
   \"free_valuation_type\":\"\",
   \"full_pack\":\"0\",
   \"full_pack_rate_override\":\"0.00\",
   \"full_unpack\":\"0\",
   \"grr\":0.0,
   \"grr_cp\":\"0.00\",
   \"grr_override\":\"0\",
   \"grr_override_amount\":0.0,
   \"increased_base\":\"0.00\",
   \"interstate_effective_date\":\"2016-04-13\",
   \"interstate_mileage\":\"0\",
   \"irr_charge\":0.0,
   \"is_primary\":\"1\",
   \"lead_type\":\"\",
   \"linehaul_disc\":0.0,
   \"listprice\":\"\",
   \"load_date\":\"2016-04-13\",
   \"local_bl_discount\":0.0,
   \"local_carrier\":\"\",
   \"longcarry_destination_occurrence\":\"0\",
   \"longcarry_origin_occurrence\":\"0\",
   \"min_declared_value_mult\":\"0.00\",
   \"modifiedby\":\"19x54\",
   \"modifiedtime\":\"2016-04-13 20:27:35\",
   \"move_type\":\"Local US\",
   \"nat_account_no\":\"\",
   \"orders_id\":\"\",
   \"origin_address1\":\"2 East Main Street\",
   \"origin_address2\":null,
   \"origin_city\":\"Reynoldsburg\",
   \"origin_phone1\":\"2044441234\",
   \"origin_phone2\":null,
   \"origin_state\":\"OH\",
   \"origin_zip\":\"48323\",
   \"ori_sit2_container_number\":\"\",
   \"ori_sit2_container_or_warehouse\":\"0\",
   \"ori_sit2_date_in\":\"\",
   \"ori_sit2_number_days\":\"0\",
   \"ori_sit2_pickup_date\":\"\",
   \"ori_sit2_weight\":\"0\",
   \"packing_disc\":0.0,
   \"parent_contract\":\"\",
   \"percent_smf\":0.0,
   \"potential_id\":\"46x35918\",
   \"pre_tax_total\":\"0.00000000\",
   \"pricing_color_lock\":\"0\",
   \"pricing_level\":\"\",
   \"productid\":\"\",
   \"quantity\":\"\",
   \"quotestage\":\"Created\",
   \"quote_no\":null,
   \"rate_per_100\":\"0.00\",
   \"shipper_type\":\"COD\",
   \"sit_addl_day_dest_override\":\"0.00\",
   \"sit_addl_day_origin_override\":\"0.00\",
   \"sit_cartage_dest_override\":\"0.00\",
   \"sit_cartage_origin_override\":\"0.00\",
   \"sit_dest_date_in\":\"\",
   \"sit_dest_delivery_date\":\"\",
   \"sit_dest_miles\":\"0\",
   \"sit_dest_number_days\":\"0\",
   \"sit_dest_overtime\":\"0\",
   \"sit_dest_weight\":\"0\",
   \"sit_dest_zip\":\"0\",
   \"sit_disc\":0.0,
   \"sit_first_day_dest_override\":\"0.00\",
   \"sit_first_day_origin_override\":\"0.00\",
   \"sit_origin_date_in\":\"\",
   \"sit_origin_miles\":\"0\",
   \"sit_origin_number_days\":\"0\",
   \"sit_origin_overtime\":\"0\",
   \"sit_origin_pickup_date\":\"\",
   \"sit_origin_weight\":\"0\",
   \"sit_origin_zip\":\"0\",
   \"smf_type\":\"0\",
   \"assigned_user_id\":\"19x54\",
   \"space_reserve_bool\":\"0\",
   \"space_reserve_cf\":\"0\",
   \"stair_destination_occurrence\":\"0\",
   \"stair_origin_occurrence\":\"0\",
   \"subject\":\"Estimate\",
   \"hdnSubTotal\":\"\",
   \"hdnS_H_Amount\":\"0.00000000\",
   \"hdnS_H_Percent\":\"0\",
   \"tax1\":\"\",
   \"tax2\":\"\",
   \"tax3\":\"\",
   \"hdnTaxType\":\"\",
   \"terms_conditions\":\"\",
   \"hdnGrandTotal\":\"43000.00000000\",
   \"syncrate\":\"1\",
   \"validtill\":\"0001-01-01\",
   \"valuation_amount\":0.0,
   \"valuation_deductible\":\"\",
   \"valuation_flat_charge\":\"0.00\",
   \"weight\":\"0\",
   \"id\":\"45x36074\",
   \"effective_tariff\":\"\",
   \"origin_phone1_type\":\"Home\",
   \"origin_phone2_type\":\"Work\",
   \"destination_phone1_type\":\"Home\",
   \"destination_phone2_type\":\"Work\",
   \"pickup_date\":\"2016-04-13\",
   \"local_tariff\":\"35684\",
   \"SectionDiscount35685\":\"0.00\",
   \"SectionDiscount35686\":\"0.00\",
   \"SectionDiscount35687\":\"0.00\",
   \"SectionDiscount35714\":\"0.00\",
   \"SectionDiscount35739\":\"0.00\",
   \"cost_service_total35689\":\"0.00\",
   \"cost_container_total35689\":\"0.00\",
   \"cost_packing_total35689\":\"0.00\",
   \"cost_unpacking_total35689\":\"0.00\",
   \"cost_crating_total35689\":\"0.00\",
   \"cost_uncrating_total35689\":\"0.00\",
   \"Men35689\":\"7\",
   \"Vans35689\":\"3\",
   \"Hours35689\":\"15.00\",
   \"TravelTime35689\":\"13.00\",
   \"Rate35689\":\"25.00\",
   \"cost_service_total35693\":\"0.00\",
   \"cost_container_total35693\":\"0.00\",
   \"cost_packing_total35693\":\"0.00\",
   \"cost_unpacking_total35693\":\"0.00\",
   \"cost_crating_total35693\":\"0.00\",
   \"cost_uncrating_total35693\":\"0.00\",
   \"Miles35693\":\"0\",
   \"Weight35693\":\"0.00\",
   \"Rate35693\":\"0.00\",
   \"Excess35693\":\"\",
   \"cost_service_total35696\":\"0.00\",
   \"cost_container_total35696\":\"0.00\",
   \"cost_packing_total35696\":\"0.00\",
   \"cost_unpacking_total35696\":\"0.00\",
   \"cost_crating_total35696\":\"0.00\",
   \"cost_uncrating_total35696\":\"0.00\",
   \"Miles35696\":\"0\",
   \"Weight35696\":\"0.00\",
   \"Rate35696\":\"0.00\",
   \"calcWeight35696\":\"0.00\",
   \"cost_service_total35710\":\"0.00\",
   \"cost_container_total35710\":\"0.00\",
   \"cost_packing_total35710\":\"0.00\",
   \"cost_unpacking_total35710\":\"0.00\",
   \"cost_crating_total35710\":\"0.00\",
   \"cost_uncrating_total35710\":\"0.00\",
   \"Miles35710\":\"0\",
   \"Weight35710\":\"0.00\",
   \"Rate35710\":\"0.00\",
   \"cost_service_total35716\":\"0.00\",
   \"cost_container_total35716\":\"0.00\",
   \"cost_packing_total35716\":\"0.00\",
   \"cost_unpacking_total35716\":\"0.00\",
   \"cost_crating_total35716\":\"0.00\",
   \"cost_uncrating_total35716\":\"0.00\",
   \"County35716\":\"\",
   \"Rate35716\":\"\",
   \"cost_service_total35741\":\"0.00\",
   \"cost_container_total35741\":\"0.00\",
   \"cost_packing_total35741\":\"0.00\",
   \"cost_unpacking_total35741\":\"0.00\",
   \"cost_crating_total35741\":\"0.00\",
   \"cost_uncrating_total35741\":\"0.00\",
   \"cost_service_total35759\":\"0.00\",
   \"cost_container_total35759\":\"0.00\",
   \"cost_packing_total35759\":\"0.00\",
   \"cost_unpacking_total35759\":\"0.00\",
   \"cost_crating_total35759\":\"0.00\",
   \"cost_uncrating_total35759\":\"0.00\",
   \"cost_service_total35691\":\"0.00\",
   \"cost_container_total35691\":\"0.00\",
   \"cost_packing_total35691\":\"0.00\",
   \"cost_unpacking_total35691\":\"0.00\",
   \"cost_crating_total35691\":\"0.00\",
   \"cost_uncrating_total35691\":\"0.00\",
   \"Quantity35691\":\"0\",
   \"Hours35691\":\"0.00\",
   \"Rate35691\":\"55.00\",
   \"cost_service_total35692\":\"0.00\",
   \"cost_container_total35692\":\"0.00\",
   \"cost_packing_total35692\":\"0.00\",
   \"cost_unpacking_total35692\":\"0.00\",
   \"cost_crating_total35692\":\"0.00\",
   \"cost_uncrating_total35692\":\"0.00\",
   \"Deductible35692\":\"0.00\",
   \"Amount35692\":\"0.00\",
   \"Rate35692\":\"0.00\",
   \"cost_service_total35792\":\"0.00\",
   \"cost_container_total35792\":\"0.00\",
   \"cost_packing_total35792\":\"0.00\",
   \"cost_unpacking_total35792\":\"0.00\",
   \"cost_crating_total35792\":\"0.00\",
   \"cost_uncrating_total35792\":\"0.00\",
   \"ValuationType35792\":\"0\",
   \"Coverage35792\":\"0.00\",
   \"Amount35792\":\"0.00\",
   \"Deductible35792\":\"0.00\",
   \"Rate35792\":\"0.00\",
   \"cost_service_total35713\":\"0.00\",
   \"cost_container_total35713\":\"0.00\",
   \"cost_packing_total35713\":\"0.00\",
   \"cost_unpacking_total35713\":\"0.00\",
   \"cost_crating_total35713\":\"0.00\",
   \"cost_uncrating_total35713\":\"0.00\",
   \"cost_service_total35763\":\"0.00\",
   \"cost_container_total35763\":\"0.00\",
   \"cost_packing_total35763\":\"0.00\",
   \"cost_unpacking_total35763\":\"0.00\",
   \"cost_crating_total35763\":\"0.00\",
   \"cost_uncrating_total35763\":\"0.00\",
   \"CubicFeet35763\":\"0.00\",
   \"Days35763\":\"0\",
   \"Rate35763\":\"75.00\",
   \"cost_service_total35778\":\"0.00\",
   \"cost_container_total35778\":\"0.00\",
   \"cost_packing_total35778\":\"0.00\",
   \"cost_unpacking_total35778\":\"0.00\",
   \"cost_crating_total35778\":\"0.00\",
   \"cost_uncrating_total35778\":\"0.00\",
   \"CubicFeet35778\":\"0.00\",
   \"Months35778\":\"0\",
   \"Rate35778\":\"88.00\",
   \"cost_service_total35782\":\"0.00\",
   \"cost_container_total35782\":\"0.00\",
   \"cost_packing_total35782\":\"0.00\",
   \"cost_unpacking_total35782\":\"0.00\",
   \"cost_crating_total35782\":\"0.00\",
   \"cost_uncrating_total35782\":\"0.00\",
   \"Weight35782\":\"0.00\",
   \"Rate35782\":\"99.00\",
   \"cost_service_total35787\":\"0.00\",
   \"cost_container_total35787\":\"0.00\",
   \"cost_packing_total35787\":\"0.00\",
   \"cost_unpacking_total35787\":\"0.00\",
   \"cost_crating_total35787\":\"0.00\",
   \"cost_uncrating_total35787\":\"0.00\",
   \"Weight35787\":\"0.00\",
   \"Days35787\":\"0\",
   \"Rate35787\":\"11.00\",
   \"cost_service_total35788\":\"0.00\",
   \"cost_container_total35788\":\"0.00\",
   \"cost_packing_total35788\":\"0.00\",
   \"cost_unpacking_total35788\":\"0.00\",
   \"cost_crating_total35788\":\"0.00\",
   \"cost_uncrating_total35788\":\"0.00\",
   \"Quantity35788\":\"5\",
   \"Rate35788\":\"77.00\",
   \"cost_service_total35790\":\"0.00\",
   \"cost_container_total35790\":\"0.00\",
   \"cost_packing_total35790\":\"0.00\",
   \"cost_unpacking_total35790\":\"0.00\",
   \"cost_crating_total35790\":\"0.00\",
   \"cost_uncrating_total35790\":\"0.00\",
   \"Quantity35790\":\"0\",
   \"Days35790\":\"0\",
   \"Rate35790\":\"99.00\",
   \"cost_service_total35791\":\"0.00\",
   \"cost_container_total35791\":\"0.00\",
   \"cost_packing_total35791\":\"0.00\",
   \"cost_unpacking_total35791\":\"0.00\",
   \"cost_crating_total35791\":\"0.00\",
   \"cost_uncrating_total35791\":\"0.00\",
   \"Quantity35791\":\"0\",
   \"Months35791\":\"0\",
   \"Rate35791\":\"45.00\"
}";
    $element = '{
    "potential_id": "46x28710",
    "assigned_user_id": "19x6",
    "agentid": "15",
    "modifiedby": "19x6",
    "business_line_est": "Interstate Move",
    "move_type": "Interstate",
    "interstate_effective_date": "2016-04-22",
    "effective_date": "2016-04-22",
    "effective_tariff": "17",
    "validtill": "2016-05-22",
    "bill_street": null, "bill_city": null, "bill_state": null, "bill_pobox": null, "bill_code": null,
    "bill_country": "",
    "origin_city": "Columbus", "origin_state": "OH", "origin_zip": "43229",
    "destination_city": "Beverly Hills", "destination_state": "CA", "destination_zip": "90210",
    "weight": "0",
    "pickup_date": "2016-04-22",
    "load_date": "2016-04-22",
    "interstate_mileage": "2231",
    "bottom_line_discount": 0.0,
    "valuation_deductible": "FVP - $0",
    "valuation_amount": 6000.0,
    "accesorial_fuel_surcharge": 4.0, "irr_charge": 4.0,
    "demand_color": "Green",
    "pricing_level": "Level 3",
    "flat_smf": 233.98,
    "percent_smf": 18.6,
    "syncrate": "1",
    "subject": "Estimate", "quote_no": null, "quotestage": "Created", "shipper_type": "COD", "billing_type": "COD", "contact_id": "", "account_id": "", "origin_address1": "Will Advise", "origin_address2": null, "destination_address1": "Will Advise", "destination_address2": null, "origin_phone1": "6145555555", "origin_phone1_type": "Home", "origin_phone2": null, "origin_phone2_type": "Work", "destination_phone1": null, "destination_phone1_type": "Home", "destination_phone2": null, "destination_phone2_type": "Work", "createdtime": "2016-04-22 19:40:19", "is_primary": "1", "pre_tax_total": "0.00000000", "conversion_rate": "1.0000", "quantity": "1.000", "hdnS_H_Percent": "0", "hdnS_H_Amount": "0.00000000", "hdnSubTotal": "0.00000000", "hdnGrandTotal": "1663.15206500", "txtAdjustment": "0.00000000", "tax1": "0.000", "tax2": "0.000", "tax3": "0.000", "listprice": "0.00000000", "local_bl_discount": 0.0, "modifiedtime": "2016-04-22 19:40:19", "terms_conditions": "", "description": "", "linehaul_disc": 0.0, "accessorial_disc": 0.0, "packing_disc": 0.0, "sit_disc": 0.0, "full_pack": "0", "full_unpack": "0", "accesorial_expedited_service": "0", "acc_shuttle_origin_applied": "0", "acc_shuttle_origin_weight": "0", "acc_shuttle_origin_miles": "0", "acc_shuttle_origin_over25": "0", "acc_shuttle_origin_ot": "0", "acc_ot_origin_applied": "0", "acc_ot_origin_weight": "0", "acc_exlabor_origin_hours": "0", "acc_exlabor_ot_origin_hours": "0", "acc_wait_origin_hours": "0", "acc_wait_ot_origin_hours": "0", "acc_shuttle_dest_applied": "0", "acc_shuttle_dest_weight": "0", "acc_shuttle_dest_miles": "0", "acc_shuttle_dest_over25": "0", "acc_shuttle_dest_ot": "0", "acc_ot_dest_applied": "0", "acc_ot_dest_weight": "0", "acc_exlabor_dest_hours": "0", "acc_exlabor_ot_dest_hours": "0", "acc_wait_dest_hours": "0", "acc_wait_ot_dest_hours": "0", "desired_total": 0.0, "apply_custom_pack_rate_override": "1", "apply_sit_first_day_origin": "0", "apply_sit_addl_day_origin": "0", "apply_sit_cartage_origin": "0", "apply_sit_first_day_dest": "0", "apply_sit_addl_day_dest": "0", "apply_sit_cartage_dest": "0", "apply_exlabor_rate_origin": "0", "apply_exlabor_ot_rate_origin": "0", "apply_exlabor_rate_dest": "0", "apply_exlabor_ot_rate_dest": "0", "smf_type": "0", "apply_custom_sit_rate_override": "0", "pack1": "", "unpack1": "", "ot_pack1": "", "ot_unpack1": "", "packCustomRate1": "", "pack4": "", "unpack4": "", "ot_pack4": "", "ot_unpack4": "", "packCustomRate4": "", "pack7": "", "unpack7": "", "ot_pack7": "", "ot_unpack7": "", "packCustomRate7": "", "pack10": "", "unpack10": "", "ot_pack10": "", "ot_unpack10": "", "packCustomRate10": "", "pack50": "", "unpack50": "", "ot_pack50": "", "ot_unpack50": "", "packCustomRate50": "", "pack108": "", "unpack108": "", "ot_pack108": "", "ot_unpack108": "", "packCustomRate108": "", "pack111": "", "unpack111": "", "ot_pack111": "", "ot_unpack111": "", "packCustomRate111": "", "pack123": "", "unpack123": "", "ot_pack123": "", "ot_unpack123": "", "packCustomRate123": "", "pack170": "", "unpack170": "", "ot_pack170": "", "ot_unpack170": "", "packCustomRate170": "", "pack200": "", "unpack200": "", "ot_pack200": "", "ot_unpack200": "", "packCustomRate200": "", "pack208": "", "unpack208": "", "ot_pack208": "", "ot_unpack208": "", "packCustomRate208": "", "pack245": "", "unpack245": "", "ot_pack245": "", "ot_unpack245": "", "packCustomRate245": "", "pack313": "", "unpack313": "", "ot_pack313": "", "ot_unpack313": "", "packCustomRate313": "", "pack345": "", "unpack345": "", "ot_pack345": "", "ot_unpack345": "", "packCustomRate345": "", "pack427": "", "unpack427": "", "ot_pack427": "", "ot_unpack427": "", "packCustomRate427": "", "pack12": "", "unpack12": "", "ot_pack12": "", "ot_unpack12": "", "packCustomRate12": "", "pack232": "", "unpack232": "", "ot_pack232": "", "ot_unpack232": "", "packCustomRate232": "", "pack271": "", "unpack271": "", "ot_pack271": "", "ot_unpack271": "", "packCustomRate271": "", "pack1309": "", "unpack1309": "", "ot_pack1309": "", "ot_unpack1309": "", "packCustomRate1309": "", "pack1311": "", "unpack1311": "", "ot_pack1311": "", "ot_unpack1311": "", "packCustomRate1311": "", "pack1313": "", "unpack1313": "", "ot_pack1313": "", "ot_unpack1313": "", "packCustomRate1313": "", "bulky9": "", "bulky16": "", "bulky17": "", "bulky18": "", "bulky20": "", "bulky27": "", "bulky28": "", "bulky47": "", "bulky48": "", "bulky49": "", "bulky67": "", "bulky68": "", "bulky69": "", "bulky70": "", "bulky71": "", "bulky121": "", "bulky122": "", "bulky126": "", "bulky140": "", "bulky141": "", "bulky142": "", "bulky182": "", "bulky183": "", "bulky189": "", "bulky190": "", "bulky191": "", "bulky196": "", "bulky197": "", "bulky198": "", "bulky199": "", "bulky202": "", "bulky203": "", "bulky204": "", "bulky212": "", "bulky216": "", "bulky217": "", "bulky235": "", "bulky263": "", "bulky264": "", "bulky281": "", "bulky282": "", "bulky283": "", "bulky285": "", "bulky286": "", "bulky287": "", "bulky288": "", "bulky289": "", "bulky308": "", "bulky326": "", "bulky327": "", "bulky328": "", "bulky334": "", "bulky336": "", "bulky348": "", "bulky349": "", "bulky353": "", "bulky362": "", "bulky363": "", "bulky392": "", "bulky401": "", "bulky402": "", "bulky403": "", "bulky404": "", "bulky419": "", "bulky423": "", "bulky424": "", "bulky426": "", "bulky435": "", "bulky436": "", "bulky438": "", "bulky439": "", "bulky58": "", "bulky188": "", "bulky333": ""
}';
    $post_string = "mode=syncEstimate&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for syncEstimate : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('getTariffsByType', $thingsToTest)) {
    //testing getTariffsByType
    $element     = json_encode(['Type' => '400N Base']);
    $post_string = "mode=getTariffsByType&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for getTariffsByType : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('getUserDepth', $thingsToTest)) {
    //testing getUserDepth
    $element     = json_encode(['userId' => '19x6']);
    $post_string = "mode=GetUserDepth&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for GetUserDepth : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('getOpListsByAgentCode', $thingsToTest)) {
    //testing getOpListsByAgentCode
    $element     = json_encode(['agent_code' => '2222000']);
    $post_string = "mode=GetOpListsByAgentCode&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for GetOpListsByAgentCode : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('SaveOpListAnswers', $thingsToTest)) {
    //testing SaveOpListAnswers
    $element     = "{
    \"module\": \"OPList\",
    \"action\": \"SaveOpListAnswers\",
    \"record\": \"531\",
    \"source_record\": \"530\",
    \"display_name\": \"Test\",
    \"numSections\": \"3\",
    \"sectionOrder_1\": \"1\",
    \"section_name_1\": \"Section 1 No Answers\",
    \"numQuestions_1\": \"7\",
    \"question_type_1_1\": \"Text\",
    \"question_order_1_1\": \"1\",
    \"question_1_1\": \"Text Question with No Default Answer\",
    \"answer_text_1_1\": \"Nailed it!\",
    \"default_answer_text_1_1\": \"This is my answer to this question\",
    \"question_type_1_2\": \"Yes/No\",
    \"question_order_1_2\": \"2\",
    \"question_1_2\": \"Yes/No Question with No Default Answer\",
    \"answer_bool_1_2\": \"on\",
    \"default_answer_bool_1_2\": \"0\",
    \"question_type_1_3\": \"Date\",
    \"question_order_1_3\": \"3\",
    \"question_1_3\": \"Date Question with No Default Answer\",
    \"answer_date_1_3\": \"01-28-2016\",
    \"default_answer_date_1_3\": \"01-28-2016\",
    \"question_type_1_4\": \"Date and Time\",
    \"question_order_1_4\": \"4\",
    \"question_1_4\": \"Date and Time Question with No Default Answer\",
    \"answer_datetime_date_1_4\": \"01-28-2016\",
    \"default_answer_datetime_date_1_4\": \"--\",
    \"answer_datetime_time_1_4\": \"10:00 PM\",
    \"default_answer_datetime_time_1_4\": \"2:52 PM\",
    \"question_type_1_5\": \"Time\",
    \"question_order_1_5\": \"5\",
    \"question_1_5\": \"Time Question with No Default Answer\",
    \"answer_time_1_5\": \"05:00 PM\",
    \"default_answer_time_1_5\": \"5:00 PM\",
    \"question_type_1_6\": \"Quantity\",
    \"question_order_1_6\": \"6\",
    \"question_1_6\": \"Quantity Question with No Default Answer\",
    \"answer_number_1_6\": \"11\",
    \"answer_use_decimal_1_6\": \"\",
    \"default_answer_use_decimal_1_6\": \"\",
    \"default_answer_number_1_6\": \"0\",
    \"question_type_1_7\": \"Multiple Choice\",
    \"question_order_1_7\": \"7\",
    \"question_1_7\": \"Multiple Choice Question with No Default Answer\",
    \"numOptions_1_7\": \"3\",
    \"default_answer_select_multiple_1_7\": \"0\",
    \"option_order_1_7_3\": \"1\",
    \"default_multi_option_1_7_3\": \"0\",
    \"MultiOption_prev_1_7\": \"none\",
    \"MultiOption_1_7\": \"3\",
    \"multi_option_1_7_3\": \"on\",
    \"multi_option_answer_1_7_3\": \"C\",
    \"option_order_1_7_2\": \"2\",
    \"default_multi_option_1_7_2\": \"0\",
    \"multi_option_1_7_2\": \"0\",
    \"multi_option_answer_1_7_2\": \"B\",
    \"option_order_1_7_1\": \"3\",
    \"default_multi_option_1_7_1\": \"0\",
    \"multi_option_1_7_1\": \"0\",
    \"multi_option_answer_1_7_1\": \"A\",
    \"sectionOrder_2\": \"2\",
    \"section_name_2\": \"Section 2 With Answers\",
    \"numQuestions_2\": \"8\",
    \"question_type_2_1\": \"Text\",
    \"question_order_2_1\": \"1\",
    \"question_2_1\": \"Text Question with Default Answer\",
    \"answer_text_2_1\": \"This is my answer to this question\",
    \"default_answer_text_2_1\": \"This is the Default Answer\",
    \"question_type_2_2\": \"Yes/No\",
    \"question_order_2_2\": \"2\",
    \"question_2_2\": \"Yes/No Question with Default Answer\",
    \"answer_bool_2_2\": \"0\",
    \"default_answer_bool_2_2\": \"0\",
    \"question_type_2_3\": \"Date\",
    \"question_order_2_3\": \"3\",
    \"question_2_3\": \"Date Question with Default Answer\",
    \"answer_date_2_3\": \"01-28-2016\",
    \"default_answer_date_2_3\": \"01-30-2016\",
    \"question_type_2_4\": \"Date and Time\",
    \"question_order_2_4\": \"4\",
    \"question_2_4\": \"Date and Time Question with Default Answer\",
    \"answer_datetime_date_2_4\": \"01-28-2016\",
    \"default_answer_datetime_date_2_4\": \"01-30-2016\",
    \"answer_datetime_time_2_4\": \"03:30 PM\",
    \"default_answer_datetime_time_2_4\": \"12:00 PM\",
    \"question_type_2_5\": \"Time\",
    \"question_order_2_5\": \"5\",
    \"question_2_5\": \"Time Question with Default Answer\",
    \"answer_time_2_5\": \"03:30 PM\",
    \"default_answer_time_2_5\": \"12:00 PM\",
    \"question_type_2_6\": \"Quantity\",
    \"question_order_2_6\": \"6\",
    \"question_2_6\": \"Quantity Question with Default Answer\",
    \"answer_number_2_6\": \"11\",
    \"answer_use_decimal_2_6\": \"\",
    \"default_answer_use_decimal_2_6\": \"\",
    \"default_answer_number_2_6\": \"0\",
    \"question_type_2_7\": \"Multiple Choice\",
    \"question_order_2_7\": \"7\",
    \"question_2_7\": \"Multiple Choice Question with Default Answer\",
    \"numOptions_2_7\": \"3\",
    \"default_answer_select_multiple_2_7\": \"1\",
    \"option_order_2_7_1\": \"1\",
    \"default_multi_option_2_7_1\": \"1\",
    \"MultiOption_prev_2_7\": \"none\",
    \"multi_option_2_7_1\": \"0\",
    \"multi_option_answer_2_7_1\": \"A\",
    \"option_order_2_7_2\": \"2\",
    \"default_multi_option_2_7_2\": \"0\",
    \"multi_option_2_7_2\": \"0\",
    \"multi_option_answer_2_7_2\": \"B\",
    \"option_order_2_7_3\": \"3\",
    \"default_multi_option_2_7_3\": \"1\",
    \"multi_option_2_7_3\": \"0\",
    \"multi_option_answer_2_7_3\": \"C\",
    \"sectionOrder_3\": \"3\",
    \"section_name_3\": \"Section 3 Reordered Things\",
    \"numQuestions_3\": \"3\",
    \"question_type_3_3\": \"Text\",
    \"question_order_3_3\": \"1\",
    \"question_3_3\": \"When was this question made?\",
    \"answer_text_3_3\": \"I answered this one first\",
    \"default_answer_text_3_3\": \"This one was made third\",
    \"question_type_3_2\": \"Text\",
    \"question_order_3_2\": \"2\",
    \"question_3_2\": \"When was this question made?\",
    \"answer_text_3_2\": \"I answered this one second\",
    \"default_answer_text_3_2\": \"This one was made second\",
    \"question_type_3_1\": \"Text\",
    \"question_order_3_1\": \"3\",
    \"question_3_1\": \"When was this question made?\",
    \"answer_text_3_1\": \"I answered this one third\",
    \"default_answer_text_3_1\": \"This one was made first\"
}";
    $post_string = "mode=saveOpListAnswers&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for SaveOpListAnswers : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('testOpList', $thingsToTest)) {
    //testing testOpList
    $element     = json_encode(['OpListId' => '531']);
    $post_string = "mode=testOpList&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for testOpList : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('retrieveSurvey', $thingsToTest)) {
    //testing retrieveSurvey
    $element     = json_encode(['LeadId' => '10x398']);
    $post_string = "mode=RetrieveSurvey&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for RetrieveSurvey : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('retrieveOpportunityByLeadId', $thingsToTest)) {
    //testing retrieveOpportunityByLeadId
    $element     = json_encode(['LeadId' => '10x441']);
    $post_string = "mode=RetrieveOpportunityByLeadId&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for RetrieveOpportunityByLeadId : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('retrieveOpportunity', $thingsToTest)) {
    //testing retrieveOpportunity
    $element     = json_encode(['OpportunityId' => '46x201340']);
    $post_string = "mode=RetrieveOpportunity&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for RetrieveOpportunity : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('deleteLead', $thingsToTest)) {
    //testing deleteLead
    $element     = json_encode(['LeadId' => '10x18']);
    $post_string = "mode=DeleteLead&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for DeleteLead : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('retrieveLead', $thingsToTest)) {
    //testing retrieveLead
    $element     = json_encode(['LeadId' => '10x19']);
    $post_string = "mode=RetrieveLead&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for RetrieveLead : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('CreateMilitaryOpportunity', $thingsToTest)) {
    //testing CreateMilitaryOpportunity
    $element     = "{
    \"opportunity_name\": \"This is a Military Opportunity\",
    \"contact_name\": \"Alex Smith\",
    \"contact_email\": \"asmith@igcsoftware.com\",
    \"amount\": \"404.11\",
    \"assigned_to\": \"2\",
    \"next_step\": \"Next Step\",
    \"salesperson\": \"5\",
    \"order_number\": \"A0123456\",
    \"assigned_date\": \"2015-12-01\",
    \"received_date\": \"2015-12-01\",
    \"comments\": \"This is a comment\",
    \"participating_agents\": [
        {
            \"type\": \"Booking Agent\",
            \"agent\": \"1\",
            \"permission\": \"Full\"
        },
        {
            \"type\": \"Destination Agent\",
            \"agent\": \"2\",
            \"permission\": \"No-Access\"
        }
    ],
    \"origin_address_1\": \"8202 Inistork Ct\",
    \"origin_address_2\": \"\",
    \"origin_city\": \"Dublin\",
    \"origin_state\": \"OH\",
    \"origin_zip\": \"43017\",
    \"origin_phone_1\": \"6147460925\",
    \"origin_phone_1_type\": \"Cell\",
    \"origin_phone_2\": \"6147660471\",
    \"origin_phone_2_type\": \"Home\",
    \"origin_country\": \"United States\",
    \"origin_description\": \"My House\",
    \"destination_address_1\": \"1600 Pennsylvania Ave NW\",
    \"destination_address_2\": \"West Wing\",
    \"destination_city\": \"Washington\",
    \"destination_state\": \"DC\",
    \"destination_zip\": \"20500\",
    \"destination_phone_1\": \"6147460925\",
    \"destination_phone_1_type\": \"Cell\",
    \"destination_phone_2\": \"6147660471\",
    \"destination_phone_2_type\": \"Home\",
    \"destination_country\": \"United States\",
    \"destination_description\": \"The White House\",
    \"pack_date\": \"2015-12-01\",
    \"pack_to_date\": \"2015-12-01\",
    \"preferred_pack_date\": \"2015-12-01\",
    \"load_date\": \"2015-12-01\",
    \"load_to_date\": \"2015-12-01\",
    \"requested_move_date\": \"2015-12-01\",
    \"deliver_date\": \"2015-12-01\",
    \"deliver_to_date\": \"2015-12-01\",
    \"expected_delivery_date\": \"2015-12-01\",
    \"survey_date\": \"2015-12-01\",
    \"survey_time\": \"12:00:00\",
    \"follow_up_date\": \"2015-12-01\",
    \"decision_date\": \"2015-12-01\",
    \"days_to_move\": \"12\",

    \"extra_stops\": [
        {
            \"name\": \"Test Stop One\",
            \"sequence\": \"1\",
            \"weight\": \"2200\",
            \"is_primary\": \"1\",
            \"address_1\": \"5524 Mesa Ridge Ln\",
            \"address_2\": \"\",
            \"city\": \"Columbus\",
            \"state\": \"OH\",
            \"zip\": \"43231\",
            \"country\": \"US\",
            \"phone_1\": \"6147660471\",
            \"phone_1_type\": \"Home\",
            \"phone_2\": \"6147460925\",
            \"phone_2_type\": \"Cell\",
            \"date\": \"2015-12-01\",
            \"contact_name\": \"Alex Smith\",
            \"contact_email\": \"asmith@igcsoftware.com\",
            \"stop_type\": \"Origin\"
        },
        {
            \"name\": \"Test Stop Two\",
            \"sequence\": \"2\",
            \"weight\": \"2200\",
            \"is_primary\": \"1\",
            \"address_1\": \"113 W. Oakland Ave\",
            \"address_2\": \"\",
            \"city\": \"Columbus\",
            \"state\": \"OH\",
            \"zip\": \"43210\",
            \"country\": \"US\",
            \"phone_1\": \"6147660471\",
            \"phone_1_type\": \"Home\",
            \"phone_2\": \"6147460925\",
            \"phone_2_type\": \"Cell\",
            \"date\": \"2015-12-01\",
            \"contact_name\": \"Alex Smith\",
            \"contact_email\": \"asmith@igcsoftware.com\",
            \"stop_type\": \"Origin\"
        }
    ]
}
";
    $post_string = "mode=CreateMilitaryOpportunity&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for CreateMilitaryOpportunity : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('UpdateLead', $thingsToTest)) {
    //testing UpdateLead
    $element     = "{
    \"Id\": \"10x381\",
    \"OriginAddress1\": \"5524 Mesa Ridge Ln\",
    \"OriginAddress2\": \"\",
    \"OriginCity\": \"Columbus\",
    \"OriginState\": \"OH\",
    \"OriginZip\": \"43231\",
    \"OriginCountry\": \"US\",
    \"OwnCurrent\": \"Yes\",
    \"DestinationAddress1\": \"1600 Pennslyvania Ave NW\",
    \"DestinationAddress2\": \"\",
    \"DestinationCity\": \"Washington\",
    \"DestinationState\": \"DC\",
    \"DestinationZip\": \"20500\",
    \"DestinationCountry\": \"US\",
    \"OwnNew\": \"Yes\",
    \"ListOfLeadNote\": [
        {
            \"Provider\": \"Testing this\",
            \"NoteSource\": \"\",
            \"CreatedBy\": \"\",
            \"DateTime\": \"10/15/2015 16:00:00\",
            \"Note\": \"This is a new comment\"
        }
    ]
}
";
    $post_string = "mode=UpdateLead&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for UpdateLead : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('CreateLead', $thingsToTest)) {
    //testing CreateLead
    $element     = '{"LeadId":null,"LMPLeadId":"4426023","CCDisposition":"Assigned","AgentDisposition":"New","MoveType":"Interstate","Brand":"NAVL","LMPAssignedAgentOrgId":"1954000","AssignedToPositionId":"","AMCSalesPersonId":"3","SalesPersonUsername":"","FirstName":"Jill","LastName":"QtestLMP","Organization":"","EmailAddress":"jquinte258@258gmail.com","PrimaryPhoneType":"Cell Phone","HomePhone":"","WorkPhone":"","WorkPhoneExt":"","CellularPhone":"6302208000","FaxPhone":"","TimeZone":"CST","PreferTime":null,"Language":"English","DwellingType":"MysgQmVkcm9vbSBBcHQu","CorporateLead":"Y","OriginAddress1":"TBD","OriginAddress2":null,"OriginCity":"CHICAGO","OriginState":"IL","OriginZip":"60601","OriginCountry":"US","OwnCurrent":"No","DestinationAddress1":"WA","DestinationAddress2":null,"DestinationCity":"FARGO","DestinationState":"ND","DestinationZip":"58104","DestinationCountry":"US","OwnNew":"No","DateFlexible":"N","LeadCreatedDate":"8/23/2016 10:00:31 AM","LeadReceiveDate":"8/23/2016 10:00:31 AM","ExpectedDeliveryDate":null,"RequiredMoveDate":"9/28/2016","DaysToMove":"","FulfillmentDate":"","Funded":"CORP LMP","AAProgramName":"NAVL SEO","AASourceName":"NAVL SEO","MarketingChannel":"Interactive","OfferNumber":"","PromotionTerms":"","SpecialItems":null,"BusinessChannel":"","EmployerAssistingFlg":"N","EmployerCompanyName":null,"EmployerContactName":"","EmployerContactEmail":"","EmployerContactPhone":"","FurnishLevel":"Medium","MovingVehicleFlg":"N","NumberOfVehicles":"","VehicleYear":"","VehicleMake":"","VehicleModel":"","OfferValuationFlg":"N","OutofOriginFlg":"N","OutofTimeFlg":"N","OfficeandIndustrialFlg":"N","SmallMoveFlg":"N","OutofAreaFlg":"N","SIRVAExpectsPhEstimateFlg":"N","Comments":null,"Surveyor":"","SurveyAppointmentPlanned":"","SurveyAppointmentDuration":"","ListOfLeadNote":[{"Provider":"SIRVA","NoteSource":"Jill Quinte","CreatedBy":"Call Center Manager","DateTime":"08/23/2016 10:02:30","Note":"Moving product reassigned from Agent 2028000 to Agent: 1954000"},{"Provider":"SIRVA","NoteSource":"Jill Quinte","CreatedBy":"Call Center Manager","DateTime":"08/23/2016 10:13:54","Note":"CAS Failure Reason set to - Appt not accepted at this time"},{"Provider":"SIRVA","NoteSource":"Jill Quinte","CreatedBy":"Call Center Manager","DateTime":"08/23/2016 10:14:15","Note":"Finish Lead"},{"Provider":"SIRVA","NoteSource":"Jill Quinte","CreatedBy":"Call Center Manager","DateTime":"08/23/2016 10:14:16","Note":"Automated Mails sent successfully."},{"Provider":"SIRVA","NoteSource":"Jill Quinte","CreatedBy":"Call Center Manager","DateTime":"08/23/2016 10:14:16","Note":"Lead sent to QIO2."}]}';
    $post_string = "mode=CreateLead&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for CreateLead : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('CreateLeadSource', $thingsToTest)) {
    //testing CreateLead
    //    $element     = "{
    //    \"Brand\": \"AVL\",
    //    \"LMPAssignedAgentOrgId\": \"22221111\",
    //    \"AAProgramName\": \"1 Lead Source a  3\",
    //    \"AAProgramTerms\": \"Test terms\",
    //    \"AASourceName\": \"Test Name a new\",
    //    \"AASourceType\": \"Agent Sourced QLAB\",
    //    \"LMPProgramId\": \"888856789\",
    //    \"LMPSourceId\": \"123456789\",
    //    \"MarketingChannel\": \"Telephone\",
    //    \"LeadSourceActive\": \"0\"
    //    } ";
    $element = "{
    \"Brand\": \"AVL\",
    \"LMPAssignedAgentOrgId\": \"22221111\",
    \"AAProgramName\": \"Mogs 3\",
    \"AASourceName\": \"Dogs t 3\",
    \"AASourceType\": \"Agent Sourced QLAB\",
    \"MarketingChannel\": \"MC 2\",
    \"LeadSourceActive\": \"on\"
    } ";
    $post_string = "mode=CreateLeadSource&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for CreateLeadSource : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('RetrieveLeadSource', $thingsToTest)) {
    //testing retrieveLeadSource
    $element     = json_encode(['LeadSrcId' => '73x1026']);
    $post_string = "mode=RetrieveLeadSource&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for RetrieveLead : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br><b> result : </b>".$curlResult."<br />";
}
if (in_array('getlocaltariffs', $thingsToTest)) {
    //testing getlocaltariffs
    $element     = json_encode(['agentid' => '39x17']);
    $post_string = "mode=getlocaltariffs&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for getlocaltariffs : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $json = json_encode(json_decode($curlResult), JSON_PRETTY_PRINT);
    echo "<br>".$curlResult."<br />";
}
if (in_array('createestimate', $thingsToTest)) {
    //testing create estimate
    $element = '{"estimate_upload": {
   "agent_code": "99",
   "potential_id": "48",
   "contact_id": "52",
   "account_id": "51",
   "order_id": "",
   "dates": {
     "pack_from": "08/19/2015",
     "pack_to": "08/19/2015",
     "load_from": "08/19/2015",
     "load_to": "08/19/2015",
     "deliver_from": "08/19/2015",
     "deliver_to": "08/19/2015",
     "survey": "08/19/2015",
     "follow_up": "08/19/2015",
     "decision": "08/19/2015",
     "pack_requested": "08/19/2015",
     "load_requested": "08/19/2015",
     "deliver_requested": "08/19/2015"
   },
   "is_primary": "false",
   "self_haul": "false",
   "business_line": "Local",
   "billing_info": {
     "charge_approved": "false",
     "address": "Sample",
     "po_box": "",
     "city": "Columbus",
     "state": "OH",
     "zip": "43202",
     "country": "US"
   },
   "origin_info": {
     "country": "US",
     "address1": "6432 E Main St.",
     "address2": "",
     "city": "Reynoldsburg",
     "state": "OH",
     "zip": "43068",
     "phone1": "",
     "phone2": ""
   },
   "dest_info": {
     "country": "US",
     "address1": "6432 E Main St.",
     "address2": "",
     "city": "Reynoldsburg",
     "state": "OH",
     "zip": "43068",
     "phone1": "",
     "phone2": ""
   },
   "cube_sheet": {
     "mode": "Road",
     "weight_factor": "7"
   },
   "dynamic_local_data": {
     "tariff_id": "18",
     "effective_date_id": "21",
     "effective_date": "2015-08-10",
     "bottom_line_discount": "1.25",
     "estimate": {
       "sections": {
         "section": [
           {
             "section_name": "Section 1",
             "section_id": "19",
             "section_discount": "0.0",
             "services": {
               "service": [
                 {
                   "service_name": "Base Plus Trans.",
                   "rate_type": "Base Plus Trans.",
                   "service_id": "22",
                   "miles": "10",
                   "rate": "10.00",
                   "weight": "10",
                   "excess": "20.00"
                 },
                 {
                   "service_name": "Break Point Trans.",
                   "rate_type": "Break Point Trans.",
                   "service_id": "23",
                   "miles": "10",
                   "rate": "10.00",
                   "weight": "10",
                   "calcweight": "10"
                 },
                 {
                   "service_name": "Weight/Mileage Trans.",
                   "rate_type": "Weight/Mileage Trans.",
                   "service_id": "24",
                   "miles": "10",
                   "weight": "10",
                   "rate": "10.00"
                 },
                 {
                   "service_name": "Crating",
                   "rate_type": "Crating Item",
                   "service_id": "58",
                   "crates": {
                     "crate": {
                       "ID": "C-1",
                       "line_item_id": "1",
                       "description": "Ten",
                       "length": "10",
                       "width": "10",
                       "height": "10",
                       "inches_added": "10",
                       "crating_qty": "10",
                       "crating_rate": "10.000",
                       "uncrating_qty": "10",
                       "uncrating_rate": "10.000"
                     }
                   }
                 },
                 {
                   "service_name": "Valuation",
                   "rate_type": "Tabled Valuation",
                   "service_id": "60",
                   "valuationtype": "Select an Option"
                 },
                 {
                   "service_name": "Packing",
                   "rate_type": "Packing Items",
                   "service_id": "165",
                   "packing_items": {
                     "packing_item": [
                       {
                         "name": "1.5 cf",
                         "container_qty": "10",
                         "container_rate": "10.00",
                         "pack_qty": "10",
                         "pack_rate": "10.00",
                         "unpack_qty": "10",
                         "unpack_rate": "10.00",
                         "line_item_id": "173"
                       },
                       {
                         "name": "3.0 cf",
                         "container_qty": "10",
                         "container_rate": "10.00",
                         "pack_qty": "10",
                         "pack_rate": "10.00",
                         "unpack_qty": "10",
                         "unpack_rate": "10.00",
                         "line_item_id": "174"
                       },
                       {
                         "name": "4.5 cf",
                         "container_qty": "10",
                         "container_rate": "10.00",
                         "pack_qty": "10",
                         "pack_rate": "10.00",
                         "unpack_qty": "10",
                         "unpack_rate": "10.00",
                         "line_item_id": "175"
                       },
                       {
                         "name": "6.0 cf",
                         "container_qty": "0",
                         "container_rate": "0.00",
                         "pack_rate": "0.00",
                         "unpack_rate": "0.00",
                         "line_item_id": "176"
                       },
                       {
                         "name": "6.5 cf",
                         "container_qty": "0",
                         "container_rate": "6.23",
                         "pack_rate": "2.51",
                         "unpack_rate": "7.00",
                         "line_item_id": "177"
                       },
                       {
                         "name": "Crib",
                         "container_qty": "0",
                         "container_rate": "0.00",
                         "pack_rate": "0.00",
                         "unpack_rate": "0.00",
                         "line_item_id": "178"
                       },
                       {
                         "name": "Dish Pack",
                         "container_qty": "0",
                         "container_rate": "0.00",
                         "pack_rate": "0.00",
                         "unpack_rate": "0.00",
                         "line_item_id": "179"
                       },
                       {
                         "name": "Double",
                         "container_qty": "0",
                         "container_rate": "0.00",
                         "pack_rate": "0.00",
                         "unpack_rate": "0.00",
                         "line_item_id": "180"
                       },
                       {
                         "name": "Flat Screen TV",
                         "container_qty": "0",
                         "container_rate": "0.00",
                         "pack_rate": "0.00",
                         "unpack_rate": "0.00",
                         "line_item_id": "181"
                       },
                       {
                         "name": "Heavy Duty",
                         "container_qty": "0",
                         "container_rate": "0.00",
                         "pack_rate": "0.00",
                         "unpack_rate": "0.00",
                         "line_item_id": "182"
                       },
                       {
                         "name": "Mirror",
                         "container_qty": "0",
                         "container_rate": "0.00",
                         "pack_rate": "0.00",
                         "unpack_rate": "0.00",
                         "line_item_id": "183"
                       },
                       {
                         "name": "Other",
                         "container_qty": "0",
                         "container_rate": "0.00",
                         "pack_rate": "0.00",
                         "unpack_rate": "0.00",
                         "line_item_id": "184"
                       },
                       {
                         "name": "Queen/King",
                         "container_qty": "0",
                         "container_rate": "0.00",
                         "pack_rate": "0.00",
                         "unpack_rate": "0.00",
                         "line_item_id": "185"
                       },
                       {
                         "name": "Twin/Long",
                         "container_qty": "0",
                         "container_rate": "0.00",
                         "pack_rate": "0.00",
                         "unpack_rate": "0.00",
                         "line_item_id": "186"
                       },
                       {
                         "name": "Wardrobe",
                         "container_qty": "0",
                         "container_rate": "0.00",
                         "pack_rate": "0.00",
                         "unpack_rate": "0.00",
                         "line_item_id": "187"
                       }
                     ]
                   }
                 },
                 {
                   "service_name": "Bulky",
                   "rate_type": "Bulky List",
                   "service_id": "200",
                   "bulky_items": {
                     "bulky": [
                       {
                         "description": "Whirlpool Bath &gt; 65 Cu Ft",
                         "qty": "10",
                         "weight_add": "10",
                         "rate": "10.00",
                         "line_item_id": "0000"
                       },
                       {
                         "description": "Windsurfer",
                         "qty": "10",
                         "weight_add": "10",
                         "rate": "10.00",
                         "line_item_id": "0000"
                       },
                       {
                         "description": "Windsurfer &gt; 14 Ft",
                         "qty": "10",
                         "weight_add": "10",
                         "rate": "10.00",
                         "line_item_id": "0000"
                       }
                     ]
                   }
                 }
               ]
             }
           },
           {
             "section_name": "Section 2",
             "section_id": "20",
             "section_discount": "0",
             "services": {
               "service": [
                 {
                   "service_name": "County Charge",
                   "rate_type": "County Charge",
                   "service_id": "27",
                   "county": "Wood County",
                   "rate": "10.00"
                 },
                 {
                   "service_name": "Flat Charge",
                   "rate_type": "Flat Charge",
                   "service_id": "29",
                   "rate": "10.00"
                 },
                 {
                   "service_name": "Hourly Simple",
                   "rate_type": "Hourly Simple",
                   "service_id": "31",
                   "quantity": "10",
                   "hours": "10.00",
                   "rate": "10.00"
                 },
                 {
                   "service_name": "Per Cu Ft/Per Day",
                   "rate_type": "Per Cu Ft/Per Day",
                   "service_id": "33",
                   "cubic_feet": "10.00",
                   "days": "10",
                   "rate": "10.00"
                 },
                 {
                   "service_name": "Per CWT",
                   "rate_type": "Per CWT",
                   "service_id": "35",
                   "weight": "10",
                   "rate": "10.00"
                 },
                 {
                   "service_name": "Per CWT/Per Day",
                   "rate_type": "Per CWT/Per Day",
                   "service_id": "36",
                   "weight": "10",
                   "days": "10",
                   "rate": "10"
                 },
                 {
                   "service_name": "Per Quantity",
                   "rate_type": "Per Quantity",
                   "service_id": "38",
                   "quantity": "10",
                   "rate": "10.00"
                 },
                 {
                   "service_name": "Per Quanity/Per Day",
                   "rate_type": "Per Quantity/Per Day",
                   "service_id": "39",
                   "quantity": "10",
                   "days": "10",
                   "rate": "10.00"
                 },
                 {
                   "service_name": "Hourly Set",
                   "rate_type": "Hourly Set",
                   "service_id": "198",
                   "men": "10",
                   "hours": "10.00",
                   "vans": "10",
                   "travel_time": "10.00",
                   "rate": "10.00"
                 }
               ]
             }
           }
         ]
       }
     }
   },
   "user_terms": { "print_terms": "false" }
 }
}';
    $element = json_decode($element, true);
    $element = json_encode($element, JSON_HEX_AMP | JSON_HEX_TAG);
    $element = str_replace('&', '&amp;', $element);
    $element = str_replace('<', '&lt;', $element);
    $element = str_replace('>', '&gt;', $element);
    $element = stripslashes($element);
    echo $element;
    $post_string = "mode=createestimate&sessionName=$sessionId&element=$element";
    echo "<br><b>post string for createestimate : </b>".$post_string;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    echo "<br>".$curlResult."<br />";
}
if (in_array('getimage', $thingsToTest)) {
    //testing getimage
    $element     = json_encode(['userid' => '19x1']);
    $post_string = "mode=getimage&sessionName=$sessionId&element=$element";
    $ch          = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    echo $curlResult."<br />";
    $curlJSON = json_decode($curlResult, true);
    file_put_contents('test.jpg', base64_decode($curlJSON['result']));
}
if (in_array('getwalks', $thingsToTest)) {
    //testing getwalks
    $post_string = "mode=getwalks&element=$element";
    $ch          = curl_init();
    curl_setopt($ch, CURLOPT_URL, $webserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    echo $curlResult."<br />";
    $curlJSON = json_decode($curlResult, true);
    $walkid   = $curlJSON['result']['walks'][4];
    echo $walkid."<br />";
    $element     = json_encode(['accesskey' => $accesskey]);
    $post_string = "mode=getnextwalk&sessionName=$sessionId&element=$element";
    echo "<br />$syncserviceURL?$post_string<br />";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    echo $curlResult."<br />";
}
if (in_array('query', $thingsToTest)) {
    //testing query
    $get_string = "?operation=query&query=SELECT+*+FROM+Users+WHERE+user_name%3d%27tbrame%27%3b&sessionName=".$sessionId;
    $ch         = curl_init();
    curl_setopt($ch, CURLOPT_URL, $webserviceURL.$get_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    $userInfo = json_decode($curlResult, true);
    echo "<br /> <br />";
    print_r($userInfo['result'][0]['gmms_deviceid']);
    $userInfo['result'][0]['gmms_deviceid'] = "15435";
    echo "<br />";
    $element     =
        json_encode($userInfo['result'][0]);//'{"address_city":"","address_country":"","address_postalcode":"","address_state":"","custom_reports_pw":"","dbx_token":"","department":"","email1":"tbrame@igcsoftware.com","email2":"","first_name":"Tony","gmms_deviceid":"123","is_owner":"0","last_name":"Brame","oi_push_notification_token":"","phone_crm_extension":"","phone_fax":"6143288231","phone_home":"","phone_mobile":"","phone_other":"","phone_work":"","push_notification_token":"","secondaryemail":"","title":"","user_name":"tbrame","user_smtp_server":"","user_smtp_fromemail":"","user_smtp_password":"","user_smtp_username":"","vanline":"","id":"19x24"}';
    $post_string = "operation=update&sessionName=$sessionId&element=$element";
    $ch          = curl_init();
    curl_setopt($ch, CURLOPT_URL, $webserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    echo $curlResult."<br />";
}
if (in_array('getadmin', $thingsToTest)) {
    //testing get admin
    $post_string = 'mode=getadmin&element={"appKey":"FFAe9MVWMXVQDvfM8QJB"}';
    $ch          = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $curlResult = curl_exec($ch);
    curl_close($ch);
    echo "<br /> <br />".$curlResult."<br />";
}
if (in_array('geteventsbyid', $thingsToTest)) {
    $element     = json_encode(['RecordId' => '46x5531', 'ChangedAfter' => '2016-04-19 00:00:00']);
    $post_string = "mode=geteventsbyid&sessionName=$sessionId&element=$element";
    echo "<br />$post_string<br />";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    $curlResult = curl_exec($ch);
    curl_close($ch);
    echo "<br /> <br />".$curlResult."<br />";
}
if (in_array('checkrecordfordeletion', $thingsToTest)) {
    $element     = json_encode(['RecordId' => '46x32258']);
    $post_string = "mode=checkrecordfordeletion&sessionName=$sessionId&element=$element";
    echo "<br />$post_string<br />";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    $curlResult = curl_exec($ch);
    curl_close($ch);
    echo "<br /> <br />".$curlResult."<br />";
}
if (in_array('migrateDocumentsForOrders', $thingsToTest)) {
    $element = json_encode(['oldOrder' => 'HQ00014', 'newOrder' => 'HQ41573']);
    $post_string = "mode=migratedocumentsfororders&sessionName=$sessionId&element=$element";
    echo "<br />$post_string<br />";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $syncserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    $curlResult = curl_exec($ch);
    curl_close($ch);
    echo "<br /> <br />".$curlResult."<br />";
}
