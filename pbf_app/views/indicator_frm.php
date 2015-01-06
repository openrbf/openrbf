<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.min.js"></script>
<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.date_input.pack.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.add').click(function() {
			  
			  $('#filetypes_table tbody>tr.filetype:last').clone(true).insertAfter('#filetypes_table tbody>tr.filetype:last');
			  
			  $('#filetypes_table tbody>tr.filetype:last').find("td:last").html('<a href="#"><?php echo img('cside/images/icons/delete.png');?></a>');
			  
			  $('#filetypes_table tbody>tr.filetype:last').find("td:last a").addClass('remove');
			  
			  return false;
			  
        });	
		
		$('.remove').live('click', function() {
			
			$(this).parent().parent().remove();
	
			return false;
		
		});
	$('input.date_picker').date_input();
			
    });

$.extend(DateInput.DEFAULT_OPTS, {
  stringToDate: function(string) {
    var matches;
    if (matches = string.match(/^(\d{4,4})-(\d{2,2})-(\d{2,2})$/)) {
      return new Date(matches[1], matches[2] - 1, matches[3]);
    } else {
      return null;
    };
  },

  dateToString: function(date) {
    var month = (date.getMonth() + 1).toString();
    var dom = date.getDate().toString();
    if (month.length == 1) month = "0" + month;
    if (dom.length == 1) dom = "0" + dom;
    return date.getFullYear() + "-" + month + "-" + dom;
  },
  
 month_names: ["<?php echo $this->lang->line('app_month_1');?>", "<?php echo $this->lang->line('app_month_2');?>", "<?php echo $this->lang->line('app_month_3');?>", "<?php echo $this->lang->line('app_month_4');?>", "<?php echo $this->lang->line('app_month_5');?>", "<?php echo $this->lang->line('app_month_6');?>", "<?php echo $this->lang->line('app_month_7');?>", "<?php echo $this->lang->line('app_month_8');?>", "<?php echo $this->lang->line('app_month_9');?>", "<?php echo $this->lang->line('app_month_10');?>", "<?php echo $this->lang->line('app_month_11');?>", "<?php echo $this->lang->line('app_month_12');?>"],
 short_month_names: ["<?php echo $this->lang->line('app_month_1_short');?>", "<?php echo $this->lang->line('app_month_2_short');?>", "<?php echo $this->lang->line('app_month_3_short');?>", "<?php echo $this->lang->line('app_month_4_short');?>", "<?php echo $this->lang->line('app_month_5_short');?>", "<?php echo $this->lang->line('app_month_6_short');?>", "<?php echo $this->lang->line('app_month_7_short');?>", "<?php echo $this->lang->line('app_month_8_short');?>", "<?php echo $this->lang->line('app_month_9_short');?>", "<?php echo $this->lang->line('app_month_10_short');?>", "<?php echo $this->lang->line('app_month_11_short');?>", "<?php echo $this->lang->line('app_month_12_short');?>"],
 short_day_names: ["<?php echo $this->lang->line('app_day_1_short');?>", "<?php echo $this->lang->line('app_day_2_short');?>", "<?php echo $this->lang->line('app_day_3_short');?>", "<?php echo $this->lang->line('app_day_4_short');?>", "<?php echo $this->lang->line('app_day_5_short');?>", "<?php echo $this->lang->line('app_day_6_short');?>", "<?php echo $this->lang->line('app_day_7_short');?>"]
});
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
$controls = '';

$yes_no_arr = array (
		'1' => $this->lang->line ( 'frm_indicator_linked_filetypes_quality_yes' ),
		'0' => $this->lang->line ( 'frm_indicator_linked_filetypes_quality_no' ) 
);

$tmpl = array (
		'table_open' => '<table border="0" cellpadding="0" cellspacing="0" id="filetypes_table" width="500">',
		'heading_row_start' => '<tr>',
		'heading_row_end' => '</tr>',
		'heading_cell_start' => '<th>',
		'heading_cell_end' => '</th>',
		'row_start' => '<tr class="filetype">',
		'row_alt_start' => '<tr class="filetype">',
		'row_end' => '</tr>',
		'cell_start' => '<td>',
		'cell_end' => '</td>',
		'table_close' => '</table>' 
);

