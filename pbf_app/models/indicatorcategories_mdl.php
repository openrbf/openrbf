<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Indicatorcategories_mdl extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	function get_indicatorcategories($num = 0, $filters, $lang) {
		$record_set = array ();
		
		$sql_append = " WHERE 1=1";
		
		$sql = "SELECT * FROM pbf_indicatorcategories ORDER BY category_order ASC";
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT $num , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function save_indicator_category($indicator_category) {
		if (empty ( $indicator_category ['category_id'] )) {
			
			$this->db->insert ( 'pbf_indicatorcategories', $indicator_category );
			$indicator_id = $this->db->insert_id ();
		} else {
			$category_id = $indicator_category ['category_id'];
			$this->db->update ( 'pbf_indicatorcategories', $indicator_category, array (
					'category_id' => $category_id 
			) );
		}
		
		if ($this->db->affected_rows () > 0) {
			return true;
		} else {
			return false;
		}
	}
	function get_indicator_category($category_id) {
		$sql = "SELECT * FROM pbf_indicatorcategories WHERE category_id='" . $category_id . "' LIMIT 1";
		$indicator_category = $this->db->query ( $sql )->row_array ();
		return $indicator_category;
	}
	function del_indicatorcategory($category_id) {
		return $this->db->delete ( 'pbf_indicatorcategories', array (
				'category_id' => $category_id 
		) );
	}
}