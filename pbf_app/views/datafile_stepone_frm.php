<script language="javascript">
function load_nextstep(){
	

	

	//var temp_period = document.getElementById('period').value;


	
	//temp_period = temp_period.split('_');

	var temp_period = document.getElementById('period').options[document.getElementById('period').options.selectedIndex].id



	

	if(document.getElementById('entity_id').value=="")
	{
		alert('<?php echo $this->lang->line('datafile_select_entity');?>');
		return;	
		} 
	if(document.getElementById('filetype_id').value=="")
	{
		alert('<?php echo $this->lang->line('datafile_select_filetype');?>');	
		return;
		}
	if(document.getElementById('period').value=="")
	{
		alert('<?php echo $this->lang->line('datafile_select_period');?>');	
		return;
		}
	//if((temp_period[1] >= <?php echo date('n');?>) && (document.getElementById('datafile_year').value >= <?php echo date('Y');?>))
	if((temp_period > <?php echo date('n');?>) && (document.getElementById('datafile_year').value >= <?php echo date('Y');?>))
	 
	{
		alert('<?php echo $this->lang->line('datafile_select_period_error');?>');	
		return;
		}
	if(document.getElementById('datafile_year').value=="")
	{
		alert('<?php echo $this->lang->line('datafile_select_year');?>');	
		return;
	}
	else{
	
	var entity_id = document.getElementById('entity_id').options[document.getElementById('entity_id').options.selectedIndex].id;
	var filetype_id = document.getElementById('filetype_id').options[document.getElementById('filetype_id').options.selectedIndex].id;
//	var period = document.getElementById('period').value;
	var period = document.getElementById('period').options[document.getElementById('period').options.selectedIndex].id;

	var datafile_year = document.getElementById('datafile_year').value;
	
	// process necessary texts
	
	var entity_name=document.getElementById('entity_id').options[document.getElementById('entity_id').options.selectedIndex].text;
	var filetype_name=document.getElementById('filetype_id').options[document.getElementById('filetype_id').options.selectedIndex].text;
	var period_name=document.getElementById('period').options[document.getElementById('period').options.selectedIndex].text;
	
	if(confirm("<?php echo $this->lang->line('datafile_warning_one');?> :\n\n - "+filetype_name.toUpperCase()+"\n\n - <?php echo $this->lang->line('datafile_warning_two');?> "+entity_name.toUpperCase()+"\n\n - <?php echo $this->lang->line('datafile_warning_three');?> "+period_name.toUpperCase()+" "+datafile_year.toUpperCase()+"\n\n\n <?php echo $this->lang->line('datafile_warning_four');?>"))
	{
	document.getElementById("frm_step_one").action='<?php echo base_url();?>datafiles/newfile/0/'+entity_id+'/'+period+'/'+datafile_year+'/'+filetype_id;
	document.getElementById("frm_step_one").submit();
	}
	}
	}
	
function applyCascadingDropdowns() {

	applyCascadingDropdown("entity_geozone_id", "entity_id"); 
	applyCascadingDropdown("entity_id", "filetype_id"); 
//	applyCascadingDropdown("level_0", "filetype_id"); 
	applyCascadingDropdown("filetype_id", "period");    

}
window.onload=applyCascadingDropdowns;
</script>

<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	// prepare a spacial drop down for the entities with id, class, and value for each option...
$current_geozone = "";
$form_entities_dropdown = '<select id="entity_id" name="entity_id">';

foreach ( $entities as $entity ) {
	
	if ($current_geozone != $entity ['entity_geozone_id']) {
		$current_geozone = $entity ['entity_geozone_id'];
		$form_entities_dropdown .= '<option class="' . $entity ['entity_geozone_id'] . '" value="">' . $this->lang->line ( 'app_form_dropdown_select' ) . '</option>';
	}
	
	$form_entities_dropdown .= '<option id="' . $entity ['entity_id'] . '" class="' . $entity ['entity_geozone_id'] . '" value="' . $entity ['entity_type_id'] . '">' . $entity ['entity_name'] . '</option>';
}

$form_entities_dropdown .= '</select>';

echo '<div class="block small left">
			
				<div class="block_head">
					
					<h2>' . $this->lang->line ( 'datafile_step_one' ) . '</h2>	
				</div>		<!-- .block_head ends -->
				<div class="block_content">
				
					' . form_open ( '', 'name="frm_step_one" id="frm_step_one"' ) . '
						
						' . form_cascaded_geozones ( 'entity_geozone_id', true ) . '
						
						<p>
							' . form_label ( $this->lang->line ( 'list_entity' ), 'entity_id' ) . $form_entities_dropdown . '
						</p>
						
						<p>' . form_label ( $this->lang->line ( 'list_report_type' ), 'filetype_id' ) . form_cascaded_dropdown ( 'filetype_id', $filetypes, '', 'id="filetype_id"' ) . '</p>
						
						<p>' . form_label ( $this->lang->line ( 'datafile_period' ), 'period' ) . 

form_cascaded_dropdown ( 'period', $periodics, '', 'id="period" class="month"' ) . 

form_dropdown ( 'datafile_year', $years, '', 'id="datafile_year" class="year" style="width:100px"' ) . '</p>
						
						<p>' . form_button ( 'dataentry', $this->lang->line ( 'app_form_continue' ), 'onClick="load_nextstep()" class="submit small"' ) . '</p>
					' . form_close () . '
					
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
				
			</div>';
