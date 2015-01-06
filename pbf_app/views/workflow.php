
<div class="block">

	<div class="block_head">
					
	<?php
	echo (isset ( $mod_title ) ? $this->pbf->get_mod_title ( $mod_title ) : '');
	?>
										
	</div>
	<!-- .block_head ends -->
								
	<?php
	
	echo '<div class="block_content">' . (isset ( $tab_menus ) ? '<p class="breadcrumb">' . $tab_menus . '</p>' : '');
	
	echo form_open ( 'workflow/save_workflow' );
	
	echo '<table>' . '<tr>
	<th>' . $this->lang->line ( 'state' ) . '</th>
	<th>' . $this->lang->line ( 'condition' ) . '</th>
	</tr>';
	foreach ( $states as $state ) {
		$options [$state ['state_id']] = $this->lang->line ( $state ['state_name'] );
	}
	$options ['0'] = 'No condition';
	
	unset ( $states [0] ); // remove condition on data entered....
	
	foreach ( $states as $state ) {
		
		echo '<tr><td>' . $this->lang->line ( $state ['state_name'] ) . ' : </td><td>' . form_dropdown ( $state ['state_id'], $options, $state ['condition'] ) . '</td></tr>';
	}
	echo '</table>';
	
	echo form_fieldset_close () . '</BR>' . 
	// Begin form footer...........
	form_submit ( 'submit', $this->lang->line ( 'app_form_save' ), 'class="submit small"' ) . form_reset ( '', $this->lang->line ( 'app_form_cancel' ), 'onClick="history.go(-1);return true;" class="submit small"' );
	
	?>	
						
<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

?>
</div>
<!-- .block_content ends -->

<div class="bendl"></div>
<div class="bendr"></div>

</div>