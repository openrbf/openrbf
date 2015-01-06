<?php
//error_reporting(E_ALL);	
function equipe_cadre($params)
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
			
			// score qualite de regulation -> id : 84
			//Crédit FBR de la période (FCFA) -> id: 85
			
			$sql = "SELECT pbf_datafiledetails.indicator_validated_value FROM pbf_datafiledetails LEFT JOIN pbf_datafile ON (pbf_datafiledetails.datafile_id = pbf_datafile.datafile_id) WHERE pbf_datafiledetails.indicator_id = '84' AND pbf_datafile.filetype_id = '11' AND pbf_datafile.entity_id = '".$ci->input->post('entity_id')."' AND pbf_datafile.datafile_month = '".$params['datafile_month']."' AND pbf_datafile.datafile_year = '".$params['datafile_year']."'";
			
			$score = $ci->db->query($sql)->row_array();

			$sql = "SELECT pbf_datafiledetails.indicator_validated_value FROM pbf_datafiledetails LEFT JOIN pbf_datafile ON (pbf_datafiledetails.datafile_id = pbf_datafile.datafile_id) WHERE pbf_datafiledetails.indicator_id = '85' AND pbf_datafile.filetype_id = '11' AND pbf_datafile.entity_id = '".$ci->input->post('entity_id')."' AND pbf_datafile.datafile_month = '".$params['datafile_month']."' AND pbf_datafile.datafile_year = '".$params['datafile_year']."'";
				
			$credit = $ci->db->query($sql)->row_array();


	
	if (!isset($credit['indicator_validated_value']))
	   $credit['indicator_validated_value']='';
	if (!isset($score['indicator_validated_value']))
	 $score['indicator_validated_value']='';
			
			$row = array();
			//$row[] = array('data' => $entity_info['geozone_name'], 'align' => 'center', 'width' => '2%');
			$row[] = array('data' => 'Equipe d encadrement de la Zone Sanitaire de: '.$entity_info['geozone_name']);
			$row[] = array('data' => utf8_decode($ci->lang->line('app_month_'.$params['datafile_month'])).' '.$params['datafile_year']);
	//		$row[] = array('data' => '<strong>'.number_format($indicator_montant).'</strong>','align' => 'right', 'width' => '10%');
			$ci->table->add_row($row);
			unset($row);
			
			
			$headers[] = utf8_decode('Structure de Regulation');
			$headers[] = utf8_decode('Periode');
			
			$ci->table->set_heading($headers);
			unset($headers);
		
		$ci->dpdf->content .= '<p>&nbsp;</p><p style="font-size:10px">'.$ci->table->generate().'</p>';

			
		
		
		$ci->table->clear();

	
		
		$row = array();
		//$row[] = array('data' => $entity_info['geozone_name'], 'align' => 'center', 'width' => '2%');
		$row[] = array('data' => 'EEZS '.$entity_info['geozone_name']);
		$row[] = array('data' => $score['indicator_validated_value'].' %', 'align' => 'center');
		$row[] = array('data' => $credit['indicator_validated_value'], 'align' => 'center');
		//$row[] = array('data' => utf8_decode($ci->lang->line('app_month_'.$params['datafile_quarter'])).' '.$params['datafile_year']);
		//		$row[] = array('data' => '<strong>'.number_format($indicator_montant).'</strong>','align' => 'right', 'width' => '10%');
		$ci->table->add_row($row);
		unset($row);
			
			
		$headers[] = utf8_decode('Structure');
		$headers[] = utf8_decode('Score Performance de la periode');
		$headers[] = utf8_decode('Credits FBR de la periode (FCFA)');
			
		$ci->table->set_heading($headers);
			
		
		$ci->dpdf->content .= '<p>&nbsp;</p><p style="font-size:10px">'.$ci->table->generate().'</p>';
		
		
		$ci->dpdf->content .= '<p>&nbsp;</p><p style="font-size:10px">Certifiee par la firme chargee de la verification (AEDES - Scen Afrik)</p>';
		
		
		$ci->dpdf->html($ci->dpdf->content);
		$ci->dpdf->create();	
			
	}   

