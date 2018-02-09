<?php
class Opportunities_RegisterQuote_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    /*
    * Prepare and process email that needs to be sent to Sirva's international team
    */
    public function process(Vtiger_Request $request)
    {
        $emailRecord = Emails_Record_Model::getCleanInstance('Emails');
        $emailRecord->set('toemailinfo', [[getenv('SIRVA_INTL_EMAIL')]]);
        $emailRecord->set('toMailNamesList', []);
        $emailRecord->set('subject', 'International Quote Request');

        $structuredRequest = $this->structureRequest($request->get('quoteInfo'));
        $emailRecord->set('description', $this->returnHTMLBody($structuredRequest));

        if($structuredRequest['record']) {
            $_REQUEST['linked_ids'] = [
                $structuredRequest['record']
            ];
        }

        $emailRecord->save();
        $emailRecord->send();

        $response = new Vtiger_Response();
        $response->setResult('success');
        $response->emit();
    }

    /*
    * Using the provieded HTML template from sirva(I didn't and would never make this),
    * we insert our data and return the email body
    */
    private function returnHTMLBody($request)
    {
        return "<table width='675'>
		    	<tr>
		      		<td width='675' bgcolor='#666666' align='center'><font face='Arial' color='white' size='4'><strong>INTERNATIONAL RATE REQUEST</strong></font></td>
		    	</tr>
			  </table>
			  <br />

			  <table width='675' border='1'>
			  <tr>
			  <td width='100' bgcolor='#666666' align='center'><font color='white'><b>TO</b></font></td>

			  <td width='250'><b>To: International</b></td>

			  <td width='250'><b>Attention:</b><br />
			  " . $request['to_attention'] . "</td>

			  <td width='177'><b>Request Date:</b><br />
			  " . $request['to_request_date'] . "</td>
			</tr>

			<tr>
			  <td width='100' rowspan='2' bgcolor='#666666' align='center'><font color=
			  'white'><b>FROM</b></font></td>

			  <td width='250'><b>Agent Name:</b><br />
			  " . $request['from_agent_name'] . "</td>

			  <td width='250'><b>Requested By:</b><br />
			  " . $request['requested_by'] . "</td>

			  <td width='177'><b>Fax:</b><br />
			  " . $request['fax'] . "</td>
			</tr>

			<tr>
			<td width='200'><b>Agent Code:</b><br />
			" . $request['from_agent_code'] . "</td>

			<td width='200'><b>Rate Response Needed By:</b><br />
			" . $request['rate_response_needed_by'] . "</td>

			<td width='177'><b>Email:</b><br />
			" . $request['email'] . "</td>
			</tr>
		</table>
		  <br />
		  <br />

		  <table width='675' border='1'>
		    <tr>
		      <td width='100' rowspan='3' bgcolor='#666666' align='center'><font color=
		      'white'><b>QUOTE</b></font></td>

		      <td width='577' colspan='2'><b>Transferee Name:</b> " . $request['transferee_name'] . "</td>
		    </tr>

		    <tr>
		      <td colspan='2' align='center' bgcolor='#666666'><font color='white'>QUOTE
		      TYPE</font></td>
		    </tr>

		    <tr>
		      <td>" . ($request['first_rate_request'] == 'on' ? 'First Rate Request' : '&nbsp;') . "</td>

		      <td>" . ($request['private_transferee'] == 'on' ? 'Private Transferee' : '&nbsp;') . "</td>
		    </tr>
		  </table><br />

		  <table width='675' border='1'>
		    <tr>
		      <td width='103' rowspan='2' bgcolor='#666666' align='center'><font color=
		      'white'><b>ORIGIN</b></font></td>

		      <td width='200'><b>Service Type:</b><br />
		      " . $request['origin_type'] . "</td>

		      <td width='103' rowspan='2' bgcolor='#666666' align='center'><font color=
		      'white'><b>DESTINATION</b></font></td>

		      <td width='200'><b>Service Type:</b><br />
		      " . $request['destination_type'] . "</td>
		    </tr>

		    <tr>
		      <td><b>City/Country:</b><br />
		      " . $request['origin_city_country'] . "</td>

		      <td><b>City/Country:</b><br />
		      " . $request['destination_city_country'] . "</td>
		    </tr>
		  </table><br />

		  <table width='675' border='1'>
		    <tr>
		      <td width='53' bgcolor='#666666'></td>

		      <td width='150' colspan='2' bgcolor='#666666' align='center'><font color=
		      'white'><b>Air</b></font></td>

		      <td width='150' colspan='2' bgcolor='#666666' align='center'><font color=
		      'white'><b>LCL</b></font></td>

		      <td width='150' colspan='2' bgcolor='#666666' align='center'><font color=
		      'white'><b>FCL</b></font></td>

		      <td width='150' colspan='2' bgcolor='#666666' align='center'><font color=
		      'white'><b>Vehicle</b></font></td>
		    </tr>

		    <tr>
		      <td rowspan='2' bgcolor='#666666'><font color='white'><b>Est. Weight
		      (lbs)</b></font></td>

		      <td rowspan='2' width='100'><font size='1'><b>AirWeight:</b></font><br />
		      " . $request['air_weight'] . "</td>

		      <td rowspan='2' width='50'><font size='1'><b>Net/Gross:</b></font><br />
		      " . $request['air_weight_type'] . "</td>

		      <td rowspan='2' width='100'><font size='1'><b>LCLWeight:</b></font><br />
		      " . $request['lcl_weight'] . "</td>

		      <td rowspan='2' width='50'><font size='1'><b>Net/Gross:</b></font><br />
		      " . $request['lcl_weight_type'] . "</td>

		      <td rowspan='2' width='100'><font size='1'><b>FCLWeight:</b></font><br />
		      " . $request['fcl_weight'] . "</td>

		      <td rowspan='2' width='20'><font size='1'><b>Net/Gross:</b></font><br />
		      " . $request['fcl_weight_type'] . "</td>

		      <td colspan='2'><font size='1'><b>Weight:</b></font><br />
		      " . $request['vehicle_weight'] . "</td>
		    </tr>

		    <tr>
		      <td colspan='2'><font size='1'><b>Cube:</b></font><br />
		      " . $request['vehicle_cube'] . "</td>
		    </tr>

		    <tr>
		      <td rowspan='2' bgcolor='#666666'><font color='white'><b>Est. Volume
		      (cft)</b></font></td>

		      <td rowspan='2'><font size='1'><b>AirVolume:</b></font><br />
		      " . $request['air_volume'] . "</td>

		      <td rowspan='2'><font size='1'><b>Net/Gross:</b></font><br />
		      " . $request['air_volume_type'] . "</td>

		      <td rowspan='2'><font size='1'><b>LCLVolume:</b></font><br />
		      " . $request['lcl_volume'] . "</td>

		      <td rowspan='2'><font size='1'><b>Net/Gross:</b></font><br />
		      " . $request['lcl_volume_type'] . "</td>

		      <td rowspan='2'><font size='1'><b>FCLVolume:</b></font><br />
		      " . $request['fcl_volume'] . "</td>

		      <td rowspan='2'><font size='1'><b>Net/Gross:</b></font><br />
		      " . $request['fcl_volume_type'] . "</td>

		      <td colspan='2'><font size='1'><b>Make:</b><br /></font>
		      " . $request['vehicle_make'] . "</td>
		    </tr>

		    <tr>
		      <td width='100'><font size='1'><b>Model:</b><br /></font>
		      " . $request['vehicle_model'] . "</td>

		      <td width='50'><font size='1'>Year:<br /></font>
		      " . $request['vehicle_year'] . "</td>
		    </tr>

		    <tr>
		      <td bgcolor='#666666' rowspan='2'><font color='white'><b>Packing</b></font></td>

		      <td colspan='2' rowspan='2'><font size='1'><b>AirPacking:</b></font><br />
		      " . ($request['air_packing_type'] == 'Other' ? $request['air_packing_type_other'] : $request['air_packing_type']) . "</td>

		      <td colspan='2' rowspan='2'><font size='1'><b>LCLPacking:</b></font><br />
		      " . $request['lcl_packing_type'] . "</td>

		      <td><font size='1'><b>Container:</b></font><br />
		      " . $request['fcl_packing_type'] . "</td>

		      <td>" . $request['fcl_packing_type_2'] . "</td>

		      <td colspan='2' rowspan='2'><font size='1'><b>VehiclePacking:</b></font><br />
		      " . ($request['vehicle_packing_type'] == 'Other' ? $request['vehicle_packing_type_other'] : $request['vehicle_packing_type']) . "</td>
		    </tr>

		    <tr>
		      <td colspan='2'><font size='1'>Other:</font>
		      " . $request['fcl_packing_type_other'] . "</td>
		    </tr>
		</table><br />
		<b>Special Requirements:</b> " . $request['special_requirements'] . "<br />
		<br />
		<b>Storage:</b> " . $request['storage'] . "<br />";
    }

    public function structureRequest($request)
    {
        $returnRequest = [];
        foreach ($request as $field) {
            $returnRequest[$field['name']] = $field['value'];
        }
        return $returnRequest;
    }
}