$this->table->set_template ( $tmpl );

$this->table->set_heading ( array (
		$this->lang->line ( 'frm_indicator_linked_filetypes_name' ) . ' / ' . $this->lang->line ( 'frm_indicator_linked_filetypes_category' ),
		$this->lang->line ( 'frm_indicator_linked_filetypes_quality_associated' ),
		$this->lang->line ( 'frm_indicator_linked_filetypes_sortorder' ),
		$this->lang->line ( 'frm_indicator_linked_filetypesdefault_tarif' ),
		'Bonus',
		$this->lang->line ( 'frm_indicator_target' ) . ' rel',
		$this->lang->line ( 'frm_indicator_target' ) . ' abs',
		$this->lang->line ( 'frm_indicator_linked_filetypes_from' ),
		$this->lang->line ( 'frm_indicator_linked_filetypes_to' ),
		'' 
) );

if (empty ( $indicatorsfileypes )) {
	
	$this->table->add_row ( form_dropdown ( 'filetype_id[]', $filetypes, isset ( $indicator ['filetype_id'] ) ? $indicator ['filetype_id'] : '' ) . '<br>' . form_dropdown ( 'indicator_category_id[]', $indicator_category, isset ( $indicator ['indicator_category_id'] ) ? $indicator ['indicator_category_id'] : '', 'class="year"' ), form_dropdown ( 'quality_associated[]', $yes_no_arr, isset ( $indicator ['quality_associated'] ) ? $indicator ['quality_associated'] : '1', 'class="year"' ), form_input ( array (
			'name' => 'order[]',
			'id' => 'order',
			'class' => 'dataentry_small',
			'value' => set_value ( 'order', isset ( $indicator ['order'] ) ? $indicator ['order'] : '' ) 
	) ), form_input ( array (
			'name' => 'default_tarif[]',
			'id' => 'default_tarif',
			'class' => 'dataentry_small',
			'value' => set_value ( 'default_tarif', isset ( $indicator ['default_tarif'] ) ? $indicator ['default_tarif'] : '' ) 
	) ), form_input ( array (
			'name' => 'bonus_indigent[]',
			'id' => 'bonus_indigent',
			'class' => 'dataentry_small',
			'value' => set_value ( 'bonus_indigent', isset ( $indicator ['bonus_indigent'] ) ? $indicator ['bonus_indigent'] : '' ) 
	) ), form_input ( array (
			'name' => 'dataelts_target_rel[]',
			'id' => 'dataelts_target_rel',
			'class' => 'dataentry_small',
			'value' => set_value ( 'dataelts_target_rel', isset ( $indicator ['dataelts_target_rel'] ) ? $indicator ['dataelts_target_rel'] : '' ) 
	) ), form_input ( array (
			'name' => 'dataelts_target_abs[]',
			'id' => 'dataelts_target_abs',
			'class' => 'dataentry_small',
			'value' => set_value ( 'dataelts_target_abs', isset ( $indicator ['dataelts_target_abs'] ) ? $indicator ['dataelts_target_abs'] : '' ) 
	) ), form_input ( array (
			'name' => 'use_from[]',
			'id' => 'use_from',
			'class' => 'text date_picker',
			'value' => set_value ( 'use_from', isset ( $indicator ['use_from'] ) ? $indicator ['use_from'] : '' ) 
	) ), form_input ( array (
			'name' => 'use_to[]',
			'id' => 'use_to',
			'class' => 'text date_picker',
			'value' => set_value ( 'use_to', isset ( $indicator ['use_to'] ) ? $indicator ['use_to'] : '' ) 
	) ), '' );
} else {
	foreach ( $indicatorsfileypes as $key => $val ) {
		$this->table->add_row ( form_dropdown ( 'filetype_id[]', $filetypes, $indicatorsfileypes [$key] ['filetype_id'] ) . '<br>' . form_dropdown ( 'indicator_category_id[]', $indicator_category, $indicatorsfileypes [$key] ['indicator_category_id'], 'class="year"' ), form_dropdown ( 'quality_associated[]', $yes_no_arr, $indicatorsfileypes [$key] ['quality_associated'], 'class="year"' ), form_input ( array (
				'name' => 'order[]',
				'id' => 'order',
				'class' => 'dataentry_small',
				'value' => set_value ( 'order', $indicatorsfileypes [$key] ['order'] ) 
		) ), form_input ( array (
				'name' => 'default_tarif[]',
				'id' => 'default_tarif',
				'class' => 'dataentry_small',
				'value' => set_value ( 'default_tarif', $indicatorsfileypes [$key] ['default_tarif'] ) 
		) ), 

		form_input ( array (
				'name' => 'bonus_indigent[]',
				'id' => 'bonus_indigent',
				'class' => 'dataentry_small',
				'value' => set_value ( 'bonus_indigent', $indicatorsfileypes [$key] ['bonus_indigent'] ) 
		) ), 

		form_input ( array (
				'name' => 'dataelts_target_rel[]',
				'id' => 'dataelts_target_rel',
				'class' => 'dataentry_small',
				'value' => set_value ( 'dataelts_target_rel', $indicatorsfileypes [$key] ['dataelts_target_rel'] ) 
		) ), form_input ( array (
				'name' => 'dataelts_target_abs[]',
				'id' => 'dataelts_target_abs',
				'class' => 'dataentry_small',
				'value' => set_value ( 'dataelts_target_abs', $indicatorsfileypes [$key] ['dataelts_target_abs'] ) 
		) ), form_input ( array (
				'name' => 'use_from[]',
				'id' => 'use_from',
				'class' => 'text date_picker',
				'value' => set_value ( 'use_from', $indicatorsfileypes [$key] ['use_from'] ) 
		) ), form_input ( array (
				'name' => 'use_to[]',
				'id' => 'use_to',
				'class' => 'text date_picker',
				'value' => set_value ( 'use_to', $indicatorsfileypes [$key] ['use_to'] ) 
		) ), ($key == 0) ? '' : '<a class="remove" href="#">' . img ( 'cside/images/icons/delete.png' ) . '</a>' );
	}
}

