<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Budgets extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'budgets_mdl' );
		$this->lang->load ( 'budgets', $this->config->item ( 'language' ) );
		$this->lang->load ( 'datafiles', $this->config->item ( 'language' ) );
		$this->lang->load ( 'pbfapp', $this->config->item ( 'language' ) );
	}
	function index($type = 1) {
		$this->session->set_userdata ( array (
				'entity_type' => $type 
		) );
		
		redirect ( 'budgets/lines/' );
	}
	function lines() {
		$this->load->model ( 'entities_mdl' );
		
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->budgets_mdl->get_budgets ( $preps ['offset'], $preps ['terms'] );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['budget_month'] = ((! $data ['list'] [$k] ['budget_month'] == '') ? $this->lang->line ( 'app_month_' . $data ['list'] [$k] ['budget_month'] . '_short' ) : '');
			$data ['list'] [$k] ['budget_value'] = number_format ( $data ['list'] [$k] ['budget_value'] );
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/budgets/edit/' . $data ['list'] [$k] ['budget_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/budgets/del/' . $data ['list'] [$k] ['budget_id'] );
			$data ['list'] [$k] ['geozone_name'] = anchor ( '/budgets/edit/' . $data ['list'] [$k] ['budget_id'] . '/' . (($data ['list'] [$k] ['budget_month'] == '') ? 'annuel' : 'mensuel'), $data ['list'] [$k] ['geozone_name'] );
			$data ['list'] [$k] ['budget_id'] = $k + $preps ['offset'] + 1;
			unset ( $data ['list'] [$k] ['Region'] );
			unset ( $data ['list'] [$k] ['entity_class_name'] );
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'budget_district' ),
				$this->lang->line ( 'budget_entity_type' ),
				$this->lang->line ( 'budget_frm_budget_fosa' ),
				$this->lang->line ( 'budget_budget_month' ),
				$this->lang->line ( 'budget_entity_year' ),
				$this->lang->line ( 'budget_value' ) . '(' . $this->config->item ( 'app_country_currency' ) . ')',
				'',
				'' 
		) );
		
		$entity_type = $this->entities_mdl->get_entitytype ( $this->session->userdata ( 'entity_type' ) );
		
		$data ['mod_title'] ['mod_title'] = 'Budgets';
		$data ['mod_title'] ['/budgets/add_annuel'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['mngt_submenu'] = $this->pbf->get_entities_types ();
		
		$data ['rec_filters'] = $this->pbf->get_filters ( array (
				'entity_id',
				'budget_year' 
		) );
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	function add($data = '') {
		$this->session->set_userdata ( array (
				'data_entity_class' => 2 
		) );
		$data ['usergeozones'] = $this->session->userdata ( 'usergeozones' );
		$data ['entity_class'] = $this->pbf->get_entity_classes ();
		$data ['entity_type'] = $this->pbf->get_entity_types ();
		$data ['entities'] = $this->pbf->get_entities ( true, false );
		$data ['years'] = $this->pbf->get_years_list ( 4 );
		$data ['months'] = $this->pbf->get_months_list ();
		$data ['quarters'] = $this->pbf->get_months_list ( true );
		if (! $data ['mod_title'] ['mod_title']) {
			$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'budget_title' ) . ' - ADD';
		}
		// $data['tab_menus'] = $this->pbf->get_mod_submenu(19);
		$data ['page'] = 'budget_frm';
		$data ['periodic'] = 'mensuel';
		$this->load->view ( 'body', $data );
	}
	function add_annuel($data = '') {
		$this->session->set_userdata ( array (
				'data_entity_class' => 2 
		) );
		$data ['usergeozones'] = $this->session->userdata ( 'usergeozones' );
		$data ['entity_class'] = $this->pbf->get_entity_classes ();
		$data ['entity_type'] = $this->pbf->get_entity_types ();
		$data ['entities'] = $this->pbf->get_entities ( true, false, 2 );
		$data ['years'] = $this->pbf->get_years_list ( 4 );
		$data ['months'] = $this->pbf->get_months_list ();
		$data ['quarters'] = $this->pbf->get_months_list ( true );
		if (! $data ['mod_title'] ['mod_title']) {
			$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'budget_title' ) . ' - ADD';
		}
		
		// $data['tab_menus'] = $this->pbf->get_mod_submenu(19);
		$data ['page'] = 'budget_frm';
		$data ['periodic'] = 'annuel';
		$this->load->view ( 'body', $data );
	}
	function edit($budget_id, $periodic) {
		$this->load->model ( 'geo_mdl' );
		
		$data ['budget'] = $this->budgets_mdl->get_budget_line ( $budget_id );
		
		$geo_info = $this->geo_mdl->get_zone ( $data ['budget'] ['geozone_id'] );
		
		$this->session->set_userdata ( 'sel_parent_geozone_id', $geo_info ['geozone_parentid'] );
		$this->session->set_userdata ( 'sel_geozone_id', $geo_info ['geozone_id'] );
		$data ['periodic'] = $periodic;
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'budget_title' ) . ' - EDIT';
		$data ['page'] = 'budget_edit_frm';
		$this->load->view ( 'body', $data );
	}
	function del($budget_id) {
		if ($this->budgets_mdl->del_budgets ( $budget_id )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => 'Budget deleted successfully' 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => 'Error deleting budget' 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	function save_budget() {
		$budget_post = $this->input->post ();
		
		if (isset ( $budget_post ['budget_id'] ) && (! $budget_post ['budget_id'] == '')) {
			$budget = $this->budgets_mdl->get_budget_line ( $budget_post ['budget_id'] );
			$budget ['budget_value'] = $budget_post ['budget_value'];
			$this->save_budget_exist ( $budget_post ['periodic'], $budget );
		}
		
		$budget = $budget_post;
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'budget_value', 'budget value', 'trim|required|numeric' );
		$periodic = $budget_post ['periodic'];
		if ($periodic == 'mensuel') {
			$this->form_validation->set_rules ( 'budget_month', 'Month', 'trim|required' );
		}
		$this->form_validation->set_rules ( 'budget_year', 'Year', 'trim|required' );
		
		// =====================================controle existance des budgets===================================================
		if ($periodic == 'annuel') {
			if ($this->exist_annual_budget ( $budget ['budget_year'], $budget ['entity_id'] ) > 0) {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'annual_budget_exist' ) 
				) );
				redirect ( 'budgets/add_annuel/' );
			}
		}
		
		if ($periodic == 'mensuel') {
			if ($this->exist_mensual_budget ( $budget ['budget_month'], $budget ['budget_year'], $budget ['entity_id'] ) > 0) {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'mensual_budget_exist' ) 
				) );
				redirect ( 'budgets/add/' );
			}
		}
		
		// ====================================================================================================================
		
		// ========================verification saisie du budget mensuel===========================================
		if ($periodic == 'mensuel') {
			
			$budget_annuel = $this->get_annual_budget ( $budget ['budget_year'], $budget ['entity_id'] );
			$budget_menuesl_cumule = $this->get_cumul_month_budget ( $budget ['budget_year'], $budget ['entity_id'], $budget ['budget_value'] );
			
			if ($this->exist_annual_budget ( $budget ['budget_year'], $budget ['entity_id'] ) == 0) {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'annual_budget_before' ) 
				) );
				redirect ( 'budgets/add/' );
			}
			
			if ($budget_menuesl_cumule > $budget_annuel) {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'annual_budget_overload' ) 
				) );
				redirect ( 'budgets/add/' );
			}
		}
		// ==================================================================================================================
		
		if ($this->form_validation->run () == FALSE) {
			
			$budget ['budget_quarter'] = $this->pbf->get_quarter ( $budget ['budget_month'] );
			$budget ['entity_class_id'] = $this->budgets_mdl->get_entity_classes_id ( $budget ['entity_id'] );
			$budget ['entity_type_id'] = $this->budgets_mdl->get_entity_type_id ( $budget ['entity_id'] );
			$data ['budget'] = $budget;
			
			if ($periodic == 'mensuel') {
				$this->add ( $data );
			} else {
				$this->add_annuel ( $data );
			}
		} else {
			unset ( $budget ['submit'] );
			unset ( $budget ['level_0'] );
			unset ( $budget ['periodic'] );
			$budget ['budget_quarter'] = $this->pbf->get_quarter ( $budget ['budget_month'] );
			$budget ['entity_class_id'] = $this->budgets_mdl->get_entity_classes_id ( $budget ['entity_id'] );
			$budget ['entity_type_id'] = $this->budgets_mdl->get_entity_type_id ( $budget ['entity_id'] );
			
			if ($this->budgets_mdl->save_budget ( $budget )) 

			{
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => 'Budget saved successfully' 
				) );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => 'Error saving budget' 
				) );
			}
			$this->pbf->set_eventlog ( '', 0 );
			redirect ( $this->session->userdata ( 'next_base_url' ) );
		}
	}
	function save_budget_exist($periodic, $budget) {
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'budget_value', 'budget value', 'trim|required|numeric' );
		
		// ========================verification saisie du budget mensuel===========================================
		if ($periodic == 'mensuel') {
			
			$budget_annuel = $this->get_annual_budget ( $budget ['budget_year'], $budget ['entity_id'] );
			$budget_menuesl_cumule = $this->get_cumul_month_budget_month ( $budget ['budget_year'], $budget ['entity_id'], $budget ['budget_value'] );
			
			if ($budget_menuesl_cumule > $budget_annuel) {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'annual_budget_overload' ) 
				) );
				redirect ( 'budgets/edit/' . $budget ['budget_id'] );
			}
		}
		
		if ($periodic == 'annuel') {
			
			$budget_annuel = $budget ['budget_value'];
			$budget_menuesl_cumule = $this->get_cumul_month_budget ( $budget ['budget_year'], $budget ['entity_id'], 0 );
			
			if ($budget_menuesl_cumule > $budget_annuel) {
				
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'annual_budget_overload' ) 
				) );
				redirect ( 'budgets/edit/' . $budget ['budget_id'] );
			}
		}
		
		// ==================================================================================================================
		
		if ($this->form_validation->run () == FALSE) {
			$budget ['budget_quarter'] = $this->pbf->get_quarter ( $budget ['budget_month'] );
			$budget ['entity_class_id'] = $this->budgets_mdl->get_entity_classes_id ( $budget ['entity_id'] );
			$budget ['entity_type_id'] = $this->budgets_mdl->get_entity_type_id ( $budget ['entity_id'] );
			$data ['budget'] = $budget;
			redirect ( 'budget/edit/' . $budget ['budget_id'] );
		} else {
			unset ( $budget ['submit'] );
			unset ( $budget ['level_0'] );
			
			$budget ['budget_quarter'] = $this->pbf->get_quarter ( $budget ['budget_month'] );
			$budget ['entity_class_id'] = $this->budgets_mdl->get_entity_classes_id ( $budget ['entity_id'] );
			$budget ['entity_type_id'] = $this->budgets_mdl->get_entity_type_id ( $budget ['entity_id'] );
			
			if ($this->budgets_mdl->save_budget ( $budget )) 

			{
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => 'Budget saved successfully' 
				) );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => 'Error saving budget' 
				) );
			}
			$this->pbf->set_eventlog ( '', 0 );
			redirect ( $this->session->userdata ( 'next_base_url' ) );
		}
	}
	function exist_annual_budget($year, $entity) {
		$verif_anual_budget = $this->budgets_mdl->verif_annual_budget ( $year, $entity );
		return $verif_anual_budget;
	}
	function exist_mensual_budget($month, $year, $entity) {
		$verif_mensual_budget = $this->budgets_mdl->verif_mensual_budget ( $month, $year, $entity );
		return $verif_mensual_budget;
	}
	function get_cumul_month_budget($year, $entity, $month_budget) {
		$get_cumul_month_budget = $this->budgets_mdl->get_cumul_month_budget ( $year, $entity );
		$sum = 0;
		foreach ( $get_cumul_month_budget as $k ) {
			$sum = $sum + $k ['budget'];
		}
		return $sum + $month_budget;
	}
	function get_cumul_month_budget_month($year, $entity, $month_budget) {
		$get_cumul_month_budget = $this->budgets_mdl->get_cumul_month_budget ( $year, $entity );
		$current_month_budget = $this->budgets_mdl->current_month_budget ( $budget ['budget_id'] );
		$budget_current_month = 0;
		
		foreach ( $current_month_budget as $b ) {
			$budget_current_month = $budget_current_month + $b ['budget'];
		}
		
		$sum = 0;
		foreach ( $get_cumul_month_budget as $k ) {
			$sum = $sum + $k ['budget'];
		}
		return $sum + $month_budget - $budget_current_month;
	}
	function get_annual_budget($year, $entity) {
		$get_year_budget = $this->budgets_mdl->get_annual_budget_entity ( $year, $entity );
		$sum = 0;
		foreach ( $get_year_budget as $k ) {
			$sum = $sum + $k ['budget'];
		}
		return $sum;
	}
}
