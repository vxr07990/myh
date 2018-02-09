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


    //Update_users_20150729.php
    $module = Vtiger_Module::getInstance('Users');

    $block = Vtiger_Block::getInstance('LBL_USER_ADV_OPTIONS', $module);

    $field1 = Vtiger_Field::getInstance('push_notification_token', $module);
    if ($field1) {
        echo "<h4>Field <b>push_notification_token</b> already exists!</h4>";
    } else {
        $field1 = new Vtiger_Field();
        $field1->label = 'LBL_USERS_PUSHNOTIFICATIONTOKEN';
        $field1->name = 'push_notification_token';
        $field1->table = 'vtiger_users';
        $field1->column = 'push_notification_token';
        $field1->columntype = 'VARCHAR(255)';
        $field1->uitype = 1;
        $field1->typeofdata = 'V~O';
        
        $block->addField($field1);
    }

    $field2 = Vtiger_Field::getInstance('dbx_token', $module);
    if ($field2) {
        echo "<h4>Field <b>dbx_token</b> already exists!</h4>";
    } else {
        $field2 = new Vtiger_Field();
        $field2->label = 'LBL_USERS_DROPBOXTOKEN';
        $field2->name = 'dbx_token';
        $field2->table = 'vtiger_users';
        $field2->column = 'dbx_token';
        $field2->columntype = 'VARCHAR(100)';
        $field2->uitype = 1;
        $field2->typeofdata = 'V~O';
        $field2->displaytype = 2;
        
        $block->addField($field2);
    }

    $field3 = Vtiger_Field::getInstance('oi_enabled', $module);
    if ($field3) {
        echo "<h4>Field <b>oi_enabled</b> already exists!</h4>";
    } else {
        $field3 = new Vtiger_Field();
        $field3->label = 'LBL_USERS_OIENABLED';
        $field3->name = 'oi_enabled';
        $field3->table = 'vtiger_users';
        $field3->column = 'oi_enabled';
        $field3->columntype = 'VARCHAR(3)';
        $field3->uitype = 56;
        $field3->typeofdata = 'C~O';
        $field3->displaytype = 3;
        
        $block->addField($field3);
    }

    $field4 = Vtiger_Field::getInstance('dbx_userid', $module);
    if ($field4) {
        echo "<h4>Field <b>dbx_userid</b> already exists!</h4>";
    } else {
        $field4 = new Vtiger_Field();
        $field4->label = 'LBL_USERS_DROPBOXUSERID';
        $field4->name = 'dbx_userid';
        $field4->table = 'vtiger_users';
        $field4->column = 'dbx_userid';
        $field4->columntype = 'VARCHAR(50)';
        $field4->uitype = 1;
        $field4->typeofdata = 'V~O';
        $field4->displaytype = 3;
        
        $block->addField($field4);
    }

    $field5 = Vtiger_Field::getInstance('oi_push_notification_token', $module);
    if ($field5) {
        echo "<h4>Field <b>oi_push_notification_token</b> already exists!</h4>";
    } else {
        $field5 = new Vtiger_Field();
        $field5->label = 'LBL_USERS_SURVEYOIPUSHNOTIFICATIONTOKEN';
        $field5->name = 'oi_push_notification_token';
        $field5->table = 'vtiger_users';
        $field5->column = 'oi_push_notification_token';
        $field5->columntype = 'VARCHAR(255)';
        $field5->uitype = 1;
        $field5->typeofdata = 'V~O';
        
        $block->addField($field5);
    }

    $field6 = Vtiger_Field::getInstance('vanline', $module);
    if ($field6) {
        echo "<h4>Field <b>vanline</b> already exists!</h4>";
    } else {
        $field6 = new Vtiger_Field();
        $field6->label = 'LBL_USERS_VANLINE';
        $field6->name = 'vanline';
        $field6->table = 'vtiger_users';
        $field6->column = 'vanline';
        $field6->columntype = 'VARCHAR(75)';
        $field6->uitype = 1;
        $field6->typeofdata = 'V~O';
        $field6->defaultvalue = 'BASE';
        
        $block->addField($field6);
    }

    $field7 = Vtiger_Field::getInstance('custom_reports_pw', $module);
    if ($field7) {
        echo "<h4>Field <b>custom_reports_pw</b> already exists!</h4>";
    } else {
        $field7 = new Vtiger_Field();
        $field7->label = 'LBL_USERS_CUSTOMREPORTSPASSWORD';
        $field7->name = 'custom_reports_pw';
        $field7->table = 'vtiger_users';
        $field7->column = 'custom_reports_pw';
        $field7->columntype = 'VARCHAR(100)';
        $field7->uitype = 1;
        $field7->typeofdata = 'V~O';
        $field7->displaytype = 4;
        
        $block->addField($field7);
    }
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET defaultvalue = 'tightview' WHERE tabid = 29 AND columnname = 'theme'");

