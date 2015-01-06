<?php

	function quarterly_consolidated($params){
		
		$ci = get_instance();
		
		$ci->load->library('cezpdf');
		$ci->load->library('numbers/numbers_words');
		$ci->load->helper('pdf');
		
		$params_array=array();
		
		$report_params = json_decode($params['report_params'],true);
		
		foreach($report_params as $param){
			
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
					$params_array['param_caption'],
					'add',
					$params['report_header']); 
					
		$raw_quarterly_info = $ci->report_mdl->get_quarterly_consolidated_info($ci->input->post());

		if(!empty($raw_quarterly_info)){
			
			$buffer=array(); $main_invoice_tot = 0; $main_palu_tot = 0; $main_linehf_tot = 0;
			
			$months = $ci->pbf->get_monthsBy_quarter($ci->input->post('datafile_quarter'));
			
			$function_str = $ci->pbf->get_runnable_script($ci->input->post('datafile_year'),$ci->input->post('datafile_quarter'));
			
			foreach($raw_quarterly_info as $r_key => $r_val){
				
				$dig_quality = $ci->report_mdl->get_quality_evaluation($ci->input->post('datafile_year'),$ci->input->post('datafile_quarter'),$r_val['entity_id']);
				
				$pbf_group = $ci->entities_mdl->get_entitygroup($r_val['entity_pbf_group_id']);
				
				$buffer[$r_key]['#'] = $r_key+1;
				$buffer[$r_key][$ci->lang->line('entity_name')] = $r_val['entity_name'].'  ['.$pbf_group['entity_group_abbrev'].']';
				$line_tot = 0;
				
				foreach($months as $month){
				$buffer[$r_key][utf8_decode($ci->lang->line('app_month_'.$month))] = number_format($r_val[$month]);
				$line_tot += $r_val[$month];
				}
				
				$buffer[$r_key][$ci->lang->line('total_subsides')] = number_format($line_tot);
				$buffer[$r_key][$ci->lang->line('score_quality')] = $dig_quality['datafile_total'];
				
				$linehf_tot = $ci->pbf->calculate_final_payment(	$dig_quality['datafile_total'],
																	$line_tot,
																	$r_val['entity_class'],
																	$r_val['entity_type'],
																	$r_val['entity_pbf_group_id'],
																	$ci->input->post('entity_geozone_id'),
																	$function_str);
																	
																	
				$palu = $ci->report_mdl->get_non_quality_assoc_tot( 	$ci->input->post('datafile_year'),
																		$ci->input->post('datafile_quarter'),
																		$r_val['entity_id']);
																		
				$buffer[$r_key][$ci->lang->line('payable_amount')] = number_format($linehf_tot);
				
				$buffer[$r_key][utf8_decode($ci->lang->line('report_prod_category_4'))] = number_format($palu['indicator_montant']);
				
				$buffer[$r_key][utf8_decode($ci->lang->line('report_final_tot'))] = number_format($linehf_tot+$palu['indicator_montant']);
				
				$main_linehf_tot += $linehf_tot;
				
				$main_palu_tot += $palu['indicator_montant'];
				
				$main_invoice_tot += $linehf_tot+$palu['indicator_montant'];
				
				}
			
			$buffer[$r_key+1]['#'] = '';
			$buffer[$r_key+1][$ci->lang->line('entity_name')] = '<b>'.$ci->lang->line('report_final_tot').'</b>';	
			
			foreach($months as $month){ // making the monthly column totals
				
				foreach($raw_quarterly_info as $monthly_tot){
					
					$buffer[$r_key+1][utf8_decode($ci->lang->line('app_month_'.$month))] += $monthly_tot[$month];
					
					}
				

				}
				
			//monthly column totals number_format and right alignments...
			$somearray=array();
			$final_subsides_tot = 0;			
			foreach($months as $month){
				
			$final_subsides_tot += $buffer[$r_key+1][utf8_decode($ci->lang->line('app_month_'.$month))];
			
			$buffer[$r_key+1][utf8_decode($ci->lang->line('app_month_'.$month))] = '<b>'.number_format($buffer[$r_key+1][utf8_decode($ci->lang->line('app_month_'.$month))]).'</b>';
			
			$somearray[utf8_decode($ci->lang->line('app_month_'.$month))]['justification']='right';	
				}
			
			$somearray[$ci->lang->line('total_subsides')]['justification']='right';
			$somearray[$ci->lang->line('score_quality')]['justification']='right';
			$somearray[$ci->lang->line('payable_amount')]['justification']='right';
			$somearray[utf8_decode($ci->lang->line('report_prod_category_4'))]['justification']='right';
			$somearray[utf8_decode($ci->lang->line('report_final_tot'))]['justification']='right';
			
			$buffer[$r_key+1][$ci->lang->line('total_subsides')] = '<b>'.number_format($final_subsides_tot).'</b>';
			$buffer[$r_key+1][$ci->lang->line('payable_amount')] = '<b>'.number_format($main_linehf_tot).'</b>';
			$buffer[$r_key+1][utf8_decode($ci->lang->line('report_prod_category_4'))] = '<b>'.number_format($main_palu_tot).'</b>';
			$buffer[$r_key+1][utf8_decode($ci->lang->line('report_final_tot'))] = '<b>'.number_format($main_invoice_tot).'</b>';
		
		$ci->cezpdf->ezTable($buffer,'','',array('fontSize' => 9,'width'=>$table_width,'cols'=>$somearray));
		
		$ci->cezpdf->ezSetDy(-20);
		
		$ci->cezpdf->ezText(utf8_decode($ci->lang->line('report_total_amount_in_letter')).'   <b>'.$ci->config->item('app_country_currency').' '.number_format($main_invoice_tot).' ('.$ci->numbers_words->toWords($main_invoice_tot,($ci->config->item('language_abbr')=='en')?'en_US':$ci->config->item('language_abbr')).' '.$ci->config->item('app_country_currency').').</b> ',8,array('justification'=>'left'));
		
		$ci->cezpdf->ezSetDy(-40);
		
		$signatories = json_decode($params['report_signatories'],true);
		$sign[0]['1'] = $signatories[0];
		$sign[0]['2'] = $signatories[1];
		$sign[0]['3'] = $signatories[2];
		
		$ci->cezpdf->ezTable(	$sign,
								'',
								'',
								array(	'fontSize' => 10,
										'showHeadings' => 0,
										'showLines' => 0,
										'shaded' => '0',
										'width'	=>	$table_width,
										'cols'	=> array(	'1' => array('justification'=>'left','width'=>($table_width/3)),
															'2' => array('justification'=>'left','width'=>($table_width/3)),
															'3' => array('justification'=>'left','width'=>($table_width/3)))));
															
		//$ci->cezpdf->ezText(utf8_decode(trim($params['report_signatories'])),10,array('justification'=>'left'));
		
		}
		
		else{
			
		$ci->cezpdf->ezText($ci->lang->line('no_data_display'),10,array('justification'=>'left'));
			
			}
		
		$ci->cezpdf->ezStream();
		
		}