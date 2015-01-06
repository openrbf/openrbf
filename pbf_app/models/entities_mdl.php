<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Entities_mdl extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	function get_entities($num = 0, $filters) { // this is not a deprecation... the same function is in the _pbf lib for other reasons other than listing...
		$record_set = array ();
		$sql_append = " WHERE 1=1 ";
		$usergeozones = $this->session->userdata ( 'usergeozones' );
		
		$sql_append .= " AND pbf_entities.entity_class='" . $this->session->userdata ( 'entity_class' ) . "'";
		
		if (! empty ( $usergeozones )) {
			$sql_append .= " AND pbf_entities.entity_geozone_id IN (" . implode ( ',', $usergeozones ) . ")";
		}
		
		if (! empty ( $filters ['entity_name'] )) {
			
			$sql_append .= " AND (pbf_entities.entity_name LIKE '%" . $filters ['entity_name'] . "%' OR pbf_entitytypes.entity_type_abbrev LIKE '%" . $filters ['entity_name'] . "%' OR pbf_entities.entity_responsible_name LIKE '%" . $filters ['entity_name'] . "%' OR pbf_entities.entity_responsible_email LIKE '%" . $filters ['entity_name'] . "%')";
		}
		
		if (! empty ( $filters ['geozone_id'] )) {
			
			$sql_append .= " AND pbf_geozones.geozone_id='" . $filters ['geozone_id'] . "'";
		}
		
		$sql = "SELECT pbf_entities.entity_id,pbf_entitytypes.entity_type_abbrev,pbf_entities.entity_name,pbf_geozones.geozone_name,pbf_entitytypes.entity_type_id AS entity_type,pbf_entities.entity_contracttype,pbf_lkp_status.lookup_id AS entity_status,pbf_entities.entity_responsible_name,pbf_entities.entity_responsible_email,pbf_entities.entity_active FROM pbf_entities LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_lookups pbf_lkp_status ON (pbf_lkp_status.lookup_id=pbf_entities.entity_status AND pbf_lkp_status.lookup_linkfile='entity_status') " . $sql_append;
		
		$record_set ['entity_class_name'] = $this->get_entityclass ( $this->session->userdata ( 'entity_class' ) );
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT $num , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function get_groups($num = 0, $filters) {
		$sql = 'SELECT * FROM pbf_entitygroups';
		
		$record_set ['records_num'] = $this->db->get ( 'pbf_entitygroups' )->num_rows ();
		
		$sql .= " LIMIT $num , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function get_classes() {
		$record_set ['records_num'] = $this->db->get ( 'pbf_entityclasses' )->num_rows ();
		
		$record_set ['list'] = $this->db->get ( 'pbf_entityclasses' )->result_array ();
		
		return $record_set;
	}
	function get_types($num = 0, $filters) {
		$sql = "SELECT pbf_entitytypes.entity_type_id, pbf_entitytypes.entity_class_id,pbf_entitytypes.entity_type_name,pbf_entitytypes.entity_type_abbrev,pbf_entityclasses.entity_class_abbrev,pbf_entityclasses.entity_class_name FROM pbf_entitytypes JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entitytypes.entity_class_id) ORDER BY entity_type_name";
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT $num , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function get_entityclass($entity_class_id) {
		return $this->db->get_where ( 'pbf_entityclasses', array (
				'entity_class_id' => $entity_class_id 
		) )->row_array ();
	}
	function get_entitygroup($entity_group_id) {
		return $this->db->get_where ( 'pbf_entitygroups', array (
				'entity_group_id' => $entity_group_id 
		) )->row_array ();
	}
	function get_entitytype($entity_type_id) {
		return $this->db->get_where ( 'pbf_entitytypes', array (
				'entity_type_id' => $entity_type_id 
		) )->row_array ();
	}
	function save_group($group) {
		if (empty ( $group ['entity_group_id'] )) {
			return $this->db->insert ( 'pbf_entitygroups', $group );
		} else {
			return $this->db->update ( 'pbf_entitygroups', $group, array (
					'entity_group_id' => $group ['entity_group_id'] 
			) );
		}
	}
	function save_class($klass) {
		$usegroupassets = $klass ['user_group_assets'];
		
		unset ( $klass ['user_group_assets'] );
		
		unset ( $klass ['usergroup_id'] );
		
		if (empty ( $klass ['entity_class_id'] )) {
			$this->db->insert ( 'pbf_entityclasses', $klass );
			$entity_class_id = $this->db->insert_id ();
			echo 'entity class : ' . $entity_class_id;
			foreach ( $usegroupassets as $key => $ass ) {
				$usegroupassets [$key] ['asset_id'] = $entity_class_id;
			}
		} else {
			
			$this->db->update ( 'pbf_entityclasses', $klass, array (
					'entity_class_id' => $klass ['entity_class_id'] 
			) );
		}
		
		$this->save_usersgroupsassets ( $usegroupassets );
		$this->pbf->set_translation ( array (
				array (
						'text' => $klass ['entity_class_name'],
						'text_key' => 'etty_cls_ky_' . $entity_class_id 
				),
				array (
						'text' => $klass ['entity_class_abbrev'],
						'text_key' => 'etty_cls_abbrv_ky_' . $entity_class_id 
				) 
		), 'hfrentities' );
		
		return true; // Is this hard to do?
	}
	function save_usersgroupsassets($data) {
		foreach ( $data as $asset ) {
			if (empty ( $asset ['id'] )) {
				$this->db->insert ( 'pbf_usersgroupsassets', $asset );
			} else {
				$this->db->where ( 'id', $asset ['id'] );
				$this->db->update ( 'pbf_usersgroupsassets', $asset );
			}
		}
	}
	function save_type($type) {
		$entity_type_id = $type ['entity_type_id'];
		
		if (empty ( $type ['entity_type_id'] )) {
			$this->db->insert ( 'pbf_entitytypes', $type );
			$entity_type_id = $this->db->insert_id ();
		} else {
			$this->db->update ( 'pbf_entitytypes', $type, array (
					'entity_type_id' => $entity_type_id 
			) );
		}
		
		$this->pbf->set_translation ( array (
				array (
						'text' => $type ['entity_type_name'],
						'text_key' => 'etty_typ_ky_' . $entity_type_id 
				),
				array (
						'text' => $type ['entity_type_abbrev'],
						'text_key' => 'etty_typ_abbrv_ky_' . $entity_type_id 
				) 
		), 'hfrentities' );
		
		return $entity_type_id;
	}
	
	// function save_entity($entity){
	function save_entity($entity, $entitytime, $update = TRUE) {
		$this->db->delete ( 'pbf_entitiestime', array (
				'entity_id' => $entitytime ['entity_id'] 
		) );
		
		foreach ( $entitytime ['entity_active_time'] as $key => $val ) {
			
			$obj ['entitytime_id'] = '';
			$obj ['entity_id'] = $entitytime ['entity_id'];
			$obj ['entity_pbf_group_id'] = $entitytime ['entity_pbf_group_id_time'] [$key];
			$obj ['entity_pop'] = $entitytime ['entity_pop_time'] [$key];
			$obj ['entity_pop_year'] = $entitytime ['entity_pop_year_time'] [$key];
			$obj ['entity_type'] = $entitytime ['entity_type_time'] [$key];
			$obj ['entity_active'] = $entitytime ['entity_active_time'] [$key];
			$obj ['use_from'] = $entitytime ['use_from'] [$key];
			$obj ['use_to'] = $entitytime ['use_to'] [$key];
			
			$this->db->insert ( 'pbf_entitiestime', $obj );
		}
		
		if (empty ( $entity ['entity_id'] )) {
			return $this->db->insert ( 'pbf_entities', $entity );
		} else {
			$entity['update_flag'] = $update;
			return $this->db->update ( 'pbf_entities', $entity, array (
					'entity_id' => $entity ['entity_id'] 
			) );
		}
	}
	function get_last_entity() { // Return the last entity added
		$sql = "select entity_id from pbf_entities ORDER BY entity_id DESC LIMIT 1";
		$res = $this->db->query ( $sql )->row_array ();
		
		return $res ['entity_id'];
	}
	function get_entity($entity_id) {
		$sql = "SELECT pbf_entities.*,pbf_entitygroups.entity_group_name,pbf_entitygroups.entity_group_abbrev,pbf_banks.bank_id,pbf_banks.bank_name,pbf_banks.bank_name_abbrev,pbf_banks.bank_parent_id,pbf_banks_hq.bank_name as parent_bank_name, pbf_banks_hq.bank_name_abbrev as parent_bank_abbrev,pbf_entitytypes.entity_type_name,pbf_entitytypes.entity_type_abbrev,pbf_entityclasses.entity_class_name,pbf_entityclasses.entity_class_abbrev,pbf_entityclasses.entity_class_properties,pbf_geozones.geozone_id,pbf_geozones.geozone_name,pbf_geozones_parent.geozone_id AS parent_geozone_id,pbf_geozones_parent.geozone_name AS parent_geozone_name,pbf_geozones.geozone_equity_bonus AS district_equity_bonus FROM pbf_entities LEFT JOIN pbf_banks ON (pbf_banks.bank_id=pbf_entities.entity_bank_id) LEFT JOIN pbf_banks pbf_banks_hq ON (pbf_banks_hq.bank_id=pbf_banks.bank_parent_id) LEFT JOIN pbf_entitytypes ON (pbf_entities.entity_type =pbf_entitytypes.entity_type_id) LEFT JOIN pbf_entitygroups ON (pbf_entitygroups.entity_group_id=pbf_entities.entity_pbf_group_id) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_geozones pbf_geozones_parent ON (pbf_geozones_parent.geozone_id=pbf_geozones.geozone_parentid) WHERE pbf_entities.entity_id='" . $entity_id . "'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_entity_form($entity_id) {
		$sql = "SELECT pbf_entities.*,pbf_entitygroups.entity_group_name,pbf_entitygroups.entity_group_abbrev,pbf_banks.bank_id,pbf_banks.bank_name,pbf_banks.bank_name_abbrev,pbf_banks.bank_parent_id,pbf_banks_hq.bank_name as parent_bank_name, pbf_banks_hq.bank_name_abbrev as parent_bank_abbrev,pbf_entitytypes.entity_type_name,pbf_entitytypes.entity_type_abbrev,pbf_entityclasses.entity_class_name,pbf_entityclasses.entity_class_abbrev,pbf_entityclasses.entity_class_properties,pbf_geozones.geozone_id,pbf_geozones.geozone_name,pbf_geozones_parent.geozone_id AS parent_geozone_id,pbf_geozones_parent.geozone_name AS parent_geozone_name FROM pbf_entities LEFT JOIN pbf_banks ON (pbf_banks.bank_id=pbf_entities.entity_bank_id) LEFT JOIN pbf_banks pbf_banks_hq ON (pbf_banks_hq.bank_id=pbf_banks.bank_parent_id) LEFT JOIN pbf_entitytypes ON (pbf_entities.entity_type =pbf_entitytypes.entity_type_id) LEFT JOIN pbf_entitygroups ON (pbf_entitygroups.entity_group_id=pbf_entities.entity_pbf_group_id) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_geozones pbf_geozones_parent ON (pbf_geozones_parent.geozone_id=pbf_geozones.geozone_parentid) WHERE pbf_entities.entity_id='" . $entity_id . "'";
		
		$entities ['entity'] = $this->db->query ( $sql )->row_array ();
		$entities ['entitytime'] = $this->db->get_where ( 'pbf_entitiestime', array (
				'entity_id' => $entity_id 
		) )->result_array ();
		
		// return $this->db->query($sql)->row_array();
		
		return $entities;
	}
	function get_raw_entities($entity_type = '', $entity_geozone_id = '', $parent_entity_geozone_id = '') {
		$sql = "SELECT pbf_entities.*,pbf_geozones.geozone_parentid,pbf_banks.bank_parent_id AS entity_bank_hq_id FROM pbf_entities LEFT JOIN pbf_banks ON (pbf_banks.bank_id=pbf_entities.entity_bank_id) LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id = pbf_entities.entity_geozone_id) WHERE pbf_entities.entity_active='1' AND pbf_entities.entity_class='1' " . (($entity_type == '') ? '' : " AND pbf_entities.entity_type='" . $entity_type . "'") . " " . (($entity_geozone_id == '') ? '' : " AND pbf_entities.entity_geozone_id='" . $entity_geozone_id . "'") . " " . (($parent_entity_geozone_id == '') ? '' : " AND pbf_geozones.geozone_parentid='" . $parent_entity_geozone_id . "'") . " ";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function delentity($entity_id) {
		$filename = $this->get_entity ( $entity_id );
		
		unlink ( FCPATH . '/cside/contents/docs/contracts/' . $filename ['entity_contractpath'] );
		
		unlink ( FCPATH . 'cside/images/portal/' . $filename ['entity_picturepath'] );
		
		return $this->db->delete ( 'pbf_entities', array (
				'entity_id' => $entity_id 
		) );
	}
	function delgroup($entity_group_id) {
		
		// COULDN'T DELETE A CLASS THAT CONTAINS ENTITIES...
		if ($this->db->query ( "SELECT * FROM pbf_entities WHERE entity_pbf_group_id ='" . $entity_group_id . "'" )->num_rows () == 0) {
			return $this->db->delete ( 'pbf_entitygroups', array (
					'entity_group_id' => $entity_group_id 
			) );
		} else {
			return false;
		}
	}
	function delclass($entity_class_id) {
		
		// COULDN'T DELETE A CLASS THAT CONTAINS ENTITIES...
		if ($this->db->query ( "SELECT * FROM pbf_entities WHERE entity_class ='" . $entity_class_id . "'" )->num_rows () == 0) {
			return $this->db->delete ( 'pbf_entityclasses', array (
					'entity_class_id' => $entity_class_id 
			) );
		} else {
			return false;
		}
	}
	function deltype($entity_type_id) {
		
		// COULDN'T DELETE A TYPE THAT CONTAINS ENTITIES...
		if ($this->db->query ( "SELECT * FROM pbf_entities WHERE entity_type ='" . $entity_type_id . "'" )->num_rows () == 0) {
			return $this->db->delete ( 'pbf_entitytypes', array (
					'entity_type_id' => $entity_type_id 
			) );
		} else {
			return false;
		}
	}
	function delete_selected_entities($entity_id) {
		$this->db->where_in ( 'entity_id', $entity_id );
		return $this->db->delete ( 'pbf_entities' );
	}
	function delinfo($entity_id, $info) {
		$filename = $this->get_entity ( $entity_id );
		$sql = "";
		
		if ($info == 'entity_contractpath') {
			
			unlink ( FCPATH . '/cside/contents/docs/contracts/' . $filename ['entity_contractpath'] );
			
			$sql .= " , entity_contractvalidity_start = '2010-01-01', entity_contractvalidity_end = '2010-01-01' ";
		} elseif ($info == 'entity_picturepath') {
			
			unlink ( FCPATH . 'cside/images/portal/' . $filename ['entity_picturepath'] );
		}
		
		return $this->db->simple_query ( "UPDATE pbf_entities SET " . $info . " = '' " . $sql . " WHERE entity_id='" . $entity_id . "'" );
	}
	function set_entity_state($entity_id, $state) {
		return $this->db->simple_query ( "UPDATE pbf_entities SET entity_active='" . $state . "' WHERE entity_id='" . $entity_id . "'" );
	}
	function get_geozone_compl($geozone_id = 0, $type = 'region', $cond = "") {
		// $cond='';
		if ($type == 'region') {
			$sql = "select geozone_id,geozone_name,geozone_pop_year,geozone_active from pbf_geozones where geozone_parentid IS NULL AND geozone_active=1 $cond ORDER BY geozone_name ASC ";
		} elseif ($type == 'district') {
			$sql = "select geozone_id,geozone_name,geozone_pop_year,geozone_active from pbf_geozones where geozone_parentid = $geozone_id AND geozone_active=1 $cond ORDER BY geozone_name ASC ";
			
			// echo $sql."<br/><br/>";
		}
		// echo $sql;
		
		$sql2 = "select geozone_id,geozone_name from pbf_geozones where geozone_parentid IS NULL AND geozone_active=1";
		$list_of_regions = $this->db->query ( $sql )->result_array ();
		
		return $list_of_regions;
	}
	function get_region_entities($geozone_id = 0, $type = 'region', $cond = '') {
		if ($type == 'region') {
			$sql = "SELECT * FROM `pbf_entities`,pbf_geozones,pbf_entitytypes WHERE  pbf_entitytypes.entity_type_id=pbf_entities.entity_type  AND pbf_entitytypes.entity_class_id='1' AND  entity_geozone_id IN ( select geozone_id  from pbf_geozones where geozone_parentid=$geozone_id  AND geozone_active=1 $cond) AND entity_geozone_id=geozone_id   AND entity_active=1 ORDER BY geozone_name ASC, entity_name ASC ";
		} else {
			$sql = "SELECT * FROM `pbf_entities`,pbf_geozones,pbf_entitytypes WHERE   pbf_entitytypes.entity_type_id=pbf_entities.entity_type  AND pbf_entitytypes.entity_class_id='1' AND entity_geozone_id = $geozone_id AND entity_geozone_id=geozone_id   AND entity_active=1  AND geozone_active=1 ORDER BY geozone_name ASC, entity_name ASC ";
		}
		// echo $sql."<br/><br/>";
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		return $record_set;
	}
	function count_entities($zone_id = '', $active = true) {
		$sql = "SELECT COUNT(*) nb_entities FROM pbf_entities";
		if (! empty ( $zone_id )) {
			
			$sql .= " WHERE entity_geozone_id = '" . $zone_id . "'";
		}
		
		if ($active) {
			$sql .= " AND entity_active=1";
		}
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_quarter($entity_id) {
		$sql = "SELECT pbf_datafile.datafile_id, pbf_datafile.entity_id, pbf_datafile.datafile_quarter, pbf_datafile.datafile_year, pbf_datafile.datafile_valid_reg,pbf_datafile.datafile_status, pbf_datafile.datafile_modified, pbf_entities.entity_id,pbf_entities.entity_geozone_id, pbf_geozones.geozone_id,pbf_geozones.geozone_parentid FROM pbf_datafile, pbf_entities,pbf_geozones";
		$sql .= " WHERE pbf_datafile.entity_id = pbf_entities.entity_id";
		$sql .= " AND pbf_datafile.entity_id =" . $entity_id;
		$sql .= " AND pbf_entities.entity_geozone_id = pbf_geozones.geozone_id";
		$sql .= " AND pbf_datafile.datafile_status =1";
		$sql .= " GROUP BY pbf_datafile.datafile_year,pbf_datafile.datafile_quarter";
		$sql .= " ORDER BY pbf_datafile.datafile_year DESC,pbf_datafile.datafile_quarter DESC LIMIT 0 , 4";
		return $this->db->query ( $sql )->result_array ();
	}
	
	function test_update_data_entity($entity_id){
		$query = $this->db->select ( 'update_flag' )->from ( 'pbf_entities' )->where ( 'entity_id', $entity_id );
		return $query->get ()->row_array ();
	}
		
	function get_entities_to_update(){
		$query = $this->db->select ( 'entity_id' )->from ( 'pbf_entities' )->where ( 'update_flag', 1 );
		return $query->get ()->result_array ();
	}
	function get_entity_geozone_id($entity_id){
		$query=$this->db->select ('entity_geozone_id')->from ( 'pbf_entities' )->where ( 'entity_id',$entity_id);
		return $query->get ()->row_array ();
	}
	
	function set_update_flag($entity_id){
		$data=array('update_flag'=>0);
		$this->db->where_in('entity_id', $entity_id);
		return $this->db->update('pbf_entities', $data); 
	}
	function get_entities_topublish($region_list){
		$query=$this->db->select( 'entity_id ' )->from ( 'pbf_entities' )-> where_in('entity_geozone_id',$region_list)->where( 'entity_active ',1 );
		return $query->get()->result_array();
	}
}