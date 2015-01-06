<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Dashboard extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( array (
				'dashboard_mdl',
				'report_mdl' 
		) );
		$this->lang->load ( 'datafiles', $this->config->item ( 'language' ) );
		$this->lang->load ( 'files', $this->config->item ( 'language' ) );
		$this->lang->load ( 'dashboard', $this->config->item ( 'language' ) );
		$this->lang->load ( 'auth', $this->config->item ( 'language' ) );
		
		$this->lang->load ( 'acl', $this->config->item ( 'language' ) );
		$this->lang->load ( 'budgets', $this->config->item ( 'language' ) );
		$this->lang->load ( 'cms', $this->config->item ( 'language' ) );
		
		$this->lang->load ( 'exports', $this->config->item ( 'language' ) );
		$this->lang->load ( 'fees', $this->config->item ( 'language' ) );
		
		$this->lang->load ( 'hfrentities', $this->config->item ( 'language' ) );
		$this->lang->load ( 'management', $this->config->item ( 'language' ) );
		$this->lang->load ( 'indicators', $this->config->item ( 'language' ) );
		$this->lang->load ( 'geo', $this->config->item ( 'language' ) );
		$this->lang->load ( 'otheroptions', $this->config->item ( 'language' ) );
	}
	function index() {
		$user_group_id = $this->session->userdata ['usergroup_id'];
		$helpers = $this->dashboard_mdl->get_helpers ( $user_group_id );
		foreach ( $helpers as $helper ) {
			$this->load->helper ( 'dashboard/' . $helper ['helper_name'] );
		}
		
		$next_base_url = array ();
		$next_base_url ['next_base_url'] = '/dashboard/';
		$this->session->set_userdata ( $next_base_url );
		
		if ($this->config->item ( 'debug' ))
			$this->output->enable_profiler ( TRUE );
		
		$business_time = ($this->session->userdata ( 'business_time' ) == '') ? $this->pbf->get_current_business_time () : $this->session->userdata ( 'business_time' );
		$get_data_quarters = $this->dashboard_mdl->get_data_quarter ();
		
		$data ['business_time'] = $business_time;
		
		$data ['data_quarters'] = '';
		
		foreach ( $get_data_quarters as $get_data_quarters_key => $get_data_quarters_val ) {
			
			$data ['data_quarters'] [base_url () . 'dashboard/showquarter/' . $get_data_quarters_val ['datafile_quarter'] . '/' . $get_data_quarters_val ['datafile_year']] = $this->lang->line ( 'app_quarter_' . $get_data_quarters_val ['datafile_quarter'] ) . ' ' . $get_data_quarters_val ['datafile_year'];
		}
		
		$general_compteteness = $this->dashboard_mdl->get_general_completeness ( $business_time );
		
		$data ['dashboard_year'] = $business_time ['year'];
		
		$dynamic_headers = isset ( $general_compteteness [0] ) ? array_keys ( $general_compteteness [0] ) : NULL;
		
		$data ['dynamic_headers'] = isset ( $dynamic_headers ) ? array_slice ( $dynamic_headers, 3 ) : NULL;
		
		$data ['general_compteteness'] = $general_compteteness;
		
		$this->lang->load ( 'dashboard', $this->config->item ( 'language' ) );
		
		$raw_dataentry_types = $this->pbf->get_entities_classes_access ();
		
		$usergeozones = $this->session->userdata ( 'usergeozones' );
		if (($usergeozones = '') || ($usergeozones = null)) {
			$raw_reports = $this->report_mdl->get_reports ( $usergeozones );
		} else 		// acces all reports
		{
			$raw_reports = $this->report_mdl->get_reports_all ();
		}
		
		$dataentry = array ();
		
		$dataentry [] = anchor ( 'datafiles/datamngr/', $this->lang->line ( 'dashb_data_entry' ) );
		$dataentry [] = anchor ( 'report', $this->lang->line ( 'dashb_report' ) );
		
		$dataentry [] = anchor ( 'acl/profile/', $this->lang->line ( 'dashb_profile_upd' ) );
		// $dataentry[] = anchor('#',$this->lang->line('dashb_faq_link'));
		$dataentry [] = anchor ( 'help/', $this->lang->line ( 'dashb_help_link' ) );
		$dataentry [] = anchor ( base_url (), $this->lang->line ( 'dashb_back_to_site' ) );
		// $dataentry[] = anchor('#',$this->lang->line('dashb_about_application'));
		
		$data ['dataentry'] = $dataentry;
		
		$data ['page'] = 'dashboard';
		
		$k = 1;
		foreach ( $helpers as $helper ) {
			$data ['helpers'] [$k] ['display'] = call_user_func ( $helper ['helper_name'] );
			$data ['helpers'] [$k] ['details'] = $helper;
			$k ++;
		}
		
		$this->load->view ( 'body', $data );
	}
	function log_details() {
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		$data = $this->dashboard_mdl->get_general_logs_data ( $preps ['offset'], $preps ['terms'] );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['log_id'] = $k + $preps ['offset'] + 1;
			if (! $this->lang->line ( $data ['list'] [$k] ['event'] ) == '') {
				$data ['list'] [$k] ['event'] = $this->lang->line ( $data ['list'] [$k] ['event'] );
			}
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'dashb_logs_users' ),
				$this->lang->line ( 'dashb_logs_date' ),
				$this->lang->line ( 'dashb_logs_descr' ) 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'dashb_logs_details_list' ) . ' - ' . ' [' . $data ['records_num'] . ']';
		$data ['rec_filters'] = $this->pbf->get_filters ( array (
				'user_fullname',
				'date_from_to',
				'event_type' 
		) );
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	function completeness_fosa() {
		$geozone_id = $this->input->post ( 'id' );
		$category = $this->input->post ( 'year' ); // utilise le meme code que data completeness d'`ou l utilisation des paramettre de data completeness (year) c'est pas clean mais :)
		
		$sql = "SELECT * FROM `pbf_entities`,pbf_geozones,pbf_entitytypes WHERE pbf_entitytypes.entity_type_id=pbf_entities.entity_type  AND pbf_entitytypes.entity_class_id='1' AND entity_geozone_id IN ( select geozone_id  from pbf_geozones where geozone_parentid=$geozone_id  AND geozone_active=1) AND entity_geozone_id=geozone_id   AND entity_active=1 ORDER BY geozone_name ASC, entity_name ASC";
		$entites = $this->db->query ( $sql )->result_array ();
		
		echo "<div class='missing_compl_fosa'>
		<div style='text-align:right;'> <a class='close_detail' href='#' style='font-size:1.5em; color:red'>X</a></div>
		<table><th>FOSA</th><th>District</th>";
		foreach ( $entites as $key => $value ) {
			$ligne = "<tr>
				<td>" . anchor ( '/hfrentities/editentity/' . $value ['entity_id'], $value ['entity_name'] ) . "</td>
				<td>" . $value ['geozone_name'] . "</td>
				</tr>
		";
			if ($category == 'geo') {
				if (($value ['entity_geo_lat'] != 0) and ($value ['entity_geo_long'] != 0)) {
				} else {
					echo $ligne;
				}
			}
			if ($category == 'photo') {
				
				if (($value ['entity_picturepath'] == NULL)) 

				{
					echo $ligne;
				} else {
					$pictures = scandir ( FCPATH . 'cside/images/portal/' );
					$NomImage = $value ['entity_picturepath'] . '_big.jpg';
					
					if (in_array ( $NomImage, $pictures )) {
					} else {
						echo $ligne;
					}
				}
			}
			if ($category == 'pop') {
				if (($value ['entity_pop'])) {
				} else {
					echo $ligne;
				}
			}
			
			if ($category == 'status') {
				if (($value ['entity_status'])) {
				} else {
					echo $ligne;
				}
			}
			
			if ($category == 'resp') {
				if (($value ['entity_responsible_name'] != NULL)) {
				} else {
					echo $ligne;
				}
			}
			
			if ($category == 'mail') {
				if (($value ['entity_responsible_email'] != NULL)) {
				} else {
					echo $ligne;
				}
			}
		}
		echo "</table>
		<script>
		$('.close_detail').click(function(){
			$('.plus').removeClass('col_gris');
			$('.detailsRow').hide();

		});
		</script>
		</div>";
	}
	function completeness() {
		$this->load->model ( 'files_mdl' );
		
		$file_type = $this->files_mdl->get_file_type ( $this->input->post ( 'id' ) );
		
		$missing_compteteness = $this->dashboard_mdl->get_missing_completeness ( $this->input->post ( 'id' ), $this->input->post ( 'month' ), $this->input->post ( 'year' ) );
		
		if (! empty ( $missing_compteteness )) {
			// distinct zones
			
			foreach ( $missing_compteteness as $missing ) {
				$geo_zones [] = $missing ['geozone_name'];
			}
			
			$geo_zones = array_unique ( $geo_zones );
			
			echo '<script>$(document).ready(function(){$("#vtabs_uncompleted").jVertTabs({equalHeights: true});});</script>
	<link rel="stylesheet" type="text/css" href="' . $this->config->item ( 'base_url' ) . 'cside/css/jquery.jverttabs.css" />
	<div style="font-size:1.5em;text-align:right;"><a href="#" style="color:red;" class="close_detail">X</a></div>
	<div id="vtabs_uncompleted">
	<div>
		<ul>';
			foreach ( $geo_zones as $geo_zone ) {
				echo '<li><a href="#vtabs_' . strtolower ( str_replace ( ' ', '_', $geo_zone ) ) . '">' . $geo_zone . '</a></li>';
			}
			echo '</ul></div><div>';
			
			foreach ( $geo_zones as $geo_zone ) {
				echo '<div id="#vtabs_' . strtolower ( str_replace ( ' ', '_', $geo_zone ) ) . '">';
				$current_zone = array ();
				$acol = 1;
				foreach ( $missing_compteteness as $missing ) {
					if (in_array ( $geo_zone, $missing )) {
						unset ( $missing ['geozone_id'] );
						unset ( $missing ['geozone_name'] );
						unset ( $missing ['entity_address'] );
						$missing ['entity_responsible_name'] = empty ( $missing ['entity_responsible_email'] ) ? $missing ['entity_responsible_name'] : mailto ( $missing ['entity_responsible_email'], $missing ['entity_responsible_name'] );
						unset ( $missing ['entity_responsible_email'] );
						// $missing['entity_name'] = anchor('datafiles/newfile/0/'.$missing['entity_id'].'/'.$this->input->post('month').'/'.$this->input->post('year').'/'.$this->input->post('id'),$missing['entity_name'], 'onClick="return dirking_warn(\''.$file_type['filetype_name'].'\',\''.$missing['entity_name'].'\',\''.$this->lang->line('app_month_'.$this->input->post('month')).'\',\''.$this->input->post('year').'\');"');
						$missing ['entity_name'] = anchor ( 'datafiles/newfile/0/' . $missing ['entity_id'] . '/' . $this->input->post ( 'month' ) . '/' . $this->input->post ( 'year' ) . '/' . $this->input->post ( 'id' ), $missing ['entity_name'], 'onClick="return dirking_warn(\'' . $this->lang->line ( 'filetype_ky_' . $file_type ['filetype_id'] ) . '\',\'' . $missing ['entity_name'] . '\',\'' . $this->lang->line ( 'app_month_' . $this->input->post ( 'month' ) ) . '\',\'' . $this->input->post ( 'year' ) . '\');"' );
						
						$missing ['entity_id'] = $acol;
						$current_zone [] = $missing;
						$acol ++;
					}
				}
				array_unshift ( $current_zone, array (
						'#',
						$this->lang->line ( 'dashb_entity_name' ),
						$this->lang->line ( 'dashb_responsible_name' ) 
				) );
				$tmpl = array (
						'table_open' => '<table border="0" cellpadding="1" cellspacing="0" class="innertable">',
						'row_start' => '<tr class="even">',
						'row_end' => '</tr>',
						'row_alt_start' => '<tr class="odd">',
						'row_alt_end' => '</tr>',
						'table_close' => '</table>' 
				);
				$this->table->set_template ( $tmpl );
				echo $this->table->generate ( $current_zone );
				echo '</div>';
			}
			echo '</div></div>';
			
			echo "
	<script>
		$('.close_detail').click(function(){
			$('.plus').removeClass('col_gris');
			$('.detailsRow').hide();

		});
		</script>
	";
		} else {
			echo $this->lang->line ( 'dashb_ajax_failure' );
		}
	}
	function showquarter($quarter, $year) {
		$business_time = array (
				'quarter' => $quarter,
				'months' => $this->pbf->get_monthsBy_quarter ( $quarter ),
				'year' => $year 
		);
		
		$this->session->set_userdata ( array (
				'business_time' => $business_time 
		) );
		
		redirect ( 'dashboard/' );
	}
	function deletemessage($message_id) {

		$this->dashboard_mdl->check_message ( $message_id );
		
		redirect ( 'dashboard/' );
	}
}
