<?php
$default_frm_action = base_url () . $this->router->fetch_class () . '/' . $this->router->fetch_method ();
?>

<div class="block">

	<div class="block_head">
									
         <?php
									echo (isset ( $mod_title ) ? $this->pbf->get_mod_title ( $mod_title ) : '');
									?>
                    
	</div>
	<!-- .block_head ends -->

	<div class="block_content">
				
     <?php
					
					echo (isset ( $tab_menus ) ? '<p class="breadcrumb">' . $tab_menus . '</p>' : '') . ($this->session->flashdata ( 'mod_clss' ) ? '<div class="message ' . $this->session->flashdata ( 'mod_clss' ) . '" style="display: block;"><p>' . $this->session->flashdata ( 'mod_msg' ) . '</p></div>' : '') . 
					
					// see if you can make this an array for displaying info and warning at the same time
					(isset ( $mod_clss ) ? '<div class="message ' . $mod_clss . '" style="display: block;"><p>' . $mod_msg . '</p></div>' : '');
					
					/*
					 * RED =>errormsg GREEN => success BLUE => info YELLOW => warning
					 */
					
					$tmpl = array (
							'table_open' => '<table border="0" cellpadding="2" cellspacing="0" class="filters">',
							'table_close' => '</table>' 
					);
					$this->table->set_template ( $tmpl );
					
					echo (isset ( $rec_filters ) && ! empty ( $rec_filters )) ? form_open ( $default_frm_action, array (
							'name' => 'filter_frm',
							'id' => 'filter_frm' 
					) ) . $this->table->generate ( $rec_filters ) . form_close () : '';
					
					echo form_open ( '', array (
							'name' => 'itemized_list',
							'id' => 'itemized_list' 
					) );
					
					$tmpl = array (
							'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',
							'row_start' => '<tr class="even">',
							'row_end' => '</tr>',
							'row_alt_start' => '<tr class="odd">',
							'row_alt_end' => '</tr>',
							'table_close' => '</table>' 
					);
					
					$this->table->set_template ( $tmpl );
					
					// echo $this->table->generate($list);
					
					// --------------------------------------------------
					?>
<!--
<div class='testbrice' >
<h1>
Hi you
</h1>
<div class='accordionButton'>
click
</div>
<div class='accordionContent'>
<h2>Hey man what are </h2>
<h2>Hey man what are1 </h2>
<h2>Hey man what are2 </h2>
<span class='accordionClose'> Xclose </span>
</div>
</div>
-->
<?php
// -----------------------
?>


<div class="block ">

			<div class="block_head">
					
					<?php
					echo heading ( $this->lang->line ( 'completude_recapitulatif' ) );
					
					?>
					
				</div>
			<!-- .block_head ends -->



			<div class="block_content" id="table_completeness">
				<table cellspacing="1" cellpadding="0" border="0"
					class="tablesorter" id="table_completeness">
					<thead>
						<tr>
				<?php
				
				foreach ( $list [0] as $k => $v ) {
					?>
					<th class="header"><?php echo $v ?></th>
                <?php
				}
				?>
			</tr>
					</thead>

					<tbody>	
		<?php
		unset ( $list [0] );
		
		foreach ( $list as $key => $value ) {
			?>
		
		<tr>
				
                <?php
			
			foreach ( $value as $k => $v ) {
				
				if ($k == 'geozone_name') {
					
					?>
					<td class="accordionButton" id="<?php echo $value['geozone_id']?>"><b><?php echo $v ?></b></td>
                <?php
				} else {
					?>
					<td style='text-align: center'><?php echo $v ?></td>
                <?php
				}
			}
			$geoId = $value ['geozone_id'];
			// echo "<pre>";
			// print_r($list);
			// echo "</pre><br/><br/>";
			?>
				
			</tr>
						<tr class='accordionContent'
							id='cont_<?php echo $value['geozone_id']?>'>
							<td style='vertical-align: top'><span class='accordionClose'><a><?php echo $this->pbf->rec_op_icon('close');  ?></a></span></td>
							<td colspan='11'>
								<div
									style='font-size: 0.8em; color: black; border-left: 1px solid gray;'>
				<?php
			
			echo $this->table->generate ( $district [$geoId] );
			?>
				</div>
							</td>
						</tr>
<?php
		}
		// echo "<pre>";
		// print_r($district);
		// echo "</pre>";
		?>
	</tbody>
				</table>



			</div>
			<!-- .block_content ends -->

			<div class="bendl"></div>
			<div class="bendr"></div>

		</div>
			
<?php

// ---------------------------------------------------
?>
<div class='block'>
			<div class="block_head">
					
					<?php
					echo heading ( $this->lang->line ( 'completude_detail' ) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp" . $detail_links );
					
					?>
					
	</div>
			<!-- .block_head ends -->
			<div class="block_content">
				
<?php
// echo"<br/><br/><h2>".$this->lang->line('completude_detail')." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp".$detail_links."</h2>";
echo "<br/><p style='text-align:left; font-size:1.2em;'><span>$export_link</span></p>";

echo $this->table->generate ( $detail );

echo form_close ();
?>
	</div>
			<!-- .block_content ends -->
		</div>
		<div class="pagination right">
          <?php echo $this->pagination->create_links();?>
	</div>
		<!-- .pagination ends -->

	</div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.min.js"></script>

<script>

$(document).ready(function() {
	
	/********************************************************************************************************************
	SIMPLE ACCORDIAN STYLE MENU FUNCTION
	********************************************************************************************************************/	
	$('td.accordionButton').click(function() {
		var idd="cont_"+$(this).attr('id');
		//$('tr.accordionContent[id=$idd]');
		$("tr.accordionContent").hide();
		$("tr#" + idd ).slideDown('normal'); 
		$("tr#" + idd ).next().slideDown('normal'); 
		
		
	});
	
	$('span.accordionClose').click(function(){
	
		$('tr.accordionContent').slideUp('normal');
		$('tr.accordionContent').next().slideDown('normal');
		//$("div.accordionContent").hide();
	});
	/********************************************************************************************************************
	CLOSES ALL DIVS ON PAGE LOAD
	********************************************************************************************************************/	
	$("tr.accordionContent").hide();

});
</script>
