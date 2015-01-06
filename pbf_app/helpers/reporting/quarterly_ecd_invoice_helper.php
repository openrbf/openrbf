<?php

	function quarterly_ecd_invoice($params){
	 
	 $ci = get_instance();
		
		$ci->load->library('cezpdf');
		$ci->load->library('numbers/numbers_words');
		$ci->load->helper('pdf');
		
		$params_array=array();
		
		$report_params = json_decode($params['report_params'],true);
		
		foreach($report_params as $param){
			
			$param = ($param=='entity_id_2')?'entity_id':$param;
			
			if (array_key_exists($param, $ci->input->post())) {

				$params_array['param_id'][] = $ci->input->post($param);
				$params_array['param_caption'][] = $ci->pbf->get_param_caption($param,$ci->input->post($param));
				
				}
			
			}
			
		$report_params = $params_array; // $params_array containing the real params to use through out...
		
		$table_width = ($params['report_page_layout']=='portrait')?550:820;
		
		$ci->cezpdf->Cezpdf('a4',$params['report_page_layout']);
		
		// creates the HEADER and FOOTER for the document we are creating.
		prep_pdf(	$params['report_page_layout'],
					$params['report_logo_position'],
					$params['report_title'],
					$params['report_subtitle'],
					$params_array['param_caption']); 

		$raw_quarterly_info = $ci->report_mdl->get_quarterly_ecd_info($ci->input->post());
		
		if(!empty($raw_quarterly_info)){
			
			$buffer=array(); $perfomance_payable = 0; $indicator_verified_value = 0; $indicator_tarif = 0; $a=0;
			
			foreach($raw_quarterly_info as $r_key => $r_val){
				
				$buffer[$r_key]['#'] = $r_key+1;
				$buffer[$r_key][$ci->lang->line('indicator_title')] = $r_val['indicator_title'];
				$buffer[$r_key][$ci->lang->line('indicator_verified_value')] = $r_val['indicator_validated_value'];
				$buffer[$r_key][$ci->lang->line('indicator_tarif')] = number_format($r_val['indicator_tarif'],2);
				$buffer[$r_key][$ci->lang->line('indicator_montant')] = number_format($r_val['indicator_montant'],2);
				
				$indicator_verified_value += $r_val['indicator_verified_value'];
				$indicator_tarif += $r_val['indicator_tarif'];
				$a++;				
			}
			
		$buffer[$a]['#'] = '';
		$buffer[$a][$ci->lang->line('indicator_title')] = '<b>'.$ci->lang->line('report_final_tot').'</b>';
		$buffer[$a][$ci->lang->line('indicator_verified_value')] = '<b>'.number_format($indicator_verified_value,2).'</b>';
		$buffer[$a][$ci->lang->line('indicator_tarif')] = '<b>'.number_format($indicator_tarif,2).'</b>';
		$buffer[$a][$ci->lang->line('indicator_montant')] = '<b>'.number_format($raw_quarterly_info[0]['datafile_total'],2).'</b>';	
		
		$perfomance_payable = round(($raw_quarterly_info[0]['budget_value']*$r_val['datafile_total'])/100);
		
		$buffer[$a+1]['#'] = '';
		$buffer[$a+1][$ci->lang->line('indicator_title')] = '<b>'.$ci->lang->line('datafile_total').'</b>';
		$buffer[$a+1][$ci->lang->line('indicator_verified_value')] = '<b>'.number_format($perfomance_payable).'</b>';
		$buffer[$a+1][$ci->lang->line('indicator_tarif')] = '<b>'.number_format($raw_quarterly_info[0]['budget_value']).'</b>';
		$buffer[$a+1][$ci->lang->line('indicator_montant')] = '<b>'.number_format($raw_quarterly_info[0]['datafile_total'],2).'</b>';
		
			$somearray[$ci->lang->line('indicator_verified_value')]['justification']='right';
			$somearray[$ci->lang->line('indicator_tarif')]['justification']='right';
			$somearray[$ci->lang->line('indicator_montant')]['justification']='right';
		
		$ci->cezpdf->ezTable($buffer,'','',array('fontSize' => 9,'width'=>$table_width,'cols'=>$somearray));
		
		$ci->cezpdf->ezSetDy(-20);
		
		$ci->cezpdf->ezText(utf8_decode($ci->lang->line('report_total_amount_in_letter')).'  <b>'.$ci->config->item('app_country_currency').' '.number_format($perfomance_payable).' ('.$ci->numbers_words->toWords($perfomance_payable,($ci->config->item('language_abbr')=='en')?'en_US':$ci->config->item('language_abbr')).' '.$ci->config->item('app_country_currency').').</b> ',8,array('justification'=>'left'));
		
		$ci->cezpdf->ezSetDy(-20);
		
		$ci->cezpdf->ezText(utf8_decode(trim($config['report_signatories'])),10,array('justification'=>'left'));	
			
		}
		else{
			
			$ci->cezpdf->ezText($ci->lang->line('no_data_display'),10,array('justification'=>'left'));
			
			}
		
		$ci->cezpdf->ezStream();
		
		}