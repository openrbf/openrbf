<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Geo extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'geo_mdl' );
		$this->lang->load ( 'geo', $this->config->item ( 'language' ) );
	}
	function index() {
		redirect ( 'geo/geos' );
	}
	function geos() {
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->pbf_mdl->get_geoleveles_listing ( $preps ['offset'], $preps ['terms'] );
		$lvl_hlder = '';
		$last_parent = '';
		foreach ( $data ['list'] as $k => $v ) {
			if ($v ['lvl_hlder'] != '' && ($last_parent != $v ['geo_parent'])) {
				$lvl_hlder .= '&nbsp;&nbsp;&nbsp;';
			} elseif ($last_parent == $v ['geo_parent']) {
				$lvl_hlder = $lvl_hlder;
			} else {
				$lvl_hlder = '';
			}
			$last_parent = $v ['geo_parent'];
			$data ['list'] [$k] ['geo_title'] = $lvl_hlder . $v ['lvl_hlder'] . ' ' . anchor ( '/geo/editgeo/' . $v ['geo_id'], $this->lang->line ( 'geo_key_' . $v ['geo_id'] ) );
			
			$data ['list'] [$k] ['geo_active'] = $this->pbf->rec_op_icon ( 'active_' . $data ['list'] [$k] ['geo_active'], '/geo/setgeostate/' . $data ['list'] [$k] ['geo_id'] );
			
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/geo/editgeo/' . $data ['list'] [$k] ['geo_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/geo/delgeo/' . $data ['list'] [$k] ['geo_id'] );
			$data ['list'] [$k] ['geo_id'] = $k + $preps ['offset'] + 1;
			unset ( $data ['list'] [$k] ['sortorder'] );
			unset ( $data ['list'] [$k] ['geo_parent'] );
			unset ( $data ['list'] [$k] ['lvl_hlder'] );
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'geo_geo_title' ),
				'',
				'',
				'' 
		) );
		
		$data ['mod_title'] ['geo/addgeo'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'geo_title' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['tab_menus'] = implode ( '&nbsp;&nbsp;|&nbsp;&nbsp;', $this->pbf->get_geo_classes () ) . '&nbsp;&nbsp;|&nbsp;&nbsp;' . $this->pbf->get_mod_submenu ( 16 );
		
		// TRIGGER WARNING MESSAGE WHEN THERE IS NO DEFAULT USER GROUP
		$get_default_pbf_geo = $this->pbf->get_default_pbf_geo ();
		
		if (empty ( $get_default_pbf_geo )) {
			$data ['mod_clss'] = 'warning';
			$data ['mod_msg'] = $this->lang->line ( 'geo_missing_default_geo' );
		}
		//
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	function geozones($geo_id) {
		$this->session->set_userdata ( 'geoclass', $geo_id );
		redirect ( 'geo/geoclass/class/' . $geo_id );
	}
	function geoclass() {
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->geo_mdl->get_zones_by_geo ( $preps ['offset'], $preps ['terms'] );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['geozone_name'] = anchor ( '/geo/editzone/' . $data ['list'] [$k] ['geozone_id'], $data ['list'] [$k] ['geozone_name'] );
			$data ['list'] [$k] ['geozone_active'] = $this->pbf->rec_op_icon ( 'active_' . $data ['list'] [$k] ['geozone_active'], '/geo/setgeozonestate/' . $data ['list'] [$k] ['geozone_id'] );
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/geo/editzone/' . $data ['list'] [$k] ['geozone_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/geo/delzone/' . $data ['list'] [$k] ['geozone_id'] );
			$data ['list'] [$k] ['geozone_id'] = $k + $preps ['offset'] + 1;
			
			unset ( $data ['list'] [$k] ['geozone_htmlmap'] );
			unset ( $data ['list'] [$k] ['geozone_geojson'] );
			unset ( $data ['list'] [$k] ['geozone_mapath'] );
			unset ( $data ['list'] [$k] ['geo_id'] );
			unset ( $data ['list'] [$k] ['geozone_parentid'] );
			unset ( $data ['list'] [$k] ['geo_gov_code'] );
			unset ( $data ['list'] [$k] ['geozone_description'] );
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'geo_zone_name' ),
				$this->lang->line ( 'geo_zone_catch_pop' ),
				$this->lang->line ( 'geo_zone_pop_year' ),
				$this->lang->line ( 'geo_bonus' ),
				'',
				'',
				'' 
		) );
		
		$data ['mod_title'] ['geo/addzone'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'geo_title' ) . ' - [' . $data ['records_num'] . ' ' . $this->lang->line ( 'geo_key_' . $data ['geo_class_name'] ['geo_id'] ) . ']';
		
		$data ['tab_menus'] = implode ( '&nbsp;&nbsp;|&nbsp;&nbsp;', $this->pbf->get_geo_classes () ) . '&nbsp;&nbsp;|&nbsp;&nbsp;' . $this->pbf->get_mod_submenu ( 16 );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	function addgeo($data = '') {
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'geo_title' );
		$data ['geos'] = $this->pbf_mdl->get_geoleveles ();
		
		$zones = array ();
		$zones [''] = $this->lang->line ( 'app_form_dropdown_national' );
		
		foreach ( $data ['geos'] as $k => $v ) {
			$zones [$data ['geos'] [$k] ['geo_id']] = $this->lang->line ( 'geo_key_' . $v ['geo_id'] );
		}
		
		$data ['geos'] = $zones;
		
		$data ['page'] = 'geo_frm';
		$this->load->view ( 'body', $data );
	}
	function addzone($data = '') {
		$raw_zones = $this->pbf->get_geozones_by_parent_geo_id ( $this->session->userdata ( 'geoclass' ) );
		$zones = array ();
		foreach ( $raw_zones as $zone ) {
			
			$zones [$zone ['geozone_id']] = $zone ['geozone_name'];
		}
		
		$data ['geozone'] = $zones;
		
		$data ['years'] = $this->pbf->get_years_list ();
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'geo_title' );
		$data ['page'] = 'zone_frm';
		
		$this->load->view ( 'body', $data );
	}
	function savegeo() {
		$geo = $this->input->post ();
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'geo_title', 'Title', 'trim|required|strtoupper' );
		
		if ($this->form_validation->run () == FALSE) {
			$this->addgeo ( $geo );
		} else {
			unset ( $geo ['submit'] );
			
			$geo ['geo_parent'] = empty ( $geo ['geo_parent'] ) ? NULL : $geo ['geo_parent'];
			$geo ['geo_active'] = ! isset ( $geo ['geo_active'] ) ? 0 : 1;
			
			if ($this->geo_mdl->save_geo ( $geo )) {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => $this->lang->line ( 'geo_save_success' ) 
				) );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'geo_save_error' ) 
				) );
			}
			$this->pbf->set_eventlog ();
			redirect ( $this->session->userdata ( 'next_base_url' ) );
		}
	}
	function savezone() {
		$zone = $this->input->post ();
		// print_test($zone);exit;
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'geozone_name', 'Zone name', 'trim|required|strtoupper' );
		// $this->form_validation->set_rules('geo_id', 'Zone type', 'trim|required');
		
		if ($this->form_validation->run () == FALSE) {
			
			$this->addzone ( $zone );
		} else {
			$zone ['geo_id'] = $this->session->userdata ( 'geoclass' );
			
			unset ( $zone ['submit'] );
			if ($_FILES ['geozone_mapath'] ['tmp_name']) {
				
				$config ['file_field_name'] = 'geozone_mapath';
				$config ['file_name'] = $zone ['geozone_name'];
				$config ['upload_path'] = FCPATH . 'cside/images/portal/';
				$config ['allowed_types'] = 'gif|jpg|png';
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
					$this->addzone ();
				} else {
					$data = $this->upload->data ();
					// $this->load->view('upload_success', $data);
					$zone ['geozone_mapath'] = $data ['file_name']; // may file_name is enough
				}
			}
			$zone ['geozone_active'] = ! isset ( $zone ['geozone_active'] ) ? 0 : 1;
			$zone ['geozone_parentid'] = (! isset ( $zone ['geozone_parentid'] ) || $zone ['geozone_parentid'] == '') ? NULL : $zone ['geozone_parentid'];
			if ($this->geo_mdl->save_zone ( $zone )) 

			{
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => $this->lang->line ( 'geo_zone_save_success' ) 
				) );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'error',
						'mod_msg' => $this->lang->line ( 'geo_zone_save_error' ) 
				) );
			}
			// redirect('geo/geozones/'.$zone['geo_id']); // which also works so well
			$this->pbf->set_eventlog ( 'geo_zone_edit', 1 );
			redirect ( $this->session->userdata ( 'next_base_url' ) );
		}
	}
	function editgeo($geo_id) {
		$data ['geo'] = $this->geo_mdl->get_geo ( $geo_id );
		$this->addgeo ( $data );
	}
	function editzone($geozone_id) {
		$data ['zone'] = $this->geo_mdl->get_zone ( $geozone_id );
		
		$this->addzone ( $data );
	}
	function setgeostate($geo_id, $state) {
		if ($this->geo_mdl->set_geo_state ( $geo_id, $state )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'geo_level_state_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'geo_level_state_error' ) 
			) );
		}
		$this->pbf->set_eventlog ();
		redirect ( 'geo/geos' );
	}
	function setgeozonestate($geozone_id, $state) {
		if ($this->geo_mdl->set_geo_zone_state ( $geozone_id, $state )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'geo_zone_state_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'geo_zone_state_error' ) 
			) );
		}
		$this->pbf->set_eventlog ();
		redirect ( '/geo/geozones/' . $this->session->userdata ( 'geoclass' ) );
	}
	function delgeo($geo_id) {
		if ($this->geo_mdl->del_geo ( $geo_id )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'geo_delete_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'error',
					'mod_msg' => $this->lang->line ( 'geo_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ();
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	function delzone($geozone_id) {
		if ($this->geo_mdl->del_geo_zone ( $geozone_id )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'geo_zone_delete_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'error',
					'mod_msg' => $this->lang->line ( 'geo_zone_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ();
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
}
