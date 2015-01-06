<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Publication extends CI_Controller {
	function __construct() {
		parent::__construct ();
		
		$this->load->model ( 'frontdata_mdl' );
		$this->lang->load ( 'cms', $this->config->item ( 'language' ) );
		$this->lang->load ( 'publication', $this->config->item ( 'language' ) );
		$this->lang->load ( 'otheroptions', $this->config->item ( 'language' ) );
	}
	
	function index() {
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data ['publish'] = $this->frontdata_mdl->get_available_data ( $preps ['offset'], $preps ['terms'] );
		
		$data ['validate'] = $this->frontdata_mdl->get_available_data_validation ( $preps ['offset'], $preps ['terms'] );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'cms_title' ) . ' - ' . $this->lang->line ( 'frontdata_title' ) . ' [' . $data ['publish'] ['records_num'] . ']';
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['tab_menus'] = implode ( '&nbsp;&nbsp;|&nbsp;&nbsp;', $this->pbf->get_lookup_submenu ( 'cms/cms/', 'front_articles_category' ) ) . '&nbsp;&nbsp;|&nbsp;&nbsp;' . $this->pbf->get_mod_submenu ( 11 );
		
		$this->pbf->get_pagination ( $data ['publish'] ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'publication';
		$this->load->view ( 'body', $data );
	}
	
	function load_publication() {
		$this->lang->load ( 'hfrentities', $this->config->item ( 'language' ) );
		
		$comments = array ();
		$status = array ();
		
		$status ['validation_reg'] = $this->frontdata_mdl->get_validation_reg_status ( $this->input->post ( 'id' ), $this->input->post ( 'year' ) );
		$status ['validation'] = $this->frontdata_mdl->get_validation_status ( $this->input->post ( 'id' ), $this->input->post ( 'year' ) );
		$status ['publication'] = $this->frontdata_mdl->get_publication_status ( $this->input->post ( 'id' ), $this->input->post ( 'year' ) );
		
		$permissions = $this->session->userdata ( 'usergroupsrules' );
		
		$canValidate = array_search ( 'publication/can_validate/', $permissions );
		$canPublish = array_search ( 'publication/can_publish/', $permissions );
		$canValidateReg = array_search ( 'datafiles/validate/', $permissions );
		
		if (! empty ( $status )) {
			
			foreach ( $status ['publication'] as $k => $sts ) {
				
				$status [$k] ['geozone_id'] = $k + 1;
				$status [$k] ['geozone_name'] = $sts ['geozone_name'];
				
				$nb_ent_valid = $this->frontdata_mdl->check_all_valid_reg ( $sts ['geozone_id'], $this->input->post ( 'id' ), $this->input->post ( 'year' ) );
				$nb_datafiles_to_enter = $this->frontdata_mdl->nb_datafiles_zone_to_enter ( $sts ['geozone_id'], $this->input->post ( 'id' ), $this->input->post ( 'year' ) );
				$nb_datafiles_entered = $this->frontdata_mdl->nb_datafiles_zone_entered ( $sts ['geozone_id'], $this->input->post ( 'id' ), $this->input->post ( 'year' ) );
				
				$status [$k] ['completude_id'] = $nb_datafiles_entered ['nb'] . "/" . $nb_datafiles_to_enter ['nb'];
				
				
				$condition = $this->pbf_mdl->get_workflow_condition ( 'reg_valid' );
				$previous_step_ok = true;
				switch ($condition ['state_name']) {
					case 'data_entered' :
						if ($nb_datafiles_entered ['nb'] == $nb_datafiles_to_enter ['nb']) {
							$previous_step_ok = true;
						} else {
							$previous_step_ok = false;
							$message = $this->lang->line ( 'workflow_data_incomplete' );
						}
						break;
					case 'reg_valid' :
						if (($nb_ent_valid ['nb'] - $nb_datafiles_entered ['nb']) != 0) {
							$previous_step_ok = false;
							$message = $this->lang->line ( 'workflow_reg_val_incomplete' );
						} else {
							$previous_step_ok = true;
						}
						break;
					case 'nat_valid' :
						$previous_step_ok = false;
						$message = $this->lang->line ( 'workflow_nat_val_incomplete' );
						foreach ( $status ['validation'] as $keyz => $valuez ) {
							if ($status ['validation'] [$keyz] ['datafile_valid_nat'] == 1 and $status ['validation'] [$keyz] ['geozone_id'] == $sts ['geozone_id']) {
								$previous_step_ok = true;
							}
						}
						
						break;
				}
				if ($canValidateReg) {
					if ($previous_step_ok) {
						$status [$k] ['validation_reg_id'] = form_checkbox ( 'validation_reg_id[]', $sts ['geozone_id'], ($nb_ent_valid ['nb'] == $nb_datafiles_entered ['nb']) ? TRUE : FALSE ) . "(" . $nb_ent_valid ['nb'] . "/" . $nb_datafiles_entered ['nb'] . ")";
					} else {
						$status [$k] ['validation_reg_id'] = $message;
					}
				} else {
					$data = array (
							'name' => 'validation_reg_id[]',
							'value' => $sts ['geozone_id'],
							'checked' => is_null ( $status ['validation_reg'] [$k] ['validation_reg_id'] ) ? FALSE : TRUE,
							'style' => 'visibility:hidden' 
					);
					$status [$k] ['validation_reg_id'] = form_checkbox ( $data ) . (is_null ( $status ['validation_reg'] [$k] ['validation_reg_id'] ) ? '' : '' . $this->config->item ( 'base_url' ) . 'cside/images/icons/publish.png" border="0">') . "(" . $nb_ent_valid ['nb'] . "/" . $nb_datafiles_entered ['nb'] . ")";
				}
				
								
				$condition = $this->pbf_mdl->get_workflow_condition ( 'nat_valid' );
				$previous_step_ok = true;
				switch ($condition ['state_name']) {
					case 'data_entered' :
						if ($nb_datafiles_entered ['nb'] == $nb_datafiles_to_enter ['nb']) {
							$previous_step_ok = true;
						} else {
							$previous_step_ok = false;
							$message = $this->lang->line ( 'workflow_data_incomplete' );
						}
						break;
					case 'reg_valid' :
						if (($nb_ent_valid ['nb'] - $nb_datafiles_entered ['nb']) != 0) {
							$previous_step_ok = false;
							$message = $this->lang->line ( 'workflow_reg_val_incomplete' );
						} else {
							$previous_step_ok = true;
							$message = $this->lang->line ( 'workflow_reg_val_incomplete' );
						}
						break;
					case 'nat_valid' :
						
						$previous_step_ok = false;
						$message = $this->lang->line ( 'workflow_nat_val_incomplete' );
						foreach ( $status ['validation'] as $keyz => $valuez ) {
							if ($status ['validation'] [$keyz] ['datafile_valid_nat'] == 1 and $status ['validation'] [$keyz] ['geozone_id'] == $sts ['geozone_id']) {
								$previous_step_ok = true;
							}
						}
						
						break;
				}
				
				if ($canValidate) {
					$status [$k] ['validation_id'] = $message;
					$t_status ['validation'] = $status ['validation'];
					foreach ( $t_status ['validation'] as $keyz => $valuez ) {
						if ($previous_step_ok and $t_status ['validation'] [$keyz] ['geozone_id'] == $sts ['geozone_id']) {
							$status [$k] ['validation_id'] = form_checkbox ( 'validation_id[]', $sts ['geozone_id'], ($t_status ['validation'] [$keyz] ['datafile_valid_nat'] != '1') ? FALSE : TRUE );
						} elseif ($previous_step_ok) {
							
							
						}
					}
				} else {
					$data = array (
							'name' => 'validation_id[]',
							'value' => $sts ['geozone_id'],
							'checked' => is_null ( $status ['validation'] [$k] ['validation_id'] ) ? FALSE : TRUE,
							'style' => 'visibility:hidden' 
					);
					
					$status [$k] ['validation_id'] = form_checkbox ( $data ) . (is_null ( $status ['validation'] [$k] ['validation_id'] ) ? '' : '');
				}
				
				
				
				$condition = $this->pbf_mdl->get_workflow_condition ( 'publication' );
				$previous_step_ok = true;
				switch ($condition ['state_name']) {
					case 'data_entered' :
						if ($nb_datafiles_entered ['nb'] == $nb_datafiles_to_enter ['nb']) {
							$previous_step_ok = true;
						} else {
							$previous_step_ok = false;
							$message = $this->lang->line ( 'workflow_data_incomplete' );
						}
						break;
					case 'reg_valid' :
						if (($nb_ent_valid ['nb'] - $nb_datafiles_entered ['nb']) != 0) {
							$previous_step_ok = false;
							$message = $this->lang->line ( 'workflow_reg_val_incomplete' );
						} else {
							$previous_step_ok = true;
						}
						break;
					case 'nat_valid' :
						$previous_step_ok = false;
						$message = $this->lang->line ( 'workflow_nat_val_incomplete' );
						foreach ( $status ['validation'] as $keyz => $valuez ) {
							if ($status ['validation'] [$keyz] ['datafile_valid_nat'] == 1 and $status ['validation'] [$keyz] ['geozone_id'] == $sts ['geozone_id']) {
								$previous_step_ok = true;
							}
						}
						
						break;
				}
				if ($canPublish) {
					if ($previous_step_ok)
						$status [$k] ['published_id'] = form_checkbox ( 'published_id[]', $sts ['geozone_id'], is_null ( $sts ['published_id'] ) ? FALSE : TRUE );
					else
						$status [$k] ['published_id'] = $message;
				} else {
					$data = array (
							'name' => 'published_id[]',
							'value' => $sts ['geozone_id'],
							'checked' => is_null ( $sts ['published_id'] ) ? FALSE : TRUE,
							'style' => 'visibility:hidden' 
					);
					$status [$k] ['published_id'] = form_checkbox ( $data ) . (is_null ( $sts ['published_id'] ) ? '' : '');
				}
				
				$publication ['comments'] [] = $sts ['data_comment'];
				$validation ['comments'] [] = $status ['validation'] [$k] ['data_comment'];
			}
			unset ( $status ['validation_reg'] );
			unset ( $status ['validation'] );
			unset ( $status ['publication'] );
			
			if ($canPublish)
				$checkbox_publish = array (
						'name' => 'sel_all',
						'id' => 'sel_all',
						'onClick' => 'selecte_all_publish(this)',
						'checked' => FALSE 
				);
			else
				$checkbox_publish = array (
						'name' => 'sel_all',
						'id' => 'sel_all',
						'onClick' => 'selecte_all_publish(this)',
						'checked' => FALSE,
						'style' => 'visibility:hidden' 
				);
			if ($canValidate)
				$checkbox_validate = array (
						'name' => 'sel_all',
						'id' => 'sel_all',
						'onClick' => 'selecte_all_validate(this)',
						'checked' => FALSE 
				);
			else
				$checkbox_validate = array (
						'name' => 'sel_all',
						'id' => 'sel_all',
						'onClick' => 'selecte_all_validate(this)',
						'checked' => FALSE,
						'style' => 'visibility:hidden' 
				);
			if ($canValidateReg)
				$checkbox_validate_reg = array (
						'name' => 'sel_all',
						'id' => 'sel_all',
						'onClick' => 'selecte_all_validate_reg(this)',
						'checked' => FALSE 
				);
			else
				$checkbox_validate_reg = array (
						'name' => 'sel_all',
						'id' => 'sel_all',
						'onClick' => 'selecte_all_validate_reg(this)',
						'checked' => FALSE,
						'style' => 'visibility:hidden' 
				);
			
			array_unshift ( $status, array (
					'',
					$this->lang->line ( 'entity_district' ),
					$this->lang->line ( 'data_compleness' ),
					$this->lang->line ( 'regional_validation' ) . ($canValidateReg ? form_checkbox ( $checkbox_validate_reg ) . '(' . $this->lang->line ( 'all' ) : '') . ')',
					$this->lang->line ( 'national_validation' ) . ($canValidate ? form_checkbox ( $checkbox_validate ) . '(' . $this->lang->line ( 'all' ) : '') . ')',
					$this->lang->line ( 'publication' ) . ($canPublish ? form_checkbox ( $checkbox_publish ) . '(' . $this->lang->line ( 'all' ) : '') . ')' 
			) );
			$tmpl = array (
					'table_open' => '<table border="0" cellpadding="1" cellspacing="0" class="innertable" width="70%">',
					'row_start' => '<tr class="even">',
					'row_end' => '</tr>',
					'row_alt_start' => '<tr class="odd">',
					'row_alt_end' => '</tr>',
					'table_close' => '</table>' 
			);
			$this->table->set_template ( $tmpl );
			
			echo '<div>' . form_open ( 'publication/setdatapublish' ) . '<fieldset>' . 

			form_hidden ( array (
					'data_quarter' => $this->input->post ( 'id' ),
					'data_year' => $this->input->post ( 'year' ) 
			) ) . 

			$this->table->generate ( $status ) . 

			form_label ( $this->lang->line ( 'data_publish_rmq' ), 'data_comment' ) . 

			form_textarea ( array (
					'name' => 'data_comment',
					'cols' => '40',
					'rows' => '2',
					'value' => implode ( '', array_unique ( $comments ) ) 
			) ) . 

			'</fieldset>' . form_submit ( 'submit', $this->lang->line ( 'data_publish' ), 'class="submit small"' ) . form_close () . '</div>';
		} else {
			echo $this->lang->line ( 'dashb_ajax_failure' );
		}
	}
	
	function setdatapublish() {
		$pbf_frontdata ['datafile_quarter'] = $this->input->post ( 'data_quarter' );
		$pbf_frontdata ['datafile_year'] = $this->input->post ( 'data_year' );
		$pbf_frontdata ['data_author'] = $this->session->userdata ( 'user_id' );
		$pbf_frontdata ['data_comment'] = $this->input->post ( 'data_comment' );
		$pbf_frontdata ['published_id'] = $this->input->post ( 'published_id' );
		$pbf_frontdata ['validation_id'] = $this->input->post ( 'validation_id' );
		$pbf_frontdata ['validation_reg_id'] = $this->input->post ( 'validation_reg_id' );
		
		if ($this->frontdata_mdl->set_data_state ( $pbf_frontdata )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'data_publish_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'data_publish_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( 'quarter/' . $quarter . '/year/' . $year . '/state/' . $state, 1 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	
	function can_validate() { // dummy function to allow validation
	}
	
	function can_publish() { // dummy function to allow publication
	}
}