<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Helpers_mdl extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	
	// =============================================================================================================================================
	function get_helpers($num = 0, $filters) {
		$record_set = array ();
		
		$sql_append = " WHERE 1=1";
		
		if (! empty ( $filters ['title'] )) {
			
			$sql_append .= " AND (pbf_helpers.helper_name LIKE '%" . trim ( $filters ['name'] ) . "%'";
		}
		
		$sql = "SELECT * FROM pbf_helpers " . $sql_append . "  ORDER BY pbf_helpers.helper_position,pbf_helpers.helper_order ASC";
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT " . $num . " , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function get_helper($helper_id) {
		$sql = "SELECT * FROM pbf_helpers WHERE helper_id='" . $helper_id . "' LIMIT 1";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function save_helper($helpers) {
		if (empty ( $helpers ['helper_id'] )) {
			
			return $this->db->insert ( 'pbf_helpers', $helpers );
		} else {
			
			return $this->db->update ( 'pbf_helpers', $helpers, array (
					'helper_id' => $helpers ['helper_id'] 
			) );
		}
	}
	function del_helper($helper_id) {
		return $this->db->delete ( 'pbf_helpers', array (
				'helper_id' => $helper_id 
		) );
	}
	// ===============================================================================================================================================
}