<div class="block">

	<div class="block_head">
					
					<?php
					echo (isset ( $mod_title ) ? $this->pbf->get_mod_title ( $mod_title ) : '');
					?>
					
					
				</div>
	<!-- .block_head ends -->



	<div class="block_content"><?php
	
	echo form_open ( 'otheroptions/savebank' ) . 

	form_hidden ( array (
			'bank_id' => isset ( $bank ['bank_id'] ) ? $bank ['bank_id'] : '' 
	) ) . 

	form_fieldset ( $this->lang->line ( 'bank_definition' ) ) . 

	'<p>' . form_label ( $this->lang->line ( 'bank_frm_title' ), 'bank_name' ) . 

	form_input ( array (
			'name' => 'bank_name',
			'id' => 'bank_name',
			'value' => set_value ( 'bank_name', isset ( $bank ['bank_name'] ) ? $bank ['bank_name'] : '' ) 
	) ) . '</p>' . 

	'<p>' . form_label ( $this->lang->line ( 'bank_frm_abrev' ), 'bank_name_abbrev' ) . 

	form_input ( array (
			'name' => 'bank_name_abbrev',
			'id' => 'bank_name_abbrev',
			'value' => set_value ( 'bank_name_abbrev', isset ( $bank ['bank_name_abbrev'] ) ? $bank ['bank_name_abbrev'] : '' ) 
	) ) . '</p>' . 

	'<p>' . form_label ( $this->lang->line ( 'bank_frm_parent' ), 'bank_parent_id' ) . 

	form_cascaded_dropdown ( 'bank_parent_id', $bank_parent, isset ( $bank ['bank_parent_id'] ) ? $bank ['bank_parent_id'] : '' ) . '</p>' . 
	// form_cascaded_dropdown('entity_class_id', $entity_class ,isset($file['entity_class_id'])?$file['entity_class_id']:'', 'id="entity_class_id"').'</p>'.
	
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