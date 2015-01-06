<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Exports extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'exports_mdl' );
		$this->lang->load ( 'exports', $this->config->item ( 'language' ) );
		$this->load->library ( 'pearloader' );
		$this->pearloader->load ( 'Writer' );
	}
	function export($year = '', $offset = null) {
		$file_name_is = FCPATH . 'cside/exports/' . str_replace ( ' ', '_', 'export_alice' ) . '.xls';
		
		$excel = new Spreadsheet_Excel_Writer ( $file_name_is );
		
		$zones = $this->pbf_mdl->render_geozones ( 3 );
		
		foreach ( $zones as $zone ) {
			if ($zone ['geozone_active'] == 1) {
				$sql = "select entity_name, pl.lookup_title, et.entity_type_name, ec.entity_class_name,entity_responsible_name,entity_phone_number, entity_address,
                entity_geo_long, entity_geo_lat from pbf_entities 
                LEFT JOIN pbf_entityclasses ec on pbf_entities.entity_class=ec.entity_class_id
                LEFT JOIN pbf_entitytypes et on et.entity_type_id = pbf_entities.entity_type
                LEFT JOIN pbf_lookups pl on pl.lookup_id = pbf_entities.entity_status
                where entity_geozone_id='" . $zone ['geozone_id'] . "'";
				
				$sheet = & $excel->addWorksheet ( $zone ['geozone_name'] );
				$sheet->setPaper ( 9 ); // Définit une page A4
				$sheet->setLandscape (); // Définit une orientation Paysage.
				                         
				// ==========================Ajout d'un formatage==========================================================================
				$titleFormat = $excel->addFormat ();
				$titleFormat->setFontFamily ( 'Helvetica' );
				$titleFormat->setBold ();
				$titleFormat->setSize ( '10' );
				
				$columns = array (
						'Entity name',
						'Entity Status',
						'Entity type',
						'Entity Class',
						'Entity responsible',
						'Entity phone number',
						'Entyity Adress',
						'Longitude',
						'Latitude' 
				);
				
				$rowKeyVar = 0;
				
				foreach ( $columns as $column_key => $column_val ) {
					$sheet->setColumn ( 0, 5, 35 );
					$sheet->write ( $rowKeyVar, $column_key, mb_convert_encoding ( $column_val, "windows-1252", "UTF-8" ), $titleFormat );
				}
				
				$entities = $this->db->query ( $sql )->result_array ();
				
				$NextRow = 1;
				foreach ( $entities as $sheet_row ) {
					
					$keys = array_keys ( $sheet_row );
					
					foreach ( $keys as $column_key => $column_val ) {
						
						$sheet->write ( $NextRow, $column_key, mb_convert_encoding ( $sheet_row [$column_val], "windows-1252", "UTF-8" ) );
					}
					
					$NextRow ++;
				}
			}
		}
		
		// ===============Enregistrement du fichier Excel sur le disque dur=================================================
		$excel->close ();
	}
	function index($year = '', $offset = null) {
		if (empty ( $offset ))
			
			$offset = 0;
			
			// if year not specified, grab and use the current year
		if (empty ( $year ))
			$year = date ( 'Y' );
		$this->load->helper ( 'file' );
		
		$preps = $this->pbf->prep_listing_terms_uri_keys_custom ();
		$data = $this->exports_mdl->get_exports ( $offset, $preps ['terms'] );
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'exports_title' ) . ' [' . $data ['records_num'] . ' ]';
		$data ['mod_title'] ['/exports/process_files/' . $year] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['/exports/addconfig'] = $this->pbf->rec_op_icon ( 'config' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		$years = $this->exports_mdl->get_export_years ();
		$filenameUser = FCPATH . 'cside/exports/' . str_replace ( ' ', '_', 'List_User.xls' );
		$data ['export_link'] .= (file_exists ( $filenameUser )) ? anchor ( '/exports/export_list_User/', '' . str_replace ( '_', ' ', $this->lang->line ( 'export_list_user' ) . '.xls' ) ) : anchor ( '/exports/export_list_User/list_User/', '' . str_replace ( '_', ' ', $this->lang->line ( 'export_list_user' ) . '.xls' ) );
		$download_link = base_url () . 'cside/exports/' . str_replace ( ' ', '_', 'List_User.xls' );
		$data ['download'] = (file_exists ( $filenameUser )) ? $this->pbf->rec_op_icon ( 'download_record', $download_link ) : '';
		$data ['export'] = $this->pbf->rec_op_icon ( 'small_add', '/exports/export_list_User/list_User/' );
		foreach ( $data ['list'] as $k => $v ) {
			
			$filename = FCPATH . 'cside/exports/' . str_replace ( ' ', '_', $data ['list'] [$k] ['exports_title'] ) . '_' . $year . '.xls';
			
			$del_path = '/exports/delconf/' . $data ['list'] [$k] ['exports_id'] . '/' . str_replace ( ' ', '_', $data ['list'] [$k] ['exports_title'] ) . '.xls';
			
			$download_link = base_url () . 'cside/exports/' . utf8_encode ( str_replace ( ' ', '_', $data ['list'] [$k] ['exports_title'] ) . '_' . $year . '.xls' );
			
			$data ['list'] [$k] ['exports_title'] = (file_exists ( $filename )) ? anchor_popup ( $download_link, $data ['list'] [$k] ['exports_title'], array (
					'width' => '300',
					'height' => '300' 
			) ) : $data ['list'] [$k] ['exports_title'];
			$data ['list'] [$k] ['exports_year'] = $year;
			$data ['list'] [$k] ['size'] = (file_exists ( $filename )) ? round ( filesize ( $filename ) / 1000 ) . ' Kb' : '';
			$data ['list'] [$k] ['date'] = (file_exists ( $filename )) ? date ( "F jS Y", filemtime ( $filename ) ) : '';
			$data ['list'] [$k] ['export'] = $this->pbf->rec_op_icon ( 'small_add', 'exports/process_file/' . $data ['list'] [$k] ['exports_id'] . '/' . $year );
			$data ['list'] [$k] ['download'] = (file_exists ( $filename )) ? $this->pbf->rec_op_icon ( 'download_record', $download_link ) : '';
			$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', 'exports/editconfig/' . $data ['list'] [$k] ['exports_id'] );
			
			$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', $del_path );
			
			$data ['list'] [$k] ['exports_id'] = $k + $offset + 1;
			unset ( $data ['list'] [$k] ['filetype_id'] );
			unset ( $data ['list'] [$k] ['datatype'] );
			unset ( $data ['list'] [$k] ['level_0'] );
			unset ( $data ['list'] [$k] ['entity_geozone_id'] );
		}
		
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'exports_title_file_name' ),
				$this->lang->line ( 'exports_year' ),
				$this->lang->line ( 'exports_title_file_size' ),
				$this->lang->line ( 'exports_title_file_date' ),
				'',
				'',
				'' 
		) );
		/* $data['export_link'].=(file_exists($filename))?anchor('/exports/export_list_User/',' List_User.xls'):anchor('/exports/export_list_User/list_User/',' List_User.xls'); */
		
		$export_years = array ();
		$this->pbf->get_pagination ( $data ['records_num'], ! empty ( $year ) ? $year : date ( 'Y' ), $preps ['uri_segment'] );
		foreach ( $years as $year ) {
			$link = anchor ( site_url ( 'exports/exports/' . $year ['export_year'] ), $year ['export_year'] );
			array_push ( $export_years, $link );
		}
		
		$tab_menus = implode ( '&nbsp;&nbsp;|&nbsp;&nbsp;', $export_years );
		$data ['tab_menus'] = $tab_menus;
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
	function editconfig($config_id) {
		$data ['exports'] = $this->exports_mdl->get_export ( $config_id );
		
		$this->addconfig ( $data );
	}
	function addconfig($data = '') {
		$data ['filetypes'] = $this->pbf->get_filetypes_lookup ();
		
		$data ['datatype'] = array (
				'indicator_claimed_value' => 'Quantités declarées',
				'indicator_verified_value' => 'Quantités vérifiés',
				// 'indicator_validated_value' => 'indicator_validated_value',
				'indicator_tarif' => 'Tarif',
				'indicator_montant' => 'Montant' 
		);
		$this->load->model ( 'geo_mdl' );
		
		$zones_array = $this->geo_mdl->get_regions ();
		$zones = array ();
		foreach ( $zones_array as $r ) {
			// =$r['geozone_name'];
			$children = $this->geo_mdl->get_zones_by_parent ( $r ['geozone_id'] );
			
			foreach ( $children as $child ) {
				if ($child ['geozone_active'] == 1) {
					$zones [$r ['geozone_name']] [$child ['geozone_id']] = $child ['geozone_name'];
				}
			}
		}
		
		$data ['districts'] = $zones;
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'exports_frm_title' );
		
		$data ['page'] = 'exportconf_frm';
		$this->load->view ( 'body', $data );
	}
	function delconf($exports_id, $exports_title) {
		if ($this->exports_mdl->del_export ( $exports_id )) {
			
			unlink ( FCPATH . 'cside/exports/' . $exports_title );
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'exports_conf_delete_success' ) 
			) );
		} 

		else {
			
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'exports_conf_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( 'exports' );
	}
	function saveconf() {
		$exports = $this->input->post ();
		
		print_r ( $exports );
		
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( 'exports_title', 'exports title', 'trim|required' );
		
		if ($this->form_validation->run () == FALSE) {
			$this->add ( $exports );
		} else {
			unset ( $exports ['submit'] );
			
			if ($this->exports_mdl->save_conf ( $exports )) {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'success',
						'mod_msg' => $this->lang->line ( 'exports_conf_save_success' ) 
				) );
			} else {
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'exports_conf_save_error' ) 
				) );
			}
			$this->pbf->set_eventlog ( '', 0 );
			redirect ( 'exports/' );
		}
	}
	function process_file($exports_id, $year = '') {
		if (empty ( $year )) {
			$year = date ( 'Y' );
		}
		// ================Clean db before export from computed_routine_data tables==============================================
		$this->exports_mdl->drop_routine_data_all ();
		// ======================================================================================================================
		$lang = ($this->config->item ( 'language' ) == 'francais' ? 'fr' : 'en');
		$ext_aleatoire = rand ( 1, 1000 );
		$this->exports_mdl->set_routine_data_table ( $ext_aleatoire, $year, $lang );
		$task = $this->exports_mdl->get_export ( $exports_id );
		$columns = $this->exports_mdl->get_file_columns ( $task ['filetype_id'], $lang );
		$raw_data = $this->exports_mdl->get_file_contents ( $task, $columns, $ext_aleatoire );
		$columns = $raw_data->list_fields ();
		$raw_data = $raw_data->result_array ();
		
		// =====================creation du fchier excel sur le disque dur====================================================
		$file_name_is = FCPATH . 'cside/exports/' . str_replace ( ' ', '_', $task ['exports_title'] ) . '_' . $year . '.xls';
		
		// =====================creation d'une feuille====================================================
		$excel = new Spreadsheet_Excel_Writer ( $file_name_is );
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
		
		// ===============Enregistrement du fichier Excel sur le disque dur=================================================
		
		if ($excel->close ()) {
			$this->pbf->set_eventlog ( 'export_created', 1 );
		}
		$this->exports_mdl->drop_routin_data ( $ext_aleatoire );
		redirect ( 'exports/exports/' . $year );
	}
	function export_odk() {
		$lang = ($this->config->item ( 'language' ) == 'francais' ? 'fr' : 'en');
		
		// =====================creation du fchier excel sur le disque dur====================================================
		$file_name_is = 'Export_ODK.xls';
		
		// =====================creation d'une feuille====================================================
		$excel = new Spreadsheet_Excel_Writer ();
		$excel->send ( $file_name_is );
		
		$sheet = & $excel->addWorksheet ( 'choices' );
		$sheet->setPaper ( 9 ); // Définit une page A4
		$sheet->setLandscape (); // Définit une orientation Paysage.
		                         
		// ==========================Ajout d'un formatage==========================================================================
		$titleFormat = $excel->addFormat ();
		$titleFormat->setFontFamily ( 'Helvetica' );
		$titleFormat->setBold ();
		$titleFormat->setSize ( '10' );
		
		/*
		 * //=========================Titres de colonnes ======================================================================= $NextRow=0; foreach($columns as $column_key => $column_val){ $sheet->write($NextRow,$column_key,mb_convert_encoding($column_val,"windows-1252","UTF-8"),$titleFormat); } //=============================Chargement des donnees vers le fichier Excel============================ $NextRow++; foreach($raw_data as $sheet_row){ foreach($columns as $column_key => $column_val){ $sheet_row[$column_val] = ($column_val=='Mois')?$this->lang->line('app_month_'.$sheet_row[$column_val]):$sheet_row[$column_val]; $sheet->write($NextRow,$column_key, mb_convert_encoding($sheet_row[$column_val],"windows-1252","UTF-8")); } $NextRow++; } //===============Enregistrement du fichier Excel sur le disque dur=================================================
		 */
		
		if ($excel->close ()) {
			$this->pbf->set_eventlog ( 'export_created', 1 );
		}
		// redirect('hfrentities/classes/');
	}
	function process_files($year) {
		// ================Clean db before export from computed_routine_data tables==============================================
		$this->exports_mdl->drop_routine_data_all ();
		// ======================================================================================================================
		$lang = ($this->config->item ( 'language' ) == 'francais' ? 'fr' : 'en');
		$ext_aleatoire = rand ( 1, 1000 );
		$this->exports_mdl->set_routine_data_table ( $ext_aleatoire, $year, $lang );
		
		$data = $this->exports_mdl->get_exports ();
		
		foreach ( $data ['list'] as $task ) {
			
			$columns = $this->exports_mdl->get_file_columns ( $task ['filetype_id'], $lang );
			$raw_data = $this->exports_mdl->get_file_contents ( $task, $columns, $ext_aleatoire );
			$columns = $raw_data->list_fields ();
			$raw_data = $raw_data->result_array ();
			
			// =====================creation du fchier excel sur le disque dur====================================================
			$file_name_is = FCPATH . 'cside/exports/' . str_replace ( ' ', '_', $task ['exports_title'] ) . '_' . $year . '.xls';
			
			// =====================creation du fchier excel sur le disque dur====================================================
			$excel = new Spreadsheet_Excel_Writer ( $file_name_is );
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
			
			// ===============Enregistrement du fichier Excel sur le disque dur=================================================
			$excel->close ();
		}
		$this->exports_mdl->drop_routin_data ( $ext_aleatoire );
		$this->pbf->set_eventlog ( 'export_created', 1 );
		redirect ( 'exports/exports/' . $year );
	}
	function export_list_User() {
		$this->load->library ( "phpexcel" );
		$this->load->library ( "PHPExcel/IOFactory" );
		$data = $this->exports_mdl->get_User (); // print_r($data); exit;
		$detail_data = $data;
		$data_wb = new PHPExcel ();
		
		$data_wb->getProperties ()->setCreator ( "Portail FBR" )->setLastModifiedBy ( "Portail FBR" )->setTitle ( substr ( "User", 0, 30 ) )->setSubject ( "User list" )->setDescription ( "User list" )->setKeywords ( $data ['exports_title'] )->setCategory ( $data ['exports_title'] );
		
		$data_wb_sheet = $data_wb->createSheet ();
		
		$data_wb_sheet->getPageSetup ()->setOrientation ( PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE )->setPaperSize ( PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4 )->setFitToPage ( true );
		
		$data_wb_sheet->getPageMargins ()->setTop ( 0.40 )->setRight ( 0.40 )->setLeft ( 0.40 )->setBottom ( 0.40 );
		
		$data_wb_sheet->setTitle ( substr ( User, 0, 30 ) );
		$columns_title = array (
				$this->lang->line ( 'user' ),
				$this->lang->line ( 'access_zone' ),
				$this->lang->line ( 'group' ) 
		);
		$NextRow = 1; // print_r($columns_title); exit;
		foreach ( $columns_title as $column_key => $column_val ) {
			$data_wb_sheet->setCellValueByColumnAndRow ( $column_key, $NextRow, $column_val );
		}
		
		$NextRow ++;
		// echo "<pre>"; print_r($detail_data); echo "<br/><br/><br/>";echo $NextRow."<br/>"; $i=0; echo "</pre>";
		foreach ( $detail_data as $row_key => $row_value ) {
			
			$i = 0;
			foreach ( $detail_data [$row_key] as $k => $v ) {
				$data_wb_sheet->setCellValueByColumnAndRow ( $i, $NextRow, $v );
				$name = "" . $detail_data [$i] ['name'];
				$group = "" . $detail_data [$i] ['user_group'];
				
				$i ++;
			}
			
			$NextRow ++;
		}
		$lastColumn = $data_wb_sheet->getHighestColumn ();
		
		$data_wb_sheet->getStyle ( 'A1:' . $lastColumn . '1' )->getFont ()->setBold ( true );
		$data_wb_sheet->getStyle ( 'A1:' . $lastColumn . $NextRow )->getBorders ()->getAllBorders ()->setBorderStyle ( PHPExcel_Style_Border::BORDER_THIN );
		
		$data_wb->getSheet ( 0 )->setSheetState ( PHPExcel_Worksheet::SHEETSTATE_VERYHIDDEN );
		$data_wb->setActiveSheetIndex ( 1 );
		
		$objWriter = IOFactory::createWriter ( $data_wb, 'Excel2007' );
		
		$file_name_is = FCPATH . 'cside/exports/' . str_replace ( ' ', '_', str_replace ( ' ', '_', 'List_User.xls' ) );
		
		$objWriter->save ( $file_name_is );
		redirect ( "exports/" );
	}
}