<?php
//error_reporting(E_ALL);	
function fraude($params)
		{
			
			if(!$params['report_id'] OR !$params['entity_id'] OR !$params['datafile_month'] OR !$params['datafile_year'] ){
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
			$entity_info = $ci->entities_mdl->get_entity($ci->input->post('entity_id'));
			
			
			$signatories = json_decode($params['report_signatories'],true);
			$sign1 = $signatories[0];
			$sign2 = $signatories[1];
			$sign3= $signatories[2];
			
			
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
		
		</style>
		";//Debut du contenu
		$ci->dpdf->content .= utf8_decode('
		<table class="tb_logo">
		<tr>
			<td><img src="'.base_url().'cside/images/LogoOrbf.png" /></td><td class="centre"> <h1>Ministère de la santé du xxx <br/> Projet FBR </h1></td>
		</tr>
		</table>
		
		');
		
		
	
	$ci->dpdf->content .= utf8_decode('	
<table width="100%" border="0" cellspacing="0" cellpadding="0" class>
  
  <tr>
    <td><strong>REGION: </strong>'.$entity_info['parent_geozone_name'].'</td>
  </tr>
  <tr>
    <td><strong>DISTRICT SANITAIRE: </strong>'.$entity_info['geozone_name'].'</td>
  </tr>
  <tr>
    <td><strong>FORMATION SANITAIRE: </strong>'.$entity_info['entity_name'].'</td></tr>
  <tr><td><strong>PERIODE:</strong> '.$ci->lang->line('app_month_'.$params['datafile_month']).' '.$params['datafile_year'].'</td>
  </tr></table></p><br/><br/>');
  
  $ci->dpdf->content .= utf8_decode('<div style="padding:3px; border:0px solid black;"><h1 style="text-align:center ;  border-bottom: solid black 0px; padding:5px; margin:auto;font-size:17px;"><u>RAPPORT DE FRAUDE</u></h1></div><br/>');
  
  
 $sql = "SELECT datafile_remark,datafile_info,user_fullname,user_name,user_jobtitle,user_phonenumber  FROM pbf_datafile LEFT JOIN pbf_users ON (pbf_users.user_id = pbf_datafile.datafile_author_id) WHERE pbf_datafile.filetype_id = '8' AND pbf_datafile.entity_id = '".$ci->input->post('entity_id')."' AND pbf_datafile.datafile_month = '".$params['datafile_month']."' AND pbf_datafile.datafile_year = '".$params['datafile_year']."'";
			
			$results = $ci->db->query($sql)->row_array();
			//print_test($results);exit;
		
			
			
		$ci->dpdf->content .=utf8_decode('
		<div>
			<strong>VERIFICATEUR :</strong><br>'.(($results['user_fullname']=='')?'':$results['user_fullname'].'<br>').(($results['user_jobtitle']=='')?'':$results['user_jobtitle'].'<br>').(($results['user_name']=='')?'':$results['user_name'].'<br>').(($results['user_phonenumber']=='')?'':$results['user_phonenumber']).'
		</div>
		');
		
			
		$ci->dpdf->content .= '<br/><br/><br/><div style="font-size:10px"><strong>DESCRIPTION:</strong><div style="margin-left:30px;">'.utf8_decode(str_replace(array("\r\n","\n"),'<br />',$results['datafile_remark'])).'</div></div>';
			
	
		$ci->dpdf->content .=utf8_decode('<br/><br/>'.
		
		'<table style="width:100%" class="signature"><tr>
		<td style="">'.$sign1.'</td>
		<td style="">'.$sign3.'</td>
		</tr></table>'.
		'<br/><br/>
		<div style="text-align:center; border-top:0px solid black; "><br/><br/>'.$sign2.'</div>
		');
	
				
		 	$ci->dpdf->content.="</div>";//Fin du contenu
		 	//$ci->dpdf->content.="</div>";//Fin du contenu
		$ci->dpdf->html($ci->dpdf->content);
		//$ci->dpdf->create();	
			$file = './cside/reports/fraude_'.time().'.pdf';
						
						$ci->dpdf->create_and_save($file);	
	}   

