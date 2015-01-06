<?php 

    function donor_helper() {
    	//Mettre tous les info concernant la completude
    	$ci = &get_instance();
		
		$submit_data=$ci->input->post();
		$user_id=$ci->session->userdata('user_id');
		$user_group=$ci->pbf->get_usergroup($user_id);
		
		$sql="SELECT * FROM pbf_donors WHERE groupassociated_id='".$user_group."'"; 
		$donor_data=$ci->db->query($sql)->row_array();
		
		$sql="SELECT pbf_donors.*,pbf_donorsentity_details.entity_id,pbf_donorsconf_details.indicator_id,pbf_donorsconf_details.percentage  FROM pbf_donorsentity_details LEFT JOIN pbf_donorsconf_details ON (pbf_donorsentity_details.donorconf_id=pbf_donorsconf_details.conf_details_id)
			LEFT JOIN pbf_donorsconfig ON (pbf_donorsconfig.donorconfig_id=pbf_donorsconf_details.donor_conf_id) LEFT JOIN pbf_donors ON(pbf_donors.donor_id=pbf_donorsconfig.donor_id) 
			LEFT JOIN pbf_entities ON (pbf_entities.) 
			
			
			
			
			WHERE pbf_donorsentity_details.entity_id='".$params['entity_id']."'";
		
		
		
		
		
		
		
		
			
		$logo=substr($donor_data['donor_logopath'], 0 , (strrpos($donor_data['donor_logopath'], ".")));
		$html='<div class="block">
				<div class="block_head">
								
					<h2>'.$donor_data['donor_name'].
					'</h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
				
				<div style="float:left; width:45%">'.
				'<img  src="'.$ci->config->item('base_url').'/cside/frontend/temp/'.$logo.'_med.jpg'.'" border="0">
				
				</div>
					
				
				<div style="width:45%; float:right">';
												
				$html.='</div>
						
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>
				
		';
		return $html;	
}
	
	function donor_pie($serie,$titre){
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