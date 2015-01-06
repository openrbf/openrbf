<div class="block">

	<div class="block_head">
					
					<?php
					echo (isset ( $mod_title ) ? $this->pbf->get_mod_title ( $mod_title ) : '');
					?>
									
				</div>
	<!-- .block_head ends -->
	<div class="block_content">
				
					<?php
					
					echo "<label><img src=" . $this->config->item ( 'base_url' ) . "cside/images/warning.jpg style='width:30px;heigth:30px'/>" . $this->lang->line ( "acl_control_form" ) . "</label>";
					
					echo form_open ( 'acl/saveacc', 'onsubmit="return controlFormOnSubmit(this)"' ) . 

					form_hidden ( array (
							'user_id' => isset ( $user ['user_id'] ) ? $user ['user_id'] : '' 
					) ) . 

					form_fieldset ( $this->lang->line ( 'acl_profile_identification' ) ) . 

					'<p>' . form_label ( $this->lang->line ( 'acl_form_rule_fullname' ), 'user_fullname' ) . 

					form_input ( array (
							'name' => 'user_fullname',
							'id' => 'user_fullname',
							'value' => set_value ( 'user_fullname', isset ( $user ['user_fullname'] ) ? $user ['user_fullname'] : '' ) 
					) ) . '<span style="color:red;">*</span></p>' . 

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

					'<p>' . form_label ( $this->lang->line ( 'acl_form_acc_featured' ), 'user_published' ) . 

					form_checkbox ( array (
							'name' => 'user_published',
							'id' => 'user_published',
							'value' => 1,
							'checked' => (isset ( $user ['user_published'] ) && $user ['user_published'] == 1) ? TRUE : FALSE 
					) ) . '</p>' . 

					form_fieldset_close () . 

					form_fieldset ( $this->lang->line ( 'acl_profile_login_info' ) ) . 

					'<p>' . form_label ( $this->lang->line ( 'acl_form_rule_email' ), 'user_name' ) . 

					form_input ( array (
							'name' => 'user_name',
							'id' => 'user_name',
							'value' => set_value ( 'user_name', isset ( $user ['user_name'] ) ? $user ['user_name'] : '' ) 
					) ) . '<span style="color:red;">*</span></p>' . 

					'<p>' . form_label ( $this->lang->line ( 'acl_form_rule_password' ), 'user_pwd' ) . 

					form_password ( array (
							'name' => 'user_pwd',
							'id' => 'user_pwd' 
					) );
					if ($user ['user_id'] == 0)
						echo '<span style="color:red;">*</span>';
					echo '</p>';
					
					echo '<p>' . form_label ( $this->lang->line ( 'acl_form_rule_confpassword' ), 'user_pwd_conf' ) . 

					form_password ( array (
							'name' => 'user_pwd_conf',
							'id' => 'user_pwd_conf' 
					) );
					if ($user ['user_id'] == 0)
						echo '<span style="color:red;">*</span>';
					echo '</p>';
					
					echo '<p>' . form_label ( $this->lang->line ( 'acl_form_acc_group' ), 'usergroup_id' ) . 

					form_dropdown ( 'usergroup_id', $usergroup_id, isset ( $user ['usergroup_id'] ) ? $user ['usergroup_id'] : $default_user_group_id, 'id="usergroup_id"' ) . '<span style="color:red;">*</span></p>' . 

					'<p>' . form_label ( $this->lang->line ( 'acl_form_acc_active' ), 'user_active' ) . 

					form_checkbox ( array (
							'name' => 'user_active',
							'id' => 'user_active',
							'value' => 1,
							'checked' => (isset ( $user ['user_active'] ) && $user ['user_active'] == 1) ? TRUE : FALSE 
					) ) . '</p>' . 
					
					// 3rd parameter for set_checkbox can be FALSE or TRUE
					
					form_fieldset_close () . 

					form_fieldset ( $this->lang->line ( 'acl_profile_work_area' ) ) . 

					'<p>' . form_label ( $this->lang->line ( 'acl_form_acc_click_ctrl' ), 'geozones' );
					
					// form_dropdown('geozones[]', $geozones ,isset($user['usergeozone'])?$user['usergeozone']:'', 'id="geozones" multiple="multiple"').'</p>'
					
					// Hardcod a corriger
					if ($user ['usergroup_id'] == '13') {
						$multiple = '';
					} else {
						$multiple = 'multiple="multiple"';
					}
					
					echo '<select name="geozones[]" id="geozones" ' . $multiple . ' onchange="fill_entities()">';
					
					$act_region = '';
					foreach ( $geozones_parent as $k => $v ) {
						if ($v ['P_geozoneId'] != $act_region) {
							echo '<optgroup label="' . $v ['P_geozoneName'] . '">';
						}
						if (isset ( $user ['usergeozone'] )) {
							if (in_array ( $v ['F_geozoneId'], $user ['usergeozone'] )) {
								$selected = 'selected="selected"';
							} else {
								$selected = '';
							}
							$user ['usergeozone'];
						}
						echo '<option value="' . $v ['F_geozoneId'] . '" ' . $selected . ' style="margin-left:20px">' . $v ['F_geozoneName'] . '</option>';
						$act_region = $v ['P_geozoneId'];
					}
					
					echo '</select><span style="color:red;">*</span></p>';
					
					echo '<p id="label_entities">' . 

					'</p>' . 

					form_input ( array (
							'name' => 'acc_user_entity',
							'id' => 'acc_user_entity',
							'value' => set_value ( 'acc_user_entity', isset ( $user ['user_entity'] ) ? $user ['user_entity'] : '0' ) 
					) ) . 

					form_input ( array (
							'name' => 'group_entity_associated',
							'id' => 'group_entity_associated',
							'value' => set_value ( 'group_entity_associated', isset ( $user ['group_entity_associated'] ) ? $user ['group_entity_associated'] : '0' ) 
					) );
					
					echo form_fieldset_close () . 
					
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
<script>

$('select#usergroup_id').on('change', function (e) {
	
	$.ajax({
	 type: 'POST',
	 url: '<?php echo base_url(); ?>/acl/check_group_entityassociated/', 
	 data: 'group_id='+this.value,
	 async: false, 
	 success: function(resp1) {
	 $('#group_entity_associated').val(resp1); 
	 }
	 });
	
	if($('#group_entity_associated').val()=='1'){
		$('#label_entities').html('<label for="user_entity">Entities</label><select name="user_entity" id="user_entity"></select>');
		fill_entities(0);
		$('#geozones').attr('multiple',false);
	}else{
		$('#label_entities').html('');
		$('#geozones').attr('multiple','multiple');
	}
   
});

function fill_entities(user_entity)
 {
 	
	 var geo_id = $('select#geozones').val(); 
	 //alert(user_entity);
	 $.ajax({
	 type: 'POST',
	 url: '<?php echo base_url(); ?>/acl/get_user_entities/', 
	 data: 'geozone_id='+geo_id+'&user_entity='+user_entity, 
	 success: function(resp) {
	 $('select#user_entity').html(resp); 
	 }
	 });
 }

	$(function() { 
		
		if($('#group_entity_associated').val()=='1'){
		$('#label_entities').html('<label for="user_entity">Entities</label><select name="user_entity" id="user_entity"></select>');
		$('#geozones').attr('multiple',false);
		}else{
			
			$('#label_entities').html('');
			
		}
		fill_entities($('#acc_user_entity').val());
		$('#acc_user_entity').hide();
		$('#group_entity_associated').hide();
	 });

</script>