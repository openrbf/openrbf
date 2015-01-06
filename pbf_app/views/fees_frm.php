<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.date_input.pack.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	
	$("#filetype_id").change(function() {
   				
		if(($("#entity_id").val()!='') && ($("#filetype_id").val()!='')){
			
			$.ajax({
   				type: "POST",
   				url: "<?php echo base_url(); ?>indicators/load_fees",
   				data: {	"filetype_id":$("#filetype_id").find(':selected')[0].id,
						"geozone_id":$("#geozone_id").val(),
						"entity_id":$("#entity_id").find(':selected')[0].id
						},
   				beforeSend: function() {
        			$('#selection_bay').html('<p>&nbsp;</p><p>&nbsp;</p><p><center><img src="<?php echo base_url().'cside/images/loading.gif'; ?>"></center></p><p>&nbsp;</p><p>&nbsp;</p>');
   				 },
   				success: function(msg) {
					// desible all the selectors
					//$("#selectors").find('select').css("display", "none");
	   				$('#selection_bay').html(msg);
  			 	},
				error: function(){
					$('#selection_bay').html('<p>&nbsp;</p><p>&nbsp;</p><p><center><font color="#ff0000"><strong>Couldn \'t retrive info</strong></font></center></p><p>&nbsp;</p><p>&nbsp;</p>');
					}
 			});
			
		}
		else{
			<?php if(!isset($targetz)){?>
			$('#selection_bay').html('');
			<?php
			}
			?>
			}
		
	});
	
	
	});


</script>
<div class="block">

	<div class="block_head">

		<h2><?php
		echo (isset ( $mod_title ) ? $this->pbf->get_mod_title ( $mod_title ) : '');
		?></h2>

	</div>
	<!-- .block_head ends -->



	<div class="block_content">

		<div id="selectors"><?php
		
echo form_open ( '', array (
				'name' => 'selection_frm',
				'id' => 'selection_frm' 
		) ) . $hf_selector . form_close ();
		?></div>
		<div id="selection_bay"><?php echo $targetz;?></div>

	</div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>

</div>