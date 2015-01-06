<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Dashboard_mdl extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function get_data_quarter(){
		
		$bind_region = implode(',',$this->session->userdata('usergeozones'));
		
		$sql = "SELECT pbf_datafile.datafile_quarter,pbf_datafile.datafile_year FROM pbf_datafile LEFT JOIN pbf_entities ON (pbf_entities.entity_id=pbf_datafile.entity_id) ";
		
		if(!empty($bind_region)){
			$sql .= " WHERE pbf_entities.entity_geozone_id IN (".$bind_region.")";
			}
		
		$sql .= " ORDER BY pbf_datafile.datafile_year,pbf_datafile.datafile_quarter";
		

	
		return $this->db->query($sql)->result_array();
		
		}
		
		function get_helpers($groups_id){
				$sql1="SELECT * FROM pbf_helpers";
		        $helpers=$this->db->query($sql1)->result_array();
				$helper_liste=array();
				foreach ($helpers as $helper){
					if (in_array($groups_id,explode(',',$helper['groups']))){
						$helper_liste[]=$helper['helper_id'];
					}
				}
				$sql = "SELECT * FROM pbf_helpers WHERE pbf_helpers.actif=1 AND pbf_helpers.helper_id IN (".implode(',',$helper_liste).") ORDER BY pbf_helpers.helper_order ASC ";
				return $this->db->query($sql)->result_array();
		}
		
		
		
		
		
	function get_general_completeness($business_time){
		

		$sql_append = "";
	
		$usergeozones = $this->session->userdata('usergeozones');
		
			if(!empty($usergeozones)){
			
				$sql_append.=" AND pbf_entities.entity_geozone_id IN (".implode(',',$usergeozones).") ";
			 $sql_append.=" AND pbf_filetypesgeozones.geozone_id IN (".implode(',',$usergeozones).") ";
			  
			 
			
			}
			
		
			
			
		// do not select the pbf_lookups.lookup_title
		$sql = "SELECT pbf_filetypes.filetype_id,pbf_filetypes.filetype_name,COUNT(DISTINCT(pbf_entities.entity_id)) AS 'awaited_rpt_number',pbf_filetypesfrequency.frequency_months AS frequency";
		
		foreach($business_time['months'] as $month){
			$sql .=", SUM(IF(pbf_datafile.datafile_month='".$month."',1,0)) AS '".$month."' ";
			}
		
		$sql .= " FROM pbf_filetypes LEFT JOIN pbf_filetypesentities ON (pbf_filetypesentities.filetype_id=pbf_filetypes.filetype_id) LEFT JOIN pbf_entities ON (pbf_entities.entity_class=pbf_filetypesentities.entity_class_id AND pbf_entities.entity_type=pbf_filetypesentities.entity_type_id) LEFT JOIN pbf_datafile ON (pbf_datafile.filetype_id=pbf_filetypes.filetype_id AND pbf_datafile.entity_id=pbf_entities.entity_id AND pbf_datafile.datafile_year='".$business_time['year']."') LEFT JOIN pbf_usersgroupsassets ON (pbf_usersgroupsassets.asset_id=pbf_filetypesentities.entity_class_id AND pbf_usersgroupsassets.asset_link='entity_class') LEFT JOIN pbf_filetypesgeozones ON (pbf_filetypesgeozones.filetype_id=pbf_filetypes.filetype_id) LEFT JOIN pbf_filetypesfrequency ON (pbf_filetypesfrequency.frequency_id=pbf_filetypes.filetype_frequency) LEFT JOIN pbf_entitiestime ON ((pbf_entitiestime.entity_id = pbf_entities.entity_id) AND (LAST_DAY('".$business_time['year']."-".($business_time['quarter']*3)."-1') BETWEEN pbf_entitiestime.use_from AND pbf_entitiestime.use_to) ) WHERE pbf_entities.entity_active = '1' AND pbf_usersgroupsassets.usersgroup_id='".$this->session->userdata('usergroup_id')."' AND IF(pbf_entitiestime.entity_active IS NOT NULL,pbf_entitiestime.entity_active, pbf_entities.entity_active) ='1' AND pbf_entities.entity_geozone_id  = pbf_filetypesgeozones.geozone_id ".$sql_append." AND pbf_filetypes.filetype_active = '1' AND pbf_filetypes.dashboard_active = '1' GROUP BY pbf_filetypes.filetype_id"; //pbf_filetypes.filetype_active = '1' AND a fix for Tchad


		return $this->db->query($sql)->result_array();

		}
		
		
		//=============================================Modules des logs=================================================================================
		
		function get_general_logs_data($num = 0, $filters){
			
			$record_set=array();
		
			$sql_append=" WHERE 1=1 AND publish=1";
			$join_append="";
			
			 if(!empty($filters['user_fullname'])){
		  	
					$sql_append.= " AND (pbf_users.user_fullname LIKE '%".trim($filters['user_fullname'])."%')";
		  	
				}
			
			 if(!empty($filters['event_type'])){
		  	
					$sql_append.= " AND (pbf_syseventlog.uri_string LIKE '".'/'.trim($filters['event_type'])."%')";
		  	
				}
			
			 if(!empty($filters['use_from']) && !empty($filters['use_to'])){
		  	
					$sql_append.= " AND (pbf_syseventlog.event_time BETWEEN '".trim($filters['use_from'])."' AND '".trim($filters['use_to'])."')";
		  	
				}
						
						
			$sql="SELECT pbf_syseventlog.user_id as log_id, pbf_users.user_fullname as username,pbf_syseventlog.event_time as date,pbf_syseventlog.event as event FROM pbf_users  LEFT JOIN pbf_syseventlog ON(pbf_users.user_id=pbf_syseventlog.user_id)".$sql_append." ORDER BY pbf_syseventlog.event_time DESC";
			
			$record_set['records_num']=$this->db->query($sql)->num_rows();
		
			$sql .= " LIMIT $num , ".$this->config->item('rec_per_page');
		
			$record_set['list']=$this->db->query($sql)->result_array();
		
			return $record_set;
				
			}
		
		//=========================================================================================================================================================
		
		
		function get_general_completeness_per_type($business_time){
		
		$sql_append = "";
	
		$usergeozones = $this->session->userdata('usergeozones');
		
			if(!empty($usergeozones)){
			
				$sql_append.=" AND pbf_entities.entity_geozone_id IN (".implode(',',$usergeozones).") ";
			 $sql_append.=" AND pbf_filetypesgeozones.geozone_id IN (".implode(',',$usergeozones).") ";
			  
			 
			
			}
			
		
			
			
		// do not select the pbf_lookups.lookup_title
		$sql = "SELECT pbf_filetypes.filetype_id,pbf_filetypes.filetype_name,filetype_contenttype,COUNT(DISTINCT(pbf_entities.entity_id)) AS 'awaited_rpt_number',pbf_filetypesfrequency.frequency_months AS frequency";
		
		foreach($business_time['months'] as $month){
			$sql .=", SUM(IF(pbf_datafile.datafile_month='".$month."',1,0)) AS '".$month."' ";
			}
		

		
		$sql .= " FROM pbf_filetypes LEFT JOIN pbf_filetypesentities ON (pbf_filetypesentities.filetype_id=pbf_filetypes.filetype_id) LEFT JOIN pbf_entities ON (pbf_entities.entity_class=pbf_filetypesentities.entity_class_id AND pbf_entities.entity_type=pbf_filetypesentities.entity_type_id) LEFT JOIN pbf_datafile ON (pbf_datafile.filetype_id=pbf_filetypes.filetype_id AND pbf_datafile.entity_id=pbf_entities.entity_id AND pbf_datafile.datafile_year='".$business_time['year']."') LEFT JOIN pbf_usersgroupsassets ON (pbf_usersgroupsassets.asset_id=pbf_filetypesentities.entity_class_id AND pbf_usersgroupsassets.asset_link='entity_class') LEFT JOIN pbf_filetypesgeozones ON (pbf_filetypesgeozones.filetype_id=pbf_filetypes.filetype_id) LEFT JOIN pbf_filetypesfrequency ON (pbf_filetypesfrequency.frequency_id=pbf_filetypes.filetype_frequency) WHERE pbf_entities.entity_active = '1' AND pbf_usersgroupsassets.usersgroup_id='".$this->session->userdata('usergroup_id')."' AND pbf_entities.entity_geozone_id  = pbf_filetypesgeozones.geozone_id ".$sql_append." AND pbf_filetypes.filetype_active = '1' AND pbf_filetypes.dashboard_active = '1' GROUP BY pbf_filetypes.filetype_id"; //pbf_filetypes.filetype_active = '1' AND a fix for Tchad
		
		$resultat = $this->db->query($sql)->result_array();
		
		$TotQty=0;
		$TotQly=0;
		
		$Qty=0;
		$Qly=0;
		
		$dynamic_headers = isset($resultat[0])?array_keys($resultat[0]):NULL;
		$dynamic_headers = isset($dynamic_headers)?array_slice($dynamic_headers, 4):NULL;
		
		
		foreach ($resultat as $key => $value) {
			if($value['filetype_contenttype']==12)//Quantity
			{
					if ($value['frequency']=='["3","6","9","12"]') {
						$TotQty+=$value['awaited_rpt_number'];
					}else{
						$TotQty+=($value['awaited_rpt_number']*3);
					}
				foreach($dynamic_headers as $dynamic_header){
						$Qty+=$value[$dynamic_header];
					
					}
				
			}else{//Quality
				if ($value['frequency']=='["3","6","9","12"]') {
						$TotQly+=$value['awaited_rpt_number'];
					}else{
						$TotQly+=($value['awaited_rpt_number']*3);
					}
					
					foreach($dynamic_headers as $dynamic_header){
						$Qly+=$value[$dynamic_header];
					
					}
			}
			
		}
		$total['Qty']=$Qty;
		$total['TotQty']=$TotQty;
		$total['Qly']=$Qly;
		$total['TotQly']=$TotQly;
		
		return $total;
		}
		
		
		
		
		
	function get_missing_completeness($filetype_id,$datafile_month,$datafile_year){
		
		$usergeozones = $this->session->userdata('usergeozones');
		
		$bind_region = "";
		
		if(!empty($usergeozones)){
			
			$bind_region .= " AND pbf_geozones.geozone_id IN (".implode(',',$usergeozones).") ";
			
			}
		
		$sql = "SELECT pbf_geozones.geozone_id,pbf_geozones.geozone_name,pbf_entities.entity_id,(pbf_entities.entity_name) AS entity_name,pbf_entities.entity_address,pbf_entities.entity_responsible_name,pbf_entities.entity_responsible_email FROM pbf_entities LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type AND pbf_entitytypes.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_filetypesentities ON (pbf_filetypesentities.entity_class_id=pbf_entities.entity_class AND pbf_filetypesentities.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_datafile ON (pbf_datafile.datafile_month = '".$datafile_month."' AND pbf_datafile.datafile_year='".$datafile_year."' AND pbf_datafile.entity_id = pbf_entities.entity_id AND pbf_datafile.filetype_id=pbf_filetypesentities.filetype_id) LEFT JOIN pbf_geozones ON (pbf_entities.entity_geozone_id=pbf_geozones.geozone_id) LEFT JOIN pbf_filetypesgeozones ON (pbf_filetypesgeozones.filetype_id=pbf_filetypesentities.filetype_id) WHERE  pbf_filetypesentities.filetype_id='".$filetype_id."' AND pbf_entities.entity_active='1' AND pbf_filetypesgeozones.geozone_id = pbf_entities.entity_geozone_id AND (pbf_datafile.entity_id IS NULL) ".$bind_region." GROUP BY pbf_geozones.geozone_id,pbf_entities.entity_id ORDER BY pbf_geozones.geozone_name,entity_name"; //  AND pbf_entities.entity_active='1' a fix for tchad
		


		return $this->db->query($sql)->result_array();
		
		}
		
		
		function delete_message($message_id){
		
		   $this->db->delete('pbf_message', array('message_id' => $message_id));
		}

		
		
		function check_message($message_id){
		
		 $this->db->where('message_id', $message_id);
		 $this->db->update('pbf_message', array('checked' => '1'));
		}
		
	
}