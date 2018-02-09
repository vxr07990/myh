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
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport ('includes.runtime.EntryPoint');
global  $adb;

function createFieldsAndBlocks_3424($moduleInstance,$listFieldsInfo){
    global $adb;
    foreach($listFieldsInfo as $blockLabel => $listField){
        $blockInstance = Vtiger_Block::getInstance($blockLabel, $moduleInstance);
        if(!$blockInstance){
            $blockInstance = new Vtiger_Block();
            $blockInstance->label = $blockLabel;
            $moduleInstance->addBlock($blockInstance);
        }
        foreach($listField as $fieldName =>$fieldInfo){
            echo "\n<br>BEGINNING create $fieldName field\n<br>";
            $fieldModel = Vtiger_Field_Model::getInstance($fieldName,$moduleInstance);
            if($fieldModel){
                echo "\n<br>$fieldName field already exists.\n<br>";
                // Update Field Label
                $adb->pquery("update `vtiger_field` set `fieldlabel`=? where `fieldid`=?", array($fieldInfo['label'], $fieldModel->getId()));
            }else{
                $fieldModel = new Vtiger_Field();
                $fieldModel->table = 'vtiger_'.strtolower($moduleInstance->name);
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
                echo "done!\n<br>";
            }
        }
    }
}

$moduleInstance = Vtiger_Module::getInstance('OrdersTask');
if(!$moduleInstance){
    echo "<h3>The Orders Task module DONT exists </h3>";
}else{
    $listNewFields = [
        'LBL_OPERATIVE_TASK_INFORMATION'=>[
            'calendarcode'=>[
                'label'=>'Calendar Code',
                'columntype'=>'int(11)',
                'uitype'=>10,
                'typeofdata'=>'I~O',
                'related_modules'=>array('CapacityCalendarCounter'),
            ],
            'specialrequest'=>[
                'label'=>'Special Request',
                'columntype'=>'varchar(255)',
                'uitype'=>1,
                'typeofdata'=>'V~O',
            ],
        ],
        'LBL_PERSONNEL'=>[
            'num_of_personal'=>[
                'label'=>'Estimated Number of Personnel',
                'columntype'=>'int(11)',
                'uitype'=>7,
                'typeofdata'=>'I~O',
                'presence'=>1,
            ],
            'est_hours_personnel'=>[
                'label'=>'Est. Hours / Personnel',
                'columntype'=>'decimal(5,2)',
                'uitype'=>7,
                'typeofdata'=>'I~O',
                'presence'=>1,
            ],
            'personnel_type'=>[
                'label'=>'Personnel Type',
                'columntype'=>'text',
                'uitype'=>1991,
                'typeofdata'=>'V~O',
                'presence'=>1,
                'defaultvalue'=>-1,
            ],
        ],
        'LBL_VEHICLES'=>[
            'num_of_vehicle'=>[
                'label'=>'Estimated Number of Vehicles',
                'columntype'=>'int(11)',
                'uitype'=>7,
                'typeofdata'=>'I~O',
                'presence'=>1,
            ],
            'est_hours_vehicle'=>[
                'label'=>'Est. Hours / Vehicle',
                'columntype'=>'decimal(5,2)',
                'uitype'=>7,
                'typeofdata'=>'I~O',
                'presence'=>1,
            ],
            'vehicle_type'=>[
                'label'=>'Vehicle Type',
                'columntype'=>'text',
                'uitype'=>1992,
                'typeofdata'=>'V~O',
                'presence'=>1,
                'defaultvalue'=>'Any Vehicle Type'
            ],
        ],
        'LBL_ADDRESSES'=>[],
        'LBL_CPU'=>[
            'carton_name'=>[
                'label'=>'Name',
                'columntype'=>'varchar(255)',
                'uitype'=>1,
                'typeofdata'=>'V~O',
                'presence'=>1,
            ],
            'cartonqty'=>[
                'label'=>'Carton Qty',
                'columntype'=>'int(11)',
                'uitype'=>7,
                'typeofdata'=>'I~O',
                'presence'=>1,
            ],
            'packingqty'=>[
                'label'=>'Packing Qty',
                'columntype'=>'int(11)',
                'uitype'=>7,
                'typeofdata'=>'I~O',
                'presence'=>1,
            ],
            'unpackingqty'=>[
                'label'=>'Unpacking Qty',
                'columntype'=>'int(11)',
                'uitype'=>7,
                'typeofdata'=>'I~O',
                'presence'=>1,
            ],
        ],
        'LBL_EQUIPMENT'=>[
            'equipment_name'=>[
                'label'=>'Name',
                'columntype'=>'varchar(255)',
                'uitype'=>1,
                'typeofdata'=>'V~O',
                'presence'=>1,
            ],
            'equipmentqty'=>[
                'label'=>'Qty Requested',
                'columntype'=>'int(11)',
                'uitype'=>7,
                'typeofdata'=>'I~O',
                'presence'=>1,
            ],
        ],
    ];

    createFieldsAndBlocks_3424($moduleInstance,$listNewFields);

    //remove Fields
    $listDeleteFields=['cod_amount','crew_number','est_vehicle_number'];

    foreach ($listDeleteFields as $fieldName){
        $removeField = Vtiger_Field_Model::getInstance($fieldName,$moduleInstance);
        if(!$removeField){
            echo "<h3>The $fieldName field DONT exists </h3>";
        }else{
            $removeField->delete();
        }

    }

    //Update Position of Blocks
    $arrayMoveBlock = ['LBL_PERSONNEL','LBL_Vehicles','LBL_Addresses','LBL_CPU','LBL_Equipment'];
    $jumpSeq = count($arrayMoveBlock);
    $tabid = getTabid('OrdersTask');

    $operativeTaskBlock = Vtiger_Block::getInstance('LBL_OPERATIVE_TASK_INFORMATION',$moduleInstance);
    $adb->pquery("UPDATE vtiger_blocks SET sequence = (sequence + $jumpSeq) WHERE tabid = $tabid AND sequence > ? ",[$operativeTaskBlock->sequence]);
    $numCount = 1;
    foreach ($arrayMoveBlock as $index => $blockLabel){
        $newSeq = $operativeTaskBlock->sequence + $numCount;
        $adb->pquery("UPDATE vtiger_blocks SET sequence = ? WHERE tabid = $tabid AND blocklabel = ?",[$newSeq,$blockLabel]);
        $numCount ++;
    }
    $newRS = $adb->pquery("SELECT * FROM vtiger_blocks WHERE  tabid = $tabid AND sequence > ? ORDER BY sequence ASC",[$newSeq]);
    while ($row = $adb->fetchByAssoc($newRS)){
        $newSeq = $operativeTaskBlock->sequence + $numCount;
        $adb->pquery("UPDATE vtiger_blocks SET sequence = ? WHERE blockid = ? ",[$newSeq,$row['blockid']]);
        $numCount ++;
    }

    //Update position of fields for LBL_OPERATIVE_TASK_INFORMATION Block
    $operativeTaskBlockId = $operativeTaskBlock->id;
    $adb->pquery("UPDATE vtiger_field SET sequence = 1 WHERE fieldname = 'ordersid' AND block = ?",[$operativeTaskBlockId]);
    $adb->pquery("UPDATE vtiger_field SET sequence = 2 WHERE fieldname = 'agentid' AND block = ?",[$operativeTaskBlockId]);
    $adb->pquery("UPDATE vtiger_field SET sequence = 3 WHERE fieldname = 'operations_task' AND block = ?",[$operativeTaskBlockId]);
    $adb->pquery("UPDATE vtiger_field SET sequence = 4 WHERE fieldname = 'business_line' AND block = ?",[$operativeTaskBlockId]);
    $adb->pquery("UPDATE vtiger_field SET sequence = 5 WHERE fieldname = 'date_spread' AND block = ?",[$operativeTaskBlockId]);
    $adb->pquery("UPDATE vtiger_field SET sequence = 6 WHERE fieldname = 'multiservice_date' AND block = ?",[$operativeTaskBlockId]);
    $adb->pquery("UPDATE vtiger_field SET sequence = 7 WHERE fieldname = 'include_saturday' AND block = ?",[$operativeTaskBlockId]);
    $adb->pquery("UPDATE vtiger_field SET sequence = 8 WHERE fieldname = 'include_sunday' AND block = ?",[$operativeTaskBlockId]);
    $adb->pquery("UPDATE vtiger_field SET sequence = 9 WHERE fieldname = 'service_date_from' AND block = ?",[$operativeTaskBlockId]);
    $adb->pquery("UPDATE vtiger_field SET sequence = 10 WHERE fieldname = 'service_date_to' AND block = ?",[$operativeTaskBlockId]);
    $adb->pquery("UPDATE vtiger_field SET sequence = 11 WHERE fieldname = 'pref_date_service' AND block = ?",[$operativeTaskBlockId]);
    $adb->pquery("UPDATE vtiger_field SET sequence = 12 WHERE fieldname = 'task_start' AND block = ?",[$operativeTaskBlockId]);
    $adb->pquery("UPDATE vtiger_field SET sequence = 14 WHERE fieldname = 'participating_agent' AND block = ?",[$operativeTaskBlockId]);
    $adb->pquery("UPDATE vtiger_field SET sequence = 15 WHERE fieldname = 'calendarcode' AND block = ?",[$operativeTaskBlockId]);
    $adb->pquery("UPDATE vtiger_field SET sequence = 16 WHERE fieldname = 'estimated_hours' AND block = ?",[$operativeTaskBlockId]);
    $adb->pquery("UPDATE vtiger_field SET sequence = 17 WHERE fieldname = 'specialrequest' AND block = ?",[$operativeTaskBlockId]);
    $adb->pquery("UPDATE vtiger_field SET sequence = 18 WHERE fieldname = 'notes_to_dispatcher' AND block = ?",[$operativeTaskBlockId]);
    $adb->pquery("UPDATE vtiger_field SET sequence = 19 WHERE fieldname = 'service_provider_notes' AND block = ?",[$operativeTaskBlockId]);
    $adb->pquery("UPDATE vtiger_field SET sequence = 20 WHERE fieldname = 'cancel_task' AND block = ?",[$operativeTaskBlockId]);
    $adb->pquery("UPDATE vtiger_field SET sequence = 21 WHERE fieldname = 'reason_cancelled' AND block = ?",[$operativeTaskBlockId]);

    // Create extra tables
    echo "<h3>Beginning Create vtiger_orderstask_extra table</h3>";
    if(!Vtiger_Utils::CheckTable('vtiger_orderstask_extra')) {
        Vtiger_Utils::CreateTable(
            'vtiger_orderstask_extra',
            "(extraid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				orderstaskid INT(19) NOT NULL,
				blocklabel varchar(255) NOT NULL,
				sequence INT(3) NOT NULL,
                fieldname varchar(255) NOT NULL,
                fieldvalue text)",
            true);
    }else{
        echo "<br>vtiger_orderstask_extra table already exists.</br>";
    }

    // Update field attributes
    $adb->pquery("UPDATE vtiger_field SET typeofdata = 'V~M' WHERE fieldname = 'equipment_name' AND tabid = ?",[$tabid]);
    $adb->pquery("UPDATE vtiger_field SET uitype = 10 WHERE fieldname = 'equipment_name' AND tabid = ?",[$tabid]);
    $equipmentField = Vtiger_Field_Model::getInstance('equipment_name',$moduleInstance);
    if($equipmentField){
        $equipmentField->setRelatedModules(['Equipment']);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";