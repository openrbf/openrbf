<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Pbf_mdl extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	function set_eventlog($eventlog) {
		$this->db->insert ( 'pbf_syseventlog', $eventlog );
	}
	function set_asset_access($asset_id, $asset_link, $asset_access) {
		$this->db->delete ( 'pbf_usersgroupsassets', array (
				'asset_id' => $asset_id,
				'asset_link' => $asset_link 
		) );
		
		foreach ( $asset_access as $access ) {
			
			$this->db->insert ( 'pbf_usersgroupsassets', array (
					'asset_id' => $asset_id,
					'asset_link' => $asset_link,
					'usersgroup_id' => $access 
			) );
		}
		
		return true;
	}
	function get_donor_name($id_donor_config) {
		$sql = "SELECT pbf_donors.donor_name FROM pbf_donors LEFT JOIN pbf_donorsconfig ON (pbf_donors.donor_id=pbf_donorsconfig.donor_id)
		WHERE pbf_donorsconfig.donorconfig_id='" . $id_donor_config . "' LIMIT 1";
		return $this->db->query ( $sql )->row_array ();
	}
	function get_menus() {
		return $this->db->get ( 'pbf_appmenu' )->result_array ();
	}
	function get_geozone_breadcrumb($geozone_id) {
		$sql = "SELECT geozone_lvl1.geozone_id AS lvl1_link,geozone_lvl1.geozone_name AS lvl1_title,geozone_lvl2.geozone_id AS lvl2_link,geozone_lvl2.geozone_name AS lvl2_title FROM pbf_geozones geozone_lvl2 LEFT JOIN pbf_geozones geozone_lvl1 ON (geozone_lvl1.geozone_id=geozone_lvl2.geozone_parentid) WHERE geozone_lvl2.geozone_id='" . $geozone_id . "'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_geozones_by_parent_geo_id($geo_id) {
		if ($geo_id == '') {
			
			$sql = "SELECT geozone_id,geo_id,geozone_name,geozone_pop,geozone_pop_year,AsText(geozone_htmlmap) AS geo_cords FROM pbf_geozones WHERE geozone_parentid IS NULL AND geozone_active = '1' ORDER BY geozone_name";
		} else {
			
			$sql = "SELECT geozone_id,geo_id,geozone_name,AsText(geozone_htmlmap) AS geo_cords,geozone_active,geozone_parentid FROM pbf_geozones WHERE geo_id = (SELECT geo_parent FROM pbf_geo WHERE pbf_geo.geo_id='" . $geo_id . "') AND geozone_active = '1' ORDER BY geozone_active DESC, geozone_name ASC";
		}
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_pop_cible_projected($asso_types, $entity_group_id, $zones) {
		$sql = "SELECT entity_pop AS entity_pop, entity_pop_year AS entity_pop_year FROM pbf_entities WHERE entity_pbf_group_id='" . $entity_group_id . "' AND entity_type IN (" . implode ( ',', $asso_types ) . ") AND pbf_entities.entity_geozone_id IN (" . implode ( ',', $zones ) . ")";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_pop_cible($asso_types, $entity_group_id, $zones) {
		$sql = "SELECT SUM(entity_pop) AS entity_pop FROM pbf_entities WHERE entity_pbf_group_id='" . $entity_group_id . "' AND entity_type IN (" . implode ( ',', $asso_types ) . ") AND pbf_entities.entity_geozone_id IN (" . implode ( ',', $zones ) . ")";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_zone_population($zone_id) {
		$sql = "SELECT SUM(round(entity_pop)) population FROM pbf_entities ent LEFT JOIN pbf_geozones geo on geo.geozone_id = ent.entity_geozone_id 
            WHERE ent.entity_active=1";
		
		if ($zone_id != '') {
			$id = $this->get_rqst_geozone_ids ( $zone_id, TRUE );
			$ids = empty ( $id ) ? $zone_id : $this->get_rqst_geozone_ids ( $zone_id, TRUE );
			$sql .= " AND geo.geozone_id IN ('" . $ids . "')";
		}
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_entity_breadcrumb($entity_id) {
		$sql = "SELECT CONCAT(geozone_lvl1.geo_id,'/',geozone_lvl1.geozone_id) AS lvl1_link,geozone_lvl1.geozone_name AS lvl1_title,(geozone_lvl2.geozone_id) AS lvl2_link,geozone_lvl2.geozone_name AS lvl2_title FROM pbf_entities LEFT JOIN pbf_geozones geozone_lvl2 ON (geozone_lvl2.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_geozones geozone_lvl1 ON (geozone_lvl1.geozone_id=geozone_lvl2.geozone_parentid) WHERE pbf_entities.entity_id='" . $entity_id . "'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_runnable_script($year, $month) {
		$sql = "SELECT pbf_computationdetails.computation_entity_class_id,pbf_computationdetails.computation_entity_type_id,pbf_computationdetails.computation_entity_group_id,computation_entity_ass_group_id,pbf_computationdetails.computation_geozone_id,pbf_computationdetails.computation_calculation_basis,pbf_computationdetails.computation_main_logic,pbf_computationdetails.computation_score_condition_one,pbf_computationdetails.computation_score_fact_one,pbf_computationdetails.computation_score_condition_two,pbf_computationdetails.computation_score_fact_two,pbf_computationdetails.fav_action,pbf_computationdetails.consider_score FROM pbf_computation,pbf_computationdetails WHERE pbf_computation.computation_id=pbf_computationdetails.computation_id AND ('" . $year . "-" . $month . "-" . cal_days_in_month ( CAL_GREGORIAN, $month, $month ) . "' BETWEEN pbf_computation.computation_date_start AND pbf_computation.computation_date_end)";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_assoc_group($year, $month, $computation_geozone_id, $entity_group_id) {
		$computation_geozone_id = empty ( $computation_geozone_id ) ? "" : " AND pbf_computationdetails.computation_geozone_id='" . $computation_geozone_id . "'";
		
		$sql = "SELECT DISTINCT(pbf_computationdetails.computation_entity_ass_group_id) FROM pbf_computationdetails LEFT JOIN pbf_computation ON (pbf_computation.computation_id=pbf_computationdetails.computation_id) WHERE pbf_computationdetails.computation_entity_group_id='" . $entity_group_id . "' AND ('" . $year . "-" . $month . "-" . cal_days_in_month ( CAL_GREGORIAN, $month, $month ) . "' BETWEEN pbf_computation.computation_date_start AND pbf_computation.computation_date_end)" . $computation_geozone_id;
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_active_geozones() {
		$sql = "SELECT pbf_geozones.geozone_id,pbf_geozones.geozone_name FROM pbf_geozones JOIN pbf_geo ON (pbf_geo.geo_id=pbf_geozones.geo_id AND pbf_geo.geo_active='1') WHERE pbf_geozones.geozone_active='1'";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_active_geozones_in_zone($zone) {
		$sql = "SELECT pbf_geozones.geozone_id,pbf_geozones.geozone_name FROM pbf_geozones JOIN pbf_geo ON (pbf_geo.geo_id=pbf_geozones.geo_id AND pbf_geo.geo_active='1') WHERE pbf_geozones.geozone_active='1' AND pbf_geozones.geozone_parentid='" . $zone . "'";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_lookup($lookup_id) {
		$sql = "SELECT lookup_id,CONCAT(lookup_title,' ',IF(lookup_title_abbrev!='',CONCAT('(',lookup_title_abbrev,')'),'')) AS lookup_title FROM pbf_lookups WHERE lookup_id='" . $lookup_id . "'";
		return $this->db->query ( $sql )->row_array ();
	}
	function get_lookups($lookup_linkfile) {
		$sql = "SELECT lookup_id,CONCAT(lookup_title,' ',IF(lookup_title_abbrev!='',CONCAT('(',lookup_title_abbrev,')'),'')) AS lookup_title FROM pbf_lookups WHERE lookup_linkfile='" . $lookup_linkfile . "' ORDER BY lookup_order";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_lookup_id($lookup_title) {
		$sql = "SELECT lookup_id FROM pbf_lookups where lookup_title='" . $lookup_title . "' order by lookup_id desc limit 1";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_asset_access($asset_id, $asset_link, $access_level) {
		$sql = "SELECT " . $access_level . " FROM pbf_usersgroupsassets WHERE asset_id='" . $asset_id . "' AND asset_link='" . $asset_link . "'";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_user_group_assets_access($user_group_id, $asset_id, $asset_link) {
		$sql = "SELECT * FROM pbf_usersgroupsassets WHERE asset_id='" . $asset_id . "'
                AND asset_link='" . $asset_link . "' AND usersgroup_id = '" . $user_group_id . "' limit 1";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_asset_access_rw($asset_id, $asset_link) {
		$return = array ();
		$row = array ();
		$sql = "SELECT usersgroup_id,usersgroup_name FROM pbf_usersgroups";
		$users_groups = $this->db->query ( $sql )->result_array ();
		
		foreach ( $users_groups as $group ) {
			$row ['usersgroup_id'] = $group ['usersgroup_id'];
			$row ['name'] = $group ['usersgroup_name'];
			
			$access = $this->get_user_group_assets_access ( $row ['usersgroup_id'], $asset_id, $asset_link );
			if (empty ( $access )) {
				$row ['read_access'] = '';
				$row ['write_access'] = '';
				$row ['id'] = '';
			} else {
				$row ['read_access'] = $access ['read_access'];
				$row ['write_access'] = $access ['write_access'];
				$row ['id'] = $access ['id'];
			}
			
			array_push ( $return, $row );
		}
		
		return $return;
	}
	function get_geo__entities_classes($classes = false) {
		$sql = "(SELECT entity_class_id AS link_id,CONCAT('hfrentities/hfrentities/',entity_class_id) AS link,entity_class_name AS link_title FROM pbf_entityclasses) UNION (SELECT geo_id AS link_id,CONCAT('geo/geozones/',geo_id) AS link,geo_title AS link_title FROM pbf_geo WHERE geo_level <=(SELECT geo_level FROM pbf_geo WHERE geo_active='1'))";
		
		if ($classes) {
			$sql = "SELECT entity_class_id,IF(entity_class_abbrev='',entity_class_name,CONCAT(entity_class_name,' (',entity_class_abbrev,')')) entity_class_name FROM pbf_entityclasses";
		}
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_entities_classes() {
		$sql = "SELECT entity_class_id AS link_id,CONCAT('hfrentities/hfrentities/',entity_class_id) AS link,entity_class_name AS link_title FROM pbf_entityclasses";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_geo_classes() {
		$sql = "SELECT geo_id,IF(geo_parent IS NULL,geo_id,CONCAT(geo_parent,geo_id)) AS sortorder,geo_id AS link_id,CONCAT('geo/geozones/',geo_id) AS link,geo_title AS link_title FROM pbf_geo HAVING sortorder <= (SELECT IF(geo_parent IS NULL,geo_id,CONCAT(geo_parent,geo_id)) AS sortorder FROM pbf_geo WHERE geo_active='1') ORDER BY sortorder";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_entity_groups() {
		$sql = "SELECT pbf_entitygroups.entity_group_id,IF(pbf_entitygroups.entity_group_abbrev IS NULL,pbf_entitygroups.entity_group_name,CONCAT(pbf_entitygroups.entity_group_name,' (',pbf_entitygroups.entity_group_abbrev,')')) AS entity_group_name FROM pbf_entitygroups ORDER BY pbf_entitygroups.entity_group_name";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_entity_types($class_id) {
		$sql = "SELECT entity_type_id,entity_class_id,IF(entity_type_abbrev='',entity_type_name,CONCAT(entity_type_name,' (',entity_type_abbrev,')')) AS entity_type_name FROM pbf_entitytypes";
		
		if ($class_id != '') {
			$sql .= " WHERE pbf_entitytypes.entity_class_id = '" . $class_id . "'";
		}
		
		$sql .= " ORDER BY entity_type_name";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_entities($class) {
		$sql_append = "";
		
		$usergeozones = $this->session->userdata ( 'usergeozones' );
		$user_entity = $this->session->userdata ( 'user_entity' );
		if (! empty ( $usergeozones )) {
			$sql_append .= " AND pbf_entities.entity_geozone_id IN (" . implode ( ',', $usergeozones ) . ")";
		}
		
		if ((! empty ( $user_entity )) and ($this->pbf->check_group_entityassociated ( $this->session->userdata ( 'usergroup_id' ) ) == '1')) {
			$sql_append .= " AND pbf_entities.entity_id = $user_entity";
		}
		
		$sql = "SELECT pbf_entities.entity_id,(pbf_entities.entity_name) AS entity_name,entity_geozone_id,pbf_geozones.geozone_name,pbf_entitytypes.entity_type_abbrev AS entity_type,pbf_lkp_status.lookup_title AS entity_status,pbf_entities.entity_responsible_name,pbf_entities.entity_responsible_email,pbf_entities.entity_class,pbf_entities.entity_type AS entity_type_id,pbf_entities.entity_status FROM pbf_entities JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_lookups pbf_lkp_status ON (pbf_lkp_status.lookup_id=pbf_entities.entity_status AND pbf_lkp_status.lookup_linkfile='entity_status')";
		
		if ($class != '') {
			$sql .= "  WHERE pbf_entities.entity_class='" . $class . "'";
		}
		
		$sql .= "  AND pbf_entities.entity_active='1' " . $sql_append;
		
		$sql .= " ORDER BY entity_geozone_id,entity_name";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_donor_details($donor_id) {
		$sql = "SELECT donor_priority FROM pbf_donors WHERE donor_id='" . $donor_id . "'";
		return $this->db->query ( $sql )->row_array ();
	}
	function get_entities_default() {
		$sql_entity_details = "SELECT pbf_donorsentity_details.entity_id,pbf_donorsentity_details.indicator_id,pbf_donorsconf_details.percentage FROM pbf_donorsentity_details LEFT JOIN pbf_donorsconf_details ON (pbf_donorsentity_details.donorconf_id=pbf_donorsconf_details.conf_details_id)
					LEFT JOIN pbf_donorsconfig ON (pbf_donorsconfig.donorconfig_id=pbf_donorsconf_details.donor_conf_id) LEFT JOIN pbf_donors ON(pbf_donors.donor_id=pbf_donorsconfig.donor_id)";
		$entity_details = $this->db->query ( $sql_entity_details )->result_array ();
		
		$entity_list = array ();
		foreach ( $entity_details as $detail ) {
			$entity_list [] = $detail ['entity_id'];
		}
		
		$entity_det = array ();
		if (! empty ( $entity_details )) {
			
			foreach ( $entity_list as $entit ) {
				$entityid = $entit;
				$entity_det [$entit] = 0;
				foreach ( $entity_details as $detail ) {
					if (($detail ['entity_id'] == $entityid) && ($detail ['indicator_id'] == 0)) {
						$entity_det [$entit] = $entity_det [$entit] + $detail ['percentage'];
					}
				}
			}
		}
		return $entity_det;
	}
	function get_entities_donor($donor_id) {
		$this->get_entities_default ();
		// exit();
		
		$sql_entity_details = "SELECT DISTINCT(pbf_donorsentity_details.entity_id),pbf_donorsentity_details.indicator_id,pbf_donorsconf_details.percentage FROM pbf_donorsentity_details LEFT JOIN pbf_donorsconf_details ON (pbf_donorsentity_details.donorconf_id=pbf_donorsconf_details.conf_details_id)
					LEFT JOIN pbf_donorsconfig ON (pbf_donorsconfig.donorconfig_id=pbf_donorsconf_details.donor_conf_id) LEFT JOIN pbf_donors ON(pbf_donors.donor_id=pbf_donorsconfig.donor_id) WHERE pbf_donors.donor_id='" . $donor_id . "'";
		
		$entity_details = $this->db->query ( $sql_entity_details )->result_array ();
		$donors = $this->get_donor_details ( $donor_id );
		$donor_priority = $donors ['donor_priority'];
		
		if ($donor_priority == 1) {
			$entity_list = array ();
			$entities_default_donor = $this->get_entities_default ();
			foreach ( $entities_default_donor as $key => $value ) {
				if ($value < 100) {
					$entity_list [] = $key;
				}
			}
		} else {
			$entity_list = array ();
			if (! empty ( $entity_details )) {
				foreach ( $entity_details as $detail ) {
					$entity_list [] = $detail ['entity_id'];
				}
			}
		}
		
		$sql_append = "";
		$usergeozones = $this->session->userdata ( 'usergeozones' );
		$user_entity = $this->session->userdata ( 'user_entity' );
		if (! empty ( $usergeozones )) {
			$sql_append .= " AND pbf_entities.entity_geozone_id IN (" . implode ( ',', $usergeozones ) . ")";
		}
		
		if ((! empty ( $user_entity )) and ($this->pbf->check_group_entityassociated ( $this->session->userdata ( 'usergroup_id' ) ) == '1')) {
			$sql_append .= " AND pbf_entities.entity_id = $user_entity";
		}
		
		$sql = "SELECT pbf_entities.entity_id,CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev) AS entity_name,entity_geozone_id,pbf_geozones.geozone_name,pbf_entitytypes.entity_type_abbrev AS entity_type,pbf_lkp_status.lookup_title AS entity_status,pbf_entities.entity_responsible_name,pbf_entities.entity_responsible_email,pbf_entities.entity_class,pbf_entities.entity_type AS entity_type_id,pbf_entities.entity_status FROM pbf_entities JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_lookups pbf_lkp_status ON (pbf_lkp_status.lookup_id=pbf_entities.entity_status AND pbf_lkp_status.lookup_linkfile='entity_status')";
		
		if (! empty ( $entity_list )) {
			$sql_append .= " WHERE pbf_entities.entity_id IN (" . implode ( ',', $entity_list ) . ")";
		} else {
			$sql_append .= " WHERE 2=1";
		}
		
		$sql .= "  AND pbf_entities.entity_active='1' " . $sql_append;
		
		$sql .= " ORDER BY entity_geozone_id,entity_name";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_user_entities($user_id) {
		$sql = "SELECT pbf_entities.entity_id,pbf_entities.entity_name,pbf_entitytypes.entity_type_id FROM pbf_entities LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id = pbf_entities.entity_type) LEFT JOIN pbf_usersgeozones ON (pbf_usersgeozones.entity_id = pbf_entities.entity_id) WHERE pbf_usersgeozones.user_id = '" . $user_id . "'";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_banks($hq = true) {
		if ($hq) {
			$sql = "SELECT bank_id,CONCAT(bank_name,' ',IF(bank_name_abbrev!='',CONCAT('(',bank_name_abbrev,')'),'')) AS bank_name,bank_parent_id FROM pbf_banks WHERE (bank_parent_id IS NULL OR bank_parent_id='0')";
		} else {
			$sql = "SELECT bank_id,CONCAT(bank_name,' ',IF(bank_name_abbrev!='',CONCAT('(',bank_name_abbrev,')'),'')) AS bank_name,bank_parent_id FROM pbf_banks WHERE (bank_parent_id IS NOT NULL OR bank_parent_id!='0')";
		}
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_geozone_geo() {
		$sql = "SELECT geozone_id,geozone_name,geoz.geo_id AS appealing_id FROM pbf_geozones JOIN pbf_geo geos ON (pbf_geozones.geo_id=geos.geo_id) LEFT JOIN pbf_geo geoz ON (geoz.geo_level=(geos.geo_level+1)) WHERE pbf_geozones.geozone_active = '1'";
		return $this->db->query ( $sql )->result_array ();
	}
	function get_geoleveles_listing($num = 0, $filters) {
		$record_set = array ();
		
		$sql = "SELECT pbf_geo.geo_id,IF(pbf_geo.geo_parent IS NULL,'','  |_') AS lvl_hlder,pbf_geo.geo_title,IF(pbf_geo.geo_parent IS NULL,'',pbf_geo.geo_parent) AS geo_parent,pbf_geo.geo_active,IF(pbf_geo.geo_parent IS NULL,pbf_geo.geo_id,CONCAT(pbf_geo.geo_parent,pbf_geo.geo_id)) AS sortorder FROM pbf_geo LEFT JOIN pbf_geo pbf_parent_geos ON (pbf_parent_geos.geo_id=pbf_geo.geo_parent) ORDER BY sortorder";
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT $num , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function get_geoleveles() {
		$sql = "SELECT IF(geo_parent IS NULL,geo_id,CONCAT(geo_parent,geo_id)) AS sortorder,geo_id AS geo_id,geo_title,geo_active FROM pbf_geo HAVING sortorder <= (SELECT IF(geo_parent IS NULL,geo_id,CONCAT(geo_parent,geo_id)) AS sortorder FROM pbf_geo WHERE geo_active='1') ORDER BY sortorder";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_geozones() {
		$bind_region = " WHERE 1=1 ";
		
		$usergeozones = $this->session->userdata ( 'usergeozones' );
		
		if (! empty ( $usergeozones )) {
			
			$sql = "SELECT * FROM pbf_geozones WHERE (pbf_geozones.geozone_id IN (" . implode ( ',', $usergeozones ) . ") OR pbf_geozones.geozone_id IN (SELECT geozone_parentid FROM pbf_geozones WHERE pbf_geozones.geozone_id IN (" . implode ( ',', $usergeozones ) . "))) AND pbf_geozones.geozone_active='1' ORDER BY pbf_geozones.geozone_name";
			
			return $this->db->query ( $sql )->result_array ();
		} else {
			
			return $this->db->get_where ( 'pbf_geozones', array (
					'geozone_active' => '1' 
			) )->result_array ();
		}
	}
	function get_filetypes() {
		return $this->db->get_where ( 'pbf_filetypes', array (
				'filetype_active' => 1 
		) )->result_array ();
	}
	function get_regions() {
		return $this->db->get_where ( 'pbf_geozones', array (
				'geozone_parentid' => NULL 
		) )->result_array ();
	}
	function get_districts() {
		return $this->db->get_where ( 'pbf_geozones', 'geozone_parentid IS NOT NULL' )->result_array ();
	}
	function get_districts_region($id_region) {
		return $this->db->get_where ( 'pbf_geozones', array (
				'geozone_parentid' => $id_region 
		) )->result_array ();
	}
	function get_groups() {
		return $this->db->get_where ( 'pbf_usersgroups', array (
				'usersgroup_active' => 1 
		) )->result_array ();
	}
	function get_indicators() {
		$sql = "SELECT * FROM pbf_indicators";
		return $this->db->query ( $sql )->result_array ();
	}
	function get_entities_donors() {
		return $this->db->get_where ( 'pbf_entities', array (
				'entity_active' => 1 
		) )->result_array ();
	}
	function get_entities_district_donors($district_id) {
		$sql = "SELECT * FROM pbf_entities WHERE entity_active='1' AND entity_geozone_id='" . $district_id . "'";
		return $this->db->query ( $sql )->result_array ();
	}
	function get_donors() {
		$sql = "SELECT * FROM pbf_donors";
		return $this->db->query ( $sql )->result_array ();
	}
	function get_users() {
		return $this->db->get_where ( 'pbf_users', array (
				'user_active' => 1 
		) )->result_array ();
	}
	function get_alertes_config() {
		return $this->db->get_where ( 'pbf_alerteconfig' )->result_array ();
	}
	function get_file_types($entity_class, $entity_type) { // should be extended to coop with content_type and frequency
		$sql = "(SELECT DISTINCT(pbf_filetypes.filetype_id),pbf_filetypesentities.filetype_entity_id,pbf_filetypes.filetype_name,'' AS entity_type_id FROM pbf_filetypes LEFT JOIN pbf_filetypesentities ON (pbf_filetypes.filetype_id=pbf_filetypesentities.filetype_id) WHERE 1=1";
		
		if (! empty ( $entity_class )) {
			
			$sql .= " AND pbf_filetypesentities.entity_class_id='" . $entity_class . "'";
		}
		
		if (! empty ( $entity_type )) {
			
			$sql .= " AND pbf_filetypesentities.entity_type_id='" . $entity_type . "'";
		}
		
		$sql .= " AND pbf_filetypes.filetype_active='1' GROUP BY pbf_filetypes.filetype_id) UNION (";
		
		$sql .= "SELECT DISTINCT(pbf_filetypes.filetype_id),pbf_filetypesentities.filetype_entity_id,pbf_filetypes.filetype_name,pbf_filetypesentities.entity_type_id FROM pbf_filetypes JOIN pbf_filetypesentities ON (pbf_filetypesentities.filetype_id=pbf_filetypes.filetype_id) WHERE pbf_filetypesentities.entity_class_id='" . $entity_class . "' AND filetype_active='1')";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_filetypes__entity_type($class) {
		$sql = "SELECT pbf_filetypesentities.filetype_entity_id,pbf_filetypes.filetype_id,pbf_filetypes.filetype_name,pbf_filetypesentities.entity_type_id FROM pbf_filetypes JOIN pbf_filetypesentities ON (pbf_filetypesentities.filetype_id=pbf_filetypes.filetype_id) WHERE 1=1";
		
		if ($class != '') {
			
			$sql .= " AND pbf_filetypesentities.entity_class_id='" . $class . "' ";
		}
		
		$sql .= " AND filetype_active='1'";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_filetypes__entity_type2($class) {
		$sql = "SELECT pbf_filetypesentities.filetype_entity_id,pbf_filetypes.filetype_id,pbf_filetypes.filetype_name,pbf_filetypesentities.entity_type_id, pbf_filetypesgeozones.geozone_id FROM pbf_filetypes JOIN pbf_filetypesentities ON (pbf_filetypesentities.filetype_id=pbf_filetypes.filetype_id) JOIN pbf_filetypesgeozones ON (pbf_filetypesgeozones.filetype_id=pbf_filetypes.filetype_id) WHERE 1=1";
		
		if ($class != '') {
			
			$sql .= " AND pbf_filetypesentities.entity_class_id='" . $class . "' ";
		}
		
		$sql .= " AND filetype_active='1'";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_filetypes__entity_type_zone($class, $zones) {
		$sql = "SELECT pbf_filetypesentities.filetype_entity_id,pbf_filetypes.filetype_id,pbf_filetypes.filetype_name,pbf_filetypesentities.entity_type_id FROM pbf_filetypes JOIN pbf_filetypesentities ON (pbf_filetypesentities.filetype_id=pbf_filetypes.filetype_id)
		    JOIN pbf_filetypesgeozones ON (pbf_filetypesgeozones.filetype_id=pbf_filetypes.filetype_id) LEFT JOIN pbf_usersgroupsassets ON (pbf_usersgroupsassets.asset_id=pbf_filetypesentities.filetype_id AND pbf_usersgroupsassets.asset_link='data_filetype') WHERE 1=1";
		
		if ($class != '') {
			
			$sql .= " AND pbf_filetypesentities.entity_class_id='" . $class . "' ";
		}
		
		$matches = implode ( ',', $zones );
		if ($matches != '') {
			
			$sql .= " AND pbf_filetypesgeozones.geozone_id IN (" . $matches . ")";
		}
		
		$sql .= " AND filetype_active='1' AND pbf_usersgroupsassets.usersgroup_id='" . $this->session->userdata ( 'usergroup_id' ) . "'";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_datafile_periodics() {
		$sql = "SELECT pbf_filetypesentities.filetype_entity_id,pbf_filetypesfrequency.frequency_months FROM pbf_filetypes LEFT JOIN pbf_filetypesfrequency ON (pbf_filetypes.filetype_frequency = pbf_filetypesfrequency.frequency_id) LEFT JOIN pbf_filetypesentities ON (pbf_filetypesentities.filetype_id = pbf_filetypes.filetype_id)";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_filetypes_frequency($class) {
		$sql = "SELECT pbf_filetypesentities.filetype_entity_id,pbf_filetypes.filetype_id,pbf_lookups.lookup_title FROM pbf_filetypes JOIN pbf_filetypesentities ON (pbf_filetypesentities.filetype_id=pbf_filetypes.filetype_id) JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_frequency) WHERE pbf_filetypesentities.entity_class_id='" . $class . "' AND filetype_active='1'";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function users_groups($usersgroup_id = '') {
		$sql_append = ($usersgroup_id == '') ? "" : " WHERE sortorder >= (SELECT sortorder FROM pbf_usersgroups WHERE usersgroup_id = '" . $usersgroup_id . "') ";
		
		$sql = "SELECT * FROM pbf_usersgroups " . $sql_append . " ORDER BY sortorder";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function users_tasks($users_tasks_id = '') {
		$sql = "SELECT usertask_id,usertask_name FROM pbf_userstasks ";
		if ($users_tasks_id != '') {
			$sql .= " WHERE usertask_id != '" . $users_tasks_id . "'";
		}
		
		$sql .= " ORDER BY usertask_name";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_default_user_group() {
		$sql = "SELECT usersgroup_id, usersgroup_name FROM pbf_usersgroups WHERE usersgroup_active='1' AND isdefault='1'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_default_pbf_geo() {
		$sql = "SELECT geo_id,geo_title FROM pbf_geo WHERE geo_active ='1'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_principal_geo_info() {
		$sql = "SELECT geo_id,geo_title FROM pbf_geo LIMIT 0,1";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_active_geo_info() {
		$sql = "SELECT geo_id,geo_title FROM pbf_geo WHERE geo_parent =(SELECT geo_parent FROM pbf_geo WHERE geo_active='1') ORDER BY geo_parent LIMIT 0,1";
		return $this->db->query ( $sql )->row_array ();
	}
	function get_zone_borders($geozone_id) {
		return $this->db->select ( 'geozone_geojson' )->from ( 'pbf_geozones' )->where ( 'geozone_id', $geozone_id )->get ()->row_array ();
	}
	function render_child_zones($geozone_id) {
		$sql = "SELECT geozone_id,geo_id,geozone_name,geozone_htmlmap,geozone_active FROM pbf_geozones WHERE geozone_parentid='" . $geozone_id . "' ORDER BY geozone_active DESC, geozone_name ASC";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function render_zone_entities($geozone_id) {
		$sql = "SELECT pbf_entities.entity_id,pbf_entities.entity_type, pbf_entitytypes.entity_type_name,CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev) AS entity_name,pbf_entities.entity_geo_long,pbf_entities.entity_geo_lat, pbf_entities.entity_pop FROM pbf_entities JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_lookups pbf_lkp_status ON (pbf_lkp_status.lookup_id=pbf_entities.entity_status AND pbf_lkp_status.lookup_linkfile='entity_status') WHERE pbf_entities.entity_geozone_id='" . $geozone_id . "' AND pbf_entities.entity_active = '1' ORDER BY pbf_entityclasses.entity_class_id DESC,pbf_entitytypes.entity_type_id DESC,pbf_entities.entity_name";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_entities_By_zone($geozone_id, $entity_class, $entity_type, $entity_group) {
		$sql = "SELECT pbf_entities.entity_id,pbf_entitytypes.*,pbf_entities.* FROM pbf_entities JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_lookups pbf_lkp_status ON (pbf_lkp_status.lookup_id=pbf_entities.entity_status AND pbf_lkp_status.lookup_linkfile='entity_status') WHERE pbf_entities.entity_geozone_id='" . $geozone_id . "' AND pbf_entities.entity_active = '1'";
		
		if ($entity_class != '') {
			$sql .= " AND pbf_entities.entity_class='" . $entity_class . "'";
		}
		if ($entity_type != '') {
			$sql .= " AND pbf_entities.entity_type='" . $entity_type . "'";
		}
		
		if ($entity_group != '') {
			$sql .= " AND pbf_entities.entity_pbf_group_id='" . $entity_group . "'";
		}
		
		$sql .= " ORDER BY pbf_entityclasses.entity_class_id DESC,pbf_entitytypes.entity_type_id DESC,pbf_entities.entity_name";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_entities_By_region($region_id) {
		$sql = "SELECT pbf_entities.entity_id,pbf_entitytypes.*,pbf_entities.* FROM pbf_entities JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_lookups pbf_lkp_status ON (pbf_lkp_status.lookup_id=pbf_entities.entity_status AND pbf_lkp_status.lookup_linkfile='entity_status') WHERE pbf_entities.entity_geozone_id IN (SELECT pbf_geozones.geozone_id FROM pbf_geozones WHERE 
		pbf_geozones.geozone_parentid='" . $region_id . "') AND pbf_entities.entity_active = '1'";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_entities_By_district($region_id) {
		$sql = "SELECT pbf_entities.entity_id,pbf_entitytypes.*,pbf_entities.* FROM pbf_entities JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_lookups pbf_lkp_status ON (pbf_lkp_status.lookup_id=pbf_entities.entity_status AND pbf_lkp_status.lookup_linkfile='entity_status') WHERE pbf_entities.entity_geozone_id='" . $region_id . "' AND pbf_entities.entity_active = '1'";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_entities_all($entity_class, $entity_type, $entity_group) {
		$sql = "SELECT pbf_entities.entity_id,pbf_entitytypes.*,pbf_entities.* FROM pbf_entities JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_lookups pbf_lkp_status ON (pbf_lkp_status.lookup_id=pbf_entities.entity_status AND pbf_lkp_status.lookup_linkfile='entity_status') WHERE pbf_entities.entity_active = '1'";
		
		if ($entity_class != '') {
			$sql .= " AND pbf_entities.entity_class='" . $entity_class . "'";
		}
		if ($entity_type != '') {
			$sql .= " AND pbf_entities.entity_type='" . $entity_type . "'";
		}
		
		if ($entity_group != '') {
			$sql .= " AND pbf_entities.entity_pbf_group_id='" . $entity_group . "'";
		}
		
		$sql .= " ORDER BY pbf_entityclasses.entity_class_id DESC,pbf_entitytypes.entity_type_id DESC,pbf_entities.entity_name";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function render_entity($entity_id) { // bank information is not necessary
		$sql = "SELECT pbf_entities.entity_id,pbf_entities.entity_name,pbf_entities.entity_class,pbf_entityclasses.entity_class_name,pbf_entitytypes.entity_type_id,pbf_entitytypes.entity_type_name,pbf_entities.entity_geo_long,pbf_entities.entity_geo_lat,pbf_entities.entity_pop,pbf_entities.entity_pop_year,pbf_entities.entity_address,pbf_entities.entity_responsible_name,pbf_entities.entity_responsible_email,pbf_entities.entity_phone_number,pbf_entities.entity_picturepath,pbf_entities.entity_contractpath,pbf_lkp_status.lookup_title AS entity_status,entity_staff_size FROM pbf_entities JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_lookups pbf_lkp_status ON (pbf_lkp_status.lookup_id=pbf_entities.entity_status AND pbf_lkp_status.lookup_linkfile='entity_status') WHERE pbf_entities.entity_id='" . $entity_id . "'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_last_quarters($number) {
		$sql = "SELECT DISTINCT(data_quarter),data_year FROM pbf_frontdata GROUP BY data_year,data_quarter ORDER BY data_year ASC,data_quarter ASC LIMIT 0," . $number;
		return $this->db->query ( $sql )->result_array ();
	}
	function get_last_budget_year($annee) {
		$sql = "SELECT * FROM (SELECT DISTINCT(data_quarter),data_year FROM pbf_frontdata WHERE data_year='" . $annee . "' GROUP BY data_year,data_quarter ORDER BY data_year DESC,data_quarter DESC) as t  ORDER BY t.data_year ASC,t.data_quarter ASC ";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_all_quarters() {
		$sql = "SELECT DISTINCT(data_quarter),data_year FROM pbf_frontdata GROUP BY data_year,data_quarter ORDER BY data_year ASC,data_quarter ASC";
		return $this->db->query ( $sql )->result_array ();
	}
	function get_last_quarters_zone($number, $entities) {
		$sql = "SELECT * FROM (SELECT DISTINCT(data_quarter),data_year FROM pbf_frontdata LEFT JOIN pbf_frontdatadetails ON (pbf_frontdatadetails.frontdata_id = pbf_frontdata.frontdata_id) WHERE pbf_frontdatadetails.entity_id IN (" . implode ( ',', $entities ) . ") GROUP BY data_year,data_quarter ORDER BY data_year DESC,data_quarter DESC LIMIT 0," . $number . ") as t  ORDER BY t.data_year ASC,t.data_quarter ASC ";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_last_periods($number, $type) {
		switch ($type) {
			case 'month' :
				$period = 'datafile_month';
				break;
			case 'quarter' :
				$period = 'datafile_quarter';
				break;
			case 'year' :
				$period = 'datafile_year';
				break;
			default :
				$period = 'datafile_quarter';
				break;
		}
		
		$sql = "SELECT * FROM (SELECT DISTINCT(" . $period . "),datafile_year FROM pbf_datafile WHERE datafile_status = 1 GROUP BY datafile_year," . $period . " ORDER BY datafile_year DESC," . $period . " DESC LIMIT 0," . $number . ") as t  ORDER BY t.datafile_year ASC,t." . $period . " ASC ";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_last_periods_zone($number, $type, $entities) {
		switch ($type) {
			case 'month' :
				$period = 'datafile_month';
				break;
			case 'quarter' :
				$period = 'datafile_quarter';
				break;
			case 'year' :
				$period = 'datafile_year';
				break;
			default :
				$period = 'datafile_quarter';
				break;
		}
		
		if (is_array ( $entities ))
			$entities = implode ( ',', $entities );
		
		$sql = "SELECT * FROM (SELECT DISTINCT(" . $period . "),datafile_year FROM pbf_datafile WHERE datafile_status = 1 AND pbf_datafile.entity_id IN (" . $entities . ") GROUP BY datafile_year," . $period . " ORDER BY " . $period . " DESC, datafile_year DESC LIMIT 0," . $number . ") as t  ORDER BY t.datafile_year ASC,t." . $period . " ASC ";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_totals_class($arr_period, $zone_id) {
		$sql = "SELECT pbf_entityclasses.entity_class_id,pbf_entityclasses.entity_class_name AS '" . addslashes ( $this->lang->line ( 'front_entity_class' ) ) . "'";
		
		foreach ( $arr_period as $arr_period_val ) {
			
			/* $sql .= ",CONCAT(FORMAT(SUM(IF(pbf_frontdata.data_quarter='".$arr_period_val['data_quarter']."' AND pbf_frontdata.data_year='".$arr_period_val['data_year']."',pbf_frontdatadetails.amount_total,0)),0),' ".$this->config->item('app_country_currency')."') AS '".$this->lang->line('app_quarter_'.$arr_period_val['data_quarter'])." ".$arr_period_val['data_year']."'"; */
			
			$sql .= ",SUM(IF(pbf_frontdata.data_quarter='" . $arr_period_val ['data_quarter'] . "' AND pbf_frontdata.data_year='" . $arr_period_val ['data_year'] . "',pbf_frontdatadetails.amount_total,0)) AS '" . $this->lang->line ( 'app_quarter_' . $arr_period_val ['data_quarter'] ) . " " . $arr_period_val ['data_year'] . "'";
		}
		
		$sql .= " FROM pbf_entityclasses LEFT JOIN pbf_entities ON  (pbf_entities.entity_class=pbf_entityclasses.entity_class_id) LEFT JOIN pbf_frontdatadetails ON (pbf_frontdatadetails.entity_id=pbf_entities.entity_id) LEFT JOIN pbf_frontdata ON (pbf_frontdata.frontdata_id=pbf_frontdatadetails.frontdata_id) WHERE pbf_entities.entity_active = 1";
		
		if (! empty ( $zone_id )) {
			$sql .= " and pbf_entities.entity_geozone_id in (" . $this->get_rqst_geozone_ids ( $zone_id ) . ")";
		}
		$sql .= " GROUP BY pbf_entityclasses.entity_class_id";
		// echo $sql;
		return $this->db->query ( $sql )->result_array ();
	}
	function get_hospital_payement($arr_period, $zone_id = '', $entiy_class_id = '1') {
		$zone_id = '';
		$sql = "SELECT pbf_entityclasses.entity_class_id,pbf_entityclasses.entity_class_name AS '" . addslashes ( $this->lang->line ( 'front_entity_class' ) ) . "'";
		
		foreach ( $arr_period as $arr_period_val ) {
			
			$sql .= ",count(distinct pbf_frontdatadetails.entity_id) nbentities,SUM(IF(pbf_frontdata.data_quarter='" . $arr_period_val ['data_quarter'] . "' AND pbf_frontdata.data_year='" . $arr_period_val ['data_year'] . "',pbf_frontdatadetails.amount_total,0)) AS '" . $this->lang->line ( 'app_quarter_' . $arr_period_val ['data_quarter'] ) . " " . $arr_period_val ['data_year'] . "'";
		}
		
		$sql .= " FROM pbf_entityclasses LEFT JOIN pbf_entities ON  (pbf_entities.entity_class=pbf_entityclasses.entity_class_id) LEFT JOIN pbf_frontdatadetails ON (pbf_frontdatadetails.entity_id=pbf_entities.entity_id) 
                    LEFT JOIN pbf_frontdata ON (pbf_frontdata.frontdata_id=pbf_frontdatadetails.frontdata_id) LEFT JOIN pbf_entitytypes ON (pbf_entities.entity_type = pbf_entitytypes.entity_type_id) WHERE pbf_entities.entity_active = 1";
		// TODO Hard corded hospital id... should fix this
		$sql .= " AND pbf_entities.entity_type = 6";
		if (! empty ( $zone_id )) {
			$sql .= " and pbf_entities.entity_geozone_id in (" . $this->get_rqst_geozone_ids ( $zone_id ) . ")";
		}
		$sql .= " AND pbf_entityclasses.entity_class_id = '" . $entiy_class_id . "'";
		$sql .= " GROUP BY pbf_entityclasses.entity_class_id";
		
		return $this->db->query ( $sql )->result_array ();
	}
	
	/**
	 * Get fosa payement except hospital.
	 * Needed on fosa page to draw payement comparison graph
	 */
	function get_non_hospital_payement($arr_period, $zone_id = '', $entiy_class_id = '1') {
		$sql = "SELECT pbf_entityclasses.entity_class_id,pbf_entityclasses.entity_class_name AS '" . addslashes ( $this->lang->line ( 'front_entity_class' ) ) . "'";
		
		foreach ( $arr_period as $arr_period_val ) {
			
			$sql .= ",SUM(IF(pbf_frontdata.data_quarter='" . $arr_period_val ['data_quarter'] . "' AND pbf_frontdata.data_year='" . $arr_period_val ['data_year'] . "',pbf_frontdatadetails.amount_total,0)) AS '" . $this->lang->line ( 'app_quarter_' . $arr_period_val ['data_quarter'] ) . " " . $arr_period_val ['data_year'] . "'";
		}
		
		$sql .= " ,count(distinct pbf_frontdatadetails.entity_id) nbentities FROM pbf_entityclasses LEFT JOIN pbf_entities ON  (pbf_entities.entity_class=pbf_entityclasses.entity_class_id) LEFT JOIN pbf_frontdatadetails ON (pbf_frontdatadetails.entity_id=pbf_entities.entity_id) 
                    LEFT JOIN pbf_frontdata ON (pbf_frontdata.frontdata_id=pbf_frontdatadetails.frontdata_id) LEFT JOIN pbf_entitytypes ON (pbf_entities.entity_type = pbf_entitytypes.entity_type_id) WHERE pbf_entities.entity_active = 1";
		// TODO Hard corded hospital id... should fix this
		$sql .= " AND pbf_entities.entity_type != 6";
		if (! empty ( $zone_id )) {
			$sql .= " and pbf_entities.entity_geozone_id in (" . $this->get_rqst_geozone_ids ( $zone_id ) . ")";
		}
		$sql .= " AND pbf_entityclasses.entity_class_id = '" . $entiy_class_id . "'";
		$sql .= " GROUP BY pbf_entityclasses.entity_class_id";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_featured_indicators($arr_period, $lang) {
		$sql = "SELECT pbf_indicators.indicator_id,IF(pbf_lookups.lookup_title_abbrev='' OR pbf_lookups.lookup_title_abbrev IS NULL,pbf_indicatorstranslations.indicator_title,CONCAT(pbf_indicatorstranslations.indicator_title,' (',pbf_lookups.lookup_title_abbrev,')')) AS 'pbf_data_header_indicator'";
		
		foreach ( $arr_period as $arr_period_val ) {
			
			$sql .= ",IF((pbf_lkps_contenttype.lookup_title='Quantity') AND (pbf_datafile.datafile_quarter='" . $arr_period_val ['datafile_quarter'] . "') AND (pbf_datafile.datafile_year='" . $arr_period_val ['datafile_year'] . "'),CONCAT(FORMAT(SUM(indicator_montant),0),' " . $this->config->item ( 'app_country_currency' ) . "'),CONCAT(FORMAT(AVG(indicator_montant),0),' %')) AS '" . trim ( $arr_period_val ['datafile_quarter'] ) . " " . $arr_period_val ['datafile_year'] . "'";
		}
		
		$sql .= " FROM pbf_indicators LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_indicators.indicator_category_id AND pbf_lookups.lookup_linkfile='indicator_category') JOIN pbf_datafiledetails ON (pbf_datafiledetails.indicator_id=pbf_indicators.indicator_id) JOIN pbf_datafile ON (pbf_datafile.datafile_id=pbf_datafiledetails.datafile_id) JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) JOIN pbf_lookups pbf_lkps_contenttype ON (pbf_lkps_contenttype.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lkps_contenttype.lookup_linkfile='content_type') LEFT JOIN pbf_indicatorstranslations ON pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id";
		
		$sql .= ($this->config->item ( 'active_data_toggle_link' )) ? "" : " WHERE pbf_indicators.indicator_featured='1' AND pbf_indicatorstranslations.indicator_language ='" . $lang . "'";
		
		$sql .= " GROUP BY pbf_indicators.indicator_id ORDER BY pbf_indicators.indicator_featured ASC";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_featured_indic($arr_period, $zone, $entity, $entity_class, $table_field, $content_type, $lang) {
		$units = ''; // should be the indicator units in the very near future
		$fx = 'SUM'; // is the mysql math function to apply depending on the datatype
		
		if ($table_field == 'indicator_montant' && $content_type == 'Quantity') {
			
			$units = ' ' . $this->config->item ( 'app_country_currency' );
			$fx = 'SUM';
		} elseif ($table_field == 'indicator_montant' && $content_type == 'Quality') {
			
			$units = '%';
			$fx = 'AVG';
		} elseif ($table_field == 'indicator_verified_value' && $content_type == 'Quality') {
			$fx = 'AVG';
		}
		
		$sql = "SELECT pbf_indicators.indicator_id,pbf_indicatorstranslations.indicator_title AS '" . strtoupper ( $this->lang->line ( 'front_data' ) ) . "'";
		// $sql = "SELECT pbf_indicators.indicator_id,pbf_indicators.indicator_id AS '".strtoupper($this->lang->line('front_data'))."'";
		
		foreach ( $arr_period as $arr_period_val ) {
			
			// check if month or quarteR
			
			if (isset ( $arr_period_val ['datafile_quarter'] )) { // quarter
				
				$sql .= ",CONCAT(FORMAT(" . $fx . "(IF(pbf_datafile.datafile_quarter='" . $arr_period_val ['datafile_quarter'] . "' AND pbf_datafile.datafile_year='" . $arr_period_val ['datafile_year'] . "' AND pbf_datafile.filetype_id=pbf_filetypes.filetype_id AND pbf_datafile.datafile_status = '1',pbf_datafiledetails." . $table_field . ",0)),0),'" . $units . "') AS '" . $this->lang->line ( 'app_quarter_' . $arr_period_val ['datafile_quarter'] ) . " " . $arr_period_val ['datafile_year'] . "'";
			} else 			// month
			{
				
				$sql .= ",CONCAT(FORMAT(" . $fx . "(IF(pbf_datafile.datafile_month='" . $arr_period_val ['datafile_month'] . "' AND pbf_datafile.datafile_year='" . $arr_period_val ['datafile_year'] . "' AND pbf_datafile.filetype_id=pbf_filetypes.filetype_id AND pbf_datafile.datafile_status = '1',pbf_datafiledetails." . $table_field . ",0)),0),'" . $units . "') AS '" . $this->lang->line ( 'app_month_' . $arr_period_val ['datafile_month'] ) . " " . $arr_period_val ['datafile_year'] . "'";
			}
		}
		
		$sql .= " FROM pbf_datafile LEFT JOIN pbf_datafiledetails ON (pbf_datafiledetails.datafile_id = pbf_datafile.datafile_id) LEFT JOIN pbf_indicators ON (pbf_indicators.indicator_id=pbf_datafiledetails.indicator_id) LEFT JOIN pbf_entities ON (pbf_entities.entity_id = pbf_datafile.entity_id) LEFT JOIN pbf_indicatorsfileypes ON (pbf_indicatorsfileypes.indicator_id = pbf_indicators.indicator_id) LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id = pbf_indicatorsfileypes.filetype_id AND pbf_datafile.filetype_id=pbf_filetypes.filetype_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id = pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_indicatorstranslations ON pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id";
		
		$sql .= ($this->config->item ( 'active_data_toggle_link' )) ? " WHERE 1=1 " : " WHERE pbf_indicators.indicator_featured='1' ";
		
		$sql .= " AND pbf_lookups.lookup_title = '" . $content_type . "' ";
		
		$sql .= " AND pbf_entities.entity_class = '" . $entity_class . "' ";
		
		$sql .= " AND pbf_datafile.datafile_status = '1' ";
		
		if ($entity != '') {
			
			$sql .= " AND pbf_datafile.entity_id='" . $entity . "' ";
		}
		if ($zone != '') {
			$sql .= " AND pbf_entities.entity_geozone_id IN (" . $this->get_rqst_geozone_ids ( $zone ) . ") ";
		}
		
		$sql .= " AND pbf_indicatorstranslations.indicator_language ='" . $lang . "' 
		   AND (LAST_DAY(CONCAT(" . "pbf_datafile.datafile_year" . ",'-'," . "pbf_datafile.datafile_month" . ",'-1')) BETWEEN pbf_indicatorsfileypes.use_from AND pbf_indicatorsfileypes.use_to)
		  GROUP BY pbf_datafiledetails.indicator_id ORDER BY pbf_indicators.indicator_featured DESC,pbf_datafile.datafile_year DESC,pbf_datafile.datafile_month DESC";
		
		$featured_data ['pbf_data'] = $this->db->query ( $sql )->result_array ();
		
		$featured_data ['tot_rows'] = count ( $featured_data ['pbf_data'] );
		
		$sql = "SELECT COUNT(DISTINCT(pbf_indicatorsfileypes.indicator_id)) AS tot_featured FROM pbf_indicators JOIN pbf_indicatorsfileypes ON (pbf_indicatorsfileypes.indicator_id = pbf_indicators.indicator_id) JOIN pbf_filetypes ON (pbf_filetypes.filetype_id = pbf_indicatorsfileypes.filetype_id) JOIN pbf_lookups ON (pbf_lookups.lookup_id = pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_filetypesentities ON (pbf_filetypes.filetype_id=pbf_filetypesentities.filetype_id ) LEFT JOIN pbf_entities ON (pbf_entities.entity_class =pbf_filetypesentities.entity_class_id AND pbf_entities.entity_type=pbf_filetypesentities.entity_type_id) LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) WHERE pbf_indicators.indicator_featured='1' AND pbf_lookups.lookup_title = '" . $content_type . "'";
		
		if ($entity != '') {
			
			$sql .= " AND  pbf_entities.entity_id ='" . $entity . "' ";
		}
		if ($zone != '') {
			$sql .= " AND pbf_entities.entity_geozone_id IN (" . $this->get_rqst_geozone_ids ( $zone ) . ") ";
		}
		
		$tot_featured = $this->db->query ( $sql )->row_array ();
		$featured_data ['tot_featured'] = $tot_featured ['tot_featured'];
		
		return $featured_data;
	}
	function get_avg_perfomance($arr_period, $zone, $entity, $entity_class) {
		$data_label = ($entity_class == 2) ? $this->lang->line ( 'front_admin_data_label' ) : $this->lang->line ( 'front_data_label' );
		
		$sql = "SELECT '" . $data_label . "' AS '" . strtoupper ( $this->lang->line ( 'front_data' ) ) . "'";
		
		foreach ( $arr_period as $arr_period_val ) {
			
			$sql .= ",CONCAT(FORMAT(AVG(IF(pbf_datafile.datafile_quarter='" . $arr_period_val ['data_quarter'] . "' AND pbf_datafile.datafile_year='" . $arr_period_val ['data_year'] . "' AND pbf_datafile.filetype_id=pbf_filetypes.filetype_id,pbf_datafile.datafile_total,NULL)),2),'') AS '" . $this->lang->line ( 'app_quarter_' . $arr_period_val ['data_quarter'] ) . " " . $arr_period_val ['data_year'] . "'";
		}
		
		$sql .= " FROM pbf_datafile LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_filetypesentities ON (pbf_filetypesentities.filetype_id=pbf_filetypes.filetype_id) LEFT JOIN pbf_entities ON (pbf_entities.entity_id = pbf_datafile.entity_id AND pbf_entities.entity_class=pbf_filetypesentities.entity_class_id) WHERE pbf_lookups.lookup_title='Quality' AND pbf_filetypesentities.entity_class_id = '" . $entity_class . "' AND pbf_datafile.datafile_status='1'";
		
		if ($zone != '') {
			
			$sql .= " AND pbf_entities.entity_geozone_id IN (" . $this->get_rqst_geozone_ids ( $zone ) . ") ";
		}
		
		if ($entity != '') {
			
			$sql .= " AND pbf_datafile.entity_id='" . $entity . "' ";
		}
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_rqst_geozone_ids($geozone_id, $active = FALSE) {
		$sql = "SELECT GROUP_CONCAT(IF(A.geozone_parentid IS NULL,B.geozone_id,A.geozone_id)) AS geozone_id FROM pbf_geozones AS A LEFT OUTER JOIN pbf_geozones AS B ON A.geozone_id=B.geozone_parentid WHERE A.geozone_id='" . $geozone_id . "'";
		if ($active) {
			$sql .= " AND B.geozone_active=1";
		}
		$geozone_id = $this->db->query ( $sql )->row_array ();
		
		return $geozone_id ['geozone_id'];
	}
	function get_computed_payments($arr_period, $zone, $entity, $entity_class) {
		$sql = "SELECT 'Total' AS '" . strtoupper ( $this->lang->line ( 'front_data' ) ) . "'";
		
		foreach ( $arr_period as $arr_period_val ) {
			
			$sql .= ",CONCAT(FORMAT(SUM(IF(pbf_frontdata.data_quarter='" . $arr_period_val ['data_quarter'] . "' AND pbf_frontdata.data_year='" . $arr_period_val ['data_year'] . "',amount_total,0)),0),'') AS '" . $this->lang->line ( 'app_quarter_' . $arr_period_val ['data_quarter'] ) . " " . $arr_period_val ['data_year'] . "'";
		}
		
		$sql .= " FROM pbf_frontdata LEFT JOIN pbf_frontdatadetails ON (pbf_frontdatadetails.frontdata_id=pbf_frontdata.frontdata_id) LEFT JOIN pbf_entities ON (pbf_entities.entity_id=pbf_frontdatadetails.entity_id) LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) WHERE pbf_entities.entity_class='" . $entity_class . "'";
		
		if ($zone != '') {
			
			$sql .= " AND pbf_geozones.geozone_id IN (" . $this->get_rqst_geozone_ids ( $zone ) . ") ";
		}
		
		if ($entity != '') {
			
			$sql .= " AND pbf_entities.entity_id='" . $entity . "' ";
		}
		// echo $sql;
		return $this->db->query ( $sql )->result_array ();
	}
	function get_last_quantities_reports($month, $year, $zone, $entity) {
		$sql = "SELECT pbf_indicatorstranslations.indicator_title, pbf_indicators.indicator_id AS '" . strtoupper ( $this->lang->line ( 'report_indicateur' ) ) . "',FORMAT(SUM(pbf_datafiledetails.indicator_claimed_value),0) AS '" . strtoupper ( $this->lang->line ( 'report_claimed' ) ) . "',FORMAT(SUM(pbf_datafiledetails.indicator_validated_value),0) AS '" . strtoupper ( $this->lang->line ( 'report_validated' ) ) . "',FORMAT(SUM(pbf_datafiledetails.indicator_tarif),0) AS '" . strtoupper ( $this->lang->line ( 'report_tarif' ) ) . "',FORMAT(SUM(pbf_datafiledetails.indicator_montant),0) AS '" . strtoupper ( $this->lang->line ( 'report_total' ) . ' (' . $this->config->item ( 'app_country_currency' ) . ')' ) . "' FROM pbf_datafiledetails LEFT JOIN pbf_datafile ON (pbf_datafile.datafile_id=pbf_datafiledetails.datafile_id) LEFT JOIN pbf_indicators ON (pbf_indicators.indicator_id = pbf_datafiledetails.indicator_id ) LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) LEFT JOIN pbf_indicatorsfileypes ON (pbf_indicatorsfileypes.indicator_id=pbf_indicators.indicator_id AND pbf_indicatorsfileypes.filetype_id=pbf_filetypes.filetype_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_entities ON (pbf_entities.entity_id=pbf_datafile.entity_id) LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_indicatorstranslations ON pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id WHERE pbf_datafile.datafile_month='" . $month . "' AND pbf_datafile.datafile_year='" . $year . "' AND (LAST_DAY('" . $year . "-" . $month . "-1') BETWEEN pbf_indicatorsfileypes.use_from AND pbf_indicatorsfileypes.use_to) AND pbf_datafile.datafile_status = '1'";
		
		$sql .= ($this->config->item ( 'active_data_toggle_link' )) ? " " : " AND pbf_indicators.indicator_featured='1' ";
		
		if ($zone != '') {
			
			$sql .= " AND pbf_geozones.geozone_id IN (" . $this->get_rqst_geozone_ids ( $zone ) . ") ";
		}
		
		if ($entity != '') {
			
			$sql .= " AND pbf_entities.entity_id='" . $entity . "' ";
		}
		
		$sql .= "GROUP BY pbf_datafiledetails.indicator_id ORDER BY pbf_indicators.indicator_featured DESC,pbf_indicatorsfileypes.order ASC";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_element_details($arr_period, $indicator_id, $geozone_id) {
		if ($geozone_id != $this->get_rqst_geozone_ids ( $geozone_id ) || $geozone_id == '') {
			
			$sql = ($geozone_id == '') ? "SELECT parent_zones.geozone_id AS geozone_id,parent_zones.geozone_name AS '" . strtoupper ( $this->lang->line ( 'front_element_zone' ) ) . "'" : "SELECT pbf_geozones.geozone_id AS geozone_id,pbf_geozones.geozone_name AS '" . strtoupper ( $this->lang->line ( 'front_element_zone' ) ) . "'";
			
			foreach ( $arr_period as $arr_period_val ) {
				
				if (isset ( $arr_period_val ['datafile_month'] )) {
					$sql .= ", FORMAT(SUM(IF(pbf_datafile.datafile_month='" . $arr_period_val ['datafile_month'] . "' AND pbf_datafile.datafile_year='" . $arr_period_val ['datafile_year'] . "',pbf_datafiledetails.indicator_verified_value,0)),0) AS '" . $this->lang->line ( 'app_month_' . $arr_period_val ['datafile_month'] ) . " " . $arr_period_val ['datafile_year'] . "' ";
				} else {
					
					$sql .= ", FORMAT(SUM(IF(pbf_datafile.datafile_quarter='" . $arr_period_val ['datafile_quarter'] . "' AND pbf_datafile.datafile_year='" . $arr_period_val ['datafile_year'] . "',pbf_datafiledetails.indicator_verified_value,0)),0) AS '" . $this->lang->line ( 'app_quarter_' . $arr_period_val ['datafile_quarter'] ) . " " . $arr_period_val ['datafile_year'] . "' ";
				}
			}
			
			$sql .= " FROM pbf_geozones LEFT JOIN pbf_entities ON (pbf_entities.entity_geozone_id=pbf_geozones.geozone_id) LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id=pbf_entities.entity_id) LEFT JOIN pbf_datafiledetails ON (pbf_datafiledetails.datafile_id=pbf_datafile.datafile_id) LEFT JOIN pbf_geozones parent_zones ON (parent_zones.geozone_id=pbf_geozones.geozone_parentid) WHERE pbf_datafiledetails.indicator_id = '" . $indicator_id . "' AND pbf_datafile.datafile_status='1'";
			
			if ($geozone_id != '') {
				
				$sql .= " AND pbf_geozones.geozone_id IN (" . $this->get_rqst_geozone_ids ( $geozone_id ) . ") ";
			}
			
			$sql .= " GROUP BY geozone_id";
		} elseif ($geozone_id != '' && $geozone_id == $this->get_rqst_geozone_ids ( $geozone_id )) {
			
			$sql = "SELECT pbf_entities.entity_id,CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev) AS '" . strtoupper ( $this->lang->line ( 'front_data_fosa' ) ) . "'";
			
			foreach ( $arr_period as $arr_period_val ) {
				
				if (isset ( $arr_period_val ['datafile_month'] )) {
					$sql .= ", FORMAT(SUM(IF(pbf_datafile.datafile_month='" . $arr_period_val ['datafile_month'] . "' AND pbf_datafile.datafile_year='" . $arr_period_val ['datafile_year'] . "',pbf_datafiledetails.indicator_verified_value,0)),0) AS '" . $this->lang->line ( 'app_month_' . $arr_period_val ['datafile_month'] ) . " " . $arr_period_val ['datafile_year'] . "' ";
				} else {
					$sql .= ", FORMAT(SUM(IF(pbf_datafile.datafile_quarter='" . $arr_period_val ['datafile_quarter'] . "' AND pbf_datafile.datafile_year='" . $arr_period_val ['datafile_year'] . "',pbf_datafiledetails.indicator_verified_value,0)),0) AS '" . $this->lang->line ( 'app_quarter_' . $arr_period_val ['datafile_quarter'] ) . " " . $arr_period_val ['datafile_year'] . "' ";
				}
			}
			
			$sql .= " FROM pbf_entities JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_lookups pbf_lkp_status ON (pbf_lkp_status.lookup_id=pbf_entities.entity_status AND pbf_lkp_status.lookup_linkfile='entity_status') LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id = pbf_entities.entity_id) LEFT JOIN pbf_datafiledetails ON (pbf_datafiledetails.datafile_id=pbf_datafile.datafile_id) WHERE pbf_entities.entity_geozone_id='" . $geozone_id . "' AND pbf_datafiledetails.indicator_id ='" . $indicator_id . "' AND pbf_entities.entity_active = '1' AND pbf_datafile.datafile_status='1' GROUP BY pbf_entities.entity_id ORDER BY pbf_entityclasses.entity_class_id DESC,pbf_entitytypes.entity_type_id DESC,pbf_entities.entity_name";
		}
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_performance_details($arr_period, $entity_class, $geozone_id) {
		if ($geozone_id != $this->get_rqst_geozone_ids ( $geozone_id ) || $geozone_id == '') {
			
			$sql = ($geozone_id == '') ? "SELECT parent_zones.geozone_id AS geozone_id,parent_zones.geozone_name AS '" . strtoupper ( $this->lang->line ( 'front_element_zone' ) ) . "'" : "SELECT pbf_geozones.geozone_id AS geozone_id,pbf_geozones.geozone_name AS '" . strtoupper ( $this->lang->line ( 'front_element_zone' ) ) . "'";
			
			foreach ( $arr_period as $arr_period_val ) {
				
				$sql .= ",CONCAT(FORMAT(AVG(IF(pbf_datafile.datafile_quarter='" . $arr_period_val ['data_quarter'] . "' AND pbf_datafile.datafile_year='" . $arr_period_val ['data_year'] . "',pbf_datafile.datafile_total,NULL)),2),'') AS '" . $this->lang->line ( 'app_quarter_' . $arr_period_val ['data_quarter'] ) . " " . $arr_period_val ['data_year'] . "'";
			}
			
			$sql .= " FROM pbf_datafile LEFT JOIN pbf_entities ON (pbf_entities.entity_id=pbf_datafile.entity_id) LEFT JOIN pbf_filetypes ON (pbf_datafile.filetype_id=pbf_filetypes.filetype_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id = pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile = 'content_type') LEFT JOIN pbf_geozones ON (pbf_entities.entity_geozone_id=pbf_geozones.geozone_id) LEFT JOIN pbf_geozones parent_zones ON (parent_zones.geozone_id=pbf_geozones.geozone_parentid) WHERE pbf_lookups.lookup_title='Quality' AND pbf_entities.entity_class='" . $entity_class . "'";
			
			if ($geozone_id != '') {
				$sql .= " AND pbf_entities.entity_geozone_id IN (" . $this->get_rqst_geozone_ids ( $geozone_id ) . ")";
			}
			
			$sql .= " GROUP BY geozone_id";
		} elseif ($geozone_id != '' && $geozone_id == $this->get_rqst_geozone_ids ( $geozone_id )) {
			
			$sql = "SELECT pbf_entities.entity_id,CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev) AS '" . strtoupper ( $this->lang->line ( 'front_data_fosa' ) ) . "'";
			
			foreach ( $arr_period as $arr_period_val ) {
				
				$sql .= ", CONCAT(FORMAT(AVG(IF(pbf_datafile.datafile_quarter='" . $arr_period_val ['data_quarter'] . "' AND pbf_datafile.datafile_year='" . $arr_period_val ['data_year'] . "',pbf_datafile.datafile_total,NULL)),2),'') AS '" . $this->lang->line ( 'app_quarter_' . $arr_period_val ['data_quarter'] ) . " " . $arr_period_val ['data_year'] . "' ";
			}
			
			$sql .= " FROM pbf_datafile LEFT JOIN pbf_entities ON (pbf_entities.entity_id=pbf_datafile.entity_id) LEFT JOIN pbf_filetypes ON (pbf_datafile.filetype_id=pbf_filetypes.filetype_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id = pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile = 'content_type') LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) WHERE pbf_lookups.lookup_title='Quality' AND pbf_entities.entity_class='" . $entity_class . "' AND pbf_entities.entity_geozone_id='" . $geozone_id . "' GROUP BY pbf_entities.entity_id ORDER BY pbf_entities.entity_type DESC,pbf_entities.entity_name";
		}
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_payment_details($arr_period, $entity_class, $geozone_id) {
		if ($geozone_id != $this->get_rqst_geozone_ids ( $geozone_id ) || $geozone_id == '') {
			
			$sql = ($geozone_id == '') ? "SELECT parent_zones.geozone_id AS geozone_id,parent_zones.geozone_name AS '" . strtoupper ( $this->lang->line ( 'front_element_zone' ) ) . "'" : "SELECT pbf_geozones.geozone_id AS geozone_id,pbf_geozones.geozone_name AS '" . strtoupper ( $this->lang->line ( 'front_element_zone' ) ) . "'";
			
			foreach ( $arr_period as $arr_period_val ) {
				
				$sql .= ",CONCAT(FORMAT(SUM(IF(pbf_frontdata.data_quarter='" . $arr_period_val ['data_quarter'] . "' AND pbf_frontdata.data_year='" . $arr_period_val ['data_year'] . "',pbf_frontdatadetails.amount_total,0)),0),'') AS '" . $this->lang->line ( 'app_quarter_' . $arr_period_val ['data_quarter'] ) . " " . $arr_period_val ['data_year'] . "'";
			}
			
			$sql .= "FROM pbf_frontdatadetails LEFT JOIN pbf_frontdata ON (pbf_frontdatadetails.frontdata_id=pbf_frontdata.frontdata_id) LEFT JOIN pbf_entities ON (pbf_entities.entity_id=pbf_frontdatadetails.entity_id) LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_geozones parent_zones ON (parent_zones.geozone_id=pbf_geozones.geozone_parentid) WHERE pbf_entities.entity_class='" . $entity_class . "'";
			
			if ($geozone_id != '') {
				$sql .= " AND pbf_entities.entity_geozone_id IN (" . $this->get_rqst_geozone_ids ( $geozone_id ) . ")";
			}
			
			$sql .= " GROUP BY geozone_id";
		} elseif ($geozone_id != '' && $geozone_id == $this->get_rqst_geozone_ids ( $geozone_id )) {
			
			$sql = "SELECT pbf_entities.entity_id,
                    CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev)
                    AS '" . strtoupper ( $this->lang->line ( 'front_data_fosa' ) ) . "'";
			
			foreach ( $arr_period as $arr_period_val ) {
				
				$sql .= ",CONCAT(FORMAT(SUM(IF(pbf_frontdata.data_quarter='" . $arr_period_val ['data_quarter'] . "' AND pbf_frontdata.data_year='" . $arr_period_val ['data_year'] . "',pbf_frontdatadetails.amount_total,0)),0),' ') AS '" . $this->lang->line ( 'app_quarter_' . $arr_period_val ['data_quarter'] ) . " " . $arr_period_val ['data_year'] . "'";
			}
			
			$sql .= " FROM pbf_frontdatadetails LEFT JOIN pbf_frontdata ON (pbf_frontdatadetails.frontdata_id=pbf_frontdata.frontdata_id) LEFT JOIN pbf_entities ON (pbf_entities.entity_id=pbf_frontdatadetails.entity_id) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) WHERE pbf_entities.entity_class='" . $entity_class . "' AND pbf_entities.entity_geozone_id='" . $geozone_id . "' GROUP BY pbf_entities.entity_id ORDER BY pbf_entities.entity_type DESC,pbf_entities.entity_name";
		}
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_feature_accounts() {
		$sql = "SELECT user_fullname,user_name,user_jobtitle,user_phonenumber FROM pbf_users WHERE user_published='1'";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_reports() {
		$sql = "SELECT * FROM pbf_reporting";
		return $this->db->query ( $sql )->result_array ();
	}
	function get_entities_classes_access() {
		$sql = "SELECT DISTINCT(pbf_entityclasses.entity_class_id),IF((pbf_entityclasses.entity_class_abbrev='') OR (pbf_entityclasses.entity_class_abbrev IS NULL),pbf_entityclasses.entity_class_name,CONCAT(pbf_entityclasses.entity_class_name,' (',pbf_entityclasses.entity_class_abbrev,')')) AS entity_class_name FROM pbf_entityclasses JOIN pbf_usersgroupsassets ON (pbf_usersgroupsassets.asset_id=pbf_entityclasses.entity_class_id AND pbf_usersgroupsassets.asset_link='entity_class') WHERE pbf_usersgroupsassets.usersgroup_id='" . $this->session->userdata ( 'usergroup_id' ) . "'";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_indicators_by_filetype($lang) {
		$sql = "SELECT pbf_indicators.indicator_id,pbf_indicatorstranslations.indicator_title,pbf_indicatorstranslations.indicator_abbrev FROM pbf_indicators LEFT JOIN pbf_indicatorsfileypes ON (pbf_indicatorsfileypes.indicator_id=pbf_indicators.indicator_id) LEFT JOIN pbf_indicatorstranslations ON pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id WHERE pbf_indicatorsfileypes.filetype_id IN (1,5) AND pbf_indicatorstranslations.indicator_language ='" . $lang . "' ORDER BY pbf_indicatorsfileypes.order";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function render_geozones($geo_id) {
		$sql = "SELECT geozone_id,geo_id,geozone_name,geozone_htmlmap,geozone_active FROM pbf_geozones WHERE geo_id='" . $geo_id . "' ORDER BY geozone_active DESC, geozone_name ASC";
		
		return $this->db->query ( $sql )->result_array ();
	}
	
	/*
	 * Used to render geojson data for google map.
	 */
	function render_geojson($geo_id, $parent_id) {
		if (! empty ( $parent_id ) && $parent_id != 0) {
			
			$sql = "SELECT pbf_child.geozone_id,pbf_child.geozone_name,pbf_child.geo_id,pbf_child.geozone_geojson,pbf_child.geozone_active 
                FROM pbf_geozones pbf_child left join pbf_geozones pbf_p on pbf_p.geozone_id=pbf_child.geozone_parentid WHERE pbf_child.geozone_parentid='" . $parent_id . "' and pbf_p.geo_id=" . $geo_id . " ORDER BY pbf_child.geozone_active DESC, pbf_child.geozone_name ASC";
		} else {
			$sql = "SELECT geozone_id,geo_id,geozone_name,geozone_geojson,geozone_active FROM pbf_geozones WHERE geo_id='" . $geo_id . "' ORDER BY geozone_active DESC, geozone_name ASC";
		}
		
		return $this->db->query ( $sql )->result_array ();
	}
	function count_entities($geozone_id, $parent = false) {
		if ($parent) {
			// Requete qui compte le nombre d'entités actives dans une zone active
			$sql = "SELECT COUNT(*) as num_rows FROM pbf_entities WHERE entity_geozone_id in 

                (select geozone_id from pbf_geozones where geozone_parentid='" . $geozone_id . "' AND geozone_active =1) and entity_active =1";
		} else {
			$sql = "SELECT COUNT(*) as num_rows FROM pbf_entities WHERE entity_geozone_id=" . $geozone_id . " and entity_active=1";
		}
		
		return $this->db->query ( $sql )->row ()->num_rows;
	}
	function get_entities_without_coords($num = 0, $filters) {
		$sql = "SELECT en.entity_id, region.geozone_name region, district.geozone_name district, en.entity_name entity, en.entity_geo_long, en.entity_geo_lat
                    FROM pbf_entities en
                    LEFT JOIN pbf_geozones district ON en.entity_geozone_id = district.geozone_id
                    LEFT JOIN pbf_geozones region ON district.geozone_parentid = region.geozone_id
                    WHERE entity_geo_long = 0
                    AND entity_geo_lat = 0
                    AND entity_active = 1";
		
		$return ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$append = " ORDER BY region.geozone_name, district.geozone_name ASC , en.entity_name ASC";
		
		$append .= " LIMIT $num , " . $this->config->item ( 'rec_per_page' );
		
		$sql .= $append;
		
		$result = $this->db->query ( $sql )->result_array ();
		
		$return ['list'] = $result;
		
		return $return;
	}
	
	/*
	 * Center coordiantes of a polygon
	 */
	function get_center_coords($geo_id) {
		return $this->db->select ( 'geo_lat_long' )->from ( 'pbf_geozones' )->where ( 'geozone_id', $geo_id )->get ()->row ();
	}
	
	// get edito at a given position
	function get_edito($content_position) {
		$sql = "SELECT pbf_content_news.content_id FROM pbf_content_news WHERE pbf_content_news.content_published='1' AND pbf_content_news.content_category = '38' AND pbf_content_news.content_position ='" . $content_position . "'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_keydata($zone) {
		if (! empty ( $zone )) {
			$sql = "SELECT pbf_popcible.popcible_id,pbf_popcible.popcible_name, pbf_popciblezones.popcible_percentage, pbf_popcible.popcible_published FROM pbf_popcible LEFT JOIN pbf_popciblezones ON pbf_popciblezones.popcible_id = pbf_popcible.popCible_id WHERE pbf_popciblezones.zone_id ='" . $zone . "'";
		} else {
			
			$sql = "SELECT pbf_popcible.popcible_id,pbf_popcible.popcible_name, pbf_popcible.popcible_percentage, pbf_popcible.popcible_published FROM pbf_popcible";
		}
		
		return $this->db->query ( $sql )->result_array ();
	}
	
	// get edito at a given position
	function get_topcms($zone) {
		$sql = "SELECT pbf_content_news.content_position as type, pbf_content_news.content_title, pbf_content_news.content_description as amount,pbf_content_news.content_params FROM pbf_content_news WHERE pbf_content_news.content_published='1' AND pbf_content_news.content_category = '39'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	
	// get edito translation
	function get_edito_translation($content_id, $lang) {
		$sql = "SELECT pbf_editotranslation.html_block FROM pbf_editotranslation WHERE pbf_editotranslation.content_id='" . $content_id . "' AND pbf_editotranslation.language = '" . $lang . "'";
		return $this->db->query ( $sql )->row_array ();
	}
	function get_top_quality($period, $zone = '', $zone_type = '', $content_type = '13', $limit = '5') {
		$sql = "SELECT df.datafile_id,df.entity_id,datafile_total,en.entity_name,
                    et.entity_type_name, et.entity_type_abbrev,en.entity_picturepath
                    FROM pbf_datafile df
                    JOIN pbf_filetypes ft ON df.filetype_id = ft.filetype_id
                    AND ft.filetype_id IN (14,15,16)
                    JOIN pbf_entities en ON en.entity_id = df.entity_id
                    JOIN pbf_entitytypes et ON et.entity_type_id = en.entity_type
                    JOIN pbf_geozones ge ON ge.geozone_id = en.entity_geozone_id
                    JOIN pbf_geozones pg ON pg.geozone_id = ge.geozone_parentid                    
                    WHERE 1=1                    
                    AND ft.filetype_active =1 AND df.datafile_status=1";
		if (! empty ( $period ) && count ( $period )) {
			$sql .= " AND df.datafile_quarter = " . $period ['data_quarter'] . " AND df.datafile_year = " . $period ['data_year'];
		}
		
		if ($zone != '') {
			if ($zone_type == 'district') {
				$sql .= " AND en.entity_geozone_id = '" . $zone . "'";
			} else {
				$sql .= " AND ge.geozone_parentid = '" . $zone . "'";
			}
		}
		
		$sql = $sql . " ORDER BY df.datafile_year DESC , df.datafile_quarter DESC , df.datafile_total DESC 
                    LIMIT " . $limit;
		// echo $sql."<br/>";
		
		return $this->db->query ( $sql )->result_array ();
	}
	
	// TOP
	// @$entity_type_id value must be the id of the indentity type
	// curently used are 1 : DH and 2 : IHC
	// @content_type indicates content types possible values are id of quantity = 12, quality = 13
	// @limit number of top to return
	function get_top($entity_type_id, $content_type, $limit, $zone = '', $period = '') {
		
		// Amelioration de la requete en ajoutant le champ du chemin d'acces à l'image
		$sql = "SELECT df.datafile_id, df.filetype_id, df.datafile_total, df.entity_id, en.entity_picturepath ,df.datafile_quarter, df.datafile_year, en.entity_id, en.entity_name, en.entity_picturepath, ft.filetype_name, et.entity_type_name, et.entity_type_abbrev, ge.geozone_name, pg.geozone_name as parentgeo FROM pbf_datafile df JOIN pbf_filetypes ft ON df.filetype_id = ft.filetype_id AND ft.filetype_contenttype = " . $content_type . " JOIN pbf_entities en ON en.entity_id = df.entity_id JOIN pbf_entitytypes et ON et.entity_type_id = en.entity_type AND ft.filetype_active =1 AND df.datafile_status=1 AND et.entity_type_id = " . $entity_type_id . " JOIN pbf_geozones ge ON ge.geozone_id = en.entity_geozone_id JOIN pbf_geozones pg ON pg.geozone_id = ge.geozone_parentid WHERE 1=1";
		if ($zone != '') {
			$sql .= " AND ge.geozone_parentid = '" . $zone . "'";
		}
		
		if (! empty ( $period ) && count ( $period )) {
			$sql .= " AND df.datafile_quarter = " . $period ['data_quarter'] . " AND df.datafile_year = " . $period ['data_year'];
		}
		
		$sql = $sql . " ORDER BY df.datafile_year DESC , df.datafile_quarter DESC , df.datafile_total DESC LIMIT " . $limit;
		
		return $this->db->query ( $sql )->result_array ();
	}
	
	// Fonction qui retourne les top qualités d'une entité specifiée
	function get_top_previous_period($entity_type_id, $entity_id, $content_type, $limit, $zone = '', $period = '') {
		$sql = "SELECT df.datafile_id, df.filetype_id, df.datafile_total, df.entity_id, en.entity_picturepath ,df.datafile_quarter, df.datafile_year, en.entity_id, en.entity_name, ft.filetype_name, et.entity_type_name, et.entity_type_abbrev, ge.geozone_name, pg.geozone_name as parentgeo FROM pbf_datafile df JOIN pbf_filetypes ft ON df.filetype_id = ft.filetype_id AND ft.filetype_contenttype = " . $content_type . " JOIN pbf_entities en ON en.entity_id = df.entity_id JOIN pbf_entitytypes et ON et.entity_type_id = en.entity_type AND ft.filetype_active =1 AND df.datafile_status=1 AND et.entity_type_id = " . $entity_type_id . " JOIN pbf_geozones ge ON ge.geozone_id = en.entity_geozone_id JOIN pbf_geozones pg ON pg.geozone_id = ge.geozone_parentid WHERE 1=1";
		if ($zone != '') {
			$sql .= " AND ge.geozone_parentid = '" . $zone . "'";
		}
		
		if (! empty ( $period ) && count ( $period )) {
			$sql .= " AND df.datafile_quarter = " . $period ['data_quarter'] . " AND df.datafile_year = " . $period ['data_year'] . " AND df.entity_id=" . $entity_id . "";
		}
		
		$sql = $sql . " ORDER BY df.datafile_year DESC , df.datafile_quarter DESC , df.datafile_total DESC LIMIT " . $limit;
		// echo $sql."<br/>";
		return $this->db->query ( $sql )->result_array ();
	}
	function get_top_district($entity_type_id, $content_type, $limit, $district = '', $period = '') {
		$sql = "SELECT df.datafile_id, df.filetype_id, df.datafile_total, df.entity_id, df.datafile_quarter, df.datafile_year, en.entity_id, en.entity_name, en.entity_picturepath, ft.filetype_name, et.entity_type_name, et.entity_type_abbrev, ge.geozone_name, pg.geozone_name as parentgeo
                    FROM pbf_datafile df
                    JOIN pbf_filetypes ft ON df.filetype_id = ft.filetype_id
                    AND ft.filetype_contenttype = " . $content_type . "
                    JOIN pbf_entities en ON en.entity_id = df.entity_id
                    JOIN pbf_entitytypes et ON et.entity_type_id = en.entity_type
                    AND ft.filetype_active =1 AND df.datafile_status=1
                    AND et.entity_type_id = " . $entity_type_id . "
                    JOIN pbf_geozones ge ON ge.geozone_id = en.entity_geozone_id
                    JOIN pbf_geozones pg ON pg.geozone_id = ge.geozone_parentid
                    where 1=1 AND en.entity_geozone_id = '" . $district . "'";
		
		if (! empty ( $period ) && count ( $period )) {
			$sql .= " AND df.datafile_quarter = " . $period ['data_quarter'] . " AND df.datafile_year = " . $period ['data_year'];
		}
		
		$sql = $sql . " ORDER BY df.datafile_year DESC , df.datafile_quarter DESC , df.datafile_total DESC
                    LIMIT " . $limit;
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_average_quality_region($region = '', $period = '') {
		$sql = "SELECT pbf_entitytypes.entity_type_name as name_type, AVG(df.datafile_total) as average_qual
                    FROM pbf_entitytypes
                    LEFT JOIN pbf_entities ON pbf_entities.entity_type = pbf_entitytypes.entity_type_id  
                    LEFT JOIN pbf_datafile df ON df.entity_id = pbf_entities.entity_id
                   LEFT JOIN pbf_filetypes ON pbf_filetypes.filetype_id = df.filetype_id
                 LEFT JOIN pbf_geozones ON pbf_geozones.geozone_id = pbf_entities.entity_geozone_id
                       where pbf_entitytypes.entity_class_id = '1' AND df.datafile_status = '1' AND pbf_filetypes.filetype_contenttype='13' ";
		if ($region != '') {
			$sql .= " AND pbf_geozones.geozone_parentid = '" . $region . "'";
		}
		if ($period != '') {
			$period_array = $this->pbf->get_start_end_date_published ( $period );
			$start_date = $period_array ['start_date'];
			$end_date = $period_array ['end_date'];
			
			$sql .= " AND  STR_TO_DATE( CONCAT( df.datafile_year,'-', df.datafile_month ) ,  '%Y-%m' ) BETWEEN  '" . $start_date . "' AND '" . $end_date . "' ";
		}
		
		$sql .= "GROUP BY pbf_entitytypes.entity_type_name";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_average_quality_zone($zone = '', $period = '') {
		$sql = "SELECT  pbf_entitytypes.entity_type_id, pbf_entitytypes.entity_type_name as name_type, pbf_entitytypes.entity_type_abbrev,  AVG(df.datafile_total) as average_qual FROM pbf_entitytypes
                    LEFT JOIN pbf_entities ON pbf_entities.entity_type = pbf_entitytypes.entity_type_id
                    LEFT JOIN pbf_datafile df ON df.entity_id = pbf_entities.entity_id
                   LEFT JOIN pbf_filetypes ON pbf_filetypes.filetype_id = df.filetype_id
                     where pbf_entitytypes.entity_class_id = '1' AND df.datafile_status = '1' AND pbf_filetypes.filetype_id IN(14,15,16)";
		if ($zone != '') {
			$sql .= " AND pbf_entities.entity_geozone_id = '" . $zone . "'";
		}
		// $period=24;
		
		if ($period != '') {
			$period_array = $this->pbf->get_start_end_date_published ( $period );
			$start_date = $period_array ['start_date'];
			$end_date = $period_array ['end_date'];
			
			$sql .= " AND  STR_TO_DATE( CONCAT( df.datafile_year,'-', df.datafile_month ) ,  '%Y-%m' ) BETWEEN  '" . $start_date . "' AND '" . $end_date . "' ";
		}
		
		$sql .= "GROUP BY pbf_entitytypes.entity_type_name";
		// echo $sql."<br/>";
		
		return $this->db->query ( $sql )->result_array ();
	}
	
	/**
	 *
	 * @param type $quarter
	 *        	which quarter to we want
	 * @param type $entity_type_id
	 *        	which entity type do we want
	 *        	curently used are 1 : DH and 2 : IHC
	 * @param type $year
	 *        	optional year. If not set the current year will be used
	 */
	function get_global_quality_score($quarter, $entity_type_id, $year = '') {
		if (empty ( $year )) {
			// let's use the current year
			$year = date ( 'Y' );
		}
		
		$sql = "SELECT AVG(df.datafile_total) average
                    FROM pbf_datafile df 
                    JOIN pbf_filetypes ft on df.filetype_id = ft.filetype_id
                    JOIN pbf_entities en on en.entity_id = df.entity_id
                    WHERE df.datafile_quarter = '" . $quarter . "' and ft.filetype_contenttype = 13 and en.entity_type='" . $entity_type_id . "' and df.datafile_status = 1";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_global_quality_score_zone($quarter, $entity_type_id, $year = '', $zone) {
		if (empty ( $year )) {
			// let's use the current year
			$year = date ( 'Y' );
		}
		
		$sql = "SELECT AVG(df.datafile_total) average
                    FROM pbf_datafile df
                    JOIN pbf_filetypes ft on df.filetype_id = ft.filetype_id
                    JOIN pbf_entities en on en.entity_id = df.entity_id
                    JOIN pbf_geozones geo on geo.geozone_id = en.entity_geozone_id
                    WHERE df.datafile_quarter = '" . $quarter . "' and df.datafile_year = '" . $year . "' and ft.filetype_contenttype = 13 and en.entity_type='" . $entity_type_id . "' and geo.geozone_parentid ='" . $zone . "
                    ' and df.datafile_status='1'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_global_quality_score_district($quarter, $entity_type_id, $year = '', $district) {
		if (empty ( $year )) {
			// let's use the current year
			$year = date ( 'Y' );
		}
		
		$sql = "SELECT AVG(df.datafile_total) average
                    FROM pbf_datafile df
                    JOIN pbf_filetypes ft on df.filetype_id = ft.filetype_id
                    JOIN pbf_entities en on en.entity_id = df.entity_id
                    WHERE df.datafile_quarter = '" . $quarter . "' and df.datafile_year = '" . $year . "' and ft.filetype_contenttype = 13 and en.entity_type='" . $entity_type_id . "' and en.entity_geozone_id ='" . $district . "
                    ' and df.datafile_status='1'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_real_time_result_indicators_id() {
		$sql = "SELECT indicator_id, indicator_use_coverage, indicator_popcible FROM pbf_indicators where indicator_realtime_result = 1";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_real_time_result($indicator_id, $start_date, $end_date, $lang, $geozone_id) {
		$append = '';
		if (! empty ( $geozone_id )) {
			$append = " AND pe.entity_geozone_id in (" . $this->get_rqst_geozone_ids ( $geozone_id ) . ") ";
		}
		
		$sql = "SELECT dd.indicator_id,i.indicator_abbrev, SUM( dd.indicator_verified_value ) sum_validated_value, i.indicator_icon_file, tr.indicator_common_name FROM pbf_indicators i JOIN pbf_datafiledetails dd ON dd.indicator_id = i.indicator_id JOIN pbf_datafile df ON dd.datafile_id = df.datafile_id LEFT JOIN pbf_entities pe on pe.entity_id = df.entity_id JOIN pbf_indicatorstranslations tr ON ( tr.indicator_id = i.indicator_id AND tr.indicator_language =  '" . $lang . "') AND i.indicator_realtime_result =1 AND i.indicator_id ='" . $indicator_id . "' WHERE STR_TO_DATE( CONCAT( df.datafile_year,'-', df.datafile_month ) ,  '%Y-%m' ) BETWEEN  '" . $start_date . "' AND '" . $end_date . "' AND df.datafile_status=1 " . $append . " GROUP BY dd.indicator_id, tr.indicator_title, i.indicator_icon_file";
		// echo $sql."<br/>";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_real_time_all($indicator_id, $lang, $geozone_id, $entity = false) {
		$append = '';
		if (! empty ( $geozone_id )) {
			$append = " AND pe.entity_geozone_id in ('" . $this->get_rqst_geozone_ids ( $geozone_id ) . "') ";
			// echo $append.'<br />';
		}
		if ($entity) {
			$append = "AND pe.entity_id='" . $geozone_id . "'";
		}
		$sql = "SELECT dd.indicator_id, tr.indicator_abbrev, SUM( dd.indicator_verified_value ) sum_validated_value, i.indicator_icon_file, tr.indicator_common_name FROM pbf_indicators i
				LEFT JOIN pbf_datafiledetails dd ON dd.indicator_id = i.indicator_id JOIN pbf_datafile df ON dd.datafile_id = df.datafile_id
				LEFT JOIN pbf_entities pe on pe.entity_id = df.entity_id JOIN pbf_indicatorstranslations tr ON ( tr.indicator_id = i.indicator_id AND tr.indicator_language =  '" . $lang . "' ) AND i.indicator_realtime_result =1 AND i.indicator_id ='" . $indicator_id . "'AND df.datafile_status=1 " . $append . " GROUP BY dd.indicator_id, tr.indicator_title, i.indicator_icon_file";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_real_time_total($start_date, $end_date) {
		$sql = "SELECT SUM(pbf_frontdatadetails.amount_total) AS total FROM pbf_frontdatadetails 
                    LEFT JOIN pbf_frontdata ON pbf_frontdata.frontdata_id = pbf_frontdatadetails.frontdata_id
                    WHERE STR_TO_DATE( CONCAT( pbf_frontdata.data_year,'-', pbf_frontdata.data_month) ,  '%Y-%m' )
                    BETWEEN  '" . $start_date . "'
                    AND  '" . $end_date . "'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_real_time_total_all() {
		$sql = "SELECT SUM(pbf_frontdatadetails.amount_total) AS total FROM pbf_frontdatadetails
                    LEFT JOIN pbf_frontdata ON pbf_frontdata.frontdata_id = pbf_frontdatadetails.frontdata_id";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_real_time_total_budget() {
		$sql = "SELECT SUM(pbf_budget.budget_value) AS total FROM pbf_budget
                    WHERE pbf_budget.budget_month IS NULL AND pbf_budget.budget_quarter IS NULL AND pbf_budget.budget_year=(YEAR(CURDATE())-1)";
		$budget_total = $this->db->query ( $sql )->row_array ();
		return $budget_total;
	}
	function get_real_time_payment_all($zone) {
		$append = '';
		if (! empty ( $zone )) {
			$append = " WHERE pe.entity_geozone_id in (" . $this->get_rqst_geozone_ids ( $zone ) . ") ";
		}
		
		$sql = "SELECT SUM(pbf_frontdatadetails.amount_total) AS total FROM pbf_frontdatadetails
                    LEFT JOIN pbf_frontdata ON pbf_frontdata.frontdata_id = pbf_frontdatadetails.frontdata_id
                    LEFT JOIN pbf_entities pe on pe.entity_id = pbf_frontdatadetails.entity_id" . $append;
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_oldest_datafile_year() {
		$sql = "SELECT datafile_year FROM pbf_datafile order by datafile_year asc limit 1";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_oldest_budget_year() {
		$sql = "SELECT budget_year FROM pbf_budget order by budget_year asc limit 1";
		
		return $this->db->query ( $sql )->row_array ();
	}
	
	/* new methods */
	function render_zone_entities_group($geozone_id) {
		$sql = "SELECT pbf_entities.entity_id,pbf_entitytypes.entity_type_name,CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev) AS entity_name,pbf_entities.entity_geo_long,pbf_entities.entity_geo_lat,pbf_entities.entity_type as entity_type_id,pbf_entitygroups.entity_group_id,pbf_entitygroups.entity_group_abbrev AS groupe FROM pbf_entities JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class ) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_lookups pbf_lkp_status  ON (pbf_lkp_status.lookup_id=pbf_entities.entity_status AND pbf_lkp_status.lookup_linkfile='entity_status') LEFT JOIN pbf_entitygroups ON(pbf_entities.entity_pbf_group_id=pbf_entitygroups.entity_group_id ) WHERE pbf_entities.entity_geozone_id='" . $geozone_id . "' AND pbf_entities.entity_active = '1' AND pbf_entityclasses.entity_class_id='1' ORDER BY pbf_entities.entity_type";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_quality_per_region($district_id, $period, $entity_type, $parent = TRUE) {
		$append = '';
		$entity_level = '';
		if (! empty ( $district_id )) {
			$entity_level = 'geozone_id';
			if ($parent) {
				$append = "and pe.entity_geozone_id in (SELECT geozone_id from pbf_geozones where geozone_parentid='" . $district_id . "')";
			} else {
				$append = "and pe.entity_geozone_id in (SELECT geozone_id from pbf_geozones where geozone_id='" . $district_id . "')";
			}
		} else {
			$entity_level = 'geozone_parentid';
		}
		$sql = "select p_geo.geozone_id, p_geo.geozone_name,p_geo.geo_id ";
		foreach ( $period as $p ) {
			$sql .= ",ROUND(AVG(t." . $p ['datafile_quarter'] . "_" . $p ['datafile_year'] . ")) AS 'Q " . $p ['datafile_quarter'] . " " . $p ['datafile_year'] . "'";
		}
		$sql .= " FROM( SELECT pgeo." . $entity_level;
		
		foreach ( $period as $p ) {
			$sql .= ",sum(IF(df.datafile_year='" . $p ['datafile_year'] . "' and df.datafile_quarter='" . $p ['datafile_quarter'] . "',df.datafile_total,'0')) as '" . $p ['datafile_quarter'] . "_" . $p ['datafile_year'] . "' ";
		}
		$sql .= " FROM pbf_datafile df LEFT JOIN `pbf_entities` pe on df.entity_id = pe.entity_id
                 LEFT JOIN pbf_filetypes ft on ft.filetype_id=df.filetype_id LEFT JOIN pbf_geozones pgeo ON pgeo.geozone_id=pe.entity_geozone_id
                 WHERE pe.entity_type ='" . $entity_type . "' and ft.filetype_contenttype = 13 " . $append . "
                 GROUP BY df.entity_id ";
		$sql .= ") AS t left join pbf_geozones p_geo on p_geo.geozone_id=t." . $entity_level . " GROUP by t." . $entity_level;
		
		// echo $sql.'<br />';
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_district_qualities($district_id, $period, $entity_type, $parent = TRUE) {
		$append = '';
		
		if (! empty ( $district_id )) {
			if ($parent) {
				$append = "and pe.entity_geozone_id in (SELECT geozone_id from pbf_geozones where geozone_parentid='" . $district_id . "')";
			} else {
				$append = "and pe.entity_geozone_id in (SELECT geozone_id from pbf_geozones where geozone_id='" . $district_id . "')";
			}
		}
		
		$sql = "SELECT pe.entity_name,pe.entity_id";
		
		foreach ( $period as $p ) {
			$sql .= ",sum(IF(df.datafile_year='" . $p ['datafile_year'] . "' and df.datafile_quarter='" . $p ['datafile_quarter'] . "',df.datafile_total,'0')) as 'Q " . $p ['datafile_quarter'] . " " . $p ['datafile_year'] . "' ";
		}
		
		$sql .= "
                 FROM pbf_datafile df LEFT JOIN `pbf_entities` pe on df.entity_id = pe.entity_id
                 LEFT JOIN pbf_filetypes ft on ft.filetype_id=df.filetype_id 

                 WHERE df.datafile_status=1 and pe.entity_type ='" . $entity_type . "' and ft.filetype_contenttype = 13 " . $append . "
                 GROUP BY df.entity_id";
		
		// echo $sql;
		return $this->db->query ( $sql )->result_array ();
	}
	function get_entity_qualities($district_id, $period, $entity_type) {
		$sql = "SELECT pe.entity_name,pe.entity_id";
		
		foreach ( $period as $p ) {
			$sql .= ",sum(IF(df.datafile_year='" . $p ['datafile_year'] . "' and df.datafile_quarter='" . $p ['datafile_quarter'] . "',df.datafile_total,'0')) as 'Q " . $p ['datafile_quarter'] . " " . $p ['datafile_year'] . "' ";
		}
		
		$sql .= "
                 FROM pbf_datafile df LEFT JOIN `pbf_entities` pe on df.entity_id = pe.entity_id
                 LEFT JOIN pbf_filetypes ft on ft.filetype_id=df.filetype_id 
                 WHERE df.datafile_status = 1 and pe.entity_type ='" . $entity_type . "' and ft.filetype_contenttype = 13 and df.entity_id = '" . $district_id . "'
                 GROUP BY df.entity_id";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_entity_quality_indicators($entity_id, $period) {
		$sql = "SELECT ind.indicator_id,tr.indicator_title as '" . strtoupper ( $this->lang->line ( 'heat_map_indicator' ) ) . "'";
		
		foreach ( $period as $p ) {
			$sql .= ",FORMAT(sum(IF(df.datafile_year='" . $p ['datafile_year'] . "' and df.datafile_quarter='" . $p ['datafile_quarter'] . "',dd.indicator_montant,NULL)),0) as 'Q " . $p ['datafile_quarter'] . " " . $p ['datafile_year'] . "' ";
		}
		
		$sql .= "
                 FROM pbf_datafiledetails dd left JOIN pbf_datafile df on dd.datafile_id = df.datafile_id LEFT JOIN pbf_indicators ind on dd.indicator_id=ind.indicator_id LEFT JOIN pbf_filetypes ft on ft.filetype_id=df.filetype_id JOIN pbf_indicatorstranslations tr ON ( tr.indicator_id = ind.indicator_id AND tr.indicator_language =  '" . $this->config->item ( 'language_abbr' ) . "' )
                 WHERE df.datafile_status = 1 and df.entity_id ='" . $entity_id . "' and ft.filetype_contenttype = 13
                 GROUP BY dd.indicator_id";
		// echo $sql;
		return $this->db->query ( $sql )->result_array ();
	}
	function get_zone_quality_indicators($zone_id, $period, $entity_type) {
		$sql = "SELECT ind.indicator_id,tr.indicator_title as '" . strtoupper ( $this->lang->line ( 'heat_map_indicator' ) ) . "'";
		
		foreach ( $period as $p ) {
			$sql .= ",FORMAT(AVG(IF(df.datafile_year='" . $p ['datafile_year'] . "' and df.datafile_quarter='" . $p ['datafile_quarter'] . "',dd.indicator_montant,NULL)),0) as 'Q " . $p ['datafile_quarter'] . " " . $p ['datafile_year'] . "' ";
			// $sql.= ", FORMAT( AVG( NULLIF( IF( df.datafile_year = '".$p['datafile_year']."' AND df.datafile_quarter = '".$p['datafile_quarter']."', dd.indicator_montant, NULL ) , 0 ) ) , 0 ) AS 'Q ".$p['datafile_quarter']." ".$p['datafile_year']."' ";
		}
		
		$sql .= "
                 FROM pbf_datafiledetails dd left JOIN pbf_datafile df on dd.datafile_id = df.datafile_id LEFT JOIN pbf_indicators ind on dd.indicator_id=ind.indicator_id LEFT JOIN pbf_filetypes ft on ft.filetype_id=df.filetype_id
                 LEFT JOIN pbf_entities ent on df.entity_id = ent.entity_id LEFT JOIN pbf_geozones gz on gz.geozone_id = ent.entity_geozone_id
                 LEFT JOIN pbf_entitytypes ent_type on ent_type.entity_type_id = ent.entity_type JOIN pbf_indicatorstranslations tr ON ( tr.indicator_id = ind.indicator_id AND tr.indicator_language =  '" . $this->config->item ( 'language_abbr' ) . "' )
                 WHERE df.datafile_status = 1 and ent.entity_geozone_id in (" . $this->get_rqst_geozone_ids ( $zone_id ) . ") and ft.filetype_contenttype = 13
                 AND entity_type = '" . $entity_type . "' GROUP BY dd.indicator_id";
		// echo $sql.'<br />';
		// echo '===================================================================';
		return $this->db->query ( $sql )->result_array ();
	}
	function get_last_published_date() {
		$sql = "SELECT pbf_frontdata.data_month, pbf_frontdata.data_quarter, pbf_frontdata.data_year FROM pbf_frontdata ORDER BY pbf_frontdata.data_year DESC, pbf_frontdata.data_quarter DESC, pbf_frontdata.data_month DESC";
		
		return $this->db->query ( $sql )->result_array ();
	}
	
	// =======================================================================================================
	function get_annual_budget($annee, $entity) {
		$sql = "SELECT pbf_entityclasses.entity_class_name as Entity_class";
		$sql .= " ,FORMAT(SUM(IF(budget_month is NULL AND pbf_budget.budget_year='" . $annee . "',pbf_budget.budget_value,0)),0) AS '" . $annee . "'";
		$sql .= "  FROM pbf_budget LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id = pbf_budget.entity_geozone_id) LEFT JOIN pbf_entities ON (pbf_budget.entity_id=pbf_entities.entity_id) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) AND (pbf_entitytypes.entity_class_id=pbf_entityclasses.entity_class_id)
		WHERE pbf_entityclasses.entity_class_id=1 ";
		
		if ($entity != '') {
			
			$sql .= " AND pbf_budget.entity_id IN (" . $entity . ") ";
		}
		
		$sql .= "GROUP BY pbf_entityclasses.entity_class_id ORDER BY pbf_budget.budget_year DESC";
		$budget_data = $this->db->query ( $sql )->result_array ();
		return $budget_data;
	}
	function get_budget_values($arr_period, $entity) {
		$units = '';
		$fx = 'SUM';
		$sql = "SELECT 'BUDGET' as TRIMESTRE";
		
		foreach ( $arr_period as $arr_period_val ) {
			
			// check if month or quarteR
			
			// print_r($arr_period_val);
			
			$sql .= ",CONCAT(" . $fx . "(IF(budget_quarter='" . $arr_period_val ['data_quarter'] . "' AND pbf_budget.budget_year='" . $arr_period_val ['data_year'] . "',pbf_budget.budget_value,0)),'" . $units . "') AS '" . $this->lang->line ( 'app_quarter_' . $arr_period_val ['data_quarter'] ) . " " . $arr_period_val ['data_year'] . "'";
		}
		
		/* $sql .= " FROM pbf_datafile LEFT JOIN pbf_datafiledetails ON (pbf_datafiledetails.datafile_id = pbf_datafile.datafile_id) LEFT JOIN pbf_indicators ON (pbf_indicators.indicator_id=pbf_datafiledetails.indicator_id) LEFT JOIN pbf_entities ON (pbf_entities.entity_id = pbf_datafile.entity_id) LEFT JOIN pbf_indicatorsfileypes ON (pbf_indicatorsfileypes.indicator_id = pbf_indicators.indicator_id) LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id = pbf_indicatorsfileypes.filetype_id AND pbf_datafile.filetype_id=pbf_filetypes.filetype_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id = pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_indicatorstranslations ON pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id"; */
		
		// ==================================
		$sql .= " FROM pbf_budget LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id = pbf_budget.entity_geozone_id) LEFT JOIN pbf_entities ON (pbf_budget.entity_id=pbf_entities.entity_id) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) AND (pbf_entitytypes.entity_class_id=pbf_entityclasses.entity_class_id)
		WHERE pbf_entityclasses.entity_class_id=1 ";
		
		// ==================================================
		
		if ($entity != '') {
			
			$sql .= " AND pbf_budget.entity_id IN (" . $entity . ") ";
		}
		if ($zone != '') {
			
			$sql .= " AND pbf_budget.entity_geozone_id IN (" . $this->get_rqst_geozone_ids ( $zone ) . ") ";
		}
		
		$sql .= "GROUP BY pbf_entityclasses.entity_class_id ORDER BY pbf_budget.budget_year DESC,pbf_budget.budget_month DESC";
		
		$budget_data = $this->db->query ( $sql )->result_array ();
		
		return $budget_data;
	}
	function check_annual_budget($annee) {
		$sql = "SELECT budget_year FROM pbf_budget WHERE budget_month is NULL AND budget_year='" . $annee . "'";
		return $this->db->query ( $sql )->num_rows ();
	}
	function check_month_budget($annee) {
		$sql = "SELECT budget_month FROM pbf_budget WHERE budget_month is NOT NULL AND budget_year='" . $annee . "'";
		return $this->db->query ( $sql )->num_rows ();
	}
	function get_user_group_access($group_id) {
		$sql = "SELECT user_group_access FROM pbf_usersgroups WHERE usersgroup_id =" . $group_id;
		
		$group_access = $this->db->query ( $sql )->row_array ();
		
		return json_decode ( $group_access ['user_group_access'], true );
	}
	
	// fonction qui retourne le nombre d'entités par type
	function get_number_entity() {
		$get_entity_type = $this->get_average_quality_zone ();
		if (empty ( $get_entity_type )) {
			$get_entity_type = $this->get_entity_types ();
		}
		
		$sql_append = "";
		
		$usergeozones = $this->session->userdata ( 'usergeozones' ); // Liste des zones auxquelles l'utilisateur a accès
		
		if (! empty ( $usergeozones )) {
			
			$sql_append .= " AND pbf_geozones.geozone_id IN (" . implode ( ',', $usergeozones ) . ") ";
		}
		
		$entity = array ();
		
		foreach ( $get_entity_type as $res ) {
			// Requette qui recuperre le nombre d'entité par type d'entités et par zones auxquelles l'utilisateur a accès
			$sql = "SELECT pbf_entitytypes.entity_type_id, pbf_entitytypes.entity_type_name, pbf_entitytypes.entity_type_abbrev,
				SUM( 	pbf_entities.entity_pop ) AS total_pop, COUNT( * ) AS total FROM pbf_entities
				JOIN pbf_entitytypes ON pbf_entitytypes.entity_type_id = pbf_entities.entity_type
				JOIN pbf_geozones ON pbf_geozones.geozone_id=pbf_entities.entity_geozone_id
				WHERE pbf_entities.entity_active = 1 AND pbf_entities.entity_type =" . $res ['entity_type_id'] . " " . $sql_append . "";
			// echo $sql."<br/>";
			$res = $this->db->query ( $sql )->result_array ();
			
			if ($res [0] ['total'] != 0) {
				array_push ( $entity, $this->db->query ( $sql )->result_array () );
			}
		}
		
		return $entity;
	}
	function users_groups_list() {
		$sql = "SELECT usersgroup_id FROM pbf_usersgroups";
		$group_list = $this->db->query ( $sql )->result_array ();
		return $group_list;
	}
	function users_list($group) {
		$sql = "SELECT pbf_users.user_id,pbf_users.user_fullname,pbf_users.user_name FROM pbf_users LEFT JOIN pbf_usersgroupsmap ON(pbf_usersgroupsmap.user_id=pbf_users.user_id) WHERE pbf_usersgroupsmap.usergroup_id='" . $group . "'";
		$users_list = $this->db->query ( $sql )->result_array ();
		return $users_list;
	}
	function alertes_list($group) {
		$sql = "SELECT pbf_alerteconfig.* FROM pbf_alerteconfig LEFT JOIN pbf_alertesgroups_map ON (pbf_alerteconfig.alerteconfig_id=pbf_alertesgroups_map.alertes_id) WHERE pbf_alertesgroups_map.group_id='" . $group . "'";
		$alertes_list = $this->db->query ( $sql )->result_array ();
		return $alertes_list;
	}
	function files_types_list($alerte) {
		$sql = "SELECT DISTINCT filetype_id FROM alertes_filtype_map WHERE alertes_id='" . $alerte . "'";
		$filetype_list = $this->db->query ( $sql )->result_array ();
		return $filetype_list;
	}
	function liste_fosa($user) {
		$sql = "SELECT pbf_entities.entity_name,pbf_entities.entity_id,pbf_geozones.geozone_name FROM pbf_entities LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_usersgeozones ON (pbf_usersgeozones.geozone_id=pbf_geozones.geozone_id) WHERE pbf_usersgeozones.user_id='" . $user . "' ORDER BY pbf_geozones.geozone_name ASC";
		$fosa_list = $this->db->query ( $sql )->result_array ();
		return $fosa_list;
	}
	function check_data_file($fosa, $year, $month, $filetypes) {
		$sql = "SELECT * from pbf_datafile WHERE entity_id='" . $fosa . "' AND datafile_month='" . $month . "' AND datafile_year='" . $year . "' AND filetype_id IN (" . $filetypes . ")";
		$fosa_missing_data = $this->db->query ( $sql )->num_rows ();
		return $fosa_missing_data;
	}
	function check_claimed_existence($fosa, $year, $month, $filetypes, $fields) {
		$sql = "SELECT pbf_datafiledetails.datafile_id  FROM pbf_datafiledetails LEFT JOIN pbf_datafile ON (pbf_datafiledetails.datafile_id=pbf_datafile.datafile_id) 
			WHERE pbf_datafile.entity_id='" . $fosa . "' AND pbf_datafile.datafile_month='" . $month . "' AND pbf_datafile.datafile_year='" . $year . "'            
			AND pbf_datafile.filetype_id IN (" . $filetypes . ") AND pbf_datafiledetails.indicator_claimed_value IS NULL";
		$fosa_missing_data = $this->db->query ( $sql )->num_rows ();
		return $fosa_missing_data;
	}
	function check_verified_existence($fosa, $year, $month, $filetypes, $fields) {
		$sql = "SELECT pbf_datafiledetails.datafile_id  FROM pbf_datafiledetails LEFT JOIN pbf_datafile ON (pbf_datafiledetails.datafile_id=pbf_datafile.datafile_id) 
			WHERE pbf_datafile.entity_id='" . $fosa . "' AND pbf_datafile.datafile_month='" . $month . "' AND pbf_datafile.datafile_year='" . $year . "'            
			AND pbf_datafile.filetype_id IN (" . $filetypes . ") AND pbf_datafiledetails.indicator_verified_value IS NULL";
		$fosa_missing_data = $this->db->query ( $sql )->num_rows ();
		return $fosa_missing_data;
	}
	function check_validated_existence($fosa, $year, $month, $filetypes, $fields) {
		$sql = "SELECT pbf_datafiledetails.datafile_id  FROM pbf_datafiledetails LEFT JOIN pbf_datafile ON (pbf_datafiledetails.datafile_id=pbf_datafile.datafile_id) 
			WHERE pbf_datafile.entity_id='" . $fosa . "' AND pbf_datafile.datafile_month='" . $month . "' AND pbf_datafile.datafile_year='" . $year . "'            
			AND pbf_datafile.filetype_id IN (" . $filetypes . ") AND pbf_datafiledetails.indicator_validated_value IS NULL";
		$fosa_missing_data = $this->db->query ( $sql )->num_rows ();
		return $fosa_missing_data;
	}
	function check_data_verified($fosa, $year, $month) {
		$sql = "SELECT * from pbf_datafile LEFT JOIN pbf_datafiledetails ON(pbf_datafile.datafile_id=pbf_datafiledetails.datafile_id)WHERE pbf_datafiledetails.indicator_verified_value is NULL AND pbf_datafile.entity_id='" . $fosa . "' AND pbf_datafile.datafile_month='" . $month . "' AND pbf_datafile.datafile_year='" . $year . "'";
		$fosa_missing_data = $this->db->query ( $sql )->num_rows ();
		return $fosa_missing_data;
	}
	function check_file_state($fosa, $year, $month, $filetypes) {
		$sql = "SELECT * from pbf_datafile WHERE entity_id='" . $fosa . "' AND datafile_month='" . $month . "' AND datafile_year='" . $year . "' AND filetype_id IN (" . $filetypes . ")
			AND datafile_state='0'";
		$fosa_missing_data = $this->db->query ( $sql )->num_rows ();
		return $fosa_missing_data;
	}
	function check_file_status($fosa, $year, $month, $filetypes) {
		$sql = "SELECT * from pbf_datafile WHERE entity_id='" . $fosa . "' AND datafile_month='" . $month . "' AND datafile_year='" . $year . "' AND filetype_id IN (" . $filetypes . ")
			AND datafile_status='0'";
		$fosa_missing_data = $this->db->query ( $sql )->num_rows ();
		return $fosa_missing_data;
	}
	function get_usergroup($user) {
		$sql = "SELECT pbf_usersgroupsmap.usergroup_id as groupe FROM pbf_usersgroupsmap LEFT JOIN pbf_users ON (pbf_usersgroupsmap.user_id=pbf_users.user_id)  
			WHERE pbf_users.user_id='" . $user . "' LIMIT 1";
		$group = $this->db->query ( $sql )->row_array ();
		return $group;
	}
	function checksent($alerte_id, $period, $user) {
		$sql = "SELECT * FROM pbf_alertes WHERE alerteconfig_id='" . $alerte_id . "' AND month='" . $period ['month'] . "' AND year='" . $period ['year'] . "' AND user_id='" . $user . "'";
		$group = $this->db->query ( $sql )->num_rows ();
		return $group;
	}
	function checksent_trim($alerte_id, $trim, $user, $year) {
		$sql = "SELECT * FROM pbf_alertes WHERE alerteconfig_id='" . $alerte_id . "' AND quarter='" . $trim . "' AND year='" . $year . "' AND user_id='" . $user . "'";
		$group = $this->db->query ( $sql )->num_rows ();
		return $group;
	}
	function clear_alerts() {
		$sql = "DELETE FROM pbf_alertes WHERE HOUR(TIMEDIFF(NOW( ),date_alerte)) >=4320";
		$clear = $this->db->query ( $sql );
		return $clear;
	}
	function insert_report($report) {
		$this->db->insert ( 'pbf_invoices', $report );
		$id = $this->db->insert_id ();
		$this->db->where ( 'invoice_id', $id );
		$this->db->update ( 'pbf_invoices', array (
				'report_file' => $this->config->item ( 'report_prefix' ) . '_' . $id 
		) );
		
		return $id;
	}
	function update_report($report) {
		$this->db->where ( 'invoice_id', $report ['invoice_id'] );
		$this->db->update ( 'pbf_invoices', $report );
		
		return $report ['invoice_id'];
	}
	function uptodate_report_exist($report) {
		$sql = "SELECT * FROM pbf_invoices WHERE year ='" . $report ['year'] . "' AND uptodate ='1'";
		
		if ($report ['entity_id'] != '') {
			$sql .= " AND entity_id = '" . $report ['entity_id'] . "'";
		}
		if ($report ['zone_id'] != '') {
			$sql .= " AND zone_id = '" . $report ['zone_id'] . "'";
		}
		if ($report ['month'] != '') {
			$sql .= " AND month = '" . $report ['month'] . "'";
		}
		if ($report ['quarter'] != '') {
			$sql .= " AND quarter = '" . $report ['quarter'] . "'";
		}
		if ($report ['reporting_id'] != '') {
			$sql .= " AND reporting_id = '" . $report ['reporting_id'] . "'";
		}
		if ($report ['donnor_id'] != '') {
			$sql .= " AND donnor_id = '" . $report ['donnor_id'] . "'";
		}
		
		$report = $this->db->query ( $sql )->row_array ();
		
		return $report;
	}
	function not_sent_report_exist($report) {
		$sql = "SELECT * FROM pbf_invoices WHERE year ='" . $report ['year'] . "' AND sent_date IS NULL";
		
		if ($report ['entity_id'] != '') {
			$sql .= " AND entity_id = '" . $report ['entity_id'] . "'";
		}
		if ($report ['zone_id'] != '') {
			$sql .= " AND zone_id = '" . $report ['zone_id'] . "'";
		}
		if ($report ['month'] != '') {
			$sql .= " AND month = '" . $report ['month'] . "'";
		}
		if ($report ['quarter'] != '') {
			$sql .= " AND quarter = '" . $report ['quarter'] . "'";
		}
		if ($report ['reporting_id'] != '') {
			$sql .= " AND reporting_id = '" . $report ['reporting_id'] . "'";
		}
		if ($report ['donnor_id'] != '') {
			$sql .= " AND donnor_id = '" . $report ['donnor_id'] . "'";
		}
		
		$report = $this->db->query ( $sql )->row_array ();
		
		return $report;
	}
	function invalidate_report($report) {
		$this->db->update ( 'pbf_invoices', array (
				'uptodate' => '0' 
		), $report );
	}
	function set_invoice_sent_date($invoice_id) {
		$invoice = $this->db->get_where ( 'pbf_invoices', array (
				'invoice_id' => $invoice_id 
		) )->row_array ();
		
		if (empty ( $invoice )) {
			$msg = 'invoice does not exist';
			echo '<script type="text/javascript">alert("' . $msg . '"); window.location = \'' . site_url ( 'report/send_invoices' ) . '\'; </script>';
		} else {
			if (is_null ( $invoice ['sent_date'] )) {
				$this->db->where ( 'invoice_id', $invoice_id );
				
				$date = date ( "Y-m-d H:i:s", strtotime ( date ( "Y-m-d H:i:s" ) ) );
				$this->db->update ( 'pbf_invoices', array (
						'sent_date' => $date 
				) );
				
				$msg = 'invoice sent on ' . $date;
				echo '<script type="text/javascript">alert("' . $msg . '"); window.location = \'' . site_url ( 'report/send_invoices' ) . '\'; </script>';
			} else {
				$msg = 'invoice already sent on ' . $invoice ['sent_date'];
				echo '<script type="text/javascript">alert("' . $msg . '"); window.location = \'' . site_url ( 'report/send_invoices' ) . '\'; </script>';
			}
		}
	}
	function set_invoice_received_date($invoice_id) {
		$invoice = $this->db->get_where ( 'pbf_invoices', array (
				'invoice_id' => $invoice_id 
		) )->row_array ();
		
		if (empty ( $invoice )) {
			$msg = 'invoice does not exist';
			echo '<script type="text/javascript">alert("' . $msg . '"); window.location = \'' . site_url ( 'report/receive_invoices' ) . '\'; </script>';
		} else {
			if (is_null ( $invoice ['received_date'] )) {
				$this->db->where ( 'invoice_id', $invoice_id );
				$date = date ( "Y-m-d H:i:s", strtotime ( date ( "Y-m-d H:i:s" ) ) );
				
				$this->db->update ( 'pbf_invoices', array (
						'received_date' => $date 
				) );
				$msg = 'invoice received on ' . $date;
				echo '<script type="text/javascript">alert("' . $msg . '"); window.location = \'' . site_url ( 'report/receive_invoices' ) . '\'; </script>';
			} else {
				$msg = 'invoice already received on ' . $invoice ['sent_date'];
				echo '<script type="text/javascript">alert("' . $msg . '"); window.location = \'' . site_url ( 'report/receive_invoices' ) . '\'; </script>';
			}
		}
	}
	function get_states() {
		return $this->db->get ( pbf_workflow )->result_array ();
	}
	function get_workflow_condition($state) {
		$sql = "SELECT condition_tab.state_name FROM pbf_workflow LEFT JOIN pbf_workflow condition_tab ON condition_tab.state_id = pbf_workflow.condition WHERE pbf_workflow.state_name = '" . $state . "'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	
	// Fonction qui retourne tous les types de fichiers qui sont actifs
	function get_all_filetypes() {
		// $sql="SELECT pbf_filetypes.filetype_id, pbf_filetypes.filetype_name FROM pbf_filetypes";
		$sql = "SELECT * FROM pbf_filetypes WHERE filetype_active=1";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_donor_entity_config($donor_id, $year) {
		$sql = "SELECT * FROM pbf_donorsconfig WHERE donor_id='" . $donor_id . "' AND YEAR(pbf_donorsconfig.from)='" . $year . "'";
		return $this->db->query ( $sql )->row_array ();
	}
	function get_donor_config_details($config_id) {
		$sql = "SELECT * FROM pbf_donorsconf_details WHERE donor_conf_id='" . $config_id . "'";
		return $this->db->query ( $sql )->result_array ();
	}
	function entity_donor_details($config_id, $entity_id) {
		$sql = "SELECT * FROM pbf_donorsentity_details WHERE donorconf_id='" . $config_id . "' AND entity_id='" . $entity_id . "'";
		return $this->db->query ( $sql )->result_array ();
	}
	function get_indicator_title($indicator_id) {
		$sql = "SELECT indicator_title FROM pbf_indicators WHERE indicator_id='" . $indicator_id . "'";
		return $this->db->query ( $sql )->row_array ();
	}
	function get_donor_list_config($year) {
		$sql = "SELECT * FROM pbf_donorsconfig WHERE pbf_donorsconfig.from='" . $year . "'";
		return $this->db->query ( $sql )->row_array ();
	}
	function get_master_donor_pay($donor_id, $entity_id) {
		$sql_entity_details = "SELECT pbf_donorsentity_details.entity_id,pbf_donorsentity_details.indicator_id,pbf_donorsconf_details.percentage,pbf_donors.donor_id FROM pbf_donorsentity_details LEFT JOIN pbf_donorsconf_details ON (pbf_donorsentity_details.donorconf_id=pbf_donorsconf_details.conf_details_id)
					LEFT JOIN pbf_donorsconfig ON (pbf_donorsconfig.donorconfig_id=pbf_donorsconf_details.donor_conf_id) LEFT JOIN pbf_donors ON(pbf_donors.donor_id=pbf_donorsconfig.donor_id) WHERE pbf_donorsentity_details.entity_id='" . $entity_id . "'";
		$entity_details = $this->db->query ( $sql_entity_details )->result_array ();
		$entity_det = array ();
		$somme_tot = 0;
		$somme_master_donor = 0;
		if (! empty ( $entity_details )) {
			foreach ( $entity_details as $detail ) {
				if ($detail ['indicator_id'] == 0) {
					$somme_tot = $somme_tot + $detail ['percentage'];
					if ($donor_id == $detail ['donor_id']) {
						$somme_master_donor = $somme_master_donor + $detail ['percentage'];
					}
				}
			}
		}
		$entity_det ['somme_tot'] = $somme_tot;
		$entity_det ['somme_master_donor'] = $somme_master_donor;
		return $entity_det;
	}
	function get_pbf_group_bonus($group_id) {
		$sql = "SELECT entity_group_bonus FROM pbf_entitygroups WHERE entity_group_id=" . $group_id;
		$result = $this->db->query ( $sql )->row_array ();
		return $result ['entity_group_bonus'];
	}
	function get_indicator_categories() {
		$sql = "SELECT category_id,category_title FROM pbf_indicatorcategories";
		
		return $this->db->query ( $sql )->result_array ();
	}
}