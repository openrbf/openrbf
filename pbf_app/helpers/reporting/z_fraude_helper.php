<?php
//error_reporting(E_ALL);	
function fraude($params)
		{
			$ci = get_instance();
			$config = $ci->report_mdl->get_reports_conf($ci->input->post('report_id'));
			
			$ci->load->library(array('dpdf','numbers/numbers_words'));
			$ci->dpdf->folder('./cside/pdfs/');
			$ci->dpdf->filename(str_ireplace(' ','',$config['report_title']).'.pdf');			
			$ci->dpdf->paper('a4', $config['report_page_layout']); 
			$ci->table->set_template($ci->dpdf->table_tmpl); 
			$ci->dpdf->content .= $ci->dpdf->set_header();
			
			$ci->load->model(array('entities_mdl'));
			$entity_info = $ci->entities_mdl->get_entity($ci->input->post('entity_id'));
			
			$sql = "SELECT datafile_remark,datafile_info,user_fullname,user_name,user_jobtitle,user_phonenumber  FROM pbf_datafile LEFT JOIN pbf_users ON (pbf_users.user_id = pbf_datafile.datafile_author_id) WHERE pbf_datafile.filetype_id = '8' AND pbf_datafile.entity_id = '".$ci->input->post('entity_id')."' AND pbf_datafile.datafile_month = '".$params['datafile_month']."' AND pbf_datafile.datafile_year = '".$params['datafile_year']."'";
			
			$results = $ci->db->query($sql)->row_array();
							
			
		$ci->dpdf->content .= '<br><br><br><p align="center"><b><u>'.strtoupper(utf8_decode($params['report_title'])).'</u></b></p><p><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><strong>REGION SANITAIRE: </strong>'.$entity_info['parent_geozone_name'].'</td>
    <td rowspan="4" valign="top"><strong>VERIFICATEUR :</strong><br>'.(($results['user_fullname']=='')?'':$results['user_fullname'].'<br>').(($results['user_jobtitle']=='')?'':$results['user_jobtitle'].'<br>').(($results['user_name']=='')?'':$results['user_name'].'<br>').(($results['user_phonenumber']=='')?'':$results['user_phonenumber']).'</td>
  </tr>
  <tr>
    <td><strong>ZONE SANITAIRE: </strong>'.$entity_info['geozone_name'].'</td>
  </tr>
  <tr>
    <td><strong>FORMATION SANITAIRE: </strong>'.$entity_info['entity_name'].' '.$entity_info['entity_type_abbrev'].'</td>
  </tr>
  <tr><td><strong>PERIODE :</strong> '.utf8_decode($ci->lang->line('app_month_'.$params['datafile_month'])).' '.$params['datafile_year'].'</td></tr></table></p>';

			
		$ci->dpdf->content .= '<br><br><br><br><p style="font-size:10px"><strong>DESCRIPTION:</strong><br><br>'.utf8_decode(str_replace(array("\r\n","\n"),'<br />',$results['datafile_remark'])).'</p>';
			
			
		$ci->table->clear();
		
		$signatories = json_decode($params['report_signatories'],true);
		
		$ci->table->set_template($ci->dpdf->sign_tmpl);
		
		$ci->table->add_row(	
								array('data' => utf8_decode($signatories[0]),'width' => '33%'),
								array('data' => utf8_decode($signatories[1]),'width' => '34%'),
								array('data' => utf8_decode($signatories[2]),'width' => '33%') );
		
		$ci->dpdf->content .= '<p style="font-size:10px">'.$ci->table->generate().'</p>';
			
		$ci->dpdf->html($ci->dpdf->content);
		$ci->dpdf->create();	
			
	}   

