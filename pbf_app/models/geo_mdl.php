<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Geo_mdl extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	function get_geo($geo_id) {
		return $this->db->get_where ( 'pbf_geo', array (
				'geo_id' => $geo_id 
		) )->row_array ();
	}
	function get_zones_by_geo($num = 0, $filters) {
		$record_set = array ();
		$sql_append = " WHERE 1=1 ";
		
		if (! empty ( $filters ['class'] )) {
			
			$sql_append .= " AND pbf_geozones.geo_id='" . $filters ['class'] . "'";
		}
		
		$sql = "SELECT * FROM pbf_geozones " . $sql_append;
		
		$record_set ['geo_class_name'] = $this->get_geo ( $filters ['class'] );
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT $num , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function get_zone($geozone_id) {
		return $this->db->get_where ( 'pbf_geozones', array (
				'geozone_id' => $geozone_id 
		) )->row_array ();
	}
	function get_zones_by_parent($parent_id) {
		return $this->db->get_where ( 'pbf_geozones', array (
				'geozone_parentid' => $parent_id,
				'geozone_active' => '1' 
		) )->result_array ();
	}
	function count_child_zones($zone_id = '') {
		if ($zone_id == '') {
			$append = " AND geozone_parentid IS NULL";
		} else {
			$append = " AND geozone_parentid ='" . $zone_id . "' ";
		}
		
		$sql = "SELECT COUNT(*) nbZones FROM pbf_geozones WHERE 1=1 AND geozone_active = 1 " . $append;
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_parent_geozone($child_id) {
		$sql = "SELECT parent.* FROM pbf_geozones child left join pbf_geozones parent on child.geozone_parentid= parent.geozone_id where child.geozone_id = " . $child_id;
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_regions() {
		return $this->db->get_where ( 'pbf_geozones', array (
				'geozone_parentid' => NULL,
				'geozone_active' => '1' 
		) )->result_array ();
	}
	function save_geo($geo) {
		if ($geo ['geo_active'] == 1) {
			$this->db->simple_query ( "UPDATE pbf_geo SET geo_active='0'" );
		}
		
		$geo_id = $geo ['geo_id'];
		
		if (empty ( $geo ['geo_id'] )) {
			$this->db->insert ( 'pbf_geo', $geo );
			$geo_id = $this->db->insert_id ();
		} else {
			$this->db->update ( 'pbf_geo', $geo, array (
					'geo_id' => $geo_id 
			) );
		}
		
		$this->pbf->set_translation ( array (
				'text' => $geo ['geo_title'],
				'text_key' => 'geo_key_' . $geo_id 
		), 'geo' );
		
		return $geo_id;
	}
	function save_zone($zone) {
		if (! empty ( $zone ['geozone_id'] )) {
			return $this->db->update ( 'pbf_geozones', $zone, array (
					'geozone_id' => $zone ['geozone_id'] 
			) );
		} else {
			return $this->db->insert ( 'pbf_geozones', $zone );
		}
	}
	function set_geo_state($geo_id, $state) {
		$this->db->simple_query ( "UPDATE pbf_geo SET geo_active='0'" );
		return $this->db->simple_query ( "UPDATE pbf_geo SET geo_active='" . $state . "' WHERE geo_id='" . $geo_id . "'" );
	}
	function set_geo_zone_state($geozone_id, $state) {
		return $this->db->simple_query ( "UPDATE pbf_geozones SET geozone_active='" . $state . "' WHERE geozone_id='" . $geozone_id . "'" );
	}
	function del_geo($geo_id) {
		$this->pbf->set_translation ( array (
				'text' => NULL,
				'text_key' => 'geo_key_' . $geo_id 
		), 'geo' );
		
		$this->db->simple_query ( "DELETE FROM pbf_geozones WHERE geo_id ='" . $geo_id . "'" ); 
		return $this->db->delete ( 'pbf_geo', array (
				'geo_id' => $geo_id 
		) );
	}
	function del_geo_zone($geozone_id) {
		return $this->db->delete ( 'pbf_geozones', array (
				'geozone_id' => $geozone_id 
		) );
	}
	function get_geozone_perparent() {
		$sql = "SELECT pbf_geozones.geozone_id,pbf_geozones.geozone_name FROM pbf_geozones WHERE pbf_geozones.geozone_active='1' AND pbf_geozones.geozone_parentid IN (select pbfgezone)";
		
		$sql = "select F.geozone_id as F_geozoneId,F.geozone_name as F_geozoneName,P.geozone_id as P_geozoneId,P.geozone_name AS P_geozoneName from pbf_geozones F , pbf_geozones P where F.geozone_parentid=P.geozone_id AND F.geozone_active=1  AND P.geozone_active=1 ORDER BY P.geozone_name ASC , F.geozone_name ASC";
		
		return $this->db->query ( $sql )->result_array ();
	}
}