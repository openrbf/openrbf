<?php
    function fraud_helper() {
     
		$ci = &get_instance();
		$ci->lang->load('dashboard', 'en');
		$ci->lang->load('dashboard', 'fr');
        

		
		$business_time = ($ci->session->userdata('business_time')=='')?$ci->pbf->get_current_business_time():$ci->session->userdata('business_time');
		
		$get_data_quarters = $ci->dashboard_mdl->get_data_quarter();
		
		
		
		$data_quarters = '';
		
		foreach($get_data_quarters as $get_data_quarters_key => $get_data_quarters_val){
		 	
		 $data_quarters[base_url().'dashboard/showquarter/'.$get_data_quarters_val['datafile_quarter'].'/'.$get_data_quarters_val['datafile_year']] = $ci->lang->line('app_quarter_'.$get_data_quarters_val['datafile_quarter']).' '.$get_data_quarters_val['datafile_year'];
		 	
		}
		
		
	     $html= '<div class="block"><div class="block_head">
					<h2>'.strtoupper($ci->lang->line('dashb_fraud')).' ['.$ci->lang->line('dashb_trim_abbr').$business_time['quarter'].':'.$business_time
					['year'].']</h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">
				
					<table cellspacing="1" cellpadding="0" border="0" class="tablesorter" id="table_logs_summary">
						<thead>
							<tr>
							<th class="header">District</th>';
									foreach($business_time['months'] as $month){
							           $html.= '<th class="header">'.$ci->lang->line('app_month_'.$month).'</th>';
									}
        
							$html.='	</tr>
						</thead>
						<tbody>';	
        
         $sql_append = "";
        
         $usergeozones = $ci->session->userdata('usergeozones');
        
         if(!empty($usergeozones)){
           
          $sql_append.=" AND pbf_entities.entity_geozone_id IN (".implode(',',$usergeozones).") ";
          //		  $sql_append.=" AND pbf_filetypesgeozones.geozone_id IN (".implode(',',$usergeozones).") ";

           
         }

         $sql = "SELECT pa_geo.geozone_name as district";
         foreach($business_time['months'] as $month){
          $sql .=", SUM(IF(pbf_datafile.datafile_month='".$month."',1,0)) AS '".$month."' ";
         }
        
        
         $sql .= " FROM pbf_geozones LEFT JOIN pbf_geozones pa_geo ON pbf_geozones.geozone_parentid = pa_geo.geozone_id LEFT JOIN pbf_entities ON (pbf_entities.entity_geozone_id=pbf_geozones.geozone_id) LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id = pbf_entities.entity_id AND pbf_datafile.filetype_id='8'".$sql_append." AND pbf_datafile.datafile_year='".$business_time['year']."') WHERE pbf_geozones.geozone_parentid IS NOT NULL AND pbf_geozones.geozone_active ='1' GROUP BY pbf_geozones.geozone_parentid";
     
        
       	$fraud_data = $ci->db->query($sql)->result_array();			


		foreach($fraud_data as $fraud_zone){

					$html.=	'<tr>
								<td>'.$fraud_zone['district'].'</td>';  
					foreach($business_time["months"] as $month){
								$html.=	'<td>'.$fraud_zone[$month].'</td>';  
								  }
								
						$html.=	'</tr>';
	
						}

			$html.=	'</tbody>
					</table>';
				


				
		 $sql = "SELECT pbf_entities.entity_name,pbf_entities.entity_id,pbf_datafile.datafile_id, pbf_entities.entity_geozone_id as entity_geozone_id, pbf_geozones.geozone_parentid as level_0, pbf_datafile.datafile_month,pbf_datafile.datafile_year,pbf_datafile.datafile_file_upload";
		
		 $sql .= " FROM pbf_entities LEFT JOIN pbf_datafile ON pbf_datafile.entity_id = pbf_entities.entity_id LEFT JOIN pbf_geozones ON pbf_geozones.geozone_id = pbf_entities.entity_geozone_id WHERE pbf_datafile.filetype_id='8' AND pbf_datafile.datafile_year='".$business_time['year']."' AND pbf_datafile.datafile_month IN (".implode(',',$business_time['months']).")".$sql_append;

		 $reports = $ci->db->query($sql)->result_array();
		 $lien_report=null;
		
		 if (!empty($reports)) {

	foreach ($reports as $report) {
	 $rep[$report['datafile_id']]= $report['entity_name']." : ". $report['datafile_month']."/".$report['datafile_year'];
	 $lien_report=$report['datafile_file_upload'];
	 $options[$report['datafile_id']]['entity_id']= $report['entity_id'];
	 $options[$report['datafile_id']]['level_0']= $report['level_0'];
	 $options[$report['datafile_id']]['entity_geozone_id']= $report['entity_geozone_id'];
	 $options[$report['datafile_id']]['datafile_month']= $report['datafile_month'];
	 $options[$report['datafile_id']]['datafile_year']= $report['datafile_year'];
	 }
	$html.= form_open('report/show','onSubmit="return popWindow(this.target)" target="report_show" id="report_window"');
	$html.= '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="filters">
  	<tr><td>';
    $html.= form_label($ci->lang->line('dashb_report'));
  	$html.= form_dropdown('fraud_report', $rep,'','id = "fraud_report" onChange="change_fields(this.value);"');
    $html.='</td><td>';
	$html.= '<input type="hidden" name="report_id" id="report_id" value="11"/>';
	$html.= '<input type="hidden" name="level_0" id="level_0" value="'.$report['level_0'].'"/>';
	$html.= '<input type="hidden" name="entity_geozone_id" id="entity_geozone_id" value="'.$report['entity_geozone_id'].'"/>';
	$html.= '<input type="hidden" name="entity_id" id="entity_id" value="'.$report['entity_id'].'"/>';
	$html.= '<input type="hidden" name="datafile_month" id="datafile_month" value="'.$report['datafile_month'].'"/>';
	$html.= '<input type="hidden" name="datafile_year" id="datafile_year" value="'.$report['datafile_year'].'"/>';
	$html .='</td><td>';
	$html.=form_submit('open', 'open', 'class="submit small"');
	$html .='</td></tr>';
	$script = '<script>
    var opt ='.json_encode($options)
	.'; function change_fields(value){
   	var data = opt[value];
		        
		     document.getElementById("level_0").value = data.level_0;
		     document.getElementById("entity_id").value = data.entity_id;
		     document.getElementById("entity_geozone_id").value = data.entity_geozone_id;
		     document.getElementById("datafile_month").value = data.datafile_month;
		     document.getElementById("datafile_year").value = data.datafile_year;
			 
     	}
   
   </script>';
	}else {
	 $html.= '
	   <table width="100%" border="0" cellspacing="0" cellpadding="0" class="filters">
    <tr>
    <td>'.$ci->lang->line('dashb_fraud_no_report').'</td>';
	 
	}
	$html.=
	'
    <td>';
	$js = 'id="data_quarters" onChange="jump_data_quarters(\'parent\',this,0);"';
	$html.=form_dropdown('data_quarters',$data_quarters,base_url().'dashboard/showquarter/'.$business_time['quarter'].'/'.$business_time['year'],$js).
	'</td>
	
	</tr>
	
	</table></div>
	<div class="bendl"></div>
				<div class="bendr"></div>
	</div>';
	
	$html=$html.$script;

	return $html;	
		
}		

