<div class="block">

	<div class="block_head">
									
					<?php
					echo (isset ( $mod_title ) ? $this->pbf->get_mod_title ( $mod_title ) : '');
					?>
					
					
				</div>
	<!-- .block_head ends -->



	<div class="block_content"><?php
	echo form_open ( 'acl/savegp' ) . 

	form_hidden ( array (
			'usersgroup_id' => isset ( $group ['usersgroup_id'] ) ? $group ['usersgroup_id'] : '' 
	) ) . 

	form_fieldset ( isset ( $group ['usersgroup_id'] ) ? 'Edit new user group' : 'Add new user group' ) . 

	'<p>' . form_label ( $this->lang->line ( 'acl_list_group' ) . ':', 'usersgroup_name' ) . 

	form_input ( array (
			'name' => 'usersgroup_name',
			'id' => 'usersgroup_name',
			'value' => set_value ( 'usersgroup_name', isset ( $group ['usersgroup_id'] ) ? $this->lang->line ( 'acl_group_key_' . $group ['usersgroup_id'] ) : '' ) 
	) ) . '</p>' . 

	'<p>' . form_label ( $this->lang->line ( 'acl_list_description' ) . ':', 'usersgroup_description' ) . 

	form_textarea ( array (
			'name' => 'usersgroup_description',
			'id' => 'usersgroup_description',
			'rows' => 6,
			'cols' => 40,
			'value' => set_value ( 'usersgroup_description', isset ( $group ['usersgroup_description'] ) ? $group ['usersgroup_description'] : '' ) 
	) ) . '</p>' . 

	'<p>' . form_label ( $this->lang->line ( 'acl_list_after_login' ) . ':', 'afterlogin' ) . 

	form_dropdown ( 'afterlogin', $afterlogin, isset ( $group ['afterlogin'] ) ? $group ['afterlogin'] : '' ) . '</p>' . 

	'<p>' . form_label ( $this->lang->line ( 'acl_list_order' ) . ':', 'sortorder' ) . 

	form_input ( array (
			'name' => 'sortorder',
			'id' => 'sortorder',
			'value' => set_value ( 'sortorder', isset ( $group ['sortorder'] ) ? $group ['sortorder'] : '' ) 
	) ) . '</p>' . 

	'<p>' . form_label ( $this->lang->line ( 'acl_group_default_group' ) . ':', 'isdefault' ) . 

	form_checkbox ( array (
			'name' => 'isdefault',
			'id' => 'isdefault',
			'value' => 1,
			'checked' => (isset ( $group ['isdefault'] ) && $group ['isdefault'] == 1) ? TRUE : FALSE 
	) ) . '</p>' . 

	'<p>' . form_label ( $this->lang->line ( 'acl_group_active' ) . ':', 'usersgroup_active' ) . 

	form_checkbox ( array (
			'name' => 'usersgroup_active',
			'id' => 'usersgroup_active',
			'value' => 1,
			'checked' => (isset ( $group ['usersgroup_active'] ) && $group ['usersgroup_active'] == 1) ? TRUE : FALSE 
	) ) . '</p>' . 

	'<p>' . form_label ( 'Entity associated:', 'usersgroup_entity_associated' ) . 

	form_checkbox ( array (
			'name' => 'usersgroup_entity_associated',
			'id' => 'usersgroup_entity_associated',
			'value' => 0,
			'checked' => (isset ( $group ['usersgroup_entity_associated'] ) && $group ['usersgroup_entity_associated'] == 1) ? TRUE : FALSE 
	) ) . '</p>' . 
	
	// 3rd parameter for set_checkbox can be FALSE or TRUE
	
	form_fieldset_close () . 

	form_fieldset ( 'Data entry access settings' ) . 

	'<p>' . form_label ( 'Data type', 'datatype_access' ) . 

	form_multiselect ( 'datatype_access[]', $datatype, (isset ( $group ['datatype_access'] ) && ! empty ( $group ['datatype_access'] )) ? $group ['datatype_access'] : $datatype, 'size="4" id="datatype_access"' ) . '</p>' . '<p>' . form_label ( 'User Group access', 'user_group_access' ) . form_multiselect ( 'user_group_access[]', $user_group_access, (isset ( $group ['user_group_access'] ) && ! empty ( $group ['user_group_access'] )) ? $group ['user_group_access'] : $user_group_access, 'size="6" id="user_group_access"' ) . '</p>' . 

	form_fieldset_close () . 

	form_fieldset ( 'Report access' ) . 

	'<p>' . form_label ( 'Report Group access', 'report_group_access' ) . form_multiselect ( 'report_group_access[]', $get_report_access, (isset ( $group ['report_group_access'] ) && ! empty ( $group ['report_group_access'] )) ? $group ['report_group_access'] : $get_report_access [0], 'size="6" id="report_group_access"' ) . '</p>' . 

	form_fieldset_close () . 
	
	// Begin form footer...........
	form_submit ( 'submit', 'Save', 'class="submit small"' ) . form_reset ( '', 'Cancel', 'onClick="history.go(-1);return true;" class="submit small"' ) . 
	// End form footer...........
	
	form_close ();
	?>					
					
				</div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>

</div>