<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Indicators_mdl extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	function get_indicators($num = 0, $filters, $lang) {
		$record_set = array ();
		
		$sql_append = " WHERE 1=1 AND pbf_indicatorstranslations.indicator_language ='" . $lang . "'";
		
		if (! empty ( $filters ['title'] )) {
			$filters ['title'] = mysql_real_escape_string ( $filters ['title'] );
			
			$sql_append .= " AND (pbf_indicatorstranslations.indicator_title LIKE '%" . trim ( $filters ['title'] ) . "%' OR pbf_indicatorstranslations.indicator_abbrev LIKE '%" . trim ( $filters ['title'] ) . "%') ";
		}
		
		if (! empty ( $filters ['file'] )) {
			
			$sql_append .= " AND (pbf_indicatorsfileypes.filetype_id LIKE '%" . trim ( $filters ['file'] ) . "%') ";
		}
		
		$sql = "SELECT DISTINCT(pbf_indicators.indicator_id),pbf_indicatorstranslations.indicator_title,pbf_indicatorstranslations.indicator_abbrev,IF(pbf_indicatorunits.lookup_title_abbrev IS NOT NULL,CONCAT(pbf_indicatorunits.lookup_title,' (',pbf_indicatorunits.lookup_title_abbrev,')'),pbf_indicatorunits.lookup_title) AS indicator_units ,IF(pbf_indicatorvartype.lookup_title_abbrev IS NOT NULL,CONCAT(pbf_indicatorvartype.lookup_title,' (',pbf_indicatorvartype.lookup_title_abbrev,')'),pbf_indicatorvartype.lookup_title) AS indicator_vartype,indicator_featured FROM pbf_indicators LEFT JOIN pbf_lookups pbf_indicatorunits ON (pbf_indicatorunits.lookup_id=pbf_indicators.indicator_units AND pbf_indicatorunits.lookup_linkfile='indicator_units') LEFT JOIN pbf_lookups pbf_indicatorvartype ON (pbf_indicatorvartype.lookup_id=pbf_indicators.indicator_vartype AND pbf_indicatorvartype.lookup_linkfile='indicator_vartypes') LEFT JOIN pbf_indicatorsfileypes ON (pbf_indicatorsfileypes.indicator_id=pbf_indicators.indicator_id) LEFT JOIN pbf_indicatorstranslations ON pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id " . $sql_append . "  ORDER BY pbf_indicatorstranslations.indicator_title";
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT $num , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function get_fees($num = 0, $filters) {
		$sql_append = '';
		
		if (! empty ( $filters ['level_0'] )) {
			
			$sql_append .= " AND pbf_geozones.geozone_parentid='" . $filters ['level_0'] . "'";
		}
		
		if (! empty ( $filters ['geozone_id'] )) {
			
			$sql_append .= " AND pbf_geozones.geozone_id='" . $filters ['geozone_id'] . "'";
		}
		
		if (! empty ( $filters ['entity_id'] )) {
			
			$sql_append .= " AND pbf_entities.entity_id='" . $filters ['entity_id'] . "'";
		}
		
		if (! empty ( $filters ['datafile_month'] )) {
			
			$sql_append .= " AND ('" . $filters ['datafile_month'] . "' BETWEEN pbf_indicatorstarif.indicatortarif_monthfrom AND pbf_indicatorstarif.indicatortarif_monthto)";
		}
		
		if (! empty ( $filters ['filetype_id'] )) {
			
			$sql_append .= " AND pbf_indicatorstarif.indicatortarif_filetype_id='" . $filters ['filetype_id'] . "'";
		}
		
		$sql = "SELECT pbf_entities.entity_id,pbf_indicatorstarif.indicatortarif_id,pbf_entities.entity_name,pbf_geozones.geozone_id,pbf_indicatorstarif.indicatortarif_entity_type_id,pbf_geozones.geozone_name,indicatortarif_filetype_id,pbf_indicatorstarif.indicatortarif_monthfrom, pbf_indicatorstarif.indicatortarif_monthto,pbf_indicatorstarif.indicatortarif_year FROM pbf_indicatorstarif LEFT JOIN pbf_entities ON (pbf_indicatorstarif.indicatortarif_entity_id = pbf_entities.entity_id) LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id = pbf_entities.entity_geozone_id) WHERE 1=1 " . $sql_append;
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT $num , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function load_fees($postvars) {
		$postvars ['indicatortarif_year'] = (isset ( $postvars ['indicatortarif_year'] ) && ! is_null ( $postvars ['indicatortarif_year'] )) ? $postvars ['indicatortarif_year'] : date ( 'Y' );
		
		$postvars ['indicatortarif_monthfrom'] = (isset ( $postvars ['indicatortarif_monthfrom'] ) && ! is_null ( $postvars ['indicatortarif_monthfrom'] )) ? $postvars ['indicatortarif_monthfrom'] : date ( 'n' );
		
		$postvars ['indicatortarif_monthto'] = (isset ( $postvars ['indicatortarif_monthto'] ) && ! is_null ( $postvars ['indicatortarif_monthto'] )) ? $postvars ['indicatortarif_monthto'] : date ( 'n' );
		
		$sql = "SELECT '' AS indic_order, indicatortarif_id,indicatortarif_tarif,indicatortarif_scope FROM pbf_indicatorstarif WHERE indicatortarif_entity_id = '" . $postvars ['entity_id'] . "' AND indicatortarif_filetype_id = '" . $postvars ['filetype_id'] . "' AND indicatortarif_geozone_id = '" . $postvars ['geozone_id'] . "' AND indicatortarif_monthfrom ='" . $postvars ['indicatortarif_monthfrom'] . "' AND indicatortarif_monthto = '" . $postvars ['indicatortarif_monthto'] . "' AND indicatortarif_year = '" . $postvars ['indicatortarif_year'] . "'";
		
		if ($this->db->query ( $sql )->num_rows () == 0) {
			$sql = "SELECT pbf_indicatorsfileypes.order AS indic_order,'' AS indicatortarif_id,indicator_id,default_tarif AS indicatortarif_tarif,'self' AS indicatortarif_scope FROM pbf_indicatorsfileypes WHERE filetype_id='" . $postvars ['filetype_id'] . "' AND ('" . $postvars ['indicatortarif_year'] . "-" . $postvars ['indicatortarif_monthfrom'] . "-1' BETWEEN use_from AND use_to) ORDER BY pbf_indicatorsfileypes.order";
		}
		
		return $this->db->query ( $sql )->result_array ();
	}
	function save_indicator($indicator, $indicatorsfileypes, $language) {
		$indicator_id = $indicator ['indicator_id'];
		$this->db->delete ( 'pbf_indicatorsfileypes', array (
				'indicator_id' => $indicator_id 
		) );
		
		if (empty ( $indicator ['indicator_id'] )) {
			
			$this->db->insert ( 'pbf_indicators', $indicator );
			$indicator_id = $this->db->insert_id ();
		} else {
			
			$this->db->update ( 'pbf_indicators', $indicator, array (
					'indicator_id' => $indicator_id 
			) );
		}
		
		$sql = "SELECT pbf_indicatorstranslations.indicator_translation_id FROM pbf_indicatorstranslations WHERE pbf_indicatorstranslations.indicator_id='" . $indicator_id . "' AND pbf_indicatorstranslations.indicator_language='" . $language ['indicator_language'] . "'";
		
		$trans = $this->db->query ( $sql )->row_array ();
		
		if (isset ( $trans ['indicator_translation_id'] )) {
			$this->db->update ( 'pbf_indicatorstranslations', $language, array (
					'indicator_translation_id' => $trans ['indicator_translation_id'] 
			) );
		} else {
			$language ['indicator_id'] = $indicator_id;
			// insert directly in all languages......translate later
			
			foreach ( $this->config->item ( 'lang_uri_abbr' ) as $lk => $lv ) {
				$language ['indicator_language'] = $lk;
				$this->db->insert ( 'pbf_indicatorstranslations', $language );
			}
		}
		
		foreach ( $indicatorsfileypes ['filetype_id'] as $key => $val ) {
			
			if (! empty ( $indicatorsfileypes ['order'] [$key] )) {
				$obj ['indicatorfiletypes_id'] = '';
				$obj ['filetype_id'] = $indicatorsfileypes ['filetype_id'] [$key];
				$obj ['order'] = $indicatorsfileypes ['order'] [$key];
				$obj ['default_tarif'] = $indicatorsfileypes ['default_tarif'] [$key];
				$obj ['bonus_indigent'] = $indicatorsfileypes ['bonus_indigent'] [$key];
				$obj ['dataelts_target_abs'] = $indicatorsfileypes ['dataelts_target_abs'] [$key];
				$obj ['dataelts_target_rel'] = $indicatorsfileypes ['dataelts_target_rel'] [$key];
				$obj ['indicator_category_id'] = $indicatorsfileypes ['indicator_category_id'] [$key];
				$obj ['quality_associated'] = $indicatorsfileypes ['quality_associated'] [$key];
				$obj ['use_from'] = $indicatorsfileypes ['use_from'] [$key];
				$obj ['use_to'] = $indicatorsfileypes ['use_to'] [$key];
				$obj ['indicator_id'] = $indicator_id;
				$this->db->insert ( 'pbf_indicatorsfileypes', $obj );
			}
		}
		
		if ($this->db->affected_rows () > 0) {
			return true;
		} else {
			return false;
		}
	}
	function save_dataelmfees($fees) {
		$this->load->model ( 'entities_mdl' );
		
		$entity = $this->entities_mdl->get_entity ( $fees ['indicatortarif_entity_id'] );
		
		// check if any modif in tarif
		$storedfeesjson = $this->db->get_where ( 'pbf_indicatorstarif', array (
				'indicatortarif_id' => $fees ['indicatortarif_id'] 
		) )->result_array ();
		$storedfees = json_decode ( $storedfeesjson [0] ['indicatortarif_tarif'], true );
		
		$format = 'DATE_RFC822';
		$time = time ();
		
		$tarif_modified = false;
		$notice = "Notice changes made by " . $this->session->userdata ( 'user_fullname' ) . " on " . standard_date ( $format, $time );
		foreach ( $fees ['indicator_id'] as $k => $v ) {
			if (! empty ( $fees ['indicator_tarif'] [$k] ) && ! is_null ( $fees ['indicator_tarif'] [$k] )) {
				$fee [$v] = str_replace ( ',', '', $fees ['indicator_tarif'] [$k] );
			}
			
			if ($fee [$v] != $storedfees [$v]) 

			{
				$tarif_modified = true;
				$indic_name = $this->db->get_where ( 'pbf_indicators', array (
						'indicator_id' => $v 
				) )->result_array ();
				
				$notice .= "</BR>" . $indic_name [0] ['indicator_title'] . " was " . $storedfees [$v] . " and is now " . $fees ['indicator_tarif'] [$k];
			}
		}
		
		if ($tarif_modified) {
			$data = array (
					'group_id' => $this->session->userdata ( 'usergroup_id' ),
					'user_id' => $this->session->userdata ( 'user_id' ),
					'message' => $notice 
			);
			$this->db->insert ( 'pbf_message', $data );
		}
		
		$fees ['indicatortarif_tarif'] = json_encode ( $fee );
		unset ( $fees ['indicator_id'] );
		unset ( $fees ['indicator_tarif'] );
		
		$fees ['indicatortarif_entity_type_id'] = $entity ['entity_type'];
		$fees ['indicatortarif_entity_class_id'] = $entity ['entity_class'];
		
		switch ($fees ['indicatortarif_scope']) {
			
			case 'self' :
				
				$this->db->delete ( 'pbf_indicatorstarif', array (
						'indicatortarif_monthfrom' => $fees ['indicatortarif_monthfrom'],
						'indicatortarif_monthto' => $fees ['indicatortarif_monthto'],
						'indicatortarif_year' => $fees ['indicatortarif_year'],
						'indicatortarif_entity_id' => $fees ['indicatortarif_entity_id'],
						'indicatortarif_filetype_id' => $fees ['indicatortarif_filetype_id'] 
				) );
				$this->db->insert ( 'pbf_indicatorstarif', $fees );
				
				break;
			
			case 'sametypes' :
				
				$this->db->delete ( 'pbf_indicatorstarif', array (
						'indicatortarif_id' => $fees ['indicatortarif_id'] 
				) );
				
				$entities = $this->entities_mdl->get_raw_entities ( $entity ['entity_type'], $entity ['entity_geozone_id'], '' );
				
				foreach ( $entities as $entity ) {
					
					$this->db->delete ( 'pbf_indicatorstarif', array (
							'indicatortarif_entity_id' => $entity ['entity_id'],
							'indicatortarif_filetype_id' => $fees ['indicatortarif_filetype_id'],
							'indicatortarif_monthfrom' => $fees ['indicatortarif_monthfrom'],
							'indicatortarif_monthto' => $fees ['indicatortarif_monthto'],
							'indicatortarif_year' => $fees ['indicatortarif_year'] 
					) );
					
					$fees ['indicatortarif_id'] = '';
					$fees ['indicatortarif_entity_id'] = $entity ['entity_id'];
					$fees ['indicatortarif_geozone_id'] = $entity ['entity_geozone_id'];
					
					$this->db->insert ( 'pbf_indicatorstarif', $fees );
				}
				
				break;
			
			case 'all' :
				
				$this->db->delete ( 'pbf_indicatorstarif', array (
						'indicatortarif_id' => $fees ['indicatortarif_id'] 
				) );
				
				$entities = $this->entities_mdl->get_raw_entities ( '', $entity ['entity_geozone_id'], '' );
				
				foreach ( $entities as $entity ) {
					
					$this->db->delete ( 'pbf_indicatorstarif', array (
							'indicatortarif_entity_id' => $entity ['entity_id'],
							'indicatortarif_filetype_id' => $fees ['indicatortarif_filetype_id'],
							'indicatortarif_monthfrom' => $fees ['indicatortarif_monthfrom'],
							'indicatortarif_monthto' => $fees ['indicatortarif_monthto'],
							'indicatortarif_year' => $fees ['indicatortarif_year'] 
					) );
					
					$fees ['indicatortarif_id'] = '';
					$fees ['indicatortarif_entity_id'] = $entity ['entity_id'];
					$fees ['indicatortarif_geozone_id'] = $entity ['entity_geozone_id'];
					
					$this->db->insert ( 'pbf_indicatorstarif', $fees );
				}
				
				break;
			
			case 'level_0_sametypes' :
				
				$this->db->delete ( 'pbf_indicatorstarif', array (
						'indicatortarif_id' => $fees ['indicatortarif_id'] 
				) );
				
				$entities = $this->entities_mdl->get_raw_entities ( $entity ['entity_type'], '', $entity ['geozone_parentid'] );
				
				foreach ( $entities as $entity ) {
					
					$this->db->delete ( 'pbf_indicatorstarif', array (
							'indicatortarif_entity_id' => $entity ['entity_id'],
							'indicatortarif_filetype_id' => $fees ['indicatortarif_filetype_id'],
							'indicatortarif_monthfrom' => $fees ['indicatortarif_monthfrom'],
							'indicatortarif_monthto' => $fees ['indicatortarif_monthto'],
							'indicatortarif_year' => $fees ['indicatortarif_year'] 
					) );
					
					$fees ['pbf_indicatorstarif'] = '';
					$fees ['indicatortarif_entity_id'] = $entity ['entity_id'];
					$fees ['indicatortarif_geozone_id'] = $entity ['entity_geozone_id'];
					
					$this->db->insert ( 'pbf_indicatorstarif', $fees );
				}
				
				break;
			
			case 'country_sametypes' :
				
				$this->db->delete ( 'pbf_indicatorstarif', array (
						'indicatortarif_id' => $fees ['indicatortarif_id'] 
				) );
				
				$entities = $this->entities_mdl->get_raw_entities ( $entity ['entity_type'], '', '' );
				
				foreach ( $entities as $entity ) {
					
					$this->db->delete ( 'pbf_indicatorstarif', array (
							'indicatortarif_entity_id' => $entity ['entity_id'],
							'indicatortarif_filetype_id' => $fees ['indicatortarif_filetype_id'],
							'indicatortarif_monthfrom' => $fees ['indicatortarif_monthfrom'],
							'indicatortarif_monthto' => $fees ['indicatortarif_monthto'],
							'indicatortarif_year' => $fees ['indicatortarif_year'] 
					) );
					
					$fees ['pbf_indicatorstarif'] = '';
					$fees ['indicatortarif_entity_id'] = $entity ['entity_id'];
					$fees ['indicatortarif_geozone_id'] = $entity ['entity_geozone_id'];
					
					$this->db->insert ( 'pbf_indicatorstarif', $fees );
				}
				
				break;
			
			case 'all' :
				
				$this->db->delete ( 'pbf_indicatorstarif', array (
						'indicatortarif_id' => $fees ['indicatortarif_id'] 
				) );
				
				$entities = $this->entities_mdl->get_raw_entities ( '', '', '' );
				
				foreach ( $entities as $entity ) {
					
					$this->db->delete ( 'pbf_indicatorstarif', array (
							'indicatortarif_entity_id' => $entity ['entity_id'],
							'indicatortarif_filetype_id' => $fees ['indicatortarif_filetype_id'],
							'indicatortarif_monthfrom' => $fees ['indicatortarif_monthfrom'],
							'indicatortarif_monthto' => $fees ['indicatortarif_monthto'],
							'indicatortarif_year' => $fees ['indicatortarif_year'] 
					) );
					
					$fees ['pbf_indicatorstarif'] = '';
					$fees ['indicatortarif_entity_id'] = $entity ['entity_id'];
					$fees ['indicatortarif_geozone_id'] = $entity ['entity_geozone_id'];
					
					$this->db->insert ( 'pbf_indicatorstarif', $fees );
				}
				
				break;
		}
		
		if ($this->db->affected_rows () > 0) {
			return true;
		} else {
			return false;
		}
	}
	function get_indicator($indicator_id, $lang) {
		$sql_append = " WHERE 1=1 AND pbf_indicatorstranslations.indicator_language ='" . $lang . "'";
		
		$sql = "SELECT DISTINCT(pbf_indicators.indicator_id),pbf_indicators.indicator_units,pbf_indicators.indicator_vartype,pbf_indicators.indicator_realtime_result,pbf_indicators.indicator_use_indigence_bonus,pbf_indicators.indicator_editable_tarif,pbf_indicators.indicator_icon_file,pbf_indicatorstranslations.indicator_title,pbf_indicatorstranslations.indicator_abbrev,pbf_indicatorstranslations.indicator_description,pbf_indicatorstranslations.indicator_common_name,IF(pbf_indicatorunits.lookup_title_abbrev IS NOT NULL,CONCAT(pbf_indicatorunits.lookup_title,' (',pbf_indicatorunits.lookup_title_abbrev,')'),pbf_indicatorunits.lookup_title) AS indicator_units ,IF(pbf_indicatorvartype.lookup_title_abbrev IS NOT NULL,CONCAT(pbf_indicatorvartype.lookup_title,' (',pbf_indicatorvartype.lookup_title_abbrev,')'),pbf_indicatorvartype.lookup_title) AS indicator_vartype,indicator_featured, indicator_use_coverage,indicator_popcible FROM pbf_indicators LEFT JOIN pbf_lookups pbf_indicatorunits ON (pbf_indicatorunits.lookup_id=pbf_indicators.indicator_units AND pbf_indicatorunits.lookup_linkfile='indicator_units') LEFT JOIN pbf_lookups pbf_indicatorvartype ON (pbf_indicatorvartype.lookup_id=pbf_indicators.indicator_vartype AND pbf_indicatorvartype.lookup_linkfile='indicator_vartypes') LEFT JOIN pbf_indicatorsfileypes ON (pbf_indicatorsfileypes.indicator_id=pbf_indicators.indicator_id) LEFT JOIN pbf_indicatorstranslations ON pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id " . $sql_append . " AND pbf_indicators.indicator_id ='" . $indicator_id . "' ORDER BY pbf_indicatorstranslations.indicator_title";
		
		$indicator ['indicator'] = $this->db->query ( $sql )->row_array ();
		$indicator ['indicatorsfileypes'] = $this->db->get_where ( 'pbf_indicatorsfileypes', array (
				'indicator_id' => $indicator_id 
		) )->result_array ();
		
		return $indicator;
	}
	function set_feature($indicator_id, $state) {
		$sql = "UPDATE pbf_indicators SET indicator_featured='" . $state . "' WHERE indicator_id='" . $indicator_id . "'";
		return $this->db->simple_query ( $sql );
	}
	function del_indicator($indicator_id) {
		$this->db->delete ( 'pbf_indicators', array (
				'indicator_id' => $indicator_id 
		) );
		$this->db->delete ( 'pbf_indicatorsfileypes', array (
				'indicator_id' => $indicator_id 
		) );
	}
	function delete_fees($dataelttarif_id) {
		return $this->db->delete ( 'pbf_indicatorstarif', array (
				'indicatortarif_id' => $dataelttarif_id 
		) );
	}
}