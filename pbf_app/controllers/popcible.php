<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Popcible extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'popcible_mdl' );
		$this->lang->load ( 'popcible', $this->config->item ( 'language' ) );
	}
	function index($type) {
		$this->session->set_userdata ( array (
				'entity_type' => $type 
		) );
		
		redirect ( 'popcible/lines/' );
	}
	function lines() {
		$this->load->model ( 'popcible_mdl' );
		
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->popcible_mdl->get_popcible ( $preps ['offset'], $preps ['terms'] );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['popcible_name'] = anchor ( '/popcible/edit/' . $data ['list'] [$k] ['popcible_id'], $data ['list'] [$k] ['popcible_name'] );
			$data ['list'] [$k] ['popcible_percentage'] = number_format ( $data ['list'] [$k] ['popcible_percentage'] );
			$data ['list'] [$k] ['popcible_published'] = $this->pbf->rec_op_icon ( 'publish_' . $data ['list'] [$k] ['popcible_published'], '/popcible/setpublish/' . $data ['list'] [$k] ['popcible_id'] );
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/popcible/edit/' . $data ['list'] [$k] ['popcible_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/popcible/del/' . $data ['list'] [$k] ['popcible_id'] );
			$data ['list'] [$k] ['popcible_id'] = $k + $preps ['offset'] + 1;
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'popcible_name' ),
				$this->lang->line ( 'popcible_percentage' ) 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'popcible_name' );
		$data ['mod_title'] ['/popcible/add'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		// $data['rec_filters'] = $this->pbf->get_filters(array('geozone_id','datafile_year'));
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		
		$this->load->view ( 'body', $data );
	}
	function add($data = '') {
		if ($data == '') {
			$data ['zones'] = $this->popcible_mdl->get_zones ();
		}
		$data ['page'] = 'popcible_frm';
		$this->load->view ( 'body', $data );
	}
	function edit($popcible_id) {
		$data ['popcible'] = $this->popcible_mdl->get_popcible_line ( $popcible_id );
		$popzones = $this->popcible_mdl->get_popciblezone ( $popcible_id );
		
		foreach ( $popzones as $popzone ) {
			$data ['popcible_zone'] [$popzone ['zone_id']] ['zone'] = $popzone ['popcible_percentage'];
			$data ['popcible_zone'] [$popzone ['zone_id']] ['popzone_id'] = $popzone ['popciblezone_id'];
		}
		$data ['zones'] = $this->popcible_mdl->get_zones ();
		
		$this->add ( $data );
	}
	function del($popcible_id) {
		if ($this->popcible_mdl->del_popcible ( $popcible_id )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => 'Population cible deleted successfully' 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => 'Error deleting population cible' 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	function save_popcible() {
		$post = $this->input->post ();
		
		$popcible ['popcible'] ['popcible_id'] = $post ['popcible_id'];
		$popcible ['popcible'] ['popcible_name'] = $post ['popcible_name'];
		$popcible ['popcible'] ['popcible_percentage'] = $post ['popcible_percentage'];
		$popcible ['popcible'] ['popcible_published'] = $post ['popcible_published'];
		
		unset ( $post ['popcible_id'] );
		unset ( $post ['popcible_name'] );
		unset ( $post ['popcible_percentage'] );
		unset ( $post ['popcible_published'] );
		
		$popcible ['popciblezone'] = $post;
		
		$zones = $this->popcible_mdl->get_zones ();
		
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'popcible_name', 'popcible_name', 'required' );
		// $this->form_validation->set_rules('popcible_name', 'popcible_percentage', 'numeric');
		$this->form_validation->set_rules ( 'popcible_name', 'popcible_percentage', 'required' );
		if ($this->form_validation->run () == FALSE) {
			
			$data ['popcible'] = $popcible;
			$this->add ( $data );
		} else {
			
			if ($this->popcible_mdl->save_popcible ( $popcible, $zones )) 

			{
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => 'Population cible saved successfully' 
				) );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => 'Error saving population cible' 
				) );
			}
			$this->pbf->set_eventlog ( '', 0 );
			redirect ( $this->session->userdata ( 'next_base_url' ) );
		}
	}
	function setpublish($popcible_id, $state) {
		$this->popcible_mdl->setpublish ( $popcible_id, $state );
		
		redirect ( 'popcible' );
	}
}
