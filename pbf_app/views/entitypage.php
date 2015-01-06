<section class="content" id="content">
	<div class="section-a">
		<nav class="crumbs-a">
			<ul>
				<li><a href="<?php echo $this->config->item('base_url')?>">Home</a></li>
				<li><a href=<?php echo $this->config->item('base_url')?> data>Data</a></li>
				<li><a
					href=<?php echo $this->config->item('base_url')."data/showzone/".$breadcrumb['lvl1_link'] ?>><?php echo $breadcrumb['lvl1_title'] ?></a></li>
				<li><a
					href=<?php echo $this->config->item('base_url')."data/showentities/".$breadcrumb['lvl2_link'] ?>><?php echo $breadcrumb['lvl2_title'] ?></a></li>
				<li class="active"><?php echo $entity_info['entity_name'] ?></li>
			</ul>
		</nav>
		<h1><?php echo $entity_info['entity_name']; ?></h1>
		<p class="info-b"><?php echo $entity_info['entity_type_name']; ?> <span
				class="label-a"><?php echo $entity_info['entity_status']; ?></span>
		</p>

		<div class="report-a">
			<div class="header">
				<div class="grid-a">    
						<?php if($pop!=''):?>
                    <div class="column w25">
						<h4><?php echo $this->lang->line('yearly_result');?></h4>
					</div>
						<?php endif;?>
                    <div class="column w25">
						<h4><?php echo $this->lang->line('front_quality');?></h4>
					</div>
					<div class="column w50">
						<h4><?php echo $this->lang->line('front_achieved_payments'); ?></h4>
						<p class="info-a">
							<a class="tooltip-a" href="#" data-toggle="tooltip"
								data-placement="left"
								title="<?php echo $this->lang->line('payement_fosa_tooltip')?>">Info</a>
						</p>
					</div>
				</div>
			</div>

			<div class="body">
				<div class="grid-a">
						<?php if($pop!=''){ ?>
                    <div class="column w25">
						<ul class="data-a">
							<!-- <li><span class="icon-a children"></span> <strong><?php echo number_format($pop,0,$this->lang->line('decimal_separator'),$this->lang->line('thousand_separator'));?></strong> <span><?php echo $this->lang->line('pop_covered'); ?></span></li>-->

							<!--<li><span class="icon-a children"></span> <strong><?php echo $pop;?></strong> <span><?php echo $this->lang->line('pop_covered'); ?></span></li>-->
							<li><span class="icon-a children"></span> <strong><?php echo number_format($pop,0,$this->lang->line('decimal_separator'),$this->lang->line('thousand_separator'));?></strong>
								<span><?php echo $this->lang->line('pop_covered'); ?></span></li>                  
                            <?php
							// Affichage des détails sur les resultats annuels
							foreach ( $key_data as $kd ) :
								?>
                                 <li><strong><?php echo number_format(round($pop*$kd['value']/100),0,$this->lang->line('decimal_separator'),$this->lang->line('thousand_separator'));?></strong>
								<span><?php echo $kd['data']; ?></span></li>
                            <?php
							
endforeach
							;
							if (empty ( $real_time_result ['data'] )) {
								echo "<li>" . $this->lang->line ( 'front_not_available' ) . "</li>";
							} else {
								foreach ( $real_time_result ['data'] as $result ) :
									$realtime = $result ['realtime'];
									$comparaison = $result ['comparaison'];
									if (! empty ( $realtime ['indicator_common_name'] )) :
										?>
                                   <li><img class="icon-a" width="30"
								height="34"
								src="<?php echo base_url('cside/images/portal').'/'.$realtime['indicator_icon_file'];?>"
								alt="<?php echo $realtime['indicator_common_name'];?>"> <strong
								style="font-size: 1.7em;"><?php echo $realtime['sum_validated_value']; ?></strong>
								<span><?php echo $realtime['indicator_common_name'];?></span></li> 
                            
									<?php
                                    endif;
								endforeach
								;
							}
							?>
                            
                        </ul>
					</div>
						<?php  }?>
                    <div class="column w25">
                    
                             <?php
																													
