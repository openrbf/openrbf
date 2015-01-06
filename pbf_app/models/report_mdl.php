<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Report_mdl extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	function set_routine_data_table_BM($lang) {
		$this->db->simple_query ( 'DROP TABLE IF EXISTS pbf_computed_routine_data;' );
		
		$sql = "CREATE TABLE pbf_computed_routine_data SELECT pbf_entities.entity_id,IF(pbf_entitytypes.entity_type_abbrev!=0 OR pbf_entitytypes.entity_type_abbrev!='',CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev),pbf_entities.entity_name) AS entity_name,pbf_entities.entity_sis_code,pbf_entityclasses.entity_class_id,pbf_entitytypes.entity_type_id,pbf_geozones.geozone_parentid,pbf_parent_geozones.geozone_name AS parent_geozone_name,pbf_geozones.geozone_id,pbf_geozones.geozone_name,pbf_entitytypes.entity_type_name AS entity_type,pbf_entities.entity_status AS entity_status_id,pbf_lkp_status.lookup_title AS entity_status,pbf_entities.entity_active,pbf_datafile.filetype_id,pbf_filetypes.filetype_name,pbf_datafile.datafile_month,pbf_datafile.datafile_quarter,pbf_datafile.datafile_year,pbf_datafile.datafile_total,pbf_datafile.datafile_author_id,pbf_datafile.datafile_status,pbf_indicators.indicator_id,pbf_indicatorstranslations.indicator_title,pbf_indicatorstranslations.indicator_abbrev,pbf_indicators.indicator_target,pbf_datafiledetails.indicator_claimed_value,pbf_datafiledetails.indicator_verified_value,pbf_datafiledetails.indicator_validated_value,pbf_datafiledetails.indicator_tarif,pbf_datafiledetails.indicator_montant,pbf_entities.entity_pop,pbf_entities.entity_pop_year,pbf_geozones.geozone_pop,pbf_geozones.geozone_pop_year,pbf_parent_geozones.geozone_pop AS parent_geozone_pop,pbf_parent_geozones.geozone_pop_year AS parent_geozone_pop_year FROM pbf_entities LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_geozones pbf_parent_geozones ON (pbf_parent_geozones.geozone_id=pbf_geozones.geozone_parentid) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_lookups pbf_lkp_status ON (pbf_lkp_status.lookup_id=pbf_entities.entity_status AND pbf_lkp_status.lookup_linkfile='entity_status') LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id=pbf_entities.entity_id) LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) LEFT JOIN pbf_datafiledetails ON (pbf_datafiledetails.datafile_id=pbf_datafile.datafile_id) LEFT JOIN pbf_indicators ON (pbf_indicators.indicator_id=pbf_datafiledetails.indicator_id) LEFT JOIN pbf_indicatorstranslations ON pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id WHERE pbf_indicatorstranslations.indicator_language ='" . $lang . "'";
		
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
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (entity_sis_code);' );
	}
	function set_routine_data_table_BM_geo($zone, $lang) {
		$this->db->simple_query ( 'DROP TABLE IF EXISTS pbf_computed_routine_data;' );
		
		$sql = "CREATE TABLE pbf_computed_routine_data SELECT pbf_entities.entity_id,IF(pbf_entitytypes.entity_type_abbrev!=0 OR pbf_entitytypes.entity_type_abbrev!='',CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev),pbf_entities.entity_name) AS entity_name,pbf_entities.entity_sis_code,pbf_entityclasses.entity_class_id,pbf_entitytypes.entity_type_id,pbf_geozones.geozone_parentid,pbf_parent_geozones.geozone_name AS parent_geozone_name,pbf_geozones.geozone_id,pbf_geozones.geozone_name,pbf_entitytypes.entity_type_name AS entity_type,pbf_entities.entity_status AS entity_status_id,pbf_lkp_status.lookup_title AS entity_status,pbf_entities.entity_active,pbf_datafile.filetype_id,pbf_filetypes.filetype_name,pbf_datafile.datafile_month,pbf_datafile.datafile_quarter,pbf_datafile.datafile_year,pbf_datafile.datafile_total,pbf_datafile.datafile_author_id,pbf_datafile.datafile_status,pbf_indicators.indicator_id,pbf_indicatorstranslations.indicator_title,pbf_indicatorstranslations.indicator_abbrev,pbf_indicators.indicator_target,pbf_datafiledetails.indicator_claimed_value,pbf_datafiledetails.indicator_verified_value,pbf_datafiledetails.indicator_validated_value,pbf_datafiledetails.indicator_tarif,pbf_datafiledetails.indicator_montant,pbf_entities.entity_pop,pbf_entities.entity_pop_year,pbf_geozones.geozone_pop,pbf_geozones.geozone_pop_year,pbf_parent_geozones.geozone_pop AS parent_geozone_pop,pbf_parent_geozones.geozone_pop_year AS parent_geozone_pop_year FROM pbf_entities LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_geozones pbf_parent_geozones ON (pbf_parent_geozones.geozone_id=pbf_geozones.geozone_parentid) LEFT JOIN pbf_entityclasses ON (pbf_entityclasses.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_lookups pbf_lkp_status ON (pbf_lkp_status.lookup_id=pbf_entities.entity_status AND pbf_lkp_status.lookup_linkfile='entity_status') LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id=pbf_entities.entity_id) LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) LEFT JOIN pbf_datafiledetails ON (pbf_datafiledetails.datafile_id=pbf_datafile.datafile_id) LEFT JOIN pbf_indicators ON (pbf_indicators.indicator_id=pbf_datafiledetails.indicator_id) LEFT JOIN pbf_indicatorstranslations ON pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id WHERE pbf_geozones.geozone_id = '" . $zone . "' AND pbf_indicatorstranslations.indicator_language ='" . $lang . "'";
		
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
		$this->db->simple_query ( 'ALTER TABLE pbf_computed_routine_data ADD INDEX (entity_sis_code);' );
	}
	function get_reports_all() {
		$sql = "SELECT DISTINCT report_id,report_title FROM pbf_reporting WHERE report_access ='public'";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_reports($zones = array()) {
		$sql = "SELECT DISTINCT report_id,report_title FROM pbf_reporting LEFT JOIN pbf_reporting_geozones on pbf_reporting.report_id = pbf_reporting_geozones.pbf_reporting_id WHERE report_access ='public' " . (empty ( $zones ) ? "" : " AND pbf_reporting_geozones.pbf_geozone_id IN (" . implode ( ',', $zones ) . ")") . " ";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function list_reports_conf($num = 0, $filters) {
		$record_set = array ();
		$sql_append = " WHERE 1=1";
		
		if (! empty ( $filters ['user_fullname'] )) {
			
			$sql_append .= " AND (pbf_users.user_fullname LIKE '%" . trim ( $filters ['user_fullname'] ) . "%') ";
		}
		
		$sql = "SELECT report_id,report_title,report_category,report_page_layout,report_access,report_media,report_type,pbf_users.user_fullname
 FROM pbf_reporting LEFT JOIN pbf_users ON (pbf_users.user_id=pbf_reporting.report_author)" . $sql_append;
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT $num , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function monthly_payment_request($datafile_month, $datafile_year, $geozone_id, $filetypes) {
		$sql = "SELECT pbf_entities.entity_id,IF(((pbf_entitiestime.entity_type IS NOT NULL) OR (pbf_entitiestime.entity_type != '0')),pbf_entitiestime.entity_type, pbf_entities.entity_type),IF(((pbf_entitiestime.entity_pbf_group_id IS NOT NULL) OR (pbf_entitiestime.entity_pbf_group_id != '0')),pbf_entitiestime.entity_pbf_group_id, pbf_entities.entity_pbf_group_id),CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev) AS entity_name,pbf_geozones.geozone_name,pbf_entities.entity_address 	,pbf_entities.entity_pop,pbf_entitygroups.entity_group_abbrev,pbf_datafile.datafile_total FROM pbf_entities LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_entitygroups ON (pbf_entitygroups.entity_group_id = pbf_entities.entity_pbf_group_id) LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id=pbf_entities.entity_id) LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id = pbf_entities.entity_geozone_id) LEFT JOIN pbf_entitiestime ON ((pbf_entitiestime.entity_id = pbf_entities.entity_id) AND (LAST_DAY('" . $datafile_year . "-" . $datafile_month . "-1') BETWEEN pbf_entitiestime.use_from AND pbf_entitiestime.use_to)) WHERE pbf_datafile.filetype_id IN (" . implode ( ',', $filetypes ) . ") AND pbf_datafile.datafile_month = '" . $datafile_month . "' AND pbf_datafile.datafile_year='" . $datafile_year . "' AND IF(((pbf_entitiestime.entity_pbf_group_id IS NOT NULL) AND (pbf_entitiestime.entity_pbf_group_id != '0')),pbf_entitiestime.entity_pbf_group_id, pbf_entities.entity_pbf_group_id) IN (0,1,2) AND pbf_entities.entity_geozone_id='" . $geozone_id . "' ORDER BY pbf_entities.entity_name";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function monthly_consolidated_resumee_for_c1_zones($datafile_month, $datafile_year, $asso_types, $entity_group_id, $zone, $filetypes) {
		$sql = "SELECT pbf_entities.entity_id,pbf_entities.entity_geozone_id,IF(((pbf_entitiestime.entity_type IS NOT NULL) OR (pbf_entitiestime.entity_type != '0')),pbf_entitiestime.entity_type, pbf_entities.entity_type),IF(((pbf_entitiestime.entity_pbf_group_id IS NOT NULL) OR (pbf_entitiestime.entity_pbf_group_id != '0')),pbf_entitiestime.entity_pbf_group_id, pbf_entities.entity_pbf_group_id),CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev) AS entity_name,pbf_entities.entity_pop,pbf_entitygroups.entity_group_abbrev,pbf_datafile.datafile_total FROM pbf_entities LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type) LEFT JOIN pbf_entitygroups ON (pbf_entitygroups.entity_group_id = pbf_entities.entity_pbf_group_id) LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id=pbf_entities.entity_id) WHERE pbf_datafile.filetype_id IN (" . implode ( ',', $filetypes ) . ") AND pbf_datafile.datafile_month = '" . $datafile_month . "' AND pbf_datafile.datafile_year='" . $datafile_year . "' AND pbf_entities.entity_pbf_group_id IN (0,1,2) AND pbf_entities.entity_pbf_group_id='" . $entity_group_id . "' AND pbf_entities.entity_type IN (" . implode ( ',', $asso_types ) . ") AND pbf_entities.entity_geozone_id IN (" . implode ( ',', $zone ) . ") ORDER BY pbf_entities.entity_name";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function report_count($asso_types, $entity_group_id, $zone) {
		$sql = "SELECT COUNT(DISTINCT(pbf_entities.entity_id)) AS 'awaited_rpt_number', pbf_entities.entity_name, pbf_entities.entity_geozone_id FROM pbf_entities ";
		
		$sql .= "WHERE pbf_entities.entity_active = '1' AND pbf_entities.entity_type IN (" . implode ( ',', $asso_types ) . ") AND pbf_entities.entity_geozone_id IN (" . implode ( ',', $zone ) . ") AND pbf_entities.entity_pbf_group_id ='" . $entity_group_id . "'";
		
		return $this->db->query ( $sql )->result_array ();
	}
	
	// --
	function quarterly_consolidated_resumee_for_c1_zones($datafile_quarter, $datafile_year, $asso_types, $entity_group_id, $zone) {
		$sql = "SELECT pbf_entities.entity_id,IF(pbf_entitytypes.entity_type_abbrev='' OR pbf_entitytypes.entity_type_abbrev IS NULL,pbf_entities.entity_name,CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev)) AS entity_name,SUM(pbf_datafiledetails.indicator_montant) AS 'tot_subsides',pbf_entities.entity_class,IF(((pbf_entitiestime.entity_type IS NOT NULL) OR (pbf_entitiestime.entity_type != '0')),pbf_entitiestime.entity_type, pbf_entities.entity_type),IF(((pbf_entitiestime.entity_pbf_group_id IS NOT NULL) (OR pbf_entitiestime.entity_pbf_group_id != '0')),pbf_entitiestime.entity_pbf_group_id, pbf_entities.entity_pbf_group_id),pbf_entities.entity_pop,pbf_entities.entity_pop_year FROM pbf_entities LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type AND pbf_entitytypes.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id=pbf_entities.entity_id) LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) LEFT JOIN pbf_datafiledetails ON (pbf_datafiledetails.datafile_id=pbf_datafile.datafile_id) LEFT JOIN pbf_indicatorsfileypes ON (pbf_indicatorsfileypes.indicator_id=pbf_datafiledetails.indicator_id AND pbf_indicatorsfileypes.filetype_id=pbf_filetypes.filetype_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_entitiestime ON ((pbf_entitiestime.entity_id = pbf_entities.entity_id) AND (LAST_DAY('" . $datafile_year . "-" . ($datafile_quarter * 3) . "-1') BETWEEN pbf_entitiestime.use_from AND pbf_entitiestime.use_to)) LEFT JOIN pbf_entitygroups ON (pbf_entitygroups.entity_group_id=pbf_entities.entity_pbf_group_id) WHERE pbf_entities.entity_class IN (1) AND IF(pbf_entitiestime.entity_active IS NOT NULL,pbf_entitiestime.entity_active, pbf_entities.entity_active) ='1' AND pbf_indicatorsfileypes.quality_associated='1' AND pbf_lookups.lookup_title='Quantity' AND pbf_datafile.datafile_quarter='" . $datafile_quarter . "' AND pbf_datafile.datafile_year='" . $datafile_year . "' AND pbf_entities.entity_pbf_group_id='" . $entity_group_id . "'  AND pbf_entities.entity_type IN (" . implode ( ',', $asso_types ) . ") AND pbf_entities.entity_geozone_id IN (" . implode ( ',', $zone ) . ")  AND (LAST_DAY(CONCAT('" . $datafile_year . "-'," . "pbf_datafile.datafile_month" . ",'-1')) BETWEEN pbf_indicatorsfileypes.use_from AND pbf_indicatorsfileypes.use_to) GROUP BY pbf_entities.entity_id ORDER BY entity_name";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_quarterly_consolidated_resumee($datafile_quarter, $datafile_year, $entity_geozone_id, $computation_entity_ass_group_id) {
		$months = $this->pbf->get_monthsBy_quarter ( $datafile_quarter );
		
		$entity_geozone_id = empty ( $entity_geozone_id ) ? "" : " AND pbf_entities.entity_geozone_id='" . $entity_geozone_id . "'";
		
		$sql = "SELECT pbf_entities.entity_id,IF(pbf_entitytypes.entity_type_abbrev='' OR pbf_entitytypes.entity_type_abbrev IS NULL,pbf_entities.entity_name,CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev)) AS entity_name,SUM(pbf_datafiledetails.indicator_montant) AS 'tot_subsides',pbf_entities.entity_class,IF(((pbf_entitiestime.entity_type IS NOT NULL) OR (pbf_entitiestime.entity_type != '0')),pbf_entitiestime.entity_type, pbf_entities.entity_type),IF(((pbf_entitiestime.entity_pbf_group_id IS NOT NULL) AND (pbf_entitiestime.entity_pbf_group_id != '0')),pbf_entitiestime.entity_pbf_group_id, pbf_entities.entity_pbf_group_id) FROM pbf_entities LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type AND pbf_entitytypes.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_entitygroups ON (pbf_entitygroups.entity_group_id=pbf_entities.entity_pbf_group_id) LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id=pbf_entities.entity_id) LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) LEFT JOIN pbf_datafiledetails ON (pbf_datafiledetails.datafile_id=pbf_datafile.datafile_id) LEFT JOIN pbf_indicatorsfileypes ON (pbf_indicatorsfileypes.indicator_id=pbf_datafiledetails.indicator_id AND pbf_indicatorsfileypes.filetype_id=pbf_filetypes.filetype_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_entitiestime ON ((pbf_entitiestime.entity_id = pbf_entities.entity_id) AND (LAST_DAY('" . $datafile_year . "-" . ($datafile_quarter * 3) . "-1') BETWEEN pbf_entitiestime.use_from AND pbf_entitiestime.use_to) ) WHERE pbf_entities.entity_class IN (1) AND pbf_entities.entity_active ='1' AND pbf_indicatorsfileypes.quality_associated='1' AND pbf_lookups.lookup_title='Quantity' AND pbf_datafile.datafile_quarter='" . $datafile_quarter . "' AND pbf_datafile.datafile_year='" . $datafile_year . "' AND (LAST_DAY('" . $datafile_year . "-" . $months [2] . "-1') BETWEEN pbf_indicatorsfileypes.use_from AND pbf_indicatorsfileypes.use_to) AND pbf_entities.entity_pbf_group_id='" . $computation_entity_ass_group_id . "' " . $entity_geozone_id . " AND IF(pbf_entitiestime.entity_active IS NOT NULL,pbf_entitiestime.entity_active, pbf_entities.entity_active) ='1' GROUP BY pbf_entities.entity_id ORDER BY entity_name";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_quarterly_consolidated_info($postvars) {
		$months = $this->pbf->get_monthsBy_quarter ( $postvars ['datafile_quarter'] );
		
		$sql = "SELECT pbf_entities.entity_id,IF(pbf_entitytypes.entity_type_abbrev='' OR pbf_entitytypes.entity_type_abbrev IS NULL,pbf_entities.entity_name,CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev)) AS entity_name";
		
		foreach ( $months as $month ) {
			
			$sql .= ",SUM(IF(pbf_datafile.datafile_month='" . $month . "' AND (LAST_DAY('" . $postvars ['datafile_year'] . "-" . $month . "-1') BETWEEN pbf_indicatorsfileypes.use_from AND pbf_indicatorsfileypes.use_to),pbf_datafiledetails.indicator_montant,0)) AS '" . $month . "'";
		}
		
		$sql .= ",pbf_entities.entity_class,IF(((pbf_entitiestime.entity_type IS NOT NULL) OR (pbf_entitiestime.entity_type != '0')),pbf_entitiestime.entity_type, pbf_entities.entity_type),IF(((pbf_entitiestime.entity_pbf_group_id IS NOT NULL) AND (pbf_entitiestime.entity_pbf_group_id != '0')),pbf_entitiestime.entity_pbf_group_id, pbf_entities.entity_pbf_group_id) AS entity_pbf_group_id FROM pbf_entities LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type AND pbf_entitytypes.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id=pbf_entities.entity_id) LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) LEFT JOIN pbf_datafiledetails ON (pbf_datafiledetails.datafile_id=pbf_datafile.datafile_id) LEFT JOIN pbf_indicatorsfileypes ON (pbf_indicatorsfileypes.indicator_id=pbf_datafiledetails.indicator_id AND pbf_indicatorsfileypes.filetype_id=pbf_filetypes.filetype_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_entitiestime ON ((pbf_entitiestime.entity_id = pbf_entities.entity_id) AND (LAST_DAY('" . $postvars ['datafile_year'] . "-" . ($postvars ['datafile_quarter'] * 3) . "-1') BETWEEN pbf_entitiestime.use_from AND pbf_entitiestime.use_to)) LEFT JOIN pbf_entitygroups ON (pbf_entitygroups.entity_group_id=IF(((pbf_entitiestime.entity_pbf_group_id IS NOT NULL) AND (pbf_entitiestime.entity_pbf_group_id != '0')),pbf_entitiestime.entity_pbf_group_id, pbf_entities.entity_pbf_group_id)) WHERE pbf_entities.entity_geozone_id='" . $postvars ['entity_geozone_id'] . "' AND pbf_entities.entity_class IN (1) AND IF(pbf_entitiestime.entity_active IS NOT NULL,pbf_entitiestime.entity_active, pbf_entities.entity_active) ='1' AND pbf_indicatorsfileypes.quality_associated='1' AND pbf_filetypes.filetype_id IN(1,2) AND pbf_datafile.datafile_year='" . $postvars ['datafile_year'] . "' GROUP BY pbf_entities.entity_id ORDER BY entity_name";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_quarterly_consolidated_info_all($postvars) {
		$months = $this->pbf->get_monthsBy_quarter ( $postvars ['datafile_quarter'] );
		
		$regional_bind = (empty ( $postvars ['published_id'] ) ? "" : " AND pbf_entities.entity_geozone_id IN (" . implode ( ',', $postvars ['published_id'] ) . ") ");
		
		$sql = "SELECT pbf_entities.entity_id,pbf_entities.entity_name,pbf_geozones.geozone_id,SUM(IF(pbf_datafile.datafile_month IN (" . implode ( ',', $months ) . ") AND pbf_lookups.lookup_title='Quantity',datafile_total,0)) AS 'tot_subsidies' FROM pbf_entities LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id=pbf_entities.entity_id AND pbf_datafile.datafile_year='" . $postvars ['datafile_year'] . "') LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id)  LEFT JOIN pbf_geozones ON pbf_geozones.geozone_id = pbf_entities.entity_geozone_id LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_entitiestime ON ((pbf_entitiestime.entity_id = pbf_entities.entity_id) AND (LAST_DAY('" . $postvars ['datafile_year'] . "-" . ($postvars ['datafile_quarter'] * 3) . "-1') BETWEEN pbf_entitiestime.use_from AND pbf_entitiestime.use_to)) WHERE pbf_entities.entity_class IN (1) AND pbf_lookups.lookup_title='Quantity' AND IF(pbf_entitiestime.entity_active IS NOT NULL,pbf_entitiestime.entity_active, pbf_entities.entity_active) ='1' " . $regional_bind . "GROUP BY pbf_entities.entity_id";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_quarterly_validation_info_all($postvars) {
		$months = $this->pbf->get_monthsBy_quarter ( $postvars ['datafile_quarter'] );
		
		$regional_bind = (empty ( $postvars ['validation_id'] ) ? "" : " AND pbf_entities.entity_geozone_id IN (" . implode ( ',', $postvars ['validation_id'] ) . ") ");
		
		$sql = "SELECT pbf_entities.entity_id,pbf_entities.entity_name,pbf_geozones.geozone_id FROM pbf_entities LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id=pbf_entities.entity_id AND pbf_datafile.datafile_year='" . $postvars ['datafile_year'] . "') LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id)  LEFT JOIN pbf_geozones ON pbf_geozones.geozone_id = pbf_entities.entity_geozone_id LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_entitiestime ON ((pbf_entitiestime.entity_id = pbf_entities.entity_id) AND (LAST_DAY('" . $postvars ['datafile_year'] . "-" . ($postvars ['datafile_quarter'] * 3) . "-1') BETWEEN pbf_entitiestime.use_from AND pbf_entitiestime.use_to)) WHERE pbf_entities.entity_class IN (1)  AND IF(pbf_entitiestime.entity_active IS NOT NULL,pbf_entitiestime.entity_active, pbf_entities.entity_active) ='1' " . $regional_bind . "GROUP BY pbf_entities.entity_id";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_quarterly_validation_reg_info_all($postvars) {
		$months = $this->pbf->get_monthsBy_quarter ( $postvars ['datafile_quarter'] );
		
		$regional_bind = (empty ( $postvars ['validation_reg_id'] ) ? "" : " AND pbf_entities.entity_geozone_id IN (" . implode ( ',', $postvars ['validation_reg_id'] ) . ") ");
		
		$sql = "SELECT pbf_entities.entity_id,pbf_entities.entity_name,pbf_geozones.geozone_id FROM pbf_entities LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id=pbf_entities.entity_id AND pbf_datafile.datafile_year='" . $postvars ['datafile_year'] . "') LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id)  LEFT JOIN pbf_geozones ON pbf_geozones.geozone_id = pbf_entities.entity_geozone_id LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_entitiestime ON ((pbf_entitiestime.entity_id = pbf_entities.entity_id) AND (LAST_DAY('" . $postvars ['datafile_year'] . "-" . ($postvars ['datafile_quarter'] * 3) . "-1') BETWEEN pbf_entitiestime.use_from AND pbf_entitiestime.use_to)) WHERE pbf_entities.entity_class IN (1) AND IF(pbf_entitiestime.entity_active IS NOT NULL,pbf_entitiestime.entity_active, pbf_entities.entity_active) ='1' " . $regional_bind . "GROUP BY pbf_entities.entity_id";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_indicator_id($order, $filetype) {
		$sql = "SELECT pbf_indicatorsfileypes.indicator_id FROM pbf_indicatorsfileypes WHERE pbf_indicatorsfileypes.filetype_id='" . $filetype . "' AND pbf_indicatorsfileypes.order='" . $order . "'";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_quarterly_payment_order_info($postvars) {
		$months = $this->pbf->get_monthsBy_quarter ( $postvars ['datafile_quarter'] );
		
		$sql = "(SELECT pbf_entities.entity_id,pbf_entities.entity_class,IF(((pbf_entitiestime.entity_type IS NOT NULL) OR (pbf_entitiestime.entity_type != '0')),pbf_entitiestime.entity_type, pbf_entities.entity_type),IF(((pbf_entitiestime.entity_pbf_group_id IS NOT NULL) AND (pbf_entitiestime.entity_pbf_group_id != '0')),pbf_entitiestime.entity_pbf_group_id, pbf_entities.entity_pbf_group_id) AS entity_pbf_group_id,pbf_geozones.geozone_id,pbf_geozones.geozone_name,IF(pbf_entitytypes.entity_type_abbrev='' OR pbf_entitytypes.entity_type_abbrev IS NULL,pbf_entities.entity_name,CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev)) AS entity_name,SUM(IF(pbf_datafile.datafile_month IN (" . implode ( ',', $months ) . ") AND pbf_lookups.lookup_title='Quantity',datafile_total,0)) AS 'tot_subsidies',SUM(IF(pbf_datafile.datafile_month='" . $months [2] . "' AND pbf_lookups.lookup_title='Quality',datafile_total,0)) AS score_quality,pbf_bks.bank_name,pbf_banks.bank_name AS bank_branch_name,pbf_entities.entity_bank_acc FROM pbf_entities LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type AND pbf_entitytypes.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id=pbf_entities.entity_id AND pbf_datafile.datafile_year='" . $postvars ['datafile_year'] . "') LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_banks ON (pbf_banks.bank_id=pbf_entities.entity_bank_id) LEFT JOIN pbf_banks pbf_bks ON (pbf_bks.bank_id=pbf_banks.bank_parent_id) LEFT JOIN pbf_entitiestime ON ((pbf_entitiestime.entity_id = pbf_entities.entity_id) AND (LAST_DAY('" . $postvars ['datafile_year'] . "-" . ($postvars ['datafile_quarter'] * 3) . "-1') BETWEEN pbf_entitiestime.use_from AND pbf_entitiestime.use_to))
		  WHERE pbf_entities.entity_class IN (1) AND IF(pbf_entitiestime.entity_active IS NOT NULL,pbf_entitiestime.entity_active, pbf_entities.entity_active) ='1' GROUP BY pbf_entities.entity_id)

		UNION 
		
		(SELECT pbf_entities.entity_id,pbf_entities.entity_class,IF(((pbf_entitiestime.entity_type IS NOT NULL) OR (pbf_entitiestime.entity_type != '0')),pbf_entitiestime.entity_type, pbf_entities.entity_type),IF(((pbf_entitiestime.entity_pbf_group_id IS NOT NULL) AND (pbf_entitiestime.entity_pbf_group_id != '0')),pbf_entitiestime.entity_pbf_group_id, pbf_entities.entity_pbf_group_id),pbf_geozones.geozone_id,pbf_geozones.geozone_name,IF(pbf_entitytypes.entity_type_abbrev='' OR pbf_entitytypes.entity_type_abbrev IS NULL,pbf_entities.entity_name,CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev)) AS entity_name,pbf_budget.budget_value AS 'tot_subsidies',SUM(IF(pbf_datafile.datafile_month='" . $months [2] . "' AND pbf_lookups.lookup_title='Quality',datafile_total,0)) AS score_quality,pbf_bks.bank_name,pbf_banks.bank_name AS bank_branch_name,pbf_entities.entity_bank_acc FROM pbf_entities LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type AND pbf_entitytypes.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id=pbf_entities.entity_id AND pbf_datafile.datafile_year='" . $postvars ['datafile_year'] . "') LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_budget ON (pbf_budget.entity_type_id=pbf_entities.entity_type AND pbf_budget.entity_geozone_id=pbf_entities.entity_geozone_id AND pbf_budget.budget_quarter=pbf_datafile.datafile_quarter AND pbf_budget.budget_year=datafile_year) LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_banks ON (pbf_banks.bank_id=pbf_entities.entity_bank_id) LEFT JOIN pbf_banks pbf_bks ON (pbf_bks.bank_id=pbf_banks.bank_parent_id) LEFT JOIN pbf_entitiestime ON ((pbf_entitiestime.entity_id = pbf_entities.entity_id) AND (LAST_DAY('" . $postvars ['datafile_year'] . "-" . ($postvars ['datafile_quarter'] * 3) . "-1') BETWEEN pbf_entitiestime.use_from AND pbf_entitiestime.use_to)) WHERE pbf_entities.entity_class IN (2) AND IF(pbf_entitiestime.entity_active IS NOT NULL,pbf_entitiestime.entity_active, pbf_entities.entity_active) ='1' GROUP BY pbf_entities.entity_id) 
		
		ORDER BY bank_name,bank_branch_name,entity_class,entity_name";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_monthly_payment_order_info($postvars) {
		$this->load->model ( 'geo_mdl' );
		
		$zones_array = $this->geo_mdl->get_zones_by_parent ( $postvars ['region'] );
		foreach ( $zones_array as $z ) {
			$zones [] = $z ['geozone_id'];
		}
		
		$bank_info = isset ( $postvars ['bank_id'] ) ? "  AND pbf_banks.bank_parent_id = '" . $postvars ['bank_id'] . "' " : "";
		
		$regional_bind = (empty ( $postvars ['published_id'] ) ? "" : " AND pbf_entities.entity_geozone_id IN (" . implode ( $postvars ['published_id'] ) . ") ");
		
		$month = $postvars ['datafile_month'];
		// check if last month of quarter to add quality.
		if (in_array ( $month, array (
				3,
				6,
				9,
				12 
		) )) {
			$last_month_quarter = 'true';
		} else {
			$last_month_quarter = 'false';
		}
		
		$sql = "(SELECT pbf_entities.entity_id,pbf_entities.entity_class,IF(((pbf_entitiestime.entity_type IS NOT NULL) OR (pbf_entitiestime.entity_type != '0')),pbf_entitiestime.entity_type, pbf_entities.entity_type),IF(((pbf_entitiestime.entity_pbf_group_id IS NOT NULL) OR (pbf_entitiestime.entity_pbf_group_id != '0')),pbf_entitiestime.entity_pbf_group_id, pbf_entities.entity_pbf_group_id),pbf_geozones.geozone_id,pbf_geozones.geozone_name,IF(pbf_entitytypes.entity_type_abbrev='' OR pbf_entitytypes.entity_type_abbrev IS NULL,pbf_entities.entity_name,CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev)) AS entity_name,IF(pbf_datafile.datafile_month IN (" . $month . ") AND pbf_lookups.lookup_title='Quantity',pbf_datafile.datafile_total,0) AS 'tot_subsidies',IF(pbf_datafile.datafile_month='" . $month . "' AND " . $last_month_quarter . "='true' AND pbf_lookups.lookup_title='Quality',pbf_datafile.datafile_total,0) AS score_quality,pbf_bks.bank_name,pbf_banks.bank_name AS bank_branch_name,pbf_entities.entity_bank_acc FROM pbf_entities LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type AND pbf_entitytypes.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id=pbf_entities.entity_id AND pbf_datafile.datafile_year='" . $postvars ['datafile_year'] . "') LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_banks ON (pbf_banks.bank_id=pbf_entities.entity_bank_id) LEFT JOIN pbf_banks pbf_bks ON (pbf_bks.bank_id=pbf_banks.bank_parent_id) 
		   LEFT JOIN pbf_entitiestime ON ((pbf_entitiestime.entity_id = pbf_entities.entity_id) AND (LAST_DAY('" . $postvars ['datafile_year'] . "-" . ($month) . "-1') BETWEEN pbf_entitiestime.use_from AND pbf_entitiestime.use_to)) WHERE pbf_entities.entity_class IN (1) AND pbf_entities.entity_geozone_id IN (" . implode ( ',', $zones ) . ") AND IF(pbf_entitiestime.entity_active IS NOT NULL,pbf_entitiestime.entity_active, pbf_entities.entity_active) ='1' " . $bank_info . $regional_bind . " GROUP BY pbf_entities.entity_id)
		
		UNION
		
		(SELECT pbf_entities.entity_id,pbf_entities.entity_class,IF(((pbf_entitiestime.entity_type IS NOT NULL) OR (pbf_entitiestime.entity_type != '0')),pbf_entitiestime.entity_type, pbf_entities.entity_type),IF(((pbf_entitiestime.entity_pbf_group_id IS NOT NULL) AND (pbf_entitiestime.entity_pbf_group_id != '0')),pbf_entitiestime.entity_pbf_group_id, pbf_entities.entity_pbf_group_id),pbf_geozones.geozone_id,pbf_geozones.geozone_name,IF(pbf_entitytypes.entity_type_abbrev='' OR pbf_entitytypes.entity_type_abbrev IS NULL,pbf_entities.entity_name,CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev)) AS entity_name,pbf_budget.budget_value AS 'tot_subsidies',IF(pbf_datafile.datafile_month='" . $month . "' AND " . $last_month_quarter . "='true' AND pbf_lookups.lookup_title='Quality',pbf_datafile.datafile_total,0) AS score_quality,pbf_bks.bank_name,pbf_banks.bank_name AS bank_branch_name,pbf_entities.entity_bank_acc FROM pbf_entities LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type AND pbf_entitytypes.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id=pbf_entities.entity_id AND pbf_datafile.datafile_year='" . $postvars ['data_year'] . "') LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_budget ON (pbf_budget.entity_type_id=pbf_entities.entity_type AND pbf_budget.entity_geozone_id=pbf_entities.entity_geozone_id AND pbf_budget.budget_quarter=pbf_datafile.datafile_quarter AND pbf_budget.budget_year=datafile_year) LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_banks ON (pbf_banks.bank_id=pbf_entities.entity_bank_id) LEFT JOIN pbf_banks pbf_bks ON (pbf_bks.bank_id=pbf_banks.bank_parent_id) AND (LAST_DAY('" . $postvars ['datafile_year'] . "-" . ($month) . "-1') BETWEEN pbf_entitiestime.use_from AND pbf_entitiestime.use_to)) WHERE pbf_entities.entity_class IN (2)  AND pbf_entities.entity_geozone_id IN (" . implode ( ',', $zones ) . ") AND IF(pbf_entitiestime.entity_active IS NOT NULL,pbf_entitiestime.entity_active, pbf_entities.entity_active) ='1' " . $bank_info . $regional_bind . " GROUP BY pbf_entities.entity_id)
		
		ORDER BY bank_name,bank_branch_name,entity_class,entity_name";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_quarterly_ecd_info($postvars) {
		$sql = "SELECT pbf_indicators.indicator_id,pbf_indicatorstranslations.indicator_title,pbf_datafiledetails.indicator_verified_value,pbf_datafiledetails.indicator_validated_value,pbf_datafiledetails.indicator_tarif,pbf_datafiledetails.indicator_montant,pbf_datafile.datafile_total,pbf_budget.budget_value FROM pbf_indicators LEFT JOIN pbf_datafiledetails ON (pbf_datafiledetails.indicator_id=pbf_indicators.indicator_id) LEFT JOIN pbf_datafile ON (pbf_datafile.datafile_id=pbf_datafiledetails.datafile_id) LEFT JOIN pbf_indicatorsfileypes ON (pbf_indicatorsfileypes.indicator_id=pbf_indicators.indicator_id AND pbf_indicatorsfileypes.filetype_id=pbf_datafile.filetype_id) LEFT JOIN pbf_entities ON (pbf_entities.entity_id=pbf_datafile.entity_id) LEFT JOIN pbf_budget ON (pbf_budget.entity_type_id=pbf_entities.entity_type AND pbf_budget.entity_geozone_id=pbf_entities.entity_geozone_id AND pbf_budget.budget_quarter=pbf_datafile.datafile_quarter AND pbf_budget.budget_year=datafile_year) LEFT JOIN pbf_indicatorstranslations ON (pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id)  WHERE pbf_indicatorsfileypes.filetype_id IN (11) AND pbf_datafile.entity_id='" . $postvars ['entity_id'] . "' AND pbf_entities.entity_geozone_id='" . $postvars ['entity_geozone_id'] . "' AND pbf_datafile.datafile_quarter ='" . $postvars ['datafile_quarter'] . "' AND datafile_year = '" . $postvars ['datafile_year'] . "' AND pbf_indicatorstranslations.indicator_language = '" . $this->config->item ( 'language_abbr' ) . "' ORDER BY pbf_indicatorsfileypes.order";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_indicator_evolution($postvars) {
		$sql_append = "";
		$pop_params = ",SUM(pbf_computed_routine_data.parent_geozone_pop) AS pop,pbf_computed_routine_data.parent_geozone_pop_year AS pop_year";
		
		if (! empty ( $postvars ['level_0'] )) {
			
			$sql_append .= " AND pbf_computed_routine_data.geozone_parentid = '" . $postvars ['level_0'] . "' ";
			$pop_params = ",SUM(pbf_computed_routine_data.parent_geozone_pop) AS pop,pbf_computed_routine_data.parent_geozone_pop_year AS pop_year";
		}
		
		if (! empty ( $postvars ['entity_geozone_id'] )) {
			
			$sql_append .= " AND pbf_computed_routine_data.geozone_id = '" . $postvars ['entity_geozone_id'] . "' ";
			$pop_params = ",SUM(pbf_computed_routine_data.geozone_pop) AS pop,pbf_computed_routine_data.geozone_pop_year AS pop_year";
		}
		
		if (! empty ( $postvars ['entity_id'] )) {
			
			$sql_append .= " AND pbf_computed_routine_data.entity_id = '" . $postvars ['entity_id'] . "' ";
			$pop_params = ",SUM(pbf_computed_routine_data.entity_pop) AS pop,pbf_computed_routine_data.entity_pop_year AS pop_year";
		}
		
		if (! empty ( $postvars ['datafile_year'] )) {
			
			$sql_append .= " AND pbf_computed_routine_data.datafile_year = '" . $postvars ['datafile_year'] . "' ";
		}
		
		$sql = "SELECT pbf_computed_routine_data.indicator_id,pbf_computed_routine_data.indicator_title,pbf_computed_routine_data.indicator_abbrev,SUM(pbf_computed_routine_data.indicator_verified_value) AS indicator_value,pbf_indicators.indicator_target,pbf_computed_routine_data.datafile_month,pbf_computed_routine_data.datafile_year " . $pop_params . " FROM pbf_computed_routine_data LEFT JOIN pbf_indicators ON (pbf_indicators.indicator_id=pbf_computed_routine_data.indicator_id) WHERE pbf_computed_routine_data.indicator_id='" . $postvars ['data_element'] . "' " . $sql_append . " GROUP BY pbf_computed_routine_data.datafile_month,pbf_computed_routine_data.datafile_year ORDER BY pbf_computed_routine_data.datafile_year,pbf_computed_routine_data.datafile_month";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_quarterly_production($postvars) {
		$sql = "SELECT SUM(pbf_datafile.datafile_total) AS datafile_total FROM pbf_datafile LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id = pbf_datafile.filetype_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') WHERE pbf_datafile.entity_id='" . $postvars ['entity_id'] . "' AND pbf_datafile.datafile_quarter ='" . $postvars ['datafile_quarter'] . "' AND pbf_datafile.datafile_year='" . $postvars ['datafile_year'] . "' AND pbf_lookups.lookup_title='Quantity'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_quarterly_entity_report($postvars) {
		$months = $this->pbf->get_monthsBy_quarter ( $postvars ['datafile_quarter'] );
		
		$record_set = array ();
		
		$sql = "SELECT pbf_indicatorsfileypes.order,pbf_indicatorstranslations.indicator_title,FORMAT(SUM(pbf_datafiledetails.indicator_verified_value),0) AS verified_value,FORMAT(pbf_indicatorsfileypes.default_tarif,0) AS default_tarif,FORMAT(SUM(pbf_datafiledetails.indicator_montant),0) AS indicator_montant,pbf_lkp_indicategory.lookup_title AS indicator_category,pbf_indicatorsfileypes.quality_associated FROM pbf_indicators LEFT JOIN pbf_indicatorsfileypes ON (pbf_indicators.indicator_id = pbf_indicatorsfileypes.indicator_id) LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_indicatorsfileypes.filetype_id) LEFT JOIN pbf_lookups ON (pbf_filetypes.filetype_contenttype=pbf_lookups.lookup_id AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_datafile ON (pbf_datafile.filetype_id=pbf_filetypes.filetype_id) LEFT JOIN pbf_datafiledetails ON (pbf_datafiledetails.datafile_id=pbf_datafile.datafile_id AND pbf_datafiledetails.indicator_id=pbf_indicators.indicator_id) LEFT JOIN pbf_lookups pbf_lkp_indicategory ON (pbf_lkp_indicategory.lookup_id=pbf_indicatorsfileypes.indicator_category_id AND pbf_lkp_indicategory.lookup_linkfile='indicator_category') LEFT JOIN pbf_indicatorstranslations ON (pbf_indicatorstranslations.indicator_id = pbf_indicators.indicator_id) WHERE pbf_lookups.lookup_title='Quantity' AND pbf_datafile.datafile_quarter = '" . $postvars ['datafile_quarter'] . "' AND pbf_datafile.datafile_year='" . $postvars ['datafile_year'] . "' AND pbf_datafile.entity_id='" . $postvars ['entity_id'] . "' AND pbf_indicatorstranslations.indicator_language = '" . $this->config->item ( 'language_abbr' ) . "' AND (LAST_DAY('" . $postvars ['datafile_year'] . "-" . $months [2] . "-1') BETWEEN pbf_indicatorsfileypes.use_from AND pbf_indicatorsfileypes.use_to) GROUP BY pbf_indicators.indicator_id ORDER BY pbf_indicatorsfileypes.order";
		
		$record_set ['list_quality'] = $this->get_quality_evaluation ( $postvars ['datafile_year'], $postvars ['datafile_quarter'], $postvars ['entity_id'] );
		
		$record_set ['list_quantity'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function get_monthly_entity_report($postvars, $lang) {
		$record_set = array ();
		
		$sql = "SELECT pbf_indicatorsfileypes.order,pbf_indicators.indicator_id AS indicator_id,pbf_indicatorstranslations.indicator_title AS indicator_title,FORMAT(pbf_datafiledetails.indicator_claimed_value,0) AS claimed_value,FORMAT(pbf_datafiledetails.indicator_validated_value,0) AS validated_value,FORMAT(pbf_datafiledetails.indicator_tarif,0) AS default_tarif,FORMAT(pbf_datafiledetails.indicator_montant,0) AS indicator_montant,pbf_lkp_indicategory.lookup_title AS indicator_category,pbf_indicatorsfileypes.quality_associated, pbf_indicatorsfileypes.filetype_id FROM pbf_indicators LEFT JOIN pbf_indicatorsfileypes ON (pbf_indicators.indicator_id = pbf_indicatorsfileypes.indicator_id) LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_indicatorsfileypes.filetype_id) LEFT JOIN pbf_lookups ON (pbf_filetypes.filetype_contenttype=pbf_lookups.lookup_id AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_datafile ON (pbf_datafile.filetype_id=pbf_filetypes.filetype_id) LEFT JOIN pbf_datafiledetails ON (pbf_datafiledetails.datafile_id=pbf_datafile.datafile_id AND pbf_datafiledetails.indicator_id=pbf_indicators.indicator_id) LEFT JOIN pbf_lookups pbf_lkp_indicategory ON (pbf_lkp_indicategory.lookup_id=pbf_indicatorsfileypes.indicator_category_id AND pbf_lkp_indicategory.lookup_linkfile='indicator_category') LEFT JOIN pbf_indicatorstranslations ON pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id WHERE pbf_lookups.lookup_title='Quantity' AND pbf_datafile.datafile_month = '" . $postvars ['datafile_month'] . "' AND pbf_datafile.datafile_year='" . $postvars ['datafile_year'] . "' AND pbf_datafile.entity_id='" . $postvars ['entity_id'] . "' AND pbf_indicatorstranslations.indicator_language ='" . $lang . "' AND (LAST_DAY(CONCAT('" . $postvars ['datafile_year'] . "-'," . "pbf_datafile.datafile_month" . ",'-1')) BETWEEN pbf_indicatorsfileypes.use_from AND pbf_indicatorsfileypes.use_to) GROUP BY pbf_indicators.indicator_id ORDER BY pbf_indicatorsfileypes.order";
		
		$record_set ['list_quantity'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function get_quality_evaluation($year, $quarter, $entity_id) {
		$sql = "SELECT MAX(pbf_datafile.datafile_total) AS datafile_total FROM pbf_datafile LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') WHERE  pbf_datafile.datafile_quarter = '" . $quarter . "' AND pbf_datafile.datafile_year = '" . $year . "' AND pbf_datafile.entity_id = '" . $entity_id . "' AND pbf_lookups.lookup_title='Quality'";
		return $this->db->query ( $sql )->row_array ();
	}

	function get_non_quality_assoc_tot($year, $quarter, $entity_id) {
		$sql = "SELECT SUM(pbf_datafiledetails.indicator_montant) AS indicator_montant FROM pbf_datafiledetails LEFT JOIN pbf_indicatorsfileypes ON (pbf_indicatorsfileypes.indicator_id=pbf_datafiledetails.indicator_id) LEFT JOIN  pbf_datafile ON (pbf_datafile.datafile_id=pbf_datafiledetails.datafile_id AND pbf_datafile.filetype_id=pbf_indicatorsfileypes.filetype_id) WHERE quality_associated!='1' AND pbf_datafile.datafile_quarter='" . $quarter . "' AND pbf_datafile.datafile_year='" . $year . "' AND pbf_datafile.entity_id='" . $entity_id . "'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_reports_conf($report_id) {
		return $this->db->get_where ( 'pbf_reporting', array (
				'report_id' => $report_id 
		) )->row_array ();
	}
	function save_config($report) {
		$report_id = $report ['report_id'];
		$rep = 0;
		if (empty ( $report ['report_id'] )) {
			
			$rep = $this->db->insert ( 'pbf_reporting', $report );
			$report_id = $this->db->insert_id ();
		} else {
			
			if ($report ['associated_filetypes'] == 'null') {
				unset ( $report ['associated_filetypes'] );
			}
			$rep = $this->db->update ( 'pbf_reporting', $report, array (
					'report_id' => $report_id 
			) );
		}
		
		$this->pbf->set_translation ( array (
				array (
						'text' => $report ['report_title'],
						'text_key' => 'reporting_key_' . $report_id 
				) 
		), 'report' );
		return $rep;
	}
	function del_conf($report_id) {
		return $this->db->delete ( 'pbf_reporting', array (
				'report_id' => $report_id 
		) );
	}
	function save_geo_report_config($report_id, $district_id) {
		$this->db->insert ( 'pbf_reporting_geozones', array (
				'pbf_reporting_id' => $report_id,
				'pbf_geozone_id' => $district_id 
		) );
		
		return $this->db->affected_rows () > 0;
	}
	function delete_geo_report_config($id) {
		$this->db->delete ( 'pbf_reporting_geozones', array (
				'pbf_reporting_id' => $id 
		) );
		
		return $this->db->affected_rows () > 0;
	}
	function geo_report_config_exists($id) {
		$query = $this->db->get_where ( 'pbf_reporting_geozones', array (
				'pbf_reporting_id' => $id 
		) );
		
		return $query->num_rows () > 0;
	}
	function get_report_districts($report_id) {
		$query = $this->db->get_where ( 'pbf_reporting_geozones', array (
				'pbf_reporting_id' => $report_id 
		) );
		
		return $query->result_array ();
	}
	/* method for the new report */
	function get_quarterly_entity_report_indicator($postvars, $EntityId) {
		$record_set = array ();
		$months = $this->pbf->get_monthsBy_quarter ( $postvars ['datafile_quarter'] );
		
		$sql = "SELECT pbf_indicatorsfileypes.order,pbf_indicators.indicator_id,pbf_indicatorstranslations.indicator_title AS indicator_title";

		foreach ( $months as $month ) {
			
			$sql .= ",SUM(IF(pbf_datafile.datafile_month='" . $month . "' AND (LAST_DAY('" . $postvars ['datafile_year'] . "-" . $month . "-1') BETWEEN pbf_indicatorsfileypes.use_from AND pbf_indicatorsfileypes.use_to),pbf_datafiledetails.indicator_montant,0)) AS '" . $month . "'";
		}
		
		$sql .= ",FORMAT(SUM(pbf_datafiledetails.indicator_verified_value),0) AS verified_value,FORMAT(pbf_indicatorsfileypes.default_tarif,0) AS default_tarif,FORMAT(SUM(pbf_datafiledetails.indicator_montant),0) AS indicator_montant,pbf_lkp_indicategory.lookup_title AS indicator_category,pbf_indicatorsfileypes.quality_associated FROM pbf_indicators LEFT JOIN pbf_indicatorsfileypes ON (pbf_indicators.indicator_id = pbf_indicatorsfileypes.indicator_id) LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_indicatorsfileypes.filetype_id) LEFT JOIN pbf_lookups ON (pbf_filetypes.filetype_contenttype=pbf_lookups.lookup_id AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_datafile ON (pbf_datafile.filetype_id=pbf_filetypes.filetype_id) LEFT JOIN pbf_datafiledetails ON (pbf_datafiledetails.datafile_id=pbf_datafile.datafile_id AND pbf_datafiledetails.indicator_id=pbf_indicators.indicator_id) LEFT JOIN pbf_lookups pbf_lkp_indicategory ON (pbf_lkp_indicategory.lookup_id=pbf_indicatorsfileypes.indicator_category_id AND pbf_lkp_indicategory.lookup_linkfile='indicator_category') LEFT JOIN pbf_indicatorstranslations ON pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id WHERE pbf_lookups.lookup_title='Quantity' AND pbf_datafile.datafile_quarter = '" . $postvars ['datafile_quarter'] . "' AND pbf_datafile.datafile_year='" . $postvars ['datafile_year'] . "' AND pbf_datafile.entity_id='" . $EntityId . "' AND pbf_indicatorstranslations.indicator_language = '" . $this->config->item ( 'language_abbr' ) . "' AND (LAST_DAY('" . $postvars ['datafile_year'] . "-" . $months [2] . "-1') BETWEEN pbf_indicatorsfileypes.use_from AND pbf_indicatorsfileypes.use_to)  GROUP BY pbf_indicators.indicator_id ORDER BY pbf_indicatorsfileypes.order ";

		
		$record_set ['list_quality'] = $this->get_quality_evaluation ( $postvars ['datafile_year'], $postvars ['datafile_quarter'], $EntityId );
		
		$record_set ['list_quantity'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}

	function get_quarterly_payment_entity_order_info($postvars, $entityid) {
		$months = $this->pbf->get_monthsBy_quarter ( $postvars ['datafile_quarter'] );
		
		$sql = "(SELECT pbf_entities.entity_id,pbf_entities.entity_class,IF(((pbf_entitiestime.entity_type IS NOT NULL) OR (pbf_entitiestime.entity_type != '0')),pbf_entitiestime.entity_type, pbf_entities.entity_type),IF(((pbf_entitiestime.entity_pbf_group_id IS NOT NULL) AND (pbf_entitiestime.entity_pbf_group_id != '0')),pbf_entitiestime.entity_pbf_group_id, pbf_entities.entity_pbf_group_id),pbf_geozones.geozone_id,pbf_geozones.geozone_name,IF(pbf_entitytypes.entity_type_abbrev='' OR pbf_entitytypes.entity_type_abbrev IS NULL,pbf_entities.entity_name,CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev)) AS entity_name,SUM(IF(pbf_datafile.datafile_month IN (" . implode ( ',', $months ) . ") AND pbf_lookups.lookup_title='Quantity',datafile_total,0)) AS 'tot_subsidies',SUM(IF(pbf_datafile.datafile_month='" . $months [2] . "' AND pbf_lookups.lookup_title='Quality',datafile_total,0)) AS score_quality,pbf_bks.bank_name,pbf_banks.bank_name AS bank_branch_name,pbf_entities.entity_bank_acc FROM pbf_entities LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type AND pbf_entitytypes.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id=pbf_entities.entity_id AND pbf_datafile.datafile_year='" . $postvars ['datafile_year'] . "') LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_banks ON (pbf_banks.bank_id=pbf_entities.entity_bank_id) LEFT JOIN pbf_banks pbf_bks ON (pbf_bks.bank_id=pbf_banks.bank_parent_id) 
		  LEFT JOIN pbf_entitiestime ON ((pbf_entitiestime.entity_id = pbf_entities.entity_id) AND (LAST_DAY('" . $postvars ['datafile_year'] . "-" . ($postvars ['datafile_quarter'] * 3) . "-1') BETWEEN pbf_entitiestime.use_from AND pbf_entitiestime.use_to))
		  WHERE pbf_entities.entity_class IN (1) AND IF(pbf_entitiestime.entity_active IS NOT NULL,pbf_entitiestime.entity_active, pbf_entities.entity_active) ='1' AND pbf_entities.entity_id='" . $entityid . "'GROUP BY pbf_entities.entity_id)

		UNION 
		
		(SELECT pbf_entities.entity_id,pbf_entities.entity_class,IF(((pbf_entitiestime.entity_type IS NOT NULL) OR (pbf_entitiestime.entity_type != '0')),pbf_entitiestime.entity_type, pbf_entities.entity_type),IF(((pbf_entitiestime.entity_pbf_group_id IS NOT NULL) AND (pbf_entitiestime.entity_pbf_group_id != '0')),pbf_entitiestime.entity_pbf_group_id, pbf_entities.entity_pbf_group_id),pbf_geozones.geozone_id,pbf_geozones.geozone_name,IF(pbf_entitytypes.entity_type_abbrev='' OR pbf_entitytypes.entity_type_abbrev IS NULL,pbf_entities.entity_name,CONCAT(pbf_entities.entity_name,' ',pbf_entitytypes.entity_type_abbrev)) AS entity_name,pbf_budget.budget_value AS 'tot_subsidies',SUM(IF(pbf_datafile.datafile_month='" . $months [2] . "' AND pbf_lookups.lookup_title='Quality',datafile_total,0)) AS score_quality,pbf_bks.bank_name,pbf_banks.bank_name AS bank_branch_name,pbf_entities.entity_bank_acc FROM pbf_entities LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type AND pbf_entitytypes.entity_class_id=pbf_entities.entity_class) LEFT JOIN pbf_datafile ON (pbf_datafile.entity_id=pbf_entities.entity_id AND pbf_datafile.datafile_year='" . $postvars ['datafile_year'] . "') LEFT JOIN pbf_filetypes ON (pbf_filetypes.filetype_id=pbf_datafile.filetype_id) LEFT JOIN pbf_lookups ON (pbf_lookups.lookup_id=pbf_filetypes.filetype_contenttype AND pbf_lookups.lookup_linkfile='content_type') LEFT JOIN pbf_budget ON (pbf_budget.entity_type_id=pbf_entities.entity_type AND pbf_budget.entity_geozone_id=pbf_entities.entity_geozone_id AND pbf_budget.budget_quarter=pbf_datafile.datafile_quarter AND pbf_budget.budget_year=datafile_year) LEFT JOIN pbf_geozones ON (pbf_geozones.geozone_id=pbf_entities.entity_geozone_id) LEFT JOIN pbf_banks ON (pbf_banks.bank_id=pbf_entities.entity_bank_id) LEFT JOIN pbf_banks pbf_bks ON (pbf_bks.bank_id=pbf_banks.bank_parent_id) LEFT JOIN pbf_entitiestime ON ((pbf_entitiestime.entity_id = pbf_entities.entity_id) AND (LAST_DAY('" . $postvars ['datafile_year'] . "-" . ($postvars ['datafile_quarter'] * 3) . "-1') BETWEEN pbf_entitiestime.use_from AND pbf_entitiestime.use_to)) WHERE pbf_entities.entity_class IN (2) AND IF(pbf_entitiestime.entity_active IS NOT NULL,pbf_entitiestime.entity_active, pbf_entities.entity_active) ='1' AND pbf_entities.entity_id='" . $entityid . "' GROUP BY pbf_entities.entity_id) 
		
		ORDER BY bank_name,bank_branch_name,entity_class,entity_name";
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_report_id_for_fraude() {
		$report_title = "Rapport de Fraude";
		$sql = "SELECT report_id,report_title FROM pbf_reporting";
		$sql .= " WHERE report_title='" . $report_title . "'";
		return $this->db->query ( $sql )->result_array ();
	}
	function get_invoices($num = 0, $filters) {
		$record_set = array ();
		
		$user_id = $this->session->userdata ( 'user_id' );
		$user_group = $this->pbf->get_usergroup ( $user_id );
		
		$sql = "SELECT * FROM pbf_donors WHERE groupassociated_id='" . $user_group . "'";
		$donor_data = $this->db->query ( $sql )->row_array ();
		
		$sql_append = " WHERE 1=1 ";
		
		if (! empty ( $filters ['entity_name'] )) {
			
			$sql_append .= " AND (pbf_entities.entity_name LIKE '%" . $filters ['entity_name'] . "%' OR pbf_geozones.geozone_name LIKE '%" . $filters ['entity_name'] . "%' OR pbf_users.user_fullname LIKE '%" . $filters ['entity_name'] . "%' OR report_title LIKE '%" . $filters ['entity_name'] . "%')";
		}
		
		if (! empty ( $filters ['datafile_year'] )) {
			
			$sql_append .= " AND pbf_invoices.year='" . $filters ['datafile_year'] . "'";
		}
		
		if (! empty ( $filters ['filetype_id'] )) {
			
			$sql_append .= " AND pbf_invoices.reporting_id='" . $filters ['filetype_id'] . "'";
		}
		
		if (! empty ( $filters ['entity_id'] )) {
			
			$sql_append .= " AND pbf_invoices.entity_id='" . $filters ['entity_id'] . "'";
		}
		
		if (! empty ( $filters ['invoice_id'] )) {
			
			$sql_append .= " AND pbf_invoices.invoice_id='" . $filters ['invoice_id'] . "'";
		}
		if (! empty ( $donor_data ['donor_id'] )) {
			
			$sql_append .= " AND pbf_invoices.donnor_id='" . $donor_data ['donor_id'] . "'";
		}
		
		// Amelioration de la requête en ajoutant la date d'envoi de la facture
		$sql = "SELECT  invoice_id,report_title,entity_name,pbf_entitytypes.entity_type_abbrev,pbf_geozones.geozone_name,pbf_invoices.month,pbf_invoices.quarter,pbf_invoices.year,pbf_invoices.total_invoice,pbf_users.user_fullname,pbf_invoices.date,pbf_invoices.uptodate, pbf_invoices.sent_date  FROM pbf_invoices LEFT JOIN pbf_entities ON(pbf_invoices.entity_id=pbf_entities.entity_id AND pbf_entities.entity_active=1) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type)  LEFT JOIN pbf_geozones ON(pbf_invoices.zone_id=pbf_geozones.geozone_id AND pbf_geozones.geozone_active=1) LEFT JOIN pbf_users ON(pbf_invoices.author=pbf_users.user_id) LEFT JOIN pbf_reporting ON(pbf_invoices.reporting_id=pbf_reporting.report_id) " . $sql_append;
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT $num , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function get_invoice($invoice_id) {
		$record_set = array ();
		$sql_append = " WHERE invoice_id=$invoice_id ";
		
		$sql = "SELECT invoice_id,report_title,entity_name,pbf_entitytypes.entity_type_abbrev,pbf_entities.entity_id,pbf_geozones.geozone_name,pbf_invoices. 	month,pbf_invoices.quarter,pbf_invoices.year,pbf_invoices.total_invoice,pbf_users.user_fullname,pbf_invoices.date,pbf_invoices.uptodate,received_date,sent_date FROM pbf_invoices LEFT JOIN pbf_entities ON(pbf_invoices.entity_id=pbf_entities.entity_id AND pbf_entities.entity_active=1) LEFT JOIN pbf_entitytypes ON (pbf_entitytypes.entity_type_id=pbf_entities.entity_type)  LEFT JOIN pbf_geozones ON(pbf_invoices.zone_id=pbf_geozones.geozone_id AND pbf_geozones.geozone_active=1) LEFT JOIN pbf_users ON(pbf_invoices.author=pbf_users.user_id) LEFT JOIN pbf_reporting ON(pbf_invoices.reporting_id=pbf_reporting.report_id) " . $sql_append;
		
		$result = $this->db->query ( $sql )->row_array ();
		return $result;
	}
	
	// Fonction qui change la date d'envoi de la facture
	function update_date_invoice($invoice_id) {
		$date = date ( "Y-m-d" );
		$sql = "UPDATE pbf_invoices SET sent_date='" . $date . "' WHERE invoice_id=" . $invoice_id;
		
		return $this->db->simple_query ( $sql );
	}
	
	// Fonction qui change la date d'envoi de la facture
	function remove_date_invoice($invoice_id) {
		$sql = "UPDATE pbf_invoices SET sent_date = NULL WHERE invoice_id=" . $invoice_id;
		
		return $this->db->simple_query ( $sql );
	}
	
	function get_invoice_reports(){
		$query = $this->db->get_where('pbf_reporting',array('report_category'=>'invoice'));
		return $query->result_array();
	}
	
	function get_report_filetypes ( $report_id ){
		$query = $this->db->get_where ('pbf_reporting',array('report_id'=>$report_id));
		return $query->row_array();
	}
}