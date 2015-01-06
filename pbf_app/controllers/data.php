<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Data extends CI_Controller {
	function __construct() {
		parent::__construct ();
		
		$this->load->model ( 'cms_mdl' );
		$this->load->model ( 'budgets_mdl' );
		$this->load->model ( 'exports_mdl' );
		$this->lang->load ( 'budgets', $this->config->item ( 'language' ) );
		$this->lang->load ( 'front', $this->config->item ( 'language' ) );
		$this->lang->load ( 'indicators', $this->config->item ( 'language' ) );
		$this->lang->load ( 'popcible', $this->config->item ( 'language' ) );
		$this->lang->load ( 'hfrentities', $this->config->item ( 'language' ) );
		$this->load->library ( 'pearloader' );
		$this->pearloader->load ( 'Writer' );
	}
	function __convert_to_json($data) {
		$indicator = $data [$this->lang->line ( 'front_data' )];
		
		// $url = 'data/element/'.$indicator['pbf_data']['indicator_id'];
		$text = $this->pbf->extract_text ( $indicator );
		
		$url = $this->pbf->extract_url ( site_url ( 'data/element/' . $indicator ) );
		
		$data = array_slice ( $data, 1 );
		
		$categories = array_keys ( $data );
		
		$data_series = array ();
		
		foreach ( $data as $d ) {
			array_push ( $data_series, intval ( str_replace ( ',', '', $d ) ) );
		}
		
		$chart = array ();
		$credit = array ();
		
		$credit ['text'] = $this->lang->line ( 'more_details' );
		
		$credit ['href'] = $url;
		
		$chart ['series_name'] = $text;
		
		$chart ['categories'] = $categories;
		
		$chart ['data_series'] = $data_series;
		
		$chart ['credit'] = $credit;
		
		return json_encode ( $chart );
	}
	function chart_data($id) {
		if ($id != 5) { // temp should be last one
			
			$data = $this->pbf->get_data ( '', '', 'indicator_verified_value', 'Quantity' );
			$data_slice = array_slice ( $data ['pbf_data'] ['pbf_data_slice'], 1 );
			$data = $data_slice [$id];
		} else {
			// add money spend per hab
			$periods = $this->pbf->get_last_quarters ( $this->config->item ( 'num_period_display' ) );
			$totals = $this->pbf->get_totals_class ( $periods );
			
			$p = $this->cms_mdl->get_pop_tot ();
			$paybyperson = $totals [0];
			// unset($paybyperson['Entity class']);
			
			foreach ( $paybyperson as $pbpkey => $pbpval ) {
				if ($pbpkey != 'Entity class') {
					$paybyperson [$pbpkey] = round ( $pbpval * 100000 / $p ['tot'] );
					// $paybyperson[$pbpkey] = $pbpval/$p['tot'];
				}
			}
			// $paybyperson['INDICATOR'] = $paybyperson['Entity class'];
			unset ( $paybyperson ['Entity class'] );
			$data = $paybyperson;
		}
		
		echo $this->__convert_to_json ( $data );
	}
	function index() {
		if ($this->config->item ( 'debug' ))
			$this->output->enable_profiler ( TRUE );
		
		$this->lang->load ( 'geo' );
		
		$geo = $this->pbf->get_principal_geo_info ();
		
		// all zones
		
		$data = $this->pbf->get_data ( '', '', 'indicator_verified_value', 'Quantity' );
		
		// no parent pfb data because we're on the top level
		$data ['parent_pbf_data'] = array ();
		
		$national_quality_data = $this->pbf->get_quality_per_region ( '' );
		$data ['qualities'] = $national_quality_data;
		
		$data ['pbf_data'] ['pbf_data_graph'] = $data ['pbf_data'] ['pbf_data_slice'];
		
		$data ['graph_key'] = $data ['pbf_data'] ['pbf_data_graph'] [0] [0];
		
		$data ['geos_title'] = $this->lang->line ( 'geo_province' );
		
		$data ['map_render'] = $this->pbf->render_geozones ( $geo ['geo_id'] );
		
		$data ['front_main_nav'] = $this->pbf->get_front_menu ();
		
		$data ['top_quality_score'] = $this->pbf->get_top_score ();
		
		$data ['page_title'] = $this->lang->line ( 'front_map_title' );
		$data ['meta_description'] = $this->lang->line ( 'front_map_title' );
		
		$this->load->model ( 'cms_mdl' );
		$data ['logo'] = $this->cms_mdl->get_logo ();
		
		$data ['page'] = 'datahomepage';
		
		$periods = $this->pbf->get_last_quarters ( $this->config->item ( 'num_period_display' ) );
		
		$data ['class_totals'] = $this->pbf->get_totals_class ( $periods );
		
		if (! empty ( $data ['class_totals'] ))
			array_unshift ( $data ['class_totals'], array_keys ( $data ['class_totals'] [0] ) );
		
		$p = $this->cms_mdl->get_pop_tot ( 3 );
		
		$data ['pop'] = $p ['tot'];
		
		$data ['key_data'] = $this->pbf->get_keydata ( '' );
		
		$data ['real_time_result'] = $this->pbf->get_real_time_result ( '', $data ['pop'] );
		
		$data ['average_qual'] = $this->pbf->get_average_quality ();
		
		$payments = $this->pbf->get_payments_all ( '' );
		
		$money = explode ( ' ', $payments ['realtime'] ['sum_validated_value'] );
		
		if (! empty ( $payments )) {
			// $money = explode(' ', $data['received_payement']['realtime']['sum_validated_value']);
			$data ['received_payement'] ['realtime'] ['money_amount'] = $money [0];
			$data ['received_payement'] ['realtime'] ['money_local'] = $money [1];
			$data ['received_payement'] ['realtime'] ['money_usd'] = 'USD';
			$data ['received_payement'] ['realtime'] ['indicator_icon_file'] = $payments ['realtime'] ['indicator_icon_file'];
			
			$benin_cfa_exchange_rade = 478.41; // found on internet TODO change this
			$data ['received_payement'] ['realtime'] ['money_usd_amount'] = floatval ( str_replace ( $this->lang->line ( 'thousand_separator' ), '', $money [0] ) ) / $benin_cfa_exchange_rade;
			$data ['received_payement'] ['realtime'] ['per_capita'] = floatval ( str_replace ( $this->lang->line ( 'thousand_separator' ), '', $money [0] ) ) / intval ( $data ['pop'] );
			$data ['received_payement'] ['realtime'] ['per_capita_usd'] = $data ['received_payement'] ['realtime'] ['money_usd_amount'] / intval ( $data ['pop'] );
			
			$data ['received_payement'] ['realtime'] ['money_usd_amount'] = number_format ( $data ['received_payement'] ['realtime'] ['money_usd_amount'], 2, $this->lang->line ( 'decimal_separator' ), $this->lang->line ( 'thousand_separator' ) );
			$data ['received_payement'] ['realtime'] ['per_capita'] = number_format ( $data ['received_payement'] ['realtime'] ['per_capita'], 2, $this->lang->line ( 'decimal_separator' ), $this->lang->line ( 'thousand_separator' ) );
			$data ['received_payement'] ['realtime'] ['per_capita_usd'] = number_format ( $data ['received_payement'] ['realtime'] ['per_capita_usd'], 2, $this->lang->line ( 'decimal_separator' ), $this->lang->line ( 'thousand_separator' ) );
			
			$paybyperson = $data ['class_totals'] [1];
			
			foreach ( $paybyperson as $pbpkey => $pbpval ) {
				if ($pbpkey != 'Entity class') {
					$paybyperson [$pbpkey] = round ( $pbpval * 100000 / $p ['tot'] );
				}
			}
		}
		
		// ==================check data existence for graphic===============================================================================
		if ($this->budgets_mdl->verif_budget () > 0) {
			$year = date ( "Y" ) - 1;
			$data ['budget_graph'] = $this->pbf->get_budgets_all_entities ( $budget_period_graph );
			$data ['payement_graph'] = $this->pbf->get_totals_class ( $budget_period_graph );
			$data ['budget_year_data'] = $this->pbf->get_annual_budget ( $year );
			if ((! empty ( $data ['budget_graph'] )) && (! empty ( $data ['payement_graph'] )) && (! empty ( $data ['budget_year_data'] ))) {
				$data ['check_graph'] = '1';
			}
			
			$data ['budget_data'] = $this->pbf->get_budgets_all_entities ( $periods );
			
			$periods_graph = $this->pbf->get_last_quarters ( $this->config->item ( 'num_period_display' ) );
			
			$data ['exist_budget'] = $this->budgets_mdl->exist_budget_year ( date ( "Y" ) );
			$data ['verif_budget'] = 1;
		} else {
			
			$data ['verif_budget'] = 0;
		}
		
		// =========================================================================================================================
		
		$data ['paybyperson'] = $paybyperson;
		

		
		
		$this->load->view ( 'front_body', $data );
	}
	function export_pbf_manager() {
		$this->lang->load ( 'exports', $this->config->item ( 'language' ) );
		$this->load->library ( 'pearloader' );
		$this->pearloader->load ( 'Writer' );
		$this->load->model ( 'exports_mdl' );
		// ================Clean db before export from computed_routine_data tables==============================================
		$this->exports_mdl->drop_routine_data_all ();
		// ======================================================================================================================
		$lang = ($this->config->item ( 'language' ) == 'francais' ? 'fr' : 'en');
		$ext_aleatoire = rand ( 1, 1000 );
		$this->exports_mdl->set_routine_data_table_full ( $ext_aleatoire, $lang );
		$data = $this->exports_mdl->get_exports ();
		
		// =====================creation du fchier excel sur le disque dur====================================================
		$file_name_is = 'Data full export.xls';
		$excel = new Spreadsheet_Excel_Writer ();
		$excel->send ( $file_name_is );
		foreach ( $data ['list'] as $task ) {
			
			$columns = $this->exports_mdl->get_file_columns ( $task ['filetype_id'], $lang );
			$raw_data = $this->exports_mdl->get_file_contents ( $task, $columns, $ext_aleatoire );
			$columns = $raw_data->list_fields ();
			$raw_data = $raw_data->result_array ();
			
			// =====================creation du fchier excel sur le disque dur====================================================
			$sheet = & $excel->addWorksheet ( substr ( $task ['exports_title'], 0, 30 ) );
			$sheet->setPaper ( 9 ); // Définit une page A4
			$sheet->setLandscape (); // Définit une orientation Paysage.
			                         
			// ==========================Ajout d'un formatage==========================================================================
			$titleFormat = $excel->addFormat ();
			$titleFormat->setFontFamily ( 'Helvetica' );
			$titleFormat->setBold ();
			$titleFormat->setSize ( '10' );
			
			// =========================Titres de colonnes =======================================================================
			$NextRow = 0;
			foreach ( $columns as $column_key => $column_val ) {
				$sheet->write ( $NextRow, $column_key, mb_convert_encoding ( $column_val, "windows-1252", "UTF-8" ), $titleFormat );
			}
			
			// =============================Chargement des donnees vers le fichier Excel============================
			$NextRow ++;
			
			foreach ( $raw_data as $sheet_row ) {
				
				foreach ( $columns as $column_key => $column_val ) {
					
					$sheet_row [$column_val] = ($column_val == 'Mois') ? $this->lang->line ( 'app_month_' . $sheet_row [$column_val] ) : $sheet_row [$column_val];
					$sheet->write ( $NextRow, $column_key, mb_convert_encoding ( $sheet_row [$column_val], "windows-1252", "UTF-8" ) );
				}
				
				$NextRow ++;
			}
		}
		// ===============Enregistrement du fichier Excel sur le disque dur=================================================
		$excel->close ();
		$this->exports_mdl->drop_routin_data ( $ext_aleatoire );
		$this->pbf->set_eventlog ( 'export_created', 1 );
		redirect ( 'exports/' );
	}
	
	// ========================fonctions d'export specifiques aux pages du front end quantity====================================================
	function export_front_quantite() {
		$data = $this->pbf->get_data_export ( '', '', 'indicator_verified_value', 'Quantity' );
		$file_name_is = str_replace ( ' ', '_', 'export_front_quantite' ) . '.xls';
		$redirect = 'data/';
		$this->export_quantity ( $data, $file_name_is, $redirect );
	}
	function export_zone_quantite($geo_id, $zone_id) {
		$data = $this->pbf->get_data_export ( $zone_id, '', 'indicator_verified_value', 'Quantity' );
		$file_name_is = str_replace ( ' ', '_', 'export_zone_quantite' ) . '.xls';
		$redirect = 'data/showzone/' . $geo_id . '/' . $zone_id;
		$this->export_quantity ( $data, $file_name_is, $redirect );
	}
	function export_showentities_quantite($zone_id) {
		$data = $this->pbf->get_data_export ( $zone_id, '', 'indicator_verified_value', 'Quantity' );
		$file_name_is = str_replace ( ' ', '_', 'export_district_quantite' ) . '.xls';
		$redirect = 'data/showentities/' . $zone_id;
		$this->export_quantity ( $data, $file_name_is, $redirect );
	}
	function export_entity_quantite($entity_id) {
		$data = $this->pbf->get_data_export ( '', $entity_id, 'indicator_verified_value', 'Quantity' );
		$file_name_is = str_replace ( ' ', '_', 'export_entity_quantite' ) . '.xls';
		$redirect = 'data/showentity/' . $entity_id;
		$this->export_quantity ( $data, $file_name_is, $redirect );
	}
	// ==============================================================================================================================================
	
	// ========================fonctions d'export specifiques aux pages du front end quality================================
	function export_front_qualite() {
		$data ['qualities'] = $this->pbf->get_quality_per_region_export_front ( '' );
		$file_name_is = str_replace ( ' ', '_', 'export_qualite' ) . '.xls';
		$redirect = 'data/';
		$this->export_quality ( $data, $file_name_is, $redirect );
	}
	function export_zone_qualite($geo_id, $zone_id) {
		$data ['qualities'] = $this->pbf->get_quality_per_region_export ( $zone_id );
		$file_name_is = str_replace ( ' ', '_', 'export_zone_qualite' ) . '.xls';
		$redirect = 'data/showzone/' . $geo_id . '/' . $zone_id;
		$this->export_quality ( $data, $file_name_is, $redirect );
	}
	function export_showentities_qualite($zone_id) {
		$data ['qualities'] = $this->pbf->get_district_inf_excel ( $zone_id );
		$file_name_is = str_replace ( ' ', '_', 'export_district_qualite' ) . '.xls';
		$redirect = 'data/showentities/' . $zone_id;
		$this->export_quality ( $data, $file_name_is, $redirect );
	}
	function export_entity_qualite($entity_id) {
		$data ['qualities'] = $this->pbf->get_entity_qualities_excel ( $entity_id, FALSE );
		$file_name_is = str_replace ( ' ', '_', 'export_entity_qualite' ) . '.xls';
		$redirect = 'data/showentity/' . $entity_id;
		$this->export_quality ( $data, $file_name_is, $redirect );
	}
	// ================================================================================================================================
	
	// ========================fonctions d'export specifiques aux pages du front end payement====================================================
	function export_front_payemet($entity_class, $geozone_id = '') {
		$periods = $this->pbf->get_last_quarters ( $this->config->item ( 'num_period_display' ) );
		
		$data ['indicator_title'] = $this->lang->line ( 'total_payement' );
		
		if (isset ( $periods )) {
			
			$data ['pbf_data'] = $this->pbf->get_payment_details_export ( $periods, $entity_class, $geozone_id );
			
			$firstKey = 0;
			
			foreach ( $data ['pbf_data'] as $k => $v ) {
				$firstKey = $k;
				break;
			}
			
			if (! empty ( $data ['pbf_data'] )) {
				array_unshift ( $data ['pbf_data'], array_keys ( $data ['pbf_data'] [$firstKey] ) );
			}
		}
		
		$data_pay = $data;
		$file_name_is = 'Export_payment_front.xls';
		$redirect = 'data/';
		$this->export_payment ( $data_pay, $file_name_is, $redirect );
	}
	function export_previous_payemet($entity_id) {
		$periods = $this->pbf->get_all_quarters ();
		$data ['entity_info'] = $this->pbf->render_single_entity ( $entity_id );
		$data ['indicator_title'] = $this->lang->line ( 'total_payement' );
		
		if (isset ( $periods )) {
			$data ['pbf_data_payment_fosa'] = $this->pbf->get_computed_payments_export ( $periods, '', $entity_id, $data ['entity_info'] ['entity_class'] );
			
			$firstKey = 0;
			
			foreach ( $data ['pbf_data_payment_fosa'] as $k => $v ) {
				$firstKey = $k;
				break;
			}
			
			if (! empty ( $data ['pbf_data_payment_fosa'] )) {
				array_unshift ( $data ['pbf_data_payment_fosa'], array_keys ( $data ['pbf_data_payment_fosa'] [$firstKey] ) );
			}
		}
		
		$file_name_is = 'Export_previous_front.xls';
		$redirect = 'data/showentity/' . $entity_id;
		$this->export_payment_previous ( $data, $file_name_is, $redirect );
	}
	function export_entity_payemet($entity_id) {
		$periods = $this->pbf->get_last_quarters ( $this->config->item ( 'num_period_display' ) );
		$data ['entity_info'] = $this->pbf->render_single_entity ( $entity_id );
		$data ['indicator_title'] = $this->lang->line ( 'total_payement' );
		
		if (isset ( $periods )) {
			$data ['pbf_data_payment_fosa'] = $this->pbf->get_computed_payments_export ( $periods, '', $entity_id, $data ['entity_info'] ['entity_class'] );
			
			$firstKey = 0;
			
			foreach ( $data ['pbf_data_payment_fosa'] as $k => $v ) {
				$firstKey = $k;
				break;
			}
			
			if (! empty ( $data ['pbf_data_payment_fosa'] )) {
				array_unshift ( $data ['pbf_data_payment_fosa'], array_keys ( $data ['pbf_data_payment_fosa'] [$firstKey] ) );
			}
		}
		
		$file_name_is = 'Export_previous_front.xls';
		$redirect = 'data/showentity/' . $entity_id;
		$this->export_payment_previous ( $data, $file_name_is, $redirect );
	}
	function export_zone_payemet($geozone_id) {
		$periods = $this->pbf->get_last_quarters ( $this->config->item ( 'num_period_display' ) );
		
		$data ['indicator_title'] = $this->lang->line ( 'total_payement' );
		
		if (isset ( $periods )) { // vs !empty($periods)
			
			$data ['pbf_data'] = $this->pbf->get_payment_details_export ( $periods, '1', $geozone_id );
			
			$firstKey = 0;
			
			foreach ( $data ['pbf_data'] as $k => $v ) {
				$firstKey = $k;
				break;
			}
			
			if (! empty ( $data ['pbf_data'] )) {
				array_unshift ( $data ['pbf_data'], array_keys ( $data ['pbf_data'] [$firstKey] ) );
			}
		}
		
		$file_name_is = 'Export_payment_zone.xls';
		$redirect = 'data/showentity/' . $entity_id;
		$this->export_payment ( $data, $file_name_is, $redirect );
	}
	
	// ================================================================================================================================
	
	// ======================fonctions de base export du frontend=======================================================================
	function export_quality($data, $file_name_is, $redirect) {
		$this->lang->load ( 'exports', $this->config->item ( 'language' ) );
		$this->load->library ( 'pearloader' );
		$this->pearloader->load ( 'Writer' );
		
		// ==================creation du fichier et d'une feuille======================
		$excel = new Spreadsheet_Excel_Writer ();
		$excel->send ( $file_name_is );
		$sheet = & $excel->addWorksheet ( substr ( 'Qualite', 0, 30 ) );
		
		// =================Formatage des cellules=========================================
		$titleFormat = $excel->addFormat ();
		$titleFormat->setFontFamily ( 'Helvetica' );
		$titleFormat->setBold ();
		$titleFormat->setSize ( '10' );
		$sheet->setColumn ( 0, 0, 35 );
		// ============================================================================
		
		$entittypes = array_keys ( $data ['qualities'] );
		$i = 0;
		
		foreach ( $entittypes as $entitytype ) {
			$titres = $data ['qualities'] [$entitytype];
			$headers = array_keys ( $titres [0] );
			$headers [0] = $entitytype;
			
			if ($i == 0) {
				$col_titre = 0;
				foreach ( $headers as $titre ) {
					$sheet->write ( 0, $col_titre, mb_convert_encoding ( $titre, "windows-1252", "UTF-8" ), $titleFormat );
					$col_titre ++;
				}
				$i ++;
			} else {
				$sheet->write ( $i, 0, mb_convert_encoding ( $entitytype, "windows-1252", "UTF-8" ), $titleFormat );
				$i ++;
			}
			
			$donnees = $data ['qualities'] [$entitytype];
			foreach ( $donnees as $d ) {
				$col = 0;
				foreach ( $d as $donnee ) {
					$sheet->write ( $i, $col, mb_convert_encoding ( $donnee, "windows-1252", "UTF-8" ) );
					$col ++;
				}
				$i ++;
			}
		}
		$excel->close ();
		// redirect($redirect);
	}
	function export_quantity($data, $file_name_is, $redirect) {
		$this->lang->load ( 'exports', $this->config->item ( 'language' ) );
		$this->load->library ( 'pearloader' );
		$this->pearloader->load ( 'Writer' );
		
		// ==================creation du fichier et d'une feuille======================
		$excel = new Spreadsheet_Excel_Writer ();
		$excel->send ( $file_name_is );
		$sheet = & $excel->addWorksheet ( substr ( 'Quantite', 0, 30 ) );
		// =========================================================================
		
		// ==================Formatage cellules===========================================
		$titleFormat = $excel->addFormat ();
		$number_format = & $excel->addFormat ();
		$titleFormat->setFontFamily ( 'Helvetica' );
		$titleFormat->setBold ();
		$titleFormat->setSize ( '10' );
		$sheet->setColumn ( 0, 0, 80 );
		$number_format->setNumFormat ( '#,##0.00' );
		// =======================================================================
		
		$ligne = 0;
		foreach ( $data ['pbf_data'] ['pbf_data'] as $donnees ) {
			
			$col_titre = 0;
			foreach ( $donnees as $donneesligne ) {
				
				if ($ligne == 0) {
					$sheet->write ( $ligne, $col_titre, mb_convert_encoding ( $donneesligne, "windows-1252", "UTF-8" ), $titleFormat );
					$col_titre ++;
				} 

				else {
					if ($col_titre == 0) {
						$sheet->write ( $ligne, $col_titre, mb_convert_encoding ( $donneesligne, "windows-1252", "UTF-8" ) );
						$col_titre ++;
					} else {
						$sheet->write ( $ligne, $col_titre, str_replace ( '.', '', $donneesligne ), $number_format );
						$col_titre ++;
					}
				}
			}
			$ligne ++;
		}
		$excel->close ();
		redirect ( $redirect );
	}
	function export_payment($data, $file_name_is, $redirect) {
		$this->lang->load ( 'exports', $this->config->item ( 'language' ) );
		$this->load->library ( 'pearloader' );
		$this->pearloader->load ( 'Writer' );
		
		// ==================creation du fichier et d'une feuille======================
		$excel = new Spreadsheet_Excel_Writer ();
		$excel->send ( $file_name_is );
		$sheet = & $excel->addWorksheet ( substr ( 'Payments', 0, 30 ) );
		// =========================================================================
		
		// ==================Formatage cellules===========================================
		$number_format = & $excel->addFormat ();
		$titleFormat = $excel->addFormat ();
		$titleFormat->setFontFamily ( 'Helvetica' );
		$titleFormat->setBold ();
		$titleFormat->setSize ( '10' );
		$sheet->setColumn ( 0, 7, 20 );
		$number_format->setNumFormat ( '#,##0' );
		// =======================================================================
		$ligne = 0;
		
		foreach ( $data ['pbf_data'] as $donnees ) {
			$col_titre = 0;
			foreach ( $donnees as $donneesligne ) {
				if ($ligne == 0) {
					$sheet->write ( $ligne, $col_titre, mb_convert_encoding ( $donneesligne, "windows-1252", "UTF-8" ), $titleFormat );
					$col_titre ++;
				} else {
					if ($col_titre == 0) {
						$sheet->write ( $ligne, $col_titre, $donneesligne );
						$col_titre ++;
					} else {
						$sheet->write ( $ligne, $col_titre, $donneesligne, $number_format );
						$col_titre ++;
					}
				}
			}
			$ligne ++;
		}
		$excel->close ();
		redirect ( $redirect );
	}
	function export_payment_previous($data, $file_name_is, $redirect) {
		$this->lang->load ( 'exports', $this->config->item ( 'language' ) );
		$this->load->library ( 'pearloader' );
		$this->pearloader->load ( 'Writer' );
		
		// ==================creation du fichier et d'une feuille======================
		$excel = new Spreadsheet_Excel_Writer ();
		$excel->send ( $file_name_is );
		$sheet = & $excel->addWorksheet ( substr ( 'Payments', 0, 30 ) );
		// =========================================================================
		
		// ==================Formatage cellules===========================================
		$number_format = & $excel->addFormat ();
		$titleFormat = $excel->addFormat ();
		$titleFormat->setFontFamily ( 'Helvetica' );
		$titleFormat->setBold ();
		$titleFormat->setSize ( '10' );
		$sheet->setColumn ( 0, 7, 20 );
		$number_format->setNumFormat ( '#,##0' );
		// =======================================================================
		$ligne = 0;
		
		foreach ( $data ['pbf_data_payment_fosa'] as $donnees ) {
			
			$col_titre = 0;
			foreach ( $donnees as $donneesligne ) {
				
				if ($ligne == 0) {
					$sheet->write ( $ligne, $col_titre, mb_convert_encoding ( $donneesligne, "windows-1252", "UTF-8" ), $titleFormat );
					$col_titre ++;
				} else {
					if ($col_titre == 0) {
						$sheet->write ( $ligne, $col_titre, $donneesligne );
						$col_titre ++;
					} else {
						$sheet->write ( $ligne, $col_titre, str_replace ( '.', '', $donneesligne ), $number_format );
						$col_titre ++;
					}
				}
			}
			$ligne ++;
		}
		$excel->close ();
		// redirect($redirect);
	}
	
	// ===========================================================================================================
	function __total_quantity_chart($data, $level = 'INDICATOR', $graph_title_arg, $target = 0) {
		$this->load->library ( 'highcharts' );
		
		$graph_axis_title = 'Total';
		
		$indicator = array_slice ( $data, 1 );
		
		$data = array ();
		$target_data = array ();
		foreach ( $indicator as $k => $v ) {
			
			$val = intval ( str_replace ( ',', '', $v ) );
			
			array_push ( $data, $val );
			if ($target > 0)
				array_push ( $target_data, $target );
		}
		
		$categories = array (
				'categories' => array_keys ( $indicator ) 
		);
		
		$this->highcharts->set_title ( $graph_title_arg );
		
		$this->highcharts->set_xAxis ( $categories );
		
		$chart_data ['name'] = $graph_title_arg;
		
		// $chart_data['showInLegend'] = false;
		
		$chart_data ['data'] = $data;
		
		if ((! empty ( $target_data ))) {
			$chart_targets_data ['name'] = $this->lang->line ( 'target_population' );
			$chart_targets_data ['data'] = $target_data;
			$this->highcharts->set_serie ( $chart_targets_data );
		}
		
		$this->highcharts->set_axis_titles ( '', $graph_axis_title );
		
		$this->highcharts->set_serie ( $chart_data );
		
		$credits->href = '#';
		$credits->text = $graph_title;
		
		$this->highcharts->set_credits ( $credits );
		
		$this->highcharts->render_to ( 'graph' ); // choose a specific div to render to graph
		
		return $this->highcharts->render ();
	}
	function __draw_quantity_chart($data, $level = 'INDICATOR') {
		$level = strtoupper ( $this->lang->line ( 'front_data' ) );
		$charts = array ();
		
		$i = 0;
		
		$this->load->library ( 'highcharts' );
		
		foreach ( $data as $key => $indicator ) {
			
			$graph_title = $indicator [$level];
			
			$indicator = array_slice ( $indicator, 1 );
			
			// echo $indicator
			preg_match_all ( '_<a.*?>(.*?)</a_i', $graph_title, $matches );
			
			$text = $matches [1] [0];
			
			preg_match ( '_<a href=(.*?)>(.*?)</a_i', $graph_title, $match );
			$url = str_replace ( '"', '', $match [1] );
			
			$this->highcharts->set_title ( $text ); // set chart title: title, subtitle(optional)
			                                     
			// $this->highcharts->set_axis_titles('language', 'population'); // axis titles: x axis, y axis
			                                     // $this->highcharts->set_type('column');
			
			$data = array ();
			foreach ( $indicator as $k => $v ) {
				
				array_push ( $data, intval ( str_replace ( ',', '', $v ) ) );
			}
			$categories = array (
					'categories' => array_keys ( $indicator ) 
			);
			
			$this->highcharts->set_xAxis ( $categories );
			
			$chart_data ['name'] = $text;
			
			$chart_data ['data'] = $data;
			
			$this->highcharts->set_serie ( $chart_data );
			
			$credits->href = $url;
			$credits->text = $text;
			
			$this->highcharts->set_credits ( $credits );
			
			$this->highcharts->render_to ( 'graph_' . $i ); // choose a specific div to render to graph
			
			$chart ['chart'] = $this->highcharts->render ();
			$chart ['title'] = $graph_title;
			array_push ( $charts, $chart ); // we render js and div in same time
			
			$i ++;
		}
		
		return $charts;
	}
	function __format_total($return) {
		foreach ( $return as $k => $val ) {
			if (is_int ( $val )) {
				$return [$k] = '<span class="table_footer">' . number_format ( $val, 0, $this->lang->line ( 'decimal_separator' ), $this->lang->line ( 'thousand_separator' ) ) . '</span>';
			} else {
				$return [$k] = '<span class="table_footer">' . ($val) . '</span>';
			}
		}
		
		return $return;
	}
	function __count_total($pfb_data) {
		$return = array ();
		
		$keys = array_keys ( $pfb_data [0] );
		
		$data = $pfb_data;
		
		$values = array_values ( $keys );
		$i = 0;
		
		foreach ( $values as $value ) {
			if ($i == 0) {
				$return [$value] = 'TOTAL';
			} else {
				$return [$value] = 0;
			}
			$i ++;
		}
		
		foreach ( $data as $line ) {
			foreach ( $keys as $key => $value ) {
				
				if ($key != 0) {
					
					$return [$value] += intval ( str_replace ( $this->lang->line ( 'thousand_separator' ), '', $line [$value] ) );
				}
			}
		}
		
		return $return;
	}
	function showzone($geo_id, $zone_id) {
		$this->load->model ( 'geo_mdl' );
		$this->lang->load ( 'geo' );
		
		if ($geo_id == $this->pbf->get_active_geo_info ()) {
			
			redirect ( 'data/showentities/' . $zone_id );
		}
		
		$data = $this->pbf->get_data ( $zone_id, '', 'indicator_verified_value', 'Quantity' );
		
		$periods_datafile = $this->pbf->get_last_periods ( $this->config->item ( 'num_period_display' ), $this->config->item ( 'period_type' ) );
		if (($this->config->item ( 'period_type' ) == 'quarter') && (count ( $periods ) < $this->config->item ( 'min_period' ))) {
			$periods_datafile = $this->pbf->get_last_periods ( $this->config->item ( 'num_period_display' ), 'month' );
		}
		
		$data ['parent_pbf_data'] = $this->pbf->get_featured_indic_avg ( $periods_datafile, '', '', '1', 'indicator_verified_value', 'Quantity' );
		
		$district_quality_data = $this->pbf->get_quality_per_region ( $zone_id );
		$data ['qualities'] = $district_quality_data;
		
		$data ['geos_title'] = $this->lang->line ( 'geo_district' );
		
		$data ['current_zone_info'] = $this->geo_mdl->get_zone ( $zone_id );
		
		$periods = $this->pbf->get_last_quarters ( $this->config->item ( 'num_period_display' ) );
		
		$data ['map_render'] = $this->pbf->render_child_zones ( $geo_id, $zone_id );
		
		$data ['front_main_nav'] = $this->pbf->get_front_menu ();
		
		$data ['map'] ['html'] = '';
		$data ['map'] ['js'] = '';
		
		$data ['class_totals'] = $this->pbf->get_totals_class ( $periods, $zone_id );
		if (! empty ( $data ['class_totals'] ))
			array_unshift ( $data ['class_totals'], array_keys ( $data ['class_totals'] [0] ) );
		
		$year = date ( "Y" );
		$p = $this->cms_mdl->get_pop_tot ( 3, $zone_id );
		
		$data ['pop'] = $p ['tot'];
		
		$data ['key_data'] = $this->pbf->get_keydata ( $zone_id );
		
		$data ['real_time_result'] = $this->pbf->get_real_time_result ( $zone_id, $data ['pop'] );
		
		$payments = $this->pbf->get_payments_all ( $zone_id );
		
		if (! empty ( $payments )) {
			
			$money = explode ( ' ', $payments ['realtime'] ['sum_validated_value'] );
			
			$data ['received_payement'] ['realtime'] ['money_amount'] = $money [0];
			$data ['received_payement'] ['realtime'] ['money_local'] = $money [1];
			$data ['received_payement'] ['realtime'] ['money_usd'] = 'USD';
			
			$data ['received_payement'] ['realtime'] ['indicator_icon_file'] = $payments ['realtime'] ['indicator_icon_file'];
			
			$dollar_exchange_rade = 478.41; // found on internet TODO change this, get it from the internet
			$data ['received_payement'] ['realtime'] ['money_usd_amount'] = floatval ( str_replace ( $this->lang->line ( 'thousand_separator' ), '', $money [0] ) ) / $dollar_exchange_rade;
			$data ['received_payement'] ['realtime'] ['per_capita'] = floatval ( str_replace ( $this->lang->line ( 'thousand_separator' ), '', $money [0] ) ) / intval ( $data ['pop'] );
			$data ['received_payement'] ['realtime'] ['per_capita_usd'] = $data ['received_payement'] ['realtime'] ['money_usd_amount'] / intval ( $data ['pop'] );
			
			$data ['received_payement'] ['realtime'] ['money_usd_amount'] = number_format ( $data ['received_payement'] ['realtime'] ['money_usd_amount'], 2, $this->lang->line ( 'decimal_separator' ), $this->lang->line ( 'thousand_separator' ) );
			$data ['received_payement'] ['realtime'] ['per_capita'] = number_format ( $data ['received_payement'] ['realtime'] ['per_capita'], 2, $this->lang->line ( 'decimal_separator' ), $this->lang->line ( 'thousand_separator' ) );
			$data ['received_payement'] ['realtime'] ['per_capita_usd'] = number_format ( $data ['received_payement'] ['realtime'] ['per_capita_usd'], 2, $this->lang->line ( 'decimal_separator' ), $this->lang->line ( 'thousand_separator' ) );
		}
		
		$data ['top_quality_score'] = $this->pbf->get_top_score ( $zone_id );
		
		$data ['quality_definition'] = $this->pbf->get_edito ( 'Quality_definition' );
		
		$average_quality_period = $this->config->item ( 'average_quality_period' ) * 3; // convert the value in months(the original value is in quarters)
		
		$data ['average_qual'] = $this->pbf_mdl->get_average_quality_region ( $zone_id, $average_quality_period );
		
		$data ['page_title'] = $data ['current_zone_info'] ['geozone_name'] . ' : ' . $this->lang->line ( 'front_pbf_data' );
		$data ['meta_description'] = $data ['page_title'];
		$data ['page'] = 'datapage';
		
		// =====================================export===========================================================
		$filename_qual = FCPATH . 'cside/exports/' . str_replace ( ' ', '_', 'export_zone_qualite' ) . '.xls';
		$download_link_qual = base_url () . 'cside/exports/' . str_replace ( ' ', '_', 'export_zone_qualite' ) . '.xls';
		$data ['filename_qual'] = $filename_qual;
		$data ['download_link_qual'] = $download_link_qual;
		$filename_quant = FCPATH . 'cside/exports/' . str_replace ( ' ', '_', 'export_zone_quantite' ) . '.xls';
		$download_link_quant = base_url () . 'cside/exports/' . str_replace ( ' ', '_', 'export_zone_quantite' ) . '.xls';
		$data ['filename_quant'] = $filename_quant;
		$data ['download_link_quant'] = $download_link_quant;
		$data ['geo_id'] = $geo_id;
		$data ['zone_id'] = $zone_id;
		// ===========================================================================================================
		
		// ==================check data existence for graphic===============================================================================
		if ($this->budgets_mdl->verif_budget () > 0) {
			$data ['budget_data'] = $this->pbf->get_budgets_zone ( $zone_id, $periods );
			$data ['verif_budget'] = 1;
		} else {
			$data ['verif_budget'] = 0;
		}
		// ==============================================================================================================================
		$this->load->view ( 'front_body', $data );
	}
	function showzone_graph_data($zone_id, $id) {
		$all_data = $this->pbf->get_data ( $zone_id, '', 'indicator_verified_value', 'Quantity' );
		
		$data_slice = array_slice ( $all_data ['pbf_data'] ['pbf_data_slice'], 1 );
		
		$data = $data_slice [$id];
		
		echo $this->__convert_to_json ( $data );
	}
	function show_entity_graph_data($entity_id, $id) {
		$all_data = $this->pbf->get_data ( '', $entity_id, 'indicator_verified_value', 'Quantity' );
		
		$data_slice = array_slice ( $all_data ['pbf_data'] ['pbf_data_slice'], 1 );
		
		$data = $data_slice [$id];
		
		echo $this->__convert_to_json ( $data );
	}
	function showentities_graph_data($id) {
		$data = $this->pbf->get_data ( '', $id, 'indicator_verified_value', 'Quantity' );
		
		$data_slice = array_slice ( $data ['pbf_data'] ['pbf_data_slice'], 1 );
		
		$data = $data_slice [$id];
		
		echo $this->__convert_to_json ( $data );
	}
	function showentities($zone_id) {
		$this->load->library ( 'googlemaps' );
		$this->load->model ( 'geo_mdl' );
		
		$data = $this->pbf->get_data ( $zone_id, '', 'indicator_verified_value', 'Quantity' );
		
		// data for parent id : Used only for quantity chart. Safe to delete if no chart needed.
		$district = $this->geo_mdl->get_zone ( $zone_id );
		// TODO improve this. One is using datafile
		$periods_datafile = $this->pbf->get_last_periods ( $this->config->item ( 'num_period_display' ), $this->config->item ( 'period_type' ) );
		if (($this->config->item ( 'period_type' ) == 'quarter') && (count ( $periods ) < $this->config->item ( 'min_period' ))) {
			$periods_datafile = $this->pbf->get_last_periods ( $this->config->item ( 'num_period_display' ), 'month' );
		}
		
		$data ['parent_pbf_data'] = $this->pbf->get_featured_indic_avg ( $periods_datafile, $district ['geozone_parentid'], '', '1', 'indicator_verified_value', 'Quantity' );
		
		$data ['qualities'] = $this->pbf->get_district_qualities ( $zone_id, FALSE );
		
		$data ['pbf_data'] ['pbf_data_graph'] = $data ['pbf_data'] ['pbf_data_slice'];
		
		$breadcrumb = $this->pbf->get_geozone_breadcrumb ( $zone_id );
		
		$data ['breadcrumb'] = $breadcrumb;
		
		$data ['map_render'] = $this->pbf->render_zone_entities ( $zone_id );
		
		$entities_coords = $this->pbf->zone_entities_geo ( $zone_id );
		
		$data ['front_main_nav'] = $this->pbf->get_front_menu ();
		
		$periods = $this->pbf->get_last_quarters ( $this->config->item ( 'num_period_display' ) );
		
		$data ['class_totals'] = $this->pbf->get_totals_class ( $periods, $zone_id );
		if (! empty ( $data ['class_totals'] ))
			array_unshift ( $data ['class_totals'], array_keys ( $data ['class_totals'] [0] ) );
		
		$data ['top_quality_score'] = $this->pbf->get_top_score ( $zone_id, 'district' );
		
		$border = $this->pbf->render_geo_borders ( $zone_id );
		
		$geo_json = json_decode ( $border ['geozone_geojson'] );
		
		$coordinates = $geo_json->coordinates [0];
		
		$polygon = array ();
		$points = array ();
		foreach ( $coordinates as $coord ) {
			
			$points [] = ($coord [1] . ',' . $coord [0]);
		}
		$polygon ['points'] = $points;
		
		$config ['center'] = "3.9833,13.1667";
		$config ['zoom'] = 'auto';
		$config ['map_type'] = 'ROADMAP';
		// $config['cluster'] = TRUE;
		// $config['clusterGridSize'] = 5;
		$polygon ['strokeColor'] = '#2E2D2D';
		$polygon ['fillColor'] = '#2E2D2D';
		$polygon ['strokeOpacity'] = '0.8';
		$polygon ['strokeWeight'] = 2;
		$polygon ['fillOpacity'] = '0.35';
		$config ['clusterAverageCenter'] = TRUE;
		$config ['map_height'] = '450px';
		$config ['minifyJS'] = TRUE;
		$this->googlemaps->initialize ( $config );
		$this->googlemaps->add_polygon ( $polygon );
		
		$poptot = 0;
		foreach ( $entities_coords as $coordinate ) {
			if (($coordinate ['entity_geo_lat'] != 0) && ($coordinate ['entity_geo_long'] != 0)) {
				$marker = array ();
				$marker ['position'] = $coordinate ['entity_geo_lat'] . ',' . $coordinate ['entity_geo_long'];
				$marker ['title'] = $coordinate ['entity_name'];
				$marker ['icon'] = site_url () . 'cside/images/hospital-building.png';
				$marker_template = '<div class=\"map-info-window\">';
				if (! empty ( $coordinate ['entity_picturepath'] )) {
					
					$append = 'cside/images/portal/' . $coordinate ['entity_picturepath'];
					
					$marker_template .= '<img src=\"' . site_url () . $append . '\" alt=\"picture\" width=\"240\" height=\"128\" />';
				}
				$marker_template .= '<a href=\"' . site_url ( 'data/showentity/' . $coordinate ['entity_id'] ) . '\"><h5>' . $coordinate ['entity_name'] . '</h5></a>';
				$marker_template .= '</div>';
				$marker ['infowindow_content'] = $marker_template;
				$this->googlemaps->add_marker ( $marker );
			}
			if (($coordinate ['entity_type'] == 2) || ($coordinate ['entity_type'] == 3) || ($coordinate ['entity_type'] == 4) || ($coordinate ['entity_type'] == 5)) {
				$poptot += $coordinate ['entity_pop'];
			}
		}
		
		$data ['current_zone_info'] = $this->geo_mdl->get_zone ( $zone_id );
		
		if (($data ['current_zone_info'] ['geozone_pop'] != '0') && (! empty ( $data ['current_zone_info'] ['geozone_pop'] ))) { // is set take pop in DB
			$year = date ( "Y" );
			$data ['pop'] = round ( $data ['current_zone_info'] ['geozone_pop'] * pow ( (1 + ($this->config->item ( 'pop_growth_rate' ) / 100)), ($year - $data ['current_zone_info'] ['geozone_pop_year']) ) );
		} else { // take SUM pop in entiteis
			$data ['pop'] = $poptot;
		}
		
		// Si la population couverte est nulle, le nombre à afficher est nul
		if ($data ['pop'] == 0) {
			$data ['pop'] = '';
		}
		
		$payments = $this->pbf->get_payments_all ( $zone_id );
		
		if (! empty ( $payments )) {
			$money = explode ( ' ', $payments ['realtime'] ['sum_validated_value'] );
			
			$data ['received_payement'] ['realtime'] ['money_amount'] = $money [0];
			$data ['received_payement'] ['realtime'] ['money_local'] = $money [1];
			$data ['received_payement'] ['realtime'] ['money_usd'] = 'USD';
			$data ['received_payement'] ['realtime'] ['indicator_icon_file'] = $payments ['realtime'] ['indicator_icon_file'];
			
			$benin_cfa_exchange_rade = 478.41; // found on internet TODO change this
			$data ['received_payement'] ['realtime'] ['money_usd_amount'] = floatval ( str_replace ( $this->lang->line ( 'thousand_separator' ), '', $money [0] ) ) / $benin_cfa_exchange_rade;
			$data ['received_payement'] ['realtime'] ['per_capita'] = floatval ( str_replace ( $this->lang->line ( 'thousand_separator' ), '', $money [0] ) ) / intval ( $data ['pop'] );
			$data ['received_payement'] ['realtime'] ['per_capita_usd'] = $data ['received_payement'] ['realtime'] ['money_usd_amount'] / intval ( $data ['pop'] );
			
			$data ['received_payement'] ['realtime'] ['money_usd_amount'] = number_format ( $data ['received_payement'] ['realtime'] ['money_usd_amount'], 2, $this->lang->line ( 'decimal_separator' ), $this->lang->line ( 'thousand_separator' ) );
			$data ['received_payement'] ['realtime'] ['per_capita'] = number_format ( $data ['received_payement'] ['realtime'] ['per_capita'], 2, $this->lang->line ( 'decimal_separator' ), $this->lang->line ( 'thousand_separator' ) );
			$data ['received_payement'] ['realtime'] ['per_capita_usd'] = number_format ( $data ['received_payement'] ['realtime'] ['per_capita_usd'], 2, $this->lang->line ( 'decimal_separator' ), $this->lang->line ( 'thousand_separator' ) );
		}
		
		$data ['real_time_result'] = $this->pbf->get_real_time_result ( $zone_id, $data ['pop'] );
		
		$data ['key_data'] = $this->pbf->get_keydata ( $data ['current_zone_info'] ['geozone_parentid'] ); // take keydata from region
		                                                                                             
		// TODO Quick and dirty way to display icons for realtime result.
		$data ['real_time_result'] ['data'] [0] ['realtime'] ['class'] = 'delivery';
		$data ['real_time_result'] ['data'] [1] ['realtime'] ['class'] = 'patient';
		$data ['real_time_result'] ['data'] [2] ['realtime'] ['class'] = 'vaccinate';
		$data ['real_time_result'] ['data'] [3] ['realtime'] ['class'] = 'lungs';
		$average_quality_period = '';
		$average_quality_period = $this->config->item ( 'average_quality_period' ) * 3; // convert the value in months(the original value is in quarters)
		
		$data ['average_qual'] = $this->pbf_mdl->get_average_quality_zone ( $zone_id, $average_quality_period );
		
		$data ['map'] = $this->googlemaps->create_map ();
		$data ['page_title'] = $data ['breadcrumb'] ['lvl2_title'] . ' - ' . $this->lang->line ( 'front_pbf_data' );
		$data ['meta_description'] = $data ['page_title'];
		$data ['page'] = 'entitiespage';
		
		// =====================================export===========================================================
		$filename_qual = FCPATH . 'cside/exports/' . str_replace ( ' ', '_', 'export_district_qualite' ) . '.xls';
		$download_link_qual = base_url () . 'cside/exports/' . str_replace ( ' ', '_', 'export_district_qualite' ) . '.xls';
		$data ['filename_qual'] = $filename_qual;
		$data ['download_link_qual'] = $download_link_qual;
		$filename_quant = FCPATH . 'cside/exports/' . str_replace ( ' ', '_', 'export_district_quantite' ) . '.xls';
		$download_link_quant = base_url () . 'cside/exports/' . str_replace ( ' ', '_', 'export_district_quantite' ) . '.xls';
		$data ['filename_quant'] = $filename_quant;
		$data ['download_link_quant'] = $download_link_quant;
		$data ['zone_id'] = $zone_id;
		// ==========================================================================================================
		if ($this->budgets_mdl->verif_budget () > 0) {
			$data ['budget_data'] = $this->pbf->get_budgets_district ( $zone_id, $periods );
			$data ['verif_budget'] = 1;
		} else {
			$data ['verif_budget'] = 0;
		}
		
		$this->load->view ( 'front_body', $data );
	}
	function get_entities_geo_json($zone_id) {
		$return = array ();
		
		$this->load->model ( 'geo_mdl' );
		$entities_coords = $this->pbf->zone_entities_geo ( $zone_id );
		
		$return ['entities_coords'] = $entities_coords;
		$return ['map_center'] = '{"latitude":3.9833,"longitude":13.1667}';
		
		echo json_encode ( $return );
	}
	function __draw_payement_chart($data, $data2, $entity_type = 'fosa') {
		$this->load->library ( 'highcharts' );
		
		$graph_axis_title = $this->lang->line ( 'sum_cfa' );
		// data for entity payement
		foreach ( $data as $d ) {
			$indicators = array_slice ( $d, 1 );
			$row = array ();
			foreach ( $indicators as $k => $v ) {
				
				$val = intval ( str_replace ( $this->lang->line ( 'thousand_separator' ), '', $v ) );
				
				array_push ( $row, $val );
			}
		}
		
		// second serie : data for district payement
		foreach ( $data2 as $key => $d ) {
			
			$indicators = array_slice ( $d, 1 );
			$row2 = array ();
			$nbEntities = $d ['nbentities'];
			unset ( $data2 [$key] ['nbentities'] );
			unset ( $indicators ['nbentities'] );
			foreach ( $indicators as $k => $v ) {
				
				$val = round ( intval ( str_replace ( $this->lang->line ( 'thousand_separator' ), '', $v ) ) / $nbEntities );
				
				array_push ( $row2, $val );
			}
		}
		
		$categories = array (
				'categories' => array_keys ( $indicators ) 
		);
		
		$this->highcharts->set_title ( '' );
		
		$this->highcharts->set_xAxis ( $categories );
		
		$chart_data ['name'] = $this->lang->line ( 'fosa_payement' );
		
		$chart_data ['showInLegend'] = true;
		
		$chart_data ['data'] = $row;
		if ($entity_type == 'fosa') {
			$chart_data2 ['name'] = $this->lang->line ( 'district_payement' );
		} else {
			if ($entity_type == 'hospital') {
				$chart_data2 ['name'] = $this->lang->line ( 'national_payement' );
			}
		}
		
		$chart_data2 ['showInLegend'] = true;
		
		$chart_data2 ['data'] = $row2;
		
		$this->highcharts->set_axis_titles ( '', $graph_axis_title );
		
		$this->highcharts->set_serie ( $chart_data );
		$this->highcharts->set_serie ( $chart_data2 );
		
		$credits->href = '#';
		$credits->text = 'highcharts';
		
		$this->highcharts->set_credits ( null );
		
		$this->highcharts->render_to ( 'payement_graph' ); // choose a specific div to render to graph
		
		return $this->highcharts->render ();
	}
	function showentity($entity_id) {
		$this->load->library ( 'googlemaps' );
		$this->load->model ( 'entities_mdl' );
		$data = $this->pbf->get_data ( '', $entity_id, 'indicator_verified_value', 'Quantity' );
		$data ['pbf_data'] ['pbf_data_slice'] = array_slice ( $data ['pbf_data'] ['pbf_data'], 0, $data ['pbf_data'] ['tot_featured'] + 1 );
		
		// data for parent id : Used only for quantity chart. Safe to delete if no chart needed.
		
		$entity = $this->entities_mdl->get_entity ( $entity_id );
		
		// TODO improve this. One is using datafile
		$periods_datafile = $this->pbf->get_last_periods ( $this->config->item ( 'num_period_display' ), $this->config->item ( 'period_type' ) );
		if (($this->config->item ( 'period_type' ) == 'quarter') && (count ( $periods ) < $this->config->item ( 'min_period' ))) {
			$periods_datafile = $this->pbf->get_last_periods ( $this->config->item ( 'num_period_display' ), 'month' );
		}
		
		// $data['parent_pbf_data'] = $this->pbf->get_featured_indic($periods_datafile,$district['geozone_parentid'],'','1','indicator_verified_value','Quantity');
		$data ['parent_pbf_data'] = $this->pbf->get_featured_indic_avg ( $periods_datafile, $entity ['geozone_id'], $entity_id, '1', 'indicator_verified_value', 'Quantity' );
		
		$data ['qualities'] = $this->pbf->get_entity_qualities ( $entity_id, FALSE );
		
		$keys = array_keys ( $data ['qualities'] );
		
		$temp = $data ['qualities'] [$keys [0]] [0];
		
		$temp_keys = array_keys ( $temp );
		$last_term_quality = $temp [$temp_keys [count ( $temp_keys ) - 2]];
		$last_but_one_term_quality = $temp [$temp_keys [count ( $temp_keys ) - 3]];
		
		$data ['last_term_quality'] = array (
				'pourcent' => round ( $last_term_quality ),
				'icon' => ($last_term_quality > $last_but_one_term_quality) ? 'up double' : 'down double' 
		);
		
		$data_quarter_datafile = $this->entities_mdl->get_quarter ( $entity_id );
		foreach ( $data_quarter_datafile as $get_quarter_key => $get_quarter_val ) {
			$data_quarter = $get_quarter_val ['datafile_quarter'];
			$datayear = $get_quarter_val ['datafile_year'];
			$geozone_parentid = $get_quarter_val ['geozone_parentid'];
			$entity_geozone_id = $get_quarter_val ['entity_geozone_id'];
			$report_id = 6;
			$entity_id_recup = $get_quarter_val ['entity_id'];
			$data_quarters [$data_quarter . '-' . $datayear . '-' . $entity_id_recup . '-' . $report_id . '-' . $entity_geozone_id . '-' . $geozone_parentid] = $this->lang->line ( 'app_quarter_' . $get_quarter_val ['datafile_quarter'] ) . ' ' . $get_quarter_val ['datafile_year'];
		}
		$data ['data_quarter_datafile'] = $data_quarters;
		$data ['entity_info'] = $this->pbf->render_single_entity ( $entity_id );
		
		$data ['breadcrumb'] = $this->pbf->get_entity_breadcrumb ( $entity_id );
		
		$periods = $this->pbf->get_last_quarters ( $this->config->item ( 'num_period_display' ) );
		$all_periods = $this->pbf->get_all_quarters ();
		
		if (isset ( $periods )) { // vs !empty($periods)
			
			$data ['pbf_detailed_data'] = $this->pbf->get_featured_indic ( $periods_datafile, '', $entity_id, $data ['entity_info'] ['entity_class'], 'indicator_verified_value', 'Quantity', $this->config->item ( 'language_abbr' ) );
			
			$data ['pbf_qlt_data'] = $this->pbf->get_avg_perfomance ( $periods, '', $entity_id, $data ['entity_info'] ['entity_class'] );
			
			$data ['pbf_data_payment_fosa'] = $this->pbf->get_computed_payments ( $periods, '', $entity_id, $data ['entity_info'] ['entity_class'] );
			$data ['parent_average_payement'] = $this->pbf->get_entity_parent_payement ( $periods, $entity_id );
			
			// third argument indicates if it's fosa or hospital.
			$data ['payement_chart'] = $this->__draw_payement_chart ( $data ['pbf_data_payment_fosa'], $data ['parent_average_payement'], $data ['entity_info'] ['entity_type_id'] == 6 ? 'hospital' : 'fosa' );
			
			$data ['last_quantity_report'] = $this->pbf->get_last_quantities_reports ( '', $entity_id );
			
			if (! empty ( $data ['pbf_detailed_data'] ['pbf_data'] )) {
				array_unshift ( $data ['pbf_detailed_data'] ['pbf_data'], array_keys ( $data ['pbf_detailed_data'] ['pbf_data'] [0] ) );
			}
			
			if (! empty ( $data ['pbf_qlt_data'] )) {
				array_unshift ( $data ['pbf_qlt_data'], array_keys ( $data ['pbf_qlt_data'] [0] ) );
			}
			if (! empty ( $data ['pbf_data_payment_fosa'] )) {
				array_unshift ( $data ['pbf_data_payment_fosa'], array_keys ( $data ['pbf_data_payment_fosa'] [0] ) );
			}
			if (! empty ( $data ['last_quantity_report'] )) {
				array_unshift ( $data ['last_quantity_report'], array_keys ( $data ['last_quantity_report'] [0] ) );
			}
		}
		
		//
		$data ['pbf_qlt_data'] = $this->pbf->clean_table_for_front ( $data ['pbf_qlt_data'] );
		
		$data ['pbf_data_payment_fosa'] = $this->pbf->clean_table_for_front ( $data ['pbf_data_payment_fosa'] );
		$data ['last_quantity_report'] = $this->pbf->clean_table_for_front ( $data ['last_quantity_report'] );
		
		$data ['pbf_detailed_data'] ['pbf_data_slice'] = array_slice ( $data ['pbf_detailed_data'] ['pbf_data'], 0, $data ['pbf_detailed_data'] ['tot_featured'] + 1 );
		
		// $data['quantity_chart'] = $this->__draw_quantity_chart(array_slice($data['pbf_data']['pbf_data_slice'],1));
		
		array_push ( $data ['pbf_detailed_data'] ['pbf_data_slice'], $this->pbf->clean_table_for_front ( $totals ) );
		
		$data ['pbf_detailed_data'] ['pbf_data_slice'] = $this->pbf->clean_table_for_front ( $data ['pbf_detailed_data'] ['pbf_data_slice'] );
		$data ['pbf_detailed_data'] ['pbf_data'] = $this->pbf->clean_table_for_front ( $data ['pbf_detailed_data'] ['pbf_data'] );
		$data ['last_quantity_report_slice'] = isset ( $data ['last_quantity_report'] ) ? array_slice ( $data ['last_quantity_report'], 0, $data ['pbf_detailed_data'] ['tot_featured'] + 1 ) : NULL;
		
		$data ['front_main_nav'] = $this->pbf->get_front_menu ();
		
		$year = date ( 'Y' );
		$data ['pop'] = round ( $data ['entity_info'] ['entity_pop'] * pow ( (1 + ($this->config->item ( 'pop_growth_rate' ) / 100)), ($year - $data ['entity_info'] ['entity_pop_year']) ) );
		
		// Si le resultat annuel est null, le pop=0
		if ($data ['pop'] == '0') {
			
			$data ['pop'] = '';
		}
		
		$data ['key_data'] = $this->pbf->get_keydata ( $geozone_parentid );
		
		if ($data ['entity_info'] ['entity_geo_lat'] != 0 && $data ['entity_info'] ['entity_geo_long'] != 0) {
			$config ['center'] = $data ['entity_info'] ['entity_geo_lat'] . ',' . $data ['entity_info'] ['entity_geo_long'];
			$config ['zoom'] = 'auto';
			$config ['map_type'] = 'SATELLITE';
			$config ['map_height'] = '230px';
			
			$config ['minifyJS'] = TRUE;
			$this->googlemaps->initialize ( $config );
			$marker = array ();
			$marker ['position'] = $data ['entity_info'] ['entity_geo_lat'] . ',' . $data ['entity_info'] ['entity_geo_long'];
			$marker ['title'] = $data ['entity_info'] ['entity_name'] . ' (' . $data ['entity_info'] ['entity_type_name'] . ')';
			
			if ($data ['entity_info'] ['entity_geo_lat'] != 0 && $data ['entity_info'] ['entity_geo_long'] != 0) {
				$this->googlemaps->add_marker ( $marker );
			}
			$data ['map'] = $this->googlemaps->create_map ();
		} else {
			// Si il n' y a pas de coordonnées géographiques, on affiche la carte sur le district mais sans markeur
			
			// get center postion of district
			$zzz = $this->geo_mdl->get_zone ( $entity ['parent_geozone_id'] );
			$geo_json = json_decode ( $zzz ['geo_lat_long'], true );
			// $config['center'] = $data['entity_info']['entity_geo_lat'].','.$data['entity_info']['entity_geo_long'];
			$config ['center'] = $geo_json ['latitude'] . ',' . $geo_json ['longitude'];
			
			$config ['zoom'] = 'auto';
			$config ['map_type'] = 'SATELLITE';
			$config ['map_height'] = '230px';
			
			$config ['minifyJS'] = TRUE;
			$this->googlemaps->initialize ( $config );
			$data ['map'] = $this->googlemaps->create_map ();
		}
		
		$data ['page_title'] = $data ['entity_info'] ['entity_name'] . ' (' . $data ['entity_info'] ['entity_type_name'] . ')';
		$data ['meta_description'] = $data ['page_title'];
		$data ['page'] = 'entitypage';
		
		// =====================================export===========================================================
		
		$data ['entity_id'] = $entity_id;
		// ===========================================================================================================
		
		if ($this->budgets_mdl->verif_budget () > 0) {
			$data ['budget_data'] = $this->pbf->get_budgets_entity ( $entity_id, $periods );
			$data ['verif_budget'] = 1;
		} else {
			$data ['verif_budget'] = 0;
		}
		$this->load->view ( 'front_body', $data );
	}
	function element($indicator_id, $geozone_id = '') {
		$this->load->model ( 'indicators_mdl' );
		
		$breadcrumb = $this->pbf->get_geozone_breadcrumb ( $geozone_id );
		
		$curent_geozone = empty ( $breadcrumb ) ? $this->lang->line ( 'app_country_name' ) : $breadcrumb ['lvl2_title'];
		
		if (! empty ( $breadcrumb )) {
			$data ['breadcrumb'] = ($breadcrumb ['lvl1_link'] != '') ? ' ' . anchor ( base_url () . 'data/element/' . $indicator_id . '/' . $breadcrumb ['lvl1_link'], $breadcrumb ['lvl1_title'] ) : '';
			$data ['breadcrumb'] .= empty ( $data ['breadcrumb'] ) ? $breadcrumb ['lvl2_title'] : ' > ' . $breadcrumb ['lvl2_title'];
		} else {
			$data ['breadcrumb'] = '';
		}
		
		$indicators = $this->indicators_mdl->get_indicator ( $indicator_id, $this->config->item ( 'language_abbr' ) );
		
		if (($geozone_id == '') || empty ( $geozone_id )) {
			$pop = $this->cms_mdl->get_pop_tot ();
			$population = round ( $pop ['tot'] );
		} else {
			$zone_info = $this->geo_mdl->get_zone ( $geozone_id );
			$population = $zone_info ['geozone_pop'];
		}
		
		$file_types = $indicators ['indicatorsfileypes'];
		
		$indicator_file_type = $file_types [count ( $file_types ) - 1];
		
		$target_abs = $indicator_file_type ['dataelts_target_abs'];
		$dataelts_target_rel = $indicator_file_type ['dataelts_target_rel'];
		
		if (! empty ( $dataelts_target_rel )) {
			$target_pop = eval ( 'return round(' . $population . $dataelts_target_rel . ');' );
		} else {
			$target_pop = $target_abs;
		}
		
		$data ['indicator'] = $indicators ['indicator'];
		
		$periods = $this->pbf->get_last_periods ( $this->config->item ( 'num_period_display' ), $this->config->item ( 'period_type' ) );
		
		if (($this->config->item ( 'period_type' ) == 'quarter') && (count ( $periods ) < $this->config->item ( 'min_period' ))) {
			$periods = $this->pbf->get_last_periods ( $this->config->item ( 'num_period_display' ), 'month' );
		}
		
		if (isset ( $periods )) { // vs !empty($periods)
			
			$data ['pbf_data'] = $this->pbf->get_element_details ( $periods, $indicator_id, $geozone_id );
			
			foreach ( $data ['pbf_data'] as $key => $d ) {
				
				foreach ( $d as $k => $v ) {
					$value = str_replace ( ',', '', $v );
					
					if (is_numeric ( $value )) {
						$formatted_value = $this->pbf->format_number ( $value );
						
						$data ['pbf_data'] [$key] [$k] = $formatted_value;
					}
				}
			}
			
			$type_elements = $data ['pbf_data'] [0];
			
			$type_keys = array_keys ( $type_elements );
			
			$type_element = $type_keys [0];
			
			$totals = $this->__count_total ( $data ['pbf_data'] );
			
			$data ['quantity_chart_zone'] = $this->__total_quantity_chart ( $totals, $type_element, $curent_geozone . ' : ' . $data ['indicator'] ['indicator_title'], $target_pop );
			
			$totals = $this->__format_total ( $totals );
			
			array_push ( $data ['pbf_data'], $totals );
			if (! empty ( $data ['pbf_data'] )) {
				array_unshift ( $data ['pbf_data'], array_keys ( $data ['pbf_data'] [0] ) );
			}
		}
		
		$data ['featured_docs'] = $this->cms_mdl->get_featured_items ( 30, 4 );
		$data ['featured_news'] = $this->cms_mdl->get_featured_items ( 29, 4 );
		$data ['featured_links'] = $this->cms_mdl->get_featured_items ( 32, 4 );
		$data ['featured_accounts'] = $this->pbf->get_feature_accounts ();
		$data ['front_main_nav'] = $this->pbf->get_front_menu ();
		
		$data ['page_title'] = $data ['indicator'] ['indicator_title'] . ' - ' . strtoupper ( $this->lang->line ( 'app_country_name' ) ) . strip_tags ( $data ['breadcrumb'] );
		$data ['meta_description'] = 'Indicator, ' . $data ['page_title'];
		$data ['page'] = 'elementpage';
		$this->load->view ( 'front_body', $data );
	}
	function perfomance($entity_class, $geozone_id = '') {
		$data_label = ($entity_class == 2) ? $this->lang->line ( 'front_admin_data_label' ) : $this->lang->line ( 'front_data_label' );
		
		$data ['indicator_title'] = $data_label;
		$data ['breadsegments'] = $entity_class;
		
		$data ['breadsegments'] .= ($geozone_id != '') ? '/' . $geozone_id : '';
		
		$periods = $this->pbf->get_last_quarters ( $this->config->item ( 'num_period_display' ) );
		
		if (isset ( $periods )) { // vs !empty($periods)
			
			$data ['pbf_data'] = $this->pbf->get_performance_details ( $periods, $entity_class, $geozone_id );
			
			if (! empty ( $data ['pbf_data'] )) {
				array_unshift ( $data ['pbf_data'], array_keys ( $data ['pbf_data'] [0] ) );
			}
		}
		
		$data ['featured_docs'] = $this->cms_mdl->get_featured_items ( 30, 4 );
		$data ['featured_news'] = $this->cms_mdl->get_featured_items ( 29, 4 );
		$data ['featured_links'] = $this->cms_mdl->get_featured_items ( 32, 4 );
		$data ['featured_accounts'] = $this->pbf->get_feature_accounts ();
		$data ['front_main_nav'] = $this->pbf->get_front_menu ();
		
		$data ['page'] = 'perfomancepage';
		$this->load->view ( 'front_body', $data );
	}
	function payment($entity_class, $geozone_id = '') {
		$periods = $this->pbf->get_last_quarters ( $this->config->item ( 'num_period_display' ) );
		
		// $this->load->lang('pbfapp_lang');
		
		$data ['indicator_title'] = $this->lang->line ( 'total_payement' );
		
		if (isset ( $periods )) { // vs !empty($periods)
			
			$data ['pbf_data'] = $this->pbf->get_payment_details ( $periods, $entity_class, $geozone_id );
			
			$firstKey = 0;
			
			foreach ( $data ['pbf_data'] as $k => $v ) {
				$firstKey = $k;
				break;
			}
			
			if (! empty ( $data ['pbf_data'] )) {
				array_unshift ( $data ['pbf_data'], array_keys ( $data ['pbf_data'] [$firstKey] ) );
			}
		}
		
		$data ['featured_docs'] = $this->cms_mdl->get_featured_items ( 30, 4 );
		$data ['featured_news'] = $this->cms_mdl->get_featured_items ( 29, 4 );
		$data ['featured_links'] = $this->cms_mdl->get_featured_items ( 32, 4 );
		$data ['featured_accounts'] = $this->pbf->get_feature_accounts ();
		// $data['front_main_nav'] = $this->cms_mdl->get_front_menu();
		$zone_name = $this->cms_mdl->get_zone_name ( $geozone_id );
		
		$data ['class'] = $entity_class;
		$data ['zone1'] = $zone_name ['zone1'];
		$data ['zone2'] = $zone_name ['zone2'];
		$data ['zone2id'] = $zone_name ['zone2id'];
		
		$data ['page_title'] = $data ['indicator_title'];
		$data ['page_title'] .= ! empty ( $data ['zone1'] ) ? ' : ' . $data ['zone1'] : '';
		$data ['page'] = 'perfomancepage';
		$data ['front_main_nav'] = $this->pbf->get_front_menu ();
		$this->load->view ( 'front_body', $data );
	}
	function quality_score_chart($periods) {
		// $year = time
		if (empty ( $periods ))
			return null;
		
		$quarters = array ();
		foreach ( $periods as $pkey => $pval ) {
			$quart = "Q" . $pval ['data_quarter'] . " " . $pval ['data_year'];
			// $quarter[$quart] = $pval['data_quarter'];
			$quarters [$quart] ['quarter'] = $pval ['data_quarter'];
			$quarters [$quart] ['year'] = $pval ['year'];
		}
		
		$this->load->model ( 'entities_mdl' );
		$this->load->library ( 'highcharts' );
		
		// TODO GET this from CMS Settings
		// We are now using top quality configuration
		$top = $this->pbf_mdl->get_topcms ();
		
		$params = json_decode ( $top ['content_params'], true );
		
		$entity_types = array ();
		
		foreach ( $params as $param ) {
			$entity_type = $this->entities_mdl->get_entitytype ( $param );
			$entity_types [$entity_type ['entity_type_name'] . '( ' . $entity_type ['entity_type_abbrev'] . ' )'] = $entity_type ['entity_type_id'];
		}
		
		$data ['axis'] ['categories'] = array_keys ( $entity_types );
		
		$this->highcharts->set_type ( 'column' ); // drauwing type
		$this->highcharts->set_title ( $this->lang->line ( 'front_quality' ) ); // set chart title: title, subtitle(optional)
		$this->highcharts->set_axis_titles ( $this->lang->line ( 'front_evolution' ), $this->lang->line ( 'front_evolution' ) ); // axis titles: x axis, y axis
		
		$categories = array (
				'categories' => array_keys ( $entity_types ) 
		);
		
		$this->highcharts->set_xAxis ( $categories );
		
		foreach ( $quarters as $key => $value ) {
			$data = array ();
			$chart_data = array ();
			foreach ( $entity_types as $k => $v ) {
				// TODO make sure the returned value is correct!!!
				$average = $this->pbf->get_quality_score ( $value ['quarter'], $v, $value ['year'] );
				array_push ( $data, ( int ) $average ['average'] );
			}
			
			$chart_data ['data'] = $data;
			$chart_data ['name'] = $key;
			
			$this->highcharts->set_serie ( $chart_data );
		}
		
		$credits->href = '#';
		$credits->text = $this->lang->line ( 'front_quality' );
		$credits->enabled = false;
		$this->highcharts->set_credits ( $credits );
		
		$this->highcharts->render_to ( 'graph_div' ); // choose a specific div to render to graph
		
		return $this->highcharts->render (); // we render js and div in same time
	}
	function quality_score_chart_zone($periods, $zone) {
		// $year = time
		$quarters = array ();
		foreach ( $periods as $pkey => $pval ) {
			$quart = "Q" . $pval ['data_quarter'] . " " . $pval ['data_year'];
			// $quarter[$quart] = $pval['data_quarter'];
			$quarters [$quart] ['quarter'] = $pval ['data_quarter'];
			$quarters [$quart] ['year'] = $pval ['data_year'];
		}
		
		$this->load->model ( 'entities_mdl' );
		$this->load->library ( 'highcharts' );
		
		// TODO GET this from CMS Settings
		// We are now using top quality configuration
		$top = $this->pbf_mdl->get_topcms ();
		
		$params = json_decode ( $top ['content_params'], true );
		
		$entity_types = array ();
		
		foreach ( $params as $param ) {
			$entity_type = $this->entities_mdl->get_entitytype ( $param );
			$entity_types [$entity_type ['entity_type_name'] . '( ' . $entity_type ['entity_type_abbrev'] . ' )'] = $entity_type ['entity_type_id'];
		}
		
		$data ['axis'] ['categories'] = array_keys ( $entity_types );
		
		$this->highcharts->set_type ( 'column' ); // drauwing type
		$this->highcharts->set_title ( $this->lang->line ( 'front_quality' ) ); // set chart title: title, subtitle(optional)
		$this->highcharts->set_axis_titles ( $this->lang->line ( 'front_evolution' ), $this->lang->line ( 'front_evolution' ) ); // axis titles: x axis, y axis
		
		$categories = array (
				'categories' => array_keys ( $entity_types ) 
		);
		
		$this->highcharts->set_xAxis ( $categories );
		
		$all_zeros = true;
		
		foreach ( $quarters as $key => $value ) {
			$data = array ();
			$chart_data = array ();
			foreach ( $entity_types as $k => $v ) {
				$average = $this->pbf->get_quality_score_zone ( $value ['quarter'], $v, $value ['year'], $zone );
				
				if ($all_zeros)
					$all_zeros = (( int ) $average ['average']) == 0;
				
				array_push ( $data, ( int ) $average ['average'] );
			}
			
			$chart_data ['data'] = $data;
			$chart_data ['name'] = $key;
			
			$this->highcharts->set_serie ( $chart_data );
		}
		
		if ($all_zeros)
			return null;
		
		$credits->href = '#';
		$credits->text = $this->lang->line ( 'front_evolution' );
		$credits->enabled = false;
		$this->highcharts->set_credits ( $credits );
		
		$this->highcharts->render_to ( 'graph_div' ); // choose a specific div to render to graph
		
		return $this->highcharts->render (); // we render js and div in same time
	}
	function json_chart() {
		if (isset ( $_POST ["year"] )) {
			$year = $_POST ['year'];
		} else {
			$year = date ( "Y" ) - 1;
		}
		
		$entity_type = 'fosa';
		$this->load->library ( 'highcharts' );
		$graph_axis_title = $this->lang->line ( 'sum_cfa' );
		$budget_period_graph = $this->pbf->get_last_budget_year ( $year );
		
		$data ['budget_graph'] = $this->pbf->get_budgets_all_entities ( $budget_period_graph );
		$data ['payement_graph'] = $this->pbf->get_totals_class ( $budget_period_graph );
		$data ['budget_year_data'] = $this->pbf->get_annual_budget ( $year );
		
		foreach ( $data ['budget_graph'] as $d ) {
			$indicators = array_slice ( $d, 1 );
			$row = array ();
			$cumul = 0;
			foreach ( $indicators as $k => $v ) {
				if ($this->config->item ( 'language' ) == 'francais') {
					$v = intval ( str_replace ( '.', '', $v ) );
				} else {
					$v = intval ( str_replace ( ',', '', $v ) );
				}
				$cumul = $cumul + $v;
				array_push ( $row, $cumul );
			}
		}
		
		foreach ( $data ['payement_graph'] as $d ) {
			$indicators = array_slice ( $d, 1 );
			$row2 = array ();
			$cumul = 0;
			foreach ( $indicators as $k => $v ) {
				
				if ($this->config->item ( 'language' ) == 'francais') {
					$v = intval ( str_replace ( '.', '', $v ) );
				} else {
					$v = intval ( str_replace ( ',', '', $v ) );
				}
				$cumul = $cumul + $v;
				array_push ( $row2, $cumul );
			}
		}
		
		foreach ( $data ['budget_year_data'] as $key => $d ) {
			$indicators = array_slice ( $d, 1 );
			$row3 = array ();
			foreach ( $indicators as $k => $v ) {
				$val = intval ( str_replace ( ',', '', $v ) );
				array_push ( $row3, $val );
				array_push ( $row3, $val );
				array_push ( $row3, $val );
				array_push ( $row3, $val );
			}
		}
		$indicators = array ();
		$indicators ['Trim. I ' . $year] = 0;
		$indicators ['Trim. II ' . $year] = 0;
		$indicators ['Trim. III ' . $year] = 0;
		$indicators ['Trim. V ' . $year] = 0;
		$categories = array (
				'categories' => array_keys ( $indicators ) 
		);
		$categories = $categories ['categories'];
		$budget_name = array (
				$this->lang->line ( 'budget_line_cumul' ) 
		);
		$payment_name = $this->lang->line ( 'payement_line_cumul' );
		$budget_year_name = $this->lang->line ( 'budget_line_annuel' );
		$budget = $row;
		$payment = $row2;
		$year_budget = $row3;
		
		if ((! empty ( $data ['budget_graph'] )) && (! empty ( $data ['payement_graph'] )) && (! empty ( $data ['budget_year_data'] ))) {
			$graph_data = array (
					'categories' => $categories,
					'budget' => $budget,
					'budget_name' => $budget_name,
					'payment' => $payment,
					'payment_name' => $payment_name,
					'year_budget' => $year_budget,
					'year_budget_name' => $this->lang->line ( 'budget_line_annuel' ) 
			);
			
			echo json_encode ( $graph_data );
		}
	}
	function json_chart_district() {
		if (isset ( $_POST ["year"] )) {
			$year = $_POST ['year'];
		} else {
			$year = date ( "Y" ) - 1;
		}
		$zone_id = $_POST ['zone_id'];
		$entity_type = 'fosa';
		$this->load->library ( 'highcharts' );
		$graph_axis_title = $this->lang->line ( 'sum_cfa' );
		
		$budget_period_graph = $this->pbf->get_last_budget_year ( $year );
		$data ['budget_graph'] = $this->pbf->get_budgets_district ( $zone_id, $budget_period_graph );
		$data ['payement_graph'] = $this->pbf->get_totals_class ( $budget_period_graph, $zone_id );
		$data ['budget_year_data'] = $this->pbf->get_annual_budget ( $year );
		
		foreach ( $data ['budget_graph'] as $d ) {
			$indicators = array_slice ( $d, 1 );
			$row = array ();
			$cumul = 0;
			foreach ( $indicators as $k => $v ) {
				if ($this->config->item ( 'language' ) == 'francais') {
					$v = intval ( str_replace ( '.', '', $v ) );
				} else {
					$v = intval ( str_replace ( ',', '', $v ) );
				}
				$cumul = $cumul + $v;
				array_push ( $row, $cumul );
			}
		}
		
		foreach ( $data ['payement_graph'] as $d ) {
			$indicators = array_slice ( $d, 1 );
			$row2 = array ();
			$cumul = 0;
			foreach ( $indicators as $k => $v ) {
				if ($this->config->item ( 'language' ) == 'francais') {
					$v = intval ( str_replace ( '.', '', $v ) );
				} else {
					$v = intval ( str_replace ( ',', '', $v ) );
				}
				$cumul = $cumul + $v;
				array_push ( $row2, $cumul );
			}
		}
		
		foreach ( $data ['budget_year_data'] as $key => $d ) {
			$indicators = array_slice ( $d, 1 );
			$row3 = array ();
			foreach ( $indicators as $k => $v ) {
				$val = intval ( str_replace ( ',', '', $v ) );
				array_push ( $row3, $val );
				array_push ( $row3, $val );
				array_push ( $row3, $val );
				array_push ( $row3, $val );
			}
		}
		$indicators = array ();
		$indicators ['Trim. I ' . $year] = 0;
		$indicators ['Trim. II ' . $year] = 0;
		$indicators ['Trim. III ' . $year] = 0;
		$indicators ['Trim. V ' . $year] = 0;
		$categories = array (
				'categories' => array_keys ( $indicators ) 
		);
		$categories = $categories ['categories'];
		$budget_name = array (
				$this->lang->line ( 'budget_line_cumul' ) 
		);
		$payment_name = $this->lang->line ( 'payement_line_cumul' );
		$budget_year_name = $this->lang->line ( 'budget_line_annuel' );
		$budget = $row;
		$payment = $row2;
		$year_budget = $row3;
		if ((! empty ( $data ['budget_graph'] )) && (! empty ( $data ['payement_graph'] )) && (! empty ( $data ['budget_year_data'] ))) {
			$graph_data = array (
					'categories' => $categories,
					'budget' => $budget,
					'budget_name' => $budget_name,
					'payment' => $payment,
					'payment_name' => $payment_name,
					'year_budget' => $year_budget,
					'year_budget_name' => $this->lang->line ( 'budget_line_annuel' ) 
			);
			echo json_encode ( $graph_data );
		}
	}
	function json_chart_zone() {
		if (isset ( $_POST ["year"] )) {
			$year = $_POST ['year'];
		} else {
			$year = date ( "Y" ) - 1;
		}
		$zone_id = $_POST ['zone_id'];
		$entity_type = 'fosa';
		$this->load->library ( 'highcharts' );
		$graph_axis_title = $this->lang->line ( 'sum_cfa' );
		$budget_period_graph = $this->pbf->get_last_budget_year ( $year );
		
		$data ['budget_graph'] = $this->pbf->get_budgets_zone ( $zone_id, $budget_period_graph );
		$data ['payement_graph'] = $this->pbf->get_totals_class ( $budget_period_graph, $zone_id );
		$data ['budget_year_data'] = $this->pbf->get_annual_budget ( $year );
		
		foreach ( $data ['budget_graph'] as $d ) {
			$indicators = array_slice ( $d, 1 );
			$row = array ();
			$cumul = 0;
			foreach ( $indicators as $k => $v ) {
				if ($this->config->item ( 'language' ) == 'francais') {
					$v = intval ( str_replace ( '.', '', $v ) );
				} else {
					$v = intval ( str_replace ( ',', '', $v ) );
				}
				$cumul = $cumul + $v;
				array_push ( $row, $cumul );
			}
		}
		
		foreach ( $data ['payement_graph'] as $d ) {
			$indicators = array_slice ( $d, 1 );
			$row2 = array ();
			$cumul = 0;
			foreach ( $indicators as $k => $v ) {
				if ($this->config->item ( 'language' ) == 'francais') {
					$v = intval ( str_replace ( '.', '', $v ) );
				} else {
					$v = intval ( str_replace ( ',', '', $v ) );
				}
				$cumul = $cumul + $v;
				array_push ( $row2, $cumul );
			}
		}
		
		foreach ( $data ['budget_year_data'] as $key => $d ) {
			$indicators = array_slice ( $d, 1 );
			$row3 = array ();
			foreach ( $indicators as $k => $v ) {
				$val = intval ( str_replace ( ',', '', $v ) );
				array_push ( $row3, $val );
				array_push ( $row3, $val );
				array_push ( $row3, $val );
				array_push ( $row3, $val );
			}
		}
		$indicators = array ();
		$indicators ['Trim. I ' . $year] = 0;
		$indicators ['Trim. II ' . $year] = 0;
		$indicators ['Trim. III ' . $year] = 0;
		$indicators ['Trim. V ' . $year] = 0;
		$categories = array (
				'categories' => array_keys ( $indicators ) 
		);
		$categories = $categories ['categories'];
		$budget_name = array (
				$this->lang->line ( 'budget_line_cumul' ) 
		);
		$payment_name = $this->lang->line ( 'payement_line_cumul' );
		$budget_year_name = $this->lang->line ( 'budget_line_annuel' );
		$budget = $row;
		$payment = $row2;
		$year_budget = $row3;
		if ((! empty ( $data ['budget_graph'] )) && (! empty ( $data ['payement_graph'] )) && (! empty ( $data ['budget_year_data'] ))) {
			$graph_data = array (
					'categories' => $categories,
					'budget' => $budget,
					'budget_name' => $budget_name,
					'payment' => $payment,
					'payment_name' => $payment_name,
					'year_budget' => $year_budget,
					'year_budget_name' => $this->lang->line ( 'budget_line_annuel' ) 
			);
			echo json_encode ( $graph_data );
		}
	}
	function json_chart_entity() {
		if (isset ( $_POST ["year"] )) {
			$year = $_POST ['year'];
		} else {
			$year = date ( "Y" ) - 1;
		}
		$entity_id = $_POST ['zone_id'];
		$entity_type = 'fosa';
		$this->load->library ( 'highcharts' );
		$graph_axis_title = $this->lang->line ( 'sum_cfa' );
		$budget_period_graph = $this->pbf->get_last_budget_year ( $year );
		// $data['budget_graph']=$this->pbf->get_budgets_entity($entity_id,$budget_period_graph);
		$data ['budget_graph'] = $this->pbf->get_budgets_entity ( $entity_id, $budget_period_graph );
		// $data['payement_graph']=$this->pbf->get_totals_class($budget_period_graph,$entity_id);
		$data ['payement_graph'] = $this->pbf->get_computed_payments ( $budget_period_graph, '', $entity_id, '1' );
		$data ['budget_year_data'] = $this->pbf->get_annual_budget_entity ( $year, $entity_id );
		
		foreach ( $data ['budget_graph'] as $d ) {
			$indicators = array_slice ( $d, 1 );
			$row = array ();
			$cumul = 0;
			foreach ( $indicators as $k => $v ) {
				
				$val = intval ( str_replace ( ',', '', $v ) );
				$cumul = $cumul + $val;
				array_push ( $row, $cumul );
			}
		}
		
		foreach ( $data ['payement_graph'] as $d ) {
			$indicators = array_slice ( $d, 1 );
			$row2 = array ();
			$cumul = 0;
			foreach ( $indicators as $k => $v ) {
				if ($this->config->item ( 'language' ) == 'francais') {
					$val = intval ( str_replace ( '.', '', $v ) );
				} else {
					$val = intval ( str_replace ( ',', '', $v ) );
				}
				$cumul = $cumul + $val;
				array_push ( $row2, $cumul );
			}
		}
		
		foreach ( $data ['budget_year_data'] as $key => $d ) {
			$indicators = array_slice ( $d, 1 );
			$row3 = array ();
			foreach ( $indicators as $k => $v ) {
				$val = intval ( str_replace ( ',', '', $v ) );
				array_push ( $row3, $val );
				array_push ( $row3, $val );
				array_push ( $row3, $val );
				array_push ( $row3, $val );
			}
		}
		$indicators = array ();
		$indicators ['Trim. I ' . $year] = 0;
		$indicators ['Trim. II ' . $year] = 0;
		$indicators ['Trim. III ' . $year] = 0;
		$indicators ['Trim. V ' . $year] = 0;
		$categories = array (
				'categories' => array_keys ( $indicators ) 
		);
		$categories = $categories ['categories'];
		$budget_name = array (
				$this->lang->line ( 'budget_line_cumul' ) 
		);
		$payment_name = $this->lang->line ( 'payement_line_cumul' );
		$budget_year_name = $this->lang->line ( 'budget_line_annuel' );
		$budget = $row;
		$payment = $row2;
		$year_budget = $row3;
		if ((! empty ( $data ['budget_graph'] )) && (! empty ( $data ['payement_graph'] )) && (! empty ( $data ['budget_year_data'] ))) {
			$graph_data = array (
					'categories' => $categories,
					'budget' => $budget,
					'budget_name' => $budget_name,
					'payment' => $payment,
					'payment_name' => $payment_name,
					'year_budget' => $year_budget,
					'year_budget_name' => $this->lang->line ( 'budget_line_annuel' ) 
			);
			echo json_encode ( $graph_data );
		}
	}
}