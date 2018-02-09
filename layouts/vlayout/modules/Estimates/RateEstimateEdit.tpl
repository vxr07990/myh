{strip}
{assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST['INTERSTATE_SERVICES'])}
<div id="contentHolder_INTERSTATE_SERVICES" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
{if $HAS_CONTENT}
{assign var=IS_HIDDEN value='1'}
{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
	{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}

		{if !$BLOCK_LABEL|in_array:$ALLOWED_BLOCKS}
			{continue}
		{/if}

		{if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
		<table {if $BLOCK_LABEL eq 'LBL_QUOTES_ACCESSORIALDETAILS'}id='acc_table' {else if $BLOCK_LABEL eq 'LBL_ESTIMATES_EXTRASTOPS'}id='extra_stops' {else if $BLOCK_LABEL eq 'LBL_QUOTES_SITDETAILS'}id='sit_table' {else if $BLOCK_LABEL eq 'LBL_QUOTES_SITDETAILS2'}id='sit2_table' {else if $BLOCK_LABEL eq 'LBL_QUOTES_STAIR'}id='stair_table' {else if $BLOCK_LABEL eq 'LBL_QUOTES_LONGCARRY'}id='longcarry_table' {else if $BLOCK_LABEL eq 'LBL_QUOTES_ELEVATOR'}id='elevator_table' {/if}class="table table-bordered blockContainer equalSplit showInlineTable{if $BLOCK_LABEL eq "LBL_QUOTES_SITDETAILS" or $BLOCK_LABEL eq "LBL_QUOTES_ACCESSORIALDETAILS"} sit{/if}">
		<thead>
		<tr>
			<th class="blockHeader" colspan="4">
				<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide">
				<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show">
				&nbsp;&nbsp;{vtranslate($BLOCK_LABEL, $MODULE)}
			</th>
		</tr>
		</thead>
		<tbody{if $IS_HIDDEN} class="hide" {/if}>
		{if $BLOCK_LABEL eq "LBL_QUOTES_SITDETAILS"}
			{if getenv('INSTANCE_NAME') == 'sirva'}
				<tr>
					<td class="fieldLabel">
						<label class="muted pull-right marginRight10px">Use Custom Rates - Origin</label>
					</td>
					<td class="fieldValue" style="text-align:left">
						<input type="hidden" name="apply_custom_sit_rate_override" value="{($CUSTOM_SIT['origin'])?$CUSTOM_SIT['origin']:0}">
						<input type="checkbox" class="sit_override "  data-location="origin"  name="apply_custom_sit_rate_override" {if $CUSTOM_SIT['origin'] == 1}checked{/if}>
						<a href="javascript:void(0)" class="btn pull-right marginBottom5px btnLoadTariff loadTariffSit {if !$CUSTOM_SIT['origin']}hide{/if}" data-location="origin" >Load Tariff</a>
					</td>
					<td class="fieldLabel">
						<label class="muted pull-right marginRight10px">Use Custom Rates - Destination</label>
					</td>
					<td class="fieldValue" style="text-align:left">
						<input type="hidden" name="apply_custom_sit_rate_override_dest" value="{($CUSTOM_SIT['destination'])?$CUSTOM_SIT['destination']:0}">
						<input type="checkbox" class="sit_override" data-location="dest" name="apply_custom_sit_rate_override_dest" {if $CUSTOM_SIT['destination'] == 1}checked{/if}>
						<a href="javascript:void(0)" class="btn pull-right marginBottom5px btnLoadTariff loadTariffSit {if !$CUSTOM_SIT['destination']}hide{/if}" data-location="dest" >Load Tariff</a>
					</td>
				</tr>
			{/if}
			<tr class='cbxblockhead'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
		{/if}
		<tr{if $BLOCK_LABEL eq "LBL_QUOTES_ACCESSORIALDETAILS"} id='shuttleRow_1'{/if}>
		{assign var=COUNTER value=0}

        {assign var=PRICING_DONE value=false}
		{assign var=ROW_HAS_CLASS value=false}
		{assign var=ROW_COUNT value=1}
		{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
			{if getenv('INSTANCE_NAME') == 'sirva'}
				{if $FIELD_MODEL->getName() eq 'acc_ot_origin_weight' || $FIELD_MODEL->getName() eq 'acc_ot_dest_weight' || $FIELD_MODEL->getName() eq 'acc_ot_origin_applied' || $FIELD_MODEL->getName() eq 'acc_ot_dest_applied' || $FIELD_NAME eq 'accesorial_ot_packing' OR $FIELD_NAME eq 'accesorial_ot_unpacking'}{continue}{/if}
			{/if}
			{if getenv('INSTANCE_NAME') == 'graebel'}
				{if $FIELD_NAME eq 'accesorial_ot_packing' OR $FIELD_NAME eq 'accesorial_ot_unpacking' OR $FIELD_NAME eq 'accessorial_space_reserve_bool' OR $FIELD_NAME eq 'acc_day_certain_fee' OR $FIELD_NAME eq 'acc_day_certain_pickup'}{continue}{/if}
				{if $FIELD_NAME eq 'acc_ot_origin_weight' || $FIELD_NAME eq 'acc_ot_dest_weight' || $FIELD_NAME eq 'acc_ot_origin_applied' || $FIELD_MODEL->getName() eq 'acc_ot_dest_applied'}{continue}{/if}
			{/if}

			{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
			{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
				{if $COUNTER eq '1'}
					<td class="{$WIDTHTYPE}"></td><td class="{$WIDTH_TYPE_CLASSSES[$WIDTHTYPE]}"></td></tr><tr>
					{assign var=COUNTER value=0}
				{/if}
			{/if}

			{if $FIELD_MODEL->getName() eq 'acc_shuttle_origin_weight'}
				 {assign var=ROW_HAS_CLASS value=true}
				 {assign var=ROW_CLASS value='shuttleRow'}
				 {assign var=ROW_COUNT value=0}
			{else if $FIELD_MODEL->getName() eq 'acc_ot_origin_weight'}
				 {assign var=ROW_HAS_CLASS value=true}
				 {assign var=ROW_CLASS value='otServiceRow'}
				 {assign var=ROW_COUNT value=0}
			{else if $FIELD_MODEL->getName() eq 'acc_selfstg_origin_weight'}
				 {assign var=ROW_HAS_CLASS value=true}
				 {assign var=ROW_CLASS value='selfStgRow'}
				 {assign var=ROW_COUNT value=0}
			{else if $FIELD_MODEL->getName() eq 'acc_exlabor_origin_hours'}
				 {assign var=ROW_HAS_CLASS value=true}
				 {assign var=ROW_CLASS value='exLaborRow'}
				 {assign var=ROW_COUNT value=0}
			{else if $FIELD_MODEL->getName() eq 'acc_wait_origin_hours'}
				 {assign var=ROW_HAS_CLASS value=true}
				 {assign var=ROW_CLASS value='waitRow'}
				 {assign var=ROW_COUNT value=0}
			{else if $FIELD_MODEL->getName() eq 'bulky_article_changes'}
				 {assign var=ROW_HAS_CLASS value=true}
				 {assign var=ROW_CLASS value='bulkyArticleRow'}
				 {assign var=ROW_COUNT value=0}
			{else if $FIELD_MODEL->getName() eq 'rush_shipment_fee' ||
				($FIELD_MODEL->getName() eq 'accesorial_ot_loading')}
				 {assign var=ROW_HAS_CLASS value=true}
				 {assign var=ROW_CLASS value='pricingRow'}
				 {assign var=ROW_COUNT value=0}
			{elseif $FIELD_MODEL->getName() eq 'gsa500_supervisory_hours_origin_regular'}
				{assign var=ROW_HAS_CLASS value=true}
				{assign var=ROW_CLASS value='gsa500SuperRow'}
				{assign var=ROW_COUNT value=0}
			{elseif $FIELD_MODEL->getName() eq 'gsa500_extra_driver_hours'}
				{assign var=ROW_HAS_CLASS value=true}
				{assign var=ROW_CLASS value='gsa500DriverRow'}
				{assign var=ROW_COUNT value=0}
			{elseif $FIELD_MODEL->getName() eq 'gsa500_washing_machine_employee'}
				{assign var=ROW_HAS_CLASS value=true}
				{assign var=ROW_CLASS value='gsa500MachineRow'}
				{assign var=ROW_COUNT value=0}
			{else if $FIELD_MODEL->getName() eq 'acc_debris_reg'}
				{assign var=ROW_HAS_CLASS value=true}
				{assign var=ROW_CLASS value='debrisRow'}
				{assign var=ROW_COUNT value=0}
			{* OT 15866, hiding number of days fields in edit view but not detail view *}
			{else if getenv('INSTANCE_NAME') == 'graebel' && ($FIELD_MODEL->getName() eq 'sit_origin_number_days' ||
				$FIELD_MODEL->getName() eq 'sit_dest_number_days')}
				{continue}
			{/if}

			{if $COUNTER eq 2 || $ROW_COUNT eq 0}
				{if $COUNTER eq 1}
					<td class='fieldLabel'></td><td></td></tr>
				{else}
					{assign var=COUNTER value=1}
				{/if}
				{assign var=ROW_COUNT value=$ROW_COUNT+1}
				</tr><tr{if $ROW_HAS_CLASS} id='{$ROW_CLASS}_{$ROW_COUNT}'{/if}>
			{else}
				{assign var=COUNTER value=$COUNTER+1}
			{/if}
			{if $FIELD_MODEL->getName() eq 'acc_shuttle_origin_weight'}
				{if $COUNTER eq 2}
					<td class='fieldLabel'></td><td></td></tr>
					{assign var=COUNTER value=1}
				{/if}
				 <td style='background-color:#E8E8E8; padding-left:30px;' colspan=4>- Shuttle Service</td></tr>
				 {assign var=ROW_COUNT value=$ROW_COUNT+1}
				 <tr id='{$ROW_CLASS}_{$ROW_COUNT}' class='cbxblockhead'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
				 {assign var=ROW_COUNT value=$ROW_COUNT+1}
				 <tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
			{else if $FIELD_MODEL->getName() eq 'acc_ot_origin_weight'}
				{if $COUNTER eq 2}
					<td class='fieldLabel'></td><td></td></tr>
					{assign var=COUNTER value=1}
				{/if}
				 <td style='background-color:#E8E8E8; padding-left:30px;' colspan=4>- OT Service</td></tr>
				 {assign var=ROW_COUNT value=$ROW_COUNT+1}
				 <tr id='{$ROW_CLASS}_{$ROW_COUNT}' class='cbxblockhead'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
				 {assign var=ROW_COUNT value=$ROW_COUNT+1}
				 <tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
			{else if $FIELD_MODEL->getName() eq 'acc_selfstg_origin_weight'}
				{if $COUNTER eq 2}
					<td class='fieldLabel'></td><td></td></tr>
					{assign var=COUNTER value=1}
				{/if} <td style='background-color:#E8E8E8; padding-left:30px;' colspan=4>- Self/Mini Stg</td></tr>
				 {assign var=ROW_COUNT value=$ROW_COUNT+1}
				 <tr id='{$ROW_CLASS}_{$ROW_COUNT}' class='cbxblockhead'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
				 {assign var=ROW_COUNT value=$ROW_COUNT+1}
				 <tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
			{else if $FIELD_MODEL->getName() eq 'acc_exlabor_origin_hours'}
				{if $COUNTER eq 2}
					<td class='fieldLabel'></td><td></td></tr>
					{assign var=COUNTER value=1}
				{/if}
				 <td style='background-color:#E8E8E8; padding-left:30px;' colspan=4>- Extra Labor</td></tr>
				 {assign var=ROW_COUNT value=$ROW_COUNT+1}
				 <tr id='{$ROW_CLASS}_{$ROW_COUNT}' class='cbxblockhead'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
				 {assign var=ROW_COUNT value=$ROW_COUNT+1}
				 <tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
			{else if $FIELD_MODEL->getName() eq 'acc_wait_origin_hours'}
				{if $COUNTER eq 2}
					<td class='fieldLabel'></td><td></td></tr>
					{assign var=COUNTER value=1}
				{/if}
				 <td style='background-color:#E8E8E8; padding-left:30px;' colspan=4>- Wait Time</td></tr>
				 {assign var=ROW_COUNT value=$ROW_COUNT+1}
				 <tr id='{$ROW_CLASS}_{$ROW_COUNT}' class='cbxblockhead'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
				 {assign var=ROW_COUNT value=$ROW_COUNT+1}
				 <tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
			{else if $FIELD_MODEL->getName() eq 'bulky_article_changes'}
				{if $COUNTER eq 2}
					<td class='fieldLabel'></td><td></td></tr>
					{assign var=COUNTER value=1}
				{/if}
				<td style='background-color:#E8E8E8; padding-left:30px;' colspan=4>- Bulky Articles</td></tr>
				{assign var=ROW_COUNT value=$ROW_COUNT+1}
				<tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
			{else if ($FIELD_MODEL->getName() eq 'rush_shipment_fee') || ($FIELD_MODEL->getName() eq 'accesorial_ot_loading')}
                {*@TODO: this is is sad.*}
				{if $COUNTER eq 2}
					<td class='fieldLabel'></td><td></td></tr>
				<tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
					{assign var=COUNTER value=1}
				{/if}
                {if (!$PRICING_DONE)}
                    <td style='background-color:#E8E8E8; padding-left:30px;' colspan=4>- Pricing</td></tr>
                    {assign var=ROW_COUNT value=$ROW_COUNT+1}
                    {assign var=PRICING_DONE value=true}
                    <tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
                {/if}
			{else if $FIELD_MODEL->getName() eq 'gsa500_extra_driver_hours'}
				{if $COUNTER eq 2}
					<td class='fieldLabel'></td><td></td></tr>
					{assign var=COUNTER value=1}
				{/if}
				<td style='background-color:#E8E8E8; padding-left:30px;' colspan=4>- Extra Driver</td></tr>
				{assign var=ROW_COUNT value=$ROW_COUNT+1}
				<tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
			{else if $FIELD_MODEL->getName() eq 'gsa500_washing_machine_employee'}
				{if $COUNTER eq 2}
					<td class='fieldLabel'></td><td></td></tr>
					{assign var=COUNTER value=1}
				{/if}
				<td style='background-color:#E8E8E8; padding-left:30px;' colspan=4>- Front Load Washing Machines and Associated Pedestals</td></tr>
				{assign var=ROW_COUNT value=$ROW_COUNT+1}
				<tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
			{else if $FIELD_MODEL->getName() eq 'gsa500_supervisory_hours_origin_regular'}
			{if $COUNTER eq 2}
				<td class='fieldLabel'></td><td></td></tr>
				{assign var=COUNTER value=1}
			{/if}
				 <td style='background-color:#E8E8E8; padding-left:30px;' colspan=4>- Supervisory Personnel</td></tr>
				 {assign var=ROW_COUNT value=$ROW_COUNT+1}
				 <tr id='{$ROW_CLASS}_{$ROW_COUNT}' class='cbxblockhead'><td class='fieldLabel' colspan=2>Origin</td><td class='fieldLabel' colspan=2>Destination</td></tr>
				 {assign var=ROW_COUNT value=$ROW_COUNT+1}
				 <tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
			{else if $FIELD_MODEL->getName() eq 'acc_debris_reg'}
				{if $COUNTER eq 2}
					<td class='fieldLabel'></td><td></td></tr>
					{assign var=COUNTER value=1}
				{/if}
				<td style='background-color:#E8E8E8; padding-left:30px;' colspan=4>- Debris Removal/Minimum Unpacking</td></tr>
				{assign var=ROW_COUNT value=$ROW_COUNT+1}
				<tr id='{$ROW_CLASS}_{$ROW_COUNT}'>
			{/if}
			{if $FIELD_MODEL->getName() eq 'acc_shuttle_origin_ot'}
				{assign var=SPEC_CLASS value=' shuttleOriginOT'}
				{assign var=SPEC_CLASS_HIDE value=' hide shuttleOriginOTHide'}
			{else if $FIELD_MODEL->getName() eq 'acc_shuttle_dest_ot'}
				{assign var=SPEC_CLASS value=' shuttleDestOT'}
				{assign var=SPEC_CLASS_HIDE value=' hide shuttleDestOTHide'}
			{else if $FIELD_MODEL->getName() eq 'acc_shuttle_origin_over25'}
				{assign var=SPEC_CLASS value=' shuttleOriginOver25'}
				{assign var=SPEC_CLASS_HIDE value=' hide shuttleOriginOver25Hide'}
			{else if $FIELD_MODEL->getName() eq 'acc_shuttle_dest_over25'}
				{assign var=SPEC_CLASS value=' shuttleDestOver25'}
				{assign var=SPEC_CLASS_HIDE value=' hide shuttleDestOver25Hide'}
			{else if $FIELD_MODEL->getName() eq 'acc_shuttle_origin_miles'}
				{assign var=SPEC_CLASS value=' shuttleOriginMiles'}
				{assign var=SPEC_CLASS_HIDE value=' hide shuttleOriginMilesHide'}
			{else if $FIELD_MODEL->getName() eq 'acc_shuttle_dest_miles'}
				{assign var=SPEC_CLASS value=' shuttleDestMiles'}
				{assign var=SPEC_CLASS_HIDE value=' hide shuttleDestMilesHide'}
			{else if $FIELD_MODEL->getName() eq 'acc_selfstg_origin_ot'}
				{assign var=SPEC_CLASS value=' selfstgOriginOT'}
				{assign var=SPEC_CLASS_HIDE value=' hide selfstgOriginOTHide'}
			{else if $FIELD_MODEL->getName() eq 'acc_selfstg_dest_ot'}
				{assign var=SPEC_CLASS value=' selfstgDestOT'}
				{assign var=SPEC_CLASS_HIDE value=' hide selfstgDestOTHide'}
			{else}
				{assign var=SPEC_CLASS value=''}
				{assign var=SPEC_CLASS_HIDE value=' hide'}
			{/if}
			<td class="fieldLabel {$WIDTHTYPE}{if $BLOCK_LABEL eq "LBL_QUOTES_ACCESSORIALDETAILS" && $SPEC_CLASS neq ''}{$SPEC_CLASS}{/if}">
				{if $isReferenceField neq "reference"}<label id='{$MODULE}_editView_fieldName_{$FIELD_NAME}_label' class="muted pull-right marginRight10px">{/if}
					{if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
					{if $isReferenceField eq "reference"}
						{assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
						{assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
						{if $REFERENCE_LIST_COUNT > 1}
							{assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
							{assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
							{if !empty($REFERENCED_MODULE_STRUCT)}
								{assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
							{/if}
							<span class="pull-right">
								{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
								<select class="chzn-select referenceModulesList streched" style="width:140px;">
									<optgroup>
										{foreach key=index item=value from=$REFERENCE_LIST}
											<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>
										{/foreach}
									</optgroup>
								</select>
							</span>
						{else}
							<label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>
						{/if}
					{else if $FIELD_MODEL->get('uitype') eq "83"}
						{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER}
					{else}
						{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
					{/if}
				{if $isReferenceField neq "reference"}</label>{/if}
			</td>
			{if $FIELD_MODEL->get('uitype') neq "83"}
				<td class="fieldValue preservePlace {$WIDTHTYPE}{if $BLOCK_LABEL eq "LBL_QUOTES_ACCESSORIALDETAILS" && $SPEC_CLASS neq ''}{$SPEC_CLASS}{/if}" {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if} {if $FIELD_MODEL->get('uitype') eq '20'} colspan="3"{/if}>
					{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
				</td>
			{/if}
			{if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
				<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
			{/if}
		{/foreach}
		{* adding additional column for odd number of fields in a block *}
		{if $BLOCK_FIELDS|@end eq true and $BLOCK_FIELDS|@count neq 1 and $COUNTER eq 1}
			<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
		{/if}
		</tr>

			{if $BLOCK_LABEL == 'LBL_QUOTES_INTERSTATEMOVEDETAILS'}
				<script type='text/javascript' src='libraries/jquery/colorbox/jquery.colorbox-min.js'></script>
				{if !$LOCK_RATING}
					<tr><td class='fieldLabel'></td><td class='fieldValue'><button type='button' id='interstateRateQuick'>Quick Rate Estimate</button></td><td class='fieldLabel'></td><td class='fieldValue'><button type='button' class='interstateRateDetail'>{vtranslate('LBL_DETAILED_RATE_ESTIMATE', $MODULE)}</button></td></tr>
				{/if}
			{/if}
		</tbody>
		</table>
		<br/>
	{/foreach}
{/if}
</div>
{/strip}
