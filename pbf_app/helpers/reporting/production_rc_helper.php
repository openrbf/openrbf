<?php
//error_reporting(E_ALL);	
function production_rc($params)
		{
			$ci = get_instance();
			$config = $ci->report_mdl->get_reports_conf($ci->input->post('report_id'));
			
			$ci->load->library(array('dpdf','numbers/numbers_words'));
			$ci->dpdf->folder('./cside/pdfs/');
			$ci->dpdf->filename(str_ireplace(' ','',$config['report_title']).'.pdf');			
			$ci->dpdf->paper('a4', $config['report_page_layout']); 
			$ci->table->set_template($ci->dpdf->table_tmpl); 
			$ci->dpdf->content .= $ci->dpdf->set_header();
			
			$ci->load->model(array('entities_mdl'));
			$entity_info = $ci->entities_mdl->get_entity($ci->input->post('entity_id'));
			
			$sql = "SELECT pbf_indicators.indicator_id,pbf_indicatorstranslations.indicator_title,SUM(pbf_datafiledetails.indicator_claimed_value) AS indicator_claimed_value,SUM(pbf_datafiledetails.indicator_verified_value) AS indicator_verified_value,SUM(pbf_datafiledetails.indicator_validated_value) AS indicator_validated_value,pbf_datafiledetails.indicator_tarif AS indicator_tarif,SUM(pbf_datafiledetails.indicator_montant) AS indicator_montant FROM pbf_indicators LEFT JOIN pbf_datafiledetails ON (pbf_indicators.indicator_id=pbf_datafiledetails.indicator_id) LEFT JOIN pbf_indicatorstranslations ON (pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id) LEFT JOIN pbf_datafile ON (pbf_datafile.datafile_id = pbf_datafiledetails.datafile_id) WHERE pbf_datafile.filetype_id='6' AND pbf_datafile.entity_id = '".$ci->input->post('entity_id')."' AND pbf_datafile.datafile_quarter = '".$ci->input->post('datafile_quarter')."' AND pbf_datafile.datafile_year = '".$ci->input->post('datafile_year')."' AND pbf_indicatorstranslations.indicator_language ='fr' GROUP BY pbf_indicators.indicator_id";
			
			$results = $ci->db->query($sql)->result_array();
			
			$sql = "SELECT pbf_datafile.datafile_total FROM pbf_datafile WHERE pbf_datafile.filetype_id='7' AND pbf_datafile.entity_id = '".$ci->input->post('entity_id')."' AND pbf_datafile.datafile_quarter = '".$ci->input->post('datafile_quarter')."' AND pbf_datafile.datafile_year = '".$ci->input->post('datafile_year')."'";
			
			$qresults = $ci->db->query($sql)->row_array();
			
			$row = array();
			$totalrc = 0;
			$indicator_montant = 0;
			$payabletot = 0;
			$counter = 1;
			
			foreach($results as $k => $result){
				if($result['indicator_id'] != '51'){
					$row[] = array('data' => ($counter), 'align' => 'right', 'valign' => 'middle', 'width' => '2%');
					$row[] = array('data' => utf8_decode($result['indicator_title']));
					$row[] = array('data' => number_format($result['indicator_claimed_value']), 'align' => 'right', 'width' => '10%');
					$row[] = array('data' => number_format($result['indicator_verified_value']), 'align' => 'right', 'width' => '10%');
					$row[] = array('data' => number_format($result['indicator_tarif']), 'align' => 'right', 'width' => '10%');
					$row[] = array('data' => number_format($result['indicator_montant']), 'align' => 'right', 'width' => '10%');
					$ci->table->add_row($row);
					unset($row);
					$indicator_montant += $result['indicator_montant'];
					$counter++;
				}
				else{
					$totalrc = $result['indicator_claimed_value'];
					}
				}
				
		$row = array();
		$row[] = array('data' => '', 'align' => 'right', 'width' => '2%');
		$row[] = array('data' => '<strong>TOTAL</strong>');
		$row[] = array('data' => '');
		$row[] = array('data' => '');
		$row[] = array('data' => '');
		$row[] = array('data' => '<strong>'.number_format($indicator_montant).'</strong>','align' => 'right', 'width' => '10%');
		$ci->table->add_row($row);
		unset($row);
		
		$headers[] = utf8_decode('No');
		$headers[] = utf8_decode('Indicateur');
		$headers[] = utf8_decode('Déclarée');
		$headers[] = utf8_decode('Vérifiée');
		$headers[] = utf8_decode('Prix unitaire');
		$headers[] = utf8_decode('Total');
	
		$ci->table->set_heading($headers);	
			
		$ci->dpdf->content .= '<p align="center"><b><u>'.strtoupper(utf8_decode($params['report_title'])).'</u></b></p><p><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><strong>REGION SANITAIRE: </strong>'.$entity_info['parent_geozone_name'].'</td>
    <td align="right">&nbsp;</td>
  </tr>
  <tr>
    <td><strong>ZONE SANITAIRE: </strong>'.$entity_info['geozone_name'].'</td>
    <td align="right"></td>
  </tr>
  <tr>
    <td><strong>FORMATION SANITAIRE: </strong>'.$entity_info['entity_name'].' '.$entity_info['entity_type_abbrev'].'</td>
    <td align="right"><strong>NOMBRE DE RC:</strong> '.number_format($totalrc).'</td>
  </tr>
  <tr><td><strong>PERIODE :</strong> '.utf8_decode($ci->lang->line('app_quarter_'.$params['datafile_quarter'])).'</td><td align="right"><strong>ANNEE :</strong> '.$params['datafile_year'].'</td></tr></table></p>';

			
			$ci->dpdf->content .= '<p>&nbsp;</p><p>'.$ci->table->generate().'</p><br><br>';
			
			$ci->dpdf->content .= '<p style="font-size:10px">'.utf8_decode('Le score qualité trimestrielle des relais communautaires: <strong>').number_format($qresults['datafile_total'],2).'%</strong></p><br><br>';
			
			$payabletot = round(($indicator_montant * $qresults['datafile_total'])/100);
			
			$ci->dpdf->content .= '<p style="font-size:10px">'.utf8_decode('Le montant alloué à la structure de santé pour les relais communautaires: <strong>').number_format($payabletot).' FCFA</strong></p><br><br>';
			
			$ci->dpdf->content .= '<p style="font-size:10px">'.utf8_decode('Arrêté la présente '.utf8_decode($params['report_title']).' pour le '.utf8_decode($ci->lang->line('app_quarter_'.$params['datafile_quarter'])).' '.$params['datafile_year'].' à la somme de <b>'.$ci->numbers_words->toWords($payabletot,'fr').' Francs CFA').'.</b></p>';
			
		$ci->table->clear();
		
		$signatories = json_decode($params['report_signatories'],true);
		
		$ci->table->set_template($ci->dpdf->sign_tmpl);
		
		$ci->table->add_row(	
								array('data' => utf8_decode($signatories[0]),'width' => '33%'),
								array('data' => utf8_decode($signatories[1]),'width' => '34%'),
								array('data' => utf8_decode($signatories[2]),'width' => '33%') );
		
		$ci->dpdf->content .= '<p style="font-size:10px">'.$ci->table->generate().'</p>';
			
		$ci->dpdf->html($ci->dpdf->content);
		$ci->dpdf->create();	
			
	}   

