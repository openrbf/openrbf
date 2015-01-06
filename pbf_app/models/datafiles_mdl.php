<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Datafiles_mdl extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	function get_datafile_uploadFile($entity_id, $datafile_moth, $datafile_year, $data_quarter, $filetype_name) {
		$sql = "SELECT pbf_datafile.datafile_id,pbf_datafile.filetype_id,pbf_datafile.entity_id,pbf_datafile.datafile_month,pbf_datafile.datafile_quarter,pbf_datafile.datafile_year,pbf_datafile.datafile_file_upload,pbf_filetypes.filetype_id,pbf_filetypes.filetype_name FROM pbf_datafile,pbf_filetypes";
		$sql .= " WHERE pbf_datafile.filetype_id=pbf_filetypes.filetype_id";
		$sql .= " AND pbf_datafile.entity_id='" . $entity_id . "'";
		$sql .= " AND pbf_datafile.datafile_month='" . $datafile_moth . "'";
		$sql .= " AND pbf_datafile.datafile_quarter='" . $data_quarter . "'";
		$sql .= " AND pbf_datafile.datafile_year='" . $datafile_year . "'";
		$sql .= " AND pbf_filetypes.filetype_name='" . $filetype_name . "'";
		return $this->db->query ( $sql )->result_array ();
	}
	function get_datafile_last_three_fraud() {
		$filetype_name = "Fraude";
		$sql = "SELECT pbf_datafile.datafile_id,pbf_datafile.filetype_id,pbf_datafile.entity_id,pbf_datafile.datafile_month,pbf_datafile.datafile_quarter,pbf_datafile.datafile_year,pbf_datafile.datafile_file_upload,pbf_datafile.datafile_valid_reg,pbf_datafile.datafile_created,pbf_datafile.datafile_info,pbf_filetypes.filetype_id,pbf_filetypes.filetype_name,pbf_entities.entity_id,pbf_entities.entity_name,pbf_entities.entity_geozone_id,pbf_geozones.geozone_id,pbf_geozones.geozone_name,pbf_geozones.geozone_parentid AS level_0 FROM pbf_datafile,pbf_filetypes,pbf_entities,pbf_geozones";
		$sql .= " WHERE pbf_datafile.filetype_id=pbf_filetypes.filetype_id";
		$sql .= " AND pbf_datafile.entity_id=pbf_entities.entity_id";
		$sql .= " AND pbf_entities.entity_geozone_id=pbf_geozones.geozone_id";
		$sql .= " AND pbf_filetypes.filetype_name='" . $filetype_name . "'";
		$sql .= " AND pbf_datafile.datafile_valid_reg=1";
		$sql .= " ORDER BY pbf_datafile.datafile_created DESC LIMIT 0,3";
		return $this->db->query ( $sql )->result_array ();
	}
	function get_datafiles_old($num = 0, $filters) {
		$this->load->model ( 'entities_mdl' );
		$record_set = array ();
		$sql_append = " WHERE pbf_datafile.datafile_status='0' ";
		$bind_region = "";
		
		$sql_append .= " AND pbf_entities.entity_class='" . $this->session->userdata ( 'data_entity_class' ) . "' ";
		
		if (! empty ( $filters ['geozone_id'] )) {
			
			$sql_append .= " AND pbf_entities.entity_geozone_id='" . $filters ['geozone_id'] . "' ";
		}
		
		if (! empty ( $filters ['entity_id'] )) {
			
			$sql_append .= " AND pbf_entities.entity_id='" . $filters ['entity_id'] . "' ";
		}
		
		if (! empty ( $filters ['datafile_month'] )) {
			
			$sql_append .= " AND pbf_datafile.datafile_month='" . $filters ['datafile_month'] . "' ";
		}
		
		if (! empty ( $filters ['datafile_year'] )) {
			
			$sql_append .= " AND pbf_datafile.datafile_year='" . $filters ['datafile_year'] . "' ";
		}
		
		if (! empty ( $filters ['filetype_id'] )) {
			
			$sql_append .= " AND pbf_datafile.filetype_id='" . $filters ['filetype_id'] . "' ";
		}
		
		$usergeozones = $this->session->userdata ( 'usergeozones' );
		
		if (! empty ( $usergeozones )) {
			
			$sql_append .= " AND pbf_geozones.geozone_id IN (" . implode ( ',', $usergeozones ) . ") ";
			// $sql_append.=" AND pbf_datafile.datafile_author_id = '".$this->session->userdata('user_id')."' ";
		}
		$user_entity = $this->session->userdata ( 'user_entity' );
		
		if ((! empty ( $user_entity )) and ($this->pbf->check_group_entityassociated ( $this->session->userdata ( 'usergroup_id' ) ) == '1')) {
			
			$sql_append .= " AND pbf_datafile.entity_id = $user_entity ";
		}
		
		$sql = "SELECT datafile_id,pbf_datafile.entity_id,(entity_name) AS data_file,pbf_geozones.geozone_name,pbf_datafile.filetype_id,filetype_name,datafile_month,datafile_year, pbf_datafile.datafile_modified AS datafile_modified,user_fullname,datafile_valid_reg,pl.lookup_title_abbrev AS status FROM pbf_datafile JOIN pbf_entities ON (pbf_entities.entity_id=pbf_datafile.entity_id) JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) LEFT JOIN pbf_users ON (pbf_users.user_id=datafile_modified_id) JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_usersgroupsassets ON(pbf_usersgroupsassets.asset_id=pbf_datafile.filetype_id AND pbf_usersgroupsassets.asset_link='data_filetype') LEFT join pbf_lookups pl on lookup_id = datafile_state " . $sql_append . " AND pbf_usersgroupsassets.usersgroup_id='" . $this->session->userdata ( 'usergroup_id' ) . "' ORDER BY pbf_datafile.datafile_modified DESC";
		
		// echo $sql;
		
		$record_set ['entity_class_name'] = $this->entities_mdl->get_entityclass ( $this->session->userdata ( 'data_entity_class' ) );
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT $num , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function get_datafiles($num = 0, $filters) {
		$this->load->model ( 'entities_mdl' );
		$record_set = array ();
		$sql_append = " WHERE pbf_datafile.datafile_valid_nat='0' AND IF(pbf_entitiestime.entity_active IS NOT NULL,pbf_entitiestime.entity_active, pbf_entities.entity_active) ='1'";
		$bind_region = "";
		
		$sql_append .= " AND pbf_entities.entity_class='" . $this->session->userdata ( 'data_entity_class' ) . "' ";
		
		if (! empty ( $filters ['geozone_id'] )) {
			
			$sql_append .= " AND pbf_entities.entity_geozone_id='" . $filters ['geozone_id'] . "' ";
		}
		
		if (! empty ( $filters ['entity_id'] )) {
			
			$sql_append .= " AND pbf_entities.entity_id='" . $filters ['entity_id'] . "' ";
		}
		
		if (! empty ( $filters ['datafile_month'] )) {
			
			$sql_append .= " AND pbf_datafile.datafile_month='" . $filters ['datafile_month'] . "' ";
		}
		
		if (! empty ( $filters ['datafile_year'] )) {
			
			$sql_append .= " AND pbf_datafile.datafile_year='" . $filters ['datafile_year'] . "' ";
		}
		
		if (! empty ( $filters ['filetype_id'] )) {
			
			$sql_append .= " AND pbf_datafile.filetype_id='" . $filters ['filetype_id'] . "' ";
		}
		
		$usergeozones = $this->session->userdata ( 'usergeozones' );
		
		if (! empty ( $usergeozones )) {
			
			$sql_append .= " AND pbf_geozones.geozone_id IN (" . implode ( ',', $usergeozones ) . ") ";
			// $sql_append.=" AND pbf_datafile.datafile_author_id = '".$this->session->userdata('user_id')."' ";
		}
		$user_entity = $this->session->userdata ( 'user_entity' );
		
		if ((! empty ( $user_entity )) and ($this->pbf->check_group_entityassociated ( $this->session->userdata ( 'usergroup_id' ) ) == '1')) {
			
			$sql_append .= " AND pbf_datafile.entity_id = $user_entity ";
		}
		
		$sql = "SELECT datafile_id,pbf_datafile.entity_id,CONCAT(entity_name,'','') AS data_file,pbf_geozones.geozone_name,pbf_datafile.filetype_id,filetype_name,datafile_month,datafile_year,SUBSTR(datafile_modified,1,10) AS datafile_modified,user_fullname,datafile_valid_reg,pl.lookup_title_abbrev AS status FROM pbf_datafile JOIN pbf_entities ON (pbf_entities.entity_id=pbf_datafile.entity_id) JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) LEFT JOIN pbf_users ON (pbf_users.user_id=datafile_modified_id) JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_usersgroupsassets ON (pbf_usersgroupsassets.asset_id=pbf_datafile.filetype_id AND pbf_usersgroupsassets.asset_link='data_filetype') LEFT join pbf_lookups pl on lookup_id = datafile_valid_reg LEFT JOIN pbf_entitiestime ON ((pbf_entitiestime.entity_id = pbf_entities.entity_id) AND (LAST_DAY('" . $filters ['datafile_year'] . "-" . $filters ['datafile_month'] . "-1') BETWEEN pbf_entitiestime.use_from AND pbf_entitiestime.use_to))" . $sql_append . " AND pbf_usersgroupsassets.usersgroup_id='" . $this->session->userdata ( 'usergroup_id' ) . "' ORDER BY pbf_datafile.datafile_modified DESC";
		
		// echo $sql;
		
		$record_set ['entity_class_name'] = $this->entities_mdl->get_entityclass ( $this->session->userdata ( 'data_entity_class' ) );
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT $num , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function get_datafile_header($datafile_id, $entity_id, $datafile_month, $datafile_year, $filetype_id) {
		$sql = "SELECT pbf_filetypes.filetype_id,pbf_filetypes.filetype_name,pbf_filetypes.filetype_template,pbf_datafile.datafile_id,pbf_entities.entity_id,CONCAT(IF(geozone_lvl1.geozone_name!='',CONCAT(geozone_lvl1.geozone_name,' - '),''),geozone_lvl2.geozone_name,' - ',pbf_entities.entity_name,'','') AS entity_name,pbf_datafile.datafile_remark,IF(pbf_datafile.datafile_month IS NULL," . $datafile_month . ",pbf_datafile.datafile_month) AS datafile_month, IF(pbf_datafile.datafile_year IS NULL," . $datafile_year . ",pbf_datafile.datafile_year) AS datafile_year, pbf_datafile.datafile_total,pbf_datafile.datafile_info FROM pbf_filetypes LEFT JOIN pbf_datafile ON (pbf_datafile.datafile_id='" . $datafile_id . "' AND pbf_datafile.entity_id='" . $entity_id . "' AND pbf_datafile.datafile_month='" . $datafile_month . "' AND pbf_datafile.datafile_year='" . $datafile_year . "') LEFT JOIN pbf_entities ON (pbf_entities.entity_id='" . $entity_id . "') LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_geozones geozone_lvl2 ON (geozone_lvl2.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_geozones geozone_lvl1 ON (geozone_lvl1.geozone_id=geozone_lvl2.geozone_parentid) WHERE pbf_filetypes.filetype_id='" . $filetype_id . "'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_datafile_details($datafile_id, $filetype_id, $entity_id, $datafile_month, $datafile_year, $lang) {
		$datafile_quarter = $this->pbf->get_current_quarterBy_month ( $datafile_month );
		
		// query to be adapted to the period when the indicatot is in use... passing month and year as parameters
		
		$sql = "SELECT pbf_indicators.indicator_id,pbf_datafiledetails.datafiledetail_id,pbf_indicators.indicator_vartype,pbf_indicatorstranslations.indicator_title,pbf_datafiledetails.indicator_claimed_value,pbf_datafiledetails.indicator_verified_value,pbf_datafiledetails.indicator_verified_value,IF(pbf_datafiledetails.indicator_tarif IS NULL,IF(pbf_indicatorstarif.indicatortarif_tarif IS NULL,pbf_indicatorsfileypes.default_tarif,pbf_indicatorstarif.indicatortarif_tarif),pbf_datafiledetails.indicator_tarif)AS indicator_tarif,pbf_datafiledetails.indicator_montant,pbf_indicatorsfileypes.indicator_category_id FROM pbf_indicators LEFT JOIN pbf_datafiledetails ON (pbf_indicators.indicator_id=pbf_datafiledetails.indicator_id AND pbf_datafiledetails.datafile_id='" . $datafile_id . "') LEFT JOIN pbf_indicatorsfileypes ON (pbf_indicatorsfileypes.indicator_id=pbf_indicators.indicator_id) LEFT JOIN pbf_entities ON (pbf_entities.entity_id='" . $entity_id . "') LEFT JOIN pbf_indicatorstranslations ON pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id LEFT JOIN pbf_indicatorstarif ON 
		  (pbf_indicatorstarif.indicatortarif_monthto>='" . $datafile_month . "' 
		    AND pbf_indicatorstarif.indicatortarif_monthfrom<='" . $datafile_month . "'
	  AND pbf_indicatorstarif.indicatortarif_year='" . $datafile_year . "' AND ((pbf_indicatorstarif.indicatortarif_entity_id=pbf_entities.entity_id AND pbf_indicatorstarif.indicatortarif_geozone_id=pbf_entities.entity_geozone_id) AND (pbf_indicatorstarif.indicatortarif_entity_type_id=pbf_entities.entity_type AND pbf_indicatorstarif.indicatortarif_entity_class_id=pbf_entities.entity_class)) AND pbf_indicatorstarif.indicatortarif_filetype_id=pbf_indicatorsfileypes.filetype_id) 
		   WHERE pbf_indicatorsfileypes.filetype_id='" . $filetype_id . "' AND (LAST_DAY('" . $datafile_year . "-" . $datafile_month . "-1') BETWEEN pbf_indicatorsfileypes.use_from AND pbf_indicatorsfileypes.use_to) AND pbf_indicatorstranslations.indicator_language ='" . $lang . "' ORDER BY pbf_indicatorsfileypes.order";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function save_file($dataheader, $post_arr, $update = TRUE) {
		$this->load->model ( 'files_mdl' );
		$filetype_prop = $this->files_mdl->get_file_type ( $dataheader ['filetype_id'] );
		
		if ($dataheader ['datafile_id'] == '' || is_null ( $dataheader ['datafile_id'] )) {
			
			$existing = $this->check_file_exist ( $dataheader ['entity_id'], $dataheader ['datafile_month'], $dataheader ['datafile_year'], $dataheader ['filetype_id'] );
			
			if (! empty ( $existing )) {
				return true;
			} 

			else {
				$headersaved = $this->db->insert ( 'pbf_datafile', $dataheader );
				$data_file_key = $this->db->insert_id ();
			}
		} else { // update datafile
			$dataheader['update_flag'] = $update;
			$headersaved = $this->db->update ( 'pbf_datafile', $dataheader, array (
					'datafile_id' => $dataheader ['datafile_id'] 
			) );
		}
		
		$hard_coded_fields = array (
				'indicator_claimed_value',
				'indicator_verified_value',
				'indicator_validated_value',
				'indicator_tarif',
				'indicator_montant' 
		);
		
		foreach ( $post_arr ['indicator_id'] as $k => $v ) {
			
			$datadetails ['indicator_id'] = $post_arr ['indicator_id'] [$k];
			
			foreach ( $hard_coded_fields as $field ) {
				
				if (isset ( $post_arr [$field] [$k] )) {
					
					$datadetails [$field] = (isset ( $post_arr [$field] [$k] ) && $post_arr [$field] [$k] == "-") ? NULL : str_replace ( ",", "", $post_arr [$field] [$k] );
				}
			}
			
			if (! isset ( $post_arr ['datafiledetail_id'] [$k] ) || $post_arr ['datafiledetail_id'] [$k] == '') {
				$datadetails ['datafile_id'] = empty ( $data_file_key ) ? $dataheader ['datafile_id'] : $data_file_key;
				$detailesaved = $this->db->insert ( 'pbf_datafiledetails', $datadetails );
			} else {
				$detailesaved = $this->db->update ( 'pbf_datafiledetails', $datadetails, array (
						'datafiledetail_id' => $post_arr ['datafiledetail_id'] [$k] 
				) );
			}
		}
		
		if ($detailesaved || $headersaved) {
			return true;
		} else {
			return false;
		}
	}
	function check_file_exist($entity_id, $datafile_month, $datafile_year, $filetype_id) {
		$sql = "SELECT datafile_id FROM pbf_datafile WHERE datafile_month='" . $datafile_month . "' AND datafile_year='" . $datafile_year . "' AND filetype_id='" . $filetype_id . "' AND entity_id='" . $entity_id . "'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function delete_datafile($datafile_id) {
		$affected_tables = array (
				'pbf_datafile',
				'pbf_datafiledetails' 
		);
		$this->db->where_in ( 'datafile_id', $datafile_id );
		$this->db->delete ( $affected_tables );
		return 1; // issue CodeIgniter
	}
	function validate($datafile_id, $value) {
		if (isset ( $value ) && $value != '0') {
			$value = 1;
		} else {
			$value = 0;
		}
		
		$values = array (
				'datafile_valid_reg' => $value 
		);
		$this->db->where ( 'datafile_id', $datafile_id )->update ( 'pbf_datafile', $values );
		
		return $this->db->affected_rows () > 0;
	}
	function get_datafile_status($data_file_id) {
		$query = $this->db->select ( 'datafile_valid_reg' )->from ( 'pbf_datafile' )->where ( 'datafile_id', $data_file_id );
		
		return $query->get ()->row_array ();
	}
	
	function test_update_data_datafile($datafile_id){
		$query = $this->db->select ( 'update_flag' )->from ( 'pbf_datafile' )->where ( 'datafile_id', $datafile_id );
		return $query->get ()->row_array ();
	
	}
	
	function get_files_to_update(){
		$query = $this->db->select ( 'datafile_id,entity_id,filetype_id,datafile_month,datafile_quarter,datafile_year' )->from ( 'pbf_datafile' )->where ( 'update_flag', 1 );
		return $query->get ()->result_array ();
	}
	
	function get_files_to_update_entity($entity_id){
		$query = $this->db->select ( 'datafile_id,entity_id,filetype_id,datafile_month,datafile_quarter,datafile_year' )->from ( 'pbf_datafile' )->where ( array('entity_id' => $entity_id));
		return $query->get ()->result_array ();
	}
	
	function set_update_flag($datafiles){
		$data=array('update_flag'=>0);
		$this->db->where_in('datafile_id', $datafiles);
		return $this->db->update('pbf_datafile', $data); 
	}
}