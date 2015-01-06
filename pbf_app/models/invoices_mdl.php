<?php

if (! ('BASEPATH'))
	exit ( 'No direct script access allowed' );
class Invoices_mdl extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	
	function create($data) {
		return $this->db->insert('pbf_invoices',$data);
	}
	
	function update($data){
		$this->db->where('pdf_file_name',$data['pdf_file_name']);
		return $this->db->update('pbf_invoices',$data);
	}
	
	function find_invoice($filename){
		return $this->db->get_where('pbf_invoices',array('pdf_file_name'=>$filename));
	}
	
	function exists($filename){
	  if(sizeof($this->find_invoice($filename))==1){
		return TRUE;
	  }else{
	    return FALSE;
	  }
	}
	function get_data_topublish($periods,$entity_id){
		$query = $this->db->select('entity_id,quality_bonus,total_invoice')->from('pbf_invoices')
		->where('report_type_id',$this->config->item ( 'report_feed_frontend' ))
		->where('data_quarter', $periods ['datafile_quarter'])
		->where('data_year', $periods ['datafile_year'])
		->where('entity_id',$entity_id);
		return $query->get()->row_array();
	}
}
