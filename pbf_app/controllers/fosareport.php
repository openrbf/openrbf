<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
/**
 */
class Fosareport extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( array (
				'report_mdl',
				'entities_mdl',
				'pbf_mdl',
				'geo_mdl' 
		) );
		$this->lang->load ( 'report', $this->config->item ( 'language' ) );
	}
	function index($report_id = '') {
		$raw_reports = $this->report_mdl->get_reports ();
		
		$reports = array ();
		
		foreach ( $raw_reports as $raw_reports_val ) {
			
			$reports [] = anchor ( 'report/report/' . $raw_reports_val ['report_id'], $raw_reports_val ['report_title'] );
		}
		
		if (! empty ( $report_id )) {
			
			$config = $this->report_mdl->get_reports_conf ( $report_id );
			
			$data ['report_params'] = json_decode ( $config ['report_params'], true );
			$data ['report_title'] = $config ['report_title'];
			$data ['report_descript'] = $config ['report_descript'];
			$data ['report_id'] = $report_id;
			$data ['report_sql_conf'] = (strstr ( $config ['report_content_sql'], 'SELECT' ) != '') ? '' : $config ['report_content_sql'];
		}
		
		$data ['reports'] = $reports;
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'report_title' );
		// $data['mod_title']['/otheroptions/config/']=$this->pbf->rec_op_icon('config'); // would be good to give a quick access throu this button
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		$data ['page'] = 'reporting';
		$this->load->view ( 'body', $data );
	}
	function fraude($params) {
		$config = $this->report_mdl->get_reports_conf ( $this->input->get ( 'report_id' ) );
		
		$this->load->library ( array (
				'dpdf',
				'numbers/numbers_words' 
		) );
		$this->dpdf->folder ( './cside/pdfs/' );
		$this->dpdf->filename ( str_ireplace ( ' ', '', $config ['report_title'] ) . '.pdf' );
		$this->dpdf->paper ( 'a4', $config ['report_page_layout'] );
		$this->table->set_template ( $this->dpdf->table_tmpl );
		$this->dpdf->content .= $this->dpdf->set_header ();
		
		$this->load->model ( array (
				'entities_mdl' 
		) );
		$entity_info = $this->entities_mdl->get_entity ( $this->input->get ( 'entity_id' ) );
		
		$sql = "SELECT datafile_remark,datafile_info,user_fullname,user_name,user_jobtitle,user_phonenumber  FROM pbf_datafile LEFT JOIN pbf_users ON (pbf_users.user_id = pbf_datafile.datafile_author_id) WHERE pbf_datafile.filetype_id = '8' AND pbf_datafile.entity_id = '" . $this->input->get ( 'entity_id' ) . "' AND pbf_datafile.datafile_month = '" . $params ['datafile_month'] . "' AND pbf_datafile.datafile_year = '" . $params ['datafile_year'] . "'";
		
		$results = $this->db->query ( $sql )->row_array ();
		
		$this->dpdf->content .= '<br><br><br><p align="center"><b><u>' . strtoupper ( utf8_decode ( $params ['report_title'] ) ) . '</u></b></p><p><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><strong>REGION SANITAIRE: </strong>' . $entity_info ['parent_geozone_name'] . '</td>
    <td rowspan="4" valign="top"><strong>VERIFICATEUR :</strong><br>' . (($results ['user_fullname'] == '') ? '' : $results ['user_fullname'] . '<br>') . (($results ['user_jobtitle'] == '') ? '' : $results ['user_jobtitle'] . '<br>') . (($results ['user_name'] == '') ? '' : $results ['user_name'] . '<br>') . (($results ['user_phonenumber'] == '') ? '' : $results ['user_phonenumber']) . '</td>
  </tr>
  <tr>
    <td><strong>ZONE SANITAIRE: </strong>' . $entity_info ['geozone_name'] . '</td>
  </tr>
  <tr>
    <td><strong>FORMATION SANITAIRE: </strong>' . $entity_info ['entity_name'] . ' ' . $entity_info ['entity_type_abbrev'] . '</td>
  </tr>
  <tr><td><strong>PERIODE :</strong> ' . utf8_decode ( $this->lang->line ( 'app_month_' . $params ['datafile_month'] ) ) . ' ' . $params ['datafile_year'] . '</td></tr></table></p>';
		
		$this->dpdf->content .= '<br><br><br><br><p style="font-size:10px"><strong>DESCRIPTION:</strong><br><br>' . utf8_decode ( str_replace ( array (
				"\r\n",
				"\n" 
		), '<br />', $results ['datafile_remark'] ) ) . '</p>';
		
		$this->table->clear ();
		
		$signatories = json_decode ( $params ['report_signatories'], true );
		
		$this->table->set_template ( $this->dpdf->sign_tmpl );
		
		$this->table->add_row ( array (
				'data' => utf8_decode ( $signatories [0] ),
				'width' => '33%' 
		), array (
				'data' => utf8_decode ( $signatories [1] ),
				'width' => '34%' 
		), array (
				'data' => utf8_decode ( $signatories [2] ),
				'width' => '33%' 
		) );
		
		$this->dpdf->content .= '<p style="font-size:10px">' . $this->table->generate () . '</p>';
		
		$this->dpdf->html ( $this->dpdf->content );
		$this->dpdf->create ();
	}
	function production($params, $dataparm) {
		$this->load->library ( 'cezpdf' );
		$this->load->library ( 'numbers/numbers_words' );
		$this->load->helper ( 'pdf' );
		
		$this->load->model ( 'entities_mdl' );
		$params_array = array ();
		
		$report_params = json_decode ( $params ['report_params'], true );
		foreach ( $report_params as $param ) {
			
			$param = ($param == 'entity_id_2') ? 'entity_id' : $param;
			
			if (array_key_exists ( $param, $dataparm )) {
				
				$params_array ['param_id'] [] = $dataparm;
				$params_array ['param_caption'] [] = $this->pbf->get_param_caption ( $param, $dataparm [$param] );
			}
		}
		$report_params = $params_array; // $params_array containing the real params to use through out...
		
		$table_width = ($params ['report_page_layout'] == 'portrait') ? 550 : 820;
		
		$this->cezpdf->Cezpdf ( 'a4', $params ['report_page_layout'] );
		
		// creates the HEADER and FOOTER for the document we are creating.
		prep_pdf ( $params ['report_page_layout'], $params ['report_logo_position'], $params ['report_title'], $params ['report_subtitle'], $params_array ['param_caption'], 'add', $params ['report_header'] );
		
		$function_str = $this->pbf->get_runnable_pbf_script_for_indicator ( $dataparm ['datafile_year'], $dataparm ['datafile_quarter'] );
		
		$function_str_entity = $this->pbf->get_runnable_script ( $dataparm ['datafile_year'], $dataparm ['datafile_quarter'] );
		
		$raw_report_info = $this->report_mdl->get_quarterly_entity_report ( $dataparm );
		
		$entity = $this->entities_mdl->get_entity ( $dataparm ['entity_id'] );
		
		if (! empty ( $raw_report_info ['list_quantity'] )) {
			
			$buffer = array ();
			$category_1 = 0;
			$category_4 = 0;
			$tot_subsidies = 0;
			
			foreach ( $raw_report_info ['list_quantity'] as $r_key => $r_val ) {
				
				$buffer [$r_key] ['#'] = $r_key + 1;
				$buffer [$r_key] [ucwords ( $this->lang->line ( 'indicator_title' ) )] = $r_val ['indicator_title'];
				$buffer [$r_key] [utf8_decode ( $this->lang->line ( 'report_prod_forecast' ) )] = '';
				$buffer [$r_key] [utf8_decode ( $this->lang->line ( 'report_prod_achieved' ) )] = $r_val ['verified_value'];
				$buffer [$r_key] [utf8_decode ( $this->lang->line ( 'report_prod_unit_fee' ) )] = $r_val ['default_tarif'];
				$buffer [$r_key] [utf8_decode ( $this->lang->line ( 'report_prod_quality' ) )] = '';
				
				$enveloppe = $this->pbf->calculate_final_indicator_payment ( $raw_report_info ['list_quality'] ['datafile_total'], str_replace ( ',', '', $r_val ['indicator_montant'] ), $entity ['entity_pbf_group_id'], $function_str );
				
				$buffer [$r_key] [utf8_decode ( $this->lang->line ( 'report_prod_avail_budget' ) )] = number_format ( $enveloppe );
				
				if (($r_val ['indicator_category'] != 'Paludisme PMA' && $r_val ['indicator_category'] != 'Paludisme PCA')) {
					
					$buffer [$r_key] [utf8_decode ( $this->lang->line ( 'report_prod_quality' ) )] = $raw_report_info ['list_quality'] ['datafile_total'];
					$buffer [$r_key] [utf8_decode ( $this->lang->line ( 'report_prod_category_1' ) )] = number_format ( $enveloppe );
					$category_1 += $enveloppe;
					
					$buffer [$r_key] [utf8_decode ( $this->lang->line ( 'report_prod_category_4' ) )] = '';
					
					$tot_subsidies += str_replace ( ',', '', $r_val ['indicator_montant'] );
				}
				
				if (($r_val ['indicator_category'] == 'Paludisme PMA' || $r_val ['indicator_category'] == 'Paludisme PCA')) {
					
					$buffer [$r_key] [utf8_decode ( $this->lang->line ( 'report_prod_category_1' ) )] = '';
					$buffer [$r_key] [utf8_decode ( $this->lang->line ( 'report_prod_category_4' ) )] = number_format ( $enveloppe );
					$category_4 += $enveloppe;
				}
			}
			
			$final_payment = $this->pbf->calculate_final_payment ( $raw_report_info ['list_quality'] ['datafile_total'], $tot_subsidies, $entity ['entity_class'], $entity ['entity_type'], $entity ['entity_pbf_group_id'], $dataparm ['entity_geozone_id'], $function_str_entity );
			
			$buffer [$r_key + 1] [ucwords ( $this->lang->line ( 'indicator_title' ) )] = '<b>' . $this->lang->line ( 'report_sub_tot' ) . ' ' . $this->lang->line ( 'report_prod_category_1' ) . '</b>';
			$buffer [$r_key + 1] [utf8_decode ( $this->lang->line ( 'report_prod_category_1' ) )] = '<b>' . number_format ( $category_1 ) . '</b>';
			$buffer [$r_key + 2] [ucwords ( $this->lang->line ( 'indicator_title' ) )] = '<b>' . $this->lang->line ( 'report_sub_tot' ) . ' ' . $this->lang->line ( 'report_prod_category_4' ) . '</b>';
			$buffer [$r_key + 2] [utf8_decode ( $this->lang->line ( 'report_prod_category_4' ) )] = '<b>' . number_format ( $category_4 ) . '</b>';
			
			$plafond = $this->pbf->get_regional_avg ( $dataparm ['datafile_year'], $dataparm ['datafile_quarter'], '', $entity ['entity_pbf_group_id'] );
			
			$buffer [$r_key + 3] [ucwords ( $this->lang->line ( 'indicator_title' ) )] = '<b>' . $this->lang->line ( 'report_final_plafond' ) . ' ' . str_replace ( 'C', 'P', $entity ['entity_group_abbrev'] ) . '</b>';
			$buffer [$r_key + 3] [utf8_decode ( $this->lang->line ( 'report_prod_avail_budget' ) )] = '<b>' . number_format ( $plafond ) . '</b>';
			
			$buffer [$r_key + 4] [ucwords ( $this->lang->line ( 'indicator_title' ) )] = '<b>' . $this->lang->line ( 'report_sub_tot_payable' ) . ' ' . $this->lang->line ( 'report_prod_category_1' ) . '</b>';
			$buffer [$r_key + 4] [utf8_decode ( $this->lang->line ( 'report_prod_avail_budget' ) )] = '<b>' . number_format ( $final_payment ) . '</b>';
			
			$buffer [$r_key + 5] [ucwords ( $this->lang->line ( 'indicator_title' ) )] = '<b>' . $this->lang->line ( 'report_final_tot' ) . '</b>';
			$buffer [$r_key + 5] [utf8_decode ( $this->lang->line ( 'report_prod_avail_budget' ) )] = '<b>' . number_format ( $final_payment + $category_4 ) . '</b>';
			
			$somearray [utf8_decode ( $this->lang->line ( 'report_prod_forecast' ) )] ['justification'] = 'right';
			$somearray [utf8_decode ( $this->lang->line ( 'report_prod_achieved' ) )] ['justification'] = 'right';
			$somearray [utf8_decode ( $this->lang->line ( 'report_prod_unit_fee' ) )] ['justification'] = 'right';
			$somearray [utf8_decode ( $this->lang->line ( 'report_prod_quality' ) )] ['justification'] = 'right';
			$somearray [utf8_decode ( $this->lang->line ( 'report_prod_avail_budget' ) )] ['justification'] = 'right';
			$somearray [utf8_decode ( $this->lang->line ( 'report_prod_category_1' ) )] ['justification'] = 'right';
			$somearray [utf8_decode ( $this->lang->line ( 'report_prod_category_4' ) )] ['justification'] = 'right';
			
			$somearray [utf8_decode ( $this->lang->line ( 'report_prod_forecast' ) )] ['width'] = 80;
			$somearray [utf8_decode ( $this->lang->line ( 'report_prod_achieved' ) )] ['width'] = 80;
			$somearray [utf8_decode ( $this->lang->line ( 'report_prod_unit_fee' ) )] ['width'] = 80;
			$somearray [utf8_decode ( $this->lang->line ( 'report_prod_quality' ) )] ['width'] = 80;
			$somearray [utf8_decode ( $this->lang->line ( 'report_prod_avail_budget' ) )] ['width'] = 80;
			$somearray [utf8_decode ( $this->lang->line ( 'report_prod_category_1' ) )] ['width'] = 80;
			$somearray [utf8_decode ( $this->lang->line ( 'report_prod_category_4' ) )] ['width'] = 80;
			
			/*
			 * print_r($buffer); exit;
			 */
			
			$this->cezpdf->ezTable ( $buffer, '', '', array (
					'fontSize' => 7,
					'width' => $table_width,
					'cols' => $somearray 
			) );
			
			$this->cezpdf->ezSetDy ( - 3 );
			
			$this->cezpdf->ezText ( utf8_decode ( trim ( $this->lang->line ( 'report_sub_tot_payable' ) . ' ' . $this->lang->line ( 'report_prod_category_1' ) ) ) . ': <b> ' . $this->config->item ( 'app_country_currency' ) . ' ' . number_format ( $final_payment ) . ' (' . $this->numbers_words->toWords ( ($final_payment), ($this->config->item ( 'language_abbr' ) == 'en') ? 'en_US' : $this->config->item ( 'language_abbr' ) ) . ' ' . $this->config->item ( 'app_country_currency' ) . ').</b> ', 8, array (
					'justification' => 'left' 
			) );
			
			$this->cezpdf->ezText ( utf8_decode ( trim ( $this->lang->line ( 'report_sub_tot_payable' ) . ' ' . $this->lang->line ( 'report_prod_category_4' ) ) ) . ': <b> ' . $this->config->item ( 'app_country_currency' ) . ' ' . number_format ( $category_4 ) . ' (' . $this->numbers_words->toWords ( ($category_4), ($this->config->item ( 'language_abbr' ) == 'en') ? 'en_US' : $this->config->item ( 'language_abbr' ) ) . ' ' . $this->config->item ( 'app_country_currency' ) . ').</b> ', 8, array (
					'justification' => 'left' 
			) );
			
			$this->cezpdf->ezSetDy ( - 7 );
			
			$this->cezpdf->ezText ( utf8_decode ( $this->lang->line ( 'report_total_amount_in_letter' ) ) . '  <b>  ' . $this->config->item ( 'app_country_currency' ) . ' ' . number_format ( $final_payment + $category_4 ) . ' (' . $this->numbers_words->toWords ( ($final_payment + $category_4), ($this->config->item ( 'language_abbr' ) == 'en') ? 'en_US' : $this->config->item ( 'language_abbr' ) ) . ' ' . $this->config->item ( 'app_country_currency' ) . ').</b> ', 8, array (
					'justification' => 'left' 
			) );
			
			$this->cezpdf->ezNewPage ();
			
			$this->cezpdf->ezText ( '<b>FACTURE TRIMESTRIELLE FOSA POUR RELAIS  COMMUNAUTAIRES</b>', 11, array (
					'justification' => 'center' 
			) );
			
			$sql = "SELECT pbf_indicators.indicator_id,pbf_indicatorstranslations.indicator_title,SUM(pbf_datafiledetails.indicator_claimed_value) AS indicator_claimed_value,SUM(pbf_datafiledetails.indicator_verified_value) AS indicator_verified_value,SUM(pbf_datafiledetails.indicator_validated_value) AS indicator_validated_value,pbf_datafiledetails.indicator_tarif AS indicator_tarif,SUM(pbf_datafiledetails.indicator_montant) AS indicator_montant FROM pbf_indicators LEFT JOIN pbf_datafiledetails ON (pbf_indicators.indicator_id=pbf_datafiledetails.indicator_id) LEFT JOIN pbf_indicatorstranslations ON (pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id) LEFT JOIN pbf_datafile ON (pbf_datafile.datafile_id = pbf_datafiledetails.datafile_id) WHERE pbf_datafile.filetype_id='6' AND pbf_datafile.entity_id = '" . $dataparm ['entity_id'] . "' AND pbf_datafile.datafile_quarter = '" . $dataparm ['datafile_quarter'] . "' AND pbf_datafile.datafile_year = '" . $dataparm ['datafile_year'] . "' AND pbf_indicatorstranslations.indicator_language ='fr' GROUP BY pbf_indicators.indicator_id";
			
			$results = $this->db->query ( $sql )->result_array ();
			
			$sql = "SELECT pbf_datafile.datafile_total FROM pbf_datafile WHERE pbf_datafile.filetype_id='7' AND pbf_datafile.entity_id = '" . $dataparm ['entity_id'] . "' AND pbf_datafile.datafile_quarter = '" . $dataparm ['datafile_quarter'] . "' AND pbf_datafile.datafile_year = '" . $dataparm ['datafile_year'] . "'";
			
			$qresults = $this->db->query ( $sql )->row_array ();
			
			$totalrc = 0;
			$indicator_montant = 0;
			$payabletot = 0;
			$counter = 1;
			
			$buffer = array ();
			
			foreach ( $results as $k => $result ) {
				if ($result ['indicator_id'] != '51') {
					$buffer [$k] ['<b>No</b>'] = $counter;
					$buffer [$k] ['<b>Indicateur</b>'] = utf8_encode ( utf8_decode ( $result ['indicator_title'] ) );
					$buffer [$k] [utf8_decode ( '<b>Déclarée</b>' )] = number_format ( $result ['indicator_claimed_value'] );
					$buffer [$k] [utf8_decode ( '<b>Vérifiée</b>' )] = number_format ( $result ['indicator_verified_value'] );
					$buffer [$k] [utf8_decode ( '<b>Prix unitaire</b>' )] = number_format ( $result ['indicator_tarif'] );
					$buffer [$k] ['<b>Total</b>'] = number_format ( $result ['indicator_montant'] );
					$indicator_montant += $result ['indicator_montant'];
					$counter ++;
				} else {
					$totalrc = $result ['indicator_claimed_value'];
				}
			}
			
			$buffer [$counter] ['<b>No</b>'] = '';
			$buffer [$counter] ['<b>Indicateur</b>'] = '<b>TOTAL</b>';
			$buffer [$counter] [utf8_decode ( '<b>Déclarée</b>' )] = '';
			$buffer [$counter] [utf8_decode ( '<b>Vérifiée</b>' )] = '';
			$buffer [$counter] ['<b>Prix unitaire</b>'] = '';
			$buffer [$counter] ['<b>Total</b>'] = number_format ( $indicator_montant );
			
			$somearray = array ();
			$somearray ['<b>No</b>'] ['justification'] = 'right';
			$somearray [utf8_decode ( '<b>Déclarée</b>' )] ['justification'] = 'right';
			$somearray [utf8_decode ( '<b>Vérifiée</b>' )] ['justification'] = 'right';
			$somearray ['<b>Prix unitaire</b>'] ['justification'] = 'right';
			$somearray ['<b>Total</b>'] ['justification'] = 'right';
			
			$somearray [utf8_decode ( '<b>Déclarée</b>' )] ['width'] = 80;
			$somearray [utf8_decode ( '<b>Vérifiée</b>' )] ['width'] = 80;
			$somearray ['<b>Prix unitaire</b>'] ['width'] = 80;
			$somearray ['<b>Total</b>'] ['width'] = 80;
			
			$this->cezpdf->ezSetDy ( - 20 );
			
			$this->cezpdf->ezTable ( $buffer, '', '', array (
					'fontSize' => 8,
					'width' => $table_width,
					'cols' => $somearray 
			) );
			
			$this->cezpdf->ezSetDy ( - 20 );
			
			$this->cezpdf->ezText ( utf8_decode ( 'Nombre de relais  communautaires dans le district: <b>' ) . number_format ( $totalrc ) . '</b>', 10, array (
					'justification' => 'left' 
			) );
			$this->cezpdf->ezSetDy ( - 10 );
			$this->cezpdf->ezText ( utf8_decode ( 'Le score qualité trimestrielle des relais communautaires: <b>' ) . number_format ( $qresults ['datafile_total'], 2 ) . '%</b>', 10, array (
					'justification' => 'left' 
			) );
			
			$payabletot = round ( ($indicator_montant * $qresults ['datafile_total']) / 100 );
			
			$this->cezpdf->ezSetDy ( - 10 );
			$this->cezpdf->ezText ( utf8_decode ( 'Le montant alloué à la structure de santé pour les relais communautaires: <b>' ) . number_format ( $payabletot ) . ' FCFA</b>', 10, array (
					'justification' => 'left' 
			) );
			
			$this->cezpdf->ezSetDy ( - 20 );
			
			$signatories = json_decode ( $params ['report_signatories'], true );
			$sign [0] ['1'] = $signatories [0];
			$sign [0] ['2'] = $signatories [1];
			$sign [0] ['3'] = $signatories [2];
			
			$this->cezpdf->ezTable ( $sign, '', '', array (
					'fontSize' => 10,
					'showHeadings' => 0,
					'rowGap' => '0',
					'showLines' => 0,
					'shaded' => '0',
					'width' => $table_width,
					'cols' => array (
							'1' => array (
									'justification' => 'left',
									'width' => ($table_width / 3) 
							),
							'2' => array (
									'justification' => 'left',
									'width' => ($table_width / 3) 
							),
							'3' => array (
									'justification' => 'left',
									'width' => ($table_width / 3) 
							) 
					) 
			) );
		} else {
			
			$this->cezpdf->ezText ( $this->lang->line ( 'no_data_display' ), 8, array (
					'justification' => 'left' 
			) );
		}
		
		$this->cezpdf->ezStream ();
	}
	function show() {
		$submit = $this->input->post ( 'submit' );
		$data [] = explode ( '-', $this->input->post ( 'datafileQuarterYear' ) );
		$quarter = $data [0] [0];
		$year = $data [0] [1];
		$entity_id = $data [0] [2];
		$report_id = $data [0] [3];
		$entity_geaozone_id = $data [0] [4];
		$geozone_parentid = $data [0] [5];
		$dataparm = array (
				'report_id' => $report_id,
				'level_0' => $geozone_parentid,
				'entity_geozone_id' => $entity_geaozone_id,
				'entity_id' => $entity_id,
				'datafile_quarter' => $quarter,
				'datafile_year' => $year,
				'submit' => $submit 
		);
		$config = $this->report_mdl->get_reports_conf ( $report_id );
		
		if (is_null ( $config ['report_helper'] ) || $config ['report_helper'] == '') {
			echo 'Missing helper function...';
			exit ();
		}
		unset ( $config ['report_id'] );
		$params = array_merge_recursive ( $dataparm, $config );
		$this->production ( $params, $dataparm );
		call_user_func ( $params ['report_helper'], $params );
	}
	function downloadFile($file) {
		$file_name = $file;
		$mime = 'application/force-download';
		header ( 'Pragma: public' );
		header ( 'Expires: 0' );
		header ( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header ( 'Cache-Control: private', false );
		header ( 'Content-Type: ' . $mime );
		header ( 'Content-Disposition: attachment; filename="' . basename ( $file_name ) . '"' );
		header ( 'Content-Transfer-Encoding: binary' );
		header ( 'Connection: close' );
		readfile ( $file_name );
		exit ();
	}
	function show_fraud_home_page() {
		$this->load->model ( 'datafiles_mdl' );
		$config = $this->report_mdl->get_reports_conf ( $this->input->get ( 'report_id' ) );
		if (is_null ( $config ['report_helper'] ) || $config ['report_helper'] == '') {
			echo 'Missing helper function...';
			exit ();
		}
		
		unset ( $config ['report_id'] );
		$params = array_merge_recursive ( $this->input->get (), $config );
		if ($params ['report_title'] == 'Rapport de Fraude') {
			$filetype_name = 'Fraude';
			$datafilequarter = $params ['datafile_quarters'];
			$entityid = $params ['entity_id'];
			$datafilemonth = $params ['datafile_month'];
			$datafileyear = $params ['datafile_year'];
			$datafile_data = $this->datafiles_mdl->get_datafile_uploadFile ( $entityid, $datafilemonth, $datafileyear, $datafilequarter, $filetype_name );
			foreach ( $datafile_data as $datafile_key => $datafile_val ) {
				$uploaded_file = $datafile_val ['datafile_file_upload'];
			}
			if (is_null ( $uploaded_file ) || $uploaded_file == "") {
				echo "pas de fichier";
			} else {
				$this->downloadFile ( FCPATH . 'cside/contents/docs/' . $uploaded_file );
				exit ();
			}
		}
		$this->fraude ( $params );
		// call_user_func($params['report_helper'], $params);
	}
}

?>