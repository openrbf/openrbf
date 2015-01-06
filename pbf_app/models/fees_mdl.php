<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Fees_mdl extends CI_Model
{
	
	function __construct()
		{
		parent::__construct();
		} 
		
	function get_fees_lines($num = 0, $filters){
		
		$record_set=array();
		
		$sql_append=" WHERE 1=1";
		
		if(!empty($filters['datafile_month'])){
			
			$sql_append.= " AND (pbf_indicatorstarif.indicatortarif_month = '".trim($filters['datafile_month'])."') ";
			
			}
			
		if(!empty($filters['datafile_year'])){
			
			$sql_append.= " AND (pbf_indicatorstarif.indicatortarif_year = '".trim($filters['datafile_year'])."') ";
			
			}
			
		if(!empty($filters['file'])){
			
			$sql_append.= " AND (pbf_filetypes.filetype_id = '".trim($filters['file'])."') ";
			
			}
		
		$sql = "SELECT pbf_indicatorstarif.indicatortarif_id,CONCAT('<b>',pbf_filetypes.filetype_name,'</b> (',pbf_entityclasses.entity_class_name,IF(pbf_indicatorstarif.indicatortarif_entity_type_id IS NULL,'',CONCAT(' - ',pbf_entitytypes.entity_type_name)),')') AS filetype_name,CONCAT(pbf_geozones.geozone_name,' (',pbf_geo.geo_title,')') AS geozone_name,pbf_indicatorstarif.indicatortarif_month,pbf_indicatorstarif.indicatortarif_quarter,pbf_indicatorstarif.indicatortarif_year FROM pbf_indicatorstarif LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_indicatorstarif.indicatortarif_filetype_id) LEFT JOIN pbf_geo ON (pbf_geo.geo_id=pbf_indicatorstarif.indicatortarif_geo_id) LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_indicatorstarif.indicatortarif_geozone_id) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id = pbf_indicatorstarif.indicatortarif_entity_class_id) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_indicatorstarif.indicatortarif_entity_type_id) ".$sql_append." ORDER BY pbf_filetypes.filetype_id,indicatortarif_year DESC,indicatortarif_quarter DESC,indicatortarif_month DESC";
		
		$record_set['records_num']=$this->db->query($sql)->num_rows();
		
		$sql .= " LIMIT $num , ".$this->config->item('rec_per_page');
		
		$record_set['list']=$this->db->query($sql)->result_array();
				
		return $record_set;
		
		}
		
	function get_fees($indicatortarif_id,$lang){
		
		$sql = "SELECT pbf_indicatorstarif.indicatortarif_id,pbf_indicatorstarifdetails.indicatortarifdetails_id,pbf_indicatorstarif.indicatortarif_geo_id,pbf_indicatorstarif.indicatortarif_geozone_id,pbf_indicatorstarif.indicatortarif_entity_id,pbf_indicatorstarif.indicatortarif_entity_type_id,pbf_indicatorstarif.indicatortarif_entity_class_id,pbf_indicatorstarif.indicatortarif_entity_group_id,pbf_indicatorstarif.indicatortarif_entity_status_id,pbf_indicatorstarif.indicatortarif_filetype_id,pbf_indicatorstarif.indicatortarif_month,pbf_indicatorstarif.indicatortarif_quarter,pbf_indicatorstarif.indicatortarif_year,pbf_filetypes.filetype_name,pbf_indicatorstranslations.indicator_title,pbf_indicatorstarifdetails.indicator_id,pbf_indicatorstarif.indicatortarif_step_perc,pbf_indicatorstarif.indicatortarif_num_categories,pbf_indicatorsfileypes.default_tarif,pbf_indicatorstarifdetails.indicator_tarif,pbf_indicatorstarifdetails.indicator_exclusion,pbf_lookups.lookup_title AS content_type,filetypefrequency.lookup_title AS filefrequency FROM pbf_indicatorstarif LEFT JOIN pbf_indicatorstarifdetails ON (pbf_indicatorstarifdetails.indicatortarif_id=pbf_indicatorstarif.indicatortarif_id) LEFT JOIN pbf_indicators ON (pbf_indicators.indicator_id=pbf_indicatorstarifdetails.indicator_id) LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_indicatorstarif.indicatortarif_filetype_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_lookups filetypefrequency ON (filetypefrequency.lookup_id=pbf_filetypes.filetype_frequency AND filetypefrequency.lookup_linkfile='frequency') LEFT JOIN pbf_indicatorsfileypes ON (pbf_indicatorsfileypes.filetype_id=pbf_filetypes.filetype_id AND pbf_indicatorsfileypes.indicator_id=pbf_indicators.indicator_id AND (LAST_DAY(CONCAT(pbf_indicatorstarif.indicatortarif_year,'-',pbf_indicatorstarif.indicatortarif_month,'-1')) BETWEEN pbf_indicatorsfileypes.use_from AND pbf_indicatorsfileypes.use_to)) LEFT JOIN pbf_indicatorstranslations ON pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id WHERE pbf_indicatorstarif.indicatortarif_id='".$indicatortarif_id."' AND pbf_indicatorstranslations.indicator_language ='".$lang."'";
		
		return $this->db->query($sql)->result_array();
		
		}

	function get_indicators_new_fees($filetype_id,$lang){
		
		$sql = "SELECT pbf_indicators.indicator_id,pbf_indicatorstranslations.indicator_title,'' AS set_tarif,pbf_indicatorsfileypes.default_tarif,pbf_lookups.lookup_title AS content_type FROM pbf_indicators LEFT JOIN pbf_indicatorsfileypes ON (pbf_indicatorsfileypes.indicator_id=pbf_indicators.indicator_id) LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_indicatorsfileypes.filetype_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_indicatorstranslations ON pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id WHERE pbf_indicatorsfileypes.filetype_id='".$filetype_id."' AND pbf_indicatorstranslations.indicator_language ='".$lang."' ORDER BY pbf_indicatorsfileypes.order";
		
		return $this->db->query($sql)->result_array();
		
		}
		
	function get_fees_header($indicatortarif_id){
		
		return $this->db->get_where('pbf_indicatorstarif',array('indicatortarif_id'=>$indicatortarif_id))->row_array();
		
		}
		
	function save_fees_set($indicatortarif,$indicatortarifdetails){
		
			$res = $this->db->insert('pbf_indicatorstarif', $indicatortarif);
			
			if(!$res){
			   $error_arr["err_msg"] = $this->db->_error_message();
			   $error_arr["err_num"] = $this->db->_error_number();
			   $error_arr["result"] = $res;
			   return false;
  				}
			else{
			
			$indicatortarif_id = $this->db->insert_id();
			
			foreach($indicatortarifdetails['indicatortarif_indicator_id'] as $k => $v){
				
					//'indicator_tarif' => $fees['indicator_tarif'][$k],
					//'indicator_exclusion' => (in_array($v, $fees['indicator_exclusion']))?1:0,
					$obj['indicatortarif_id'] = $indicatortarif_id;
					$obj['indicator_id'] = $v;
					$obj['indicator_tarif'] = str_replace(",", "",$indicatortarifdetails['indicator_tarif'][$k]);
					//$obj['indicator_score_min'] =
					//$obj['indicator_score_max'] =
					$obj['indicator_exclusion'] = (isset($indicatortarifdetails['indicator_exclusion']) && in_array($v, $indicatortarifdetails['indicator_exclusion']))?1:0;
					
					$this->db->insert('pbf_indicatorstarifdetails', $obj);
				
				}
			}
				
			if ($this->db->affected_rows() > 0) { 
					return true; 
				} 
		 	 else { 
		  			return false; 
		  		}	
		
		}
		
		
	function del_fees($indicatortarif_id){
		
		$this->db->delete('pbf_indicatorstarif', array('indicatortarif_id' => $indicatortarif_id));
		$this->db->delete('pbf_indicatorstarifdetails', array('indicatortarif_id' => $indicatortarif_id));
		
		}

}