<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Donneurs extends CI_Controller {
	
	// ==========================================================================================================================
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'donneurs_mdl' );
		$this->lang->load ( 'donors', $this->config->item ( 'language' ) );
		$this->lang->load ( 'hfrentities', $this->config->item ( 'language' ) );
	}
	
	// ==============================Donors================================================================================
	function index() {
		redirect ( 'donneurs/donors_list' );
	}
	function move_up($id_donor = null) {
		$this->donneurs_mdl->move_up ( $id_donor );
		redirect ( 'donneurs/donors_list' );
	}
	function donors_list() {
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		$data = $this->donneurs_mdl->get_donors ( $preps ['offset'], $preps ['terms'] );
		foreach ( $data ['list'] as $k => $v ) {
			$data ['list'] [$k] ['donor_name'] = anchor ( '/donneurs/edit/' . $data ['list'] [$k] ['donor_id'], $data ['list'] [$k] ['donor_name'] );
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/donneurs/edit/' . $data ['list'] [$k] ['donor_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/donneurs/delete/' . $data ['list'] [$k] ['donor_id'] );
			$data ['list'] [$k] ['up'] = (($data ['list'] [$k] ['donor_priority'] == 1) ? '' : $this->pbf->rec_op_icon ( 'up', '/donneurs/move_up/' . $data ['list'] [$k] ['donor_id'] ));
			$data ['list'] [$k] ['donor_logopath'] = '<img width="50" height="50" src="' . $this->config->item ( 'base_url' ) . '/cside/frontend/temp/' . $data ['list'] [$k] ['donor_logopath'] . '" border="0">';
			$data ['list'] [$k] ['donor_id'] = $k + $preps ['offset'] + 1;
			unset ( $data ['list'] [$k] ['groupassociated_id'] );
			unset ( $data ['list'] [$k] ['donor_priority'] );
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'list_donors_title' ),
				$this->lang->line ( 'list_donor_logo' ),
				$this->lang->line ( 'list_donor_website' ),
				$this->lang->line ( 'list_donor_contact' ),
				$this->lang->line ( 'list_donor_email' ),
				$this->lang->line ( 'frm_donor_abrev' ),
				'' 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'donors_title' ) . ' [' . $data ['records_num'] . ' ]';
		$data ['mod_title'] ['/donneurs/add'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		$data ['tab_menus'] = $this->pbf->get_mod_submenu ( 54 );
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		$data ['tab_menus'] = $this->pbf->get_mod_submenu ( 54 );
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	function add($data = '') {
		$data ['groups'] = $this->pbf->get_groups ();
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'donors_title' );
		$data ['groups'] = $this->pbf->get_groups ();
		$data ['page'] = 'donors_frm';
		$this->load->view ( 'body', $data );
	}
	function edit($donor_id) {
		$data = $this->donneurs_mdl->get_donor ( $donor_id );
		$this->add ( $data );
	}
	function delete($donor_id) {
		if ($this->donneurs_mdl->delete ( $donor_id )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'donor_success_delete_msg' ) 
			) );
			$this->donneurs_mdl->retrieve_priority ();
			$this->pbf->set_eventlog ( '', 0 );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'donor_delete_error' ) 
			) );
		}
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	function save() {
		$donor = $this->input->post ();
		unset ( $donor ['submit'] );
		$this->load->library ( 'form_validation' );
		$max_id = $this->donneurs_mdl->get_maxid ();
		if (! isset ( $donor ['donor_id'] ) or (empty ( $donor ['donor_id'] ))) {
			$priority = $this->donneurs_mdl->set_priority ();
			$donor ['donor_priority'] = $priority;
		}
		if ($_FILES ['donor_logopath'] ['tmp_name']) {
			
			$config ['file_field_name'] = 'donor_logopath';
			$config ['file_name'] = ((! empty ( $donor ['donor_id'] )) ? $donor ['donor_id'] : $max_id) . '_donor_logo.jpg';
			$config ['file_new_name'] = ((! empty ( $donor ['donor_id'] )) ? $donor ['donor_id'] : $max_id) . '_donor_logo';
			$config ['upload_path'] = FCPATH . '/cside/frontend/temp';
			$config ['allowed_types'] = 'jpg|png|JPG';
			$config ['overwrite'] = TRUE;
			$config ['remove_spaces'] = TRUE;
			$config ['max_filename'] = '0';
			$config ['max_size'] = '0'; // use the system limit... see php.ini config in regards
			$config ['max_width'] = '0'; // should be 360 at destination
			$config ['max_height'] = '0'; // should be 300 at destination
			
			$this->load->library ( 'upload', $config );
			
			if (! $this->upload->do_upload ( $config ['file_field_name'] )) {
				$error = array (
						'error' => $this->upload->display_errors () 
				);
				
				// $this->load->view('upload_form', $error);
				$this->add ();
			} else {
				$data = $this->upload->data ();
				// $this->load->view('upload_success', $data);
				$donor ['donor_logopath'] = $data ['file_name']; // may file_name is enough
			}
			
			$this->pbf->resize_image ( $config );
		}
		
		$this->form_validation->set_rules ( 'donor_name', 'donor name', 'trim|required' );
		if ($this->form_validation->run () == FALSE) {
			$this->add ( $donor );
		} else {
			if ($this->donneurs_mdl->save_donors ( $donor )) {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => $this->lang->line ( 'donor_save_success' ) 
				) );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'donor_save_error' ) 
				) );
			}
			
			$this->pbf->set_eventlog ( '', 0 );
			redirect ( 'donneurs/donors_list' );
		}
	}
	// =============================================================================================================================================================
	
	// =====================================================Donors config===========================================================================================
	function donorsconfig_list() {
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		$data = $this->donneurs_mdl->get_donors_conf ( $preps ['offset'], $preps ['terms'] );
		foreach ( $data ['list'] as $k => $v ) {
			$data ['list'] [$k] ['donor_id'] = anchor ( '/donneurs/edit_config/' . $data ['list'] [$k] ['donorconfig_id'], $this->pbf->get_donor_name ( $data ['list'] [$k] ['donorconfig_id'] ) );
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/donneurs/edit_config/' . $data ['list'] [$k] ['donorconfig_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/donneurs/delete_config/' . $data ['list'] [$k] ['donorconfig_id'] );
			$data ['list'] [$k] ['donorconfig_id'] = $k + $preps ['offset'] + 1;
			$data ['list'] [$k] ['donorsconfig_name'];
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'donors_title' ),
				$this->lang->line ( 'list_donor_from' ),
				$this->lang->line ( 'list_donor_to' ),
				'' 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'donors_config_title' ) . ' [' . $data ['records_num'] . ' ]';
		$data ['mod_title'] ['/donneurs/addconfig/'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		$data ['rec_filters'] = $this->pbf->get_filters ( array (
				'donor_name',
				'date' 
		) );
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		$data ['tab_menus'] = $this->pbf->get_mod_submenu ( 54 );
		;
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	function addconfig($data = '') {
		$default_donors = array ();
		$default_donors = $this->pbf->get_donors ();
		$data ['regions'] = $this->pbf->get_regions ();
		$data ['districts'] = $this->pbf->get_districts ();
		$data ['entities'] = $this->pbf->get_entities_donors ( 2 );
		$data ['indicators'] = $this->pbf->get_indicators ();
		$data ['default_donors'] = $default_donors;
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'donors_config_title' );
		$data ['page'] = 'donorsconf_frm';
		$this->load->view ( 'body', $data );
	}
	function save_config() {
		$collect_donor_confs = $this->input->post ();
		$donorconfig ['donor_id'] = $collect_donor_confs ['donor_id'];
		$donorconfig ['from'] = $collect_donor_confs ['from'];
		$donorconfig ['to'] = $collect_donor_confs ['to'];
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'donor_id', 'donor id', 'trim|required' );
		$this->form_validation->set_rules ( 'from', 'from', 'trim|required' );
		$this->form_validation->set_rules ( 'to', 'to', 'trim|required' );
		if ($this->form_validation->run () == FALSE) {
			$this->addconfig ( $donorconfig );
		} else {
			if ($this->donneurs_mdl->save_donorconfig ( $donorconfig )) {
				$current_config_id = $this->db->insert_id ();
				foreach ( $collect_donor_confs ['zone_id'] as $key => $zone_temp ) {
					$config_details ['donor_conf_id'] = $current_config_id;
					$config_details ['zone_id'] = (! empty ( $zone_temp )) ? $zone_temp : '';
					$config_details ['district_id'] = (! empty ( $collect_donor_confs ['district_id'] [$key] )) ? $collect_donor_confs ['district_id'] [$key] : '';
					$config_details ['entity_id'] = (! empty ( $collect_donor_confs ['entity_id'] [$key] )) ? $collect_donor_confs ['entity_id'] [$key] : '';
					$config_details ['indicator_id'] = (! empty ( $collect_donor_confs ['indicator_id'] [$key] )) ? $collect_donor_confs ['indicator_id'] [$key] : '';
					$config_details ['percentage'] = $collect_donor_confs ['percentage_id_value'] [$key];
					$this->donneurs_mdl->save_donorconfig_details ( $config_details );
					$current_conf_indic_id = $this->db->insert_id ();
					
					$entity_list = array ();
					if ((! empty ( $config_details ['entity_id'] )) and ($config_details ['entity_id'] !== 0)) {
						$entity_list [] = $config_details ['entity_id'];
					}
					if ((! empty ( $config_details ['district_id'] )) and ($config_details ['district_id'] !== 0) and ((empty ( $config_details ['entity_id'] )) or ($config_details ['entity_id'] == 0))) {
						$entity_list = $this->pbf->get_entities_by_district ( $config_details ['district_id'] );
					}
					if ((! empty ( $config_details ['zone_id'] )) and ($config_details ['zone_id'] !== 0) and ((empty ( $config_details ['district_id'] )) or ($config_details ['district_id'] == 0) and ((empty ( $config_details ['entity_id'] )) or ($config_details ['entity_id'] == 0)))) {
						$entity_list = $this->pbf->get_entities_by_region ( $config_details ['zone_id'] );
					}
					
					if (((empty ( $config_details ['zone_id'] )) or ($config_details ['zone_id'] == 0)) and ((empty ( $config_details ['district_id'] )) or ($config_details ['district_id'] == 0)) and ((empty ( $config_details ['entity_id'] )) or ($config_details ['entity_id'] == 0))) {
						exit ();
					}
					foreach ( $entity_list as $entity ) {
						$detail_level_indic ['donorconf_id'] = $current_conf_indic_id;
						$detail_level_indic ['entity_id'] = $entity;
						$detail_level_indic ['indicator_id'] = $config_details ['indicator_id'];
						$this->donneurs_mdl->save_donor_indic_details ( $detail_level_indic );
					}
				}
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'indicator_save_error' ) 
				) );
			}
			
			$this->pbf->set_eventlog ( '', 0 );
			redirect ( 'donneurs/donorsconfig_list' );
		}
	}
	function edit_config($donorconfig_id) {
		$data = $this->donneurs_mdl->get_donorconfig ( $donorconfig_id );
		$data ['config_details'] = $this->donneurs_mdl->get_donorconfig_details ( $donorconfig_id );
		$this->addconfig ( $data );
	}
	function delete_config($donorconfig_id) {
		if ($this->donneurs_mdl->delete_config ( $donorconfig_id )) {
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'config_donor_success_delete_msg' ) 
			) );
		} 

		else {
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'config_donor_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	function json_get_zones() {
		if (isset ( $_POST ["region_id"] )) {
			$region_id = $_POST ['region_id'];
		} else {
			$region_id = 1;
		}
		$zones = $this->pbf->get_district_region ( $region_id );
		echo json_encode ( $zones );
	}
	function json_get_entities() {
		if (isset ( $_POST ["district_id"] )) {
			$district_id = $_POST ['district_id'];
		} else {
			$district_id = 1;
		}
		$entities = $this->pbf->get_entities_district_donor ( $district_id );
		echo json_encode ( $entities );
	}
	// =================================================================================================================================================
}