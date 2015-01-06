<?php

	function synthese($params,$invoice_params = null)
		{
			if(!$params['report_id'] OR !$params['entity_geozone_id'] OR !$params['datafile_month'] OR !$params['datafile_year'] ){
				echo "Veuillez choisir tous les champs";exit;
			}
					
			$ci = get_instance();
			$config = $ci->report_mdl->get_reports_conf($ci->input->post('report_id'));
			
			$ci->load->library(array('dpdf','numbers/numbers_words'));
			$ci->dpdf->folder('./cside/pdfs/');
			$ci->dpdf->filename(str_ireplace(' ','',$config['report_title']).'.pdf');			
			$ci->dpdf->paper('a4', $config['report_page_layout']); 
			$ci->table->set_template($ci->dpdf->table_tmpl); 
			$ci->load->model(array('entities_mdl'));
			$ci->load->model(array('invoices_mdl'));
			
			$sql="SELECT G.geozone_name AS district,P.geozone_name AS region from pbf_geozones G JOIN pbf_geozones P ON (G.geozone_parentid=P.geozone_id) WHERE G.geozone_id=".$params['entity_geozone_id'] ;
			$geozone=$ci->db->query($sql)->row_array();
			
			$signatories = json_decode($params['report_signatories'],true);
			$sign1 = $signatories[0];
			$sign2 = $signatories[1];
			$sign3= $signatories[2];
			
			
		$sql = "SELECT pbf_indicatorsfileypes.order AS '#',pbf_indicatorstranslations.indicator_title AS 'Indicateur',SUM(pbf_datafiledetails.indicator_claimed_value) AS 'Declaree',SUM(pbf_datafiledetails.indicator_verified_value) AS 'Verifiee',FORMAT(pbf_datafiledetails.indicator_tarif,0) AS 'Bareme',SUM(pbf_datafiledetails.indicator_montant) AS 'Montant' FROM pbf_indicators JOIN pbf_indicatorstranslations ON (pbf_indicators.indicator_id=pbf_indicatorstranslations.indicator_id) JOIN pbf_datafiledetails ON (pbf_indicators.indicator_id=pbf_datafiledetails.indicator_id) JOIN pbf_datafile ON (pbf_datafiledetails.datafile_id=.pbf_datafile.datafile_id) JOIN pbf_indicatorsfileypes ON (pbf_indicators.indicator_id = pbf_indicatorsfileypes.indicator_id AND pbf_datafile.filetype_id=pbf_indicatorsfileypes.filetype_id) JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) JOIN pbf_lookups ON (pbf_filetypes.filetype_contenttype=pbf_lookups.lookup_id AND pbf_lookups.lookup_linkfile='content_type') WHERE pbf_indicatorstranslations.indicator_language='fr' AND pbf_lookups.lookup_title='Quantity' AND  pbf_datafile.datafile_month='".$params['datafile_month']."' AND pbf_datafile.datafile_year='".$params['datafile_year']."' AND (LAST_DAY('".$params['datafile_year']."-".$params['datafile_month']."-1') BETWEEN pbf_indicatorsfileypes.use_from AND pbf_indicatorsfileypes.use_to) GROUP BY pbf_indicators.indicator_id ORDER BY pbf_indicatorsfileypes.order";
			
		$results = $ci->db->query($sql)->result_array();
		$ci->dpdf->content='';		
		$ci->dpdf->content .="<div style='width:95%; margin:auto'>
		<style type='text/css'>
		*{
			font-size:13px;
		}
		.tb_contenu{
			border-collapse:collapse;
			
		}
		.tb_contenu td,.tb_contenu th{
			border:1px solid black;
			padding:8px;
			padding-top:5px;
			padding-bottom:5px;
		}
		.gauche{
			text-align:left;
		}
		
		.centre{
			text-align:center;
		}
		
		.droite{
			text-align:right;
		}
		table.signature td{
			width:50%;
		}
		.tb_logo{
			width:80%;
		}
		.tb_logo td h1{
			
			font-size:18px;
		}
		
		.tb_logo img{
			
			height:80;
			width:80;
		}
		.td_indicateur{
			width:50%;
		}
		
		.td_num{
			width:2%;
		}
		
		.td_val{
			width:24%;
		}
				
		</style>
		";
		
		$ci->dpdf->content .= utf8_decode('
		<table class="tb_logo">
		<tr>
			<td><img src="'.base_url().'cside/images/LogoOrbf.png" /></td><td class="centre"> <h1>Ministère de la santé du XXX <br/> Projet FBR </h1></td>
		</tr>
		</table>
		
		');
		
		
	
		$ci->dpdf->content .= utf8_decode('	
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class>
		<tr>
			<td><strong>REGION: </strong>'.$geozone['region'].'</td>
		</tr>
		<tr>
			<td><strong>DISTRICT SANITAIRE: </strong>'.$geozone['district'].'</td>
		</tr>
 
		<tr><td><strong>PERIODE:</strong> '.$ci->lang->line('app_month_'.$params['datafile_month']).' '.$params['datafile_year'].'</td>
  
		</tr></table></p><br/><br/>');
  
		$ci->dpdf->content .= utf8_decode('<div style="padding:3px; border:0px solid black;"><h1 style="text-align:center ;  border-bottom: solid black 0px; padding:5px; margin:auto;font-size:17px;">'.$params['report_title'].'</h1></div><br/>');
  
		
			
		$ci->dpdf->content .=utf8_decode('<table class="tb_contenu" style="width:100%">
		<tr><th class="td_num">#</th><th class="td_indicateur">INDICATEURS</th><th>QUANTITE</th><th>MONTANT PAYE (FCFA)</th></tr>');
		$i=1;
		$total=0;
		foreach ($results as $key => $value) {
			$value["Indicateur"]=str_replace("?", "'", utf8_decode($value["Indicateur"]));
			$ci->dpdf->content .='<tr>'.'<td class="td_num" >'.$i.'</td>
			<td class="gauche td_indicateur">'.($value['Indicateur']).'</td>
			<td class="droite td_val">'.number_format($value['Verifiee'],0).'</td>
			<td class="droite td_val">'.number_format($value['Montant'],0).'</td>'.
			'</tr>';
			$total+=$value['Montant'];
			$i++;
		}
		
		$ci->dpdf->content .=utf8_decode('<tr><td></td><td colspan="2"><b>TOTAL A PAYER  POUR LE MOIS</b></td><td class="droite">'.number_format($total,0).'</td></tr>'.
		'</table>');
		
				
		$ci->dpdf->content .=utf8_decode('<br/><br/>'.
		
		'<table style="width:100%" class="signature"><tr>
		<td style="">'.$sign1.'</td>
		<td style="">'.$sign3.'</td>
		</tr></table>'.
		'<br/><br/>
		<div style="text-align:center; border-top:0px solid black; "><br/><br/>'.$sign2.'</div>
		');
			
				
		$ci->dpdf->content.="</div>";
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