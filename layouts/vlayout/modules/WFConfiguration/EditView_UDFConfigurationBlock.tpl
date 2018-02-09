<table class="table table-bordered blockContainer showInlineTable equalSplit{if is_array($HIDDEN_BLOCKS)}{if in_array($BLOCK_LABEL, $HIDDEN_BLOCKS)} hide{/if}{/if} block_{$BLOCK_LABEL}">
  <thead>
    <tr>
      <th class="blockHeader" colspan="6">{vtranslate($BLOCK_LABEL, $MODULE)}</th>
    </tr>
    <tr>
      {* Header bar, couldn't figure out a good way to do this programmatically  *}
      <th></th>
      <th>
        {vtranslate('LBL_WFCONFIGURATION_UDF_LABEL', $MODULE)}
      </th>
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
  {section name=udfFields start=1 loop=21 step=1}
    {assign var=FIELDSTRING value='udf'|cat:{$smarty.section.udfFields.index}}
    <tr>
      <td class='fieldLabel'>
        {vtranslate($BLOCK_FIELDS[$FIELDSTRING|cat:'_label']->get('label'),$MODULE)}
      </td>
      <td>
        {include file=vtemplate_path($BLOCK_FIELDS[$FIELDSTRING|cat:'_label']->getUITypeModel()->getTemplateName(),$MODULE) FIELD_MODEL=$BLOCK_FIELDS[$FIELDSTRING|cat:'_label'] COUNTER=1 MODULE=$MODULE}
      <td style='width:50px;'>
        {include file=vtemplate_path($BLOCK_FIELDS[$FIELDSTRING|cat:'_mobile']->getUITypeModel()->getTemplateName(),$MODULE) FIELD_MODEL=$BLOCK_FIELDS[$FIELDSTRING|cat:'_mobile'] MODULE=$MODULE}
      </td>
      <td style='width:50px;'>
        {include file=vtemplate_path($BLOCK_FIELDS[$FIELDSTRING|cat:'_repeat']->getUITypeModel()->getTemplateName(),$MODULE) FIELD_MODEL=$BLOCK_FIELDS[$FIELDSTRING|cat:'_repeat'] MODULE=$MODULE}
      </td>
      <td style='width:50px;'>
        {include file=vtemplate_path($BLOCK_FIELDS[$FIELDSTRING|cat:'_portal']->getUITypeModel()->getTemplateName(),$MODULE) FIELD_MODEL=$BLOCK_FIELDS[$FIELDSTRING|cat:'_portal'] MODULE=$MODULE}
      </td>
      <td style='width:50px;'>
        {include file=vtemplate_path($BLOCK_FIELDS[$FIELDSTRING|cat:'_group']->getUITypeModel()->getTemplateName(),$MODULE) FIELD_MODEL=$BLOCK_FIELDS[$FIELDSTRING|cat:'_group'] MODULE=$MODULE}
      </td>
    </tr>
  {/section}
</table>
