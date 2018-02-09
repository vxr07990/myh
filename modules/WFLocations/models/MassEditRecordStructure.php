<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Mass Edit Record Structure Model
 */
class WFLocations_MassEditRecordStructure_Model extends Vtiger_MassEditRecordStructure_Model
{
    //Determines if a fields shows up in mass edit for a given location type.
    // true  = show
    // false = hide
    protected $fieldVisibilityRules = [
        'vault'          => [
            'tag'                  => false,
            'wflocation_warehouse' => false,
            'wflocation_type'      => false,
            'pre'                  => true,
            'post'                 => true,
            'name'                 => false,
            'wflocations_status'   => true,
            'description'          => true,
            'wflocation_base'      => true,
            'create_multiple'      => false,
            'range_from'           => false,
            'range_to'             => false,
            'row'                  => false,
            'row_to'               => false,
            'bay'                  => false,
            'bay_to'               => false,
            'level'                => false,
            'level_to'             => false,
            'wfslot_configuration' => false,
            'reserved'             => true,
            'offsite'              => true,
            'squarefeet'           => true,
            'cubefeet'             => true,
            'cost'                 => true,
            'agentid'              => true,
            'assigned_user_id'     => true,
            'vault_capacity'       => false,
            'double_high'          => false,
        ],
        'floor'          => [
            'tag'                  => false,
            'wflocation_warehouse' => false,
            'wflocation_type'      => false,
            'pre'                  => true,
            'post'                 => true,
            'name'                 => false,
            'wflocations_status'   => true,
            'description'          => true,
            'wflocation_base'      => false,
            'create_multiple'      => false,
            'range_from'           => false,
            'range_to'             => false,
            'row'                  => false,
            'row_to'               => false,
            'bay'                  => false,
            'bay_to'               => false,
            'level'                => false,
            'level_to'             => false,
            'wfslot_configuration' => false,
            'reserved'             => true,
            'offsite'              => true,
            'squarefeet'           => true,
            'cubefeet'             => true,
            'cost'                 => true,
            'agentid'              => true,
            'assigned_user_id'     => true,
            'vault_capacity'       => true,
            'double_high'          => false,
        ],
        'cage'           => [
            'tag'                  => false,
            'wflocation_warehouse' => false,
            'wflocation_type'      => false,
            'pre'                  => true,
            'post'                 => true,
            'name'                 => false,
            'wflocations_status'   => true,
            'description'          => true,
            'wflocation_base'      => false,
            'create_multiple'      => false,
            'range_from'           => false,
            'range_to'             => false,
            'row'                  => false,
            'row_to'               => false,
            'bay'                  => false,
            'bay_to'               => false,
            'level'                => false,
            'level_to'             => false,
            'wfslot_configuration' => false,
            'reserved'             => true,
            'offsite'              => true,
            'squarefeet'           => true,
            'cubefeet'             => true,
            'cost'                 => true,
            'agentid'              => true,
            'assigned_user_id'     => true,
            'vault_capacity'       => false,
            'double_high'          => false,
        ],
        'rack'           => [
            'tag'                  => false,
            'wflocation_warehouse' => false,
            'wflocation_type'      => false,
            'pre'                  => true,
            'post'                 => true,
            'name'                 => false,
            'wflocations_status'   => true,
            'description'          => true,
            'wflocation_base'      => false,
            'create_multiple'      => false,
            'range_from'           => false,
            'range_to'             => false,
            'row'                  => false,
            'row_to'               => false,
            'bay'                  => false,
            'bay_to'               => false,
            'level'                => false,
            'level_to'             => false,
            'wfslot_configuration' => true,
            'reserved'             => true,
            'offsite'              => true,
            'squarefeet'           => true,
            'cubefeet'             => true,
            'cost'                 => true,
            'agentid'              => true,
            'assigned_user_id'     => true,
            'vault_capacity'       => false,
            'double_high'          => true,
        ],
        'record Storage' => [
            'tag'                  => false,
            'wflocation_warehouse' => false,
            'wflocation_type'      => false,
            'pre'                  => true,
            'post'                 => true,
            'name'                 => false,
            'wflocations_status'   => true,
            'description'          => true,
            'wflocation_base'      => false,
            'create_multiple'      => false,
            'range_from'           => false,
            'range_to'             => false,
            'row'                  => false,
            'row_to'               => false,
            'bay'                  => false,
            'bay_to'               => false,
            'level'                => false,
            'level_to'             => false,
            'wfslot_configuration' => true,
            'reserved'             => true,
            'offsite'              => true,
            'squarefeet'           => true,
            'cubefeet'             => true,
            'cost'                 => true,
            'agentid'              => true,
            'assigned_user_id'     => true,
            'vault_capacity'       => false,
            'double_high'          => false,
        ],
        'trailer'        => [
            'tag'                  => false,
            'wflocation_warehouse' => false,
            'wflocation_type'      => false,
            'pre'                  => true,
            'post'                 => true,
            'name'                 => false,
            'wflocations_status'   => true,
            'description'          => true,
            'wflocation_base'      => false,
            'create_multiple'      => false,
            'range_from'           => false,
            'range_to'             => false,
            'row'                  => false,
            'row_to'               => false,
            'bay'                  => false,
            'bay_to'               => false,
            'level'                => false,
            'level_to'             => false,
            'wfslot_configuration' => false,
            'reserved'             => true,
            'offsite'              => true,
            'squarefeet'           => true,
            'cubefeet'             => true,
            'cost'                 => true,
            'agentid'              => true,
            'assigned_user_id'     => true,
            'vault_capacity'       => false,
            'double_high'          => false,
        ],
        'pallet'         => [
            'tag'                  => false,
            'wflocation_warehouse' => false,
            'wflocation_type'      => false,
            'pre'                  => true,
            'post'                 => true,
            'name'                 => false,
            'wflocations_status'   => true,
            'description'          => true,
            'wflocation_base'      => true,
            'create_multiple'      => false,
            'range_from'           => false,
            'range_to'             => false,
            'row'                  => false,
            'row_to'               => false,
            'bay'                  => false,
            'bay_to'               => false,
            'level'                => false,
            'level_to'             => false,
            'wfslot_configuration' => false,
            'reserved'             => true,
            'offsite'              => true,
            'squarefeet'           => true,
            'cubefeet'             => true,
            'cost'                 => true,
            'agentid'              => true,
            'assigned_user_id'     => true,
            'vault_capacity'       => false,
            'double_high'          => false,
        ],
        'default'         => [
            'tag'                  => false,
            'wflocation_warehouse' => false,
            'wflocation_type'      => false,
            'pre'                  => true,
            'post'                 => true,
            'name'                 => true,
            'wflocations_status'   => true,
            'description'          => true,
            'wflocation_base'      => true,
            'create_multiple'      => false,
            'range_from'           => false,
            'range_to'             => false,
            'row'                  => false,
            'row_to'               => false,
            'bay'                  => false,
            'bay_to'               => false,
            'level'                => false,
            'level_to'             => false,
            'wfslot_configuration' => false,
            'reserved'             => true,
            'offsite'              => true,
            'squarefeet'           => true,
            'cubefeet'             => true,
            'cost'                 => true,
            'agentid'              => true,
            'assigned_user_id'     => true,
            'vault_capacity'       => false,
            'double_high'          => false,
        ],
    ];

