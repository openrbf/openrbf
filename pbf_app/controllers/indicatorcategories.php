<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Indicatorcategories extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'indicatorcategories_mdl' );
		$this->lang->load ( 'indicators', $this->config->item ( 'language' ) );
	}
	function index() {
		redirect ( 'indicators/dataelements' );
	}
	function dataelements() {
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		$data = $this->indicatorcategories_mdl->get_indicatorcategories ( $preps ['offset'], $preps ['terms'], $this->config->item ( 'language_abbr' ) );
		foreach ( $data ['list'] as $k => $v ) {
			$data ['list'] [$k] ['category_title'] = anchor ( '/indicatorcategories/edit/' . $data ['list'] [$k] ['category_id'], $data ['list'] [$k] ['category_title'] );
			
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/indicatorcategories/edit/' . $data ['list'] [$k] ['category_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/indicatorcategories/delete/' . $data ['list'] [$k] ['category_id'] );
			$data ['list'] [$k] ['category_id'] = $k + $preps ['offset'] + 1;
			unset ( $data ['list'] [$k] ['category_order'] );
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'list_indicator_title' ),
				'',
				'',
				'' 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'indicators_category_title' ) . ' [' . $data ['records_num'] . ' ]';
		$data ['mod_title'] ['/indicatorcategories/add'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		$data ['tab_menus'] = $this->pbf->get_mod_submenu ( 17 );
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	function add($data = '') {
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'indicators_category_title' );
		$data ['page'] = 'indicator_category_frm';
		$this->load->view ( 'body', $data );
	}
	function save() {
		$indicator_category = $this->input->post ();
		unset ( $indicator_category ['submit'] );
		
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'category_title', 'category title', 'trim|required' );
		$this->form_validation->set_rules ( 'category_order', 'category order', 'trim|required' );
		
		if ($this->form_validation->run () == FALSE) {
			$this->add ( $indicator_category );
		} else {
			
			if ($this->indicatorcategories_mdl->save_indicator_category ( $indicator_category )) {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => $this->lang->line ( 'indicator_category_save_success' ) 
				) );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'indicator_category_save_error' ) 
				) );
			}
			
			$this->pbf->set_eventlog ( '', 0 );
			
			redirect ( $this->session->userdata ( 'next_base_url' ) );
		}
	}
	function edit($category_id) {
		$data ['category'] = $this->indicatorcategories_mdl->get_indicator_category ( $category_id );
		$data ['tab_menus'] = $this->pbf->get_mod_submenu ( 17 );
		$this->add ( $data );
	}
	function delete($category_id) {
		if ($this->indicatorcategories_mdl->del_indicatorcategory ( $category_id )) {
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'indicator_category_delete_success' ) 
			) );
		} else {
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'indicator_category_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
}