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
echo form_open_multipart ( 'hfrentities/savetype' ) . 

form_hidden ( array (
		'entity_type_id' => isset ( $entitytype ['entity_type_id'] ) ? $entitytype ['entity_type_id'] : '' 
) ) . 

form_fieldset ( isset ( $entitytype ['entity_type_id'] ) ? 'Edit entity type' : 'Add new entity type' ) . 

'<p>' . form_label ( 'Type title:', 'entity_type_name' ) . 

form_input ( array (
		'name' => 'entity_type_name',
		'id' => 'entity_type_name',
		'value' => set_value ( 'entity_type_name', isset ( $entitytype ['entity_type_name'] ) ? $entitytype ['entity_type_name'] : '' ) 
) ) . '</p>' . 

'<p>' . form_label ( 'Type title abbrev:', 'entity_type_abbrev' ) . 

form_input ( array (
		'name' => 'entity_type_abbrev',
		'id' => 'entity_type_abbrev',
		'value' => set_value ( 'entity_type_abbrev', isset ( $entitytype ['entity_type_abbrev'] ) ? $entitytype ['entity_type_abbrev'] : '' ) 
) ) . '</p>' . 

'<p>' . form_label ( 'Class:', 'entity_class_id' ) . 

form_dropdown ( 'entity_class_id', $entity_class, isset ( $entitytype ['entity_class_id'] ) ? $entitytype ['entity_class_id'] : '', 'id="entity_class_id"' ) . '</p>' . 

form_fieldset_close () . 

// Begin form footer...........
form_submit ( 'submit', 'Save', 'class="submit small"' ) . form_reset ( '', 'Cancel', 'onClick="history.go(-1);return true;" class="submit small"' ) . 

// End form footer...........

form_close ();
?></div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>

</div>