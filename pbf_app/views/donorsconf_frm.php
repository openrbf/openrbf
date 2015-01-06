<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.min.js"></script>
<script type="text/javascript"
	src="<?php echo $this->config->item('base_url');?>cside/js/jquery.date_input.pack.js"></script>
<script type="text/javascript">
	
$(document).ready(function() {
			$('.addregion').click(function() {
			$('#regions_table tbody>tr.donorconf:last').clone(true).insertAfter('#regions_table tbody>tr.donorconf:last');
			$('#regions_table tbody>tr.donorconf:last').find("td:last").html('<a href="#"><?php echo img('cside/images/icons/delete.png');?></a>');
			$('#regions_table tbody>tr.donorconf:last').find("td:last a").addClass('remove');
				  return false;
			});	
			$("#percentage_donor").focus();
			$("#coverage").live('click', function(event) { // use `on()` as `live()` is deprecated
				var rselect;
				rselect = $(this).closest('tr');
				if($(this).prop('checked')){
					rselect.find('input').val(100);
				}else{
					rselect.find('input').val('');
				}
			});
		
		
			$("#region").live('change', function(event) { 
				var siteUrl = "<?php echo site_url('donneurs/json_get_zones')?>";
				var t = $(this);
				var region_process=t.val();
				var rselect = t.closest('tr');
				var dist=rselect.find(".district");
				$.ajax({
					url: siteUrl,
					data: ({region_id:region_process}),
					type:'post',
					dataType: "json",
					success: function(districts_list){
					dist.empty();
					$.each(districts_list,function(geozone_id,geozone_name)
                            {
								var opt = $('<option />'); // here we're creating a new select option for each group
                                opt.val(geozone_id);
                                opt.text(geozone_name);
                                dist.append(opt);
                            });




					
					}
				});
	
			});
			
			$(".district").live('change', function(event) { 
				var siteUrl = "<?php echo site_url('donneurs/json_get_entities')?>";
				var t = $(this);
				var region_process=t.val();
				var rselect = t.closest('tr');
				var dist=rselect.find(".entity");
				$.ajax({
					url: siteUrl,
					data: ({district_id:region_process}),
					type:'post',
					dataType: "json",
					success: function(entities_list){
					dist.empty();
					$.each(entities_list,function(entity_id,entity_name)
                            {
								var opt = $('<option />'); // here we're creating a new select option for each group
                                opt.val(entity_id);
                                opt.text(entity_name);
                                dist.append(opt);
                            });




					
					}
				});
	
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

$tmpl = array (
		'table_open' => '<table border="0" cellpadding="0" cellspacing="0" id="regions_table" >',
		'heading_row_start' => '<tr>',
		'heading_row_end' => '</tr>',
		'heading_cell_start' => '<th>',
		'heading_cell_end' => '</th>',
		'row_start' => '<tr class="donorconf" id="1">',
		'row_alt_start' => '<tr class="donorconf">',
		'row_end' => '</tr>',
		'cell_start' => '<td>',
		'cell_end' => '</td>',
		'table_close' => '</table>' 
);

$tbl = $this->table->set_template ( $tmpl );
$this->table->set_heading ( array (
		$this->lang->line ( 'frm_add_region' ),
		$this->lang->line ( 'frm_add_zone' ),
		$this->lang->line ( 'list_donor_entity' ),
		$this->lang->line ( 'list_donor_indicator' ),
		$this->lang->line ( 'frm_donor_percentage' ),
		$this->lang->line ( 'frm_donor_full' ),
		'' 
) );
$js = 'id="region"';
$ks = 'id="district"';

if (! isset ( $config_details )) {
	$this->table->add_row ( form_dropdown ( 'zone_id[]', $regions, '', $js ), form_dropdown ( 'district_id[]', $districts, '', 'class="district"' ), form_dropdown ( 'entity_id[]', $entities, '', 'class="entity"' ), form_dropdown ( 'indicator_id[]', $indicators ), form_input ( array (
			'name' => 'percentage_id_value[]',
			'id' => 'percentage_donor',
			'class' => 'dataentry_small' 
	) ), form_checkbox ( array (
			'name' => 'coverage',
			'id' => 'coverage' 
	) ), 

	'' );
} else {
	$k = 1;
	foreach ( $config_details as $confdetails ) {
		if ($k == 1) {
			$this->table->add_row ( form_dropdown ( 'zone_id[]', $regions, (empty ( $confdetails ['zone_id'] ) or ($confdetails ['zone_id']) == 0) ? '' : $confdetails ['zone_id'], $js ), form_dropdown ( 'district_id[]', $districts, (empty ( $confdetails ['district_id'] ) or ($confdetails ['district_id']) == 0) ? '' : $confdetails ['district_id'], 'class="district"' ), form_dropdown ( 'entity_id[]', $entities, (empty ( $confdetails ['entity_id'] ) or ($confdetails ['entity_id']) == 0) ? '' : $confdetails ['entity_id'], 'class="entity"' ), form_dropdown ( 'indicator_id[]', $indicators, (empty ( $confdetails ['indicator_id'] ) or ($confdetails ['indicator_id']) == 0) ? '' : $confdetails ['indicator_id'] ), form_input ( array (
					'name' => 'percentage_id_value[]',
					'id' => 'percentage_donor',
					'class' => 'dataentry_small',
					'value' => $confdetails ['percentage'] 
			) ), form_checkbox ( array (
					'name' => 'coverage',
					'id' => 'coverage',
					'checked' => (isset ( $confdetails ['percentage'] ) && $confdetails ['percentage'] == 100) ? TRUE : FALSE 
			) ), 

			'' );
		} else {
			
			$this->table->add_row ( form_dropdown ( 'zone_id[]', $regions, (empty ( $confdetails ['zone_id'] ) or ($confdetails ['zone_id']) == 0) ? '' : $confdetails ['zone_id'], $js ), form_dropdown ( 'district_id[]', $districts, (empty ( $confdetails ['district_id'] ) or ($confdetails ['district_id']) == 0) ? '' : $confdetails ['district_id'], 'class="district"' ), form_dropdown ( 'entity_id[]', $entities, (empty ( $confdetails ['entity_id'] ) or ($confdetails ['entity_id']) == 0) ? '' : $confdetails ['entity_id'], 'class="entity"' ), form_dropdown ( 'indicator_id[]', $indicators, (empty ( $confdetails ['indicator_id'] ) or ($confdetails ['indicator_id']) == 0) ? '' : $confdetails ['indicator_id'] ), form_input ( array (
					'name' => 'percentage_id_value[]',
					'id' => 'percentage_donor',
					'class' => 'dataentry_small',
					'value' => $confdetails ['percentage'] 
			) ), form_checkbox ( array (
					'name' => 'coverage',
					'id' => 'coverage',
					'checked' => (isset ( $confdetails ['percentage'] ) && $confdetails ['percentage'] == 100) ? TRUE : FALSE 
			) ), 

			'<a class="remove" href="#">' . img ( 'cside/images/icons/delete.png' ) . '</a>' );
		}
		
		$k ++;
	}
}

$attributes = array (
		'id' => 'conf_donor' 
);
echo form_open_multipart ( 'donneurs/save_config', $attributes ) . 

form_hidden ( array (
		'donorconfig_id' => isset ( $donorconfig_id ) ? $donorconfig_id : '' 
) ) . "&nbsp" . '<p>' . form_label ( $this->lang->line ( 'frm_donor' ) ) . form_dropdown ( 'donor_id', $default_donors, $donor_id ) . "</p>" . "<p>" . form_label ( $this->lang->line ( 'list_donor_from' ) ) . form_input ( array (
		'name' => 'from',
		'id' => 'from',
		'class' => 'text date_picker',
		'value' => set_value ( 'from', $from ) 
) ) . "</p>" . form_input ( array (
		'name' => 'to',
		'id' => 'to',
		'class' => 'text date_picker',
		'value' => set_value ( 'to', $to ) 
) ) . form_label ( $this->lang->line ( 'list_donor_to' ) );

echo 

form_fieldset ( $this->lang->line ( 'frm_donor_config' ) ) . 

'<a class="addregion" href="#">' . img ( 'cside/images/icons/small_add.png' ) . '</a>' . 

$this->table->generate () . 

form_fieldset_close () . 
// =====================================================================================================================================

form_submit ( 'submit', $this->lang->line ( 'app_form_save' ), 'class="submit small"' ) . form_reset ( '', $this->lang->line ( 'app_form_cancel' ), 'onClick="history.go(-1);return true;" class="submit small"' ) . 
// End form footer...........

form_close ();
?></div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>

</div>










