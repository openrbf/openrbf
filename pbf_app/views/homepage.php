<section class="locations-a" id="map_container">
	<div class="map" id="map_canvas"></div>
	<div class="filter">
		<div class="wrap">
			<h3><?php echo $this->lang->line('front_map_title'); ?></h3>

			<h4 class="light-red" id="current_region"><?php echo $map_render['links'][0];?></h4>
            <?php $colors = array('dark-red','light-red','orange','yellow','mid-green','light-green','','','','','','');?>
            <div class="dropdown">
				<a data-toggle="dropdown" href="#"><?php echo $this->lang->line('select_region_here');?></a>
				<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                    <?php $i=0;foreach ($map_render['links'] as $link) : ?>
                        <li class="<?php echo $colors[$i]; ?>"><?php echo $link; $i++;?></li>
                    <?php endforeach;?>
                </ul>
			</div>
		</div>
	</div>

	<div class="info" id="info-transparent">
		<div class="wrap">
			<h2 style="white-space: nowrap;"><?php echo $this->lang->line('front_pbf_in_country');?></h2>
			<div class="copy">
				<p><?php echo $this->lang->line('front_infos_map');?></p>

			</div>
		</div>
	</div>

</section>

<section class="content" id="content">
     <?php
					if (isset ( $real_time_result ['data'] ) && ! empty ( $real_time_result ['data'] )) {
						
						?> 
    <article class="results-a">
		<header>
			<h1><?php
						echo $this->lang->line ( 'frm_realtime_result' );
						?>
            </h1>
			<p><?php  echo $real_time_result['tooltip'];?></p>
		</header>
		<div class="items">
            <?php
						
foreach ( $real_time_result ['data'] as $result ) :
							$realtime = $result ['realtime'];
							$comparaison = $result ['comparaison'];
							?>
                <article class="item">
				<a href="<?php echo $realtime['indicator_link']?>">
					<figure>
						<img
							src="<?php echo base_url('cside/images/portal').'/'.$realtime['indicator_icon_file'];?>"
							alt="<?php echo $realtime['indicator_common_name'];?>">
					</figure>
					<h4 style="font-size: 28px;"><?php echo $realtime['sum_validated_value']; ?></h4>
					<p><?php echo $realtime['indicator_common_name'];?></p>
                            <?php
							
if (! empty ( $comparaison )) {
								?>
                            <p
						class="evolution <?php echo $comparaison['icon'] ?>">
						Evolution <span><?php echo $comparaison['pourcentage'];?></span>
					</p>
                            <?php
							}
							?>
                    </a>
			</article>
            <?php endforeach;?>             
        </div>
		<p class="action">
			<a class="button-a icon-search" href="<?php echo site_url('data');?>"><?php echo $this->lang->line('front_detailed_pbf_result'); ?></a>
		</p>
		<footer>
			<p><?php echo $this->lang->line('front_quality_description');?></p>
		</footer>
	</article>
    <?php } ?>
    <?php
				if (isset ( $top_quality ) && isset ( $top_quality ['data'] ) && ! empty ( $top_quality ['data'] ) && count ( $top_quality ['data'] ) > 1) {
					$keys = array_keys ( $top_quality ['data'] );
					
					if (count ( $top_quality ['data'] [$keys [0]] ) > 1 || count ( $top_quality ['data'] [$keys [1]] ) > 1) {
						?>
        <article class="quality-a">
		<h1><?php echo $this->lang->line('frm_top_quality') ?></h1>
		<div class="grid-a">
                <?php
						if (empty ( $top_quality )) {
							echo "<h4>" . $this->lang->line ( 'top_score_quality' ) . "</h4>";
							echo "<li>" . $this->lang->line ( 'front_not_available' ) . "</li>";
						} else {
							$first = $top_quality [0];
							
							unset ( $top_quality [0] );
							$i = 1;
							foreach ( $keys as $k => $key ) :
								?>
                    <div class="column w50">

				<h5 class="caption"><?php echo $key; ?></h5>
							<?php
								$quality = $top_quality ['data'] [$key];
								
								// unset($quality[0]);
								// foreach ($quality as $q) :
								?>

						<article class="item">

					<figure>
						<table height="150px">
							<tr>
								<p class="global">
								
								
								<td style="width: 200px;"><span>1.</span> <?php echo $quality[1]['entity_name'];?></td>
								<td style="margin-left: 150px"><span
									class="badge-a <?php echo $quality[1]['comparaison']; ?>"><?php echo $quality[1]['datafile_total']; ?></span>
								<!--</td>-->
								<?php //if($quality[1]['entity_picturepath']!=""){?>
							<!--<td>-->
								<?php if(file_exists('cside/images/portal/'.$quality[1]['entity_picturepath'].'_or.jpg')){?>
							<a
									href="<?php echo base_url('cside/images/portal').'/'.$quality[1]['entity_picturepath'].'_or.jpg';?>">
										<img style="border-radius: 10px;"
										src="<?php echo base_url('cside/images/portal').'/'.$quality[1]['entity_picturepath'].'_med.jpg';?>"
										width="150px" height="130px" />
								</a>
										<?php }else{if($quality[1]['entity_picturepath']==""){?><?php  //} ?>
							<img style="border-radius: 10px;"
									src="<?php echo base_url('cside/images/portal').'/'.$quality[1]['entity_picturepath'].'_med.jpg';?>"
									width="150px" height="130px" />
							<?php }} ?>
							</td>
								</p>
							</tr>
						</table>
					</figure>
				</article>
				<div class="table" style="border-radius: 10px;">
					<table>
						<tr>
							<td width="490px"><span></span></td>
							<td width="100px"><span>Global</span></td>
						</tr>
					</table>

								<?php for($i=2; $i<count($quality);$i++){?>
							<!--<div class="item">-->

					<p class="global">
					
					
					<table>
						<tr>
							<td width="400px"><span><?php echo $i; ?>.</span><?php echo $quality[$i]['entity_name'];?></td>
							<td width="100px"><i
								class="badge-b <?php echo $quality[$i]['comparaison'];?>"> </i><?php echo $quality[$i]['datafile_total'];?></td>
						</tr>
					</table>
					</p>

					<!--</div>-->
								<?php }?>
						</div>

				<!--<div class="ftable-a hospital">
                            <h3 class="caption"><?php echo $key; ?></h3>
                            <div class="body">
                                <?php
								$quality = $top_quality ['data'] [$key];
								
								unset ( $quality [0] );
								foreach ( $quality as $q ) :
									?>
                                        <p class="tr">
                                            <span class="td a"><?php echo $q['entity_name']?></span>
                                            <span class="td b"><?php echo $q['datafile_total']?></span>
                                        </p>
                                    <?php
								endforeach
								;
								?>                
                            </div>
                            <p class="more"><a href="<?php echo site_url('data');?>">See more Quality Results</a></p>
                        </div>-->
			</div>
                <?php
							endforeach
							;
						}
						?>
            </div>
	</article>   
    <?php
					}
				}
				?>
    
    <div class="featured-a grid-a">
    <?php if(!empty($featured_news)) : ?>        
        <div class="column w65 news">
			<h3><?php echo $this->lang->line('front_news_header');?></h3>
                     
            <?php foreach($featured_news as $news): ?>
                <article class="news-item-a">
                <?php if(isset($news['content_link'])) : ?>
                    <figure>
					<a
						href="<?php echo site_url().'articles/item/'.$news['content_id']; ?>"><img
						src="<?php echo base_url().'cside/contents/images/'.$news['content_link'].'_med.jpg';?>"
						alt="PBF Training" /></a>
				</figure>
                            <?php endif;?>
                    <h4><?php echo $news['content_title']; ?></h4>
				<p class="date"><?php echo mdate("%d %M  %Y",  strtotime($news['content_create_date'])); ?></p>
				<p><?php echo strip_tags(character_limiter($news['content_description'], 200)); ?></p>
				<p class="more">
					<a
						href="<?php echo site_url().'articles/item/'.$news['content_id']; ?>">More</a>
				</p>
			</article>
            <?php endforeach;?>
            
        </div>       
    
				<?php
        endif;
				?>
    
    <?php if(!empty($featured_docs)) : ?>        
        <div class="column w35 docs">
        	<?php
					
