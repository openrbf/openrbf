<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Home extends CI_Controller {
	public function index() {
		if ($this->config->item ( 'debug' ))
			$this->output->enable_profiler ( TRUE );
		
		$this->lang->load ( 'front', $this->config->item ( 'language' ) );
		$this->lang->load ( 'indicators', $this->config->item ( 'language' ) );
		$this->lang->load ( 'geo', $this->config->item ( 'language' ) );
		
		$this->load->model ( 'cms_mdl' );
		$this->load->model ( 'datafiles_mdl' );
		$this->load->model ( 'report_mdl' );
		$periods = $this->pbf->get_last_quarters ( $this->config->item ( 'num_period_display' ) );
		
		$data ['report_id'] = $this->report_mdl->get_report_id_for_fraude ();
		
		$geo = $this->pbf->get_principal_geo_info ();
		
		$data ['geos_title'] = $this->lang->line ( 'geo_province' );
		
		$data ['map_render'] = $this->pbf->render_geozones ( $geo ['geo_id'] );
		
		$data ['featured_docs'] = $this->__set_doc_icon ( $this->cms_mdl->get_featured_items ( 30, 2 ) );
		$data ['featured_news'] = $this->cms_mdl->get_featured_items ( 29, 4 );
		$data ['featured_links'] = $this->cms_mdl->get_featured_items ( 32, 4 );
		$data ['featured_accounts'] = $this->pbf->get_feature_accounts ();
		$data ['home_top_left'] = $this->pbf->get_edito ( 'Home_Top_Left' );
		$data ['home_top_right'] = $this->pbf->get_edito ( 'Home_Top_Right' );
		$data ['home_below_map'] = $this->pbf->get_edito ( 'Home_Below_Map' );
		$data ['quality_definition'] = $this->pbf->get_edito ( 'Quality_definition' );
		$data ['fraude_reports'] = $this->datafiles_mdl->get_datafile_last_three_fraud ();
		$data ['top_quality'] = $this->pbf->get_top ();
		
		$data ['real_time_result'] = $this->pbf->get_real_time_result_home ();
		
		$data ['front_main_nav'] = $this->pbf->get_front_menu ();
		$data ['page_title'] = 'Openrbf';
		$data ['logo'] = $this->cms_mdl->get_logo ();
		$data ['page'] = 'homepage';
		
		$this->load->view ( 'front_body', $data );
	}
	function __set_doc_icon($docs) {
		foreach ( $docs as $key => $doc ) {
			$extension = substr ( strrchr ( $doc ['content_link'], "." ), 1 );
			
			if (! empty ( $extension )) {
				
				switch ($extension) {
					case 'pdf' :
						$doc ['icon_file'] = 'pdf-icon.png';
						break;
					case 'doc' :
					case 'docx' :
						$doc ['icon_file'] = 'word-icon.png';
						break;
					case 'xls' :
					case 'xlsx' :
						$doc ['icon_file'] = 'excel-icon.png';
						break;
					case 'zip' :
						$doc ['icon_file'] = 'zip-icon.png';
						break;
					default :
						$doc ['icon_file'] = 'zip-icon.png';
				}
				$file = APPPATH . '../cside/contents/docs/' . $doc ['content_link'];
				
				if (file_exists ( $file )) {
					$doc ['file_size'] = $this->pbf->human_filesize ( filesize ( $file ) );
				} else {
					$doc ['file_size'] = '';
				}
			} else {
				$doc ['icon_file'] = null;
				$doc ['file_size'] = '';
			}
			
			$docs [$key] = $doc;
		}
		
		return $docs;
	}
	
	/*
	 * echos geo_json data to draw maps poygons
	 */
	function get_geo_json($geo_id, $district_id = '') {
		$return = array ();
		$return ['layer'] = $this->pbf->render_geo_json ( $geo_id, $district_id );
		$return ['map'] = $this->pbf->get_center_coords ( $district_id );
		
		echo json_encode ( $return );
	}
	function heatmap() {
		$data ['page'] = 'heatmap';
		
		$this->load->view ( 'front_body', $data );
	}
}

