<script language="javascript">
function applyCascadingDropdowns() {
    applyCascadingDropdown("geo_id", "geozone_parentid");    
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

echo form_open_multipart ( 'geo/savezone' ) . 

form_hidden ( array (
		'geozone_id' => isset ( $zone ['geozone_id'] ) ? $zone ['geozone_id'] : '' 
) ) . 

form_fieldset ( $this->lang->line ( 'zone_definition' ) ) . 

'<p>' . form_label ( $this->lang->line ( 'zone_frm_name' ), 'geozone_name' ) . 

form_input ( array (
		'name' => 'geozone_name',
		'id' => 'geozone_name',
		'value' => set_value ( 'geozone_name', isset ( $zone ['geozone_name'] ) ? $zone ['geozone_name'] : '' ) 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'zone_frm_html_map' ), 'geozone_htmlmap' ) . 

form_textarea ( array (
		'name' => 'geozone_htmlmap',
		'id' => 'geozone_htmlmap',
		'rows' => 6,
		'cols' => 40,
		'value' => set_value ( 'geozone_htmlmap', isset ( $zone ['geozone_htmlmap'] ) ? $zone ['geozone_htmlmap'] : '' ) 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'zone_frm_map_image' ), 'geozone_mapath' ) . 

form_upload ( array (
		'name' => 'geozone_mapath',
		'id' => 'geozone_mapath' 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'zone_frm_parent_zone' ), 'geozone_parentid' ) . 

form_cascaded_dropdown ( 'geozone_parentid', $geozone, isset ( $zone ['geozone_parentid'] ) ? $zone ['geozone_parentid'] : '', 'id="geozone_parentid"' ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'geo_frm_bonus' ), 'geozone_equity_bonus' ) . 

form_input ( array (
		'name' => 'geozone_equity_bonus',
		'id' => 'geozone_equity_bonus',
		'style' => 'text-align:right',
		'value' => set_value ( 'geozone_equity_bonus', isset ( $zone ['geozone_equity_bonus'] ) ? $zone ['geozone_equity_bonus'] : '' ) 
) ) . '</p>' . '<p>' . form_label ( $this->lang->line ( 'zone_frm_cactchement_pop' ), 'geozone_pop' ) . 

form_input ( array (
		'name' => 'geozone_pop',
		'id' => 'geozone_pop',
		'style' => 'text-align:right',
		'value' => set_value ( 'geozone_pop', isset ( $zone ['geozone_pop'] ) ? $zone ['geozone_pop'] : '' ) 
) ) . '</p>' . '<p>' . form_label ( $this->lang->line ( 'zone_frm_cactchement_pop_year' ), 'geozone_pop_year' ) . 

form_dropdown ( 'geozone_pop_year', $years, isset ( $zone ['geozone_pop_year'] ) ? $zone ['geozone_pop_year'] : '' ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'zone_frm_active' ), 'geozone_active' ) . 

form_checkbox ( array (
		'name' => 'geozone_active',
		'id' => 'geozone_active',
		'value' => 1,
		'checked' => (isset ( $zone ['geozone_active'] ) && $zone ['geozone_active'] == 1) ? TRUE : FALSE 
) ) . '</p>' . 

form_fieldset_close () . 

// Begin form footer...........
form_submit ( 'submit', $this->lang->line ( 'app_form_save' ), 'class="submit small"' ) . form_reset ( '', $this->lang->line ( 'app_form_cancel' ), 'onClick="history.go(-1);return true;" class="submit small"' ) . 
// End form footer...........

form_close ();

// echo 'PS: show the image near this form';
?></div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>

</div>