<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Files extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'files_mdl' );
		$this->lang->load ( 'files', $this->config->item ( 'language' ) );
	}
	function index() {
		redirect ( 'files/filetypes' );
	}
	function filetypes() {
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$this->lang->load ( 'otheroptions', $this->config->item ( 'language' ) );
		
		$data = $this->files_mdl->get_file_types ( $preps ['offset'], $preps ['terms'] );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['filetype_name'] = anchor ( '/files/edit/' . $v ['filetype_id'], $this->lang->line ( 'filetype_ky_' . $v ['filetype_id'] ) );
			
			$data ['list'] [$k] ['filetype_contenttype'] = $this->lang->line ( 'option_lkp_ky_' . $v ['filetype_contenttype'] );
			$data ['list'] [$k] ['filetype_frequency'] = $this->lang->line ( 'file_frq_ky_' . $v ['filetype_frequency'] );
			
			$data ['list'] [$k] ['filetype_active'] = $this->pbf->rec_op_icon ( 'active_' . $v ['filetype_active'], '/files/setfilestate/' . $v ['filetype_id'] );
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/files/edit/' . $v ['filetype_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/files/delete/' . $v ['filetype_id'] );
			$data ['list'] [$k] ['filetype_id'] = $k + $preps ['offset'] + 1;
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'list_file_title' ),
				$this->lang->line ( 'list_file_contenttype' ),
				$this->lang->line ( 'list_file_frequency' ),
				'',
				'',
				'' 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'files_title' ) . ' [' . $data ['records_num'] . ']';
		$data ['mod_title'] ['/files/add'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['tab_menus'] = $this->pbf->get_mod_submenu ( 18 );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	function filefrequence() {
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->files_mdl->get_file_frequences ( $preps ['offset'], $preps ['terms'] );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['frequency_title'] = anchor ( '/files/editfrequence/' . $v ['frequency_id'], $this->lang->line ( 'file_frq_ky_' . $v ['frequency_id'] ) );
			
			$months = json_decode ( $data ['list'] [$k] ['frequency_months'] );
			
			$data ['list'] [$k] ['frequency_months'] = '';
			
			foreach ( $months as $month ) {
				
				$data ['list'] [$k] ['frequency_months'] .= $this->lang->line ( 'app_month_' . $month ) . ', ';
			}
			if (strlen ( trim ( $data ['list'] [$k] ['frequency_months'], ', ' ) ) > 97) {
				
				$data ['list'] [$k] ['frequency_months'] = substr ( trim ( $data ['list'] [$k] ['frequency_months'], ', ' ), 0, 90 ) . ' ...';
			} 

			else {
				
				$data ['list'] [$k] ['frequency_months'] = trim ( $data ['list'] [$k] ['frequency_months'], ', ' );
			}
			
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/files/editfrequence/' . $v ['frequency_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/files/deletefrequence/' . $v ['frequency_id'] );
			$data ['list'] [$k] ['frequency_id'] = $k + $preps ['offset'] + 1;
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'list_file_frequency' ),
				$this->lang->line ( 'list_file_frequency_months' ),
				'',
				'' 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'list_file_frequencies' ) . ' [' . $data ['records_num'] . ']';
		$data ['mod_title'] ['/files/addfrequence'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['tab_menus'] = $this->pbf->get_mod_submenu ( 18 );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	function add($data = '') {
		$data ['entity_class'] = $this->pbf->get_entity_classes ();
		$data ['entity_type'] = $this->pbf->get_entity_types ();
		$data ['usergroup_id'] = $this->pbf->get_usersgroups_multiselect ();
		$data ['content_type'] = $this->pbf->get_lookups ( 'content_type' );
		
		$data ['helpers'] = $this->pbf->get_helpers ( 'dataentry' );
		
		$frequencies = $this->files_mdl->get_file_frequences ( 0, '' );
		
		$data ['frequency'] [''] = $this->lang->line ( 'app_form_dropdown_select' );
		
		foreach ( $frequencies ['list'] as $frequency ) {
			
			$data ['frequency'] [$frequency ['frequency_id']] = $this->lang->line ( 'file_frq_ky_' . $frequency ['frequency_id'] );
		}
		$geozones = $this->pbf_mdl->get_geozones ();
		
		foreach ( $geozones as $geozone ) {
			$data ['geozones'] [$geozone ['geozone_id']] = $geozone ['geozone_name'];
		}
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'files_title' );
		
		$data ['page'] = 'file_frm';
		
		$this->load->view ( 'body', $data );
	}
	function addfrequence($data = '') {
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'list_file_frequencies' );
		
		for($i = 1; $i <= 12; $i ++) {
			$data ['months'] [$i] = $this->lang->line ( 'app_month_' . $i );
		}
		
		$data ['page'] = 'filefrequency_frm';
		$this->load->view ( 'body', $data );
	}
	function save() {
		$file = $this->input->post ();
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'filetype_name', 'file type', 'trim|required' );
		
		if ($this->form_validation->run () == FALSE) {
			$this->add ( $file );
		} else {
			
			$file ['filetype_active'] = ! isset ( $file ['filetype_active'] ) ? 0 : 1;
			$file ['dashboard_active'] = ! isset ( $file ['dashboard_active'] ) ? 0 : 1;
			
			$filetypesentities ['entity_type_id'] = $file ['entity_type_id'];
			$filetypesentities ['entity_class_id'] = $file ['entity_class_id'];
			
			unset ( $file ['entity_type_id'] );
			unset ( $file ['entity_class_id'] );
			unset ( $file ['submit'] );
			unset ( $file ['template_cols'] );
			
			// sdl--
			$filetypezone = $file ['filetype_geozone'];
			unset ( $file ['filetype_geozone'] );
			
			if ($this->files_mdl->save_file_type ( $file, $filetypesentities, $filetypezone ))
		//if($this->files_mdl->save_file_type($file,$filetypesentities))
			
			{

				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => $this->lang->line ( 'file_save_success' ) 
				) );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'file_save_error' ) 
				) );
			}
			$this->pbf->set_eventlog ( '', 0 );
			redirect ( 'files/filetypes/' );
		}
	}
	function savefrequency() {
		$frequency = $this->input->post ();
		
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'frequency_title', 'Frequency', 'trim|required' );
		
		if ($this->form_validation->run () == FALSE) {
			$this->addfrequence ( $frequency );
		} else {
			
			unset ( $frequency ['submit'] );
			
			$frequency ['frequency_months'] = json_encode ( $frequency ['frequency_months'] );
			
			if ($this->files_mdl->save_frequency ( $frequency )) 

			{
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => $this->lang->line ( 'frequency_save_success' ) 
				) );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'frequency_save_error' ) 
				) );
			}
			$this->pbf->set_eventlog ( 'process_frequency', 1 );
			redirect ( 'files/filefrequence/' );
		}
	}
	function edit($filetype_id) {
		$data ['file'] = $this->files_mdl->get_file_type ( $filetype_id );
		$data ['file'] ['usergroup_id'] = $this->pbf->get_asset_access ( $filetype_id, 'data_filetype', 'usersgroup_id' );
		
		$this->add ( $data );
	}
	function editfrequence($frequency_id) {
		$data ['frequency'] = $this->files_mdl->get_frequency ( $frequency_id );
		
		$this->addfrequence ( $data );
	}
	function setfilestate($filetype_id, $state) {
		if ($this->files_mdl->set_file_state ( $filetype_id, $state )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'file_state_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'file_state_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	function delete($filetype_id) {
		if ($this->files_mdl->del_file_type ( $filetype_id )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'file_delete_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'file_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	function deletefrequence($frequency_id) {
		$data ['frequency'] = $this->files_mdl->get_frequency ( $frequency_id );
		
		$saved_trans = $this->pbf->set_translation ( NULL, $data ['frequency'] ['frequency_title'], 'files' );
		
		if ($this->files_mdl->del_frequency ( $frequency_id ) && $saved_trans) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'frequency_delete_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'frequency_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( 'delete_frequency', 1 );
		redirect ( 'files/filefrequence/' );
	}
}