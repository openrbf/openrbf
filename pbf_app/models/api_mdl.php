<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class api_mdl extends CI_Model
{
	
	function __construct()
		{
		parent::__construct();
		} 
	
	function get_entities(){ 
	
	$record_set=array();
	$sql_append = " WHERE 1=1 ";

		
		$sql="SELECT pbf_entities.entity_id,pbf_entitytypes.entity_type_abbrev,pbf_entities.entity_name,pbf_geozones.geozone_name,pbf_entitytypes.entity_type_id AS entity_type,pbf_lkp_status.lookup_id AS entity_status,pbf_entities.entity_responsible_name,pbf_entities.entity_responsible_email,pbf_entities.entity_active FROM pbf_entities LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_lookups pbf_lkp_status ON (pbf_lkp_status.lookup_id=pbf_entities.entity_status AND pbf_lkp_status.lookup_linkfile='entity_status') ".$sql_append;
		$record_set['records_num']=$this->db->query($sql)->num_rows();
		$record_set['list']=$this->db->query($sql)->result_array();
		
		return $record_set;
		}
		
		function get_entity($id){
		
		 $record_set=array();
		 $sql_append = " WHERE 1=1 ";
		
		
		 $sql="SELECT pbf_entities.entity_id,pbf_entitytypes.entity_type_abbrev,pbf_entities.entity_name,pbf_geozones.geozone_name,pbf_entitytypes.entity_type_id AS entity_type,pbf_lkp_status.lookup_id AS entity_status,pbf_entities.entity_responsible_name,pbf_entities.entity_responsible_email,pbf_entities.entity_active FROM pbf_entities LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_lookups pbf_lkp_status ON (pbf_lkp_status.lookup_id=pbf_entities.entity_status AND pbf_lkp_status.lookup_linkfile='entity_status') ".$sql_append. " AND pbf_entities.entity_id ='".$id."'";
		 $record_set['records_num']=$this->db->query($sql)->num_rows();
		 $record_set['list']=$this->db->query($sql)->result_array();
		
		 return $record_set;
		}
		
		function get_geozones(){
		
		 $record_set=array();
		 $sql_append = " WHERE 1=1 ";
		
		
		 $sql="SELECT pbf_geozones.geozone_id,pbf_geozones.geozone_name,pbf_geozones.geozone_pop,pbf_geozones.geozone_pop_year from pbf_geozones".$sql_append;
		 $record_set['records_num']=$this->db->query($sql)->num_rows();
		 $record_set['list']=$this->db->query($sql)->result_array();
		
		 return $record_set;
		}		
		
		function get_geozone($id){
		
		 $record_set=array();
		 $sql_append = " WHERE 1=1 ";
		
		
		 $sql="SELECT pbf_geozones.geozone_id,pbf_geozones.geozone_name,pbf_geozones.geozone_pop,pbf_geozones.geozone_pop_year from pbf_geozones".$sql_append. " AND pbf_geozones.geozone_id ='".$id."'";
		 $record_set['records_num']=$this->db->query($sql)->num_rows();
		 $record_set['list']=$this->db->query($sql)->result_array();
		
		 return $record_set;
		}
		
		function get_datafile($entity,$filetype,$month,$quarter, $year){
		
		 $record_set=array();
		 $sql_append = " WHERE 1=1 ";
		 if(!empty($entity)){
		  $sql_append.=" AND pbf_datafile.entity_id ='".$entity."'";
		 }
		 if(!empty($filetype)){
		  $sql_append.=" AND pbf_datafile.filetype_id ='".$filetype."'";
		 }
		 if(!empty($month)){
		  $sql_append.=" AND pbf_datafile.datafile_month ='".$month."'";
		 }
		 if(!empty($quarter)){
		  $sql_append.=" AND pbf_datafile.datafile_quarter ='".$quarter."'";
		 }
		 if(!empty($year)){
		  $sql_append.=" AND pbf_datafile.datafile_year ='".$year."'";
		 }
		
		 $sql="SELECT pbf_datafile.datafile_id,pbf_datafile.entity_id,  pbf_datafile.datafile_total FROM pbf_datafile".$sql_append. "AND pbf_datafile.datafile_status='1'";
		 $record_set['records_num']=$this->db->query($sql)->num_rows();
		 $record_set['list']=$this->db->query($sql)->result_array();
		
		 return $record_set;
		}
		
		function get_datafiles(){
		
		 $record_set=array();
		 $sql_append = " WHERE 1=1 ";
		
		 
		
		 $sql="SELECT pbf_datafile.datafile_id,pbf_datafile.filetype_id,pbf_datafile.entity_id, pbf_datafile.datafile_month, pbf_datafile.datafile_quarter, pbf_datafile.datafile_year, pbf_datafile.datafile_total FROM pbf_datafile".$sql_append. "AND pbf_datafile.datafile_status='1'";
		 $record_set['records_num']=$this->db->query($sql)->num_rows();
		 $record_set['list']=$this->db->query($sql)->result_array();
		
		 return $record_set;
		}
		
	
		function get_datafiledetails($id){
		
		 $record_set=array();
		 $sql_append = " WHERE 1=1 ";
		
		
		 $sql="SELECT pbf_datafiledetails.indicator_id, pbf_datafiledetails.indicator_verified_value,pbf_datafiledetails.indicator_tarif,pbf_datafiledetails.indicator_montant FROM pbf_datafiledetails".$sql_append. " AND pbf_datafiledetails.datafile_id ='".$id."'";
		 $record_set['records_num']=$this->db->query($sql)->num_rows();
		 $record_set['list']=$this->db->query($sql)->result_array();
		
		 return $record_set;
		}
		
	
	

}