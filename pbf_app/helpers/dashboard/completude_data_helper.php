<?php
    function completude_data_helper($geozone_id=0){		
		$ci = &get_instance();
		$ci->load->model('dashboard_mdl');
		$ci->load->model('report_mdl');
		$ci->lang->load('datafiles', $ci->config->item('language'));
		$ci->lang->load('files', $ci->config->item('language'));
		$ci->lang->load('dashboard', $ci->config->item('language'));
		
		$business_time = ($ci->session->userdata('business_time')=='')?$ci->pbf->get_current_business_time():$ci->session->userdata('business_time');
		
		$get_data_quarters = $ci->dashboard_mdl->get_data_quarter();
		
		
		
		$data_quarters = '';
		
		foreach($get_data_quarters as $get_data_quarters_key => $get_data_quarters_val){
			
			$data_quarters[base_url().'dashboard/showquarter/'.$get_data_quarters_val['datafile_quarter'].'/'.$get_data_quarters_val['datafile_year']] = $ci->lang->line('app_quarter_'.$get_data_quarters_val['datafile_quarter']).' '.$get_data_quarters_val['datafile_year'];
			
			}
		
			if(count($data_quarters)==1)
		{
			if($get_data_quarters_val['datafile_quarter']==4){
				$get_data_quarters_val['datafile_quarter']=1;
				$get_data_quarters_val['datafile_year']=$get_data_quarters_val['datafile_year']+1;
			}	
		$data_quarters[base_url().'dashboard/showquarter/'.($get_data_quarters_val['datafile_quarter']+1).'/'.$get_data_quarters_val['datafile_year']] = $ci->lang->line('app_quarter_'.($get_data_quarters_val['datafile_quarter']+1)).' - '.($get_data_quarters_val['datafile_year']);
			
		
		
			
		}
		
		$general_compteteness = $ci->dashboard_mdl->get_general_completeness($business_time);
		
		$dashboard_year = $business_time['year'];
				
		$dynamic_headers = isset($general_compteteness[0])?array_keys($general_compteteness[0]):NULL;
		
		$dynamic_headers = isset($dynamic_headers)?array_slice($dynamic_headers, 3):NULL;
		
		
		/////                                    
		
		
		
		
		
		$html='
					<div class="block">
					<div class="block_head">'.
					
					heading(strtoupper($ci->lang->line('dashb_completeness_box_title')).' ['.$ci->lang->line('dashb_trim_abbr').$business_time['quarter'].':'.$business_time
					['year'].']',2).
					
					
				'</div>		<!-- .block_head ends -->
				

				
				<div class="block_content" style=" height:530px; overflow-y:scroll">
					<table cellspacing="1" cellpadding="0" border="0" class="tablesorter" id="table_completeness">
					<thead>

			<tr>
				<th class="header">'. $ci->lang->line('dashb_filetype').'</th>';
				
              
                foreach($dynamic_headers as $dynamic_header){
				if($dynamic_header!='frequency'){
				
				$html.='<th class="header">'. $ci->lang->line('app_month_'.$dynamic_header).'</th>';
               
				}
				}
				
		$html.=	'</tr>
		</thead>
		<tbody>	';

			foreach($general_compteteness as $k_compteteness => $v_compteteness){
	
				$frequency = json_decode($v_compteteness['frequency'],true);
				unset($v_compteteness['frequency']);
	
	$html.='<tr id="'.$v_compteteness['filetype_id'].'">
	  <td>'.
	     $ci->lang->line('filetype_ky_'.$v_compteteness['filetype_id']).
	
	'</td>';
              
               
                foreach($dynamic_headers as $dynamic_header){
					if(in_array($dynamic_header, $frequency)){
						if($v_compteteness[$dynamic_header]< $v_compteteness['awaited_rpt_number'])
						{
							$style='style="cursor:pointer; color:green;" class="plus" ';
						}else{
							$style='';
						}
						
				
				$html.='<td '.$style.'title="'.$dynamic_header.'" abbr="'. $dashboard_year.'">'.
				 $v_compteteness[$dynamic_header].'/'.$v_compteteness['awaited_rpt_number'].
				
				'</td>';
                
					}
				elseif($dynamic_header!='frequency'){
					$html.= "<td>&nbsp;</td>";
					}
				}
				
	$html.='</tr>';
	
	}
$html.=
'</tbody>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="filters">
  <tr>
    <td>';
$js = 'id="data_quarters" onChange="jump_data_quarters(\'parent\',this,0);"';
$html.=form_dropdown('data_quarters',$data_quarters,base_url().'dashboard/showquarter/'.$business_time['quarter'].'/'.$business_time['year'],$js).
'</td>
  </tr>
</table>
				</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr">
				</div>
				</div>
				
				';
	return $html;	
}	
		
	

		