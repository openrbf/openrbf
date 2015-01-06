<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Datafiles extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'datafiles_mdl' );
		$this->lang->load ( 'datafiles', $this->config->item ( 'language' ) );
		$this->lang->load ( 'indicators', $this->config->item ( 'language' ) );
	}
	function index($entity_class) {
		if ($this->config->item ( 'debug' ))
			$this->output->enable_profiler ( TRUE );
			// $entity_class access is not controlled
		$this->session->set_userdata ( array (
				'data_entity_class' => $entity_class 
		) );
		
		redirect ( 'datafiles/datamngr/' );
	}
	function datamngr() {
		if ($this->config->item ( 'debug' ))
			$this->output->enable_profiler ( TRUE );
		
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$entity_class_id = $this->session->userdata ( 'data_entity_class' );
		$user_group_id = $this->session->userdata ( 'usergroup_id' );
		
		$group_access = $this->pbf_mdl->get_user_group_assets_access ( $user_group_id, $entity_class_id, 'entity_class' );
		
		$has_read_access = FALSE;
		
		if (! empty ( $group_access )) {
			$has_read_access = $group_access ['read_access'] == '1' ? TRUE : FALSE;
		}
		
		$has_write_access = FALSE;
		
		if (! empty ( $group_access )) {
			$has_write_access = $group_access ['write_access'] == '1' ? TRUE : FALSE;
		}
		
		if (! $has_read_access) {
			// if no read access, redirect the user to the home page
			$this->session->set_flashdata ( array (
					'mod_clss' => 'warning',
					'mod_msg' => $this->lang->line ( 'app_badiest_message' ) 
			) );
			
			redirect ( $this->session->userdata ( 'afterlogin' ) );
		}
		
		$data = $this->datafiles_mdl->get_datafiles ( $preps ['offset'], $preps ['terms'] );
		
		$permissions = $this->session->userdata ( 'usergroupsrules' );
		
		$canValidate = array_search ( 'datafiles/validate/', $permissions );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['data_file'] = anchor ( '/datafiles/dataentry/' . $data ['list'] [$k] ['datafile_id'] . '/' . $data ['list'] [$k] ['entity_id'] . '/' . $data ['list'] [$k] ['datafile_month'] . '/' . $data ['list'] [$k] ['datafile_year'] . '/' . $data ['list'] [$k] ['filetype_id'], $data ['list'] [$k] ['data_file'] );
			
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/datafiles/dataentry/' . $data ['list'] [$k] ['datafile_id'] . '/' . $data ['list'] [$k] ['entity_id'] . '/' . $data ['list'] [$k] ['datafile_month'] . '/' . $data ['list'] [$k] ['datafile_year'] . '/' . $data ['list'] [$k] ['filetype_id'] );
			
			$data ['list'] [$k] ['datafile_month'] = $this->lang->line ( 'app_month_' . $data ['list'] [$k] ['datafile_month'] );
			
			if ($has_write_access) {
				$data ['list'] [$k] ['delete'] = $data ['list'] [$k] ['datafile_state'] == 0 ? $this->pbf->rec_op_icon ( 'delete', '/datafiles/delete/' . $data ['list'] [$k] ['datafile_id'] ) : '';
			}
			
			if ($canValidate) {
				// validate icon just using tick green and tick_green
				$icon = $data ['list'] [$k] ['datafile_valid_reg'] != '1' ? 'tick' : 'tick_green';
				$url = $data ['list'] [$k] ['datafile_valid_reg'] != '1' ? '/datafiles/validate/' . $data ['list'] [$k] ['datafile_id'] : '/datafiles/validate/' . $data ['list'] [$k] ['datafile_id'] . '/0';
				$title = $data ['list'] [$k] ['datafile_valid_reg'] != '1' ? $this->lang->line ( 'validate_file' ) : $this->lang->line ( 'unvalidate_file' );
				
				$data ['list'] [$k] ['validate'] = $this->pbf->rec_op_icon ( $icon, $url, $title );
			}
			
			$data ['list'] [$k] ['status'] = $data ['list'] [$k] ['datafile_valid_reg'] != '1' ? $this->lang->line ( 'unvalidated' ) : $this->lang->line ( 'validated' );
			
			$data ['list'] [$k] ['datafile_id'] = $k + $preps ['offset'] + 1;
			unset ( $data ['list'] [$k] ['entity_id'] );
			unset ( $data ['list'] [$k] ['filetype_id'] );
			unset ( $data ['list'] [$k] ['datafile_valid_reg'] );
		}
		
		$titles = array (
				'#',
				$this->lang->line ( 'list_entity' ),
				$this->lang->line ( 'list_geozone_name' ),
				$this->lang->line ( 'list_report_type' ),
				$this->lang->line ( 'list_month' ),
				$this->lang->line ( 'list_year' ),
				$this->lang->line ( 'list_modified' ),
				$this->lang->line ( 'list_author' ),
				$this->lang->line ( 'file_status' ),
				'',
				'' 
		);
		
		if ($canValidate) {
			array_push ( $titles, '' );
		}
		
		array_unshift ( $data ['list'], $titles );
		
		$data ['can_validate'] = $canValidate;
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'datafile_mod_title' ) . ' - [' . $data ['records_num'] . ' ' . $this->lang->line ( 'datafile_mod_title_files' ) . ' ]';
		
		if ($has_write_access) {
			$data ['mod_title'] ['/datafiles/add/' . $this->session->userdata ( 'data_entity_class' )] = $this->pbf->rec_op_icon ( 'add_file' );
		}
		
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$this->session->set_userdata ( array (
				'data_entity_class' => $data ['entity_class_name'] ['entity_class_id'] 
		) );
		
		// sdl: get 3 types of filters
		$data ['rec_filters'] = $this->pbf->get_filters ( array (
				'entity_id',
				'datafile_month',
				'filetype_id' 
		) );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	function validate($datafile_id, $value = '') {
		if ($value == null && $value == '') {
			
			$lookups = $this->pbf->get_by_lookup_title ( 'validated_regional' );
			$value = $lookups ['lookup_id'];
		}
		
		if ($this->datafiles_mdl->validate ( $datafile_id, $value )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'info',
					'mod_msg' => $this->lang->line ( 'validation_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'validation_failed' ) 
			) );
		}
		
		redirect ( 'datafiles/datamngr/' );
	}
	function add($entity_class) {
		$entity_class_id = $entity_class;
		$user_group_id = $this->session->userdata ( 'usergroup_id' );
		
		$group_access = $this->pbf_mdl->get_user_group_assets_access ( $user_group_id, $entity_class_id, 'entity_class' );
		
		$has_read_access = FALSE;
		
		if (! empty ( $group_access )) {
			$has_read_access = $group_access ['read_access'] == '1' ? TRUE : FALSE;
		}
		
		$has_write_access = FALSE;
		
		if (! empty ( $group_access )) {
			$has_write_access = $group_access ['write_access'] == '1' ? TRUE : FALSE;
		}
		
		if (! $has_write_access) {
			// if no read access, redirect the user to the home page
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'warning',
					'mod_msg' => $this->lang->line ( 'app_badiest_message' ) 
			) );
			
			redirect ( $this->session->userdata ( 'afterlogin' ) );
		}
		
		$usergeozones = $this->session->userdata ( 'usergeozones' );
		$data ['entities'] = $this->pbf->get_entities_data_entry ( $entity_class );
		
		$data ['filetypes'] = $this->pbf->get_filetypes__entity_type_zone ( $entity_class, $usergeozones );
		
		$data ['years'] = $this->pbf->get_years_list ( 2 );
		
		$data ['periodics'] = $this->pbf->get_datafile_periodics ();
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'datafile_mod_title' );
		$data ['mod_title'] ['/'] = $this->lang->line ( 'app_mod_close' );
		$data ['page'] = 'datafile_stepone_frm';
		
		$this->session->set_userdata ( array (
				'data_entity_class' => $entity_class 
		) );
		
		$this->load->view ( 'body', $data );
	}
	function newfile($datafile_id = '', $entity_id = '', $datafile_month = '', $datafile_year = '', $filetype_id = '') {
		$this->load->model ( 'entities_mdl' );
		
		$entity_info = $this->entities_mdl->get_entity ( $entity_id );
		
		$this->session->set_userdata ( array (
				'data_entity_class' => $entity_info ['entity_class'] 
		) );
		
		if (strpos ( $datafile_month, '_' )) {
			$datafile_month = explode ( '_', $datafile_month );
			$datafile_month = $datafile_month [1];
		}
		
		$xisting_id = $this->datafiles_mdl->check_file_exist ( $entity_id, $datafile_month, $datafile_year, $filetype_id );
		
		if (! empty ( $xisting_id )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'info',
					'mod_msg' => $this->lang->line ( 'datafile_file_exists' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		
		redirect ( 'datafiles/dataentry/' . (empty ( $xisting_id ) ? 0 : $xisting_id ['datafile_id']) . '/' . $entity_id . '/' . $datafile_month . '/' . $datafile_year . '/' . $filetype_id );
	}
	function dataentry($datafile_id, $entity_id, $datafile_month, $datafile_year, $filetype_id) {
		$this->lang->load ( 'indicators', $this->config->item ( 'language' ) );
		
		$this->load->model ( array (
				'entities_mdl',
				'datafiles_mdl' 
		) );
		
		$entity_info = $this->entities_mdl->get_entity ( $entity_id );
		
		$this->session->set_userdata ( array (
				'data_entity_class' => $entity_info ['entity_class'] 
		) );
		
		$existing = $this->datafiles_mdl->check_file_exist ( $entity_id, $datafile_month, $datafile_year, $filetype_id );
		// we got the class_id
		
		if (($datafile_id == '0') && (! empty ( $existing ))) {
			$this->session->set_flashdata ( array (
					'note_class' => 'note-info',
					'note_msg' => $this->lang->line ( 'datafile_file_exists' ) 
			) );
			$this->pbf->set_eventlog ( $this->lang->line ( 'datafile_tempate_create' ) . $existing ['datafile_id'], 1 );
			
			redirect ( 'datafiles/datamngr/' );
		}
		// end of file existance cheching
		
		$data ['header'] = $this->datafiles_mdl->get_datafile_header ( (count ( $existing ) == 0) ? 0 : $existing ['datafile_id'], $entity_id, $datafile_month, $datafile_year, $filetype_id );
		
		if ($data ['header'] ['filetype_template'] == '' || is_null ( $data ['header'] ['filetype_template'] )) {
			
			$this->session->set_flashdata ( array (
					'note_class' => 'info',
					'note_msg' => 'Veuillez contacter l\'administration pour la configuration du fichier de saisie' 
			) );
			$this->pbf->set_eventlog ( 'datafile_without_template', 1 );
			redirect ( 'datafiles/datamngr/' );
		}
		
		$this->load->helper ( 'dataentry/' . $data ['header'] ['filetype_template'] );
		
		$data ['template'] = call_user_func ( $data ['header'] ['filetype_template'], $data ['header'] );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'datafile_data_entry' ) . ' - ' . $this->lang->line ( 'filetype_ky_' . $data ['header'] ['filetype_id'] );
		$data ['page'] = 'datafile_steptwo_frm';
		
		$this->load->view ( 'body', $data );
	}
	function setcookie($url) {
		setcookie ( "info", "yes", (time () + 2592000), "/" ); // 1 month
		redirect ( implode ( '/', func_get_args () ) );
	}
	function save($update = TRUE) {
		$post_arr = $this->input->post ();
		if (isset ( $_FILES ['fraude_upload'] )) {
			$config ['allowed_types'] = 'pdf';
			$config ['upload_path'] = FCPATH . 'cside/contents/docs/';
			$config ['file_field_name'] = 'fraude_upload';
			$this->load->library ( 'upload', $config );
			if (! $this->upload->do_upload ( $config ['file_field_name'] )) {
				$erro = $this->upload->display_errors ();
			}
			$data_uploaded_file = $this->upload->data ();
		}
		$dataheader ['datafile_id'] = $post_arr ['datafile_id'];
		$dataheader ['filetype_id'] = $post_arr ['filetype_id'];
		$dataheader ['entity_id'] = $post_arr ['entity_id'];
		$dataheader ['datafile_remark'] = $post_arr ['datafile_remark'];
		$dataheader ['datafile_month'] = $post_arr ['datafile_month'];
		$dataheader ['datafile_info'] = isset ( $post_arr ['datafile_info'] ) ? json_encode ( $post_arr ['datafile_info'] ) : NULL;
		$dataheader ['datafile_total'] = isset ( $post_arr ['datafile_total'] ) ? str_replace ( ',', '', $post_arr ['datafile_total'] ) : NULL;
		$dataheader ['datafile_original_id'] = isset ( $post_arr ['datafile_original_id'] ) ? $post_arr ['datafile_original_id'] : NULL;
		$dataheader ['datafile_quarter'] = $this->pbf->get_current_quarterBy_month ( $post_arr ['datafile_month'] );
		$dataheader ['datafile_year'] = $post_arr ['datafile_year'];
		if (empty ( $post_arr ['datafile_id'] )) {
			$dataheader ['datafile_author_id'] = $this->session->userdata ( 'user_id' );
			$dataheader ['datafile_created'] = date ( 'Y-m-d H:i:s' );
		}
		$dataheader ['datafile_modified_id'] = $this->session->userdata ( 'user_id' );
		
		$h = date ( 'H' ) + 2; // Recuperation de l'heure
		if ($h < 10) {
			$h = "0" . date ( 'H' ) + 2;
			$d = date ( 'Y-m-d' ) . " 0" . $h . ":" . date ( 'i' ) . ":" . date ( 's' );
		} else
			$d = date ( 'Y-m-d' ) . " " . $h . ":" . date ( 'i' ) . ":" . date ( 's' );
		
		$dataheader ['datafile_modified'] = $d;
		$dataheader ['datafile_file_upload'] = $data_uploaded_file ['file_name'];
		$display_value = $this->input->post ( 'datafile_info' );
		if (is_null ( $display_value [1] ) || $display_value [1] == "") {
			$dataheader ['datafile_state'] = 0;
		} else {
			$dataheader ['datafile_state'] = $display_value [1];
		}
		$this->pbf->set_eventlog ( $this->uri->assoc_to_uri ( $dataheader ), 0 );
		
		unset ( $post_arr ['submit'] );
		unset ( $post_arr ['filetype_id'] );
		unset ( $post_arr ['entity_id'] );
		unset ( $post_arr ['datafile_remark'] );
		unset ( $post_arr ['datafile_year'] );
		unset ( $post_arr ['datafile_month'] );
		unset ( $post_arr ['datafile_total'] );
		
		if ($this->datafiles_mdl->save_file ( $dataheader, $post_arr, $update )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'datafile_save_success' ) 
			) );
			$report ['entity_id'] = $dataheader ['entity_id'];
			$report ['month'] = $dataheader ['datafile_month'];
			$report ['quarter'] = $dataheader ['datafile_quarter'];
			$report ['year'] = $dataheader ['datafile_year'];
			
			//$this->pbf->invalidate_reports ( $report );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'datafile_save_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		
		redirect ( '/datafiles/datamngr/' );
	}
	function delete($datafile_id) {
		if ($this->datafiles_mdl->delete_datafile ( $datafile_id )) 

		{
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'datafile_delete_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'datafile_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	
	
	//========test if data for this datafile are all up to date=================
	function test_update_data_datafile($datafile_id){
		$test_update=TRUE;
		$test_update_data_entity=$this->datafiles_mdl->test_update_data_datafile($datafile_id);
		
		if ($test_update_data_entity['update_flag']==0){
			$test_update=FALSE;
		}
	    return $test_update;
	}
	
	function get_files_to_update(){
			return $this->datafiles_mdl->get_files_to_update();
	}
}