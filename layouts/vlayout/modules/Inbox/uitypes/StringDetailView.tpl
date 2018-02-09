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
{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'), $RECORD->getId(), $RECORD)}
	{if $LINK_DATA['status'] eq 0 and ($USER_DEPTH eq 6 or $USER_DEPTH EQ 7) and $FIELD_MODEL->getName() eq 'inbox_message'}
		<br>
		<br>
		<b>Current Status:</b> Pending
		<br>
		<br>
		<button paid='{$RECORD->get("inbox_link")}' status='1' inboxid='{$RECORD->get("record_id")}' class='updateStatus'>Accept</button>
		<button paid='{$RECORD->get("inbox_link")}' status='3' inboxid='{$RECORD->get("record_id")}' class='updateStatus'>Decline</button>
	{elseif $LINK_DATA['status'] eq 1 and ($USER_DEPTH eq 6 or $USER_DEPTH EQ 7) and $FIELD_MODEL->getName() eq 'inbox_message'}
		<br>
		<br>
		<b>Current Status:</b> <span class='greenColor'>Accpeted</span>
		<br>
		<br>
		<b>This was accepted by: {$LINK_DATA['modified_by_first_name']} {$LINK_DATA['modified_by_last_name']}</b>
	{elseif $LINK_DATA['status'] eq 2 and ($USER_DEPTH eq 6 or $USER_DEPTH EQ 7) and $FIELD_MODEL->getName() eq 'inbox_message'}
		<br>
		<br>
		Current Status: Removed
		<br>
		<br>
		<b>You have been removed from this Participating Agent Request by {$LINK_DATA['modified_by_first_name']} {$LINK_DATA['modified_by_last_name']}</b>
	{elseif $LINK_DATA['status'] eq 3 and ($USER_DEPTH eq 6 or $USER_DEPTH EQ 7) and $FIELD_MODEL->getName() eq 'inbox_message'}
		<br>
		<br>
		<b>Current Status:</b><span class='redColor'> Declined By {$LINK_DATA['modified_by_first_name']} {$LINK_DATA['modified_by_last_name']}</span>
		<br>
		<br>
	{/if}