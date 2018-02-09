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
{strip}
{foreach key=index item=jsModel from=$SCRIPTS}
	<script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}
<div class='modelContainer'>
	<div class="modal-header contentsBackground">
		<button type="button" class="close " data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="massEditHeader">{vtranslate('LBL_MASS_EDITING', $MODULE)} {vtranslate($MODULE, $MODULE)}</h3>
	</div>
			<div class="modal-body">
       <div style="margin:0 auto;width: 50em;">
          <table border='0' cellpadding='5' cellspacing='0' height='300px' width="100%">
          <tr><td align='center'>
            <div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 80%; position: relative; z-index: 100000020;'>

            <table border='0' cellpadding='5' cellspacing='0' width='98%'>
            <tr>
              <td rowspan='2' width='11%'><img src="{vimage_path('denied.gif')}" ></td>
              <td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'>
                <span class='genHeaderSmall'>{vtranslate($MESSAGE)}</span></td>
            </tr>
            </table>
            </div>
          </td></tr>
          </table>
        </div>
      </div>
      <div class="modal-footer">
		<div class="pull-right cancelLinkContainer" style="margin-top:0px;">
			<a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
		</div>
	</div>
</div>
{/strip}