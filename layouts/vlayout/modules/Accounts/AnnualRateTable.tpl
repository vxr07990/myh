{strip}
	<!-- AnnualRateTable.tpl -->
	<table class="table table-bordered blockContainer showInlineTable equalSplit">
		<thead>
			<tr>
				<th class="blockHeader" colspan="8">{vtranslate("LBL_ACCOUNTS_ANNUALRATE", $MODULE)}</th>
			</tr>
		</thead>
		<tbody id='annualRateIncreaseTable'>
			<tr>
			<td style='width:100%;text-align:center' colspan="8">
				<button type="button" id="addRateIncrease" style="clear:left; float:left;">+</button>
				<button type="button" id="addRateIncrease2" style="clear:right; float:right;">+</button>
			</td>
			</tr>
			<tr>
				<td class="blockHeader" style='width:4%;text-align:center;'><input type="hidden" name="numAnnualRate" id="numAnnualRate" value="{$ANNUAL_RATES|@count}">&nbsp;</td>
				<td class="blockHeader" style='width:48%;text-align:center;'>{vtranslate("LBL_ACCOUNTS_FROMDATE", $MODULE)}</td>
				<td class="blockHeader" style='width:48%;text-align:center;'>{vtranslate("LBL_ACCOUNTS_PERCINCREASE", $MODULE)}</td>
			</tr>
			<tr class="defaultAnnualRate hide" id="annualRateRow">
				<td style='width:4%;text-align:center;vertical-align:middle'>
					<a class="deleteAnnualRateButton">
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
					<input type="hidden" name="annualRateId" value='0'>
					<input type="hidden" name="annualRateDeleted" value=''>
				</a>
				</td>
				<td class="fieldValue {$WIDTHTYPE}" style='width:48%;text-align:center'>
				   {assign var="dateFormat" value=$USER_MODEL->get('date_format')}
					{$LOCALFIELDINFO.mandatory = false}
					{$LOCALFIELDINFO.name = 'annual_rate_date'}
					{$LOCALFIELDINFO.label = 'LBL_ACCOUNTS_ANNUALRATEDATE'}
					{$LOCALFIELDINFO.type = 'date'}
					{$LOCALFIELDINFO.dateFormat = $dateFormat}
					{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
					<div class="input-append row-fluid" style="text-align:center;margin:auto;">
						<div class="span12 row-fluid date">
							<input style="margin:auto;width:60%;float:none" id='default_rate_date' type="text" class="dateField" data-date-format="{$dateFormat}" name="default_rate_date" data-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							<span class="add-on" style="float:none;clear:none;display:inline-block;vertical-align:middle;"><i class="icon-calendar"></i></span>
						</div>
					</div>
				</td>
				<td class="fieldValue {$WIDTHTYPE}" style='width:48%;text-align:center'>
					{$LOCALFIELDINFO.mandatory = false}
					{$LOCALFIELDINFO.name = 'annual_rate_increase'}
					{$LOCALFIELDINFO.label = 'LBL_ACCOUNTS_ANNUALRATEINCREASE'}
					{$LOCALFIELDINFO.type = 'percentage'}
					{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
					<input style="width:60%" id="default_rate_increase" type="number" min="0" max="100" data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]' name="default_rate_increase" value="0.00" data-fieldinfo={$INFO} step="any">
				</td>
			</tr>
		{foreach key=ROW_NUM item=ANNUAL_RATE from=$ANNUAL_RATES}
			<tr class="annualRate" id="annualRateRow{$ROW_NUM+1}">
				<td style='width:4%;text-align:center;vertical-align:middle'>
					<a class="deleteAnnualRateButton">
						<i title="Delete" class="icon-trash alignMiddle"></i>
					</a>
					<input type="hidden" name="annualRateId{$ROW_NUM+1}" value='{$ANNUAL_RATE['annualrateid']}'>
					<input type="hidden" name="annualRateDeleted{$ROW_NUM+1}" value=''>
				</a>
				</td>
				<td class="fieldValue {$WIDTHTYPE}" style='width:48%;text-align:center'>
				   {assign var="dateFormat" value=$USER_MODEL->get('date_format')}
					{$LOCALFIELDINFO.mandatory = false}
					{$LOCALFIELDINFO.name = 'annual_rate_date'}
					{$LOCALFIELDINFO.label = 'LBL_ACCOUNTS_ANNUALRATEDATE'}
					{$LOCALFIELDINFO.type = 'date'}
					{$LOCALFIELDINFO.dateFormat = $dateFormat}
					{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
					<div class="input-append row-fluid" style="text-align:center;margin:auto;">
						<div class="span12 row-fluid date">
							<input style="margin:auto;width:60%;float:none" type="text" class="dateField" data-date-format="{$dateFormat}" name="annual_rate_date{$ROW_NUM+1}" value="{$ANNUAL_RATE['date']}" cdata-fieldinfo={$INFO} data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]'>
							<span class="add-on" style="float:none;clear:none;display:inline-block;vertical-align:middle;"><i class="icon-calendar"></i></span>
						</div>
					</div>
				</td>
				<td class="fieldValue {$WIDTHTYPE}" style='width:48%;text-align:center'>
					{$LOCALFIELDINFO.mandatory = false}
					{$LOCALFIELDINFO.name = 'annual_rate_increase'}
					{$LOCALFIELDINFO.label = 'LBL_ACCOUNTS_ANNUALRATEINCREASE'}
					{$LOCALFIELDINFO.type = 'percentage'}
					{$INFO = Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($LOCALFIELDINFO))}
					<input style="width:60%" type="number" min="0" max="100" data-validation-engine='validate[{if $LOCALFIELDINFO.mandatory}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]' name="annual_rate_increase{$ROW_NUM+1}" value="{$ANNUAL_RATE['rate']}" data-fieldinfo={$INFO} step="any">
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
	<br />
	<div class='hide'>
		<link rel="stylesheet" href="libraries/jquery/colorbox/example1/colorbox.css" />
		<script type='text/javascript' src='libraries/jquery/colorbox/jquery.colorbox-min.js'></script>
	</div>
{/strip}