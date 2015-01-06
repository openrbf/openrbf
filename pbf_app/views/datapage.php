<section class="content" id="content">
	<div class="section-a">
		<nav class="crumbs-a">
			<ul>
				<li><a href="<?php echo $this->config->item('base_url')?>">Home</a></li>
				<li><a href=<?php echo $this->config->item('base_url')?> data><?php echo $this->lang->line('front_breadcrumb_data');?></a></li>
				<li class="active"><?php echo $current_zone_info['geozone_name'] ?></li>
			</ul>
		</nav>
		<h1><?php echo $current_zone_info['geozone_name'].' : '.$this->lang->line('front_pbf_data'); ?></h1>
		<div class="report-a">
			<div class="header">
				<div class="grid-a">
					<div class="column w25">
						<h4><?php echo $this->lang->line('key_data'); ?></h4>
					</div>
					<div class="column w25">
						<h4 style="width: 80%;"><?php echo $this->lang->line('frm_realtime_result');?></h4>
                        <?php $realtime_period=$this->config->item('realtimeresult_period_data');  ?>
                        <p class="info-a">
							<a class="tooltip-a" href="#" data-toggle="tooltip"
								data-placement="left"
								title="<?php echo $this->lang->line('realtime_info_append').$realtime_period .$this->lang->line('realtime_info_preppend')?>">Info</a>
						</p>
					</div>
					<div class="column w25">
						<h4><?php echo $this->lang->line('front_quality_score');?></h4>
                 <?php
																	
