<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Budgets_mdl extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
		
	function get_budgets_void_bay_entity($num = 0, $filters){
		
		$record_set=array();
		
		$sql_append=" WHERE 1=1";
		
		if(!empty($filters['type'])){
		$sql_append .=" AND pbf_entitytypes.entity_type_id='".$filters['type']."' ";	
			}
			
		if(!empty($filters['geozone_id'])){
		$sql_append .=" AND pbf_entities.entity_geozone_id='".$filters['geozone_id']."' ";	
			}
			
		$sql = "SELECT pbf_budget.budget_id,IF(pbf_entitytypes.entity_type_abbrev!=0 OR pbf_entitytypes.entity_type_abbrev!='',CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev),pbf_entities.entity_name) AS entity_name,pbf_geozones.geozone_name,IF(pbf_geozones.geozone_parent_id is NULL,'oui','non'),pbf_entitytypes.entity_type_name AS entity_type,pbf_budget.budget_month,pbf_budget.budget_quarter ,pbf_budget.budget_year ,pbf_budget.budget_value FROM pbf_entities LEFT JOIN pbf_geozones ON (pbf_geozones.entity_geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_budget ON (pbf_budget.entity_id=pbf_entities.entity_id ".(empty($filters['year'])?"":" AND pbf_budget.budget_year = '".$filters['year']."' ").") ".$sql_append." ORDER BY entity_name";
		
		$record_set['records_num']=$this->db->query($sql)->num_rows();
		
		$sql .= " LIMIT $num , ".$this->config->item('rec_per_page');
		
		$record_set['list']=$this->db->query($sql)->result_array();
				
		return $record_set;
		}
		
	function get_budgets($num = 0, $filters){
		
		$record_set=array();
		
		$sql_append=" WHERE 1=1";

			
		if(!empty($filters['budget_year'])){
			
			$sql_append.= " AND pbf_budget.budget_year = '".$filters['budget_year']."' ";
			
			}
			
		if(!empty($filters['geozone_id'])){
			
			$sql_append.= " AND pbf_budget.entity_geozone_id = '".$filters['geozone_id']."' ";
			
			}
			
	    if(!empty($filters['entity_id'])){
			
			$sql_append.= " AND pbf_budget.entity_id = '".$filters['entity_id']."' ";
			
			}
		//$sql = "SELECT pbf_budget.budget_id,pbf_geozones.geozone_name,IF(pbf_entityclasses.entity_class_abbrev='' OR pbf_entityclasses.entity_class_abbrev IS NULL,pbf_entityclasses.entity_class_name,CONCAT(entity_class_name,' (',pbf_entityclasses.entity_class_abbrev,')')) AS entity_class_name,IF(pbf_budget.budget_month=0 OR pbf_budget.budget_month IS NULL,NULL,pbf_budget.budget_month) AS budget_month,IF(pbf_budget.budget_quarter=0 OR pbf_budget.budget_quarter IS NULL,NULL,pbf_budget.budget_quarter) AS budget_quarter,pbf_budget.budget_value FROM pbf_budget JOIN pbf_geozones ON (pbf_budget.geozone_id=pbf_geozones.geozone_id ) JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_budget.entity_class_id) JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_budget.entity_type_id) ".$sql_append;
		
		$sql = "SELECT pbf_budget.budget_id,IF(pbf_geozones.geozone_parentid is NOT NULL,pbf_geozones.geozone_parentid,'') as Region,pbf_geozones.geozone_name,IF(pbf_entityclasses.entity_class_abbrev='' OR pbf_entityclasses.entity_class_abbrev IS NULL,pbf_entityclasses.entity_class_name,CONCAT(pbf_entityclasses.entity_class_name,' (',pbf_entityclasses.entity_class_abbrev,')')) AS entity_class_name,pbf_entitytypes.entity_type_name,pbf_entities.entity_name as nomfosa,pbf_budget.budget_month,pbf_budget.budget_year,pbf_budget.budget_value FROM pbf_budget LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id = pbf_budget.entity_geozone_id) LEFT JOIN pbf_entities ON (pbf_budget.entity_id=pbf_entities.entity_id) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) AND (pbf_entitytypes.entity_class_id=pbf_entityclasses.entity_class_id) ".$sql_append." ORDER BY pbf_budget.budget_year DESC ,pbf_budget.budget_quarter DESC";
		
		$record_set['records_num']=$this->db->query($sql)->num_rows();
		
		$sql .= " LIMIT $num , ".$this->config->item('rec_per_page');
		
		$record_set['list']=$this->db->query($sql)->result_array();
			//echo $sql;
			//exit;	
		return $record_set;
		}
		
		
		
	function get_budget_line($budget_id){
		
		return $this->db->get_where('pbf_budget',array('budget_id'=>$budget_id))->row_array();
		
		}
		
	function del_budgets($budget_id){
		
		return $this->db->delete('pbf_budget', array('budget_id' => $budget_id));
		
		}
	
	function save_budget($budget){
		
		if(empty($budget['budget_id'])){
			
			return $this->db->insert('pbf_budget', $budget);
			
			}
		else{
			
			return $this->db->update('pbf_budget', $budget, array('budget_id' => $budget['budget_id']));
			
			}
		
		}
