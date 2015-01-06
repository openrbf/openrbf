<?php
    function completude_infofosa_helper($geozone_id=0){		
		$ci = &get_instance();
		$ci->load->model('entities_mdl');
		$ci->load->model('geo_mdl');
		//$ci->lang->load('hfrentities', $ci->config->item('language'));
		 
		$data = get_geozone_entities_settings($geozone_id,'region');	
		
		if($geozone_id==0)
		{
			$geozone_id=$data['list'][0]['geozone_id'];
		}
		
		$geozone_links="";
		$geozone_title=array('#',
                                        $ci->lang->line('compl_region'),
                                       // $ci->lang->line('compl_tot_entite'),
                                        $ci->lang->line('compl_geo'),
                                        $ci->lang->line('compl_photo'),
                                        //$ci->lang->line('compl_population'),
                                        'Pop',
                                        $ci->lang->line('compl_statut'),
                                        //$ci->lang->line('compl_responsable'),
                                        'Resp',
                                        $ci->lang->line('compl_email'),
                                        //$ci->lang->line('compl_tel'),
                                       // $ci->lang->line('compl_banque'),
							//'%'
							);
		$district_title=array('#',
							District,
						//	$ci->lang->line('compl_tot_entite'),
							$ci->lang->line('compl_geo'),
							$ci->lang->line('compl_photo'),
							$ci->lang->line('compl_population'),
							$ci->lang->line('compl_statut'),
							$ci->lang->line('compl_responsable'),
							$ci->lang->line('compl_email'),
							$ci->lang->line('compl_tel'),
							$ci->lang->line('compl_banque'),
							'%'
							);
		$entities_title=array('#',
							'FOSA',
							$ci->lang->line('entity_district'),
							$ci->lang->line('compl_geo'),
							$ci->lang->line('compl_photo'),
							$ci->lang->line('compl_population'),
							$ci->lang->line('compl_statut'),
							$ci->lang->line('compl_responsable'),
							$ci->lang->line('compl_email'),
							$ci->lang->line('compl_tel'),
							$ci->lang->line('compl_banque')
							);
		//$data['district']=array();
		//$brice=geo_mdl->get_parent_geozone($geozone_id);
/*
 echo "<pre>"	;
print_r($data)	;
echo "</pre>"	; */


		foreach($data['list'] as $k=>$v){
			if( $geozone_id!=$data['list'][$k]['geozone_id']){
			
				 if(count($ci->session->userdata('usergeozones'))<=0)
				 {
					 $geozone_links.=anchor('/hfrentities/completude/'.$data['list'][$k]['geozone_id'],$data['list'][$k]['geozone_name'])." &nbsp;&nbsp;| &nbsp;&nbsp;";
				 }else
				 {
					$linkDispo=0;
					 foreach( $ci->session->userdata('usergeozones') as $sK=>$sV)
					 {
							$parent=$ci->geo_mdl->get_parent_geozone($sV);
							$parentId=$parent['geozone_id'];
							if (($parentId==$data['list'][$k]['geozone_id']) and $linkDispo==0)
							{
								 $geozone_links.=anchor('/hfrentities/completude/'.$data['list'][$k]['geozone_id'],$data['list'][$k]['geozone_name'])." &nbsp;&nbsp;| &nbsp;&nbsp;";
								 $linkDispo=1;
							}
					
					 }
				 }
				
			}else
			{
				
				$filename=FCPATH.'cside/exports/'.$data['exports_file_name'].'.xlsx';
				$download_link = base_url().'cside/exports/'.$data['exports_file_name'].'.xlsx';
								 
				 if(count($ci->session->userdata('usergeozones'))<=0)
				 {
					$geozone_links.="<span style='font-size:1.3em;text-decoration:underline'>".$data['list'][$k]['geozone_name']."</span> | ";
				 }else
				 {
					$linkDispo=0;
					 foreach( $ci->session->userdata('usergeozones') as $sK=>$sV)
					 {
							$parent=$ci->geo_mdl->get_parent_geozone($sV);
							$parentId=$parent['geozone_id'];
							if (($parentId==$data['list'][$k]['geozone_id']) and $linkDispo==0)
							{
								$geozone_links.="<span style='font-size:1.3em;text-decoration:underline'>".$data['list'][$k]['geozone_name']."</span> | ";
								 $linkDispo=1;
							}
					
					 }
				 }
				
				
				
				//$data['export_link']=anchor('/cside/export/liste_fosa_region'.$data['list'][$k]['geozone_id'].'.xlsx','liste FOSA_'.$data['list'][$k]['geozone_name'].'.xls');
				
				$data['export_link'].=(file_exists($filename))?anchor('/hfrentities/export_list_fosa/'.$data['list'][$k]['geozone_id'],' Create updated FOSA list '.'.xls '):anchor('/hfrentities/export_list_fosa/'.$data['list'][$k]['geozone_id'],' Create FOSA list '.'.xls');
				
				//$data['export_link'].=(file_exists($filename))?anchor('/hfrentities/export_list_fosa/'.$data['list'][$k]['geozone_id'],' Create updated FOSA list '.$data['list'][$k]['geozone_name'].'.xls '):anchor('/hfrentities/export_list_fosa/'.$data['list'][$k]['geozone_id'],' Create FOSA list '.$data['list'][$k]['geozone_name'].'.xls');
				
				//$data['export_link'].=(file_exists($filename))? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp'.$ci->pbf->rec_op_icon('download_record',$download_link):'';
				$data['export_link'].=(file_exists($filename))? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp'.$ci->pbf->rec_op_icon('download_excel',$download_link):'';
			}
			 if(count($ci->session->userdata('usergeozones'))<=0)
				 {
				 $cond=" ";
				 }else
				 {
					$cond = "AND ( ";
					 foreach( $ci->session->userdata('usergeozones') as $sK=>$sV)
					 {
							$parent=$ci->geo_mdl->get_parent_geozone($sV);
							$parentId=$parent['geozone_id'];
							if ($sK !=0)
							{
								$cond.=" OR ";
							}
							$cond.="geozone_parentid=$parentId ";
					 }
					$cond .= " ) ";
				 }
			$result_district=get_geozone_entities_settings($data['list'][$k]['geozone_id'],'district');
			
			//$data['district'][$geo_Id]=array();
			
			//$data['list'][$k]['geozone_name']=anchor('/hfrentities/completude/'.$data['list'][$k]['geozone_id'],$data['list'][$k]['geozone_name']);
			foreach( $result_district['list'] as $u=>$w)
			{
				$result_district['list'][$u]['geozone_id']=$u+1;
			}
			$data['list'][$k]['geozone_name']=anchor('/hfrentities/completude/'.$data['list'][$k]['geozone_id'],$data['list'][$k]['geozone_name']);
			//$data['list'][$k]['geo']='<a class="plus" style="color:green; cursor:pointer;">'.$data['list'][$k]['geo']."</a>";
			
			
			//$data['list'][$k]['geozone_id']=$k+$preps['offset']+1;
			
			$geo_Id=$data['list'][$k]['geozone_id'];
			
			$data['district'][$geo_Id]=$result_district['list'];
			
			array_unshift($data['district'][$geo_Id],$district_title);
			array_unshift($data['district'][$geo_Id],$district_title);
			
			
		}
		
				foreach($data['detail'] as $k=>$v){
		
			$data['detail'][$k]['entity_name']=anchor('/hfrentities/editentity/'.$data['detail'][$k]['entity_id'],$data['detail'][$k]['entity_name']);
			$data['detail'][$k]['entity_id']=$k+$preps['offset']+1;
			
			foreach($data['detail'][$k] as $i=>$j)
			{
				if($j=='V'){
					$data['detail'][$k][$i]="<span style='color:green; display:block; text-align:center'>V</span>";
				}elseif($j=='X'){
					$data['detail'][$k][$i]="<span style='color:red; display:block; text-align:center'>X</span>";
				}
			} 
		}
			
		//	array_unshift($data['list'],$geozone_title);
			
			array_unshift($data['detail'],$entities_title);
			
			$tmpl = array ( 'table_open' => '<table id="table_fosacompleteness" style="font-size:0.9em" border="0" cellpadding="0" cellspacing="0">', 'row_start' => '<tr class="even">', 'row_end' => '</tr>', 'row_alt_start' => '<tr class="odd">', 'row_alt_end' => '</tr>', 'table_close' => '</table>' );

			$ci->table->set_template($tmpl);
			$table=$ci->table->generate($data['list']);
			
/*
	echo "<pre>";
	print_r($data['list']);
	echo "</pre>";*/

			$html='
				<div class="block">
				<div class="block_head">
								
					<h2>'.anchor('hfrentities/completude/',$ci->lang->line('completude_title')).'</h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">'.
				
				//$table.
				'<table id="table_fosa_completeness">'.
				'<tr>';
			foreach ($geozone_title as $key => $value) {
				$html.='<th>'.$value.'</th>';
			}
				
		$html.=		'<tr >';
		$i=1;
		$cliquable='class="plus"; style="color:green; cursor:pointer"';
		foreach ($data['list'] as $k => $v) {
				$html.='<tr id="'.$v["geozone_id"].'">';
			
				
				$html.='<td >'.$i.'</td>';
				$html.='<td >'.$v['geozone_name'].'</td>';
				
				$tab=explode('/', $v['geo']);
				$style=($tab['0'] < $tab['1'])? $cliquable:''; 
				$html.='<td '.$style.' abbr="geo">'.$v['geo'].'</td>';
				
				$tab=explode('/', $v['photo']);
				$style=($tab['0'] < $tab['1'])? $cliquable :''; 
				$html.='<td '.$style.' abbr="photo">'.$v['photo'].'</td>';
				
				$tab=explode('/', $v['population']);
				$style=($tab['0'] < $tab['1'])? $cliquable :''; 
				$html.='<td '.$style.' abbr="pop">'.$v['population'].'</td>';
				
				$tab=explode('/', $v['status']);
				$style=($tab['0'] < $tab['1'])? $cliquable :''; 
				$html.='<td  '.$style.' abbr="status">'.$v['status'].'</td>';
				
				$tab=explode('/', $v['responsable']);
				$style=($tab['0'] < $tab['1'])? $cliquable :''; 
				$html.='<td  '.$style.' abbr="resp">'.$v['responsable'].'</td>';
				
				$tab=explode('/', $v['mail']);
				$style=($tab['0'] < $tab['1'])? $cliquable :''; 
				$html.='<td  '.$style.' abbr="mail">'.$v['mail'].'</td>';
					
				
				
				$html.='</tr>';
			$i++;
			
		}
		
		$html.=		'</table>'.
								
				
				'
				<h3 style="text-align:right">'.anchor('hfrentities/completude/',$ci->lang->line('dashb_plus_detail').'...').'</h3>
				</div>		<!-- .block_content ends -->
					<div class="bendl"></div>
					<div class="bendr"></div>
				</div>
		';
				
	return $html;	
}	
		
	function get_geozone_entities_settings($geozone_id,$type='region'){

		$ci = &get_instance();
		$ci->load->model('entities_mdl');
		$ci->load->model('geo_mdl');
		$ci->lang->load('hfrentities', $ci->config->item('language'));

		 if(count($ci->session->userdata('usergeozones'))<=0)
				 {
				 $cond=" ";
				 }else
				 {
					$cond = "AND ( ";
					 foreach( $ci->session->userdata('usergeozones') as $sK=>$sV)
					 {
							$parent=$ci->geo_mdl->get_parent_geozone($sV);
							$parentId=$parent['geozone_id'];
							if ($sK !=0)
							{
								$cond.=" OR ";
							}
							if($type=='region')
							{
								$cond.="geozone_id=$parentId ";
							}else
							{
								$cond.="geozone_id=$sV ";
							}
					 }
					$cond .= " ) ";
				 }
		
		//echo "<h2> $cond</h2>";

		$list_of_regions=$ci->entities_mdl->get_geozone_compl($geozone_id,$type,$cond);//recuperation de la liste des régions
		
		$resultat=array();// contient la liste repitulative des region
		$resultat_det=array();//Contient le detail des information pour les entitéss de la region selectionné
		$detail=array();
		
		
		$yes="V";
		$no="X";
		
		//Si aucune region n'est selectionn� c'est on va afficher le detail de la premiere region dans la liste
		if($geozone_id==0)
		{
			$geozone_id=$list_of_regions[0]['geozone_id'];
		}			
				
		foreach($list_of_regions as $k=>$v){//verification des informations  pour chaque region
		
			
		
				$curr_geozone_id=$list_of_regions[$k]['geozone_id'];
				
				if( $geozone_id == $curr_geozone_id){
					$record_set['exports_title']='Liste FOSA Region '.$list_of_regions[$k]['geozone_name'];
					$record_set['exports_file_name']='liste_fosa_region_'.$list_of_regions[$k]['geozone_name'];
				
				}
				
				 if(count($ci->session->userdata('usergeozones'))<=0)
				 {
				 $cond=" ";
				 }else
				 {
					$cond = "AND ( ";
					 foreach( $ci->session->userdata('usergeozones') as $sK=>$sV)
					 {
							$parent=$ci->geo_mdl->get_parent_geozone($sV);
							$parentId=$parent['geozone_id'];
							if ($sK !=0)
							{
								$cond.=" OR ";
							}
							$cond.="geozone_id=$sV ";
					 }
					$cond .= " ) ";
				 }
				
				$entites=$ci->entities_mdl->get_region_entities($curr_geozone_id,$type,$cond);
				$totalEntities=	$entites['records_num'];
				
				//Initialisation des information existante pour chaque region � 0
					$geo=0;
					$photo=0;
					$population=0;
					$status=0;
					$responsable=0;
					$mail=0;
					$tel=0;
					$banque=0;
					
					foreach($entites['list'] as $i=>$j){//verification des info pour chaque entite
					if($curr_geozone_id==$geozone_id) // Si c'est la region selection, intialisation  des information existant pour chaque entite � X
					{
						$geo_det=$no;
						$photo_det=$no;
						$population_det=$no;
						$status_det=$no;
						$responsable_det=$no;
						$mail_det=$no;
						$tel_det=$no;
						$banque_det=$no;
					}
					//echo "<p>$i".."</p>"
						if(($entites['list'][$i]['entity_geo_lat']!=0) AND ($entites['list'][$i]['entity_geo_long']!=0))
						{
							$geo++; //Si l'information est presente(pour le moment GEO ), on incremente sa valeur pour la region en cours
							
							if($curr_geozone_id==$geozone_id) $geo_det=$yes; //Si l'information est presente et que c'est la region selectionn�, on on marque que l'info est presente pour l'entite en question
						}
						
						if(($entites['list'][$i]['entity_picturepath'] != NULL))
						{
							$pictures=scandir(FCPATH.'cside/images/portal/');
							$NomImage=$entites['list'][$i]['entity_picturepath'].'_big.jpg';
							if(in_array($NomImage, $pictures))
							{
								$photo++;
								if($curr_geozone_id==$geozone_id) $photo_det=$yes;
							}
							
						}
						
						if(($entites['list'][$i]['entity_pop']))
						{
							$population++;
							if($curr_geozone_id==$geozone_id) $population_det=$yes;
						}
						
						if(($entites['list'][$i]['entity_status']))
						{
							$status++;
							if($curr_geozone_id==$geozone_id) $status_det=$yes;
						}
						if(($entites['list'][$i]['entity_responsible_name'] != NULL))
						{
							$responsable++;
							if($curr_geozone_id==$geozone_id) $responsable_det=$yes;
						}
						if(($entites['list'][$i]['entity_responsible_email'] != NULL))
						{
							$mail++;
							if($curr_geozone_id==$geozone_id) $mail_det=$yes;
						}
						if(($entites['list'][$i]['entity_phone_number'] != NULL))
						{
							$tel++;
							if($curr_geozone_id==$geozone_id) $tel_det=$yes;
						}
						
						if(($entites['list'][$i]['entity_bank_acc']) AND ($entites['list'][$i]['entity_bank_id']))
						{
							$banque++;
							if($curr_geozone_id==$geozone_id) $banque_det=$yes;
						}
						
			if($curr_geozone_id==$geozone_id) {
			//ajout des valeurs dans le tableau des information des entit�s de la region selectionn�
				$temp_det=array(
					'entity_id'=> $entites['list'][$i]['entity_id'],
					'entity_name'=> $entites['list'][$i]['entity_name'],
					'district_name'=> $entites['list'][$i]['geozone_name'],
					'geo'=> $geo_det,
					'photo'=> $photo_det,
					'population'=> $population_det,
					'status'=> $status_det,
					'responsable'=> $responsable_det,
					'mail'=> $mail_det,
					'tel'=> $tel_det,
					'banque'=> $banque_det,
				);
			array_push($resultat_det,$temp_det);
						
				}	
			}
			
			//ajout des valeurs dans le tableau des information globales des region 
			$pourcentage=number_format((($banque+$tel+$mail+$responsable+$status+$population+$photo+$geo)/($totalEntities*8)*100),0,'.',' ');
			//$pourc=$data['list'][$k]['pourcentage'];
			if ($pourcentage >= $niveau1)
			{
				$couleur='green';
			}elseif($pourcentage < $niveau1 AND $pourcentage >= $niveau2)
			{
				$couleur='turquoise';
			}elseif($pourcentage < $niveau2 AND $pourcentage >= $niveau3)
			{
				$couleur='orange';
			}else{
			
				$couleur='red';
			}
			$pourcentage="<span style='color:$couleur'>".$pourcentage."</span>";
			$temp=array(
				'geozone_id'=> $curr_geozone_id,
				'geozone_name'=> $list_of_regions[$k]['geozone_name'],
				//'tot_entities'=> $totalEntities,
				'geo'=> $geo."/".$totalEntities,
				'photo'=> $photo."/".$totalEntities,
				'population'=> $population."/".$totalEntities,
				'status'=> $status."/".$totalEntities,
				'responsable'=> $responsable."/".$totalEntities,
				'mail'=> $mail."/".$totalEntities,
				//'tel'=> $tel."/".$totalEntities,
			//	'banque'=> $banque."/".$totalEntities,
				//'pourcentage'=> $pourcentage,
				'parentId'=> $list_of_regions[$k]['geozone_parentid']
				
			);
				
				
			array_push($resultat,$temp);	
			}
		
                        $record_set['list'] = $resultat;
                        $record_set['detail'] = $resultat_det;
			
                    return $record_set;
		
		}
		

		