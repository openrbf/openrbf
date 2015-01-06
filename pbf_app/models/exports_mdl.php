<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Exports_mdl extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	function drop_routin_data($ext_aleatoire) {
		$sql = "DROP TABLE IF EXISTS pbf_computed_routine_data" . $ext_aleatoire;
		$result = $this->db->query ( $sql );
		return $result;
	}
	function set_routine_data_table($ext_aleatoire, $year, $lang) {
		$sql = "CREATE TABLE pbf_computed_routine_data" . $ext_aleatoire . " AS SELECT pbf_entities.entity_id,IF(pbf_entitytypes.entity_type_abbrev!=0 OR pbf_entitytypes.entity_type_abbrev!='',CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev),pbf_entities.entity_name) AS entity_name,pbf_entityclasses.entity_class_id,pbf_entitytypes.entity_type_id,pbf_geozones.geozone_parentid,pbf_parent_geozones.geozone_name AS parent_geozone_name,pbf_geozones.geozone_id,pbf_geozones.geozone_name,pbf_entitytypes.entity_type_name AS entity_type,pbf_entities.entity_status AS entity_status_id,pbf_entitygroups.entity_group_abbrev as entity_group_abbrev,pbf_entities.entity_sis_code as entity_fosa_id,pbf_lookups.lookup_title AS content,pbf_lkp_status.lookup_title AS entity_status,pbf_entities.entity_active,pbf_datafile.filetype_id,pbf_filetypes.filetype_name,pbf_datafile.datafile_month,pbf_datafile.datafile_quarter,pbf_datafile.datafile_year,pbf_datafile.datafile_total,pbf_datafile.datafile_author_id,pbf_datafile.datafile_status,pbf_datafile.datafile_remark,pbf_indicators.indicator_id,pbf_indicators.indicator_title,pbf_indicators.indicator_abbrev,pbf_datafiledetails.indicator_claimed_value,pbf_datafiledetails.indicator_verified_value,pbf_datafiledetails.indicator_validated_value,pbf_datafiledetails.indicator_tarif,pbf_datafiledetails.indicator_montant,pbf_entities.entity_pop,pbf_entities.entity_pop_year,pbf_geozones.geozone_pop,pbf_geozones.geozone_pop_year,pbf_parent_geozones.geozone_pop AS parent_geozone_pop,pbf_parent_geozones.geozone_pop_year AS parent_geozone_pop_year FROM pbf_entities LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_geozones pbf_parent_geozones ON (pbf_parent_geozones.geozone_id=pbf_geozones.geozone_parentid) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_lookups pbf_lkp_status ON (pbf_lkp_status.lookup_id=pbf_entities.entity_status AND pbf_lkp_status.lookup_linkfile='entity_status') LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id=pbf_entities.entity_id) LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) LEFT JOIN pbf_datafiledetails ON (pbf_datafiledetails.datafile_id=pbf_datafile.datafile_id) LEFT JOIN pbf_indicators ON (pbf_indicators.indicator_id=pbf_datafiledetails.indicator_id) LEFT JOIN pbf_entitygroups ON ( pbf_entities.entity_pbf_group_id=pbf_entitygroups.entity_group_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_indicatorstranslations ON (pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id) WHERE pbf_datafile.datafile_year = '" . $year . "' AND pbf_indicatorstranslations.indicator_language='" . $lang . "'   ";
		
		$this->db->query ( $sql );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (entity_id);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (entity_class_id);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (entity_type_id);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (geozone_parentid);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (geozone_id);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (entity_status_id);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (entity_active);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (filetype_id);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (datafile_month);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (datafile_quarter);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (datafile_year);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (datafile_author_id);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (indicator_id);' );
	}
	function set_routine_data_table_full($ext_aleatoire, $lang) {
		$sql = "CREATE TABLE pbf_computed_routine_data" . $ext_aleatoire . " AS SELECT pbf_entities.entity_id,IF(pbf_entitytypes.entity_type_abbrev!=0 OR pbf_entitytypes.entity_type_abbrev!='',CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev),pbf_entities.entity_name) AS entity_name,pbf_entityclasses.entity_class_id,pbf_entitytypes.entity_type_id,pbf_geozones.geozone_parentid,pbf_parent_geozones.geozone_name AS parent_geozone_name,pbf_geozones.geozone_id,pbf_geozones.geozone_name,pbf_entitytypes.entity_type_name AS entity_type,pbf_entities.entity_status AS entity_status_id,pbf_entitygroups.entity_group_abbrev as entity_group_abbrev,pbf_entities.entity_sis_code as entity_fosa_id,pbf_lookups.lookup_title AS content,pbf_lkp_status.lookup_title AS entity_status,pbf_entities.entity_active,pbf_datafile.filetype_id,pbf_filetypes.filetype_name,pbf_datafile.datafile_month,pbf_datafile.datafile_quarter,pbf_datafile.datafile_year,pbf_datafile.datafile_total,pbf_datafile.datafile_author_id,pbf_datafile.datafile_status,pbf_datafile.datafile_remark,pbf_indicators.indicator_id,pbf_indicators.indicator_title,pbf_indicators.indicator_abbrev,pbf_datafiledetails.indicator_claimed_value,pbf_datafiledetails.indicator_verified_value,pbf_datafiledetails.indicator_validated_value,pbf_datafiledetails.indicator_tarif,pbf_datafiledetails.indicator_montant,pbf_entities.entity_pop,pbf_entities.entity_pop_year,pbf_geozones.geozone_pop,pbf_geozones.geozone_pop_year,pbf_parent_geozones.geozone_pop AS parent_geozone_pop,pbf_parent_geozones.geozone_pop_year AS parent_geozone_pop_year FROM pbf_entities LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_geozones pbf_parent_geozones ON (pbf_parent_geozones.geozone_id=pbf_geozones.geozone_parentid) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_lookups pbf_lkp_status ON (pbf_lkp_status.lookup_id=pbf_entities.entity_status AND pbf_lkp_status.lookup_linkfile='entity_status') LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id=pbf_entities.entity_id) LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) LEFT JOIN pbf_datafiledetails ON (pbf_datafiledetails.datafile_id=pbf_datafile.datafile_id) LEFT JOIN pbf_indicators ON (pbf_indicators.indicator_id=pbf_datafiledetails.indicator_id) LEFT JOIN pbf_entitygroups ON ( pbf_entities.entity_pbf_group_id=pbf_entitygroups.entity_group_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_indicatorstranslations ON (pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id) WHERE  pbf_indicatorstranslations.indicator_language='" . $lang . "'   ";
		$this->db->query ( $sql );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (entity_id);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (entity_class_id);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (entity_type_id);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (geozone_parentid);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (geozone_id);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (entity_status_id);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (entity_active);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (filetype_id);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (datafile_month);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (datafile_quarter);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (datafile_year);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (datafile_author_id);' );
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (indicator_id);' );
	}
	function get_file_columns($filetype_id, $lang) {
		$sql = "SELECT DISTINCT pbf_indicators.indicator_id,pbf_indicatorstranslations.indicator_title,pbf_indicatorstranslations.indicator_abbrev,pbf_indicatorsfileypes.filetype_id FROM pbf_indicators LEFT JOIN pbf_indicatorsfileypes ON (pbf_indicatorsfileypes.indicator_id=pbf_indicators.indicator_id) LEFT JOIN pbf_indicatorstranslations ON (pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id)  WHERE pbf_indicatorsfileypes.filetype_id = '" . $filetype_id . "' AND pbf_indicatorstranslations.indicator_language ='" . $lang . "' 
		ORDER BY pbf_indicatorsfileypes.order";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_file_contents_all($columns, $ftype, $year) {
		if (empty ( $year )) {
			$year = date ( 'Y' );
		}
		$count = 0;
		
		$sql = "SELECT parent_geozone_name AS '" . $this->lang->line ( 'lang_ky_region' ) . "',
                     geozone_name AS '" . $this->lang->line ( 'lang_ky_district' ) . "',
                     entity_name AS '" . $this->lang->line ( 'lang_ky_formation_sanitaire' ) . "',
                     entity_sis_code AS '" . $this->lang->line ( 'lang_ky_entity_sis_code' ) . "',
                     datafile_month AS '" . $this->lang->line ( 'lang_ky_month' ) . "',
                     datafile_year AS '" . $this->lang->line ( 'lang_ky_year' ) . "',
                     datafile_total AS 'Total Cout Mensuel',entity_group_abbrev AS 'group'";
		
		foreach ( $columns as $column ) {
			
			if (is_null ( $column ['indicator_target'] ) || empty ( $column ['indicator_target'] )) {
				$target = 0;
			} else { // sdl because target always start with / or *
				eval ( '$target = 1' . $column ['indicator_target'] . ';' );
			}
			
			$sql .= ",ROUND(SUM(IF(indicator_id='" . $column ['indicator_id'] . "',(
entity_pop*POWER((1+(" . ($this->config->item ( 'pop_growth_rate' ) / 100) . ")),(datafile_year-entity_pop_year))),0))*" . ($target) . ",0) AS 'target_" . $count . "'";
			
			$sql .= ",SUM(IF(indicator_id='" . $column ['indicator_id'] . "',indicator_claimed_value,0)) AS 'claimed_" . $count . "'";
			$sql .= ",SUM(IF(indicator_id='" . $column ['indicator_id'] . "',indicator_verified_value,0)) AS 'verified_" . $count . "'";
			$sql .= ",SUM(IF(indicator_id='" . $column ['indicator_id'] . "',indicator_validated_value,0)) AS 'validated_" . $count . "'";
			$sql .= ",SUM(IF(indicator_id='" . $column ['indicator_id'] . "',indicator_tarif,0)) AS 'tarif_" . $count . "'";
			$sql .= ",SUM(IF(indicator_id='" . $column ['indicator_id'] . "',indicator_montant,0)) AS 'montant_" . $count . "'";
			
			$count ++;
		}
		
		$sql .= " FROM pbf_computed_routine_data WHERE filetype_id='" . $ftype . "' AND datafile_year ='" . $year . "' GROUP BY geozone_parentid,geozone_id,entity_id,datafile_year,
				datafile_month ORDER BY parent_geozone_name,geozone_name,datafile_year,datafile_month";
		
		return $this->db->query ( $sql );
	}
	function get_exports($num = 0, $filters = '') {
		$sql = "SELECT * FROM pbf_exports";
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT $num , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function get_export($exports_id) {
		return $this->db->get_where ( 'pbf_exports', array (
				'exports_id' => $exports_id 
		) )->row_array ();
	}
	function del_export($exports_id) {
		return $this->db->delete ( 'pbf_exports', array (
				'exports_id' => $exports_id 
		) );
	}
	function save_conf($exports) {
		if (empty ( $exports ['exports_id'] )) {
			
			return $this->db->insert ( 'pbf_exports', $exports );
		} else {
			
			return $this->db->update ( 'pbf_exports', $exports, array (
					'exports_id' => $exports ['exports_id'] 
			) );
		}
	}
	function get_file_contents($task, $columns, $ext_aleatoire) {
		$title = 'Total Mensuel';
		
		if ($task ['filetype_id'] == 3 or $task ['filetype_id'] == 4 or $task ['filetype_id'] == 5) {
			
			$title = 'Score trimestrielle Qualite';
		}
		
		if ($task ['filetype_id'] == 10) {
			
			$sql = "SELECT parent_geozone_name AS Region,geozone_name AS District,entity_name AS 'Formation Sanitaire',entity_group_abbrev AS 'Groupe',entity_fosa_id AS 'Identidiant FOSA',datafile_remark as 'Nom OCB',datafile_month AS Mois,datafile_year AS Annee";
		} 

		else {
			$sql = "SELECT parent_geozone_name AS Region,geozone_name AS District,entity_name AS 'Formation Sanitaire',entity_group_abbrev AS 'Groupe',entity_fosa_id AS 'Identidiant FOSA',datafile_remark as 'Nom OCB',datafile_month AS Mois,datafile_year AS Annee,datafile_total AS '" . $title . "'";
		}
		
		foreach ( $columns as $column ) {
			
			$sql .= ",SUM(IF(indicator_id='" . $column ['indicator_id'] . "'," . $task ['datatype'] . ",0)) AS '" . mysql_real_escape_string ( $column ['indicator_title'] ) . "'";
		}
		
		$sql .= " FROM pbf_computed_routine_data" . $ext_aleatoire . " WHERE filetype_id='" . $task ['filetype_id'] . "' GROUP BY geozone_parentid,geozone_id,entity_id,datafile_year,datafile_month ORDER BY parent_geozone_name,geozone_name,datafile_year,datafile_month";
		
		return $this->db->query ( $sql );
	}
	function get_file_contents_manager($task, $columns) {
		if (empty ( $year ))
			$year = date ( 'Y' );
		
		$sql = "SELECT parent_geozone_name AS '" . $this->lang->line ( 'lang_ky_region' ) . "',
                geozone_name AS '" . $this->lang->line ( 'lang_ky_district' ) . "',
                entity_name AS '" . $this->lang->line ( 'lang_ky_formation_sanitaire' ) . "',
                entity_sis_code AS '" . $this->lang->line ( 'lang_ky_entity_sis_code' ) . "',
                datafile_month AS '" . $this->lang->line ( 'lang_ky_month' ) . "',		
                datafile_year AS '" . $this->lang->line ( 'lang_ky_year' ) . "'," . (($task ['datatype'] == 'indicator_target') ? "
                (entity_pop*POWER(1+(" . ($this->config->item ( 'pop_growth_rate' ) / 100) . "),
                (entity_pop_year-datafile_year))) AS '" . $this->lang->line ( 'lang_ky_pop_cible' ) . "'" : "datafile_total AS 'Total Cout Mensuel',
                entity_group_abbrev AS 'group'") . "";
		
		foreach ( $columns as $column ) {
			
			if ($task ['datatype'] == 'indicator_target') { // sdl in case indicator_target need to multiply by population
				if (is_null ( $column ['indicator_target'] ) || empty ( $column ['indicator_target'] )) {
					$target = 0;
				} else { // sdl because target always start with / or *
					eval ( '$target = 1' . $column ['indicator_target'] . ';' );
				}
				$sql .= ",ROUND(SUM(IF(indicator_id='" . $column ['indicator_id'] . "',(
                        entity_pop*POWER((1+" . ($this->config->item ( 'pop_growth_rate' ) / 100) . "),
                            (entity_pop_year-datafile_year))),0))*" . ($target) . ",0) AS '" . mysql_real_escape_string ( $column ['indicator_title'] ) . "'";
			} else {
				$sql .= ",SUM(IF(indicator_id='" . $column ['indicator_id'] . "'," . $task ['datatype'] . ",0)) AS '" . mysql_real_escape_string ( $column ['indicator_title'] ) . "'";
			}
		}
		
		$sql .= " FROM pbf_computed_routine_data WHERE filetype_id='" . $task ['filetype_id'] . "' AND datafile_year ='" . $year . "' GROUP BY geozone_parentid,geozone_id,entity_id,datafile_year,
				datafile_month ORDER BY parent_geozone_name,geozone_name,datafile_year,datafile_month";
		
		return $this->db->query ( $sql );
	}
	function get_file_contents_all_year($columns, $ftype, $year) {
		$count = 0;
		
		$sql = "SELECT parent_geozone_name AS '" . $this->lang->line ( 'lang_ky_region' ) . "',geozone_name AS '" . $this->lang->line ( 'lang_ky_district' ) . "',
				entity_name AS '" . $this->lang->line ( 'lang_ky_formation_sanitaire' ) . "',entity_sis_code AS '" . $this->lang->line ( 'lang_ky_entity_sis_code' ) . "',datafile_month AS '" . $this->lang->line ( 'lang_ky_month' ) . "',
		
		
				datafile_year AS '" . $this->lang->line ( 'lang_ky_year' ) . "',datafile_total AS 'Total Cout Mensuel',
				  entity_group_abbrev AS 'group'";
		
		foreach ( $columns as $column ) {
			
			if (is_null ( $column ['indicator_target'] ) || empty ( $column ['indicator_target'] )) {
				$target = 0;
			} else { // sdl because target always start with / or *
				eval ( '$target = 1' . $column ['indicator_target'] . ';' );
			}
			
			$sql .= ",ROUND(SUM(IF(indicator_id='" . $column ['indicator_id'] . "',(
entity_pop*POWER(1+(" . ($this->config->item ( 'pop_growth_rate' ) / 100) . "),(entity_pop_year-datafile_year))),0))*" . ($target) . ",0) AS 'target_" . $count . "'";
			
			$sql .= ",SUM(IF(indicator_id='" . $column ['indicator_id'] . "',indicator_claimed_value,0)) AS 'claimed_" . $count . "'";
			$sql .= ",SUM(IF(indicator_id='" . $column ['indicator_id'] . "',indicator_verified_value,0)) AS 'verified_" . $count . "'";
			$sql .= ",SUM(IF(indicator_id='" . $column ['indicator_id'] . "',indicator_validated_value,0)) AS 'validated_" . $count . "'";
			$sql .= ",SUM(IF(indicator_id='" . $column ['indicator_id'] . "',indicator_tarif,0)) AS 'tarif_" . $count . "'";
			$sql .= ",SUM(IF(indicator_id='" . $column ['indicator_id'] . "',indicator_montant,0)) AS 'montant_" . $count . "'";
			
			$count ++;
		}
		
		$sql .= " FROM pbf_computed_routine_data WHERE filetype_id='" . $ftype . "' AND datafile_year ='" . $year . "' GROUP BY geozone_parentid,geozone_id,entity_id,
				datafile_month ORDER BY parent_geozone_name,geozone_name,datafile_month";
		
		return $this->db->query ( $sql );
	}
	function get_filetype_name($ftype_id) {
		return $this->db->get_where ( 'pbf_filetypes', array (
				'filetype_id' => $ftype_id 
		) )->row_array ();
	}
	function get_export_years() {
		$sql = "SELECT DISTINCT(YEAR(datafile_created)) export_year FROM pbf_datafile order by export_year asc";
		
		$result = $this->db->query ( $sql );
		
		return $result->result_array ();
	}
	
	// fonction qui retourne la liste des utilisateurs avec leur données clefs les districts ou ils ont accès groupe auquel il font parti
	function get_User() {
		$sql = "select pbf_users.user_fullname as name, GROUP_CONCAT(pbf_geozones.geozone_name SEPARATOR ',') as access_zones, pbf_usersgroups.usersgroup_name as user_group from pbf_users left join pbf_usersgeozones on pbf_usersgeozones.user_id = pbf_users.user_id left join pbf_geozones on pbf_geozones.geozone_id = pbf_usersgeozones.geozone_id left join pbf_usersgroupsmap on pbf_usersgroupsmap.user_id = pbf_users.user_id left join pbf_usersgroups on pbf_usersgroups.usersgroup_id = pbf_usersgroupsmap.usergroup_id group by pbf_users.user_id";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function drop_routine_data_all() {
		$tables = $this->db->list_tables ();
		foreach ( $tables as $table ) {
			if (strstr ( $table, pbf_computed_routine_data )) {
				$sql = "DROP TABLE IF EXISTS " . $table;
				$result = $this->db->query ( $sql );
			}
		}
	}
}