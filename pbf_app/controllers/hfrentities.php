<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Hfrentities extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'entities_mdl' );
		$this->load->model ( 'geo_mdl' );
		$this->lang->load ( 'hfrentities', $this->config->item ( 'language' ) );
		$this->load->library ( 'pearloader' );
		$this->pearloader->load ( 'Writer' );
		$this->load->helper ( 'download' );
	}
	
	function index($class_id) {
		if ($this->config->item ( 'debug' ))
			$this->output->enable_profiler ( TRUE );
		
		$this->session->set_userdata ( array (
				'entity_class' => $class_id 
		) );
		
		redirect ( 'hfrentities/hfrentity/' );
	}
	
	function hfrmanage($class_id) {
		$this->lang->load ( 'otheroptions', $this->config->item ( 'language' ) );
		
		$this->session->set_userdata ( array (
				'entity_class' => $class_id 
		) );
		
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->entities_mdl->get_entities ( $preps ['offset'], $preps ['terms'] );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['entity_name'] = anchor ( '/hfrentities/editentity/' . $v ['entity_id'], $v ['entity_name'] . ' ' . $this->lang->line ( 'etty_typ_abbrv_ky_' . $v ['entity_type'] ) );
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/hfrentities/editentity/' . $data ['list'] [$k] ['entity_id'] );
			$data ['list'] [$k] ['entity_type'] = $this->lang->line ( 'etty_typ_ky_' . $v ['entity_type'] );
			
			$data ['list'] [$k] ['entity_status'] = $this->lang->line ( 'option_lkp_ky_' . $v ['entity_status'] );
			
			$data ['list'] [$k] ['entity_id'] = $k + $preps ['offset'] + 1;
			unset ( $data ['list'] [$k] ['entity_active'] );
			unset ( $data ['list'] [$k] ['entity_type_abbrev'] );
		}
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'entity_name' ),
				$this->lang->line ( 'entity_district' ),
				$this->lang->line ( 'entity_type' ),
				$this->lang->line ( 'entity_status' ),
				$this->lang->line ( 'entity_responsible' ),
				$this->lang->line ( 'entity_responsible_email' ),
				'' 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'entity_title' ) . ' - [' . $data ['records_num'] . ' ' . $data ['entity_class_name'] ['entity_class_name'] . ']';
		
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	
	function hfrentity() {
		if ($this->config->item ( 'debug' ))
			$this->output->enable_profiler ( TRUE );
		
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$this->lang->load ( 'otheroptions', $this->config->item ( 'language' ) );
		
		$data = $this->entities_mdl->get_entities ( $preps ['offset'], $preps ['terms'] );
	
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['entity_name'] = anchor ( '/hfrentities/editentity/' . $v ['entity_id'], $v ['entity_name'] );
			
			$data ['list'] [$k] ['entity_type'] = $this->lang->line ( 'etty_typ_ky_' . $v ['entity_type'] );
			
			$data ['list'] [$k] ['entity_status'] = $this->lang->line ( 'option_lkp_ky_' . $v ['entity_status'] );
			if ($data ['list'] [$k] ['entity_contracttype'] == 1) {
				$data ['list'] [$k] ['entity_contracttype'] = $this->lang->line ( 'primary' );
			} else {
				$data ['list'] [$k] ['entity_contracttype'] = $this->lang->line ( 'second' );
			}
			$data ['list'] [$k] ['entity_active'] = $this->pbf->rec_op_icon ( 'active_' . $data ['list'] [$k] ['entity_active'], '/hfrentities/setentitystate/' . $data ['list'] [$k] ['entity_id'] );
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/hfrentities/editentity/' . $data ['list'] [$k] ['entity_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/hfrentities/delentity/' . $data ['list'] [$k] ['entity_id'] );
			$data ['list'] [$k] ['checkbox'] = form_checkbox ( 'item[]', $data ['list'] [$k] ['entity_id'] );
			$data ['list'] [$k] ['entity_id'] = $k + $preps ['offset'] + 1;
			unset ( $data ['list'] [$k] ['entity_type_abbrev'] );
		}
		$check_all = array (
				'name' => 'sel_all',
				'id' => 'sel_all',
				'onClick' => 'selecte_all(this)' 
		);
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'entity_name' ),
				$this->lang->line ( 'entity_district' ),
				$this->lang->line ( 'entity_type' ),
				$this->lang->line ( 'entity_contracttype' ),
				$this->lang->line ( 'entity_status' ),
				$this->lang->line ( 'entity_responsible' ),
				$this->lang->line ( 'entity_responsible_email' ),
				'',
				'',
				'',
				form_checkbox ( $check_all ) 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'entity_title' ) . ' - [' . $data ['records_num'] . ' ' . $this->lang->line ( 'etty_cls_ky_' . $data ['entity_class_name'] ['entity_class_id'] ) . ']';
		$data ['mod_title'] ['/hfrentities/addentity'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['#'] = $this->pbf->rec_op_icon ( 'delete_selected' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['tab_menus'] = implode ( '&nbsp;&nbsp;|&nbsp;&nbsp;', $this->pbf->get_entities_classes () ) . '&nbsp;&nbsp;|&nbsp;&nbsp;' . $this->pbf->get_mod_submenu ( 15 );
		
		$data ['rec_filters'] = $this->pbf->get_filters ( array (
				'entity_name',
				'geozone_id' 
		) );
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		
		$this->load->view ( 'body', $data );
	}
	
	function groups() {
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->entities_mdl->get_groups ( $preps ['offset'], $preps ['terms'] );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['entity_group_name'] = anchor ( '/hfrentities/editgroup/' . $data ['list'] [$k] ['entity_group_id'], $data ['list'] [$k] ['entity_group_name'] );
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/hfrentities/editgroup/' . $data ['list'] [$k] ['entity_group_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/hfrentities/delgroup/' . $data ['list'] [$k] ['entity_group_id'] );
			$data ['list'] [$k] ['entity_group_id'] = $k + $preps ['offset'] + 1;
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'entity_group' ),
				$this->lang->line ( 'entity_groups_abbrev' ),
				'',
				'' 
		) );
		
		$data ['mod_title'] ['hfrentities/addgroup'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'entity_title' ) . ' - ' . $this->lang->line ( 'entity_group_title' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['tab_menus'] = implode ( '&nbsp;&nbsp;|&nbsp;&nbsp;', $this->pbf->get_entities_classes () ) . '&nbsp;&nbsp;|&nbsp;&nbsp;' . $this->pbf->get_mod_submenu ( 15 );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	
	function classes() {
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->entities_mdl->get_classes ( $preps ['offset'], $preps ['terms'] );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['entity_class_name'] = anchor ( '/hfrentities/editclass/' . $v ['entity_class_id'], $this->lang->line ( 'etty_cls_ky_' . $v ['entity_class_id'] ) );
			
			$data ['list'] [$k] ['entity_class_abbrev'] = $this->lang->line ( 'etty_cls_abbrv_ky_' . $v ['entity_class_id'] );
			
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/hfrentities/editclass/' . $data ['list'] [$k] ['entity_class_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/hfrentities/delclass/' . $data ['list'] [$k] ['entity_class_id'] );
			$data ['list'] [$k] ['entity_class_id'] = $k + $preps ['offset'] + 1;
			unset ( $data ['list'] [$k] ['entity_class_properties'] );
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'entity_class' ),
				$this->lang->line ( 'entity_class_abbrev' ),
				'',
				'' 
		) );
		
		$data ['mod_title'] ['hfrentities/addclass'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'entity_title' ) . ' - ' . $this->lang->line ( 'entity_class_title' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['tab_menus'] = implode ( '&nbsp;&nbsp;|&nbsp;&nbsp;', $this->pbf->get_entities_classes () ) . '&nbsp;&nbsp;|&nbsp;&nbsp;' . $this->pbf->get_mod_submenu ( 15 );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	
	function types() {
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->entities_mdl->get_types ( $preps ['offset'], $preps ['terms'] );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['entity_type_name'] = anchor ( '/hfrentities/edittype/' . $v ['entity_type_id'], $this->lang->line ( 'etty_typ_ky_' . $v ['entity_type_id'] ) );
			
			$data ['list'] [$k] ['entity_type_abbrev'] = $this->lang->line ( 'etty_typ_abbrv_ky_' . $v ['entity_type_id'] );
			
			$data ['list'] [$k] ['entity_class_abbrev'] = $this->lang->line ( 'etty_cls_ky_' . $v ['entity_class_id'] ) . ' (' . $this->lang->line ( 'etty_cls_abbrv_ky_' . $v ['entity_class_id'] ) . ')';
			
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/hfrentities/edittype/' . $data ['list'] [$k] ['entity_type_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/hfrentities/deltype/' . $data ['list'] [$k] ['entity_type_id'] );
			$data ['list'] [$k] ['entity_type_id'] = $k + $preps ['offset'] + 1;
			unset ( $data ['list'] [$k] ['entity_class_name'] );
			unset ( $data ['list'] [$k] ['entity_class_id'] );
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'entity_type' ),
				$this->lang->line ( 'entity_type_abbrev' ),
				$this->lang->line ( 'entity_class' ),
				'',
				'' 
		) );
		
		$data ['mod_title'] ['hfrentities/addtype'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'entity_title' ) . ' - ' . $this->lang->line ( 'entity_types_title' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['tab_menus'] = implode ( '&nbsp;&nbsp;|&nbsp;&nbsp;', $this->pbf->get_entities_classes () ) . '&nbsp;&nbsp;|&nbsp;&nbsp;' . $this->pbf->get_mod_submenu ( 15 );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	
	function addentity($data = '') {
		if ($this->config->item ( 'debug' ))
			$this->output->enable_profiler ( TRUE );
		
		$data ['entity_web_form'] = $this->pbf->get_entity_web_form ( $this->session->userdata ( 'entity_class' ) );
		
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'entity_title' );
		
		$data ['page'] = 'entity_frm';
		
		$this->load->view ( 'body', $data );
	}
	
	function addgroup($data = '') {
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'entity_group' );
		$data ['page'] = 'entitygroup_frm';
		$this->load->view ( 'body', $data );
	}
	
	function addclass($data = '', $entity_class_id = '') {
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'entity_title' ) . ' - ' . $this->lang->line ( 'entity_class_title' );
		$data ['usergroup_id'] = $this->pbf->get_usersgroups_multiselect ();
		
		$data ['class_properties'] = $this->pbf->get_asset_properties ( 'pbf_entities' );
		
		$data ['entityclass'] ['usergroup_id'] = $this->pbf->get_asset_access_rw ( $entity_class_id, 'entity_class' );
		
		foreach ( $data ['entityclass'] ['usergroup_id'] as $k => $value ) {
			$write = $data ['entityclass'] ['usergroup_id'] [$k] ['write_access'] == 1 ? TRUE : FALSE;
			$read = $data ['entityclass'] ['usergroup_id'] [$k] ['read_access'] == 1 ? TRUE : FALSE;
			$data ['entityclass'] ['usergroup_id'] [$k] ['checkbox_read'] = form_checkbox ( 'items_read[]', $k, $read );
			$data ['entityclass'] ['usergroup_id'] [$k] ['checkbox_write'] = form_checkbox ( 'items_write[]', $k, $write );
			
			unset ( $data ['entityclass'] ['usergroup_id'] [$k] ['write_access'] );
			unset ( $data ['entityclass'] ['usergroup_id'] [$k] ['read_access'] );
			unset ( $data ['entityclass'] ['usergroup_id'] [$k] ['id'] );
		}
		
		array_unshift ( $data ['entityclass'] ['usergroup_id'], array (
				'#',
				'Profile',
				$this->lang->line ( 'access_read' ),
				$this->lang->line ( 'access_write' ) 
		) );
		
		$data ['page'] = 'entityclass_frm';
		$this->load->view ( 'body', $data );
	}
	
	function addtype($data = '') {
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'entity_title' ) . ' - ' . $this->lang->line ( 'entity_types_title' );
		$data ['entity_class'] = $this->pbf->get_entity_classes ();
		$data ['page'] = 'entitytype_frm';
		$this->load->view ( 'body', $data );
	}
	
	function editgroup($entity_group_id) {
		$data ['entitygroup'] = $this->entities_mdl->get_entitygroup ( $entity_group_id );
		$this->addgroup ( $data );
	}
	
	function editclass($entity_class_id) {
		$data ['entityclass'] = $this->entities_mdl->get_entityclass ( $entity_class_id );
		
		$data ['entityclass'] ['class_properties'] = json_decode ( $data ['entityclass'] ['entity_class_properties'] );
		
		$this->addclass ( $data, $entity_class_id );
	}
	
	function edittype($entity_type_id) {
		$data ['entitytype'] = $this->entities_mdl->get_entitytype ( $entity_type_id );
		$this->addtype ( $data );
	}
	
	function savegroup() {
		$group = $this->input->post ();
		
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'entity_group_name', 'Entity group title', 'trim|required' );
		$this->form_validation->set_rules ( 'entity_group_abbrev', 'Entity group abbrev', 'trim|required' );
		
		if ($this->form_validation->run () == FALSE) {
			$this->addgroup ( $group );
		} else {
			unset ( $group ['submit'] );
			
			if ($this->entities_mdl->save_group ( $group )) 

			{
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => $this->lang->line ( 'entity_group_save_success' ) 
				) );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'entity_group_save_error' ) 
				) );
			}
			$this->pbf->set_eventlog ( '', 0 );
			redirect ( $this->session->userdata ( 'next_base_url' ) );
		}
	}
	
	function saveclass() {
		$klass = $this->input->post ();
		
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'entity_class_name', 'Entity class title', 'trim|required' );
		$this->form_validation->set_rules ( 'entity_class_abbrev', 'Entity class abbrev', 'trim|required' );
		
		if ($this->form_validation->run () == FALSE) {
			$this->addclass ( $klass );
		} else {
			
			$entity_class_id = $klass ['entity_class_id'];
			
			$old_access = $this->pbf->get_asset_access_rw ( $entity_class_id, 'entity_class' );
			
			$access_data = array ();
			
			foreach ( $old_access as $key => $access ) {
				$read = '';
				if (isset ( $klass ['items_read'] )) {
					$read = array_search ( $key, $klass ['items_read'] );
				}
				
				$read = trim ( $read );
				$row_access = array ();
				$row_access ['id'] = $access ['id'];
				$row_access ['usersgroup_id'] = $access ['usersgroup_id'];
				$row_access ['asset_id'] = $entity_class_id;
				$row_access ['asset_link'] = 'entity_class';
				
				if ($read != '') {
					$row_access ['read_access'] = 1;
				} else {
					$row_access ['read_access'] = 0;
				}
				
				$write = '';
				
				if (isset ( $klass ['items_write'] )) {
					$write = array_search ( $key, $klass ['items_write'] );
				}
				
				$write = trim ( $write );
				if ($write != '') {
					$row_access ['write_access'] = 1;
				} else {
					$row_access ['write_access'] = 0;
				}
				
				array_push ( $access_data, $row_access );
			}
			
			$klass ['user_group_assets'] = $access_data;
			
			unset ( $klass ['items_read'] );
			unset ( $klass ['items_write'] );
			unset ( $klass ['submit'] );
			
			$klass ['entity_class_properties'] = json_encode ( $klass ['entity_class_properties'] );
			
			if ($this->entities_mdl->save_class ( $klass )) 

			{
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => $this->lang->line ( 'entity_class_save_success' ) 
				) );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'entity_class_save_error' ) 
				) );
			}
			
			$this->pbf->set_eventlog ( '', 0 );
			redirect ( $this->session->userdata ( 'next_base_url' ) );
		}
	}
	
	function savetype() {
		$type = $this->input->post ();
		
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'entity_class_id', 'Entity class', 'trim|required' );
		$this->form_validation->set_rules ( 'entity_type_name', 'Entity type name', 'trim|required' );
		
		if ($this->form_validation->run () == FALSE) {
			$this->addtype ( $type );
		} else {
			unset ( $type ['submit'] );
			
			if ($this->entities_mdl->save_type ( $type )) 

			{
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => $this->lang->line ( 'entity_type_save_success' ) 
				) );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'entity_type_save_error' ) 
				) );
			}
			$this->pbf->set_eventlog ( '', 0 );
			redirect ( $this->session->userdata ( 'next_base_url' ) );
		}
	}
	
	function saveentity($update = TRUE) {
		$entity = $this->input->post ();
		
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'entity_name', 'Entity name', 'trim|required' );
		
		if ($this->form_validation->run () == FALSE) {
			$this->addentity ( $entity );
		} else {
			unset ( $entity ['submit'] );
			unset ( $entity ['entity_bank_hq_id'] );
			unset ( $entity ['level_0'] );
			unset ( $entity ['level_1'] ); 
			
			if ($_FILES ['entity_contractpath'] ['tmp_name']) {
				
				$config ['file_field_name'] = 'entity_contractpath';
				$config ['file_name'] = $entity ['entity_class'] . $entity ['entity_type'] . $entity ['entity_status'] . $entity ['entity_name'] . '_contract';
				$config ['upload_path'] = FCPATH . '/cside/contents/docs/contracts';
				$config ['allowed_types'] = 'pdf';
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
					$this->addentity ();
				} else {
					$data = $this->upload->data ();
					$entity ['entity_contractpath'] = $data ['file_name']; 
				}
			}
									
			$entity ['entity_use_isolation_bonus'] = ! isset ( $entity ['entity_use_isolation_bonus'] ) ? 0 : 1;
			$entity ['entity_use_equity_bonus'] = ! isset ( $entity ['entity_use_equity_bonus'] ) ? 0 : 1;
			
			$entitytime ['entity_active_time'] = $entity ['entity_active_time'];
			$entitytime ['entity_id'] = $entity ['entity_id'];
			$entitytime ['entity_pop_time'] = $entity ['entity_pop_time'];
			$entitytime ['entity_pop_year_time'] = $entity ['entity_pop_year_time'];
			$entitytime ['entity_type_time'] = $entity ['entity_type_time'];
			$entitytime ['entity_pbf_group_id_time'] = $entity ['entity_pbf_group_id_time'];
			
			$entitytime ['use_from'] = $entity ['use_from'];
			$entitytime ['use_to'] = $entity ['use_to'];
			
			unset ( $entity ['entity_pop_time'] );
			unset ( $entity ['entity_pop_year_time'] );
			unset ( $entity ['entity_pbf_group_id_time'] );
			unset ( $entity ['entity_active_time'] );
			unset ( $entity ['entity_type_time'] );
			unset ( $entity ['use_from'] );
			unset ( $entity ['use_to'] );
			

			
			if ($this->entities_mdl->save_entity ( $entity, $entitytime, $update )) {

				
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => $this->lang->line ( 'entity_save_success' ) 
				) );
				
				// picture Upload
				
				if ($_FILES ['entity_picturepath'] ['tmp_name']) {
					
					if (empty ( $entity ['entity_id'] )) { // Ajout d'une nouvelle entit�
					                                 
						// Recuperation du dernier element enregistre
						
						$up_entity ['entity_id'] = $this->entities_mdl->get_last_entity ();
						$picture_name = $up_entity ['entity_id'] . "_" . $entity ['entity_name'];
					} else { // Modification de l'entit� existante
						
						$picture_name = $entity ['entity_id'] . "_" . $entity ['entity_name'];
						$up_entity ['entity_id'] = $entity ['entity_id'];
					}
					
					$picture_name = $this->pbf->Slug ( $picture_name );
					
					$config ['file_field_name'] = 'entity_picturepath';
					
					$config ['file_name'] = $picture_name . "_or";
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
						
						
						$this->addentity ();
					} else {
						$data = $this->upload->data ();
						$up_entity ['entity_picturepath'] = $picture_name; 
					}
					// resizing the images
					$thumb_size = $this->config->item ( 'image_thumb_size' );
					$medium_size = $this->config->item ( 'image_medium_size' );
					$big_size = $this->config->item ( 'image_big_size' );
					$image_data = $this->upload->data ();
					$config_thumb = array (
							'source_image' => $image_data ['full_path'],
							'new_image' => FCPATH . 'cside/images/portal/' . $picture_name . "_thumb.jpg",
							'maintain_ration' => true,
							'width' => $thumb_size,
							'height' => $thumb_size 
					);
					
					$config_medium = array (
							'source_image' => $image_data ['full_path'],
							'new_image' => FCPATH . 'cside/images/portal/' . $picture_name . '_med.jpg',
							'maintain_ration' => true,
							'width' => $medium_size,
							'height' => $medium_size 
					);
					
					$config_big = array (
							'source_image' => $image_data ['full_path'],
							'new_image' => FCPATH . 'cside/images/portal/' . $picture_name . '_big.jpg',
							'maintain_ration' => true,
							'width' => $big_size,
							'height' => $big_size 
					);
					
					$this->load->library ( 'image_lib' );
					$this->image_lib->initialize ( $config_thumb );
					
					if (! $this->image_lib->resize ()) {
						die ( $this->image_lib->display_errors () );
					}
					
					$this->image_lib->clear ();
					
					$this->image_lib->initialize ( $config_medium );
					
					if (! $this->image_lib->resize ()) {
						die ( $this->image_lib->display_errors () );
					}
					
					$this->image_lib->clear ();
					
					$this->image_lib->initialize ( $config_big );
					
					if (! $this->image_lib->resize ()) {
						die ( $this->image_lib->display_errors () );
					}
				}
				
				if ($up_entity ['entity_picturepath'])
					$this->entities_mdl->save_entity ( $up_entity );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'entity_save_error' ) 
				) );
			}
			$this->pbf->set_eventlog ( '', 0 );
			
	
			
			if (($this->session->userdata ( 'usergroup_id' ) != '1') && ($this->session->userdata ( 'group_id' ) != '2')) {
				$format = 'DATE_RFC822';
				$time = time ();
				
				$notice .= "</BR>Notice, entity " . $entity ['entity_name'] . " has been edited by " . $this->session->userdata ( 'user_fullname' ) . " on " . standard_date ( $format, $time );
				
				$data = array (
						'group_id' => $this->session->userdata ( 'usergroup_id' ),
						'user_id' => $this->session->userdata ( 'user_id' ),
						'message' => $notice 
				);
				$this->db->insert ( 'pbf_message', $data );
			}
			redirect ( $this->session->userdata ( 'next_base_url' ) );
		}
	}
	
	function _resize_image($conf) {
		$this->load->library ( 'image_lib', $conf );
		$this->image_lib->resize ();
	}
	
	function editentity($entity_id) {
		$this->load->model ( 'geo_mdl' );
		
		
		$data = $this->entities_mdl->get_entity_form ( $entity_id );
		
		if (! isset ( $data ['entity'] ['bank_parent_id'] )) {
			$data ['entity'] ['bank_parent_id'] = $data ['entity'] ['bank_id'];
			$data ['entity'] ['bank_id'] = null;
		}
		
		if ((in_array ( $data ['entity'] ['entity_geozone_id'], $this->session->userdata ( 'usergeozones' ) )) || ($this->session->userdata ( 'usergeozones' ) == NULL)) 		
		{
			
			$geo_info = $this->geo_mdl->get_zone ( $data ['entity'] ['entity_geozone_id'] );
			
			$data ['entity'] ['sel_parent_geozone_id'] = $geo_info ['geozone_parentid'];
			$data ['entity'] ['sel_geozone_id'] = $geo_info ['geozone_id'];
			
			$this->session->set_userdata ( $data ['entity'] );
			
			$this->addentity ( $data );
			
			// clean the session hog, could also be done inside the addentity function
			$this->session->unset_userdata ( array (
					'entity_address' => '',
					'entity_phone_number' => '',
					'entity_staff_size' => '',
					'entity_responsible_name' => '',
					'entity_responsible_email' => '',
					'entity_geo_long' => '',
					'entity_geo_lat' => '',
					'entity_picturepath' => '',
					'entity_contractpath' => '',
					'entity_pop' => '',
					'entity_pop_year' => '',
					'entity_status' => '',
					'entity_sis_code' => '',
					'entity_active' => '',
					'entity_id' => '',
					'entity_name' => '',
					'entity_type' => '',
					'entity_geozone_id' => '',
					'entity_related_entity' => '',
					'entity_bank_hq_id' => '',
					'entity_bank_id' => '',
					'entity_bank_acc' => '',
					'entity_bank_acc_fees' => '',
					'entity_contractvalidity_start' => '',
					'entity_contractvalidity_end' => '',
					'entity_pbf_group_id' => '',
					'entity_distance_tobase' => '',
					'entity_use_isolation_bonus' => '',
					'entity_use_equity_bonus' => '' 
			) );
		}
	}
	
	function delentity($entity_id) {
		if ($this->entities_mdl->delentity ( $entity_id )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'entity_delete_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'entity_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	
	function delgroup($entity_group_id) {
		if ($this->entities_mdl->delgroup ( $entity_group_id )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'entity_group_delete_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'entity_group_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	
	function delclass($entity_class_id) {
		if ($this->entities_mdl->delclass ( $entity_class_id )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'entity_class_delete_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'entity_class_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	
	function deltype($entity_type_id) {
		if ($this->entities_mdl->deltype ( $entity_type_id )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'entity_type_delete_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'entity_type_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	
	function delete_selected_acc() {
		if ($this->entities_mdl->delete_selected_entities ( $this->input->post ( 'item' ) )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'entity_type_delete_selected_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'entity_type_delete_selected_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	
	function delinfo($entity_id, $info) {
		if ($this->entities_mdl->delinfo ( $entity_id, $info )) {
			$this->pbf->set_eventlog ( $this->lang->line ( 'entity_info_delete_success' ) . $info . $this->lang->line ( 'entity_for_entity' ) . $entity_id, 1 );
		} else {
			$this->pbf->set_eventlog ( $this->lang->line ( 'entity_info_delete_error' ) . $info . $this->lang->line ( 'entity_for_entity' ) . $entity_id, 1 );
		}
		
		redirect ( $this->config->item ( 'base_url' ) . 'hfrentities/editentity/' . $entity_id );
	}
	
	function setentitystate($entity_id, $state) {
		if ($this->entities_mdl->set_entity_state ( $entity_id, $state )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'entity_state_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'entity_state_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	
	function completude($geozone_id = 0) {
		
		
		$data = $this->_get_geozone_entities_settings ( $geozone_id, 'region' );
		
		if ($geozone_id == 0) {
			$geozone_id = $data ['list'] [0] ['geozone_id'];
		}
		
		$geozone_links = "";
		$geozone_title = array (
				'#',
				$this->lang->line ( 'compl_region' ),
				$this->lang->line ( 'compl_tot_entite' ),
				$this->lang->line ( 'compl_geo' ),
				$this->lang->line ( 'compl_photo' ),
				$this->lang->line ( 'compl_population' ),
				$this->lang->line ( 'compl_statut' ),
				$this->lang->line ( 'compl_responsable' ),
				$this->lang->line ( 'compl_email' ),
				$this->lang->line ( 'compl_tel' ),
				$this->lang->line ( 'compl_banque' ),
				'%' 
		);
		$district_title = array (
				'#',
				District,
				$this->lang->line ( 'compl_tot_entite' ),
				$this->lang->line ( 'compl_geo' ),
				$this->lang->line ( 'compl_photo' ),
				$this->lang->line ( 'compl_population' ),
				$this->lang->line ( 'compl_statut' ),
				$this->lang->line ( 'compl_responsable' ),
				$this->lang->line ( 'compl_email' ),
				$this->lang->line ( 'compl_tel' ),
				$this->lang->line ( 'compl_banque' ),
				'%' 
		);
		$entities_title = array (
				'#',
				'FOSA',
				$this->lang->line ( 'entity_district' ),
				$this->lang->line ( 'compl_geo' ),
				$this->lang->line ( 'compl_photo' ),
				$this->lang->line ( 'compl_population' ),
				$this->lang->line ( 'compl_statut' ),
				$this->lang->line ( 'compl_responsable' ),
				$this->lang->line ( 'compl_email' ),
				$this->lang->line ( 'compl_tel' ),
				$this->lang->line ( 'compl_banque' ) 
		);
			
		foreach ( $data ['list'] as $k => $v ) {
			if ($geozone_id != $data ['list'] [$k] ['geozone_id']) {
				
				if (count ( $this->session->userdata ( 'usergeozones' ) ) <= 0) {
					$geozone_links .= anchor ( '/hfrentities/completude/' . $data ['list'] [$k] ['geozone_id'], $data ['list'] [$k] ['geozone_name'] ) . " &nbsp;&nbsp;| &nbsp;&nbsp;";
				} else {
					$linkDispo = 0;
					foreach ( $this->session->userdata ( 'usergeozones' ) as $sK => $sV ) {
						$parent = $this->geo_mdl->get_parent_geozone ( $sV );
						$parentId = $parent ['geozone_id'];
						if (($parentId == $data ['list'] [$k] ['geozone_id']) and $linkDispo == 0) {
							$geozone_links .= anchor ( '/hfrentities/completude/' . $data ['list'] [$k] ['geozone_id'], $data ['list'] [$k] ['geozone_name'] ) . " &nbsp;&nbsp;| &nbsp;&nbsp;";
							$linkDispo = 1;
						}
					}
				}
			} else {
				
				$filename = FCPATH . 'cside/exports/' . $data ['exports_file_name'] . '.xlsx';
				$download_link = base_url () . 'cside/exports/' . $data ['exports_file_name'] . '.xlsx';
				
				if (count ( $this->session->userdata ( 'usergeozones' ) ) <= 0) {
					$geozone_links .= "<span style='font-size:1.3em;text-decoration:underline'>" . $data ['list'] [$k] ['geozone_name'] . "</span> | ";
				} else {
					$linkDispo = 0;
					foreach ( $this->session->userdata ( 'usergeozones' ) as $sK => $sV ) {
						$parent = $this->geo_mdl->get_parent_geozone ( $sV );
						$parentId = $parent ['geozone_id'];
						if (($parentId == $data ['list'] [$k] ['geozone_id']) and $linkDispo == 0) {
							$geozone_links .= "<span style='font-size:1.3em;text-decoration:underline'>" . $data ['list'] [$k] ['geozone_name'] . "</span> | ";
							$linkDispo = 1;
						}
					}
				}
				
							
				$data ['export_link'] .= (file_exists ( $filename )) ? anchor ( '/hfrentities/export_list_fosa/' . $data ['list'] [$k] ['geozone_id'], ' Create updated FOSA list ' . '.xls ' ) : anchor ( '/hfrentities/export_list_fosa/' . $data ['list'] [$k] ['geozone_id'], ' Create FOSA list ' . '.xls' );
				
			
				$data ['export_link'] .= (file_exists ( $filename )) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp' . $this->pbf->rec_op_icon ( 'download_excel', $download_link ) : '';
			}
			if (count ( $this->session->userdata ( 'usergeozones' ) ) <= 0) {
				$cond = " ";
			} else {
				$cond = "AND ( ";
				foreach ( $this->session->userdata ( 'usergeozones' ) as $sK => $sV ) {
					$parent = $this->geo_mdl->get_parent_geozone ( $sV );
					$parentId = $parent ['geozone_id'];
					if ($sK != 0) {
						$cond .= " OR ";
					}
					$cond .= "geozone_parentid=$parentId ";
				}
				$cond .= " ) ";
			}
			$result_district = $this->_get_geozone_entities_settings ( $data ['list'] [$k] ['geozone_id'], 'district' );
			
			
			foreach ( $result_district ['list'] as $u => $w ) {
				$result_district ['list'] [$u] ['geozone_id'] = $u + 1;
			}
			$data ['list'] [$k] ['geozone_id'] = $k + $preps ['offset'] + 1;
			
			$geo_Id = $data ['list'] [$k] ['geozone_id'];
			
			$data ['district'] [$geo_Id] = $result_district ['list'];
			
			array_unshift ( $data ['district'] [$geo_Id], $district_title );
		}
		
	
		
		$data ['detail_links'] = $geozone_links;
		
		foreach ( $data ['detail'] as $k => $v ) {
			
			$data ['detail'] [$k] ['entity_name'] = anchor ( '/hfrentities/editentity/' . $data ['detail'] [$k] ['entity_id'], $data ['detail'] [$k] ['entity_name'] );
			$data ['detail'] [$k] ['entity_id'] = $k + $preps ['offset'] + 1;
			
			foreach ( $data ['detail'] [$k] as $i => $j ) {
				if ($j == 'V') {
					$data ['detail'] [$k] [$i] = "<span style='color:green; display:block; text-align:center'>V</span>";
				} elseif ($j == 'X') {
					$data ['detail'] [$k] [$i] = "<span style='color:red; display:block; text-align:center'>X</span>";
				}
			}
		}
		
		array_unshift ( $data ['list'], $geozone_title );
		
		array_unshift ( $data ['detail'], $entities_title );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'entity_title' ) . ' - ' . $this->lang->line ( 'entity_class_title' );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'entity_title' ) . ' - ' . $this->lang->line ( 'completude_title' );
		
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['tab_menus'] = implode ( '&nbsp;&nbsp;|&nbsp;&nbsp;', $this->pbf->get_entities_classes () ) . '&nbsp;&nbsp;|&nbsp;&nbsp;' . $this->pbf->get_mod_submenu ( 15 );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'dashboard_entities_settings';
		$this->load->view ( 'body', $data );
	}
	
	function _get_geozone_entities_settings($geozone_id, $type = 'region') {
		$thumb_size = $this->config->item ( 'image_thumb_size' );
		$medium_size = $this->config->item ( 'image_medium_size' );
		$big_size = $this->config->item ( 'image_big_size' );
		
		$niveau1 = $this->config->item ( 'color_pourcentage_1stlever' );
		$niveau2 = $this->config->item ( 'color_pourcentage_2ndlever' );
		$niveau3 = $this->config->item ( 'color_pourcentage_3rdlever' );
		
		if (count ( $this->session->userdata ( 'usergeozones' ) ) <= 0) {
			$cond = " ";
		} else {
			$cond = "AND ( ";
			foreach ( $this->session->userdata ( 'usergeozones' ) as $sK => $sV ) {
				$parent = $this->geo_mdl->get_parent_geozone ( $sV );
				$parentId = $parent ['geozone_id'];
				if ($sK != 0) {
					$cond .= " OR ";
				}
				if ($type == 'region') {
					$cond .= "geozone_id=$parentId ";
				} else {
					$cond .= "geozone_id=$sV ";
				}
			}
			$cond .= " ) ";
		}
		
	
		$list_of_regions = $this->entities_mdl->get_geozone_compl ( $geozone_id, $type, $cond ); // recuperation de la liste des r�gions
		
		$resultat = array (); // contient la liste repitulative des region
		$resultat_det = array (); 
		$detail = array ();
		
		$yes = "V";
		$no = "X";
		
		
		if ($geozone_id == 0) {
			$geozone_id = $list_of_regions [0] ['geozone_id'];
		}
		
		foreach ( $list_of_regions as $k => $v ) { // verification des informations pour chaque region
			
			$curr_geozone_id = $list_of_regions [$k] ['geozone_id'];
			
			if ($geozone_id == $curr_geozone_id) {
				$record_set ['exports_title'] = 'Liste FOSA Region ' . $list_of_regions [$k] ['geozone_name'];
				$record_set ['exports_file_name'] = 'liste_fosa_region_' . $list_of_regions [$k] ['geozone_name'];
			}
			
			if (count ( $this->session->userdata ( 'usergeozones' ) ) <= 0) {
				$cond = " ";
			} else {
				$cond = "AND ( ";
				foreach ( $this->session->userdata ( 'usergeozones' ) as $sK => $sV ) {
					$parent = $this->geo_mdl->get_parent_geozone ( $sV );
					$parentId = $parent ['geozone_id'];
					if ($sK != 0) {
						$cond .= " OR ";
					}
					$cond .= "geozone_id=$sV ";
				}
				$cond .= " ) ";
			}
			
			$entites = $this->entities_mdl->get_region_entities ( $curr_geozone_id, $type, $cond );
			$totalEntities = $entites ['records_num'];
			
			// Initialisation des information existante pour chaque region � 0
			$geo = 0;
			$photo = 0;
			$population = 0;
			$status = 0;
			$responsable = 0;
			$mail = 0;
			$tel = 0;
			$banque = 0;
			
			foreach ( $entites ['list'] as $i => $j ) { // verification des info pour chaque entite
				if ($curr_geozone_id == $geozone_id) 				// Si c'est la region selection, intialisation des information existant pour chaque entite � X
				{
					$geo_det = $no;
					$photo_det = $no;
					$population_det = $no;
					$status_det = $no;
					$responsable_det = $no;
					$mail_det = $no;
					$tel_det = $no;
					$banque_det = $no;
				}
				
				if (($entites ['list'] [$i] ['entity_geo_lat'] != 0) and ($entites ['list'] [$i] ['entity_geo_long'] != 0)) {
					$geo ++; // Si l'information est presente(pour le moment GEO ), on incremente sa valeur pour la region en cours
					
					if ($curr_geozone_id == $geozone_id)
						$geo_det = $yes; // Si l'information est presente et que c'est la region selectionn�, on on marque que l'info est presente pour l'entite en question
				}
				
				if (($entites ['list'] [$i] ['entity_picturepath'] != NULL)) {
					$pictures = scandir ( FCPATH . 'cside/images/portal/' );
					$NomImage = $entites ['list'] [$i] ['entity_picturepath'] . '_big.jpg';
					if (in_array ( $NomImage, $pictures )) {
						$photo ++;
						if ($curr_geozone_id == $geozone_id)
							$photo_det = $yes;
					}
				}
				
				if (($entites ['list'] [$i] ['entity_pop'] != NULL)) {
					$population ++;
					if ($curr_geozone_id == $geozone_id)
						$population_det = $yes;
				}
				
				if (($entites ['list'] [$i] ['entity_status'])) {
					$status ++;
					if ($curr_geozone_id == $geozone_id)
						$status_det = $yes;
				}
				if (($entites ['list'] [$i] ['entity_responsible_name'] != NULL)) {
					$responsable ++;
					if ($curr_geozone_id == $geozone_id)
						$responsable_det = $yes;
				}
				if (($entites ['list'] [$i] ['entity_responsible_email'] != NULL)) {
					$mail ++;
					if ($curr_geozone_id == $geozone_id)
						$mail_det = $yes;
				}
				if (($entites ['list'] [$i] ['entity_phone_number'] != NULL)) {
					$tel ++;
					if ($curr_geozone_id == $geozone_id)
						$tel_det = $yes;
				}
				
				if (($entites ['list'] [$i] ['entity_bank_acc']) and ($entites ['list'] [$i] ['entity_bank_id'])) {
					$banque ++;
					if ($curr_geozone_id == $geozone_id)
						$banque_det = $yes;
				}
				
				if ($curr_geozone_id == $geozone_id) {
					// ajout des valeurs dans le tableau des information des entit�s de la region selectionn�
					$temp_det = array (
							'entity_id' => $entites ['list'] [$i] ['entity_id'],
							'entity_name' => $entites ['list'] [$i] ['entity_name'],
							'district_name' => $entites ['list'] [$i] ['geozone_name'],
							'geo' => $geo_det,
							'photo' => $photo_det,
							'population' => $population_det,
							'status' => $status_det,
							'responsable' => $responsable_det,
							'mail' => $mail_det,
							'tel' => $tel_det,
							'banque' => $banque_det 
					);
					array_push ( $resultat_det, $temp_det );
				}
			}
			
			// ajout des valeurs dans le tableau des information globales des region
			$pourcentage = number_format ( (($banque + $tel + $mail + $responsable + $status + $population + $photo + $geo) / ($totalEntities * 8) * 100), 0, '.', ' ' );
			
			if ($pourcentage >= $niveau1) {
				$couleur = 'green';
			} elseif ($pourcentage < $niveau1 and $pourcentage >= $niveau2) {
				$couleur = 'turquoise';
			} elseif ($pourcentage < $niveau2 and $pourcentage >= $niveau3) {
				$couleur = 'orange';
			} else {
				
				$couleur = 'red';
			}
			$pourcentage = "<span style='color:$couleur'>" . $pourcentage . "</span>";
			$temp = array (
					'geozone_id' => $curr_geozone_id,
					'geozone_name' => $list_of_regions [$k] ['geozone_name'],
					'tot_entities' => $totalEntities,
					'geo' => $geo,
					'photo' => $photo,
					'population' => $population,
					'status' => $status,
					'responsable' => $responsable,
					'mail' => $mail,
					'tel' => $tel,
					'banque' => $banque,
					'pourcentage' => $pourcentage,
					'parentId' => $list_of_regions [$k] ['geozone_parentid'] 
			)
			;
			
			array_push ( $resultat, $temp );
		}
		
		$record_set ['list'] = $resultat;
		$record_set ['detail'] = $resultat_det;
		
		return $record_set;
	}
	
	function _verifimage($NomImage) 	// verifier si une image est pr�sente dans le fichier .. cside/images/portal/
	{
		$pictures = scandir ( FCPATH . 'cside/images/portal/' );
		$NomImage = $NomImage . '_big.jpg';
		if (in_array ( $NomImage, $pictures )) {
		}
	
	}
	
	function export_list_fosa($geozone_id) {
		$this->load->library ( "phpexcel" );
		$this->load->library ( "PHPExcel/IOFactory" );
		
		$data = $this->_get_geozone_entities_settings ( $geozone_id );
		$detail_data = $data ['detail'];
		
		
		$data_wb = new PHPExcel ();
		
		$data_wb->getProperties ()->setCreator ( "Portail FBR" )->setLastModifiedBy ( "Portail FBR" )->setTitle ( substr ( $data ['exports_title'], 0, 30 ) )->setSubject ( $data ['exports_title'] )->setDescription ( $data ['exports_title'] )->setKeywords ( $data ['exports_title'] )->setCategory ( $data ['exports_title'] );
		
		$data_wb_sheet = $data_wb->createSheet ();
		
		$data_wb_sheet->getPageSetup ()->setOrientation ( PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE )->setPaperSize ( PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4 )->setFitToPage ( true );
		
		$data_wb_sheet->getPageMargins ()->setTop ( 0.40 )->setRight ( 0.40 )->setLeft ( 0.40 )->setBottom ( 0.40 );
		
		$data_wb_sheet->setTitle ( substr ( $data ['exports_title'], 0, 30 ) );
		
		$columns_title = array (
				'FOSA',
				'District',
				'Geo',
				'Photo',
				'Population',
				'Statut',
				'Responsable',
				'Email',
				'Tel',
				'Banque' 
		);
		
		
		$NextRow = 1;
		foreach ( $columns_title as $column_key => $column_val ) {
			$data_wb_sheet->setCellValueByColumnAndRow ( $column_key, $NextRow, $column_val );
		}
		
		$NextRow ++;
		
		foreach ( $detail_data as $row_key => $row_value ) {
			
			unset ( $detail_data [$row_key] ['entity_id'] );
			
			$i = 0;
			foreach ( $detail_data [$row_key] as $k => $v ) {
							
				$data_wb_sheet->setCellValueByColumnAndRow ( $i, $NextRow, $v );
				$i ++;
			}
			
			$NextRow ++;
		}
		
		$lastColumn = $data_wb_sheet->getHighestColumn ();
		
		$data_wb_sheet->getStyle ( 'A1:' . $lastColumn . '1' )->getFont ()->setBold ( true );
		$data_wb_sheet->getStyle ( 'A1:' . $lastColumn . $NextRow )->getBorders ()->getAllBorders ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		
		$data_wb->getSheet ( 0 )->setSheetState ( PHPExcel_Worksheet::SHEETSTATE_VERYHIDDEN );
		$data_wb->setActiveSheetIndex ( 1 );
		
		$objWriter = IOFactory::createWriter ( $data_wb, 'Excel2007' );
		
		$file_name_is = FCPATH . 'cside/exports/' . $data ['exports_file_name'] . '.xlsx';
		
		$objWriter->save ( $file_name_is );
		
		redirect ( "hfrentities/completude/" . $geozone_id );
	}
	function resize_existing_image($op = 0) {
		$thumb_size = $this->config->item ( 'image_thumb_size' );
		$medium_size = $this->config->item ( 'image_medium_size' );
		$big_size = $this->config->item ( 'image_big_size' );
		
		
		
		echo "<h1>Redimentionnement des images</h1>";
		
		if ($op == 0) {
			$titre = "Vous etes sur le point de transformer les images suivant:";
			
			echo "<h2>$titre</h2>";
			
			$fichier_source = FCPATH . 'cside/images/portal/';
			for($j = 1; $j <= 2; $j ++) {
				$pictures = scandir ( $fichier_source );
				
				$pictures = array_diff ( $pictures, array (
						'.',
						'..',
						'big',
						'medium',
						'thumbs',
						'index.html',
						'news' 
				) );
				
				echo "<h4>Dans le dossier $fichier_source :</h4>";
				
				
				foreach ( $pictures as $k => $v ) {
					
					$nom_enregistre = $v;
					if (strstr ( $v, '_big.jpg' )) { 
						
						$nom_enregistre = str_replace ( '_big.jpg', '', $v );
					}
					
					
					if ($j == 1) 					
					{
						$req = "select entity_id,entity_name,entity_picturepath from  pbf_entities  where entity_picturepath like'$nom_enregistre'";
					} else {
						$req = "select content_id,content_title,content_link from  pbf_content_news where content_link like'$nom_enregistre'";
					}
					$num = $this->db->query ( $req )->num_rows ();
					
					if ($num > 0) {
						echo "<p>$v</p>";
					}
				}
				
				$fichier_source = FCPATH . 'cside/contents/images/';
			}
			echo "<h1><a href='" . base_url () . "hfrentities/resize_existing_image/11' target='_blank'>Demarrer le traitement</a></h1>";
		}
		
		if ($op == 11) {
			// Repertoire source pour les images des entites
			$fichier_source = FCPATH . 'cside/images/portal/';
			
			for($j = 1; $j <= 2; $j ++) {
				$i = 0;
				$pictures = scandir ( $fichier_source );
				
				$pictures = array_diff ( $pictures, array (
						'.',
						'..',
						'big',
						'medium',
						'thumbs',
						'index.html',
						'news' 
				) );
				
				
				foreach ( $pictures as $k => $v ) {
					
					
					$nom_enregistre = $v;
					if (strstr ( $v, '_big.jpg' )) { // cette verification est necessaire si on a d�ja fait un encodage avant
						
						$nom_enregistre = str_replace ( '_big.jpg', '', $v );
					}
					if ($j == 1) 					// pour les entit�s
					
					{
						$req = "select entity_id,entity_name,entity_picturepath from  pbf_entities  where entity_picturepath like'$nom_enregistre'";
					} else {
						$req = "select content_id,content_title,content_link from  pbf_content_news where content_link like'$nom_enregistre'";
					}
					$num = $this->db->query ( $req )->num_rows ();
					
					if ($num > 0) {
						if ($j == 1) {
							$content = $this->db->query ( $req )->row_array ();
							$id_Image = $content ['entity_id'];
							$title_image = $content ['entity_name'];
						} else {
							
							$content = $this->db->query ( $req )->row_array ();
							$id_Image = $content ['content_id'];
							$title_image = $content ['content_title'];
						}
						// Sauvegarder l'image
						
						$image_source = $fichier_source . $v;
						
						$picture_name = $id_Image . '_' . $title_image;
						$picture_name = $this->pbf->Slug ( $picture_name );
						$config_thumb = array (
								'source_image' => $image_source,
								'new_image' => $fichier_source . $picture_name . "_thumb.jpg",
								'maintain_ration' => true,
								'width' => $thumb_size,
								'height' => $thumb_size 
						);
						
						$config_medium = array (
								'source_image' => $image_source,
								'new_image' => $fichier_source . $picture_name . "_med.jpg",
								'maintain_ration' => true,
								'width' => $medium_size,
								'height' => $medium_size 
						);
						
						$config_big = array (
								'source_image' => $image_source,
								'new_image' => $fichier_source . $picture_name . "_big.jpg",
								'maintain_ration' => true,
								'width' => $big_size,
								'height' => $big_size 
						);
						
						$this->load->library ( 'image_lib' );
						
						$this->image_lib->initialize ( $config_thumb );
						
						if (! $this->image_lib->resize ()) {
							echo "<p>" . $image_source . "</p>";
							die ( $this->image_lib->display_errors () );
						} else {
							$i ++;
						}
						
						$this->image_lib->clear ();
						
						$this->image_lib->initialize ( $config_medium );
						
						if (! $this->image_lib->resize ()) {
							echo "<p>" . $image_source . "</p>";
							die ( $this->image_lib->display_errors () );
						} else {
							$i ++;
						}
						
						$this->image_lib->clear ();
						
						$this->image_lib->initialize ( $config_big );
						
						if (! $this->image_lib->resize ()) {
							echo "<p>" . $image_source . "</p>";
							die ( $this->image_lib->display_errors () );
						} else {
							$i ++;
						}
						
						// Enregitrer le nom de l'image dans la BD
						
						if ($j == 1) {
							$req = "update pbf_entities set entity_picturepath='$picture_name' where entity_id=$id_Image";
						} else {
							$req = "update pbf_content_news set content_link='$picture_name' where content_id=$id_Image";
						}
						
						$this->db->query ( $req );
						
						
						
						// renommer l'ancienne image
						if (strstr ( $image_source, '_big.jpg' )) { // cette verification est necessaire si on a d�ja fait un encodage avant
							
							copy ( $image_source, $fichier_source . $picture_name . "_or.jpg" );
						} else {
							rename ( $image_source, $fichier_source . $picture_name . "_or.jpg" );
						}
					}
				}
				echo "<h4> Dans $fichier_source : $i images g�ner�s</h4>";
				
				$fichier_source = FCPATH . 'cside/contents/images/';
			}
			
			echo "<p>$i images trait�es pour $fichier_source</p>";
			// Repertoire source pour les images du CMS
			$fichier_source = FCPATH . 'cside/contents/images/';
			
			echo "<a href='" . base_url () . "hfrentities'> Retour</a>";
		}
	}
	
	function export_to_odk() {
		$file_name_is = FCPATH . 'cside/exports/' . str_replace ( ' ', '_', 'export_to_odk' ) . '.xls';
		$excel = new Spreadsheet_Excel_Writer ( $file_name_is );
		$sheet = & $excel->addWorksheet ( "survey" );
		$sheet->setPaper ( 9 ); // Définit une page A4
		$sheet->setLandscape (); // Définit une orientation Paysage.
		$sheet->setColumn ( 0, 5, 20 );
		$sheet->write ( 0, 0, mb_convert_encoding ( "type", "windows-1252", "UTF-8" ) );
		$sheet->write ( 0, 1, mb_convert_encoding ( "name", "windows-1252", "UTF-8" ) );
		$sheet->write ( 0, 2, mb_convert_encoding ( "label", "windows-1252", "UTF-8" ) );
		$sheet->write ( 0, 3, mb_convert_encoding ( "choice_filter", "windows-1252", "UTF-8" ) );
		$geozones_active = 1;
		$sql = "SELECT * FROM pbf_geozones WHERE geozone_parentid IS NULL AND geozone_active=" . $geozones_active . "";
		$geozones_region = $this->db->query ( $sql )->result_array ();
		if (! empty ( $geozones_region )) {
			$sheet->write ( 1, 0, mb_convert_encoding ( "select_one region", "windows-1252", "UTF-8" ) );
			$sheet->write ( 1, 1, mb_convert_encoding ( "region", "windows-1252", "UTF-8" ) );
			$sheet->write ( 1, 2, mb_convert_encoding ( "region", "windows-1252", "UTF-8" ) );
			$column_value = 'region=${region}';
			$sheet->write ( 2, 0, mb_convert_encoding ( "select_one district", "windows-1252", "UTF-8" ) );
			$sheet->write ( 2, 1, mb_convert_encoding ( "district", "windows-1252", "UTF-8" ) );
			$sheet->write ( 2, 2, mb_convert_encoding ( "district", "windows-1252", "UTF-8" ) );
			$sheet->write ( 2, 3, mb_convert_encoding ( $column_value, "windows-1252", "UTF-8" ) );
			$column_value = 'region=${district}';
			// $sheet->write(1,3,mb_convert_encoding("","windows-1252","UTF-8"));
			$sheet->write ( 3, 0, mb_convert_encoding ( "select_one entities", "windows-1252", "UTF-8" ) );
			$sheet->write ( 3, 1, mb_convert_encoding ( "entity", "windows-1252", "UTF-8" ) );
			$sheet->write ( 3, 2, mb_convert_encoding ( "entity", "windows-1252", "UTF-8" ) );
			$sheet->write ( 3, 3, mb_convert_encoding ( $column_value, "windows-1252", "UTF-8" ) );
			$sheet->write ( 4, 0, mb_convert_encoding ( "geopoint", "windows-1252", "UTF-8" ) );
			$sheet->write ( 4, 1, mb_convert_encoding ( "location", "windows-1252", "UTF-8" ) );
			$sheet->write ( 4, 2, mb_convert_encoding ( "location", "windows-1252", "UTF-8" ) );
			// $sheet->write(3,3,mb_convert_encoding("choice_filter","windows-1252","UTF-8"));
			$sheet->write ( 5, 0, mb_convert_encoding ( "image", "windows-1252", "UTF-8" ) );
			$sheet->write ( 5, 1, mb_convert_encoding ( "picture", "windows-1252", "UTF-8" ) );
			$sheet->write ( 5, 2, mb_convert_encoding ( "picture of the entity", "windows-1252", "UTF-8" ) );
		} else {
			$sheet->write ( 1, 0, mb_convert_encoding ( "select_one district", "windows-1252", "UTF-8" ) );
			$sheet->write ( 1, 1, mb_convert_encoding ( "district", "windows-1252", "UTF-8" ) );
			$sheet->write ( 1, 2, mb_convert_encoding ( "district", "windows-1252", "UTF-8" ) );
			// $sheet->write(1,3,mb_convert_encoding("","windows-1252","UTF-8"));
			$sheet->write ( 2, 0, mb_convert_encoding ( "select_one entities", "windows-1252", "UTF-8" ) );
			$sheet->write ( 2, 1, mb_convert_encoding ( "entity", "windows-1252", "UTF-8" ) );
			$sheet->write ( 2, 2, mb_convert_encoding ( "entity", "windows-1252", "UTF-8" ) );
			$column_value = 'region=${region}';
			$sheet->write ( 2, 3, mb_convert_encoding ( $column_value, "windows-1252", "UTF-8" ) );
			$sheet->write ( 3, 0, mb_convert_encoding ( "geopoint", "windows-1252", "UTF-8" ) );
			$sheet->write ( 3, 1, mb_convert_encoding ( "location", "windows-1252", "UTF-8" ) );
			$sheet->write ( 3, 2, mb_convert_encoding ( "location", "windows-1252", "UTF-8" ) );
			// $sheet->write(3,3,mb_convert_encoding("choice_filter","windows-1252","UTF-8"));
			$sheet->write ( 4, 0, mb_convert_encoding ( "image", "windows-1252", "UTF-8" ) );
			$sheet->write ( 4, 1, mb_convert_encoding ( "picture", "windows-1252", "UTF-8" ) );
			$sheet->write ( 4, 2, mb_convert_encoding ( "picture of the entity", "windows-1252", "UTF-8" ) );
		}
		// $excel = new Spreadsheet_Excel_Writer($file_name_is);
		$sheet1 = & $excel->addWorksheet ( "choices" );
		$sheet1->setPaper ( 9 ); // Définit une page A4
		$sheet1->setLandscape (); // Définit une orientation Paysage.
		$sheet1->setColumn ( 0, 5, 20 );
		$sheet1->write ( 0, 0, mb_convert_encoding ( "list_name", "windows-1252", "UTF-8" ) );
		$sheet1->write ( 0, 1, mb_convert_encoding ( "name", "windows-1252", "UTF-8" ) );
		$sheet1->write ( 0, 2, mb_convert_encoding ( "label", "windows-1252", "UTF-8" ) );
		$sheet1->write ( 0, 3, mb_convert_encoding ( "region", "windows-1252", "UTF-8" ) );
		$geozone_active = 1;
		$sql = "SELECT pbf_geozones.geozone_id,pbf_geozones.geozone_name,pbf_geozones.geozone_parentid,pbf_geozones.geozone_active FROM pbf_geozones WHERE pbf_geozones.geozone_parentid IS NOT NULL AND pbf_geozones.geozone_active=" . $geozone_active . "";
		$geozones = $this->db->query ( $sql )->result_array ();
		$NextRow = 1;
		if (! empty ( $geozones_region )) {
			foreach ( $geozones_region as $sheet_row ) {
				
				$keys = array_keys ( $sheet_row );
				foreach ( $keys as $column_key => $column_val ) {
					if ($column_key == 0) {
						$sheet1->write ( $NextRow, $column_key, mb_convert_encoding ( "region", "windows-1252", "UTF-8" ) );
					} else {
						if ($column_key == 1) {
							$column_val = "geozone_id";
							$sheet1->write ( $NextRow, $column_key, mb_convert_encoding ( $sheet_row [$column_val], "windows-1252", "UTF-8" ) );
						} else {
							if ($column_key == 2) {
								$column_val = "geozone_name";
								$sheet1->write ( $NextRow, $column_key, mb_convert_encoding ( $sheet_row [$column_val], "windows-1252", "UTF-8" ) );
							}
						}
					}
				}
				
				$NextRow ++;
			}
		}
		foreach ( $geozones as $sheet_row ) {
			
			$keys = array_keys ( $sheet_row );
			
			foreach ( $keys as $column_key => $column_val ) {
				if ($column_key == 0) {
					$sheet1->write ( $NextRow, $column_key, mb_convert_encoding ( "district", "windows-1252", "UTF-8" ) );
				} else {
					if ($column_key == 1) {
						$column_val = "geozone_id";
						$sheet1->write ( $NextRow, $column_key, mb_convert_encoding ( $sheet_row [$column_val], "windows-1252", "UTF-8" ) );
					} else {
						if ($column_key == 2) {
							$column_val = "geozone_name";
							$sheet1->write ( $NextRow, $column_key, mb_convert_encoding ( $sheet_row [$column_val], "windows-1252", "UTF-8" ) );
						} else {
							if ($column_key == 3) {
								$column_val = "geozone_parentid";
								$sheet1->write ( $NextRow, $column_key, mb_convert_encoding ( $sheet_row [$column_val], "windows-1252", "UTF-8" ) );
							}
						}
					}
				}
			}
			
			$NextRow ++;
		}
		$entity_active = 1;
		$sql = "SELECT pbf_entities.entity_id,pbf_entities.entity_name,pbf_entities.entity_geozone_id,pbf_entities.entity_active,pbf_geozones.geozone_id FROM pbf_entities,pbf_geozones WHERE pbf_entities.entity_geozone_id=pbf_geozones.geozone_id AND pbf_entities.entity_active=" . $entity_active . "";
		$entities = $this->db->query ( $sql )->result_array ();
		foreach ( $entities as $sheet_row ) {
			
			$keys = array_keys ( $sheet_row );
			
			foreach ( $keys as $column_key => $column_val ) {
				if ($column_key == 0) {
					$sheet1->write ( $NextRow, $column_key, mb_convert_encoding ( "entities", "windows-1252", "UTF-8" ) );
				} else {
					if ($column_key == 1) {
						$column_val = "entity_id";
						$sheet1->write ( $NextRow, $column_key, mb_convert_encoding ( $sheet_row [$column_val], "windows-1252", "UTF-8" ) );
					} else {
						if ($column_key == 2) {
							$column_val = "entity_name";
							$sheet1->write ( $NextRow, $column_key, mb_convert_encoding ( $sheet_row [$column_val], "windows-1252", "UTF-8" ) );
						} else {
							if ($column_key == 3) {
								$column_val = "entity_geozone_id";
								$sheet1->write ( $NextRow, $column_key, mb_convert_encoding ( $sheet_row [$column_val], "windows-1252", "UTF-8" ) );
							}
						}
					}
				}
			}
			
			$NextRow ++;
		}
		$sheet2 = & $excel->addWorksheet ( "settings" );
		$sheet2->setPaper ( 9 ); // Définit une page A4
		$sheet2->setLandscape (); // Définit une orientation Paysage.
		$sheet2->setColumn ( 0, 5, 20 );
		$sheet2->write ( 0, 0, mb_convert_encoding ( "form_title", "windows-1252", "UTF-8" ) );
		$sheet2->write ( 0, 1, mb_convert_encoding ( "form_id", "windows-1252", "UTF-8" ) );
		$sheet2->write ( 1, 0, mb_convert_encoding ( "config test", "windows-1252", "UTF-8" ) );
		$sheet2->write ( 1, 1, mb_convert_encoding ( "config_test", "windows-1252", "UTF-8" ) );
		
		$excel->close ();
		$data = file_get_contents ( $file_name_is );
		force_download ( "export_to_odk.xls", $data );
		redirect ( "hfrentities/hfrentity" );
	}
	
	function import_from_odk() {
		$data ['usertask_name'] = $this->pbf->get_controllers ();
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'entity_upload_title' );
		$data ['page'] = 'upload_odk_frm';
		
		$this->load->view ( 'body', $data );
	}
	
	function recursive_dir($dir) {
		foreach ( scandir ( $dir ) as $file ) {
			if ('.' === $file || '..' === $file)
				continue;
			if (is_dir ( $dir . '/' . $file ))
				recursive_dir ( $dir . '/' . $file );
			else
				unlink ( $dir . '/' . $file );
		}
		rmdir ( $dir );
	}
	
	function upload_zipped_directory() {
		$this->load->library ( 'image_lib' );
		$this->load->library ( 'PHPExcel' );
		$this->load->library ( 'PHPExcel/IOFactory' );
		$entities_pictures;
		$entities_geo;
		if ($_FILES ["upload_odk"]) {
			$filename = $_FILES ["upload_odk"] ["name"];
			$source = $_FILES ["upload_odk"] ["tmp_name"];
			$type = $_FILES ["upload_odk"] ["type"];
			$name = explode ( ".", $filename );
			$accepted_types = array (
					'application/zip',
					'application/x-zip-compressed',
					'multipart/x-zip',
					'application/x-compressed' 
			);
			foreach ( $accepted_types as $mime_type ) {
				if ($mime_type == $type) {
					$okay = true;
					break;
				}
			}
			$continue = strtolower ( $name [1] ) == 'zip' ? true : false;
			if (! $continue) {
				$myMsg = "Le dossier que vous uploader n'est pas zippé";
			}
			
			/* PHP current path */
			$path = $source . '/';
			$filenoext = basename ( $filename, '.zip' );
			$filenoext = basename ( $filenoext, '.ZIP' );
			$myDir = FCPATH . 'cside/images/portal/'; // target directory
			$myFile = FCPATH . 'cside/images/portal/' . $filename; // target zip file
			if (is_dir ( $myDir ))
				$this->recursive_dir ( $myFile );
			mkdir ( $myDir, 0777 );
			if (move_uploaded_file ( $source, $myFile )) {
				$zip = new ZipArchive ();
				$x = $zip->open ( $myFile ); // open the zip file to extract
				if ($x === true) {
					$zip->extractTo ( $myDir ); // place in the directory with same name
					$zip->close ();
				}
				$myMsg = "Le dossier est uploader et dézippé";
			} else {
				$myMsg = "Il ya eu un problème lors de l'upload";
			}
			$nom_dossier = explode ( '.', $filename );
			$path_dir = FCPATH . 'cside/images/portal/' . $nom_dossier [0];
			$result_dir = scandir ( $path_dir );
			foreach ( $result_dir as $result_dir_val ) {
				
				if ($result_dir_val === '.' or $result_dir_val === '..') {
					continue;
				} else {
					if (is_dir ( $path_dir . '/' . $result_dir_val )) {
						continue;
					} else {
						$result_dir_recup = explode ( '.', $result_dir_val );
						if ($result_dir_recup [1] == 'xlsx' or $result_dir_recup [1] == 'xls') {
							$inputFileType = IOFactory::identify ( $path_dir . '/' . $result_dir_val );
							$inputFileName = $path_dir . '/' . $result_dir_val;
							$objReader = IOFactory::createReader ( $inputFileType );
							$objReader->setReadDataOnly ( true );
							$objPHPExcelReader = $objReader->load ( $inputFileName );
							$objWriter = IOFactory::createWriter ( $objPHPExcelReader, 'CSV' );
							$objWriter->save ( $path_dir . '/' . $result_dir_recup [0] . '.csv' );
							unlink ( $path_dir . '/' . $result_dir_val );
							if (($handle = fopen ( $path_dir . '/' . $result_dir_recup [0] . '.csv', 'r' )) !== FALSE) {
								$i = 0;
								while ( ($data = fgetcsv ( $handle, 1000, "," )) != FALSE ) {
									if ($i > 0) {
										$entity_picture = "";
										$sql = "SELECT * FROM pbf_entities WHERE pbf_entities.entity_id=" . $data [1] . " AND pbf_entities.entity_picturepath='" . $entity_picture . "'";
										$entity = $this->db->query ( $sql )->result_array ();
										if (! empty ( $entity )) {
											$thumb_size = $this->config->item ( 'image_thumb_size' );
											$medium_size = $this->config->item ( 'image_medium_size' );
											$big_size = $this->config->item ( 'image_big_size' );
											foreach ( $entity as $key => $value ) {
												$entity_id = $value ['entity_id'];
												$entity_name = $value ['entity_name'];
											}
											$entity_name_recup = $entity_name;
											$entities_pictures = $entities_pictures . '-' . $entity_name_recup;
											$picture_name = strtolower ( $entity_id . '_' . $entity_name );
											$picture_name = $this->pbf->Slug ( $picture_name );
											$resu_scan = scandir ( $path_dir );
											foreach ( $resu_scan as $resu_scan_val ) {
												if ($resu_scan_val === '.' or $resu_scan_val === '..') {
													continue;
												} else {
													if (is_dir ( $path_dir . '/' . $resu_scan_val )) {
														continue;
													} else {
														$resu_scan_recup = explode ( '.', $resu_scan_val );
														if ($resu_scan_recup [1] != 'csv' && $resu_scan_val == $data [7]) {
															$config_thumb = array (
																	'source_image' => $path_dir . '/' . $resu_scan_val,
																	'new_image' => $myDir . $picture_name . "_thumb.jpg",
																	'maintain_ration' => true,
																	'width' => $thumb_size,
																	'height' => $thumb_size 
															);
															
															$config_medium = array (
																	'source_image' => $path_dir . '/' . $resu_scan_val,
																	'new_image' => $myDir . $picture_name . "_med.jpg",
																	'maintain_ration' => true,
																	'width' => $medium_size,
																	'height' => $medium_size 
															);
															
															$config_big = array (
																	'source_image' => $path_dir . '/' . $resu_scan_val,
																	'new_image' => $myDir . $picture_name . "_big.jpg",
																	'maintain_ration' => true,
																	'width' => $big_size,
																	'height' => $big_size 
															);
															$this->image_lib->initialize ( $config_thumb );
															$this->image_lib->resize ();
															$this->image_lib->clear ();
															$this->image_lib->initialize ( $config_medium );
															$this->image_lib->resize ();
															$this->image_lib->clear ();
															$this->image_lib->initialize ( $config_big );
															$this->image_lib->resize ();
															$this->image_lib->clear ();
															$sql = "UPDATE pbf_entities SET entity_picturepath='" . $picture_name . "' WHERE entity_id=" . $entity_id . "";
															$this->db->query ( $sql );
														}
													}
												}
											}
										}
										$latitude = 0.00000;
										$longitude = 0.00000;
										$sql = "SELECT * FROM pbf_entities WHERE pbf_entities.entity_id=" . $data [1] . " AND pbf_entities.entity_geo_long=" . $longitude . " AND pbf_entities.entity_geo_lat=" . $latitude . "";
										$entities = $this->db->query ( $sql )->result_array ();
										if (! empty ( $entities )) {
											$entity_id = intval ( $data [1] );
											$latitude = floatval ( str_replace ( "-", "", $data [3] ) );
											$longitude = floatval ( $data [4] );
											foreach ( $entities as $key => $value ) {
												
												$entity_nom = $value ['entity_name'];
											}
											$entity_name_recup = $entity_nom;
											$entities_geo = $entities_geo . '-' . $entity_name_recup;
											$sql = "UPDATE pbf_entities SET entity_geo_long=" . $longitude . ",entity_geo_lat=" . $latitude . " WHERE entity_id=" . $entity_id . "";
											$this->db->query ( $sql );
										}
									}
									$i ++;
								}
							}
							fclose ( $handle );
						}
					}
				}
			}
		}
		
		$data ['usertask_name'] = $this->pbf->get_controllers ();
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'entity_upload_title' );
		$data ['page'] = 'upload_odk_frm';
		$data ['entities_picture'] = $entities_pictures;
		$data ['entities_geo'] = $entities_geo;
		$this->delete_directory ( $path_dir );
		chmod ( $path_dir . '.zip', 0777 );
		unlink ( $path_dir . '.zip' );
		$this->load->view ( 'body', $data );
	}
	
	function delete_directory($dirname) {
		if (is_dir ( $dirname ))
			$dir_handle = opendir ( $dirname );
		if (! $dir_handle)
			return false;
		while ( $file = readdir ( $dir_handle ) ) {
			if ($file != "." && $file != "..") {
				if (! is_dir ( $dirname . "/" . $file ))
					unlink ( $dirname . "/" . $file );
				else
					delete_directory ( $dirname . '/' . $file );
			}
		}
		closedir ( $dir_handle );
		rmdir ( $dirname );
	}
	

	function test_update_data_entity($entity_id){
		$test_update=TRUE;
		$test_update_data_file=$this->entities_mdl->test_update_data_entity($entity_id);
		
		if ($test_update_data_file['update_flag']==0){
			$test_update=FALSE;
		}
	    return $test_update;
	}
	
	function get_entities_to_update(){
		return $this->entities_mdl->get_entities_to_update();	
	}
	
}