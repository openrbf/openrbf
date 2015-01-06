<?php
function bonus($params,$invoice_params = null)
		{
			if( empty($params['datafile_year'])  OR empty($params['entity_geozone_id'])  ){
				echo "Selection incomplete";
				exit;
			}
				
			$ci = get_instance();
			$config = $ci->report_mdl->get_reports_conf($ci->input->post('report_id'));
			$lang=$ci->config->item('language_abbr');
			$ci->load->library(array('dpdf','numbers/numbers_words'));
			$ci->dpdf->folder('./cside/pdfs/');
			$ci->dpdf->filename(str_ireplace(' ','',$config['report_title']).'.pdf');			
			$ci->dpdf->paper('a4', $config['report_page_layout']); 
			$ci->table->set_template($ci->dpdf->table_tmpl); 
			
			$ci->dpdf->content='';
			
			$ci->dpdf->content .="<div style='width:95%; margin:auto'>
				<style type='text/css'>
				*{
					font-size:11px;
				}
				table{
					width:100%;
				}
				.tb_header td{
					font-weight:bolder;
					font-size:12px;
				}
				
				#tb_content{
						border-collapse:collapse;
						margin-top:30px;
						border:1px solid black;
				}
				#tb_content td{
						border:1px solid black;
						border-left:0px ;
						border-right:0px ;
						padding:2px;
				}
				#tb_content th{
					padding-top:4px;	
					padding-bottom:4px;	
				}
				.gauche{
					text-align:left;
					
				}
				.droite{
					text-align:right;
					padding-right:10px;
					
				}
				.categorie td{
					padding-top:5px;
					background-color:gray;
					font-weight:bolder;
				}
				.total td{
					padding-top:5px;
					background-color:gray;
					font-weight:bolder;
					font-size:13px;
				}
				.indicateur{
					width:50%;
					padding-left:10px;
					text-align:justify;
				}
				.signataire{
					width:100%;
				}
				.signataire td{
					padding:20px;
					width:49%;
				}
				
				.resume{
					font-size:1.2em;
					font-weight:bolder;
					border-bottom:1px solid black;
				}
				.num{
					width:30px;
				}
				#tb_logo td img{
					height:80px;
				}
				
				</style>";
			
			$ci->load->model(array('entities_mdl'));
			
		
			$ci->dpdf->content .=utf8_decode("
			<table id='tb_logo'><tr>
			<td class='gauche' colspan='2'><img src='".base_url()."cside/images/LogoOrbf.png'/></td>
			
			<tr></table>
			");	
			
			
			$sql="SELECT G.geozone_name AS district,P.geozone_name AS region from pbf_geozones G JOIN pbf_geozones P ON (G.geozone_parentid=P.geozone_id) WHERE G.geozone_id=".$params['entity_geozone_id'] ;
			$geozone=$ci->db->query($sql)->row_array();
		
			
	$ci->dpdf->content .= utf8_decode('	
<table width="100%" border="0" cellspacing="0" cellpadding="0" class>
  
  <tr>
    <td><strong>REGION : </strong>'.$geozone['region'].'</td>
  </tr>
  <tr>
    <td><strong>DISTRICT SANITAIRE: </strong>'.$geozone['district'].'</td>
  </tr>
  
  <tr><td><strong>ANNEE:</strong> '.$params['datafile_year'].'</td>
  
  </tr></table></p><br/><br/>');
  
		 $ci->dpdf->content .= utf8_decode('<div style="padding:3px; border:0px solid black;"><h1 style="text-align:center ;  border-bottom: solid black 0px; padding:5px; margin:auto;font-size:17px;">'.$params['report_title'].'</h1></div><br/><br/>');
			
			////////////////////
			
		$sql="SELECT pbf_entities.entity_name,pbf_datafile.datafile_total FROM pbf_datafile,pbf_entities,pbf_entitytypes WHERE pbf_datafile.entity_id=pbf_entities.entity_id AND pbf_entities.entity_type=pbf_entitytypes.entity_type_id AND  pbf_entities.entity_active ='1'  AND  pbf_datafile.filetype_id =25 AND pbf_entities.entity_geozone_id=".$params['entity_geozone_id']."  AND pbf_datafile.datafile_year=".$params['datafile_year']." ORDER BY pbf_entities.entity_name ASC";
			
		$bonus = $ci->db->query($sql)->result_array();	
		
		$ci->dpdf->content.= utf8_decode("
		<table id='tb_content'>
		<tr><th class='droite num'>#</th>
			<th class='gauche indicateur'>Entité</th>
			<th class='droite'>Bonus (FCFA)</th>
		</tr>
		
		");

			$totBonus=0;
			$i=1;
		
			  foreach ($bonus as $k=>$row) {
	
			  	$ci->dpdf->content.= "<tr>
			  	<td class='droite'>".$i."</td>
			  	<td class='indicateur' >".utf8_decode($row["entity_name"])."</td>
			  	<td class='droite' >".number_format($row["datafile_total"],0)."</td>
			  	</tr>";					  		
			  	$i++;
				$totBonus+=$row['datafile_total'];
			  }
         		
         	
		$ci->dpdf->content.= utf8_decode("
			 	<tr class='total'><td></td><td>Total</td>
			 	<td class='droite'>".number_format($totBonus,0)."</td>
			 	</tr>
			 	");
		
		//table footer
		
		
       	$ci->dpdf->content.="</table>";	
					/////////////////////////
		$signatories = json_decode($params['report_signatories'],true);
			
			$sign1 = $signatories[0];
						$sign2 = $signatories[1];
						$sign3= $signatories[2];
			
		$signatures="<br/><br/><table><tr>
				<td>".$sign1."</td>
				<td>".$sign2."</td>
				<td>".$sign3."</td>
				
			</tr></table>";
		
		
		$ci->dpdf->content.= utf8_decode($signatures);
			$ci->dpdf->html($ci->dpdf->content);
		
	if ($params['origin']!=='cron_job'){
			if (!empty($results)){
				$file = './cside/reports/report_'.time().'.pdf';
				$ci->dpdf->create_and_save_form($file);
			}else{
				$ci->dpdf->content .= '<p>&nbsp;</p><p>';
				$ci->dpdf->content .= utf8_decode($ci->lang->line('report_missing_data'));
				$ci->dpdf->content .='</p><br><br>';
				$ci->dpdf->html($ci->dpdf->content);
				$file = './cside/reports/report_'.time().'.pdf';
				$ci->dpdf->create_and_save_form($file);
			}
		
		}else{
			$file = './cside/reports/'.$params['report_category'].'/'.$params['file_name'];
			$invoice_params['total_invoice']=$total;
			$file_path='./cside/reports/'.$params['report_category'].'/'.$params['file_name'];
			if (file_exists($file_path) && $ci->invoices_mdl->exists($params['file_name'])){
				$ci->invoices_mdl->update($invoice_params);
			}else{
				$ci->invoices_mdl->create($invoice_params);
			}
			
			$automate_report_generation = $ci->config->item ( 'auto_report_generation' );
			if ($automate_report_generation == '1'){
				$ci->dpdf->create_and_save($file);
			}
		}
	}   

