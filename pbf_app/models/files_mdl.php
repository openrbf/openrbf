<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Files_mdl extends CI_Model
{
	
	function __construct()
		{
		parent::__construct();
		} 
		
	function get_file_types($num = 0, $filters){
		
		$record_set=array();
		
		$sql_append=" WHERE 1=1";
		
		$sql="SELECT pbf_filetypes.filetype_id,pbf_filetypes.filetype_name,pbf_filetypes.filetype_contenttype 	,pbf_filetypesfrequency.frequency_id AS filetype_frequency,pbf_filetypes.filetype_active FROM pbf_filetypes LEFT JOIN pbf_filetypesfrequency ON (pbf_filetypesfrequency.frequency_id = pbf_filetypes.filetype_frequency) ".$sql_append." ORDER BY pbf_filetypes.filetype_name";

		$record_set['records_num']=$this->db->query($sql)->num_rows();
		
		$sql .= " LIMIT $num , ".$this->config->item('rec_per_page');
		
		$record_set['list']=$this->db->query($sql)->result_array();
				
		return $record_set;
		}
		
	function get_file_frequences($num = 0, $filters){

		$record_set=array();
		
		$sql_append=" WHERE 1=1";
		
		$sql = "SELECT * FROM pbf_filetypesfrequency";

		$record_set['records_num']=$this->db->query($sql)->num_rows();
		
		$sql .= " LIMIT $num , ".$this->config->item('rec_per_page');
		
		$record_set['list']=$this->db->query($sql)->result_array();
				
		return $record_set;		
		
		}
	
	function get_file_type($filetype_id){
		
		$file = $this->db->get_where('pbf_filetypes',array('filetype_id'=>$filetype_id))->row_array();
		
		$file['filetypesentities'] = $this->db->get_where('pbf_filetypesentities',array('filetype_id'=>$filetype_id))->result_array();
		
		foreach($file['filetypesentities'] as $key => $val){
			$file['entity_class_id'] = $val['entity_class_id'];
			$file['entity_type_id'][] = $val['entity_type_id'];
			}
		unset($file['filetypesentities']);
		

			$file['filetypegeozone'] = $this->db->get_where('pbf_filetypesgeozones',array('filetype_id'=>$filetype_id))->result_array();
	
		return $file;		
		}
		
	function get_frequency($frequency_id){
		
		return $this->db->get_where('pbf_filetypesfrequency',array('frequency_id'=>$frequency_id))->row_array();
		
		}
	
	function save_file_type($files, $filetypesentities,$geozone_id){
		
		$asset_access = $files['usergroup_id'];
		unset($files['usergroup_id']);
		
		$filetype_id = $files['filetype_id'];
		
		$this->db->delete('pbf_filetypesentities', array('filetype_id' => $filetype_id));
		$this->db->delete('pbf_filetypesgeozones', array('filetype_id' => $filetype_id));
		
		
		if(empty($files['filetype_id'])){
			
			$this->db->insert('pbf_filetypes', $files);
			$filetype_id = $this->db->insert_id();
			}
		else{
			
			$this->db->update('pbf_filetypes', $files, array('filetype_id' => $filetype_id));
			
			}
		
		$this->pbf->set_translation(array(	'text' => $files['filetype_name'],
											'text_key' => 'filetype_ky_'.$filetype_id) , 'files');
		
		foreach($filetypesentities['entity_type_id'] as $key => $val){
			
			$obj['filetype_entity_id']='';
			$obj['entity_class_id'] = $filetypesentities['entity_class_id'];	
			$obj['entity_type_id'] = $filetypesentities['entity_type_id'][$key];	
			$obj['filetype_id'] = $filetype_id;
			$this->db->insert('pbf_filetypesentities', $obj);
			}
		//sdl--	 
			foreach($geozone_id as $key => $val){	
			 $obj2['filetype_geozone_id']='';
			 $obj2['filetype_id'] = $filetype_id;
			 $obj2['geozone_id'] = $val;
		
			 $this->db->insert('pbf_filetypesgeozones', $obj2);
			}
		//--sdl
			
		$this->pbf->set_asset_access($filetype_id, 'data_filetype', $asset_access);
				
		return true;
		}
		
	function save_frequency($frequency){
		
		$frequency_id = $frequency['frequency_id'];
		
		if(empty($frequency['frequency_id'])){
			
			$this->db->insert('pbf_filetypesfrequency', $frequency);

			}
		else{
			
			$this->db->update('pbf_filetypesfrequency', $frequency, array('frequency_id' => $frequency_id));
			
			}
			
		$this->pbf->set_translation(array(	'text' => $frequency['frequency_title'],
											'text_key' => 'file_frq_ky_'.$frequency_id) , 'files');
													
		return $frequency_id;
					
		}
		
	function set_file_state($filetype_id, $state){
		
		$sql="UPDATE pbf_filetypes SET filetype_active='".$state."' WHERE filetype_id='".$filetype_id."'";
		return $this->db->simple_query($sql);
		}
		
	function del_file_type($filetype_id){
		
		$this->pbf->set_translation(array(	'text' => NULL,
											'text_key' => 'filetype_ky_'.$filetype_id) , 'files');
													
		$this->db->delete('pbf_filetypes', array('filetype_id' => $filetype_id));
		$this->db->delete('pbf_usersgroupsassets', array('asset_id' => $filetype_id, 'asset_link' => 'data_filetype'));
		return $this->db->delete('pbf_filetypesentities', array('filetype_id' => $filetype_id));
		
		}
		
	function del_frequency($frequency_id){
		
		$this->pbf->set_translation(array(	'text' => NULL,
											'text_key' => 'file_frq_ky_'.$frequency_id) , 'files');
		
		return $this->db->delete('pbf_filetypesfrequency', array('frequency_id' => $frequency_id));
		
		}
	function get_all_file_types ( ) {
		return $this->db->get('pbf_filetypes')->result_array();
	}
	
}