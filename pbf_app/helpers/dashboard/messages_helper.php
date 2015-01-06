<?php 
	function messages_helper(){
		
		$ci = &get_instance();
		$ci->lang->load('dashboard', 'en');
		$ci->lang->load('dashboard', 'fr');	 
		
		  
		  $html='<div class="block"> <div class="block_head">
										<h2>'.strtoupper($ci->lang->line('dashb_message_box_title')).'</h2>
									</div><!-- .block_head ends -->
					
					<div class="block_content">
							<table>
						<thead>
								<tr>
									<th class="header">'.$ci->lang->line('dashb_message_box_title').'</th>
									<th class="header">'.$ci->lang->line('dash_user').'</th>
								</tr>
						</thead>
						
						<tbody>';
			

			$sql="SELECT * FROM pbf_message pbfm JOIN pbf_users pdfu ON pbfm.user_id=pdfu.user_id WHERE pbfm.checked=0 ORDER BY message_id DESC LIMIT 10";

			$general_message_data=$ci->db->query($sql)->result_array();
			
					foreach($general_message_data as $messages){
						$html.='<tr><td>'.$messages['message'].'</td><td>'.$messages['user_fullname'].'</td>
						<td>
						<a href="'.base_url().'dashboard/deletemessage/'.$messages['message_id'].'">
							<img alt="Close" width="20" height="20" src ='.base_url().'cside/images/icons/close.png border:\"0\'/>
						</a></td></tr>';
					}
					
				$html.='</tbody>
							</table>
						
					</div><!-- .block_content ends -->
						<div class="bendl"></div>
						<div class="bendr"></div>
	                 	</div>';
	
				return $html;
					
	}
?>