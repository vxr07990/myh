{assign var=SHOW_LABEL value=false}
{if $BLOCK_LABEL eq 'LBL_WFCONFIGURATION_SETUP'}
    {assign var=SHOW_LABEL value=true}
{/if}
<table class="table table-bordered blockContainer showInlineTable equalSplit{if is_array($HIDDEN_BLOCKS)}{if in_array($BLOCK_LABEL, $HIDDEN_BLOCKS)} hide{/if}{/if} block_{$BLOCK_LABEL}">
  <thead>
    <tr>
      <th class="blockHeader" colspan="6">{vtranslate($BLOCK_LABEL, $MODULE)}</th>
    </tr>
    <tr>
      {* Header bar, couldn't figure out a good way to do this programmatically  *}
      <th></th>
      {if $SHOW_LABEL}
        <th>
          {vtranslate('LBL_WFCONFIGURATION_UDF_LABEL', $MODULE)}
        </th>
      {/if}
      <th style='width:50px;'>
        {vtranslate('LBL_WFCONFIGURATION_UDF_MOBILE', $MODULE)}
      </th>
      <th style='width:50px;'>
        {vtranslate('LBL_WFCONFIGURATION_UDF_REPEAT', $MODULE)}
      </th>
      <th style='width:50px;'>
        {vtranslate('LBL_WFCONFIGURATION_UDF_PORTAL', $MODULE)}
      </th>
      <th style='width:50px;'>
        {vtranslate('LBL_WFCONFIGURATION_UDF_GROUP', $MODULE)}
      </th>
    </tr>
  </thead>
  <tbody>
{assign var=COUNT value=0}
  {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
      {* if !$FIELD_MODEL->isViewableInDetailView()}
          {continue}
      {/if *}
      {if $COUNT == 5 OR $COUNT == 0}
          {assign var=COUNT value=0}
          <tr>
          <td class='fieldLabel'>
            {vtranslate($FIELD_MODEL->get('label'),$MODULE)}
          </td>
          {if $SHOW_LABEL}
            <td>
              {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE RECORD=$RECORD}
            </td>
          {/if}
      {else}
          <td style='width:50px;'>
          {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE RECORD=$RECORD}
          </td>
      {/if}
      {if $COUNT > 4}
        </tr>
      {/if}
      {assign var=COUNT value=$COUNT+1}
  {/foreach}
</table>
