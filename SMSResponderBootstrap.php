<?php
include_once 'vtlib/Vtiger/Module.php';
include_once 'include/Webservices/Utils.php';

$Vtiger_Utils_Log = true;

$MODULENAME = 'SMSResponder';

$moduleInstance = Vtiger_Module::getInstance($MODULENAME);
/*
if ($moduleInstance || file_exists('modules/'.$MODULENAME)) {
        echo "Module already present - choose a different name.";

        $toolsMenu = Vtiger_Menu::getInstance('Tools');
        $toolsMenu->detachModule($moduleInstance);
} else {
*/
        $moduleInstance = new Vtiger_Module();
        $moduleInstance->name = $MODULENAME;
        $moduleInstance->parent= '';
        $moduleInstance->save();

        // Schema Setup
        $moduleInstance->initTables();

        // Field Setup
        $block = new Vtiger_Block();
        $block->label = 'LBL_'. strtoupper($moduleInstance->name) . '_INFORMATION';
        $moduleInstance->addBlock($block);

        $blockcf = new Vtiger_Block();
        $blockcf->label = 'LBL_CUSTOM_INFORMATION';
        $moduleInstance->addBlock($blockcf);

        $field1  = new Vtiger_Field();
        $field1->name = 'message';
        $field1->label= 'Message';
        $field1->uitype= 19;
        $field1->column = $field1->name;
        $field1->columntype = 'Text';
        $field1->typeofdata = 'V~M';
        $block->addField($field1);

        $moduleInstance->setEntityIdentifier($field1);

        $field2  = new Vtiger_Field();
        $field2->name = 'status';
        $field2->label= 'Status';
        $field2->uitype= 1;
        $field2->column = $field2->name;
        $field2->columntype = 'VARCHAR(100)';
        $field2->typeofdata = 'V~O~LE~100';
        $block->addField($field2);

        $field3  = new Vtiger_Field();
        $field3->name = 'description';
        $field3->label= 'Description';
        $field3->uitype= 19;
        $field3->column = 'description';
        $field3->table = 'vtiger_crmentity';
        $blockcf->addField($field3);

        // Recommended common fields every Entity module should have (linked to core table)
        $mfield1 = new Vtiger_Field();
        $mfield1->name = 'assigned_user_id';
        $mfield1->label = 'Assigned To';
        $mfield1->table = 'vtiger_crmentity';
        $mfield1->column = 'smownerid';
        $mfield1->uitype = 53;
        $mfield1->typeofdata = 'V~M';
        $block->addField($mfield1);

        $mfield2 = new Vtiger_Field();
        $mfield2->name = 'CreatedTime';
        $mfield2->label= 'Created Time';
        $mfield2->table = 'vtiger_crmentity';
        $mfield2->column = 'createdtime';
        $mfield2->uitype = 70;
        $mfield2->typeofdata = 'T~O';
        $mfield2->displaytype= 2;
        $block->addField($mfield2);

        $mfield3 = new Vtiger_Field();
        $mfield3->name = 'ModifiedTime';
        $mfield3->label= 'Modified Time';
        $mfield3->table = 'vtiger_crmentity';
        $mfield3->column = 'modifiedtime';
        $mfield3->uitype = 70;
        $mfield3->typeofdata = 'T~O';
        $mfield3->displaytype= 2;
        $block->addField($mfield3);

        // Filter Setup
        $filter1 = new Vtiger_Filter();
        $filter1->name = 'All';
        $filter1->isdefault = true;
        $moduleInstance->addFilter($filter1);
        $filter1->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($mfield1, 3);

        // Sharing Access Setup
        $moduleInstance->setDefaultSharing();

        // Webservice Setup
        $moduleInstance->initWebservice();


        $name = 'twilio.receiver';
        $handler_path = 'modules/SMSNotifier/smsresponse.php';
        $handler_method = 'sms_response_process';
        $type = 'POST';
        $prelogin = '0';

        /*
         * Add the custom handler operation to the webservice.
         *
         * @param $name name of the webservice to be added with namespace.
         * @param $handlerFilePath file to be include which provides the handler method for the given webservice.
         * @param $handlerMethodName name of the function to the called when this webservice is invoked.
         * @param $requestType type of request that this operation should be, if in doubt give it as GET,
         *	general rule of thumb is that, if the operation is adding/updating data on server then it must be POST
         *	otherwise it should be GET.
         * @param $preLogin 0 if the operation need the user to authorised to access the webservice and
         *	1 if the operation is called before login operation hence the there will be no user authorisation happening
         *	for the operation.
         * @return Integer operationId of successful or null upon failure.
         */
        if (vtws_addWebserviceOperation($name, $handler_path, $handler_method, $type, $prelogin)) {
            print "Created the webservice operation. <br />\n";
        } else {
            print "FAILED to add the webservice operation. <br />\n";
            //Vtiger_Utils doesn't have an overlay for pquery... so just using this other style.
            $stmt = "INSERT IGNORE INTO `vtiger_ws_operation` SET "
                    . "`name` = '" . Vtiger_Utils::SQLEscape($name) . "'"
                    . ", `handler_path` = '" . Vtiger_Utils::SQLEscape($handler_path) . "'"
                    . ", `handler_method` = '" . Vtiger_Utils::SQLEscape($handler_method) . "'"
                    . ", `type` = '" . Vtiger_Utils::SQLEscape($type) . "'"
                    . ", `prelogin` = '" . Vtiger_Utils::SQLEscape($prelogin) . "'";
            Vtiger_Utils::ExecuteQuery($stmt);
            print "Running:  $stmt;\n";
            print "\n<br />\n";
        }

//        mkdir('modules/'.$MODULENAME);
        echo "OK\n";
//}
