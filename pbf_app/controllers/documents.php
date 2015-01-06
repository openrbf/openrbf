<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Documents extends CI_Controller {
	function __construct() {
		parent::__construct ();
		
		$this->load->model ( 'cms_mdl' );
		$this->lang->load ( 'front', $this->config->item ( 'language' ) );
		$this->session->set_userdata ( array (
				'front_item_category_id' => 30 
		) );
	}
	function index() {
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->cms_mdl->get_articles ( $preps ['offset'], $preps ['terms'] );
		
		$data ['list'] = $this->__set_doc_icon ( $data ['list'] );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		// $data['featured_docs'] = $this->cms_mdl->get_featured_items(30,4);
		// $data['featured_news'] = $this->cms_mdl->get_featured_items(29,4);
		$data ['featured_links'] = $this->cms_mdl->get_featured_items ( 32, 4 );
		$data ['featured_accounts'] = $this->pbf->get_feature_accounts ();
		$data ['front_main_nav'] = $this->pbf->get_front_menu ();
		
		$data ['page_title'] = $this->lang->line ( 'front_docs_header' );
		$data ['meta_description'] = $this->lang->line ( 'front_docs_header' );
		
		$data ['logo'] = $this->cms_mdl->get_logo ();
		$data ['page'] = 'documentpage';
		
		$this->load->view ( 'front_body', $data );
	}
	function item($content_id) {
		$item = $this->cms_mdl->get_content_item ( $content_id );
		
		$this->session->set_userdata ( array (
				'front_item_category_id' => $item ['content_category'] 
		) );
		
		$data ['article_item'] = $this->cms_mdl->get_article ( $content_id );
		
		$data ['featured_docs'] = $this->cms_mdl->get_featured_items ( 30, 4 );
		$data ['featured_news'] = $this->cms_mdl->get_featured_items ( 29, 4 );
		$data ['featured_links'] = $this->cms_mdl->get_featured_items ( 32, 4 );
		$data ['featured_accounts'] = $this->pbf->get_feature_accounts ();
		$data ['front_main_nav'] = $this->pbf->get_front_menu ();
		
		$data ['page_title'] = $data ['article_item'] ['content_title'];
		$data ['meta_description'] = $data ['article_item'] ['content_title'];
		
		$data ['page'] = 'articlepage';
		$this->load->view ( 'front_body', $data );
	}
	function __set_doc_icon($docs) {
		foreach ( $docs as $key => $doc ) {
			$extension = substr ( strrchr ( $doc ['content_link'], "." ), 1 );
			
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
			
			$docs [$key] = $doc;
		}
		
		return $docs;
	}
}

