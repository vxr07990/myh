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
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';

echo "<br><h1>Starting </h1><br>\n";
$db = PearDatabase::getInstance();
$module = Vtiger_Module::getInstance('Leads');
$field1 = Vtiger_Field::getInstance('origin_country', $module);
$field2 = Vtiger_Field::getInstance('destination_country', $module);

if ($field1 && $field2) {
    //Update vtiger_destination_country_seq ids
    echo '<h4>Setting  vtiger_destination_country_seq to 0</h4>';
    $sql = "UPDATE vtiger_destination_country_seq SET `id` = '0'";
    $query = $db->pquery($sql);
    if ($db->getAffectedRowCount($query)>0) {
        echo '<p>Set SEQ id = 0</p>';
    }

    //Update vtiger_origin_country_seq ids
    echo '<h4>Setting  vtiger_origin_country to 0</h4>';
    $sql = "UPDATE vtiger_origin_country_seq SET `id` = '0'";
    $query = $db->pquery($sql);
    if ($db->getAffectedRowCount($query)>0) {
        echo '<p>Set SEQ id = 0</p>';
    }

    // Delete all rows from the country table
    Vtiger_Utils::ExecuteQuery("TRUNCATE TABLE `vtiger_destination_country`");
    Vtiger_Utils::ExecuteQuery("TRUNCATE TABLE `vtiger_origin_country`");

    echo '<h4>Removing rows from vtiger_destination_country</h4>';


    $countries =
        ['Afghanistan', 'Albania', 'Algeria', 'American Samoa', 'Andorra', 'Angola', 'Anguilla', 'Antarctica',
         'Antigua and Barbuda', 'Argentina', 'Armenia', 'Aruba', 'Australia', 'Austria', 'Azerbaijan', 'Bahamas',
         'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bermuda', 'Bhutan',
         'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 'Bouvet Island', 'Brazil',
         'British Indian Ocean Territory', 'Brunei Darussalam', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cambodia',
         'Cameroon', 'Canada', 'Cape Verde', 'Cayman Islands', 'Central African Republic', 'Chad', 'Chile', 'China',
         'Christmas Island', 'Cocos (Keeling) Islands', 'Colombia', 'Comoros', 'Congo',
         'Congo, The Democratic Republic', 'Cook Islands', 'Costa Rica', 'Cote D\'ivoire', 'Croatia', 'Cuba',
         'Cyprus', 'Czech Republic', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'East Timor',
         'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Ethiopia',
         'Falkland Islands (Malvinas)', 'Faroe Islands', 'Fiji', 'Finland', 'France', 'France, Metropolitan',
         'French Guiana', 'French Polynesia', 'French Southern Territories', 'Gabon', 'Gambia', 'Georgia',
         'Germany', 'Ghana', 'Gibraltar', 'Greece', 'Greenland', 'Grenada', 'Guadeloupe', 'Guam', 'Guatemala',
         'Guinea', 'Guinea-bissau', 'Guyana', 'Haiti', 'Heard and Mc Donald Islands',
         'Holy See (Vatican City State)', 'Honduras', 'Hong Kong', 'Hungary', 'Iceland', 'India', 'Indonesia',
         'Iran (Islamic Republic of)', 'Iraq', 'Ireland', 'Israel', 'Italy', 'Jamaica', 'Japan', 'Jordan',
         'Kazakhstan', 'Kenya', 'Kiribati', 'Korea', 'Korea, Democratic People\'s Rep', 'Kuwait', 'Kyrgyzstan',
         'Lao People\'s Democratic Republ', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libyan Arab Jamahiriya',
         'Liechtenstein', 'Lithuania', 'Luxembourg', 'Macau', 'Macedonia, The Former Yugoslav', 'Madagascar',
         'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Martinique', 'Mauritania',
         'Mauritius', 'Mayotte', 'Mexico', 'Micronesia, Federated States o', 'Moldova, Republic of', 'Monaco',
         'Mongolia', 'Montserrat', 'Morocco', 'Mozambique', 'Myanmar', 'Namibia', 'Nauru', 'Nepal', 'Netherlands',
         'Netherlands Antilles', 'New Caledonia', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'Niue',
         'Norfolk Island', 'Northern Mariana Islands', 'Norway', 'Oman', 'Pakistan', 'Palau', 'Panama',
         'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Pitcairn', 'Poland', 'Portugal', 'Puerto Rico',
         'Qatar', 'Reunion', 'Romania', 'Russia', 'Russian Federation', 'Rwanda', 'Saint Kitts and Nevis',
         'Saint Lucia', 'Saint Vincent and the Grenadin', 'Samoa', 'San Marino', 'Sao Tome and Principe',
         'Saudi Arabia', 'Senegal', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovakia (Slovak Republic)',
         'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Georgia and the South Sa', 'Spain',
         'Sri Lanka', 'St. Helena', 'St. Pierre and Miquelon', 'Sudan', 'Suriname',
         'Svalbard and Jan Mayen Islands', 'Swaziland', 'Sweden', 'Switzerland', 'Syrian Arab Republic', 'Taiwan',
         'Tajikistan', 'Tanzania, United Republic of', 'Thailand', 'Togo', 'Tokelau', 'Tonga',
         'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Turks and Caicos Islands', 'Tuvalu',
         'United States', 'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom',
         'United States Minor Outlying I', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Venezuela', 'Viet Nam',
         'Virgin Islands (British)', 'Virgin Islands (U.S.)', 'Wallis and Futuna Islands', 'Western Sahara',
         'Yemen', 'Yugoslavia', 'Zambia', 'Zimbabwe'];


    $field1->setPicklistValues($countries);
    $field2->setPicklistValues($countries);
}

echo "<br><h1>Finished </h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";