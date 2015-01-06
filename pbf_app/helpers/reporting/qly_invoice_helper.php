<?php
	function qly_invoice($params,$invoice_params = null){
			
		if(!$params['report_id'] OR !$params['entity_id'] OR !$params['datafile_quarter'] OR !$params['datafile_year'] ){
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
		$entity_info = $ci->entities_mdl->get_entity($params['entity_id']);
		if(!$entity_info['entity_use_equity_bonus']){
			$entity_info['entity_equity_pourcentage']=0;
		}
	
		$signatories = json_decode($params['report_signatories'],true);
		$sign1 = $signatories[0];
		$sign2 = $signatories[1];
		$sign3= $signatories[2];
			
		
		$sql="SELECT SUM(datafile_total) AS subsides FROM pbf_datafile  WHERE  (filetype_id=12 OR filetype_id=13) AND datafile_quarter=".$params['datafile_quarter']." AND datafile_year=".$params['datafile_year']."  AND pbf_datafile.entity_id = ".$params['entity_id'];
		$rw_subsides = $ci->db->query($sql)->row_array();
		if($rw_subsides){
			$subsides=$rw_subsides['subsides'];
		}else{
			$subsides=0;
		}
			
			
		$sql="SELECT (datafile_total) AS quality FROM pbf_datafile  WHERE  (filetype_id=14 OR filetype_id=15) AND datafile_quarter=".$params['datafile_quarter']." AND datafile_year=".$params['datafile_year']."  AND pbf_datafile.entity_id = ".$params['entity_id'];
		$rw_qual = $ci->db->query($sql)->row_array();
		if($rw_qual['quality']){
			$quality=$rw_qual['quality'];
		}else{
			$quality=0;
		}
			
	
		$subside_dispo=0.25*$subsides;
		if($quality>50){
			$prime=$subside_dispo*$quality/100;
		}else{
			$prime=0;
		}
			
	
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
		
		</style>";
		
		$ci->dpdf->content .= utf8_decode('
		<table class="tb_logo">
		<tr>
			<td><img src="'.base_url().'cside/images/LogoOrbf.png" /></td><td class="centre"> <h1>Ministère de la santé du XXX <br/> Projet FBR </h1></td>
		</tr>
		</table>');
		
		
		$months=$ci->pbf->get_monthsBy_quarter($params['datafile_quarter']);
	
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
		</tr>
		<tr>
			<td><strong>PERIODE:</strong> '.$ci->lang->line('app_month_'.$months['0']).' - '.$ci->lang->line('app_month_'.$months['2']).' , '.$params['datafile_year'].'</td>
  
		</tr></table></p><br/><br/>');
  
		$ci->dpdf->content .= utf8_decode('<div style="padding:3px; border:0px solid black;"><h1 style="text-align:center ;  border-bottom: solid black 0px; padding:5px; margin:auto;font-size:17px;">'.$params['report_title'].'</h1></div><br/><br/>');
  	
			
		$ci->dpdf->content .=utf8_decode('<table class="tb_contenu" style="width:80%; margin:auto;">
		<tr>
			<th class="gauche">A. Score qualité </th><td class="droite">'.number_format($quality,2).' %</td>
		</tr>
		
		<tr>
			<th class="gauche">B. Subsides trimestriels</th><td class="droite">'.number_format($subsides).'</td>
		</tr>
		
		<tr>
			<th class="gauche">C. Subsides trimestriels disponibles (B X 25%)</th><td class="droite">'.number_format($subside_dispo).'</td>
		</tr>
		
		<tr>
			<th class="gauche">TOTAL A PAYER (C X A)</th><th class="droite">'.number_format($prime).'</th>
		</tr>
		
		</table>');
		
		$prime=str_replace(',','',number_format($prime,0));
		$prime=str_replace('.','',$prime);
		
		$ci->dpdf->content .=utf8_decode('<br/><br/><p>Arrêté la présente facture pour le bonus qualité <b>'.$ci->lang->line('app_quarter_'.$params['datafile_quarter']).' - '.$params['datafile_year'].' </b> pour la formation sanitaire <b>'.$entity_info['entity_name'].'</b> à un montant de <b>'.$ci->numbers_words->toWords($prime,'fr').' FCFA </b></p>');
		
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