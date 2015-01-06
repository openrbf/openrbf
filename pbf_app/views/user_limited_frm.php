<div class="block">

	<div class="block_head">
					
					<?php
					echo (isset ( $mod_title ) ? $this->pbf->get_mod_title ( $mod_title ) : '');
					?>
									
				</div>
	<!-- .block_head ends -->
	<div class="block_content">
				
					<?php
					
					echo form_open ( 'acl/saveacc' ) . 

					form_hidden ( array (
							'user_id' => isset ( $user ['user_id'] ) ? $user ['user_id'] : '' 
					) ) . 

					form_fieldset ( $this->lang->line ( 'acl_profile_identification' ) ) . 

					'<p>' . form_label ( $this->lang->line ( 'acl_form_rule_fullname' ), 'user_fullname' ) . 

					form_input ( array (
							'name' => 'user_fullname',
							'id' => 'user_fullname',
							'value' => set_value ( 'user_fullname', isset ( $user ['user_fullname'] ) ? $user ['user_fullname'] : '' ) 
					) ) . '</p>' . 

					'<p>' . form_label ( $this->lang->line ( 'acl_form_rule_jobtitle' ), 'user_jobtitle' ) . 

					form_input ( array (
							'name' => 'user_jobtitle',
							'id' => 'user_jobtitle',
							'value' => set_value ( 'user_jobtitle', isset ( $user ['user_jobtitle'] ) ? $user ['user_jobtitle'] : '' ) 
					) ) . '</p>' . 

					form_label ( $this->lang->line ( 'acl_form_rule_phone' ), 'user_phonenumber' ) . 

					form_input ( array (
							'name' => 'user_phonenumber',
							'id' => 'user_phonenumber',
							'value' => set_value ( 'user_phonenumber', isset ( $user ['user_phonenumber'] ) ? $user ['user_phonenumber'] : '' ) 
					) ) . '</p>' . 

					form_fieldset_close () . 

					form_fieldset ( $this->lang->line ( 'acl_profile_login_info' ) ) . 

					'<p>' . form_label ( $this->lang->line ( 'acl_form_rule_email' ), 'user_name' ) . 

					form_input ( array (
							'name' => 'user_name',
							'id' => 'user_name',
							'value' => set_value ( 'user_name', isset ( $user ['user_name'] ) ? $user ['user_name'] : '' ) 
					) ) . '</p>' . 

					'<p>' . form_label ( $this->lang->line ( 'acl_form_rule_password' ), 'user_pwd' ) . 

					form_password ( array (
							'name' => 'user_pwd',
							'id' => 'user_pwd' 
					) ) . '</p>' . 

					'<p>' . form_label ( $this->lang->line ( 'acl_form_rule_confpassword' ), 'user_pwd_conf' ) . 

					form_password ( array (
							'name' => 'user_pwd_conf',
							'id' => 'user_pwd_conf' 
					) ) . '</p>' . 

					'<p>' . form_label ( $this->lang->line ( 'acl_form_acc_group' ), 'usergroup_id' ) . 

					form_dropdown ( 'usergroup_id', $usergroup_id, isset ( $user ['usergroup_id'] ) ? $user ['usergroup_id'] : $default_user_group_id, 'id="usergroup_id"' ) . '</p>' . 

					'<p>' . form_label ( $this->lang->line ( 'acl_form_acc_active' ), 'user_active' ) . 

					form_checkbox ( array (
							'name' => 'user_active',
							'id' => 'user_active',
							'value' => 1,
							'checked' => (isset ( $user ['user_active'] ) && $user ['user_active'] == 1) ? TRUE : FALSE 
					) ) . '</p>' . 
					
					// 3rd parameter for set_checkbox can be FALSE or TRUE
					
					form_fieldset_close () . 
					
					// Begin form footer...........
					
					form_submit ( 'submit', $this->lang->line ( 'app_form_save' ), 'class="submit small"' ) . form_reset ( '', $this->lang->line ( 'app_form_cancel' ), 'onClick="history.go(-1);return true;" class="submit small"' ) . 
					
					// End form footer...........
					
					form_close ();
					?>					
					
				</div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>

</div>