$block1 = Vtiger_Block::getInstance('LBL_SMTP_USER_INFORMATION', $module);
if ($block1) {
    echo "<h4>Block <b>LBL_SMTP_USER_INFORMATION</b> already exists!</h4>";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_SMTP_USER_INFORMATION';
    $module->addBlock($block1);
}

$smtp_field1 = Vtiger_Field::getInstance('user_smtp_server', $module);
if ($smtp_field1) {
    echo "<h4>Field <b>user_smtp_server</b> already exists!</h4>";
} else {
    $smtp_field1 = new Vtiger_Field();
    $smtp_field1->label = 'Server Name';
    $smtp_field1->name = 'user_smtp_server';
    $smtp_field1->table = 'vtiger_users';
    $smtp_field1->column = 'user_server';
    $smtp_field1->columntype = 'VARCHAR(100)';
    $smtp_field1->uitype = 2;
    $smtp_field1->typeofdata = 'V~O';

    $block1->addField($smtp_field1);
}

$smtp_field2 = Vtiger_Field::getInstance('user_smtp_username', $module);
if ($smtp_field2) {
    echo "<h4>Field <b>user_smtp_username</b> already exists!</h4>";
} else {
    $smtp_field2 = new Vtiger_Field();
    $smtp_field2->label = 'User Name';
    $smtp_field2->name = 'user_smtp_username';
    $smtp_field2->table = 'vtiger_users';
    $smtp_field2->column = 'user_smtp_username';
    $smtp_field2->columntype = 'VARCHAR(50)';
    $smtp_field2->uitype = 2;
    $smtp_field2->typeofdata = 'V~O';

    $block1->addField($smtp_field2);
}

$smtp_field3 = Vtiger_Field::getInstance('user_smtp_password', $module);
if ($smtp_field3) {
    echo "<h4>Field <b>user_smtp_password</b> already exists!</h4>";
} else {
    $smtp_field3 = new Vtiger_Field();
    $smtp_field3->label = 'Password';
    $smtp_field3->name = 'user_smtp_password';
    $smtp_field3->table = 'vtiger_users';
    $smtp_field3->column = 'user_smtp_password';
    $smtp_field3->columntype = 'VARCHAR(50)';
    $smtp_field3->uitype = 2;
    $smtp_field3->typeofdata = 'V~O';

    $block1->addField($smtp_field3);
}

$smtp_field4 = Vtiger_Field::getInstance('user_smtp_fromemail', $module);
if ($smtp_field4) {
    echo "<h4>Field <b>user_smtp_fromemail</b> already exists!</h4>";
} else {
    $smtp_field4 = new Vtiger_Field();
    $smtp_field4->label = 'From E-Mail';
    $smtp_field4->name = 'user_smtp_fromemail';
    $smtp_field4->table = 'vtiger_users';
    $smtp_field4->column = 'user_smtp_fromemail';
    $smtp_field4->columntype = 'VARCHAR(50)';
    $smtp_field4->uitype = 2;
    $smtp_field4->typeofdata = 'V~O';

    $block1->addField($smtp_field4);
}

$smtp_field5 = Vtiger_Field::getInstance('user_smtp_authentication', $module);
if ($smtp_field5) {
    echo "<h4>Field <b>user_smtp_authentication</b> already exists!</h4>";
} else {
    $smtp_field5 = new Vtiger_Field();
    $smtp_field5->label = 'Requires Authentication';
    $smtp_field5->name = 'user_smtp_authentication';
    $smtp_field5->table = 'vtiger_users';
    $smtp_field5->column = 'user_smtp_authentication';
    $smtp_field5->columntype = 'VARCHAR(3)';
    $smtp_field5->uitype = 56;
    $smtp_field5->typeofdata = 'C~O';

    $block1->addField($smtp_field5);
}

$block1->save($module);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";