if (sizeof ( $fraude_reports ) < 1) {
						echo "";
					} else {
						echo '<h3>' . $this->lang->line ( 'front_fraud_header' ) . '</h3>';
						
						?>
        	
        	<?php
						
foreach ( $report_id as $report_id_key => $report_id_val ) {
							$reportid = $report_id_val ['report_id'];
						}
						
						?>
            <?php foreach($fraude_reports as $fraude_report_key=>$fraude_report_val): ?>
                <article class="fraud-item">
                	<?php echo form_open('fosareport/show_fraud_home_page');?>
                    <h4>FOSA: <?php echo $fraude_report_val['entity_name'].'('.$fraude_report_val['geozone_name'].')';?></h4>
				<p><?php
							if (is_null ( $fraude_report_val ['datafile_info'] ) || $fraude_report_val ['datafile_info'] == '[""]') {
								echo "Pas de date spécifié";
							} else {
								
								$datafile_info = explode ( '","', $fraude_report_val ['datafile_info'] );
								$date_Constant = explode ( '["', $datafile_info [0] );
								$date_constant_fraude = $date_Constant [1];
								?>
								 	 
				
				
				<p class="date"><?php echo mdate("%d %M %Y", strtotime($date_constant_fraude));?></p>
								<?php
							}
							?>
							 	
                    </p>
                    <?php $quarter=$fraude_report_val['datafile_quarter'];?>
                    <a
					href="<?php echo base_url().'fosareport/show_fraud_home_page';?>?fraud_report=<?php echo $fraude_report_val['entity_id'];?>&report_id=<?php echo $reportid;?>&level_0=<?php echo $fraude_report_val['level_0'];?>&entity_geozone_id=<?php echo $fraude_report_val['entity_geozone_id'];?>&entity_id=<?php echo $fraude_report_val['entity_id'];?>&datafile_month=<?php echo $fraude_report_val['datafile_month'];?>&datafile_year=<?php echo $fraude_report_val['datafile_year'];?>&datafile_quarters=<?php echo $quarter;?>"><img
					src="<?php echo base_url().'cside/images/pdf-icon.png'?>"
					style="height: 35px; width: 55px" /></a>
                    <?php echo form_close();?>
                </article>
            <?php endforeach;?>
            <?php }?>
            <h3><?php echo $this->lang->line('front_docs_header'); ?></h3>
            <?php foreach($featured_docs as $doc): ?>
                <article class="doc-item-a">
				<h4><?php echo $doc['content_title']; ?></h4>
				<p class="date"><?php echo mdate("%d %M  %Y",  strtotime($doc['content_create_date'])); ?></p>
				<p><?php echo strip_tags(character_limiter($doc['content_description'], 200)); ?></p>
				<p class="more">
					<a
						href="<?php echo site_url().'documents/item/'.$doc['content_id']; ?>">More</a>
				</p>
                    <?php if(isset($doc['content_link'])) : ?>
                        <p class="action">
					<a class="button-a icon-pdf" target="_blank"
						href="<?php echo base_url().'cside/contents/docs/'.$doc['content_link']; ?>">
                                <?php echo $this->lang->line('download').' '.$doc['file_size'];?>
                            </a>
				</p>
                        
                    <?php endif;?>
                </article>
            <?php endforeach;?>
        </div>   
    <?php endif;?>
    </div>

