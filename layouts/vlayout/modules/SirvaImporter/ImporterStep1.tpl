<div style="width: 65%;margin: auto;margin-top: 2em;padding: 2em;">
    <h3 style="padding-bottom: 1em;text-align: center">{vtranslate('sirva_fieldrelatedmodule', $MODULE)}</h3
    <div>
        <h4 style="margin-top: 4em;margin-bottom: 0.5em;">{vtranslate('Sirva Importer Module. Instructions', $MODULE)}</h4>
        <p>{vtranslate('sirva_importer_instructions', $MODULE)}</p>
        <!--p><a target="_blank" href="{$SITE_URL}modules/SirvaImporter/samples/samples.zip">{vtranslate('Download Sample files', $MODULE)}</a></p--><br><br>


        <form name="importBasic" method="POST" enctype="multipart/form-data" action="index.php?module=SirvaImporter&action=ImportStep2">

            <input type="hidden" value="$quoteId" name="quoteid">

            <table cellspacing="12" cellpadding="5" class="table table-bordered table-condensed themeTableColor" style="width:80%;margin-left:auto;margin-right:auto;margin-top:10px;">
                <thead>
                    <tr class="blockHeader">
                        <th colspan="4" class="mediumWidthType"><span class="alignMiddle">{vtranslate('Sirva Importer Results', $MODULE)}</span></th>
                    </tr>
                </thead>
                <tbody>
                     <tr>
                        <td class="heading2"><label class="muted pull-right marginRight10px">Step 1: Agent Name</label></td>
                        <td class="big"><select name="agent_id" id="agent_id">
                                <option value="-">--</option>
                                {foreach from=$AGENTS item=AGENT_NAME key=AGENT_ID}
                                    <option value="{$AGENT_ID}">{$AGENT_NAME}</option>
                                {/foreach}
                            </select>  </td>

                    <tr>
                    <tr>
                        <td class="heading2"><label class="muted pull-right marginRight10px">Step 2: Module Name</label></td>
                        <td class="big"><select name="module1" id="module1">
                                <option value="-">--</option>
                                {foreach from=$ENTITY_MODULES item=MODULE1}
                                    <option value="{$MODULE1}">{vtranslate($MODULE1)}</option>
                                {/foreach}
                            </select>  </td>

                    <tr>
                        <td><label class="muted pull-right marginRight10px">Step 3: Select File</label></td>
                        <td>
                            <input type="hidden" name="MAX_FILE_SIZE" value="500000000">
                            <input type="file" size="60" class="small" id="userfile" name="userfile">

                        </td>
                    </tr>


                    <tr id="delimiter_container">

                        <td><label class="muted pull-right marginRight10px">Delimiter</label></td>
                        <td>
                            <select class="small" id="delimiter" name="delimiter">
                                <option value=",">, (comma)</option>
                                <option value=";">; (semi-colon)</option>
                            </select>
                        </td>
                    </tr>
                    <tr><td colspan="2">
                            <input type="submit" class="btn pull-right" value="Next" name="next">

                        </td>
                    </tr>
                </tbody></table>
        </form>
    </div>
</div>
