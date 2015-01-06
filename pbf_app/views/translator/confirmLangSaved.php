<div class="block">
			
				<div class="block_head">
					<?php
                    echo (isset($mod_title)?$this->pbf->get_mod_title($mod_title):'');
					?>
				</div>		<!-- .block_head ends -->
				<div class="block_content">
<table>

<?php

echo '<tr>';
echo '<th class="translator_table_header">' . ucwords( $slaveLang ) . '</td>';
echo '<th class="translator_table_header">' . ucwords( $langModule ) . '</td>';
echo '</tr>';


?>

</table>

<p><?php echo $this->data['saved_data']; ?></p>
</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
					
			</div>