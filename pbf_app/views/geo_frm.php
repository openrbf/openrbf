<div class="block">

	<div class="block_head">
					
					<?php
					echo (isset ( $mod_title ) ? $this->pbf->get_mod_title ( $mod_title ) : '');
					?>
					
					
				</div>
	<!-- .block_head ends -->



	<div class="block_content"><?php
	echo form_open ( 'geo/savegeo' ) . 

	form_hidden ( array (
			'geo_id' => isset ( $geo ['geo_id'] ) ? $geo ['geo_id'] : '' 
	) ) . 

	form_fieldset ( $this->lang->line ( 'geo_definition' ) ) . 

	'<p>' . form_label ( $this->lang->line ( 'geo_frm_title' ), 'geo_title' ) . 

	form_input ( array (
			'name' => 'geo_title',
			'id' => 'geo_title',
			'value' => set_value ( 'geo_title', isset ( $geo ['geo_title'] ) ? $this->lang->line ( 'geo_key_' . $geo ['geo_id'] ) : '' ) 
	) ) . '</p>' . 

	'<p>' . form_label ( $this->lang->line ( 'geo_frm_level' ), 'geo_parent' ) . 

	form_dropdown ( 'geo_parent', $geos, isset ( $geo ['geo_parent'] ) ? $geo ['geo_parent'] : '' ) . '</p>' . 

	'<p>' . form_label ( $this->lang->line ( 'geo_frm_active' ), 'geo_active' ) . 

	form_checkbox ( array (
			'name' => 'geo_active',
			'id' => 'geo_active',
			'value' => 1,
			'checked' => (isset ( $geo ['geo_active'] ) && $geo ['geo_active'] == 1) ? TRUE : FALSE 
	) ) . '</p>' . 

	form_fieldset_close () . 
	
	// Begin form footer...........
	form_submit ( 'submit', $this->lang->line ( 'app_form_save' ), 'class="submit small"' ) . form_reset ( '', $this->lang->line ( 'app_form_cancel' ), 'onClick="history.go(-1);return true;" class="submit small"' ) . 
	// End form footer...........
	
	form_close ();
	?></div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>

</div>