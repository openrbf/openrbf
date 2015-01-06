<link rel="stylesheet" type="text/css"
	href="<?php echo $this->config->item('base_url');?>cside/css/jquery.jverttabs.css" />
<?php
echo '<div class="block small left">';
foreach ( $helpers as $helper => $v ) {
	if ($v ['details'] ['helper_position'] == 1) {
		echo $v ['display'];
	}
}
echo '</div>';
echo '<div class="block small right">';
foreach ( $helpers as $helper => $v ) {
	if ($v ['details'] ['helper_position'] == 0) {
		echo $v ['display'];
	}
}
echo '</div>';

?>

<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.min.js"></script>
<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.detailsRow.js"></script>
<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery-jvert-tabs.js"></script>



<script>
$(document).ready(function() {
    
	$('#table_completeness').detailsRow('<?php echo $this->config->item('base_url').'dashboard/completeness';?>',{data:{"id":"id"}});
	
	
	$('#table_fosa_completeness').detailsRow('<?php echo $this->config->item('base_url').'dashboard/completeness_fosa';?>',{data:{"id":"id"}});
	
	
});

function dirking_warn(filetype_name,entity_name,period_name,datafile_year){
	
	if(confirm("<?php echo $this->lang->line('datafile_warning_one');?> :\n\n - "+filetype_name.toUpperCase()+"\n\n - <?php echo $this->lang->line('datafile_warning_two');?> "+entity_name.toUpperCase()+"\n\n - <?php echo $this->lang->line('datafile_warning_three');?> "+period_name.toUpperCase()+" "+datafile_year.toUpperCase()+"\n\n\n <?php echo $this->lang->line('datafile_warning_four');?>"))
		{
		return true;
		}
	else{
		
		return false;
		
		}
	
	}

function jump_data_quarters(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
$(".plus").click(function(){
	

	$(".plus").removeClass('col_gris');
	$(this).addClass('col_gris');
});

</script>



