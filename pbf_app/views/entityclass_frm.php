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
echo form_open_multipart ( 'hfrentities/saveclass' ) . 

form_hidden ( array (
		'entity_class_id' => isset ( $entityclass ['entity_class_id'] ) ? $entityclass ['entity_class_id'] : '' 
) ) . 

form_fieldset ( $this->lang->line ( 'frm_entity_class_definition' ) ) . 

'<p>' . form_label ( $this->lang->line ( 'frm_entity_class_title' ), 'entity_class_name' ) . 

form_input ( array (
		'name' => 'entity_class_name',
		'id' => 'entity_class_name',
		'value' => set_value ( 'entity_class_name', isset ( $entityclass ['entity_class_name'] ) ? $entityclass ['entity_class_name'] : '' ) 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_entity_class_title_abbrev' ), 'entity_class_abbrev' ) . 

form_input ( array (
		'name' => 'entity_class_abbrev',
		'id' => 'entity_class_abbrev',
		'value' => set_value ( 'entity_class_abbrev', isset ( $entityclass ['entity_class_abbrev'] ) ? $entityclass ['entity_class_abbrev'] : '' ) 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_entity_class_properties' ), 'entity_class_properties' ) . 

form_multiselect ( 'entity_class_properties[]', $class_properties, isset ( $entityclass ['class_properties'] ) ? $entityclass ['class_properties'] : '', 'id="entity_class_properties"' ) . '</p>';

// access level table

echo '<div>';
echo form_label ( $this->lang->line ( 'frm_entity_class_accesslevel' ), 'usergroup_id' );
$tmpl = array (
		'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
		'row_start' => '<tr class="even">',
		'row_end' => '</tr>',
		'row_alt_start' => '<tr class="odd">',
		'row_alt_end' => '</tr>',
		'table_close' => '</table>' 
);

$this->table->set_template ( $tmpl );
echo $this->table->generate ( $entityclass ['usergroup_id'] );
echo '</div>';

// end access level table

echo form_fieldset_close () . 

// Begin form footer...........
form_submit ( 'submit', $this->lang->line ( 'app_form_save' ), 'class="submit small"' ) . form_reset ( '', $this->lang->line ( 'app_form_cancel' ), 'onClick="history.go(-1);return true;" class="submit small"' ) . 

// End form footer...........

form_close ();
?></div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>

</div>