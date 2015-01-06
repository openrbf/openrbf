
<div class="block">
			
	<div class="block_head">
					
	<?php
          echo (isset($mod_title)?$this->pbf->get_mod_title($mod_title):'');
	?>
										
	</div>		<!-- .block_head ends -->
								
	<?php 

	echo '<div class="block_content">'.				
	(isset($tab_menus)?'<p class="breadcrumb">'.$tab_menus.'</p>':'');

	
	echo 'Entrer ici le numero de la facture mentione en bas de page</br>';
	echo form_open('report/set_invoice_sent');
	$inv_send = array (
	  'name' => 'invoice',
	  'id' => 'invoice',
	  'maxlength'   => '11',
	  'size'        => '11',

	) ;
	echo '<div>Invoice ID : '.$this->config->item('report_prefix').'_'.form_input($inv_send).'</div>'.
	
	form_fieldset_close().
	'</BR>'.
	// Begin form footer...........
	form_submit('submit', $this->lang->line('app_form_send'), 'class="submit small"').
	form_reset('',$this->lang->line('app_form_cancel'),'onClick="history.go(-1);return true;" class="submit small"');
	
	

	
	?>	
						
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
?>
</div>		<!-- .block_content ends -->
				
	<div class="bendl"></div>
	<div class="bendr"></div>
					
</div>