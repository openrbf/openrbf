<?php

if (! ('BASEPATH'))
	exit ( 'No direct script access allowed' );
class Donneurs_mdl extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	
	// =============================================================================================================================================
	
	// ================================================Donors=========================================================================================
	public function get_donors($num = 0, $filters) {
		$record_set = array ();
		
		$sql_append = " WHERE 1=1";
		
		if (! empty ( $filters ['name'] )) {
			
			$sql_append .= " AND (pbf_donors.donor_name LIKE '%" . trim ( $filters ['name'] ) . "%'";
		}
		
		$sql = "SELECT * FROM pbf_donors " . $sql_append . "  ORDER BY pbf_donors.donor_priority ASC";
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT " . $num . " , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	public function move_up($id_donor = null) {
		$sql_current = "SELECT * FROM pbf_donors WHERE donor_id='" . $id_donor . "'";
		$current_record_set = $this->db->query ( $sql_current )->row_array ();
		$current_priority = $current_record_set ['donor_priority'];
		if ($current_record_set ['donor_priority'] > 1) {
			$nex_priority = $current_record_set ['donor_priority'] - 1;
			$sql_next = "SELECT * FROM pbf_donors WHERE donor_priority='" . $nex_priority . "'";
			$next_record_set = $this->db->query ( $sql_next )->row_array ();
			$next_record_set ['donor_priority'] = $current_priority;
			$current_record_set ['donor_priority'] = $nex_priority;
			
			$this->db->where ( 'donor_id', $current_record_set ['donor_id'] );
			$this->db->update ( 'pbf_donors', $current_record_set );
			$this->db->where ( 'donor_id', $next_record_set ['donor_id'] );
			$this->db->update ( 'pbf_donors', $next_record_set );
		}
	}
	public function retrieve_priority() {
		$sql = "SELECT * FROM pbf_donors ORDER BY donor_priority ASC";
		$record_set = $this->db->query ( $sql )->result_array ();
		$priority = 1;
		foreach ( $record_set as $record ) {
			$record ['donor_priority'] = $priority;
			$this->db->where ( 'donor_id', $record ['donor_id'] );
			$this->db->update ( 'pbf_donors', $record );
			$priority ++;
		}
	}
	function save_donors($donor) {
		if (empty ( $donor ['donor_id'] )) {
			
			return $this->db->insert ( 'pbf_donors', $donor );
		} else {
			
			return $this->db->update ( 'pbf_donors', $donor, array (
					'donor_id' => $donor ['donor_id'] 
			) );
		}
	}
	function get_donor($id_donor = null) {
		$record_set = array ();
		
		$sql = "SELECT * FROM pbf_donors WHERE donor_id='" . $id_donor . "' LIMIT 1";
		
		$record_set = $this->db->query ( $sql )->row_array ();
		
		return $record_set;
	}
	function set_priority() {
		$record_count = $this->db->count_all_results ( 'pbf_donors' );
		if ($record_count == 0) {
			return 1;
		} else {
			$this->db->select_max ( 'donor_priority' );
			$query = $this->db->get ( 'pbf_donors' );
			$priority = $query->row_array ();
			return $priority ['donor_priority'] + 1;
		}
	}
	function get_maxid() {
		$this->db->select_max ( 'donor_id' );
		$query = $this->db->get ( 'pbf_donors' );
		$maxid = $query->row_array ();
		return $maxid ['donor_id'] + 1;
	}
	function delete($donor_id) {
		$sql = "SELECT donorconfig_id FROM pbf_donorsconfig WHERE donor_id='" . $donor_id . "'";
		$record_set = $this->db->query ( $sql )->result_array ();
		if (! empty ( $record_set )) {
			foreach ( $record_set as $record ) {
				print_r ( $record );
				$this->delete_config ( $record ['donorconfig_id'] );
			}
		}
		return $this->db->delete ( 'pbf_donors', array (
				'donor_id' => $donor_id 
		) );
	}
	// ===============================================================================================================================================
	
	// ====================================================Donors config=============================================================================
	function save_donorconfig($donorconfig) {
		if (empty ( $donorconfig ['donorconfig_id'] )) {
			
			return $this->db->insert ( 'pbf_donorsconfig', $donorconfig );
		} else {
			
			return $this->db->update ( 'pbf_donorsconfig', $donorconfig, array (
					'donorconfig_id' => $donorconfig ['donorconfig_id'] 
			) );
		}
	}
	function save_donorconfig_details($donorconfig_details) {
		return $this->db->insert ( 'pbf_donorsconf_details', $donorconfig_details );
	}
	function save_details_indic($donor_indic_details) {
		return $this->db->insert ( 'pbf_donorsconfig', $donor_indic_details );
	}
	function save_donor_indic_details($donorconfig_details) {
		return $this->db->insert ( 'pbf_donorsentity_details', $donorconfig_details );
	}
	function delete_config($donorconfig_id) {
		$sql = "SELECT conf_details_id FROM pbf_donorsconf_details WHERE donor_conf_id='" . $donorconfig_id . "'";
		$details_list = $this->db->query ( $sql )->result_array ();
		
		if (! empty ( $details_list )) {
			$list_conf = array ();
			foreach ( $details_list as $list ) {
				$list_conf [] = $list ['conf_details_id'];
			}
			
			$sql_det_entit = "DELETE FROM pbf_donorsentity_details WHERE donorconf_id IN (" . implode ( ',', $list_conf ) . ")";
			if ($this->db->query ( $sql_det_entit )) {
				$sql_conf_det = "DELETE FROM pbf_donorsconf_details WHERE conf_details_id IN (" . implode ( ',', $list_conf ) . ")";
				if ($this->db->query ( $sql_conf_det )) {
					return $this->db->delete ( 'pbf_donorsconfig', array (
							'donorconfig_id' => $donorconfig_id 
					) );
				}
			}
		}
	}
	function get_donors_conf($num = 0, $filters) {
		$record_set = array ();
		
		$sql_append = " WHERE 1=1";
		if (! empty ( $filters ['donor_name'] )) {
			
			$sql_append .= " AND pbf_donors.donor_name LIKE '%" . $filters ['donor_name'] . "%'";
			$sql_join = " LEFT JOIN pbf_donors ON (pbf_donors.donor_id=pbf_donorsconfig.donor_id)";
			$name = ",pbf_donors.donor_name";
		}
		
		if (! empty ( $filters ['date'] )) {
			$sql_append .= " AND ( " . $filters ['date'] . " BETWEEN pbf_donorsconfig.from AND  pbf_donorsconfig.to)";
		}
		
		$sql = "SELECT pbf_donorsconfig.*" . $name . " FROM pbf_donorsconfig" . $sql_join . $sql_append . "  ORDER BY pbf_donorsconfig.to DESC";
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT " . $num . " , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function get_donorconfig($idconfig = null) {
		$record_set = array ();
		
		$sql = "SELECT * FROM pbf_donorsconfig WHERE donorconfig_id='" . $idconfig . "'";
		
		$record_set = $this->db->query ( $sql )->row_array ();
		
		return $record_set;
	}
	function get_donorconfig_details($idconfig = null) {
		$record_set = array ();
		
		$sql = "SELECT * FROM pbf_donorsconf_details WHERE donor_conf_id='" . $idconfig . "' ORDER BY conf_details_id ASC";
		
		$record_set = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function get_indic_details($id_conf_det = null) {
		$record_set = array ();
		
		$sql = "SELECT * FROM pbf_donors_indic WHERE detail_conf_id='" . $id_conf_det . "' ORDER BY detail_conf_id ASC";
		
		$record_set = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	
	// =====================================================================================================================================================
}