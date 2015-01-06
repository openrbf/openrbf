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
echo '<th class="translator_table_header">' . ucwords( $masterLang ) . '</td>';
echo '<th class="translator_table_header">' . ucwords( $slaveLang ) . '</td>';
echo '</tr>';

foreach ( $moduleData as $key => $line ) {
	echo '<tr valign="top">';
	echo '<td>' . $key . '</td>';
	echo '<td>' . htmlspecialchars( $line[ 'master' ] ) . '</td>';
	
	if ( mb_strlen( $line[ 'slave' ] ) > $textarea_line_break ) {
		echo '<td>' . form_textarea( array( 'name' => $postUniquifier . $key,
											'value' => $line[ 'slave' ],
											'rows' => $textarea_rows
											)
									);
	} else {
		echo '<td>' . form_input( $postUniquifier . $key, $line[ 'slave' ], 'class="longtext"');
	}

	if ( strlen( $line[ 'error' ] ) > 0 ) {
		echo '<br /><span class="translator_error">' . $line[ 'error' ] . '</span>';
	}

	if ( strlen( $line[ 'note' ] ) > 0 ) {
		echo '<br /><span class="translator_note">' . $line[ 'note' ] . '</span>';
	}

	echo '</td>';
	echo '</tr>';
}

?>

</table>

<?php

echo form_submit('SaveLang', $this->lang->line('app_form_save') , 'class="submit"');
echo form_reset('',$this->lang->line('app_form_cancel'),'onClick="history.go(-1);return true;" class="submit small"');

echo form_close();
	
?>
</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
					
			</div>