<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
********************************************************************************/

require_once './load_phpdotenv.php';

try {
    (new \Dotenv\Dotenv(__DIR__))->load();
} catch (\InvalidArgumentException $e) {
}

//version_compare(PHP_VERSION, '5.5.0') <= 0 ? error_reporting(E_WARNING & ~E_NOTICE & ~E_DEPRECATED) : error_reporting(E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT); // PRODUCTION
//Please set the DISPLAY_ERRORS flag in the .env file to 1 if you need to turn on errors.
if (getenv('DISPLAY_ERRORS')) {
    ini_set('display_errors', 'on');
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
} else {
    ini_set('display_errors', 'off');
}
//error_reporting(E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR);

include('vtigerversion.php');

// more than 8MB memory needed for graphics
// memory limit default value = 64M
ini_set('memory_limit', '512M');

// show or hide calendar, world clock, calculator, chat and CKEditor
// Do NOT remove the quotes if you set these to false!
$CALENDAR_DISPLAY = 'true';
$WORLD_CLOCK_DISPLAY = 'true';
$CALCULATOR_DISPLAY = 'true';
$CHAT_DISPLAY = 'true';
$USE_RTE = 'true';

// helpdesk support email id and support name (Example: 'support@domain.com' and 'IGC support')
$HELPDESK_SUPPORT_EMAIL_ID = getenv('SUPPORT_EMAIL_ADDRESS');
$HELPDESK_SUPPORT_NAME = (getenv('IGC_MOVEHQ') ? 'moveHQ' : 'moveCRM').' Support';
$HELPDESK_SUPPORT_EMAIL_REPLY_ID = $HELPDESK_SUPPORT_EMAIL_ID;

$dbconfig['db_server']   = getenv('DB_SERVER');
$dbconfig['db_port']     = sprintf(':%u', getenv('DB_PORT'));
$dbconfig['db_username'] = getenv('DB_USERNAME');
$dbconfig['db_password'] = getenv('DB_PASSWORD');
$dbconfig['db_name']     = getenv('DB_NAME');
$dbconfig['db_type']     = getenv('DB_TYPE');
$dbconfig['db_status']   = 'true';

// TODO: test if port is empty
// TODO: set db_hostname dependending on db_type
$dbconfig['db_hostname'] = $dbconfig['db_server'].$dbconfig['db_port'];

// log_sql default value = false
$dbconfig['log_sql'] = false;

// persistent default value = true
$dbconfigoption['persistent'] = true;

// autofree default value = false
$dbconfigoption['autofree'] = false;

// debug default value = 0
$dbconfigoption['debug'] = 0;

// seqname_format default value = '%s_seq'
$dbconfigoption['seqname_format'] = '%s_seq';

// portability default value = 0
$dbconfigoption['portability'] = 0;

// ssl default value = false
$dbconfigoption['ssl'] = false;

$host_name = $dbconfig['db_hostname'];

if (!getenv('SITE_URL')) {
    $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['SCRIPT_NAME']);

    putenv("SITE_URL=${scheme}${host}${path}");
}

if (!getenv('WEBSERVICE_URL')) {
    $webservice_url = getenv('SITE_URL') . '/webservice.php';

    putenv("WEBSERVICE_URL=${webservice_url}");
}

$site_URL = getenv('SITE_URL');

// URL for customer portal (Example: http://domain.com/portal)
$PORTAL_URL = $site_URL . '/portal';

// root directory path
$root_directory = realpath(__DIR__) . '/';

// cache direcory path
$cache_dir = 'cache/';

// tmp_dir default value prepended by cache_dir = images/
$tmp_dir = 'cache/images/';

// import_dir default value prepended by cache_dir = import/
$import_dir = 'cache/import/';

// upload_dir default value prepended by cache_dir = upload/
$upload_dir = 'cache/upload/';

// maximum file size for uploaded files in bytes also used when uploading import files
// upload_maxsize default value = 3000000
$upload_maxsize = ini_get('upload_max_filesize');
if ($upload_maxsize) {
    //only translate it IF there is a letter at the end.
    if (preg_match('/[^0-9]$/', $upload_maxsize)) {
        $upload_maxsize = trim($upload_maxsize);
        $last = strtolower($upload_maxsize[strlen($upload_maxsize)-1]);
        switch ($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $upload_maxsize *= 1024;
            case 'm':
                $upload_maxsize *= 1024;
            case 'k':
                $upload_maxsize *= 1024;
        }
    }
}

