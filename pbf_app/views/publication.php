<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.min.js"></script>
<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.detailsRow.js"></script>
<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.jverttabs.js"></script>
<link rel="stylesheet" type="text/css"
	href="<?php echo $this->config->item('base_url');?>cside/css/jquery.jverttabs.css" />
<script language="javascript">
$(document).ready(function() {
	$('#table_publication').detailsRow('<?php echo $this->config->item('base_url').'publication/load_publication';?>',{data:{"id":"id"}});
	
});

function selecte_all_publish(source){
  	checkboxes = document.getElementsByName('published_id[]');
  	 for(var i in checkboxes){
    checkboxes[i].checked = source.checked;

	}
	}

function selecte_all_validate(source){
  	checkboxes = document.getElementsByName('validation_id[]');
  	 for(var i in checkboxes){
    checkboxes[i].checked = source.checked;

	}
	}

function selecte_all_validate_reg(source){
  	checkboxes = document.getElementsByName('validation_reg_id[]');
  	 for(var i in checkboxes){
    checkboxes[i].checked = source.checked;

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

															(isset ( $tab_menus ) ? '<p class="breadcrumb">' . $tab_menus . '</p>' : '');
															?>
				
 <table width="100%" border="0" cellspacing="0" cellpadding="0"
			id="table_publication">
			<tr>
				<th><?php echo $this->lang->line('data_quarter').' '.$this->lang->line('data_year');?>
				
				</td>
				<th><?php echo $this->lang->line('date_validated');?>
				
				</td>
				<th><?php echo $this->lang->line('validation_author');?>
				
				</td>
				<th><?php echo $this->lang->line('data_date_published');?>
				
				</td>
				<th><?php echo $this->lang->line('data_author');?>
				
				</td>

			</tr>
<?php
$i = 0;
// print_test($publish);
// print_test($validate);exit;
foreach ( $publish ['list'] as $k => $list_item ) {
	?> 
  <tr id="<?php
	
	echo $list_item ['datafile_quarter'];
	?>">
				<td class="plus" abbr="<?php echo $list_item['datafile_year'];?>"><?php echo $this->lang->line('app_quarter_'.$list_item['datafile_quarter']).' '.$list_item['datafile_year'].' <b>'.$this->lang->line('click_details').'</b>';?></td>
				<td><?php echo $validate['list'][$k]['date_created'];?></td>
				<td><?php echo $validate['list'][$k]['user_fullname'];?></td>
				<td><?php echo $list_item['date_created'];?></td>
				<td><?php echo $list_item['user_fullname'];?></td>
			</tr>
<?php
	$i ++;
}
?> 
</table>


		<!-- .pagination ends -->

	</div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>