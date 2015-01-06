<script language="javascript">
function popWindow(wName){
	features = 'width=980,height=700,toolbar=no,location=yes,directories=no,menubar=no,scrollbars=no,copyhistory=no,resizable=1,left = 312,top = 184';
	pop = window.open('',wName,features);
	if(pop.focus){ pop.focus(); }
	return true;
}

function applyCascadingDropdowns() {
    applyCascadingDropdown("entity_geozone_id", "entity_id");   
}
window.onload=applyCascadingDropdowns;
</script>
<div class="block withsidebar">

	<div class="block_head">

		<?php
		echo (isset ( $mod_title ) ? $this->pbf->get_mod_title ( $mod_title ) : '');
		?>
	</div>
	<!-- .block_head ends -->



	<div class="block_content">

		<div class="sidebar">
			<?php
			
			echo ul ( $reports, array (
					'class' => 'sidemenu' 
			) );
			
			if (isset ( $report_descript )) {
				
				echo '<p>' . $report_descript . '</p>';
			}
			
			?>
		</div>
		<!-- .sidebar ends -->

		<div class="sidebar_content">
			<?php
			if (isset ( $report_title )) {
				
				echo form_open ( 'report/show', 'onSubmit="return popWindow(this.target)" target="report_show" id="report_window"' ) . form_hidden ( array (
						'report_id' => $report_id 
				) ) . form_fieldset ( $report_title ) . '<p>';
				
				foreach ( $report_params as $param ) {
					
					if (empty ( $donor_id )) {
						echo $this->pbf->get_report_param ( $param );
					} else {
						echo $this->pbf->get_report_param_donor ( $param, $donor_id );
					}
				}
				echo '</p>' . form_fieldset_close () . form_submit ( 'submit', $this->lang->line ( 'app_form_open_report' ), 'class="submit small"' ) . form_reset ( '', $this->lang->line ( 'app_form_cancel' ), 'onClick="history.go(-1);return true;" class="submit small"' ) . form_close ();
			}
			?>
		</div>
		<!-- .sidebar_content ends -->


		<!-- .sidebar_content ends -->

	</div>
	<!-- .block_content ends -->


	<div class="bendl"></div>
	<div class="bendr"></div>

</div>
