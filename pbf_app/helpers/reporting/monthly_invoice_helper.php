<?php
	function monthly_invoice($params,$invoice_params = null)
		{
		if ($params['origin']!=='cron_job'){
			if(!$params['report_id'] OR !$params['entity_id'] OR !$params['datafile_month'] OR !$params['datafile_year'] ){
				echo "Veuillez choisir tous les champs";exit;
			}
		}
		$ci = get_instance();
		$config = $ci->report_mdl->get_reports_conf($params['report_id']);
			
		$ci->load->library(array('dpdf','numbers/numbers_words'));
		$ci->dpdf->folder('./cside/pdfs/');
		$ci->dpdf->filename(str_ireplace(' ','',$config['report_title']).'.pdf');			
		$ci->dpdf->paper('a4', $config['report_page_layout']); 
		$ci->table->set_template($ci->dpdf->table_tmpl); 
			
			
		$ci->load->model(array('entities_mdl'));
		$ci->load->model(array('invoices_mdl'));
		$entity_info = $ci->entities_mdl->get_entity($params['entity_id']);
		if(!$entity_info['entity_use_equity_bonus']){
			$entity_info['entity_equity_pourcentage']=0;
		}
		
		$signatories = json_decode($params['report_signatories'],true);
		$sign1 = $signatories[0];
		$sign2 = $signatories[1];
		$sign3= $signatories[2];
			
		$sql = "SELECT datafile_remark,datafile_info,user_fullname,user_name,user_jobtitle,user_phonenumber  FROM pbf_datafile LEFT JOIN pbf_users ON (pbf_users.user_id = pbf_datafile.datafile_author_id) WHERE pbf_datafile.filetype_id = '8' AND pbf_datafile.entity_id = '".$ci->input->post('entity_id')."' AND pbf_datafile.datafile_month = '".$params['datafile_month']."' AND pbf_datafile.datafile_year = '".$params['datafile_year']."'";
		
		
		$sql = "SELECT pbf_indicatorsfileypes.order AS '#',pbf_indicatorstranslations.indicator_title AS 'Indicateur',FORMAT(pbf_datafiledetails.indicator_claimed_value,0) AS 'Declaree',FORMAT(pbf_datafiledetails.indicator_verified_value,0) AS 'Verifiee',FORMAT(pbf_datafiledetails.indicator_tarif,0) AS 'Bareme',FORMAT(pbf_datafiledetails.indicator_montant,0) AS 'Montant',pbf_datafiledetails.indicator_montant as 'Montant_v' FROM pbf_indicators JOIN pbf_indicatorstranslations ON (pbf_indicators.indicator_id=pbf_indicatorstranslations.indicator_id) JOIN pbf_datafiledetails ON (pbf_indicators.indicator_id=pbf_datafiledetails.indicator_id) JOIN pbf_datafile ON (pbf_datafiledetails.datafile_id=.pbf_datafile.datafile_id) JOIN pbf_indicatorsfileypes ON (pbf_indicators.indicator_id = pbf_indicatorsfileypes.indicator_id AND pbf_datafile.filetype_id=pbf_indicatorsfileypes.filetype_id) JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) JOIN pbf_lookups ON (pbf_filetypes.filetype_contenttype=pbf_lookups.lookup_id AND pbf_lookups.lookup_linkfile='content_type') WHERE pbf_indicatorstranslations.indicator_language='fr' AND pbf_lookups.lookup_title='Quantity' AND pbf_datafile.entity_id='".$params['entity_id']."' AND pbf_datafile.datafile_month='".$params['datafile_month']."' AND pbf_datafile.datafile_year='".$params['datafile_year']."' AND (LAST_DAY('".$params['datafile_year']."-".$params['datafile_month']."-1') BETWEEN pbf_indicatorsfileypes.use_from AND pbf_indicatorsfileypes.use_to)  ORDER BY pbf_indicatorsfileypes.order";
			
		$results = $ci->db->query($sql)->result_array();
					

		$ci->dpdf->content='';
		$ci->dpdf->content .= '<html><style type="text/css">@page { margin: 0.5cm 1cm 0.6cm 1cm; }body {font-family: sans-serif;margin: 0.5cm 0;text-align: justify; font-size:10.5px}#header,#footer {position: fixed;left: 0;right: 0;color: #000;font-size: 0.9em;}#header {top: -30;border-bottom: 0.1pt solid #aaa;}#footer {bottom: 0;border-top: 0.1pt solid #aaa;}#header table,#footer table {width: 100%;border-collapse: collapse;border: none;}#header td,#footer td {padding: 0;width: 50%;}hr {page-break-after: always;border: 0;}</style><body><script type="text/php">
		if ( isset($pdf) ) { 
			$font = Font_Metrics::get_font(\'helvetica\', \'normal\');
			$size = 6;
			$y = $pdf->get_height() - 17;
			$x = $pdf->get_width() - 35 - Font_Metrics::get_text_width(\'1 de 1\', $font, $size);
			$pdf->page_text($x, $y, \'{PAGE_NUM} de {PAGE_COUNT}\', $font, $size);
		} 
		</script>';
		
		
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
			width:16%;
		}
		
		</style>";
		
		
		$ci->dpdf->content .= utf8_decode('
		<table class="tb_logo">
		<tr>
			<td><img src="'.base_url().'cside/images/LogoOrbf.png" /></td><td class="centre"> <h1>Ministère de la santé du XXX <br/> Projet FBR </h1></td>
		</tr>
		</table>');
		
		
	
		$ci->dpdf->content .= utf8_decode('	
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class>
		<tr>
			<td><strong>REGION: </strong>'.$entity_info['parent_geozone_name'].'</td>
		</tr>
		<tr>
			<td><strong>DISTRICT SANITAIRE: </strong>'.$entity_info['geozone_name'].'</td>
		</tr>
		<tr>
			<td><strong>FORMATION SANITAIRE: </strong>'.$entity_info['entity_name'].'</td>
			<td><strong>CAT DS : </strong>'.$entity_info['district_equity_bonus'].' %</td>
		</tr>
			<tr><td><strong>PERIODE:</strong> '.$ci->lang->line('app_month_'.$params['datafile_month']).' '.$params['datafile_year'].'</td>
			<td><strong>CAT FS : </strong>'.$entity_info['entity_equity_pourcentage'].' %</td>
		</tr></table></p><br/><br/>');
  
		$ci->dpdf->content .= utf8_decode('<div style="padding:3px; border:0px solid black;"><h1 style="text-align:center ;  border-bottom: solid black 0px; padding:5px; margin:auto;font-size:17px;">'.$params['report_title'].'</h1></div><br/>');
  
		
			
		$ci->dpdf->content .=utf8_decode('<table class="tb_contenu" style="width:100%">
		<tr><th>#</th><th class="td_indicateur">Indicateurs</th><th>Quantité</th><th>Prix de base<br/>+ Bonus</th><th>Total en FCFA</th></tr>');
		$i=1;
		$total=0;
		foreach ($results as $key => $value) {
			$value["Indicateur"]=str_replace("?", "'", utf8_decode($value["Indicateur"]));
			$ci->dpdf->content .='<tr>'.'<td class="td_num">'.$i.'</td>
			<td class="gauche td_indicateur">'.($value['Indicateur']).'</td>
			<td class="droite td_val">'.$value['Verifiee'].'</td>
			<td class="droite td_val">'.$value['Bareme'].'</td>
			<td class="droite td_val">'.$value['Montant'].'</td>'.
			'</tr>';
			$total+=$value['Montant_v'];
			$i++;
		}
		
		$ci->dpdf->content .=utf8_decode('<tr><td></td><td colspan="3"><b>TOTAL A PAYER  POUR LE MOIS</b></td><td class="droite">'.number_format($total,0).'</td></tr>'.
		'</table>');
		
		$total=str_replace(',','',number_format($total,0));
		$total=str_replace('.','',$total);
		
		$ci->dpdf->content .=utf8_decode('<br/><br/><p>Arrêté la présente facture pour le mois <b>'.$ci->lang->line('app_month_'.$params['datafile_month']).' - '.$params['datafile_year'].' </b> pour la formation sanitaire <b>'.$entity_info['entity_name'].'</b> à un montant de <b>'.$ci->numbers_words->toWords($total,'fr').' FCFA </b></p>');
		 
		
		$ci->dpdf->content .=utf8_decode('<br/><br/>'.
		
		'<table style="width:100%" class="signature">
		<tr>
			<td style="">'.$sign1.'</td>
			<td style="">'.$sign3.'</td>
		</tr>
		</table>'.
		'<br/><br/>
		<div style="text-align:center; border-top:0px solid black; "><br/><br/>'.$sign2.'</div>
		');
		$ci->dpdf->content.=$params['date'];	
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

