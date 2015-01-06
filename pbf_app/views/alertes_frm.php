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

form_open_multipart ( 'alertes/save' ) . 

form_hidden ( array (
		'alerteconfig_id' => isset ( $alerteconfig_id ) ? $alerteconfig_id : '' 
) ) . 

'<p>' . form_label ( $this->lang->line ( 'frm_alerte_title' ), 'alerte_title' ) . 

form_input ( array (
		'name' => 'alerte_title',
		'id' => 'alerte_title',
		'class' => 'longtext',
		'value' => set_value ( 'alerte_title', isset ( $alerte_title ) ? $alerte_title : '' ) 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_alerte_message' ), 'alerte_message' ) . 

form_textarea ( array (
		'name' => 'alerte_message',
		'id' => 'alerte_message',
		'rows' => 6,
		'cols' => 40,
		'value' => set_value ( 'alerte_message', isset ( $alerte_message ) ? $alerte_message : '' ) 
) ) . form_wysiwyg ( $ckeditor ) . '</p>' . 

form_fieldset ( $this->lang->line ( 'frm_alertes_delay' ) ) . '<p>' . form_label ( $this->lang->line ( 'helper_alerte_days' ), 'alerte_delay' ) . 

form_dropdown ( 'alerte_delay', $days_list, isset ( $alerte_delay ) ? $alerte_delay : '', 'style="width: 100px; font-size: 13px"' ) . '</p>' . '<p>' . form_label ( $this->lang->line ( 'helper_alerte_month' ), 'alerte_delay' ) . 

form_dropdown ( 'month_delay', $months_list, isset ( $month_delay ) ? $month_delay : '', 'style="width: 100px; font-size: 13px"' ) . '</p>' . 

form_fieldset_close () . 

'<p>' . form_label ( $this->lang->line ( 'form_alerte_filestypes' ), 'alerte_email' ) . 

form_dropdown ( 'filetypes[]', $filetypes, isset ( $filetypes_default ) ? $filetypes_default : '', 'id="filetypes" multiple="multiple"' ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'form_alerte_fields' ), 'fields_monitor' ) . 

form_dropdown ( 'fields_monitor[]', $fields_monitor, isset ( $fields_monitor_default ) ? $fields_monitor_default : '', 'id="fields_monitor" ' ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'form_alerte_group' ), 'groups' ) . 

form_dropdown ( 'groups[]', $groups, isset ( $groups_default ) ? $groups_default : '', 'id="groups" multiple="multiple"' ) . 

'</p>' . 

'<p>' . form_label ( $this->lang->line ( 'form_alerte_users' ), 'users_list' ) . '</p>' . '<div id="users_list" style="overflow-y: scroll;height:400px;width:400px;margin-left:195px;margin-bottom:20px;margin-top:0px;">' . $this->table->generate ( $users ) . '</div>' . '<p>' . form_label ( $this->lang->line ( 'frm_alert_publish_dashboard' ), 'alert_dashboard' ) . 

form_checkbox ( array (
		'name' => 'alerte_dashboard',
		'id' => 'alerte_dashboard',
		'value' => 1,
		'style' => 'width:5%',
		'checked' => ($alerte_dashboard == 1) ? TRUE : FALSE 
) ) . '</p>' . 

'<p>' . form_label ( $this->lang->line ( 'frm_alert_send_email' ), 'alerte_email' ) . 

form_checkbox ( array (
		'name' => 'alerte_email',
		'id' => 'alerte_email',
		'value' => 1,
		'style' => 'width:5%',
		'checked' => ($alerte_email == 1) ? TRUE : FALSE 
) ) . '</p>' . form_submit ( 'submit', $this->lang->line ( 'app_form_save' ), 'class="submit small"' ) . form_reset ( '', $this->lang->line ( 'app_form_cancel' ), 'onClick="history.go(-1);return true;" class="submit small"' ) . 
// End form footer...........

form_close ();
?></div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>

</div>

<script>
$('[id^=users_id]').click(function() {
    var count_checked = $("[name='users[]']:checked").length; // count the checked rows
        if(count_checked == 0) 
        {
             document.getElementById("groups").disabled = false;
            
        }else{
		    
			$("#groups option:selected").each(function(){
				$(this).removeAttr("selected");
			});
			document.getElementById("groups").disabled = true;
		  
        } 
});

$(function(){
   var count_checked = $("[name='users[]']:checked").length; // count the checked rows
        if(count_checked == 0) 
        {
             document.getElementById("groups").disabled = false;
            
        }else{
		    
			$("#groups option:selected").each(function(){
				$(this).removeAttr("selected");
			});
			document.getElementById("groups").disabled = true;
		  
        } 




});
</script>