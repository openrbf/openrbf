<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Pbf {
	public function __construct() {
		$this->CI = & get_instance ();
		
		$this->CI->load->model ( 'pbf_mdl' );
		$this->CI->load->model ( 'geo_mdl' );
		$this->CI->load->model ( 'cms_mdl' );
		$this->menus = $this->get_menus ();
		
		if (! in_array ( $this->CI->router->fetch_class (), $this->CI->config->item ( 'free_controllers' ) )) {
			$this->check_authorisation ();
		}
	}
	function get_menus() {
		$menus = $this->CI->pbf_mdl->get_menus ();
		
		$group_menus = $this->CI->session->userdata ( 'usergroupsrules' );
		
		$usermenus = array ();
		
		foreach ( $menus as $menu ) {
			
			if ($menu ['menu_security'] == 'open') {
				
				$usermenus [] = $menu;
			} elseif (! empty ( $group_menus ) && ($menu ['menu_security'] != 'open')) {
				
				if (in_array ( $menu ['menu_controller'], $group_menus )) {
					
					$usermenus [] = $menu;
				}
			}
		}
		
		return $usermenus;
	}
	function get_child_menus($menu_id, $menu_section, $menu_type) {
		$menus = $this->menus;
		
		$child_menus = array ();
		
		foreach ( $menus as $menu ) {
			
			if (($menu ['menu_parentid'] == $menu_id) && ($menu ['menu_appsection'] == $menu_section) && ($menu ['menu_type'] == $menu_type)) {
				
				$child_menus [] = anchor ( $menu ['menu_controller'], $this->CI->lang->line ( 'app_menu_' . $menu ['menu_id'] ) );
			}
		}
		
		return $child_menus;
	}
	function get_donor_name($idconfig = null) {
		$donor_name = $this->CI->pbf_mdl->get_donor_name ( $idconfig );
		return $donor_name ['donor_name'];
	}
	function build_menu($menu_section, $menu_type) {
		$this->CI->lang->load ( 'hfrentities', $this->CI->config->item ( 'language' ) );
		
		$available_menu = array ();
		
		$menus = $this->menus;
		
		switch ($menu_type) {
			
			case '0' : // main menu
				
				if ($menu_section == '0') { // back end
					
					foreach ( $menus as $menu ) {
						
						if ($menu ['menu_type'] == '0' && $menu ['menu_appsection'] == '0') {
							
							$child_menus = $this->get_child_menus ( $menu ['menu_id'], $menu_section, '3' );
							
							if (empty ( $child_menus )) {
								
								if ($menu ['menu_autodrop'] == 'entity_class') { // tentative autodrops
									
									$dataentry_raw = $this->get_entities_classes_access ();
									
									$dataentry = array ();
									foreach ( $dataentry_raw as $key ) {
										$dataentry [] = anchor ( $menu ['menu_controller'] . 'datafiles/' . $key ['entity_class_id'], $this->CI->lang->line ( 'etty_cls_ky_' . $key ['entity_class_id'] ) . ' (' . $this->CI->lang->line ( 'etty_cls_abbrv_ky_' . $key ['entity_class_id'] ) . ')' );
									}
									$available_menu [anchor ( $menu ['menu_controller'], $this->CI->lang->line ( 'app_menu_' . $menu ['menu_id'] ) )] = $dataentry;
								} else {
									
									$available_menu [] = anchor ( $menu ['menu_controller'], $this->CI->lang->line ( 'app_menu_' . $menu ['menu_id'] ) );
								}
							} else {
								
								$available_menu [anchor ( $menu ['menu_controller'], $this->CI->lang->line ( 'app_menu_' . $menu ['menu_id'] ) )] = $child_menus;
							}
						}
					}
				} else { // front end
				}
				
				break;
		}
		return $available_menu;
	}
	function get_front_menu() {
		$responsive_menu ['/home/'] = $this->CI->lang->line ( 'app_mainmenu_tab_home' );
		$responsive_menu ['/about/'] = $this->CI->lang->line ( 'app_mainmenu_tab_about' );
		$responsive_menu ['/data/'] = $this->CI->lang->line ( 'app_mainmenu_tab_data' );
		$responsive_menu ['/documents/'] = $this->CI->lang->line ( 'app_mainmenu_tab_documents' );
		$responsive_menu ['/articles/'] = $this->CI->lang->line ( 'app_mainmenu_tab_news' );
		$responsive_menu ['/auth/'] = $this->CI->lang->line ( 'app_mainmenu_tab_management' );
		
		return $responsive_menu;
	}
	function set_asset_access($asset_id, $asset_link, $asset_access) {
		return $this->CI->pbf_mdl->set_asset_access ( $asset_id, $asset_link, $asset_access );
	}
	function set_translation($text_settings, $lang_file) { // array of (text and text_key then) lang file
		$this->CI->load->helper ( 'file' );
		
		$language_path = APPPATH . 'language/' . $this->CI->config->item ( 'language' ) . '/' . $lang_file . '_lang.php';
		$language = file ( $language_path );
		
		// Remove closing PHP tag if it exists - allows for easy addition of additonal lines
		if ($language && mb_strpos ( $language [count ( $language ) - 1], '?>' ) !== FALSE) {
			unset ( $language [count ( $language ) - 1] );
		}
		
		foreach ( $language as $line_nbr => $line ) {
			
			foreach ( $text_settings as $text_setting ) {
				
				if (! is_array ( $text_setting )) {
					
					if (mb_stripos ( $line, "'" . $text_settings ['text_key'] . "'" ) > 0) {
						
						unset ( $language [$line_nbr] );
					}
				} 

				else {
					
					foreach ( $text_setting as $low_setting ) {
						
						if (mb_stripos ( $line, "'" . $text_setting ['text_key'] . "'" ) > 0) {
							
							unset ( $language [$line_nbr] );
						}
					}
				}
			}
		}
		
		// now start adding new text settings
		foreach ( $text_settings as $key => $text_setting ) {
			
			if (! is_array ( $text_setting )) {
				
				if (! is_null ( $text_settings ['text'] ) && $key == 'text_key') {
					
					$language [] = '$lang[\'' . $text_settings ['text_key'] . '\'] = \'' . addslashes ( $text_settings ['text'] ) . '\';';
				}
			} else {
				
				foreach ( $text_setting as $key => $low_setting ) {
					
					if (! is_null ( $text_setting ['text'] ) && $key == 'text_key') {
						
						$language [] = '$lang[\'' . $text_setting ['text_key'] . '\'] = \'' . addslashes ( $text_setting ['text'] ) . '\';';
					}
				}
			}
		}
		
		$language_str = "";
		
		// Clean up new line characters from textarea inputs
		foreach ( $language as $line_number => $line ) {
			$language [$line_number] = str_replace ( "\n", '', $line );
			$language [$line_number] .= "\n";
			$language_str .= $language [$line_number];
		}
		
		if (! write_file ( $language_path, $language_str )) {
			return false;
		} else {
			return true;
		}
	}
	function get_district_qualities_excel($district_id, $parent = TRUE) {
		$entity_types = $this->CI->pbf_mdl->get_entity_types ( 1 );
		$periods = $this->get_last_periods ( $this->CI->config->item ( 'num_period_display' ) );
		
		$rows = array ();
		
		foreach ( $entity_types as $entity_type ) {
			
			$row = $this->CI->pbf_mdl->get_district_qualities ( $district_id, $periods, $entity_type ['entity_type_id'], $parent );
			
			if (! empty ( $row )) {
				foreach ( $row as $k => $r ) {
					$row [$k] ['entity_name'] = $r ['entity_name'];
					unset ( $row [$k] ['entity_id'] );
				}
				$rows [$entity_type ['entity_type_name']] = $row;
			}
		}
		
		return $rows;
	}
	function get_district_inf_excel($district_id, $parent = FALSE) {
		$entity_types = $this->CI->pbf_mdl->get_entity_types ( 1 );
		$periods = $this->get_last_periods ( $this->CI->config->item ( 'num_period_display' ) );
		
		$rows = array ();
		
		foreach ( $entity_types as $entity_type ) {
			
			$row = $this->CI->pbf_mdl->get_district_qualities ( $district_id, $periods, $entity_type ['entity_type_id'], $parent );
			
			if (! empty ( $row )) {
				foreach ( $row as $k => $r ) {
					$row [$k] ['entity_name'] = $r ['entity_name'];
					unset ( $row [$k] ['entity_id'] );
				}
				$rows [$entity_type ['entity_type_name']] = $row;
			}
		}
		
		return $rows;
	}
	function check_authorisation() {
		$requ_resource = explode ( '/', ltrim ( uri_string (), '/' ) );
		
		$requ_resource = array_unique ( array_slice ( $requ_resource, 0, 2 ) );
		
		if (isset ( $requ_resource [1] ) && is_numeric ( $requ_resource [1] )) {
			unset ( $requ_resource [1] );
		}
		
		$requ_resource = implode ( '/', $requ_resource ) . '/';
		
		$user_id = $this->CI->session->userdata ( 'user_id' );
		
		if (isset ( $user_id ) && ! empty ( $user_id ) && ! is_null ( $user_id )) {
			
			if (! in_array ( $requ_resource, $this->CI->session->userdata ( 'usergroupsrules' ) )) {
				
				$this->CI->session->set_flashdata ( array (
						'mod_clss' => 'warning',
						'mod_msg' => $this->CI->lang->line ( 'app_badiest_message' ) 
				) );
				
				redirect ( $this->CI->session->userdata ( 'afterlogin' ) );
			}
		} else {
			
			$this->CI->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->CI->lang->line ( 'app_notlogged_message' ) 
			) );
			
			redirect ( 'auth/' );
		}
	}
	function set_eventlog($event_log_mess, $publish) {
		$eventlog ['session_id'] = $this->CI->session->userdata ( 'session_id' );
		$eventlog ['uri_string'] = $this->CI->uri->uri_string ();
		$eventlog ['event'] = ($event_log_mess == '') ? $this->CI->session->userdata ( 'flash:new:mod_msg' ) : $event_log_mess;
		$eventlog ['user_id'] = $this->CI->session->userdata ( 'user_id' );
		$eventlog ['ip_address'] = $this->CI->session->userdata ( 'ip_address' );
		$eventlog ['user_agent'] = $this->CI->session->userdata ( 'user_agent' );
		$eventlog ['publish'] = $publish;
		$this->CI->pbf_mdl->set_eventlog ( $eventlog );
	}
	function get_front_data($zone_id, $entity_id, $table_field, $content_type) {
		
		// function get_last_quarters_zone($number,$zones){
		if ($entity_id == '') {
			
			$entity_id = array ();
			$districts = $zone_id;
			
			$zones = $this->CI->geo_mdl->get_zones_by_parent ( $zone_id );
			// if($zones !=''){
			if (! empty ( $zones )) {
				$districts = $zones;
				
				foreach ( $districts as $zone ) {
					
					$entities = $this->CI->pbf_mdl->get_entities_By_zone ( $zone ['geozone_id'], '1', '', '' );
					foreach ( $entities as $entity ) {
						$entity_id [] = $entity ['entity_id'];
					}
				}
			} else {
				
				$entities = $this->CI->pbf_mdl->get_entities_By_zone ( $zone_id, '1', '', '' );
				foreach ( $entities as $entity ) {
					$entity_id [] = $entity ['entity_id'];
				}
			}
			
			if (empty ( $entity_id )) {
				
				$periods = $this->get_last_quarters ( $this->CI->config->item ( 'num_period_display' ) );
				$trendperiod = $this->get_last_quarters ( 2 );
				$entity_id = '';
			} else {
				$periods = $this->get_last_quarters_zone ( $this->CI->config->item ( 'num_period_display' ), $entity_id ); // because frontdata only linked to entity_id
				$trendperiod = $this->get_last_quarters_zone ( 2, $entity_id );
				$entity_id = '';
			}
		} else {
			$periods = $this->get_last_quarters_zone ( $this->CI->config->item ( 'num_period_display' ), $entity_id ); // because frontdata only linked to entity_id
			$trendperiod = $this->get_last_quarters_zone ( 2, $entity_id );
		}
		
		// $periods = $this->get_last_quarters($this->CI->config->item('num_period_display'));
		
		if (isset ( $periods )) { // vs !empty($periods)
			
			$front_data ['pbf_data'] = $this->get_featured_indic ( $periods, $zone_id, $entity_id, '1', $table_field, $content_type );
			
			$front_data ['pbf_qlt_data'] = $this->clean_table_for_front ( $front_data ['pbf_qlt_data'] );
			$front_data ['pbf_data_ecd'] = $this->clean_table_for_front ( $front_data ['pbf_data_ecd'] );
			$front_data ['pbf_data_payment_fosa'] = $this->clean_table_for_front ( $front_data ['pbf_data_payment_fosa'] );
			$front_data ['pbf_data_payment_ecd'] = $this->clean_table_for_front ( $front_data ['pbf_data_payment_ecd'] );
			
			$front_data ['pbf_data'] ['pbf_data_slice'] = array_slice ( $front_data ['pbf_data'] ['pbf_data'], 0, $front_data ['pbf_data'] ['tot_featured'] + 1 );

		}
		
		return $front_data;
	}
	function get_data($zone_id, $entity_id, $table_field, $content_type) {
		

		if ($entity_id == '') {
			
			$entity_id = array ();
			$districts = $zone_id;
			
			$zones = $this->CI->geo_mdl->get_zones_by_parent ( $zone_id );
			
			if (! empty ( $zones )) {
				$districts = $zones;
				
				foreach ( $districts as $zone ) {
					
					$entities = $this->CI->pbf_mdl->get_entities_By_zone ( $zone ['geozone_id'], '1', '', '' );
					
					foreach ( $entities as $entity ) {
						$entity_id [] = $entity ['entity_id'];
					}
				}
			} else {
				
				$entities = $this->CI->pbf_mdl->get_entities_By_zone ( $zone_id, '1', '', '' );
				
				foreach ( $entities as $entity ) {
					$entity_id [] = $entity ['entity_id'];
				}
			}
			
			if (empty ( $entity_id )) {
				
				$periods = $this->get_last_periods ( $this->CI->config->item ( 'num_period_display' ), $this->CI->config->item ( 'period_type' ) );
				
				if (($this->CI->config->item ( 'period_type' ) == 'quarter') && (count ( $periods ) < $this->CI->config->item ( 'min_period' ))) {
					$periods = $this->get_last_periods ( $this->CI->config->item ( 'num_period_display' ), 'month' );
				}

				
				$entity_id = '';
			} else {
				
				$periods = $this->get_last_periods_zone ( $this->CI->config->item ( 'num_period_display' ), $this->CI->config->item ( 'period_type' ), $entity_id );
				if (($this->CI->config->item ( 'period_type' ) == 'quarter') && (count ( $periods ) < $this->CI->config->item ( 'min_period' ))) {
					$periods = $this->get_last_periods_zone ( $this->CI->config->item ( 'num_period_display' ), 'month', $entity_id );
				}
				$entity_id = '';
			}
		} else {
			
			$periods = $this->get_last_periods_zone ( $this->CI->config->item ( 'num_period_display' ), $this->CI->config->item ( 'period_type' ), $entity_id );
			if (($this->CI->config->item ( 'period_type' ) == 'quarter') && (count ( $periods ) < $this->CI->config->item ( 'min_period' ))) {
				$periods = $this->get_last_periods_zone ( $this->CI->config->item ( 'num_period_display' ), 'month', $entity_id );
			}
		}
		
		
		if (isset ( $periods )) { // vs !empty($periods)
			
			$front_data ['pbf_data'] = $this->get_featured_indic ( $periods, $zone_id, $entity_id, '1', $table_field, $content_type );
			
			if (! empty ( $front_data ['pbf_data'] ['pbf_data'] )) {
				array_unshift ( $front_data ['pbf_data'] ['pbf_data'], array_keys ( $front_data ['pbf_data'] ['pbf_data'] [0] ) );
			}
			
			$front_data ['pbf_data'] ['pbf_data_slice'] = array_slice ( $front_data ['pbf_data'] ['pbf_data'], 0, $front_data ['pbf_data'] ['tot_featured'] + 1 );
		}
		
		return $front_data;
	}
	function get_data_export($zone_id, $entity_id, $table_field, $content_type) {
		
		if ($entity_id == '') {
			
			$entity_id = array ();
			$districts = $zone_id;
			
			$zones = $this->CI->geo_mdl->get_zones_by_parent ( $zone_id );
			

			if (! empty ( $zones )) {
				$districts = $zones;
				
				foreach ( $districts as $zone ) {
					
					$entities = $this->CI->pbf_mdl->get_entities_By_zone ( $zone ['geozone_id'], '1', '', '' );
					
					foreach ( $entities as $entity ) {
						$entity_id [] = $entity ['entity_id'];
					}
				}
			} else {
				
				$entities = $this->CI->pbf_mdl->get_entities_By_zone ( $zone_id, '1', '', '' );
				
				foreach ( $entities as $entity ) {
					$entity_id [] = $entity ['entity_id'];
				}
			}
			
			if (empty ( $entity_id )) {
				
				$periods = $this->get_last_periods ( $this->CI->config->item ( 'num_period_display' ), $this->CI->config->item ( 'period_type' ) );
				
				if (($this->CI->config->item ( 'period_type' ) == 'quarter') && (count ( $periods ) < $this->CI->config->item ( 'min_period' ))) {
					$periods = $this->get_last_periods ( $this->CI->config->item ( 'num_period_display' ), 'month' );
				}
				
			
				$entity_id = '';
			} else {
				
				$periods = $this->get_last_periods_zone ( $this->CI->config->item ( 'num_period_display' ), $this->CI->config->item ( 'period_type' ), $entity_id );
				if (($this->CI->config->item ( 'period_type' ) == 'quarter') && (count ( $periods ) < $this->CI->config->item ( 'min_period' ))) {
					$periods = $this->get_last_periods_zone ( $this->CI->config->item ( 'num_period_display' ), 'month', $entity_id );
				}
				
				$entity_id = '';
			}
		} else {
			
			$periods = $this->get_last_periods_zone ( $this->CI->config->item ( 'num_period_display' ), $this->CI->config->item ( 'period_type' ), $entity_id );
			if (($this->CI->config->item ( 'period_type' ) == 'quarter') && (count ( $periods ) < $this->CI->config->item ( 'min_period' ))) {
				$periods = $this->get_last_periods_zone ( $this->CI->config->item ( 'num_period_display' ), 'month', $entity_id );
			}
		}
		
		if (isset ( $periods )) { // vs !empty($periods)
			
			$front_data ['pbf_data'] = $this->get_featured_indic_export ( $periods, $zone_id, $entity_id, '1', $table_field, $content_type );
			
			if (! empty ( $front_data ['pbf_data'] ['pbf_data'] )) {
				array_unshift ( $front_data ['pbf_data'] ['pbf_data'], array_keys ( $front_data ['pbf_data'] ['pbf_data'] [0] ) );
			}
			
			$front_data ['pbf_data'] ['pbf_data_slice'] = array_slice ( $front_data ['pbf_data'] ['pbf_data'], 0, $front_data ['pbf_data'] ['tot_featured'] + 1 );
		}
		
		return $front_data;
	}
	function get_runnable_pbf_script($year, $quarter) {
		$month = $this->get_monthsBy_quarter ( $quarter );
		
		$sql = $this->CI->pbf_mdl->get_runnable_script ( $year, $month [2] );
		
		$function_str = '';
		
		foreach ( $sql as $k => $v ) {
			
			if (in_array ( $sql [$k] ['computation_calculation_basis'], array (
					'normal_pbfbusiness_bonus',
					'normal_pbfbusiness',
					'normal_pbfbusiness_half_subsidies' 
			) )) {
				$function_str .= " if((\$entity_class_id==" . $sql [$k] ['computation_entity_class_id'] . ")";
				$function_str .= empty ( $sql [$k] ['computation_entity_type_id'] ) ? '' : " && (\$entity_type_id==" . $sql [$k] ['computation_entity_type_id'] . ")";
				$function_str .= empty ( $sql [$k] ['computation_entity_group_id'] ) ? '' : " && (\$entity_group_id==" . $sql [$k] ['computation_entity_group_id'] . ")";
				$function_str .= empty ( $sql [$k] ['computation_geozone_id'] ) ? '' : " && (\$geozone_id==" . $sql [$k] ['computation_geozone_id'] . ")";
			}
			
			switch ($sql [$k] ['computation_calculation_basis']) {
				
				case 'normal_pbfbusiness_bonus' :
					
					$function_str .= " && (\$quality_score" . $sql [$k] ['computation_score_condition_one'] . $sql [$k] ['computation_score_fact_one'] . ") && (\$quality_score" . $sql [$k] ['computation_score_condition_two'] . $sql [$k] ['computation_score_fact_two'] . ")";
					
					$function_str .= "){";
					if ($sql [$k] ['consider_score'] != "0") {
						$function_str .= "\$amount=round((\$tot_subsides*(" . $sql [$k] ['fav_action'] . "/100))*(\$quality_score/100));";
					} else {
						$function_str .= "\$amount=round(\$tot_subsides*(" . $sql [$k] ['fav_action'] . "/100));";
					}
					$function_str .= "}";
					break;
				
				case 'normal_pbfbusiness' :
					
					$function_str .= " && (\$quality_score" . $sql [$k] ['computation_score_condition_one'] . $sql [$k] ['computation_score_fact_one'] . ") && (\$quality_score" . $sql [$k] ['computation_score_condition_two'] . $sql [$k] ['computation_score_fact_two'] . ")";
					
					$function_str .= "){";
					if ($sql [$k] ['consider_score'] != "0") {
						$function_str .= "\$amount=round((\$tot_subsides*(" . $sql [$k] ['fav_action'] . "/100))*(\$quality_score/100));";
					} else {
						$function_str .= "\$amount=round(\$tot_subsides*(" . $sql [$k] ['fav_action'] . "/100));";
					}
					$function_str .= "\$amount += \$tot_subsides;}";
					break;
				case 'normal_pbfbusiness_half_subsidies' :
					
					$function_str .= " && (\$quality_score" . $sql [$k] ['computation_score_condition_one'] . $sql [$k] ['computation_score_fact_one'] . ") && (\$quality_score" . $sql [$k] ['computation_score_condition_two'] . $sql [$k] ['computation_score_fact_two'] . ")";
					
					$function_str .= "){";
					if ($sql [$k] ['consider_score'] != "0") {
						$function_str .= "\$tot_subsides = round(\$tot_subsides/2);";
						$function_str .= "\$amount=round((\$tot_subsides*(" . $sql [$k] ['fav_action'] . "/100))*(\$quality_score/100));";
					} else {
						$function_str .= "\$amount=round(\$tot_subsides*(" . $sql [$k] ['fav_action'] . "/100));";
					}
					$function_str .= "\$amount += \$tot_subsides;}";
					break;
			}
		}
		
		return $function_str .= " return \$amount;";
	}
	function get_runnable_script($year, $quarter) {
		$month = $this->get_monthsBy_quarter ( $quarter );
		$sql = $this->CI->pbf_mdl->get_runnable_script ( $year, $month [2] );
		
		$function_str = '';
		
		foreach ( $sql as $k => $v ) {
			
			$function_str .= " if((\$entity_class_id==" . $sql [$k] ['computation_entity_class_id'] . ")";
			$function_str .= empty ( $sql [$k] ['computation_entity_type_id'] ) ? '' : " && (\$entity_type_id==" . $sql [$k] ['computation_entity_type_id'] . ")";
			$function_str .= empty ( $sql [$k] ['computation_entity_group_id'] ) ? '' : " && (\$entity_group_id==" . $sql [$k] ['computation_entity_group_id'] . ")";
			$function_str .= empty ( $sql [$k] ['computation_geozone_id'] ) ? '' : " && (\$geozone_id==" . $sql [$k] ['computation_geozone_id'] . ")";
			
			switch ($sql [$k] ['computation_calculation_basis']) {
				
				case 'normal_pbfbusiness_bonus' :
					
					$function_str .= " && (\$quality_score" . $sql [$k] ['computation_score_condition_one'] . $sql [$k] ['computation_score_fact_one'] . ") && (\$quality_score" . $sql [$k] ['computation_score_condition_two'] . $sql [$k] ['computation_score_fact_two'] . ")";
					
					$function_str .= "){";
					if ($sql [$k] ['consider_score'] != "0") {
						$function_str .= "\$amount=round((\$tot_subsides*(" . $sql [$k] ['fav_action'] . "/100))*(\$quality_score/100));";
					} else {
						$function_str .= "\$amount=round(\$tot_subsides*(" . $sql [$k] ['fav_action'] . "/100));";
					}
					$function_str .= "}";
					break;
				
				case 'normal_pbfbusiness' :
					
					$function_str .= " && (\$quality_score" . $sql [$k] ['computation_score_condition_one'] . $sql [$k] ['computation_score_fact_one'] . ") && (\$quality_score" . $sql [$k] ['computation_score_condition_two'] . $sql [$k] ['computation_score_fact_two'] . ")";
					
					$function_str .= "){";
					if ($sql [$k] ['consider_score'] != "0") {
						$function_str .= "\$amount=round((\$tot_subsides*(" . $sql [$k] ['fav_action'] . "/100))*(\$quality_score/100));";
					} else {
						$function_str .= "\$amount=round(\$tot_subsides*(" . $sql [$k] ['fav_action'] . "/100));";
					}
					$function_str .= "\$amount += \$tot_subsides;}";
					break;
				case 'normal_pbfbusiness_half_subsidies' :
					
					$function_str .= " && (\$quality_score" . $sql [$k] ['computation_score_condition_one'] . $sql [$k] ['computation_score_fact_one'] . ") && (\$quality_score" . $sql [$k] ['computation_score_condition_two'] . $sql [$k] ['computation_score_fact_two'] . ")";
					
					$function_str .= "){";
					if ($sql [$k] ['consider_score'] != "0") {
						$function_str .= "\$tot_subsides = round(\$tot_subsides/2);";
						$function_str .= "\$amount=round((\$tot_subsides*(" . $sql [$k] ['fav_action'] . "/100))*(\$quality_score/100));";
					} else {
						$function_str .= "\$amount=round(\$tot_subsides*(" . $sql [$k] ['fav_action'] . "/100));";
					}
					$function_str .= "\$amount += \$tot_subsides;}";
					break;
				case 'perc_available_budget' :
					
					// get entity available budget..., in this case $tot_subsides is the available budget... great GREAT :)
					$function_str .= "){";
					$function_str .= "\$amount=round(\$tot_subsides*(\$quality_score/100));}";
					
					break;
				case 'regional_avg' :
					
					$regional_avg = $this->get_regional_avg ( $year, $quarter, $sql [$k] ['computation_geozone_id'], $sql [$k] ['computation_entity_group_id'] );
					
					$function_str .= "){";
					
					$function_str .= "\$regional_avg = $regional_avg;";
					
					$function_str .= "if(\$regional_avg > \$tot_subsides ){
				
				\$regional_avg = \$tot_subsides;
				
				}";
					
					$function_str .= "\$amount = \$regional_avg;}";
					
					break;
				
				case 'national_avg' :
					
					$regional_avg = $this->get_regional_avg ( $year, $quarter, '', $sql [$k] ['computation_entity_group_id'] );
					
					$function_str .= "){";
					
					$function_str .= "\$regional_avg = $regional_avg;";
					
					$function_str .= "if(\$regional_avg > \$tot_subsides ){
				
				\$regional_avg = \$tot_subsides;
				
				}";
					
					$function_str .= "\$amount = \$regional_avg;}";
					
					break;
				
				case 'capitated_amount' :
					
					break;
			}
		}
		
		return $function_str .= " return \$amount;";
	}
	function get_runnable_pbf_script_for_indicator($year, $quarter) {
		$month = $this->get_monthsBy_quarter ( $quarter );
		
		// get the runable conditions
		$sql = $this->CI->pbf_mdl->get_runnable_script ( $year, $month [2] );
		
		$function_str = '';
		
		foreach ( $sql as $k => $v ) {
			
			if (in_array ( $sql [$k] ['computation_calculation_basis'], array (
					'normal_pbfbusiness_bonus',
					'normal_pbfbusiness',
					'normal_pbfbusiness_half_subsidies',
					'regional_avg',
					'national_avg' 
			) )) {
				$function_str .= " if((\$entity_group_id==" . $sql [$k] ['computation_entity_group_id'] . ")";
			}
			
			switch ($sql [$k] ['computation_calculation_basis']) {
				
				case 'normal_pbfbusiness_bonus' :
					
					$function_str .= " && (\$quality_score" . $sql [$k] ['computation_score_condition_one'] . $sql [$k] ['computation_score_fact_one'] . ") && (\$quality_score" . $sql [$k] ['computation_score_condition_two'] . $sql [$k] ['computation_score_fact_two'] . ")";
					
					$function_str .= "){";
					if ($sql [$k] ['consider_score'] != "0") {
						$function_str .= "\$amount=round((\$tot_subsides*(" . $sql [$k] ['fav_action'] . "/100))*(\$quality_score/100));";
					} else {
						$function_str .= "\$amount=round(\$tot_subsides*(" . $sql [$k] ['fav_action'] . "/100));";
					}
					$function_str .= "}";
					break;
				
				case 'normal_pbfbusiness' :
					
					$function_str .= " && (\$quality_score" . $sql [$k] ['computation_score_condition_one'] . $sql [$k] ['computation_score_fact_one'] . ") && (\$quality_score" . $sql [$k] ['computation_score_condition_two'] . $sql [$k] ['computation_score_fact_two'] . ")";
					
					$function_str .= "){";
					if ($sql [$k] ['consider_score'] != "0") {
						$function_str .= "\$amount=round((\$tot_subsides*(" . $sql [$k] ['fav_action'] . "/100))*(\$quality_score/100));";
					} else {
						$function_str .= "\$amount=round(\$tot_subsides*(" . $sql [$k] ['fav_action'] . "/100));";
					}
					$function_str .= "\$amount += \$tot_subsides;}";
					break;
				case 'normal_pbfbusiness_half_subsidies' :
					
					$function_str .= " && (\$quality_score" . $sql [$k] ['computation_score_condition_one'] . $sql [$k] ['computation_score_fact_one'] . ") && (\$quality_score" . $sql [$k] ['computation_score_condition_two'] . $sql [$k] ['computation_score_fact_two'] . ")";
					
					$function_str .= "){";
					if ($sql [$k] ['consider_score'] != "0") {
						$function_str .= "\$tot_subsides = round(\$tot_subsides/2);";
						$function_str .= "\$amount=round((\$tot_subsides*(" . $sql [$k] ['fav_action'] . "/100))*(\$quality_score/100));";
					} else {
						$function_str .= "\$amount=round(\$tot_subsides*(" . $sql [$k] ['fav_action'] . "/100));";
					}
					$function_str .= "\$amount += \$tot_subsides;}";
					break;
				
				case 'regional_avg' :
					
					$function_str .= " && (\$quality_score" . $sql [$k] ['computation_score_condition_one'] . $sql [$k] ['computation_score_fact_one'] . ") && (\$quality_score" . $sql [$k] ['computation_score_condition_two'] . $sql [$k] ['computation_score_fact_two'] . ")";
					
					$function_str .= "){";
					
					$function_str .= "\$amount = \$tot_subsides;}";
					break;
				
				case 'national_avg' :
					
					$function_str .= " && (\$quality_score" . $sql [$k] ['computation_score_condition_one'] . $sql [$k] ['computation_score_fact_one'] . ") && (\$quality_score" . $sql [$k] ['computation_score_condition_two'] . $sql [$k] ['computation_score_fact_two'] . ")";
					
					$function_str .= "){";
					
					$function_str .= "\$amount = \$tot_subsides;}";
					break;
			}
		}
		
		return $function_str .= " return \$amount;";
	}
	function get_assoc_group($year, $quarter, $computation_geozone_id, $entity_group_id) {
		$month = $this->get_monthsBy_quarter ( $quarter );
		
		return $this->CI->pbf_mdl->get_assoc_group ( $year, $month [2], $computation_geozone_id, $entity_group_id );
	}
	function calculate_bonus($entity_id, $quality_score, $tot_subsides, $entity_class_id, $entity_type_id, $entity_group_id, $geozone_id, $fn_code) {
		$amount = 0;
		return eval ( $fn_code );
	}
	function calculate_final_payment($quality_score, $tot_subsides, $entity_class_id, $entity_type_id, $entity_group_id, $geozone_id, $fn_code) {
		$amount = 0;
		
		return eval ( $fn_code );
	}
	function calculate_final_indicator_payment($quality_score, $tot_subsides, $entity_group_id, $fn_code) {
		$amount = 0;
		
		return eval ( $fn_code );
	}
	function get_regional_avg($year, $quarter, $computation_geozone_id, $entity_group_id) {
		$ass_group_id = $this->get_assoc_group ( $year, $quarter, $computation_geozone_id, $entity_group_id );
		
		$this->CI->load->model ( 'report_mdl' );
		
		$zone_entities = $this->CI->report_mdl->get_quarterly_consolidated_resumee ( $quarter, $year, $computation_geozone_id, $ass_group_id ['computation_entity_ass_group_id'] );
		
		$fn_script = $this->get_runnable_pbf_script ( $year, $quarter );
		
		foreach ( $zone_entities as $zone_entitie_key => $zone_entitie_val ) {
			
			$dig_quality = $this->CI->report_mdl->get_quality_evaluation ( $year, $quarter, $zone_entitie_val ['entity_id'] );
			
			$regional_payments [] = $this->calculate_final_payment ( $dig_quality ['datafile_total'], $zone_entitie_val ['tot_subsides'], $zone_entitie_val ['entity_class'], $zone_entitie_val ['entity_type'], $zone_entitie_val ['entity_pbf_group_id'], $computation_geozone_id, $fn_script );
		}
		
		return round ( array_sum ( $regional_payments ) / count ( $regional_payments ) );
	}
	function rec_op_icon($operation, $controller = '', $custom_title = '') {
		switch ($operation) {
			case 'delete' :
				$ArrayOptions = array (
						'title' => $this->CI->lang->line ( 'app_mod_record_operation_delete' ),
						'onclick' => "return confirm('" . $this->CI->lang->line ( 'app_mod_delete_confirm' ) . "')" 
				);
				return anchor ( $controller, '<img width="16" height="16" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/delete.png" border="0">', $ArrayOptions );
				break;
			
			case 'edit' :
				$ArrayOptions = array (
						'title' => $this->CI->lang->line ( 'app_mod_record_operation_edit' ) 
				);
				return anchor ( $controller, '<img width="16" height="16" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/edit.png" border="0">', $ArrayOptions );
				break;
			
			case 'copy' :
				$ArrayOptions = array (
						'title' => $this->CI->lang->line ( 'app_mod_record_operation_copy' ) 
				);
				return anchor ( $controller, '<img width="16" height="16" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/copy.png" border="0">', $ArrayOptions );
				break;
			
			case 'open' :
				$ArrayOptions = array (
						'title' => $this->CI->lang->line ( 'app_mod_record_operation_open' ) 
				);
				return anchor ( $controller, '<img width="16" height="16" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/open.png" border="0">', $ArrayOptions );
				break;
			
			case 'publish_1' :
				$ArrayOptions = array (
						'title' => $this->CI->lang->line ( 'app_mod_record_unpublish' ) 
				);
				return anchor ( $controller . '/0', '<img width="16" height="16" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/publish.png" border="0">', $ArrayOptions );
				break;
			
			case 'publish_0' :
				$ArrayOptions = array (
						'title' => $this->CI->lang->line ( 'app_mod_record_publish' ) 
				);
				return anchor ( $controller . '/1', '<img width="16" height="16" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/unpublish.png" border="0">', $ArrayOptions );
				break;
			
			case 'add' :
				return '<img alt="' . $this->CI->lang->line ( 'app_mod_record_operation_add_new' ) . '" width="32" height="32" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/add.png" border="0">';
				break;
			
			case 'synch' :
				return '<img title="' . $this->CI->lang->line ( 'app_mod_record_operation_synch_reproduce' ) . '" alt="' . $this->CI->lang->line ( 'app_mod_record_operation_synch_reproduce' ) . '" width="32" height="32" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/synch.png" border="0">';
				break;
			
			case 'close' :
				return '<img alt="' . $this->CI->lang->line ( 'app_mod_close' ) . '" width="32" height="32" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/close.png" border="0">';
				break;
			
			case 'add_file' :
				return '<img alt="' . $this->CI->lang->line ( 'app_mod_record_operation_add_new' ) . '" width="32" height="32" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/add_file.png" border="0">';
				break;
			
			case 'config' :
				return '<img alt="' . $this->CI->lang->line ( 'app_mod_record_operation_add_new' ) . '" width="32" height="32" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/config.png" border="0">';
				break;
			
			case 'delete_selected' :
				return '<input type="image" name="delete_selected" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/delete_selected.png"  title="' . $this->CI->lang->line ( 'app_mod_record_operation_delete_selected' ) . '" onClick="return submit_delete()">';
				break;
			
			case 'active_1' :
				$ArrayOptions = array (
						'title' => $this->CI->lang->line ( 'app_mod_record_operation_diactivate' ) 
				);
				return anchor ( $controller . '/0', '<img width="16" height="16" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/active.png" border="0">', $ArrayOptions );
				break;
			
			case 'active_0' :
				$ArrayOptions = array (
						'title' => $this->CI->lang->line ( 'app_mod_record_operation_activate' ) 
				);
				return anchor ( $controller . '/1', '<img width="16" height="16" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/diactive.png" border="0">', $ArrayOptions );
				break;
			
			case 'default_0' :
				$ArrayOptions = array (
						'title' => $this->CI->lang->line ( 'app_mod_record_default' ) 
				);
				return anchor ( $controller . '/1', '<img width="16" height="16" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/nodefault.png" border="0">', $ArrayOptions );
				break;
			
			case 'default_1' :
				$ArrayOptions = array (
						'title' => $this->CI->lang->line ( 'app_mod_record_undefault' ) 
				);
				return anchor ( $controller . '/0', '<img width="16" height="16" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/default.png" border="0">', $ArrayOptions );
				break;
			
			case 'small_add' :
				$ArrayOptions = array (
						'title' => $this->CI->lang->line ( 'app_mod_record_operation_add_new' ) 
				);
				return anchor ( $controller, '<img width="16" height="16" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/small_add.png" border="0">', $ArrayOptions );
				break;
			
			case 'download_record' :
				$ArrayOptions = array (
						'title' => $this->CI->lang->line ( 'app_mod_record_operation_download' ) 
				);
				return anchor_popup ( $controller, '<img width="16" height="16" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/download_record.png" border="0">', array (
						'width' => '300',
						'height' => '300' 
				) );
				break;
			
			case 'download_excel' :
				$ArrayOptions = array (
						'title' => 'Download excel file' 
				);
				return anchor_popup ( $controller, '<img width="26" height="26" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/excel_logo.png" border="0">', array (
						'width' => '300',
						'height' => '300' 
				) );
				break;
			case 'home_1' :
				$ArrayOptions = array (
						'title' => $this->CI->lang->line ( 'app_mod_record_operation_diactivate_home' ) 
				);
				return anchor ( $controller . '/0', '<img width="16" height="16" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/active.png" border="0">', $ArrayOptions );
				break;
			
			case 'home_0' :
				$ArrayOptions = array (
						'title' => $this->CI->lang->line ( 'app_mod_record_operation_activate_home' ) 
				);
				return anchor ( $controller . '/1', '<img width="16" height="16" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/diactive.png" border="0">', $ArrayOptions );
				break;
			case 'up' :
				$ArrayOptions = array (
						'title' => 'Promote donor' 
				);
				return anchor ( $controller, '<img width="16" height="16" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/up.png" border="0">', array (
						'width' => '300',
						'height' => '300' 
				) );
				break;
			default :
				$ArrayOptions = array (
						'title' => $custom_title 
				);
				return anchor ( $controller, '<img width="16" height="16" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/icons/' . $operation . '.png" border="0">', $ArrayOptions );
		}
	}
	function prep_listing_terms_uri_keys() {
		
		// empty array for search terms
		$terms = array ();
		
		// offset
		$uri_segment = 2;
		
		// return third URI segment, if no third segment returns ''
		$offset = $this->CI->uri->segment ( $uri_segment, 0 );
		
		// assign eventually posted valued
		$filters_uri = $this->get_filters_uri ( $this->CI->input->post () );
		
		// gets total URI segments
		$total_seg = $this->CI->uri->total_segments ();
		
		// set search params
		
		// enters here only when 'Search' button is pressed or through 'Paging'
		if (! empty ( $filters_uri ) || $total_seg > 2) {
			
			if ($total_seg > 2) { // navigation from paging
				
				$terms = $this->CI->uri->uri_to_assoc ( 2 );
				
				// 'this is the extra segments ariko ziri unprocessed <br>';
				
				$uri_segment = $total_seg;
				$offset = $this->CI->uri->segment ( $total_seg );
				

				
				// echo '<br>Total_seg : '.$total_seg.'<br>';
				
				// When the page is navigated through paging, it enters the condition below
				if (($total_seg % 2) > 0) {
					
					// echo "<br>When the page is navigated through paging, it enters the condition below do not know what is happening here<br>";
					
					// exclude the last array item (i.e. the array key for page number), prepare array for database query
					// $this->terms = array_slice($this->terms, 0 , (floor($total_seg/2)-1));
					
					$offset = 0;
					$uri_segment = $total_seg;
					

				}
				$terms = $this->clean_uri__keys ( $terms ); 
				                                         

				$keys = $this->CI->uri->assoc_to_uri ( $terms );
				
				} 

			else { // navigation through POST search button

				
				$terms = $filters_uri;
				
				$keys = $this->CI->uri->assoc_to_uri ( $terms );
				
			}
		} else { // load data
		      
					$keys = '';
			
			}
		
		return array (
				'offset' => $offset,
				'terms' => $terms,
				'keys' => $keys,
				'uri_segment' => $uri_segment 
		);
	}
	function prep_listing_terms_uri_keys_custom() {
		
		// empty array for search terms
		$terms = array ();
		
		// offset
		$uri_segment = 3;
		
		// return third URI segment, if no third segment returns ''
		$offset = $this->CI->uri->segment ( $uri_segment, 0 );
		
		// assign eventually posted valued
		$filters_uri = $this->get_filters_uri ( $this->CI->input->post () );
		
		// gets total URI segments
		$total_seg = $this->CI->uri->total_segments ();
		
		// set search params
		
		// enters here only when 'Search' button is pressed or through 'Paging'
		if (! empty ( $filters_uri ) || $total_seg > 2) {
			
			if ($total_seg > 2) { // navigation from paging
				
				$terms = $this->CI->uri->uri_to_assoc ( 2 );
				
			
				
				$uri_segment = $total_seg;
				$offset = $this->CI->uri->segment ( $total_seg );
				
	
				if (($total_seg % 2) > 0) {
					
				
					
					$offset = 0;
					$uri_segment = $total_seg;
					
						}
				$terms = $this->clean_uri__keys ( $terms ); 
				                                         
		
				
				$keys = $this->CI->uri->assoc_to_uri ( $terms );
				
			
			} 

			else { // navigation through POST search button
			      
				
				$terms = $filters_uri;
				
				$keys = $this->CI->uri->assoc_to_uri ( $terms );
				
			
			}
		} else { // load data
		      
			
			
			$keys = '';

		}
		
		return array (
				'offset' => $offset,
				'terms' => $terms,
				'keys' => $keys,
				'uri_segment' => $uri_segment 
		);
	}
	function get_filters_uri($post_vars) {
		$processed_post_vars = array ();
		
		if (! empty ( $post_vars )) {
			
			unset ( $post_vars ['submit'] );
			
			if (isset ( $post_vars ['level_0'] )) {
				$this->CI->session->set_userdata ( 'level_0', $post_vars ['level_0'] );
			}
			if (isset ( $post_vars ['geozone_id'] )) {
				$this->CI->session->set_userdata ( 'filtered_geozone_id', $post_vars ['geozone_id'] );
			}
			if (isset ( $post_vars ['entity_id'] )) {
				$this->CI->session->set_userdata ( 'filtered_entity_id', $post_vars ['entity_id'] );
			}
			
			foreach ( $post_vars as $post_vars_key => $post_vars_val ) {
				
				if ($post_vars_val == '') {
					unset ( $post_vars [$post_vars_key] );
				}
			}
			
			$processed_post_vars = $post_vars;
		}
		return $processed_post_vars;
	}
	function clean_uri__keys($uri_terms) { 
		foreach ( $uri_terms as $uri_terms_key => $uri_terms_val ) {
			
			if (is_numeric ( $uri_terms_key ) && empty ( $uri_terms_val )) {
				unset ( $uri_terms [$uri_terms_key] );
			}
		}
		
		return $uri_terms;
	}
	function get_pagination($records_num, $keys, $uri_segment) {
		$this->CI->load->library ( 'pagination' );
		
		$config ['base_url'] = base_url () . $this->CI->router->fetch_class () . '/' . ($this->CI->router->fetch_method () == 'index' ? $this->CI->router->fetch_class () : $this->CI->router->fetch_method ()) . '/' . $keys;
		$config ['total_rows'] = $records_num;
		$config ['first_link'] = '&laquo;';
		$config ['last_link'] = '&raquo;';
		$config ['prev_link'] = '&#8249;';
		$config ['next_link'] = '&#8250;';
		$config ['per_page'] = $this->CI->config->item ( 'rec_per_page' );
		$config ['num_links'] = $this->CI->config->item ( 'pag_num_links' );
		$config ["uri_segment"] = $uri_segment;
		
		$this->CI->pagination->initialize ( $config );
		
		$this->CI->session->set_userdata ( 'next_base_url', $this->CI->uri->uri_string () );
	}
	function get_selectors($selectors) {
		$selectors_arr = array ();
		
		foreach ( $selectors as $selector_val ) {
			switch ($selector_val) {
				
				case 'entity_id' :
					$selectors_arr [] = form_cascaded_geozones_entities_filter ( 'geozone_id', true );
					break;
				
				case 'datafile_year' :
					
					$yearz = array (
							'' => $this->CI->lang->line ( 'app_list_filter_year' ) 
					) + $this->get_years_list ( 1 );
					$selectors_arr [] = form_dropdown ( 'datafile_year', $yearz, ($this->CI->session->userdata ( 'datafile_year' ) != '') ? $this->CI->session->userdata ( 'datafile_year' ) : $this->CI->input->post ( 'datafile_year' ), 'id="datafile_year"' );
					$this->CI->session->unset_userdata ( 'datafile_year' );
					break;
				
				case 'filetype_entities' :
					
					$selectors_arr [] = form_cascaded_geozones_entities_filetype_filter ( 'geozone_id', true );
					break;
				
				case 'date_from_to' :
					$selectors_arr [] = form_input ( array (
							'name' => 'use_from',
							'id' => 'use_from',
							'class' => 'text date_picker dataentry_small',
							'value' => set_value ( 'use_from', $this->CI->input->post ( 'use_from' ) ) 
					) ) . ' ' . $this->CI->lang->line ( 'report_param_from_to' ) . ' ' . form_input ( array (
							'name' => 'use_to',
							'id' => 'use_to',
							'class' => 'text date_picker dataentry_small',
							'value' => set_value ( 'use_to', $this->CI->input->post ( 'use_to' ) ) 
					) );
					break;
			}
		}
		return array (
				$selectors_arr 
		);
	}
	function get_filters($filters) {
		$filters_arr = array ();
		
		foreach ( $filters as $filter_val ) {
			
			switch ($filter_val) {
				
				case 'user_fullname' :
					$filters_arr [] = form_input ( 'user_fullname', set_value ( 'user_fullname', '' ) );
					break;
				
				case 'usergroup_id' :
					
					$usergroup = $this->group_access_order ( $this->CI->session->userdata ( 'usergroup_id' ) );
					
					foreach ( $usergroup as $grp ) {
						
						$query = $this->CI->db->query ( 'SELECT usersgroup_name FROM pbf_usersgroups WHERE usersgroup_id = ' . $grp . ' LIMIT 1' );
						
						$row = $query->row ();
						
						$groups [$grp] = $row->usersgroup_name;
					}
					
					$groups = array (
							'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
					) + $groups;
					
					$filters_arr [] = form_dropdown ( 'usergroup_id', $groups, $this->CI->input->post ( 'usergroup_id' ), 'id="usergroup_id"' );
					
					break;
				
				case 'geozone_id' :

					
					$filters_arr [] = form_cascaded_geozones_filter ( 'geozone_id', true );
					break;
				
				case 'usertask_name' :
					$filters_arr [] = form_input ( 'usertask_name', set_value ( 'usertask_name', '' ) );
					break;
				
				case 'entity_name' :
					$filters_arr [] = form_input ( 'entity_name', set_value ( 'entity_name', '' ) );
					break;
				
				case 'donor_name' :
					$filters_arr [] = form_input ( 'donor_name', set_value ( 'donor_name', '' ) );
					break;
				
				case 'entity_type' :
					$filters_arr [] = form_cascaded_class_type_filter ( 'class', 'type' );
					break;
				
				case 'title' :
					$filters_arr [] = $this->CI->lang->line ( 'app_list_filter_article_title' ) . form_input ( 'title', set_value ( 'title', '' ) );
					break;
				
				case 'indicator_title' :
					$filters_arr [] = $this->CI->lang->line ( 'app_list_filter_indicator_title' ) . form_input ( 'title', set_value ( 'title', '' ) );
					break;
				
				case 'author' :
					$filters_arr [] = $this->CI->lang->line ( 'app_list_filter_author' ) . form_input ( 'author', set_value ( 'author', '' ) );
					break;
				
				case 'datafile_year' :
					$currentYear = date ( 'Y' );
					$startYear = $this->CI->pbf_mdl->get_oldest_datafile_year ();
					$nbYears = $currentYear - $startYear ['datafile_year'];
					
					$yearz = array (
							'' => $this->CI->lang->line ( 'app_list_filter_year' ) 
					) + $this->get_years_list ( $nbYears );
					$filters_arr [] = form_dropdown ( 'datafile_year', $yearz, $this->CI->input->post ( 'datafile_year' ), 'id="datafile_year"' );
					break;
				
				case 'budget_year' :
					$currentYear = date ( 'Y' );
					$startYear = $this->CI->pbf_mdl->get_oldest_budget_year ();
					$nbYears = 5;
					$yearz = array (
							'' => $this->CI->lang->line ( 'app_list_filter_year' ) 
					) + $this->get_years_list ( $nbYears );
					$filters_arr [] = form_dropdown ( 'budget_year', $yearz, $this->CI->input->post ( 'budget_year' ), 'id="budget_year"' );
					break;
				
				case 'datafile_month' :
					$monthz = $this->get_months_list ();
					$monthz [0] = $this->CI->lang->line ( 'app_list_filter_month' );
					$currentYear = date ( 'Y' );
					$startYear = $this->CI->pbf_mdl->get_oldest_datafile_year ();
					$nbYears = $currentYear - $startYear ['datafile_year'];
					$yearz = array (
							'' => $this->CI->lang->line ( 'app_list_filter_year' ) 
					) + $this->get_years_list ( $nbYears );
					$filters_arr [] = form_dropdown ( 'datafile_month', $monthz, $this->CI->input->post ( 'datafile_month' ), 'id="datafile_month"' ) . form_dropdown ( 'datafile_year', $yearz, $this->CI->input->post ( 'datafile_year' ), 'id="datafile_year"' );
					break;
				
				case 'entity_id' :
					
					$filters_arr [] = form_cascaded_geozones_entities_filter ( 'geozone_id', true );
					
					break;
		
				
				case 'filetype_id' :
					
					$filetype_ids = $this->get_filetypes_lookup_by_classes_and_zones ( $this->CI->session->userdata ( 'data_entity_class' ), $this->CI->session->userdata ( 'usergeozones' ) );
					$filetype_ids [''] = $this->CI->lang->line ( 'app_list_filter_filetype' );
					$filters_arr [] = form_dropdown ( 'filetype_id', $filetype_ids, $this->CI->input->post ( 'filetype_id' ), 'id="filetype_id"' );
					break;
				
				case 'indicator_filetype_id' :
					
					$filetype_ids = $this->get_filetypes_lookup_by_classes ( '' );
					$filetype_ids [''] = $this->CI->lang->line ( 'app_list_filter_filetype' );
					$filters_arr [] = form_dropdown ( 'file', $filetype_ids, $this->CI->input->post ( 'file' ), 'id="file"' );
					break;
				case 'date_from_to' :
					$filters_arr [] = form_input ( array (
							'name' => 'use_from',
							'id' => 'use_from',
							'class' => 'text date_picker dataentry_small',
							'value' => set_value ( 'use_from', $this->CI->input->post ( 'use_from' ) ) 
					) ) . ' ' . $this->CI->lang->line ( 'report_param_from_to' ) . ' ' . form_input ( array (
							'name' => 'use_to',
							'id' => 'use_to',
							'class' => 'text date_picker dataentry_small',
							'value' => set_value ( 'use_to', $this->CI->input->post ( 'use_to' ) ) 
					) );
					break;
				
				case 'date' :
					$filters_arr [] = form_input ( array (
							'name' => 'date',
							'id' => 'date',
							'class' => 'text date_picker dataentry_small' 
					) );
					break;
				
				case 'event_type' :
					$selected = array ();
					$event_type_list = array (
							'' => '',
							'acl' => 'Actions sur les utilisateurs',
							'datafiles' => 'Actions sur les fichiers de données',
							'hfrentities' => 'Actions sur les entités',
							'indicators' => 'Actions sur les indicateurs' 
					);
					// $filetype_ids[''] = $this->CI->lang->line('app_list_filter_filetype');
					$filters_arr [] = form_dropdown ( 'event_type', $event_type_list, $this->CI->input->post ( 'event_type' ), 'id="event_type"' );
					break;
				
				case 'invoice_id' :
					$filters_arr [] = $this->CI->config->item ( 'report_prefix' ) . '_' . form_input ( 'invoice_id', set_value ( 'invoice_id', '' ) );
					break;
			}
		}
		
		$filters_arr [] = form_submit ( 'submit', $this->CI->lang->line ( 'app_search_filter' ) );
		$filters_arr [] = form_reset ( 'clear_filter', $this->CI->lang->line ( 'app_clear_filter' ), 'onClick="reset_filter();return true;"' );
		
		return array (
				$filters_arr 
		);
	}
	function get_raw_config() {
		$this->CI->load->helper ( 'file' );
		
		$string = read_file ( APPPATH . '/config/config.php' );
		
		$string = str_replace ( '$config[', '', $string );
		$string = str_replace ( ']', '', $string );
		$string = explode ( ';', $string );
		
		$new_conf_array = array ();
		
		foreach ( $string as $stringk => $stringvar ) {
			$str = explode ( ' = ', trim ( $stringvar ) );
			if (! empty ( $str [0] ) && ! empty ( $str [1] )) {
				$new_conf_array [trim ( str_replace ( "'", "", $str [0] ) )] = $str [1];
			}
		}
		
		$new_conf_array ['lang_uri_abbr'] = str_replace ( 'array(', '', $new_conf_array ['lang_uri_abbr'] );
		$new_conf_array ['lang_uri_abbr'] = str_replace ( ')', '', $new_conf_array ['lang_uri_abbr'] );
		
		$new_conf_array ['lang_uri_abbr'] = explode ( ',', $new_conf_array ['lang_uri_abbr'] );
		
		$temp = array ();
		
		foreach ( $new_conf_array ['lang_uri_abbr'] as $arrk => $arrv ) {
			$str = explode ( ' => ', trim ( $arrv ) );
			if (! empty ( $str [0] ) && ! empty ( $str [1] )) {
				$temp [trim ( str_replace ( "'", "", $str [0] ) )] = trim ( str_replace ( "'", "", $str [1] ) );
			}
		}
		
		$new_conf_array ['lang_uri_abbr'] = $temp;
		
		return $new_conf_array;
	}
	function get_lookup($lookup_id) {
		return $this->CI->pbf_mdl->get_lookup ( $lookup_id );
	}
	function get_lookup_submenu($ctrl_path, $lookup_linkfile) {
		$raw_lookups = $this->get_lookups ( $lookup_linkfile );
		
		unset ( $raw_lookups [''] );
		unset ( $raw_lookups ['32'] );
		unset ( $raw_lookups ['38'] );
		
		foreach ( $raw_lookups as $raw_lookups_key => $raw_lookups_val ) {
			
			// tweak point 11 spec multi-region
			$usergeozones = $this->CI->session->userdata ( 'usergeozones' );
			
			if (! empty ( $usergeozones ) && $raw_lookups_key == '31') {
				
				continue;
			} else {
				
				$lookups [] = anchor ( $ctrl_path . $raw_lookups_key, $this->CI->lang->line ( trim ( 'option_lkp_ky_' ) . $raw_lookups_key ) );
			}
		}
		return $lookups;
	}
	function get_mod_submenu($menu_id) {
		$tabmenus = $this->get_child_menus ( $menu_id, '0', '1' );
		
		return implode ( '&nbsp;&nbsp;|&nbsp;&nbsp;', $tabmenus );
	}
	function get_mod_title($mod_title) {
		if (is_array ( $mod_title ) && ! empty ( $mod_title )) {
			$mod_title = array_reverse ( $mod_title );
			$mod_title_temp = '';
			$buttons_arr = array ();
			
			foreach ( $mod_title as $mod_title_key => $mod_title_var ) {
				
				if ($mod_title_key == 'mod_title') {
					$mod_title_temp .= '<h2>' . $mod_title_var . '</h2>';
				} else {
					if (strpos ( $mod_title_var, 'type="image"' )) {
						$buttons_arr [] = $mod_title_var;
					} else {
						$buttons_arr [] = anchor ( $mod_title_key, $mod_title_var, array (
								'title' => '' 
						) ); // To do, improve this
					}
				}
			}
			
			$mod_title = $mod_title_temp . ul ( array_reverse ( $buttons_arr ) );
		}
		return $mod_title;
	}
	function get_current_business_month_year() {
		$newdate = strtotime ( '-2 month', strtotime ( date ( 'Y-m-j' ) ) );
		$month_year ['month'] = date ( 'm', $newdate );
		$month_year ['year'] = date ( 'Y', $newdate );
		
		return $month_year;
	}
	function get_current_business_time() { // make it dynamic to handle FY 2012-2013
		$month_year = $this->get_current_business_month_year ();
		
		if ($month_year ['month'] >= 1 && $month_year ['month'] <= 3) {
			return array (
					'quarter' => 1,
					'months' => array (
							1,
							2,
							3 
					),
					'year' => $month_year ['year'] 
			);
		} elseif ($month_year ['month'] >= 4 && $month_year ['month'] <= 6) {
			return array (
					'quarter' => 2,
					'months' => array (
							4,
							5,
							6 
					),
					'year' => $month_year ['year'] 
			);
		}
		if ($month_year ['month'] >= 7 && $month_year ['month'] <= 9) {
			return array (
					'quarter' => 3,
					'months' => array (
							7,
							8,
							9 
					),
					'year' => $month_year ['year'] 
			);
		}
		if ($month_year ['month'] >= 10 && $month_year ['month'] <= 12) {
			return array (
					'quarter' => 4,
					'months' => array (
							10,
							11,
							12 
					),
					'year' => $month_year ['year'] 
			);
		}
	}
	
	function get_real_business_time($month_year) { // make it dynamic to handle FY 2012-2013
				
		if ($month_year ['datafile_month'] >= 1 && $month_year ['datafile_month'] <= 3) {
			return array (
					'quarter' => 1,
					'months' => array (
							1,
							2,
							3 
					),
					'year' => $month_year ['datafile_year'] 
			);
		} elseif ($month_year ['datafile_month'] >= 4 && $month_year ['datafile_month'] <= 6) {
			return array (
					'quarter' => 2,
					'months' => array (
							4,
							5,
							6 
					),
					'year' => $month_year ['datafile_year'] 
			);
		}
		if ($month_year ['datafile_month'] >= 7 && $month_year ['datafile_month'] <= 9) {
			return array (
					'quarter' => 3,
					'months' => array (
							7,
							8,
							9 
					),
					'year' => $month_year ['datafile_year'] 
			);
		}
		if ($month_year ['datafile_month'] >= 10 && $month_year ['datafile_month'] <= 12) {
			return array (
					'quarter' => 4,
					'months' => array (
							10,
							11,
							12 
					),
					'year' => $month_year ['datafile_year'] 
			);
		}
	}
	
	
	function get_current_quarterBy_month($month) {
		if ($month >= 1 && $month <= 3) {
			return 1;
		} elseif ($month >= 4 && $month <= 6) {
			return 2;
		}
		if ($month >= 7 && $month <= 9) {
			return 3;
		}
		if ($month >= 10 && $month <= 12) {
			return 4;
		}
	}
	function get_monthsBy_quarter($quarter) {
		if ($quarter == 1) {
			return array (
					1,
					2,
					3 
			);
		} elseif ($quarter == 2) {
			return array (
					4,
					5,
					6 
			);
		}
		if ($quarter == 3) {
			return array (
					7,
					8,
					9 
			);
		}
		if ($quarter == 4) {
			return array (
					10,
					11,
					12 
			);
		}
	}
	function get_years_list($interval = 15) {
		for($i = date ( 'Y' ); $i >= date ( 'Y' ) - $interval; $i --) {
			$years [$i] = $i;
		}
		return $years;
	}
	function get_semesters_list() {
		$months [6] = $this->CI->lang->line ( 'app_month_6' );
		$months [12] = $this->CI->lang->line ( 'app_month_12' );
		
		return $months;
	}
	function get_months_list($return_quarters = false) {
		// $months = array(0 => $this->CI->lang->line('app_form_dropdown_select'));
		if ($return_quarters === true) {
			
			$months [1] = $this->CI->lang->line ( 'app_quarter_1' );
			$months [2] = $this->CI->lang->line ( 'app_quarter_2' );
			$months [3] = $this->CI->lang->line ( 'app_quarter_3' );
			$months [4] = $this->CI->lang->line ( 'app_quarter_4' );
		} else {
			for($i = 1; $i <= 12; $i ++) {
				$months [$i] = $this->CI->lang->line ( 'app_month_' . $i );
			}
		}
		return $months;
	}
	function get_datafile_periodics() {
		$filetypes_frequency = $this->CI->pbf_mdl->get_datafile_periodics ();
		
		$periodics = array (
				'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
		);
		foreach ( $filetypes_frequency as $key => $val ) {
			$months = json_decode ( $val ['frequency_months'] );
			if (count ( $months ) == 4) 			// trim
			{
				foreach ( $months as $month ) {
					
					$periodics [] = array (
							$val ['filetype_entity_id'] => array (
									$month => $this->CI->lang->line ( 'app_quart_month_' . $month ) 
							) 
					);
				}
			} else {
				foreach ( $months as $month ) {
					
					$periodics [] = array (
							$val ['filetype_entity_id'] => array (
									$month => $this->CI->lang->line ( 'app_month_' . $month ) 
							) 
					);
				}
			}
		}
		return $periodics;
	}

	function get_active_geozones($dropdown = true) {
		$raw_geozones = $this->CI->pbf_mdl->get_active_geozones ();
		
		if ($dropdown === true) {
			$active_geozones = array (
					'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
			);
		} else {
			$active_geozones = array ();
		}
		
		foreach ( $raw_geozones as $key ) {
			$active_geozones [$key ['geozone_id']] = strtoupper ( $key ['geozone_name'] );
		}
		return $active_geozones;
	}
	function get_active_geozones_parents($dropdown = true) {
		$raw_geozones = $this->CI->geo_mdl->get_geozone_perparent ();
		
		if ($dropdown === true) {
			$active_geozones = array (
					'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
			);
		} else {
			$active_geozones = array ();
		}
		
		foreach ( $raw_geozones as $key ) {
				$active_geozones [$key ['F_geozoneId']] = array (
					strtoupper ( $key ['F_geozoneName'] ),
					strtoupper ( $key ['P_geozoneName'] ) 
			);
		}
		return $active_geozones;
	}
	function get_active_geozones_in_zone($zone, $dropdown = true) {
		$raw_geozones = $this->CI->pbf_mdl->get_active_geozones_in_zone ( $zone );
		
		if ($dropdown === true) {
			$active_geozones = array (
					'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
			);
		} else {
			$active_geozones = array ();
		}
		
		foreach ( $raw_geozones as $key ) {
			$active_geozones [$key ['geozone_id']] = strtoupper ( $key ['geozone_name'] );
		}
		return $active_geozones;
	}
	function get_lookups($lookup_linkfile) {
		$raw_lookups = $this->CI->pbf_mdl->get_lookups ( $lookup_linkfile );
		
		$lookups = array (
				'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
		);
		
		foreach ( $raw_lookups as $key ) {
			$pos = strpos ( $key ['lookup_title'], '_' );
			$lookups [$key ['lookup_id']] = ($pos !== false) ? $this->CI->lang->line ( trim ( $key ['lookup_title'] ) ) : $key ['lookup_title'];
		}
		
		// TO DO check secu
		// remove edition of edito, top, keydata if not super admin or admin nat
		$sess = $this->CI->session->userdata ( 'usergroup_id' );
		if (($sess != 1) && ($sess != 2)) {
			unset ( $lookups ['38'] );
			unset ( $lookups ['39'] );
			unset ( $lookups ['40'] );
		}
		
		return $lookups;
	}
	function get_asset_access($asset_id, $asset_link, $access_level = 'usersgroup_id') {
		$raw_asset_access = $this->CI->pbf_mdl->get_asset_access ( $asset_id, $asset_link, $access_level );
		
		$asset_access = array ();
		foreach ( $raw_asset_access as $key ) {
			$asset_access [] = $key [$access_level];
		}
		return $asset_access;
	}
	function get_asset_access_rw($asset_id, $asset_link, $access_level = 'usersgroup_id') {
		$raw_asset_access = $this->CI->pbf_mdl->get_asset_access_rw ( $asset_id, $asset_link, $access_level );
		
		return $raw_asset_access;
	}
	function get_entity_classes($select = true) {
		$raw_classes = $this->CI->pbf_mdl->get_geo__entities_classes ( true );
		
		if ($select) {
			$lookups = array (
					'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
			);
		}
		
		foreach ( $raw_classes as $key ) {
			$lookups [$key ['entity_class_id']] = $key ['entity_class_name'];
		}
		return $lookups;
	}
	function get_entity_groups() {
		$raw_groups = $this->CI->pbf_mdl->get_entity_groups ();
		
		$lookups = array ();
		
		foreach ( $raw_groups as $key ) {
			$lookups [$key ['entity_group_id']] = $key ['entity_group_name'];
		}
		return $lookups;
	}
	function get_entity_types_by_class($entity_class_id) {
		$raw_types = $this->CI->pbf_mdl->get_entity_types ( $entity_class_id );
		
		$lookups = array ();
		
		foreach ( $raw_types as $key ) {
			$lookups [$key ['entity_type_id']] = $key ['entity_type_name'];
		}
		return $lookups;
	}
	function get_entity_types() {
		$raw_types = $this->CI->pbf_mdl->get_entity_types ( '' );
		
		$lookups = array (
				'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
		);
		
		foreach ( $raw_types as $key ) {
			$lookups [$key ['entity_type_id']] = array (
					$key ['entity_class_id'] => $key ['entity_type_name'] 
			);
		}
		return $lookups;
	}
	function get_banks($hq = true) {
		$raw_banks = $this->CI->pbf_mdl->get_banks ( $hq );
		
		$banks = array ();
		
		foreach ( $raw_banks as $key ) {
			$banks [$key ['bank_id']] = array (
					$key ['bank_parent_id'] => $key ['bank_name'] 
			);
		}
		return $banks;
	}

	function get_geo__entities_classes($classes = false) {
		$raw_classes = $this->CI->pbf_mdl->get_geo__entities_classes ( $classes );
		
		if (! $classes) {
			$raw_classes ['hfrentities/types/'] = $this->CI->lang->line ( 'app_submenu_settings_entities_entity_types' );
			$raw_classes ['hfrentities/classes/'] = $this->CI->lang->line ( 'app_submenu_settings_entities_entity_classes' );
			$raw_classes ['geo/geos/'] = $this->CI->lang->line ( 'app_submenu_settings_entities_region_types' );
		}
		
		return $raw_classes;
	}
	function get_entities_classes() {
		$this->CI->lang->load ( 'hfrentities', $this->CI->config->item ( 'language' ) );
		
		$raw_classes = $this->CI->pbf_mdl->get_entities_classes ();
		
		foreach ( $raw_classes as $raw_class_k => $raw_class_v ) {
			$raw_classes [] = anchor ( $raw_class_v ['link'], $this->CI->lang->line ( 'etty_cls_ky_' . $raw_class_v ['link_id'] ) );
			unset ( $raw_classes [$raw_class_k] );
		}
		return $raw_classes;
	}
	function get_geo_classes() {
		$this->CI->lang->load ( 'geo', $this->CI->config->item ( 'language' ) );
		
		$raw_classes = $this->CI->pbf_mdl->get_geo_classes ();
		
		foreach ( $raw_classes as $raw_class_k => $raw_class_v ) {
			
			$raw_classes [] = anchor ( $raw_class_v ['link'], $this->CI->lang->line ( 'geo_key_' . $raw_class_v ['geo_id'] ) );
			unset ( $raw_classes [$raw_class_k] );
		}
		
		return $raw_classes;
	}
	function get_entities_classes_access() {
		return $this->CI->pbf_mdl->get_entities_classes_access ();
	}
	function get_entities_types() {
		$raw_types = $this->CI->pbf_mdl->get_entity_types ( '' ); // reused the function, but this type I do not need the classes and the lookup
		
		foreach ( $raw_types as $raw_type ) {
			
			$types ['budgets/budgets/' . $raw_type ['entity_type_id']] = $raw_type ['entity_type_name'];
		}
		
		return $types;
	}
	function get_geoleveles() {
		return $this->CI->pbf_mdl->get_geoleveles ();
	}
	function get_geozones() {
		return $this->CI->pbf_mdl->get_geozones ();
	}
	function get_geozones_by_parent_geo_id($geo_id = '') {
		return $this->CI->pbf_mdl->get_geozones_by_parent_geo_id ( $geo_id );
	}
	function get_entity_web_form($classe_id) {
		$usergroupsrules = $this->CI->session->userdata ( 'usergroupsrules' );
		
		$this->CI->load->model ( 'entities_mdl' );
		
		if (in_array ( "hfrentities/addentity/", $usergroupsrules )) {
	
			
			$general_information = form_hidden ( array (
					'entity_id' => $this->CI->session->userdata ( 'entity_id' ) 
			) ) . form_hidden ( array (
					'entity_class' => $this->CI->session->userdata ( 'entity_class' ) 
			) ) . form_fieldset ( $this->CI->lang->line ( 'frm_entity_definition' ) ) . '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_name' ), 'entity_name' ) . form_input ( array (
					'name' => 'entity_name',
					'id' => 'entity_name',
					'value' => set_value ( 'entity_name', $this->CI->session->userdata ( 'entity_name' ) ) 
			) ) . '</p>' . '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_type' ), 'entity_type' ) . form_cascaded_dropdown ( 'entity_type', array (
					'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
			) + $this->get_entity_types_by_class ( $classe_id ), $this->CI->session->userdata ( 'entity_type' ), 'id="entity_type"' ) . '</p>';
			
			$geolocal_information = form_fieldset ( $this->CI->lang->line ( 'frm_entity_geo_location' ) ) . form_cascaded_geozones ( 'entity_geozone_id' );
			
			$contact_information = form_fieldset ( $this->CI->lang->line ( 'frm_entity_contact_info' ) );
			$contract_information = form_fieldset ( $this->CI->lang->line ( 'frm_entity_contract_info' ) );
			
			$bank_information = form_fieldset ( $this->CI->lang->line ( 'frm_entity_bank_info' ) ) . '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_bank_bank' ), 'entity_bank_hq_id' ) . form_cascaded_dropdown ( 'entity_bank_hq_id', $this->get_banks (), $this->CI->session->userdata ( 'bank_parent_id' ), 'id="entity_bank_hq_id"' ) . '</p>' . '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_bank_branch' ), 'entity_bank_id' ) . form_cascaded_dropdown ( 'entity_bank_id', $this->get_banks ( false ), $this->CI->session->userdata ( 'entity_bank_id' ), 'id="entity_bank_id"' ) . '</p>' . '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_bank_account' ), 'entity_bank_acc' ) . form_input ( array (
					'name' => 'entity_bank_acc',
					'id' => 'entity_bank_acc',
					'value' => set_value ( 'entity_bank_acc', $this->CI->session->userdata ( 'entity_bank_acc' ) ) 
			) ) . '</p>' . form_fieldset_close ();
			
			$entity_property = $this->CI->entities_mdl->get_entityclass ( $classe_id );
			
			$entity_property = json_decode ( $entity_property ['entity_class_properties'] );
			
			if (! empty ( $entity_property )) {
				
				foreach ( $entity_property as $entity_property_val ) {
					
					switch ($entity_property_val) {
						
						case 'entity_staff_size' :
							
							$general_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_staf_size' ), 'entity_staff_size' ) . 

							form_input ( array (
									'name' => 'entity_staff_size',
									'id' => 'entity_staff_size',
									'value' => set_value ( 'entity_staff_size', $this->CI->session->userdata ( 'entity_staff_size' ) ) 
							) ) . '</p>';
							
							break;
						
						case 'entity_sis_code' :
							
							$general_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_hmiscode' ), 'entity_sis_code' ) . 

							form_input ( array (
									'name' => 'entity_sis_code',
									'id' => 'entity_sis_code',
									'value' => set_value ( 'entity_sis_code', $this->CI->session->userdata ( 'entity_sis_code' ) ) 
							) ) . '</p>';
							
							break;
						
						case 'entity_pop' :
							
							$general_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_population' ), 'entity_pop' ) . 

							form_input ( array (
									'name' => 'entity_pop',
									'id' => 'entity_pop',
									'value' => set_value ( 'entity_pop', $this->CI->session->userdata ( 'entity_pop' ) ) 
							) ) . '</p>';
							
							break;
						
						case 'entity_pop_year' :
							
							$general_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_population_year' ), 'entity_pop_year' ) . 

							form_dropdown ( 'entity_pop_year', $this->get_years_list (), $this->CI->session->userdata ( 'entity_pop_year' ) ) . '</p>';
							
							break;
						
						case 'entity_contracttype' :
							$contact_type = array (
									'1' => 'Principal',
									'2' => 'Sécondaire' 
							);
							$contract_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_contracttype' ), 'entity_contracttype' ) . 

							form_dropdown ( 'entity_contracttype', $contact_type, $this->CI->session->userdata ( 'entity_contracttype' ) ) . '</p>';
							
							break;
						
						case 'entity_contractpath' :
							
							$contract_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_contract' ), 'entity_contractpath' ) . 

							form_upload ( array (
									'name' => 'entity_contractpath',
									'id' => 'entity_contractpath' 
							) ) . (($this->CI->session->userdata ( 'entity_contractpath' ) == '') ? '' : ' &nbsp;&nbsp;' . anchor_popup ( $this->CI->config->item ( 'base_url' ) . 'cside/contents/docs/contracts/' . $this->CI->session->userdata ( 'entity_contractpath' ), $this->CI->lang->line ( 'frm_entity_contract_open' ), array () ) . ' | ' . anchor ( 'hfrentities/delinfo/' . $this->CI->session->userdata ( 'entity_id' ) . '/entity_contractpath', $this->CI->lang->line ( 'frm_entity_contract_delete' ) )) . '</p>';
							
							break;
						
						case 'entity_contractvalidity_start' :
							
							$contract_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_contract_start' ) . ':', 'entity_contractvalidity_start' ) . 

							form_input ( array (
									'name' => 'entity_contractvalidity_start',
									'id' => 'entity_contractvalidity_start',
									'class' => 'text date_picker',
									'value' => set_value ( 'entity_contractvalidity_start', $this->CI->session->userdata ( 'entity_contractvalidity_start' ) ) 
							) ) . '</p>';
							
							break;
						
						case 'entity_contractvalidity_end' :
							
							$contract_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_contract_end' ) . ':', 'entity_contractvalidity_end' ) . 

							form_input ( array (
									'name' => 'entity_contractvalidity_end',
									'id' => 'entity_contractvalidity_end',
									'class' => 'text date_picker',
									'value' => set_value ( 'entity_contractvalidity_end', $this->CI->session->userdata ( 'entity_contractvalidity_end' ) ) 
							) ) . '</p>';
							
							break;
						
						case 'entity_pbf_group_id' :
							
							$contract_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_group' ), 'entity_pbf_group_id' ) . 

							form_dropdown ( 'entity_pbf_group_id', array (
									'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
							) + $this->get_entity_groups (), $this->CI->session->userdata ( 'entity_pbf_group_id' ) ) . '</p>';
							
							break;
						
						case 'entity_use_equity_bonus' :
							
							$contract_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_equity_bonus' ) . ':', 'entity_use_equity_bonus' ) . form_checkbox ( array (
									'name' => 'entity_use_equity_bonus',
									'id' => 'entity_use_equity_bonus',
									'value' => 1,
									'checked' => set_value ( 'entity_use_equity_bonus', $this->CI->session->userdata ( 'entity_use_equity_bonus' ) ) 
							) ) . '</p>';
							
							break;
						
						case 'entity_use_isolation_bonus' :
							
							$contract_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_isolation_bonus' ) . ':', 'entity_use_isolation_bonus' ) . 

							form_checkbox ( array (
									'name' => 'entity_use_isolation_bonus',
									'id' => 'entity_use_isolation_bonus',
									'value' => 1,
									'checked' => set_value ( 'entity_use_isolation_bonus', $this->CI->session->userdata ( 'entity_use_isolation_bonus' ) ) 
							) ) . '</p>';
							
							break;
						case 'entity_equity_pourcentage' :
							
							$contract_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_equity_pourcentage' ) . ':', 'entity_equity_pourcentage' ) . form_dropdown ( 'entity_equity_pourcentage', array (
									'0' => '0',
									'10' => '10%',
									'20' => '20%',
									'30' => '30%',
									'40' => '40%' 
							), $this->CI->session->userdata ( 'entity_equity_pourcentage' ) ) . '</p>';
							/*
							 * form_input(array( 'name' => 'entity_equity_pourcentage', 'id' => 'entity_equity_pourcentage', 'value' => set_value('entity_equity_pourcentage',$this->CI->session->userdata('entity_equity_pourcentage')) )).'</p>';
							 */
							
							break;
						
						case 'entity_picturepath' :
							
							$general_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_picture' ), 'entity_picturepath' ) . 

							form_upload ( array (
									'name' => 'entity_picturepath',
									'id' => 'entity_picturepath' 
							) ) . (($this->CI->session->userdata ( 'entity_picturepath' ) == '') ? '' : ' &nbsp;&nbsp;' . anchor_popup ( $this->CI->config->item ( 'base_url' ) . 'cside/images/portal/' . $this->CI->session->userdata ( 'entity_picturepath' ) . '_big.jpg', $this->CI->lang->line ( 'frm_entity_contract_open' ), array () ) . ' | ' . anchor ( 'hfrentities/delinfo/' . $this->CI->session->userdata ( 'entity_id' ) . '/entity_picturepath', $this->CI->lang->line ( 'frm_entity_contract_delete' ) ) . '<img  style="float:right;" src="' . $this->CI->config->item ( 'base_url' ) . 'cside/images/portal/' . $this->CI->session->userdata ( 'entity_picturepath' ) . '_thumb.jpg" />') . '</p>';
							
							break;
						
						case 'entity_status' :
							
							$general_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_status' ), 'entity_status' ) . 

							form_dropdown ( 'entity_status', $this->get_lookups ( 'entity_status' ), $this->CI->session->userdata ( 'entity_status' ) ) . '</p>';
							
							break;
						
						case 'entity_responsible_email' :
							
							$contact_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_responsible_email' ), 'entity_responsible_email' ) . 

							form_input ( array (
									'name' => 'entity_responsible_email',
									'id' => 'entity_responsible_email',
									'class' => 'longtext',
									'value' => set_value ( 'entity_responsible_email', $this->CI->session->userdata ( 'entity_responsible_email' ) ) 
							) ) . '</p>';
							
							break;
						
						case 'entity_responsible_name' :
							
							$contact_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_responsible_name' ), 'entity_responsible_name' ) . 

							form_input ( array (
									'name' => 'entity_responsible_name',
									'id' => 'entity_responsible_name',
									'value' => set_value ( 'entity_responsible_name', $this->CI->session->userdata ( 'entity_responsible_name' ) ) 
							) ) . '</p>';
							
							break;
						
						case 'entity_phone_number' :
							
							$contact_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_pnone_number' ), 'entity_phone_number' ) . 

							form_input ( array (
									'name' => 'entity_phone_number',
									'id' => 'entity_phone_number',
									'value' => set_value ( 'entity_phone_number', $this->CI->session->userdata ( 'entity_phone_number' ) ) 
							) ) . '</p>';
							
							break;
						
						case 'entity_address' :
							
							$contact_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_address' ), 'entity_address' ) . 

							form_textarea ( array (
									'name' => 'entity_address',
									'id' => 'entity_address',
									'rows' => 2,
									'cols' => 40,
									'value' => set_value ( 'entity_address', $this->CI->session->userdata ( 'entity_address' ) ) 
							) ) . '</p>';
							
							break;
						
						case 'entity_geo_long' :
							
							$geolocal_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_geo_long' ), 'entity_geo_long' ) . 

							form_input ( array (
									'name' => 'entity_geo_long',
									'id' => 'entity_geo_long',
									'value' => set_value ( 'entity_geo_long', $this->CI->session->userdata ( 'entity_geo_long' ) ) 
							) ) . '</p>';
							
							break;
						
						case 'entity_geo_lat' :
							
							$geolocal_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_geo_lat' ), 'entity_geo_lat' ) . 

							form_input ( array (
									'name' => 'entity_geo_lat',
									'id' => 'entity_geo_lat',
									'value' => set_value ( 'entity_geo_lat', $this->CI->session->userdata ( 'entity_geo_lat' ) ) 
							) ) . '</p>';
							
							break;
						
						case 'entity_distance_tobase' :
							
							$geolocal_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_distance_tobase' ), 'entity_distance_tobase' ) . 

							form_input ( array (
									'name' => 'entity_distance_tobase',
									'id' => 'entity_distance_tobase',
									'value' => set_value ( 'entity_distance_tobase', $this->CI->session->userdata ( 'entity_distance_tobase' ) ) 
							) ) . '</p>';
							
							break;
					}
				}
			}
			
			$general_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_active' ), 'entity_active' ) . form_checkbox ( array (
					'name' => 'entity_active',
					'id' => 'entity_active',
					'value' => 1,
					'checked' => ($this->CI->session->userdata ( 'entity_active' ) == 1) ? TRUE : FALSE 
			) ) . '</p>' . form_fieldset_close ();
			
			$geolocal_information .= form_fieldset_close ();
			$contact_information .= form_fieldset_close ();
			$contract_information .= form_fieldset_close ();
			$bank_information .= form_fieldset_close ();
			
			if ($contract_information != form_fieldset ( $this->CI->lang->line ( 'frm_entity_contract_info' ) ) . form_fieldset_close ()) 

			{
				$general_information .= $contract_information;
			}
			
			$general_information .= $geolocal_information . $contact_information . $bank_information;
			
			return $general_information;
		} else {
			
			$general_information = form_hidden ( array (
					'entity_id' => $this->CI->session->userdata ( 'entity_id' ) 
			) ) . form_hidden ( array (
					'entity_class' => $this->CI->session->userdata ( 'entity_class' ) 
			) ) . form_hidden ( array (
					'entity_active' => 1 
			) ) . form_fieldset ( $this->CI->lang->line ( 'frm_entity_definition' ) ) . '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_name' ), 'entity_name' ) . form_input ( array (
					'name' => 'entity_name',
					'id' => 'entity_name',
					'value' => set_value ( 'entity_name', $this->CI->session->userdata ( 'entity_name' ) ) 
			) ) . '</p>';
			
			$contact_information = form_fieldset ( $this->CI->lang->line ( 'frm_entity_contact_info' ) );
			
			$geolocal_information = form_fieldset ( $this->CI->lang->line ( 'frm_entity_geo_location' ) );
			
			$entity_property = $this->CI->entities_mdl->get_entityclass ( $classe_id );
			
			$entity_property = json_decode ( $entity_property ['entity_class_properties'] );
			
			if (! empty ( $entity_property )) {
				
				foreach ( $entity_property as $entity_property_val ) {
					
					switch ($entity_property_val) {
						
						case 'entity_staff_size' :
							
							$general_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_staf_size' ), 'entity_staff_size' ) . 

							form_input ( array (
									'name' => 'entity_staff_size',
									'id' => 'entity_staff_size',
									'value' => set_value ( 'entity_staff_size', $this->CI->session->userdata ( 'entity_staff_size' ) ) 
							) ) . '</p>';
							
							break;
						
						case 'entity_picturepath' :
							
							$general_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_picture' ), 'entity_picturepath' ) . 

							form_upload ( array (
									'name' => 'entity_picturepath',
									'id' => 'entity_picturepath' 
							) ) . (($this->CI->session->userdata ( 'entity_picturepath' ) == '') ? '' : ' &nbsp;&nbsp;' . anchor_popup ( $this->CI->config->item ( 'base_url' ) . 'cside/images/portal/' . $this->CI->session->userdata ( 'entity_picturepath' ), $this->CI->lang->line ( 'frm_entity_contract_open' ), array () ) . ' | ' . anchor ( 'hfrentities/delinfo/' . $this->CI->session->userdata ( 'entity_id' ) . '/entity_picturepath', $this->CI->lang->line ( 'frm_entity_contract_delete' ) )) . '</p>';
							
							break;
						
						case 'entity_responsible_email' :
							
							$contact_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_responsible_email' ), 'entity_responsible_email' ) . 

							form_input ( array (
									'name' => 'entity_responsible_email',
									'id' => 'entity_responsible_email',
									'class' => 'longtext',
									'value' => set_value ( 'entity_responsible_email', $this->CI->session->userdata ( 'entity_responsible_email' ) ) 
							) ) . '</p>';
							
							break;
						
						case 'entity_responsible_name' :
							
							$contact_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_responsible_name' ), 'entity_responsible_name' ) . 

							form_input ( array (
									'name' => 'entity_responsible_name',
									'id' => 'entity_responsible_name',
									'value' => set_value ( 'entity_responsible_name', $this->CI->session->userdata ( 'entity_responsible_name' ) ) 
							) ) . '</p>';
							
							break;
						
						case 'entity_phone_number' :
							
							$contact_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_pnone_number' ), 'entity_phone_number' ) . 

							form_input ( array (
									'name' => 'entity_phone_number',
									'id' => 'entity_phone_number',
									'value' => set_value ( 'entity_phone_number', $this->CI->session->userdata ( 'entity_phone_number' ) ) 
							) ) . '</p>';
							
							break;
						
						case 'entity_address' :
							
							$contact_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_address' ), 'entity_address' ) . 

							form_textarea ( array (
									'name' => 'entity_address',
									'id' => 'entity_address',
									'rows' => 2,
									'cols' => 40,
									'value' => set_value ( 'entity_address', $this->CI->session->userdata ( 'entity_address' ) ) 
							) ) . '</p>';
							
							break;
						
						case 'entity_geo_long' :
							
							$geolocal_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_geo_long' ), 'entity_geo_long' ) . 

							form_input ( array (
									'name' => 'entity_geo_long',
									'id' => 'entity_geo_long',
									'value' => set_value ( 'entity_geo_long', $this->CI->session->userdata ( 'entity_geo_long' ) ) 
							) ) . '</p>';
							
							break;
						
						case 'entity_geo_lat' :
							
							$geolocal_information .= '<p>' . form_label ( $this->CI->lang->line ( 'frm_entity_geo_lat' ), 'entity_geo_lat' ) . 

							form_input ( array (
									'name' => 'entity_geo_lat',
									'id' => 'entity_geo_lat',
									'value' => set_value ( 'entity_geo_lat', $this->CI->session->userdata ( 'entity_geo_lat' ) ) 
							) ) . '</p>';
							
							break;
					}
				}
			}
			
			$general_information .= form_fieldset_close ();
			
			$contact_information .= form_fieldset_close ();
			
			$geolocal_information .= form_fieldset_close ();
			
			return $general_information . $geolocal_information . $contact_information;
		}
	}
	function get_filetypes($entity_class, $entity_type) {
		$raw_filetypes = $this->CI->pbf_mdl->get_file_types ( $entity_class, $entity_type );
		
		$filetypes = array ();
		
		foreach ( $raw_filetypes as $key ) {
			if (array_key_exists ( $key ['filetype_entity_id'], $filetypes )) {
				$filetypes ['0' . $key ['filetype_entity_id']] = array (
						$key ['entity_type_id'] => array (
								$key ['filetype_id'] => $key ['filetype_name'] 
						) 
				);
			} else {
				$filetypes [$key ['filetype_entity_id']] = array (
						$key ['entity_type_id'] => array (
								$key ['filetype_id'] => $key ['filetype_name'] 
						) 
				);
			}
		}
		return $filetypes;
	}
	function get_filetypess() {
		$raw_filetypes = $this->CI->pbf_mdl->get_filetypes ();
		
		$filetypes_array = array ();
		
		foreach ( $raw_filetypes as $filetype ) {
			$filetypes_array [$filetype ['filetype_id']] = $filetype ['filetype_name'];
		}
		return $filetypes_array;
	}
	function get_regions() {
		$raw_regions = $this->CI->pbf_mdl->get_regions ();
		
		$regions_array = array ();
		
		foreach ( $raw_regions as $region ) {
			$regions_array [$region ['geozone_id']] = $region ['geozone_name'];
		}
		$regions_array = array (
				'' 
		) + $regions_array;
		
		return $regions_array;
	}
	function get_districts() {
		$raw_districts = $this->CI->pbf_mdl->get_districts ();
		
		$districts_array = array ();
		
		foreach ( $raw_districts as $district ) {
			$districts_array [$district ['geozone_id']] = $district ['geozone_name'];
		}
		$districts_array = array (
				'' 
		) + $districts_array;
		return $districts_array;
	}
	function get_district_region($id_region) {
		$raw_districts = $this->CI->pbf_mdl->get_districts_region ( $id_region );
		
		$districts_array = array ();
		
		foreach ( $raw_districts as $district ) {
			$districts_array [$district ['geozone_id']] = $district ['geozone_name'];
		}
		$districts_array = array (
				'' 
		) + $districts_array;
		return $districts_array;
	}
	function json_getzones() {
		if (isset ( $_POST ["region"] )) {
			$id_region = $_POST ['region'];
		} else {
			$region = 1;
		}
		$zones = $this->pbf->get_zones ( $region );
		echo json_encode ( $zones );
	}
	function get_groups() {
		$raw_groups = $this->CI->pbf_mdl->get_groups ();
		
		$groups_array = array ();
		
		foreach ( $raw_groups as $group ) {
			$groups_array [$group ['usersgroup_id']] = $group ['usersgroup_name'];
		}
		return $groups_array;
	}
	function get_indicators() {
		$raw_indicators = $this->CI->pbf_mdl->get_indicators ();
		
		$indicators_array = array ();
		
		foreach ( $raw_indicators as $indicator ) {
			$indicators_array [$indicator ['indicator_id']] = $indicator ['indicator_title'];
		}
		$indicators_array = array (
				'' 
		) + $indicators_array;
		return $indicators_array;
	}
	function get_entities_donors() {
		$raw_entities = $this->CI->pbf_mdl->get_entities_donors ();
		
		$entities_array = array ();
		
		foreach ( $raw_entities as $entity ) {
			$entities_array [$entity ['entity_id']] = $entity ['entity_name'];
		}
		$entities_array = array (
				'' 
		) + $entities_array;
		return $entities_array;
	}
	function get_entities_district_donor($district_id) {
		$raw_entities = $this->CI->pbf_mdl->get_entities_district_donors ( $district_id );
		
		$entities_array = array ();
		
		foreach ( $raw_entities as $entity ) {
			$entities_array [$entity ['entity_id']] = $entity ['entity_name'];
		}
		$entities_array = array (
				'' 
		) + $entities_array;
		return $entities_array;
	}
	function get_donors() {
		$raw_donors = $this->CI->pbf_mdl->get_donors ();
		
		$donors_array = array ();
		
		foreach ( $raw_donors as $donor ) {
			$donors_array [$donor ['donor_id']] = $donor ['donor_name'];
		}
		return $donors_array;
	}
	function get_groups_details() {
		$raw_groups = $this->CI->pbf_mdl->get_groups ();
		
		return $raw_groups;
	}
	function get_users() {
		$raw_users = $this->CI->pbf_mdl->get_users ();
		return $raw_users;
	}
	function get_filetypes_lookup() {
		$raw_filetypes = $this->CI->pbf_mdl->get_filetypes ();
		
		$filetypes = array (
				'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
		);
		
		foreach ( $raw_filetypes as $key ) {
			$filetypes [$key ['filetype_id']] = $key ['filetype_name'];
		}
		return $filetypes;
	}
	function get_pop_cible($entity_arr, $entity_group_id, $zones) {
		return $this->CI->pbf_mdl->get_pop_cible ( $entity_arr, $entity_group_id, $zones );
	}
	function get_pop_cible_projected($entity_arr, $entity_group_id, $zones, $proj_year) {
		$entities_pop = $this->CI->pbf_mdl->get_pop_cible_projected ( $entity_arr, $entity_group_id, $zones );
		
		$pop = 0;
		foreach ( $entities_pop as $k => $v ) {
			$pop = $pop + round ( $v ['entity_pop'] * pow ( (1 + ($this->CI->config->item ( 'pop_growth_rate' ) / 100)), ($proj_year - $v ['entity_pop_year']) ) );
		}
		
		return $pop;
	}
	function get_filetypes_lookup_by_classes($classe) {
		$raw_filetypes = $this->CI->pbf_mdl->get_filetypes__entity_type ( $classe );
		
		$filetypes = array (
				'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
		);
		
		foreach ( $raw_filetypes as $key ) {
			$filetypes [$key ['filetype_id']] = $key ['filetype_name'];
		}
		return $filetypes;
	}
	function get_filetypes_lookup_by_classes_and_zones($classe, $zones) {
		$raw_filetypes = $this->CI->pbf_mdl->get_filetypes__entity_type_zone ( $classe, $zones );
		
		$filetypes = array (
				'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
		);
		
		foreach ( $raw_filetypes as $key ) {
			$filetypes [$key ['filetype_id']] = $key ['filetype_name'];
		}
		return $filetypes;
	}
	function get_filetypes__entity_type($classe) {
		$raw_filetypes = $this->CI->pbf_mdl->get_filetypes__entity_type ( $classe );
		
		// $filetypes = array('' => $this->CI->lang->line('app_form_dropdown_select'));
		
		foreach ( $raw_filetypes as $key ) {
			
			$filetypes [$key ['filetype_entity_id']] = array (
					$key ['entity_type_id'] => array (
							$key ['filetype_id'] => $key ['filetype_name'] 
					) 
			);
		}
		return $filetypes;
	}
	function get_filetypes__entity_type_zone($classe, $zones) {
		$raw_filetypes = $this->CI->pbf_mdl->get_filetypes__entity_type_zone ( $classe, $zones );
		
	
		
		$filetypes = array ();
		foreach ( $raw_filetypes as $key ) {
			
			$filetypes [$key ['filetype_entity_id']] = array (
					$key ['entity_type_id'] => array (
							$key ['filetype_id'] => $key ['filetype_name'] 
					) 
			);
		}
		return $filetypes;
	}
	function get_filetypes__entity_type2($classe) {
		$raw_filetypes = $this->CI->pbf_mdl->get_filetypes__entity_type2 ( $classe );
		
		foreach ( $raw_filetypes as $key ) {
			
			$filetypes [$key ['filetype_entity_id']] = array (
					$key ['geozone_id'] => array (
							$key ['entity_type_id'] => array (
									$key ['filetype_id'] => $key ['filetype_name'] 
							) 
					) 
			);
		}
		
		return $filetypes;
	}
	function get_entities_data_entry($class) {
		return $this->CI->pbf_mdl->get_entities ( $class );
	}
	function get_user_entities($user_id) {
		$this->CI->lang->load ( 'hfrentities', $this->CI->config->item ( 'language' ) );
		$user_entities = $this->CI->pbf_mdl->get_user_entities ( $user_id );
		$entities = array ();
		foreach ( $user_entities as $user_entity ) {
			$entities [$user_entity ['entity_id']] = $user_entity ['entity_name'] . ' ' . $this->CI->lang->line ( 'etty_typ_abbrv_ky_' . $user_entity ['entity_type_id'] );
		}
		return $entities;
	}
	function get_entities($lookup = false, $lkp_class = true, $class = '') {
		$raw_entities = $this->CI->pbf_mdl->get_entities ( $class );

		if ($lookup) {
			
			$entities = array (
					'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
			);
			
			foreach ( $raw_entities as $key ) {
				
				$entities [$key ['entity_id']] = array (
						($lkp_class) ? $key ['entity_class'] : $key ['entity_geozone_id'] => $key ['entity_name'] 
				);
			}
			
			return $entities;
		} else {
			return $raw_entities;
		}
	}
	function get_entities_donor($lookup = false, $lkp_class = true, $class = '', $donor_id = '') {
		$raw_entities = $this->CI->pbf_mdl->get_entities_donor ( $donor_id );
		
		if ($lookup) {
			
			$entities = array (
					'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
			);
			
			foreach ( $raw_entities as $key ) {
				
				$entities [$key ['entity_id']] = array (
						($lkp_class) ? $key ['entity_class'] : $key ['entity_geozone_id'] => $key ['entity_name'] 
				);
			}
			
			return $entities;
		} else {
			return $raw_entities;
		}
	}
	function get_entities_frm($geo_id = '2') {
		

		$raw_entities = $this->CI->pbf_mdl->get_entities_By_zone ( $geo_id );
		
		foreach ( $raw_entities as $key ) {
			
			$entities [$key ['entity_id']] = $key ['entity_name'];
		}
		
		return $entities;
	}
	function get_asset_properties($asset) {
		$fields = $this->CI->db->field_data ( $asset );
		
		$properties = array ();
		
		foreach ( $fields as $field ) {
			
			if (! in_array ( $field->name, array (
					'entity_id',
					'entity_name',
					'entity_active',
					'entity_type',
					'entity_geozone_id',
					'entity_related_entity',
					'entity_bank_id',
					'entity_bank_acc',
					'entity_class' 
			) )) {
				$properties [$field->name] = $field->name;
			}
		}
		
		return $properties;
	}
	function get_usersgroups($usersgroup_id = '') {
		$raw_groups = $this->CI->pbf_mdl->users_groups ( $usersgroup_id );
		
		$groups = array (
				'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
		);
		
		foreach ( $raw_groups as $key ) {
			
			$groups [$key ['usersgroup_id']] = $key ['usersgroup_name'];
		}
		
		return $groups;
	}
	function get_reports() {
		$raw_reports = $this->CI->pbf_mdl->get_reports ();
		
		$reports = array ();
		
		foreach ( $raw_reports as $key ) {
			
			$reports [$key ['report_id']] = $key ['report_title'];
		}
		
		return $reports;
	}
	

	function group_access_order($group) {
		$group_access_order = $this->CI->pbf_mdl->get_user_group_access ( $group );
		
		return $group_access_order;
	}
	function get_usersgroups_multiselect($usersgroup_id = '') {
		$raw_groups = $this->CI->pbf_mdl->users_groups ( $usersgroup_id );
		
		$groups = array ();
		
		foreach ( $raw_groups as $key ) {
			
			$groups [$key ['usersgroup_id']] = $key ['usersgroup_name'];
		}
		
		return $groups;
	}
	function get_default_user_group() {
		return $this->CI->pbf_mdl->get_default_user_group ();
	}
	function get_default_pbf_geo() {
		return $this->CI->pbf_mdl->get_default_pbf_geo ();
	}
	function get_resources($user_task_id = '', $as_resource = false) {
		$raw_resources = $this->CI->pbf_mdl->users_tasks ( $user_task_id );
		
		$resources = array (
				'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
		);
		
		foreach ( $raw_resources as $key ) {
			
			if ($as_resource === true) {
				$resources [$key ['usertask_name']] = $key ['usertask_name'];
			} else {
				$resources [$key ['usertask_id']] = $key ['usertask_name'];
			}
		}
		
		return $resources;
	}
	function get_principal_geo_info() {
		return $this->CI->pbf_mdl->get_principal_geo_info ();
	}
	function get_active_geo_info() {
		$geoinfo = $this->CI->pbf_mdl->get_active_geo_info ();
		
		return $geoinfo ['geo_id'];
	}
	function render_geozones($geo_id) {
		$raw_zones = $this->CI->pbf_mdl->render_geozones ( $geo_id );
		
		$zones = array ();
		
		foreach ( $raw_zones as $zones_val ) {
			

			if ($zones_val ['geozone_active'] == 1) {
				
				$zones ['links'] [] = anchor ( 'data/showzone/' . $zones_val ['geo_id'] . '/' . $zones_val ['geozone_id'], $zones_val ['geozone_name'] );
				
				$zones ['html_map'] [] = '<area id="area' . $zones_val ['geozone_id'] . '" shape="poly" state="' . $zones_val ['geozone_name'] . '" full="' . $zones_val ['geozone_name'] . '" coords="' . $zones_val ['geozone_htmlmap'] . '" href="' . base_url () . 'data/showzone/' . $zones_val ['geo_id'] . '/' . $zones_val ['geozone_id'] . '"/>';
			} else {
				
				$zones ['links'] [] = $zones_val ['geozone_name'];
			}
		}
		
		return $zones;
	}
	function render_child_zones($geo_id, $zone_id) {
		$raw_zones = $this->CI->pbf_mdl->render_child_zones ( $zone_id );
		
		$zones = array ();
		
		foreach ( $raw_zones as $zones_val ) {

			if ($zones_val ['geozone_active'] == 1) {
				
				$zones ['links'] [] = anchor ( 'data/showzone/' . $zones_val ['geo_id'] . '/' . $zones_val ['geozone_id'], $zones_val ['geozone_name'] );
				
				$zones ['html_map'] [] = '<area id="area' . $zones_val ['geozone_id'] . '" shape="poly" state="' . $zones_val ['geozone_name'] . '" full="' . $zones_val ['geozone_name'] . '" coords="' . $zones_val ['geozone_htmlmap'] . '" href="' . base_url () . 'data/showzone/' . $zones_val ['geo_id'] . '/' . $zones_val ['geozone_id'] . '"/>';
			} else {
				
				$zones ['links'] [] = $zones_val ['geozone_name'];
			}
		}
		
		return $zones;
	}
	function zone_entities_geo($zone_id) {
		return $this->CI->pbf_mdl->render_zone_entities ( $zone_id );
	}
	function render_zone_entities($zone_id) {
		$raw_entities = $this->CI->pbf_mdl->render_zone_entities ( $zone_id );
		
		$entities = array ();
		
		$current_type = $raw_entities [0] ['entity_type_name'];
		
		foreach ( $raw_entities as $raw_entities_val ) {
			
			if ($current_type != $raw_entities_val ['entity_type_name']) {
				$current_type = $raw_entities_val ['entity_type_name'];
				$entities ['links'] [$current_type] [] = anchor ( 'data/showentity/' . $raw_entities_val ['entity_id'], $raw_entities_val ['entity_name'] );
			} else {
				
				$entities ['links'] [$current_type] [] = anchor ( 'data/showentity/' . $raw_entities_val ['entity_id'], $raw_entities_val ['entity_name'] );
			}
		}
		
		return $entities;
	}
	function render_single_entity($entity_id) {
		return $this->CI->pbf_mdl->render_entity ( $entity_id );
	}
	function get_last_quarters($number = 2) {
		return $this->CI->pbf_mdl->get_last_quarters ( $number );
	}
	function get_last_budget_year($year) {
		return $this->CI->pbf_mdl->get_last_budget_year ( $year );
	}
	function get_all_quarters() {
		return $this->CI->pbf_mdl->get_all_quarters ();
	}
	function get_last_quarters_zone($number = 2, $zones) {
		return $this->CI->pbf_mdl->get_last_quarters_zone ( $number, $zones );
	}
	function get_last_periods($number = 2, $type) {
		return $this->CI->pbf_mdl->get_last_periods ( $number, $type );
	}
	function get_last_periods_zone($number = 2, $type, $zones) {
		return $this->CI->pbf_mdl->get_last_periods_zone ( $number, $type, $zones );
	}
	function remove_row_zero($rows) {
		foreach ( $rows as $key => $row ) {
			$data = array_slice ( $row, 1 );
			$num_zeros = 0;
			foreach ( $data as $d ) {
				$val = ( int ) str_replace ( ',', '', $d );
				
				if ($val == 0) {
					$num_zeros ++;
				}
			}
			
			if (count ( $data ) == $num_zeros) {
				unset ( $rows [$key] );
			}
		}
		
		return $rows;
	}
	function format_number($number, $decimals = 0) {
	
		$n = number_format ( $number, $decimals, $this->CI->lang->line ( 'decimal_separator' ), $this->CI->lang->line ( 'thousand_separator' ) );
		
		return $n;
	}
	function get_totals_class($arr_period, $zone_id) {
		$raw_totals_class = $this->CI->pbf_mdl->get_totals_class ( $arr_period, $zone_id );
		
		foreach ( $raw_totals_class as $fi_key => $fi_val ) {
			
			foreach ( $fi_val as $k => $t ) {
				if ($k != $this->CI->lang->line ( 'front_entity_class' )) {
					
					$r = $this->format_number ( ( int ) $t );
					$raw_totals_class [$fi_key] [$k] = $r;
				}
			}
			
			$raw_totals_class [$fi_key] [$this->CI->lang->line ( 'front_entity_class' )] = 

			anchor ( base_url () . 'data/payment/' . $raw_totals_class [$fi_key] ['entity_class_id'], $raw_totals_class [$fi_key] [$this->CI->lang->line ( 'front_entity_class' )] );
			
			unset ( $raw_totals_class [$fi_key] ['entity_class_id'] );
		}
		
		$raw_totals_class = $this->remove_row_zero ( $raw_totals_class );
		
		return $raw_totals_class;
		
	
	}
	function get_featured_indicators($arr_period) {
		return $this->CI->pbf_mdl->get_featured_indicators ( $arr_period, $this->config->item ( 'language_abbr' ) );
	}
	function get_featured_indic($arr_period, $zone, $entity, $entity_class, $table_field, $content_type) {
		$additional_segments = ($zone != '') ? '/' . $zone : '';
		
		$raw_featured_indic = $this->CI->pbf_mdl->get_featured_indic ( $arr_period, $zone, $entity, $entity_class, $table_field, $content_type, $this->CI->config->item ( 'language_abbr' ) );
		
		foreach ( $raw_featured_indic ['pbf_data'] as $fi_key => $fi_val ) {
			
			$raw_featured_indic ['pbf_data'] [$fi_key] [strtoupper ( $this->CI->lang->line ( 'front_data' ) )] = 
			
		
anchor ( base_url () . 'data/element/' . $raw_featured_indic ['pbf_data'] [$fi_key] ['indicator_id'] . $additional_segments, $raw_featured_indic ['pbf_data'] [$fi_key] [strtoupper ( $this->CI->lang->line ( 'front_data' ) )] );
			
			foreach ( $fi_val as $k => $v ) {
				if ($k != $this->CI->lang->line ( 'front_data' )) {
					$raw_featured_indic ['pbf_data'] [$fi_key] [$k] = $this->format_number ( str_replace ( ',', '', $v ) );
				}
			}
			unset ( $raw_featured_indic ['pbf_data'] [$fi_key] ['indicator_id'] );
		}
		
		return $raw_featured_indic;
	}
	function get_featured_indic_avg($arr_period, $zone, $entity, $entity_class, $table_field, $content_type) {
		$nbZones = 1;
		
		if ($entity != '') {
			$this->CI->load->model ( 'entities_mdl' );
			
			$entities = $this->CI->entities_mdl->count_entities ( $zone );
			
			$nbZones = $entities ['nb_entities'];
			$entity = '';
		} else {
			$zones = $this->CI->geo_mdl->count_child_zones ( $zone );
			$nbZones = $zones ['nbZones'];
		}
		
		$additional_segments = ($zone != '') ? '/' . $zone : '';
		
		$raw_featured_indic = $this->CI->pbf_mdl->get_featured_indic ( $arr_period, $zone, $entity, $entity_class, $table_field, $content_type, $this->CI->config->item ( 'language_abbr' ) );
		
		foreach ( $raw_featured_indic ['pbf_data'] as $fi_key => $fi_val ) {
			
			$raw_featured_indic ['pbf_data'] [$fi_key] [strtoupper ( $this->CI->lang->line ( 'front_data' ) )] = 

			anchor ( base_url () . 'data/element/' . $raw_featured_indic ['pbf_data'] [$fi_key] ['indicator_id'] . $additional_segments, $raw_featured_indic ['pbf_data'] [$fi_key] [strtoupper ( $this->CI->lang->line ( 'front_data' ) )] );
			
			foreach ( $fi_val as $k => $v ) {
				if ($k != $this->CI->lang->line ( 'front_data' )) {
					$raw_featured_indic ['pbf_data'] [$fi_key] [$k] = $this->format_number ( str_replace ( ',', '', $v ) / $nbZones );
				}
			}
			unset ( $raw_featured_indic ['pbf_data'] [$fi_key] ['indicator_id'] );
		}
		
		return $raw_featured_indic;
	}
	function get_featured_indic_export($arr_period, $zone, $entity, $entity_class, $table_field, $content_type) {
		$additional_segments = ($zone != '') ? '/' . $zone : '';
		
		$raw_featured_indic = $this->CI->pbf_mdl->get_featured_indic ( $arr_period, $zone, $entity, $entity_class, $table_field, $content_type, $this->CI->config->item ( 'language_abbr' ) );
		
		foreach ( $raw_featured_indic ['pbf_data'] as $fi_key => $fi_val ) {
			

			
			
			$raw_featured_indic['pbf_data'][$fi_key][strtoupper($this->CI->lang->line('front_data'))] =

						$raw_featured_indic['pbf_data'][$fi_key][strtoupper($this->CI->lang->line('front_data'))];
			
			foreach ( $fi_val as $k => $v ) {
				if ($k != $this->CI->lang->line ( 'front_data' )) {
					$raw_featured_indic ['pbf_data'] [$fi_key] [$k] = $this->format_number ( str_replace ( ',', '', $v ) );
				}
			}
			unset ( $raw_featured_indic ['pbf_data'] [$fi_key] ['indicator_id'] );
		}
		
		return $raw_featured_indic;
	}
	function get_computed_payments($arr_period, $zone, $entity, $entity_class) {
		$additional_segments = ($zone != '') ? '/' . $zone : '';
		
		$raw_computed_payments = $this->CI->pbf_mdl->get_computed_payments ( $arr_period, $zone, $entity, $entity_class );
		
		foreach ( $raw_computed_payments as $fi_key => $fi_val ) {
			
			$raw_computed_payments [$fi_key] [strtoupper ( $this->CI->lang->line ( 'front_data' ) )] = 

			anchor ( base_url () . 'data/payment/' . $entity_class . $additional_segments, $raw_computed_payments [$fi_key] [strtoupper ( $this->CI->lang->line ( 'front_data' ) )] );
			
			$zero_row = true;
			foreach ( $fi_val as $k => $v ) {
				$value = trim ( str_replace ( ',', '', $v ) );
				
				if (is_numeric ( $value )) {
					$formated_value = $this->format_number ( $value );
					
					if ($value != 0)
						$zero_row = false;
					$raw_computed_payments [$fi_key] [$k] = $formated_value;
				}
			}
			
			if ($zero_row) {
				unset ( $raw_computed_payments [$fi_key] );
			}
		}
		
		return $raw_computed_payments;
	}
	function get_entity_parent_payement($array_period, $entity_id) {
		$this->CI->load->model ( 'entities_mdl' );
		$entity_info = $this->CI->entities_mdl->get_entity ( $entity_id );
		
		// TODO 6 is hospital. To do improve this.
		if ($entity_info ['entity_type'] == '6') {
			$return = $this->get_hospital_payement ( $array_period, $entity_info ['parent_geozone_id'], $entity_info ['entity_class'] );
		} else {
			$return = $this->get_non_hospital_payement ( $array_period, $entity_info ['geozone_id'], $entity_info ['entity_class'] );
		}
		
		return $return;
	}
	function get_hospital_payement($arr_period, $zone_id, $entiy_class_id) {
		$raw_totals_class = $this->CI->pbf_mdl->get_hospital_payement ( $arr_period, $zone_id, $entiy_class_id );
		
		foreach ( $raw_totals_class as $fi_key => $fi_val ) {
			
			foreach ( $fi_val as $k => $t ) {
				if ($k != $this->CI->lang->line ( 'front_entity_class' )) {
					
					$r = $this->format_number ( ( int ) $t );
					$raw_totals_class [$fi_key] [$k] = $r;
				}
			}
			
			$raw_totals_class [$fi_key] [$this->CI->lang->line ( 'front_entity_class' )] = 

			anchor ( base_url () . 'data/payment/' . $raw_totals_class [$fi_key] ['entity_class_id'], $raw_totals_class [$fi_key] [$this->CI->lang->line ( 'front_entity_class' )] );
			
			unset ( $raw_totals_class [$fi_key] ['entity_class_id'] );
		}
		
		$totals_class = $this->remove_row_zero ( $raw_totals_class );
		
		return $totals_class;
	}
	
	/**
	 * Get fosa payement except hospital.
	 * Needed on fosa page to draw payement comparison graph
	 */
	function get_non_hospital_payement($arr_period, $zone_id, $entiy_class_id) {
		$raw_totals_class = $this->CI->pbf_mdl->get_non_hospital_payement ( $arr_period, $zone_id, $entiy_class_id );
		
		foreach ( $raw_totals_class as $fi_key => $fi_val ) {
			
			foreach ( $fi_val as $k => $t ) {
				if ($k != $this->CI->lang->line ( 'front_entity_class' )) {
					
					$r = $this->format_number ( ( int ) $t );
					$raw_totals_class [$fi_key] [$k] = $r;
				}
			}
			
			$raw_totals_class [$fi_key] [$this->CI->lang->line ( 'front_entity_class' )] = 

			anchor ( base_url () . 'data/payment/' . $raw_totals_class [$fi_key] ['entity_class_id'], $raw_totals_class [$fi_key] [$this->CI->lang->line ( 'front_entity_class' )] );
			
			unset ( $raw_totals_class [$fi_key] ['entity_class_id'] );
		}
		
		$totals_class = $this->remove_row_zero ( $raw_totals_class );
		
		return $totals_class;
	}
	function get_computed_payments_export($arr_period, $zone, $entity, $entity_class) {
		$additional_segments = ($zone != '') ? '/' . $zone : '';
		
		$raw_computed_payments = $this->CI->pbf_mdl->get_computed_payments ( $arr_period, $zone, $entity, $entity_class );
		
		foreach ( $raw_computed_payments as $fi_key => $fi_val ) {
			
			$zero_row = true;
			foreach ( $fi_val as $k => $v ) {
				$value = trim ( str_replace ( ',', '', $v ) );
				
				if (is_numeric ( $value )) {
					$formated_value = $this->format_number ( $value );
					
					if ($value != 0)
						$zero_row = false;
					$raw_computed_payments [$fi_key] [$k] = $formated_value;
				}
			}
			
			if ($zero_row) {
				unset ( $raw_computed_payments [$fi_key] );
			}
		}
		
		return $raw_computed_payments;
	}
	function get_avg_perfomance($arr_period, $zone, $entity, $entity_class) {
		$additional_segments = ($zone != '') ? '/' . $zone : '';
		
		$raw_avg_perfomance = $this->CI->pbf_mdl->get_avg_perfomance ( $arr_period, $zone, $entity, $entity_class );
		
		foreach ( $raw_avg_perfomance as $fi_key => $fi_val ) {
			
			$raw_avg_perfomance [$fi_key] [strtoupper ( $this->CI->lang->line ( 'front_data' ) )] = 

			anchor ( base_url () . 'data/perfomance/' . $entity_class . $additional_segments, $raw_avg_perfomance [$fi_key] [strtoupper ( $this->CI->lang->line ( 'front_data' ) )] );
			
			$zero_row = true;
			foreach ( $fi_val as $k => $v ) {
				$value = trim ( str_replace ( ',', '', $v ) );
				
				if (is_numeric ( $value )) {
					$formated_value = $this->format_number ( $value, 2 ) . ' %';
					
					if ($value != 0)
						$zero_row = false;
					$raw_avg_perfomance [$fi_key] [$k] = $formated_value;
				}
			}
			
			if ($zero_row) {
				unset ( $raw_avg_perfomance [$fi_key] );
			}
		}
		
		return $raw_avg_perfomance;
	}
	function get_last_quantities_reports($zone, $entity) {
		$periods = $this->get_last_quarters ( 1 );
		
		$months = $this->get_monthsBy_quarter ( $periods [0] ['data_quarter'] );
		
		$raw_last_quantities_reports = $this->CI->pbf_mdl->get_last_quantities_reports ( $months [2], $periods [0] ['data_year'], $zone, $entity );
		
		foreach ( $raw_last_quantities_reports as $fi_key => $fi_val ) {
			
			$keys = array_keys ( $fi_val );
			
			$raw_last_quantities_reports [$fi_key] [$keys [0]] = $raw_last_quantities_reports [$fi_key] [$keys [0]];
		}
		
		return $raw_last_quantities_reports;
	}
	function get_element_details($periods, $indicator_id, $geozone_id) {
		$element_details = $this->CI->pbf_mdl->get_element_details ( $periods, $indicator_id, $geozone_id );
		
		if ($geozone_id != $this->CI->pbf_mdl->get_rqst_geozone_ids ( $geozone_id ) || $geozone_id == '') {
			foreach ( $element_details as $fi_key => $fi_val ) {
				
				$element_details [$fi_key] [strtoupper ( $this->CI->lang->line ( 'front_element_zone' ) )] = 

				anchor ( base_url () . 'data/element/' . $indicator_id . '/' . $element_details [$fi_key] ['geozone_id'], $element_details [$fi_key] [strtoupper ( $this->CI->lang->line ( 'front_element_zone' ) )] );
				
				unset ( $element_details [$fi_key] ['geozone_id'] );
			}
		} elseif ($geozone_id != '' && $geozone_id == $this->CI->pbf_mdl->get_rqst_geozone_ids ( $geozone_id )) {
			
			foreach ( $element_details as $fi_key => $fi_val ) {
				
				$element_details [$fi_key] [strtoupper ( $this->CI->lang->line ( 'front_data_fosa' ) )] = 

				anchor ( base_url () . 'data/showentity/' . $element_details [$fi_key] ['entity_id'], $element_details [$fi_key] [strtoupper ( $this->CI->lang->line ( 'front_data_fosa' ) )] );
				
				unset ( $element_details [$fi_key] ['entity_id'] );
			}
		}
		
		return $element_details;
	}
	function get_performance_details($periods, $entity_class, $geozone_id) {
		$element_details = $this->CI->pbf_mdl->get_performance_details ( $periods, $entity_class, $geozone_id );
		
		$data_label = ($entity_class == 2) ? $this->CI->lang->line ( 'front_admin_data_label' ) : $this->CI->lang->line ( 'front_data_label' );
		
		if ($geozone_id != $this->CI->pbf_mdl->get_rqst_geozone_ids ( $geozone_id ) || $geozone_id == '') {
			
			foreach ( $element_details as $fi_key => $fi_val ) {
				
				$element_details [$fi_key] [strtoupper ( $this->CI->lang->line ( 'front_element_zone' ) )] = 

				anchor ( base_url () . 'data/perfomance/' . $entity_class . '/' . $element_details [$fi_key] ['geozone_id'], $element_details [$fi_key] [strtoupper ( $this->CI->lang->line ( 'front_element_zone' ) )] );
				
				unset ( $fi_val ['geozone_id'] );
				
				$zero_row = true;
				
				foreach ( $fi_val as $k => $v ) {
					$value = trim ( str_replace ( ',', '', $v ) );
					
					if (is_numeric ( $value )) {
						
						if ($value != 0) {
							$zero_row = false;
						}
						$formated_value = $this->format_number ( $value, 2 ) . ' %';
						
						$element_details [$fi_key] [$k] = $formated_value;
					}
				}
				if ($zero_row) {
					
					unset ( $element_details [$fi_key] );
				}
				
				unset ( $element_details [$fi_key] ['geozone_id'] );
			}
		} 

		elseif ($geozone_id != '' && $geozone_id == $this->CI->pbf_mdl->get_rqst_geozone_ids ( $geozone_id )) {
			
			foreach ( $element_details as $fi_key => $fi_val ) {
				
				$element_details [$fi_key] [strtoupper ( $this->CI->lang->line ( 'front_data_fosa' ) )] = 

				anchor ( base_url () . 'data/showentity/' . $element_details [$fi_key] ['entity_id'], $element_details [$fi_key] [strtoupper ( $this->CI->lang->line ( 'front_data_fosa' ) )] );
				
				unset ( $fi_val ['entity_id'] );
				
				$zero_row = true;
				
				foreach ( $fi_val as $k => $v ) {
					$value = trim ( str_replace ( ',', '', $v ) );
					
					if (is_numeric ( $value )) {
						
						if ($value != 0) {
							$zero_row = false;
						}
						$formated_value = $this->format_number ( $value, 2 ) . ' %';
						
						$element_details [$fi_key] [$k] = $formated_value;
					}
				}
				if ($zero_row) {
					
					unset ( $element_details [$fi_key] );
				}
				
				unset ( $element_details [$fi_key] ['entity_id'] );
			}
		}
		
		return $element_details;
	}
	function get_payment_details($periods, $entity_class, $geozone_id) {
		$element_details = $this->CI->pbf_mdl->get_payment_details ( $periods, $entity_class, $geozone_id );
		
		if ($geozone_id != $this->CI->pbf_mdl->get_rqst_geozone_ids ( $geozone_id ) || $geozone_id == '') {
			
			foreach ( $element_details as $fi_key => $fi_val ) {
				
				$element_details [$fi_key] [strtoupper ( $this->CI->lang->line ( 'front_element_zone' ) )] = 

				anchor ( base_url () . 'data/payment/' . $entity_class . '/' . $element_details [$fi_key] ['geozone_id'], $element_details [$fi_key] [strtoupper ( $this->CI->lang->line ( 'front_element_zone' ) )] );
				
				unset ( $fi_val ['geozone_id'] );
				
				$zero_row = true;
				
				foreach ( $fi_val as $k => $v ) {
					$value = trim ( str_replace ( ',', '', $v ) );
					
					if (is_numeric ( $value )) {
						if ($value != 0) {
							$zero_row = false;
						}
						$formated_value = $this->format_number ( $value ) . " " . $this->CI->config->item ( 'app_country_currency' );
						
						$element_details [$fi_key] [$k] = $formated_value;
					}
				}
				if ($zero_row) {
					unset ( $element_details [$fi_key] );
				}
				
				unset ( $element_details [$fi_key] ['geozone_id'] );
			}
		} 

		elseif ($geozone_id != '' && $geozone_id == $this->CI->pbf_mdl->get_rqst_geozone_ids ( $geozone_id )) {
			
			foreach ( $element_details as $fi_key => $fi_val ) {
				
				$element_details [$fi_key] [strtoupper ( $this->CI->lang->line ( 'front_data_fosa' ) )] = 

				anchor ( base_url () . 'data/showentity/' . $element_details [$fi_key] ['entity_id'], $element_details [$fi_key] [strtoupper ( $this->CI->lang->line ( 'front_data_fosa' ) )] );
				
				unset ( $fi_val ['entity_id'] );
				
				$zero_row = true;
				
				foreach ( $fi_val as $k => $v ) {
					$value = trim ( str_replace ( ',', '', $v ) );
					if (is_numeric ( $value )) {
						if ($value != 0) {
							
							$zero_row = false;
						}
						$formated_value = $this->format_number ( $value ) . " " . $this->CI->config->item ( 'app_country_currency' );
						
						$element_details [$fi_key] [$k] = $formated_value;
					}
				}
				
				unset ( $element_details [$fi_key] ['entity_id'] );
				
				if ($zero_row) {
					unset ( $element_details [$fi_key] );
				}
			}
		}
		
		return $element_details;
	}
	function get_payment_details_export($periods, $entity_class, $geozone_id) {
		$element_details = $this->CI->pbf_mdl->get_payment_details ( $periods, $entity_class, $geozone_id );
		
		if ($geozone_id != $this->CI->pbf_mdl->get_rqst_geozone_ids ( $geozone_id ) || $geozone_id == '') {
			
			foreach ( $element_details as $fi_key => $fi_val ) {
				
				unset ( $fi_val ['geozone_id'] );
				
				$zero_row = true;
				
				foreach ( $fi_val as $k => $v ) {
					$value = trim ( str_replace ( ',', '', $v ) );
					
					if (is_numeric ( $value )) {
						if ($value != 0) {
							$zero_row = false;
						}
						$formated_value = $this->format_number ( $value );
						
						$element_details [$fi_key] [$k] = $value;
					}
				}
				if ($zero_row) {
					unset ( $element_details [$fi_key] );
				}
				
				unset ( $element_details [$fi_key] ['geozone_id'] );
			}
		} 

		elseif ($geozone_id != '' && $geozone_id == $this->CI->pbf_mdl->get_rqst_geozone_ids ( $geozone_id )) {
			
			foreach ( $element_details as $fi_key => $fi_val ) {
				
				unset ( $fi_val ['entity_id'] );
				
				$zero_row = true;
				
				foreach ( $fi_val as $k => $v ) {
					$value = trim ( str_replace ( ',', '', $v ) );
					if (is_numeric ( $value )) {
						if ($value != 0) {
							
							$zero_row = false;
						}
						$formated_value = $this->format_number ( $value );
						
						$element_details [$fi_key] [$k] = $value;
					}
				}
				
				unset ( $element_details [$fi_key] ['entity_id'] );
				
				if ($zero_row) {
					unset ( $element_details [$fi_key] );
				}
			}
		}
		
		return $element_details;
	}
	function clean_table_for_front($table) {
		if (isset ( $table [0] )) {
			$keys = $table [0];
			unset ( $keys [0] );
			
			foreach ( $keys as $key ) {
				
				$col_assessment = NULL;
				
				foreach ( $table as $array_keys => $array_vars ) {
					
					if ($array_keys != 0) {
						
						$col_assessment += $array_vars [$key];
					}
				}
				
				if (is_null ( $col_assessment ) || $col_assessment == 0) {
					
					$deletable_key = array_search ( $key, $table [0] );
					
					unset ( $table [0] [$deletable_key] );
					
					foreach ( $table as $array_keys => $array_vars ) {
						
						unset ( $table [$array_keys] [$key] );
					}
				}
			}
		}
		
		return $table;
	}
	function get_indicators_by_filetype() {
		$indicatorz = $this->CI->pbf_mdl->get_indicators_by_filetype ();
		
		$resources = array (
				'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
		);
		
		foreach ( $indicatorz as $key ) {
			
			$resources [$key ['indicator_id']] = $key ['indicator_title'];
		}
		
		return $resources;
	}
	function get_report_param($param_identifier) {
		switch ($param_identifier) {
			
			case 'datafile_semester' :
				$semesters = array (
						'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
				) + $this->get_semesters_list ();
				return '<p>' . form_label ( $this->CI->lang->line ( 'report_param_semester' ), 'datafile_month' ) . form_dropdown ( 'datafile_month', $semesters, '', 'id="datafile_month" class="year"' ) . '</p>';
				break;
			
			case 'datafile_quarter' :
				$quarters = array (
						'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
				) + $this->get_months_list ( true );
				return '<p>' . form_label ( $this->CI->lang->line ( 'report_param_trimestre' ), 'datafile_quarter' ) . form_dropdown ( 'datafile_quarter', $quarters, '', 'id="datafile_quarter" class="year"' ) . '</p>';
				break;
			
			case 'datafile_month' :
				$months = array (
						'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
				) + $this->get_months_list ();
				return '<p>' . form_label ( $this->CI->lang->line ( 'report_param_month' ), 'datafile_month' ) . form_dropdown ( 'datafile_month', $months, '', 'id="datafile_month" class="year"' ) . '</p>';
				break;
			
			case 'datafile_year' :
				// $years = $this->get_years_list(1);
				$currentYear = date ( 'Y' );
				$startYear = $this->CI->pbf_mdl->get_oldest_datafile_year ();
				$nbYears = $currentYear - $startYear ['datafile_year'];
				$years = array (
						'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
				) + $this->get_years_list ( $nbYears );
				return '<p>' . form_label ( $this->CI->lang->line ( 'report_param_year' ), 'datafile_year' ) . form_dropdown ( 'datafile_year', $years, '', 'id="datafile_year" class="year"' ) . '</p>';
				break;
			
			case 'level_0' :
				$this->CI->load->model ( 'geo_mdl' );
				
				$zones = array ();
				
				$usergeozones = $this->CI->session->userdata ( 'usergeozones' );
				
				if (empty ( $usergeozones )) {
					$zones_array = $this->CI->geo_mdl->get_regions ();
					
					foreach ( $zones_array as $r ) {
						$zones [$r ['geozone_id']] = $r ['geozone_name'];
					}
				} else {
					foreach ( $usergeozones as $ugz ) {
						$parent = $this->CI->geo_mdl->get_parent_geozone ( $ugz );
						
						if (! array_key_exists ( $parent ['geozone_id'], $zones )) {
							$zones [$parent ['geozone_id']] = $parent ['geozone_name'];
						}
					}
				}
				
				return '<p>' . form_label ( $this->CI->lang->line ( 'report_param_zone' ), 'datafile_zone' ) . form_dropdown ( 'level_0', $zones, '', 'id="level_0" class="zone"' ) . '</p>';
				break;
			
			case 'entity_geozone_id' :
				return form_cascaded_geozones ( 'entity_geozone_id', true );
				break;
			
			case 'entity_id' :
				
				$entities = $this->get_entities ( true, false, 1 );
				
				return form_cascaded_geozones ( 'entity_geozone_id', true ) . '<p>' . form_label ( $this->CI->lang->line ( 'app_frm_entity' ), 'entity_id' ) . form_cascaded_dropdown ( 'entity_id', $entities, '', 'id="entity_id"' ) . '</p>';
				break;
			
			case 'entity_id_reg' :
				
				$entities = $this->get_entities ( true, false, 2 );
				
				return form_cascaded_geozones ( 'entity_geozone_id', true ) . '<p>' . form_label ( $this->CI->lang->line ( 'app_frm_entity' ), 'entity_id' ) . form_cascaded_dropdown ( 'entity_id', $entities, '', 'id="entity_id"' ) . '</p>';
				break;
			
			case 'entity_id_mutuelles' :
				$entities = $this->get_entities ( true, false, 3 );
				
				return form_cascaded_geozones ( 'entity_geozone_id', true ) . '<p>' . form_label ( $this->CI->lang->line ( 'app_frm_entity' ), 'entity_id' ) . form_cascaded_dropdown ( 'entity_id', $entities, '', 'id="entity_id"' ) . '</p>';
				break;
			
			case 'entity_id_ECD' :
				$entities = $this->get_entities ( true, false, 2 );
				
				return form_cascaded_geozones ( 'entity_geozone_id', true ) . '<p>' . form_label ( $this->CI->lang->line ( 'app_frm_entity' ), 'entity_id' ) . form_cascaded_dropdown ( 'entity_id', $entities, '', 'id="entity_id"' ) . '</p>';
				break;
			
			case 'data_elements' :
				return '<p>' . form_label ( $this->CI->lang->line ( 'report_param_data_element' ), 'data_element' ) . form_dropdown ( 'data_element', $this->get_indicators_by_filetype (), '', 'id="data_element" class="longtext"' ) . '</p>';
				break;
			
			case 'entity_group_id' :
				$groups_id = array (
						'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
				) + $this->get_entity_groups ();
				
				array_push ( $groups_id, 'T1+C1', 'T1+C1+C2', 'All' );
				
				return '<p>' . form_label ( $this->CI->lang->line ( 'report_param_group' ), 'entity_group_id' ) . form_dropdown ( 'entity_group_id', $groups_id, '', 'id="entity_group_id" class="group"' ) . '</p>';
				
				break;
			
			default :
				
				if (preg_match ( '/entity_id/i', $param_identifier )) {
					
					$param_identifier = explode ( '_', $param_identifier );
					
					$entities = $this->get_entities ( true, false, $param_identifier [2] );
					return form_cascaded_geozones ( 'entity_geozone_id', true ) . '<p>' . form_label ( $this->CI->lang->line ( 'app_frm_entity' ), 'entity_id' ) . form_cascaded_dropdown ( 'entity_id', $entities, '', 'id="entity_id"' ) . '</p>';
				}
				
				break;
		}
	}
	function get_report_param_donor($param_identifier, $donor_id) {
		switch ($param_identifier) {
			
			case 'datafile_semester' :
				$semesters = array (
						'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
				) + $this->get_semesters_list ();
				return '<p>' . form_label ( $this->CI->lang->line ( 'report_param_semester' ), 'datafile_month' ) . form_dropdown ( 'datafile_month', $semesters, '', 'id="datafile_month" class="year"' ) . '</p>';
				break;
			
			case 'datafile_quarter' :
				$quarters = array (
						'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
				) + $this->get_months_list ( true );
				return '<p>' . form_label ( $this->CI->lang->line ( 'report_param_trimestre' ), 'datafile_quarter' ) . form_dropdown ( 'datafile_quarter', $quarters, '', 'id="datafile_quarter" class="year"' ) . '</p>';
				break;
			
			case 'datafile_month' :
				$months = array (
						'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
				) + $this->get_months_list ();
				return '<p>' . form_label ( $this->CI->lang->line ( 'report_param_month' ), 'datafile_month' ) . form_dropdown ( 'datafile_month', $months, '', 'id="datafile_month" class="year"' ) . '</p>';
				break;
			
			case 'datafile_year' :
				// $years = $this->get_years_list(1);
				$currentYear = date ( 'Y' );
				$startYear = $this->CI->pbf_mdl->get_oldest_datafile_year ();
				$nbYears = $currentYear - $startYear ['datafile_year'];
				$years = array (
						'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
				) + $this->get_years_list ( $nbYears );
				return '<p>' . form_label ( $this->CI->lang->line ( 'report_param_year' ), 'datafile_year' ) . form_dropdown ( 'datafile_year', $years, '', 'id="datafile_year" class="year"' ) . '</p>';
				break;
			
			case 'level_0' :
				$this->CI->load->model ( 'geo_mdl' );
				
				$zones = array ();
				
				$usergeozones = $this->CI->session->userdata ( 'usergeozones' );
				
				if (empty ( $usergeozones )) {
					$zones_array = $this->CI->geo_mdl->get_regions ();
					
					foreach ( $zones_array as $r ) {
						$zones [$r ['geozone_id']] = $r ['geozone_name'];
					}
				} else {
					foreach ( $usergeozones as $ugz ) {
						$parent = $this->CI->geo_mdl->get_parent_geozone ( $ugz );
						
						if (! array_key_exists ( $parent ['geozone_id'], $zones )) {
							$zones [$parent ['geozone_id']] = $parent ['geozone_name'];
						}
					}
				}
				
				return '<p>' . form_label ( $this->CI->lang->line ( 'report_param_zone' ), 'datafile_zone' ) . form_dropdown ( 'level_0', $zones, '', 'id="level_0" class="zone"' ) . '</p>';
				break;
			
			case 'entity_geozone_id' :
				return form_cascaded_geozones ( 'entity_geozone_id', true );
				break;
			
			case 'entity_id' :
				
				$entities = $this->get_entities_donor ( true, false, 1, $donor_id );
				
				return form_cascaded_geozones ( 'entity_geozone_id', true ) . '<p>' . form_label ( $this->CI->lang->line ( 'app_frm_entity' ), 'entity_id' ) . form_cascaded_dropdown ( 'entity_id', $entities, '', 'id="entity_id"' ) . '</p>';
				break;
			
			case 'entity_id_mutuelles' :
				$entities = $this->get_entities ( true, false, 3 );
				
				return form_cascaded_geozones ( 'entity_geozone_id', true ) . '<p>' . form_label ( $this->CI->lang->line ( 'app_frm_entity' ), 'entity_id' ) . form_cascaded_dropdown ( 'entity_id', $entities, '', 'id="entity_id"' ) . '</p>';
				break;
			
			case 'entity_id_ECD' :
				$entities = $this->get_entities ( true, false, 2 );
				
				return form_cascaded_geozones ( 'entity_geozone_id', true ) . '<p>' . form_label ( $this->CI->lang->line ( 'app_frm_entity' ), 'entity_id' ) . form_cascaded_dropdown ( 'entity_id', $entities, '', 'id="entity_id"' ) . '</p>';
				break;
			
			case 'data_elements' :
				return '<p>' . form_label ( $this->CI->lang->line ( 'report_param_data_element' ), 'data_element' ) . form_dropdown ( 'data_element', $this->get_indicators_by_filetype (), '', 'id="data_element" class="longtext"' ) . '</p>';
				break;
			
			case 'entity_group_id' :
				$groups_id = array (
						'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
				) + $this->get_entity_groups ();
				
				array_push ( $groups_id, 'T1+C1', 'T1+C1+C2', 'All' );
				
				return '<p>' . form_label ( $this->CI->lang->line ( 'report_param_group' ), 'entity_group_id' ) . form_dropdown ( 'entity_group_id', $groups_id, '', 'id="entity_group_id" class="group"' ) . '</p>';
				
				break;
			
			default :
				
				if (preg_match ( '/entity_id/i', $param_identifier )) {
					
					$param_identifier = explode ( '_', $param_identifier );
					
					$entities = $this->get_entities ( true, false, $param_identifier [2] );
					return form_cascaded_geozones ( 'entity_geozone_id', true ) . '<p>' . form_label ( $this->CI->lang->line ( 'app_frm_entity' ), 'entity_id' ) . form_cascaded_dropdown ( 'entity_id', $entities, '', 'id="entity_id"' ) . '</p>';
				}
				
				break;
		}
	}
	function get_param_caption($param_identifier, $id) {
		switch ($param_identifier) {
			
			case 'entity_id' :
				
				if (! empty ( $id )) {
					
					$this->CI->load->model ( 'entities_mdl' );
					
					$entity = $this->CI->entities_mdl->get_entity ( $id );
					// return strtoupper(utf8_decode($this->CI->lang->line('app_frm_entity'))).''.utf8_decode($entity['entity_name']).' '.$entity['entity_type_abbrev'].' '.strtoupper(utf8_decode($this->CI->lang->line('report_prod_groupe'))).' '.$entity['entity_group_abbrev'].' '.strtoupper($this->CI->lang->line('report_param_district')).' '.$entity['geozone_name'];
					
					return utf8_decode ( $this->CI->lang->line ( 'app_frm_entity' ) ) . '' . utf8_decode ( $entity ['entity_name'] ) . ' ' . $entity ['entity_type_abbrev'] . '  ' . utf8_decode ( $this->CI->lang->line ( 'report_prod_groupe' ) ) . ' ' . $entity ['entity_group_abbrev'] . '  ' . $this->CI->lang->line ( 'report_param_district' ) . ' ' . $entity ['geozone_name'];
				} else {
					return;
				}
				
				break;
			
			case 'entity_geozone_id' :
				
				$this->CI->load->model ( 'geo_mdl' );
				
				$geozone = $this->CI->geo_mdl->get_zone ( $id );
				
				return strtoupper ( $this->CI->lang->line ( 'report_param_district' ) ) . ' ' . $geozone ['geozone_name']; // DISTRICT IS SUPPOSED TO BE THE ZONE CLASS TITLE...
				
				break;
			
			case 'datafile_year' :
				
				if (! empty ( $id )) {
					return utf8_decode ( $this->CI->lang->line ( 'report_param_year' ) ) . ' ' . $id;
				} else {
					return;
				}
				
				break;
			
			case 'datafile_month' :
				
				return strtoupper ( $this->CI->lang->line ( 'report_param_month' ) ) . ' ' . utf8_decode ( $this->CI->lang->line ( 'app_month_' . $id ) );
				
				break;
			
			case 'datafile_quarter' :
				
				return strtoupper ( $this->CI->lang->line ( 'report_param_period' ) ) . ' ' . $this->CI->lang->line ( 'app_quarter_' . $id );
				
				break;
			
			case 'entity_group_id' :
				
				$this->CI->load->model ( 'entities_mdl' );
				
				$group = $this->CI->entities_mdl->get_entitygroup ( $id );
				
				if ($id == 5)
					$group = array (
							'entity_group_id' => '5',
							'entity_group_name' => 'T1+C1' 
					);
				if ($id == 6)
					$group = array (
							'entity_group_id' => '6',
							'entity_group_name' => 'T1+C1+C2' 
					);
				if ($id == 7)
					$group = array (
							'entity_group_id' => '6',
							'entity_group_name' => 'All' 
					);
				
				return strtoupper ( $this->CI->lang->line ( 'report_param_group' ) ) . ' ' . $group ['entity_group_name']; // DISTRICT IS SUPPOSED TO BE THE ZONE CLASS TITLE...
				                                                                                                
				// return $id;
				
				break;
			
			default :
				
				if (preg_match ( '/entity_id/i', $param_identifier )) {
					
					$this->CI->load->model ( 'entities_mdl' );
					
					$entity = $this->CI->entities_mdl->get_entity ( $id );
					
					return strtoupper ( $this->CI->lang->line ( 'app_frm_entity' ) ) . ' : ' . $entity ['entity_name'];
				} else {
					
					return $param_identifier . ' : ' . $id;
				}
				
				break;
		}
	}
	function get_controllers() {
		
		// $this->router->fetch_class();
		// $this->router->fetch_method();
		// $this -> router -> fetch_module(); //Module Name if you are using HMVC Component
		$controllers = array (
				'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
		);
		
		foreach ( glob ( APPPATH . 'controllers/*' . EXT ) as $controller ) {
			
			// if(!in_array(basename($controller, EXT), array('translator'))){
			require_once ($controller);
			$methods = get_class_methods ( basename ( $controller, EXT ) );
			// $controllers[] = basename($controller, EXT);
			
			foreach ( $methods as $method_key => $method_var ) {
				if (! in_array ( $method_var, array (
						'__construct',
						'get_instance' 
				) )) {
					$method_var = ($method_var == 'index') ? '' : $method_var . '/';
					
					$controllers [basename ( $controller, EXT ) . '/' . $method_var] = basename ( $controller, EXT ) . '/' . $method_var;
				}
			}
			
			// }
		}
		
		return $controllers;
	}
	function get_geozone_breadcrumb($geozone_id) {
		return $this->CI->pbf_mdl->get_geozone_breadcrumb ( $geozone_id );
	}
	function get_entity_breadcrumb($entity_id) {
		$breadcrumb = $this->CI->pbf_mdl->get_entity_breadcrumb ( $entity_id );
		
		return $breadcrumb;
		
		// return anchor(base_url().'data/showzone/'.$breadcrumb['lvl1_link'],$breadcrumb['lvl1_title']).' > '.anchor(base_url().'data/showzone/'.$breadcrumb['lvl2_link'],$breadcrumb['lvl2_title']);
	}
	function get_feature_accounts() {
		return $this->CI->pbf_mdl->get_feature_accounts ();
	}
	function get_edito($pos) {
		$id_pos = $this->CI->pbf_mdl->get_edito ( $pos );
		
		if (isset ( $id_pos ['content_id'] )) {
			$ret = $this->CI->pbf_mdl->get_edito_translation ( $id_pos ['content_id'], $this->CI->config->item ( 'language_abbr' ) );
			if (isset ( $ret ['html_block'] ))
				return $ret ['html_block'];
		} else
			return null;
	}
	function get_keydata($zone = '') {
		$d_pc = $this->CI->pbf_mdl->get_keydata ( $zone );
		foreach ( $d_pc as $d ) {
			if (isset ( $d ['popcible_id'] )) {
				$kd [] = array (
						'data' => $this->CI->lang->line ( 'dataelmt_key_' . $d ['popcible_id'] ),
						'value' => $d ['popcible_percentage'] 
				);
			}
		}
		
		return $kd;
	}
	function extract_url($link) {
		preg_match ( '_<a href=(.*?)>(.*?)</a_i', $link, $match );
		
		$url = str_replace ( '"', '', $match [1] );
		
		return $url;
	}
	function extract_text($link) {
		preg_match_all ( '_<a.*?>(.*?)</a_i', $link, $matches );
		
		$text = $matches [1] [0];
		
		return $text;
	}
	function render_geo_json($geo_id, $district_id = 0) {
		$json_data = $this->CI->pbf_mdl->render_geojson ( $geo_id, $district_id );
		$results = array ();
		
		foreach ( $json_data as $data ) {
			
			$result ['active'] = $data ['geozone_active'];
			$result ['zone_name'] = $data ['geozone_name'];
			$result ['url'] = site_url () . 'data/showzone/' . $data ['geo_id'] . '/' . $data ['geozone_id'] . '.html';
			$result ['geometries'] = $data ['geozone_geojson'];
			$result ['entities'] = $this->CI->pbf_mdl->count_entities ( $data ['geozone_id'], empty ( $district_id ) );
			$results [] = $result;
		}
		
		return $results;
	}
	function render_geo_borders($geo_id) {
		return $this->CI->pbf_mdl->get_zone_borders ( $geo_id );
	}
	function get_center_coords($geo_id) {
		return $this->CI->pbf_mdl->get_center_coords ( $geo_id );
	}
	function get_top_score($zone = '', $zone_type = '') {
		$periods = $this->get_last_quarters ( $this->CI->config->item ( 'num_period_display' ) );
		
		$last_period = $periods [count ( $periods ) - 1];
		
		$previous_period = $periods [count ( $periods ) - 2];
		
		$data_last_period = $this->CI->pbf_mdl->get_top_quality ( $last_period, $zone, $zone_type );
		// print_r($data_last_period);
		$data_previous_period = $this->CI->pbf_mdl->get_top_quality ( $previous_period, $zone, $zone_type );
		$data = array ();
		
		// $data[] = array($this->CI->lang->line('app_menu_15'),'');
		
		foreach ( $data_last_period as $k => $d ) {
			$row ['entity'] = anchor ( 'data/showentity/' . $d ['entity_id'], $d ['entity_name'] . ' (' . $d ['entity_type_abbrev'] . ') ' );
			$row ['montant'] = round ( $d ['datafile_total'] ) . '%';
			$row ['picture'] = $d ['entity_picturepath'];
			$row_previous = $data_previous_period [$k];
			$row ['previous'] = $row_previous ['datafile_total'];
			$row ['comparaison'] = round ( $d ['datafile_total'] ) > round ( $row_previous ['datafile_total'] ) ? 'up' : 'down';
			// echo "Data file:".$d['datafile_total'].":".$row_previous['datafile_total']."<br/>";
			array_push ( $data, $row );
		}
		
		return $data;
	}
	function get_top($zone = '') {
		$this->CI->load->model ( 'entities_mdl' );
		$top = $this->CI->pbf_mdl->get_topcms ();
		// quantity = 12, quality = 13
		$content_type = $this->CI->pbf_mdl->get_lookup_id ( $top ['type'] );
		$params = json_decode ( $top ['content_params'], true );
		
		$keys = array ();
		
		foreach ( $params as $param ) {
			$entity_type = $this->CI->entities_mdl->get_entitytype ( $param );
			$keys [$entity_type ['entity_type_name'] . '( ' . $entity_type ['entity_type_abbrev'] . ' )'] = $entity_type ['entity_type_id'];
		}
		
		$result = array ();
		
		$limit = $top ['amount'];
		$periods = $this->get_last_quarters ( $this->CI->config->item ( 'num_period_display' ) );
		
		$last_period = $periods [count ( $periods ) - 1];
		
		$previous_period = $periods [count ( $periods ) - 2];
		
		$r = array ();
		
		if (! empty ( $content_type )) {
			
			foreach ( $keys as $key => $value ) {
				
				// check if distict or zone
				$zone_type = $this->CI->db->get_where ( 'pbf_geozones', array (
						'geozone_id' => $zone 
				) )->result_array ();
				if ($zone_type [0] ['geozone_parentid'] != '') { // district
					$rep = $this->CI->pbf_mdl->get_top_district ( $value, $content_type ['lookup_id'], $limit, $zone, $last_period );
				} else {
					$rep = $this->CI->pbf_mdl->get_top ( $value, $content_type ['lookup_id'], $limit, $zone, $last_period );
				}
				
				for($i = 0; $i < count ( $rep ); $i ++) {
	
					array_push ( $r, $this->CI->pbf_mdl->get_top_previous_period ( $value, $rep [$i] ['entity_id'], $content_type ['lookup_id'], $limit, $zone, $previous_period ) );
					
					// Si le trimestre précédant le dernier trimestre publié n'a pas des données, on utilise le trimestre qui le précède
					if (empty ( $r [0] )) {
						$previous_period = $periods [count ( $periods ) - 3];
					}
					array_push ( $r, $this->CI->pbf_mdl->get_top_previous_period ( $value, $rep [$i] ['entity_id'], $content_type ['lookup_id'], $limit, $zone, $previous_period ) );
				}
				
				$rows = array ();
				
				/* work around for insert columns header for top quality */
				
				array_push ( $rows, array (
						'column1' => $key,
						'column2' => '' 
				) );
				
				/* end work around */
				
				foreach ( $rep as $val ) {
					$row = array ();
					for($j = 0; $j < count ( $r ); $j ++) {
						
						if ($val ['entity_id'] == $r [$j] [0] ['entity_id']) {
							// comparaison de resultat du dernier trimetre publié avec le trimetre précedent
							$row ['comparaison'] = round ( $val ['datafile_total'] ) > round ( $r [$j] [0] ['datafile_total'] ) ? 'up' : 'down';
						}
					}
					/* $row['entity_name'] = '<a href="'.site_url('data/showentity/'.$val['entity_id']).'">'.$val['entity_name'].'</a> ('.$val['geozone_name'].', '.$val['parentgeo'].')'; */
					$row ['entity_name'] = '<a width="400px" href="' . site_url ( 'data/showentity/' . $val ['entity_id'] ) . '" >' . $val ['entity_name'] . '</a>';
					$row ['datafile_total'] = round ( $val ['datafile_total'] ) . ' %';
					$row ['entity_picturepath'] = $val ['entity_picturepath'];
					
					array_push ( $rows, $row );
				}
				
				$result ['data'] [$key] = $rows;
			}
			$result ['title'] = $top ['content_title'];
			
			return $result;
		} else {
			return null;
		}
	}
	
	/**
	 *
	 * @param type $zone_id        	
	 * @param type $is_entity
	 *        	boolean indicating whether it's an entity if true
	 * @return real time result for the specified region
	 */

	function get_real_time_result($zone_id = '', $pop = 0) {
		
	
		$last_published_date = $this->CI->pbf_mdl->get_last_published_date ();
		if (! empty ( $last_published_date )) {
			
			$realtime_period = $this->CI->config->item ( 'realtimeresult_period_data' ) * 3;
			
			$period_array = $this->CI->pbf->get_start_end_date_published ( $realtime_period );
			$start_date = $period_array ['start_date'];
			$end_date = $period_array ['end_date'];
			
			$indicators = $this->CI->pbf_mdl->get_real_time_result_indicators_id ();
			$result = array ();
			if (! empty ( $indicators )) {
				
				$temp = array ();
				
				foreach ( $indicators as $indicator ) {
					
					$real_time = $this->CI->pbf_mdl->get_real_time_result ( $indicator ['indicator_id'], $start_date, $end_date, $this->CI->config->item ( 'language_abbr' ), $zone_id );
					
					// / couverture check popcible
					if ($indicator ['indicator_use_coverage']) {
						
						$this->CI->load->model ( 'popcible_mdl' );
						
						if (! empty ( $zone_id ) || ($zone_id != '')) {
							
							$zone_info = $this->CI->geo_mdl->get_zone ( $zone_id );
							
							if (empty ( $zone_info ['geozone_parentid'] ) || ($zone_info ['geozone_parentid'] == '')) {
								$perc_pop = $this->CI->popcible_mdl->get_popcible_line_zone ( $indicator ['indicator_popcible'], $zone_id );
							} else {
								$perc_pop = $this->CI->popcible_mdl->get_popcible_line_zone ( $indicator ['indicator_popcible'], $zone_info ['geozone_parentid'] );
							}
						} else {
							$perc_pop = $this->CI->popcible_mdl->get_popcible_line ( $indicator ['indicator_popcible'] );
						}
						
						$pop_pc = round ( $pop * $perc_pop ['popcible_percentage'] / 100 );
						
						$real_time ['sum_validated_value'] = number_format ( round ( 100 * $real_time ['sum_validated_value'] / $pop_pc ), 0, $this->CI->lang->line ( 'decimal_separator' ), $this->CI->lang->line ( 'thousand_separator' ) ) . ' %';
					} else {
						$real_time ['sum_validated_value'] = number_format ( round ( $real_time ['sum_validated_value'] ), 0, $this->CI->lang->line ( 'decimal_separator' ), $this->CI->lang->line ( 'thousand_separator' ) );
					}
					
					$real_time ['indicator_common_name'] = $real_time ['indicator_common_name'];
					$real_time ['indicator_link'] = site_url () . 'data/element/' . $indicator ['indicator_id'];
					$t ['realtime'] = $real_time;
					
					array_push ( $temp, $t );
				}
				
				$result ['data'] = $temp;
			}
		} else {
			$result = '';
		}
		return $result;
	}
	function get_payments_all($zone) {
		$rt_all = $this->CI->pbf_mdl->get_real_time_payment_all ( $zone );
		
		if (isset ( $rt_all ['total'] ) && ! empty ( $rt_all ['total'] )) {
			
			$unite_monetaire = $this->CI->config->item ( 'app_country_currency' );
			
			$disbursed = number_format ( round ( $rt_all ['total'] ), 0, $this->CI->lang->line ( 'decimal_separator' ), $this->CI->lang->line ( 'thousand_separator' ) ) . ' ' . $unite_monetaire;
			
			$t ['realtime'] = array (
					'sum_validated_value' => $disbursed,
					'indicator_common_name' => $this->CI->lang->line ( 'total_payement' ),
					'indicator_icon_file' => 'icon-total.png',
					'indicator_link' => site_url () . 'data/payment/1' 
			);
			
	
		}
		
		return $t;
	}
	function get_real_time_result_home() {

		$last_published_date = $this->CI->pbf_mdl->get_last_published_date();

		$cur_year = $last_published_date [0] ['data_year'];
		if ((! isset ( $last_published_date [0] ['data_month'] ) || ($last_published_date [0] ['data_month'] == null))) {
			$cur_month = $last_published_date [0] ['data_quarter'] * 3;
		} else {
			
			$cur_month = $last_published_date [0] ['data_month'];
		}
		
		// SDL, temporaire car pas assez de données, on prend les 2 derniers trimestres, comparés au 2 derniers trimestres de l'année d'avant.
		// $start_date = ($cur_year-1).'-'.$cur_month.'-00';
		$evolution_period = $this->CI->config->item ( 'realtimeresult_evolution_period_home' );
		
		if ($cur_month >= $evolution_period) {
			$start_date = $cur_year . '-' . ($cur_month - ($evolution_period - 1)) . '-00';
		} else {
			// $start_date = ($cur_year-1).'-'.($cur_month + $evolution_period + 1).'-00';
			$start_date = ($cur_year - 1) . '-' . (12 + $cur_month - $evolution_period + 1) . '-00';
		}
		
		$end_date = $cur_year . '-' . $cur_month . '-00';
		
		// $lastyear_start_date = ($cur_year-2).'-'.$cur_month.'-00';
		
		if ($cur_month >= $evolution_period) {
			$lastyear_start_date = ($cur_year - 1) . '-' . ($cur_month - ($evolution_period - 1)) . '-00';
		} else {
			// $lastyear_start_date = ($cur_year-2).'-'.($cur_month + $evolution_period +1).'-00';
			$lastyear_start_date = ($cur_year - 2) . '-' . (12 + $cur_month - $evolution_period + 1) . '-00';
		}
		
		$lastyear_end_date = ($cur_year - 1) . '-' . $cur_month . '-00';
		
		$indicators = $this->CI->pbf_mdl->get_real_time_result_indicators_id ();
		
		$result = array ();
		if (! empty ( $indicators )) {
			
			$tooltip = '<a href="#">' . $this->CI->lang->line ( 'ytoy' ) . '</a> <span class="sep">|</span> ';
			// $tooltip .= $cur_month.'/'.$cur_year.'-'.$cur_month.'/'.($cur_year-1).$this->CI->lang->line('ytoywith').$cur_month.'/'.($cur_year-1).'-'.$cur_month.'/'.($cur_year-2);
			if ($cur_month >= $evolution_period) {
				
				$tooltip .= ($cur_month - ($evolution_period - 1)) . '/' . $cur_year . '-' . $cur_month . '/' . $cur_year . $this->CI->lang->line ( 'ytoywith' ) . ($cur_month - ($evolution_period - 1)) . '/' . ($cur_year - 1) . '-' . $cur_month . '/' . ($cur_year - 1);
			} else {
				$tooltip .= (12 + $cur_month - $evolution_period + 1) . '/' . ($cur_year - 1) . '-' . $cur_month . '/' . $cur_year . $this->CI->lang->line ( 'ytoywith' ) . (12 + $cur_month - $evolution_period + 1) . '/' . ($cur_year - 2) . '-' . $cur_month . '/' . ($cur_year - 1);
			}
			$temp = array ();
			
			foreach ( $indicators as $indicator ) {
				
				$real_time = $this->CI->pbf_mdl->get_real_time_result ( $indicator ['indicator_id'], $start_date, $end_date, $this->CI->config->item ( 'language_abbr' ) );
				
				if (empty ( $real_time ))
					continue;
				
				$previous_year = $this->CI->pbf_mdl->get_real_time_result ( $indicator ['indicator_id'], $lastyear_start_date, $lastyear_end_date, $this->CI->config->item ( 'language_abbr' ) );
				
				$comparaison = array ();
				
				$t = array ();
				
				if (! empty ( $previous_year ['sum_validated_value'] ) && $previous_year ['sum_validated_value'] != 0) {
					// echo "<br/>";
					$pourcent = (($real_time ['sum_validated_value'] - $previous_year ['sum_validated_value']) / $previous_year ['sum_validated_value']) * 100;
					if (isset ( $pourcent )) {
						$t ['real'] = $real_time ['sum_validated_value'];
						$t ['previous'] = $previous_year ['sum_validated_value'];
						$t ['indicator_abbrev'] = $real_time ['indicator_abbrev'];
						$t ['real_start_date'] = $start_date;
						$t ['real_end_date'] = $end_date;
						$t ['lastyear_start_date'] = $lastyear_start_date;
						$t ['lastyear_end_date'] = $lastyear_end_date;
						// echo "Abbrev:".$real_time['indicator_abbrev']." real time:".$real_time['sum_validated_value']." ".$start_date."-".$end_date." previous year:".$previous_year['sum_validated_value']." ".$lastyear_start_date."-".$lastyear_end_date."<br/>";
					}
					$comparaison ['pourcentage'] = (( int ) abs ( $pourcent )) . '%';
					
					if ($pourcent > 0) {
						$comparaison ['icon'] = 'up';
						} else {
						$comparaison ['icon'] = 'down';
						}
				} else {
					$comparaison ['pourcentage'] = '100%';
					$comparaison ['pourcentage'] = '<span title="Not available">N A</span>';
					$comparaison ['icon'] = 'up-icon.png';
					$comparaison ['icon'] = '';
				}
				
				$real_time_all = $this->CI->pbf_mdl->get_real_time_all ( $indicator ['indicator_id'], $this->CI->config->item ( 'language_abbr' ), $zone_id, $is_entity );
				
				$real_time ['sum_validated_value'] = number_format ( round ( $real_time_all ['sum_validated_value'] ), 0, $this->CI->lang->line ( 'decimal_separator' ), $this->CI->lang->line ( 'thousand_separator' ) );
				
				$real_time ['indicator_common_name'] = $real_time ['indicator_common_name'];
				$real_time ['indicator_link'] = site_url () . 'data/element/' . $indicator ['indicator_id'];
				$t ['realtime'] = $real_time;
				$t ['comparaison'] = $comparaison;
				
				array_push ( $temp, $t );
			}
			
			// * comment to remove disbursed indicator
			
			if (isset ( $indicators )) { // if indicators, also add total disbursed
				
				$rt = $this->CI->pbf_mdl->get_real_time_total ( $start_date, $end_date );
				
				$py = $this->CI->pbf_mdl->get_real_time_total ( $lastyear_start_date, $lastyear_end_date );
				
				$comparaison = array ();
				
				if (! empty ( $py ['total'] ) && $py ['total'] != 0) {
					
					$pourcent = (($rt ['total'] - $py ['total']) / $py ['total']) * 100;
					
					$comparaison ['pourcentage'] = (( int ) abs ( $pourcent )) . '%';
					
					if ($pourcent > 0) {
						
						$comparaison ['icon'] = 'up-icon.png';
					} else {
						$comparaison ['icon'] = 'down-icon.png';
					}
				} else {
					$comparaison = null;
				}
				
				$rt_all = $this->CI->pbf_mdl->get_real_time_total_all ();
				$budget_all = $this->CI->pbf_mdl->get_real_time_total_budget ();
				
				if (isset ( $rt_all ['total'] ) && ! empty ( $rt_all ['total'] )) {
					$unite_monetaire = $this->CI->config->item ( 'app_country_currency' );
					
					$disbursed = number_format ( round ( $rt_all ['total'] ), 0, $this->CI->lang->line ( 'decimal_separator' ), $this->CI->lang->line ( 'thousand_separator' ) ) . ' ' . $unite_monetaire;
					
					$t ['realtime'] = array (
							'sum_validated_value' => $disbursed,
							'indicator_common_name' => $this->CI->lang->line ( 'total_payement' ),
							'indicator_icon_file' => 'icon-total.png',
							'indicator_link' => site_url () . 'data/payment/1' 
					);
					
					$t ['comparaison'] = $comparaison;
					$t ['real'] = $real_time ['sum_validated_value'];
					$t ['previous'] = $previous_year ['sum_validated_value'];
					array_push ( $temp, $t );
				}
				
				if (isset ( $budget_all ['total'] ) && ! empty ( $budget_all ['total'] )) {
					$unite_monetaire = $this->CI->config->item ( 'app_country_currency' );
					
					$prevision = number_format ( round ( $budget_all ['total'] ), 0, $this->CI->lang->line ( 'decimal_separator' ), $this->CI->lang->line ( 'thousand_separator' ) ) . ' ' . $unite_monetaire;
					
					$b ['realtime'] = array (
							'sum_validated_value' => $prevision,
							'indicator_common_name' => $this->CI->lang->line ( 'total_budget' ),
							'indicator_icon_file' => 'icon-total.png',
							'indicator_link' => site_url () . 'data/' 
					);
					
					$b ['comparaison'] = $comparaison;
					$b ['real'] = $real_time ['sum_validated_value'];
					$b ['previous'] = $previous_year ['sum_validated_value'];
					array_push ( $temp, $b );
				}
			}
			
			// comment to remove disbursed indicator
			// */
			
			$result ['data'] = $temp;
			$result ['tooltip'] = $tooltip;
		}
		return $result;
	}
	function get_average_quality() {
		$average_quality_period = $this->CI->config->item ( 'average_quality_period' ) * 3; // convert the value in months(the original value is in quarters)
		
		$av_qual = $this->CI->pbf_mdl->get_average_quality_zone ( '', $average_quality_period );
		
		return $av_qual;
	}
	function get_quality_score($quarter, $entity_type, $year) {
		return $this->CI->pbf_mdl->get_global_quality_score ( $quarter, $entity_type, $year );
	}
	function get_quality_score_zone($quarter, $entity_type, $year, $zone) {
		
		// check if distict or zone
		$zone_type = $this->CI->db->get_where ( 'pbf_geozones', array (
				'geozone_id' => $zone 
		) )->result_array ();
		
		if ($zone_type [0] ['geozone_parentid'] != '') { // district
			
			return $this->CI->pbf_mdl->get_global_quality_score_district ( $quarter, $entity_type, $year, $zone );
		} else {
			
			return $this->CI->pbf_mdl->get_global_quality_score_zone ( $quarter, $entity_type, $year, $zone );
		}
	}
	function human_filesize($bytes, $decimals = 2) {
		$size = array (
				'B',
				'kB',
				'MB',
				'GB',
				'TB',
				'PB',
				'EB',
				'ZB',
				'YB' 
		);
		$factor = floor ( (strlen ( $bytes ) - 1) / 3 );
		return sprintf ( "%.{$decimals}f", $bytes / pow ( 1024, $factor ) ) . @$size [$factor];
	}
	function get_by_lookup_title($title) {
		return $this->CI->db->get_where ( 'pbf_lookups', array (
				'lookup_title' => $title 
		) )->row_array ();
	}
	function Slug($string) 	// function for URL Friendly Username
	{
		$string = strtolower ( trim ( preg_replace ( '~[^0-9a-z]+~i', '_', html_entity_decode ( preg_replace ( '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities ( $string, ENT_QUOTES, 'UTF-8' ) ), ENT_QUOTES, 'UTF-8' ) ), '_' ) );
		if (strlen ( $string ) > 50)
			$string = substr ( $string, 0, 50 );
		
		return $string;
	}
	function check_group_entityassociated($user_group = 0) { // verifier si le groupe est associe a une entite
		$sql = "SELECT usersgroup_entity_associated FROM pbf_usersgroups where usersgroup_id=$user_group";
		
		$resultat = $this->CI->db->query ( $sql )->result_array ();
		
		return $resultat [0] ['usersgroup_entity_associated'];
	}
	function get_helpers($helpertype) {
		
		// in the very near future, this function should select and group-option the helpers....
		$helpers = array (
				'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
		);
		$helperz = glob ( APPPATH . 'helpers/' . $helpertype . '/*' . EXT );
		
		foreach ( $helperz as $helper ) {
			$helpers [str_ireplace ( APPPATH . 'helpers/' . $helpertype . '/', '', str_ireplace ( '_helper.php', '', $helper ) )] = str_ireplace ( APPPATH . 'helpers/' . $helpertype . '/', '', str_ireplace ( '_helper.php', '', $helper ) );
		}
		
		return $helpers;
	}
	function get_monthsBy_quarter_word($quarter) {
		if ($quarter == 1) {
			return array (
					'Janvier',
					'Fevrier',
					'Mars' 
			);
		} elseif ($quarter == 2) {
			return array (
					'Avril',
					'Mai',
					'Juin' 
			);
		}
		if ($quarter == 3) {
			return array (
					'Juillet',
					'Aoï¿½t',
					'Septembre' 
			);
		}
		if ($quarter == 4) {
			return array (
					'Octobre',
					'Novembre',
					'decembre' 
			);
		}
	}
	function get_quality_per_region($district_id, $parent = TRUE) {
		$entity_types = $this->CI->pbf_mdl->get_entity_types ( 1 );
		$periods = $this->get_last_periods ( $this->CI->config->item ( 'num_period_display' ) );
		$rows = array ();
		
		foreach ( $entity_types as $entity_type ) {
			
			$row = $this->CI->pbf_mdl->get_quality_per_region ( $district_id, $periods, $entity_type ['entity_type_id'], $parent );
			
			if (! empty ( $row )) {
				foreach ( $row as $k => $r ) {
					$row [$k] ['geozone_name'] = anchor ( 'data/showzone/' . $r ['geo_id'] . '/' . $r ['geozone_id'], $r ['geozone_name'] );
					
					$row [$k] ['indicators'] = $this->get_zone_quality_indicators ( $row [$k] ['geozone_id'], $entity_type ['entity_type_id'] );
					unset ( $row [$k] ['geozone_id'] );
					unset ( $row [$k] ['geo_id'] );
				}
				$rows [$entity_type ['entity_type_name']] = $row;
			}
		}
		
		return $rows;
	}
	function get_quality_per_region_export($district_id, $parent = TRUE) {
		$entity_types = $this->CI->pbf_mdl->get_entity_types ( 1 );
		$periods = $this->get_last_periods ( $this->CI->config->item ( 'num_period_display' ) );
		
		$rows = array ();
		
		foreach ( $entity_types as $entity_type ) {
			
			$row = $this->CI->pbf_mdl->get_quality_per_region ( $district_id, $periods, $entity_type ['entity_type_id'], $parent );
			
			if (! empty ( $row )) {
				foreach ( $row as $k => $r ) {
					
					unset ( $row [$k] ['geozone_id'] );
					unset ( $row [$k] ['geo_id'] );
				}
				$rows [$entity_type ['entity_type_name']] = $row;
			}
		}
		
		return $rows;
	}
	function get_quality_per_region_export_front($district_id, $parent = TRUE) {
		$entity_types = $this->CI->pbf_mdl->get_entity_types ( 1 );
		$periods = $this->get_last_periods ( $this->CI->config->item ( 'num_period_display' ) );
		
		$rows = array ();
		
		foreach ( $entity_types as $entity_type ) {
			
			$row = $this->CI->pbf_mdl->get_quality_per_region ( $district_id, $periods, $entity_type ['entity_type_id'], $parent );
			
			if (! empty ( $row )) {
				foreach ( $row as $k => $r ) {
					
					unset ( $row [$k] ['geozone_id'] );
					unset ( $row [$k] ['geo_id'] );
				}
				$rows [$entity_type ['entity_type_name']] = $row;
			}
		}
		
		return $rows;
	}
	function get_district_qualities($district_id, $parent = TRUE) {
		$entity_types = $this->CI->pbf_mdl->get_entity_types ( 1 );
		$periods = $this->get_last_periods ( $this->CI->config->item ( 'num_period_display' ) );
		
		$rows = array ();
		
		foreach ( $entity_types as $entity_type ) {
			
			$row = $this->CI->pbf_mdl->get_district_qualities ( $district_id, $periods, $entity_type ['entity_type_id'], $parent );
			
			if (! empty ( $row )) {
				foreach ( $row as $k => $r ) {
					$row [$k] ['entity_name'] = anchor ( 'data/showentity/' . $r ['entity_id'], $r ['entity_name'] );
					// $indicators = $this->get_data('',$row[$k]['entity_id'],'indicator_montant','Quality');
					$indicators = $this->get_entity_quality_indicators ( $row [$k] ['entity_id'] );
					$row [$k] ['indicators'] = $indicators;
					unset ( $row [$k] ['entity_id'] );
				}
				$rows [$entity_type ['entity_type_name']] = $row;
			}
		}
		
		return $rows;
	}
	function get_entity_qualities($entity_id) {
		$entity_types = $this->CI->pbf_mdl->get_entity_types ( 1 );
		$periods = $this->get_last_periods ( $this->CI->config->item ( 'num_period_display' ) );
		
		$rows = array ();
		
		foreach ( $entity_types as $entity_type ) {
			
			$row = $this->CI->pbf_mdl->get_entity_qualities ( $entity_id, $periods, $entity_type ['entity_type_id'] );
			
			if (! empty ( $row )) {
				foreach ( $row as $k => $r ) {
					$row [$k] ['entity_name'] = anchor ( 'data/showentity/' . $r ['entity_id'], $r ['entity_name'] );
					$indicators = $this->get_entity_quality_indicators ( $row [$k] ['entity_id'] );
					$row [$k] ['indicators'] = $indicators;
					unset ( $row [$k] ['entity_id'] );
				}
				$rows [$entity_type ['entity_type_name']] = $row;
			}
		}
		
		return $rows;
	}
	function get_entity_quality_indicators($entity_id) {
		$periods = $this->get_last_periods ( $this->CI->config->item ( 'num_period_display' ) );
		
		$row_data = $this->CI->pbf_mdl->get_entity_quality_indicators ( $entity_id, $periods );
		
		foreach ( $row_data as $k => $r_data ) {
			$row_data [$k] [strtoupper ( $this->CI->lang->line ( 'heat_map_indicator' ) )] = anchor ( 'data/element/' . $r_data ['indicator_id'], $r_data [strtoupper ( $this->CI->lang->line ( 'heat_map_indicator' ) )] );
			unset ( $row_data [$k] ['indicator_id'] );
		}
		array_unshift ( $row_data, array_keys ( $row_data [0] ) );
		return $row_data;
	}
	function get_zone_quality_indicators($zone_id, $entity_type) {
		$periods = $this->get_last_periods ( $this->CI->config->item ( 'num_period_display' ) );
		
		$row_data = $this->CI->pbf_mdl->get_zone_quality_indicators ( $zone_id, $periods, $entity_type );
		
		foreach ( $row_data as $k => $r_data ) {
			$row_data [$k] [strtoupper ( $this->CI->lang->line ( 'heat_map_indicator' ) )] = anchor ( 'data/element/' . $r_data ['indicator_id'], $r_data [strtoupper ( $this->CI->lang->line ( 'heat_map_indicator' ) )] );
			unset ( $row_data [$k] ['indicator_id'] );
		}
		array_unshift ( $row_data, array_keys ( $row_data [0] ) );
		return $row_data;
	}
	function get_entity_qualities_excel($entity_id) {
		$entity_types = $this->CI->pbf_mdl->get_entity_types ( 1 );
		$periods = $this->get_last_periods ( $this->CI->config->item ( 'num_period_display' ) );
		
		$rows = array ();
		
		foreach ( $entity_types as $entity_type ) {
			
			$row = $this->CI->pbf_mdl->get_entity_qualities ( $entity_id, $periods, $entity_type ['entity_type_id'] );
			
			if (! empty ( $row )) {
				foreach ( $row as $k => $r ) {
					$row [$k] ['entity_name'] = $r ['entity_name'];
					unset ( $row [$k] ['entity_id'] );
				}
				$rows [$entity_type ['entity_type_name']] = $row;
			}
		}
		
		return $rows;
	}
	function get_quarter($month) {
		switch ($month) {
			case 1 :
				$quarter = 1;
				break;
			case 2 :
				$quarter = 1;
				break;
			case 3 :
				$quarter = 1;
				break;
			case 4 :
				$quarter = 2;
				break;
			case 5 :
				$quarter = 2;
				break;
			case 6 :
				$quarter = 2;
				break;
			case 7 :
				$quarter = 3;
				break;
			case 8 :
				$quarter = 3;
				break;
			case 9 :
				$quarter = 3;
				break;
			case 10 :
				$quarter = 4;
				break;
			case 11 :
				$quarter = 4;
				break;
			case 12 :
				$quarter = 4;
				break;
		}
		return $quarter;
	}
	function get_entity_classes_id($entity_id) {
		$raw_classes = $this->CI->pbf_mdl->get_entity_classes_id ( $entity_id );
		
		return $lookups;
	}
	
	// ====================================================================================================
	function get_budgets($region, $zone_id, $entity, $periods) {
		if ($entity == '') {
			$entity_id = array ();
			$districts = $zone_id;
			if (! $region == '') {
				
				$zones = $this->CI->geo_mdl->get_zones_by_parent ( $zone_id );
			} else {
				$entities = $this->CI->pbf_mdl->get_entities_By_zone ( $districts, '1', '', '' );
			}
			
			if (! empty ( $zones )) {
				$districts = $zones;
				
				foreach ( $districts as $zone ) {
					
					$entities = $this->CI->pbf_mdl->get_entities_By_zone ( $zone ['geozone_id'], '1', '', '' );
					
					foreach ( $entities as $entity ) {
						$entity_id [] = $entity ['entity_id'];
					}
				}
			} else {
				
				$entities = $this->CI->pbf_mdl->get_entities_By_zone ( $zone_id, '1', '', '' );
				foreach ( $entities as $entity ) {
					$entity_id [] = $entity ['entity_id'];
				}
			}
			
			if (empty ( $entity_id )) {
				
				$entity_id = '';
			} else {
				
				$entity_id = '';
			}
		} else {
			
			$entity_id = $entity;
		}
		
		if (isset ( $periods )) {
			
			$front_data = $this->get_budget_values ( $periods, $entity_id );
			
			if (! empty ( $front_data )) {
				array_unshift ( $front_data, array_keys ( $front_data [0] ) );
			}
		}
		return $front_data;
	}
	function get_budgets_zone($zone_id, $periods) {
		$districts = $this->CI->geo_mdl->get_zones_by_parent ( $zone_id );
		
		foreach ( $districts as $zone ) {
			
			$entities = $this->CI->pbf_mdl->get_entities_By_zone ( $zone ['geozone_id'], '1', '', '' );
			
			foreach ( $entities as $entity ) {
				$entity_id [] = $entity ['entity_id'];
			}
		}
		
		if (isset ( $periods )) {
			
			$front_data = $this->get_budget_values ( $periods, implode ( ',', $entity_id ) );
			
			foreach ( $front_data as $fi_key => $fi_val ) {
				
				foreach ( $fi_val as $k => $t ) {
					if ($k != 'TRIMESTRE') {
						
						$r = $this->format_number ( ( int ) $t );
						$front_data [$fi_key] [$k] = $r;
					}
				}
			}
			
			if (! empty ( $front_data )) {
				array_unshift ( $front_data, array_keys ( $front_data [0] ) );
			}
		}
		return $front_data;
	}
	function get_budgets_district($zone_id, $periods) {
		$entities = $this->CI->pbf_mdl->get_entities_By_zone ( $zone_id, '1', '', '' );
		
		foreach ( $entities as $entity ) {
			$entity_id [] = $entity ['entity_id'];
		}
		
		if (isset ( $periods )) {
			$entity_class = 'ccxcxc';
			$front_data = $this->get_budget_values ( $periods, implode ( ',', $entity_id ) );
			foreach ( $front_data as $fi_key => $fi_val ) {
				
				foreach ( $fi_val as $k => $t ) {
					if ($k != 'TRIMESTRE') {
						
						$r = $this->format_number ( ( int ) $t );
						$front_data [$fi_key] [$k] = $r;
					}
				}
			}
			
			if (! empty ( $front_data )) {
				array_unshift ( $front_data, array_keys ( $front_data [0] ) );
			}
		}
		return $front_data;
	}
	function get_budgets_all_entities($periods) {
		$entities = $this->CI->pbf_mdl->get_entities_all ( '1', '', '' );
		
		foreach ( $entities as $entity ) {
			$entity_id [] = $entity ['entity_id'];
		}
		
		if (isset ( $periods )) {
			
			$front_data = $this->get_budget_values ( $periods, implode ( ',', $entity_id ) );
			foreach ( $front_data as $fi_key => $fi_val ) {
				
				foreach ( $fi_val as $k => $t ) {
					if ($k != 'TRIMESTRE') {
						
						$r = $this->format_number ( ( int ) $t );
						$front_data [$fi_key] [$k] = $r;
					}
				}
			}
			
			if (! empty ( $front_data )) {
				array_unshift ( $front_data, array_keys ( $front_data [0] ) );
			}
		}
		return $front_data;
	}
	function get_budgets_entity($entity_id, $periods) {
		if (isset ( $periods )) {
			$front_data = $this->get_budget_values ( $periods, $entity_id );
			
			foreach ( $front_data as $fi_key => $fi_val ) {
				
				foreach ( $fi_val as $k => $t ) {
					if ($k != 'TRIMESTRE') {
						
						$r = $this->format_number ( ( int ) $t );
						$front_data [$fi_key] [$k] = $r;
					}
				}
			}
			
			if (! empty ( $front_data )) {
				array_unshift ( $front_data, array_keys ( $front_data [0] ) );
			}
		}
		return $front_data;
	}
	
	// ========================================================================================================
	function get_budget_values($periods, $entity_id) {
		$budget_data = $this->CI->pbf_mdl->get_budget_values ( $periods, $entity_id );
		return $budget_data;
	}
	function get_annual_budget($annee) {
		$entities = $this->CI->pbf_mdl->get_entities_all ( '1', '', '' );
		
		foreach ( $entities as $entity ) {
			$entity_id [] = $entity ['entity_id'];
		}
		
		$budget_annual_data = $this->CI->pbf_mdl->get_annual_budget ( $annee, implode ( ',', $entity_id ) );
		
		return $budget_annual_data;
	}
	function get_annual_budget_entity($annee, $entity_id) {
		$budget_annual_data = $this->CI->pbf_mdl->get_annual_budget ( $annee, $entity_id );
		
		return $budget_annual_data;
	}
	function budget_data_exist($annee) {
		$annual = $this->CI->pbf_mdl->check_annual_budget ( $annee );
		$month = $this->CI->pbf_mdl->check_month_budget ( $annee );
		
		if ($annual > 0 && $month > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	// ================================SCRIPT DE GENERATION DES MESSAGES D'ALERTES=============================================================================
	function alertes() {
		$this->CI->load->model ( 'alertes_log_mdl' );
		$user_groups = $this->CI->pbf->get_groups_details ();
		$temp_array = array ();
		foreach ( $user_groups as $group ) {
			$group_id = $group ['usersgroup_id'];
			$alertes_list = $this->CI->pbf_mdl->get_alertes_config ();
			$users_list = $this->CI->pbf_mdl->users_list ( $group_id );
			
			foreach ( $users_list as $user ) {
				$message_user = '';
				$liste_fosa = $this->CI->pbf_mdl->liste_fosa ( $user ['user_id'] );
				foreach ( $alertes_list as $alerte ) {
					$send_email = $alerte ['alerte_email'];
					$year_month = $this->month_year ( $alerte ['month_delay'] );
					$message_body = '';
					$list_fosa = array ();
					foreach ( $liste_fosa as $fosa ) {
						if (($this->alertes_ready ( $alerte ['alerte_delay'], $alerte ['month_delay'] )) && ($this->check_data_existence ( $fosa ['entity_id'], $year_month ['year'], $year_month ['month'], $alerte ['filetypes'], $alerte ['fields_monitor'] )) && ($this->checksent ( $alerte ['alerteconfig_id'], $year_month, $user ['user_id'], $alerte ['month_delay'] ))) {
							$list_fosa [] = $fosa ['entity_name'];
						}
					}
					if ((! empty ( $list_fosa )) && ($this->checkdest ( $user ['user_id'], $alerte ))) {
						$alert_log = array ();
						if ($alerte ['month_delay'] >= 3) {
							$alert_log ['month'] = null;
							$alert_log ['quarter'] = $this->get_quarter ( $year_month ['month'] );
							$mess_period = "pour le " . $alert_log ['quarter'] . " trimestre " . $year_month ['year'];
						} else {
							$alert_log ['month'] = $year_month ['month'];
							$alert_log ['quarter'] = null;
							$mess_period = "pour le mois de " . $this->CI->lang->line ( 'app_month_' . $year_month ['month'] ) . ' ' . $year_month ['year'];
						}
						$listedesfosa = '';
						$k = 1;
						$l = 1;
						$geo_tmp = '';
						foreach ( $liste_fosa as $fosa_data ) {
							if ($fosa_data ['geozone_name'] == $geo_tmp) {
								$zone = '';
								$listedesfosa .= '  ' . ' ' . $l . '. ' . $fosa_data ['entity_name'] . "\n";
								$l ++;
							} else {
								$zone = $k . '. ' . $fosa_data ['geozone_name'] . "\n";
								$listedesfosa .= $zone;
								$k ++;
								$l = 1;
							}
							
							$geo_tmp = $fosa_data ['geozone_name'];
						}
						$message_body = $alerte ['alerte_message'] . ' ' . $mess_period . ' : ' . "\n" . $listedesfosa . "\n";
						$alert_log ['group_id'] = $group_id;
						$alert_log ['user_id'] = $user ['user_id'];
						$alert_log ['type_alerte'] = $alerte ['alerteconfig_id'];
						$alert_log ['message'] = implode ( ',', $list_fosa );
						$alert_log ['checked'] = '0';
						$alert_log ['year'] = $year_month ['year'];
						$alert_log ['date_alerte'] = date ( 'Y-m-d h:i:s' );
						$alert_log ['alerteconfig_id'] = $alerte ['alerteconfig_id'];
						
						$this->CI->alertes_log_mdl->save_alerte_log ( $alert_log );
					}
					if (! $send_email == 1) {
						$message_body = '';
					}
					$message_user .= $message_body;
				}
				
				if (! $message_user == '') {
					$alert_log = array ();
					$message_compl = "Pour saisir les données manquantes cliquer sur le lien suivant:";
					$message_salutation = "Bonjour " . $user ['user_fullname'] . "\n\n";
					$message = $message_salutation . $message_user . "\n" . $message_compl . "\n" . site_url () . 'auth.html';
					$result = $this->send_mail ( $message, $user ['user_name'] );
				}
			}
		}
		
		$this->clear_alertes ();
	}
	function alertes_ready($delay_day, $delay_m) {
		$sDateFin = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( "m" ), 0, date ( "Y" ) ) );
		$now = date ( "Y-m-d H:i:s" );
		$date_diff = round ( abs ( strtotime ( $now ) - strtotime ( $sDateFin ) ) / 86400 );
		
		if ($date_diff > $delay_day) {
			return true;
		} else {
			return false;
		}
	}
	function month_year($delay_m) {
		$month_year = array ();
		$month_year ['month'] = date ( "n" ) - $delay_m;
		$month_year ['year'] = date ( "Y" );
		if ($month_year ['month'] == 0) {
			$month_year ['month'] = 12;
			$month_year ['year'] = date ( "Y" ) - 1;
		}
		return $month_year;
	}
	function send_mail($body_message, $email) {
		$config = Array (
				'protocol' => 'smtp',
				'smtp_host' => 'ssl://smtp.googlemail.com',
				'smtp_port' => 465,
				'smtp_user' => 'blsqmail@gmail.com',
				'smtp_pass' => 'GTSTDE2012',
				'mailtype' => 'text',
				'charset' => 'iso-8859-1',
				'starttls' => true 
		);
		
		$this->CI->load->library ( 'email', $config );
		$this->CI->email->set_newline ( "\r\n" );
		$this->CI->email->from ( 'blsqmail@gmail.com', $this->CI->lang->line ( 'app_sub_title_key' ) );
		$this->CI->email->to ( $email );
		$this->CI->email->subject ( 'PBF alertes' );
		$this->CI->email->message ( $body_message );
		$this->CI->email->send ();
	}
	function check_data_existence($fosa, $year, $month, $filetypes, $fields) {
		if ($this->CI->pbf_mdl->check_data_file ( $fosa, $year, $month, $filetypes ) == 0) {
			return true;
		} else {
			
			switch ($fields) {
				case 'indicator_claimed_value' :
					if ($this->CI->pbf_mdl->check_claimed_existence ( $fosa, $year, $month, $filetypes, $fields ) > 0) {
						return true;
					} else {
						return false;
					}
					break;
				case 'indicator_verified_value' :
					if ($this->CI->pbf_mdl->check_verified_existence ( $fosa, $year, $month, $filetypes, $fields ) > 0) {
						return true;
					} else {
						return false;
					}
					break;
				case 'indicator_validated_value' :
					if ($this->CI->pbf_mdl->check_validated_existence ( $fosa, $year, $month, $filetypes, $fields ) > 0) {
						return true;
					} else {
						return false;
					}
					break;
				
				case 'datafile_state' :
					if ($this->CI->pbf_mdl->check_file_state ( $fosa, $year, $month, $filetypes, $fields ) > 0) {
						return true;
					} else {
						return false;
					}
					break;
				
				case 'datafile_status' :
					if ($this->CI->pbf_mdl->check_file_status ( $fosa, $year, $month, $filetypes, $fields ) > 0) {
						return true;
					} else {
						return false;
					}
					break;
			}
			return false;
		}
	}
	function checkdest($user, $alerte) {
		if (! empty ( $alerte ['users'] )) {
			if (in_array ( $user, explode ( ',', $alerte ['users'] ) )) {
				
				return true;
			} else {
				return false;
			}
		} else {
			$group = $this->get_usergroup ( $user );
			if (in_array ( $group, explode ( ',', $alerte ['groups'] ) )) {
				return true;
			} else {
				return false;
			}
		}
	}
	function checksent($alerte_id, $period, $user, $delay_m) {
		if ($delay_m >= 3) {
			if ($this->CI->pbf_mdl->checksent_trim ( $alerte_id, $this->get_quarter ( $period ['month'] ), $user, $period ['year'] ) == 0) {
				return true;
			} else {
				return false;
			}
		} else {
			
			if ($this->CI->pbf_mdl->checksent ( $alerte_id, $period, $user ) == 0) {
				return true;
			} else {
				return false;
			}
		}
	}
	function get_usergroup($user) {
		$group = $this->CI->pbf_mdl->get_usergroup ( $user );
		return $group ['groupe'];
	}
	function clear_alertes() {
		$clear = $this->CI->pbf_mdl->clear_alerts ();
		return $clear;
	}
	// =========================================================================================================================================================
	function get_number_entities() {
		$entity_types = $this->CI->pbf_mdl->get_number_entity ();
		
		return $entity_types;
	}
	// Fonction qui retourne le total de la population cible du Bénin
	function get_pop_tot() {
		$tot_pop = $this->CI->cms_mdl->get_pop_tot ();
		return $tot_pop;
	}
	function invalidate_reports($report) {
		// invalidate entity report
		$quarter = $report ['quarter'];
		$month = $report ['quarter'];
		unset ( $report ['quarter'] );
		// possible monthly invoice
		$this->CI->pbf_mdl->invalidate_report ( $report );
		// possible quarterly invoices
		unset ( $report ['month'] );
		$report ['quarter'] = $quarter;
		$this->CI->pbf_mdl->invalidate_report ( $report );
		// invalidate zone report
		$this->CI->load->model ( 'entities_mdl' );
		$entity = $this->CI->entities_mdl->get_entity ( $report ['entity_id'] );
		unset ( $report [entity_id] );
		$report ['zone_id'] = $entity ['geozone_id'];
		
		// monthly reports
		unset ( $report ['quarter'] );
		$report ['month'] = $month;
		$this->CI->pbf_mdl->invalidate_report ( $report );
		// quarterly reports
		unset ( $report ['month'] );
		$report ['quarter'] = $quarter;
		
		$this->CI->pbf_mdl->invalidate_report ( $report );
	}
	function get_entities_by_zone($zone_id = null) {
		$entities = $this->CI->pbf_mdl->get_entities_By_zone ( $zone_id, '1', '', '' );
		foreach ( $entities as $entity ) {
			$entity_id [] = $entity ['entity_id'];
		}
		return $entity_id;
	}
	function get_entities_by_region($region_id = null) {
		$entities = $this->CI->pbf_mdl->get_entities_By_region ( $region_id, '1', '', '' );
		foreach ( $entities as $entity ) {
			$entity_id [] = $entity ['entity_id'];
		}
		return $entity_id;
	}
	function get_entities_by_district($district_id = null) {
		$entities = $this->CI->pbf_mdl->get_entities_By_district ( $district_id );
		foreach ( $entities as $entity ) {
			$entity_id [] = $entity ['entity_id'];
		}
		return $entity_id;
	}
	function resize_image($config = null) {
		$config ['image_library'] = 'gd2';
		$this->CI->load->library ( 'image_lib', $config );
		$this->CI->load->library ( 'upload', $config );
		$thumb_size = $this->CI->config->item ( 'image_thumb_size' );
		$medium_size = $this->CI->config->item ( 'image_medium_size' );
		$big_size = $this->CI->config->item ( 'image_big_size' );
		$image_data = $this->CI->upload->data ();
		$config_thumb = array (
				'source_image' => $config ['upload_path'] . '/' . $config ['file_name'],
				'new_image' => $config ['upload_path'] . '/' . $config ['file_new_name'] . "_thumb.jpg",
				'maintain_ration' => true,
				'width' => $thumb_size,
				'height' => $thumb_size 
		);
		
		$config_medium = array (
				'source_image' => $config ['upload_path'] . '/' . $config ['file_name'],
				'new_image' => $config ['upload_path'] . '/' . $config ['file_new_name'] . '_med.jpg',
				'maintain_ration' => true,
				'width' => $medium_size,
				'height' => $medium_size 
		);
		
		$config_big = array (
				'source_image' => $config ['upload_path'] . '/' . $config ['file_name'],
				'new_image' => $config ['upload_path'] . '/' . $config ['file_new_name'] . '_big.jpg',
				'maintain_ration' => true,
				'width' => $big_size,
				'height' => $big_size 
		);
		
		$this->CI->load->library ( 'image_lib' );
		$this->CI->image_lib->initialize ( $config_thumb );
		
		if (! $this->CI->image_lib->resize ()) {
			die ( $this->CI->image_lib->display_errors () );
		}
		
		$this->CI->image_lib->clear ();
		
		$this->CI->image_lib->initialize ( $config_medium );
		
		if (! $this->CI->image_lib->resize ()) {
			die ( $this->CI->image_lib->display_errors () );
		}
		
		$this->CI->image_lib->clear ();
		
		$this->CI->image_lib->initialize ( $config_big );
		
		if (! $this->CI->image_lib->resize ()) {
			die ( $this->CI->image_lib->display_errors () );
		}
	}
	function get_start_end_date_published($period) {
		$period_months = $period % 12;
		$period_years = ($period - $period_months) / 12;
		
		$last_published_date = $this->CI->pbf_mdl->get_last_published_date ();
		$cur_year = $last_published_date [0] ['data_year'];
		
		if ((! isset ( $last_published_date [0] ['data_month'] ) || ($last_published_date [0] ['data_month'] == null))) {
			$cur_month = $last_published_date [0] ['data_quarter'] * 3;
		} else {
			
			$cur_month = $last_published_date [0] ['data_month'];
		}
		
		if ($cur_month >= $period_years) {
			$start_date = $cur_year - $period_years . '-' . ($cur_month - ($period_months - 1)) . '-00';
		} else {
			$start_date = ($cur_year - $period_years - 1) . '-' . (12 + $cur_month - $period_months + 1) . '-00';
		}
		
		$end_date = $cur_year . '-' . $cur_month . '-31';
		
		$result = array (
				'start_date' => $start_date,
				'end_date' => $end_date 
		);
		return $result;
	}
	
	// Fonction qui retourne tous les types de fichiers qui sont actifs
	function get_file_types() {
		$file_types = $this->CI->pbf_mdl->get_all_filetypes ();
		return $file_types;
	}
	function get_donor_entity_config($donor_id, $year) {
		$result = $this->CI->pbf_mdl->get_donor_entity_config ( $donor_id, $year );
		return $result;
	}
	function get_donor_config_details($config_id) {
		$result = $this->CI->pbf_mdl->get_donor_config_details ( $config_id );
		return $result;
	}
	function entity_donor_details($config_det_id, $entity_id) {
		$result = $this->CI->pbf_mdl->entity_donor_details ( $config_det_id, $entity_id );
		return $result;
	}
	function get_indicator_title($indicator_id) {
		$result = $this->CI->pbf_mdl->get_indicator_title ( $indicator_id );
		return $result;
	}
	function get_donor_list_config($year) {
		$result = $this->CI->pbf_mdl->get_donor_list_config ( $year );
		return $result;
	}
	function get_master_donor_pay($donor_id, $entity_id) {
		$result = $this->CI->pbf_mdl->get_master_donor_pay ( $donor_id, $entity_id );
		return $result;
	}
	function get_pbf_group_bonus($group_id) {
		return $this->CI->pbf_mdl->get_pbf_group_bonus ( $group_id );
	}
	function get_indicator_categories() {
		$raw_categories = $this->CI->pbf_mdl->get_indicator_categories ();
		
		$categories = array (
				'' => $this->CI->lang->line ( 'app_form_dropdown_select' ) 
		);
		
		foreach ( $raw_categories as $key ) {
			$categories [$key ['category_id']] = $key ['category_title'];
		}
		return $categories;
	}
	
	function create_pdf_invoice_name($params,$geo_zone){
		$filename=$params['report_category'].'_'
			.$params['report_id'].'_'
			.$params['datafile_year'];
			if(array_key_exists('datafile_quarter',$params)){
				$filename.='_'.$params['datafile_quarter'];
			}		
			if(array_key_exists('datafile_month',$params)){
				$filename.='_'.$params['datafile_month'];

			}
			if(array_key_exists('entity_id',$params)){
				$filename.='_'.$params['entity_id'];
			}else{
				$filename.='_'.$geo_zone;
			}
			
		$filename.='.pdf';
		return $filename;
	
	}
	
	function get_pdf_filename_parameters($params){
		$report_parameters=json_decode($params['report_params']);
			
		if(in_array('datafile_month',$report_parameters)){
			unset($params['datafile_quarter']);
			}
		if(in_array('datafile_quarter',$report_parameters)){
			unset($params['datafile_month']);
			}
		if(in_array('entity_geozone_id',$report_parameters)){
			unset($params['entity_id']);
			}
		if(!in_array('datafile_month',$report_parameters) && !in_array('datafile_quarter',$report_parameters)){
			unset($params['datafile_month']);
			unset($params['datafile_quarter']);
		}
	return $params;		
	}
	
	function get_report_period($params){
		$report_parameters=json_decode($params['report_params']);
		if(in_array('datafile_month',$report_parameters)){
			return "month";
		}
	
		if(in_array('datafile_quarter',$report_parameters)){
			return "quarter";
		}
		
		if(!in_array('datafile_month',$report_parameters) && !in_array('datafile_quarter',$report_parameters)){
			return "year";
		}
	}
	
}
