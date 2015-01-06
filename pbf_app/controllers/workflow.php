<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class workflow extends CI_Controller {
	
	function __construct() {
		parent::__construct ();
		
		$this->lang->load ( 'workflow', $this->config->item ( 'language' ) );
	}
	
	function index() {
		if ($this->config->item ( 'debug' ))
			$this->output->enable_profiler ( TRUE );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'app_submenu_workflow' );
		
		$data ['mod_title'] ['workflow'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['states'] = $this->pbf_mdl->get_states ();
		
		$data ['page'] = 'workflow';
		
		$this->load->view ( 'body', $data );
	}
	
	function save_workflow() {
		$workflow = $this->input->post ();
		
		foreach ( $workflow as $state => $value ) {
			$this->db->where ( 'state_id', $state );
			$this->db->update ( 'pbf_workflow', array (
					'condition' => $value 
			) );
		}
		
		redirect ( '/dashboard/' );
	}
}