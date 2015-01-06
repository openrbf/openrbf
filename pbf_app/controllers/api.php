<?php
require (APPPATH . '/libraries/REST_Controller.php');
class Api extends REST_Controller {
	function __construct() {
		parent::__construct ();
		
		$this->load->model ( 'api_mdl' );
	}
	function entity_get() {
		if (! $this->get ( 'id' )) {
			$this->response ( NULL, 400 );
		}
		
		$entity = $this->api_mdl->get_entity ( $this->get ( 'id' ) );
		
		if ($entity) {
			$this->response ( $entity, 200 ); // 200 being the HTTP response code
		} 

		else {
			$this->response ( NULL, 404 );
		}
	}
	function entities_get() {
		$entities = $this->api_mdl->get_entities ();
		
		if ($entities) {
			$this->response ( $entities, 200 );
		} 

		else {
			$this->response ( NULL, 404 );
		}
	}
	function geozone_get() {
		if (! $this->get ( 'id' )) {
			$this->response ( NULL, 400 );
		}
		
		$geozone = $this->api_mdl->get_geozone ( $this->get ( 'id' ) );
		
		if ($geozone) {
			$this->response ( $geozone, 200 ); // 200 being the HTTP response code
		} 

		else {
			$this->response ( NULL, 404 );
		}
	}
	function geozones_get() {
		$geozones = $this->api_mdl->get_geozones ();
		
		if ($geozones) {
			$this->response ( $geozones, 200 );
		} 

		else {
			$this->response ( NULL, 404 );
		}
	}
	function datafiles_get() {
		$datafiles = $this->api_mdl->get_datafiles ();
		
		if ($datafiles) {
			$this->response ( $datafiles, 200 );
		} 

		else {
			$this->response ( NULL, 404 );
		}
	}
	function datafile_get() {
		
		/*
		 * if((!$this->get('id'))||(!$this->get('filetype'))||(!$this->get('month'))||(!$this->get('quarter'))||(!$this->get('year'))) { $this->response(NULL, 400); }
		 */
		$datafile = $this->api_mdl->get_datafile ( $this->get ( 'id' ), $this->get ( 'filetype' ), $this->get ( 'month' ), $this->get ( 'quarter' ), $this->get ( 'year' ) );
		
		if ($datafile) {
			$this->response ( $datafile, 200 ); // 200 being the HTTP response code
		} 

		else {
			$this->response ( NULL, 404 );
		}
	}
	function datafiledetails_get() {
		if (! $this->get ( 'id' )) {
			$this->response ( NULL, 400 );
		}
		
		$datafiledetails = $this->api_mdl->get_datafiledetails ( $this->get ( 'id' ) );
		
		if ($datafiledetails) {
			$this->response ( $datafiledetails, 200 ); // 200 being the HTTP response code
		} 

		else {
			$this->response ( NULL, 404 );
		}
	}
}
?>