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

echo form_open_multipart ( 'indicatorcategories/save' ) . 

form_hidden ( array (
		'category_id' => isset ( $category ['category_id'] ) ? $category ['category_id'] : '' 
) ) . 

'<p>' . form_label ( $this->lang->line ( 'frm_indicator_title' ), 'category_title' ) . 

form_input ( array (
		'name' => 'category_title',
		'id' => 'category_title',
		'class' => 'longtext',
		'value' => set_value ( 'category_title', isset ( $category ['category_title'] ) ? $category ['category_title'] : '' ) 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_indicator_linked_filetypes_sortorder' ), 'category_order' ) . 

form_input ( array (
		'name' => 'category_order',
		'id' => 'category_order',
		'class' => 'dataentry_small',
		'value' => set_value ( 'category_order', isset ( $category ['category_order'] ) ? $category ['category_order'] : '' ) 
) ) . '</p>' . 

// Begin form footer...........
form_submit ( 'submit', $this->lang->line ( 'app_form_save' ), 'class="submit small"' ) . form_reset ( '', $this->lang->line ( 'app_form_cancel' ), 'onClick="history.go(-1);return true;" class="submit small"' ) . 
// End form footer...........

form_close ();
?></div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>

</div>