<?php

    function resume_performance_helper() {
    	
    	$ci = &get_instance();
		$real_time_result =$ci->pbf->get_real_time_result_home();
		
		$entity_types=$ci->pbf->get_number_entities();
		$score_qual=$ci->pbf->get_average_quality();      
		  
		 // print_r($score_qual);
		 		  
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
						//array_push($tab, $result['realtime']['indicator_common_name']=>$result['comparaison']['pourcentage']);
						//$tab=array($result['realtime']['indicator_common_name']=>$result['comparaison']['pourcentage']);
					//echo " Abbréviation:".$result['realtime']['indicator_abbrev']."Indicateur:".$result['realtime']['indicator_common_name']." Real start date:".$result['real_start_date']." End start date:".$result['real_end_date']."  Real:".$result['real']."  Last year start date:".$result['lastyear_start_date']." Last year End date:".$result['lastyear_end_date']." Last:".$result['previous']."<br/>";
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
                                        $titre='Evolution des indicateurs clés';
                                        $categorie=array($real_time_date, $lastyear_time_date);
										
						//$graphe=draw_bar($serie, $titre, $categorie);
					  
                                         // $graphe=chart_bar($tpsreal, $prev, 'Score qualité des entités en %', $s);

		   if(isset($real_time_result['data']) && !empty($real_time_result['data'])){

    $html.='<div class="block"> 
    <div class="block_head">
								
					<h2>'.$ci->lang->line('dashb_realtime_result').'</h2>
				</div>		<!-- .block_head ends -->
				
    <div class="block_content">
    <table>';
                        foreach ($real_time_result['data'] as $result ) : 
                                $realtime = $result['realtime'];
                                $comparaison = $result['comparaison'];
                            
                          $html.='<tr> 
                          <td>
                                           <img style="height:30px; width:30px" class="img-responsive img-realtime-result" src="'. site_url().'cside/images/portal/'.$realtime['indicator_icon_file'].'"/>
                                                
                                        </td>
                                        <td>'.
                                                $realtime['indicator_common_name'].

                                            '</td>
                                            <td >'.
                                                 '<h4 style="text-align:right">'.
                                                 number_format($realtime['sum_validated_value']).
                                                 '</h4>'.

                                                
                                            '</td>';
                                        
                                             if (isset($comparaison)){
                                               $html.= '<td><h3 class="pourcentage">'.$comparaison['pourcentage'].'</h3></td>';                                            
                                           
											if($comparaison['icon']!=''){
											 $html.=   '<td><img class="img-responsive icone-evolution" src="'.site_url().'cside/images/'.$comparaison['icon'].'"></td>';	
											}
                                              
                                              
                                        }
                                            
                                     $html.='</tr>';
                        endforeach;
						
					$html.='</table>';
										
							
					$html.=	'</div><div>';
					
					
					$html.='</div>
                   <div class="bendl"></div>
				<div class="bendr"></div></div>';    
        

}
				
			return $html;	
}
        
           	
	  function draw_bar($serie, $titre, $categorie){
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
		
		$ci->highcharts->set_dimensions('280','300');
		
		//$this->highcharts->render_to('graph_div');
		return $ci->highcharts->render();
	}

		function draw_bar_entity($serie, $titre, $tab_entity){
		$ci = &get_instance();
		$ci->load->library('highcharts');
		
		$plot->bar->dataLabels->enabled =false;
		$credits->enabled = false;
        $callback = "function() { return '<b>'+ this.point.name +'</b>: '+ this.y +'%'}";
		$tool->formatter = $callback;
		//$plot->bar->dataLabels->formatter = $callback;
		$ci->highcharts->set_type('bar');
		
		$ci->highcharts->set_xAxis(array('categories'=>$tab_entity));
				
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

