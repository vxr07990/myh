<tr class='cwt_overflow{$ID} {IF $DETAILS[0]["weight"] <= $DETAILS[0]["weight_cap"]}hide{/if}'>
  <td class='fieldLabel'>
      <label class="muted pull-right marginRight10px">CWT Overflow Rate</label>
  </td>
  <td class='fieldValue'>
    {if !empty($DETAILS)}
      <input id="{$MODULE_NAME}_editView_fieldName_cwt_rate{$ID}" name="Excess{$ID}" step="0.01" min='0' class="input-medium currencyField" type="text" value="{if !empty($DETAILS[0]['cwt_rate'])}{$DETAILS[0]['cwt_rate']}{else}0{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"  />
    {/if}
  </td>
  <td colspan=2></td>
</tr>
<tr class='frbw_table'>
  <td class='fieldLabel'>
      <label class="muted pull-right marginRight10px">Weight</label>
  </td>
  <td class='fieldValue'>
    <input id="{$MODULE_NAME}_editView_fieldName_weight{$ID}" name="Weight{$ID}" serviceid='{$ID}' min='0' class="input-medium" type="number" value="{if !empty($DETAILS[0]['weight'])}{$DETAILS[0]['weight']}{else}0{/if}" data-decimal-seperator="." data-group-seperator="," data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}"/>
  </td>
  <td class='fieldLabel frbw_rate{$ID}'>
    <label class="muted pull-right marginRight10px">Rate</label>
  </td>
  <td class='fieldValue frbw_rate{$ID}'>
    <input id="{$MODULE_NAME}_editView_fieldName_rate{$ID}" name="Rate{$ID}" step="0.01" min='0' class="input-medium currencyField" type="number" value="{if !empty($DETAILS[0]['rate'])}{$DETAILS[0]['rate']}{else}0{/if}" data-decimal-seperator="." data-group-seperator="," data-number-of-decimal-places="2" data-validation-engine="validate[{if $SERVICE->get(is_required)}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{$INFO}" />
  </td>
</tr>

{if !empty($DETAILS)}
  {assign var=SECTION_TABLE value=[]}
  {foreach item=BREAKPOINT key=INDEX from=$SERVICE->getDefaultTable()}
    {$SECTION_TABLE[] = ['from_weight' => $BREAKPOINT.from_weight, 'to_weight' => $BREAKPOINT.to_weight, 'rate' => $BREAKPOINT.rate, 'excess' => $BREAKPOINT.cwt_rate]}
  {/foreach}
  <input type='hidden' value='{$SECTION_TABLE|@json_encode}' id='frbw_tabled{$ID}'/>
  <input type='hidden' value='{$ID}' class='frbw_id' />
  <input type='hidden' value='{$DETAILS[0]["weight_cap"]}' id='frbw_cap{$ID}' name='frbw_cap{$ID}'/>
{/if}
