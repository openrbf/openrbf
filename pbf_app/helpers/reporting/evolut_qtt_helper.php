<?php

	function evolut_qtt(){
		
		$this->load->library('cezpdf');
		$this->load->helper('pdf');		
		$this->load->library('jpgraph');
		
		$params_array=array();
		
		$config = $this->report_mdl->get_reports_conf($this->input->post('report_id'));
		
		$report_params = json_decode($config['report_params'],true);
		
		foreach($report_params as $param){
			
			$param = ($param=='entity_id_2')?'entity_id':$param;
			
			if (array_key_exists($param, $this->input->post())) {

				$params_array['param_id'][] = $this->input->post($param);
				$params_array['param_caption'][] = $this->pbf->get_param_caption($param,$this->input->post($param));
				
				}
			
			}
			
		$report_params = $params_array; // $params_array containing the real params to use through out...
		
		$table_width = ($config['report_page_layout']=='portrait')?550:820;
		
		$this->cezpdf->Cezpdf('a4',$config['report_page_layout']);
		
		// creates the HEADER and FOOTER for the document we are creating.
		prep_pdf(	$config['report_page_layout'],
					$config['report_logo_position'],
					$config['report_title'],
					$config['report_subtitle'],
					$params_array['param_caption']);
		
		$post_vars = $this->input->post();
		
		$raw_info = $this->report_mdl->get_indicator_evolution($post_vars);
		
		if(empty($raw_info)){
		$this->cezpdf->ezText($this->lang->line('no_data_display'),10,array('justification'=>'left'));	
			}
		else{
		
		$post_vars['datafile_year'] = ($post_vars['datafile_year']=='')?date('Y'):$post_vars['datafile_year'];
		
		$chart_contents = array();
		
		foreach($raw_info as $info){
			
			$projected_pop = $info['pop']*pow(1.031,$post_vars['datafile_year']-$info['pop_year']); // reading purposes
			$chart_contents['projected_pop'][] = $projected_pop;
			$chart_contents['periodicity'][] = $this->lang->line('app_month_'.$info['datafile_month']).' - '.$info['datafile_year'];
			
			$ProjectedTarget = $projected_pop.$info['indicator_target'];
			eval("\$ProjectedTarget = $ProjectedTarget;");
			
			$chart_contents['achievement'][] = ($info['indicator_value']/$ProjectedTarget)*10000;
			$chart_contents['targeted'][] = 100;
			
			}
			
			
		$graph = $this->jpgraph->indic_evolut($chart_contents);
		
		$graph_temp_directory = FCPATH.'cside/images/portal';  // in the webroot (add directory to .htaccess exclude)
        
		$graph_file_name = $this->session->userdata('session_id').'.jpg';    
        
        $graph_file_location = $graph_temp_directory . '/' . $graph_file_name;
                
        $graph->Stroke($graph_file_location);  // create the graph and write to file
		
		$this->load->model('indicators_mdl');
			
		$indicator = $this->indicators_mdl->get_indicator($post_vars['data_element']);
		
		$this->cezpdf->ezText(utf8_decode($indicator['indicator']['indicator_title']),12,array('justification'=>'center'));
		
		$this->cezpdf->ezSetDy(-20);
			
		$this->cezpdf->ezIfoto($graph_file_location,'','530','none','left'); 
		
		unlink($graph_file_location); // and delete the file from the server
		
		}
	
		$this->cezpdf->ezStream();
		
		}