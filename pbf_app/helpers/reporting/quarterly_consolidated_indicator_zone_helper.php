<?php

		/* rapport trimestriel par zone et par catégorie */
		function quarterly_consolidated_indicator_zone($params)
		{
			$ci = get_instance();
			
			$ci->load->library('fpdf/fpdf_lib');
			$params=$ci->input->post();
			$months = $ci->pbf->get_monthsBy_quarter($params['datafile_quarter']);
			$months_word = $ci->pbf->get_monthsBy_quarter_word($params['datafile_quarter']);
			$plafond=0;$plafond2=0;
			$diff=0;
			
			$function_str = $ci->pbf->get_runnable_pbf_script_for_indicator($ci->input->post('datafile_year'),$ci->input->post('datafile_quarter'));	
	
			$function_str_entity = $ci->pbf->get_runnable_script($ci->input->post('datafile_year'),$ci->input->post('datafile_quarter'));	
	
			$entities=$ci->pbf_mdl->render_zone_entities_group($params['entity_geozone_id']);
			
			
			$data=array();$data_control=array();$traitementGroup_Id=array(3,4,5,6,7,8,9,10,19);$controlGroup_id=array(11,12,13,14,15,16,17,18);$sous_tot_trait_mois1=0;$sous_tot_trait_mois2=0;$sous_tot_trait_mois3=0;$sous_tot_trait_trim=0;$sous_tot_control_mois1=0;$sous_tot_control_mois2=0;$sous_tot_control_mois3=0;$sous_tot_control_trim=0;$linehf_tot=0;$payable_fosa=array();
			
		
			if(!empty($entities)){
				foreach($entities as $e_key=>$e_val){
					$raw_quarterly_info=$ci->report_mdl->get_quarterly_entity_report_indicator($params,$e_val['entity_id']);
					
										
				if(!empty($raw_quarterly_info['list_quality']) &&  in_array($e_val['entity_group_id'],$traitementGroup_Id)){
					$score_qlt=$raw_quarterly_info['list_quality']['datafile_total'];					
					
					$dig_quality = $ci->report_mdl->get_quality_evaluation($ci->input->post('datafile_year'),$ci->input->post('datafile_quarter'),$e_val['entity_id']);
					
					
					$temp1=$ci->report_mdl->get_quarterly_payment_entity_order_info($params,$e_val['entity_id']);
					$montant_paye_fosa=  $ci->pbf->calculate_final_payment(	$score_qlt,
													$temp1[0]['tot_subsidies'],
													1,
													$e_val['entity_type_id'],
													$e_val['entity_group_id'],
													$ci->input->post('entity_geozone_id'),
													$function_str_entity);
					$montant_paye_fosa_indicator=0;							
					foreach($raw_quarterly_info['list_quantity'] as $r_key=>$r_val){
						
					
					$montant_paye_fosa_indicator+=((($r_val['quality_associated']==1)?($ci->pbf->calculate_final_indicator_payment(
																			$score_qlt,
																			str_replace(',','',$r_val[$months[0]]),
																			$e_val['entity_group_id'],
																			$function_str
																			)):str_replace(',','',$r_val[$months[0]]))+(($r_val['quality_associated']==1)?($ci->pbf->calculate_final_indicator_payment(
																			$score_qlt,
																			str_replace(',','',$r_val[$months[1]]),
																			$e_val['entity_group_id'],
																			$function_str
																			)):str_replace(',','',$r_val[$months[1]]))+(($r_val['quality_associated']==1)?($ci->pbf->calculate_final_indicator_payment(
																			$score_qlt,
																			str_replace(',','',$r_val[$months[2]]),
																			$e_val['entity_group_id'],
																			$function_str
																			)):str_replace(',','',$r_val[$months[2]])));
					}
													
					$diff+=(($montant_paye_fosa_indicator-$montant_paye_fosa));
					
										
					foreach($raw_quarterly_info['list_quantity'] as $r_key=>$r_val){
											
						$data[$r_val['indicator_id']]['order']=$r_val['order'];
						$data[$r_val['indicator_id']]['indicator_title']=($r_val['quality_associated']!=0)?$r_val['indicator_title']:$r_val['indicator_title']."*";						
						
						$data[$r_val['indicator_id']]['credit_mois1']+=($r_val['quality_associated']==1)?round($ci->pbf->calculate_final_indicator_payment(
																			$score_qlt,
																			str_replace(',','',$r_val[$months[0]]),
																			$e_val['entity_group_id'],
																			$function_str
																			)):str_replace(',','',$r_val[$months[0]]);									
						$data[$r_val['indicator_id']]['credit_mois2']+=($r_val['quality_associated']==1)?$ci->pbf->calculate_final_indicator_payment(
																			$score_qlt,
																			str_replace(',','',$r_val[$months[1]]),
																			$e_val['entity_group_id'],
																			$function_str
																			):str_replace(',','',$r_val[$months[1]]);
						
																	
						$data[$r_val['indicator_id']]['credit_mois3']+=(($r_val['quality_associated']==1)?$ci->pbf->calculate_final_indicator_payment(
																			$score_qlt,
																			str_replace(',','',$r_val[$months[2]]),
																			$e_val['entity_group_id'],
																			$function_str
																			):str_replace(',','',$r_val[$months[2]]));
						
						$data[$r_val['indicator_id']]['credit_trim']+=($r_val['quality_associated']==1)?$ci->pbf->calculate_final_indicator_payment(
																			$score_qlt,
																			str_replace(',','',$r_val['indicator_montant']),
																			$e_val['entity_group_id'],
																			$function_str
																			):str_replace(',','',$r_val['indicator_montant']);
																			
																			
				
																
					}
				
					
					
				}
				
				if(!empty($raw_quarterly_info['list_quality']) &&  in_array($e_val['entity_group_id'],$controlGroup_id)){
					$score_qlt=$raw_quarterly_info['list_quality']['datafile_total'];
					    
										
					
					$temp1=$ci->report_mdl->get_quarterly_payment_entity_order_info($params,$e_val['entity_id']);
					
					$linehf_tot += $ci->pbf->calculate_final_payment(	$score_qlt,
													$temp1[0]['tot_subsidies'],
													1,
													$e_val['entity_type_id'],
													$e_val['entity_group_id'],
													$ci->input->post('entity_geozone_id'),
													$function_str_entity);
					
				
					
					foreach($raw_quarterly_info['list_quantity'] as $r_key=>$r_val){
						
						if($r_val['quality_associated']==0){
						
						$data_control[$r_val[indicator_id]]['order']=$r_val['order'];
						$data_control[$r_val[indicator_id]]['indicator_title']=$r_val['indicator_title']."*";						
						
						$data_control[$r_val[indicator_id]]['credit_mois1']+=str_replace(',','',$r_val[$months[0]]);									
						$data_control[$r_val[indicator_id]]['credit_mois2']+=str_replace(',','',$r_val[$months[1]]);	
						$data_control[$r_val[indicator_id]]['credit_mois3']+=str_replace(',','',$r_val[$months[2]]);	
						$data_control[$r_val[indicator_id]]['credit_trim']+=str_replace(',','',$r_val['indicator_montant']);
					
										 						
						
						}
																		
					}
					
				}
				
				}	
				
			}
			
		$data[1]['credit_mois3']-=$diff;	
			
	$geo_zone=$ci->geo_mdl->get_zone($params['entity_geozone_id']);	
	$quarter=$ci->input->post('datafile_quarter');
	$parametres=array('Facture trimestrielle par indicateur et par zone',$geo_zone['geozone_name'],$ci->input->post('datafile_quarter'),$ci->input->post('datafile_year'));
		
	$header=array('#',utf8_decode('Indicateur'),utf8_decode("Crédits ".$months_word[0]." (avec note qte)"),utf8_decode("Crédits ".$months_word[1]." (avec note qte)"),utf8_decode("Crédits ".$months_word[2]." (avec note qte)"),utf8_decode('Total trimestre'));		
	$pdf = new FPDF_LIB('P', 'mm', 'A4',$parametres);
	$pdf->addpage('P');
	
	
	/*$pdf->Cell(150,10,utf8_decode('Facture trimestrielle par indicateur et par zone'),0,0,'L');
	$pdf->SetFont('Arial','B',9);
	$pdf->Ln(10);
	$pdf->SetFillColor(192,192,192);	
	$pdf->Cell(38,10,utf8_decode('Zone sanitaire de : '),0,0,'L',true);
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(90,10,utf8_decode($geo_zone['geozone_name']),0,0,'L',true);
	$pdf->SetFont('Arial','B',9);
	$pdf->Cell(20,10,utf8_decode('Période : '),0,0,'L',true);
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(42,10,"Trimestre".$ci->input->post('datafile_quarter')."    ".$ci->input->post('datafile_year'),0,0,'L',true);*/	
	$pdf->Ln(5);
	
	
	$pdf->facture_trim_indic_zon_table($header,$data,$data_control,$linehf_tot);
	$pdf->Ln(5);
	
	//$pdf->Ln(10);
	//$pdf->AddPage($ci->CurOrientation);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Ln(10);
	$pdf->SetFont('Arial','B',9);
	$pdf->Cell(100,5,utf8_decode('Signataire 1'),0,0,'L',true);
	$pdf->Cell(100,5,utf8_decode('Signataire 2'),0,0,'L',true);
	$pdf->Ln();
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(100,10,utf8_decode('Nom et Prénom.......................'),0,0,'L',true);
	$pdf->Cell(100,10,utf8_decode('Nom et Prénom.......................'),0,0,'L',true);
	$pdf->SetAuthor('Jean Claude');
	$pdf->Output();
	}