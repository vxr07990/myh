<?php
if(checkIsWindfallActive()) {
    if (function_exists("call_ms_function_ver")) {
        $version = 1;
        if (call_ms_function_ver(__FILE__, $version)) {
            //already ran
            print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
            return;
        }
    }

    global $adb;
    function createFieldsAndBlocks_4457($moduleInstance, $listFieldsInfo)
    {
        $filter = Vtiger_Filter::getInstance('All', $moduleInstance);
        if(!$filter){
            echo "<br>BEGINNING create filter for module";
            $filter = new Vtiger_Filter();
            $filter->name = "All";
            $filter->isdefault = true;
            $moduleInstance->addFilter($filter);
        }
        foreach ($listFieldsInfo as $blockLabel => $listField) {
            $blockInstance = Vtiger_Block::getInstance($blockLabel, $moduleInstance);
            if (!$blockInstance) {
                echo "<br>BEGINNING create $blockLabel block";
                $blockInstance = new Vtiger_Block();
                $blockInstance->label = $blockLabel;
                $moduleInstance->addBlock($blockInstance);
            }

            foreach ($listField as $fieldName => $fieldInfo) {
                echo "<br>BEGINNING create $fieldName field";
                $fieldModel = Vtiger_Field_Model::getInstance($fieldName, $moduleInstance);
                if ($fieldModel) {
                    echo "<br>$fieldName field already exists.";
                } else {
                    $fieldModel = new Vtiger_Field();
                    $fieldModel->table = $fieldInfo['table'];
                    if ($fieldInfo['table'] == '') {
                        $fieldModel->table = 'vtiger_' . strtolower($moduleInstance->name);
                    }
                    $fieldModel->name = $fieldName;
                    foreach ($fieldInfo as $option => $value) {
                        if (!in_array($option, array('picklistvalues', 'related_modules', 'isentityidentifier'))) {
                            $fieldModel->$option = $value;
                        }
                    }

                    $blockInstance->addField($fieldModel);
                    if (isset($fieldInfo['picklistvalues'])) {
                        $fieldModel->setPicklistValues($fieldInfo['picklistvalues']);
                    }
                    if (isset($fieldInfo['related_modules'])) {
                        $fieldModel->setRelatedModules($fieldInfo['related_modules']);
                    }
                    if (isset($fieldInfo['isentityidentifier'])) {
                        $moduleInstance->setEntityIdentifier($fieldModel);
                    }
                    if($fieldInfo['isFilterField']){
                        $filter->addField($fieldModel,$fieldModel->id);
                    }
                    echo "<br>done!";
                }
            }
        }
    }

    $wfAddressInstance = Vtiger_Module::getInstance('WFAddress');
    if(!$wfAddressInstance){
        echo "<h3>BEING CREATE WFAddress MODULE</h3>";
        $wfAddressInstance = new Vtiger_Module();
        $wfAddressInstance->name = 'WFAddress';
        $wfAddressInstance->save();
        $wfAddressInstance->initTables();
        $wfAddressInstance->setDefaultSharing();
        $wfAddressInstance->initWebservice();
        $tableid = $wfAddressInstance->getId();
        $sql = "SELECT * FROM `vtiger_modtracker_tabs` WHERE `vtiger_modtracker_tabs`.`tabid` = ?";
        $result = $adb->pquery($sql, array($tableid));
        if ($adb->num_rows($result) == 0) {
            $adb->pquery("insert into `vtiger_modtracker_tabs` ( `visible`, `tabid`) values (?, ?)", array('1', $tableid));
        }
    }
    $moduleInfos = array(
        'LBL_WFADDRESS_DETAILS'=>array(
            'address_name'=>array(
                'label'=>'LBL_WFADDRESS_ADDRESS_NAME',
                'columntype' => 'varchar(255)',
                'uitype' => 1,
                'typeofdata' => 'V~M',
                'isFilterField' => true,
                'isentityidentifier'=>true
            ),
            'wfaddress_type'=>array(
                'label'=>'LBL_WFADDRESS_TYPE',
                'columntype' => 'varchar(255)',
                'uitype' => 16,
                'typeofdata' => 'V~O',
                'picklistvalues'=>array('Business','Destination','Home','Origin','Shipping','Other'),
                'isFilterField' => true,
            ),
            'firstname'=>array(
                'label'=>'LBL_WFADDRESS_FIRST_NAME',
                'columntype' => 'varchar(255)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'isFilterField' => true,
            ),
            'lastname'=>array(
                'label'=>'LBL_WFADDRESS_LAST_NAME',
                'columntype' => 'varchar(255)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'isFilterField' => true,
            ),
            'company'=>array(
                'label'=>'LBL_WFADDRESS_COMPANY_NAME',
                'columntype' => 'varchar(255)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
                'isFilterField' => true,
            ),

            'wfaddress_related_to'=>array(
                'label'=>'LBL_WFADDRESS_RELATED_TO',
                'columntype' => 'int(19)',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'related_modules' => array('WFAccounts'),
                'isFilterField' => true,
            ),
            'wfaddress_address1'=>array(
                'label'=>'LBL_WFADDRESS_ADDRESS1',
                'columntype' => 'varchar(255)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
            ),
            'wfaddress_address2'=>array(
                'label'=>'LBL_WFADDRESS_ADDRESS2',
                'columntype' => 'varchar(255)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
            ),
            'wfaddress_city'=>array(
                'label'=>'LBL_WFADDRESS_CITY',
                'columntype' => 'varchar(255)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
            ),
            'wfaddress_state'=>array(
                'label'=>'LBL_WFADDRESS_STATE',
                'columntype' => 'varchar(255)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
            ),
            'wfaddress_zip'=>array(
                'label'=>'LBL_WFADDRESS_ZIP',
                'columntype' => 'varchar(255)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
            ),
            'wfaddress_country'=>array(
                'label'=>'LBL_WFADDRESS_COUNTRY',
                'columntype' => 'varchar(255)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
            ),
            'wfaddress_phone'=>array(
                'label'=>'LBL_WFADDRESS_PHONE',
                'columntype' => 'varchar(30)',
                'uitype' => 11,
                'typeofdata' => 'V~O',
            ),
            'wfaddress_fax'=>array(
                'label'=>'LBL_WFADDRESS_FAX',
                'columntype' => 'varchar(30)',
                'uitype' => 11,
                'typeofdata' => 'V~O',
            ),
            'wfaddress_email'=>array(
                'label'=>'LBL_WFADDRESS_EMAIL',
                'columntype' => 'varchar(30)',
                'uitype' => 13,
                'typeofdata' => 'V~O',
            ),

        ),
        'LBL_WFADDRESS_RECORDUPDATE' => array(
            'createdtime' => array(
                'label' => 'LBL_WFADDRESS_CREATED_TIME',
                'columntype' => 'datetime',
                'uitype' => 70,
                'typeofdata' => 'T~O',
                'displaytype' => 2,
                'table' => 'vtiger_crmentity'
            ),
            'modifiedtime' => array(
                'label' => 'LBL_WFADDRESS_MODIFIED_TIME',
                'columntype' => 'datetime',
                'uitype' => 70,
                'typeofdata' => 'T~O',
                'displaytype' => 2,
                'table' => 'vtiger_crmentity'
            ),
            'created_by' => array(
                'label' => 'LBL_WFADDRESS_CREATED_BY',
                'columntype' => 'int(19)',
                'uitype' => 52,
                'typeofdata' => 'V~O',
                'column' => 'smcreatorid',
                'displaytype' => 2,
                'table' => 'vtiger_crmentity'
            ),
            'assigned_user_id' => array(
                'label' => 'LBL_WFADDRESS_ASSIGNED_TO',
                'columntype' => 'int(19)',
                'uitype' => 53,
                'typeofdata' => 'V~O',
                'column' => 'smownerid',
                'displaytype' => 2,
                'table' => 'vtiger_crmentity'
            ),
        )
    );
    createFieldsAndBlocks_4457($wfAddressInstance,$moduleInfos);
    $adb->pquery("ALTER TABLE vtiger_guestmodulerel ADD block_type varchar(10) DEFAULT NULL");
    $blockInstance = Vtiger_Block::getInstance('LBL_WFADDRESS_DETAILS',$wfAddressInstance);
    $rsCheck = $adb->pquery("SELECT guestmodulerelid  FROM vtiger_guestmodulerel WHERE hostmodule = 'WFAccounts' AND guestmodule = 'WFAddress' AND blockid = ?",array($blockInstance->id));
    if($adb->num_rows($rsCheck) == 0){
        $adb->pquery("INSERT INTO vtiger_guestmodulerel(hostmodule,guestmodule,blockid,active,after_block,block_type) VALUES('WFAccounts','WFAddress',?,1,'LBL_WFACCOUNTS_DETAIL','list')",array($blockInstance->id));
    }
}