if (empty ( $last_term_quality ['pourcent'] )) {
																														
																														echo "<li>" . $this->lang->line ( 'front_not_available' ) . "</li>";
																													} else {
																														?>
                        <ul class="data-e">
							<!-- .badge-a optional classes: up (green), down (red) & double (two arrows) -->
							<li><span
								class="badge-a <?php echo $last_term_quality['icon'];?>"><?php echo $last_term_quality['pourcent']?><small>%</small></span>
								<span>Global score</span></li>

						</ul>
						<!--<p class="action"><a class="button-c icon-view" href="#qualities"><?php //echo $this->lang->line('entitypage_view_by_indicator');?></a></p>-->
                        <?php  }?>
                    </div>
					<div class="column w50">
						<div class="graph-a" id="payement_graph" style="height: 220px;">
                             <?php
																													if (empty ( $payement_chart )) {
																														
																														echo "<li>" . $this->lang->line ( 'front_not_available' ) . "</li>";
																													} else {
																														echo $payement_chart;
																													}
																													?>
                        </div>
                        <?php
																								
if (is_null ( $data_quarter_datafile ) || $data_quarter_datafile == "") {
																									echo "";
																								} else {
																									?>
							<div class="row" style="width: 1000px; margin-left: 140px">
			        		<?php echo form_open('fosareport/show','class="form-inline"');?>
			        		<div class="col-lg-1">
			        			 <?php echo form_label("Rapport:");?>
			        		</div>
							<div class="col-lg-2">
			        			 <?php echo form_dropdown('datafileQuarterYear',$data_quarter_datafile,'class="input-small"');?>
			        		</div>
							<div class="col-lg-4">
			        			 <?php echo form_submit('submit',$this->lang->line('app_form_open_report'),'class="btn" style="background-color:green;color:white"')?>
			        		</div>
			        		<?php echo form_close();?>
        	              </div>
						<?php }?>
                    </div>

				</div>
			</div>
		</div>

		<div class="grid-a">
			<div class="column w50">
				<div class="details-a">
						<?php
						// On affiche la photo si elle existe
						if (file_exists ( 'cside/images/portal/' . $entity_info ['entity_picturepath'] . '_med.jpg' )) {
							?>
				
                    <div class="gallery-a">
						<figure class="large">
							<?php if(file_exists('cside/images/portal/'.$entity_info['entity_picturepath'].'_or.jpg')){?>
							<a
								href="<?php echo base_url('cside/images/portal').'/'.$entity_info['entity_picturepath'].'_or.jpg';?>">
								<img
								src="<?php echo $this->config->item('base_url').'cside/images/portal/'.$entity_info['entity_picturepath'].'_med.jpg'?>"
								alt="No picture available" />
							</a><?php }else{?>
								<img
								src="<?php echo $this->config->item('base_url').'cside/images/portal/'.$entity_info['entity_picturepath'].'_med.jpg'?>"
								alt="No picture available" />
								<?php } ?>
                        </figure>
					</div>
					<?php }?>
                    
                    <div class="vcard">
								<h4>Information</h4>						
								<p><H4><?php echo $entity_info['entity_name']; ?></H4></p>
								<?php if (!empty($entity_info['entity_type_name'])) {?>
								<p class="org icon"><?php echo $entity_info['entity_type_name'].'('.$entity_info['entity_status'].')'; ?></p>
								<?php } ?>
								<?php if (!empty($entity_info['entity_address'])) {?>
								<p class="adr"><?php echo $entity_info['entity_address']; ?></p>
								<?php } ?>
								<?php if (!empty($entity_info['entity_responsible_name'])) {?>
								<p class="fn n icon"><?php echo $entity_info['entity_responsible_name']; ?></p>
								<?php } ?>
								<?php if (!empty($entity_info['entity_phone_number'])) {?>
								<p class="tel icon"><?php echo $entity_info['entity_phone_number']; ?></p>
								<?php } ?>
								<?php if (!empty($entity_info['entity_responsible_email'])) {?>	
								<p class="em icon"><a class="email" href="mailto:<?php echo $entity_info['entity_responsible_email']; ?>"><?php echo $entity_info['entity_responsible_email']; ?></a></p>
								<?php } ?>
							</div>
							<?php 
			
							if($contract_type['entity_contracttype']==1 || $contract_type['entity_contracttype']==0 ){
									
							?>
						  <div class="vcard">
														
							<?php 
							
							if($contract_type['entity_contracttype']==1) { ?>
								
								
								<p><H5><?php echo $this->lang->line('entity_contract_type_princ') ;
																
								?></H5></p>
								
							
								<?php	
							}?>
								
							<?php 
						
							
							if($contract_type['entity_contracttype']==0) { ?>
							<p>
								<H5>
								<?php echo $this->lang->line('entity_contract_type_sec') ?>
								</H5>
							</p>
																
								<?php
								}
								?>
						
						
						
						
						
						
						
							</div>
							<?php } ?>
                </div>
            </div>
            
            <div class="column w50">
                <div class="map-a" id="map_canvas" style="height: 300px;">
					<?php  echo $map['js']; ?>
                </div>
			</div>
		</div>
		<div class="column col-l" style="display: inline-block"></div>



	</div>
     
    <?php if(count($pbf_data['pbf_data'])>1) : ?>
    <h2><?php echo $this->lang->line('entity_quantity');?></h2>
	<div id="district-quantity" class="table-a table-quantities-a">
		<div id="data-slice">
			<table>
				<thead>
					<tr>
                    <?php
					$headers = $pbf_data ['pbf_data_slice'] [0];
					$i = 1;
					unset ( $pbf_data ['pbf_data_slice'] [0] );
					foreach ( $headers as $header ) {
						echo '<th class="c' . $i . '">' . $header . '</th>';
						$i ++;
					}
					
					?>
                </tr>
				</thead>
				<tbody>
                <?php
					$j = 1;
					foreach ( $pbf_data ['pbf_data_slice'] as $data ) {
						echo '<tr data-rowId="' . $j . '">';
						$i = 1;
						foreach ( $headers as $header ) {
							
							echo '<td class="c' . $i . '" >' . $data [$header];
							echo $i == 1 ? '<div style="width:442px;height:396px;z-index:100;" class="overlay"><div style="z-index:101;" id="less_quantityGraphContainer_' . $j . '"></div></div>' : '';
							echo '</td>';
							$i ++;
						}
						echo '</tr>';
						$j ++;
					}
					?>
            </tbody>
			</table>
		</div>
		<div id="data-full" style="display: none;">
			<table>
				<thead>
					<tr>
                    <?php
					$headers = $pbf_data ['pbf_data'] [0];
					$i = 1;
					unset ( $pbf_data ['pbf_data'] [0] );
					foreach ( $headers as $header ) {
						echo '<th class="c' . $i . '">' . $header . '</th>';
						$i ++;
					}
					
					?>
                </tr>
				</thead>
				<tbody>
                <?php
					$j = 1;
					foreach ( $pbf_data ['pbf_data'] as $data ) {
						
						echo '<tr data-rowId="' . $j . '">';
						$i = 1;
						foreach ( $headers as $header ) {
							echo '<td class="c' . $i . '" >' . $data [$header];
							echo $i == 1 ? '<div style="width:442px;height:396px;z-index:100;" class="overlay"><div style="z-index:101;" id="full_quantityGraphContainer_' . $j . '"></div></div>' : '';
							echo '</td>';
							$i ++;
						}
						echo '</tr>';
						$j ++;
					}
					
					?>
            </tbody>
			</table>

		</div>
		<p class="action">
			<span id="view-all" class="bttn-indicators-a" style="cursor: pointer"><span
				class="title"><?php echo $this->lang->line('view_all_indicators');?></span>

			</span> <span id="view-less" style="cursor: pointer; display: none;"
				class="bttn-indicators-a" style="cursor:pointer"><span class="title"><?php echo $this->lang->line('view_less_indicators');?></span>

			</span>
           
           <?php echo anchor('data/export_entity_quantite/'.$entity_id,$this->lang->line('export_full_data'), 'class="button-b red icon icon-export"' ); ?>
		     
       </p>
	</div>
    <?php endif; ?>
    
    <?php if(count($qualities)>0) : ?>
    <div class="header-a" id="qualities">
		<h2><?php echo $this->lang->line('entity_quality');?></h2>
		<p class="info-a">
			<a class="tooltip-a" href="#" data-toggle="tooltip"
				data-placement="left"
				title="<?php echo $this->lang->line('quality_tooltip')?>">Info</a>
		</p>
	</div>
	<div id="district-quality" class="table-a box table-qualities-a">

		<table>
            <?php
					$entittypes = array_keys ( $qualities );
					$i = 0;
					foreach ( $entittypes as $type_key => $entitytype ) {
						$data = $qualities [$entitytype];
						$headers = array_keys ( $data [0] );
						$headers [0] = $entitytype;
						unset ( $headers [count ( $headers ) - 1] );
						if ($i == 0) {
							echo '<thead>';
							echo '<tr>';
							$class = 1;
							
							foreach ( $headers as $h ) {
								echo '<th class="c' . $class . '">' . $h . '</th>';
								$class ++;
							}
							echo '</tr>';
							echo '</thead>';
							echo '<tbody>';
						} else {
							echo '<tr>';
							echo '<th colspan="8" class="c' . $class . '">' . $entitytype . '</th>';
							echo '</tr>';
						}
						$i ++;
						if (! empty ( $data )) {
							$j = 1;
							foreach ( $data as $d ) {
								
								if (! empty ( $d )) {
									$indicators = $d ['indicators'];
									unset ( $d ['indicators'] );
									unset ( $qualities [$type_key] ['indicators'] );
									
									echo '<tr>';
									$k = 1;
									foreach ( $d as $data_key => $r ) {
										
										echo '<td class="c' . $k . '">' . $r;
										if ($k == 1) {
											echo '<div class="overlay" style="width:750px; height:750px;"><div id="table_quality_' . $j . '">';
											echo '<table id="district_row_' . $j . '" style="display:none"></table>';
											$indicator_header = $indicators [0];
											unset ( $indicators [0] );
											echo '<table id="heat_map_' . $j . '">';
											echo '<thead>';
											echo '<tr>';
											foreach ( $indicator_header as $h ) :
												echo '<th>' . $h . '</th>';
											endforeach
											;
											echo '</tr>';
											echo '</thead>';
											echo '<tbody>';
											// affichage des qualite moyenne de la region dans la overlay. cfr issue BeninPBF-33
											echo '<tr>';
											$d_copy = $d;
											$p = 1;
											foreach ( $d_copy as $d_c ) {
												$class = '';
												if ($p == 1) {
													$d_c = $this->lang->line ( 'global_score' );
													$class = 'stats-title';
												}
												echo '<td class="' . $class . '" style="font-weight:bold;">' . $d_c . '</td>';
												$p ++;
											}
											echo '</tr>';
											// end. Note, enlever le code ci haut pour enlever cette feauture
											foreach ( $indicators as $ind ) :
												echo '<tr>';
												$l = 1;
												foreach ( $indicator_header as $ind_h ) :
													$class = $l == 1 ? 'stats-title' : '';
													echo '<td class="' . $class . '">' . (! is_null ( $ind [$ind_h] ) ? $ind [$ind_h] : '-') . '</td>';
													$l ++;
												endforeach
												;
												echo '</tr>';
											endforeach
											;
											echo '</tbody>';
											echo '</table>';
											echo '</div></div>';
										}
										
										echo '</td>';
										$k ++;
									}
									
									echo '<tr>';
								}
								$j ++;
							}
						}
					}
					?>
        </tbody>

		</table>

		<p class="action">
     <?php echo anchor('data/export_entity_qualite/'.$entity_id,$this->lang->line('export_full_data'), 'class="button-b small red icon icon-export"' ); ?>
	
        </p>

	</div>
    <?php endif; ?>
    
    <?php if(isset($pbf_data_payment_fosa)&&!empty($pbf_data_payment_fosa)) : ?>
        <div class="header-a">
		<h2><?php echo $this->lang->line('front_achieved_payments'); ?></h2>
		<p class="info-a">
			<a class="tooltip-a" href="#" data-toggle="tooltip"
				data-placement="left"
				title="<?php echo $this->lang->line('received_payments')?>">Info</a>
		</p>
	</div>
	<div class="table-a box table-payment-a">
            <?php
					$tmpl = array (
							'table_open' => '<table>',
							'table_close' => '</table>' 
					);
					$this->table->set_template ( $tmpl );
					echo $this->table->generate ( $pbf_data_payment_fosa );
					?>
            <p class="action">
			<span class="bttn-indicators-a"> <span class="title"><a
					style="color: white !important;" class="fcfa active"
					href="<?php echo site_url('data/payment/1')?>"><?php echo $this->lang->line('view_all_payement');?></a></span>
			</span> 

               					
				 <?php echo anchor('data/export_entity_payemet/'.$entity_id,$this->lang->line('export_full_data'), 'class="button-b red icon icon-export"' ); ?>

            </p>
	</div>
    <?php endif;?>
	
	<?php if($verif_budget==1) : ?>
		  <?php if(isset($budget_data)&&!empty($budget_data)) : ?>
	   <div class="header-a">
		<h2><?php echo $this->lang->line('budgets_title'); ?></h2>
		<p class="info-a">
			<a class="tooltip-a" href="#" data-toggle="tooltip"
				data-placement="left"
				title="<?php echo $this->lang->line('received_payments')?>">Info</a>
		</p>
	</div>
	<div class="table-a box table-payment-a">
            <?php
			$tmpl = array (
					'table_open' => '<table>',
					'table_close' => '</table>' 
			);
			$this->table->set_template ( $tmpl );
			echo $this->table->generate ( $budget_data );
			?>
			<div class="header_graph">
			<p class="action">
				<span class="bttn-indicators-a" onClick="InitHighChart();"> <span
					class="title"><?php echo $this->lang->line('view_budget_graphic');?></span>
				</span>

			</p>
		</div>
		<div id="chart"></div>
		<div class="chose_year" style="width: 100px; display: none;">
			<select id="myselect" onChange="InitHighChart();">
				<option value="1">2010</option>
				<option value="2">2011</option>
				<option value="3">2012</option>
				<option selected="selected" value="3">2013</option>
				<option value="4">2014</option>
				<option value="5">2015</option>
			</select>

		</div>
	</div>
		 <?php endif;?>
	<?php endif;?>
