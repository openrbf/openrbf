<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.min.js"></script>
<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.date_input.pack.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
     		
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


<?php
$default_frm_action = base_url () . $this->router->fetch_class () . '/' . $this->router->fetch_method ();
?>

<script language="javascript">
function reset_filter(){
	
	window.location.href = '<?php echo $default_frm_action;?>';
	
	}

function selecte_all(source){
  	checkboxes = document.getElementsByName('item[]');
  	 for(var i in checkboxes){
    checkboxes[i].checked = source.checked;

	}
	}
	
function submit_delete(){
	if(confirm('<?php echo $this->lang->line('app_mod_delete_selected_confirm');?>')){
	var itemized_list_frm = document.getElementById('itemized_list');
	itemized_list_frm.action = '<?php echo base_url().$this->router->fetch_class().'/delete_selected_acc';?>';
	itemized_list_frm.submit();
	}
	else{
		return false;
		}
	}
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
																
																echo 

																(isset ( $tab_menus ) ? '<p class="breadcrumb">' . $tab_menus . '</p>' : '') . 

																($this->session->flashdata ( 'mod_clss' ) ? '<div class="message ' . $this->session->flashdata ( 'mod_clss' ) . '" style="display: block;"><p>' . $this->session->flashdata ( 'mod_msg' ) . '</p></div>' : '') . 
																
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
																echo $this->table->generate ( $list );
																
																echo form_close ();
																?>
						
						<div class="pagination right">
                        <?php echo $this->pagination->create_links();?>
						</div>
		<!-- .pagination ends -->
		<div><?php echo "<br/><p style='text-align:left; font-size:1.2em;'><span>".str_replace('.xls','',$export_link)."</span> <span>".$export." ".$download."</span></p>";?></div>
		<!--<div><a href="<?php echo $export_link;?>">Export Users</a></div>-->
	</div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>