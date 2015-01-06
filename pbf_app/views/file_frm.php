<script language="JavaScript" type="text/javascript">
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

if (isset ( $file ['filetypegeozone'] )) {
	foreach ( $file ['filetypegeozone'] as $zone ) {
		$selectedzones [] = $zone ['geozone_id'];
	}
}

echo form_open ( 'files/save', array (
		'name' => 'filetypeform',
		'id' => 'filetypeform' 
) ) . 

form_hidden ( array (
		'filetype_id' => isset ( $file ['filetype_id'] ) ? $file ['filetype_id'] : '' 
) ) . 

form_fieldset ( $this->lang->line ( 'frm_filetypes_definition' ) ) . 

'<p>' . form_label ( $this->lang->line ( 'frm_filetypes_title' ), 'filetype_name' ) . 

form_input ( array (
		'name' => 'filetype_name',
		'id' => 'filetype_name',
		'class' => 'longtext',
		'value' => set_value ( 'filetype_name', isset ( $file ['filetype_name'] ) ? $file ['filetype_name'] : '' ) 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_filetypes_content_type' ), 'filetype_contenttype' ) . 

form_dropdown ( 'filetype_contenttype', $content_type, isset ( $file ['filetype_contenttype'] ) ? $file ['filetype_contenttype'] : '' ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_filetypes_frequency' ), 'filetype_frequency' ) . 

form_dropdown ( 'filetype_frequency', $frequency, isset ( $file ['filetype_frequency'] ) ? $file ['filetype_frequency'] : '' ) . '</p>' . 

'<p>' . form_label ( 'Function helper:', 'filetype_template', $label_attr ) . 

form_dropdown ( 'filetype_template', $helpers, isset ( $file ['filetype_template'] ) ? $file ['filetype_template'] : '' ) . '<p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_filetypes_active' ), 'filetype_active' ) . 

form_checkbox ( array (
		'name' => 'filetype_active',
		'id' => 'filetype_active',
		'value' => 1,
		'checked' => (isset ( $file ['filetype_active'] ) && $file ['filetype_active'] == 1) ? TRUE : FALSE 
) ) . '</p>' . 

'<p>' . form_label ( 'Dashboard active', 'dashboard_active' ) . 

form_checkbox ( array (
		'name' => 'dashboard_active',
		'id' => 'dashboard_active',
		'value' => 1,
		'checked' => (isset ( $file ['dashboard_active'] ) && $file ['dashboard_active'] == 1) ? TRUE : FALSE 
) ) . '</p>' . 

form_fieldset_close () . 

form_fieldset ( $this->lang->line ( 'frm_filetypes_affiliated_entities' ) ) . 

'<p>' . form_label ( $this->lang->line ( 'frm_filetypes_entity_class' ), 'entity_class_id' ) . 

form_cascaded_dropdown ( 'entity_class_id', $entity_class, isset ( $file ['entity_class_id'] ) ? $file ['entity_class_id'] : '', 'id="entity_class_id"' ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_filetypes_entity_type' ), 'entity_type_id' ) . 

form_cascaded_multiselect ( 'entity_type_id[]', $entity_type, isset ( $file ['entity_type_id'] ) ? $file ['entity_type_id'] : '', 'id="entity_type_id"' ) . '</p>' . 

form_fieldset_close () . 

form_fieldset ( 'Groupes utlisateurs ayant access a ce type de fichier' ) . 

'<p>' . form_label ( $this->lang->line ( 'frm_usergroup_label' ), 'usergroup_id' ) . 

form_cascaded_multiselect ( 'usergroup_id[]', $usergroup_id, isset ( $file ['usergroup_id'] ) ? $file ['usergroup_id'] : '', 'id="usergroup_id"' ) . '</p>' . 

form_fieldset_close () . 

// sdl--

form_fieldset ( $this->lang->line ( 'frm_filetypes_affiliated_geozones' ) ) . 

'<p>' . form_label ( $this->lang->line ( 'frm_filetypes_affiliated_geozones' ), 'geozone_id' ) . 

form_multiselect ( 'filetype_geozone[]', $geozones, isset ( $file ['filetypegeozone'] ) ? $selectedzones : '' ) . '</p>' . 

// form_multiselect('filetype_geozone[]', $geozones,isset($file['filetypegeozone'])?$file['filetypegeozone']:'').'</p>'.

form_fieldset_close () . 

// Begin form footer...........
form_submit ( 'submit', $this->lang->line ( 'app_form_save' ), 'onClick="return check_form();" class="submit small"' ) . form_reset ( '', $this->lang->line ( 'app_form_cancel' ), 'onClick="history.go(-1);return true;" class="submit small"' ) . 
// End form footer...........

form_close ();
?></div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>

</div>