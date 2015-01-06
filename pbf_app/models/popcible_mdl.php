<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Popcible_mdl extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	function get_popcible($num = 0, $filters) {
		$record_set = array ();
		
		$sql_append = " WHERE 1=1";
		
		$sql = "SELECT pbf_popcible.popcible_id,pbf_popcible.popcible_name, pbf_popcible.popcible_percentage, pbf_popcible.popcible_published FROM pbf_popcible " . $sql_append;
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT $num , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		return $record_set;
	}
	function get_popcible_line_zone($popcible_id, $zone) {
		if (! empty ( $zone )) {
			$sql = "SELECT pbf_popcible.popcible_id,pbf_popcible.popcible_name, pbf_popciblezones.popcible_percentage, pbf_popcible.popcible_published FROM pbf_popcible LEFT JOIN pbf_popciblezones ON pbf_popciblezones.popcible_id = pbf_popcible.popCible_id WHERE pbf_popciblezones.zone_id ='" . $zone . "'";
		} else {
			
			$sql = "SELECT pbf_popcible.popcible_id,pbf_popcible.popcible_name, pbf_popcible.popcible_percentage, pbf_popcible.popcible_published FROM pbf_popcible";
		}
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_popcible_line($popcible_id) {
		return $this->db->get_where ( 'pbf_popcible', array (
				'popcible_id' => $popcible_id 
		) )->row_array ();
	}
	function del_popcible($popcible_id) {
		return $this->db->delete ( 'pbf_popcible', array (
				'popcible_id' => $popcible_id 
		) );
	}
	function save_popcible($popcible, $zones) {
		if (empty ( $popcible ['popcible'] ['popcible_id'] )) {
			
			$this->db->insert ( 'pbf_popcible', $popcible ['popcible'] );
			$popcible ['popcible'] ['popcible_id'] = $this->db->insert_id ();
		} else {
			$this->db->update ( 'pbf_popcible', $popcible ['popcible'], array (
					'popcible_id' => $popcible ['popcible'] ['popcible_id'] 
			) );
		}
		$this->pbf->set_translation ( array (
				'text' => $popcible ['popcible'] ['popcible_name'],
				'text_key' => 'dataelmt_key_' . $popcible ['popcible'] ['popcible_id'] 
		), 'popcible' );
		
		foreach ( $zones as $zone ) {
			
			if (($popcible ['popciblezone'] ['pop_cible_percentage_' . $zone ['geozone_id']] == '') || empty ( $popcible ['popciblezone'] ['pop_cible_percentage_' . $zone ['geozone_id']] )) {
				$perc = $popcible ['popcible'] ['popcible_percentage'];
			} else {
				$perc = $popcible ['popciblezone'] ['pop_cible_percentage_' . $zone ['geozone_id']];
			}
			
			if (empty ( $popcible ['popciblezone'] ['popciblezone_id_' . $zone ['geozone_id']] )) {
				
				$this->db->insert ( 'pbf_popciblezones', array (
						'zone_id' => $popcible ['popciblezone'] ['zone_id_' . $zone ['geozone_id']],
						'popcible_id' => $popcible ['popcible'] ['popcible_id'],
						'popcible_id' => $popcible ['popcible'] ['popcible_id'],
						'popcible_percentage' => $perc 
				) );
			} else {
				$this->db->update ( 'pbf_popciblezones', array (
						'popciblezone_id' => $popcible ['popciblezone'] ['popciblezone_id_' . $zone ['geozone_id']],
						'zone_id' => $popcible ['popciblezone'] ['zone_id_' . $zone ['geozone_id']],
						'popcible_id' => $popcible ['popcible'] ['popcible_id'],
						'popcible_percentage' => $perc 
				), array (
						'popciblezone_id' => $popcible ['popciblezone'] ['popciblezone_id_' . $zone ['geozone_id']] 
				) );
			}
		}
		
		if ($this->db->affected_rows () > 0) {
			return true;
		} else {
			return false;
		}
	}
	function setpublish($popcible_id, $state) {
		$sql = "UPDATE pbf_popcible SET popcible_published='" . $state . "' WHERE popcible_id='" . $popcible_id . "'";
		
		return $this->db->simple_query ( $sql );
	}
	function get_zones() {
		$sql = "select geozone_id,geozone_name from pbf_geozones where geozone_parentid IS NULL AND geozone_active=1";
		return $this->db->query ( $sql )->result_array ();
	}
	function get_popciblezone($popcible_id) {
		$sql = "select popciblezone_id, zone_id, popcible_percentage from pbf_popciblezones where popcible_id= '" . $popcible_id . "'";
		return $this->db->query ( $sql )->result_array ();
	}
}