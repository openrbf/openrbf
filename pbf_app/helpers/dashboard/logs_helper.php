<?php
    function logs_helper() {
		$ci = &get_instance();
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

		
		$html= '<div class="block"><div class="block_head">
					<h2>'.strtoupper($ci->lang->line('dashb_logs')).' </h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
				<div id="logresults">
					<table cellspacing="1" cellpadding="0" border="0" class="tablesorter" id="table_logs_summary">
						<thead>
							<tr>
								<th class="header">'.$ci->lang->line('dashb_logs_date') .'</th>
								<th class="header">'.$ci->lang->line('dashb_logs_users') .'</th>
								<th class="header">'.$ci->lang->line('dashb_logs_descr') .'</th>
							</tr>
						</thead>
						<tbody>';	
	
		$sql="SELECT pbf_users.user_fullname as username,pbf_syseventlog.event_time as date,pbf_syseventlog.event as event FROM pbf_users  LEFT JOIN pbf_syseventlog ON(pbf_users.user_id=pbf_syseventlog.user_id) WHERE pbf_syseventlog.publish=1 ORDER BY pbf_syseventlog.event_time DESC LIMIT 0,5";
		$general_logs_data = $ci->db->query($sql)->result_array();			

		foreach($general_logs_data as $logs){

					$html.=	'<tr>
								<td>'.$logs['date'].'</td>  
								<td>'.$logs['username'].'</td>  
								<td>';
								if ($ci->lang->line($logs['event'])=='')
								{ 
								$html.= $logs['event'];
								}
								else {
								$html.=  $ci->lang->line($logs['event']);
								}
								
								
					$html.= '</td>  
							</tr>';
	
						}

			$html.=	'</tbody>
					</table>
				</div>

		<p>'. anchor('/dashboard/log_details',$ci->lang->line('dashb_logs_details'),'').'</p>
					
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
		</div>';
		

			return $html;	
		
}		

