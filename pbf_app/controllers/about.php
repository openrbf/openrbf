<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class About extends CI_Controller {
	function __construct() {
		parent::__construct ();
		
		$this->load->model ( 'cms_mdl' );
		$this->lang->load ( 'front', $this->config->item ( 'language' ) );
		$this->session->set_userdata ( array (
				'front_item_category_id' => 31 
		) );
	}
	function index() {
		if ($this->config->item ( 'debug' ))
			$this->output->enable_profiler ( TRUE );
		
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		
		$data = $this->cms_mdl->get_articles ( $preps ['offset'], $preps ['terms'] );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['featured_docs'] = $this->cms_mdl->get_featured_items ( 30, 4 );
		$data ['featured_news'] = $this->cms_mdl->get_featured_items ( 29, 4 );
		$data ['featured_links'] = $this->cms_mdl->get_featured_items ( 32, 4 );
		$data ['featured_accounts'] = $this->pbf->get_feature_accounts ();
		$data ['featured_accounts_display'] = $data ['featured_accounts'];
		$data ['front_main_nav'] = $this->pbf->get_front_menu ();
		
		foreach ( $data ['featured_accounts_display'] as $key => $val ) {
			
			$data ['featured_accounts_display'] [$key] ['user_fullname'] = mailto ( $data ['featured_accounts_display'] [$key] ['user_name'], $data ['featured_accounts_display'] [$key] ['user_fullname'] );
			unset ( $data ['featured_accounts_display'] [$key] ['user_name'] );
		}
		
		array_unshift ( $data ['featured_accounts_display'], array (
				$this->lang->line ( 'front_about_actor' ),
				$this->lang->line ( 'front_about_role' ),
				$this->lang->line ( 'front_about_phonenumber' ) 
		) );
		$data ['page_title'] = $this->lang->line ( 'front_about' );
		$data ['meta_description'] = $this->lang->line ( 'front_about' );
		
		$data ['logo'] = $this->cms_mdl->get_logo ();
		$data ['page'] = 'about';
		$this->load->view ( 'front_body', $data );
	}
	function item($content_id) {
		$data ['article_item'] = $this->cms_mdl->get_article ( $content_id );
		
		$data ['featured_docs'] = $this->cms_mdl->get_featured_items ( 30, 4 );
		$data ['featured_news'] = $this->cms_mdl->get_featured_items ( 29, 4 );
		$data ['featured_links'] = $this->cms_mdl->get_featured_items ( 32, 4 );
		$data ['featured_accounts'] = $this->pbf->get_feature_accounts ();
		$data ['front_main_nav'] = $this->pbf->get_front_menu ();
		
		$data ['page'] = 'articlepage';
		$this->load->view ( 'front_body', $data );
	}
}

