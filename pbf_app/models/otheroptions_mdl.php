<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Otheroptions_mdl extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	function get_computation_methodes($num = 0, $filters) {
		$record_set = array ();
		
		$sql_append = " WHERE 1=1";
		
		$sql = "SELECT computation_id,computation_description,computation_date_start,computation_date_end FROM pbf_computation " . $sql_append;
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT $num , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function save_method($post_vars, $post_details_vars) {
		$computation_id = $post_vars ['computation_id'];
		
		$this->db->delete ( 'pbf_computationdetails', array (
				'computation_id' => $computation_id 
		) );
		
		if (empty ( $post_vars ['computation_id'] )) {
			
			$this->db->insert ( 'pbf_computation', $post_vars );
			
			$computation_id = $this->db->insert_id ();
		} else {
			
			$this->db->update ( 'pbf_computation', $post_vars, array (
					'computation_id' => $computation_id 
			) );
		}
		
		foreach ( $post_details_vars ['computation_entity_class_id'] as $key => $val ) {
			
			if (! empty ( $post_details_vars ['computation_entity_class_id'] [$key] )) {
				$obj ['computationdetail_id'] = '';
				$obj ['computation_id'] = $computation_id;
				$obj ['computation_entity_class_id'] = $post_details_vars ['computation_entity_class_id'] [$key];
				$obj ['computation_entity_type_id'] = $post_details_vars ['computation_entity_type_id'] [$key];
				$obj ['computation_entity_group_id'] = $post_details_vars ['computation_entity_group_id'] [$key];
				$obj ['computation_entity_ass_group_id'] = $post_details_vars ['computation_entity_ass_group_id'] [$key];
				$obj ['computation_geozone_id'] = $post_details_vars ['computation_geozone_id'] [$key];
				$obj ['computation_main_logic'] = $post_details_vars ['computation_main_logic'] [$key];
				$obj ['computation_calculation_basis'] = $post_details_vars ['computation_calculation_basis'] [$key];
				$obj ['computation_score_condition_one'] = $post_details_vars ['computation_score_condition_one'] [$key];
				$obj ['computation_score_fact_one'] = $post_details_vars ['computation_score_fact_one'] [$key];
				$obj ['computation_score_condition_two'] = $post_details_vars ['computation_score_condition_two'] [$key];
				$obj ['computation_score_fact_two'] = $post_details_vars ['computation_score_fact_two'] [$key];
				$obj ['fav_action'] = $post_details_vars ['fav_action'] [$key];
				$obj ['consider_score'] = $post_details_vars ['consider_score'] [$key];
				
				$this->db->insert ( 'pbf_computationdetails', $obj );
			}
		}
		
		if ($this->db->affected_rows () > 0) {
			return true;
		} else {
			return false;
		}
	}
	function get_method($computation_id) {
		$method ['method'] = $this->db->get_where ( 'pbf_computation', array (
				'computation_id' => $computation_id 
		) )->row_array ();
		
		$method ['methoddetails'] = $this->db->get_where ( 'pbf_computationdetails', array (
				'computation_id' => $computation_id 
		) )->result_array ();
		
		return $method;
	}
	function get_lookups($num = 0, $filters) {
		$sql = "SELECT * FROM pbf_lookups ORDER BY lookup_linkfile, lookup_order";
		
		$lookups ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT $num , " . $this->config->item ( 'rec_per_page' );
		
		$lookups ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $lookups;
	}
	function get_lkps_links() {
		$sql = "SELECT DISTINCT(lookup_linkfile) FROM pbf_lookups ORDER BY lookup_linkfile";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_banks() {
		$sql = "SELECT pbf_banks.bank_id,IF(pbf_banks.bank_parent_id IS NULL,CONCAT('<b>',pbf_banks.bank_name,' (',pbf_banks.bank_name_abbrev,')</b>'),CONCAT('&nbsp;&nbsp;&nbsp;&nbsp;- ',pbf_banks.bank_name)) AS bank_name,IF(pbf_banks.bank_parent_id IS NULL,pbf_banks.bank_id,pbf_banks.bank_parent_id) AS bank_parent_id,IF(pbf_banks.bank_parent_id IS NULL,pbf_banks.bank_id,CONCAT(pbf_banks.bank_parent_id,pbf_banks.bank_id)) AS sortorder FROM pbf_banks LEFT JOIN pbf_banks pbf_parent_banks ON (pbf_parent_banks.bank_id=pbf_banks.bank_parent_id) ORDER BY bank_parent_id,sortorder";
		
		$banks ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$banks ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $banks;
	}
	function get_parent_banks() {
		$sql = "SELECT * FROM pbf_banks WHERE bank_parent_id IS NULL ORDER BY pbf_banks.bank_name ASC";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_bank($bank_id) {
		return $this->db->get_where ( 'pbf_banks', array (
				'bank_id' => $bank_id 
		) )->row_array ();
	}
	function get_lookup($lookup_id) {
		return $this->db->get_where ( 'pbf_lookups', array (
				'lookup_id' => $lookup_id 
		) )->row_array ();
	}
	function save_lookup($lookup) {
		$lookup_id = $lookup ['lookup_id'];
		
		if (empty ( $lookup ['lookup_id'] )) {
			
			$this->db->insert ( 'pbf_lookups', $lookup );
			
			$lookup_id = $this->db->insert_id ();
		} else {
			
			$this->db->update ( 'pbf_lookups', $lookup, array (
					'lookup_id' => $lookup_id 
			) );
		}
		
		$this->pbf->set_translation ( array (
				array (
						'text' => $lookup ['lookup_title'],
						'text_key' => 'option_lkp_ky_' . $lookup_id 
				),
				array (
						'text' => $lookup ['lookup_title_abbrev'],
						'text_key' => 'option_lkp_abbr_ky_' . $lookup_id 
				) 
		), 'otheroptions' );
		
		return $lookup_id;
	}
	function save_bank($bank) {
		$bank_id = $bank ['bank_id'];
		
	
		
		if (empty ( $bank ['bank_id'] )) {
			
			return $this->db->insert ( 'pbf_banks', $bank );
		} else {
			
			return $this->db->update ( 'pbf_banks', $bank, array (
					'bank_id' => $bank_id 
			) );
		}
	}
	function del_bank($bank_id) {
		return $this->db->delete ( 'pbf_banks', array (
				'bank_id' => $bank_id 
		) );
	}
	function del_method($computation_id) {
		$this->db->delete ( 'pbf_computation', array (
				'computation_id' => $computation_id 
		) );
		return ( bool ) $this->db->delete ( 'pbf_computationdetails', array (
				'computation_id' => $computation_id 
		) );
	}
	function get_db_size() {
		$sql = "SELECT SUM(round(((data_length + index_length) / 1024 / 1024),2)) AS 'db_size' FROM information_schema.TABLES WHERE table_schema = '" . $this->db->database . "'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function del_lookup($lookup_id) {
		$this->pbf->set_translation ( array (
				array (
						'text' => NULL,
						'text_key' => 'option_lkp_ky_' . $lookup_id 
				),
				array (
						'text' => NULL,
						'text_key' => 'option_lkp_abbr_ky_' . $lookup_id 
				) 
		), 'otheroptions' );
		
		return $this->db->delete ( 'pbf_lookups', array (
				'lookup_id' => $lookup_id 
		) );
	}
}