<?php
$report_id = isset ( $report ['report_id'] ) ? $report ['report_id'] : '';
$js = '';
?>
<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.sqlbuilder.js"></script>

<script type="text/javascript">

$(document).ready(function() { 

    $('#report_districts').multiselect({
        noneSelectedText : "Select districts"
    });
    
	$('.add').click(function() {
			
				var timestamp = new Date().getTime();
			  
			  $('#filetypes_table tbody>tr.filetype:last').clone(true).insertAfter('#filetypes_table tbody>tr.filetype:last');
			  
			  $('#filetypes_table tbody>tr.filetype:last').find("td:last").html('<a href="#"><?php echo img('cside/images/icons/delete.png');?></a>');
			  
			  $('#filetypes_table tbody>tr.filetype:last').find("td:last a").addClass('remove');
			  
			  $('#filetypes_table tbody>tr.filetype:last').find("#use_from").attr('id', 'use_from'+timestamp);
			  
			  $('#filetypes_table tbody>tr.filetype:last').find("#use_to").attr('id', 'use_to'+timestamp);
			  
			  return false;
			  
        });	
		
		$('.remove').live('click', function() {
			
			$(this).parent().parent().remove();
	
			return false;
		
		}); 

	
	$('#submitconfig').click(function(){

		$('input[name=report_content_sql]').val($('.sqlbuild').getSQBClause('all')); 
	 	$('input[name=report_content_json]').val($('.sqldata').html()); 
		
	});
	
/* 	$('#report_filetype').dblclick(function(){
		$(this).attr('selectedIndex', '-1').children("option:selected").removeAttr("selected");
	}); */
    
});


</script>

<link rel="stylesheet" type="text/css"
	href="<?php echo $this->config->item('base_url');?>cside/css/jquery.sqlbuilder.css" />

<div class="block">

	<div class="block_head">
					
					<?php
					echo (isset ( $mod_title ) ? $this->pbf->get_mod_title ( $mod_title ) : '');
					?>
									
				</div>
	<!-- .block_head ends -->
	<div class="block_content">
				
<?php

$report_signatories = json_decode ( isset ( $report ['report_signatories'] ) ? $report ['report_signatories'] : '["","",""]', true );

