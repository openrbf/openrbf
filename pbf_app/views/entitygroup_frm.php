<div class="block">

	<div class="block_head">
					
					<?php
					echo (isset ( $mod_title ) ? $this->pbf->get_mod_title ( $mod_title ) : '');
					?>
					
					
				</div>
	<!-- .block_head ends -->

	<div class="block_content">
<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
echo form_open_multipart ( 'hfrentities/savegroup' ) . 

form_hidden ( array (
		'entity_group_id' => isset ( $entitygroup ['entity_group_id'] ) ? $entitygroup ['entity_group_id'] : '' 
) ) . 

form_fieldset ( $this->lang->line ( 'frm_entity_group_definition' ) ) . 

'<p>' . form_label ( $this->lang->line ( 'frm_entity_group_title' ), 'entity_group_name' ) . 

form_input ( array (
		'name' => 'entity_group_name',
		'id' => 'entity_group_name',
		'value' => set_value ( 'entity_group_name', isset ( $entitygroup ['entity_group_name'] ) ? $entitygroup ['entity_group_name'] : '' ) 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_entity_group_title_abbrev' ), 'entity_group_abbrev' ) . 

form_input ( array (
		'name' => 'entity_group_abbrev',
		'id' => 'entity_group_abbrev',
		'value' => set_value ( 'entity_group_abbrev', isset ( $entitygroup ['entity_group_abbrev'] ) ? $entitygroup ['entity_group_abbrev'] : '' ) 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_entity_group_entity_group_bonus' ), 'entity_group_bonus' ) . 

form_input ( array (
		'name' => 'entity_group_bonus',
		'id' => 'entity_group_bonus',
		'value' => set_value ( 'entity_group_bonus', isset ( $entitygroup ['entity_group_bonus'] ) ? $entitygroup ['entity_group_bonus'] : '' ) 
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