if ($upload_maxsize < 3000000) {
    $upload_maxsize = 3000000;
}
if (getenv('INSTANCE_NAME') == 'graebel') {
    $upload_maxsize = 100*1024*1024;
}
ini_set('upload_max_filesize', $upload_maxsize);
ini_set('post_max_size', $upload_maxsize + ini_get('post_max_size'));
$GLOBALS['upload_maxsize'] = $upload_maxsize;

// flag to allow export functionality
// 'all' to allow anyone to use exports
// 'admin' to only allow admins to export
// 'none' to block exports completely
// allow_exports default value = all
$allow_exports = 'all';

// files with one of these extensions will have '.txt' appended to their filename on upload
// upload_badext default value = php, php3, php4, php5, pl, cgi, py, asp, cfm, js, vbs, html, htm
$upload_badext = array('php', 'php3', 'php4', 'php5', 'pl', 'cgi', 'py', 'asp', 'cfm', 'js', 'vbs', 'html', 'htm', 'exe', 'bin', 'bat', 'sh', 'dll', 'phps', 'phtml', 'xhtml', 'rb', 'msi', 'jsp', 'shtml', 'sth', 'shtm');

// full path to include directory including the trailing slash
// includeDirectory default value = $root_directory..'include/
$includeDirectory = $root_directory.'include/';

// list_max_entries_per_page default value = 20
$list_max_entries_per_page = '20';

// limitpage_navigation default value = 5
$limitpage_navigation = '5';

// history_max_viewed default value = 5
$history_max_viewed = '5';

// default_module default value = Home
$default_module = 'Home';

// default_action default value = index
$default_action = 'index';

// set default theme
// default_theme default value = blue
$default_theme = 'bluelagoon';

// show or hide time to compose each page
// calculate_response_time default value = true
$calculate_response_time = true;

// default text that is placed initially in the login form for user name
// no default_user_name default value
$default_user_name = '';

// default text that is placed initially in the login form for password
// no default_password default value
$default_password = '';

// create user with default username and password
// create_default_user default value = false
$create_default_user = false;
// default_user_is_admin default value = false
$default_user_is_admin = false;

// if your MySQL/PHP configuration does not support persistent connections set this to true to avoid a large performance slowdown
// disable_persistent_connections default value = false
$disable_persistent_connections = false;

//Master currency name
$currency_name = 'USA, Dollars';

// default charset
// default charset default value = 'UTF-8' or 'ISO-8859-1'
$default_charset = 'UTF-8';

// default language
// default_language default value = en_us
$default_language = 'en_us';

// add the language pack name to every translation string in the display.
// translation_string_prefix default value = false
$translation_string_prefix = false;

//Option to cache tabs permissions for speed.
$cache_tab_perms = true;

//Option to hide empty home blocks if no entries.
$display_empty_home_blocks = false;

//Disable Stat Tracking of moveCRM instance
$disable_stats_tracking = false;

// Generating Unique Application Key
$application_unique_key = 'bbf894915974579d88e19f4f83ac4d86';

// trim descriptions, titles in listviews to this value
$listview_max_textlength = 40;

// Maximum time limit for PHP script execution (in seconds)
$php_max_execution_time = 0;

// Set the default timezone as per your preference
$default_timezone = 'UTC';

/** If timezone is configured, try to set it */
if (isset($default_timezone) && function_exists('date_default_timezone_set')) {
    @date_default_timezone_set($default_timezone);
}

/** List of blocks to hide for a given module and business line option **/