</section>
<script>
function InitHighChart()
{
	$("#chart").html(" ");
	
	var options = {
		chart: {
			renderTo: 'chart',
		},
		credits: {
			enabled: false
		},
		title: {
			text:'<?php echo $this->lang->line('payment_vs_budgets');?>',
			x: -20
		},
		xAxis: {
			categories: [{}]
		},
		yAxis: {
			title:{
				text:'<?php echo $this->lang->line('sum_cfa');?>',
			}
		},
	
		tooltip: {
            formatter: function() {
                var s = '<b>'+ this.x +'</b>';
                
                $.each(this.points, function(i, point) {
                    s += '<br/>'+point.series.name+': '+point.y;
                });
                
                return s;
            },
            shared: true
        },
		series: [{},{},{}]
	};
	var year_process=$("#myselect option:selected").text();
	var zone_id_current=<?php echo $entity_id; ?>;
	var siteUrl = "<?php echo site_url('data/json_chart_entity')?>";
	$.ajax({
		url: siteUrl,
		data: {year:year_process,zone_id:zone_id_current},
		type:'post',
		dataType: "json",
		success: function(data){
			options.xAxis.categories = data.categories;
			options.series[0].name = data.budget_name;
			options.series[0].data = data.budget;
			options.series[1].name = data.payment_name;
			options.series[1].data = data.payment;
			options.series[2].name = data.year_budget_name;
			options.series[2].data = data.year_budget;
			var chart = new Highcharts.Chart(options);			
		}
	});
$(".header_graph").html('');
$(".chose_year").show();
}

