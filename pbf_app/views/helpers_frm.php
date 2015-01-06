<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.min.js"></script>
<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.date_input.pack.js"></script>

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

$tmpl = array (
		'table_open' => '<table border="0" cellpadding="4" cellspacing="0" width="10px">',
		'row_start' => '<tr class="even">',
		'row_end' => '</tr>',
		'row_alt_start' => '<tr class="odd">',
		'row_alt_end' => '</tr>',
		'table_close' => '</table>' 
);

$this->table->set_template ( $tmpl );

echo 

form_open_multipart ( 'helpers/save' ) . 

form_hidden ( array (
		'helper_id' => isset ( $helper_id ) ? $helper_id : '' 
) ) . 

'<p>' . form_label ( $this->lang->line ( 'form_helper_name' ), 'helper_name' ) . 

form_input ( array (
		'name' => 'helper_name',
		'id' => 'helper_name',
		'class' => 'longtext',
		'value' => set_value ( 'helper_name', isset ( $helper_name ) ? $helper_name : '' ) 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'form_helper_position' ), 'form_helper_position' ) . '</p>' . 

'<div id="position_list" style="width:500px;margin-left:195px;margin-bottom:50px;">' . 

'<p>' . form_label ( $this->lang->line ( 'form_helper_gauche' ) ) . form_radio ( array (
		"name" => "helper_position",
		"id" => "gauche",
		"value" => "1",
		'checked' => set_radio ( 'helper_position', '1', ($helper_position == 1) ? true : false ) 
) ) . '</p>' . '<p>' . form_label ( $this->lang->line ( 'form_helper_droite' ) ) . form_radio ( array (
		"name" => "helper_position",
		"id" => "droite",
		"value" => "0",
		'checked' => set_radio ( 'helper_position', '0' ) 
) ) . '</p>' . '</div>' . '<p>' . form_label ( $this->lang->line ( 'form_helper_order' ), 'helper_order' ) . 

form_input ( array (
		'name' => 'helper_order',
		'id' => 'helper_order',
		'class' => 'helper_order',
		'size' => '50',
		'value' => 0,
		'style' => 'width:5%',
		'value' => set_value ( 'helper_order', isset ( $helper_order ) ? $helper_order : '' ) 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'form_groups' ), 'form_groups' ) . '</p>' . '<div id="groups_list" style="width:500px;margin-left:195px;margin-bottom:50px;">' . $this->table->generate ( $groups ) . '</div>' . 

'<p>' . form_label ( $this->lang->line ( 'form_helper_activate' ), 'form_helper_activate' ) . 

form_checkbox ( array (
		'name' => 'actif',
		'id' => 'actif',
		'value' => 1,
		'style' => 'width:5%',
		'checked' => (isset ( $actif ) && $actif == 1) ? TRUE : FALSE 
) ) . '</p>' . 

form_submit ( 'submit', $this->lang->line ( 'app_form_save' ), 'class="submit small"' ) . form_reset ( '', $this->lang->line ( 'app_form_cancel' ), 'onClick="history.go(-1);return true;" class="submit small"' ) . 
// End form footer...........

form_close ();
?></div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>

</div>


