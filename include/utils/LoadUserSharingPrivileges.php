<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 12/19/2016
 * Time: 10:40 AM
 */

global $userSharingPrivilegeCache;
global $adb;

if(!isset($currentUserId) && isset($current_user))
{
    $currentUserId = $current_user->id;
}

if(isset($userSharingPrivilegeCache[$currentUserId]))
{
    $loaduser_prefixList = $userSharingPrivilegeCache['_prefixList'];
    $loaduser_data = $userSharingPrivilegeCache[$currentUserId];
    $is_admin = $loaduser_data['is_admin'];
    $defaultOrgSharingPermission = $loaduser_data['defaultOrgSharingPermission'];
    $related_module_share = $loaduser_data['related_module_share'];
    foreach($loaduser_prefixList as $prefix)
    {
        $readName = $prefix . '_share_read_permission';
        $writeName = $prefix . '_share_write_permission';
        $$readName = $loaduser_data[$readName];
        $$writeName = $loaduser_data[$writeName];
    }
} else {
    $loaduser_prefixList = ['Leads_Emails', 'Accounts_Potentials', 'Accounts_HelpDesk', 'Accounts_Emails' , 'Accounts_Quotes',
                            'Accounts_SalesOrder', 'Accounts_Invoice', 'Potentials_Quotes' ,'Potentials_SalesOrder' ,'Quotes_SalesOrder'];

    $loaduser_query = "SELECT `name` FROM vtiger_tab 
                WHERE presence=0 AND ownedby = 0 AND isentitytype = 1 AND `name` NOT IN('Calendar','Events')";
    $loaduser_result = $adb->query($loaduser_query);
    while ($loaduser_resrow = $adb->fetch_array($loaduser_result)) {
        $loaduser_prefixList[] = $loaduser_resrow['name'];
    }
    $userSharingPrivilegeCache['_prefixList'] = $loaduser_prefixList;

    require('user_privileges/sharing_privileges_'.$currentUserId.'.php');
    $loaduser_data = [];
    $loaduser_data['is_admin'] = $is_admin;
    $loaduser_data['defaultOrgSharingPermission'] = $defaultOrgSharingPermission;
    $loaduser_data['related_module_share'] = $related_module_share;
    foreach($loaduser_prefixList as $prefix)
    {
        $readName = $prefix . '_share_read_permission';
        $writeName = $prefix . '_share_write_permission';
        $loaduser_data[$readName] = $$readName;
        $loaduser_data[$writeName] = $$writeName;
    }
    $userSharingPrivilegeCache[$currentUserId] = $loaduser_data;
}

