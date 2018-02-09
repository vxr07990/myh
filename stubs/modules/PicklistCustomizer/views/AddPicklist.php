<?php

class PicklistCustomizer_ViewName_View extends PicklistCustomizer_Edit_View
{

    /**
     * PicklistCustomizer_ViewName_View constructor.
     *
     * Initializes the view with the correct module name and fieldname by calling parent
     *
     * <PicklistModuleName> should be replaced with the module that contains the picklist
     * <picklist_fieldname> should be replaced by the picklist's fieldname value from vtiger_field
     *
     */
    public function __construct()
    {
        parent::__construct('<PicklistModuleName>', '<picklist_fieldname>');
    }
}
