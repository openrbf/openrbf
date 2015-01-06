<div class="block">
			
				<div class="block_head">
					<?php
                    echo (isset($mod_title)?$this->pbf->get_mod_title($mod_title):'');
					?>
				</div>		<!-- .block_head ends -->
				<div class="block_content">
<?php

echo form_open('translator', '', $hidden );

?>

<table>

<?php

echo '<tr>';
echo '<th class="translator_table_header">' . 'Key' . '</td>';
echo '<th class="translator_table_header"><b>' . ucwords( $masterLang ) . '</td>';
echo '<th class="translator_table_header">' . ucwords( $slaveLang ) . '</td>';
echo '</tr>';

foreach ( $moduleData as $key => $line ) {
	echo '<tr>';
	echo '<td>' . $key . '</td>';
	echo '<td>' . htmlspecialchars( $line['master'] ) . '</td>';
	echo '<td>' . htmlspecialchars( $line['slave'] ) . '</td>';
	echo '</tr>';
}

?>

</table>

<?php

echo form_submit('ConfirmSaveLang', 'Confirm' , 'class="submit"');
echo form_reset('',$this->lang->line('app_form_cancel'),'onClick="history.go(-1);return true;" class="submit small"');

echo form_close();
	
?></div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
					
			</div>