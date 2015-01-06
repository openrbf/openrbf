<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Alertes_curl extends CI_Controller {
	
	// ==========================================================================================================================
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'alertes_mdl' );
		$this->load->model ( 'alertes_log_mdl' );
		$this->lang->load ( 'alertes', $this->config->item ( 'language' ) );
		$this->lang->load ( 'hfrentities', $this->config->item ( 'language' ) );
	}
	function get_alertes($code = null) {
		if ($code !== '9835745257HLMERH675nbgtetttgGggrrtss') { // to be changed to correct key
			exit ();
		} else {
			$this->pbf->alertes ();
		}
	}
	// ===========================================================================================================================
}