$tmpl = array (
		'table_open' => '<table border="0" cellpadding="0" cellspacing="0" id="filetypes_table" width="500" class="filters">',
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

$this->table->add_row ( form_textarea ( array (
		'name' => 'report_signatories[]',
		'rows' => 5,
		'cols' => 10,
		'value' => $report_signatories [0] 
) ), form_textarea ( array (
		'name' => 'report_signatories[]',
		'rows' => 5,
		'cols' => 10,
		'value' => $report_signatories [1] 
) ), form_textarea ( array (
		'name' => 'report_signatories[]',
		'rows' => 5,
		'cols' => 10,
		'value' => $report_signatories [2] 
) ) );

echo form_open ( 'otheroptions/saveconfig' ) . 

form_hidden ( array (
		'report_id' => $report_id 
) ) . form_hidden ( array (
		'report_type' => $report ['report_type'] 
) );

if ($report ['report_type'] != 'predefined') {
	
	form_hidden ( array (
			'report_content_sql' => '' 
	) ) . form_hidden ( array (
			'report_content_json' => '' 
	) );
	
	$js = ' id="submitconfig"';
	
	form_fieldset ( 'Report body content' );
	?>
    
    <div id=sqlreport>
			<table border=0>
				<tr>
					<td><div class="sqlbuild"></div></td>
				</tr>
			</table>
		</div>

		<p id=3001></p>
		<br>
		<p id=3000></p>
		<p id=3001></p>
		<br>
		<p id=3009></p>
		<p id=4000></p>
     

<?php
	
	echo 

	'<p>' . form_label ( 'Parametres:', 'report_params' ) . 

	form_multiselect ( 'report_params[]', array (
			'entity_geozone_id' => 'zone',
			'ctrl_entity_geozone_id' => 'control zone',
			'entity_id' => 'entity',
			'datafile_month' => 'period_month',
			'datafile_quarter' => 'period_quarter',
			'datafile_year' => 'period_year' 
	), isset ( $report ['report_params'] ) ? json_decode ( $report ['report_params'], true ) : '', 'id="report_params"' ) . '</p>' . 

	'<p>' . form_label ( 'Report media:', 'report_media' ) . 

	form_dropdown ( 'report_media', array (
			'pdf' => 'pdf',
			'xls' => 'xls',
			'graph' => 'graph' 
	), isset ( $report ['report_media'] ) ? $report ['report_media'] : '' ) . '</p>' . 

	form_fieldset_close ();
}

echo form_fieldset ( 'Report page setup' ) . 

'<p>' . form_label ( 'Title', 'report_title' ) . 

form_input ( array (
		'name' => 'report_title',
		'id' => 'report_title',
		'class' => 'longtext',
		'value' => set_value ( 'report_title', isset ( $report ['report_title'] ) ? $report ['report_title'] : '' ) 
) ) . '</p>' . 

'<p>' . form_label ( 'Subtitle:', 'report_subtitle' ) . 

form_input ( array (
		'name' => 'report_subtitle',
		'id' => 'report_subtitle',
		'class' => 'longtext',
		'value' => set_value ( 'report_subtitle', isset ( $report ['report_subtitle'] ) ? $report ['report_subtitle'] : '' ) 
) ) . '</p>' . 

'<p>' . form_label ( 'Logo position:', 'report_logo_position' ) . 

form_dropdown ( 'report_logo_position', array (
		'left' => 'left',
		'center' => 'center',
		'right' => 'right' 
), isset ( $report ['report_logo_position'] ) ? $report ['report_logo_position'] : '' ) . '</p>' . 

'<p>' . form_label ( 'Function helper:', 'report_helper', $label_attr ) . 

form_dropdown ( 'report_helper', $helpers, isset ( $report ['report_helper'] ) ? $report ['report_helper'] : '' ) . '<p>' . 

'<p>' . form_label ( 'Signatories:', 'report_signatories' ) . '</p>' . 

$this->table->generate () . 

'<p>' . form_label ( 'Footer:', 'report_footer' ) . 

form_multiselect ( 'report_footer[]', array (
		'Report title',
		'Application name',
		'Application copyright' 
), isset ( $report ['report_footer'] ) ? $report ['report_footer'] : '', 'id="report_footer"' ) . '</p>' . 

'<p>' . form_label ( 'Page layout:', 'report_page_layout' ) . 

form_dropdown ( 'report_page_layout', array (
		'portrait' => 'portrait',
		'landscape' => 'landscape' 
), isset ( $report ['report_page_layout'] ) ? $report ['report_page_layout'] : '' ) . '</p>' . 

'<p>' . form_label ( 'Districts:', 'report_districts' ) . 

// form_multiselect('report_districts[]', array('North East','South West','East') ,isset($report['report_districts'])?$report['report_districts']:'', 'id="report_d"').'</p>'.
form_dropdown ( 'report_districts[]', $districts, isset ( $report_districts ) ? $report_districts : '', 'id="report_districts" multiple="multiple" class="multi-select-button"' );

// 3rd parameter for set_checkbox can be FALSE or TRUE

if ($user ['usergroup_id'] == '13') {
	$multiple = '';
} else {
	$multiple = 'multiple="multiple"';
}
echo '<p>' . form_label ( 'File type:', 'report_filetype'); 

echo form_dropdown ( 'associated_filetypes[]',$rep_filetypes,isset ( $report_filetypes) ? $report_filetypes : '',
 'id="associated_filetypes" multiple="multiple"') . '</p>' ; 




//============================================================================================================
echo '<p>' . form_label ( 'Report category:', 'report_category' ) . 

form_dropdown ( 'report_category', array (
		'invoice' => 'Invoice',
		'normal' => 'Normal'
		), isset ( $report ['report_category'] ) ? $report ['report_category'] : '' ) . '</p>' ; 

//===================================================================================================================

echo form_fieldset_close () . 
// Begin form footer...........

form_submit ( 'submit', 'Save', 'class="submit small"' . $js ) . form_reset ( '', 'Cancel', 'onClick="history.go(-1);return true;" class="submit small"' ) . 

// End form footer...........

form_close ();
?>					
					
				</div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>

</div>