$hiddenBlocksArray = array(
    'Contacts'       => require './config/hidden_blocks/contacts.php',
    'Employees'      => require './config/hidden_blocks/employees.php',
    'Estimates'      => require './config/hidden_blocks/estimates.php',
    'Actuals'         => require './config/hidden_blocks/actuals.php',
    'Leads'          => require './config/hidden_blocks/leads.php',
    'ClaimItems'     => require './config/hidden_blocks/claimItems.php',
    'Opportunities'  => array(
        'Local'=>'LBL_POTENTIALS_LOCALMOVEDETAILS::LBL_POTENTIALS_ADDRESSDETAILS::LBL_POTENTIALS_DATES::LBL_OPPORTUNITIES_HHG_INFORMATION::participatingAgentsTable::ExtraStopsTable::MoveRolesTable',
        'Local Move'=>'LBL_POTENTIALS_LOCALMOVEDETAILS::LBL_POTENTIALS_ADDRESSDETAILS::LBL_POTENTIALS_DATES::LBL_OPPORTUNITIES_HHG_INFORMATION::participatingAgentsTable::ExtraStopsTable::MoveRolesTable',
        'Interstate'=>'LBL_POTENTIALS_INTERSTATEMOVEDETAILS::LBL_POTENTIALS_ADDRESSDETAILS::LBL_POTENTIALS_DATES::LBL_OPPORTUNITIES_HHG_INFORMATION::participatingAgentsTable::ExtraStopsTable::MoveRolesTable',
        'Interstate Move'=>'LBL_POTENTIALS_INTERSTATEMOVEDETAILS::LBL_POTENTIALS_ADDRESSDETAILS::LBL_POTENTIALS_DATES::LBL_OPPORTUNITIES_HHG_INFORMATION::participatingAgentsTable::ExtraStopsTable::MoveRolesTable',
        'Intrastate'=>'LBL_POTENTIALS_INTERSTATEMOVEDETAILS::LBL_POTENTIALS_ADDRESSDETAILS::LBL_POTENTIALS_DATES::LBL_OPPORTUNITIES_HHG_INFORMATION::participatingAgentsTable::ExtraStopsTable::MoveRolesTable',
        'Intrastate Move'=>'LBL_POTENTIALS_INTERSTATEMOVEDETAILS::LBL_POTENTIALS_ADDRESSDETAILS::LBL_POTENTIALS_DATES::LBL_OPPORTUNITIES_HHG_INFORMATION::participatingAgentsTable::ExtraStopsTable::MoveRolesTable',
        'Commercial'=>'LBL_POTENTIALS_COMMERCIALMOVEDETAILS::LBL_POTENTIALS_ADDRESSDETAILS::LBL_POTENTIALS_DATES::LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION::MoveRolesTable::ExtraStopsTable',
        'Commercial Move'=>'LBL_POTENTIALS_COMMERCIALMOVEDETAILS::LBL_POTENTIALS_ADDRESSDETAILS::LBL_POTENTIALS_DATES::LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION::MoveRolesTable::ExtraStopsTable',
        'National Account'=>'LBL_POTENTIALS_NATIONALACCOUNT::LBL_POTENTIALS_DATES::LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION',
        'Auto'=>'vehicleLookupTable::LBL_POTENTIALS_ADDRESSDETAILS::LBL_POTENTIALS_DATES::ExtraStopsTable',
        'Auto Transportation'=>'vehicleLookupTable::LBL_POTENTIALS_ADDRESSDETAILS::LBL_POTENTIALS_DATES::ExtraStopsTable'
    ),
    'Potentials'     => array(
        'Local'=>'LBL_POTENTIALS_LOCALMOVEDETAILS',
        'Interstate'=>'LBL_POTENTIALS_INTERSTATEMOVEDETAILS',
        'Intrastate'=>'LBL_POTENTIALS_INTERSTATEMOVEDETAILS',
        'Commercial'=>'LBL_POTENTIALS_COMMERCIALMOVEDETAILS',
        'Local Move'=>'LBL_POTENTIALS_LOCALMOVEDETAILS',
        'Interstate Move'=>'LBL_POTENTIALS_INTERSTATEMOVEDETAILS',
        'Intrastate Move'=>'LBL_POTENTIALS_INTERSTATEMOVEDETAILS',
        'Commercial Move'=>'LBL_POTENTIALS_COMMERCIALMOVEDETAILS'
    ),
    'Quotes'         => array(
        'Local'=>'LBL_QUOTES_LOCALMOVEDETAILS',
        'Interstate'=>'LBL_QUOTES_INTERSTATEMOVEDETAILS',
        'Intrastate'=>'LBL_QUOTES_INTERSTATEMOVEDETAILS',
        'Commercial'=>'LBL_QUOTES_COMMERCIALMOVEDETAILS',
        'Local Move'=>'LBL_QUOTES_LOCALMOVEDETAILS',
        'Interstate Move'=>'LBL_QUOTES_INTERSTATEMOVEDETAILS',
        'Intrastate Move'=>'LBL_QUOTES_INTERSTATEMOVEDETAILS',
        'Commercial Move'=>'LBL_QUOTES_COMMERCIALMOVEDETAILS',
    ),
    'SalesOrder'     => array(
        'Business 1'=>'CustomBlockOption1',
        'Business 2'=>'CustomBlockOption2',
        'Business 3'=>'CustomBlockOption3::CustomBlockOption4',
        'Business4'=>'CustomBlockOption5'),

    'TariffServices' => array(
        //'Service Base Charge' => 'LBL_BASESERVICEAPPLIES',
        'Service Base Charge'=>'LBL_TARIFFSERVICES_SERVICECHARGE',
        'Base Plus Trans.'=>'LBL_TARIFFSERVICES_BASEPLUS',
        'Break Point Trans.'=>'LBL_TARIFFSERVICES_BREAKPOINT',
        'Weight/Mileage Trans.'=>'LBL_TARIFFSERVICES_WEIGHTMILEAGE',
        'Bulky List'=>'LBL_TARIFFSERVICES_BULKY',
        'Charge Per $100 (Valuation)'=>'LBL_TARIFFSERVICES_CHARGEPERHUNDRED',
        'County Charge'=>'LBL_TARIFFSERVICES_COUNTYCHARGE',
        'Crating Item'=>'LBL_TARIFFSERVICES_CRATINGITEM',
        'Flat Charge'=>'LBL_TARIFFSERVICES_FLATCHARGE',
        'Hourly Avg Lb/Man/Hour'=>'LBL_TARIFFSERVICES_HOURLYAVG',
        'Hourly Set'=>'LBL_TARIFFSERVICES_HOURLYSET',
        'Hourly Simple'=>'LBL_TARIFFSERVICES_HOURLYSIMPLE',
        'Packing Items'=>'LBL_TARIFFSERVICES_PACKING',
        'Per Cu Ft/Per Day'=>'LBL_TARIFFSERVICES_CUFTPERDAY',
        'Per Cu Ft/Per Month'=>'LBL_TARIFFSERVICES_CUFTPERMONTH',
        'Per CWT'=>'LBL_TARIFFSERVICES_CWT',
        'Per CWT/Per Day'=>'LBL_TARIFFSERVICES_CWTPERDAY',
        'Per CWT/Per Month'=>'LBL_TARIFFSERVICES_CWTPERMONTH',
        'Per Quantity'=>'LBL_TARIFFSERVICES_QTY',
        'Per Quantity/Per Day'=>'LBL_TARIFFSERVICES_QTYPERDAY',
        'Per Quantity/Per Month'=>'LBL_TARIFFSERVICES_QTYPERMONTH',
        'Tabled Valuation'=>'LBL_TARIFFSERVICES_VALUATION',
        'CWT by Weight' => 'LBL_TARIFFSERVICES_CWTBYWEIGHT',
        'SIT Cartage' => 'LBL_TARIFFSERVICES_CWTBYWEIGHT',
        'SIT First Day Rate' => 'LBL_TARIFFSERVICES_CWT',
        'SIT Additional Day Rate' => 'LBL_TARIFFSERVICES_CWTPERDAY',
        'Storage Valuation' => 'LBL_TARIFFSERVICES_SERVICECHARGE',
        'Per Cu Ft'=>'LBL_TARIFFSERVICES_CUFT',
        'SIT Item' => 'LBL_TARIFFSERVICES_SIT_ITEM',
        'CWT Per Quantity' => 'LBL_TARIFFSERVICES_CWTPERQTY',
        'Flat Rate By Weight' => 'LBL_TARIFFSERVICES_FLATRATEBYWEIGHT'
    ),
    'Orders'         =>    array('Auto Transportation'=>'vehicleLookupTable'),
);

