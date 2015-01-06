<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Acl extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->lang->load ( 'acl', $this->config->item ( 'language' ) );
	}
	function index() {
		redirect ( 'acl/users' );
	}
	function users() {
		$usergeozones = $this->session->userdata ( 'usergeozones' );
		
		$usergroupid = $this->session->userdata ( 'usergroup_id' );
		
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->acl_mdl->get_users_restricted ( $preps ['offset'], $preps ['terms'], $usergeozones, $usergroupid );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['user_fullname'] = anchor ( '/acl/editacc/' . $data ['list'] [$k] ['user_id'], $data ['list'] [$k] ['user_fullname'] );
			$data ['list'] [$k] ['user_active'] = $this->pbf->rec_op_icon ( 'active_' . $data ['list'] [$k] ['user_active'], '/acl/setuserstate/' . $data ['list'] [$k] ['user_id'] );
			$data ['list'] [$k] ['user_published'] = $this->pbf->rec_op_icon ( 'publish_' . $data ['list'] [$k] ['user_published'], '/acl/setuserpublish/' . $data ['list'] [$k] ['user_id'] );
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/acl/editacc/' . $data ['list'] [$k] ['user_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/acl/delacc/' . $data ['list'] [$k] ['user_id'] );
			$data ['list'] [$k] ['checkbox'] = form_checkbox ( 'item[]', $data ['list'] [$k] ['user_id'] );
			$data ['list'] [$k] ['user_id'] = $k + $preps ['offset'] + 1;
		}
		$check_all = array (
				'name' => 'sel_all',
				'id' => 'sel_all',
				'onClick' => 'selecte_all(this)' 
		);
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'acl_list_fullname' ),
				$this->lang->line ( 'acl_list_email_address' ),
				$this->lang->line ( 'acl_list_jobtitle' ),
				$this->lang->line ( 'acl_list_phone' ),
				'',
				'',
				'',
				'',
				form_checkbox ( $check_all ) 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'app_submenu_settings_acl' ) . ' - ' . $this->lang->line ( 'app_submenu_settings_acl_users' ) . ' [' . $data ['records_num'] . ']';
		$data ['mod_title'] ['/acl/addacc'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['#'] = $this->pbf->rec_op_icon ( 'delete_selected' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['tab_menus'] = $this->pbf->get_mod_submenu ( 14 );
		
		$data ['rec_filters'] = $this->pbf->get_filters ( array (
				'user_fullname',
				'usergroup_id',
				'geozone_id' 
		) );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	function groups() {
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->acl_mdl->get_groups ( $preps ['offset'], $preps ['terms'] );
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['usersgroup_name'] = anchor ( '/acl/editgp/' . $v ['usersgroup_id'], $this->lang->line ( 'acl_group_key_' . $v ['usersgroup_id'] ) ) . ' [<span class="innerlinks">' . anchor ( '/acl/rules/' . $data ['list'] [$k] ['usersgroup_id'], $this->lang->line ( 'app_mod_record_operation_authorize' ) ) . '</span>]';
			$data ['list'] [$k] ['isdefault'] = $this->pbf->rec_op_icon ( 'default_' . $data ['list'] [$k] ['isdefault'], '/acl/setdefault/' . $data ['list'] [$k] ['usersgroup_id'] );
			$data ['list'] [$k] ['usersgroup_active'] = $this->pbf->rec_op_icon ( 'active_' . $data ['list'] [$k] ['usersgroup_active'], '/acl/setgpstate/' . $data ['list'] [$k] ['usersgroup_id'] );
			// $data['list'][$k]['auth']=$this->pbf->rec_op_icon('authorize','/acl/rules/'.$data['list'][$k]['usersgroup_id']);
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/acl/editgp/' . $data ['list'] [$k] ['usersgroup_id'] );
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/acl/delgp/' . $data ['list'] [$k] ['usersgroup_id'] );
			$data ['list'] [$k] ['usersgroup_id'] = $k + $preps ['offset'] + 1;
			unset ( $data ['list'] [$k] ['usersgroup_description'] );
			unset ( $data ['list'] [$k] ['inheritby'] );
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'acl_list_group' ),
				$this->lang->line ( 'acl_list_after_login' ),
				$this->lang->line ( 'acl_list_order' ),
				'',
				'',
				'',
				'',
				'',
				'' 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'app_submenu_settings_acl' ) . ' - ' . $this->lang->line ( 'app_submenu_settings_acl_groups' );
		$data ['mod_title'] ['acl/addgp/'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['tab_menus'] = $this->pbf->get_mod_submenu ( 14 );
		
		// TRIGGER WARNING MESSAGE WHEN THERE IS NO DEFAULT USER GROUP
		$get_default_user_group = $this->pbf->get_default_user_group ();
		
		if (empty ( $get_default_user_group )) {
			$data ['mod_clss'] = 'warning';
			$data ['mod_msg'] = $this->lang->line ( 'acl_missing_default_user_group' );
		}
		//
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		
		$this->load->view ( 'body', $data );
	}
	function tasks() {
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->acl_mdl->get_tasks ( $preps ['offset'], $preps ['terms'] );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['usertask_name'] = anchor ( '/acl/edittask/' . $data ['list'] [$k] ['usertask_id'], $data ['list'] [$k] ['usertask_name'] );
			
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/acl/edittask/' . $data ['list'] [$k] ['usertask_id'] );
			
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/acl/deltask/' . $data ['list'] [$k] ['usertask_id'] );
			
			$data ['list'] [$k] ['usertask_id'] = $k + $preps ['offset'] + 1;
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'acl_list_task' ),
				$this->lang->line ( 'acl_list_description' ),
				'',
				'' 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'app_submenu_settings_acl' ) . ' - ' . $this->lang->line ( 'app_submenu_settings_acl_tasks' );
		$data ['mod_title'] ['acl/addtask/'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['tab_menus'] = $this->pbf->get_mod_submenu ( 14 );
		
		$data ['rec_filters'] = $this->pbf->get_filters ( array (
				'usertask_name' 
		) );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	function rules($usersgroup_id) {
		$data ['group'] = $this->acl_mdl->get_group ( $usersgroup_id );
		
		$data ['tasks'] = $this->acl_mdl->get_rules ( $usersgroup_id );
		
		foreach ( $data ['tasks'] as $k => $v ) {
			$data ['tasks'] [$k] ['usersgroupsrule_id'] = form_checkbox ( array (
					'name' => 'userstask_id[]',
					'id' => 'userstask_id' . $k,
					'value' => $v ['usertask_id'],
					'checked' => empty ( $v ['usersgroupsrule_id'] ) ? FALSE : TRUE 
			// 'style' => 'margin:10px',
						) );
			
			// $data['tasks'][$k]['usertask_name'] = '<b>'.$v['usertask_name'].'</b>';
			
			unset ( $data ['tasks'] [$k] ['usertask_id'] );
		}
		
		array_unshift ( $data ['tasks'], array (
				'',
				$this->lang->line ( 'acl_list_task' ),
				$this->lang->line ( 'acl_list_description' ) 
		) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'app_submenu_settings_acl' ) . ' - ' . $this->lang->line ( 'acl_list_permission' );
		
		$data ['page'] = 'rule_frm';
		$this->load->view ( 'body', $data );
	}
	function addacc($data = '') {
		$this->load->model ( 'geo_mdl' );
		$usergeozones = $this->session->userdata ( 'usergeozones' );
		
		$usergroupsrules = $this->session->userdata ( 'usergroupsrules' );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'app_submenu_settings_acl' ) . ' - ' . $this->lang->line ( 'acl_add_account' );
		
		// if(empty($usergeozones)){
		if (in_array ( "acl/addacc/", $usergroupsrules )) {
			
			// $data['geozones'] = array('' => $this->lang->line('app_form_dropdown_select'))+$this->pbf->get_active_geozones(false);
			
			$data ['geozones'] = $this->pbf->get_active_geozones ( false );
			$data ['geozones_parent'] = $this->geo_mdl->get_geozone_perparent ();
			
			if (($this->session->userdata ( 'usergroup_id' ) != '1') && ($this->session->userdata ( 'usergroup_id' ) != '2')) {
				foreach ( $data ['geozones'] as $k => $v ) {
					
					if (! in_array ( $k, $this->session->userdata ( 'usergeozones' ) )) {
						unset ( $data ['geozones'] [$k] );
					}
				}
			}
			
			// $data['entities'] = $this->pbf->get_entities(true, false, '');
			$data ['entities'] = $this->pbf->get_entities_frm ();
			$data ['usergroup_id'] = $this->pbf->get_usersgroups ();
			
			foreach ( $data ['usergroup_id'] as $k => $v ) {
				if (! in_array ( $k, $this->pbf->group_access_order ( $this->session->userdata ( 'usergroup_id' ) ) )) {
					unset ( $data ['usergroup_id'] [$k] );
				}
			}
			
			$data ['default_user_group_id'] = $this->pbf->get_default_user_group ();
			$data ['default_user_group_id'] = empty ( $data ['default_user_group_id'] ) ? '' : $data ['default_user_group_id'] ['usersgroup_id'];
			
			$data ['page'] = 'user_frm';
		} else {
			
			$data ['usergroup_id'] = $this->pbf->get_usersgroups ( $this->session->userdata ( 'usergroup_id' ) );
			$data ['default_user_group_id'] = $this->pbf->get_default_user_group ();
			$data ['default_user_group_id'] = empty ( $data ['default_user_group_id'] ) ? '' : $data ['default_user_group_id'] ['usersgroup_id'];
			$data ['page'] = 'user_limited_frm';
		}
		
		$this->load->view ( 'body', $data );
	}
	function get_user_entities() {
		$geozone_id = $this->input->post ( 'geozone_id' );
		$user_entity = $this->input->post ( 'user_entity' );
		$entities = $this->pbf->get_entities_frm ( $geozone_id );
		
		foreach ( $entities as $k => $v ) {
			if ($k == $user_entity) {
				$selected = "selected='selected'";
			} else {
				$selected = '';
			}
			echo "<option $selected value='{$k}'>" . $v . "</option>";
		}
	}
	function check_group_entityassociated() {
		$group_id = $this->input->post ( 'group_id' );
		echo $this->pbf->check_group_entityassociated ( $group_id );
	}
	function setuserstate($user_id, $state) {
		if ($this->acl_mdl->set_user_state ( $user_id, $state )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'acl_account_state_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'acl_account_state_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	function setgpstate($usersgroup_id, $state) {
		if ($this->acl_mdl->set_gp_state ( $usersgroup_id, $state )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'acl_group_state_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'acl_group_state_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	function setuserpublish($user_id, $state) {
		if ($this->acl_mdl->set_user_publish ( $user_id, $state )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'acl_user_publish_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'acl_user_publish_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	function setdefault($usersgroup_id, $state) {
		if ($this->acl_mdl->set_default ( $usersgroup_id, $state )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'acl_group_default_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'acl_group_default_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	function addgp($data = '') {
		$data ['afterlogin'] = $this->pbf->get_resources ( '', true );
		
		$data ['inheritby'] = $this->pbf->get_usersgroups ();
		$data ['get_report_access'] = $this->pbf->get_reports ();
		
		$data ['user_group_access'] = $data ['inheritby'];
		unset ( $data ['user_group_access'] [''] );
		
		$data ['datatype'] = array (
				'indicator_claimed_value' => 'indicator_claimed_value',
				'indicator_verified_value' => 'indicator_verified_value',
				'indicator_validated_value' => 'indicator_validated_value' 
		);
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'app_submenu_settings_acl' ) . ' - ' . $this->lang->line ( 'acl_list_group' );
		
		$data ['page'] = 'group_frm';
		
		$this->load->view ( 'body', $data );
	}
	function addtask($data = '') {
		$data ['usertask_name'] = $this->pbf->get_controllers ();
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'app_submenu_settings_acl' ) . ' - ' . $this->lang->line ( 'acl_list_task' );
		$data ['page'] = 'task_frm';
		
		$this->load->view ( 'body', $data );
	}
	function saveacc() {
		$user_profile = $this->input->post ();
		
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'user_fullname', $this->lang->line ( 'acl_form_rule_fullname' ), 'trim|required' );
		// if($user_profile['user_id']==)
		$this->form_validation->set_rules ( 'user_name', $this->lang->line ( 'acl_form_rule_email' ), 'trim|required|valid_email' );
		$this->form_validation->set_rules ( 'user_jobtitle', $this->lang->line ( 'acl_form_rule_jobtitle' ), 'trim' );
		$this->form_validation->set_rules ( 'user_phonenumber', $this->lang->line ( 'acl_form_rule_phone' ), 'trim' );
		$this->form_validation->set_rules ( 'user_pwd', $this->lang->line ( 'acl_form_rule_password' ), 'trim|matches[user_pwd_conf]' . (empty ( $user_profile ['user_id'] ) ? '|required' : '') );
		$this->form_validation->set_rules ( 'user_pwd_conf', $this->lang->line ( 'acl_form_rule_confpassword' ), 'trim' . (empty ( $user_profile ['user_id'] ) ? '|required' : '') );
		
		if (($this->session->userdata ( 'usergroup_id' ) != '1') && ($this->session->userdata ( 'usergroup_id' ) != '2')) { // if not admin nat nned to select zone
			$this->form_validation->set_rules ( 'geozones', 'geozone', 'required' );
		}
		
		if ($this->form_validation->run () == FALSE) {
			$data ['user'] = $user_profile;
			$this->addacc ( $data );
			// $this->editacc($this->input->post('user_id'));
		} else {
			$user_profile_key = '';
			
			if (empty ( $user_profile ['entity_id'] )) {
				if (empty ( $user_profile ['geozones'] ))
					$usergeozone = array ();
				else
					$usergeozone = $user_profile ['geozones'];
				$user_profile_key = 'geozone_id';
			} else {
				$usergeozone = $user_profile ['entity_id'];
				$user_profile_key = 'entity_id';
			}
			$usergroup_id = $user_profile ['usergroup_id'];
			
			unset ( $user_profile ['submit'] );
			unset ( $user_profile ['user_pwd_conf'] );
			unset ( $user_profile ['geozones'] );
			unset ( $user_profile ['entity_id'] );
			unset ( $user_profile ['entities'] );
			unset ( $user_profile ['usergroup_id'] );
			
			unset ( $user_profile ['acc_user_entity'] );
			unset ( $user_profile ['group_entity_associated'] );
			
			$user_profile ['user_active'] = ! isset ( $user_profile ['user_active'] ) ? 0 : 1;
			$user_profile ['user_published'] = ! isset ( $user_profile ['user_published'] ) ? 0 : 1;
			
			if (isset ( $user_profile ['user_pwd'] ) && ! empty ( $user_profile ['user_pwd'] )) {
				$user_profile ['user_pwd'] = md5 ( $user_profile ['user_pwd'] );
			} else {
				unset ( $user_profile ['user_pwd'] );
			}
			
			$usergeozone = empty ( $usergeozone ) ? $this->session->userdata ( 'usergeozones' ) : $usergeozone;
			
			if ($this->acl_mdl->save_user ( $user_profile, $usergeozone, $usergroup_id, $user_profile_key )) 

			{
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => $this->lang->line ( 'acl_account_save_success' ) 
				) );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'acl_account_save_error' ) 
				) );
			}
			$this->pbf->set_eventlog ( '', 0 );
			
			// if level < admin nat, add message on admin nat dashboard
			
			if (($this->session->userdata ( 'usergroup_id' ) != '1') && ($this->session->userdata ( 'group_id' ) != '2')) {
				$format = 'DATE_RFC822';
				$time = time ();
				
				$notice .= "</BR>Notice, User " . $user_profile ['user_fullname'] . " has been edited by " . $this->session->userdata ( 'user_fullname' ) . " on " . standard_date ( $format, $time );
				
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
	function updateprofile() {
		$user_profile = $this->input->post ();
		
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'user_fullname', $this->lang->line ( 'acl_form_rule_fullname' ), 'trim|required' );
		$this->form_validation->set_rules ( 'user_name', $this->lang->line ( 'acl_form_rule_email' ), 'trim|required|valid_email' );
		$this->form_validation->set_rules ( 'user_jobtitle', $this->lang->line ( 'acl_form_rule_jobtitle' ), 'trim|required' );
		$this->form_validation->set_rules ( 'user_phonenumber', $this->lang->line ( 'acl_form_rule_phone' ), 'trim|required' );
		$this->form_validation->set_rules ( 'user_pwd', $this->lang->line ( 'acl_form_rule_password' ), 'trim|matches[user_pwd_conf]' . (empty ( $user_profile ['user_pwd'] ) ? '' : '|required') );
		$this->form_validation->set_rules ( 'user_pwd_conf', $this->lang->line ( 'acl_form_rule_confpassword' ), 'trim' . (empty ( $user_profile ['user_pwd'] ) ? '' : '|required') );
		
		if ($this->form_validation->run () == FALSE) {
			$this->pbf->set_eventlog ( 'acl_missing_required_information', 1 );
			$this->profile ();
		} else {
			
			unset ( $user_profile ['submit'] );
			unset ( $user_profile ['user_pwd_conf'] );
			
			if (isset ( $user_profile ['user_pwd'] ) && ! empty ( $user_profile ['user_pwd'] )) {
				$user_profile ['user_pwd'] = md5 ( $user_profile ['user_pwd'] );
			} else {
				unset ( $user_profile ['user_pwd'] );
			}
			
			if ($this->acl_mdl->save_profile ( $user_profile )) 

			{
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => $this->lang->line ( 'acl_profile_save_success' ) . '<SCRIPT LANGUAGE="JavaScript">setTimeout(window.location.href = "' . base_url () . 'auth/logout/",10000);</SCRIPT>' 
				) );
				$this->pbf->set_eventlog ( 'acl_profile_save_success', 1 );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'acl_profile_save_error' ) 
				) );
				$this->pbf->set_eventlog ( '', 0 );
			}
		}
		redirect ( $this->session->userdata ( 'afterlogin' ) );
	}
	function savegp() {
		$group = $this->input->post ();
		
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'usersgroup_name', 'Group name', 'trim|required' );
		$this->form_validation->set_rules ( 'usersgroup_description', 'Description', 'trim|required' );
		$this->form_validation->set_rules ( 'afterlogin', 'After login task', 'trim|required' );
		$this->form_validation->set_rules ( 'sortorder', 'Order', 'trim|required' );
		
		if ($this->form_validation->run () == FALSE) {
			$data ['group'] = $group;
			$this->addgp ( $data );
		} else {
			
			unset ( $group ['submit'] );
			
			$group ['isdefault'] = ! isset ( $group ['isdefault'] ) ? 0 : 1;
			$group ['usersgroup_entity_associated'] = ! isset ( $group ['usersgroup_entity_associated'] ) ? 0 : 1;
			$group ['usersgroup_active'] = ! isset ( $group ['usersgroup_active'] ) ? 0 : 1;
			$group ['datatype_access'] = json_encode ( $group ['datatype_access'] );
			$group ['user_group_access'] = json_encode ( $group ['user_group_access'] );
			$group ['report_group_access'] = json_encode ( $group ['report_group_access'] );
			
			if ($this->acl_mdl->save_group ( $group )) 

			{
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => $this->lang->line ( 'acl_group_save_success' ) 
				) );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'error',
						'mod_msg' => $this->lang->line ( 'acl_group_save_error' ) 
				) );
			}
			$this->pbf->set_eventlog ( '', 0 );
			redirect ( $this->session->userdata ( 'next_base_url' ) );
		}
	}
	function savetask() {
		$task = $this->input->post ();
		
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'usertask_name', 'Task name', 'trim|required' );
		$this->form_validation->set_rules ( 'usertask_description', 'Description', 'trim|required' );
		
		if ($this->form_validation->run () == FALSE) {
			$this->addtask ( $task );
		} else {
			unset ( $task ['submit'] );
			
			if ($this->acl_mdl->save_task ( $task )) 

			{
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => $this->lang->line ( 'acl_task_save_success' ) 
				) );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'acl_task_save_error' ) 
				) );
			}
			$this->pbf->set_eventlog ( '', 0 );
			redirect ( $this->session->userdata ( 'next_base_url' ) );
		}
	}
	function saverule() {
		$rules = $this->input->post ();
		
		unset ( $rules ['submit'] );
		
		if ($this->acl_mdl->save_rules ( $rules )) 

		{
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'acl_grouptask_save_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'acl_grouptask_save_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( 'acl/groups' );
	}
	function editacc($user_id) {
		$data ['user'] = $this->acl_mdl->get_acc ( $user_id );
		$data ['user'] ['group_entity_associated'] = $this->pbf->check_group_entityassociated ( $data ['user'] ['usergroup_id'] );
		
		if (in_array ( $data ['user'] ['usergroup_id'], $this->pbf->group_access_order ( $this->session->userdata ( 'usergroup_id' ) ) )) 		// extra security check
		{
			
			$data ['user'] ['usergeozone'] = $this->acl_mdl->get_acc_geozones ( $user_id );
			$data ['user'] ['entities'] = $this->pbf->get_user_entities ( $user_id );
			$data ['default_user_group_id'] = $this->pbf->get_default_user_group ();
			
			$data ['default_user_group_id'] = empty ( $data ['default_user_group_id'] ) ? $data ['user'] ['usergroup_id'] : $data ['default_user_group_id'] ['usersgroup_id'];
			$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'acl_grouptask_save_error' ) . ' - ' . $this->lang->line ( 'acl_edit_account' );
			
			$this->addacc ( $data );
		}
	}
	function profile() {
		$this->pbf->set_eventlog ( 'acl_open_profile_form', 1 );
		
		$data ['user'] = array (
				'user_id' => $this->session->userdata ( 'user_id' ),
				'user_fullname' => $this->session->userdata ( 'user_fullname' ),
				'user_jobtitle' => $this->session->userdata ( 'user_jobtitle' ),
				'user_phonenumber' => $this->session->userdata ( 'user_phonenumber' ),
				'user_name' => $this->session->userdata ( 'user_name' ) 
		);
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'acl_profile_update_title' );
		
		$data ['page'] = 'userprofile_frm';
		$this->load->view ( 'body', $data );
	}
	function editgp($usersgroup_id) {
		$data ['group'] = $this->acl_mdl->get_group ( $usersgroup_id );
		$data ['group'] ['user_group_access'] = $this->pbf->group_access_order ( $usersgroup_id );
		
		$data ['group'] ['report_group_access'] = json_decode ( $data ['group'] ['report_group_access'] );
		
		// $data['inheritby'] = $this->pbf->get_usersgroups($usersgroup_id);
		$datatype_access = json_decode ( $data ['group'] ['datatype_access'] );
		$data ['group'] ['datatype_access'] = array_combine ( $datatype_access, $datatype_access );
		
		$this->addgp ( $data );
	}
	function edittask($usertask_id) {
		$data ['task'] = $this->acl_mdl->get_task ( $usertask_id );
		
		$this->addtask ( $data );
	}
	function delacc($user_id) {
		if ($this->acl_mdl->del_user ( $user_id )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'acl_account_delete_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'acl_account_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	function delete_selected_acc() {
		if ($this->acl_mdl->delete_selected_acc ( $this->input->post ( 'item' ) )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'acl_account_delete_selected_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'acl_account_delete_selected_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	function delgp($usersgroup_id) {
		if ($this->acl_mdl->del_group ( $usersgroup_id )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'acl_group_delete_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'acl_group_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	function deltask($usertask_id) {
		if ($this->acl_mdl->del_task ( $usertask_id )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'acl_task_delete_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'acl_task_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	function delrule($usersgroupsrule_id) {
		if ($this->acl_mdl->del_rule ( $usersgroupsrule_id )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'acl_grouptask_delete_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'acl_grouptask_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( $this->session->userdata ( 'next_base_url' ) );
	}
	function donators() {
	}
}
