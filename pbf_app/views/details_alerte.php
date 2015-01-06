<div class="block">

	<div class="block_head">
					<?php
					echo (isset ( $mod_title ) ? $this->pbf->get_mod_title ( $mod_title ) : '');
					?>
				</div>
	<!-- .block_head ends -->
	<div class="block_content">


		<table border="0" cellpadding="4" cellspacing="0">
			<tr>
				<td style=""><?php echo $this->lang->line('frm_alerte_title') ;?></td>
				<td><?php echo $details['alerte_title'];?></td>
			</tr>
			<tr>
				<td style=""><?php  echo ( ($details['quarter']===NULL)? $this->lang->line('helper_alerte_month'):$this->lang->line('form_alerte_quarter'));?></td>
				<td><?php  echo ( ($details['quarter']===NULL)? $details['month']:$details['quarter']);?></td>
			</tr>
			<tr>
				<td style=""><?php echo $this->lang->line('helper_alerte_year') ;?></td>
				<td><?php echo $details['year'];?> </td>
			</tr>
			<tr>
				<td style=""><?php echo $this->lang->line('helper_alerte_sentdate') ;?></td>
				<td><?php echo $details['date_alerte'];?> </td>
			</tr>
			<tr>
				<td style=""><?php echo $this->lang->line('form_alerte_message') ;?></td>
				<td><?php echo $details['messagetext'];?> </td>
			</tr>
		</table>



		<div
			style="column-count: 4; -moz-column-count: 4; -webkit-column-count: 4">
			<ol>
					
					<?php
					$liste_fosa = explode ( ',', $details ['message'] );
					foreach ( $liste_fosa as $fosa ) {
						
						?>
					
					<li> <?php echo $fosa ?></li>
					
					<?php
					}
					?>
					
					</ol>

		</div>
					<?php
					
					echo form_open_multipart ( 'alertes/delete_log_alerte' ) . form_hidden ( array (
							'alerte_id' => $details ['alerte_id'] 
					) ) . form_submit ( 'submit', $this->lang->line ( 'app_form_delete' ), 'class="submit small"' ) . form_reset ( '', $this->lang->line ( 'app_form_cancel' ), 'onClick="history.go(-1);return true;" class="submit small"' ) . form_close ();
					
					?>					
					
				</div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>

</div>