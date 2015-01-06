<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
$user_id = $this->session->userdata ( 'user_id' );
$user_group = $this->pbf->get_usergroup ( $user_id );

$sql_task = "SELECT pbf_userstasks.usertask_name FROM pbf_usersgroups LEFT JOIN pbf_usersgroupsrules ON(pbf_usersgroups.usersgroup_id=pbf_usersgroupsrules.usersgroup_id) 
LEFT JOIN pbf_userstasks ON (pbf_userstasks.usertask_id=pbf_usersgroupsrules.userstask_id) WHERE pbf_usersgroups.usersgroup_id='" . $user_group . "'";
$tasks_list = $this->db->query ( $sql_task )->result_array ();
if (! empty ( $tasks_list )) {
	$tab_task = array ();
	foreach ( $tasks_list as $task ) {
		$tab_task [] = $task ['usertask_name'];
	}
}

if ($this->session->userdata ( 'show_menu' ) === 'yes') {
	
	$attributes = array (
			'id' => 'nav' 
	);
	$dataentry = array ();
	
	$dataentry_raw = $this->pbf->get_entities_classes_access ();
	
	// available menu
	
	foreach ( $dataentry_raw as $key ) {
		$dataentry ['datafiles/datafiles/' . $key ['entity_class_id']] = $key ['entity_class_name'];
	}
	
	$available_menu ['dashboard/'] = $this->lang->line ( 'app_submenu_dashboard' );
	if (! empty ( $dataentry )) {
		$available_menu ['datafiles/datafiles/' . $dataentry_raw [0] ['entity_class_id']] = array (
				$this->lang->line ( 'app_submenu_dataentry' ),
				$dataentry 
		);
	}
	
	$available_menu ['report/'] = $this->lang->line ( 'app_submenu_report' );
	$available_menu ['exports/'] = $this->lang->line ( 'app_submenu_export' );
	$available_menu ['cms/cms/29'] = $this->lang->line ( 'app_submenu_cms' );
	
	$available_menu ['acl/'] = array (
			$this->lang->line ( 'app_submenu_settings' ),
			array (
					'acl/' => $this->lang->line ( 'app_submenu_settings_acl' ),
					'hfrentities/classes/' => $this->lang->line ( 'app_submenu_settings_entities' ),
					'geo/geos/' => $this->lang->line ( 'app_submenu_settings_regions' ),
					'indicators/' => $this->lang->line ( 'app_submenu_settings_files_indicators' ),
					'files/' => $this->lang->line ( 'app_submenu_settings_files_files' ),
					'budgets/' => $this->lang->line ( 'app_submenu_settings_budgets' ),
					'popcible/' => $this->lang->line ( 'app_submenu_settings_popcible' ),
					'otheroptions/' => $this->lang->line ( 'app_submenu_settings_otheroptions' ),
					'alertes/' => $this->lang->line('app_submenu_alert'),
					'helpers/' => $this->lang->line ( 'app_submenu_helper' ),
					
					'workflow/' => $this->lang->line ( 'app_submenu_settings_workflow' ),
					
					'donneurs/' => $this->lang->line ( 'app_submenu_donors' ),
					
					'management/configuration' => $this->lang->line ( 'app_submenu_settings_config' ) 
			) 
	);
	
	foreach ( $available_menu ['acl/'] [1] as $key => $value ) {
		if (! (in_array ( $key, $tab_task ))) {
			unset ( $available_menu ['acl/'] [1] [$key] );
		}
	}
	
	// end of available menu
	
	echo '<div id="header">';
	
	// processing the menu
	
	foreach ( $available_menu as $menu_key => $menu_item ) {
		
		$menu_controller = stristr ( $menu_key, '/', true ) . '/';
		
		if (is_array ( $menu_item )) {
			
			foreach ( $menu_item [1] as $submenu_key => $submenu_item ) {
				
				$submenu_controller = stristr ( $submenu_key, '/', true ) . '/';
				
				if (in_array ( $submenu_controller, $this->session->userdata ( 'usergroupsrules' ) )) {
					$processed_sub_menu [] = anchor ( $submenu_key, $submenu_item );
					$processed_sub_menu_keys [] = $submenu_key;
				}
			}
			if (isset ( $processed_sub_menu ) && ! empty ( $processed_sub_menu )) {
				$processed_menu [anchor ( $processed_sub_menu_keys [0], $menu_item [0] )] = $processed_sub_menu;
			}
			unset ( $processed_sub_menu );
			unset ( $processed_sub_menu_keys );
			// }
		} else {
			if (in_array ( $menu_controller, $this->session->userdata ( 'usergroupsrules' ) )) {
				$processed_menu [] = anchor ( $menu_key, $menu_item );
			}
		}
	}
	
	// end of processing the menu
	
	echo ul ( $processed_menu, $attributes ) . '<p class="user">' . $this->lang->line ( 'app_hello' ) . ', ' . 

	anchor ( 'acl/profile/', $this->session->userdata ( 'user_fullname' ) ) . ' | ' . anchor ( 'auth/logout', $this->lang->line ( 'app_submenu_logout' ) ) . '</p></div>';
}