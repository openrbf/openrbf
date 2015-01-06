<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Otheroptions extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->lang->load ( 'otheroptions', $this->config->item ( 'language' ) );
	}
	function index() {
		redirect ( 'otheroptions/config' );
	}
	function config() {
		$this->load->model ( 'report_mdl' );
		$this->lang->load ( 'report', $this->config->item ( 'language' ) );
		
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->report_mdl->list_reports_conf ( $preps ['offset'], $preps ['terms'] );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['report_title'] = anchor ( '/otheroptions/editconfig/' . $data ['list'] [$k] ['report_id'], $data ['list'] [$k] ['report_title'] );
			$data ['list'] [$k] ['open'] = $this->pbf->rec_op_icon ( 'open', '/report/report/' . $data ['list'] [$k] ['report_id'] );
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/otheroptions/editconfig/' . $data ['list'] [$k] ['report_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/otheroptions/delconf/' . $data ['list'] [$k] ['report_id'] );
			$data ['list'] [$k] ['report_id'] = $k + $preps ['offset'] + 1;
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'report_conf_title' ),
				$this->lang->line ('reporting_list_category_report'),
				$this->lang->line ( 'report_conf_layout' ),
				$this->lang->line ( 'report_conf_access' ),
				$this->lang->line ( 'report_conf_media' ),
				$this->lang->line ( 'report_conf_type' ),
				$this->lang->line ( 'report_conf_owner' ),
				'',
				'',
				'' 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'option_report_configuration' ) . ' - [' . $data ['records_num'] . ']';
		$data ['mod_title'] ['/otheroptions/addconfig'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['tab_menus'] = $this->pbf->get_mod_submenu ( 20 );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	function backup() {
		$this->load->helper ( 'file' );
		
		$raw_files = get_dir_file_info ( FCPATH . 'cside/backup/', FALSE );
		
		array_multisort ( $raw_files, SORT_DESC );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'option_autobackup' );
		$data ['mod_title'] ['/otheroptions/addbackup'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['tab_menus'] = $this->pbf->get_mod_submenu ( 20 );
		
		foreach ( $raw_files as $file_key => $file_val ) {
			
			$download_link = base_url () . 'cside/backup/' . utf8_encode ( $file_val ['name'] );
			
			$raw_files [$file_key] ['name'] = anchor_popup ( $download_link, utf8_encode ( $file_val ['name'] ), array (
					'width' => '300',
					'height' => '300' 
			) );
			$raw_files [$file_key] ['size'] = round ( $file_val ['size'] / 1000 ) . ' Kb';
			$raw_files [$file_key] ['date'] = substr ( standard_date ( 'DATE_RFC822', $file_val ['date'] ), 0, 14 );
			$raw_files [$file_key] ['download'] = $this->pbf->rec_op_icon ( 'download_record', $download_link );
			$raw_files [$file_key] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/otheroptions/delbackup/' . $file_val ['name'] );
			
			unset ( $raw_files [$file_key] ['server_path'] );
			unset ( $raw_files [$file_key] ['relative_path'] );
		}
		
		$data ['list'] = $raw_files;
		
		array_unshift ( $data ['list'], array (
				$this->lang->line ( 'option_autobackup_file_name' ),
				$this->lang->line ( 'option_autobackup_file_size' ),
				$this->lang->line ( 'option_autobackup_file_date' ),
				'',
				'' 
		) );
		
		$this->pbf->get_pagination ( 10, '', '' );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	function delbackup($file_name) {
		$this->load->helper ( 'file' );
		
		if (unlink ( FCPATH . 'cside/backup/' . $file_name )) {
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'backup_delete_success' ) 
			) );
			
			$this->pbf->set_eventlog ( $this->lang->line ( 'backup_delete_success' ) . $file_name, 1 );
		} else {
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'backup_delete_error' ) 
			) );
			
			$this->pbf->set_eventlog ( $this->lang->line ( 'backup_delete_error' ) . $file_name, 1 );
		}
		
		redirect ( 'otheroptions/backup/' );
	}
	function addbackup() {
		$this->load->dbutil ();
		$this->load->helper ( array (
				'file',
				'date' 
		) );
		$this->load->model ( 'otheroptions_mdl' );
		
		// get the DB zise
		
		$db_size = $this->otheroptions_mdl->get_db_size ();
		if ($db_size ['db_size'] < 40) {
			
			$backup = & $this->dbutil->backup ();
			
			$backup_name = str_replace ( ' ', '_', unix_to_human ( time () ) );
			$backup_name = str_replace ( '-', '_', $backup_name );
			$backup_name = str_replace ( ':', '', $backup_name );
			
			$backup_name = FCPATH . 'cside/backup/' . $backup_name . '.gz';
		} else {
			
			$backup_name = str_replace ( ' ', '_', unix_to_human ( time () ) );
			$backup_name = str_replace ( '-', '_', $backup_name );
			$backup_name = str_replace ( ':', '', $backup_name );
			
			$prefs = array (
					'tables' => array (
							'pbf_banks',
							'pbf_budget',
							'pbf_computation',
							'pbf_computationdetails',
							'pbf_content_news',
							'pbf_copayment',
							'pbf_datafile',
							'pbf_datafiledetails',
							'pbf_entities',
							'pbf_entityclasses',
							'pbf_entitygroups',
							'pbf_entitytypes',
							'pbf_exports',
							'pbf_faq',
							'pbf_filetypes',
							'pbf_filetypesentities',
							'pbf_geo',
							'pbf_geozones',
							'pbf_indicators',
							'pbf_indicatorsfileypes',
							'pbf_indicatorstarif',
							'pbf_indicatorstarifdetails',
							'pbf_lookups',
							'pbf_reporting',
							'pbf_sessions',
							'pbf_users',
							'pbf_usersgeozones',
							'pbf_usersgroups',
							'pbf_usersgroupsassets',
							'pbf_usersgroupsmap',
							'pbf_usersgroupsrules',
							'pbf_userstasks' 
					), // Array of tables to backup.
					'ignore' => array (
							'pbf_computed_routine_data',
							'pbf_syseventlog',
							'pbf_frontdata',
							'pbf_frontdatadetails' 
					), // List of tables to omit from the backup
					'format' => 'gzip', // gzip, zip, txt
					'filename' => $backup_name . '.sql', // File name - NEEDED ONLY WITH ZIP FILES
					'add_drop' => TRUE, // Whether to add DROP TABLE statements to backup file
					'add_insert' => TRUE, // Whether to add INSERT data to backup file
					'newline' => "\n"  // Newline character used in backup file
						);
			
			$backup = & $this->dbutil->backup ( $prefs );
			
			$backup_name = FCPATH . 'cside/backup/' . $backup_name . '_needs_refresh.gz';
		}
		
		write_file ( $backup_name, $backup );
		
		$this->pbf->set_eventlog ( $this->lang->line ( 'backup_creation' ) . $backup_name, 1 );
		redirect ( 'otheroptions/backup/' );
	}
	function methodes() {
		$this->load->model ( 'otheroptions_mdl' );
		
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->otheroptions_mdl->get_computation_methodes ( $preps ['offset'], $preps ['terms'] );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['computation_description'] = anchor ( '/otheroptions/editmethod/' . $data ['list'] [$k] ['computation_id'], $data ['list'] [$k] ['computation_description'] );
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/otheroptions/editmethod/' . $data ['list'] [$k] ['computation_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/otheroptions/delmethod/' . $data ['list'] [$k] ['computation_id'] );
			$data ['list'] [$k] ['computation_id'] = $k + $preps ['offset'] + 1;
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'option_computation_method' ),
				$this->lang->line ( 'option_start_date' ),
				$this->lang->line ( 'option_end_date' ),
				'',
				'' 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'option_computation_method_title' ) . ' - [' . $data ['records_num'] . ']';
		$data ['mod_title'] ['/otheroptions/addmethod'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['tab_menus'] = $this->pbf->get_mod_submenu ( 20 );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	function addmethod($data = '') {
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'option_computation_method' );
		
		$data ['entity_class'] = $this->pbf->get_entity_classes ();
		$data ['entity_type'] = $this->pbf->get_entity_types ();
		$data ['entity_group'] = array (
				'' => $this->lang->line ( 'app_form_dropdown_select' ) 
		) + $this->pbf->get_entity_groups ();
		$data ['geozones'] = $this->pbf->get_active_geozones ( false );
		
		$data ['page'] = 'method_frm';
		$this->load->view ( 'body', $data );
	}
	function editmethod($computation_id) {
		$this->load->model ( 'otheroptions_mdl' );
		
		$data = $this->otheroptions_mdl->get_method ( $computation_id );
		
		$this->addmethod ( $data );
	}
	function savemethod() {
		$this->load->model ( 'otheroptions_mdl' );
		
		$post_vars = $this->input->post ();
		
		$post_details_vars ['computation_entity_class_id'] = $post_vars ['computation_entity_class_id'];
		$post_details_vars ['computation_entity_type_id'] = $post_vars ['computation_entity_type_id'];
		$post_details_vars ['computation_entity_group_id'] = $post_vars ['computation_entity_group_id'];
		$post_details_vars ['computation_entity_ass_group_id'] = $post_vars ['computation_entity_ass_group_id'];
		$post_details_vars ['computation_geozone_id'] = $post_vars ['computation_geozone_id'];
		$post_details_vars ['computation_main_logic'] = $post_vars ['computation_main_logic'];
		$post_details_vars ['computation_calculation_basis'] = $post_vars ['computation_calculation_basis'];
		$post_details_vars ['computation_score_condition_one'] = $post_vars ['computation_score_condition_one'];
		$post_details_vars ['computation_score_fact_one'] = $post_vars ['computation_score_fact_one'];
		$post_details_vars ['computation_score_condition_two'] = $post_vars ['computation_score_condition_two'];
		$post_details_vars ['computation_score_fact_two'] = $post_vars ['computation_score_fact_two'];
		$post_details_vars ['fav_action'] = $post_vars ['fav_action'];
		$post_details_vars ['consider_score'] = $post_vars ['consider_score'];
		
		unset ( $post_vars ['computation_entity_class_id'] );
		unset ( $post_vars ['computation_entity_type_id'] );
		unset ( $post_vars ['computation_entity_group_id'] );
		unset ( $post_vars ['computation_entity_ass_group_id'] );
		unset ( $post_vars ['computation_geozone_id'] );
		unset ( $post_vars ['computation_main_logic'] );
		unset ( $post_vars ['computation_calculation_basis'] );
		unset ( $post_vars ['computation_score_condition_one'] );
		unset ( $post_vars ['computation_score_fact_one'] );
		unset ( $post_vars ['computation_score_condition_two'] );
		unset ( $post_vars ['computation_score_fact_two'] );
		unset ( $post_vars ['fav_action'] );
		unset ( $post_vars ['consider_score'] );
		unset ( $post_vars ['entity_types'] );
		unset ( $post_vars ['submit'] );
		
		$post_details_vars ['computation_entity_class_id'] = empty ( $post_details_vars ['computation_entity_class_id'] ) ? NULL : $post_details_vars ['computation_entity_class_id'];
		$post_details_vars ['computation_entity_type_id'] = empty ( $post_details_vars ['computation_entity_type_id'] ) ? NULL : $post_details_vars ['computation_entity_type_id'];
		$post_details_vars ['computation_entity_group_id'] = empty ( $post_details_vars ['computation_entity_group_id'] ) ? NULL : $post_details_vars ['computation_entity_group_id'];
		$post_details_vars ['computation_entity_ass_group_id'] = empty ( $post_details_vars ['computation_entity_ass_group_id'] ) ? NULL : $post_details_vars ['computation_entity_ass_group_id'];
		$post_details_vars ['computation_geozone_id'] = empty ( $post_details_vars ['computation_geozone_id'] ) ? NULL : $post_details_vars ['computation_geozone_id'];
		
		if ($this->otheroptions_mdl->save_method ( $post_vars, $post_details_vars )) 

		{
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'option_method_save_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'option_method_save_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( 'otheroptions/methodes/' );
	}
	function addconfig($data = '') {
		$this->load->model ( 'report_mdl' );
		$this->lang->load ( 'report', $this->config->item ( 'language' ) );
		
		$data ['helpers'] = $this->pbf->get_helpers ( 'reporting' );
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'report_configuration' );
		$data ['filetypes'] = $this->pbf->get_file_types ();
		
		$data ['page'] = 'report_frm';
		$this->load->view ( 'body', $data );
	}
	function editconfig($report_id) {
		
		$this->load->model ( 'files_mdl' );
		$this->load->model ( 'geo_mdl' );
		$this->load->model ( 'report_mdl' );
		$zones_array = $this->geo_mdl->get_regions ();
		$zones = array ();
		foreach ( $zones_array as $r ) {
			$children = $this->geo_mdl->get_zones_by_parent ( $r ['geozone_id'] );
			
			foreach ( $children as $child ) {
				if ($child ['geozone_active'] == 1) {
					$zones [$r ['geozone_name']] [$child ['geozone_id']] = $child ['geozone_name'];
				}
			}
		}
		
		$temp = $this->report_mdl->get_report_districts ( $report_id );
		$selected_districts = array ();
		foreach ( $temp as $t ) {
			array_push ( $selected_districts, $t ['pbf_geozone_id'] );
		}
		
		$selected_filetypes = array();
		$temp_filetypes = $this->report_mdl->get_report_filetypes ( $report_id );
		$selected_filetypes= json_decode($temp_filetypes['associated_filetypes']);		
		
		$filetypes=$this->files_mdl->get_all_file_types();
		$rep_filetypes=array();
		foreach ($filetypes as $filetype){
			$rep_filetypes[$filetype['filetype_id']]=$filetype['filetype_name'];
		}
		$data ['rep_filetypes'] = $rep_filetypes;
	

						
		$this->load->model ( 'report_mdl' );
		$this->lang->load ( 'report', $this->config->item ( 'language' ) );
		
		$data ['report'] = $this->report_mdl->get_reports_conf ( $report_id );
		$data ['report_districts'] = $selected_districts;
		$data ['report_filetypes'] = $selected_filetypes;
		$data ['districts'] = $zones;
				
		$this->addconfig ( $data );
	}
	function load_config($report_id) {
		$this->load->model ( 'report_mdl' );
		$this->lang->load ( 'report', $this->config->item ( 'language' ) );
		
		if (! empty ( $report_id )) {
			
			$config = $this->report_mdl->get_reports_conf ( $report_id );
			
			echo $config ['report_content_json'];
		}
	}
	function save_report_district($report_id, $districts) {
		$this->load->model ( 'report_mdl' );
		if ($this->report_mdl->geo_report_config_exists ( $report_id )) {
			$this->report_mdl->delete_geo_report_config ( $report_id );
		}
		
		$status = true;
		foreach ( $districts as $district ) {
			if (! $this->report_mdl->save_geo_report_config ( $report_id, $district ))
				$status = false;
		}
		
		return $status;
	}
	function saveconfig() {
		$this->load->model ( 'report_mdl' );
		$this->lang->load ( 'report', $this->config->item ( 'language' ) );
		
		$post_vars = $this->input->post ();
		
		unset ( $post_vars ['submit'] );
		
		$post_vars ['report_author'] = $this->session->userdata ( 'user_id' );
		if (isset ( $post_vars ['report_params'] )) {
			$post_vars ['report_params'] = json_encode ( $post_vars ['report_params'] );
		}
		
		if (isset ( $post_vars ['report_footer'] )) {
			if (is_array ( $post_vars ['report_footer'] )) {
				$post_vars ['report_footer'] = json_encode ( $post_vars ['report_footer'] );
			}
		}
		
		$post_vars ['report_signatories'] = json_encode ( $post_vars ['report_signatories'] );
		
		$success = 0;
		if (isset ( $post_vars ['report_districts'] )) {
			if (is_array ( $post_vars ['report_districts'] )) {
				$districts = $post_vars ['report_districts'];
				$success = $this->save_report_district ( $post_vars ['report_id'], $districts );
			}
			
			unset ( $post_vars ['report_districts'] );
		}
		
		if (! $success) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'report_conf_save_error' ) 
			) );
		}
		
		$post_vars ['associated_filetypes'] = json_encode ( $post_vars ['associated_filetypes'] );
		
		
		if ($this->report_mdl->save_config ( $post_vars ) && $success) {
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'report_conf_save_success' ) 
			) );
		} else {
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'report_conf_save_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		
		redirect ( 'otheroptions/config/' );
	}
	function delconf($report_id) {
		$this->load->model ( 'report_mdl' );
		$this->lang->load ( 'report', $this->config->item ( 'language' ) );
		
		if ($this->report_mdl->del_conf ( $report_id )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'report_conf_delete_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'report_conf_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( 'otheroptions/config/' );
	}
	function lookups() {
		$this->load->model ( 'otheroptions_mdl' );
		
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->otheroptions_mdl->get_lookups ( $preps ['offset'], $preps ['terms'] );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['lookup_title'] = anchor ( '/otheroptions/editlookup/' . $v ['lookup_id'], $this->lang->line ( 'option_lkp_ky_' . $v ['lookup_id'] ) );
			
			$data ['list'] [$k] ['lookup_title_abbrev'] = $this->lang->line ( 'option_lkp_abbr_ky_' . $v ['lookup_id'] );
			
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/otheroptions/editlookup/' . $v ['lookup_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/otheroptions/dellookup/' . $v ['lookup_id'] );
			
			$data ['list'] [$k] ['lookup_id'] = $k + $preps ['offset'] + 1;
			unset ( $data ['list'] [$k] ['lookup_order'] );
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'option_lookup_title' ),
				$this->lang->line ( 'option_lookup_abbrev' ),
				$this->lang->line ( 'option_lookup_linkfile' ),
				'',
				'' 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'option_lookups_title' ) . ' - [' . $data ['records_num'] . ']';
		$data ['mod_title'] ['/otheroptions/addlookup'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['tab_menus'] = $this->pbf->get_mod_submenu ( 20 );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	function addlookup($data = '') {
		$this->load->model ( 'otheroptions_mdl' );
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'option_lookups_title' );
		$lkps_links [''] = $this->lang->line ( 'app_form_dropdown_select' );
		$links = $this->otheroptions_mdl->get_lkps_links ();
		foreach ( $links as $link ) {
			$lkps_links [$link ['lookup_linkfile']] = $link ['lookup_linkfile'];
		}
		$data ['links'] = $lkps_links;
		$data ['page'] = 'lookup_frm';
		$this->load->view ( 'body', $data );
	}
	function editlookup($lookup_id) {
		$this->load->model ( 'otheroptions_mdl' );
		$data ['lookup'] = $this->otheroptions_mdl->get_lookup ( $lookup_id );
		$this->addlookup ( $data );
	}
	function savelookup() {
		$this->load->model ( 'otheroptions_mdl' );
		
		$lookup = $this->input->post ();
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'lookup_title', 'Lookup title', 'trim|required' );
		$this->form_validation->set_rules ( 'lookup_linkfile', 'Link', 'trim|required' );
		
		if ($this->form_validation->run () == FALSE) {
			$this->addlookup ( $lookup );
		} else {
			unset ( $lookup ['submit'] );
			
			if ($this->otheroptions_mdl->save_lookup ( $lookup )) 

			{
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => $this->lang->line ( 'lookup_save_success' ) 
				) );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'lookup_save_error' ) 
				) );
			}
			$this->pbf->set_eventlog ( 'lookup_processing', 1 );
			redirect ( 'otheroptions/lookups/' );
		}
	}
	function dellookup($lookup_id) {
		$this->load->model ( 'otheroptions_mdl' );
		
		if ($this->otheroptions_mdl->del_lookup ( $lookup_id )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'lookup_delete_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'error',
					'mod_msg' => $this->lang->line ( 'lookup_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( 'lookup_delete', 1 );
		redirect ( 'otheroptions/lookups/' );
	}
	function bank() {
		$this->load->model ( 'otheroptions_mdl' );
		
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->otheroptions_mdl->get_banks ( $preps ['offset'], $preps ['terms'] );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['bank_name'] = anchor ( '/otheroptions/editbank/' . $data ['list'] [$k] ['bank_id'], $data ['list'] [$k] ['bank_name'] );
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/otheroptions/editbank/' . $data ['list'] [$k] ['bank_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/otheroptions/delbank/' . $data ['list'] [$k] ['bank_id'] );
			$data ['list'] [$k] ['bank_id'] = $k + $preps ['offset'] + 1;
			unset ( $data ['list'] [$k] ['bank_parent_id'] );
			unset ( $data ['list'] [$k] ['sortorder'] );
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'bank_name' ),
				'',
				'' 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'option_bank' ) . ' - [' . $data ['records_num'] . ']';
		$data ['mod_title'] ['/otheroptions/addbank'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['tab_menus'] = $this->pbf->get_mod_submenu ( 20 );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	function addbank($data = '') {
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'option_bank' );
		
		$data ['bank_parent'] = $this->pbf->get_banks ( 'true' );
		$data ['page'] = 'bank_frm';
		$this->load->view ( 'body', $data );
	}
	function editbank($bank_id) {
		$this->load->model ( 'otheroptions_mdl' );
		$data ['bank'] = $this->otheroptions_mdl->get_bank ( $bank_id );
		$this->addbank ( $data );
	}
	function savebank() {
		$this->load->model ( 'otheroptions_mdl' );
		
		$bank = $this->input->post ();
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'bank_name', 'Banque', 'trim|required' );
		
		if ($this->form_validation->run () == FALSE) {
			$this->addbank ( $bank );
		} else {
			
			unset ( $bank ['submit'] );
			
			$bank ['bank_parent_id'] = empty ( $bank ['bank_parent_id'] ) ? NULL : $bank ['bank_parent_id'];
			
			if ($this->otheroptions_mdl->save_bank ( $bank )) 

			{
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => $this->lang->line ( 'bank_save_success' ) 
				) );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'bank_save_error' ) 
				) );
			}
			$this->pbf->set_eventlog ( '', 0 );
			redirect ( $this->session->userdata ( 'next_base_url' ) );
		}
	}
	function delbank($bank_id) {
		$this->load->model ( 'otheroptions_mdl' );
		if ($this->otheroptions_mdl->del_bank ( $bank_id )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'bank_delete_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'error',
					'mod_msg' => $this->lang->line ( 'bank_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
}