if (getenv('INSTANCE_NAME') == 'sirva') {
    //copied Interstate for the Intrastate blocks to unhide the dates.
    //expanded these for readability.
    $hiddenBlocksArray['Leads'] = array(
        'Local Move'=>'LBL_LEADS_LOCALMOVEDETAILS::LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES',
        'Interstate Move'=>'LBL_LEADS_INTERSTATEMOVEDETAILS::LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES',
        'Intrastate Move'=>'LBL_LEADS_INTERSTATEMOVEDETAILS::LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES',
        'Commercial Move'=>'LBL_LEADS_COMMERCIALMOVEDETAILS::LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES',
        'Military'=>'LBL_LEADS_INTERSTATEMOVEDETAILS::LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES',
        'Sirva Military'=>'LBL_LEADS_INTERSTATEMOVEDETAILS::LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES',
        'International Move' => 'LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES'
    );
    $hiddenBlocksArray['Opportunities'] = array(
        'Local Move'=>'LBL_POTENTIALS_LOCALMOVEDETAILS::LBL_POTENTIALS_ADDRESSDETAILS::LBL_POTENTIALS_DATES',
        'Interstate Move'=>'LBL_POTENTIALS_INTERSTATEMOVEDETAILS::LBL_POTENTIALS_ADDRESSDETAILS::LBL_POTENTIALS_DATES',
        'Intrastate Move'=>'LBL_POTENTIALS_INTERSTATEMOVEDETAILS::LBL_POTENTIALS_ADDRESSDETAILS::LBL_POTENTIALS_DATES',
        'Commercial Move'=>'LBL_POTENTIALS_COMMERCIALMOVEDETAILS::LBL_POTENTIALS_ADDRESSDETAILS::LBL_POTENTIALS_DATES',
        'National Account'=>'LBL_POTENTIALS_NATIONALACCOUNT::LBL_POTENTIALS_DATES',
        'Auto Transportation'=>'vehicleLookupTable::LBL_POTENTIALS_ADDRESSDETAILS::LBL_POTENTIALS_DATES',
        'Military'  => 'LBL_POTENTIALS_INTERSTATEMOVEDETAILS::LBL_POTENTIALS_ADDRESSDETAILS::LBL_POTENTIALS_DATES',
        'Sirva Military'  => 'LBL_POTENTIALS_INTERSTATEMOVEDETAILS::LBL_POTENTIALS_ADDRESSDETAILS::LBL_POTENTIALS_DATES',
        'International Move' => 'LBL_POTENTIALS_ADDRESSDETAILS',
    );
    $hiddenBlocksArray['Potentials'] = array(
        'Local Move'=>'LBL_POTENTIALS_LOCALMOVEDETAILS',
        'Interstate Move'=>'LBL_POTENTIALS_INTERSTATEMOVEDETAILS',
        'Intrastate Move'=>'LBL_POTENTIALS_INTERSTATEMOVEDETAILS',
        'Commercial Move'=>'LBL_POTENTIALS_COMMERCIALMOVEDETAILS',
        'Military'  => 'LBL_QUOTES_INTERSTATEMOVEDETAILS',
        'Sirva Military'  => 'LBL_QUOTES_INTERSTATEMOVEDETAILS',
        'International Move' => 'LBL_POTENTIALS_ADDRESSDETAILS',
    );
    $hiddenBlocksArray['Quotes'] = array(
        'Local Move'=>'LBL_QUOTES_LOCALMOVEDETAILS',
        'Interstate Move'=>'LBL_QUOTES_INTERSTATEMOVEDETAILS::LBL_QUOTES_VALUATION',
        'Intrastate Move'=>'LBL_QUOTES_LOCALMOVEDETAILS',
        'Commercial Move'=>'LBL_QUOTES_COMMERCIALMOVEDETAILS',
        'Military'  => 'LBL_QUOTES_INTERSTATEMOVEDETAILS::LBL_QUOTES_VALUATION',
        'Sirva Military'  => 'LBL_QUOTES_INTERSTATEMOVEDETAILS::LBL_QUOTES_VALUATION'
    );
    $hiddenBlocksArray['Estimates'] = array(
        'Local Move'=>'LBL_QUOTES_LOCALMOVEDETAILS',
        'Interstate Move'=>'LBL_QUOTES_INTERSTATEMOVEDETAILS::LBL_QUOTES_VALUATION::LBL_QUOTES_INTERSTATE_SERVICECHARGES::LBL_QUOTES_TPGPRICELOCK',
        'Intrastate Move'=>'LBL_QUOTES_LOCALMOVEDETAILS',
        'Commercial Move'=>'LBL_QUOTES_COMMERCIALMOVEDETAILS',
        'Military'  => 'LBL_QUOTES_INTERSTATEMOVEDETAILS::LBL_QUOTES_VALUATION',
        'Sirva Military'  => 'LBL_QUOTES_INTERSTATEMOVEDETAILS::LBL_QUOTES_VALUATION',
    );
} elseif (getenv('INSTANCE_NAME') == 'graebel') {
    $hiddenBlocksArray['Opportunities']['National Account'] = 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION';
    $hiddenBlocksArray['Opportunities']['NOTHING'] = 'LBL_POTENTIALS_NATIONALACCOUNT';

    //@TODO: Resolve if thse can be condensed to "Commercial Move" "International Move" etc...
    $hiddenBlocksArray['Opportunities']['HHG - International Sea'] = 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION::MoveRolesTable';
    $hiddenBlocksArray['Opportunities']['Commercial - Distribution'] = 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION::MoveRolesTable';
    $hiddenBlocksArray['Opportunities']['Commercial - Record Storage'] = 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION::MoveRolesTable';
    $hiddenBlocksArray['Opportunities']['Commercial - Storage'] = 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION::MoveRolesTable';
    $hiddenBlocksArray['Opportunities']['Commercial - Asset Management'] = 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION::MoveRolesTable';
    $hiddenBlocksArray['Opportunities']['Work Space - MAC'] = 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION::MoveRolesTable';
    $hiddenBlocksArray['Opportunities']['Commercial - Project'] = 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION::MoveRolesTable';
    $hiddenBlocksArray['Opportunities']['Work Space - Special Services'] = 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION::MoveRolesTable';
    $hiddenBlocksArray['Opportunities']['Work Space - Commodities'] = 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION::MoveRolesTable';
} elseif (getenv('IGC_MOVEHQ')) {
    $hiddenBlocksArray['Opportunities']['National'] = 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION';
    $hiddenBlocksArray['Opportunities']['NOTHING'] = 'LBL_POTENTIALS_NATIONALACCOUNT';

    //@TODO: Resolve if thse can be condensed to "Commercial Move" "International Move" etc...
    $hiddenBlocksArray['Opportunities']['HHG - International Sea'] = 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION';
    $hiddenBlocksArray['Opportunities']['Commercial - Distribution'] = 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION';
    $hiddenBlocksArray['Opportunities']['Commercial - Record Storage'] = 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION';
    $hiddenBlocksArray['Opportunities']['Commercial - Storage'] = 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION';
    $hiddenBlocksArray['Opportunities']['Commercial - Asset Management'] = 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION';
    $hiddenBlocksArray['Opportunities']['Work Space - MAC'] = 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION';
    $hiddenBlocksArray['Opportunities']['Commercial - Project'] = 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION';
    $hiddenBlocksArray['Opportunities']['Work Space - Special Services'] = 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION';
    $hiddenBlocksArray['Opportunities']['Work Space - Commodities'] = 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION';
    $hiddenBlocksArray['Orders']['Interstate'] = 'LBL_LONGDISPATCH_INFO';
    $hiddenBlocksArray['Orders']['Intrastate'] = 'LBL_LONGDISPATCH_INFO';
    $hiddenBlocksArray['Orders']['International'] = 'LBL_LONGDISPATCH_INFO';
}




/** FieldName for each module where the Business Line is stored **/

$hiddenBlocksArrayField['Contacts']='cf_705';
$hiddenBlocksArrayField['Potentials']='business_line';
$hiddenBlocksArrayField['Opportunities']='business_line';
$hiddenBlocksArrayField['Estimates']='business_line_est';
$hiddenBlocksArrayField['Actuals']='business_line_est';
$hiddenBlocksArrayField['Quotes']='business_line';
$hiddenBlocksArrayField['SalesOrder']='cf_751';
$hiddenBlocksArrayField['TariffServices']='rate_type';
$hiddenBlocksArrayField['Contacts']='contact_type';
$hiddenBlocksArrayField['Employees']='employee_type';
$hiddenBlocksArrayField['Leads']='business_line';
$hiddenBlocksArrayField['Orders']='business_line';
$hiddenBlocksArrayField['ClaimItems']='claimitems_type';

define('_MPDF_TTFONTDATAPATH','test/mpdf/ttfontdata/');