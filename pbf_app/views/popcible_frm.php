<script language="javascript">
function applyCascadingDropdowns() {
	applyCascadingDropdown("entity_class_id", "entity_type_id");    
}
window.onload=applyCascadingDropdowns;
</script>
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
echo form_open_multipart ( 'popcible/save_popcible' ) . 

form_hidden ( array (
		'popcible_id' => isset ( $popcible ['popcible_id'] ) ? $popcible ['popcible_id'] : '' 
) ) . 

form_fieldset ( $this->lang->line ( 'popcible_frm_title' ) ) . 

'<p>' . form_label ( $this->lang->line ( 'popcible_frm_name' ), 'popcible_name' ) . 

form_input ( array (
		'name' => 'popcible_name',
		'id' => 'popcible_name',
		'value' => set_value ( 'popcible_name', isset ( $popcible ['popcible_name'] ) ? $popcible ['popcible_name'] : '' ) 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'popcible_frm_pourcentage' ) . ' national', 'popcible_percentage' ) . 

form_input ( array (
		'name' => 'popcible_percentage',
		'id' => 'popcible_percentage',
		'value' => set_value ( 'popcible_percentage', isset ( $popcible ['popcible_percentage'] ) ? $popcible ['popcible_percentage'] : '' ) 
) ) . '</p>';

foreach ( $zones as $zone ) {
	echo form_hidden ( array (
			'popciblezone_id_' . $zone ['geozone_id'] => isset ( $popcible_zone [$zone ['geozone_id']] ['popzone_id'] ) ? $popcible_zone [$zone ['geozone_id']] ['popzone_id'] : '' 
	) ) . form_hidden ( array (
			'zone_id_' . $zone ['geozone_id'] => isset ( $zone ['geozone_id'] ) ? $zone ['geozone_id'] : '' 
	) ) . 

	'<p>' . form_label ( $this->lang->line ( 'popcible_frm_pourcentage' ) . ' ' . $zone ['geozone_name'], $zone ['geozone_id'] ) . 

	form_input ( array (
			'name' => 'pop_cible_percentage_' . $zone ['geozone_id'],
			'id' => 'pop_cible_percentage_' . $zone ['geozone_id'],
			'value' => set_value ( 'pop_cible_percentage_' . $zone ['geozone_id'], isset ( $popcible_zone [$zone ['geozone_id']] ['zone'] ) ? $popcible_zone [$zone ['geozone_id']] ['zone'] : '' ) 
	) ) . '</p>';
}

echo '<p>' . form_label ( $this->lang->line ( 'frm_popcible_published' ), 'popcible_published' ) . 

form_checkbox ( array (
		'name' => 'popcible_published',
		'id' => 'popcible_published',
		'value' => 1,
		'checked' => (isset ( $popcible ['popcible_published'] ) && $popcible ['popcible_published'] == 1) ? TRUE : FALSE 
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