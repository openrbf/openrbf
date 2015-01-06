<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Cron extends CI_Controller {
	
	public $files_list = array();
		
	function __construct(){
		parent::__construct();
		$this->load->model('exports_mdl');
		$this->load->model('report_mdl');
		$this->load->model ('datafiles_mdl');
		$this->load->model ('entities_mdl');
		$this->load->model ('invoices_mdl');
		$this->lang->load ( 'exports', $this->config->item ( 'language' ) );
		$this->load->library ( 'pearloader' );
		$this->pearloader->load ( 'Writer' );
	}

	function get_alertes($secret_chain){
		$test_chain="gtrrKIUF45JUGGtddszzz";
		if ($test_chain==$secret_chain){
			$this->pbf->alertes();
		}
	}
	
	
	function process_files_est(){
		$test_chain="gtrrKIUF45JUGGtddszzz";
		if ($test_chain==$secret_chain){
			if (empty ( $zones ))
				$zones = array (
						24,
						25,
						26,
						27,
						28,
						29,
						30,
						31,
						32,
						33,
						34,
						39,
						40,
						41
				);
				$years = array (
						2012,
						2013,
						2014
				);
				
					
	
				foreach($years as $year) {
									
					$this->exports_mdl->set_empty_routine_data();
					foreach($zones as $zone) {
						$this->exports_mdl->insert_routine_data_table_zone( $zone, $year, $this->config->item ( 'language_abbr' ) );
					}
							
					$file_name_is = FCPATH . 'cside/exports/PBF_Manager_EST_' . $year . '.xls';
			
					$selection = array (
						13,
						14,
						5,
						6 ); 
				$this->pbf->excel_exports ( $file_name_is, $selection, $year );
				}	
		
		}
	}
	
	function generate_reports($secret_chain){
	$test_chain="gtrrKIUF45JUGGtddszzz";
	if ($test_chain==$secret_chain){	
		$datafile_parameters=$this->datafiles_mdl->get_files_to_update();
		$this->enties_updated_list=array();
		$automate_report_generation = $this->config->item ( 'auto_report_generation' );
		if ($automate_report_generation == '1'){
			$invoice_reports=$this->report_mdl->get_invoice_reports();
		}else{
			$invoice_reports=$this->report_mdl->get_report_for_frontend();
		}
	  	foreach ($invoice_reports as $invoice_report){
			$this->reporting($invoice_report['report_id'],$datafile_parameters,$from='datafile');
		}
		
		if (!empty($this->files_list)) {
			$this->datafiles_mdl->set_update_flag($this->files_list);
		}

			
		
		$entities_changed_list=$this->entities_mdl->get_entities_to_update();
		$entities_changed=array();
		
		foreach ($entities_changed_list as $entity_change){
			$entities_changed[]=$entity_change['entity_id'];
		}
		
		
		foreach ($entities_changed as $entity_changed){
			
			$datafile_parameters_entity=$this->datafiles_mdl->get_files_to_update_entity($entity_changed);
							
					foreach ($invoice_reports as $invoice_report){
						$this->reporting($invoice_report['report_id'],$datafile_parameters_entity,$from='entity');
					}
					$this->entities_mdl->set_update_flag($entity_changed);
			
		}
	}
	}
	
	
	function reporting($report_id,$datafile_parameters,$from){
		
		$config = $this->report_mdl->get_reports_conf ($report_id);
		
		
		$report_params = array();
		foreach ($datafile_parameters as $datafile_parameter) {
			$filestypes_list=json_decode($config['associated_filetypes']);
			
		
			if(in_array($datafile_parameter['filetype_id'],$filestypes_list)){
			
				if ($from=='datafile'){
					if (!in_array($datafile_parameter['entity_id'],$htis->enties_updated_list)){
						$this->enties_updated_list[]=$datafile_parameter['entity_id'];
					}
				}
				
				
				$all_helper_params=array();
				$geozone_id=$this->entities_mdl->get_entity_geozone_id($datafile_parameter['entity_id']);
				$entity_geo_id = $geozone_id['entity_geozone_id'];
				
				$helper_params=$this->generate_helper_params($datafile_parameter,$config,$datafile_parameter['entity_id']);
				$all_helper_params=array_merge_recursive ($helper_params,$config);
				
				
				$file_name_params=array_merge_recursive ($datafile_parameter,$config);
				$filename_components=$this->pbf->get_pdf_filename_parameters($file_name_params);
								
				$all_helper_params['file_name']=$this->pbf->create_pdf_invoice_name($filename_components,$entity_geo_id);
				
				
				$all_helper_params['origin']='cron_job';
				$all_helper_params['date']=date('Y-m-d h:i:s');
				$this->load->helper ( 'reporting/' . $all_helper_params ['report_helper'] );
				
				
				$invoice_params=array();
				$invoice_params['entity_id']=$datafile_parameter['entity_id'];
				$invoice_params['zone_id']=$entity_geo_id;
				$invoice_params['data_month']=$datafile_parameter['datafile_month'];
				$invoice_params['data_quarter']=$datafile_parameter['datafile_quarter'];
				$invoice_params['data_year']=$datafile_parameter['datafile_year'];
				$invoice_params['report_type_id']=$report_id;
				$invoice_params['pdf_file_name']=$all_helper_params['file_name'];
				$invoice_params['date']=$all_helper_params['date'];
							
				if(!in_array($datafile_parameter['datafile_id'],$this->files_list)){
						$this->files_list[]=$datafile_parameter['datafile_id'];
				}
			
				call_user_func ( $all_helper_params ['report_helper'], $all_helper_params, $invoice_params);
	
			}
			
		}
		return TRUE;
	


	}

    function get_periods($datafiles_data){
		$month_list=array();
		$quarter_list=array();
		$year_list=array();
		foreach ($datafiles_data as $datafile_data){
			
			if (!in_array($datafile_data['datafile_month'],$month_list)){
				array_push($month_list,$datafile_data['datafile_month']);
			}
			
			if (!in_array($datafile_data['datafile_quarter'],$quarter_list)){
				array_push($quarter_list,$datafile_data['datafile_quarter']);
			}
			if (!in_array($datafile_data['datafile_year'],$year_list)){
				array_push($year_list,$datafile_data['datafile_year']);
			}
		}
		
		$periods['month']=$month_list;
		$periods['quarter']=$quarter_list;
		$periods['year']=$year_list;
		
		return $periods;
	}
	
	function generate_helper_params($params,$report_params,$report_id){
		
		$report_params=json_decode($report_params['report_params']);
		$entity_geozone=$this->entities_mdl->get_entity_geozone_id($params['entity_id']);
				
		if(!in_array('entity_geozone_id',$report_params)){
			if (in_array('datafile_month',$report_params)){
				unset($params['datafile_quarter']);
			};
			if (in_array('datafile_quarter',$report_params)){
				unset($params['datafile_month']);
			};
		}else{
		
			if (in_array('datafile_month',$report_params)){
				unset($params['datafile_quarter']);
			};
			if (in_array('datafile_quarter',$report_params)){
				unset($params['datafile_month']);
			};
		
			unset($params['entity_id']);
			$params['entity_geozone_id']=$entity_geozone['entity_geozone_id'];
		}
				
		unset($params['datafile_id']);
		$params['level_0']=1;
		
		return $params;
	}

}