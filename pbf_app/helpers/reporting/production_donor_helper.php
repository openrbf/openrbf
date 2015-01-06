<?php

	function production_donor($params){
			
		$ci = get_instance();
		$ci->load->library('cezpdf');
		$ci->load->library('numbers/numbers_words');
		$ci->load->helper('pdf');
		$ci->load->model('entities_mdl');
		
		
		//==================GET ALL DONORS FOR THIS PERIOD AND ENTITY (IF EXIST)=====
				
		//=================TEST IF MULTIDONORS EXIST================================
		//============test exist config on this period===============================
		
		$sql_config="SELECT * FROM pbf_donorsconfig WHERE YEAR(pbf_donorsconfig.from)='".$params['year']."'";
		$donor_data_config=$ci->db->query($sql_config)->result_array();
				
		$entity_donor_config=array();
		$detail_entity_data=array();
		$params['donor_data']['details']='';
		if(!empty($donor_data_config)){
			$params['donor_data']=$donor_data;
			$results=$ci->pbf->get_donor_list_config($params['year']);

			$sql="SELECT pbf_donors.*,pbf_donorsentity_details.entity_id,pbf_donorsconf_details.indicator_id,pbf_donorsconf_details.percentage  FROM pbf_donorsentity_details LEFT JOIN pbf_donorsconf_details ON (pbf_donorsentity_details.donorconf_id=pbf_donorsconf_details.conf_details_id)
			LEFT JOIN pbf_donorsconfig ON (pbf_donorsconfig.donorconfig_id=pbf_donorsconf_details.donor_conf_id) LEFT JOIN pbf_donors ON(pbf_donors.donor_id=pbf_donorsconfig.donor_id) WHERE pbf_donorsentity_details.entity_id='".$params['entity_id']."'";

			$entity_details=$ci->db->query($sql)->result_array();
			$global_percentage=0;
			$contrib_fac_globale=array();
			$contrib_indicator=array();
			foreach($entity_details as $entity_detail){
				if ($entity_detail['indicator_id']==0){
					$contrib_fac_globale[]=$entity_detail;
					$global_percentage=$global_percentage+$entity_detail['percentage'];
						if ($global_percentage>100){
						exit();
					}
					$global_diff=100-$global_percentage;
				}else{
					$contrib_indicator[]=$entity_detail;
				}
			}
						
						
		//=========================GET PRIMOR DONOR===================================
		$sql_primor="SELECT * FROM pbf_donors WHERE donor_priority=1";
		$sql_primor_details=$ci->db->query($sql_primor)->row_array();	
		//===============================================================================
	
	
	
	
	
	
		
		$params_array=array();
			
		$report_params = json_decode($params['report_params'],true);
		
		foreach($report_params as $param){
			
			$param = ($param=='entity_id_2')?'entity_id':$param;
			
			if (array_key_exists($param, $ci->input->post())) {

				$params_array['param_id'][] = $ci->input->post($param);
				$params_array['param_caption'][] = $ci->pbf->get_param_caption($param,$ci->input->post($param));
				
				}
			
			}
		$entity = $ci->entities_mdl->get_entity($ci->input->post('entity_id'));

	/*	$report_params = $params_array; // $params_array containing the real params to use through out...
		
		// check if uptodate report exists....
		
		$report['entity_id']= $entity['entity_id'];
		$report['zone_id']= $entity['geozone_id'];

		//	$report['month']=
		$report['quarter']=$params['datafile_quarter'];
		$report['year']=$params['datafile_year'];
		$report['author']= $ci->session->userdata('user_id');
		$report['reporting_id']=$params['report_id'];
		
		$filename = $ci->pbf_mdl->uptodate_report_exist($report);
		if ($filename!= null) { //return existing report
		 $file='./cside/reports/'.$filename.'.pdf';
		 header('Content-type: application/pdf');
		 header('Content-Disposition: inline; filename="the.pdf"');
		 header('Content-Transfer-Encoding: binary');
		 header('Content-Length: ' . filesize($file));
		 @readfile($file);
		 
		} else { //create new uptodate report*/
				

		$table_width = ($params['report_page_layout']=='portrait')?550:820;
		
		$ci->cezpdf->Cezpdf('a4',$params['report_page_layout']);
		
		// creates the HEADER and FOOTER for the document we are creating.
		
		//========================check if user is a donor and call specific header============
		
		
		prep_pdf(	$params['report_page_layout'],
					$params['report_logo_position'],
					$params['report_title'],
					$params['report_subtitle'],
					$params_array['param_caption'],
					'add',
					$params['report_header'],
					$params['report_id']); 

		
						
	$function_str = $ci->pbf->get_runnable_pbf_script_for_indicator($ci->input->post('datafile_year'),$ci->input->post('datafile_quarter'));
		
	$function_str_entity = $ci->pbf->get_runnable_script($ci->input->post('datafile_year'),$ci->input->post('datafile_quarter'));	
		
	$raw_report_info = $ci->report_mdl->get_quarterly_entity_report($ci->input->post());	
	
		
	
		if(!empty($raw_report_info['list_quantity'])){
			
			$buffer=array(); $category_1 = 0; $category_4 = 0; $tot_subsidies = 0;
			$donor_tab=array();
			foreach($raw_report_info['list_quantity'] as $r_key => $r_val){
				
				
				
				$buffer[$r_key]['#'] = $r_key+1;
				$buffer[$r_key][ucwords($ci->lang->line('indicator_title'))] = $r_val['indicator_title'].' '.$percentage;
				$buffer[$r_key][utf8_decode($ci->lang->line('report_prod_forecast'))] = '';
				$buffer[$r_key][utf8_decode($ci->lang->line('report_prod_forecast'))] = '';
				$buffer[$r_key][utf8_decode($ci->lang->line('report_prod_achieved'))] = $r_val['verified_value'];
				$buffer[$r_key][utf8_decode($ci->lang->line('report_prod_unit_fee'))] = $r_val['default_tarif'];
				$buffer[$r_key][utf8_decode($ci->lang->line('report_prod_quality'))] = '';
				
				$enveloppe = $ci->pbf->calculate_final_indicator_payment(
																			$raw_report_info['list_quality']['datafile_total'],
																			str_replace(',','',$r_val['indicator_montant']),
																			$entity['entity_pbf_group_id'],
																			$function_str
																			);
																			
											
				$buffer[$r_key][utf8_decode($ci->lang->line('report_prod_avail_budget'))] = number_format($enveloppe);
				
				if(($r_val['indicator_category']!='Paludisme PMA' && $r_val['indicator_category']!='Paludisme PCA')){
					
					$buffer[$r_key][utf8_decode($ci->lang->line('report_prod_quality'))] = $raw_report_info['list_quality']['datafile_total'];
					$buffer[$r_key][utf8_decode($ci->lang->line('report_prod_category_1'))] = number_format($enveloppe);
					$category_1 += $enveloppe;
					
					$buffer[$r_key][utf8_decode($ci->lang->line('report_prod_category_4'))] ='';
					
					$tot_subsidies += str_replace(',','',$r_val['indicator_montant']);
					}
				
				if(($r_val['indicator_category']=='Paludisme PMA' || $r_val['indicator_category']=='Paludisme PCA')){
					
					$buffer[$r_key][utf8_decode($ci->lang->line('report_prod_category_1'))] = '';
					$buffer[$r_key][utf8_decode($ci->lang->line('report_prod_category_4'))] = number_format($enveloppe);
					$category_4 += $enveloppe;			
					}				
				}
				
				$final_payment = $ci->pbf->calculate_final_payment(	$raw_report_info['list_quality']['datafile_total'],
																	$tot_subsidies,
																	$entity['entity_class'],
																	$entity['entity_type'],
																	$entity['entity_pbf_group_id'],
																	$ci->input->post('entity_geozone_id'),
																	$function_str_entity);
				
		
				$report['total_invoice']= $final_payment;

		
				
				
				
			$buffer[$r_key+1][ucwords($ci->lang->line('indicator_title'))] = 
												'<b>'.$ci->lang->line('report_sub_tot').' '.$ci->lang->line('report_prod_category_1').'</b>';
			$buffer[$r_key+1][utf8_decode($ci->lang->line('report_prod_category_1'))] = '<b>'.number_format($category_1).'</b>';
			$buffer[$r_key+2][ucwords($ci->lang->line('indicator_title'))] = 
												'<b>'.$ci->lang->line('report_sub_tot').' '.$ci->lang->line('report_prod_category_4').'</b>';
			$buffer[$r_key+2][utf8_decode($ci->lang->line('report_prod_category_4'))] = '<b>'.number_format($category_4).'</b>';
			
			$plafond = $ci->pbf->get_regional_avg(	$ci->input->post('datafile_year'),
														$ci->input->post('datafile_quarter'),
														'',
														$entity['entity_pbf_group_id']);
														
			
														
			$buffer[$r_key+3][ucwords($ci->lang->line('indicator_title'))] = 
												'<b>'.$ci->lang->line('report_final_plafond').' '.str_replace('C','P',$entity['entity_group_abbrev']).'</b>';
			$buffer[$r_key+3][utf8_decode($ci->lang->line('report_prod_avail_budget'))] = '<b>'.number_format($plafond).'</b>';
			
			$buffer[$r_key+4][ucwords($ci->lang->line('indicator_title'))] = 
												'<b>'.$ci->lang->line('report_sub_tot_payable').' '.$ci->lang->line('report_prod_category_1').'</b>';
			$buffer[$r_key+4][utf8_decode($ci->lang->line('report_prod_avail_budget'))] = '<b>'.number_format($final_payment).'</b>';
			
			$buffer[$r_key+5][ucwords($ci->lang->line('indicator_title'))] = 
												'<b>'.$ci->lang->line('report_final_tot').'</b>';
			$buffer[$r_key+5][utf8_decode($ci->lang->line('report_prod_avail_budget'))] = '<b>'.number_format($final_payment+$category_4).'</b>';
							
			$somearray[utf8_decode($ci->lang->line('report_prod_forecast'))]['justification']='right';
			$somearray[utf8_decode($ci->lang->line('report_prod_achieved'))]['justification']='right';
			$somearray[utf8_decode($ci->lang->line('report_prod_unit_fee'))]['justification']='right';
			$somearray[utf8_decode($ci->lang->line('report_prod_quality'))]['justification']='right';
			$somearray[utf8_decode($ci->lang->line('report_prod_avail_budget'))]['justification']='right';
			$somearray[utf8_decode($ci->lang->line('report_prod_category_1'))]['justification']='right';
			$somearray[utf8_decode($ci->lang->line('report_prod_category_4'))]['justification']='right';
			
			$somearray[utf8_decode($ci->lang->line('report_prod_forecast'))]['width']=80;
			$somearray[utf8_decode($ci->lang->line('report_prod_achieved'))]['width']=80;
			$somearray[utf8_decode($ci->lang->line('report_prod_unit_fee'))]['width']=80;
			$somearray[utf8_decode($ci->lang->line('report_prod_quality'))]['width']=80;
			$somearray[utf8_decode($ci->lang->line('report_prod_avail_budget'))]['width']=80;
			$somearray[utf8_decode($ci->lang->line('report_prod_category_1'))]['width']=80;
			$somearray[utf8_decode($ci->lang->line('report_prod_category_4'))]['width']=80;
			
			/*print_r($buffer);
			exit;*/
				
				
			$ci->cezpdf->ezTable($buffer,'','',array('fontSize' => 7,'width'=>$table_width,'cols'=>$somearray));
					
			$ci->cezpdf->ezSetDy(-3);
		
		$ci->cezpdf->ezText(utf8_decode(trim($ci->lang->line('report_sub_tot_payable').' '.$ci->lang->line('report_prod_category_1'))).': <b> '.$ci->config->item('app_country_currency').' '.number_format($final_payment).' ('.$ci->numbers_words->toWords(($final_payment),($ci->config->item('language_abbr')=='en')?'en_US':$ci->config->item('language_abbr')).' '.$ci->config->item('app_country_currency').').</b> ',8,array('justification'=>'left'));
		
		$ci->cezpdf->ezText(utf8_decode(trim($ci->lang->line('report_sub_tot_payable').' '.$ci->lang->line('report_prod_category_4'))).': <b> '.$ci->config->item('app_country_currency').' '.number_format($category_4).' ('.$ci->numbers_words->toWords(($category_4),($ci->config->item('language_abbr')=='en')?'en_US':$ci->config->item('language_abbr')).' '.$ci->config->item('app_country_currency').').</b> ',8,array('justification'=>'left'));
		
		$ci->cezpdf->ezSetDy(-2);
		
		$ci->cezpdf->ezText(utf8_decode($ci->lang->line('report_total_amount_in_letter')).'  <b>  '.$ci->config->item('app_country_currency').' '.number_format($final_payment+$category_4).'('.$ci->numbers_words->toWords(($final_payment+$category_4),($ci->config->item('language_abbr')=='en')?'en_US':$ci->config->item('language_abbr')).' '.$ci->config->item('app_country_currency').').</b> ',8,array('justification'=>'left'));
		$new=0;
		foreach($contrib_fac_globale as $contrib_glog){
		if(($global_percentage<100)&&($contrib_glog['donor_id']==$sql_primor_details['donor_id'])){
			$new=1;
			//$part=number_format(($final_payment+$category_4)*($contrib_glog['percentage']+$global_diff)/100);
			$ci->cezpdf->ezText('          '.'<b>'.$contrib_glog['donor_name'].'('.$contrib_glog['percentage'] .'%)+('.$global_diff.'%)'.'</b>'.'  <b>  '.$ci->config->item('app_country_currency').' '.number_format(($final_payment+$category_4)*($contrib_glog['percentage']+$global_diff)/100).'('.$ci->numbers_words->toWords((($final_payment+$category_4)*($contrib_glog['percentage']+$global_diff)/100),($ci->config->item('language_abbr')=='en')?'en_US':$ci->config->item('language_abbr')).' '.$ci->config->item('app_country_currency').').</b> ',8,array('justification'=>'left'));
		
		}else{
			//$part=number_format(($final_payment+$category_4)*($contrib_glog['percentage'])/100);
			$ci->cezpdf->ezText('          '.'<b>'.$contrib_glog['donor_name'].'('.$contrib_glog['percentage'] .'%)'.'</b>'.'  <b>  '.$ci->config->item('app_country_currency').' '.number_format(($final_payment+$category_4)*($contrib_glog['percentage'])/100).'('.$ci->numbers_words->toWords((($final_payment+$category_4)*($contrib_glog['percentage'])/100),($ci->config->item('language_abbr')=='en')?'en_US':$ci->config->item('language_abbr')).' '.$ci->config->item('app_country_currency').').</b> ',8,array('justification'=>'left'));
		}
		}
		
		if($new==0){
		$ci->cezpdf->ezText('          '.'<b>'.$sql_primor_details['donor_name'].'('.$global_diff .'%)'.'</b>'.'  <b>  '.$ci->config->item('app_country_currency').' '.number_format(($final_payment+$category_4)*$global_diff/100).'('.$ci->numbers_words->toWords(($final_payment+$category_4),($ci->config->item('language_abbr')=='en')?'en_US':$ci->config->item('language_abbr')).' '.$ci->config->item('app_country_currency').').</b> ',8,array('justification'=>'left'));
		}
		
				
		
		$ci->cezpdf->ezNewPage();
		// creates the HEADER and FOOTER for the document we are creating.
		
		
		

		
		prep_pdf(	$params['report_page_layout'],
					$params['report_logo_position'],
					$params['report_title'],
					$params['report_subtitle'],
					$params_array['param_caption'],
					'add',
					$params['report_header'],
					$params['report_id']); 

			
		$ci->cezpdf->ezText('<b>FACTURE TRIMESTRIELLE FOSA POUR RELAIS  COMMUNAUTAIRES</b>',11,array('justification'=>'center'));
		
					$sql = "SELECT pbf_indicators.indicator_id,pbf_indicatorstranslations.indicator_title,SUM(pbf_datafiledetails.indicator_claimed_value) AS indicator_claimed_value,SUM(pbf_datafiledetails.indicator_verified_value) AS indicator_verified_value,SUM(pbf_datafiledetails.indicator_validated_value) AS indicator_validated_value,pbf_datafiledetails.indicator_tarif AS indicator_tarif,SUM(pbf_datafiledetails.indicator_montant) AS indicator_montant FROM pbf_indicators LEFT JOIN pbf_datafiledetails ON (pbf_indicators.indicator_id=pbf_datafiledetails.indicator_id) LEFT JOIN pbf_indicatorstranslations ON (pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id) LEFT JOIN pbf_datafile ON (pbf_datafile.datafile_id = pbf_datafiledetails.datafile_id) WHERE pbf_datafile.filetype_id='6' AND pbf_datafile.entity_id = '".$ci->input->post('entity_id')."' AND pbf_datafile.datafile_quarter = '".$ci->input->post('datafile_quarter')."' AND pbf_datafile.datafile_year = '".$ci->input->post('datafile_year')."' AND pbf_indicatorstranslations.indicator_language ='fr' GROUP BY pbf_indicators.indicator_id";
			
			$results = $ci->db->query($sql)->result_array();
			
			$sql = "SELECT pbf_datafile.datafile_total FROM pbf_datafile WHERE pbf_datafile.filetype_id='7' AND pbf_datafile.entity_id = '".$ci->input->post('entity_id')."' AND pbf_datafile.datafile_quarter = '".$ci->input->post('datafile_quarter')."' AND pbf_datafile.datafile_year = '".$ci->input->post('datafile_year')."'";
			
			$qresults = $ci->db->query($sql)->row_array();
			
			$totalrc = 0;
			$indicator_montant = 0;
			$payabletot = 0;
			$counter = 1;
			
			$buffer=array();
			
			foreach($results as $k => $result){
				if($result['indicator_id'] != '51'){
				$buffer[$k]['<b>No</b>'] = $counter;
				$buffer[$k]['<b>Indicateur</b>'] = utf8_encode(utf8_decode($result['indicator_title']));
				$buffer[$k][utf8_decode('<b>Déclarée</b>')] = number_format($result['indicator_claimed_value']);
				$buffer[$k][utf8_decode('<b>Vérifiée</b>')] = number_format($result['indicator_verified_value']);
				$buffer[$k][utf8_decode('<b>Prix unitaire</b>')] = number_format($result['indicator_tarif']);
				$buffer[$k]['<b>Total</b>'] = number_format($result['indicator_montant']);
				$indicator_montant += $result['indicator_montant'];
				$counter++;
				}
				else{
					$totalrc = $result['indicator_claimed_value'];
					}
				
				}
				
		$buffer[$counter]['<b>No</b>'] = '';
		$buffer[$counter]['<b>Indicateur</b>'] = '<b>TOTAL</b>';
		$buffer[$counter][utf8_decode('<b>Déclarée</b>')] = '';
		$buffer[$counter][utf8_decode('<b>Vérifiée</b>')] = '';
		$buffer[$counter]['<b>Prix unitaire</b>'] = '';
		$buffer[$counter]['<b>Total</b>'] = number_format($indicator_montant);
		
		$somearray=array();
		$somearray['<b>No</b>']['justification']='right';
		$somearray[utf8_decode('<b>Déclarée</b>')]['justification']='right';
		$somearray[utf8_decode('<b>Vérifiée</b>')]['justification']='right';
		$somearray['<b>Prix unitaire</b>']['justification']='right';
		$somearray['<b>Total</b>']['justification']='right';
			
		$somearray[utf8_decode('<b>Déclarée</b>')]['width']=80;
		$somearray[utf8_decode('<b>Vérifiée</b>')]['width']=80;
		$somearray['<b>Prix unitaire</b>']['width']=80;
		$somearray['<b>Total</b>']['width']=80;
		
		$ci->cezpdf->ezSetDy(-20);
		
		$ci->cezpdf->ezTable($buffer,'','',array('fontSize' => 7,'width'=>$table_width,'cols'=>$somearray));
		
		$ci->cezpdf->ezSetDy(-20);
		
		$ci->cezpdf->ezText(utf8_decode('Nombre de relais  communautaires dans le district: <b>').number_format($totalrc).'</b>',8,array('justification'=>'left'));
		$ci->cezpdf->ezSetDy(-10);
		$ci->cezpdf->ezText(utf8_decode('Le score qualité trimestrielle des relais communautaires: <b>').number_format($qresults['datafile_total'],2).'%</b>',8,array('justification'=>'left'));
		
		$payabletot = round(($indicator_montant * $qresults['datafile_total'])/100);
		
		$ci->cezpdf->ezSetDy(-10);
		$ci->cezpdf->ezText(utf8_decode('Le montant alloué à la structure de santé pour les relais communautaires: <b>').number_format($payabletot).' FCFA'.' ('.$ci->numbers_words->toWords(($payabletot),($ci->config->item('language_abbr')=='en')?'en_US':$ci->config->item('language_abbr')).' '.$ci->config->item('app_country_currency').').</b>',8,array('justification'=>'left'));
		
		$ci->cezpdf->ezSetDy(-20);
		
		$signatories = json_decode($params['report_signatories'],true);
		$sign[0]['1'] = $signatories[0];
		$sign[0]['2'] = $signatories[1];
		$sign[0]['3'] = $signatories[2];
		
		$ci->cezpdf->ezTable(	$sign,
								'',
								'',
								array(	'fontSize' => 10,
										'showHeadings' => 0,
										'rowGap' => '0',
										'showLines' => 0,
										'shaded' => '0',
										'width'	=>	$table_width,
										'cols'	=> array(	'1' => array('justification'=>'left','width'=>($table_width/3)),
															'2' => array('justification'=>'left','width'=>($table_width/3)),
															'3' => array('justification'=>'left','width'=>($table_width/3)))));
			
			}
		else{
			
			$ci->cezpdf->ezText($ci->lang->line('no_data_display'),8,array('justification'=>'left'));
			
			}
						
		//$report_id = $ci->pbf_mdl->insert_report($report);
		
		//$ci->cezpdf->ezText('report_ID:'.$report_id,8,array('justification'=>'left'));
		
	
		$file = './cside/reports/'.$ci->config->item('report_prefix').'_'.$params['report_id'].'.pdf';
		
	
				$pdfcode = $ci->cezpdf->ezOutput();
				//$fp=fopen(base_url().'cside/reports/test.pdf','wb');
		$fp=fopen($file,'wb');
		fwrite($fp,$pdfcode);
		
		fclose($fp);
		header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename="the.pdf"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($file));
		@readfile($file);
		
	//	}
		
		}
		}