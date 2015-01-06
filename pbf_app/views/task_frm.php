<div class="block">

	<div class="block_head">
					
					<?php
					echo (isset ( $mod_title ) ? $this->pbf->get_mod_title ( $mod_title ) : '');
					?>
					
					
				</div>
	<!-- .block_head ends -->



	<div class="block_content"><?php
	echo form_open ( 'acl/savetask' ) . 

	form_hidden ( array (
			'usertask_id' => isset ( $task ['usertask_id'] ) ? $task ['usertask_id'] : '' 
	) ) . 

	form_fieldset ( isset ( $task ['usertask_id'] ) ? 'Edit task' : 'Add new task' ) . 

	'<p>' . form_label ( 'Task name', 'usertask_name' ) . 

	form_dropdown ( 'usertask_name', $usertask_name, isset ( $task ['usertask_name'] ) ? $task ['usertask_name'] : '' ) . '</p>' . 

	'<p>' . form_label ( 'Description', 'usertask_description' ) . 

	form_textarea ( array (
			'name' => 'usertask_description',
			'id' => 'usertask_description',
			'rows' => 6,
			'cols' => 40,
			'value' => set_value ( 'usertask_description', isset ( $task ['usertask_description'] ) ? $task ['usertask_description'] : '' ) 
	) ) . '</p>' . 

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