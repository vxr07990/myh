{strip}
    <link rel="stylesheet" href="layouts/vlayout/modules/ResourceDashboard/resources/jquery.dataTables.css" type="text/css" media="screen" />
{/strip}



<div class="span12">


    <div class="span12" style="padding: 10px;margin-left:0px;">
        <div class="row">

            <div class="span12" style="font-weight: 700;margin-top: 4%;"><h2 style="margin-bottom: 2%;">Available Resources</h2></div>
            <div class="span12" ><p>Please choose a resource type to filter the table</p></div>
            {if $ARESULT eq 'ok'}
                <div class="span12 alert alert-success" style="display:block;margin-top: 1%;margin-bottom: 1%;"><p>Resources has been assigned. <a href="index.php?module=Project&view=Detail&record={$PROJECT_ID}" > Click Here </a>to go back to your project</p></div>
            {/if}
            {if $ARESULT eq 'fail'}
                <div class="span12 alert alert-error" style="display:block;margin-top: 1%;margin-bottom: 1%;"><p>There was an error processing your request</p></div>
            {/if}
            <div class="span2" style="font-weight: 700; margin-top: 2%;margin-bottom: 5%;">Resource Type</div>
            <div class="span2" style="font-weight: 700; margin-top: 2%;">
                <select class="span2 select-one" name="resource_type" id="resource-type" type="text" style="width: 130%;">
                    {foreach item=RESOURCE_TYPE from=$RESOURCE_TYPES}
                        <option value="{$RESOURCE_TYPE}">{$RESOURCE_TYPE}</option>
                    {/foreach}
                </select>


            </div>
            <div class="span5" style="float:right;margin-top: 2%;"><button class="btn dropdown-toggle" style="float: right;" id="button">Select Selected Resources</button></div>

            <table id="resources_table" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>CRM ID</th>
                        <th>Name</th>
                        <th>Available Qty</th>
                        <th>Type</th>
                        <th>QTY</th>
                    </tr>
                </thead>



                <tbody>
                    {foreach item=RESOURCE from=$RESOURCE_LIST}

                        <tr>
                            <td>{$RESOURCE.id}</td>
                            <td>{$RESOURCE.name}</td>
                            <td class="aqty_{$RESOURCE.id}">{$RESOURCE.quantity}</td>
                            <td>{$RESOURCE.type}</td>
                            <td><input autocomplete="off" style="width:45px" type="text" name="qty" id="qty_{$RESOURCE.id}"></input></td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>




        </div>

    </div>


    <input type="hidden" name="tasks_id" value="{$TASK_ID}"></input>
    <input type="hidden" name="project_id" value="{$PROJECT_ID}"></input>
</div>

{strip}
    <script type="text/javascript" src="layouts/vlayout/modules/ResourceDashboard/resources/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="layouts/vlayout/modules/ResourceDashboard/resources/ResourceDashboard.js"></script>

{/strip}

<script>
    {literal}

        $(document).ready(function() {
            var table = $('#resources_table').DataTable({
                "multipleSelection": false,
                "bLengthChange": false //Hide record qty
            });
            table.draw();
            $('#resources_table tbody').on('click', 'tr', function() {
                $(this).toggleClass('selected');
            });
            $('#button').click(function() {
                var selected_ids = {};
                $('input[name="qty"]').each(function() {
                    if ($(this).val() > 0) {
                        var id = $(this).attr('id');
                        var id_arr = id.split('_');
                        selected_ids[id_arr[1]] = $(this).val();
                    }
                })

                if (Object.keys(selected_ids).length > 0) {

                    $.ajax({
                        type: "POST",
                        url: "index.php?module=ResourceDashboard&action=AssignResourcesToTask",
                        dataType: "json",
                        data: {
                            tasks_id: $('input[name="tasks_id"]').val(),
                            project_id: $('input[name="project_id"]').val(),
                            selectedIds: selected_ids
                        },
                        success: function(msg) {
                            if (msg.success && msg.result['result'] == 'works') {
                                var url = window.location.href;
                                url = url.split("&result");
                                window.location.href = url[0] + "&result=ok";

                            } else if (msg.success && msg.result['result'] == 'failed') {
                                var url = window.location.href;
                                url = url.split("&result");
                                window.location.href = url[0] + "&result=fail";
                            }
                        }
                    });
                } else {
                    alert('Error: No resources selected');
                }

            });
            $('#resource-type').change(function() {
                table.draw();
            });

            $('input[name="qty"]').blur(function() {
                var id = $(this).attr('id');
                var aqty = 'a' + id;
                var aqty = parseInt($('.' + aqty).html());

                if ($(this).val() > aqty) {
                    alert('Error: The assigned quantity is greater than the available qty');
                    $(this).focus();
                }


            });
        })
        /* Custom filtering function which will search data in column four between two values */
        $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var resourceType = $('#resource-type').val();
                    var resource = data[3];


                    if (resourceType == '') {
                        return true;
                    }


                    if (resourceType != '' && resourceType == resource)
                    {
                        return true;
                    } else {
                        return false;
                    }

                }
        );

    {/literal}
</script>