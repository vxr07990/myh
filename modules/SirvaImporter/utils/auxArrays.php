<?php

class helper
{
    public function helpMeArray($data, $moduleName)
    {
        // Contacts
        $contactsMapping['Last Name'] = array('Contacts', 'lastname', 'Leads', 'lastname');
        $contactsMapping['First Name'] = array('Contacts', 'firstname', 'Leads', 'firstname');
        $contactsMapping['Email'] = array('Contacts', 'email', 'Leads', 'email');
        $contactsMapping['Fax #'] = array('Contacts', 'fax', 'Leads', 'fax');
        $contactsMapping['Cell Phone #'] = array('Contacts', 'mobile', 'Leads', 'mobile');
        $contactsMapping['Work Phone #'] = array('Contacts', 'phone', 'Leads', 'phone');
        $contactsMapping['Home Phone #'] = array('Contacts', 'homephone', 'Leads', 'homephone');

        // Opportunities
        $oppMapping['Primary Booker name'] = array('Opportunities', 'agentname');
        $oppMapping['Bkr Code'] = array('Opportunities', 'agent_number');
        $oppMapping['Qualified Lead #'] = array('Opportunities', 'potential_no');
        $oppMapping['Assign Date/Time'] = array('Opportunities', 'assigned_date');
        $oppMapping['Business Channel'] = array('Opportunities', 'business_channel');
        $oppMapping['Move Type'] = array('Opportunities', 'move_type');
        $oppMapping['Orig Addr 1'] = array('Opportunities', 'origin_address1');
        $oppMapping['Orig Addr 2'] = array('Opportunities', 'origin_address2');
        $oppMapping['Orig City'] = array('Opportunities', 'origin_city');
        $oppMapping['Orig Ctry'] = array('Opportunities', 'origin_country');
        $oppMapping['Orig St/Prov'] = array('Opportunities', 'origin_state');
        $oppMapping['Orig Zip/Postal'] = array('Opportunities', 'origin_zip');
        $oppMapping['Dest Addr 1'] = array('Opportunities', 'destination_address1');
        $oppMapping['Dest Addr 2'] = array('Opportunities', 'destination_address2');
        $oppMapping['Dest City'] = array('Opportunities', 'destination_city');
        $oppMapping['Dest Ctry'] = array('Opportunities', 'destination_country');
        $oppMapping['Lead Type'] = array('Opportunities', 'opp_type');
        $oppMapping['Dest St/Prov'] = array('Opportunities', 'destination_state');
        $oppMapping['Dest Zip/Postal'] = array('Opportunities', 'destination_zip');
        $oppMapping['Promotion Code'] = array('Opportunities', 'promotion_code');
        $oppMapping['Employer Company Name'] = array('Opportunities', 'company_name');
        $oppMapping['Employer Contact Name'] = array('Opportunities', 'contact_name');
        $oppMapping['Employer Contact Phone'] = array('Opportunities', 'contact_phone');
        $oppMapping['Employer Assisting'] = array('Opportunities', 'enabled');
        $oppMapping['Expc Dlvr Date'] = array('Opportunities', 'preferred_pddate');
        $oppMapping['Fulfillment Date'] = array('Opportunities', 'fulfillment_date');
        $oppMapping['Funded'] = array('Opportunities', 'funded');
        $oppMapping['Lead Receive Date'] = array('Opportunities', 'receive_date');
        $oppMapping['Mktg Channel'] = array('Opportunities', 'leadsource');
        $oppMapping['Moving a Vehicle'] = array('Opportunities', 'moving_a_vehicle');
        $oppMapping['Out of Area'] = array('Opportunities', 'out_of_area');
        $oppMapping['Out of Origin'] = array('Opportunities', 'out_of_origin');
        $oppMapping['Program Terms'] = array('Opportunities', 'program_terms');
        $oppMapping['Req Move Date'] = array('Opportunities', 'preferred_pldate');
        $oppMapping['Small Move'] = array('Opportunities', 'small_move');
        $oppMapping['Phone Estimate'] = array('Opportunities', 'phone_estimate');
        $oppMapping['Order Number'] = array('Opportunities', 'order_number');
        $oppMapping['Booked Date'] = array('Opportunities', 'closingdate');
        $oppMapping['Status'] = array('Opportunities', 'sales_stage');

        // Leads
        $leadMapping['Last Name'] = array('Leads', 'lastname');
        $leadMapping['First Name'] = array('Leads', 'firstname');
        $leadMapping['Email'] = array('Leads', 'email');
        $leadMapping['Fax #'] = array('Leads', 'fax');
        $leadMapping['Cell Phone #'] = array('Leads', 'mobile');
        $leadMapping['Work Phone #'] = array('Leads', 'phone');
        $leadMapping['Home Phone #'] = array('Leads', 'homephone');
        $leadMapping['LMP Lead Id'] = array('Leads', 'lmp_lead_id');
        $leadMapping['Business Channel'] = array('Leads', 'business_channel');
        $leadMapping['Move Type'] = array('Leads', 'move_type');
        $leadMapping['Booker Brand'] = array('Leads', 'brand');
        $leadMapping['Language'] = array('Leads', 'languages');
        $leadMapping['Primary Phone Type'] = array('Leads', 'primary_phone_type');
        $leadMapping['Preferred Time'] = array('Leads', 'prefer_time');
        $leadMapping['Time Zone'] = array('Leads', 'timezone');
        $leadMapping['Work Phone Ext'] = array('Leads', 'primary_phone_ext');
        $leadMapping['Date Flexible'] = array('Leads', 'flexible_on_days');
        $leadMapping['Orig Addr 1'] = array('Leads', 'origin_address1');
        $leadMapping['Orig Addr 2'] = array('Leads', 'origin_address2');
        $leadMapping['Orig City'] = array('Leads', 'origin_city');
        $leadMapping['Orig Ctry'] = array('Leads', 'origin_country');
        $leadMapping['Orig St/Prov'] = array('Leads', 'origin_state');
        $leadMapping['Orig Zip/Postal'] = array('Leads', 'origin_zip');
        $leadMapping['Dest Addr 1'] = array('Leads', 'destination_address1');
        $leadMapping['Dest Addr 2'] = array('Leads', 'destination_address2');
        $leadMapping['Dest City'] = array('Leads', 'destination_city');
        $leadMapping['Dest Ctry'] = array('Leads', 'destination_country');
        $leadMapping['Lead Type'] = array('Leads', 'lead_type');
        $leadMapping['Dest St/Prov'] = array('Leads', 'destination_state');
        $leadMapping['Dest Zip/Postal'] = array('Leads', 'destination_zip');
        $leadMapping['Dwelling Type'] = array('Leads', 'dwelling_type');
        $leadMapping['Comments'] = array('Leads', 'employer_comments');
        $leadMapping['Employer Company Name'] = array('Leads', 'company');
        $leadMapping['Employer Contact Name'] = array('Leads', 'contact_name');
        $leadMapping['Employer Contact Phone'] = array('Leads', 'contact_phone');
        $leadMapping['Employer Assisting'] = array('Leads', 'enabled');
        $leadMapping['Expc Dlvr Date'] = array('Leads', 'preferred_pddate');
        $leadMapping['Fulfillment Date'] = array('Leads', 'fulfillment_date');
        $leadMapping['Funded'] = array('Leads', 'funded');
        $leadMapping['Furnish Level'] = array('Leads', 'furnish_level');
        $leadMapping['Lead Receive Date'] = array('Leads', 'lead_receive_date');
        $leadMapping['Mktg Channel'] = array('Leads', 'leadsource');
        $leadMapping['Moving a Vehicle'] = array('Leads', 'moving_a_vehicle');
        $leadMapping['No. of Vehicles'] = array('Leads', 'number_of_vehicles');
        $leadMapping['Offer Valuation'] = array('Leads', 'offer_valuation');
        $leadMapping['Out of Time'] = array('Leads', 'out_of_time');
        $leadMapping['Own Current'] = array('Leads', 'own_current');
        $leadMapping['Own New'] = array('Leads', 'own_new');
        $leadMapping['Program Name'] = array('Leads', 'program_name');
        $leadMapping['Promotion Terms'] = array('Leads', 'promotion_terms');
        $leadMapping['Req Move Date'] = array('Leads', 'preferred_pldate');
        $leadMapping['Small Move'] = array('Leads', 'small_move');
        $leadMapping['Source Name'] = array('Leads', 'source_name');
        $leadMapping['Make(s)'] = array('Leads', 'vehicle_make');
        $leadMapping['Model(s)'] = array('Leads', 'vehicle_model');
        $leadMapping['Year(s)'] = array('Leads', 'vehicle_year');
        $leadMapping['Phone Estimate'] = array('Leads', 'phone_estimate');
        $leadMapping['UQ Disposition'] = array('Leads', 'leadstatus');
        $leadMapping['Detail Disposition'] = array('Leads', 'disposition_lost_reasons');

        switch ($moduleName) {
            case 'Contacts':

                foreach ($data as $key => $value) {
                    $aux = $contactsMapping[$key];
                    $dataNew[$aux[1]] = $value;
                }

                return $dataNew;
                break;

            case 'Opportunities':
                foreach ($data as $key => $value) {
                    $aux = $oppMapping[$key];
                    $dataNew[$aux[1]] = $value;
                }

                return $dataNew;
                break;

            case 'Leads':

                foreach ($data as $key => $value) {
                    $aux = $leadMapping[$key];
                    $dataNew[$aux[1]] = $value;
                }

                return $dataNew;
                break;

            default:
                return array();
                break;
        }
    }
}
