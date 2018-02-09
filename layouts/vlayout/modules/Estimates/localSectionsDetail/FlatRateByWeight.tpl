{if !empty($DETAILS) && $DETAILS.weight > $DETAILS.weight_cap}
  <tr>
    <td class='fieldLabel'>
        <label class="muted pull-right marginRight10px">CWT Overflow Rate</label>
    </td>
    <td class='fieldValue'>
        <span class="value">{$DETAILS[0].cwt_rate}</span>
    </td>
  </tr>
{/if}
{if !empty($DETAILS)}
  <td class='fluid' colspan=4>
      <table name="Service{$ID}" class="table table-bordered" style='border:none;'>
        {foreach item=BREAKPOINT key=INDEX from=$DETAILS}
          <tr>
            <td class='fieldLabel'>
                <label class="muted pull-right marginRight10px">Weight</label>
            </td>
            <td class='fieldValue'>
              <span class="value">{$BREAKPOINT.weight}</span>
            </td>
            <td class='fieldLabel'>
                <label class="muted pull-right marginRight10px">Rate</label>
            </td>
            <td class='fieldValue'>
              <span class="value">{$BREAKPOINT.rate}</span>
            </td>
          </tr>
        {/foreach}
      </table>
  </td>
{/if}
