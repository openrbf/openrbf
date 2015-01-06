<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Indicators extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'indicators_mdl' );
		$this->lang->load ( 'indicators', $this->config->item ( 'language' ) );
		$this->lang->load ( 'hfrentities', $this->config->item ( 'language' ) );
	}
	
	function index() {
		redirect ( 'indicators/dataelements' );
	}
	
	function dataelements() {
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->indicators_mdl->get_indicators ( $preps ['offset'], $preps ['terms'], $this->config->item ( 'language_abbr' ) );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['indicator_title'] = anchor ( '/indicators/edit/' . $data ['list'] [$k] ['indicator_id'], $data ['list'] [$k] ['indicator_title'] );
			$data ['list'] [$k] ['indicator_featured'] = $this->pbf->rec_op_icon ( 'publish_' . $data ['list'] [$k] ['indicator_featured'], '/indicators/setfeature/' . $data ['list'] [$k] ['indicator_id'] );
			
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/indicators/edit/' . $data ['list'] [$k] ['indicator_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/indicators/delete/' . $data ['list'] [$k] ['indicator_id'] );
			$data ['list'] [$k] ['indicator_id'] = $k + $preps ['offset'] + 1;
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'list_indicator_title' ),
				$this->lang->line ( 'list_indicator_abbrev' ),
				$this->lang->line ( 'list_indicator_unit' ),
				$this->lang->line ( 'list_indicator_datatype' ),
				'',
				'',
				'' 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'indicators_title' ) . ' [' . $data ['records_num'] . ' ]';
		$data ['mod_title'] ['/indicators/add'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['rec_filters'] = $this->pbf->get_filters ( array (
				'indicator_title',
				'indicator_filetype_id' 
		) );
		
		$data ['tab_menus'] = $this->pbf->get_mod_submenu ( 17 );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	
	function fees() {
		$this->lang->load ( 'hfrentities', $this->config->item ( 'language' ) );
		$this->lang->load ( 'files', $this->config->item ( 'language' ) );
		
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->indicators_mdl->get_fees ( $preps ['offset'], $preps ['terms'] );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['entity_name'] = anchor ( '/indicators/editfees/' . $v ['entity_id'] . '/' . $v ['geozone_id'] . '/' . $v ['indicatortarif_filetype_id'] . '/' . $v ['indicatortarif_monthfrom'] . '/' . $v ['indicatortarif_monthto'] . '/' . $v ['indicatortarif_year'], $v ['entity_name'] . ' ' . $this->lang->line ( 'etty_typ_abbrv_ky_' . $v ['indicatortarif_entity_type_id'] ) );
			
			$data ['list'] [$k] ['indicatortarif_filetype_id'] = $this->lang->line ( 'filetype_ky_' . $v ['indicatortarif_filetype_id'] );
			
			$data ['list'] [$k] ['indicatortarif_monthfrom'] = $this->lang->line ( 'app_month_' . $v ['indicatortarif_monthfrom'] ) . ' - ' . $this->lang->line ( 'app_month_' . $v ['indicatortarif_monthto'] ) . ' ' . $v ['indicatortarif_year'];
			
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/indicators/editfees/' . $v ['entity_id'] . '/' . $v ['geozone_id'] . '/' . $v ['indicatortarif_filetype_id'] . '/' . $v ['indicatortarif_monthfrom'] . '/' . $v ['indicatortarif_monthfrom'] . '/' . $v ['indicatortarif_year'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/indicators/deletefees/' . $v ['indicatortarif_id'] );
			$data ['list'] [$k] ['indicatortarif_id'] = $k + $preps ['offset'] + 1;
			
			unset ( $data ['list'] [$k] ['indicatortarif_entity_type_id'] );
			unset ( $data ['list'] [$k] ['indicatortarif_monthto'] );
			unset ( $data ['list'] [$k] ['indicatortarif_year'] );
			unset ( $data ['list'] [$k] ['entity_id'] );
			unset ( $data ['list'] [$k] ['geozone_id'] );
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'frm_entity_name' ),
				$this->lang->line ( 'entity_district' ),
				$this->lang->line ( 'list_file_title' ),
				$this->lang->line ( 'frm_indicator_linked_filetypes_from' ) . ' - ' . $this->lang->line ( 'frm_indicator_linked_filetypes_to' ),
				'',
				'' 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'fees_form_title' ) . ' [' . $data ['records_num'] . ']';
		$data ['mod_title'] ['/indicators/addfees'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['tab_menus'] = $this->pbf->get_mod_submenu ( 17 );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['rec_filters'] = $this->pbf->get_filters ( array (
				'entity_id',
				'datafile_month',
				'filetype_id' 
		) );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	
	function add($data = '') {
		$data ['indicator_units'] = $this->pbf->get_lookups ( 'indicator_units' );
		$data ['indicator_vartype'] = $this->pbf->get_lookups ( 'indicator_vartypes' );
		
		$this->load->model ( 'popcible_mdl' );
		$pop = $this->popcible_mdl->get_popcible ();
		$data ['indicator_popcible'] [] = 'select';
		foreach ( $pop ['list'] as $p ) {
			$data ['indicator_popcible'] [$p ['popcible_id']] = $p ['popcible_name'];
		}
		
	
		$data ['indicator_category'] = $this->pbf->get_indicator_categories ();
		$data ['filetypes'] = $this->pbf->get_filetypes_lookup ();
		
		$data ['ckeditor'] = array (
				// ID of the textarea that will be replaced
				'id' => 'indicator_description', // Must match the textarea's id
				'path' => 'cside/js/ckeditor', // Path to the ckeditor folder relative to index.php
				                                     // Ckfinder's configuration
				'ckfinder' => array (
						'path' => 'cside/js/ckfinder'  // Path to the ckeditor folder relative to index.php
								),
				// Optionnal values
				'config' => array (
						'toolbar' => "Full", // Using the Full toolbar
						'width' => "850px", // Setting a custom width
						'height' => '200px'  // Setting a custom height
								),
				// Replacing styles from the "Styles tool"
				'styles' => array (
						// Creating a new style named "style 1"
						'style 1' => array (
								'name' => 'Blue Title',
								'element' => 'h2',
								'styles' => array (
										'color' => 'Blue',
										'font-weight' => 'bold' 
								) 
						),
						// Creating a new style named "style 2"
						'style 2' => array (
								'name' => 'Red Title',
								'element' => 'h2',
								'styles' => array (
										'color' => 'Red',
										'font-weight' => 'bold',
										'text-decoration' => 'underline' 
								) 
						) 
				) 
		);
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'indicators_title' );
		$data ['mngt_submenu'] = array (
				'indicators/' => $this->lang->line ( 'app_submenu_settings_files_indicators' ),
				'files/' => $this->lang->line ( 'app_submenu_settings_files_files' ) 
		);
		$data ['page'] = 'indicator_frm';
		

		$this->load->view ( 'body', $data );
	}
	
	function addfees($data = '') {
		$this->session->set_userdata ( array (
				'data_entity_class' => '1' 
		) ); // should be dynamically set depending on the available classes
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'fees_form_title' );
		$this->table->clear ();
		
		$tmpl = array (
				'table_open' => '<table border="0" cellpadding="0" cellspacing="0" id="hf_selector" width="100%" class="filters">',
				'heading_row_start' => '<tr>',
				'heading_row_end' => '</tr>',
				'heading_cell_start' => '<th>',
				'heading_cell_end' => '</th>',
				'row_start' => '<tr>',
				'row_alt_start' => '<tr class="even">',
				'row_end' => '</tr>',
				'cell_start' => '<td>',
				'cell_end' => '</td>',
				'table_close' => '</table>' 
		);
		
		$this->table->set_template ( $tmpl );
		
		$selectors = $this->pbf->get_selectors ( array (
				'filetype_entities' 
		) );
		
		$data ['hf_selector'] = $this->table->generate ( $selectors );
		$data ['page'] = 'fees_frm';
		
		$this->load->view ( 'body', $data );
	}
	
	function load_fees() {
		echo $this->get_ajax_fees ( $this->input->post () );
	}
	
	function get_ajax_fees($postvars) {
		$fees = $this->indicators_mdl->load_fees ( $postvars );
		
		if (! isset ( $fees [0] ['indicator_id'] )) {
			
			$dataelttarif_tarif = json_decode ( $fees [0] ['indicatortarif_tarif'], true );
			unset ( $fees [0] ['indicatortarif_tarif'] );
			$i = 0;
			
			foreach ( $dataelttarif_tarif as $k => $v ) {
				
				$fees [$i] ['indic_order'] = $i + 1;
				$fees [$i] ['indicatortarif_id'] = $fees [0] ['indicatortarif_id'];
				$fees [$i] ['indicator_id'] = $k;
				$fees [$i] ['indicatortarif_tarif'] = $v;
				$fees [$i] ['indicatortarif_scope'] = $fees [0] ['indicatortarif_scope'];
				$i ++;
			}
		}
		
		foreach ( $fees as $k => $fee ) {
			
			$fees [$k] ['indicator_id'] = $this->lang->line ( 'dataelmt_key_' . $fee ['indicator_id'] ) . form_hidden ( 'indicator_id[]', $fee ['indicator_id'] );
			
			$fees [$k] ['indicatortarif_tarif'] = form_input ( array (
					'name' => 'indicator_tarif[]',
					'id' => 'indicatortarif_tarif_' . $k,
					'value' => number_format ( $fee ['indicatortarif_tarif'], 3 ),
					'class' => 'dataentry',
					'size' => '10' 
			) );
			
			$fees [$k] ['indic_order'] = $k + 1;
			$dataelttarif_id = $fees [$k] ['indicatortarif_id'];
			$scope = $fees [$k] ['indicatortarif_scope'];
			unset ( $fees [$k] ['indicatortarif_id'] );
			unset ( $fees [$k] ['indicatortarif_scope'] );
		}
		
		array_unshift ( $fees, array (
				'#',
				$this->lang->line ( 'dataelements_title' ),
				$this->lang->line ( 'frm_indicator_linked_filetypesdefault_tarif' ) 
		) );
		
		$tmpl = array (
				'table_open' => '<table border="0" cellpadding="0" cellspacing="0" id="filetypes_table" width="100%" class="filters">',
				'heading_row_start' => '<tr>',
				'heading_row_end' => '</tr>',
				'heading_cell_start' => '<th>',
				'heading_cell_end' => '</th>',
				'row_start' => '<tr>',
				'row_alt_start' => '<tr class="even">',
				'row_end' => '</tr>',
				'cell_start' => '<td>',
				'cell_end' => '</td>',
				'table_close' => '</table>' 
		);
		
		$this->table->set_template ( $tmpl );
		
		return '<script type="text/javascript" src="' . $this->config->item ( 'base_url' ) . 'cside/js/jshashtable.js"></script>
		<script type="text/javascript" src="' . $this->config->item ( 'base_url' ) . 'cside/js/jqnumformat.js"></script>
		<script type="text/javascript">
			
					$("[id*=indicatortarif_tarif_]").blur(function(){
						$(this).parseNumber({format:"#,###.000", locale:"us"});
   						$(this).formatNumber({format:"#,###.000", locale:"us"});
					});
				
					</script>' . form_open ( 'indicators/savefees', array (
				'name' => 'targets_frm',
				'id' => 'targets_frmid' 
		) ) . $this->table->generate ( $fees ) . '<table class="filters" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td>' . form_dropdown ( 'indicatortarif_monthfrom', $this->pbf->get_months_list (), date ( 'n' ) ) . ' ' . $this->lang->line ( 'report_param_from_to' ) . ' ' . form_dropdown ( 'indicatortarif_monthto', $this->pbf->get_months_list (), date ( 'n' ) ) . form_dropdown ( 'indicatortarif_year', $this->pbf->get_years_list ( 2 ), date ( 'Y' ) ) . '<br><br>' . form_radio ( array (
				'name' => 'indicatortarif_scope',
				'value' => 'self',
				'checked' => ($scope == 'self') ? TRUE : FALSE 
		) ) . ' Save for this Health Facility<br>' . form_radio ( array (
				'name' => 'indicatortarif_scope',
				'value' => 'sametypes',
				'checked' => ($scope == 'sametypes') ? TRUE : FALSE 
		) ) . ' Save for all of the same type in the district<br>' . form_radio ( array (
				'name' => 'indicatortarif_scope',
				'value' => 'all',
				'checked' => ($scope == 'all') ? TRUE : FALSE 
		) ) . ' Save for all health facilities in the district<br>' . form_radio ( array (
				'name' => 'indicatortarif_scope',
				'value' => 'level_0_sametypes',
				'checked' => ($scope == 'level_0_sametypes') ? TRUE : FALSE 
		) ) . ' Save for all of the same type in the Region<br>'.
				//form_radio(	array(	'name' => 'indicatortarif_scope', 'value' => 'country_sametypes', 'checked' => ($scope=='country_sametypes')?TRUE:FALSE)).' Save for all of the same type in the country<br>'.
				//form_radio(	array(	'name' => 'indicatortarif_scope', 'value' => 'all', 'checked' => ($scope=='all')?TRUE:FALSE)).' Save for all in the country '.

				'</td></tr></table>' . 
		form_submit ( 'submitbtn', $this->lang->line ( 'app_form_save' ), 'class="submit"' ) . form_reset ( '', $this->lang->line ( 'app_form_cancel' ), 'onClick="history.go(-1);return true;" class="submit small"' ) . form_hidden ( 'indicatortarif_id', $dataelttarif_id ) . form_hidden ( 'indicatortarif_geozone_id', $postvars ['geozone_id'] ) . form_hidden ( 'indicatortarif_entity_id', $postvars ['entity_id'] ) . form_hidden ( 'indicatortarif_filetype_id', $postvars ['filetype_id'] ) . form_close ();

	}
	
	function save() {
		$indicator = $this->input->post ();
		// print_test($indicator);exit;
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'indicator_title', 'indicator title', 'trim|required' );
		
		if ($this->form_validation->run () == FALSE) {
			$this->add ( $indicator );
		} else {
			
			$indicatorsfileypes ['filetype_id'] = $indicator ['filetype_id'];
			$indicatorsfileypes ['order'] = $indicator ['order'];
			$indicatorsfileypes ['indicator_category_id'] = $indicator ['indicator_category_id'];
			$indicatorsfileypes ['quality_associated'] = $indicator ['quality_associated'];
			$indicatorsfileypes ['default_tarif'] = $indicator ['default_tarif'];
			$indicatorsfileypes ['bonus_indigent'] = $indicator ['bonus_indigent'];
			$indicatorsfileypes ['dataelts_target_abs'] = $indicator ['dataelts_target_abs'];
			$indicatorsfileypes ['dataelts_target_rel'] = $indicator ['dataelts_target_rel'];
			$indicatorsfileypes ['use_from'] = $indicator ['use_from'];
			$indicatorsfileypes ['use_to'] = $indicator ['use_to'];
			$indicatorsfileypes ['indicator_id'] = $indicator ['indicator_id'];
			
			$language ['indicator_id'] = $indicator ['indicator_id'];
			$language ['indicator_abbrev'] = $indicator ['indicator_abbrev'];
			$language ['indicator_description'] = $indicator ['indicator_description'];
			$language ['indicator_common_name'] = $indicator ['indicator_common_name'];
			$language ['indicator_language'] = $this->config->item ( 'language_abbr' );
			$language ['indicator_title'] = $indicator ['indicator_title'];
			
			unset ( $indicator ['indicator_title'] );
			unset ( $indicator ['indicator_abbrev'] );
			unset ( $indicator ['indicator_description'] );
			unset ( $indicator ['indicator_common_name'] );
			unset ( $indicator ['filetype_id'] );
			unset ( $indicator ['order'] );
			unset ( $indicator ['indicator_category_id'] );
			unset ( $indicator ['quality_associated'] );
			unset ( $indicator ['default_tarif'] );
			unset ( $indicator ['dataelts_target_abs'] );
			unset ( $indicator ['dataelts_target_rel'] );
			unset ( $indicator ['bonus_indigent'] );
			unset ( $indicator ['use_from'] );
			unset ( $indicator ['use_to'] );
			unset ( $indicator ['submit'] );
			
			$indicator ['indicator_featured'] = ! isset ( $indicator ['indicator_featured'] ) ? 0 : 1;
			
			$indicator ['indicator_realtime_result'] = ! isset ( $indicator ['indicator_realtime_result'] ) ? 0 : 1;
			$indicator ['indicator_use_coverage'] = ! isset ( $indicator ['indicator_use_coverage'] ) ? 0 : 1;
			$indicator ['indicator_use_indigence_bonus'] = ! isset ( $indicator ['indicator_use_indigence_bonus'] ) ? 0 : 1;
			$indicator ['indicator_editable_tarif'] = ! isset ( $indicator ['indicator_editable_tarif'] ) ? 0 : 1;
			
			if ($_FILES ['indicator_icon_file'] ['tmp_name']) {
				
				$config ['file_field_name'] = 'indicator_icon_file';
				
				$config ['upload_path'] = FCPATH . 'cside/images/portal/';
				$config ['allowed_types'] = 'gif|jpg|png';
				$config ['overwrite'] = FALSE;
				$config ['remove_spaces'] = TRUE;
				$config ['max_filename'] = '0';
				$config ['max_size'] = '1024'; // use the system limit... see php.ini config in regards
				$config ['max_width'] = '400'; // should be 360 at destination
				$config ['max_height'] = '300'; // should be 300 at destination
				$config ['remove_spaces'] = TRUE;
				
				$this->load->library ( 'upload', $config );
				
				if (! $this->upload->do_upload ( $config ['file_field_name'] )) {
					$error = array (
							'error' => $this->upload->display_errors () 
					);
					
					unset ( $indicator ['indicator_icon_file'] );
				} else {
					$data = $this->upload->data ();
					
					$indicator ['indicator_icon_file'] = $data ['file_name']; // may file_name is enough
				}
			}
			
			if ($this->indicators_mdl->save_indicator ( $indicator, $indicatorsfileypes, $language )) {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => $this->lang->line ( 'indicator_save_success' ) 
				) );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'indicator_save_error' ) 
				) );
			}
			
			$this->pbf->set_eventlog ( '', 0 );
			
			redirect ( $this->session->userdata ( 'next_base_url' ) );
		}
	}
	
	function savefees() {
		$fees = $this->input->post ();
		
		unset ( $fees ['submitbtn'] );
		
		if ($this->indicators_mdl->save_dataelmfees ( $fees )) {
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
		
		$this->pbf->set_eventlog ( 'indicator_process_entity', 1 );
		redirect ( 'indicators/fees' );
	}
	
	function edit($indicator_id) {
		$data = $this->indicators_mdl->get_indicator ( $indicator_id, $this->config->item ( 'language_abbr' ) );
		
		// print_test($data);
		
		$this->add ( $data );
	}
	
	function editfees($entity_id, $geozone_id, $filetype_id, $from, $to, $year) {
		$this->load->model ( 'entities_mdl' );
		$data ['targetz'] = $this->get_ajax_fees ( array (
				'entity_id' => $entity_id,
				'filetype_id' => $filetype_id,
				'geozone_id' => $geozone_id,
				'indicatortarif_monthfrom' => $from,
				'indicatortarif_monthto' => $to,
				'indicatortarif_year' => $year 
		) );
		
		$entity = $this->entities_mdl->get_entity ( $entity_id );
		$this->session->set_userdata ( 'filtered_entity_id', $entity_id );
		$this->session->set_userdata ( 'filtered_geozone_id', $entity ['entity_geozone_id'] );
		$this->session->set_userdata ( 'level_0', $entity ['parent_geozone_id'] );
		$this->session->set_userdata ( 'filtered_filetype_id', $filetype_id );
		$this->addfees ( $data );
	}
	
	function setfeature($indicator_id, $state) {
		if ($this->indicators_mdl->set_feature ( $indicator_id, $state )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'indicator_update_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'indicator_update_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	
	function delete($indicator_id) {
		if ($this->indicators_mdl->del_indicator ( $indicator_id )) {
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'indicator_delete_success' ) 
			) );
		} 

		else {
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'indicator_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	
	function deletefees($dataelttarif_id) {
		if ($this->indicators_mdl->delete_fees ( $dataelttarif_id )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'fees_delete_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'fees_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
}