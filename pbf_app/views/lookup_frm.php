<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.editable-select.js"></script>
<link rel="stylesheet"
	href="<?php echo $this->config->item('base_url');?>cside/css/jquery.editable-select.css">
<div class="block">

	<div class="block_head">
					
					<?php
					echo (isset ( $mod_title ) ? $this->pbf->get_mod_title ( $mod_title ) : '');
					?>
					
					
				</div>
	<!-- .block_head ends -->



	<div class="block_content"><?php
	
	echo form_open ( 'otheroptions/savelookup' ) . 

	form_hidden ( array (
			'lookup_id' => isset ( $lookup ['lookup_id'] ) ? $lookup ['lookup_id'] : '' 
	) ) . 

	form_fieldset ( $this->lang->line ( 'option_lookups_definition' ) ) . 

	'<p>' . form_label ( $this->lang->line ( 'option_lookup_title' ) . ':', 'lookup_title' ) . 

	form_input ( array (
			'name' => 'lookup_title',
			'id' => 'lookup_title',
			'value' => set_value ( 'lookup_title', isset ( $lookup ['lookup_title'] ) ? $this->lang->line ( 'option_lkp_ky_' . $lookup ['lookup_id'] ) : '' ) 
	) ) . '</p>' . 

	'<p>' . form_label ( $this->lang->line ( 'option_lookup_abbrev' ) . ':', 'lookup_title_abbrev' ) . 

	form_input ( array (
			'name' => 'lookup_title_abbrev',
			'id' => 'lookup_title_abbrev',
			'value' => set_value ( 'lookup_title_abbrev', isset ( $lookup ['lookup_title_abbrev'] ) ? $this->lang->line ( 'option_lkp_abbr_ky_' . $lookup ['lookup_id'] ) : '' ) 
	) ) . '</p>' . 

	'<p>' . form_label ( $this->lang->line ( 'option_lookup_linkfile' ) . ':', 'lookup_linkfile' ) . 

	form_dropdown ( 'lookup_linkfile', $links, isset ( $lookup ['lookup_linkfile'] ) ? $lookup ['lookup_linkfile'] : '', 'id="lookup_linkfile" class="editable-select"' ) . '</p>' . 

	'<p>' . form_label ( $this->lang->line ( 'option_lookup_order' ) . ':', 'lookup_order' ) . 

	form_input ( array (
			'name' => 'lookup_order',
			'id' => 'lookup_order',
			'class' => 'dataentry',
			'value' => set_value ( 'lookup_order', isset ( $lookup ['lookup_order'] ) ? $lookup ['lookup_order'] : '' ) 
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
<script language="javascript">
$(function() {
  $('#lookup_linkfile').editableSelect(
    {
     bg_iframe: true,
     case_sensitive: false, // If set to true, the user has to type in an exact
     items_then_scroll: 10 // If there are more than 10 items, display a scrollbar
    }
  );
});
</script>