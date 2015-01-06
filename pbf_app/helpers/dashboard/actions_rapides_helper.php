<?php
    function actions_rapides_helper() {
		$ci = &get_instance();
		$ci->lang->load('dashboard', 'en');
		$ci->lang->load('dashboard', 'fr');
		
      		
		$html= '<div class="block"><div class="block_head">
								
					<h2>'.strtoupper($ci->lang->line('dashb_rapidaction_box_title')).'</h2>
				</div>		<!-- .block_head ends -->
				
				
				
				<div class="block_content" style=" height:180px">
				
					 <ul><li>'.anchor('datafiles/datamngr/',$ci->lang->line('dashb_data_entry')).
					'</li><li>'.anchor('report',$ci->lang->line('dashb_report')).
					'</li><li>'.anchor('acl/profile/',$ci->lang->line('dashb_profile_upd')).
					'</li><li>'.anchor('help/',$ci->lang->line('dashb_help_link')).
					'</li><li>'.anchor(base_url(),$ci->lang->line('dashb_back_to_site')).'</li></ul></div>		<!-- .block_content ends -->
				
				
					<div class="bendl"></div>
					<div class="bendr"></div>	
				</div>';
	return $html;	
}		

