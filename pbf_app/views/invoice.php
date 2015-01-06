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
	
	echo '<div class="block_content">' . (isset ( $tab_menus ) ? '<p class="breadcrumb">' . $tab_menus . '</p>' : '');
	$filename = FCPATH . 'cside/reports/' . $this->config->item ( 'report_prefix' ) . '_' . $invoice ['invoice_id'] . '.pdf';
	$download_link = base_url () . 'cside/reports/' . $this->config->item ( 'report_prefix' ) . '_' . $invoice ['invoice_id'] . '.pdf';
	$size = (file_exists ( $filename )) ? round ( filesize ( $filename ) / 1000 ) . ' Kb' : '';
	
	$link = (file_exists ( $filename )) ? anchor ( $download_link, $this->lang->line ( 'download' ) . ' (' . $size . ')', array (
			'target' => '_blank' 
	) ) : ' ';
	echo '<div><table>
		<tr><td>' . $this->lang->line ( 'report_param_entity' ) . '</td><td>' . $invoice ['entity_name'] . ' (' . $invoice ['entity_type_abbrev'] . ')' . '</td><td colspan="1">' . $link . '</td></tr>
		<tr><td>' . $this->lang->line ( 'report_param_district' ) . '</td><td>' . $invoice ['geozone_name'] . '</td></tr>
		<tr><td>' . $this->lang->line ( 'report_param_year' ) . '</td><td>' . $invoice ['year'] . '</td></tr>' . (($invoice ['month'] == 0) ? ' ' : '<tr><td>' . $this->lang->line ( 'report_param_month' ) . '</td><td>' . $invoice ['month'] . '</td></tr>') . (($invoice ['quarter'] == 0) ? ' ' : '<tr><td>' . $this->lang->line ( 'report_param_trimestre' ) . '</td><td>' . $invoice ['quarter'] . '</td></tr>') . '<tr><td>Date de création</td><td>' . $invoice ['date'] . '</td></tr>' . '<tr><td>Date d\'envoi facture</td><td>' . ((is_null ( $invoice ['sent_date'] )) ? '-' : $invoice ['sent_date']) . '<tr><td>Date de réception facture</td><td>' . ((is_null ( $invoice ['received_date'] )) ? '-' : $invoice ['received_date']) . '<tr></tr>' . '</table></div>';
	
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