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
echo "\nBEGINNING Orders Task Addresses Module\n";
echo "\nBEGINNING Creating Module\n";

function createFieldsAndBlocks_3727($moduleInstance,$listFieldsInfo){
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

$addressListInstance = Vtiger_Module::getInstance('OrdersTaskAddresses');
if(!$addressListInstance){
    $addressListInstance = new Vtiger_Module();
    $addressListInstance->name = 'OrdersTaskAddresses';
    $addressListInstance->save();
    $addressListInstance->initTables();
    $addressListInstance->setDefaultSharing();
    $addressListInstance->initWebservice();
}

$arrModuleStructure = [
    'LBL_ADDRESS_DETAIL'=>[
        'related_address'=>[
            'label'=>'Related Address',
            'columntype'=>'varchar(255)',
            'uitype'=>16,
            'typeofdata'=>'V~O',
            'isentityidentifier'=>true
        ],
        'stop_order'=>[
            'label'=>'Stop Order',
            'columntype'=>'int(2)',
            'uitype'=>7,
            'typeofdata'=>'I~M',
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
            'columntype'=>'varchar(255)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
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
        'zip'=>[
            'label'=>'Zip',
            'columntype'=>'varchar(20)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ],
        'phone1'=>[
            'label'=>'Phone 1',
            'columntype'=>'varchar(20)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ],
        'phone2'=>[
            'label'=>'Phone 2',
            'columntype'=>'varchar(20)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ],
        'description'=>[
            'label'=>'Description',
            'columntype'=>'varchar(255)',
            'uitype'=>16,
            'typeofdata'=>'V~O',
            'picklistvalues'=>["Single Family","Multi Family","Office Building","Self Storage","Warehouse","Other"],
        ],
        'orderstask_id'=>[
            'label'=>'Order Task',
            'columntype'=>'int(11)',
            'uitype'=>10,
            'typeofdata'=>'I~O',
            'related_modules'=>['OrdersTask']
        ],
    ]
];

createFieldsAndBlocks_3727($addressListInstance,$arrModuleStructure);



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";