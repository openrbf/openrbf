<div class="block">

	<div class="block_head">
					
					<?php
					echo (isset ( $mod_title ) ? $this->pbf->get_mod_title ( $mod_title ) : '');
					?>
					
					
				</div>
	<!-- .block_head ends -->



	<div class="block_content"><?php
	echo form_open ( 'files/savefrequency' ) . 

	form_hidden ( array (
			'frequency_id' => isset ( $frequency ['frequency_id'] ) ? $frequency ['frequency_id'] : '' 
	) ) . 

	form_fieldset ( $this->lang->line ( 'list_file_frequency_definition' ) ) . 

	'<p>' . form_label ( $this->lang->line ( 'list_file_frequency' ) . ':', 'frequency_title' ) . 

	form_input ( array (
			'name' => 'frequency_title',
			'id' => 'frequency_title',
			'value' => set_value ( 'frequency_title', isset ( $frequency ['frequency_title'] ) ? $this->lang->line ( 'file_frq_ky_' . $frequency ['frequency_id'] ) : '' ) 
	) ) . '</p>' . 

	'<p>' . form_label ( $this->lang->line ( 'list_file_frequency_months' ), 'frequency_months' ) . 

	form_multiselect ( 'frequency_months[]', $months, isset ( $frequency ['frequency_months'] ) ? json_decode ( $frequency ['frequency_months'] ) : '', 'id="frequency_months" size="12"' ) . '</p>' . 

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