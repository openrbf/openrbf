<div class="block">

	<div class="block_head">
					<?php
					echo (isset ( $mod_title ) ? $this->pbf->get_mod_title ( $mod_title ) : '');
					?>
					
					
				</div>
	<!-- .block_head ends -->



	<div class="block_content"><?php
	
	$tmpl = array (
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
			'row_start' => '<tr class="even">',
			'row_end' => '</tr>',
			'row_alt_start' => '<tr class="odd">',
			'row_alt_end' => '</tr>',
			'table_close' => '</table>' 
	);
	
	$this->table->set_template ( $tmpl );
	
	echo form_open ( 'acl/saverule' ) . 

	form_hidden ( array (
			'usersgroup_id' => $group ['usersgroup_id'] 
	) ) . 

	form_fieldset ( 'Edit authorisation' ) . 

	'<p>' . form_label ( $this->lang->line ( 'acl_list_group' ) . ':', 'usersgroup_name' ) . 

	form_input ( array (
			'name' => 'usersgroup_name',
			'id' => 'usersgroup_name',
			'disabled' => 'disabled',
			'value' => set_value ( 'usersgroup_name', $this->lang->line ( 'acl_group_key_' . $group ['usersgroup_id'] ) ) 
	) ) . '</p>' . 

	'<p>' . form_label ( $this->lang->line ( 'acl_list_description' ) . ':', 'usersgroup_description' ) . 

	form_textarea ( array (
			'name' => 'usersgroup_description',
			'id' => 'usersgroup_description',
			'disabled' => 'disabled',
			'rows' => 6,
			'cols' => 40,
			'value' => set_value ( 'usersgroup_description', $group ['usersgroup_description'] ) 
	) ) . '</p>' . 

	$this->table->generate ( $tasks ) . 

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