    /**
     * Function to get the values in stuctured format
     * @return <array> - values in structure array('block'=>array(fieldinfo));
     */
    public function getStructure($locationType = false)
    {
        if (!empty($this->structuredValues)) {
            return $this->structuredValues;
        }

        $overrideRules = $this->getLocationTypeFieldRules($locationType);

        $values = array();
        $recordModel = $this->getRecord();
        $recordExists = !empty($recordModel);
        $moduleModel = $this->getModule();
        $blockModelList = $moduleModel->getBlocks();
        foreach ($blockModelList as $blockLabel=>$blockModel) {
            $fieldModelList = $blockModel->getFields();
            if (!empty($fieldModelList)) {
                $values[$blockLabel] = array();
                foreach ($fieldModelList as $fieldName=>$fieldModel) {
                    if ($fieldModel->isEditable() && $fieldModel->isMassEditable()) {
                        if ($fieldModel->isViewable() && $this->isFieldRestricted($fieldModel)) {
                            if ($recordExists) {
                                $fieldModel->set('fieldvalue', $recordModel->get($fieldName));
                            }
                            if($overrideRules[$fieldName]){
                                $values[$blockLabel][$fieldName] = $fieldModel;
                            }
                        }
                    }
                }
            }
        }
        $this->structuredValues = $values;
        return $values;
    }

    /*
     * Function that returns special rules depending on location type
     *	@params string
     *  @returns array
     */
    public function getLocationTypeFieldRules($locationType)
    {
        return array_key_exists($locationType, $this->fieldVisibilityRules) ? $this->fieldVisibilityRules[$locationType] : $this->fieldVisibilityRules['default'];
    }
}
