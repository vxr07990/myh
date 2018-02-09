{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
-->*}
{strip}
<div class="container-fluid"  id="AsteriskServerDetails">
    <div class="widget_header row-fluid">
    <div class="span8"><h3>Move Easy</h3></div>
    <table class="table table-bordered table-condensed themeTableColor">
        <thead>
        <tr class="blockHeader">
            <th colspan="2" class="mediumWidthType">
                <span class="alignMiddle">Move Easy</span>
            </th>
        </tr>
        </thead>
        {if $SHOW == 'subscribed'}
            <tr>
                <td colspan="2">
                    <div id="iframeContent" style="position:relative; width:100%; height:80vh;">
                        <iframe id="cubesheet" style="position:absolute; width:100%; height:100%; border: 0;" src="{$URL}"></iframe>
                    </div>
                </td>
            </tr>
        {/if}
        {if $SHOW == 'unsubscribed'}
                    <tr class="alert alert-danger">
                        <td colspan="2">You have not enabled MoveEasy Integration.</td>
                    </tr>
                    <form action="index.php?module=MoveEasyIntegration&parent=Settings&view=Index&enableInt=1&agentID={$agentID}" method="post">
                    <tr>
                        <td>First Name<span style="color:red;">*</span></td>
                        <td><input style="height:100%" type="text" name="fname" required></td>
                    </tr>
                    <tr>
                        <td>Middle Name</td>
                        <td><input style="height:100%" type="text" name="mname"></td>
                    </tr>
                    <tr>
                        <td>Last Name<span style="color:red;">*</span></td>
                        <td><input style="height:100%" type="text" name="lname" required></td>
                    </tr>
                    {if $ASKFOREMAIL}
                        <tr>
                            <td>Email<span style="color:red;">*</span></td>
                            <td><input style="height:100%" type="email" name="email" required></td>
                        </tr>
                    {/if}
                    <tr>
                        <td colspan="2"><input type="submit" class="btn btn-success" value="Enable MoveEasy Integration"></td>
                    </tr>
                    </form>
        {/if}
        {if $SHOW == 'missinginfo'}
            <tr class="alert alert-danger">
                <td colspan="2">{$ERROR}</td>
            </tr>
            <form action="index.php?module=MoveEasyIntegration&parent=Settings&view=Index&enableInt=1&agentID={$agentID}" method="post">
                <tr>
                    <td>First Name<span style="color:red;">*</span></td>
                    <td><input style="height:100%" type="text" name="fname" required></td>
                </tr>
                <tr>
                    <td>Middle Name</td>
                    <td><input style="height:100%" type="text" name="mname" required></td>
                </tr>
                <tr>
                    <td>Last Name<span style="color:red;">*</span></td>
                    <td><input style="height:100%" type="text" name="lname" required></td>
                </tr>
                {if $ASKFOREMAIL}
                    <tr>
                        <td>Email<span style="color:red;">*</span></td>
                        <td><input style="height:100%" type="email" name="email" required></td>
                    </tr>
                {/if}
                <tr>
                    <td colspan="2"><input type="submit" class="btn btn-success" value="Enable MoveEasy Integration"></td>
                </tr>
            </form>
        {/if}
    </table>
    </div>
</div>
{/strip}
