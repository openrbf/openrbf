<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Frontdata_mdl extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	function get_available_data($num = 0, $filters) { // publication
		$sql = "SELECT pbf_frontdata.frontdata_id,pbf_datafile.datafile_quarter,pbf_datafile.datafile_year,SUBSTR(pbf_frontdata.date_created,1,10) AS date_created,pbf_users.user_fullname,IF(pbf_frontdata.frontdata_id IS NULL,0,1) AS data_published FROM pbf_datafile LEFT JOIN pbf_frontdata ON (pbf_frontdata.data_quarter=pbf_datafile.datafile_quarter AND pbf_frontdata.data_year=pbf_datafile.datafile_year) LEFT JOIN pbf_users ON (pbf_users.user_id=pbf_frontdata.data_author) GROUP BY pbf_datafile.datafile_quarter,pbf_datafile.datafile_year ORDER BY pbf_datafile.datafile_year DESC,pbf_datafile.datafile_quarter DESC";
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function get_available_data_validation($num = 0, $filters) {
		$sql = "SELECT pbf_validation.validation_id,pbf_datafile.datafile_quarter,pbf_datafile.datafile_year,SUBSTR(pbf_validation.date_created,1,10) AS date_created,pbf_users.user_fullname,IF(pbf_validation.validation_id IS NULL,0,1) AS data_validated FROM pbf_datafile LEFT JOIN pbf_validation ON (pbf_validation.data_quarter=pbf_datafile.datafile_quarter AND pbf_validation.data_year=pbf_datafile.datafile_year) LEFT JOIN pbf_users ON (pbf_users.user_id=pbf_validation.data_author) GROUP BY pbf_datafile.datafile_quarter,pbf_datafile.datafile_year ORDER BY pbf_datafile.datafile_year DESC,pbf_datafile.datafile_quarter DESC";
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		// $sql .= " LIMIT $num , ".$this->config->item('rec_per_page');
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function set_data_state($pbf_frontdata) {
		
		// first unpublish all that we have in the scope
		$entities = $this->pbf->get_entities ( $lookup = false, $lkp_class = true, $class = '' );
		
		foreach ( $entities as $entity ) {
			
			$sql = "DELETE fd.* FROM pbf_frontdatadetails fd JOIN pbf_frontdata f ON fd.frontdata_id = f.frontdata_id WHERE fd.entity_id = '" . $entity ['entity_id'] . "' AND f.data_quarter = '" . $pbf_frontdata ['datafile_quarter'] . "' AND f.data_year = '" . $pbf_frontdata ['datafile_year'] . "'";
			
			$this->db->simple_query ( $sql );
			
			$sql = "DELETE fd.* FROM pbf_validationdetails fd JOIN pbf_validation f ON fd.validation_id = f.validation_id WHERE fd.entity_id = '" . $entity ['entity_id'] . "' AND f.data_quarter = '" . $pbf_frontdata ['datafile_quarter'] . "' AND f.data_year = '" . $pbf_frontdata ['datafile_year'] . "'";
			
			$this->db->simple_query ( $sql );
			
			$this->db->update ( 'pbf_datafile', array (
					'datafile_status' => 0,
					'datafile_valid_nat' => 0,
					'datafile_valid_reg' => 0 
			), array (
					'entity_id' => $entity ['entity_id'],
					'datafile_quarter' => $pbf_frontdata ['datafile_quarter'],
					'datafile_year' => $pbf_frontdata ['datafile_year'] 
			) );
		}
		
		// clean workplace...
		
		$this->db->simple_query ( "DELETE FROM pbf_frontdata WHERE frontdata_id NOT IN (SELECT DISTINCT(frontdata_id) FROM pbf_frontdatadetails)" );
		
		// check how to unpublish
		$this->db->simple_query ( "DELETE FROM pbf_validation WHERE validation_id NOT IN (SELECT DISTINCT(validation_id) FROM pbf_validationdetails)" );
		
		// end unpublishing
		
		if (! empty ( $pbf_frontdata ['published_id'] )) {
			
			$this->load->model ( array (
					'report_mdl',
					'entities_mdl',
					'invoices_mdl'
			) );
			
			$raw_quarterly_info = $this->report_mdl->get_quarterly_consolidated_info_all ( $pbf_frontdata );
			unset ( $pbf_frontdata ['published_id'] );
			
			$this->db->insert ( 'pbf_frontdata', array (
					'data_quarter' => $pbf_frontdata ['datafile_quarter'],
					'data_month' => ($pbf_frontdata ['datafile_quarter'] * 3),
					'data_year' => $pbf_frontdata ['datafile_year'],
					'data_author' => $pbf_frontdata ['data_author'],
					'data_comment' => $pbf_frontdata ['data_comment'] 
			) );
			
			$frontdata_id = $this->db->insert_id ();
			
			$quarter = $pbf_frontdata ['datafile_quarter'];
			$year = $pbf_frontdata ['datafile_year'];
			
			
			if (!empty($pbf_frontdata['validation_reg_id'])){
				$entities_published = $this->entities_mdl->get_entities_topublish($pbf_frontdata['validation_reg_id']); 
				
				foreach ($entities_published as $entity_published) {
					$data_to_publish = $this->invoices_mdl->get_data_topublish($pbf_frontdata,$entity_published['entity_id']);
					
					if (!empty($data_to_publish)){
							
							
						$this->db->insert ( 'pbf_frontdatadetails', array (
							'frontdata_id' => $frontdata_id,
							'entity_id' => $data_to_publish ['entity_id'],
							'amount_subsidies' => empty($data_to_publish['total_invoice'])?0:$data_to_publish['total_invoice'],
							'amount_bonus' => empty($data_to_publish['quality_bonus'])?0:$data_to_publish['quality_bonus'],
							'amount_total' => empty($data_to_publish['total_invoice'])?0:$data_to_publish['total_invoice'] 
				        ) );
				
								
						$this->db->update ( 'pbf_datafile', array (
							'datafile_status' => '1' ), array (
							'datafile_quarter' => $pbf_frontdata ['datafile_quarter'],
							'datafile_year' => $pbf_frontdata ['datafile_year'],
							'entity_id' => $data_to_publish ['entity_id'] 
				        ) );
					}
				}
			}
		}
		
		// save validation national
		if (! empty ( $pbf_frontdata ['validation_id'] )) {
			
			$this->load->model ( array (
					'report_mdl',
					'entities_mdl' 
			) );
			
			$raw_quarterly_info = $this->report_mdl->get_quarterly_validation_info_all ( $pbf_frontdata );
			
			unset ( $pbf_frontdata ['validation_id'] );
			
			$this->db->insert ( 'pbf_validation', array (
					'data_quarter' => $pbf_frontdata ['datafile_quarter'],
					'data_month' => ($pbf_frontdata ['datafile_quarter'] * 3),
					'data_year' => $pbf_frontdata ['datafile_year'],
					'data_author' => $pbf_frontdata ['data_author'],
					'date_created' => $pbf_frontdata ['date_created'],
					'data_comment' => $pbf_frontdata ['data_comment'] 
			) );
			$validation_id = $this->db->insert_id ();
			
			foreach ( $raw_quarterly_info as $r_key => $r_val ) {
				
				$this->db->insert ( 'pbf_validationdetails', array (
						'validation_id' => $validation_id,
						'entity_id' => $r_val ['entity_id'] 
				) );
				
				// !! for multi-region, check region
				
				$this->db->update ( 'pbf_datafile', array (
						'datafile_valid_nat' => '1' 
				), array (
						'datafile_quarter' => $pbf_frontdata ['datafile_quarter'],
						'datafile_year' => $pbf_frontdata ['datafile_year'],
						'entity_id' => $r_val ['entity_id'] 
				) );
			}
		}
		
		// save validation regional
		
		if (! empty ( $pbf_frontdata ['validation_reg_id'] )) {
			
			$this->load->model ( array (
					'report_mdl',
					'entities_mdl' 
			) );
			
			$raw_quarterly_info = $this->report_mdl->get_quarterly_validation_reg_info_all ( $pbf_frontdata );
			
			foreach ( $raw_quarterly_info as $r_key => $r_val ) {
				
				$this->db->update ( 'pbf_datafile', array (
						'datafile_valid_reg' => '1' 
				), array (
						'datafile_quarter' => $pbf_frontdata ['datafile_quarter'],
						'datafile_year' => $pbf_frontdata ['datafile_year'],
						'entity_id' => $r_val ['entity_id'] 
				) );
			}
		}
		
		return true; // check to be done..
	}
	function get_publication_status($quarter, $year) {
		$usergeozones = $this->session->userdata ( 'usergeozones' );
		
		$sql_append = '';
		
		if (! empty ( $usergeozones )) {
			
			$sql_append .= " AND pbf_geozones.geozone_id IN (" . implode ( ',', $usergeozones ) . ") ";
		}
		
		$sql = "SELECT pbf_geozones.geozone_id,published.geozone_id AS published_id,concat(pbf_geozones.geozone_name,' (',parent.geozone_name,')') AS geozone_name,published.data_comment
FROM pbf_geozones LEFT JOIN pbf_geozones parent ON (parent.geozone_id=pbf_geozones.geozone_parentid) LEFT JOIN (SELECT DISTINCT(pbf_geozones.geozone_id) AS geozone_id,pbf_frontdata.data_comment FROM pbf_frontdatadetails LEFT JOIN pbf_frontdata ON (pbf_frontdata.frontdata_id = pbf_frontdatadetails.frontdata_id) LEFT JOIN pbf_entities ON (pbf_entities.entity_id = pbf_frontdatadetails.entity_id) LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id = pbf_entities.entity_geozone_id) WHERE pbf_frontdata.data_quarter = '" . $quarter . "' AND pbf_frontdata.data_year = '" . $year . "') AS published ON (published.geozone_id = pbf_geozones.geozone_id) LEFT JOIN pbf_geo ON (pbf_geo.geo_id = pbf_geozones.geo_id) WHERE pbf_geo.geo_active = '1' AND pbf_geozones.geozone_active = '1' " . $sql_append . "  ORDER BY parent.geozone_name, pbf_geozones.geozone_name";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_validation_status($quarter, $year) {
		$usergeozones = $this->session->userdata ( 'usergeozones' );
		
		$sql_append = '';
		
		if (! empty ( $usergeozones )) {
			
			$sql_append .= " AND pbf_geozones.geozone_id IN (" . implode ( ',', $usergeozones ) . ") ";
		}
		
		$sql = "SELECT DISTINCT pbf_geozones.geozone_id, pbf_geozones.geozone_name, pbf_datafile.datafile_valid_nat FROM pbf_geozones  LEFT JOIN pbf_entities ON pbf_entities.entity_geozone_id = pbf_geozones.geozone_id LEFT JOIN pbf_datafile ON pbf_datafile.entity_id = pbf_entities.entity_id LEFT JOIN pbf_filetypesentities ON pbf_filetypesentities.filetype_id = pbf_datafile.filetype_id LEFT JOIN pbf_geozones parent ON (parent.geozone_id=pbf_geozones.geozone_parentid)  WHERE pbf_geozones.geozone_active = '1' AND pbf_entities.entity_active = '1' AND pbf_geozones.geozone_active = '1' AND pbf_filetypesentities.entity_class_id ='1' AND pbf_datafile.datafile_quarter = '" . $quarter . "'  AND pbf_datafile.filetype_id <>8 AND pbf_datafile.datafile_year = '" . $year . "'" . $sql_append . " ORDER BY parent.geozone_name, pbf_geozones.geozone_name";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_validation_reg_status($quarter, $year) {
		$usergeozones = $this->session->userdata ( 'usergeozones' );
		
		$sql_append = '';
		
		if (! empty ( $usergeozones )) {
			$sql_append .= " AND pbf_geozones.geozone_id IN (" . implode ( ',', $usergeozones ) . ") ";
		}
		
		$sql = "SELECT DISTINCT pbf_geozones.geozone_id FROM pbf_geozones  LEFT JOIN pbf_entities ON pbf_entities.entity_geozone_id = pbf_geozones.geozone_id LEFT JOIN pbf_datafile ON pbf_datafile.entity_id = pbf_entities.entity_id WHERE pbf_geozones.geozone_active = '1' AND pbf_geozones.geozone_active = '1' AND pbf_datafile.datafile_valid_reg !='1' AND pbf_datafile.datafile_quarter = '" . $quarter . "' AND pbf_datafile.filetype_id <>8 AND pbf_datafile.datafile_year = '" . $year . "'" . $sql_append . " ORDER BY pbf_geozones.geozone_name";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function check_all_valid_reg($geozone_id, $quarter, $year) {
		$sql = "SELECT COUNT(DISTINCT pbf_datafile.datafile_id) as nb FROM pbf_datafile LEFT JOIN pbf_entities ON (pbf_entities.entity_id=pbf_datafile.entity_id) LEFT JOIN pbf_filetypesentities ON pbf_filetypesentities.filetype_id = pbf_datafile.filetype_id LEFT JOIN pbf_filetypes ON pbf_filetypes.filetype_id = pbf_datafile.filetype_id LEFT JOIN pbf_entitiestime ON ((pbf_entitiestime.entity_id = pbf_entities.entity_id) AND (LAST_DAY('" . $year . "-" . ($quarter * 3) . "-1') BETWEEN pbf_entitiestime.use_from AND pbf_entitiestime.use_to)) WHERE pbf_entities.entity_geozone_id ='" . $geozone_id . "' AND IF(pbf_entitiestime.entity_active IS NOT NULL,pbf_entitiestime.entity_active, pbf_entities.entity_active) ='1' AND pbf_datafile.datafile_quarter ='" . $quarter . "' AND pbf_datafile.datafile_year ='" . $year . "' AND pbf_filetypes.dashboard_active ='1' AND pbf_filetypesentities.entity_class_id ='1' AND pbf_datafile.datafile_valid_reg = '1'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function nb_datafiles_zone_to_enter($geozone_id, $quarter, $year) { // nb of datafiles suppposed to be entered
		$sql = "SELECT SUM(IF(pbf_filetypes.filetype_frequency = '1',3,1)) as nb FROM pbf_entities LEFT JOIN pbf_filetypesentities ON (pbf_entities.entity_type=pbf_filetypesentities.entity_type_id) LEFT JOIN pbf_filetypesgeozones ON (pbf_filetypesgeozones.filetype_id = pbf_filetypesentities.filetype_id) LEFT JOIN pbf_entitiestime ON ((pbf_entitiestime.entity_id = pbf_entities.entity_id) AND (LAST_DAY('" . $year . "-" . ($quarter * 3) . "-1') BETWEEN pbf_entitiestime.use_from AND pbf_entitiestime.use_to)) LEFT JOIN pbf_filetypes ON pbf_filetypes.filetype_id = pbf_filetypesentities.filetype_id  WHERE pbf_entities.entity_geozone_id ='" . $geozone_id . "' AND IF(pbf_entitiestime.entity_active IS NOT NULL,pbf_entitiestime.entity_active, pbf_entities.entity_active) ='1' AND pbf_entities.entity_geozone_id = pbf_filetypesgeozones.geozone_id AND pbf_filetypes.dashboard_active ='1' AND pbf_filetypesentities.entity_class_id ='1'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function nb_datafiles_zone_entered($geozone_id, $quarter, $year) { // nb of datafiles entered
		$sql = "SELECT COUNT(DISTINCT pbf_datafile.datafile_id) as nb FROM pbf_datafile LEFT JOIN pbf_entities ON (pbf_entities.entity_id=pbf_datafile.entity_id) LEFT JOIN pbf_filetypesentities ON pbf_filetypesentities.filetype_id = pbf_datafile.filetype_id LEFT JOIN pbf_filetypes ON pbf_filetypes.filetype_id = pbf_datafile.filetype_id LEFT JOIN pbf_entitiestime ON ((pbf_entitiestime.entity_id = pbf_entities.entity_id) AND (LAST_DAY('" . $year . "-" . ($quarter * 3) . "-1') BETWEEN pbf_entitiestime.use_from AND pbf_entitiestime.use_to)) WHERE pbf_entities.entity_geozone_id ='" . $geozone_id . "' AND IF(pbf_entitiestime.entity_active IS NOT NULL,pbf_entitiestime.entity_active, pbf_entities.entity_active) ='1' AND pbf_datafile.datafile_quarter ='" . $quarter . "' AND pbf_datafile.datafile_year ='" . $year . "' AND pbf_filetypes.dashboard_active ='1' AND pbf_filetypesentities.entity_class_id ='1'";
		
		return $this->db->query ( $sql )->row_array ();
	}
}
		
	
