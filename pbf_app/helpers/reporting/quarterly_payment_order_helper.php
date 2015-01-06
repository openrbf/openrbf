<?php

	function quarterly_payment_order($params){
		
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
					'add');
					
		$raw_quarterly_info = $ci->report_mdl->get_quarterly_payment_order_info($ci->input->post());
				
		$function_str = $ci->pbf->get_runnable_script($ci->input->post('datafile_year'),$ci->input->post('datafile_quarter'));
		
				
		if(!empty($raw_quarterly_info)){
			
			$buffer=array(); $current_bank=$raw_quarterly_info[0]['bank_name']; $first_buffer=1; $bank_invoice_tot=0; $final_invoice_tot=0; $tot_subsidies = 0; $banks_counter = 1;
			
			foreach($raw_quarterly_info as $r_key => $r_val){
				
				if($current_bank!=$r_val['bank_name']){
					$current_bank=$r_val['bank_name'];
// do not know why $ci->cezpdf->ezGetCurrentPageNumber()!=1 does not work, should use $pdf->ezWhatPageNumber($pdf->ezGetCurrentPageNumber());
					if($first_buffer!=1){
					$ci->cezpdf->ezNewPage();
					pdf_header(	$params['report_page_layout'],
					$params['report_logo_position'],
					$params['report_title'],
					$params['report_subtitle'],
					$params_array['param_caption']);
					
					}
					
					$buffer[$r_key+1]['#'] = '';
					$buffer[$r_key][$ci->lang->line('entity_district')] = '';
					$buffer[$r_key+1][$ci->lang->line('entity_name')] = '<b>'.$ci->lang->line('report_sub_tot').'</b>';
					$buffer[$r_key+1][$ci->lang->line('payable_amount')] = '<b>'.number_format($bank_invoice_tot).'</b>';
					$buffer[$r_key+1][$ci->lang->line('bank_name')] = '';
					$buffer[$r_key+1][$ci->lang->line('bank_branch_name')] = '';
					$buffer[$r_key+1][$ci->lang->line('entity_bank_acc')] = '';
					
					$somearray[$ci->lang->line('total_subsides')]['justification']='right';
					$somearray[$ci->lang->line('score_quality')]['justification']='right';
					$somearray[$ci->lang->line('payable_amount')]['justification']='right';
					
					$ci->cezpdf->ezTable($buffer,'',$raw_quarterly_info[$r_key-1]['bank_name'],array('protectRows' =>3, 'fontSize' =>7,'width'=>$table_width,'cols'=>$somearray));
										
					$ci->cezpdf->ezSetDy(-20);
					
					if($ci->cezpdf->y<=150){
						$ci->cezpdf->ezNewPage();	
						}
		
					$ci->cezpdf->ezText($ci->lang->line('report_total_amount_in_letter').' <b>'.$ci->config->item('app_country_currency').' '.number_format($bank_invoice_tot).' ('.$ci->numbers_words->toWords($bank_invoice_tot,($ci->config->item('language_abbr')=='en')?'en_US':$ci->config->item('language_abbr')).' '.$ci->config->item('app_country_currency').').</b> ',8,array('justification'=>'left'));
		
					$ci->cezpdf->ezSetDy(-20);
		
					$ci->cezpdf->ezText(utf8_decode(trim($params['report_signatories'])),8.5,array('justification'=>'left'));
					
					$buffer=array();
					$first_buffer++;
					$bank_invoice_tot=0;
					$final_invoice_tot=0;
					$banks_counter = 1;
					
				}
				
				$buffer[$r_key]['#'] = $banks_counter;
				$buffer[$r_key][$ci->lang->line('entity_district')] = $r_val['geozone_name'];
				$buffer[$r_key][$ci->lang->line('entity_name')] = $r_val['entity_name'];
				
				$linehf_tot = $ci->pbf->calculate_final_payment(	$r_val['score_quality'],
																	$r_val['tot_subsidies'],
																	$r_val['entity_class'],
																	$r_val['entity_type'],
																	$r_val['entity_pbf_group_id'],
																	$ci->input->post('entity_geozone_id'),
																	$function_str);
				
				$buffer[$r_key][$ci->lang->line('payable_amount')] = number_format($linehf_tot);
				
				$bank_invoice_tot += $linehf_tot;
				
				$buffer[$r_key][$ci->lang->line('bank_name')] = $r_val['bank_name'];
				$buffer[$r_key][$ci->lang->line('bank_branch_name')] = $r_val['bank_branch_name'];
				$buffer[$r_key][$ci->lang->line('entity_bank_acc')] = $r_val['entity_bank_acc'];
				
				$banks_counter++;
				
			}
			
		$ci->cezpdf->ezNewPage();
		pdf_header(	$params['report_page_layout'],
					$params['report_logo_position'],
					$params['report_title'],
					$params['report_subtitle'],
					$params_array['param_caption']);
					
					$buffer[$r_key+1]['#'] = '';
					$buffer[$r_key][$ci->lang->line('entity_district')] = '';
					$buffer[$r_key+1][$ci->lang->line('entity_name')] = '<b>'.$ci->lang->line('report_sub_tot').'</b>';
					$buffer[$r_key+1][$ci->lang->line('payable_amount')] = '<b>'.number_format($bank_invoice_tot).'</b>';
					$buffer[$r_key+1][$ci->lang->line('bank_name')] = '';
					$buffer[$r_key+1][$ci->lang->line('bank_branch_name')] = '';
					$buffer[$r_key+1][$ci->lang->line('entity_bank_acc')] = '';
					
					$somearray[$ci->lang->line('total_subsides')]['justification']='right';
					$somearray[$ci->lang->line('score_quality')]['justification']='right';
					$somearray[$ci->lang->line('payable_amount')]['justification']='right';
					
		$ci->cezpdf->ezTable($buffer,'',$raw_quarterly_info[$r_key-1]['bank_name'],array('protectRows' =>3, 'fontSize' =>7,'width'=>$table_width,'cols'=>$somearray));
		
		$ci->cezpdf->ezSetDy(-20);
					
					if($ci->cezpdf->y<=150){
						$ci->cezpdf->ezNewPage();	
						}
		
					$ci->cezpdf->ezText($ci->lang->line('report_total_amount_in_letter').' <b>'.$ci->config->item('app_country_currency').' '.number_format($bank_invoice_tot).' ('.$ci->numbers_words->toWords($bank_invoice_tot,($ci->config->item('language_abbr')=='en')?'en_US':$ci->config->item('language_abbr')).' '.$ci->config->item('app_country_currency').').</b> ',8,array('justification'=>'left'));
		
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
		
		//$ci->cezpdf->ezText(utf8_decode(trim($params['report_signatories'])),8.5,array('justification'=>'left'));
		
		}
		else{
			
			$ci->cezpdf->ezText($ci->lang->line('no_data_display'),8.5,array('justification'=>'left'));
			
			}
		
		$ci->cezpdf->ezStream();
		
		}