<?php

class ExtraStops_Field_Model extends Vtiger_Field_Model{
    /**TODO: field validator need to be handled in specific module getValidator api  **/
    public function getValidator()
    {
        $validator = [];

        $typeOfData = $this->get('typeofdata');
        $components = explode('~', $typeOfData);

        if($components[0] == 'I' && $components[2] == 'MIN=0'){
            $validator[] = ['name'   => 'WholeNumber'];
            return $validator;
        }else{
            return parent::getValidator();
        }

    }
}

