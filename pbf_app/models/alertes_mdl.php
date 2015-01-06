<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Alertes_mdl extends CI_Model
{
	
	function __construct()
		{
		parent::__construct();
		} 
		
//=============================================================================================================================================	
	
	function get_alertes($num = 0, $filters,$indicator_id){
		
		$record_set=array();
		
		
		$sql_append=" WHERE 1=1";
		
		if(!empty($filters['title'])){
			
			$sql_append.= " AND (pbf_alerteconfig.alerte_title LIKE '%".trim($filters['title'])."%'";
			
			}
	    if(!$indicator_id==0){
			
			$sql_append.= " AND (pbf_alerteconfig.alerteconfig_id='".$indicator_id."')";
			
			}
	
		$sql = "SELECT * FROM pbf_alerteconfig ".$sql_append."  ORDER BY pbf_alerteconfig.alerte_title";
		
		$record_set['records_num']=$this->db->query($sql)->num_rows();
		
		$sql .= " LIMIT ". $num. " , ".$this->config->item('rec_per_page');
		
		$record_set['list']=$this->db->query($sql)->result_array();
				
		return $record_set;
		}

	function get_alertes_conf($alerteconf_id){
		
		$sql = "SELECT * FROM pbf_alerteconfig WHERE alerteconfig_id='".$alerteconf_id."' LIMIT 1";
		
		return $this->db->query($sql)->row_array();
		}
	

	
	function save_alerte($alertes){
		
		if(empty($alertes['alerteconfig_id'])){
			
			return $this->db->insert('pbf_alerteconfig',$alertes);
				
			}
		else{
			
			return $this->db->update('pbf_alerteconfig',$alertes, array('alerteconfig_id' => $alertes['alerteconfig_id']));
			
			}
		
		}
	

		
	function del_alerte($alerteconfig_id){
		
		return $this->db->delete('pbf_alerteconfig', array('alerteconfig_id' => $alerteconfig_id));
				  
        }
//===============================================================================================================================================	
}