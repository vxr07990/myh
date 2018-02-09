<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
$languageStrings = array(
    'LBL_CONTACTS_ASSISTANT' => 'Assistant',
    'LBL_CONTACTS_ASSISTANTPHONE' => 'Assistant Phone',
    'LBL_CONTACTS_BIRTHDAY' => 'Date of Birth',
    'LBL_CONTACTS_CONTACTID' => 'Contact Id',
    'LBL_CONTACTS_CONTACTIMAGE' => 'Contact Image',
    'Contacts' => 'Contacts',
    'LBL_CONTACTS_DEPARTMENT' => 'Department',
    'LBL_CONTACTS_DONOTCALL' => 'Do Not Call',
    'LBL_CONTACTS_EMAIL' => 'Primary Email',
    'LBL_CONTACTS_HOMEPHONE' => 'Home Phone',
    'LBL_ADD_RECORD' => 'Add Contact',
    'LBL_ADDRESS_INFORMATION' => 'Address Information',
    'LBL_CONTACT_INFORMATION' => 'Basic Information',
    'LBL_CUSTOM_INFORMATION' => 'Custom Information',
    'LBL_DESCRIPTION_INFORMATION' => 'Description Information',
    'LBL_COPY_MAILING_ADDRESS' => 'Copy Mailing Address',
    'LBL_COPY_OTHER_ADDRESS' => 'Copy Other Address',
    'LBL_CUSTOMER_PORTAL_INFORMATION' => 'Customer Portal Details',
    'LBL_CONTACTS_LASTNAME' => ' Last Name',
    'LBL_IMAGE_INFORMATION' => 'Profile Picture',
    'LBL_RECORDS_LIST' => 'Contacts List',
    'LBL_CONTACTS_MAILINGCITY' => 'Mailing City',
    'LBL_CONTACTS_MAILINGCOUNTRY' => 'Mailing Country',
    'LBL_CONTACTS_MAILINGPOBOX' => 'Mailing P.O. Box',
    'LBL_CONTACTS_MAILINGSTATE' => 'Mailing State',
    'LBL_CONTACTS_MAILINGSTREET' => 'Mailing Address',//'Mailing Street',
	'LBL_CONTACTS_MAILINGSTREET2' => 'Mailing Address 2',
    'LBL_CONTACTS_MAILINGZIP' => 'Mailing Zip',
    'LBL_CONTACTS_OFFICEPHONE' => 'Office Phone',
    'LBL_CONTACTS_OTHERCITY' => 'Other City',
    'LBL_CONTACTS_OTHERCOUNTRY' => 'Other Country',
    'LBL_CONTACTS_OTHERPHONE' => 'Secondary Phone',
    'LBL_CONTACTS_OTHERPOBOX' => 'Other P.O. Box',
    'LBL_CONTACTS_OTHERSTATE' => 'Other State',
    'LBL_CONTACTS_OTHERSTREET' => 'Other Address',//'Other Street',
	'LBL_CONTACTS_OTHERSTREET2' => 'Other Address 2',
    'LBL_CONTACTS_OTHERZIP' => 'Other Zip',
    'LBL_CONTACTS_REFERENCE' => 'Reference',
    'LBL_CONTACTS_REPORTSTO' => 'Reports To',
    'LBL_CONTACTS_SECONDARYEMAIL' => 'Secondary Email',
    'SINGLE_Contacts' => 'Contact',
    'LBL_CONTACTS_SUPPORTENDDATE'   => 'Support End Date',
    'LBL_CONTACTS_SUPPORTSTARTDATE' => 'Support Start Date',
    'LBL_CONTACTS_TITLE' => 'Title',
    'User List'=>'User List',
    'LBL_CONTACTS_SALUTATION' => 'Salutation',
    'LBL_CONTACTS_FIRSTNAME' =>'First Name',
    'LBL_CONTACTS_MOBILE' => 'Mobile',
    'LBL_CONTACTS_ACCOUNTNAME' => 'Account Name',
    'LBL_CONTACTS_LEADSOURCE' => 'Lead Source',
    'LBL_CONTACTS_FAX'=> 'Fax',
    'LBL_CONTACTS_EMAILOPTOUT' => 'Email Opt Out',
    'LBL_CONTACTS_ASSIGNEDTO' => 'Assigned To',
    'LBL_CONTACTS_NOTIFYOWNER' => 'Notify Owner',
    'LBL_CONTACTS_CREATEDTIME' => 'Created Time',
    'LBL_CONTACTS_MODIFIEDTIME' => 'Modified Time',
    'LBL_CONTACTS_LASTMODIFIEDBY' => 'Modified By',
    'LBL_CONTACTS_PORTALUSER' => 'Portal User',
    'LBL_CONTACTS_DESCRIPTION' => 'Description',
    'LBL_CONTACTS_STATUS' => 'Status',
    'LBL_CONTACTS_CREATEDBY' => 'Created By',
    'LBL_CONTACTS_LEADS' => 'Leads',
    'LBL_CONTACTS_ISCONVERTEDFROMLEAD' => 'Is converted from Lead',
    'LBL_CONTACTS_ORDERS'=>'Order Information',
    'LBL_CONTACTS_ACCOUNTS'=>'Account Information',
    'LBL_CONTACTS_AGENTS'=>'Agent Information',
    'LBL_CONTACTS_VANLINES'=>'Vanline Information',
    'LBL_CONTACT_AGENTS'=>'Agent',
    'LBL_CONTACT_ACCOUNT'=>'Account',
    'LBL_CONTACT_ORDER'=>'Order',
    'LBL_CONTACT_VANLINE'=>'Van Line',
    'LBL_TRANSFEREES'=>'Transferees',
    'LBL_VANLINES'=>'Van Lines',
    'LBL_AGENTS'=>'Agents',
    'LBL_ACCOUNTS'=>'Accounts',
    'LBL_CONTACTS_PRIMARYPHONEEXT' => 'Ext:',
    'LBL_CONTACTS_PRIMARYPHONETYPE' => 'Primary Phone Type',
    'LBL_CUSTOMER_NUMBER' => 'Customer Number',
    //'Account'=>'Accounts',

	'LBL_RECORDUPDATEINFORMATION' => 'Record Update Information',
	'LBL_CREATEDBY' => 'Created By',
	
    //Portal

    //Added for Picklist Values
    'Dr.'=>'Dr.',
    'Mr.'=>'Mr.',
    'Mrs.'=>'Mrs.',
    'Ms.'=>'Ms.',
    'Prof.'=>'Prof.',
);

if (getenv('INSTANCE_NAME') === 'uvlc') {
    $languageStrings['Mailing State'] = 'Mailing Province';
    $languageStrings['Other State'] = 'Other Province';
    $languageStrings['Mailing Zip'] = 'Mailing Postal Code';
    $languageStrings['Other Zip'] = 'Other Postal Code';
}
if (getenv('INSTANCE_NAME') === 'sirva') {
    $languageStrings['Office Phone'] = 'Primary Phone';
}

$jsLanguageStrings = array(
 );
