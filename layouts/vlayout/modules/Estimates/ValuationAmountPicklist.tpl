{strip}
    <div class="valuationPick hide">
    <select class="chzn-select" name="valuation_amount_pick" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  data-selected-value="">
        <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
        {foreach from=$VALUATION_AMOUNT_VALUES item=LABEL key=VALUE}
            <option value="{$VALUE}" {if $VALUE == $SELECT_VALUE} selected   {/if}>{$LABEL}</option>
        {/foreach}
    </select>
    </div>
    <p class="valuationPick valuationManual fieldLabel hide" style="width:100%;margin-top:5px;">Enter Valuation Amount:</p>
    <input class="valuationManual" type='number' name='valuation_amount' step='0.01' value='{$VALUATION_AMOUNT}'/>
{/strip}
