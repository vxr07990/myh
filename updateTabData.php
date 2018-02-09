<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'include/utils/utils.php';
include_once 'include/utils/CommonUtils.php';

create_parenttab_data_file();
create_tab_data_file();
createUserPrivilegesfile(1);
createUserSharingPrivilegesfile(1);
