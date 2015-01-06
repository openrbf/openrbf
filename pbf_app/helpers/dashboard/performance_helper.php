<?php 
	function performance_helper(){
		$ci = &get_instance();
		
		$ci->lang->load('dashboard', 'en');
		$ci->lang->load('dashboard', 'fr'); 
		$user_group_id=$ci->session->userdata['usergroup_id'];
		$ci->lang->load('popcible', $ci->config->item('language'));
		/* $ci->lang->load('front', 'fr');
		$ci->lang->load('front', 'en'); */
		$ci->lang->load('popcible', 'fr');
		$ci->lang->load('popcible', 'en');
		
		$real_time_result =$ci->pbf->get_real_time_result_home();
		$entity_types=$ci->pbf->get_number_entities();
        $score_qual=$ci->pbf->get_average_quality();
		$tot_pop=$ci->pbf->get_pop_tot(); //Total de la population couverte du Bénin
		$key_data=$ci->pbf_mdl->get_keydata('');//Detail de la population couverte du Bénin
		//print_r($key_data);echo "<br/>";

		//print_r($score_qual);
		//print_r($entity_types);
		
		$html='';
		
		$series=array();
				$names=array();
				$validated_value=array();
				$tab=array();
                                $s=array();
                                $tpsreal=array();
                                $prev=array();
                                $real_time_date=array();
                                $lastyear_time_date=array();
                                $abrev=array();
                                //echo "<br/>";
				//print_r($real_time_result['data']);
				foreach($real_time_result['data'] as $result){
						/*$realtimes = $result['realtime'];
                        $comparaisons = $result['comparaison'];*/
				
						if(isset($result['comparaison']['pourcentage'])){
						$a=str_replace('%', '', $result['comparaison']['pourcentage']);
						//echo "Pourcentage:".$a." ".$result['realtime']['sum_validated_value']."<br/>";
						array_push($names, $result['realtime']['indicator_common_name']); 
						array_push($series, $a);
						array_push($validated_value, $result['realtime']['sum_validated_value']);
						
						$tab=array($result['realtime']['indicator_common_name']=>$result['comparaison']['pourcentage']);
						
										array_push($s, $result['realtime']['indicator_abbrev']);//Categories
                                        array_push($tpsreal, $result['real']);//series
                                        array_push($prev, $result['previous']);//series
                                        array_push($real_time_date, $result['real_start_date']."/".$result['real_end_date']);
                                        array_push($lastyear_time_date, $result['lastyear_start_date']."/".$result['lastyear_end_date']);
                                        array_push($abrev, $result['realtime']['indicator_abbrev']);
					}
                                       
					
				}
			                            $serie['name']=$abrev;
                                        $serie['data']=array($tpsreal, $prev);
                                        $titre=$ci->lang->line('dash_evolution_indicators');
										//$titre=$ci->lang->line('pop_covered');
                                        $categorie=array($real_time_date, $lastyear_time_date);
								//$graphe='';
										
						//$graphe=chart_bar($serie, $titre, $categorie);
						
						
							
							$html.='<div class="block_head">
											<h2>'.$ci->lang->line('dash_performance').'</h2>
									</div>';
									
						$html.='<div class="block_content">
									<table style="display:inline-block; width:48%; height:400px;">
									<tr><td><strong>'.$ci->lang->line('dashb_entity_type').'</strong></td><td><strong>'.$ci->lang->line('dash_abrev').'</strong></td><td><strong>'.$ci->lang->line('dashb_entity_number').'</strong></td></tr>';
								$total_entity=0;//Nombre total des entités
								$total_pop=0;////Nombre total de la population
				$abbrev_entity_type=array();//Tableau qui conserve les abbréviations de types d'entités se trouvant sur le graphique
								foreach($entity_types as $res_entity):
								//echo $res_entity;
								//print_r($res_entity);
								foreach($res_entity as $value):
								
								$total_pop=$total_pop+round($value['total_pop']);
						
										if(round($value['total_pop']>0)){
										$total_entity=$total_entity+$value['total'];
									array_push($abbrev_entity_type, $value['entity_type_abbrev']);
						/* $html.='<tr><td>'.$value['entity_type_name'].'</td><td>'.$value['entity_type_abbrev'].'</td><td>'.round($value['total_pop']).'</td><td>'.$value['total'].'</td></tr>'; */

						$html.='<tr><td>'.$value['entity_type_name'].'</td><td>'.$value['entity_type_abbrev'].'</td><td>'.$value['total'].'</td></tr>';
								//echo $value['entity_type_id'].": ".$value['entity_type_abbrev'].": ".$value['total']."<br/>";
								}
							endforeach;
							endforeach;
						$html.='<tr><td><strong>Total</strong></td><td></td><td><strong>'.$total_entity.'</strong></td></tr>';

						
							//echo "Entités contractées:".$total."<br/>";
					$html.='</table>'; 
					/* $html.=	'<div><strong>Population:</strong><strong>'.number_format($tot_pop['tot']).'</strong></div>'; */
						if(isset($real_time_result['data']) && !empty($real_time_result['data'])){
						/* 	if($user_group_id==1 OR $user_group_id==2){
					$html.='<div id="container" style="display:inline-block; width:48%; height:300px;">'.$graphe.'</div>';
						} */
					$tab_entity=array();//Tableau qui conserve les abrev des entités
								$tab_average_qual=array();
			//On affiche les scores qualités des types d'entités ayant des entités accessibles à l'utilisateur connecté
								foreach($score_qual as $res){
									foreach($abbrev_entity_type as $abbrev){
										if($res['entity_type_abbrev']==$abbrev){
									/* echo "Abrev:".$res['entity_type_abbrev']." /";
									echo "Average qual:".$res['average_qual']."<br/>"; */
									array_push($tab_entity, $res['entity_type_abbrev']);
									array_push($tab_average_qual, $res['average_qual']);}
										}
								}
								
								$serie_qual['name']=$tab_entity;//On affecte les abbrev des entités aux name
								$serie_qual['data']=$tab_average_qual;//On affecte les scores qualités aux data
						
					$html.='<div id="container" style="display:inline-block; width:45%; height:380px;margin-left:43px">
								
									'.draw_entity($serie_qual, $ci->lang->line('dashb_quality_score'), $tab_entity).'
								
							</div>';
							
	
						}
$html.=	'<div id="container"><h4><strong>'.$ci->lang->line('dash_pop_covered').':</strong><strong>'.number_format($tot_pop['tot'],0,$ci->lang->line('dash_decimal_separator'),$ci->lang->line('dash_thousand_separator')).'</strong></h4><ul class="data-a color">';
		foreach($key_data as $kd){
$html.=	'<li><strong>'.$ci->lang->line('dataelmt_key_'.$kd['popcible_id']).':</strong><span>'.number_format(round($tot_pop['tot']*$kd['popcible_percentage']/100),0,$ci->lang->line('dash_decimal_separator'),$ci->lang->line('dash_thousand_separator')).'</span></li>';
		}
$html.=	'</ul></div>';
					$html.=	'</div><div>';
					
					
					$html.='</div>
                   <div class="bendl"></div>
				<div class="bendr"></div>
				';	
		return $html;	
			
	}
        
           	
	  function chart_bar($serie, $titre, $categorie){
		$ci = &get_instance();
		$ci->load->library('highcharts');
		
		$plot->bar->dataLabels->enabled =false;
		$credits->enabled = false;
        $callback = "function() { return '<b>'+ this.point.name +'</b>: '+ this.y +'%'}";
		$tool->formatter = $callback;
		//$plot->bar->dataLabels->formatter = $callback;
		$ci->highcharts->set_type('bar');
		
		
		//Tableau qui conserve les périodes
		$tabPeriode=array();
		for($j=0; $j<count($categorie[$j]);$j++){
			for($i=0; $i<count($categorie[$j][0]); $i++){
			//echo 'Cat:'.$categorie[$j][0]."<br/>";
			array_push($tabPeriode,$categorie[$j][0]);
			}
		} 

		$ci->highcharts->set_xAxis(array('categories'=>$tabPeriode));
		
		
				$d=array();
                foreach ($serie['name'] as $res){
				  array_push($d, $res);
				  }     
					 $data1['data']=array();
					 $data2['data']=array();
					 $data3['data']=array();
					 $data4['data']=array();

					
					$data1['name']=$d[0]; 
						for($i=0; $i<count($serie['data'][$i]); $i++){
							// echo "Data 1:".$serie['data'][$i][0]."<br/>";
							 array_push($data1['data'],  round($serie['data'][$i][0]));
						}
					
					$data2['name']=$d[1];	
						for($i=0; $i<count($serie['data'][$i]); $i++){
							// echo "Data 2:".$serie['data'][$i][1]."<br/>";
							 array_push($data2['data'],  round($serie['data'][$i][1]));
						}
				
					$data3['name']=$d[2];
						for($i=0; $i<count($serie['data'][$i]); $i++){
							 //echo "Data 3:".$serie['data'][$i][1]."<br/>";
							 array_push($data3['data'],  round($serie['data'][$i][2]));
						}
						
					$data4['name']=$d[3];
						for($i=0; $i<count($serie['data'][$i]); $i++){
							 //echo "Data 4:".$serie['data'][$i][1]."<br/>";
							 array_push($data4['data'],  round($serie['data'][$i][3]));
						}			   
				$ci->highcharts->set_serie($data1);
				$ci->highcharts->set_serie($data2);
				$ci->highcharts->set_serie($data3);
				$ci->highcharts->set_serie($data4); 
				
		
		$ci->highcharts->set_title($titre);
		$ci->highcharts->set_credits($credits);
		
		//$ci->highcharts->set_dimensions('280','300');
		
		//$this->highcharts->render_to('graph_div');
		return $ci->highcharts->render();
	}

		function draw_entity($serie, $titre, $tab_entity){
		$ci = &get_instance();
		$ci->load->library('highcharts');
		
		$plot->bar->dataLabels->enabled =false;
		$credits->enabled = false;
        $callback = "function() { return '<b>'+ this.point.name +'</b>: '+ this.y +'%'}";
		$tool->formatter = $callback;
		//$plot->bar->dataLabels->formatter = $callback;
		$ci->highcharts->set_type('bar');
		//$ci->highcharts->set_xAxis(array('categories'=>array($categorie[0][0], $categorie[0][1])));
		//$ci->highcharts->set_xAxis(array('categories'=>array('CSC', 'CSA', 'DI', 'HZ', 'MI')));
		$ci->highcharts->set_xAxis(array('categories'=>$tab_entity));
				/* echo "<br/><br/>";
				print_r($serie['data']);
				echo "<br/>"; */
				
					//$data[$j]['name']=array();
				  for($j=0; $j<count($tab_entity); $j++){
					$data[$j]['name']=$tab_entity[$j];
					//echo "Name ".$j.":".$data[$j]['name']."<br/>";
				} 
				
				for($t=0; $t<count($serie['data']); $t++){
					//$data[$t]['data']=$serie['data'][$t];
					$data[$t]['data']=array(0, 0, 0, 0, 0);
					$data[$t]['data'][$t]=round($serie['data'][$t]);
					//echo "Data ".$t.":".$data[$t]['data'][$t]."<br/>";
				} 
		
		for($h=0; $h<count($data); $h++){
			//print_r($data[$h]);
			$ci->highcharts->set_serie($data[$h]); 
		} 
					
		$ci->highcharts->set_title($titre);
		$ci->highcharts->set_credits($credits);
			
		return $ci->highcharts->render();
	}
	
?>