//============================================================================================================		
	  function get_entity_classes_id($entity_id) {
	   $sql="SELECT * from pbf_entities where entity_id='".$entity_id."'";
	   $row=$this->db->query($sql)->result_array();
		return $row[0]['entity_class']; 
	  }
	  function get_entity_type_id($entity_id) {
	   $sql="SELECT * from pbf_entities where entity_id='".$entity_id."'";
	   $row=$this->db->query($sql)->result_array();
		return $row[0]['entity_type']; 
	  }
	 
	  
	   function gest_last_quarters_zone() {
	   $sql = "SELECT DISTINCT(budget_quarter),budget_year FROM pbf_budget GROUP BY budget_year,budget_quarter ORDER BY budget_year ASC,budget_quarter ASC LIMIT 0,12";
		return $this->db->query($sql)->result_array();
	  }
	  
	  function verif_annual_budget($year,$entity){
	   $sql="SELECT * from pbf_budget where entity_id=".$entity." AND budget_month IS NULL AND budget_year='".$year."'";
	   $row_number=$this->db->query($sql)->num_rows();
	   return $row_number;
	  }

	   function verif_mensual_budget($month,$year,$entity){
	   $sql="SELECT * from pbf_budget where entity_id=".$entity." AND budget_month=".$month." AND budget_year='".$year."'";
	   $row_number=$this->db->query($sql)->num_rows();
	   return $row_number;
	  }	


	  


      function get_cumul_month_budget($year,$entity){
		$sql = "SELECT SUM(budget_value) as budget FROM pbf_budget where entity_id=".$entity." AND budget_month IS NOT NULL AND budget_year='".$year."' GROUP BY entity_id";
		$budget_data = $this->db->query($sql)->result_array();
        return $budget_data;
		 
        }
		
	  function current_month_budget($budget_id){
		$sql = "SELECT budget_value as budget FROM pbf_budget where budget_id='".$entity."'";
		$budget_data = $this->db->query($sql)->result_array();
        return $budget_data;
		 
        }
		
	  function get_annual_budget_entity($year,$entity){
        $sql = "SELECT budget_value as budget FROM pbf_budget where entity_id=".$entity." AND budget_month IS NULL AND budget_year='".$year."'";
		$budget_data = $this->db->query($sql)->result_array();
        return $budget_data;
	  }	  
	

//===============================check budget year national==========================================================	
	  function list_budget_year(){
	      $sql = "SELECT DISTINCT budget_year FROM pbf_budget where budget_month IS NULL AND budget_year IN (select budget_year from pbf_budget WHERE budget_month IS NOT NULL)";
	      $budget_year_list = $this->db->query($sql)->result_array();
          return $budget_year_list;
		  }
 
       function exist_budget_year($year){
	      $sql = "SELECT budget_year FROM pbf_budget where budget_month IS NULL AND budget_year IN (select budget_year from pbf_budget WHERE budget_month IS NOT NULL AND budget_year='".$year."')";
	      $budget_year_list = $this->db->query($sql)->num_rows(); 
          return $budget_year_list;
		  }
		  
	   function verif_budget(){
          $sql = "SELECT * FROM pbf_budget";
	      $verif_budget = $this->db->query($sql)->num_rows(); 
          return $verif_budget;
		}	   
		  
		  
//====================================================================================================================	  
	  
	
}