<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Configuration extends CI_Controller {
	function __construct() {
		parent::__construct ();
		
		$this->lang->load ( 'management', $this->config->item ( 'language' ) );
	}
	function index() {
		$this->load->model('report_mdl');
		$this->pbf->set_eventlog ( 'management_form_open', 1 );
		
		$raw_conf = $this->pbf->get_raw_config ();
		
		$data ['active_data_tab'] = $this->config->item ( 'active_data_tab' );
		$data ['active_data_toggle_link'] = $this->config->item ( 'active_data_toggle_link' );
		$data ['show_lang_selector'] = $this->config->item ( 'show_lang_selector' );
		$data ['num_period_display'] = $this->config->item ( 'num_period_display' );
		$data ['short_date_format'] = $this->config->item ( 'short_date_format' );
		$data ['long_date_format'] = $this->config->item ( 'long_date_format' );
		$data ['app_color_scheme'] = $this->config->item ( 'app_color_scheme' );
		$data ['app_font_size'] = $this->config->item ( 'app_font_size' );
		$data ['user_entity_aff'] = $this->config->item ( 'user_entity_aff' );
		$data ['rec_per_page'] = $this->config->item ( 'rec_per_page' );
		$data ['pop_growth_rate'] = $this->config->item ( 'pop_growth_rate' );
		$data ['app_country_currency'] = $this->config->item ( 'app_country_currency' );
		$data ['app_admin_email'] = $this->config->item ( 'app_admin_email' );
		
		$data ['image_thumb_size'] = $this->config->item ( 'image_thumb_size' );
		$data ['image_medium_size'] = $this->config->item ( 'image_medium_size' );
		$data ['image_big_size'] = $this->config->item ( 'image_big_size' );
		
		$data ['color_pourcentage_1stlever'] = $this->config->item ( 'color_pourcentage_1stlever' );
		$data ['color_pourcentage_2ndlever'] = $this->config->item ( 'color_pourcentage_2ndlever' );
		$data ['color_pourcentage_3rdlever'] = $this->config->item ( 'color_pourcentage_3rdlever' );
		$data ['auto_report_generation'] =  $this->config->item ( 'auto_report_generation' ); 
		$data ['realtimeresult_evolution_period_home'] = $this->config->item ( 'realtimeresult_evolution_period_home' );
		$data ['realtimeresult_period_data'] = $this->config->item ( 'realtimeresult_period_data' );
		$data ['report_prefix'] = $this->config->item ( 'report_prefix' );
		$data ['average_quality_period'] = $this->config->item ( 'average_quality_period' );
		
		$data ['language'] = $raw_conf ['language'];
		
		$data ['page'] = 'configuration_frm';
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'management_app_env_title' );
		$report_list=array();
		$report_list_all=$this->report_mdl->get_reports_all();
		foreach ($report_list_all as $report){
				$report_list[$report['report_id']]=$report['report_title'];
		}
		
		$data['reports_list_items']=$report_list;		
		$data ['report_feed_frontend_selected'] = $this->config->item ( 'report_feed_frontend' );
		$this->load->view ( 'body', $data );
	}
	function saveconfig() {
		$this->load->helper ( 'file' );
		
		$raw_conf = $this->pbf->get_raw_config ();
		$this->config->set_item ( 'report_feed_frontend', ($this->input->post ( 'reports' )));
		$this->config->set_item ( 'active_data_tab', ($this->input->post ( 'active_data_tab' ) == 1) ? 1 : 0 );
		$this->config->set_item ( 'active_data_toggle_link', ($this->input->post ( 'active_data_toggle_link' ) == 1) ? 1 : 0 );
		$this->config->set_item ( 'show_lang_selector', ($this->input->post ( 'show_lang_selector' ) == 1) ? 1 : 0 );
		$this->config->set_item ( 'short_date_format', $this->input->post ( 'short_date_format' ) );
		$this->config->set_item ( 'num_period_display', $this->input->post ( 'num_period_display' ) );
		$this->config->set_item ( 'long_date_format', $this->input->post ( 'long_date_format' ) );
		$this->config->set_item ( 'app_color_scheme', $this->input->post ( 'app_color_scheme' ) );
		$this->config->set_item ( 'app_font_size', $this->input->post ( 'app_font_size' ) );
		$this->config->set_item ( 'user_entity_aff', $this->input->post ( 'user_entity_aff' ) );
		$this->config->set_item ( 'rec_per_page', $this->input->post ( 'rec_per_page' ) );
		$this->config->set_item ( 'app_country_currency', $this->input->post ( 'app_country_currency' ) );
		$this->config->set_item ( 'app_admin_email', $this->input->post ( 'app_admin_email' ) );
		$this->config->set_item ( 'report_prefix', $this->input->post ( 'report_prefix' ) );
		$this->config->set_item ( 'pop_growth_rate', $this->input->post ( 'pop_growth_rate' ) );
		$this->config->set_item ( 'auto_report_generation', $this->input->post ( 'auto_report_generation' ) );
		$realtimeresult_evolution_period_home = intval ( $this->input->post ( 'realtimeresult_evolution_period_home' ) );
		
		if ($realtimeresult_evolution_period_home > 0 and $realtimeresult_evolution_period_home <= 12) {
			$this->config->set_item ( 'realtimeresult_evolution_period_home', $this->input->post ( 'realtimeresult_evolution_period_home' ) );
		}
		$this->config->set_item ( 'average_quality_period', $this->input->post ( 'average_quality_period' ) );
		$this->config->set_item ( 'realtimeresult_period_data', $this->input->post ( 'realtimeresult_period_data' ) );
		
		// pictures sizes
		$this->config->set_item ( 'image_thumb_size', $this->input->post ( 'image_thumb_size' ) );
		$this->config->set_item ( 'image_medium_size', $this->input->post ( 'image_medium_size' ) );
		$this->config->set_item ( 'image_big_size', $this->input->post ( 'image_big_size' ) );
		
		$this->config->set_item ( 'color_pourcentage_1stlever', $this->input->post ( 'color_pourcentage_1stlever' ) );
		$this->config->set_item ( 'color_pourcentage_2ndlever', $this->input->post ( 'color_pourcentage_2ndlever' ) );
		$this->config->set_item ( 'color_pourcentage_3rdlever', $this->input->post ( 'color_pourcentage_3rdlever' ) );
		
		$this->config->set_item ( 'language', $this->input->post ( 'language' ) );
		$this->config->set_item ( 'language_abbr', array_search ( $this->input->post ( 'language' ), $raw_conf ['lang_uri_abbr'] ) );
		$app_logo = $this->input->post ( 'app_logo' );
		$app_country_map = $this->input->post ( 'app_country_map' );
		
		if ($_FILES ['app_logo'] ['tmp_name']) {
			delete_files ( FCPATH . 'cside/images/portal/' . $this->config->item ( 'app_logo' ) ); // was an attempt to delete the existing file
			$config ['file_field_name'] = 'app_logo';
			$config ['file_name'] = 'pbf_portal_logo';
			$config ['upload_path'] = FCPATH . 'cside/images/portal/';
			$config ['allowed_types'] = 'jpg|png';
			$config ['overwrite'] = TRUE;
			$config ['remove_spaces'] = TRUE;
			$config ['max_filename'] = '0';
			$config ['max_size'] = '0'; // use the system limit... see php.ini config in regards
			$config ['max_width'] = '0'; // should be 360 at destination
			$config ['max_height'] = '0'; // should be 300 at destination
			
			$this->load->library ( 'upload', $config );
			
			if ($this->upload->do_upload ( $config ['file_field_name'] )) {
				$data = $this->upload->data ();
				$this->config->set_item ( 'app_logo', $data ['file_name'] );
			} else {
				$this->upload->display_errors ();
				exit ();
			}
		}
		
		if ($_FILES ['app_country_map'] ['tmp_name']) {
			delete_files ( FCPATH . 'cside/images/portal/' . $this->config->item ( 'app_country_map' ) ); // was an attempt to delete the existing file
			$config ['file_field_name'] = 'app_country_map';
			$config ['file_name'] = 'app_country_map';
			$config ['upload_path'] = FCPATH . 'cside/images/portal/';
			$config ['allowed_types'] = 'gif|jpg|png';
			$config ['overwrite'] = TRUE;
			$config ['remove_spaces'] = TRUE;
			$config ['max_filename'] = '0';
			$config ['max_size'] = '0'; // use the system limit... see php.ini config in regards
			$config ['max_width'] = '0'; // should be 360 at destination
			$config ['max_height'] = '0'; // should be 300 at destination
			
			$this->load->library ( 'upload', $config );
			
			if ($this->upload->do_upload ( $config ['file_field_name'] )) {
				$data = $this->upload->data ();
				$this->config->set_item ( 'app_country_map', $data ['file_name'] );
			} else {
				$this->upload->display_errors ();
				exit ();
			}
		}
		
		$conf_text = "<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');\n\n";
		
		$conf_array = ( array ) $this->config;
		
		foreach ( $conf_array ['config'] as $confkey => $confvar ) {
			$newvalue = "";
			
			if (in_array ( $confkey, array (
					'active_data_tab',
					'active_data_toggle_link',
					'enable_hooks',
					'allow_get_array',
					'enable_query_strings',
					'log_threshold',
					'lang_ignore',
					'show_lang_selector',
					'sess_expire_on_close',
					'sess_encrypt_cookie',
					'sess_use_database',
					'sess_match_ip',
					'sess_match_useragent',
					'cookie_secure',
					'global_xss_filtering',
					'csrf_protection',
					'compress_output',
					'rewrite_short_tags' 
			) )) { // boolean
				
				$newvalue = ($conf_array ['config'] [$confkey] == 1) ? "TRUE;" : "FALSE;";
			} 

			else if (in_array ( $confkey, array (
					'sess_expiration',
					'sess_time_to_update',
					'csrf_expire',
					'num_period_display' 
			) )) { // integer and double
				
				$newvalue = $conf_array ['config'] [$confkey] . ';';
			} 

			elseif (in_array ( $confkey, array (
					'lang_uri_abbr',
					'datafile_template',
					'free_controllers' 
			) )) { // array
				
				foreach ( $confvar as $confvarkey => $confvarvar ) {
					$newvalue .= "'" . $confvarkey . "' => '" . $confvarvar . "', ";
				}
				$newvalue = "array(" . trim ( $newvalue, " ," ) . ");";
			} 

			else { // string
				$newvalue = "'" . $conf_array ['config'] [$confkey] . "';";
			}
			
			$conf_text .= "\$config['" . $confkey . "'] = " . $newvalue . "\n";
		}
		
		if (write_file ( APPPATH . '/config/config.php', $conf_text )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'management_save_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'management_save_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( 'configuration/' );
	}
}
