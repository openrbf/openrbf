<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Helpers extends CI_Controller {
	
	// ==========================================================================================================================
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'helpers_mdl' );
		$this->lang->load ( 'helpers', $this->config->item ( 'language' ) );
		$this->lang->load ( 'hfrentities', $this->config->item ( 'language' ) );
	}
	function index() {
		redirect ( 'helpers/helpers_list' );
	}
	function helpers_list() {
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		$data = $this->helpers_mdl->get_helpers ( $preps ['offset'], $preps ['terms'] );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/helpers/edit/' . $data ['list'] [$k] ['helper_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/helpers/delete/' . $data ['list'] [$k] ['helper_id'] );
			$data ['list'] [$k] ['helper_position'] = ($data ['list'] [$k] ['helper_position'] == 1) ? $this->lang->line ( 'list_helpers_G' ) : $this->lang->line ( 'list_helpers_D' );
			$data ['list'] [$k] ['helper_order'] = $data ['list'] [$k] ['helper_order'];
			$data ['list'] [$k] ['helper_id'] = $k + $preps ['offset'] + 1;
			unset ( $data ['list'] [$k] ['groups'] );
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'list_helper_title' ),
				$this->lang->line ( 'alert_position' ),
				$this->lang->line ( 'alert_order' ),
				$this->lang->line ( 'alert_actif' ),
				'' 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'helpers_title' ) . ' [' . $data ['records_num'] . ' ]';
		$data ['mod_title'] ['/helpers/add'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		// $data['rec_filters'] = $this->pbf->get_filters(array('indicator_title','indicator_filetype_id'));
		
		// $data['tab_menus'] = $this->pbf->get_mod_submenu(17);
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	function add($data = '') {
		$default_groups = array ();
		if (! empty ( $data ['groups'] ))
			$default_groups = explode ( ',', $data ['groups'] );
		$data ['groups'] = $this->pbf->get_groups_details ();
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'helpers_title' );
		
		foreach ( $data ['groups'] as $k => $v ) {
			
			$data ['groups'] [$k] ['usersgroup_id'] = form_checkbox ( array (
					'name' => 'groups[]',
					'id' => 'groups_id' . $k,
					'value' => $v ['usersgroup_id'],
					'checked' => in_array ( $v ['usersgroup_id'], $default_groups ) ? TRUE : FALSE,
					'style' => 'margin-left:10px;margin-right:10px' 
			) );
			
			unset ( $data ['groups'] [$k] ['usersgroup_description'] );
			unset ( $data ['groups'] [$k] ['inheritby'] );
			unset ( $data ['groups'] [$k] ['afterlogin'] );
			unset ( $data ['groups'] [$k] ['sortorder'] );
			unset ( $data ['groups'] [$k] ['isdefault'] );
			unset ( $data ['groups'] [$k] ['datatype_access'] );
			unset ( $data ['groups'] [$k] ['usersgroup_active'] );
			unset ( $data ['groups'] [$k] ['user_group_access'] );
			unset ( $data ['groups'] [$k] ['usersgroup_entity_associated'] );
		}
		
		array_unshift ( $data ['groups'], array (
				'',
				$this->lang->line ( 'acl_list_task' ),
				$this->lang->line ( 'acl_list_description' ) 
		) );
		
		$data ['page'] = 'helpers_frm';
		$this->load->view ( 'body', $data );
	}
	function save() {
		$helper = $this->input->post ();
		if (! isset ( $helper ['actif'] ))
			$helper ['actif'] = 0;
			
			// print_r($helper);
		$helper ['groups'] = implode ( ',', $helper ['groups'] );
		unset ( $helper ['submit'] );
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'helper_name', 'helper name', 'trim|required' );
		if ($this->form_validation->run () == FALSE) {
			$this->add ( $helper );
		} else {
			
			if ($this->helpers_mdl->save_helper ( $helper )) {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => $this->lang->line ( 'helper_save_success' ) 
				) );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'helper_save_error' ) 
				) );
			}
			
			$this->pbf->set_eventlog ( '', 0 );
			
			redirect ( 'helpers/helpers_list' );
		}
	}
	function edit($helper_id) {
		$data = $this->helpers_mdl->get_helper ( $helper_id );
		$this->add ( $data );
	}
	function delete($helper_id) {
		if ($this->helpers_mdl->del_helper ( $helper_id )) {
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'success_delete_msg' ) 
			) );
		} 

		else {
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'helper_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
}
		
//===========================================================================================================================
