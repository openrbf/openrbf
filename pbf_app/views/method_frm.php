<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.min.js"></script>
<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.date_input.pack.js"></script>
<script type="text/javascript">
var existingtarget;

    $(document).ready(function() {
				
        $('.add_row').click(function() {
			  
			  $('#filetypes_table tbody>tr:last').clone(true).insertAfter('#filetypes_table tbody>tr:last');
			  
			  $('#filetypes_table tbody>tr:last').find("td:last").html('<a href="#"><?php echo img('cside/images/icons/delete.png');?></a>');
			  
			  $('#filetypes_table tbody>tr:last').find("td:last a").addClass('remove');
			  
			  var rowCount = $('#filetypes_table tbody>tr').length;
			  
			  $('#filetypes_table tbody>tr:last').find('td:first select[name=computation_entity_class_id[]]').attr('id','computation_entity_class_id_'+rowCount);
			  
			  $('#filetypes_table tbody>tr:last').find('td:first select[name=computation_entity_type_id[]]').attr('id','computation_entity_type_id_'+rowCount);
			  
			 // existingtarget = document.getElementById("entity_types");
			  
			  //alert(existingtarget.options.length);
			  
			  $('#computation_entity_type_id_'+rowCount).empty();
			  
			  $('#entity_types option').clone().appendTo('#computation_entity_type_id_'+rowCount);

			  
			  applyCascadingDropdown('computation_entity_class_id_'+rowCount, 'computation_entity_type_id_'+rowCount);
			  
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

function applyCascadingDropdowns() {
    applyCascadingDropdown("computation_entity_class_id", "computation_entity_type_id");  
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

$condition_one_options = array (
		'' => $this->lang->line ( 'app_form_dropdown_select' ),
		'<' => $this->lang->line ( 'option_condition_and' ) . ' ' . $this->lang->line ( 'option_condition_less' ),
		'<=' => $this->lang->line ( 'option_condition_and' ) . ' ' . $this->lang->line ( 'option_condition_less_equal' ),
		'==' => $this->lang->line ( 'option_condition_and' ) . ' ' . $this->lang->line ( 'option_condition_equal' ),
		'>=' => $this->lang->line ( 'option_condition_and' ) . ' ' . $this->lang->line ( 'option_condition_greater_equal' ),
		'>' => $this->lang->line ( 'option_condition_and' ) . ' ' . $this->lang->line ( 'option_condition_greater' ) 
);

$condition_logic_options = array (
		'||' => $this->lang->line ( 'option_condition_or' ),
		'&&' => $this->lang->line ( 'option_condition_and' ) 
);

$include_score_options = array (
		'1' => $this->lang->line ( 'option_condition_yes' ),
		'0' => $this->lang->line ( 'option_condition_no' ) 
);

$condition_calculation_basis = array (
		'' => $this->lang->line ( 'app_form_dropdown_select' ),
		'normal_pbfbusiness' => $this->lang->line ( 'option_condition_normal_pbfbusiness' ),
		'normal_pbfbusiness_bonus' => $this->lang->line ( 'option_condition_normal_pbfbusiness_bonus' ),
		'normal_pbfbusiness_half_subsidies' => $this->lang->line ( 'option_condition_normal_pbfbusiness_half' ),
		'normal_pbfbusiness_bonus_half' => $this->lang->line ( 'option_condition_normal_pbfbusiness_bonus_half' ),
		'perc_available_budget' => $this->lang->line ( 'option_condition_perc_available_budget' ),
		'regional_avg' => $this->lang->line ( 'option_condition_regional_avg' ),
		'national_avg' => $this->lang->line ( 'option_condition_national_avg' ),
		'regional_capitated_amount' => $this->lang->line ( 'option_condition_capitated_amount' ) 
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
		$this->lang->line ( 'option_condition_class_type_group' ),
		$this->lang->line ( 'option_condition_zone_flatamount' ),
		$this->lang->line ( 'option_condition_logic' ),
		$this->lang->line ( 'option_condition_score_condition_one' ),
		$this->lang->line ( 'option_condition_score_condition_two' ),
		$this->lang->line ( 'option_condition_fav_action' ),
		'' 
) );

if (empty ( $methoddetails )) {
	
	$this->table->add_row ( form_cascaded_dropdown ( 'computation_entity_class_id[]', $entity_class, '', 'id="computation_entity_class_id" class="year"' ) . form_cascaded_dropdown ( 'computation_entity_type_id[]', $entity_type, '', 'id="computation_entity_type_id" class="year"' ) . form_cascaded_dropdown ( 'computation_entity_group_id[]', $entity_group, '', 'id="computation_entity_group_id" class="year"' ), 

	form_dropdown ( 'computation_geozone_id[]', array (
			$this->lang->line ( 'app_form_dropdown_select' ) 
	) + $geozones, '', 'class="year"' ) . 

	form_cascaded_dropdown ( 'computation_entity_ass_group_id[]', $entity_group, '', 'id="computation_entity_ass_group_id" class="year"' ), 

	form_dropdown ( 'computation_main_logic[]', $condition_logic_options, '', 'class="year"' ) . form_dropdown ( 'computation_calculation_basis[]', $condition_calculation_basis, '', 'class="year"' ), 

	form_dropdown ( 'computation_score_condition_one[]', $condition_one_options, '', 'class="year"' ) . form_input ( array (
			'name' => 'computation_score_fact_one[]',
			'id' => 'computation_score_fact_one',
			'class' => 'dataentry',
			'value' => set_value ( 'computation_score_fact_one', 0 ) 
	) ), form_dropdown ( 'computation_score_condition_two[]', $condition_one_options, '', 'class="year"' ) . form_input ( array (
			'name' => 'computation_score_fact_two[]',
			'id' => 'computation_score_fact_two',
			'class' => 'dataentry',
			'value' => set_value ( 'computation_score_fact_two', 0 ) 
	) ), form_input ( array (
			'name' => 'fav_action[]',
			'id' => 'fav_action',
			'class' => 'dataentry',
			'value' => set_value ( 'fav_action', 0 ) 
	) ) . form_dropdown ( 'consider_score[]', $include_score_options, 0, 'class="year"' ), '' );
} else {
	foreach ( $methoddetails as $key => $val ) {
		$this->table->add_row ( form_cascaded_dropdown ( 'computation_entity_class_id[]', $entity_class, $methoddetails [$key] ['computation_entity_class_id'], 'id="computation_entity_class_id" class="year"' ) . form_cascaded_dropdown ( 'computation_entity_type_id[]', $entity_type, $methoddetails [$key] ['computation_entity_type_id'], 'id="computation_entity_type_id" class="year"' ) . form_cascaded_dropdown ( 'computation_entity_group_id[]', $entity_group, $methoddetails [$key] ['computation_entity_group_id'], 'id="computation_entity_group_id" class="year"' ), 

		form_dropdown ( 'computation_geozone_id[]', array (
				$this->lang->line ( 'app_form_dropdown_select' ) 
		) + $geozones, $methoddetails [$key] ['computation_geozone_id'], 'class="year"' ) . '<br>Groupe associe' . 

		form_cascaded_dropdown ( 'computation_entity_ass_group_id[]', $entity_group, $methoddetails [$key] ['computation_entity_ass_group_id'], 'id="computation_entity_ass_group_id" class="year"' ), 

		form_dropdown ( 'computation_main_logic[]', $condition_logic_options, $methoddetails [$key] ['computation_main_logic'], 'class="year"' ) . form_dropdown ( 'computation_calculation_basis[]', $condition_calculation_basis, $methoddetails [$key] ['computation_calculation_basis'], 'class="year"' ), 

		form_dropdown ( 'computation_score_condition_one[]', $condition_one_options, $methoddetails [$key] ['computation_score_condition_one'], 'class="year"' ) . form_input ( array (
				'name' => 'computation_score_fact_one[]',
				'id' => 'computation_score_fact_one',
				'class' => 'dataentry',
				'value' => set_value ( 'computation_score_fact_one', $methoddetails [$key] ['computation_score_fact_one'] ) 
		) ), form_dropdown ( 'computation_score_condition_two[]', $condition_one_options, $methoddetails [$key] ['computation_score_condition_two'], 'class="year"' ) . form_input ( array (
				'name' => 'computation_score_fact_two[]',
				'id' => 'computation_score_fact_two',
				'class' => 'dataentry',
				'value' => set_value ( 'computation_score_fact_two', $methoddetails [$key] ['computation_score_fact_two'] ) 
		) ), form_input ( array (
				'name' => 'fav_action[]',
				'id' => 'fav_action',
				'class' => 'dataentry',
				'value' => set_value ( 'fav_action', $methoddetails [$key] ['fav_action'] ) 
		) ) . form_dropdown ( 'consider_score[]', $include_score_options, $methoddetails [$key] ['consider_score'], 'class="year"' ), ($key == 0) ? '' : '<a class="remove" href="#">' . img ( 'cside/images/icons/delete.png' ) . '</a>' );
	}
}

echo form_open_multipart ( 'otheroptions/savemethod' ) . 

form_cascaded_dropdown ( 'entity_types', $entity_type, '', 'id="entity_types" style="display:none;"' ) . 

form_hidden ( array (
		'computation_id' => isset ( $method ['computation_id'] ) ? $method ['computation_id'] : '' 
) ) . 

form_fieldset ( $this->lang->line ( 'option_computation_method_information' ) ) . 

'<p>' . form_label ( $this->lang->line ( 'option_computation_method_description' ) . ':', 'computation_description' ) . 

form_input ( array (
		'name' => 'computation_description',
		'id' => 'computation_description',
		'class' => 'longtext',
		'value' => set_value ( 'computation_description', isset ( $method ['computation_description'] ) ? $method ['computation_description'] : '' ) 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'option_computation_method_type' ) . ':', 'computation_type' ) . 

form_dropdown ( 'computation_type', $computation_type, isset ( $method ['computation_type'] ) ? $method ['computation_type'] : '', '' ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'option_start_date' ) . ':', 'computation_date_start' ) . 

form_input ( array (
		'name' => 'computation_date_start',
		'id' => 'computation_date_start',
		'class' => 'text date_picker',
		'value' => set_value ( 'computation_date_start', isset ( $method ['computation_date_start'] ) ? $method ['computation_date_start'] : date ( 'Y-m-d' ) ) 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'option_end_date' ) . ':', 'computation_date_end' ) . 

form_input ( array (
		'name' => 'computation_date_end',
		'id' => 'computation_date_end',
		'class' => 'text date_picker',
		'value' => set_value ( 'computation_date_end', isset ( $method ['computation_date_end'] ) ? $method ['computation_date_end'] : date ( 'Y-m-d' ) ) 
) ) . '</p>' . 

form_fieldset_close () . 

form_fieldset ( $this->lang->line ( 'option_condition_computation_conditions' ) ) . 

'<a class="add_row" href="#">' . img ( 'cside/images/icons/small_add.png' ) . '</a>' . 

$this->table->generate () . 

'<a class="add_row" href="#">' . img ( 'cside/images/icons/small_add.png' ) . '</a>' . 

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