<?php
// OT Defect 11769
class Opportunities_Relation_Model extends Vtiger_Relation_Model
{
    public function getListUrl($parentRecordModel)
    {
        if ($this->get('modulename')=='Cubesheets') {
            return 'module='.$this->getParentModuleModel()->get('name').'&relatedModule='.$this->get('modulename').
                '&view=Detail&record='.$parentRecordModel->getId().'&mode=showRelatedList&orderby=ModifiedTime&sortorder=DESC';
        } else {
            return 'module='.$this->getParentModuleModel()->get('name').'&relatedModule='.$this->get('modulename').
                '&view=Detail&record='.$parentRecordModel->getId().'&mode=showRelatedList';
        }
    }
}
