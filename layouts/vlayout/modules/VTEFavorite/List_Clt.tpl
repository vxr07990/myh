    {*/* ********************************************************************************
* The content of this file is subject to the VTEFavorite ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
    <style>
        .vteFavDiv a{
            color:{$BASE_COLOR} !important;
        }
    </style>
<div class="vteFavDiv" >
{assign var=ARR_MODULES value=$RECORDS.arrModules}
{if not $ARR_MODULES}
    {vtranslate("No custom", "VTEFavorite")}
{/if}
{foreach from=$ARR_MODULES item=MODULE}
		{assign var=ARR_RECORDS value=$MODULE.arrRecords}
		{assign var=ARR_FIELDS value=$MODULE.arrFields}
		<div >
			<div class="widget_header row-fluid">
				<div class="span9">
				    <h3><a href="index.php?module={$MODULE.name}&parent=&page=1&view=List&viewname={$MODULE.cvid}" target="_blank">
                            <b>{vtranslate({$MODULE.name}, {$MODULE.name})} ({vtranslate({$MODULE.cvname}, {$MODULE.cvname})})</b></a></h3>
				</div>
			</div>	
		</div>
		{if ! $ARR_FIELDS}
			<a href="?module=VTEFavorite&parent=Settings&view=Settings&type=recently" >	Please add fields for show list.</a>
		{else}
		    <table  class="table table-bordered listViewEntriesTable vtetable">
						<tr>
						{foreach from=$ARR_FIELDS item=FIELD}
							<th>{vtranslate({$FIELD->get('label')},{$MODULE.name})}</th>
						{/foreach}
						</tr>
						{foreach from=$ARR_RECORDS item=RECORD}
						<tr class="vteFavdropdown">
							{foreach from=$ARR_FIELDS item=FIELD}
								{if $FIELD->isReferenceField()}
									{assign var=RModule value=$FIELD->getReferenceList()}	
									{if $RECORD['Record']->get($FIELD->get('name'))}
										{assign var=RRecord value=Vtiger_Record_Model::getInstanceById($RECORD['Record']->get($FIELD->get('name')),$RModule[0])}	
									
										<td class="listViewEntryValue medium "> <a href="{$RRecord->getDetailViewUrl()}" >  {$RECORD['Record']->getDisplayValue($FIELD->get('name'))} </a> </td>
									{else}
										<td class="listViewEntryValue medium "> <a href="{$RECORD['Record']->getDetailViewUrl()}" > &nbsp </a> </td>
									{/if}
								{else}
									<td class="listViewEntryValue medium">
                                        <a href="{$RECORD['Record']->getDetailViewUrl()}" >
                                            {if strpos($RECORD['Record']->getDisplayValue($FIELD->get('name')), '0000')===false}
                                                {$RECORD['Record']->getDisplayValue($FIELD->get('name'))}
                                            {/if}
                                        </a>
                                    </td>
								{/if}
							{/foreach}
						</tr>
						{/foreach}
					</table>
			{/if}

{/foreach}			
</div >
{/strip}

