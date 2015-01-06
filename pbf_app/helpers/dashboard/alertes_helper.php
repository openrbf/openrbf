<?php
    function alertes_helper() {
		$ci = &get_instance();
		$user_id=$ci->session->userdata('user_id');
		$ci->lang->load('dashboard', $ci->config->item('language'));
		$ci->lang->load('auth', $ci->config->item('language'));
		$ci->lang->load('acl', $ci->config->item('language'));
		$ci->lang->load('budgets', $ci->config->item('language'));
		$ci->lang->load('cms', $ci->config->item('language'));
		$ci->lang->load('datafiles', $ci->config->item('language'));
		$ci->lang->load('exports', $ci->config->item('language'));
		$ci->lang->load('fees', $ci->config->item('language'));
		$ci->lang->load('files', $ci->config->item('language'));
		$ci->lang->load('hfrentities', $ci->config->item('language'));
		$ci->lang->load('management', $ci->config->item('language'));
		$ci->lang->load('indicators', $ci->config->item('language'));
		$ci->lang->load('geo', $ci->config->item('language'));
		$ci->lang->load('otheroptions', $ci->config->item('language'));
		$ci->lang->load('alertes', $ci->config->item('language'));
		
		$html= '<div class="block"><div class="block_head">
					<h2>'.strtoupper($ci->lang->line('alertes_title')).' </h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
				<div id="logresults">
					<table cellspacing="1" cellpadding="0" border="0" class="tablesorter" id="table_logs_summary">
						<thead>
							<tr>
								<th class="header">'.$ci->lang->line('helper_alerte_title') .'</th>
								<th class="header">'.$ci->lang->line('helper_alerte_month') .'</th>
								<th class="header">'.$ci->lang->line('form_alerte_quarter') .'</th>
								<th class="header">'.$ci->lang->line('helper_alerte_year') .'</th>
								<th class="header">'.$ci->lang->line('helper_alerte_sentdate') .'</th>
							</tr>
						</thead>
						<tbody>';	
	
		$sql="SELECT pbf_alertes.alerte_id as id,pbf_alerteconfig.alerte_title as title,pbf_alertes.month as month,pbf_alertes.quarter as quarter,pbf_alertes.year as year,pbf_alertes.date_alerte as date FROM pbf_alertes 
		LEFT JOIN pbf_alerteconfig ON (pbf_alertes.type_alerte=pbf_alerteconfig.alerteconfig_id) WHERE pbf_alerteconfig.alerte_dashboard=1 AND user_id='".$user_id."'";
		$general_logs_data = $ci->db->query($sql)->result_array();			

		$comp=0;
		foreach($general_logs_data as $logs){

					$html.=	'<tr>
								<td>'.anchor('/alertes/details_alerte/'.$logs['id'],$logs['title'],'').'</td> 
								<td>'.$logs['month'].'</td> 
								<td>'.$logs['quarter'].'</td>  
								<td>'.$logs['year'].'</td> 
								<td>'.$logs['date'].'</td> 								
							</tr>';
							$comp++;
	
						}

			$html.=	'</tbody>
					</table>
				</div>
		
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
		</div>';
		
		
		if ($comp==0){
			$html='';
		}
				
			return $html;	
		
}		

