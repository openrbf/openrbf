<?php 
// no longer used
echo '<div id="management-menu">';
$this->load->view('mgmt_menu');
echo '</div>'.

	  '<div id="module-title">'.
		(isset($mod_title)?$this->pbf->get_mod_title($mod_title):'').
	 '</div>'.

	 '<div id="management-submenu">'.
		(isset($mngt_submenu)?$this->pbf->get_mod_submenu($mngt_submenu):'').
	 '</div>'.
	 
	 '<div class="'.$this->session->flashdata('mod_clss').'">'.
		$this->session->flashdata('mod_msg').' Module messages (Background is Green:OK, Red:Error, Yellow:Warning)'.
	 '</div>'.
	 
	 '<div id="management-contents">';
$this->load->view($mgmt_body);
echo '</div>';
?>