</script>


<script>
        
    $(function(){
        var quantity_graph_data = <?php echo json_encode($pbf_data['pbf_data']);?>;
        var parent_quantity_graph_data = <?php echo json_encode($parent_pbf_data['pbf_data']); ?>;
        var chart;
        var Script = {
            moreLess : function(){
                $('#view-all, #view-less').click(function(){
                    $('#data-full, #data-slice, #view-all, #view-less').toggle(); 
                });
            },
            tableHeatQuality : function() {
                
                $('#district-quality table tbody tr').hover(function(){
                    
                    if($(this)===undefined || $(this)===null)
                        return;
                    var overlay = $(this).children('td:first').children('.overlay').children('div');
                    
                    var table2 = overlay.children('table:nth-child(2)');
                    if(table2.attr('id')!==undefined)
                        Script.heatQualityChart(table2.attr('id'));
                },function(){});
            },        
            tableQuantityChart : function() {
                $('#district-quantity table tbody tr').hover(function(){
                    if($(this)===undefined || !$(this).attr('data-rowid'))
                        return;
                    var rowId = parseInt($(this).attr('data-rowid'));
                    if(quantity_graph_data===null || quantity_graph_data===undefined || quantity_graph_data.length===0)
                        return;
                    
                    var row_data = quantity_graph_data[rowId];
                    var chartDiv = 'quantityGraphContainer_'+rowId;
                    var div = $(this).children('td:first').children('.overlay').children('div');
                    
                    chartDiv = (div.attr('id'));
                    
                    Script.drawQuantityChart(row_data,chartDiv);
                },function(){
                    if(chart!==undefined && chart!==null)
        			chart.destroy();	
        		});
            },
            Utils : {
                array_keys : function arrayKeys(input) {
                    var output = new Array();
                    var counter = 0;
                    for (var i in input) {
                        output[counter++] = i;
                    } 
                    return output; 
                },
                arrayMax : function(array) {
                    //returns the maximum in an array
                    return Math.max.apply( Math, array );
                     
                },
                parseNumber : function(number) {
                    
                     return parseInt(number.replace(',','').replace(' ','').replace('.',''));                   
                },
                getTextFromUrl : function(url) {
                    return $(url).text();
                },
                tableValues : function(table_id) {
                    //table is a jquery selector that represents that
                    var selector = '#'+table_id+' tbody td';
                    
                    var rep = $(selector).not('.stats-title').map(function(){
                        
                        return parseInt($(this).text()) || 0;
                    }).get();
                    
                    return rep;
                }
            },        
            indicatorParentValues: function(indicator,indicator_key) {
                indicator = Script.Utils.getTextFromUrl(indicator);
                
                for(var obj in parent_quantity_graph_data) {
                    var indic = Script.Utils.getTextFromUrl(parent_quantity_graph_data[obj][indicator_key]);
                    
                    if(indic===indicator) {
                        return parent_quantity_graph_data[obj];
                    }
                }
                
                return null;
            },
            parseData : function(serie) {
                                
                var serie1 = JSON.parse(JSON.stringify(serie));
                
                var periods = Script.Utils.array_keys(serie1);
                
                //delete first indicator it's not required
                var first = periods[0];
                var parentData = Script.indicatorParentValues(serie1[first],first);
                
                var serie2 = JSON.parse(JSON.stringify(parentData));
                //delete the first item. It's not necessary
                delete serie1[first];
                if(serie2!==undefined && serie2!==null && serie2.hasOwnProperty(first))
                    delete serie2[first];
                
                periods.splice(0,1);
                
                var line1 = {
                    name : '<?php echo $this->lang->line('entity_quantity_js'); ?>'
                };
                var line2 = {
                    name : '<?php echo $this->lang->line('district_average_quantity'); ?>'
                };
                var line1Data = [];
                var line2Data = [];
                
                for(var prop in serie1) {                    
                    //console.log(Script.Utils.parseNumber(data[prop]));
                    line1Data.push(Script.Utils.parseNumber(serie1[prop]));
                }
                
                if(serie2!==undefined && serie2!==null) {
                    for(var prop in serie2) {                    
                    //console.log(Script.Utils.parseNumber(data[prop]));
                    line2Data.push(Script.Utils.parseNumber(serie2[prop]));
                    }
                }
                                
                line1.data = line1Data;
                line2.data = line2Data;
                
                var series = [];
                
                series.push(line1);
                series.push(line2);
                var data = {
                    periods : periods,
                    series : series 
                };
                
                return data;
            },        
            drawQuantityChart : function(copy,chartDiv) {
                //make a copy of the object so as not to modify it directly
                
                var chartData = Script.parseData(copy);
                
                 chart = new Highcharts.Chart({
                    chart : {
                        renderTo : chartDiv,
                        type : 'line',
                        height : 360
                    },
                    title : {
                        text : null
                    },
                    subtitle : {
                        text : null
                    },
                    xAxis : {
                        
                        categories : chartData.periods
                    },
                    yAxis : {
                        title : {
                            text : null
                        },
                        gridLineWidth : 1,
                        min: 0
                    },
                    tooltip: {                        
                        followPointer: true   
                        
                    },
                    series : chartData.series
                });
            },
            heatQualityChart : function(table_id) {                
                
                var counts = Script.Utils.tableValues(table_id);
                
                var max = Script.Utils.arrayMax(counts);
                
                //color : change this to customise.
                //75-100 : Green to dark green
                //50-74 : Yellow to dark yellow
                //25-49 : Orange to dark orange
                // 0 -25 : red to dark red
               //30,112,58
                var colors = {
                    'unknown' : {
                        start : {
                            xr : 173,
                            xg : 173,
                            xb : 173
                        },
                        end : {
                            yr : 112,
                            yg : 112,
                            yb : 112
                        }
                    },
                    '100-75':{
                        start : {
                                xr : 200,
                                xg : 244,
                                xb : 21
                        },
                        end : {
                                yr : 30,
                                yg : 112,
                                yb : 58
                        }
                    },
                    '74-50' : {
                        start : {
                                xr : 244, 
                                xg : 221, 
                                xb : 7 
                        },
                        end : {
                                yr : 223, 
                                yg : 242, 
                                yb : 7
                        }
                    },
                    '49-25' : {
                        start : {
                                xr : 242, 
                                xg : 128, 
                                xb : 7 
                        },
                        end : { 
                                yr : 242, 
                                yg : 218, 
                                yb : 7
                        }
                    },
                    '24-0' : {
                        start : {
                                xr : 247, 
                                xg : 21, 
                                xb : 7 
                        },
                        end : {
                                yr : 247, 
                                yg : 110, 
                                yb : 7
                        }
                    }  
                };
	           
                
                var n = 100;
	
                // add classes to cells based on nearest 10 value
                $('#'+table_id+' tbody td').not('.stats-title').each(function(){
                    var val = $(this).text();
                    
                    var color_key = 'unknown';
                    if(val==='-') {
                        val = 0;
                    }else{
                        val = parseInt(val);
                        color_key = getColorIndex(val,colors);
                    }
                    
                    var pos = parseInt((Math.round((val/max)*100)).toFixed(0));
                    var color = colors[color_key];
                    var red = parseInt((color.start.xr + (( pos * (color.end.yr - color.start.xr)) / (n-1))).toFixed(0));
                    var green = parseInt((color.start.xg + (( pos * (color.end.yg - color.start.xg)) / (n-1))).toFixed(0));
                    var blue = parseInt((color.start.xb + (( pos * (color.end.yb - color.start.xb)) / (n-1))).toFixed(0));
                    var clr = 'rgb('+red+','+green+','+blue+')';
                    
                    $(this).css({backgroundColor:clr,color:'white','font-weight':'bold'});
                    
                });
                
                function drawLegend(colors) {
                    //draw legends here and append it to the table
                }
                
                function getColorIndex(val, colorObject) {
                    
                   var indexes = Script.Utils.array_keys(colorObject);
                   
                   for(var index in indexes) {
                       var key = indexes[index];
                       
                       var maxMin = key.split('-');
                       var max = maxMin[0];
                       var min = maxMin[1];
                       
                       if(val>=min && val<=max) {
                           
                           return key;
                       }
                   }
                   
                   return null;
                }
            }
        };
        
       //run the script
       Script.moreLess();
       Script.tableQuantityChart();
       Script.tableHeatQuality();
       
       
    });
</script>