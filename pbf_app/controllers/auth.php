<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Auth extends CI_Controller {
	function __construct() {
		parent::__construct ();
		
		$this->lang->load ( 'auth', $this->config->item ( 'language' ) );
	}
	function index($data = '') {
		$this->load->model ( 'cms_mdl' );
		
		if ($this->session->userdata ( 'user_id' ) == '') {
			$data ['page'] = 'login_frm';
			$data ['front_main_nav'] = $this->pbf->get_front_menu ();
			$data ['logo'] = $this->cms_mdl->get_logo ();
			$this->load->view ( 'front_body', $data );
		} else {
			redirect ( $this->session->userdata ( 'afterlogin' ) );
		}
	}
	function login() {
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'username', $this->lang->line ( 'frm_error_email' ), 'trim|required|valid_email' );
		$this->form_validation->set_rules ( 'password', $this->lang->line ( 'frm_error_password' ), 'trim|required' );
		
		if ($this->form_validation->run () == FALSE) {
			$data ['mod_clss'] = 'errormsg';
			$data ['mod_msg'] = $this->lang->line ( 'frm_hack_attempt_msg' );
			$this->pbf->set_eventlog ( 'frm_hack_attempt_msg', 1 );
			$this->index ( $data );
		} else {
			
			$this->load->model ( 'auth_mdl' );
			$user = $this->auth_mdl->check_user_credentials ( $this->input->post () );
			
			if (empty ( $user )) {
				
				$data ['mod_clss'] = 'errormsg';
				$data ['mod_msg'] = $this->lang->line ( 'frm_missing_credentials' );
				
				$this->pbf->set_eventlog ( 'frm_missing_credentials', 1 );
				
				$this->index ( $data );
			} else {
				
				$user ['afterlogin'] = is_null ( $user ['afterlogin'] ) ? 'dashboard/' : $user ['afterlogin'];
				$user ['usergeozones'] = $this->auth_mdl->get_usergeozones ( $user ['user_id'] );
				$user ['usergroupsrules'] = $this->auth_mdl->get_usergrouprules ( $user ['usergroup_id'] );
				$user ['show_menu'] = 'yes';
				
				array_unshift ( $user ['usergroupsrules'], $user ['afterlogin'], 'auth/', 'help/' );
				
				$this->session->set_userdata ( $user );
				
				$this->pbf->set_eventlog ( 'frm_successful_login', 1 );
				
				redirect ( $this->session->userdata ( 'afterlogin' ) );
			}
		}
	}
	function edit() {
	}
	function save() {
	}
	function logout() {
		$this->pbf->set_eventlog ( 'frm_successful_logout', 1 );
		$this->session->sess_destroy ();
		$this->session->sess_create ();
		$this->session->set_flashdata ( array (
				'mod_clss' => 'info',
				'mod_msg' => $this->lang->line ( 'frm_successful_logout' ) 
		) );
		
		redirect ( 'auth/' );
	}
}
