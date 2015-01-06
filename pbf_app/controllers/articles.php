<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Articles extends CI_Controller {
	function __construct() {
		parent::__construct ();
		// Je modifie ce fichier h
		$this->load->model ( 'cms_mdl' );
		$this->lang->load ( 'front', $this->config->item ( 'language' ) );
		$this->session->set_userdata ( array (
				'front_item_category_id' => 29 
		) );
	}
	function index() {
		if ($this->config->item ( 'debug' ))
			$this->output->enable_profiler ( TRUE );
		
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->cms_mdl->get_articles ( $preps ['offset'], $preps ['terms'] );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['featured_docs'] = $this->cms_mdl->get_featured_items ( 30, 4 );
		// $data['featured_news'] = $this->cms_mdl->get_featured_items(29,4);
		$data ['featured_links'] = $this->cms_mdl->get_featured_items ( 32, 4 );
		$data ['featured_accounts'] = $this->pbf->get_feature_accounts ();
		$data ['front_main_nav'] = $this->pbf->get_front_menu ();
		
		$data ['page_title'] = $this->lang->line ( 'front_news_header' );
		$data ['meta_description'] = $this->lang->line ( 'front_news_header' );
		
		$data ['logo'] = $this->cms_mdl->get_logo ();
		$data ['page'] = 'newspage';
		$this->load->view ( 'front_body', $data );
	}
	function item($content_id) {
		$item = $this->cms_mdl->get_content_item ( $content_id );
		
		$this->session->set_userdata ( array (
				'front_item_category_id' => $item ['content_category'] 
		) );
		
		$data ['article_item'] = $this->cms_mdl->get_article ( $content_id );
		if (isset ( $data ['article_item'] ['content_link'] )) {
			$data ['article_item'] ['extension'] = end ( explode ( '.', $data ['article_item'] ['content_link'] ) );
		}
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
}

