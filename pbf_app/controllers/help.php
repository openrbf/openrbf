<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Help extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->lang->load ( 'help', $this->config->item ( 'language' ) );
	}
	function index() {
		$this->load->helper ( 'file' );
		
		$raw_files = get_dir_file_info ( $_SERVER {'DOCUMENT_ROOT'} . '/fbr/cside/help/', FALSE );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'help_title' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		foreach ( $raw_files as $file_key => $file_val ) {
			
			$raw_files [$file_key] ['name'] = anchor_popup ( base_url () . 'cside/help/' . utf8_encode ( $file_val ['name'] ), utf8_encode ( $file_val ['name'] ), array () );
			$raw_files [$file_key] ['size'] = round ( $file_val ['size'] / 1000 ) . ' Kb';
			$raw_files [$file_key] ['date'] = substr ( standard_date ( 'DATE_RFC822', $file_val ['date'] ), 0, 14 );
			
			unset ( $raw_files [$file_key] ['server_path'] );
			unset ( $raw_files [$file_key] ['relative_path'] );
		}
		
		$data ['list'] = $raw_files;
		
		array_unshift ( $data ['list'], array (
				$this->lang->line ( 'help_list_name' ),
				$this->lang->line ( 'help_list_size' ),
				$this->lang->line ( 'help_list_date' ) 
		) );
		
		$this->pbf->get_pagination ( 5, '', '' );
		
		$data ['page'] = 'list';
		$this->load->view ( 'body', $data );
	}
}
