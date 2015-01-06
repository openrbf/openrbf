<?php

	function quarterly_consolidated_indicator_zone_control($params)
	{
		$ci = get_instance();
		
			$ci->load->library('fpdf/fpdf_lib');
			
			$params=$ci->input->post();
			$months = $ci->pbf->get_monthsBy_quarter($params['datafile_quarter']);
			$months_word = $ci->pbf->get_monthsBy_quarter_word($params['datafile_quarter']);
			$plafond=0;
			
			$function_str = $ci->pbf->get_runnable_pbf_script_for_indicator($ci->input->post('datafile_year'),$ci->input->post('datafile_quarter'));	
				
			$function_str_entity = $ci->pbf->get_runnable_script($ci->input->post('datafile_year'),$ci->input->post('datafile_quarter'));
	
			$entities=$ci->pbf_mdl->render_zone_entities_group($params['entity_geozone_id']);
			
			$data=array();$controlGroup_id=array(11,12,13,14,15,16,17,18);$traitementGroup_Id=array(3,4,5,6,7,8,9,10,19);			
			$temp=0;$score_qlt_global=0;$nbre_entities_control=0;
			
			
			
		$montantTheorique=0;
		$linehf_tot=0;
			if(!empty($entities)){
				foreach($entities as $e_key=>$e_val){
					$raw_quarterly_info=$ci->report_mdl->get_quarterly_entity_report_indicator($params,$e_val['entity_id']);
					
					
					
				
				if(!empty($raw_quarterly_info['list_quantity']) &&  in_array($e_val['entity_group_id'],$controlGroup_id)){
					$montantTheorique=0;					
					$plafond = $ci->pbf->get_regional_avg(	$ci->input->post('datafile_year'),
														$ci->input->post('datafile_quarter'),
														'',
														$e_val['entity_group_id']);															
					$score_qlt=$raw_quarterly_info['list_quality']['datafile_total'];
					/*temp1 represente le montant payé à cette fosa*/
					$temp1=$ci->report_mdl->get_quarterly_payment_entity_order_info($params,$e_val['entity_id']);
					
					
					
					
					
					$linehf_tot += $ci->pbf->calculate_final_payment(	$score_qlt,
													$temp1[0]['tot_subsidies'],
													1,
													$e_val['entity_type_id'],
													$e_val['entity_group_id'],
													$ci->input->post('entity_geozone_id'),
													$function_str_entity);
					$argentpaye=$ci->pbf->calculate_final_payment(	$score_qlt,
													$temp1[0]['tot_subsidies'],
													1,
													$e_val['entity_type_id'],
													$e_val['entity_group_id'],
													$ci->input->post('entity_geozone_id'),
													$function_str_entity);
					
					
					/*calcule du montant q un fosa aurait du avoir en PBF normal*/
					
					foreach($raw_quarterly_info['list_quantity'] as $r_key=>$r_val)
					{
						$montantTheorique+=str_replace(',','',$r_val['indicator_montant']);
					}
					
					
					
					$indice=($montantTheorique/$argentpaye);
					
					
					foreach($raw_quarterly_info['list_quantity'] as $r_key=>$r_val){
						
						
						
						$data[$r_val['indicator_id']]['order']=$r_val['order'];
						$data[$r_val['indicator_id']]['indicator_title']=($r_val['quality_associated']!=0)?$r_val['indicator_title']:$r_val['indicator_title']."*";						
						
						$data[$r_val['indicator_id']]['quantite']+=$r_val['verified_value'];						
						$data[$r_val['indicator_id']]['prix_unitaire']=$r_val['default_tarif'];
						
						
						$data[$r_val['indicator_id']]['enveloppe_fbr']+=str_replace(',','',$r_val['indicator_montant']);
				 if($plafond!=0){
					 
												
						$data[$r_val['indicator_id']]['enveloppe_fbr']+=str_replace(',','',$r_val['indicator_montant']);
				 if($plafond!=0){
						$data[$r_val['indicator_id']]['enveloppe_fbr_versee']+=($montantTheorique>$plafond)?(str_replace(',','',$r_val['indicator_montant'])/$indice):str_replace(',','',$r_val['indicator_montant']);
						
						
						$data[$r_val['indicator_id']]['enveloppe_fbr_sousPlafond']+=($montantTheorique>$plafond)?0:str_replace(',','',$r_val['indicator_montant']);
						
						$data[$r_val['indicator_id']]['enveloppe_fbr_dessusPlafond']+=($montantTheorique>$plafond)?(str_replace(',','',$r_val['indicator_montant'])/$indice):0;
						
	if($r_val['quality_associated']!=0){
	
	 $data[$r_val['indicator_id']]['cat1']=$data[$r_val['indicator_id']]['enveloppe_fbr_versee'];
	 $data[$r_val['indicator_id']]['cat4']='';
	}
	else{
	  $data[$r_val['indicator_id']]['cat1']='';
	 $data[$r_val['indicator_id']]['cat4']=$data[$r_val['indicator_id']]['enveloppe_fbr_versee'];
	
	}
						
						}
						
						else{
						$data[$r_val['indicator_id']]['enveloppe_fbr_versee']+=0;
						
						
						$data[$r_val['indicator_id']]['enveloppe_fbr_sousPlafond']+=0;
						
						$data[$r_val['indicator_id']]['enveloppe_fbr_dessusPlafond']+=0;
						
						}
						
						
						
																				
						
						
						
																	
					}
					}
					
					
				}
				
				}	
			 
				
			}
			
	
	$geo_zone=$ci->geo_mdl->get_zone($params['entity_geozone_id']);	
	$quarter=$ci->input->post('datafile_quarter');
	
	$parametres=array('Facture trimestrielle par indicateur et par zone pour le groupe Contrôle',$geo_zone['geozone_name'],$ci->input->post('datafile_quarter'),$ci->input->post('datafile_year'));
	
	$header=array('#','Indicateur',utf8_decode('Production réalisée sur le trimestre'),utf8_decode('prix unitaire en FCFA'),utf8_decode('Enveloppe FBR (produit*prix) en FCFA'),utf8_decode('Enveloppe FBR versée en FCFA'),utf8_decode('ENveloppe FBR pour FS sous plafonds'),utf8_decode('Enveloppe FBR pour FS au dessus du plafond'),utf8_decode('Catégorie 1'),utf8_decode('Catégorie 4'));		
	$pdf = new FPDF_LIB('L', 'mm', 'A4',$parametres);
	$pdf->addpage('L');
	
	
	
	//$pdf->Cell(150,10,utf8_decode('Facture trimestrielle par indicateur et par zone pour le groupe Contrôle'),0,0,'L');
	/*$pdf->SetFont('Arial','B',9);
	$pdf->Ln(10);
	$pdf->SetFillColor(192,192,192);	
	$pdf->Cell(38,10,utf8_decode('Zone sanitaire de : '),0,0,'L',true);
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(180,10,utf8_decode($geo_zone['geozone_name']),0,0,'L',true);
	$pdf->SetFont('Arial','B',9);
	$pdf->Cell(18,10,utf8_decode('Période : '),0,0,'L',true);
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(42,10,"Trimestre".$ci->input->post('datafile_quarter')."    ".$ci->input->post('datafile_year'),0,0,'L',true);	
	$pdf->Ln(15);*/
	
	$pdf->facture_trim_indic_zon_controle_table($header,$data,($ci->config->item('language_abbr')=='en')?'en_US':$ci->config->item('language_abbr'),$linehf_tot);
	
	
	
	$pdf->SetFillColor(255,255,255);
	$pdf->Ln(15);
	$pdf->SetFont('Arial','B',9);
	$pdf->Cell(200,5,utf8_decode('Signataire 1'),0,0,'L',true);
	$pdf->Cell(100,5,utf8_decode('Signataire 2'),0,0,'L',true);
	$pdf->Ln();
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(200,10,utf8_decode('Nom et Prénom.......................'),0,0,'L',true);
	$pdf->Cell(100,10,utf8_decode('Nom et Prénom.......................'),0,0,'L',true);
	$pdf->SetAuthor('Jean Claude');
	$pdf->Output();
			
	}