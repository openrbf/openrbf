<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Fees extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'fees_mdl' );
		$this->lang->load ( 'fees', $this->config->item ( 'language' ) );
	}
	function index() {
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->fees_mdl->get_fees_lines ( $preps ['offset'], $preps ['terms'] );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['filetype_name'] = anchor ( '/fees/copytarif/' . $data ['list'] [$k] ['indicatortarif_id'], $data ['list'] [$k] ['filetype_name'] );
			$data ['list'] [$k] ['indicatortarif_month'] = $this->lang->line ( 'app_month_' . $data ['list'] [$k] ['indicatortarif_month'] );
			$data ['list'] [$k] ['indicatortarif_quarter'] = $this->lang->line ( 'app_quarter_' . $data ['list'] [$k] ['indicatortarif_quarter'] );
			$data ['list'] [$k] ['copy'] = $this->pbf->rec_op_icon ( 'copy', '/fees/copytarif/' . $data ['list'] [$k] ['indicatortarif_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/fees/delete/' . $data ['list'] [$k] ['indicatortarif_id'] . '/' . $data ['list'] [$k] ['indicatortarif_month'] . '' . $data ['list'] [$k] ['indicatortarif_quarter'] . '' . $data ['list'] [$k] ['indicatortarif_year'] );
			$data ['list'] [$k] ['indicatortarif_id'] = $k + $preps ['offset'] + 1;
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'fees_for' ),
				$this->lang->line ( 'fees_region' ),
				$this->lang->line ( 'fees_month' ),
				$this->lang->line ( 'fees_quarter' ),
				$this->lang->line ( 'fees_year' ),
				'',
				'' 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'app_submenu_settings_indicator_fees' ) . ' [' . $data ['records_num'] . ' ' . $this->lang->line ( 'fees_fees_set' ) . ']';
		$data ['mod_title'] ['/fees/add'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['rec_filters'] = $this->pbf->get_filters ( array (
				'indicator_filetype_id',
				'datafile_month' 
		) );
		
		$data ['mngt_submenu'] = array (
				'indicators/' => $this->lang->line ( 'app_submenu_settings_files_indicators' ),
				'fees/' => $this->lang->line ( 'app_submenu_settings_indicator_fees' ) 
		);
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	function add() {
		$step = $this->input->post ( 'step' );
		
		$step = empty ( $step ) ? 1 : $step;
		
		switch ($step) {
			
			case '1' :
				
				$data ['entity_class'] = $this->pbf->get_entity_classes ();
				$data ['entity_type'] = $this->pbf->get_entity_types ();
				$data ['entity_group'] = array (
						'' => $this->lang->line ( 'app_form_dropdown_select' ) 
				) + $this->pbf->get_entity_groups ();
				$data ['entity_status'] = array (
						'' => $this->lang->line ( 'app_form_dropdown_select' ) 
				) + $this->pbf->get_lookups ( 'entity_status' );
				$data ['step'] = 1;
				
				break;
			
			case '2' :
				
				$entity_class_id = $this->input->post ( 'entity_class_id' );
				
				$data ['entity_class_id'] = $this->input->post ( 'entity_class_id' );
				$data ['entity_type_id'] = $this->input->post ( 'entity_type_id' );
				$data ['entity_pbf_group_id'] = $this->input->post ( 'entity_pbf_group_id' );
				$data ['entity_status'] = $this->input->post ( 'entity_status' );
				
				if (empty ( $entity_class_id )) {
					$data ['entity_class'] = $this->pbf->get_entity_classes ();
					$data ['entity_type'] = $this->pbf->get_entity_types ();
					$data ['entity_group'] = array (
							'' => $this->lang->line ( 'app_form_dropdown_select' ) 
					) + $this->pbf->get_entity_groups ();
					$data ['entity_status'] = array (
							'' => $this->lang->line ( 'app_form_dropdown_select' ) 
					) + $this->pbf->get_lookups ( 'entity_status' );
					$data ['step'] = 1;
					$step = 1;
				} else {
					
					$data ['entities'] = $this->pbf->get_entities_data_entry ( $entity_class_id );
					$data ['filetypes'] = $this->pbf->get_filetypes ( $entity_class_id, $data ['entity_type_id'] );
					
					$data ['years'] = $this->pbf->get_years_list ( 1 );
					$data ['periodics'] = $this->pbf->get_datafile_periodics ( $entity_class_id );
					$data ['step'] = 2;
				}
				break;
			
			case '3' :
				
				$this->load->model ( 'entities_mdl' );
				$this->load->model ( 'entities_mdl' );
				$this->load->model ( 'geo_mdl' );
				$this->load->model ( 'files_mdl' );
				
				$filetype_id = $this->input->post ( 'filetype_id' );
				
				$data ['entity_class_id'] = $this->input->post ( 'entity_class_id' );
				$data ['entity_type_id'] = $this->input->post ( 'entity_type_id' );
				$data ['entity_pbf_group_id'] = $this->input->post ( 'entity_pbf_group_id' );
				$data ['entity_status'] = $this->input->post ( 'entity_status' );
				
				$data ['level_0'] = $this->input->post ( 'level_0' );
				$data ['entity_geozone_id'] = $this->input->post ( 'entity_geozone_id' );
				$data ['entity_id'] = $this->input->post ( 'real_entity_id' );
				$data ['filetype_id'] = $this->input->post ( 'real_filetype_id' );
				$data ['period'] = $this->input->post ( 'period' );
				$data ['datafile_year'] = $this->input->post ( 'datafile_year' );
				$data ['indicatortarif_num_categories'] = $this->input->post ( 'indicatortarif_num_categories' );
				$data ['indicatortarif_step_perc'] = $this->input->post ( 'indicatortarif_step_perc' );
				
				$categories_arr = array ();
				
				$categories = ($data ['indicatortarif_num_categories'] - 1) / 2;
				
				for($c = 1; $c <= $categories; $c ++) {
					
					$categories_arr [$c - 1] = '-' . ($data ['indicatortarif_step_perc'] * $categories) / $c;
				}
				
				$categories_arr [$categories] = 0;
				
				for($c = 1; $c <= $categories; $c ++) {
					
					$categories_arr [] = $data ['indicatortarif_step_perc'] * $c;
				}
				
				$buttons = '';
				
				for($c = 1; $c <= $data ['indicatortarif_num_categories']; $c ++) {
					
					$button = array (
							'name' => 'catbtn[]',
							'id' => $categories_arr [$c - 1],
							'value' => 'Cat ' . $c,
							'type' => 'button',
							'content' => 'Cat ' . $c,
							'style' => 'height: 40px; width: 40px; font-size: 10px;',
							'onclick' => 'setfees(this);' 
					);
					
					$buttons .= form_button ( $button );
				}
				
				$entity_id = $this->entities_mdl->get_entity ( $data ['entity_id'] );
				$entity_class_id = $this->entities_mdl->get_entityclass ( $data ['entity_class_id'] );
				$entity_type_id = $this->entities_mdl->get_entitytype ( $data ['entity_type_id'] );
				$entity_pbf_group_id = $this->entities_mdl->get_entitygroup ( $data ['entity_pbf_group_id'] );
				$entity_status = $this->pbf->get_lookup ( $data ['entity_status'] );
				$filetype_id = $this->files_mdl->get_file_type ( $data ['filetype_id'] );
				
				$level_0 = $this->geo_mdl->get_zone ( $data ['level_0'] );
				$indicatortarif_geo_id = $level_0 ['geo_id'];
				$entity_geozone_id = $this->geo_mdl->get_zone ( $data ['entity_geozone_id'] );
				
				if (! empty ( $data ['entity_geozone_id'] )) {
					
					$indicatortarif_geo_id = $entity_geozone_id ['geo_id'];
				}
				
				$active_geo = $this->pbf->get_default_pbf_geo ();
				
				$period = explode ( '_', $data ['period'] );
				
				$data ['indicatortarif_quarter'] = $this->pbf->get_current_quarterBy_month ( $period [1] );
				$data ['indicatortarif_month'] = $period [1];
				
				$data ['indicatortarif_geo_id'] = $indicatortarif_geo_id;
				
				$period = $this->lang->line ( 'app_quarter_' . $this->pbf->get_current_quarterBy_month ( $period [1] ) ) . ', ' . $this->lang->line ( 'app_month_' . $period [1] );
				
				$data ['setting_selections'] = array (
						0 => $this->lang->line ( 'fees_wizard_form_entity_class' ) . $entity_class_id ['entity_class_name'],
						1 => (empty ( $entity_type_id ['entity_type_name'] )) ? '' : $this->lang->line ( 'fees_wizard_form_entity_type' ) . $entity_type_id ['entity_type_name'],
						2 => (empty ( $entity_pbf_group_id ['entity_group_name'] )) ? '' : $this->lang->line ( 'fees_wizard_form_entity_group' ) . $entity_pbf_group_id ['entity_group_name'],
						3 => (empty ( $entity_status ['lookup_title'] )) ? '' : $this->lang->line ( 'fees_wizard_form_entity_status' ) . $entity_status ['lookup_title'],
						4 => (empty ( $level_0 ['geozone_name'] )) ? '' : $active_geo ['geo_title'] . ': ' . $level_0 ['geozone_name'] . ' - ' . $entity_geozone_id ['geozone_name'],
						5 => (empty ( $entity_id ['entity_name'] )) ? '' : $this->lang->line ( 'fees_wizard_form_entity' ) . $entity_id ['entity_name'],
						6 => (empty ( $filetype_id ['filetype_name'] )) ? '' : $this->lang->line ( 'fees_wizard_form_file_type' ) . $filetype_id ['filetype_name'],
						7 => (empty ( $data ['datafile_year'] )) ? '' : $this->lang->line ( 'fees_wizard_form_period' ) . $period . ' ' . $data ['datafile_year'],
						8 => $buttons 
				);
				
				$data ['list'] = $this->fees_mdl->get_indicators_new_fees ( $filetype_id ['filetype_id'], $this->config->item ( 'language_abbr' ) );
				
				foreach ( $data ['list'] as $k => $v ) {
					
					$data ['list'] [$k] ['indicator_title'] = $data ['list'] [$k] ['indicator_title'] . '<input type="hidden" value="' . $data ['list'] [$k] ['indicator_id'] . '" name="indicatortarif_indicator_id[]"><input id="default_tarif' . $data ['list'] [$k] ['indicator_id'] . '" type="hidden" value="' . $data ['list'] [$k] ['default_tarif'] . '" name="default_tarif[]">';
					
					$data ['list'] [$k] ['set_tarif'] = form_input ( array (
							'name' => 'indicator_tarif[]',
							'value' => $data ['list'] [$k] ['default_tarif'],
							'id' => 'indicator_tarif_' . $k,
							'class' => 'dataentry' 
					) );
					$data ['list'] [$k] ['content_type'] = ($data ['list'] [$k] ['content_type'] == 'Quality') ? form_checkbox ( 'indicator_exclusion[]', $data ['list'] [$k] ['indicator_id'], FALSE ) : form_checkbox ( 'indicator_exclusion[]', $data ['list'] [$k] ['indicator_id'], FALSE, 'disabled="disabled"' );
					$data ['list'] [$k] ['indicator_id'] = $k + 1;
					unset ( $data ['list'] [$k] ['default_tarif'] );
				}
				
				array_unshift ( $data ['list'], array (
						$this->lang->line ( 'fees_wizard_list_order' ),
						$this->lang->line ( 'fees_wizard_list_indicator' ),
						$this->lang->line ( 'fees_wizard_list_tarif' ),
						$this->lang->line ( 'fees_wizard_list_exclusion' ) 
				) );
				
				$data ['step'] = 3;
				
				break;
		}
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'fees_wizard_form_title' ) . ' ' . $step . ' ' . $this->lang->line ( 'fees_wizard_step_of_steps' ) . ' 3';
		
		$data ['page'] = 'fees_frm';
		$this->load->view ( 'body', $data );
	}
	function copytarif($indicatortarif_id) {
		$indicatortarif = $this->fees_mdl->get_fees ( $indicatortarif_id );
		
		$this->load->model ( 'entities_mdl' );
		$this->load->model ( 'entities_mdl' );
		$this->load->model ( 'geo_mdl' );
		$this->load->model ( 'files_mdl' );
		
		$filetype_id = $indicatortarif [0] ['indicatortarif_filetype_id'];
		
		$data ['entity_class_id'] = $indicatortarif [0] ['indicatortarif_entity_class_id'];
		$data ['entity_type_id'] = $indicatortarif [0] ['indicatortarif_entity_type_id'];
		$data ['entity_pbf_group_id'] = $indicatortarif [0] ['indicatortarif_entity_group_id'];
		$data ['entity_status'] = $indicatortarif [0] ['indicatortarif_entity_status_id'];
		
		$data ['entity_geozone_id'] = $indicatortarif [0] ['indicatortarif_geozone_id'];
		$data ['entity_id'] = $indicatortarif [0] ['indicatortarif_entity_id'];
		$data ['filetype_id'] = $indicatortarif [0] ['indicatortarif_filetype_id'];
		$data ['datafile_year'] = $indicatortarif [0] ['indicatortarif_year'];
		
		$data ['indicatortarif_num_categories'] = $indicatortarif [0] ['indicatortarif_num_categories'];
		$data ['indicatortarif_step_perc'] = $indicatortarif [0] ['indicatortarif_step_perc'];
		
		$categories_arr = array ();
		
		$categories = ($data ['indicatortarif_num_categories'] - 1) / 2;
		
		for($c = 1; $c <= $categories; $c ++) {
			
			$categories_arr [$c - 1] = '-' . ($data ['indicatortarif_step_perc'] * $categories) / $c;
		}
		
		$categories_arr [$categories] = 0;
		
		for($c = 1; $c <= $categories; $c ++) {
			
			$categories_arr [] = $data ['indicatortarif_step_perc'] * $c;
		}
		
		$buttons = '';
		
		for($c = 1; $c <= $data ['indicatortarif_num_categories']; $c ++) {
			
			$button = array (
					'name' => 'catbtn[]',
					'id' => $categories_arr [$c - 1],
					'value' => 'Cat ' . $c,
					'type' => 'button',
					'content' => 'Cat ' . $c,
					'style' => 'height: 40px; width: 40px; font-size: 10px;',
					'onclick' => 'setfees(this);' 
			);
			
			$buttons .= form_button ( $button );
		}
		
		$entity_id = $this->entities_mdl->get_entity ( $data ['entity_id'] );
		$entity_class_id = $this->entities_mdl->get_entityclass ( $data ['entity_class_id'] );
		$entity_type_id = $this->entities_mdl->get_entitytype ( $data ['entity_type_id'] );
		$entity_pbf_group_id = $this->entities_mdl->get_entitygroup ( $data ['entity_pbf_group_id'] );
		$entity_status = $this->pbf->get_lookup ( $data ['entity_status'] );
		$filetype_id = $this->files_mdl->get_file_type ( $data ['filetype_id'] );
		
		$data ['indicatortarif_quarter'] = $indicatortarif [0] ['indicatortarif_quarter'];
		$data ['indicatortarif_month'] = $indicatortarif [0] ['indicatortarif_month'];
		
		$data ['indicatortarif_geo_id'] = $indicatortarif [0] ['indicatortarif_geozone_id'];
		
		$period = $this->lang->line ( 'app_quarter_' . $data ['indicatortarif_quarter'] ) . ', ' . $this->lang->line ( 'app_month_' . $data ['indicatortarif_month'] );
		
		$data ['setting_selections'] = array (
				0 => $this->lang->line ( 'fees_wizard_form_entity_class' ) . $entity_class_id ['entity_class_name'],
				1 => (empty ( $entity_type_id ['entity_type_name'] )) ? '' : $this->lang->line ( 'fees_wizard_form_entity_type' ) . $entity_type_id ['entity_type_name'],
				2 => (empty ( $entity_pbf_group_id ['entity_group_name'] )) ? '' : $this->lang->line ( 'fees_wizard_form_entity_group' ) . $entity_pbf_group_id ['entity_group_name'],
				3 => (empty ( $entity_status ['lookup_title'] )) ? '' : $this->lang->line ( 'fees_wizard_form_entity_status' ) . $entity_status ['lookup_title'],
				4 => (empty ( $entity_id ['entity_name'] )) ? '' : $this->lang->line ( 'fees_wizard_form_entity' ) . $entity_id ['entity_name'],
				5 => (empty ( $filetype_id ['filetype_name'] )) ? '' : $this->lang->line ( 'fees_wizard_form_file_type' ) . $filetype_id ['filetype_name'],
				6 => (empty ( $data ['datafile_year'] )) ? '' : $this->lang->line ( 'fees_wizard_form_period' ) . form_cascaded_dropdown ( 'period', $this->pbf->get_months_list ( ($indicatortarif [0] ['filefrequency'] == 'Quarterly') ? true : false ), ($indicatortarif [0] ['filefrequency'] == 'Quarterly') ? $data ['indicatortarif_quarter'] : $data ['indicatortarif_month'], 'id="period" class="month"' ) . form_dropdown ( 'datafile_year', $this->pbf->get_years_list ( 1 ), $data ['datafile_year'], 'id="datafile_year" class="year"' ) . form_hidden ( 'indicatortarif_id', $indicatortarif [0] ['indicatortarif_id'] ) . form_hidden ( 'filefrequency', $indicatortarif [0] ['filefrequency'] ),
				7 => $buttons 
		);
		
		foreach ( $indicatortarif as $k => $v ) {
			
			$indicatortarif [$k] ['indicator_title'] = $indicatortarif [$k] ['indicator_title'] . '<input type="hidden" value="' . $indicatortarif [$k] ['indicator_id'] . '" name="indicatortarif_indicator_id[]"><input id="default_tarif' . $indicatortarif [$k] ['indicator_id'] . '" type="hidden" value="' . $indicatortarif [$k] ['default_tarif'] . '" name="default_tarif[]">';
			
			$indicatortarif [$k] ['indicator_tarif'] = form_input ( array (
					'name' => 'indicator_tarif[]',
					'value' => number_format ( $indicatortarif [$k] ['indicator_tarif'] ),
					'id' => 'indicator_tarif_' . $k,
					'class' => 'dataentry' 
			) );
			$indicatortarif [$k] ['content_type'] = ($indicatortarif [$k] ['content_type'] == 'Quality') ? form_checkbox ( 'indicator_exclusion[]', $indicatortarif [$k] ['indicator_id'], ($indicatortarif [$k] ['indicator_exclusion'] == 1) ? TRUE : FALSE ) : form_checkbox ( 'indicator_exclusion[]', $indicatortarif [$k] ['indicator_id'], FALSE, 'disabled="disabled"' );
			$indicatortarif [$k] ['filetype_name'] = $indicatortarif [$k] ['indicator_id'] = $k + 1;
			unset ( $indicatortarif [$k] ['indicatortarif_filetype_id'] );
			unset ( $indicatortarif [$k] ['indicatortarif_entity_class_id'] );
			unset ( $indicatortarif [$k] ['indicatortarif_entity_type_id'] );
			unset ( $indicatortarif [$k] ['indicatortarif_entity_group_id'] );
			unset ( $indicatortarif [$k] ['indicatortarif_entity_status_id'] );
			unset ( $indicatortarif [$k] ['indicatortarif_geozone_id'] );
			unset ( $indicatortarif [$k] ['indicatortarif_entity_id'] );
			unset ( $indicatortarif [$k] ['indicatortarif_filetype_id'] );
			unset ( $indicatortarif [$k] ['indicatortarif_year'] );
			unset ( $indicatortarif [$k] ['indicatortarif_quarter'] );
			unset ( $indicatortarif [$k] ['indicatortarif_month'] );
			unset ( $indicatortarif [$k] ['indicatortarif_geozone_id'] );
			unset ( $indicatortarif [$k] ['indicatortarif_geo_id'] );
			unset ( $indicatortarif [$k] ['indicatortarifdetails_id'] );
			unset ( $indicatortarif [$k] ['indicatortarif_id'] );
			unset ( $indicatortarif [$k] ['indicator_id'] );
			unset ( $indicatortarif [$k] ['indicator_exclusion'] );
			unset ( $indicatortarif [$k] ['filefrequency'] );
			unset ( $indicatortarif [$k] ['default_tarif'] );
			unset ( $indicatortarif [$k] ['indicatortarif_num_categories'] );
			unset ( $indicatortarif [$k] ['indicatortarif_step_perc'] );
		}
		
		array_unshift ( $indicatortarif, array (
				$this->lang->line ( 'fees_wizard_list_order' ),
				$this->lang->line ( 'fees_wizard_list_indicator' ),
				$this->lang->line ( 'fees_wizard_list_tarif' ),
				$this->lang->line ( 'fees_wizard_list_exclusion' ) 
		) );
		
		$data ['list'] = $indicatortarif;
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'fees_wizard_form_copy' );
		
		$data ['step'] = 4; // copy
		$data ['page'] = 'fees_frm';
		$this->load->view ( 'body', $data );
	}
	function save() {
		$indicatortarifdetails = $this->input->post ();
		
		$indicatortarif = array (
				'indicatortarif_entity_class_id' => $indicatortarifdetails ['indicatortarif_entity_class_id'],
				'indicatortarif_geo_id' => (empty ( $indicatortarifdetails ['indicatortarif_geo_id'] )) ? NULL : $indicatortarifdetails ['indicatortarif_geo_id'],
				'indicatortarif_geozone_id' => (empty ( $indicatortarifdetails ['indicatortarif_geozone_id'] )) ? NULL : $indicatortarifdetails ['indicatortarif_geozone_id'],
				'indicatortarif_entity_id' => (empty ( $indicatortarifdetails ['indicatortarif_entity_id'] )) ? NULL : $indicatortarifdetails ['indicatortarif_entity_id'],
				'indicatortarif_entity_type_id' => (empty ( $indicatortarifdetails ['indicatortarif_entity_type_id'] )) ? NULL : $indicatortarifdetails ['indicatortarif_entity_type_id'],
				'indicatortarif_entity_group_id' => (empty ( $indicatortarifdetails ['indicatortarif_entity_group_id'] )) ? NULL : $indicatortarifdetails ['indicatortarif_entity_group_id'],
				'indicatortarif_entity_status_id' => (empty ( $indicatortarifdetails ['indicatortarif_entity_status_id'] )) ? NULL : $indicatortarifdetails ['indicatortarif_entity_status_id'],
				'indicatortarif_filetype_id' => $indicatortarifdetails ['indicatortarif_filetype_id'],
				'indicatortarif_month' => $indicatortarifdetails ['indicatortarif_month'],
				'indicatortarif_quarter' => $indicatortarifdetails ['indicatortarif_quarter'],
				'indicatortarif_year' => $indicatortarifdetails ['indicatortarif_year'],
				'indicatortarif_num_categories' => $indicatortarifdetails ['indicatortarif_num_categories'],
				'indicatortarif_step_perc' => $indicatortarifdetails ['indicatortarif_step_perc'] 
		);
		
		unset ( $indicatortarifdetails ['submit'] );
		unset ( $indicatortarifdetails ['indicatortarif_geo_id'] );
		unset ( $indicatortarifdetails ['indicatortarif_geozone_id'] );
		unset ( $indicatortarifdetails ['indicatortarif_entity_id'] );
		unset ( $indicatortarifdetails ['indicatortarif_entity_type_id'] );
		unset ( $indicatortarifdetails ['indicatortarif_entity_class_id'] );
		unset ( $indicatortarifdetails ['indicatortarif_entity_group_id'] );
		unset ( $indicatortarifdetails ['indicatortarif_entity_status_id'] );
		unset ( $indicatortarifdetails ['indicatortarif_filetype_id'] );
		unset ( $indicatortarifdetails ['indicatortarif_month'] );
		unset ( $indicatortarifdetails ['indicatortarif_quarter'] );
		unset ( $indicatortarifdetails ['indicatortarif_year'] );
		unset ( $indicatortarifdetails ['indicatortarif_num_categories'] );
		unset ( $indicatortarifdetails ['indicatortarif_step_perc'] );
		
		if ($this->fees_mdl->save_fees_set ( $indicatortarif, $indicatortarifdetails )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'fees_save_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'fees_save_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( 'fees_process', 1 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	function savecopy() {
		$indicatortarifdetails = $this->input->post ();
		
		$original_set = $this->fees_mdl->get_fees_header ( $indicatortarifdetails ['indicatortarif_id'] );
		
		switch ($indicatortarifdetails ['filefrequency']) {
			
			case 'Monthly' :
				
				$original_set ['indicatortarif_month'] = $indicatortarifdetails ['period'];
				$original_set ['indicatortarif_quarter'] = $this->pbf->get_current_quarterBy_month ( $indicatortarifdetails ['period'] );
				
				break;
			
			case 'Quarterly' :
				
				$months = $this->pbf->get_monthsBy_quarter ( $indicatortarifdetails ['period'] );
				$original_set ['indicatortarif_month'] = $months [2];
				$original_set ['indicatortarif_quarter'] = $indicatortarifdetails ['period'];
				
				break;
		}
		
		$original_set ['indicatortarif_id'] = '';
		$original_set ['indicatortarif_year'] = $indicatortarifdetails ['datafile_year'];
		
		unset ( $indicatortarifdetails ['period'] );
		unset ( $indicatortarifdetails ['datafile_year'] );
		unset ( $indicatortarifdetails ['indicatortarif_id'] );
		unset ( $indicatortarifdetails ['filefrequency'] );
		unset ( $indicatortarifdetails ['submit'] );
		
		if ($this->fees_mdl->save_fees_set ( $original_set, $indicatortarifdetails )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'fees_copy_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'fees_copy_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( 'fees_create_copy', 1 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	function delete($indicatortarif_id, $month, $quarter, $year) {
		if ($this->fees_mdl->del_fees ( $indicatortarif_id )) {
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'fees_delete_success' ) 
			) );
		} 

		else {
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'fees_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( 'Removed tarif for month:' . $month . ', quarter: ' . $quarter . ', Year: ' . $year, 1 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
}