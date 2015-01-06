<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.min.js"></script>
<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.date_input.pack.js"></script>
<script language="javascript">
function applyCascadingDropdowns() {
    applyCascadingDropdown("entity_bank_hq_id", "entity_bank_id");   
}

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
	
	// ----temp historique des parametres ---->

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
		$this->lang->line ( 'population' ),
		$this->lang->line ( 'popyear' ),
		$this->lang->line ( 'type' ),
		$this->lang->line ( 'group' ),
		$this->lang->line ( 'active' ),
		$this->lang->line ( 'from' ),
		$this->lang->line ( 'to' ) 
) );

if (empty ( $entitytime )) {
	
	$this->table->add_row ( '<p>' . form_input ( array (
			'name' => 'entity_pop_time[]',
			'id' => 'entity_pop_time',
			'value' => set_value ( 'entity_pop_time', '' ) 
	) ) . '</p>', '<p>' . form_input ( array (
			'name' => 'entity_pop_year_time[]',
			'id' => 'entity_pop_year_time',
			'value' => set_value ( 'entity_pop_year_time', '' ) 
	) ) . '</p>', '<p>' . form_dropdown ( 'entity_type_time[]', array (
			null => $this->lang->line ( 'app_form_dropdown_select' ) 
	) + $this->pbf->get_entity_types_by_class ( $entity ['entity_class'] ), $entitytime [$key] ['entity_pbf_type'] ) . '</p>', 

	'<p>' . form_dropdown ( 'entity_pbf_group_id_time[]', array (
			'' => $this->lang->line ( 'app_form_dropdown_select' ) 
	) + $this->pbf->get_entity_groups (), '' ) . '</p>', '<p>' . form_dropdown ( 'entity_active_time[]', array (
			'' => $this->lang->line ( 'app_form_dropdown_select' ) 
	) + array (
			0 => 'not active',
			1 => 'active' 
	), '' ) . '</p>', 
			// form_checkbox(array( 'name' =>'entity_active_time[]','id' => 'entity_active_time', 'value' => 0, 'checked' => FALSE )),
			
			form_input ( array (
					'name' => 'use_from[]',
					'id' => 'use_from',
					'class' => 'text date_picker',
					'value' => set_value ( 'use_from', '' ) 
			) ), form_input ( array (
					'name' => 'use_to[]',
					'id' => 'use_to',
					'class' => 'text date_picker',
					'value' => set_value ( 'use_to', '' ) 
			) ), '' );
} else {
	
	foreach ( $entitytime as $key => $val ) {
		
		$this->table->add_row ( '<p>' . form_input ( array (
				'name' => 'entity_pop_time[]',
				'id' => 'entity_pop_time',
				'value' => set_value ( 'entity_pop_time', $entitytime [$key] ['entity_pop'] ),
				'style' => 'width:50%' 
		) ) . '</p>', '<p>' . form_input ( array (
				'name' => 'entity_pop_year_time[]',
				'id' => 'entity_pop_year_time',
				'value' => set_value ( 'entity_pop_year_time', $entitytime [$key] ['entity_pop_year'] ),
				'style' => 'width:50%' 
		) ) . '</p>', 

		'<p>' . form_dropdown ( 'entity_type_time[]', array (
				null => $this->lang->line ( 'app_form_dropdown_select' ) 
		) + $this->pbf->get_entity_types_by_class ( $entity ['entity_class'] ), $entitytime [$key] ['entity_type'] ) . '</p>', 

		'<p>' . form_dropdown ( 'entity_pbf_group_id_time[]', array (
				null => $this->lang->line ( 'app_form_dropdown_select' ) 
		) + $this->pbf->get_entity_groups (), $entitytime [$key] ['entity_pbf_group_id'] ) . '</p>', '<p>' . form_dropdown ( 'entity_active_time[]', array (
				null => $this->lang->line ( 'app_form_dropdown_select' ) 
		) + array (
				0 => 'not active',
				1 => 'active' 
		), $entitytime [$key] ['entity_active'] ) . '</p>', 
				// form_checkbox(array( 'name' =>'entity_active_time[]','id' => 'entity_active_time','value' => $entitytime[$key]['entity_active'], 'checked' => ($entitytime[$key]['entity_active']==1)?TRUE:FALSE )),
				
				form_input ( array (
						'name' => 'use_from[]',
						'id' => 'use_from',
						'class' => 'text date_picker',
						'value' => set_value ( 'use_from', isset ( $entitytime [$key] ['use_from'] ) ? $entitytime [$key] ['use_from'] : '' ) 
				) ), form_input ( array (
						'name' => 'use_to[]',
						'id' => 'use_to',
						'class' => 'text date_picker',
						'value' => set_value ( 'use_to', isset ( $entitytime [$key] ['use_to'] ) ? $entitytime [$key] ['use_to'] : '' ) 
				) ), '' );
	}
}

// --
// >----temp historique des parametres ----

echo form_open_multipart ( 'hfrentities/saveentity', 'onsubmit="return controlFormEntity(this)"' ) . '<p><label><div><img src="' . $this->config->item ( 'base_url' ) . 'cside/images/warning.jpg" style="width:30px;heigth:30px"/>' . $this->lang->line ( "entity_control_form" ) . '</div></label></p>' . $entity_web_form . 

form_fieldset ( 'Parameters changing with time' ) . 

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




