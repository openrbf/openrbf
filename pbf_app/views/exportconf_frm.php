<div class="block">

	<div class="block_head">
					
					<?php
					echo (isset ( $mod_title ) ? $this->pbf->get_mod_title ( $mod_title ) : '');
					?>
					
					
				</div>
	<!-- .block_head ends -->



	<div class="block_content"><?php
	
	echo form_open ( 'exports/saveconf' ) . 

	form_hidden ( array (
			'exports_id' => isset ( $exports ['exports_id'] ) ? $exports ['exports_id'] : '' 
	) ) . 

	form_fieldset ( $this->lang->line ( 'exports_frm_export_definition' ) ) . 

	'<p>' . form_label ( $this->lang->line ( 'exports_frm_export_title' ), 'exports_title' ) . 

	form_input ( array (
			'name' => 'exports_title',
			'id' => 'exports_title',
			'value' => set_value ( 'bank_name', isset ( $exports ['exports_title'] ) ? $exports ['exports_title'] : '' ),
			'class' => 'longtext',
			'maxlength' => '30' 
	) ) . '</p>' . 

	'<p>' . form_label ( $this->lang->line ( 'exports_frm_export_filetype' ), 'filetype_id' ) . 

	form_dropdown ( 'filetype_id', $filetypes, isset ( $exports ['filetype_id'] ) ? $exports ['filetype_id'] : '', 'id="filetype_id" class="longtext"' ) . '</p>' . 

	'<p>' . form_label ( $this->lang->line ( 'exports_frm_export_datatype' ), 'datatype' ) . 

	form_dropdown ( 'datatype', $datatype, isset ( $exports ['datatype'] ) ? $exports ['datatype'] : '', 'id="datatype"' ) . '</p>' . 

	form_cascaded_geozones ( 'entity_geozone_id', true ) . 

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