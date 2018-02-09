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
error_reporting(E_ERROR);
require_once('vtlib/Vtiger/Menu.php');
require_once('vtlib/Vtiger/Module.php');
require_once('includes/main/WebUI.php');
require_once('includes/runtime/LanguageHandler.php');
require_once('include/Webservices/Create.php');
require_once('modules/Vtiger/uitypes/Date.php');

$adb = PearDatabase::getInstance();
echo "\nBEGINNING Address List Module\n";
echo "\nBEGINNING Creating Module\n";

function createFieldsAndBlocks_3267($moduleInstance,$listFieldsInfo){
    foreach($listFieldsInfo as $blockLabel => $listField){
        $blockInstance = Vtiger_Block::getInstance($blockLabel, $moduleInstance);
        if(!$blockInstance){
            $blockInstance = new Vtiger_Block();
            $blockInstance->label = $blockLabel;
            $moduleInstance->addBlock($blockInstance);
        }
        foreach($listField as $fieldName =>$fieldInfo){
            echo "\nBEGINNING create $fieldName field\n";
            $fieldModel = Vtiger_Field_Model::getInstance($fieldName,$moduleInstance);
            if($fieldModel){
                echo "\n$fieldName field already exists.\n";
            }else{
                $fieldModel = new Vtiger_Field();
                $fieldModel->table = $fieldInfo['tablename'];
                $fieldModel->columnname = $fieldName;
                $fieldModel->name = $fieldName;
                foreach ($fieldInfo as $option =>$value){
                    if(!in_array($option,array('picklistvalues','related_modules'))){
                        $fieldModel->$option = $value;
                    }
                }
                $blockInstance->addField($fieldModel);
                if(isset($fieldInfo['picklistvalues'])){
                    $fieldModel->setPicklistValues($fieldInfo['picklistvalues']);
                }
                if(isset($fieldInfo['related_modules'])){
                    $fieldModel->setRelatedModules($fieldInfo['related_modules']);
                }
                if(isset($fieldInfo['isentityidentifier'])){
                    $moduleInstance->setEntityIdentifier($fieldModel);
                }
                echo "done!\n";
            }
        }
    }
}

$addressListInstance = Vtiger_Module::getInstance('AddressList');
if(!$addressListInstance){
    $addressListInstance = new Vtiger_Module();
    $addressListInstance->name = 'AddressList';
    $addressListInstance->save();
    $addressListInstance->initTables();
    $addressListInstance->setDefaultSharing();
    $addressListInstance->initWebservice();
}

$arrModuleStructure = [
    'LBL_ADDRESSES'=>[
        'address_type'=>[
            'label'=>'Address Type',
            'columntype'=>'varchar(255)',
            'uitype'=>16,
            'typeofdata'=>'V~M',
            'isentityidentifier'=>true
        ],
        'location_type'=>[
            'label'=>'Location Type',
            'columntype'=>'varchar(255)',
            'uitype'=>16,
            'typeofdata'=>'O~M',
            'picklistvalues'=>['Apartment','Condo','Single Family'],
        ],
        'address_contact'=>[
            'label'=>'Address Contact',
            'columntype'=>'varchar(255)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ],
        'address_company'=>[
            'label'=>'Address Company',
            'columntype'=>'varchar(255)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ],
        'address_phone'=>[
            'label'=>'Address Phone',
            'columntype'=>'varchar(30)',
            'uitype'=>11,
            'typeofdata'=>'V~O',
        ],
        'address_email'=>[
            'label'=>'Address Email',
            'columntype'=>'varchar(50)',
            'uitype'=>13,
            'typeofdata'=>'E~O',
        ],
        'address1'=>[
            'label'=>'Address 1',
            'columntype'=>'varchar(255)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ],
        'address2'=>[
            'label'=>'Address 2',
            'columntype'=>'varchar(255)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ],
        'city'=>[
            'label'=>'City',
            'columntype'=>'varchar(255)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ],
        'state'=>[
            'label'=>'State',
            'columntype'=>'varchar(100)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ],
        'zip_code'=>[
            'label'=>'Zip',
            'columntype'=>'varchar(100)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ],
        'country'=>[
            'label'=>'Country',
            'columntype'=>'varchar(255)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ],
        'zoneadminid'=>[
            'label'=>'Zone',
            'columntype'=>'varchar(100)',
            'uitype'=>10,
            'typeofdata'=>'V~O',
            'related_modules'=>['ZoneAdmin'],
        ],
        'long_carry'=>[
            'label'=>'Long Carry (Ft)',
            'columntype'=>'int(10)',
            'uitype'=>7,
            'typeofdata'=>'I~O',
        ],
        'of_flights'=>[
            'label'=>'# of Flights',
            'columntype'=>'int(10)',
            'uitype'=>7,
            'typeofdata'=>'I~O',
        ],
        'of_elevators'=>[
            'label'=>'# of Elevators',
            'columntype'=>'int(10)',
            'uitype'=>7,
            'typeofdata'=>'I~O',
        ],
        'notes'=>[
            'label'=>'Notes',
            'columntype'=>'text',
            'uitype'=>21,
            'typeofdata'=>'V~O',
        ]
    ]
];

createFieldsAndBlocks_3267($addressListInstance,$arrModuleStructure);

// Create extra tables
echo "<h3>Beginning Create vtiger_addresslistrel table</h3>";
if(!Vtiger_Utils::CheckTable('vtiger_addresslistrel')) {
    Vtiger_Utils::CreateTable(
        'vtiger_addresslistrel',
        "(addresslistid INT(19) NOT NULL ,
            crmid INT(19) NOT NULL,
            sequence INT(5) NOT NULL)",
        true);
    echo "Done!";
}else{
    echo "<br>vtiger_addresslistrel table already exists.</br>";
}

// Remove billing field in invoice details for orders module
//OT3756 - Changing this to set presence = 2
$adb->pquery("UPDATE vtiger_field SET presence = 2 WHERE  tabid = ? AND fieldname = ?",array(getTabid('Orders'),'bill_street'));
$adb->pquery("UPDATE vtiger_field SET presence = 2 WHERE  tabid = ? AND fieldname = ?",array(getTabid('Orders'),'bill_city'));
$adb->pquery("UPDATE vtiger_field SET presence = 2 WHERE  tabid = ? AND fieldname = ?",array(getTabid('Orders'),'bill_state'));
$adb->pquery("UPDATE vtiger_field SET presence = 2 WHERE  tabid = ? AND fieldname = ?",array(getTabid('Orders'),'bill_code'));
$adb->pquery("UPDATE vtiger_field SET presence = 2 WHERE  tabid = ? AND fieldname = ?",array(getTabid('Orders'),'bill_pobox'));
$adb->pquery("UPDATE vtiger_field SET presence = 2 WHERE  tabid = ? AND fieldname = ?",array(getTabid('Orders'),'bill_country'));

// Remove extra stops blocks
$adb->pquery("UPDATE vtiger_guestmodulerel SET active = 0 WHERE  hostmodule = 'Opportunities' AND guestmodule = 'ExtraStops'",[]);
$adb->pquery("UPDATE vtiger_guestmodulerel SET active = 0 WHERE  hostmodule = 'Orders' AND guestmodule = 'ExtraStops'",[]);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";