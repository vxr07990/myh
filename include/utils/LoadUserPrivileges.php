<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 12/19/2016
 * Time: 10:40 AM
 */

global $userPrivilegeCache;

if(!isset($currentUserId) && isset($current_user))
{
    $currentUserId = $current_user->id;
}

if(isset($userPrivilegeCache[$currentUserId]))
{
    $loaduser_data = $userPrivilegeCache[$currentUserId];
    $is_admin = $loaduser_data['is_admin'];
    $current_user_roles = $loaduser_data['current_user_roles'];
    $current_user_parent_role_seq = $loaduser_data['current_user_parent_role_seq'];
    $current_user_profiles = $loaduser_data['current_user_profiles'];
    $profileGlobalPermission = $loaduser_data['profileGlobalPermission'];
    $profileTabsPermission = $loaduser_data['profileTabsPermission'];
    $profileActionPermission = $loaduser_data['profileActionPermission'];
    $current_user_groups = $loaduser_data['current_user_groups'];
    $subordinate_roles = $loaduser_data['subordinate_roles'];
    $parent_roles = $loaduser_data['parent_roles'];
    $subordinate_roles_users = $loaduser_data['subordinate_roles_users'];
    $user_info = $loaduser_data['user_info'];
} else {

    $userPrivilegesFileName = 'user_privileges/user_privileges_'.$currentUserId.'.php';

    if (!file_exists($userPrivilegesFileName)) {
        throw new Exception('BAD userPrivilegesFileName ('.$userPrivilegesFileName.')', -1);
    }
    require($userPrivilegesFileName);
    $loaduser_data = [];
    $loaduser_data['is_admin'] = $is_admin;
    $loaduser_data['current_user_roles'] = $current_user_roles;
    $loaduser_data['current_user_parent_role_seq'] = $current_user_parent_role_seq;
    $loaduser_data['current_user_profiles'] = $current_user_profiles;
    $loaduser_data['profileGlobalPermission'] = $profileGlobalPermission;
    $loaduser_data['profileTabsPermission'] = $profileTabsPermission;
    $loaduser_data['profileActionPermission'] = $profileActionPermission;
    $loaduser_data['current_user_groups'] = $current_user_groups;
    $loaduser_data['subordinate_roles'] = $subordinate_roles;
    $loaduser_data['parent_roles'] = $parent_roles;
    $loaduser_data['subordinate_roles_users'] = $subordinate_roles_users;
    $loaduser_data['user_info'] = $user_info;
    $userPrivilegeCache[$currentUserId] = $loaduser_data;
}

