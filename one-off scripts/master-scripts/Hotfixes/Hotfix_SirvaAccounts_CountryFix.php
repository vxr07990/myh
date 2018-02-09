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


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
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
echo "<br/>Updating the accounts to use correct country menu type";
try {
    $db = PearDatabase::getInstance();
    $module = Vtiger_Module::getInstance('Accounts');
    if ($module) {
        $field1 = Vtiger_Field::getInstance('bill_country', $module);
        $field2 = Vtiger_Field::getInstance('ship_country', $module);

        echo "<br/>Checking for billing country";
        if ($field1) {
            echo "<br/>Updating billing country";
            $db->pquery("UPDATE `vtiger_field` SET uitype = 16 WHERE fieldid = ?", [$field1->id]);
            echo "<br/>Billing country updated";

            echo "<br/>Billing country picklist updating";
            $field1->setPicklistValues($countries);
            echo "<br/>Billing country picklist updated";
        }

        echo "<br/>Checking for shipping country";
        if ($field2) {
            echo "<br/>Updating shipping country";
            $db->pquery("UPDATE `vtiger_field` SET uitype = 16 WHERE fieldid = ?", [$field2->id]);
            echo "<br/>Shipping country updated";
            echo "<br/>Shipping country picklist updating";
            $field2->setPicklistValues($countries);
            echo "<br/>Billing country picklist updated";
        }
        echo "<br/>Account country update completed";
    }
} catch (Exception $e) {
    echo "<br/>ERROR DETECTED: ".$e->getMessage();
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";