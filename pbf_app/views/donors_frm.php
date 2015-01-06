<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.min.js"></script>
<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.date_input.pack.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.addregion').click(function() {
			  
			  $('#regions_table tbody>tr.filetype:last').clone(true).insertAfter('#regions_table tbody>tr.filetype:last');
			  
			  $('#regions_table tbody>tr.filetype:last').find("td:last").html('<a href="#"><?php echo img('cside/images/icons/delete.png');?></a>');
			  
			  $('#regions_table tbody>tr.filetype:last').find("td:last a").addClass('remove');
			  
			  return false;
			  
        });	
		$('.addzone').click(function() {
			  
			  $('#zones_table tbody>tr.filetype:last').clone(true).insertAfter('#zones_table tbody>tr.filetype:last');
			  
			  $('#zones_table tbody>tr.filetype:last').find("td:last").html('<a href="#"><?php echo img('cside/images/icons/delete.png');?></a>');
			  
			  $('#zones_table tbody>tr.filetype:last').find("td:last a").addClass('remove');
			  
			  return false;
			  
        });
		$('.remove').live('click', function() {
			
			$(this).parent().parent().remove();
	
			return false;
		
		});
	$('input.date_picker').date_input();
			
    });

$.extend(DateInput.DEFAULT_OPTS, {
  stringToDate: function(string) {
    var matches;
    if (matches = string.match(/^(\d{4,4})-(\d{2,2})-(\d{2,2})$/)) {
      return new Date(matches[1], matches[2] - 1, matches[3]);
    } else {
      return null;
    };
  },

  dateToString: function(date) {
    var month = (date.getMonth() + 1).toString();
    var dom = date.getDate().toString();
    if (month.length == 1) month = "0" + month;
    if (dom.length == 1) dom = "0" + dom;
    return date.getFullYear() + "-" + month + "-" + dom;
  },
  
 month_names: ["<?php echo $this->lang->line('app_month_1');?>", "<?php echo $this->lang->line('app_month_2');?>", "<?php echo $this->lang->line('app_month_3');?>", "<?php echo $this->lang->line('app_month_4');?>", "<?php echo $this->lang->line('app_month_5');?>", "<?php echo $this->lang->line('app_month_6');?>", "<?php echo $this->lang->line('app_month_7');?>", "<?php echo $this->lang->line('app_month_8');?>", "<?php echo $this->lang->line('app_month_9');?>", "<?php echo $this->lang->line('app_month_10');?>", "<?php echo $this->lang->line('app_month_11');?>", "<?php echo $this->lang->line('app_month_12');?>"],
 short_month_names: ["<?php echo $this->lang->line('app_month_1_short');?>", "<?php echo $this->lang->line('app_month_2_short');?>", "<?php echo $this->lang->line('app_month_3_short');?>", "<?php echo $this->lang->line('app_month_4_short');?>", "<?php echo $this->lang->line('app_month_5_short');?>", "<?php echo $this->lang->line('app_month_6_short');?>", "<?php echo $this->lang->line('app_month_7_short');?>", "<?php echo $this->lang->line('app_month_8_short');?>", "<?php echo $this->lang->line('app_month_9_short');?>", "<?php echo $this->lang->line('app_month_10_short');?>", "<?php echo $this->lang->line('app_month_11_short');?>", "<?php echo $this->lang->line('app_month_12_short');?>"],
 short_day_names: ["<?php echo $this->lang->line('app_day_1_short');?>", "<?php echo $this->lang->line('app_day_2_short');?>", "<?php echo $this->lang->line('app_day_3_short');?>", "<?php echo $this->lang->line('app_day_4_short');?>", "<?php echo $this->lang->line('app_day_5_short');?>", "<?php echo $this->lang->line('app_day_6_short');?>", "<?php echo $this->lang->line('app_day_7_short');?>"]
});
</script>

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

echo 

form_open_multipart ( 'donneurs/save' ) . 

form_hidden ( array (
		'donor_id' => isset ( $donor_id ) ? $donor_id : '' 
) ) . '<p>' . form_label ( $this->lang->line ( 'frm_donor_name' ) ) . form_input ( array (
		'name' => 'donor_name',
		'id' => 'donor_name',
		'value' => $donor_name 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_donor_abrev' ) ) . form_input ( array (
		'name' => 'donor_abrev',
		'id' => 'donor_abrev',
		'value' => $donor_abrev 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_donor_logo' ) ) . 

form_upload ( array (
		'name' => 'donor_logopath',
		'id' => 'donor_logopath' 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_donor_website' ) ) . form_input ( array (
		'name' => 'donor_url',
		'id' => 'donor_url',
		'value' => $donor_url 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_donor_contact' ) ) . form_input ( array (
		'name' => 'donor_contact',
		'id' => 'donor_contact',
		'value' => $donor_contact 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_donor_email' ) ) . form_input ( array (
		'name' => 'donor_email',
		'id' => 'donor_email',
		'value' => $donor_email 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_donor_associatedgroup' ) ) . form_dropdown ( 'groupassociated_id', $groups, $groupassociated_id, 'id="groupassociated_id"' ) . '</p>' . 

form_submit ( 'submit', $this->lang->line ( 'app_form_save' ), 'class="submit small"' ) . form_reset ( '', $this->lang->line ( 'app_form_cancel' ), 'onClick="history.go(-1);return true;" class="submit small"' ) . 
// End form footer...........

form_close ();
?></div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>

</div>

