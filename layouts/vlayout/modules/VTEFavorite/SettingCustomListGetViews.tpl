{*/* ********************************************************************************
* The content of this file is subject to the VTEFavorite ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
 
{strip}
	{assign var=SELECTED_FIRT value="selected"}
	{foreach item=BLOCK_MODEL from=$CUSTOMVIEWS}
		
		<option value="{$BLOCK_MODEL.cvid}" {$SELECTED_FIRT} >{vtranslate($BLOCK_MODEL.viewname , $BLOCK_MODEL.entitytype)}</option>
		{assign var=SELECTED_FIRT value=""}
	{/foreach}

{/strip}
 
