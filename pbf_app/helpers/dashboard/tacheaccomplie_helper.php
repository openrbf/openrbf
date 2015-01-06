<?php

    function tacheaccomplie_helper() {
    	//Mettre tous les info concernant la completude
    	$ci = &get_instance();
	    $business_time = ($ci->session->userdata('business_time')=='')?$ci->pbf->get_current_business_time():$ci->session->userdata('business_time');
		$pie_general_compteteness = $ci->dashboard_mdl->get_general_completeness_per_type($business_time);
		
		$QtyRempli=intval(number_format($pie_general_compteteness['Qty']*100/$pie_general_compteteness['TotQty'],0,'',''));
		//$QtyNotRempli=intval(number_format(($pie_general_compteteness['TotQty']-$pie_general_compteteness['Qty'])*100/$pie_general_compteteness['TotQty'],0,'',''));
		$QtyNotRempli=100-$QtyRempli;
		$serie_qty['data']	= array(
		array($ci->lang->line('dashb_pie_filled'), $QtyRempli),
		array($ci->lang->line('dashb_pie_not_filled'), $QtyNotRempli));
		
		$QlyRempli=intval(number_format($pie_general_compteteness['Qly']*100/$pie_general_compteteness['TotQly'],0,'',''));
		//$QlyNotRempli=intval(number_format(($pie_general_compteteness['TotQly']-$pie_general_compteteness['Qly'])*100/$pie_general_compteteness['TotQly'],0,'',''));
		$QlyNotRempli=100-$QlyRempli;
		$serie_qly['data']	= array(
		array($ci->lang->line('dashb_pie_filled'), $QlyRempli),
		array($ci->lang->line('dashb_pie_not_filled'),$QlyNotRempli));
		$chart_qty=($pie_general_compteteness['TotQty']>0)? draw_pie($serie_qty,$ci->lang->line('dashb_pie_qty')):'';
		
		$chart_qly= ($pie_general_compteteness['TotQly']>0)? draw_pie($serie_qly,$ci->lang->line('dashb_pie_qly')):'';
		$html='<div class="block">
				<div class="block_head">
								
					<h2>'.$ci->lang->line('dashb_tache_title').' ['.$ci->lang->line('dashb_trim_abbr').$business_time['quarter'].':'.$business_time
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
	
	function draw_pie($serie,$titre){
		$ci = &get_instance();
		$ci->load->library('highcharts');
		
		$plot->pie->dataLabels->enabled = false;
		$credits->enabled = false;
        $callback = "function() { return '<b>'+ this.point.name +'</b>: '+ this.y +'%'}";
		$tool->formatter = $callback;
		//$plot->pie->dataLabels->formatter = $callback;
		$ci->highcharts->set_type('pie');
		$ci->highcharts->set_serie($serie);
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

