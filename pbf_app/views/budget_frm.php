<script language="javascript">
function applyCascadingDropdowns() {
	applyCascadingDropdown("entity_geozone_id", "entity_id"); 
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
				
				
	<?php
	
	echo 

	'<div class="block_content">' . 

	(isset ( $tab_menus ) ? '<p class="breadcrumb">' . $tab_menus . '</p>' : '')?>

		
		
	
				
<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

echo form_open_multipart ( 'budgets/save_budget/' ) . 

form_hidden ( array (
		'budget_id' => isset ( $budget ['budget_id'] ) ? $budget ['budget_id'] : '' 
) ) . form_hidden ( array (
		'periodic' => $periodic 
) ) . form_fieldset ( $this->lang->line ( 'budget_frm_title' ) ) . 

'<p>' . form_label ( $this->lang->line ( 'budget_frm_budget_fosa' ), 'budget_value' ) . form_cascaded_geozones_entities_filter ( 'entity_geozone_id', true ) . '</p>
	<p>' . form_label ( $this->lang->line ( 'budget_frm_budget_value' ), 'budget_value' ) . 

form_input ( array (
		'name' => 'budget_value',
		'id' => 'budget_value',
		'value' => set_value ( 'budget_value', isset ( $budget ['budget_value'] ) ? $budget ['budget_value'] : '' ) 
) ) . '</p>' . 

form_fieldset_close () . 

form_fieldset ( $this->lang->line ( 'budget_frm_budget_period' ) );
?>
	<?php
	if ($periodic == 'mensuel') {
		echo '<p>' . form_label ( 'Month:', 'budget_month' ) . 

		form_dropdown ( 'budget_month', $months, isset ( $budget ['budget_month'] ) ? $budget ['budget_month'] : '', 'id="budget_month"' ) . '</p>';
	}
	
	/*
	 * '<p>'.form_label($this->lang->line('budget_frm_budget_month'), 'budget_month'). form_dropdown('budget_quarter', $quarters ,isset($budget['budget_quarter'])?$budget['budget_quarter']:'', 'id="budget_quarter"').'</p>'.
	 */
	echo 

	'<p>' . form_label ( $this->lang->line ( 'budget_frm_budget_year' ), 'budget_year' ) . 

	form_dropdown ( 'budget_year', $years, isset ( $budget ['budget_year'] ) ? $budget ['budget_year'] : '', 'id="budget_year" class="year"' ) . '</p>' . 

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