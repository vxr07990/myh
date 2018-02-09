<?php

/**
 * SignedRecord_Record_Model Class
 */
class SignedRecord_Record_Model extends Vtiger_Record_Model
{

    /**
     * const
     */
    const TYPE_SIGNED = 'Signed';
    const TYPE_OPENED = 'Opened';

    public function getName()
    {
        $displayName = $this->get('filename');
        $displayName = str_replace('storage/QuotingTool/','',$displayName);
        return $displayName;
    }
}