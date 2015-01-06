<div class="block">

	<div class="block_head">
					
					<?php
					echo (isset ( $mod_title ) ? $this->pbf->get_mod_title ( $mod_title ) : '');
					?>
					
					
				</div>
	<!-- .block_head ends -->



	<div class="block_content"><?php
	// $data=array('name'=>'upload_odk','id'=>'upload_odk');
	echo form_open_multipart ( 'hfrentities/upload_zipped_directory' ) . 
	
	// form_hidden(array( 'usertask_id' => isset($task['usertask_id'])?$task['usertask_id']:'')).
	
	form_fieldset ( $this->lang->line ( 'entity_upload_title' ) ) . 

	'<p>' . form_label ( $this->lang->line ( 'entity_upload' ), 'usertask_name' ) . 

	form_upload ( array (
			'name' => 'upload_odk',
			'id' => 'upload_odk' 
	) ) . '</p>' . 

	form_fieldset_close () . 
	
	// Begin form footer...........
	form_submit ( 'submit', 'Save', 'class="submit small"' ) . form_reset ( '', 'Cancel', 'onClick="history.go(-1);return true;" class="submit small"' ) . 
	// End form footer...........
	
	form_close ();
	
	?>	
<?php
if (! empty ( $entities_picture )) {
	$entities = explode ( '-', $entities_picture );
	?>
<fieldset>
			<legend>pictures</legend>
			<ol>
	<?php
	
for($i = 1; $i <= count ( $entities ); $i ++) {
		if (! empty ( $entities [$i] )) {
			?>
	<li><?php
			echo $entities [$i];
			?></li>
	<?php }}?>		
</ol>
		</fieldset>				
	<?php }?>
<?php

if (! empty ( $entities_geo )) {
	$entities = explode ( '-', $entities_geo );
	?>
<fieldset>
			<legend>geographical data</legend>
			<ol>
	<?php
	
for($i = 1; $i <= count ( $entities ); $i ++) {
		if (! empty ( $entities [$i] )) {
			?>
	<li><?php
			echo $entities [$i];
			?></li>
	<?php }}?>		
</ol>
		</fieldset>				
	<?php }?>				
				</div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>

</div>