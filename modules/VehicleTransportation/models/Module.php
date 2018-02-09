<?php
//OT 1812, Module should hide some fields when called by Orders but not by Estimates

class VehicleTransportation_Module_Model extends Vtiger_Module_Model
{
    public function setPropertiesForBlock($hostModule)
    {
        $db = PearDatabase::getInstance();
        $moduleName = $this->getName();
        //hit database to grab id column and table name
        $row = $db->pquery("
			SELECT entityidfield, tablename FROM `vtiger_entityname`
			WHERE modulename = ?", [$moduleName]
        )->fetchRow();
        $this->idColumn = $row['entityidfield'];
        $this->blockTable = $row['tablename'];
        //label of guest block
        //$this->guestBlock = 'LBL_' . strtoupper($moduleName) .'_INFORMATION';
        //query db for labels of guest blocks
        $this->guestBlocks = [];
        $result = $db->pquery("
			SELECT `vtiger_blocks`.blocklabel FROM `vtiger_guestmodulerel` 
			INNER JOIN `vtiger_blocks` ON `vtiger_blocks`.blockid = `vtiger_guestmodulerel`.blockid 
			WHERE guestmodule = ? AND hostmodule = ? AND active = 1", [$moduleName, $hostModule]
        );
        while ($row =& $result->fetchRow()) {
            $this->guestBlocks[] = $row['blocklabel'];
        }
        $this->guestFields = [];
        foreach ($this->guestBlocks as $guestBlock) {
            $blockFields = $this->getFields($guestBlock);
            if (is_array($blockFields)) {
                $this->guestFields = $this->guestFields + $blockFields;
            }
        }
        //TODO: make labels for guest modules
        $this->guestLabel = 'LBL_' . strtoupper($moduleName) .'_GUEST';
        //hit the database again to grab the UI type 10 that links the 2 modules
        $this->linkColumn = $db->pquery("SELECT fieldname FROM `vtiger_field` INNER JOIN `vtiger_fieldmodulerel` ON `vtiger_field`.fieldid = `vtiger_fieldmodulerel`.fieldid WHERE module = ? AND relmodule = ?", [$moduleName, $hostModule])->fetchRow()['fieldname'];
        //fields to be removed from guest block (should always include linkColumn)
        if ($hostModule == 'Orders') {
            $this->restrictedFields = [
                'assigned_user_id',
                'agentid',
                'createdtime',
                'modifiedtime',
                'modifiedby',
                'createdby',
                'created_user_id',
                'salutationtype',
                'vehicletrans_miles',
                'vehicletrans_diversions',
                'vehicletrans_valamount',
                'vehicletrans_ot',
                'vehicletrans_sitdays',
                'vehicletrans_sitmiles',
                
                $this->linkColumn,
            ];
        } else {
            $this->restrictedFields = [
                'assigned_user_id',
                'agentid',
                'createdtime',
                'modifiedtime',
                'modifiedby',
                'createdby',
                'created_user_id',
                'salutationtype',
                $this->linkColumn,
            ];
        }
    }

}