echo form_open_multipart ( 'indicators/save' ) . 

form_hidden ( array (
		'indicator_id' => isset ( $indicator ['indicator_id'] ) ? $indicator ['indicator_id'] : '' 
) ) . 

form_fieldset ( $this->lang->line ( 'frm_indicator_definition_label' ) ) . 

'<p>' . form_label ( $this->lang->line ( 'frm_indicator_title' ), 'indicator_title' ) . 

form_input ( array (
		'name' => 'indicator_title',
		'id' => 'indicator_title',
		'class' => 'longtext',
		'value' => set_value ( 'indicator_title', isset ( $indicator ['indicator_title'] ) ? $indicator ['indicator_title'] : '' ) 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_indicator_abbrev' ), 'indicator_abbrev' ) . 

form_input ( array (
		'name' => 'indicator_abbrev',
		'id' => 'indicator_abbrev',
		'value' => set_value ( 'indicator_abbrev', isset ( $indicator ['indicator_abbrev'] ) ? $indicator ['indicator_abbrev'] : '' ) 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_indicator_common_name' ), 'indicator_common_name' ) . 

form_input ( array (
		'name' => 'indicator_common_name',
		'id' => 'indicator_common_name',
		'class' => 'longtext',
		'value' => set_value ( 'indicator_common_name', isset ( $indicator ['indicator_common_name'] ) ? $indicator ['indicator_common_name'] : '' ) 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_indicator_description' ), 'indicator_description' ) . 

form_textarea ( array (
		'name' => 'indicator_description',
		'id' => 'indicator_description',
		'rows' => 6,
		'cols' => 40,
		'value' => set_value ( 'indicator_description', isset ( $indicator ['indicator_description'] ) ? $indicator ['indicator_description'] : '' ) 
) ) . form_wysiwyg ( $ckeditor ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_indicator_units' ), 'indicator_units' ) . 

form_dropdown ( 'indicator_units', $indicator_units, isset ( $indicator ['indicator_units'] ) ? $indicator ['indicator_units'] : '' ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_indicator_datatype' ), 'indicator_vartype' ) . 

form_dropdown ( 'indicator_vartype', $indicator_vartype, isset ( $indicator ['indicator_vartype'] ) ? $indicator ['indicator_vartype'] : '' ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_indicator_featured' ), 'indicator_featured' ) . 

form_checkbox ( array (
		'name' => 'indicator_featured',
		'id' => 'indicator_featured',
		'value' => 1,
		'checked' => (isset ( $indicator ['indicator_featured'] ) && $indicator ['indicator_featured'] == 1) ? TRUE : FALSE 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_use_indigence_bonus' ), 'indicator_use_indigence_bonus' ) . 

form_checkbox ( array (
		'name' => 'indicator_use_indigence_bonus',
		'id' => 'indicator_use_indigence_bonus',
		'value' => 1,
		'checked' => (isset ( $indicator ['indicator_use_indigence_bonus'] ) && $indicator ['indicator_use_indigence_bonus'] == 1) ? TRUE : FALSE 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_editable_tarif' ), 'indicator_editable_tarif' ) . 

form_checkbox ( array (
		'name' => 'indicator_editable_tarif',
		'id' => 'indicator_editable_tarif',
		'value' => 1,
		'checked' => (isset ( $indicator ['indicator_editable_tarif'] ) && $indicator ['indicator_editable_tarif'] == 1) ? TRUE : FALSE 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_realtime_result' ), 'realtime_result' ) . 

form_checkbox ( array (
		'name' => 'indicator_realtime_result',
		'id' => 'indicator_realtime_result',
		'value' => 1,
		'checked' => (isset ( $indicator ['indicator_realtime_result'] ) && $indicator ['indicator_realtime_result'] == 1) ? TRUE : FALSE 
) ) . '</p>' . '<p>' . form_label ( $this->lang->line ( 'frm_use_coverage' ), 'indicator_use_coverage' ) . 

form_checkbox ( array (
		'name' => 'indicator_use_coverage',
		'id' => 'indicator_use_coverage',
		'value' => 1,
		'checked' => (isset ( $indicator ['indicator_use_coverage'] ) && $indicator ['indicator_use_coverage'] == 1) ? TRUE : FALSE 
) ) . '</p>' . '<p>' . form_label ( $this->lang->line ( 'frm_popcible' ), 'indicator_popcible' ) . 

form_dropdown ( 'indicator_popcible', $indicator_popcible, isset ( $indicator ['indicator_popcible'] ) ? $indicator ['indicator_popcible'] : '' ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_indicator_icon_file' ), 'indicator_icon_label' ) . form_upload ( array (
		'name' => 'indicator_icon_file',
		'id' => 'indicator_icon_file' 
) ) . 
// (isset($indicator['indicator_icon_file']) && ($indicator['indicator_icon_file']==''))?'':anchor_popup(site_url('cside/images/portal)'.$indicator['indicator_icon_file'], $this->lang->line('frm_entity_contract_open'), array()).' | '.anchor('#',$this->lang->line('frm_entity_contract_delete')))
$controls .= ((isset ( $indicator ['indicator_icon_file'] ) && ($indicator ['indicator_icon_file'] == '')) ? '' : '&nbsp;&nbsp;&nbsp' . anchor_popup ( site_url () . 'cside/images/portal/' . $indicator ['indicator_icon_file'], $this->lang->line ( 'frm_entity_contract_open' ), array () ) . ' | ' . anchor ( '', $this->lang->line ( 'frm_entity_contract_delete' ) )) . 

'</p>' . 

form_fieldset_close () . 

form_fieldset ( $this->lang->line ( 'frm_indicator_linked_filetypes' ) ) . 

'<a class="add" href="#">' . img ( 'cside/images/icons/small_add.png' ) . '</a>' . 

$this->table->generate () . 

'<a class="add" href="#">' . img ( 'cside/images/icons/small_add.png' ) . '</a>' . 

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