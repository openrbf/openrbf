<?php
//error_reporting  (E_ALL);
	function monthly_invoice($params){
		
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
					$params_array['param_caption']); 
					
		$sql = "SELECT pbf_indicatorsfileypes.order AS '#',pbf_indicatorstranslations.indicator_title AS 'Indicateur',FORMAT(pbf_datafiledetails.indicator_claimed_value,0) AS 'Q. Declaree',FORMAT(pbf_datafiledetails.indicator_verified_value,0) AS 'Q. Verifiee',FORMAT(pbf_datafiledetails.indicator_tarif,0) AS 'Bareme',FORMAT(pbf_datafiledetails.indicator_montant,0) AS 'Montant' FROM pbf_indicators JOIN pbf_datafiledetails ON (pbf_indicators.indicator_id=pbf_datafiledetails.indicator_id) JOIN pbf_datafile ON (pbf_datafiledetails.datafile_id=.pbf_datafile.datafile_id) JOIN pbf_indicatorsfileypes ON (pbf_indicators.indicator_id = pbf_indicatorsfileypes.indicator_id AND pbf_datafile.filetype_id=pbf_indicatorsfileypes.filetype_id) JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) JOIN pbf_lookups ON (pbf_filetypes.filetype_contenttype=pbf_lookups.lookup_id AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_indicatorstranslations ON (pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id) WHERE pbf_datafile.entity_id='".$params['entity_id']."' AND pbf_datafile.datafile_month='".$params['datafile_month']."' AND pbf_datafile.datafile_year='".$params['datafile_year']."' AND (LAST_DAY('".$params['datafile_year']."-".$params['datafile_month']."-1') BETWEEN pbf_indicatorsfileypes.use_from AND pbf_indicatorsfileypes.use_to) AND pbf_indicatorstranslations.indicator_language ='fr' ORDER BY pbf_indicatorsfileypes.order";
		
		$buffer = $ci->db->query($sql)->result_array();
		
		if(!empty($buffer)){
		
		$somearray=array();
		$table_footer=array();
		
		foreach($buffer[0] as $col => $val){
			
			if(is_numeric(str_replace(',','',$val))){
				$somearray[$col]['justification']='right';
			}
			$table_footer[$col] = '';
			}
	
		// prepare the totals of the table
		
		$array_tot_cols = explode(',',trim($params['report_col_total']));
		
		foreach($buffer as $k => $v){
			
			foreach($array_tot_cols as $column){
			
					$table_footer[$column] += str_replace(',','',$buffer[$k][$column]);
				
				}
			
			}
			
		// bolding the footer
		$a=1;
		foreach($table_footer as $table_footer_k => $table_footer_v){
			
			// if is numeric, number format it.
			$table_footer[$table_footer_k] = '<b>'.(is_numeric($table_footer_v)?number_format($table_footer_v):$table_footer_v).'</b>';
			
			if($a == 2){
				
				$table_footer[$table_footer_k] = '<b> '.$ci->lang->line('report_final_tot').'</b>';
				
				}
			$a++;
			}
		
		array_push($buffer,$table_footer);
		
		$ci->cezpdf->ezTable($buffer,'','',array('fontSize' => 9,'width'=>$table_width,'cols'=>$somearray));
		
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
										'cols'	=> array(	'1' => array('justification'=>'left','width'=>183),
															'2' => array('justification'=>'left','width'=>184),
															'3' => array('justification'=>'left','width'=>183))));
										
		//$ci->cezpdf->ezText(utf8_decode(trim($params['report_signatories'])),10,array('justification'=>'left'));
		
		}
		
		else{
			
		$ci->cezpdf->ezText('No data to display',10,array('justification'=>'left'));
			
			}
		
		$ci->cezpdf->ezStream();
		}