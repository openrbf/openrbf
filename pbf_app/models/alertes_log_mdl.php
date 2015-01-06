<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Alertes_log_mdl extends CI_Model
{
	
	function __construct()
		{
		parent::__construct();
		} 
		
//=============================================================================================================================================	

		
	function save_alerte_log($alert_log){
		
		return $this->db->insert('pbf_alertes',$alert_log);
				
	}
	

		
	function del_alerte_log($alerte_id){
		
		return $this->db->delete('pbf_alertes', array('alerte_id' => $alerte_id));
				  
        }
		
		
		
		
	function details_alerte($alerte_id){
		$sql="SELECT pbf_alertes.*,pbf_usersgroups.usersgroup_name,pbf_alerteconfig.alerte_title,pbf_alerteconfig.alerte_message as messagetext FROM pbf_alertes 
		LEFT JOIN pbf_usersgroups ON (pbf_usersgroups.usersgroup_id=pbf_alertes.group_id)  
		LEFT JOIN pbf_alerteconfig ON(pbf_alerteconfig.alerteconfig_id=pbf_alertes.type_alerte)
		WHERE pbf_alertes.alerte_id='".$alerte_id."' LIMIT 1";
		return  $this->db->query($sql)->row_array();
		
	}
//===============================================================================================================================================	
}