$average_quality_period = $this->config->item ( 'average_quality_period' );
																	if ($average_quality_period == '') {
																		$tooltip_quality = $this->lang->line ( 'average_quality' );
																	} else {
																		
																		$tooltip_quality = $this->lang->line ( 'average_quality_append' ) . $average_quality_period . $this->lang->line ( 'average_quality_preppend' );
																	}
																	
																	?>
                        <p class="info-a">
							<a class="tooltip-a" href="#" data-toggle="tooltip"
								data-placement="left" title="<?php echo $tooltip_quality ?>">Info</a>
						</p>
					</div>
					<div class="column w25">
						<h4><?php echo $this->lang->line('front_achieved_payments'); ?></h4>
					</div>
				</div>
			</div>

			<div class="body">
				<div class="grid-a">
					<div class="column w25">
						<ul class="data-a color">
							<li><i class="icon-a children"></i> <strong><?php echo number_format($pop,0,$this->lang->line('decimal_separator'),$this->lang->line('thousand_separator'));?></strong>
								<span><?php echo $this->lang->line('pop_covered'); ?></span></li>
                            <?php
																												foreach ( $key_data as $kd ) :
																													?>
                                <li><strong><?php echo number_format(round($pop*$kd['value']/100),0,$this->lang->line('decimal_separator'),$this->lang->line('thousand_separator'));?></strong>
								<span><?php echo $kd['data']; ?></span></li>
                            <?php
																												endforeach
																												;
																												?>
                        </ul>
					</div>
					<div class="column w25">
						<ul class="data-b">
                                <?php
																																if (empty ( $real_time_result ['data'] )) {
																																	echo "<li>" . $this->lang->line ( 'front_not_available' ) . "</li>";
																																} else {
																																	foreach ( $real_time_result ['data'] as $result ) :
																																		$realtime = $result ['realtime'];
																																		$comparaison = $result ['comparaison'];
																																		?>
                                   <li><a
								href="<?php echo site_url('data/element/'.$realtime['indicator_id']);?>">
									<img class="icon-a" width="30" height="34"
									src="<?php echo base_url('cside/images/portal').'/'.$realtime['indicator_icon_file'];?>"
									alt="<?php echo $realtime['indicator_common_name'];?>"> <strong
									style="font-size: 1.7em;"><?php echo $realtime['sum_validated_value']; ?></strong>
									<span><?php echo $realtime['indicator_common_name'];?></span>
							</a></li>
                            <?php
																																	endforeach
																																	;
																																}
																																?>
                        </ul>
					</div>
					<div class="column w25">
						<ul class="data-c">
                                         <?php
																																									if (empty ( $average_qual )) {
																																										
																																										echo $this->lang->line ( 'front_not_available' );
																																									} else {
																																										foreach ( $average_qual as $av_qual ) :
																																											
																																											?>
                                <li><span><?php echo $av_qual['name_type'] ?> </span>
								<strong> <?php echo round($av_qual['average_qual']) ?><small>%</small></strong>
							</li>
                      
                            <?php
																																										endforeach
																																										;
																																									}
																																									?>
              
                        </ul>
					</div>
					<div class="column w25">
                        <?php
																								
																								if (! isset ( $received_payement )) {
																									echo "<li>" . $this->lang->line ( 'front_not_available' ) . "</li>";
																								} else {
																									?>
                        <ul class="data-d">
							<li><a href="<?php echo site_url('data/payment/1');?>"> <img
									class="icon-a" width="30" height="34"
									src="<?php echo base_url('cside/images/portal').'/'.$received_payement['realtime']['indicator_icon_file'];?>"
									alt="<?php echo $received_payement['indicator_common_name'];?>">
									<strong><span><?php echo $received_payement['realtime']['money_amount'];?></span> <?php echo $received_payement['realtime']['money_local'];?></strong>
									<span><?php echo $received_payement['realtime']['money_usd_amount'];?> <?php echo $received_payement['realtime']['money_usd'];?></span>
							</a></li>
							<li><i class="icon-a child"></i> <strong><span><?php echo $received_payement['realtime']['per_capita_usd'];?></span>
									$ <small>Per Capita</small></strong> <span><?php echo $received_payement['realtime']['per_capita'];?> FCFA</span></li>
						</ul>
                            <?php }?>  
                    </div>
				</div>
			</div>
		</div>

		<div class="grid-a">
			<div class="column w50">
				<div class="map-a" id="map_canvas" style="height: 300px;"></div>
			</div>
			<div class="column w50">
				<div class="quality-b">
                  <?php
																		if (empty ( $top_quality_score )) {
																			echo "<h4>" . $this->lang->line ( 'top_score_quality' ) . "</h4>";
																			echo "<li>" . $this->lang->line ( 'front_not_available' ) . "</li>";
																		} else {
																			$first = $top_quality_score [0];
																			unset ( $top_quality_score [0] );
																			$i = 1;
																			
																			?>                    
                    <article class="item">
						<figure>
							<?php if(file_exists('cside/images/portal/'.$first['picture'].'_or.jpg')){?>
							<a
								href="<?php echo base_url('cside/images/portal').'/'.$first['picture'].'_or.jpg';?>">
								<img
								src="<?php echo base_url('cside/images/portal').'/'.$first['picture'].'_med.jpg';?>"
								alt="No picture available for this entity" />
							</a><?php }else{?>
							<img
								src="<?php echo base_url('cside/images/portal').'/'.$first['picture'].'_med.jpg';?>"
								alt="No picture available for this entity" />
								<?php } ?>
						</figure>
						<h4><?php echo $this->lang->line('top_score_quality');?></h4>
						<p class="title">
							<span>1.</span><?php echo $first['entity'];?></p>
						<p class="global">
							<span class="badge-a <?php echo $first['comparaison']; ?>"><?php echo $first['montant']; ?></span>
						</p>

					</article>
					<div class="table">
						<div class="head">
							<p class="title">
								<span>Name</span>
							</p>
							<p class="global">Global</p>
						</div>
                        <?php foreach($top_quality_score as $score):?>
                        <div class="item">
							<p class="title"><?php echo ++$i; ?>. <?php echo $score['entity'];?></p>
							<p class="global">
								<i class="badge-b <?php echo $score['comparaison'];?>"></i> <?php echo $score['montant'];?></p>
						</div>
                        <?php endforeach; ?>
                    </div>
                         <?php
																		}
																		?>
                    
                </div>
			</div>
		</div>
		<div id="map_region">
			<ul class="links-a grid-a link-list-inline">
				<li class="column w50"><strong><?php echo $geos_title;?></strong>
                    <?php
																				echo ul ( $map_render ['links'], array (
																						'class' => 'list-unstyled link-list-inline' 
																				) );
																				?>
                </li>
			</ul>
		</div>

	</div>
    
    <?php if(count($pbf_data['pbf_data'])>0) : ?>
    <h2><?php echo $this->lang->line('region_quantity'); ?></h2>
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
            <?php echo anchor('data/export_zone_quantite/'.$geo_id.'/'.$zone_id,$this->lang->line('export_full_data'), 'class="button-b red icon icon-export"' ); ?>
		             
       </p>
	</div>
    <?php endif; ?>
    
    <?php if(count($qualities)>0) : ?>
    <div class="header-a">
		<h2><?php echo $this->lang->line('region_quality'); ?></h2>
		<p class="info-a">
			<a class="tooltip-a" href="#" data-toggle="tooltip"
				data-placement="left"
				title="<?php echo $this->lang->line('quality_evolution')?>">Info</a>
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
											echo '<table style="display:none;" id="district_row_' . $j . '">'./*<thead>
                                            <tr>';
                                            $d_copy = $d;
                                            foreach($d_copy as $d_c) {
                                                echo '<th>'.$d_c.'</th>';
                                            }
                                            '</tr>
                                        </thead>*/
                                        '</table>';
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
												// $class = $p==1?'stats-title':'';
												// echo '<td class="'.$class.'" style="font-weight:bold;">'.$d_c.'</td>';
												// $p++;
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
      <?php echo anchor('data/export_zone_qualite/'.$geo_id.'/'.$zone_id,$this->lang->line('export_full_data'), 'class="button-b small red icon icon-export"' ); ?>
	 
       </p>

	</div>
    <?php endif; ?>
    
    <?php if(isset($class_totals)&&!empty($class_totals)) : ?>
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
					echo $this->table->generate ( $class_totals );
					?>
            <p class="action">
			<span class="bttn-indicators-a"> <span class="title"><a
					style="color: white !important;" class="fcfa active"
					href="<?php echo site_url('data/payment/1')?>"><?php echo $this->lang->line('view_all_payement');?></a></span>
			</span> 

                <?php echo anchor('data/export_zone_payemet/'.$zone_id,$this->lang->line('export_full_data'), 'class="button-b red icon icon-export"' ); ?>

            </p>
	</div>
    <?php endif;?>
	<?php
	
	if ($verif_budget == 1) :
		?>
	
	  <?php if(isset($budget_data)&&!empty($budget_data)) : ?>
	   
        <div class="header-a">
		<h2><?php echo $this->lang->line('budgets_title'); ?></h2>
		<p class="info-a">
			<a class="tooltip-a" href="#" data-toggle="tooltip"
				data-placement="left"
				title="<?php echo $this->lang->line('budget_info_icon')?>">Info</a>
		</p>
	</div>
		
			
		<?php
			
			foreach ( $budget_data [1] as $key => $value ) {
				
				if ($budget_data [1] [$key] == 0 && $budget_data [1] [$key] !== 'TRIMESTRE' && $budget_data [1] [$key] !== 'BUDGET') {
					$budget_data [1] [$key] = '-';
				}
			}
			
			?>
		
		
		
        <div class="table-a box table-payment-a">
		<div class="command_graphic" onmouseover="InitHighChart();">
            <?php
			$tmpl = array (
					'table_open' => '<table>',
					'table_close' => '</table>' 
			);
			$this->table->set_template ( $tmpl );
			echo $this->table->generate ( $budget_data );
			?>
           
		</div>
		<div id="chart_year">
			<div id="chart"></div>
			<div class="chose_year" style="width: 100px; display: none;">
				<select id="myselect" onClick="InitHighChart();">
					<option value="1">2010</option>
					<option value="2">2011</option>
					<option value="3">2012</option>
					<option selected="selected" value="3">2013</option>
					<option value="4">2014</option>
					<option value="5">2015</option>
				</select>

			</div>
		</div>

	</div>	
    <?php endif;?>
	
	<?php endif;?>
		
