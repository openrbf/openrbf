<?php

    function budget_helper() {
		
		$entity_id=505;
		$mont=4;
		$year=2014;
		$months = $ci->pbf->get_monthsBy_quarter($mont);
		//$mont_list=$ci->pbf->get_monthsBy_quarter($params['datafile_quarter']);
		
			
		
		
		$ci = &get_instance();
		$sql_payements = "SELECT SUM(pbf_datafiledetails.indicator_montant) AS MONTANT
					FROM pbf_geozones LEFT JOIN pbf_geozones as geo ON (geo.geozone_id=pbf_geozones.geozone_parentid) 
					LEFT JOIN pbf_entities ON pbf_entities.entity_geozone_id =pbf_geozones.geozone_id
					LEFT JOIN pbf_banks ON pbf_banks.bank_id=pbf_entities.entity_bank_id
					LEFT JOIN pbf_datafile ON pbf_entities.entity_id=pbf_datafile.entity_id
					LEFT JOIN pbf_datafiledetails ON (pbf_datafiledetails.datafile_id=pbf_datafile.datafile_id)
					JOIN pbf_indicators ON  pbf_indicators.indicator_id=pbf_datafiledetails.indicator_id
					JOIN pbf_indicatorsfileypes ON (pbf_indicators.indicator_id = pbf_indicatorsfileypes.indicator_id AND pbf_datafile.filetype_id=pbf_indicatorsfileypes.filetype_id) 
					JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) 
					JOIN pbf_lookups ON (pbf_filetypes.filetype_contenttype=pbf_lookups.lookup_id AND pbf_lookups.lookup_linkfile='content_type') 
					LEFT JOIN pbf_indicatorstranslations ON (pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id) 
					WHERE  pbf_entities.entity_id='".$entity_id."' AND
					pbf_datafile.datafile_month='".$mont."' AND pbf_datafile.datafile_year='".$year."' AND 
					(LAST_DAY('".$year."-".$mont."-1') BETWEEN pbf_indicatorsfileypes.use_from AND pbf_indicatorsfileypes.use_to) AND pbf_indicatorstranslations.indicator_language ='fr'  
					GROUP BY pbf_entities.entity_id
					ORDER BY geo.geozone_name ASC
					";
					$results_details=$ci->db->query($sql_payements)->row_array();	
									
		$sql_budgets = "SELECT SUM(pbf_datafiledetails.indicator_montant) AS MONTANT
					FROM pbf_geozones LEFT JOIN pbf_geozones as geo ON (geo.geozone_id=pbf_geozones.geozone_parentid) 
					LEFT JOIN pbf_entities ON pbf_entities.entity_geozone_id =pbf_geozones.geozone_id
					LEFT JOIN pbf_banks ON pbf_banks.bank_id=pbf_entities.entity_bank_id
					LEFT JOIN pbf_datafile ON pbf_entities.entity_id=pbf_datafile.entity_id
					LEFT JOIN pbf_datafiledetails ON (pbf_datafiledetails.datafile_id=pbf_datafile.datafile_id)
					JOIN pbf_indicators ON  pbf_indicators.indicator_id=pbf_datafiledetails.indicator_id
					JOIN pbf_indicatorsfileypes ON (pbf_indicators.indicator_id = pbf_indicatorsfileypes.indicator_id AND pbf_datafile.filetype_id=pbf_indicatorsfileypes.filetype_id) 
					JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) 
					JOIN pbf_lookups ON (pbf_filetypes.filetype_contenttype=pbf_lookups.lookup_id AND pbf_lookups.lookup_linkfile='content_type') 
					LEFT JOIN pbf_indicatorstranslations ON (pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id) 
					WHERE  pbf_entities.entity_id='".$entity_id."' AND
					pbf_datafile.datafile_month='".$mont."' AND pbf_datafile.datafile_year='".$year."' AND 
					(LAST_DAY('".$year."-".$mont."-1') BETWEEN pbf_indicatorsfileypes.use_from AND pbf_indicatorsfileypes.use_to) AND pbf_indicatorstranslations.indicator_language ='fr'  
					GROUP BY pbf_entities.entity_id
					ORDER BY geo.geozone_name ASC
					";
					$results_details=$ci->db->query($sql_payements)->row_array();					
				
	
		$total_payment='';
		$total_budget='';
    	//Mettre tous les info concernant la completude
    	$ci = &get_instance();
	    $business_time = ($ci->session->userdata('business_time')=='')?$ci->pbf->get_current_business_time():$ci->session->userdata('business_time');
		$pie_general_compteteness = $ci->dashboard_mdl->get_general_completeness_per_type($business_time);
		
		$QtyRempli=intval(number_format(43421*100/$results_details['MONTANT'],0,'',''));
		//$QtyNotRempli=intval(number_format(($pie_general_compteteness['TotQty']-$pie_general_compteteness['Qty'])*100/$pie_general_compteteness['TotQty'],0,'',''));
		$QtyNotRempli=100-$QtyRempli;
		$serie_qty['data']	= array(
		array($ci->lang->line('dashb_pie_filled'), $QtyRempli),
		array($ci->lang->line('dashb_pie_not_filled'), $QtyNotRempli),
		array('qsqsqsqsq',$QtyRempli),
		array('qsqsq',$QtyRempli)
		);
		
				
		
		
		
		$chart_qty=($pie_general_compteteness['TotQty']>0)? draw_pie_budget($serie_qty,$serie_qty,$ci->lang->line('dashb_pie_qty')):'';
		
		$html='<div class="block">
				<div class="block_head">
								
					<h2>'.$ci->lang->line('dashb_budget_title').' ['.$ci->lang->line('dashb_trim_abbr').$business_time['quarter'].':'.$business_time
					['year'].']</h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
				
				<div style="float:left; width:45%">'.
				 $chart_qty.
				
				'</div>
					
				
				<div style="width:45%; float:right">'.
				$chart_qly.
								
				'</div>
						
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>
				
		';
		return $html;	
}
	
	function draw_pie_budget($serie,$serie1,$titre){
		$ci = &get_instance();
		$ci->load->library('highcharts');
		
		$plot->pie->dataLabels->enabled = false;
		$credits->enabled = false;
        $callback = "function() { return '<b>'+ this.point.name +'</b>: '+ this.y +'%'}";
		$tool->formatter = $callback;
		//$plot->pie->dataLabels->formatter = $callback;
		$ci->highcharts->set_type('column');
		$ci->highcharts->set_serie($serie,'Budget');
		$ci->highcharts->set_serie($serie1,'Payements');
		$ci->highcharts->set_title($titre);
		$ci->highcharts->set_axis_titles();
		$ci->highcharts->set_plotOptions($plot);
		$ci->highcharts->set_tooltip($tool);
		$ci->highcharts->set_credits($credits);
		$ci->highcharts->set_dimensions('250','250');
		
		//$this->highcharts->render_to('graph_div');
		return $ci->highcharts->render();
	}
?>

