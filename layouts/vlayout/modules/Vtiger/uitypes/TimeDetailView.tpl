{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}

{* TODO: Review the order of parameters - good to eliminate $RECORD->getId, $RECORD should be used *}

{assign var=TIMEZONE_VALUE value=getFieldTimeZoneValue($FIELD_MODEL->getFieldName(), $RECORD->getId())}
{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'), $RECORD->getId(), $RECORD)}
{assign var=TYPEOFDATA value=explode('~', $FIELD_MODEL->get('typeofdata'))}
{if count($TYPEOFDATA) gt 3 AND $TYPEOFDATA[2] eq 'REL'}
    {assign var=DATE_FIELD value=$TYPEOFDATA[3]}
{else}
    {assign var=DATE_FIELD value=''}
{/if}
{getTimeZoneDisplayValueFromFieldNames($TIMEZONE_VALUE,$DATE_FIELD, $FIELD_MODEL->getFieldName(), $RECORD->getId())}
