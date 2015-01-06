<?php
error_reporting ( 0 );

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Report extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( array (
				'report_mdl',
				'entities_mdl',
				'pbf_mdl',
				'geo_mdl',
				'acl_mdl' 
		) );
		$this->lang->load ( 'report', $this->config->item ( 'language' ) );
	}
	
	function index($report_id = '') {
		$raw_reports = $this->report_mdl->get_reports ();
		
		$user_id = $this->session->userdata ( 'user_id' );
		$user_group = $this->pbf->get_usergroup ( $user_id );
		$group_details = $this->acl_mdl->get_group ( $user_group );
		$sql = "SELECT * FROM pbf_donors WHERE groupassociated_id='" . $user_group . "'";
		$donor_data = $this->db->query ( $sql )->row_array ();
		
		$data ['donor_id'] = '';
		if (! empty ( $donor_data )) {
			$data ['donor_id'] = $donor_data ['donor_id'];
		}
		
		$report_access_list = json_decode ( $group_details ['report_group_access'] );
	
		$reports = array ();
		
		foreach ( $raw_reports as $raw_reports_val ) {
			if (in_array ( $raw_reports_val ['report_id'], $report_access_list )) {
				
				$reports [] = anchor ( 'report/report/' . $raw_reports_val ['report_id'], $this->lang->line ( 'reporting_key_' . $raw_reports_val ['report_id'] ) );

			}
		}
		
		if (! empty ( $report_id )) {
			
			$config = $this->report_mdl->get_reports_conf ( $report_id );
			
			$data ['report_params'] = json_decode ( $config ['report_params'], true );
			$data ['report_title'] = $this->lang->line ( 'reporting_key_' . $config ['report_id'] );
			$data ['report_descript'] = $config ['report_descript'];
			$data ['report_id'] = $report_id;
			$data ['report_sql_conf'] = (strstr ( $config ['report_content_sql'], 'SELECT' ) != '') ? '' : $config ['report_content_sql'];
		}
		
		$data ['reports'] = $reports;
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'report_title' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		$data ['page'] = 'reporting';
		$this->load->view ( 'body', $data );
	}
	
	function downloadFile($file) {
		$file_name = $file;
		$mime = 'application/force-download';
		header ( 'Pragma: public' );
		header ( 'Expires: 0' );
		header ( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header ( 'Cache-Control: private', false );
		header ( 'Content-Type: ' . $mime );
		header ( 'Content-Disposition: attachment; filename="' . basename ( $file_name ) . '"' );
		header ( 'Content-Transfer-Encoding: binary' );
		header ( 'Connection: close' );
		readfile ( $file_name );
		exit ();
	}
	
	function show() {
		$this->load->model ( 'datafiles_mdl' );
		$this->load->model ( 'invoices_mdl' );
		$config = $this->report_mdl->get_reports_conf ( $this->input->post ( 'report_id' ) );
		$posted_params = $this->input->post ();
				
		if (is_null ( $config ['report_helper'] ) || $config ['report_helper'] == '') {
			echo 'Missing helper function...';
			exit ();
		}
		
		unset ( $config ['report_id'] );
		$params = array_merge_recursive ( $this->input->post (), $config );
		$params['origin']='';
		
		if ($this->config->item ( 'auto_report_generation' )=='1'){
			$params['file_name']=$this->pbf->create_pdf_invoice_name($params,$params['entity_geozone_id']);
			$file_path='./cside/reports/'.$params['report_category'].'/'.$params['file_name'];
		
			if ($params['report_category']=='normal'){
				if ($params ['report_title'] == 'Rapport de Fraude') {
					$filetype_name = 'Fraude';
					$entityid = $params ['entity_id'];
					$datafilemonth = $params ['datafile_month'];
					$datafileyear = $params ['datafile_year'];
					$datafilequarter = $this->pbf->get_current_quarterBy_month ( $params ['datafile_month'] );
					$datafile_data = $this->datafiles_mdl->get_datafile_uploadFile ( $entityid, $datafilemonth, $datafileyear, $datafilequarter, $filetype_name );
					foreach ( $datafile_data as $datafile_key => $datafile_val ) {
						$uploaded_file = $datafile_val ['datafile_file_upload'];
					}
					if (is_null ( $uploaded_file ) || $uploaded_file == "") {
						echo "pas de fichier";
					} 
					else {
						$this->downloadFile ( FCPATH . 'cside/contents/docs/' . $uploaded_file );
						exit ();
					}
				}else{
					call_user_func ( $params ['report_helper'], $params);
				}

				if (is_null ( $uploaded_file ) || $uploaded_file == "") {

				} else {
					$this->downloadFile ( FCPATH . 'cside/contents/docs/' . $uploaded_file );
					exit ();
				}
			}else{
		
				if (file_exists($file_path) && $this->invoices_mdl->exists($params['file_name'])){
					header ( 'Content-type: application/pdf' );
					header ( 'Content-Disposition: inline; filename="the.pdf"' );
					header ( 'Content-Transfer-Encoding: binary' );
					header ( 'Content-Length: ' . filesize ($file_path));
					@readfile ($file_path);
				}else{
					echo "<strong>Avertissement</strong><br>";
					echo "PAS DE DONNEES";
				}
		
			}
		
		}else{
		
		    if($params['report_title']=='Rapport de Fraude'){
			
					
				$sql_fraud = "SELECT count(*) as NB_FRAUD  FROM pbf_datafile WHERE pbf_datafile.filetype_id = '12' AND pbf_datafile.entity_id = '".$params['entity_id']."' AND pbf_datafile.datafile_month = '".$params['datafile_month']."' AND pbf_datafile.datafile_year = '".$params['datafile_year']."'";
								
				$results_fraud = $this->db->query($sql_fraud)->row_array();
			
				
				if($results_fraud['NB_FRAUD']>0){
					$sql_data="SELECT datafile_file_upload as file  FROM pbf_datafile WHERE pbf_datafile.filetype_id = '8' AND pbf_datafile.entity_id = '".$params['entity_id']."' AND pbf_datafile.datafile_month = '".$params['datafile_month']."' AND pbf_datafile.datafile_year = '".$params['datafile_year']."'";
					$file_data=$this->db->query($sql_data)->row_array(); 
			
										
					if(empty($file_data['file'])){
			
						call_user_func($params['report_helper'], $params);
						
					}else{
			
						$filetype_name='Fraude';
						$quarter=explode("/",$params['data_quarters']);
						$entityid=$params['entity_id'];
						$datafilemonth=$params['datafile_month'];
						$datafileyear=$params['datafile_year'];
						$datafilequarter = $this->pbf->get_current_quarterBy_month($params['datafile_month']);
					
						$datafile_data=$this->datafiles_mdl->get_datafile_uploadFile($entityid,$datafilemonth,$datafileyear,$datafilequarter,$filetype_name);
			
						foreach ($datafile_data as $datafile_key => $datafile_val) {
							$uploaded_file=$datafile_val['datafile_file_upload'];
						}
						if(is_null($uploaded_file)||$uploaded_file==""){
							print_r("Pas de fraude");
							call_user_func($params['report_helper'], $params);
						}else{
							$this->downloadFile(FCPATH.'cside/contents/docs/'.$uploaded_file);
							exit;
						}
			
					}
				}else{
					print_r("Pas de fraude");
					exit();
				}
			}else{
				
				$this->load->helper('reporting/'.$params['report_helper']);
				call_user_func($params['report_helper'], $params);
			}
		
		}
	}
	
	
	function invoices() {
		if ($this->config->item ( 'debug' ))
			$this->output->enable_profiler ( TRUE );
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		$this->lang->load ( 'otheroptions', $this->config->item ( 'language' ) );
		$data_all = $this->report_mdl->get_invoices ( $preps ['offset'], $preps ['terms'] );
		
		$data = array ();
		
		$permissions = $this->session->userdata ( 'usergroupsrules' );
		$canSend = array_search ( 'report/change_invoice_date/', $permissions );
		
		foreach ( $data_all ['list'] as $k => $v ) {
			$data ['list'] [$k] ['invoice'] = $k + $preps ['offset'] + 1;
			$data ['list'] [$k] ['report_title'] = $v ['report_title'];
			$data ['list'] [$k] ['invoice_id'] = anchor ( '/report/invoice_det/' . $v ['invoice_id'], $this->config->item ( 'report_prefix' ) . '_' . $v ['invoice_id'] );
			$data ['list'] [$k] ['entity_name'] = $data_all ['list'] [$k] ['entity_name'] . ' (' . $data_all ['list'] [$k] ['entity_type_abbrev'] . ')';
			
			$data ['list'] [$k] ['geozone_name'] = $data_all ['list'] [$k] ['geozone_name'];
			$data ['list'] [$k] ['month'] = $data_all ['list'] [$k] ['month'];
			$data ['list'] [$k] ['quarter'] = $data_all ['list'] [$k] ['quarter'];
			$data ['list'] [$k] ['year'] = $data_all ['list'] [$k] ['year'];
			$data ['list'] [$k] ['user_fullname'] = $data_all ['list'] [$k] ['user_fullname'];
			
			$data ['list'] [$k] ['date'] = $data_all ['list'] [$k] ['date'];
			if ($data_all ['list'] [$k] ['uptodate']) {
				
				$icon = 'tick_green';
				$url = 'report/invoices';
				$title = 'up to date';
				$data ['list'] [$k] ['uptodate'] = $this->pbf->rec_op_icon ( $icon, $url, $title );
			} else {
				$icon = 'tick';
				
				$url = 'report/invoices';
				$title = 'out dated';
				$data ['list'] [$k] ['uptodate'] = $this->pbf->rec_op_icon ( $icon, $url, $title );
			}
			
			
			
			if ($canSend) {
				$icon = $v ['sent_date'] == NULL ? 'tick' : 'tick_green';
				$url = $v ['sent_date'] == NULL ? '/report/change_invoice_date/' . $v ['invoice_id'] : '/report/remove_date_invoice/' . $v ['invoice_id'];
				$title = $v ['sent_date'] == NULL ? $this->lang->line ( 'send_invoice' ) : $this->lang->line ( 'unvalidate_file' );
				
				$data ['list'] [$k] ['validate'] = $this->pbf->rec_op_icon ( $icon, $url, $title );
			}
		
		}
	
		array_unshift ( $data ['list'], array (
				'#',
				'Type de facture',
				'report_id',
				'FOSA',
				$this->lang->line ( 'report_param_district' ),
				$this->lang->line ( 'report_param_month' ),
				$this->lang->line ( 'report_param_trimestre' ),
				$this->lang->line ( 'report_param_year' ),
				'Auteur',
				'Date',
				'Up to date',
				'Sent' 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'app_submenu_invoices' );
		
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['rec_filters'] = $this->pbf->get_filters ( array (
				'entity_name',
				'invoice_id',
				'entity_id',
				'datafile_year',
				'uptodate' 
		) );
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		
		$this->load->view ( 'body', $data );
	}
	
	function invoice_det($invoice_id) {
		if ($this->config->item ( 'debug' ))
			$this->output->enable_profiler ( TRUE );
		
		$data ['invoice'] = $data = $this->report_mdl->get_invoice ( $invoice_id );
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'app_submenu_invoices' ) . ' - ' . $data ['invoice'] ['report_title'] . '. Num : ' . $data ['invoice'] ['invoice_id'];
		
		$data ['mod_title'] ['report/invoices'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['page'] = 'invoice';
		
		$this->load->view ( 'body', $data );
	}
	
	function send_invoices() {
		if ($this->config->item ( 'debug' ))
			$this->output->enable_profiler ( TRUE );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'app_submenu_send_invoices' );
		
		$data ['mod_title'] ['report/invoices'] = $this->pbf->rec_op_icon ( 'close' );
		$data ['page'] = 'send_invoice';
		
		$this->load->view ( 'body', $data );
	}
	
	function receive_invoices() {
		if ($this->config->item ( 'debug' ))
			$this->output->enable_profiler ( TRUE );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'app_submenu_receive_invoices' );
		
		$data ['mod_title'] ['report/invoices'] = $this->pbf->rec_op_icon ( 'close' );
		$data ['page'] = 'receive_invoice';
		
		$this->load->view ( 'body', $data );
	}
	
	function set_invoice_sent() {
		$post_arr = $this->input->post ();
		$this->pbf_mdl->set_invoice_sent_date ( $post_arr ['invoice'] );
	}
	
	function set_invoice_received() {
		$post_arr = $this->input->post ();
		$this->pbf_mdl->set_invoice_received_date ( $post_arr ['invoice'] );
	}
	
	
	function change_invoice_date($invoice_id) {
		$data_invoice = $this->report_mdl->update_date_invoice ( $invoice_id );
		
		if ($data_invoice == 1) {
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'info',
					'mod_msg' => $this->lang->line ( 'send_success' ) 
			) );
		} else {
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'send_failed' ) 
			) );
		}
		redirect ( 'report/invoices/' );
	}
	
	function remove_date_invoice($invoice_id) {
		$data_invoice = $this->report_mdl->remove_date_invoice ( $invoice_id );
		
		if ($data_invoice == 1) {
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'info',
					'mod_msg' => $this->lang->line ( 'remove_success' ) 
			) );
		} else {
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'remove_failed' ) 
			) );
		}
		redirect ( 'report/invoices/' );
	}
}
