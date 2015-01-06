<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Alertes extends CI_Controller {
 
//==========================================================================================================================

	function __construct()
		{
		parent::__construct();
		$this->load->model('alertes_mdl');
		$this->load->model('alertes_log_mdl');
		$this->lang->load('alertes', $this->config->item('language'));
        $this->lang->load('hfrentities',$this->config->item('language'));
		}
		
	function index(){
		
		redirect('alertes/alertes_list');
				
		}
		
	function alertes_list(){
		
		$preps = $this->pbf->prep_listing_terms_uri_keys();
		$data = $this->alertes_mdl->get_alertes($preps['offset'], $preps['terms']);
		
		
		foreach($data['list'] as $k=>$v){
			
		$data['list'][$k]['alerte_title']=anchor('/alertes/edit/'.$data['list'][$k]['alerteconfig_id'],$data['list'][$k]['alerte_title']);			
		$data['list'][$k]['edit']=$this->pbf->rec_op_icon('edit','/alertes/edit/'.$data['list'][$k]['alerteconfig_id']);
		$data['list'][$k]['delete']=$this->pbf->rec_op_icon('delete','/alertes/delete/'.$data['list'][$k]['alerteconfig_id']);
		$data['list'][$k]['alerte_email']=($data['list'][$k]['alerte_email']==1)?$this->lang->line('list_alertes_yes'):$this->lang->line('list_alertes_no');
		$data['list'][$k]['alerte_dashboard']=($data['list'][$k]['alerte_dashboard']==1)?$this->lang->line('list_alertes_yes'):$this->lang->line('list_alertes_no');
		$data['list'][$k]['alerte_delay']=$data['list'][$k]['alerte_delay'].' '.$this->lang->line('form_alerte_delay_d_brev').' '.$data['list'][$k]['month_delay'].' '.$this->lang->line('form_alerte_delay_m_brev');
		$data['list'][$k]['alerteconfig_id']=$k+$preps['offset']+1;
		unset($data['list'][$k]['alerte_message']);
		unset($data['list'][$k]['month_delay']);
		unset($data['list'][$k]['filetypes']);
		unset($data['list'][$k]['fields_monitor']);
		unset($data['list'][$k]['groups']);
		unset($data['list'][$k]['users']);
		}
		
		array_unshift($data['list'],array('#',
											$this->lang->line('list_alerte_title'),
											$this->lang->line('list_publish_dashboard'),
											$this->lang->line('list_send_email'),
											$this->lang->line('frm_alertes_delay'),
											''));
		
		
		$data['mod_title']['mod_title']=$this->lang->line('alertes_title').' ['.$data['records_num'].' ]';
		$data['mod_title']['/alertes/add']=$this->pbf->rec_op_icon('add');
		$data['mod_title']['dashboard/']=$this->pbf->rec_op_icon('close');
		
		//$data['rec_filters'] = $this->pbf->get_filters(array('indicator_title','indicator_filetype_id'));
		
		//$data['tab_menus'] = $this->pbf->get_mod_submenu(17);
				
		$this->pbf->get_pagination($data['records_num'], $preps['keys'], $preps['uri_segment']);
		
		

		$data['page']='list';
		$this->load->view('body',$data); 
		
		}
	function add($data=''){
				
		$default_users=array();
		$default_users=explode(',',$data['users']);
	 	$fields_monitor=array();
		$fields_monitor['indicator_claimed_value']=$this->lang->line('indicator_claimed_value');
		$fields_monitor['indicator_verified_value']=$this->lang->line('indicator_verified_value');
		$fields_monitor['indicator_validated_value']=$this->lang->line('indicator_validated_value');
		$fields_monitor['datafile_state']=$this->lang->line('datafile_state');
		$fields_monitor['datafile_status']=$this->lang->line('datafile_status');
		$data['fields_monitor']=$fields_monitor;
		$data['filetypes']=$this->pbf->get_filetypess();
		$data['groups']=$this->pbf->get_groups();
		$data['users']=$this->pbf->get_users();
		
        $days_list=array();
		for ($i=1; $i<29; $i++) {
			$days_list[$i]=$i;
		}
		$data['days_list']=$days_list;
		
		$months_list=array();
		for ($i=1; $i<12; $i++) {
			$months_list[$i]=$i;
		}
		$data['months_list']=$months_list;
		
		$data['mod_title']['mod_title']=$this->lang->line('alertes_title'); 
		foreach($data['users'] as $k=>$v){
		$data['users'][$k]['user_id'] = form_checkbox(array(
    															'name'        => 'users[]',
    															'id'          => 'users_id'.$k,
    															'value'       => $v['user_id'],
    															'checked'     => in_array($v['user_id'],$default_users)?TRUE:FALSE,
    															'style'       => 'margin-left:10px',
    																		));
										
		unset($data['users'][$k]['user_name']);
		unset($data['users'][$k]['user_jobtitle']);
		unset($data['users'][$k]['user_phonenumber']);
		unset($data['users'][$k]['user_active']);
		unset($data['users'][$k]['user_pwd']);
		unset($data['users'][$k]['user_published']);
		unset($data['users'][$k]['user_entity']);
		}
			
		array_unshift($data['users'],array('',$this->lang->line('acl_list_task'),$this->lang->line('acl_list_description')));
				
		$data['page']='alertes_frm';
		$this->load->view('body',$data);
		}

		
		
	function save(){
	   	$alerte=$this->input->post();
		$alerte['alerte_dashboard']=(!isset($alerte['alerte_dashboard']))?0:1;
		$alerte['alerte_email']=(!isset($alerte['alerte_email']))?0:1;
		$alerte['filetypes']=implode(',',$alerte['filetypes']);
		$alerte['fields_monitor']=implode(',',$alerte['fields_monitor']);
		$alerte['groups']=implode(',',$alerte['groups']);
		$alerte['users']=implode(',',$alerte['users']);
		
		if (($alerte['groups']===NULL && $alerte['users']===NULL) || $alerte['filetypes']==NULL  ){
			$this->session->set_flashdata(array('mod_clss' => 'errormsg', 'mod_msg' => $this->lang->line('group_error_message'))); 
			redirect('alertes/add');
		}
		unset($alerte['submit']);
       	$this->load->library('form_validation');
		$this->form_validation->set_rules('alerte_title', 'alerte title', 'trim|required');
		$this->form_validation->set_rules('alerte_delay', 'alerte delay', 'callback_chekdelay['.$alerte['alerte_delay'].'])');
		$this->form_validation->set_rules('month_delay', 'month delay', 'callback_chekdelaymonth['.$alerte['month_delay'].'])');
		if ($this->form_validation->run() == FALSE)
		{
		$this->add($alerte);
		}
		else
		{
			
	               
                              
		if($this->alertes_mdl->save_alerte($alerte))
                {
			$this->session->set_flashdata(array('mod_clss' => 'success', 'mod_msg' => $this->lang->line('alerte_save_success'))); 
		}
		else{
                    $this->session->set_flashdata(array('mod_clss' => 'errormsg', 'mod_msg' => $this->lang->line('indicator_save_error'))); 
		}
                
		$this->pbf->set_eventlog('',0);
                
		redirect('alertes/alertes_list');
                
		}
		
	}
	
	function details_alerte($id_alerte){
	     $data['details'] = $this->alertes_log_mdl->details_alerte($id_alerte);
		 $data['mod_title']['mod_title']=$this->lang->line('alertes_title'); 
		 $data['page']='details_alerte';
		 $this->load->view('body',$data);
		//print_r($data);
	}
	
	function edit($alerte_id){
		$data = $this->alertes_mdl->get_alertes_conf($alerte_id);
		$data['filetypes_default']=explode(',',$data['filetypes']);
		$data['fields_monitor_default']=explode(',',$data['fields_monitor']);
		$data['groups_default']=explode(',',$data['groups']);
		$data['users_default']=explode(',',$data['users']);
		$this->add($data);
		}

		
			
	function delete_log_alerte(){
		$alerte=$this->input->post();
		if($this->alertes_log_mdl->del_alerte_log($alerte['alerte_id'])){
			
			$this->session->set_flashdata(array('mod_clss' => 'success', 'mod_msg' => $this->lang->line('success_delete_msg'))); 
				}
				
			else{
				
			$this->session->set_flashdata(array('mod_clss' => 'errormsg', 'mod_msg' => $this->lang->line('alert_delete_error'))); 
				
				}
		$this->pbf->set_eventlog('',0);	
		redirect($this->session->userdata('next_base_url'));
		
		}

		function delete($alert_conf){
		if($this->alertes_mdl->del_alerte($alert_conf)){
			
			$this->session->set_flashdata(array('mod_clss' => 'success', 'mod_msg' => $this->lang->line('success_delete_msg'))); 
				}
				
			else{
				
			$this->session->set_flashdata(array('mod_clss' => 'errormsg', 'mod_msg' => $this->lang->line('alert_delete_error'))); 
				
				}
		$this->pbf->set_eventlog('',0);	
		redirect($this->session->userdata('next_base_url'));
		
		}

		
	function chekdelay($in) {
		if (intval($in) > 30) {
			$this->form_validation->set_message('numcheck', 'Larger than 30');
			return FALSE;
		} else {
			return TRUE;
		}
	}
	function chekdelaymonth($in) {
		if (intval($in) > 11) {
			$this->form_validation->set_message('numcheck', 'Larger than 11');
			return FALSE;
		} else {
			return TRUE;
		}
	}	
		
//===========================================================================================================================
}