</section>

<script type="text/javascript"
	src="https://maps.googleapis.com/maps/api/js?sensor=false">
 
</script>
<script type="text/javascript"
	src="<?php echo site_url()?>cside/js/infobox.js"></script>
<script>
 
   $(function(){
    
    /*
     * google map
     */
    
    //bootstrap tool tips
    $('[data-toggle="tooltip"]').tooltip({'placement': 'auto'});
    
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
            });
            return bounds;
        };

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
                    
                    shape.push(point);
                }
                shapes.push(shape);
            }
            
            return shapes;
        }
        
        /*
        * function that parses multipolyon GeoJSON data supplied in the argument.
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
     
     //track current mouse position
     var global_client_x = 0;
     var global_client_y = 0;
     
     $('body').on('mousemove','#map_canvas',function(e){
         global_client_x = e.pageX;
         global_client_y = e.pageY;
     });
     
       
     /*
      * Send ajax request to get GeoJSON data. 
      * Parm "2" references region
      */ 
     var site_url = '<?php echo site_url(); ?>';
     console.log(site_url);
     var mapOptions = {
                    center: new google.maps.LatLng(12.262892, -1.000000),
                    zoom: 7,
                    scrollwheel: false,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                 };
                 
    var map = new google.maps.Map(document.getElementById("map_canvas"),mapOptions);
             
     $.ajax({
         'url':site_url+'home/get_geo_json/2',
         'type':'POST',
         'dataType':'json',
         success : function(data) {
             
             var layer = data.layer;      
             $.each(layer,function(index,val){
                               
                 var url = val.url;
                 
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
                        $('#current_region').text(tooltip_text);
                    });
                    
                    google.maps.event.addListener(polygon, 'mouseout', function(){
                       
                        unhighlight(polygon);
                        hide_tooltip("#tooltip");
                        
                    });

                    var bound;
                    
                    if(type=='Polygon') {
                        bound = polygon.my_getBounds().getCenter();
                    }else{
                        if(type=="MultiPolygon") {
                            bound = multi_polygon_center(json);
                        }
                    }  
                    
                    
                    var marker = new google.maps.Marker({
                    position: bound,
                    map: map,
                    icon: getCircle(val.entities/3)
                    });
                    
                    google.maps.event.addListener(marker,'click',function(){
                        window.location = url;
                    });
                    
                    var labelText = '<div style="color: #FFF;font-weight:bold;font-size:12px">'+val.entities+'</div>';

                    var myOptions = {
                        content: labelText,
                        boxStyle: {
                            width : "50px"
                        },
                        disableAutoPan: true,
                        pixelOffset: new google.maps.Size(-8, -10),
                        position: marker.getPosition(),
                        closeBoxURL: "",
                        isHidden: false,
                        enableEventPropagation: false
                    };

                    var label = new InfoBox(myOptions);
                    label.open(map);
                    
                    
                }
            
             });
            
            
         },
         error : function() {
             console.log('an error occured');
             console.log('reloading');
             if(!site_url || site_url=='' || site_url==undefined)
                window.location.reload(true);
         }
         
     });
     
         
   });
   

</script>