</section>
<script type="text/javascript"
	src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<script type="text/javascript"
	src="<?php echo site_url()?>cside/js/infobox.js"></script>

<script>
function InitHighChart()
{
	$('#chart_year').show();	
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
	var baseurl = '<?php echo base_url();?>';
	$.ajax({
		url : baseurl+'index.php/data/json_chart',
		data: ({year:year_process}),
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
                    name : '<?php echo $this->lang->line('region_quantity'); ?>'
                };
                var line2 = {
                    name : '<?php echo $this->lang->line('national_average_quantity'); ?>'
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
<script>    
   
   $(function(){
       
       //map starts
       var tooltip_template = '<div id="tooltip" style="display:none;"><div id="tooltip-content"></div></div>';
       $('body').append(tooltip_template);
                        
       //enable google maps visual refresh 
       google.maps.visualRefresh = true;
       
       /*
        *   Extending the google map library to calculate the center of the polygon
        */
       google.maps.Polygon.prototype.my_getBounds=function(){
            var bounds = new google.maps.LatLngBounds();
            this.getPath().forEach(function(element,index){
                bounds.extend(element);
            })
            return bounds;
        }

       function multi_polygon_center(json) {
           
           var length = json.coordinates.length;
           var bounds_array = [];
           
           for(var i=0; i<length;i++) {
               var coordinates = json.coordinates[i][0];
               var bound = new google.maps.LatLngBounds();
               var l = coordinates.length;
               for(var j=0; j<l; j++) {
                    var point = new google.maps.LatLng(coordinates[j][1], coordinates[j][0]);
                    bound.extend(point);
               }
         
               bounds_array.push(bound);
              
           }
           
           var bounds = new google.maps.LatLngBounds();
           
           for(var k=0; k<bounds_array.length; k++) {
               bounds.union(bounds_array[k]);
           }
          
          return bounds.getCenter();
       }
       
        
       
        //function that draws a circle rounding in the supplied argument
        function getCircle(magnitude) {
            return {
                path: google.maps.SymbolPath.CIRCLE,
                fillColor: 'red',
                fillOpacity: .4,
                scale: magnitude,
                strokeColor: 'white',
                strokeWeight: 1
            };
        }
       
       //array to store polygons representing each region
       var polygons = [];
       
       /*
        * function that parse polyon GeoJSON data supplig in the argument.
        * @arg GeoJSON data
        * @return google maps coordinates path
        */
       function parse_polygon(data_array) {
            var i=0,j=0;
            var shapes = [];
            
            for(i=0; i<data_array.coordinates.length;i++) {
                var shape = [];
                for(j=0;j<data_array.coordinates[i].length;j++) {
                    var point = new google.maps.LatLng(data_array.coordinates[i][j][1], data_array.coordinates[i][j][0]);
                    //var point = data_array.coordinates[i][j][1]+' '+data_array.coordinates[i][j][0];
                    shape.push(point);
                }
                shapes.push(shape);
            }
            
            return shapes;
        }
		
		
		$('.command_graphic').click(function() {
		$('#chart_year').hide();
			});
        
        /*
        * function that parse multipolyon GeoJSON data supplig in the argument.
        * @arg GeoJSON data
        * @return google maps coordinates path
        */
       
        function parse_multi_polygon(data_array) {
            var i=0,j=0,k=0;
            var shapes = [];
            for(i=0;data_array.coordinates.length;i++) {
                
                var sub_cord = data_array.coordinates[i];
                if(!sub_cord) {
                    break;
                }
                for(j=0; j < sub_cord.length; j++) {
                    var shape = [];
                    for(k=0;k < sub_cord[j].length;k++) {
                        var point = new google.maps.LatLng(sub_cord[j][k][1],sub_cord[j][k][0]);
                        //var point = sub_cord[j][k][1]+':'+sub_cord[j][k][0];
                        shape.push(point);
                    }
                     shapes.push(shape);
                }
              
            }
            
            return shapes;
       }
       
       
     /*
      * highlight polygon
      */
     
     function highlight(polygon) {
         polygon.setOptions({
            strokeOpacity: 1,
            strokeWeight: 3,
            strokeColor : 'd83f28',
            zIndex : 10,
            fillOpacity: 0.10
        });
     }
     
     /*
      * unhighlight polygon
      */
     function unhighlight(polygon) {
          polygon.setOptions({
            strokeOpacity: 0.8,
            strokeColor: '2E2D2D',
            strokeWeight: 2,
            zIndex : 1,
            fillOpacity: 0.35
        });
     }
     
     /*
      * function that shows the tooltip
      */
     function show_tooltip(id, text) {
         $(id).css('display','block').css('position','absolute').css('z-index','10000').css('top',global_client_y).css('left',global_client_x);
         $(id).children('#tooltip-content').html(text);
     }
     
     /*
      * function that hides the tooltip
      */
     function hide_tooltip(id) {
         $(id).hide().children('#tooltip-content').html('');
         
     }
     
     var global_client_x = 0;
     var global_client_y = 0;
     
     $('body').on('mousemove','#map_canvas',function(e){
         global_client_x = e.pageX;
         global_client_y = e.pageY;
     });
       
     /*
      * Send ajax request to GeoJSON data. 
      * Parm "2" references region
      */ 
     var site_url = '<?php echo site_url() ?>';     
     var mapOptions = {
                    center: new google.maps.LatLng(12.262892, -1.000000),
                    zoom: 6,
                    scrollwheel: false,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                 };
                 
                 
    var map = new google.maps.Map(document.getElementById("map_canvas"),mapOptions);
    
    var current_url = window.location.pathname; 
    var parameterstring = current_url.substring(0,current_url.indexOf('.html')); //removes the html extension 
    if(!parameterstring) {
        parameterstring = current_url;
    }
    
    var parametters = parameterstring.split('/');
    var siteUrl = "<?php echo site_url('home/get_geo_json')?>";

    siteUrl = siteUrl.substring(0,siteUrl.indexOf('.html'));
    siteUrl = siteUrl+'/'+parametters[parametters.length-2]+'/'+parametters[parametters.length-1];
    
     $.ajax({
         'url':siteUrl,
         'type':'POST',
         'dataType':'json',
         success : function(data) {
            
            if(data.map.geo_lat_long!='') {
                
                var map_coords = JSON.parse(data.map.geo_lat_long);
           
                var gmap_center_coord = new google.maps.LatLng(map_coords.latitude, map_coords.longitude);
                map.setOptions({
                    center : gmap_center_coord,
                    zoom : map_coords.zoom
                });
            }
            
            var layer = data.layer; 
			Mapbounds  = new google.maps.LatLngBounds();
               var count=$(layer).size();
			   			
             $.each(layer,function(index,val){
			     var url = val.url;
                 if(val.geometries=='')
                     return;
                 var json = JSON.parse(val.geometries);
                 
                 var type = json.type;
                 var active = val.active;
                 var path = '';
                 
                 if(type=='Polygon') {
                     path = parse_polygon(json);
                 }else{
                     if(type=="MultiPolygon") {
                         path = parse_multi_polygon(json); 
                     }
                 }                 
                 
                 var polygon = new google.maps.Polygon({
                        paths: path,
                        map: map,
                        strokeColor: '2E2D2D',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: '2E2D2D',
                        zIndex : 1,
                        fillOpacity: 0.35
                 });
                 
                 //stores polygon for further use
                 polygons.push(polygon);
                 
                 //add listeners only if the region is active
                 if(active=='1') {
				
                   
                    google.maps.event.addListener(polygon, 'click', function() {
                           
                            window.location = url;
                    });
                    
                    google.maps.event.addListener(polygon, 'mouseover', function(){
                        
                        var tooltip_text =  val.zone_name;
                        highlight(polygon);
                        show_tooltip("#tooltip", tooltip_text);

                    });
                    
                    google.maps.event.addListener(polygon, 'mouseout', function(){
                       
                        hide_tooltip('#tooltip');
                                                
                        unhighlight(polygon);

                    });

                    var bound;
                    
                    if(type=='Polygon') {
                        bound = polygon.my_getBounds().getCenter();
                    }else{
                        if(type=="MultiPolygon") {
                            bound = multi_polygon_center(json);
                        }
                    } 
                    if (count<4){
							vertices = polygon.getPath();
							for (var i =0; i < vertices.getLength(); i=i+1) {
								var xy = vertices.getAt(i);
								loc = new google.maps.LatLng(xy.lat(),xy.lng());
								Mapbounds.extend(loc);
							}
					
					}else{
							loc = new google.maps.LatLng(bound.lat(),bound.lng());
							Mapbounds.extend(loc);
					}
					
                    var marker = new google.maps.Marker({
                        position: bound,
                        map: map,
                        icon: getCircle(val.entities/2)
                    });
                    
                    google.maps.event.addListener(marker,'click',function(){
                        window.location = url;
                    });
                    
                    var labelText = '<div style="color: #FFF;font-weight:bold;font-size:12px">'+val.entities+'</div>';

                    var myOptions = {
                        content: labelText,
                        boxStyle: {
                            width : "50px",
                            fontSize : "large"
                        },
                        disableAutoPan: true,
                        pixelOffset: new google.maps.Size(-8, -10),
                        position: marker.getPosition(),
                        closeBoxURL: "",
                        isHidden: false,
                        enableEventPropagation: false
                    };
					//console.log(map_coords.longitude);
						
					
                    var label = new InfoBox(myOptions);
                    label.open(map);
                 
                
                }
                
                return;
                
             });
			 

			map.fitBounds(Mapbounds);       
			map.panToBounds(Mapbounds);    
            
            
         },
         error : function() {
             console.log('an error occured');
             console.log('reloading');
             if(!site_url || site_url=='' || site_url==undefined)
                window.location.reload(true);
         }
         
     });
     
     
     
    //add an id to each link
    $('#map_region .link-list-inline li a').each(function(index){
        $(this).attr('id','region-'+index);
        
    });
    
    //trigger map mouseover on link mouseover
    $('#map_region .link-list-inline li a').mouseover(function(e){
        
        var region_id = $(this).attr('id');
       
        if(!region_id) {
            return;
        }
        
        var region = region_id.split('-');
        
        if(region.length>1) {
            var index = parseInt(region[1]);
            var hovered_polygon = polygons[index];
            
            highlight(hovered_polygon);
        }
    });
    
    //trigger map mouse out on link mouse out
    $('#map_region .link-list-inline li').mouseout(function(){
        var region_id = $(this).children('a').attr('id');
       
        if(!region_id) {
            return;
        }
        
        var region = region_id.split('-');
        
        if(region.length>1) {
            var index = parseInt(region[1]);
            var hovered_polygon = polygons[index];
            
            unhighlight(hovered_polygon);
        }
    });
    
   
     
   });
   
</script>